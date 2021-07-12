<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;


print '<div class="col-md-12">';
print '<div class="col-md-12">';

print '<h1 class="text-center lower_heading"><strong>'.$this->translations['thankyou']['translation_text'] . '</strong></h1>';
	

if ( $this->all_animals ) {
	
	print '<h2 class="text-center classify_heading">'.$this->translations['you_spotted']['translation_text'] . '</h2>';
	
		
	print "<div class='row spaced_row'>";
	
	print '<div class="row">';
	
	
	print "<div class='col-md-1'></div>"; // offset
	
	
	foreach ($this->all_animals as $animal) {
		
		print "<div class='col-md-2'>";
		
		$longSpeciesNameClass = 'h4';
		if ( strlen($animal->name) > 13 ) $longSpeciesNameClass = 'long_species_name';
		
		print '<h3 class="text-center"><div class=" '.$longSpeciesNameClass.'">'.$animal->name.'</div></h3>';
		
		/*
		$longSpeciesNameClass = 'h4';
		if ( strlen($animal->name) > 13 ) $longSpeciesNameClass = 'long_species_name';
		print "<div class='text-center " . $longSpeciesNameClass . "'>" . $animal->name . "</div>" ;
		*/
		print "</div>";
	}
	
	print "</div>"; // row
	
	
	print '<div class="row">';
	
	print "<div class='col-md-1'></div>"; // offset
		
	foreach ($this->all_animals as $animal) {
		
		print "<div class='col-md-2'>";
		
		/*
		$longSpeciesNameClass = 'h4';
		if ( strlen($animal->name) > 13 ) $longSpeciesNameClass = 'long_species_name';
		
		print '<h3 class="text-center"><div class=" '.$longSpeciesNameClass.'">'.$animal->name.'</div></h3>';
		
		*/
		
		$imageURL = "";
		
		if ( $animal->kiosk_image ) {
			$imageURL = JURI::root().$animal->kiosk_image;
			//print "<img src='".$imageURL."' width='100%'>";
		}
		else {
			if ( $animal->struc == "bird" ) {
				$imageURL = JURI::root()."images/thumbnails/OtherBird.png";
				//print "<img src='".$imageURL."' width='100%'>";
			}
			else if ( $animal->name == "Human" or $animal->species == 87 ){
				$imageURL = JURI::root()."images/thumbnails/Human.png";
				//print "<img src='".$imageURL."' width='100%'>";
			}
			else if ( $animal->name == "Nothing" or $animal->species == 86 ){
				$imageURL = JURI::root()."images/thumbnails/Undergrowth.jpg";
				//print "<img src='".$imageURL."' width='100%'>";
			}
			else if ( $animal->name == "Don't Know" or $animal->species == 96 ){
				$imageURL = JURI::root()."images/thumbnails/DontKnow.png";
				//print "<img src='".$imageURL."' width='100%'>";
			}
			else {
				$imageURL = JURI::root()."images/thumbnails/Fur.jpg";
				//print "<img src='".$imageURL."' width='100%'>";
			}
		}
		print '<img class="img-responsive center-block" style="max-height:48vh;" src="' . $imageURL . '" />';
		
		print '</div>'; // col-2
		
	}
	
	print '</div>'; // row spaced_row
	
	//print "</div>"; // row spotted-animals
	
	
}

print '<h2 class="text-center" style="margin-top:5vh;">'.$this->translations['help_sci']['translation_text'] . '</h1>';

//print "</div>"; // row
//print '</div>'; // opaque-bg

print '<div class="col-md-4 col-md-offset-2">';
print '	<button id="classify_again" class="btn btn-lg btn-block btn-success h2 control_btn" >'.$this->translations['classify_again']['translation_text'].'</button>';
print '</div>';

print '<div class="col-md-4">';
print '	<button class="btn btn-lg btn-block btn-success h2 control_btn back_to_home" >'.$this->translations['home']['translation_text'].'</button>';
print '</div>';

print '</div>'; // col-12
print '</div>'; // col-12



?>