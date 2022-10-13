<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;


//print '<h2>'.$this->translations['upload']['translation_text'].'</h2>';
//print '<h3>'.$this->translations['choose_files']['translation_text'].'</h3>';

print '<h4>'.$this->translations['tap_button']['translation_text'].'</h4>';
print '<h4>'.$this->translations['add_more']['translation_text'].'</h4>';

print '<div id="resourceSet" data-set_id="'.$this->newSetId.'"></div>';
print '<div class="row">';

print '<div class="col-md-12">';

print '<button id="resourceuploader" >'.$this->translations['choose_files']['translation_text'].'</button>';
//print '<div id="resourceuploader"></div>';
print '<div id="fileuploadspinner"  style="display:none"><i class="fa fa-spinner fa-spin fa-4x"></i></div>';


print '</div>'; // col-md-12




print '</div>'; // row

?>