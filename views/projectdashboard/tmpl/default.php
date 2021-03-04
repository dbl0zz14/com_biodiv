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
	
	print "<div class='col-md-12'>";

	print '<h1>'.$this->translations['dash_heading']['translation_text'].'</h1>';

	print "</div>";
	
	// Check for no admin projects
	error_log ( "Num projects = " . count($this->projects) );
	if ( count($this->projects) == 0 ) {
		print "<div class='col-md-12'>";

		print '<h3>'.$this->translations['no_admin']['translation_text'].'</h3>';

		print "</div>";

	}
	else {


		print "<div class='col-md-3'>";

		// Create dropdown of projects
		print '<h3>'.$this->translations['project']['translation_text'].'</h3>';

		//print "<label for='projects'>".$this->translations['project']['translation_text']."</label>";
		print "<select id = 'project_select' name = 'projects' class = 'form-control'>";
		//print "  <option value='' disabled selected hidden>".$this->translations['sel_proj']['translation_text']."...</option>";


		$isFirst = true;
		foreach($this->projects as $row){
			if ( $isFirst ) {
				// Default to first project
				print "<option value='".$row['project_id']."' selected>".$row['project_name']."</option>";
				$isFirst = false;
			}
			else {
				print "<option value='".$row['project_id']."'>".$row['project_name']."</option>";
			}
		}

		print "</select>";



		// Create list of reports to download/view
		print '<h3>'.$this->translations['reports']['translation_text'].'</h3>';
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
			
			print '<h4>'.$reportName.'</h4>';
			//print '<div>' . $this->reportArticles[$reportType]->introtext . '</div>';
			print '</button>';
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

JHTML::stylesheet("com_biodiv/com_biodiv.css", array(), true);
JHTML::script("com_biodiv/projectdashboard.js", true, true);
?>



