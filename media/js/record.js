
let audioBlob;
let audioMimeType;
let recordDateTime;
let maxClipLength = 5;
let hundredths = 0;
let intervalId;
let mediaRecorder;


// appends an audio element to playback and download recording
function createAudioElement(blobUrl, mimeType) {
	
	const audioEl = document.createElement('audio');
	audioEl.controls = true;
	const sourceEl = document.createElement('source');
	sourceEl.src = blobUrl;
	sourceEl.type = mimeType;
	audioEl.appendChild(sourceEl);
	
	jQuery("#preview").append(audioEl);
	
	let fileExt = mimeType.split('/').pop();
	
	jQuery("#download").attr( 'download', 'audio.' + fileExt );
	jQuery("#download").attr( 'href', blobUrl );
	
	
}


function zeroPaddedString( numToPad ) {
	let numAsStr = numToPad.toString();
	if ( numToPad < 10 ) numAsStr = '0' + numAsStr;
	return numAsStr;
}


function isDST(d) {
    let jan = new Date(d.getFullYear(), 0, 1);
	let janOffset = jan.getTimezoneOffset();
    let jul = new Date(d.getFullYear(), 6, 1);
	let julOffset = jul.getTimezoneOffset();
    return Math.max(jan, jul) != d.getTimezoneOffset(); 
}



function formatDateTime(d) {
	
	let nowDate = '' + d.getFullYear() + zeroPaddedString(d.getMonth()+1) +  zeroPaddedString(d.getDate());
	let nowTime = zeroPaddedString(d.getHours()) + zeroPaddedString(d.getMinutes()) + zeroPaddedString(d.getSeconds());
	return nowDate + '_' + nowTime;
}


function validateRecordForm ( fd ) {
	
	let success = true;
	
	// For uploadForm, check a site has been selected.
	if ( fd.has("site_id") ) {
		jQuery ('[name=site_id]').removeClass('invalid');
	}
	else {
		console.log ( "Form has no site_id: " + fd.get("site_id") );
		
		success = false;
		jQuery ('[name=site_id]').addClass('invalid');
		
	}
	
	return success;
}


function uploadFromForm (e) {
		
	e.preventDefault();
	
	let formId = jQuery(this).attr('id');
	
	let fd = new FormData(this);
	
	let success = true;
	
	if ( formId == "uploadForm" ) {
		
		success = validateRecordForm(fd);
	}
	
	if ( success ) {
			
		let fileExt = audioMimeType.split('/').pop();
		
		let formattedDT = formatDateTime(recordDateTime)
		fd.append('fname', 'narecording_' + formattedDT + '.' + fileExt);
		fd.append('data', audioBlob);
		
		let resolvedOptions = Intl.DateTimeFormat().resolvedOptions();
		//console.log(resolvedOptions);
		//console.log(resolvedOptions.timeZone);
		let tzid = Intl.DateTimeFormat().resolvedOptions().timeZone;
		
		let dst = 0;
		if ( isDST(recordDateTime) ) dst = 1;
		
		fd.append('timezone', tzid);
		fd.append('dst', dst);
			
		url = BioDiv.root + "&view=record&format=raw";
		
		jQuery(".loader").removeClass("invisible");
	
		jQuery.ajax({
			type: 'POST',
			url: url,
			data: fd,
			processData: false,
			contentType: false
		}).done(uploadSuccess);
		
	}
	
}


function mediaRecorderNotSupported () {
		
	jQuery("#record_text").hide();
	jQuery("#start_recording").hide();
	jQuery("#end_recording").hide();
	jQuery('#not_supported').show();
	
}


function uploadSuccess(data) {
	
	jQuery(".loader").addClass("invisible");
	
	jQuery("#uploaded").html(data);
	jQuery('#select_site_modal').modal('hide');	
	jQuery('#add_site_modal').modal('hide');	
	jQuery(".upload_button").hide();
	
	jQuery("#record_again").click(recordAgain);				
	
}

