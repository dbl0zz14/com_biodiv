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

	$num_sequences = count($this->sequences);

	print "<h4 class='modal-title' id='resultsTitle'>".$this->translations['you_scored']['translation_text']." " . $this->score . "/" . $this->totalSpecies . " </h4>";
	print "<p></p>";
	
	print "<div class='row'>";	
	
	for ( $i = 0; $i < $num_sequences; $i++ ) {
		$seq = $this->sequences[$i];
		$mediafile = array_values($seq->getMediaFiles())[0];
		$correct = $seq->getSpecies();
		
		$useranimals = array();
		if ( count($this->classifications) > $i) {
			$useranimals = $this->classifications[$i];
		}
		
		/*
		$tick = "<button type='button' class='right btn-danger' ><span class='fa fa-check'></span></button>";
		$cross = "<button type='button' class='right btn-primary' ><span class='fa fa-close'></span></button>";
		*/
		$smile = "<span class='fa fa-smile-o fa-lg text-danger'></span>";
		$frown = "<span class='fa fa-frown-o fa-lg text-danger'></span>";
		$meh = "<span class='fa fa-meh-o fa-lg text-danger'></span>";
		
				
		$yn = $frown;
		$isCorrect = $this->marks[$i] >= 1;
		if ( $isCorrect ) {
			$yn = $meh;
		}
		$allCorrect = ($this->wrong[$i] == 0) && ($this->marks[$i] == count($correct));
		if ( $allCorrect ) {
			$yn = $smile;
		}
		
		$correct_names = array();
		foreach ($correct as $animal) {
			$animal_id = $animal->id;
			$animal_name = codes_getOptionTranslation($animal_id);
			if ( $this->detail && $animal_id != 86 && $animal_id != 87 ) {
				$animal_name .= " (" . $animal->number;
				$animal_age = $animal->age;
				if ( $animal_age != 85 ) $animal_name .= " " . codes_getOptionTranslation($animal_age);
				
				$animal_gender = $animal->gender;
				if ( $animal_gender != 84 ) $animal_name .= " " . codes_getOptionTranslation($animal_gender);
				$animal_name .= ")";
			}
			$correct_names[] = $animal_name;
		}
		
		$user_names = array();
		foreach ($useranimals as $animal) {
			$animal_id = $animal->id;
			$user_name = codes_getOptionTranslation($animal_id);
			if ( $this->detail && $animal_id != 86 && $animal_id != 87 ) {
				$user_name .= " (" . $animal->number;
				$animal_age = $animal->age;
				if ( $animal_age != 85 ) $user_name .= " " . codes_getOptionTranslation($animal_age);
				
				$animal_gender = $animal->gender;
				if ( $animal_gender != 84 ) $user_name .= " " . codes_getOptionTranslation($animal_gender);
				$user_name .= ")";
			}
			
			$user_names[] = $user_name;
		}
		
		// Sort the species
		sort($user_names);
		sort($correct_names);
		
		print "<div class='col-md-4'>";
		print "<div class='well'>";
		/* try a different display
		print "<h4>". $yn . "  " . $this->translations['you_sel']['translation_text'] . "</h4><p id='user_" . $seq->getId() . "'>" . implode(', ', $user_names) . "</p>";
		
		if ( $seq->getMedia() == "photo" ) {
			print "<button class='media-btn' data-seq_id='".$seq->getId()."'><img src = '" . $mediafile . "' width='100%'></button>";
		}
		else if ( $seq->getMedia() == "video" ) {
			print "<button class='media-btn' data-seq_id='".$seq->getId()."'><video src = '" . $mediafile . "' width='100%'></video></button>";
		}
		else if ( $seq->getMedia() == "audio" ) {
			print "<button class='media-btn' data-seq_id='".$seq->getId()."'><audio src = '" . $mediafile . "' width='100%'></audio></button>";
		}
		
		print "<h4>". $this->translations['exp_sel']['translation_text'] . "</h4><p id='expert_" . $seq->getId() . "'>" . implode(', ', $correct_names) . "</p>";
		
		if ( !$allCorrect ) {
			print "<button class='btn btn-primary challenge-btn' id='challengeBtn_" . $seq->getId() . "' data-seq_id='".$seq->getId()."'>" . $this->translations['challenge']['translation_text'] . "</button>";
			print "<div id='challengeDone_" . $seq->getId() . "'></div>";
		}
		*/
		
		print "<h4>". $yn . "</h4>";
		if ( $seq->getMedia() == "photo" ) {
			print "<button class='media-btn' data-seq_id='".$seq->getId()."'><img src = '" . $mediafile . "' width='100%'></button>";
		}
		else if ( $seq->getMedia() == "video" ) {
			print "<button class='media-btn' data-seq_id='".$seq->getId()."'><video src = '" . $mediafile . "' width='100%'></video></button>";
		}
		else if ( $seq->getMedia() == "audio" ) {
			print "<button class='media-btn' data-seq_id='".$seq->getId()."'><audio src = '" . $mediafile . "' width='100%'></audio></button>";
		}
		
		print "<div class='row'>";
		
		print "<div class='col-md-6'>";
		
		print "<h4>" . "  " . $this->translations['you_sel']['translation_text'] . "</h4>";
		print "<p id='user_" . $seq->getId() . "'>" . implode(', <br>', $user_names) . "</p>";
		
		print "</div>"; // col 6
		
		print "<div class='col-md-6'>";
		
		print "<h4>" . "  " . $this->translations['exp_sel']['translation_text'] . "</h4>";
		print "<p id='expert_" . $seq->getId() . "'>" . implode(', <br>', $correct_names) . "</p>";
		
		print "</div>"; // col 6
		
		print "</div>"; // row
		
		if ( !$allCorrect ) {
			print "<hr>";
			print "<button class='btn btn-primary challenge-btn' id='challengeBtn_" . $seq->getId() . "' data-seq_id='".$seq->getId()."'>" . $this->translations['challenge']['translation_text'] . "</button>";
			print "<div id='challengeDone_" . $seq->getId() . "'></div>";
		}
		
		print "</div>"; // well
		print "</div>"; // col-3
		
		// Every third sequence start a new row
		if ($i%3 == 2 ) {
			print "<div class='row'>";	
			print "</div>";
		}
		
	}
	print "</div>"; // results row
	
		
	print "<div>";	// Finish div
	print "<p></p>";
	
	print "<div class='col-md-3'>";
	print "	      <form action = '".BIODIV_ROOT."' method = 'GET'>";
	print "		  <input type='hidden' name='view' value='training'/>";
	print "		  <input type='hidden' name='option' value='".BIODIV_COMPONENT."'/>";
	print "		  <button class='btn btn-danger btn-lg' type='submit' >".
				  $this->translations["finish"]["translation_text"]."</button>";
	print "		  </form>";
	print "</div>"; // col-3
	
	print "<div class='col-md-3'>";
	print "	      <form action = '".BIODIV_ROOT."' method = 'GET'>";
	print "		  <input type='hidden' name='view' value='status'/>";
	print "		  <input type='hidden' name='option' value='".BIODIV_COMPONENT."'/>";
	print "		  <button class='btn btn-danger btn-lg' type='submit' >".
				  $this->translations["spot"]["translation_text"]."</button>";
	print "		  </form>";
	print "</div>"; // col-3
	

	print "</div>"; // finish



