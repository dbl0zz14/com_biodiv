<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

error_log ( "Resource upload template called" );


if ( !$this->personId ) {
	// Please log in button
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_RESOURCEDELETE_LOGIN").'</div>';
	
}

else {
	
	if ( $this->deleted ) {	
		print '<h2>'.JText::_("COM_BIODIV_RESOURCEDELETE_DELETED").'</h2>';
		
		print '<div class="btn btn-primary" >'.JText::_("COM_BIODIV_RESOURCEDELETE_RESOURCE_HOME").'</div>';
	}
	else {
		print '<h2>'.JText::_("COM_BIODIV_RESOURCEDELETE_NOT_DELETED").'</h2>';
		
		print '<div class="btn btn-primary" >'.JText::_("COM_BIODIV_RESOURCEDELETE_RELOAD_RESOURCE").'</div>';
	}
		

}

?>