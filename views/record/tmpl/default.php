<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;


if ( $this->loaded ) {
	
	print '<div class="col-md-12"><h4>'.$this->translations['upload_success']['translation_text'].' ' . $this->siteName . '</h4></div>';
	print '<div class="col-md-4"><button id="record_again" class="btn btn-success btn-xl btn-block  btn-wrap-text" style="font-size:30px;">'.$this->translations['record_again']['translation_text'].'</button></div>';
	
}
else {
	
	print '<h3>'.$this->translations['upload_error']['translation_text'].'</h3>';
}

?>