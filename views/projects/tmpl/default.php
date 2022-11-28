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

$project_num = 0;
foreach ( $this->projects as $project ) {
	if ( $project_num%4 == 0 ) print '<div class="row">';
	print "<div class='col-md-3 project-col' >";
	$url = projectImageURL($project->project_id);
	
	print '<form action = "';
	print BIODIV_ROOT;
	print '" method = "GET">';
	print "<input type='hidden' name='view' value='projecthome'/>";
	print "<input type='hidden' name='option' value='";
	print BIODIV_COMPONENT;
	print "'/>";
	print "<input type='hidden' name='project_id' value='".$project->project_id."'/>";
	print "<button class='image-btn project-btn' type='submit' data-tooltip='".JText::_("COM_BIODIV_PROJECTS_TOOLTIP")."'><div class='crop-width'><img class='project-col-image cover scale2' alt = 'project image' src='".$url."' /></div></button>";
	print "</form>";
	
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
		print JText::_("COM_BIODIV_PROJECTS_KEEP_SPOT") . $add_note;
	}
	else {
		print '' . $progress["percentComplete"] . JText::_("COM_BIODIV_PROJECTS_FRAC_CLASS") . $add_note;
	}
	print '</div>';
	print '</div>';
	
	print '<div class="project-description">';
	// If there is an article available, use the introtext here, getting the correct language in the process.
	if ( $project->article_id ) {
		// Get the associated article (ie depending which langage we are in)
		$assoc_id = getAssociatedArticleId($project->article_id);
		$article = JTable::getInstance("content");
		$article->load($assoc_id); 
		
		// Truncate in case its long..
		$project->project_description = substr($article->introtext, 0, 315);
	}
	print '<p>'.$project->project_description.'</p>';
	print '</div>';
	
	
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
print "* " . JText::_("COM_BIODIV_PROJECTS_PROJ_NOTE") ;
print '</div>';


?>

<?php
JHTML::stylesheet("com_biodiv/com_biodiv.css", array(), true);
JHTML::script("com_biodiv/project.js", true, true);
?>



