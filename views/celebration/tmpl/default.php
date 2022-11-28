<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;


if ( !$this->personId ) {
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_CELEBRATION_LOGIN").'</div>';
}
else {

	print '<div class="studentCelebrationBox">';
	
	print '<div id="studentCelebrationRow" class="row">';
	
	if ( $this->celebration->type == "badge" ) {
		
		print '<div class="col-md-12 text-center">';
		print JText::_("COM_BIODIV_CELEBRATION_YOU_ACHIEVED");
		print '</div>'; // col-12
		
		print '<div class="col-md-12 text-center bigText">';
		print $this->celebration->badge_name . ' ' . JText::_("COM_BIODIV_CELEBRATION_BADGE");
		print '</div>'; // col-12
		
		print '<div class="col-md-12 text-center">';
		print JText::_("COM_BIODIV_CELEBRATION_CONTRIBUTED").'<span class="hugeText">'.$this->celebration->points . '</span> ' . JText::_("COM_BIODIV_CELEBRATION_POINTS");
		print '</div>'; // col-12
		
	}
	else {
	}
	
	
	print '</div>'; // row

	print '</div>'; // schoolDataBox
}



?>