function recordAgain () {
	
	jQuery("#preview").empty();
	jQuery("#uploaded").empty();
	
	jQuery("#record_again").hide();
	jQuery("#download").hide();
	
	jQuery("#start_recording").prop( 'disabled', false );
	jQuery("#start_recording").show();
	
	jQuery("#record_text").show();
	jQuery("#upload_text").hide();
}


function incrementCounter() {
	
    hundredths += 10;
	seconds = Math.floor ( hundredths/100 );
	hLeft = hundredths - (100*seconds);
	
	if (hLeft < 10 ) hText = "0" + hLeft;
	else hText = "" + hLeft;
	
    jQuery("#counter").text('' + seconds + ':' + hText);
}




async function startRecording()
{
	jQuery("#start_recording").prop( 'disabled', true );
	jQuery("#start_recording").hide();
	jQuery("#end_recording").show();
	
	if (!window.MediaRecorder) {
		console.log ("MediaRecorder not supported");
		mediaRecorderNotSupported();
	}
	else if (!MediaRecorder.isTypeSupported) {
		console.log ("MediaRecorder.isTypeSupported not supported");
		mediaRecorderNotSupported();
	}
	else {

		navigator.mediaDevices.getUserMedia({ audio: true, video: false })
			.then(stream=> {
				
				
				// Check what types can be handled, m4a is preferred
				let mimeType;
				
				if ( MediaRecorder.isTypeSupported("audio/m4a") == true ) {
					mimeType = "audio/m4a";
				}						
				else if ( MediaRecorder.isTypeSupported("audio/mp4") == true ) {
					mimeType = "audio/mp4";
				}
				else if ( MediaRecorder.isTypeSupported("audio/ogg") == true ) {
					mimeType = "audio/ogg";
				}
				else if ( MediaRecorder.isTypeSupported("audio/webm") == true ) {
					mimeType = "audio/webm";
				}
				else console.log("None of mime types supported");
				
				console.log ( "mimeType = " + mimeType );
				
				
				let options = {
					mimeType : mimeType
				}
				
				//const mediaRecorder = new MediaRecorder( stream, options );
				mediaRecorder = new MediaRecorder( stream, options );

				audioMimeType = mimeType;
				
				recordDateTime = new Date();
				
				
				
				mediaRecorder.start();
				
				intervalId = setInterval(incrementCounter, 100);
				
				//const audioChunks = [];
				let audioChunks = [];
				
				mediaRecorder.addEventListener("dataavailable", event => {
					audioChunks.push(event.data);
				});
				
				mediaRecorder.addEventListener("stop", () => {
					
					clearInterval(intervalId);
					seconds = 0;
					
					audioBlob = new Blob(audioChunks);
					//const audioUrl = URL.createObjectURL(audioBlob);
					let audioUrl = URL.createObjectURL(audioBlob);
					
					createAudioElement(audioUrl, audioMimeType);
					
					jQuery(".upload_button").show();
					jQuery("#download").show();
					jQuery("#end_recording").hide();
					jQuery("#counter").empty();
					hundredths = 0;
					
					jQuery("#record_text").hide();
					jQuery("#upload_text").show();
					
				});
				
				jQuery("#end_recording").click(() => {
					mediaRecorder.stop();
				});
				
				setTimeout(() => {
					mediaRecorder.stop();
				}, maxClipLength * 1000);
		});
	}
	
}


	
jQuery(document).ready(function(){
	
	maxClipLength = BioDiv.maxClipLength;
	selectSiteError = BioDiv.selectSiteError;
	
	
	jQuery("#start_recording").click(startRecording);
	
		
	jQuery('#upload_to_existing').click(function (){
		jQuery('#select_site_modal').modal('show');	
		
	});
	
	jQuery('#upload_to_new').click(function (){
		currentTab = 0; // Current tab is set to be the first tab (0)
		showTab(currentTab); // Display the current tab	
		jQuery('#add_site_modal').modal('show');	
		
	});
	
	jQuery('#uploadForm').submit(uploadFromForm);
	
	jQuery('#siteForm').submit(uploadFromForm);
	
	jQuery('[name=site_id]').change( function () {
		jQuery(this).removeClass('invalid');
	});
	
});


















