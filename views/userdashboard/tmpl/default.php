<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

// Set some variables for use in the Javascript, needed for multilingual
$document = JFactory::getDocument();
$document->addScriptDeclaration("BioDiv.waitText = '".$this->waitText."';");
$document->addScriptDeclaration("BioDiv.doneText = '".$this->doneText."';");
$document->addScriptDeclaration("BioDiv.genText = '".$this->genText."';");


if ( !$this->personId ) {
	print '<a type="button" href="'.JURI::root().'/'.$this->translations['dash_page']['translation_text'].'" class="list-group-item btn btn-block" >'.$this->translations['login']['translation_text'].'</a>';
}
else {
	
	//print "<div class='col-md-12'>";

	//print '<h1>'.$this->translations['dash_heading']['translation_text'].'</h1>';

	//print "</div>";
	
	print "<div class='col-md-3'>";
	
	print "<p></p>";
	// Bootstrap 3 so use panels, change to cards in bootstrap 4
	
	print '<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">';
	
	
	// Spotter and Trapper status
	print '<div class="panel panel-default">';
		
	print '<div class="panel-heading">';
  	print '<div class="row">';
    print '<div class="col-xs-9 col-sm-9 col-md-9"><h4>'.$this->translations['status']['translation_text'].'</h4></div>';
    print '<div role="button" class="col-xs-3 col-sm-3 col-md-3 text-right show_options" data-toggle="tooltip" title="'.$this->translations['show']['translation_text'].'"><h4><i class="fa fa-angle-down fa-lg"></i></h4></div>';
    print '<div role="button" class="col-xs-3 col-sm-3 col-md-3 text-right hide_options" data-toggle="tooltip" title="'.$this->translations['hide']['translation_text'].'"><h4><i class="fa fa-angle-up fa-lg"></i></h4></div>';
	print '</div>';
	print '</div>'; // panel-heading
	
	print '<div class="panel-body panel_options">';
	
	print '<div class="list-group btn-group-vertical btn-block" role="group" aria-label="Status Buttons">';
	
	print '<button id="spotter_status" type="button" class="list-group-item btn btn-block" style="white-space: normal;">';	
	print '<h5>'.$this->translations['spotter']['translation_text'].'</h5>';
	print '</button>';
		
	print '<button id="trapper_status" type="button" class="list-group-item btn btn-block" style="white-space: normal;">';	
	print '<h5>'.$this->translations['trapper']['translation_text'].'</h5>';
	print '</button>';

	print '</div>'; // list-group
		
	print '</div>'; // panel-body
	print '</div>'; //panel
	
	
		// Charts - by site or by (my contribution to) project 
	print '<div class="panel panel-default">';
		
	print '<div class="panel-heading">';
  	print '<div class="row">';
    print '<div class="col-xs-9 col-sm-9 col-md-9"><h4>'.$this->translations['charts']['translation_text'].'</h4></div>';
    print '<div role="button" class="col-xs-3 col-sm-3 col-md-3 show_options" data-toggle="tooltip" title="'.$this->translations['show']['translation_text'].'"><h4 class="text-right"><i class="fa fa-angle-down fa-lg"></i></h4></div>';
    print '<div role="button" class="col-xs-3 col-sm-3 col-md-3 hide_options" data-toggle="tooltip" title="'.$this->translations['hide']['translation_text'].'"><h4 class="text-right"><i class="fa fa-angle-up fa-lg"></i></h4></div>';
	print '</div>';
	print '</div>'; // panel-heading
	
	print '<div class="panel-body panel_options">';
	
	print '<div class="list-group btn-group-vertical btn-block" role="group" aria-label="Chart Buttons">';
	
	print '<button id="site_charts" type="button" class="list-group-item btn btn-block" style="white-space: normal;">';	
	print '<h5>'.$this->translations['site_charts']['translation_text'].'</h5>';
	print '</button>';
		
	//print '<button id="project_charts" type="button" class="list-group-item btn btn-block" style="white-space: normal;">';	
	//print '<h5>'.$this->translations['project_charts']['translation_text'].'</h5>';
	//print '</button>';

	print '</div>'; // list-group
	
	print '</div>'; // panel-body
	print '</div>'; //panel
	
	
	// Likes - mine and others of my seqoences
	print '<div class="panel panel-default">';
		
	print '<div class="panel-heading">';
  	print '<div class="row">';
    print '<div class="col-xs-9 col-sm-9 col-md-9"><h4>'.$this->translations['likes']['translation_text'].'</h4></div>';
    print '<div role="button" class="col-xs-3 col-sm-3 col-md-3 text-right show_options" data-toggle="tooltip" title="'.$this->translations['show']['translation_text'].'"><h4><i class="fa fa-angle-down fa-lg"></i></h4></div>';
    print '<div role="button" class="col-xs-3 col-sm-3 col-md-3 text-right hide_options" data-toggle="tooltip" title="'.$this->translations['hide']['translation_text'].'"><h4><i class="fa fa-angle-up fa-lg"></i></h4></div>';
	print '</div>';
	print '</div>'; // panel-heading
	
	print '<div class="panel-body panel_options">';
	
	print '<div class="list-group btn-group-vertical btn-block" role="group" aria-label="Likes Buttons">';
	
	print '<button id="my_likes" type="button" class="list-group-item btn btn-block" style="white-space: normal;">';	
	print '<h5>'.$this->translations['my_likes']['translation_text'].'</h5>';
	print '</button>';
		
	print '<button id="others_likes" type="button" class="list-group-item btn btn-block" style="white-space: normal;">';	
	print '<h5>'.$this->translations['others_likes']['translation_text'].'</h5>';
	print '</button>';

	print '</div>'; // list-group
		
	print '</div>'; // panel-body
	print '</div>'; //panel
	
	
	
	// Reports
	print '<div class="panel panel-default">';
		
	print '<div class="panel-heading">';
  	print '<div class="row">';
    print '<div class="col-xs-9 col-sm-9 col-md-9"><h4>'.$this->translations['reports']['translation_text'].'</h4></div>';
    print '<div role="button" class="col-xs-3 col-sm-3 col-md-3 text-right show_options" data-toggle="tooltip" title="'.$this->translations['show']['translation_text'].'"><h4><i class="fa fa-angle-down fa-lg"></i></h4></div>';
    print '<div role="button" class="col-xs-3 col-sm-3 col-md-3 text-right hide_options" data-toggle="tooltip" title="'.$this->translations['hide']['translation_text'].'"><h4><i class="fa fa-angle-up fa-lg"></i></h4></div>';
	print '</div>';
	print '</div>'; // panel-heading
	
	print '<div class="panel-body panel_options">';
	
	print '<div class="list-group btn-group-vertical btn-block" role="group" aria-label="Report Buttons">';
		  
	foreach($this->reports as $report){
		
		$reportType = $report[0];
		$reportName = $report[1];
		
		// When page is first loaded 
		
		$tooltipText = "";
		if ( array_key_exists ( $reportType, $this->reportText ) ) {
			error_log ("Tooltip text = " . $this->reportText[$reportType] );
			$tooltipText = ' data-toggle="tooltip" title="' . preg_replace( '/[\W]/', ' ', $this->reportText[$reportType]) . '"';
		}
		print '<button type="button" class="list-group-item btn btn-block report-btn" ' . $tooltipText . ' data-report-type="'.$reportType.'" style="white-space: normal;">';
		
		//print '<h4 class="list-group-item-heading">'.$reportName.'</h4>';
		//print '<div class="list-group-item-text">'.$this->reportArticles[$reportId]->introtext.'</div>';
		
		print '<h5>'.$reportName.'</h5>';
		//print '<div>' . $this->reportArticles[$reportType]->introtext . '</div>';
		print '</button>';
	}

	print '</div>'; // list-group
	
	print '</div>'; // panel-body
	print '</div>'; //panel
	
	print '</div>'; // panel-group

	print "</div>";	 // col-md-3

	print "<div class='col-md-9'>";

	print "<div id='report_display'>";

	print "</div>";

	print "</div>"; // col-md-9

}

print '<div id="carousel_modal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog"  >';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header">';
print '        <button type="button" class="close" data-dismiss="modal">&times;</button>';
print '        <h4 class="modal-title">'.$this->translations['review']['translation_text'].'</h4>';
print '      </div>';
print '     <div class="modal-body">';
print '	    <div id="media_carousel" ></div>';
print '      </div>';
print '	  <div class="modal-footer">';
print '        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>';
print '      </div>';
	  	  
print '    </div>';

print '  </div>';
print '</div>';


JHTML::stylesheet("com_biodiv/com_biodiv.css", array(), true);

//JHTML::script("https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js", true, true);

JHTML::script("https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js", true, true);

JHTML::script("com_biodiv/userdashboard.js", true, true);
JHTML::script("com_biodiv/mediacarousel.js", true, true);
JHTML::script("com_biodiv/report.js", true, true);
?>



