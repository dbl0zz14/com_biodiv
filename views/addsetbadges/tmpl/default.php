<?php 

if ( !$this->personId ) {
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_ADDSETBADGES_LOGIN").'</div>';
}
else {
	
	foreach ( $this->setBadges as $badge ) {
		
		print '<div id="badge_'.$badge->badge_id.'" class="setBadge" role="button" data-toggle="tooltip" title="'.
					$badge->name.'"><img src="' . $badge->badge_image . '" class="img-responsive" alt="badge icon"></div>';
	}
	
}
 
?>