<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;
print '<div id="feedback" class="jumbotron text-center" data-project-img="'.$this->projectImageUrl.'" data-project-id="'.$this->project_id.'" data-user-key="'.$this->user_key.'" >';

print "<h2>Thank you for being a citizen scientist and classifying 5 sequences!</h2>";

//print "<p>Species league table</p>";

if ( $this->all_animals ) {
	print "<div class='row spotted-animals'>";
	print "<h3>You spotted....</h3>";
	print "<div class='col-md-12'>";
	foreach ($this->all_animals as $animal) {
		if ( $animal->png_image ) {
			$imageURL = JURI::root().$animal->png_image;
			print "<img src='".$imageURL."'>";
		}
		else {
			if ( $animal->struc == "bird" ) {
				$imageURL = JURI::root()."/images/thumbnails/OtherBird.png";
				print "<img src='".$imageURL."'>";
			}
			else if ( $animal->name == "Human" or $animal->species == 87 ){
				$imageURL = JURI::root()."/images/thumbnails/Human.png";
				print "<img src='".$imageURL."'>";
			}
			else if ( $animal->name == "Nothing" or $animal->species == 86 ){
				$imageURL = JURI::root()."/images/thumbnails/Nothing.png";
				print "<img src='".$imageURL."'>";
			}
			else {
				$imageURL = JURI::root()."/images/thumbnails/OtherMammal.png";
				print "<img src='".$imageURL."'>";
			}
			
		}
	}
	print "</div>";

	foreach ($this->all_animals as $animal) {
		if ($animal->number > 1 ) {
			print "<div class='col-md-4 text-left'> " . $animal->number . " " . $animal->name . "s</div>" ;
		}
		else {
			print "<div class='col-md-4 text-left'> 1 " . $animal->name . "</div>" ;
		}
	}
	print "</div>";
}


print "<form action = '".BIODIV_ROOT."&".$this->user_key."' method = 'GET'>";
print "    <input type='hidden' name='view' value='startkiosk'/>";
print "    <input type='hidden' name='option' value='".BIODIV_COMPONENT."'/>";
print "    <input type='hidden' name='project_id' value='".$this->project_id."' />";
print "	<button  id='start-again-btn' class='btn btn-danger' type='submit'>Start Again</button>";
print "</form>";


print "</div>";



JHTML::stylesheet("com_biodiv/com_biodiv.css", array(), true);
JHTML::script("com_biodiv/feedback.js", true, true);

?>