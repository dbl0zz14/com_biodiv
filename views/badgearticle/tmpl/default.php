<?php 

if ( !$this->personId ) {
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_BADGEARTICLE_LOGIN").'</div>';
}
else {

	$this->badge->printBadgeHeader( $this->readonly );

	if ( $this->introtext ) {
		print "<div>".$this->introtext."</div>"; 
	}
	
	if ( !$this->readonly && !$this->complete ) {
		print '<hr>';
		 
		print '<div class="row">';
		
		if ( !$this->badge->isSystemCounted() ) {
			print '<div class="col-md-8 col-sm-8 col-xs-8 text-right">';
			print '<button id="badgeComplete_'.$this->badgeId.'" type="button" class="btn btn-primary btn-lg badgeComplete" >'.JText::_("COM_BIODIV_BADGEARTICLE_COMPLETE").'</button>';
			print '</div>'; // col-8
		}
		print '<div class="col-md-4 col-sm-4 col-xs-4">';
		print '<button type="button" class="btn btn-info btn-lg " data-dismiss="modal">'.JText::_("COM_BIODIV_BADGEARTICLE_CLOSE").'</button>';
		print '</div>'; // col-4
		print '</div>'; // row
	}
}
 
?>