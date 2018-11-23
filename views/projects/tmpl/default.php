<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;
?>

<?php

// Check download file:
//$filename = JURI::root()."biodivfiles/projectanimals.csv";
//print '<a href="'.$filename.'" download="animals">Download file</a> ';
//print "<p>".count($this->projects)."</p>";
//print '<div class="row">';
$project_num = 0;
foreach ( $this->projects as $project ) {
	if ( $project_num%4 == 0 ) print '<div class="row">';
	print "<div class='col-md-3 project-col' >";
	$url = projectImageURL($project->project_id);
	//print "image url is " . $url;
	//print '<img alt = "" src="/rhombus/images/tree.jpg" itemprop="thumbnailUrl" width="90%"/>';
	//print '<img alt = "" src="'.$url.'" itemprop="thumbnailUrl" width="90%"/>';
	print '<div class="crop-width">';
	print '<img class="project-col-image cover scale2" alt = "project image" src="'.$url.'" />';
	print '</div>';
	print '<div class="project-title">';
	print '<h2 itemprop="name">';
	print $project->project_prettyname;
	print '</h2>';
	print '</div>';
	// Print the progress bar
	$progress = projectProgress($project->project_id);
	// use a different colour depending on percent complete 
	$progress_bar_type = "progress-bar-success";
	if ( $progress["percentComplete"] < 25 ) $progress_bar_type = "progress-bar-danger";
	else if ( $progress["percentComplete"] < 50 ) $progress_bar_type = "progress-bar-warning";
	else if ( $progress["percentComplete"] < 75 ) $progress_bar_type = "progress-bar-info";
	
	//print "progress bar type = " . $progress_bar_type;
	
	// Add the note for multiple and Single to multiple projects
	$add_note = "";
	$multiple_complete = False;
	if ( $project->priority == "Multiple" or $project->priority == "Single to multiple" ) {
		$add_note = " *";
		if ( $progress["percentComplete"] == 100 ) {
			$multiple_complete = True;
		}
	}
	print '<div class="progress">';
	print '<div class="progress-bar ' . $progress_bar_type . '" role="progressbar" ';
	print 'aria-valuenow="' . $progress["numClassifications"] . '"  aria-valuemin="0" ';
	print 'aria-valuemax="' . $progress["numSequences"] . '" style="width:' . $progress["percentComplete"] . '%">';
	if ( $multiple_complete ) {
		print 'Please keep spotting' . $add_note;
	}
	else {
		print '' . $progress["percentComplete"] . '% Classified' . $add_note;
	}
	print '</div>';
	print '</div>';
	
	print '<div class="project-description">';
	print '<p>'.$project->project_description.'</p>';
	print '</div>';
	
	
	print "<table><tr>";
	print '<td><form action = "';
	print BIODIV_ROOT;
	print '" method = "GET">';
	print "<input type='hidden' name='view' value='projecthome'/>";
	print "<input type='hidden' name='option' value='";
	print BIODIV_COMPONENT;
	print "'/>";
	print "<input type='hidden' name='project_id' value='".$project->project_id."'/>";
	print "<button  class='btn btn-primary btn-projects' type='submit'>More</button>";
	print "</form></td>";
	print "</tr></table>";
	print '<p class="spacer-3em"></p>';
	print '</div>';
	$project_num += 1;
	if ( $project_num%4 == 0 || ($project_num >= count($this->projects) ) ) {
		print '</div>';
		// Add a spacer...
		//print '<div class="spacer-3em">
	}
}
print '<div class="row">';
print "* Please note that the progress bars relate to the number of sequences with <strong>at least one</strong> classification.  The more classifications the better, so if images are available please keep spotting, even if 100% is shown!" ;
print '</div>';


?>

<?php
JHTML::stylesheet("com_biodiv/com_biodiv.css", array(), true);
//JHTML::script("com_biodiv/bootbox.js", true, true);
//JHTML::script("com_biodiv/classify.js", true, true);

?>



