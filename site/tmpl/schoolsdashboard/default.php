<?php

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

 
// No direct access to this file
defined('_JEXEC') or die;

// Set some variables for use in the Javascript, needed for multilingual
$document = Factory::getDocument();
$document->addScriptDeclaration("BioDiv.waitText = '".$this->waitText."';");
$document->addScriptDeclaration("BioDiv.doneText = '".$this->doneText."';");
$document->addScriptDeclaration("BioDiv.genText = '".$this->genText."';");


if ( !$this->personId ) {
	print '<a type="button" href="'.$this->dashUri.'" class="list-group-item btn btn-block" >'.Text::_("COM_BIODIV_SCHOOLSDASHBOARD_LOGIN").'</a>';
}
else {
	
	print '<div class="container">';
	print '<div class="row d-flex">';
	//print '<div class="col-md-3 mh-100" style="overflow-y: scroll;">';
	print '<div class="col-md-3 overflow-auto menu-background" >';
	
	print '<p></p>';
	//print '<h4>'.Text::_("COM_BIODIV_MWSDASHBOARD_MENU").'</h4>';
	
	print '<div class="accordion " id="dashAccordion" >';
	
	print '<div class="accordion-item">';		
	print '<h2 class="accordion-header" >';
  	print '<button id="schools_dash_summary" class="accordion-button no-after"  type="button" >'.Text::_("COM_BIODIV_SCHOOLSDASHBOARD_STATUS").'</button>';
	print '</h2>';
	print '</div>'; // accordion-item
	
	
	// Charts - by site or by (my contribution to) project 
	print '<div class="accordion-item">';		
	print '<h2 class="accordion-header" id="dashChartsHeader">';
  	print '<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#dashCharts" aria-expanded="false" aria-controls="dashCharts">'.Text::_("COM_BIODIV_SCHOOLSDASHBOARD_CHARTS").'</button>';
	print '</h2>';
	print '<div id="dashCharts" class="accordion-collapse collapse" aria-labelledby="dashChartsHeader" data-bs-parent="#dashAccordion">'; 
    print '<div class="accordion-body">';
	print '<button id="site_charts" type="button" class="btn">';	
	print '<h5>'.Text::_("COM_BIODIV_SCHOOLSDASHBOARD_SITE_CHARTS").'</h5>';
	print '</button>';
	print '</div>'; // accordion-body
	print '</div>'; // accordion collapse
	print '</div>'; // accordion-item
	
	
	// Likes - mine and others of my seqoences 
	print '<div class="accordion-item">';		
	print '<h2 class="accordion-header" id="dashLikesHeader">';
  	print '<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#dashLikes" aria-expanded="false" aria-controls="dashLikes">'.Text::_("COM_BIODIV_SCHOOLSDASHBOARD_LIKES").'</button>';
	print '</h2>';
	print '<div id="dashLikes" class="accordion-collapse collapse" aria-labelledby="dashLikesHeader" data-bs-parent="#dashAccordion">'; 
    print '<div class="accordion-body">';
	print '<button id="all_my_likes" type="button" class="btn">';	
	print '<h5>'.Text::_("COM_BIODIV_SCHOOLSDASHBOARD_ALL_MY_LIKES").'</h5>';
	print '</button>';
	print '<button id="my_likes" type="button" class="btn">';	
	print '<h5>'.Text::_("COM_BIODIV_SCHOOLSDASHBOARD_MY_LIKES").'</h5>';
	print '</button>';
	print '<button id="others_likes" type="button" class="btn">';	
	print '<h5>'.Text::_("COM_BIODIV_SCHOOLSDASHBOARD_OTHERS_LIKES").'</h5>';
	print '</button>';
	print '</div>'; // accordion-body
	print '</div>'; // accordion collapse
	print '</div>'; // accordion-item
	
	// if ( count($this->optInAnalysis) > 0 ) {
		
		// print '<div class="accordion-item">';		
		// print '<h2 class="accordion-header" id="dashAnalysisHeader">';
		// print '<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#dashAnalysis" aria-expanded="false" aria-controls="dashAnalysis">'.Text::_("COM_BIODIV_SCHOOLSDASHBOARD_ANALYSIS").'</button>';
		// print '</h2>';
		// print '<div id="dashAnalysis" class="accordion-collapse collapse" aria-labelledby="dashAnalysisHeader" data-bs-parent="#dashAccordion">'; 
		// print '<div class="accordion-body">';
		// foreach($this->optInAnalysis as $analysis){
			
			// $analysisType = $analysis[0];
			// $analysisName = $analysis[1];
			
			// // When page is first loaded 
			
			// $tooltipText = "";
			// // if ( array_key_exists ( $reportType, $this->reportText ) ) {
				// // $tooltipText = ' data-bs-toggle="tooltip" title="' . preg_replace( '/[\W]/', ' ', $this->reportText[$reportType]) . '"';
			// // }
			// print '<button type="button" class="btn analysis-btn" ' . $tooltipText . ' data-analysis-type="'.$analysisType.'" >';
			
			// print '<h5>'.$analysisName.'</h5>';
			
			// print '</button>';
		// }
		
		// print '</div>'; // accordion-body
		// print '</div>'; // accordion collapse
		// print '</div>'; // accordion-item
	// }
	
	// Reports
	print '<div class="accordion-item">';		
	print '<h2 class="accordion-header" id="dashReportsHeader">';
  	print '<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#dashReports" aria-expanded="false" aria-controls="dashReports">'.Text::_("COM_BIODIV_SCHOOLSDASHBOARD_REPORTS").'</button>';
	print '</h2>';
	print '<div id="dashReports" class="accordion-collapse collapse" aria-labelledby="dashReportsHeader" data-bs-parent="#dashAccordion">'; 
    print '<div class="accordion-body">';
	foreach($this->reports as $report){
		
		$reportType = $report[0];
		$reportName = $report[1];
		
		// When page is first loaded 
		
		$tooltipText = "";
		if ( array_key_exists ( $reportType, $this->reportText ) ) {
			$tooltipText = ' data-bs-toggle="tooltip" title="' . preg_replace( '/[\W]/', ' ', $this->reportText[$reportType]) . '"';
		}
		print '<button type="button" class="btn report-btn" ' . $tooltipText . ' data-report-type="'.$reportType.'" >';
		//print '<button type="button" class="list-group-item btn btn-block report-btn" ' . $tooltipText . ' data-report-type="'.$reportType.'" style="white-space: normal;">';
		
		print '<h5>'.$reportName.'</h5>';
		
		print '</button>';
	}
	foreach($this->optInReports as $report){
		
		$reportType = $report[0];
		$reportName = $report[1];
		
		// When page is first loaded 
		
		$tooltipText = "";
		if ( array_key_exists ( $reportType, $this->reportText ) ) {
			$tooltipText = ' data-bs-toggle="tooltip" title="' . preg_replace( '/[\W]/', ' ', $this->reportText[$reportType]) . '"';
		}
		print '<button type="button" class="btn report-btn" ' . $tooltipText . ' data-report-type="'.$reportType.'" >';
		//print '<button type="button" class="list-group-item btn btn-block report-btn" ' . $tooltipText . ' data-report-type="'.$reportType.'" style="white-space: normal;">';
		
		print '<h5>'.$reportName.'</h5>';
		
		print '</button>';
	}
	print '</div>'; // accordion-body
	print '</div>'; // accordion collapse
	print '</div>'; // accordion-item

	print "</div>";	 // accordion
	
	print '<p></p>';
	
	
	print "</div>";	 // col-md-3

	print "<div class='col-md-9'>";
	
	print "<div class='row'>";

	print "<div id='report_display'></div>";

	print "</div>"; // report row

	print "<div class='row'>";

	print "<div id='data_warning' hidden>";
	
	print "<p style='margin-top: 10px'>".Text::_("COM_BIODIV_SCHOOLSDASHBOARD_DATA_WARN")."</p>";

	print '</div>';

	print '</div>'; // warning row

	print '</div>'; // col-md-9
	
	print '</div>'; // row
	
	print '</div>'; // container

}

