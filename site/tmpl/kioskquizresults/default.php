<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;


print '<div class="row">';
if ( $this->errorMsg ) {
	print '<div class="col-md-12">'.$this->errorMsg.'</div>';
}
else {
	print '<div class="col-md-12 text-center lower_heading"><h1>You scored ' . $this->score . '/' . $this->numQuestions . '</h1></div>';
}
print '</div>';


print "<div class='row p-5'>";	

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
	print "<div class='card'>";
	print "<div class='card-body'>";
	
	print "<h4>". $yn . "</h4>";
	if ( $seq->getMedia() == "photo" ) {
		print "<button class='media-btn' data-seq_id='".$seq->getId()."'><img src = '" . $mediafile . "' width='100%'></button>";
	}
	else if ( $seq->getMedia() == "video" ) {
		print "<button class='media-btn' data-seq_id='".$seq->getId()."'><video src = '" . $mediafile . "' width='100%'></video></button>";
	}
	else if ( $seq->getMedia() == "audio" ) {
		print "<button class='media-btn' data-seq_id='".$seq->getId()."'><i class='fa fa-play'></i> " . Text::_("COM_BIODIV_KIOSKQUIZRESULTS_REVIEW") . "<audio src = '" . $mediafile . "' width='100%'></audio></button>";
	}
	
	print "<div class='row pt-3'>";
	
	print "<div class='col-md-6'>";
	
	print "<h5>" . "  " . Text::_("COM_BIODIV_KIOSKQUIZRESULTS_YOU_SEL") . "</h5>";
	print "<p id='user_" . $seq->getId() . "'>" . $userSpeciesName . "</p>";
	
	print "</div>"; // col 6
	
	print "<div class='col-md-6'>";
	
	print "<h5>" . "  " . Text::_("COM_BIODIV_KIOSKQUIZRESULTS_EXP_SEL") . "</h5>";
	print "<p id='expert_" . $seq->getId() . "'>" . $correctSpeciesName . "</p>";
	
	print "</div>"; // col 6
	
	print "</div>"; // row
	
	
	print "</div>"; // card-body
	print "</div>"; // card
	print "</div>"; // col-2
	
	// Every third sequence start a new row
	if ($i%4 == 3 and $i < $this->numQuestions-1) {
		print '</div>';
		print '<div class="row p-5">';	
	}
	
}

print "</div>"; // row

print '<div class="d-flex justify-content-evenly">';
print '	<button id="play_again" class="btn btn-lg btn-block btn-primary h3 control_btn" >'.Text::_("COM_BIODIV_KIOSKQUIZRESULTS_PLAY_AGAIN").'</button>';
print '	<button class="btn btn-lg btn-block btn-primary h3 control_btn back_to_home" >'.Text::_("COM_BIODIV_KIOSKQUIZRESULTS_BACK_HOME").'</button>';
print '</div>';

// ------------------------------- Review carousel modal --------------------------------------
print '<div id="carousel_modal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog "  style="width: 60%;">';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header">';
print '        <button type="button" class="close" data-bs-dismiss="modal">&times;</button>';
print '        <h4 class="modal-title"> '.Text::_("COM_BIODIV_KIOSKQUIZRESULTS_REVIEW").'</h4>';
print '      </div>';
print '      <div class="modal-body">';
print '	    <div id="media_carousel" ></div>';
print '      </div>';
print '	  <div class="modal-footer">';
print '        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">'.Text::_("COM_BIODIV_KIOSKQUIZRESULTS_CLOSE").'</button>';
print '      </div>';
	  	  
print '    </div>';

print '  </div>';
print '</div>';

?>


