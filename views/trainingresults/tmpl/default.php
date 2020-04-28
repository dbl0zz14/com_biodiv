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

	print "<h4 class='modal-title' id='resultsTitle'>".$this->translations['you_scored']['translation_text']." " . $this->score . "/" . $num_sequences . " </h4>";
	print "<p></p>";
	
	print "<div class='row'>";	
	
	for ( $i = 0; $i < $num_sequences; $i++ ) {
		$seq = $this->sequences[$i];
		$mediafile = array_values($seq->getMediaFiles())[0];
		$correct = $seq->getSpecies();
		
		$useranimals = array();
		if ( count($this->classifications) > $i+1) {
			$useranimals = $this->classifications[$i];
		}
		
		$tick = "<button type='button' class='right btn-danger' ><span class='fa fa-check'></span></button>";
		$cross = "<button type='button' class='right btn-primary' ><span class='fa fa-close'></span></button>";
				
		$yn = $cross;
		$isCorrect = $this->marks[$i] == 1;
		if ( $isCorrect ) {
			$yn = $tick;
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
		
		print "<div class='col-md-3'>";
		print "<div class='well'>";
		
		print "<h5>". $yn . " " . implode(', ', $user_names) . "</h5>";
		
		if ( $seq->getMedia() == "photo" ) {
			print "<img src = '" . $mediafile . "' width='100%'>";
		}
		else if ( $seq->getMedia() == "video" ) {
			print "<video src = '" . $mediafile . "' width='100%'></video>";
		}
		else if ( $seq->getMedia() == "audio" ) {
			print "<audio src = '" . $mediafile . "' width='100%'></audio>";
		}
		if ( !$isCorrect ) {
			print "<h5>". $this->translations['exp_sel']['translation_text'] . implode(', ', $correct_names) . "</h5>";
		}
		print "</div>"; // well
		print "</div>"; // col-3
		
		// Every fourth sequence start a new row
		if ($i%4 == 3 ) {
			print "<div class='row'>";	
			print "</div>";
		}
		
	}
	print "</div>"; // results row
	
		
	print "<div>";	// Finish div
	print "<p></p>";
	
	print "<div class='col-md-4'>";
	print "	      <form action = '".BIODIV_ROOT."' method = 'GET'>";
	print "		  <input type='hidden' name='view' value='training'/>";
	print "		  <input type='hidden' name='option' value='".BIODIV_COMPONENT."'/>";
	print "		  <button class='btn btn-danger btn-lg' type='submit' >".
				  $this->translations["finish"]["translation_text"]."</button>";
	print "		  </form>";
	print "</div>"; // col-4
	
	

	print "</div>"; // finish

?>




<?php
JHTML::stylesheet("com_biodiv/com_biodiv.css", array(), true);

?>



