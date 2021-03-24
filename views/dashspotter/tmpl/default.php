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
	print '<a type="button" href="'.$this->translations['dash_page']['translation_text'].'" class="list-group-item btn btn-block" >'.$this->translations['login']['translation_text'].'</a>';
}
else {
	
	print '<div class="col-md-6">';
	
	print '<h3>'.$this->translations['spotter_stats']['translation_text'].'</h3>';
	
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
	
	print '<p class="text-center"><a class="btn btn-danger btn-lg" href="'. $this->translations['spotter_page']['translation_text'] .'">'. $this->translations['class_now']['translation_text'] .'</a></p>';
	
	print '</div>'; // col-6
	
	print '<div class="col-md-6">';
	
	print '<h3>'.$this->translations['quiz_results']['translation_text'].'</h3>';
	
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
		
		$errMsg = print_r ( $row, true );
		error_log ( "Spotter view, row: " . $errMsg );
		
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
			
			print '<td><a class="btn btn-danger" href="index.php/?option=com_biodiv&view=trainingtopic&topic_id='.$row['topic_id'].'">'. $this->translations['take_quiz']['translation_text'].'</a></td>';
			
			print '</tr>';
		}
	}

	print '</tbody></table>';
	
	print '</div>'; // table responsive
	
	if ( $this->numTopicScores == 0 ) {
		// No quiz results yet
		print '<p>'.$this->translations['quiz_none']['translation_text'].'</p>';
		
	}
	if ( $this->numMissingScores > 0 ) {
		print '<p class="text-center"><a class="btn btn-danger btn-lg" href="'.$this->translations['quiz_page']['translation_text'].'" >'. $this->translations['choose_quiz']['translation_text'] .'</a></p>';
	}
	
	print '</div>'; // col-6
	

}


?>






