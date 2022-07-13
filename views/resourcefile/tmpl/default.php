<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;


if ( !$this->personId ) {
	print '<a type="button" href="'.$this->translations['hub_page']['translation_text'].'" class="list-group-item btn btn-block" >'.$this->translations['login']['translation_text'].'</a>';
}
else if ( $this->resourceId ) {
	
	$this->resourceFile->printResource("fullResource");
	
}
else {
	print ('<div class="col-md-12" >'.$this->translations['no_file']['translation_text'].'</div>');
}

//print ('Resource File here');


?>