print '<div id="carousel_modal" class="modal fade hide"  data-bs-backdrop="false" tabindex="-1">';
print '  <div class="modal-dialog"  >';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header">';
print '        <h4 class="modal-title">'.Text::_("COM_BIODIV_SCHOOLSDASHBOARD_REVIEW").'</h4>';
print '        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
// print '        <button type="button" class="close" data-dismiss="modal">&times;</button>';
// print '        <h4 class="modal-title">'.Text::_("COM_BIODIV_SCHOOLSDASHBOARD_REVIEW").'</h4>';
print '      </div>';
print '     <div class="modal-body">';
print '	    <div id="media_carousel" ></div>';
print '      </div>';
print '	  <div class="modal-footer">';
print '        <button type="button" class="btn btn-primary" data-dismiss="modal">'.Text::_("COM_BIODIV_SCHOOLSDASHBOARD_CLOSE").'</button>';
print '      </div>';
	  	  
print '    </div>';

print '  </div>';
print '</div>';


$this->document->getWebAssetManager()->usePreset('com_biodiv.schoolsdashboard');
HTMLHelper::_('bootstrap.modal', '.selector', []);
HTMLHelper::_('bootstrap.carousel', '#photoCarousel', []);


// JHTML::stylesheet("com_biodiv/com_biodiv.css", array(), true);

// //JHTML::script("https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js", true, true);

// JHTML::script("https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js", true, true);

// JHTML::script("com_biodiv/userdashboard.js", true, true);
// JHTML::script("com_biodiv/mediacarousel.js", true, true);
// JHTML::script("com_biodiv/report.js", true, true);
?>



