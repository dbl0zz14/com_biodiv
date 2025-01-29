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
	print '<a type="button" href="'.JURI::root().'/'.JText::_("COM_BIODIV_USERDASHBOARD_DASH_PAGE").'" class="list-group-item btn btn-block" >'.JText::_("COM_BIODIV_USERDASHBOARD_LOGIN").'</a>';
}
else {
	
	print "<div class='col-md-3'>";
	
	print "<p></p>";
	// Bootstrap 3 so use panels, change to cards in bootstrap 4
	
	print '<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">';
	
	
	// Spotter and Trapper status
	print '<div class="panel panel-default">';
		
	print '<div class="panel-heading">';
  	print '<div class="row">';
    print '<div class="col-xs-9 col-sm-9 col-md-9"><h4>'.JText::_("COM_BIODIV_USERDASHBOARD_STATUS").'</h4></div>';
    print '<div role="button" class="col-xs-3 col-sm-3 col-md-3 text-right show_options" data-toggle="tooltip" title="'.JText::_("COM_BIODIV_USERDASHBOARD_SHOW").'"><h4><i class="fa fa-angle-down fa-lg"></i></h4></div>';
    print '<div role="button" class="col-xs-3 col-sm-3 col-md-3 text-right hide_options" data-toggle="tooltip" title="'.JText::_("COM_BIODIV_USERDASHBOARD_HIDE").'"><h4><i class="fa fa-angle-up fa-lg"></i></h4></div>';
	print '</div>';
	print '</div>'; // panel-heading
	
	print '<div class="panel-body panel_options">';
	
	print '<div class="list-group btn-group-vertical btn-block" role="group" aria-label="Status Buttons">';
	
	print '<button id="spotter_status" type="button" class="list-group-item btn btn-block" style="white-space: normal;">';	
	print '<h5>'.JText::_("COM_BIODIV_USERDASHBOARD_SPOTTER").'</h5>';
	print '</button>';
		
	print '<button id="trapper_status" type="button" class="list-group-item btn btn-block" style="white-space: normal;">';	
	print '<h5>'.JText::_("COM_BIODIV_USERDASHBOARD_TRAPPER").'</h5>';
	print '</button>';

	print '</div>'; // list-group
		
	print '</div>'; // panel-body
	print '</div>'; //panel
	
	
		// Charts - by site or by (my contribution to) project 
	print '<div class="panel panel-default">';
		
	print '<div class="panel-heading">';
  	print '<div class="row">';
    print '<div class="col-xs-9 col-sm-9 col-md-9"><h4>'.JText::_("COM_BIODIV_USERDASHBOARD_CHARTS").'</h4></div>';
    print '<div role="button" class="col-xs-3 col-sm-3 col-md-3 show_options" data-toggle="tooltip" title="'.JText::_("COM_BIODIV_USERDASHBOARD_SHOW").'"><h4 class="text-right"><i class="fa fa-angle-down fa-lg"></i></h4></div>';
    print '<div role="button" class="col-xs-3 col-sm-3 col-md-3 hide_options" data-toggle="tooltip" title="'.JText::_("COM_BIODIV_USERDASHBOARD_HIDE").'"><h4 class="text-right"><i class="fa fa-angle-up fa-lg"></i></h4></div>';
	print '</div>';
	print '</div>'; // panel-heading
	
	print '<div class="panel-body panel_options">';
	
	print '<div class="list-group btn-group-vertical btn-block" role="group" aria-label="Chart Buttons">';
	
	print '<button id="site_charts" type="button" class="list-group-item btn btn-block" style="white-space: normal;">';	
	print '<h5>'.JText::_("COM_BIODIV_USERDASHBOARD_SITE_CHARTS").'</h5>';
	print '</button>';
		
	
	print '</div>'; // list-group
	
	print '</div>'; // panel-body
	print '</div>'; //panel
	
	
	// Likes - mine and others of my seqoences
	print '<div class="panel panel-default">';
		
	print '<div class="panel-heading">';
  	print '<div class="row">';
    print '<div class="col-xs-9 col-sm-9 col-md-9"><h4>'.JText::_("COM_BIODIV_USERDASHBOARD_LIKES").'</h4></div>';
    print '<div role="button" class="col-xs-3 col-sm-3 col-md-3 text-right show_options" data-toggle="tooltip" title="'.JText::_("COM_BIODIV_USERDASHBOARD_SHOW").'"><h4><i class="fa fa-angle-down fa-lg"></i></h4></div>';
    print '<div role="button" class="col-xs-3 col-sm-3 col-md-3 text-right hide_options" data-toggle="tooltip" title="'.JText::_("COM_BIODIV_USERDASHBOARD_HIDE").'"><h4><i class="fa fa-angle-up fa-lg"></i></h4></div>';
	print '</div>';
	print '</div>'; // panel-heading
	
	print '<div class="panel-body panel_options">';
	
	print '<div class="list-group btn-group-vertical btn-block" role="group" aria-label="Likes Buttons">';
	
	print '<button id="all_my_likes" type="button" class="list-group-item btn btn-block" style="white-space: normal;">';	
	print '<h5>'.JText::_("COM_BIODIV_USERDASHBOARD_ALL_MY_LIKES").'</h5>';
	print '</button>';
	
	print '<button id="my_likes" type="button" class="list-group-item btn btn-block" style="white-space: normal;">';	
	print '<h5>'.JText::_("COM_BIODIV_USERDASHBOARD_MY_LIKES").'</h5>';
	print '</button>';
		
	print '<button id="others_likes" type="button" class="list-group-item btn btn-block" style="white-space: normal;">';	
	print '<h5>'.JText::_("COM_BIODIV_USERDASHBOARD_OTHERS_LIKES").'</h5>';
	print '</button>';

	print '</div>'; // list-group
		
	print '</div>'; // panel-body
	print '</div>'; //panel
	
	
	
	// Reports
	print '<div class="panel panel-default">';
		
	print '<div class="panel-heading">';
  	print '<div class="row">';
    print '<div class="col-xs-9 col-sm-9 col-md-9"><h4>'.JText::_("COM_BIODIV_USERDASHBOARD_REPORTS").'</h4></div>';
    print '<div role="button" class="col-xs-3 col-sm-3 col-md-3 text-right show_options" data-toggle="tooltip" title="'.JText::_("COM_BIODIV_USERDASHBOARD_SHOW").'"><h4><i class="fa fa-angle-down fa-lg"></i></h4></div>';
    print '<div role="button" class="col-xs-3 col-sm-3 col-md-3 text-right hide_options" data-toggle="tooltip" title="'.JText::_("COM_BIODIV_USERDASHBOARD_HIDE").'"><h4><i class="fa fa-angle-up fa-lg"></i></h4></div>';
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
			$tooltipText = ' data-toggle="tooltip" title="' . preg_replace( '/[\W]/', ' ', $this->reportText[$reportType]) . '"';
		}
		print '<button type="button" class="list-group-item btn btn-block report-btn" ' . $tooltipText . ' data-report-type="'.$reportType.'" style="white-space: normal;">';
		
		print '<h5>'.$reportName.'</h5>';
		
		print '</button>';
	}

	print '</div>'; // list-group
	
	print '</div>'; // panel-body
	print '</div>'; //panel
	
	print '</div>'; // panel-group

	print "</div>";	 // col-md-3

	print "<div class='col-md-9'>";
	
	print "<div class='row'>";

	print "<div id='report_display'></div>";

	print "</div>"; // report row

	print "<div class='row'>";

	print "<div id='data_warning' hidden>";
	
	print "<p style='margin-top: 10px'>".JText::_("COM_BIODIV_USERDASHBOARD_DATA_WARN")."</p>";

	print "</div>";

	print "</div>"; // warning row

	print "</div>"; // col-md-9

}

print '<div id="carousel_modal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog"  >';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header">';
print '        <button type="button" class="close" data-dismiss="modal">&times;</button>';
print '        <h4 class="modal-title">'.JText::_("COM_BIODIV_USERDASHBOARD_REVIEW").'</h4>';
print '      </div>';
print '     <div class="modal-body">';
print '	    <div id="media_carousel" ></div>';
print '      </div>';
print '	  <div class="modal-footer">';
print '        <button type="button" class="btn btn-primary" data-dismiss="modal">'.JText::_("COM_BIODIV_USERDASHBOARD_CLOSE").'</button>';
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



