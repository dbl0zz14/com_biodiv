jQuery(document).ready(function(){
	
	// Pick up text string variables - could be non-English
	const waitText = BioDiv.waitText;
	const doneText = BioDiv.doneText;
	const genText = BioDiv.genText;
	
	let currentReportType = null;
	let currentReportId = null;
	let currentProjectId = jQuery("#project_select").children("option:selected").val();
	
	// Keep track of reports generated so we can just reload them rather than recreating.
	// User should refresh to start again.
	let existingReports = [];
	
	displayReport = function ( pageNum ) {
		
		//let project_id = jQuery("#project_select").children("option:selected").val();
		
		let url = "";
		
		// Is there a report already loaded?
		if ( currentReportId == null ) {
			
			// Have we already created this report 
			let existingReportId = 0;
			for ( let i = 0; i < existingReports.length; i++ ) {
				let report = existingReports[i];
				if ( report.projectId == currentProjectId && report.reportType == currentReportType ) {
					existingReportId = report.reportId;
				}				
			}
			
			if ( existingReportId != 0 ) {
				// Load a previously generated report
				url = BioDiv.root + "&view=report&format=raw&project_id=" + currentProjectId + "&report_id=" + existingReportId + "&page=" + pageNum;
			}
			else {		
				url = BioDiv.root + "&view=report&format=raw&project_id=" + currentProjectId + "&report_type=" + currentReportType + "&page=" + pageNum;
			}
		}
		else {
			url = BioDiv.root + "&view=report&format=raw&project_id=" + currentProjectId + "&report_id=" + currentReportId + "&page=" + pageNum;
		}
		jQuery('#report_display').load(url, updateReportStatus);
		
	};
	
	updateReportStatus = function () {
		// Set id
		currentReportId = jQuery("#reportdownload").attr('data-report-id');
		if ( !currentReportId ) {
			currentReportId = jQuery("#rptfiledownload").attr('data-report-id');
		}
		
		// Add this report to list of reports
		addReport();
		
		updatePagination();
		updateDownload();
	};
	
	
	addReport = function () {
		// Create a new object for the report.
		let newReport = { projectId: currentProjectId, reportType: currentReportType, reportId: currentReportId };
		
		let existingReportId = 0;
		for ( let i = 0; i < existingReports.length; i++ ) {
			let report = existingReports[i];
			if ( report.projectId == currentProjectId && report.reportType == currentReportType ) {
				existingReportId = report.reportId;
			}				
		}
			
		if ( existingReportId == 0 ) {
			existingReports.push(newReport);
		}
	};
	
		
	updatePagination = function () {
		
		jQuery('.pagination li').click(function (){
			let pageText = null;
			let page = null;
			if ( jQuery(this).hasClass('prev-page') ) {
				pageText = jQuery('.pagination li.active').text();
				page = parseInt(pageText) -2;
				if ( page < 0 ) page = 0;
			}
			else if ( jQuery(this).hasClass('next-page') ) {
				pageText = jQuery('.pagination li.active').text();
				page = parseInt(pageText);
				
				let lastPageText = jQuery('.last-page').text();
				let lastPage = parseInt(lastPageText);
				if ( page == lastPage ) page = lastPage-1;
				
			}
			else {
				pageText = jQuery(this).text();
				page = parseInt(pageText) -1;
			}
			displayReport( page );
	
		});
		
	};
	
	
	jQuery('.report-btn').click(function (){
		
		currentProjectId = jQuery("#project_select").children("option:selected").val();
		currentReportType = jQuery(this).attr('data-report-type');
		currentReportId = null;
		
		jQuery('.report-btn h4').removeClass('text-info');
		jQuery(this).children("h4").addClass('text-info');
		
		jQuery('#report_display').html("<h4>" + genText + "<h4>");
		
		displayReport(0);
		
	});
	
	updateDownload = function () {
		jQuery('#reportdownload').click(function (){
			
			// change text to please wait
			// ajax call to generate and get url
			// eventually click the link.
			jQuery(this).prop('disabled', true);
			jQuery('#reportdownload h4').text(waitText);
			
			let reportId = jQuery(this).attr("data-report-id");
			//let url = BioDiv.root + "&view=reportdownload&format=raw&project_id=" + project_id + "&report_id=" + currentReportId;
			let url = BioDiv.root + "&view=reportdownload&format=raw" + "&report_id=" + reportId;
			
			jQuery.get(url, function(data){
				var reportObj = JSON.parse(data);
				
				let link = document.createElement('a');
				link.download = reportObj.filename;
								
				var str = '';
				
				var headings = reportObj.headings;

				str += headings + '\r\n';
				
				var dataArray = reportObj.data;

				for (var i = 0; i < dataArray.length; i++) {
					var line = dataArray[i];

					str += line + '\r\n';
				}


				let blob = new Blob([str], {type: 'text/csv;charset=utf-8;'});

				link.href = URL.createObjectURL(blob);

				link.click();

				URL.revokeObjectURL(link.href);
				
				jQuery('#reportdownload h4').text(doneText);
			});
			
		});
		
		jQuery('#rptfiledownload').click(function (){
			
			// change text to please wait
			// ajax call to generate and get url
			// eventually click the link.
			jQuery(this).prop('disabled', true);
			jQuery('#rptfiledownload h4').text(waitText);
			
			let reportId = jQuery(this).attr("data-report-id");
			//let url = BioDiv.root + "&view=rptfiledownload&format=raw&project_id=" + project_id + "&report_id=" + currentReportId;
			let url = BioDiv.root + "&view=rptfiledownload&format=raw" + "&report_id=" + reportId;
			
			jQuery.get(url, function(data){
				
				let link = document.createElement('a');
				link.href = data;
				
				let filename = data.substring(data.lastIndexOf('/')+1);
				link.download = filename;
				
				link.click();

				jQuery('#rptfiledownload h4').text(doneText);
			});
			
		});
		
		
	};
	
	jQuery('#project_select').change(function (){
			
			// Remove any report as project has been changed
			jQuery('#report_display').empty();
			
			jQuery('.report-btn h4').removeClass('text-info');
			
	});
			
	
});
