<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;


print '<div id="start-kiosk-jumbotron" class="jumbotron text-center" data-project-img="'.$this->projectImageUrl.'" data-project-id="'.$this->projectId.'" data-user-key="'.$this->user_key.'" >';

print '<div class="opaque-heading">';
print '  <h2><strong>'.$this->translations['quiz_time']['translation_text'].'</strong></h2>';  
print '  <div class=" h3 explain">'.$this->translations['choose_level']['translation_text'].'</div>';  
print '</div>'; // opaque-bg



print '<div class="col-md-4">';
print '	<button id="beginner_quiz" class="btn btn-lg btn-block btn-success h2 control_btn" type="submit">'.$this->translations['beginner']['translation_text'].'</button>';
print '</div>';



print '<div class="col-md-4">';
print '	<button id="intermediate_quiz" class="btn btn-lg btn-block btn-success h2 control_btn">'.$this->translations['intermediate']['translation_text'].'</button>';
print '</div>';



print '<div class="col-md-4">';
print '	<button id="expert_quiz" class="btn btn-lg btn-block btn-success h2 control_btn" >'.$this->translations['expert']['translation_text'].'</button>';
print '</div>';



print '<div class="opaque-heading">';
print '<p></p>';
print '  <div class="h3 explain biodiv-info"><span class="fa fa-info-circle"></span>  '.$this->translations['how_choose']['translation_text'].'</div>';  
print '</div>'; // opaque-bg


print '</div>'; // start-kiosk-jumbotron




?>


