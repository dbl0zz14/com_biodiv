<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

if ( !$this->personId ) {
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_AWARDS_LOGIN").'</div>';
}
else {

	print '<div class="awardsBox">';
	
	print_r ( $this->newAwards );
	
	print '</div>'; // awardsBox
}



?>