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
print '<h1 class="text-center lower_heading"><strong>'.JText::_("COM_BIODIV_BADGEKIOSKFEEDBACK_THANKYOU") . '</strong></h1>';

$badgeComplete = false;
if ( $this->badge > 0  ) {
	if ( $this->badgeResult ) {
		
		if ( $this->badgeResult->isComplete ) {
			$badgeComplete = true;
			print '<div class="col-md-12 text-center"><h2>'. JText::_("COM_BIODIV_BADGEKIOSKFEEDBACK_COMPLETED") .'</h2></div>';
		}
		else {
			if ( $this->badgeResult->numAchieved == 1 ) {
				$compStr = "" . $this->badgeResult->numAchieved . " " . JText::_("COM_BIODIV_BADGEKIOSKFEEDBACK_CLASSN_".$this->badge) ;
			}
			else {
				$compStr = "" . $this->badgeResult->numAchieved . " " . JText::_("COM_BIODIV_BADGEKIOSKFEEDBACK_CLASSNS_".$this->badge) ;
			}
			$toGo = $this->badgeResult->numRequired - $this->badgeResult->numAchieved;
			print '<div class="col-md-12 text-center spaced_row"><h2>'. JText::_("COM_BIODIV_BADGEKIOSKFEEDBACK_YOU_".$this->badge) .' '
							.$compStr.' '. JText::_("COM_BIODIV_BADGEKIOSKFEEDBACK_TOWARDS_".$this->badge) .' - '. $toGo . ' ' . 
							JText::_("COM_BIODIV_BADGEKIOSKFEEDBACK_TO_GO"). '</h2></div>';
		}
	}
}
	
