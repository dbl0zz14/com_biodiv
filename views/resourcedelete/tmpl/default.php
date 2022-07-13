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
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.$this->translations['login']['translation_text'].'</div>';
	
}

else {
	
	if ( $this->deleted ) {	
		print '<h2>'.$this->translations['deleted']['translation_text'].'</h2>';
		
		print '<div class="btn btn-primary" >'.$this->translations['resource_home']['translation_text'].'</div>';
	}
	else {
		print '<h2>'.$this->translations['not_deleted']['translation_text'].'</h2>';
		
		print '<div class="btn btn-primary" >'.$this->translations['reload_resource']['translation_text'].'</div>';
	}
		

}

?>