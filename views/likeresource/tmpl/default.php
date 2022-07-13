<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

if ( $this->totalLikes == 1 ) {
	print '' . $this->totalLikes . ' ' . $this->translations['single_like']['translation_text'];
}
else {
	
	print '' . $this->totalLikes . ' ' . $this->translations['many_likes']['translation_text'];
}


?>