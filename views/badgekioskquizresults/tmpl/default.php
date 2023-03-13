<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

print "<div class='col-md-12'>";
if ( $this->errorMsg ) {
	print '<div class="col-md-12">'.$this->errorMsg.'</div>';
}
else {
	$badgeComplete = false;
	if ( $this->badge > 0  ) {
		if ( $this->badgeResult ) {
			if ( $this->badgeResult->isComplete ) {
				$badgeComplete = true;
				print '<div class="col-md-12 text-center lower_heading"><h2>'. JText::_("COM_BIODIV_BADGEKIOSKQUIZRESULTS_COMPLETED") .'</h2></div>';
			}
			else {
				if ( $this->badgeResult->numAchieved == 1 ) {
					$compStr = "" . $this->badgeResult->numAchieved . " " . JText::_("COM_BIODIV_BADGEKIOSKQUIZRESULTS_QUIZ");
				}
				else {
					$compStr = "" . $this->badgeResult->numAchieved . " " . JText::_("COM_BIODIV_BADGEKIOSKQUIZRESULTS_QUIZZES");
				}
				$toGo = $this->badgeResult->numRequired - $this->badgeResult->numAchieved;
				print '<div class="col-md-12 text-center lower_heading"><h2>'. JText::_("COM_BIODIV_BADGEKIOSKQUIZRESULTS_YOU") .' '
							.$compStr.' '. JText::_("COM_BIODIV_BADGEKIOSKQUIZRESULTS_TOWARDS") .' - '. $toGo . ' ' . 
							JText::_("COM_BIODIV_BADGEKIOSKQUIZRESULTS_TO_GO") . '</h2></div>';
			}
		}
	}
	print '<div class="col-md-12"><h3>'.JText::_("COM_BIODIV_BADGEKIOSKQUIZRESULTS_SCORED").' ' . $this->score . '/' . $this->numQuestions . '</h3></div>';
}
print '</div>'; // col-12

print "<div class='row spaced_row'>";	
print "<div class='col-md-12'>";	
print "<div class='col-md-12'>";	

for ( $i = 0; $i < $this->numQuestions; $i++ ) {
	
	$seq = $this->sequences[$i];
	$mediafile = array_values($seq->getMediaFiles())[0];
	
	$result = $this->results[$i];
	
	$correctSpecies = $result["correctId"];
	$userSpecies = $result["userId"];
	$isCorrect = $result["isCorrect"];
	
	$smile = "<span class='fa fa-smile-o fa-lg text-success'></span>";
	$frown = "<span class='fa fa-frown-o fa-lg text-danger'></span>";
				
	$yn = $frown;
	if ( $isCorrect ) {
		$yn = $smile;
	}
	
	$correctSpeciesName = codes_getOptionTranslation($correctSpecies);
	$userSpeciesName = codes_getOptionTranslation($userSpecies);
	
	
	print "<div class='col-md-3'>";
	print "<div class='well'>";
	
	print "<h4>". $yn . "</h4>";
	if ( $seq->getMedia() == "photo" ) {
		print "<button class='media-btn' data-seq_id='".$seq->getId()."'><img src = '" . $mediafile . "' width='100%'></button>";
	}
	else if ( $seq->getMedia() == "video" ) {
		print "<button class='media-btn' data-seq_id='".$seq->getId()."'><video src = '" . $mediafile . "' width='100%'></video></button>";
	}
	else if ( $seq->getMedia() == "audio" ) {
		print "<button class='media-btn' data-seq_id='".$seq->getId()."'><i class='fa fa-play'></i> " . JText::_("COM_BIODIV_BADGEKIOSKQUIZRESULTS_REVIEW") . "<audio src = '" . $mediafile . "' width='100%'></audio></button>";
	}
	
	print "<div class='row'>";
	
	print "<div class='col-md-6'>";
	
	print "<h5>" . "  " . JText::_("COM_BIODIV_BADGEKIOSKQUIZRESULTS_YOU_SEL") . "</h5>";
	print "<p id='user_" . $seq->getId() . "'>" . $userSpeciesName . "</p>";
	
	print "</div>"; // col 6
	
	print "<div class='col-md-6'>";
	
	print "<h5>" . "  " . JText::_("COM_BIODIV_BADGEKIOSKQUIZRESULTS_EXP_SEL") . "</h5>";
	print "<p id='expert_" . $seq->getId() . "'>" . $correctSpeciesName . "</p>";
	
	print "</div>"; // col 6
	
	print "</div>"; // row
	
	
	print "</div>"; // well
	print "</div>"; // col-2
	
	// Every third sequence start a new row
	if ($i%4 == 3 ) {
		print "</div>";
		print "<div class='row'>";	
	}
	
}

print "</div>"; // col-12
print "</div>"; // col-12
print "</div>"; // results row



print '<div class="col-md-4 col-md-offset-2">';
print '	<button id="badge_quiz_'.$this->badge.'" class="btn btn-lg btn-block btn-success h3 control_btn reloadPage" >'.JText::_("COM_BIODIV_BADGEKIOSKQUIZRESULTS_PLAY_AGAIN").'</button>';
print '</div>';

print '<div class="col-md-4">';
$href = "bes-badges";
if ( $this->classId ) {
	$href .= "?class_id=".$this->classId;
}
print '<a href="'.$href.'">';
if ( $badgeComplete ) {
	$btnText = JText::_("COM_BIODIV_BADGEKIOSKFEEDBACK_COLLECT_BADGE");
}
else {
	$btnText = JText::_("COM_BIODIV_BADGEKIOSKFEEDBACK_BACK_BES");
}
print '	<button class="btn btn-lg btn-block btn-success h3 control_btn badgesBtn" type="submit">'.$btnText.'</button>';
print '</a>';
print '</div>';


// ------------------------------- Review carousel modal --------------------------------------
print '<div id="carousel_modal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog "  style="width: 60%;">';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header">';
print '        <button type="button" class="close" data-dismiss="modal">&times;</button>';
print '        <h4 class="modal-title"> '.JText::_("COM_BIODIV_BADGEKIOSKQUIZRESULTS_REVIEW").'</h4>';
print '      </div>';
print '      <div class="modal-body">';
print '	    <div id="media_carousel" ></div>';
print '      </div>';
print '	  <div class="modal-footer">';
print '        <button type="button" class="btn btn-primary" data-dismiss="modal">'.JText::_("COM_BIODIV_BADGEKIOSKQUIZRESULTS_CLOSE").'</button>';
print '      </div>';
	  	  
print '    </div>';

print '  </div>';
print '</div>';

?>


