<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

if ( $this->totalLikes == 1 ) {
	print '' . $this->totalLikes . ' ' . JText::_("COM_BIODIV_LIKERESOURCE_SINGLE_LIKE");
}
else {
	
	print '' . $this->totalLikes . ' ' . JText::_("COM_BIODIV_LIKERESOURCE_MANY_LIKES");
}


?>