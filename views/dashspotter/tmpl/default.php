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

if ( !$this->personId ) {
	print '<a type="button" href="'.JText::_("COM_BIODIV_DASHSPOTTER_DASH_PAGE").'" class="list-group-item btn btn-block" >'.JText::_("COM_BIODIV_DASHSPOTTER_LOGIN").'</a>';
}
else {
	
	print '<div class="col-md-6">';
	
	print '<h3>'.JText::_("COM_BIODIV_DASHSPOTTER_SPOTTER_STATS").'</h3>';
	
	// Add the table and headings
	print  '<div class="table-responsive">';
	print  '<table class="table"><tbody>';

	// Add the rows of data  
	foreach ( $this->statRows as $row ) {
		
		print '<tr>';
		
		foreach ( $row as $rowField ) {
			print '<td>'.$rowField.'</td>';
		}
		
		print '</tr>';
	}

	print '</tbody></table>';
	
	print '</div>'; // table responsive
	
	print '<p class="text-center"><a class="btn btn-success btn-lg" href="'. JText::_("COM_BIODIV_DASHSPOTTER_SPOTTER_PAGE") .'">'. JText::_("COM_BIODIV_DASHSPOTTER_CLASS_NOW") .'</a></p>';
	
	print '</div>'; // col-6
	
	print '<div class="col-md-6">';
	
	print '<h3>'.JText::_("COM_BIODIV_DASHSPOTTER_QUIZ_RESULTS").'</h3>';
	
	$nostars = "<span class='fa fa-star-o'></span></span><span class='fa fa-star-o'></span></span><span class='fa fa-star-o'></span></span><span class='fa fa-star-o'></span></span><span class='fa fa-star-o'></span>";
	$onestar = "<span class='fa fa-star'></span></span><span class='fa fa-star-o'></span></span><span class='fa fa-star-o'></span></span><span class='fa fa-star-o'></span></span><span class='fa fa-star-o'></span>";
	$twostar = "<span class='fa fa-star'></span><span class='fa fa-star'></span></span><span class='fa fa-star-o'></span></span><span class='fa fa-star-o'></span></span><span class='fa fa-star-o'></span>";
	$threestar = "<span class='fa fa-star'></span><span class='fa fa-star'></span><span class='fa fa-star'></span></span><span class='fa fa-star-o'></span></span><span class='fa fa-star-o'></span>";
	$fourstar = "<span class='fa fa-star'></span><span class='fa fa-star'></span><span class='fa fa-star'></span><span class='fa fa-star'></span></span><span class='fa fa-star-o'></span>";
	$fivestar = "<span class='fa fa-star'></span><span class='fa fa-star'></span><span class='fa fa-star'></span><span class='fa fa-star'></span><span class='fa fa-star'></span>";
	
	print  '<div class="table-responsive">';
	print  '<table class="table"><tbody>';

	// Add the rows of data  
	foreach ( $this->scores as $row ) {
		
		
		// Avoid Gold Standard or other topics we don't display
		if ( in_array($row['topic_id'], $this->topicIds ) ) {
			print '<tr>';
			
			print '<td>'.$row['topic'].'</td>';
			
			$stars = $nostars;
			if ( $row['level'] != null ) {
				if ( $row['level'] >80 ) $stars = $fivestar;
				else if ( $row['level'] > 60 ) $stars = $fourstar;
				else if ( $row['level'] > 40 ) $stars = $threestar;
				else if ( $row['level'] > 20 ) $stars = $twostar;
				else $stars = $onestar;
			}
			
			print '<td>'.$stars.'</td>';
			
			print '<td><a class="btn btn-success" href="index.php/?option=com_biodiv&view=trainingtopic&topic_id='.$row['topic_id'].'">'. JText::_("COM_BIODIV_DASHSPOTTER_TAKE_QUIZ").'</a></td>';
			
			print '</tr>';
		}
	}

	print '</tbody></table>';
	
	print '</div>'; // table responsive
	
	if ( $this->numTopicScores == 0 ) {
		// No quiz results yet
		print '<p>'.JText::_("COM_BIODIV_DASHSPOTTER_QUIZ_NONE").'</p>';
		
	}
	if ( $this->numMissingScores > 0 ) {
		print '<p class="text-center"><a class="btn btn-success btn-lg" href="'.JText::_("COM_BIODIV_DASHSPOTTER_QUIZ_PAGE").'" >'. JText::_("COM_BIODIV_DASHSPOTTER_CHOOSE_QUIZ") .'</a></p>';
	}
	
	print '</div>'; // col-6
	

}


?>






