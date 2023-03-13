<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;


if ( !$this->personId ) {
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_STUDENTPROGRESS_LOGIN").'</div>';
}
else if ( !$this->schoolUser ) {
	print '<h2>'.JText::_("COM_BIODIV_STUDENTPROGRESS_NOT_SCH_USER").'</h2>';
}
else {
	
	print '<div class="row">';
	print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	Biodiv\SchoolCommunity::generateNav($this->schoolUser, null, "teacherzone");
	
	print '</div>'; // col-12
	print '</div>'; // row
	
	// --------------------- Main content
	
	print '<div class="row">';
	print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
	print '<div id="displayArea">';
	
	print '<a href="'.$this->educatorPage.'" class="btn btn-success homeBtn" >';
	print '<i class="fa fa-arrow-left"></i> ' . JText::_("COM_BIODIV_STUDENTPROGRESS_EDUCATOR_ZONE");
	print '</a>';
	
	print '<h2><span class="greenHeading">'.JText::_("COM_BIODIV_STUDENTPROGRESS_HEADING").'</span></h2>';

	print '<div class="row">';
	
	foreach ( $this->students as $studentId=>$student ) {
		
		print '<div class="col-md-4 col-sm-6 col-xs-12">';
		print '<div class="panel">';
		print '<div class="panel-body">';
		// print '<div class="row">';
		// print '<div class="col-md-5 col-sm-5 col-xs-5">';
		
		print '<div class="progressGrid">';
		print '<div class="progressAvatar">';
		print '<img class="img-responsive avatar" src="'.$student->avatar.'" />';
		print '</div>'; // progressAvatar
		
		print '<div class="progressName">';
		print '<div>'.$student->name.'</div>';
		print '</div>'; // progressName
		
		print '<div class="progressUsername">';
		print '<div>'.$student->username.'</div>';
		print '</div>'; // progressUsername
		
		if ( $student->max_level == 3 ) {
			
			print '<div class="progressAward_1 progressAwardImg">';
			print '<img class="img-responsive" src="'.$this->award1->image.'" />';
			print '</div>'; // progressAward_1
			
			print '<div class="progressAward_2 progressAwardImg">';
			print '<img class="img-responsive" src="'.$this->award2->image.'" />';
			print '</div>'; // progressAward_2
			
			print '<div class="progressAward_3 progressAwardImg">';
			print '<img class="img-responsive" src="'.$this->award3->image.'" />';
			print '</div>'; // progressAward_3
			
		}
		else if ( $student->max_level == 2 ) {
			
			print '<div class="progressAward_1">';
			print '<img class="img-responsive progressAwardImg" src="'.$this->award1->image.'" />';
			print '</div>'; // progressAward_1
			
			print '<div class="progressAward_2">';
			print '<img class="img-responsive progressAwardImg" src="'.$this->award2->image.'" />';
			print '</div>'; // progressAward_2
			
			print '<div class="progressAward_3">';
			print '<img class="img-responsive progressAwardImg" src="'.$this->award3->uncollected_image.'" />';
			print '</div>'; // progressAward_3
			
		}
		else if ( $student->max_level == 1 ) {
			
			print '<div class="progressAward_1">';
			print '<img class="img-responsive progressAwardImg" src="'.$this->award1->image.'" />';
			print '</div>'; // progressAward_1
			
			print '<div class="progressAward_2">';
			print '<img class="img-responsive progressAwardImg" src="'.$this->award2->uncollected_image.'" />';
			print '</div>'; // progressAward_2
			
			print '<div class="progressAward_3">';
			print '<img class="img-responsive progressAwardImg" src="'.$this->award3->uncollected_image.'" />';
			print '</div>'; // progressAward_3
			
		}
		else {
			
			print '<div class="progressAward_1">';
			print '<img class="img-responsive progressAwardImg" src="'.$this->award1->uncollected_image.'" />';
			print '</div>'; // progressAward_1
			
			print '<div class="progressAward_2">';
			print '<img class="img-responsive progressAwardImg" src="'.$this->award2->uncollected_image.'" />';
			print '</div>'; // progressAward_2
			
			print '<div class="progressAward_3">';
			print '<img class="img-responsive progressAwardImg" src="'.$this->award3->uncollected_image.'" />';
			print '</div>'; // progressAward_3
			
		}
		
		// print '<div class="progressBadges">';
		// print 'Badges progress here';
		// print '</div>'; // progressBadges
		
		$currentBadgeId = 1;
		$badges = explode(',', $student->badges);
		foreach ( $this->levels as $level=>$levelText ) {
			$maxBadgeId = $this->maxBadgeIds[$level];
			$colorClass = strtolower($levelText);
			print '<div class="progressLevel_'.$level.'">';
			print $levelText;
			print '</div>'; // progressLevel
			
			for ( $i = $currentBadgeId; $i <= $maxBadgeId; $i++ ) {
				
				print '<div class="progressBadge_'.$i.'">';
					
				if ( in_array($i, $badges) ) {
					print '<i class="fa fa-lg fa-circle '.$colorClass.'"></i>';
				}
				else {
					print '<i class="fa fa-lg fa-circle-o '.$colorClass.'"></i>';
				}
				print '</div>'; // progressLevel
			}
			$currentBadgeId = $maxBadgeId + 1;
		
		}
		
		print '</div>'; // progressGrid
		
		print '</div>'; // panel-body
		print '</div>'; // panel
		print '</div>'; // col-4
				
	}
	
	print '</div>'; // displayArea

	print '</div>'; // col-12

	print '</div>'; // row

}

?>