<?php 

if ( !$this->personId ) {
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_SELECTBADGES_LOGIN").'</div>';
}
else {
	
	print '<h3>'.JText::_("COM_BIODIV_SELECTBADGES_SELECT").'</h3>';
	
	print '<form id="addBadgeForm">';
	
	print '<input type="hidden" name="set" value="'.$this->setId.'"/>';

	foreach ( $this->badgeScheme as $lockLevel=>$badgeList ) {
		print '<h4 class="vSpaced">'.JText::_("COM_BIODIV_SELECTBADGES_LEVEL_" . $lockLevel).'</h4>';
		
		foreach ( $badgeList as $badge ) {
			$badgeId = $badge->getBadgeId();
			print '<div class="selectBadgesGrid">';
			$checked = "";
			if ( $this->setBadges && in_array ( $badgeId, $this->setBadges )  ) {
				$checked = 'checked';
			}
			print '<div class="selectBadgesImg">';
			print '<div id="badge_'.$badgeId.'" class="selectBadgeImgBtn" role="button" data-toggle="tooltip" title="'.
					$badge->getBadgeName().'"><img src="' . $badge->getBadgeImage() . '" class="img-responsive" alt="badge icon"></div>';
			print '</div>'; // selectBadgesImg
			
			print '<div class="selectBadgesCheckbox">';
			print '<input type="checkbox" id="badge_'.$badgeId.'" name="badge[]" value="'.$badgeId.'" '.$checked.'>';
			print '<label for="badge_'.$badgeId.'" class="uploadLabel ">';
			print $badge->getTaskName();
			print '</label>';
			print '</div>'; // selectBadgesCheckbox
			
			print '</div>'; // selectBadgesGrid
		}
	}
	
	print '<hr/>';
	print '<button id="" class="btn btn-primary" type="submit">'.JText::_("COM_BIODIV_SELECTBADGES_SAVE").'</button>';
	print ' <button type="button" class="btn btn-default" data-dismiss="modal">'.JText::_("COM_BIODIV_SELECTBADGES_CANCEL").'</button>';
	
	print '</form>';
	
}
 
?>