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
	print '<a type="button" href="'.JURI::root().'/'.JText::_("COM_BIODIV_PROJECTDASHBOARD_DASH_PAGE").'" class="list-group-item btn btn-block" >'.JText::_("COM_BIODIV_PROJECTDASHBOARD_LOGIN").'</a>';
}
else {
	
	print "<div class='col-md-12'>";

	print '<h1>'.JText::_("COM_BIODIV_PROJECTDASHBOARD_DASH_HEADING").'</h1>';

	print "</div>";
	
	// Check for no admin projects
	if ( count($this->projects) == 0 ) {
		print "<div class='col-md-12'>";

		print '<h3>'.JText::_("COM_BIODIV_PROJECTDASHBOARD_NO_ADMIN").'</h3>';

		print "</div>";

	}
	else {


		print "<div class='col-md-3'>";

		// Create dropdown of projects
		print '<h3>'.JText::_("COM_BIODIV_PROJECTDASHBOARD_PROJECT").'</h3>';

		print "<select id = 'project_select' name = 'projects' class = 'form-control'>";
		

		$isFirst = true;
		$firstProjectId = null;
		foreach($this->projects as $projId=>$projName){
			if ( $isFirst ) {
				// Default to first project
				print "<option value='".$projId."' selected>".$projName."</option>";
				$isFirst = false;
				$firstProjectId = $projId;
			}
			else {
				print "<option value='".$projId."'>".$projName."</option>";
			}
		}

		print "</select>";
		
		
		// View and add users
		print '<h3>'.JText::_("COM_BIODIV_PROJECTDASHBOARD_SETUP").'</h3>';
		
		$tooltipText = ' data-toggle="tooltip" title="'.JText::_("COM_BIODIV_PROJECTDASHBOARD_ADD_USERS").'"';
		print '<button id="projectUsersBtn" type="button" class="list-group-item btn btn-block" ' . $tooltipText . ' style="white-space: normal;">';
			
		print '<h4>'.JText::_("COM_BIODIV_PROJECTDASHBOARD_USERS").'</h4>';
		
		print '</button>';



		// Create list of reports to download/view
		print '<h3>'.JText::_("COM_BIODIV_PROJECTDASHBOARD_REPORTS").'</h3>';
		print '<div class="list-group btn-group-vertical btn-block" role="group" aria-label="Report Buttons">';
		  
		foreach($this->reports as $report){
			
			$reportType = $report[0];
			$reportName = $report[1];
			
			// When page is first loaded 
			
			$tooltipText = "";
			if ( array_key_exists ( $reportType, $this->reportText ) ) {
				//error_log ("Tooltip text = " . $this->reportText[$reportType] );
				$tooltipText = ' data-toggle="tooltip" title="' . preg_replace( '/[\W]/', ' ', $this->reportText[$reportType]) . '"';
			}
			print '<button type="button" class="list-group-item btn btn-block report-btn" ' . $tooltipText . ' data-report-type="'.$reportType.'" style="white-space: normal;">';
			
			print '<h4>'.$reportName.'</h4>';
			
			print '</button>';
		}
		
		// Add the opt in reports by project - display only first
		foreach ( $this->optInReports as $projectId=>$projectReports ) {
			foreach ( $projectReports as $report ) {
				$reportType = $report[0];
				$reportName = $report[1];
				
				$styleText = "white-space: normal;";
				if ( $projectId != $firstProjectId ) {
					$styleText .= " display: none;";
				}
				// When page is first loaded 			
				$tooltipText = "";
				if ( array_key_exists ( $reportType, $this->reportText ) ) {
					//error_log ("Tooltip text = " . $this->reportText[$reportType] );
					$tooltipText = ' data-toggle="tooltip" title="' . preg_replace( '/[\W]/', ' ', $this->reportText[$reportType]) . '"';
				}
				print '<button type="button" class="list-group-item btn btn-block report-btn" ' . $tooltipText . ' data-report-type="'.$reportType.'"  data-project_id="'.$projectId.'" style="'.$styleText.'">';
				
				print '<h4>'.$reportName.'</h4>';
				
				print '</button>';
			}
		}

		print '</div>'; // list-group

		print "</div>";	 // col-md-3

		print "<div class='col-md-9'>";

		//print "<h3>Report displays here</h3>";

		print "<div id='report_display'>";

		print "</div>";

		print "</div>"; // col-md-9
	}
}

print '<div id="carousel_modal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog ">';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header">';
print '        <button type="button" class="close" data-dismiss="modal">&times;</button>';
print '        <h4 class="modal-title">'.JText::_("COM_BIODIV_PROJECTDASHBOARD_REVIEW").'</h4>';
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
JHTML::script("com_biodiv/commonbiodiv.js", true, true);
JHTML::script("com_biodiv/projectdashboard.js", true, true);
JHTML::script("com_biodiv/mediacarousel.js", true, true);
JHTML::script("com_biodiv/report.js", true, true);
?>



