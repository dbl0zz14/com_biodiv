<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;


if ( !$this->personId ) {
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_RESOURCEFILE_LOGIN").'</div>';
}
else if ( $this->resourceId ) {
	
	$this->resourceFile->printResource("fullResource");
	
}
else {
	print ('<div class="col-md-12" >'.JText::_("COM_BIODIV_RESOURCEFILE_NO_FILE").'</div>');
}



?>