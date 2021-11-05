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
print '  <h2><strong>'.$this->translations['what_do']['translation_text'].'</strong></h2>';  
print '  <div class=" h3 explain">'.$this->translations['how_choose']['translation_text'].'</div>';  
print '</div>'; // opaque-bg


// Use alternative views for audio sites
print '<div class="col-md-4">';
if ( $this->isCamera ) {
	print '	<button id="classify_tutorial" class="btn btn-lg btn-block btn-success h2 control_btn" type="submit">'.$this->translations['tutorial']['translation_text'].'</button>';
}
else {
	print '	<button id="classify_audio_tutorial" class="btn btn-lg btn-block btn-success h2 control_btn" type="submit">'.$this->translations['tutorial']['translation_text'].'</button>';
}
print '</div>';



print '<div class="col-md-4">';
if ( $this->isCamera ) {
	print '	<button id="classify_project" class="btn btn-lg btn-block btn-success h2 control_btn">'.$this->translations['classify_project']['translation_text'].'</button>';
}
else {
	print '	<button id="classify_audio_project" class="btn btn-lg btn-block btn-success h2 control_btn">'.$this->translations['classify_project']['translation_text'].'</button>';
}
print '</div>';



print '<div class="col-md-4">';
if ( $this->isCamera ) {
	print '	<button id="classify_wider" class="btn btn-lg btn-block btn-success h2 control_btn" >'.$this->translations['classify_wider']['translation_text'].'</button>';
}
else {
	print '	<button id="classify_audio_wider" class="btn btn-lg btn-block btn-success h2 control_btn" >'.$this->translations['classify_wider']['translation_text'].'</button>';
}
print '</div>';



print '<div class="opaque-heading">';
print '<p></p>';
print '  <div class="h3 explain biodiv-info"><span class="fa fa-info-circle"></span>'.$this->translations['classify_defn']['translation_text'].'</div>';  
print '</div>'; // opaque-bg


print '</div>'; // start-kiosk-jumbotron




?>


