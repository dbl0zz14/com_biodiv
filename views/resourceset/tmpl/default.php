<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

error_log ( "Resource set template called" );

//print '<h2>'.$this->translations['upload']['translation_text'].'</h2>';
print '<h2>'.$this->translations['choose_files']['translation_text'].'</h2>';

print '<div id="resourceSet" data-set_id="'.$this->newSetId.'"></div>';
print '<div class="row">';

print '<div class="col-md-12">';

print '<button id="resourceuploader" >'.$this->translations['choose_files']['translation_text'].'</button>';
//print '<div id="resourceuploader"></div>';
print '<div id="fileuploadspinner"  style="display:none"><i class="fa fa-spinner fa-spin fa-4x"></i></div>';


print '</div>'; // col-md-12




print '</div>'; // row

?>