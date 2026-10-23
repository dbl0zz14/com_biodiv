jQuery(document).ready(function(){
	
	const waitText = BioDiv.waitText;
	const doneText = BioDiv.doneText;
	
	jQuery('#failsdownload').click(function (){
	
		// change text to please wait
		// ajax call to generate and get url
		// eventually click the link.
		jQuery(this).prop('disabled', true);
		jQuery('#failsdownload').text(waitText);
		
		let uploadId = jQuery(this).attr("data-upload-id");
		
		let url = BioDiv.root + "&view=uploadfailsdownload&format=raw" + "&upload_id=" + uploadId;
		
		jQuery.get(url, function(data){
			var reportObj = JSON.parse(data);
			
			let link = document.createElement('a');
			link.download = reportObj.filename;
							
			var str = '';
			
			var dataArray = reportObj.data;

			for (var i = 0; i < dataArray.length; i++) {
				var line = dataArray[i];

				str += line + '\r\n';
			}


			let blob = new Blob([str], {type: 'text/csv;charset=utf-8;'});

			link.href = URL.createObjectURL(blob);

			link.click();

			URL.revokeObjectURL(link.href);
			
			jQuery('#failsdownload').text(doneText);
		});		
	});
});
