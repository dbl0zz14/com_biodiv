<?php

use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\CMS\HTML\HTMLHelper;
use MammalWeb\Component\Biodiv\Site\Library\Core\Biodiv;

 
// No direct access to this file
defined('_JEXEC') or die;

$this->document->getWebAssetManager()->usePreset('com_biodiv.projects');

HTMLHelper::_('bootstrap.modal', '.selector', []);

$projectsModel = $this->getModel();

$project_num = 0;
foreach ( $this->projects as $project ) {
	if ( $project_num == 0 ) print '<div class="row top-row">';
	else if ( $project_num%4 == 0 ) print '<div class="row">';
	print "<div class='col-md-3 project-col' >";
	$url = $projectsModel->projectImageURL($project->project_id);
	
	print '<form action = "';
	print BIODIV_ROOT;
	print '" method = "GET">';
	print "<input type='hidden' name='view' value='projecthome'/>";
	print "<input type='hidden' name='option' value='";
	print BIODIV_COMPONENT;
	print "'/>";
	print "<input type='hidden' name='project_id' value='".$project->project_id."'/>";
	print "<button class='image-btn project-btn' type='submit' data-bs-toggle='tooltip' title='".JText::_("COM_BIODIV_PROJECT_TOOLTIP")."'><div class='crop-width'><img class='project-col-image cover scale2' alt = 'project image' src='".$url."' /></div></button>";
	print "</form>";
	
	print '<div class="project-title">';
	print '<h2 itemprop="name">';
	print $project->project_prettyname;
	print '</h2>';
	print '</div>';
	
	// Print the progress bar
	$progress = $projectsModel->projectProgress($project->project_id);
	
	$numSequences = $progress["numSequences"];
	$percentQueued = 0;
	$percentScreenedOut = 0;
	$percentFullyClassified = 0;
	$PartClassified = 0;
	if ( $numSequences > 0 ) {
		$percentQueued = 100*$progress["queued"]/$numSequences;
		$percentScreenedOut = 100*$progress["screenedOut"]/$numSequences;
		$percentFullyClassified = 100*$progress["fullyClassified"]/$numSequences;
		$percentPartClassified = 100*$progress["partClassified"]/$numSequences;
		
	}
	
	$numQueuedTooltip = '' . $progress["queued"] . ' ' . Text::_("COM_BIODIV_PROJECT_QUEUED_LC");
	$numScreenedOutTooltip = '' . $progress["screenedOut"] . ' ' . Text::_("COM_BIODIV_PROJECT_SCREENED_LC");
	$numPartClassdTooltip = '' . $progress["partClassified"] . ' ' . Text::_("COM_BIODIV_PROJECT_PART_CLASSIFIED_LC");
	$numFullyClassdTooltip = '' . $progress["fullyClassified"] . ' ' . Text::_("COM_BIODIV_PROJECT_FULLY_CLASSIFIED_LC");
	$numSequencesTooltip = '' . $numSequences . ' ' . Text::_("COM_BIODIV_PROJECT_NUM_SEQUENCES_LC");
	if ( $progress["queued"] > 1000 ) {
		
		$numQueuedTooltip = '' . (int)($progress["queued"]/1000) . 'K ' . Text::_("COM_BIODIV_PROJECT_QUEUED_LC");
		
	}
	if ( $progress["screenedOut"] > 1000 ) {
		
		$numScreenedOutTooltip = '' . (int)($progress["screenedOut"]/1000) . 'K ' . Text::_("COM_BIODIV_PROJECT_SCREENED_LC");
		
	}
	if ( $progress["partClassified"] > 1000 ) {
		
		$numPartClassdTooltip = '' . (int)($progress["partClassified"]/1000) . 'K ' . Text::_("COM_BIODIV_PROJECT_PART_CLASSIFIED_LC");
		
	}
	if ( $progress["fullyClassified"] > 1000 ) {
		
		$numFullyClassdTooltip = '' . (int)($progress["fullyClassified"]/1000) . 'K ' . Text::_("COM_BIODIV_PROJECT_FULLY_CLASSIFIED_LC");
		
	}
	if ( $numSequences > 1000 ) {
		
		$numSequencesTooltip = '' . (int)($numSequences/1000) . 'K ' . Text::_("COM_BIODIV_PROJECT_NUM_SEQUENCES_LC");
		
	}
	
	print '<div class="progress" data-bs-toggle="tooltip" title="'.$numSequencesTooltip.'">';
		
	if ( !$numSequences ) {
		print '<div class="progress-bar progress-bar-queued" role="progressbar" ';
		print 'aria-valuenow="0"  aria-valuemin="0" ';
		print 'aria-valuemax="100" style="width:0%">';
		print '</div>';
	}
	else {
		print '<div class="progress-bar progress-bar-screenedout" role="progressbar" ';
		print 'data-bs-toggle="tooltip" title="'.$numScreenedOutTooltip.'"';
		print 'aria-valuenow="' . $progress["screenedOut"] . '"  aria-valuemin="0" ';
		print 'aria-valuemax="' . $progress["numSequences"] . '" style="width:' . $percentScreenedOut . '%">';
		if ( $percentScreenedOut > 50 ) {
			print '' . (int)$percentScreenedOut . '% ' . Text::_("COM_BIODIV_PROJECT_SCREENED_LC");
		}
		else if ( $percentScreenedOut > 20 ) {
			print '' . (int)$percentScreenedOut . '%';
		}
		print '</div>';
		
		print '<div class="progress-bar progress-bar-fullyclassed" role="progressbar" ';
		print 'data-bs-toggle="tooltip" title="'.$numFullyClassdTooltip.'"';
		print 'aria-valuenow="' . $progress["fullyClassified"] . '"  aria-valuemin="0" ';
		print 'aria-valuemax="' . $progress["numSequences"] . '" style="width:' . $percentFullyClassified . '%">';
		if ( $percentFullyClassified > 50 ) {
			print '' . (int)$percentFullyClassified . '% ' .Text::_("COM_BIODIV_PROJECT_FULLY_CLASSIFIED_LC");
		}
		else if ( $percentFullyClassified > 20 ) {
			print '' . (int)$percentFullyClassified . '%';
		}
		print '</div>';
		
		print '<div class="progress-bar progress-bar-queued" role="progressbar" ';
		print 'data-bs-toggle="tooltip" title="'.$numQueuedTooltip.'"';
		print 'aria-valuenow="' . $progress["queued"] . '"  aria-valuemin="0" ';
		print 'aria-valuemax="' . $progress["numSequences"] . '" style="width:' . $percentQueued . '%">';
		if ( $percentQueued > 50 ) {
			print '' . (int)$percentQueued . '% ' . Text::_("COM_BIODIV_PROJECT_QUEUED_LC");
		}
		else if ( $percentQueued > 20 ) {
			print '' . (int)$percentQueued . '%';
		}
		print '</div>';
		
		print '<div class="progress-bar progress-bar-striped progress-bar-partclassed" role="progressbar" ';
		print 'data-bs-toggle="tooltip" title="'.$numPartClassdTooltip.'"';
		print 'aria-valuenow="' . $progress["partClassified"] . '"  aria-valuemin="0" ';
		print 'aria-valuemax="' . $progress["numSequences"] . '" style="width:' . $percentPartClassified . '%">';
		if ( $percentPartClassified > 50 ) {
			print '' . (int)$percentPartClassified . '% ' . Text::_("COM_BIODIV_PROJECT_PART_CLASSIFIED_LC");
		}
		else if ( $percentPartClassified > 20 ) {
			print '' . (int)$percentPartClassified . '%';
		}
		print '</div>';
		
	}
	
	print '</div>';
	
	print '<div class="project-description">';
	// If there is an article available, use the introtext here, getting the correct language in the process.
	if ( $project->article_id ) {
		// Get the associated article (ie depending which langage we are in)
		$assoc_id = $projectsModel->getAssociatedArticleId($project->article_id);
		$article = Table::getInstance("content");
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
//print "* " . Text::_("COM_BIODIV_PROJECT_PROJ_NOTE") ;
//print Text::_("COM_BIODIV_PROJECT_PROJ_KEY");

print '</div>';


?>

<?php
// JHTML::stylesheet("com_biodiv/com_biodiv.css", array(), true);
// JHTML::script("com_biodiv/project.js", true, true);
?>