if ( $this->all_animals ) {
	
	$numAnimals = count($this->all_animals);
	
	$maxRows = 2;
	$numPerRow = 5;
	
	if ( $numAnimals > 10 and $numAnimals <= 12 ) {
		$numPerRow = 6;
	}
	else if ( $numAnimals > 12 ) {
		$numPerRow = 10;
	}
	
	$numFullRows = intval($numAnimals/$numPerRow);
	//error_log ( "num full rows: " . $numFullRows );
	
	$numOnPartRow = $numAnimals%$numPerRow;
	//error_log ( "num on part row: " . $numOnPartRow );
	
	$extraRow = 0;
	if ( $numOnPartRow > 0 ) $extraRow = 1;
	$numRows = $numFullRows + $extraRow;
	//error_log ( "num rows altogether: " . $numRows );
	
	// Only display up to max rows so displays ok
	if ( $numRows > $maxRows ) $numRows = $maxRows;
	
		
	if ( $numRows == 1 ) {
		print '<h2 class="text-center classify_heading">'.JText::_("COM_BIODIV_KIOSKFEEDBACK_YOU_SPOTTED") . '</h2>';
		
		print "<div class='row spaced_row'>";
	}
	else if ( $numPerRow > 6 ) {
		print '<h2 class="text-center classify_heading">'.JText::_("COM_BIODIV_KIOSKFEEDBACK_YOU_SPOTTED") . '</h2>';
		
		//print "<div class='row half_spaced_row'>";
		//print '</div>'; // row spaced_row
	}
	else {
		print '<h2 class="text-center">'.JText::_("COM_BIODIV_KIOSKFEEDBACK_YOU_SPOTTED") . '</h2>';
	}
		
	for ( $i=0; $i<$numRows; $i++ ) {
		
		//error_log ( "row " . $i );
		
		$animalsLeft = $numAnimals - $i*$numPerRow;
		
		$numOnRow = min ( $animalsLeft, $numPerRow );
		
		if ( $numPerRow <= 6 ) {
			
			print '<div class="row feedback_species_row">';
			
			if ( $numOnRow == 5 ) {
				print "<div class='col-md-1'></div>"; // offset
			}
			else if ( $numOnRow == 4 ) {
				print "<div class='col-md-2'></div>"; // offset
			}
			else if ( $numOnRow == 3 ) {
				print "<div class='col-md-3'></div>"; // offset
			}
			else if ( $numOnRow == 2 ) {
				print "<div class='col-md-4'></div>"; // offset
			}
			else if ( $numOnRow == 1 ) {
				print "<div class='col-md-5'></div>"; // offset
			}

		}
		else {
			print '<div class="row feedback_species_row_sml">';
			
			if ( $numOnRow > 8 and $numOnRow <= 10 ) {
				print "<div class='col-md-1'></div>"; // offset
			}
			else if ( $numOnRow > 6 and $numOnRow <= 8 ) {
				print "<div class='col-md-2'></div>"; // offset
			}
			else if ( $numOnRow > 4 and $numOnRow <= 6 ) {
				print "<div class='col-md-3'></div>"; // offset
			}
			else if ( $numOnRow > 2 and $numOnRow <= 4 ) {
				print "<div class='col-md-4'></div>"; // offset
			}
			else if ( $numOnRow <= 2 ) {
				print "<div class='col-md-5'></div>"; // offset
			}
			
			print "<div class='col-md-1'></div>"; // offset
		}
	
		for ($j=0; $j < $numPerRow; $j++) {
			
			//error_log ( "row element " . $j );
			
			if ( $i*$numPerRow + $j < $numAnimals ) {
			
				$animal = $this->all_animals[ $i*$numPerRow + $j ];
				
				$speciesNameClass = 'h4';
				
				if ( $numPerRow > 6 ) {
					
					$speciesNameClass = 'h5';
					print "<div class='col-md-1'>";
					
				}
				else {
					print "<div class='col-md-2'>";
				}
				
				if ( strlen($animal->name) > 13 ) $longSpeciesNameClass = 'long_species_name';
				
				//print '<h3 class="text-center"><div class=" '.$longSpeciesNameClass.'">'.$animal->name.'</div></h3>';
				
				print '<div class="text-center ' . $speciesNameClass . '">'.$animal->name.'</div>';
				
				print "</div>"; // col-2 or 1
				
			}
		}
	
		print "</div>"; // row
	
		if ( $numPerRow <= 6 ) {
			
			print '<div class="row">';
			
			if ( $numOnRow == 5 ) {
				print "<div class='col-md-1'></div>"; // offset
			}
			else if ( $numOnRow == 4 ) {
				print "<div class='col-md-2'></div>"; // offset
			}
			else if ( $numOnRow == 3 ) {
				print "<div class='col-md-3'></div>"; // offset
			}
			else if ( $numOnRow == 2 ) {
				print "<div class='col-md-4'></div>"; // offset
			}
			else if ( $numOnRow == 1 ) {
				print "<div class='col-md-5'></div>"; // offset
			}

		}
		else {
			print '<div class="row">';
			
			if ( $numOnRow > 8 and $numOnRow <= 10 ) {
				print "<div class='col-md-1'></div>"; // offset
			}
			else if ( $numOnRow > 6 and $numOnRow <= 8 ) {
				print "<div class='col-md-2'></div>"; // offset
			}
			else if ( $numOnRow > 4 and $numOnRow <= 6 ) {
				print "<div class='col-md-3'></div>"; // offset
			}
			else if ( $numOnRow > 2 and $numOnRow <= 4 ) {
				print "<div class='col-md-4'></div>"; // offset
			}
			else if ( $numOnRow <= 2 ) {
				print "<div class='col-md-5'></div>"; // offset
			}
			
			print "<div class='col-md-1'></div>"; // offset
		}
		
		
		for ($j=0; $j < $numPerRow; $j++) {
			
			//error_log ( "row element " . $j );
			
			if ( $i*$numPerRow + $j < $numAnimals ) {
		
				$animal = $this->all_animals[ $i*$numPerRow + $j ];
		
				if ( $numPerRow > 6 ) {
					print "<div class='col-md-1'>";
				}
				else {
					print "<div class='col-md-2'>";
				}
				
				$imageURL = "";
				
				if ( $animal->kiosk_image ) {
					$imageURL = JURI::root().$animal->kiosk_image;
				}
				else {
					if ( $animal->struc == "bird" ) {
						$imageURL = JURI::root()."images/thumbnails/OtherBird.png";
					}
					else if ( $animal->name == "Human" or $animal->species == 87 ){
						$imageURL = JURI::root()."images/thumbnails/Human.png";
					}
					else if ( $animal->name == "Nothing" or $animal->species == 86 ){
						$imageURL = JURI::root()."images/thumbnails/Undergrowth.jpg";
					}
					else if ( $animal->name == "Don't Know" or $animal->species == 96 ){
						$imageURL = JURI::root()."images/thumbnails/DontKnow.png";
					}
					else {
						$imageURL = JURI::root()."images/thumbnails/Fur.jpg";
					}
				}
				print '<img class="img-responsive center-block" style="max-height:48vh;" src="' . $imageURL . '" />';
				
				print '</div>'; // col-2
			}
			
		}
		
		print '</div>'; // row 
		
	}
	if ( $numRows == 1 ) {
		print '</div>'; // row spaced_row
	}
	else if ( $numPerRow > 6 ) {
		print '<div class="row half_spaced_row">';
		print '</div>'; // row spaced_row
	}
	
	
}

print '<h2 class="text-center" style="margin-top:5vh;">'.JText::_("COM_BIODIV_BADGEKIOSKFEEDBACK_HELP_SCI") . '</h1>';


print '<div class="col-md-4 col-md-offset-2">';
print '	<button class="btn btn-lg btn-block btn-success h2 control_btn reloadPage" >'.JText::_("COM_BIODIV_BADGEKIOSKFEEDBACK_CLASSIFY_AGAIN").'</button>';
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
print '	<button class="btn btn-lg btn-block btn-success h2 control_btn badgesBtn" type="submit">'.$btnText.'</button>';
print '</a>';
print '</div>';

print '</div>'; // col-12
print '</div>'; // col-12



?>