?>
<div id="carousel_modal" class="modal fade" role="dialog">
  <div class="modal-dialog "  style='width: 60%;'>

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"> <?php print $this->translations['review']['translation_text']; ?> </h4>
      </div>
      <div class="modal-body">
	    <div id="media_carousel" ></div>
      </div>
	  <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
      </div>
	  	  
    </div>

  </div>
</div>

<div id="challenge_modal" class="modal fade" role="dialog">
  <div class="modal-dialog "  style='width: 60%;'>

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"> <?php print $this->translations['challenge']['translation_text']; ?> </h4>
      </div>
      <div class="modal-body">
		
		<form id='challengeForm' role='form'>
		
		<input id='currSequenceId' type='hidden' name='sequence_id' value=''/>
		
		<div class="form-group">
		<label for="challenge_expert"><?php print $this->translations['expert']['translation_text']; ?></label>
		<textarea class="form-control" id="challenge_expert" name="expert_species" rows="2" maxlength='200' readonly></textarea>
		</div>
		
		<div class="form-group">
		<label for="challenge_suggestion"><?php print $this->translations['suggest']['translation_text']; ?></label>
		<textarea class="form-control" id="challenge_suggestion" name="user_species" rows="2" maxlength='200' ></textarea>
		</div>
		
		<div class="form-group">
		<label for="challenge_notes"><?php print $this->translations['notes']['translation_text']; ?></label>
		<textarea class="form-control" id="challenge_notes" name="notes" rows="2" maxlength='200' ></textarea>
		</div>
		
      </div>
	  <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php print $this->translations['cancel']['translation_text']; ?></button>
		<button type='button' class='btn btn-primary' data-dismiss="modal" id='challenge-save'><?php print $this->translations['submit']['translation_text']?></button>
	    </form>
      </div>
	  	  
    </div>

  </div>
</div>

<div id="map_modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"> <?php print $this->translations['review']['translation_text']; ?> </h4>
      </div>
      <div class="modal-body">
	    <div id="no_map"><h5> <?php print $this->translations['no_map']['translation_text']; ?> </h5></div>
        <div id="map_canvas" style="width:500px;height:500px;"></div>
      </div>
	  <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
      </div>
	  	  
    </div>

  </div>
</div>


<?php


JHTML::stylesheet("com_biodiv/com_biodiv.css", array(), true);
JHTML::script("com_biodiv/trainingresults.js", true, true);
?>



