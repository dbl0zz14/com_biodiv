<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;


if ( !$this->personId ) {
	print '<a type="button" href="'.Text::_("COM_BIODIV_SCHOOLSDASHSUMMARY_DASH_PAGE").'" class="list-group-item btn btn-block" >'.Text::_("COM_BIODIV_SCHOOLSDASHSUMMARY_LOGIN").'</a>';
}
else if ( !$this->isSchoolUser ) {
	print '<a type="button" href="'.Text::_("COM_BIODIV_SCHOOLSDASHSUMMARY_DASH_PAGE").'" class="list-group-item btn btn-block" >'.Text::_("COM_BIODIV_SCHOOLSDASHSUMMARY_LOGIN").'</a>';
}
else {
	
	print '<div class="container">';
	print '<div class="row">';
	
	// Spotter
	print '<div class="col-md-6">';
	print '<div class="card ps-2 pe-2 mb-4">';
	print '<div class="card-body">';
			
	print '<h3 class="card-title">'.Text::_("COM_BIODIV_SCHOOLSDASHSUMMARY_SPOTTER_STATS").'</h3>';
	
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
	
	print '<p class="text-center"><a class="btn btn-primary btn-lg" href="'. Text::_("COM_BIODIV_SCHOOLSDASHSUMMARY_SPOTTER_PAGE") .'">'. Text::_("COM_BIODIV_SCHOOLSDASHSUMMARY_CLASS_NOW") .'</a></p>';
	
	print '</div>'; // card-body
	print '</div>'; // card
	print '</div>'; // col-6
	
	
	// Trapper (teacher only)
	if ( $this->isTeacher ) {
		
		print '<div class="col-md-6">';
		print '<div class="card ps-2 pe-2 mb-4">';
		print '<div class="card-body">';
		
		print '<h3>'.Text::_("COM_BIODIV_SCHOOLSDASHSUMMARY_TRAPPER_STATS").'</h3>';
		
		// Add the table and headings
		print  '<div class="table-responsive">';
		print  '<table class="table"><tbody>';

		// Add the rows of data  
		foreach ( $this->trapperStatRows as $row ) {
			
			print '<tr>';
			
			foreach ( $row as $rowField ) {
				print '<td>'.$rowField.'</td>';
			}
			
			print '</tr>';
		}

		print '</tbody></table>';
		
		print '</div>'; // table responsive
		
		print '<p class="text-center"><a class="btn btn-outline-primary btn-lg" href="'.Text::_("COM_BIODIV_SCHOOLSDASHSUMMARY_TRAPPER_PAGE").'" >'. Text::_("COM_BIODIV_SCHOOLSDASHSUMMARY_MAN_SITES") .'</a></p>';
		
		print '</div>'; // card-body
		print '</div>'; // card
		print '</div>'; // col-6
	}
	
	// Quizzes
	
	print '<div class="col-md-6">';
	print '<div class="card ps-2 pe-2 mb-4">';
	print '<div class="card-body">';
	
	print '<h3>'.Text::_("COM_BIODIV_SCHOOLSDASHSUMMARY_QUIZ_RESULTS").'</h3>';
	
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
			
			//print '<td><a class="btn btn-outline-secondary" href="index.php/?option=com_biodiv&view=trainingtopic&topic_id='.$row['topic_id'].'">'. Text::_("COM_BIODIV_DASHSUMMARY_TAKE_QUIZ").'</a></td>';
			
			print '</tr>';
		}
	}

	print '</tbody></table>';
	
	print '</div>'; // table responsive
	
	if ( $this->numTopicScores == 0 ) {
		// No quiz results yet
		print '<p>'.Text::_("COM_BIODIV_SCHOOLSDASHSUMMARY_QUIZ_NONE").'</p>';
		
	}
	if ( $this->numMissingScores > 0 ) {
		print '<p class="text-center"><a class="btn btn-outline-primary btn-lg" href="'.Text::_("COM_BIODIV_SCHOOLSDASHSUMMARY_QUIZ_PAGE").'" >'. Text::_("COM_BIODIV_SCHOOLSDASHSUMMARY_CHOOSE_QUIZ") .'</a></p>';
	}
	
	print '</div>'; // card-body
	print '</div>'; // card
	print '</div>'; // col-6
	
	
	// Badges
	print '<div class="col-md-6">';
	print '<div class="card ps-2 pe-2 mb-4">';
	print '<div class="card-body">';
	
	print '<h3>'.Text::_("COM_BIODIV_SCHOOLSDASHSUMMARY_BADGES").'</h3>';
	
	print '<div class="schools-badge"><img src="'.Text::_("COM_BIODIV_SCHOOLSDASHSUMMARY_TEMP_BADGE").'"></div>';
	
	print '<p class="text-center"><a class="btn btn-outline-primary btn-lg" href="'.Text::_("COM_BIODIV_SCHOOLSDASHSUMMARY_BADGE_PAGE").'" >'. Text::_("COM_BIODIV_SCHOOLSDASHSUMMARY_CHOOSE_BADGES") .'</a></p>';

	
	print '</div>'; // card-body
	print '</div>'; // card
	print '</div>'; // col-6
	
	print '</div>'; // row
	print '</div>'; // container

}


?>






