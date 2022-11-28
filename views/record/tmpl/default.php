<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;


if ( $this->loaded ) {
	
	print '<div class="col-md-12"><h4>'.JText::_("COM_BIODIV_RECORD_UPLOAD_SUCCESS").' ' . $this->siteName . '</h4></div>';
	print '<div class="col-md-4"><button id="record_again" class="btn btn-success btn-xl btn-block  btn-wrap-text" style="font-size:30px;">'.JText::_("COM_BIODIV_RECORD_RECORD_AGAIN").'</button></div>';
	
}
else {
	
	print '<h3>'.JText::_("COM_BIODIV_RECORD_UPLOAD_ERROR").'</h3>';
}

?>