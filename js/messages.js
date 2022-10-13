
function setMessageBadge () {
	
	let numNew = jQuery(".newMessage").length;
	let prevNumNew = parseInt(jQuery("#messageBadge").text());
	
	if ( numNew != prevNumNew ) {
		if ( numNew == 0 ) {
			jQuery("#messageBadge").remove();
		}
		else {
			jQuery("#messageBadge").text("" + numNew);
		}
	}
}


// ------------------------------- Messages stuff

function activateMessageButtons () {
	
	setReloadPage();
	setMessageBadge();
	
	jQuery(".inboxTab").click ( function () {
		jQuery(this).addClass("active");
		jQuery(".sentMessagesTab").removeClass("active");
		let url = BioDiv.root + "&view=messagelist&format=raw";
		jQuery('#messageArea').load(url, activateMessageList);
	});
	
	jQuery(".sentMessagesTab").click ( function () {
		jQuery(this).addClass("active");
		jQuery(".inboxTab").removeClass("active");
		let url = BioDiv.root + "&view=messagelist&format=raw&sent=1";
		jQuery('#messageArea').load(url, activateMessageList);
	});
	
	jQuery('#newMessageForm').submit(sendMessage);
	jQuery('#replyMessageForm').submit(sendMessage);
	jQuery('#reportMessageForm').submit(sendMessage);
	
	jQuery('.replyBtn').click(replyMessage);
	
	jQuery('.reportBtn').click(reportMessage);
	
	jQuery('.helpButton').click( displayHelpArticle );
}

function activateMessageList () {
	
	setReloadPage();
	
	jQuery('.replyBtn').click(replyMessage);
	
	jQuery('.reportBtn').click(reportMessage);
	
	setMessageBadge();
}

function replyMessage () {
	
	let buttonId = this.id;
	let idbits = buttonId.split("_");
	let prevMessageId = idbits.pop();
	let prevPerson = jQuery(this).data("sender");
	
	jQuery('[name="replyTo"]').val( prevMessageId );
	jQuery('#replyRecipient').val( prevPerson );
	
	jQuery("#replyModal").modal("show");
}

function reportMessage () {
	
	let buttonId = this.id;
	let idbits = buttonId.split("_");
	let prevMessageId = idbits.pop();
	let prevPerson = jQuery(this).data("sender");
	
	jQuery('[name="reportedMsgId"]').val( prevMessageId );
	jQuery('#reportedPerson').val( prevPerson );
	
	jQuery("#reportModal").modal("show");
}

function sendMessage ( e ) {
	
	e.preventDefault();
	
	let formId = jQuery(this).attr('id');
	
	let fd = new FormData(this);
	
	let success = true;
	
	if ( formId == "newMessageForm" ) {
		
		success = validateMessageForm(fd);
		
		if ( success ) {
		
			let url = BioDiv.root + "&view=sendmessage&format=raw";
			
			jQuery.ajax({
				type: 'POST',
				url: url,
				data: fd,
				processData: false,
				contentType: false
			}).done(messageSent);
			
		}
	}
	else if ( formId == "replyMessageForm" ) {
		
		success = validateReplyMessageForm(fd);
		
		if ( success ) {
		
			let url = BioDiv.root + "&view=sendmessage&format=raw";
			
			jQuery.ajax({
				type: 'POST',
				url: url,
				data: fd,
				processData: false,
				contentType: false
			}).done(replySent);
			
		}
	}
	else if ( formId == "reportMessageForm" ) {
		
		success = validateReportMessageForm(fd);
		
		if ( success ) {
		
			let url = BioDiv.root + "&view=sendmessage&format=raw";
			
			jQuery.ajax({
				type: 'POST',
				url: url,
				data: fd,
				processData: false,
				contentType: false
			}).done(reportSent);
			
		}
	}
	
	
}

function validateMessageForm () {
	
	let success = true;
	
	// For newMessageForm, check values
	
	if ( jQuery ('[name=recipientSelect]').val() ) {
		jQuery ('[name=recipientSelect]').removeClass('invalid');
	}
	else {
		console.log ( "No recipient selected" );
		success = false;
		jQuery ('[name=recipientSelect]').addClass('invalid');
	}
	
	if ( jQuery('[name=messageText]').length > 0 ) {
		jQuery ('[name=messageText]').removeClass('invalid');
	}
	else {
		console.log ( "No text in message" );
		success = false;
		jQuery ('[name=messageText]').addClass('invalid');
	}

	return success;
}

function validateReplyMessageForm () {
	
	let success = true;
	
	if ( jQuery('[name=replyText]').length > 0 ) {
		jQuery ('[name=replyText]').removeClass('invalid');
	}
	else {
		console.log ( "No text in message" );
		success = false;
		jQuery ('[name=replyText]').addClass('invalid');
	}

	return success;
}

function validateReportMessageForm () {
	
	let success = true;
	
	if ( jQuery('[name=reportText]').length > 0 ) {
		jQuery ('[name=reportText]').removeClass('invalid');
	}
	else {
		console.log ( "No text in message" );
		success = false;
		jQuery ('[name=reportText]').addClass('invalid');
	}

	return success;
}

function messageSent ( data ) {
	jQuery("#sendMessageBtn").hide();
	
	let messageResponse = JSON.parse(data);
	if ( messageResponse.error ) {
		jQuery("#messageMsg").text(messageResponse.error);
	}
	else {
		if ( messageResponse.message ) {
			jQuery("#messageMsg").text(messageResponse.message);
		}
	}
}

function reportSent ( data ) {
	jQuery("#reportMessageBtn").hide();
	
	let messageResponse = JSON.parse(data);
	if ( messageResponse.error ) {
		jQuery("#messageMsg").text(messageResponse.error);
	}
	else {
		if ( messageResponse.message ) {
			jQuery("#reportMessageMsg").text(messageResponse.message);
		}
	}
}

function replySent ( data ) {
	jQuery("#replyMessageBtn").hide();
	
	
	let messageResponse = JSON.parse(data);
	if ( messageResponse.error ) {
		jQuery("#replyMessageMsg").text(messageResponse.error);
	}
	else {
		if ( messageResponse.message ) {
			jQuery("#replyMessageMsg").text(messageResponse.message);
		}
		if ( messageResponse.replyTo ) {
			jQuery("#replyFlag_" + messageResponse.replyTo + ' > i ').show();
		}
	}
	
}


// -------------------------------------- end of messages stuff


	
	
	
	
jQuery(document).ready(function(){
	
	let url = BioDiv.root + "&view=messagelist&format=raw";
	jQuery("#messageArea").load(url, activateMessageButtons);
	
});
