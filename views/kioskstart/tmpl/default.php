<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;


// Just want an image with a start button, at least for now.
//print '<div id="start-kiosk-jumbotron" data-project-img="'.$this->projectImageUrl.' class="jumbotron" >';
print '<div id="start-kiosk-jumbotron" class="jumbotron text-center" data-project-img="'.$this->projectImageUrl.'" data-project-id="'.$this->projectId.'" data-user-key="'.$this->user_key.'" >';

print '<div class="opaque-heading">';
print '  <h2><strong>'.$this->project->project_prettyname.'</strong></h2>';  
print '</div>'; // opaque-bg



print '<div class="col-md-4">';

print '	<button id="kiosk_start" class="btn btn-lg btn-block btn-success h2" type="submit">'.$this->translations['start_here']['translation_text'].'</button>';

print '</div>';



print '<div class="col-md-4">';

print '	<button id="kiosk_animals" class="btn btn-lg btn-block btn-success h2" type="submit">'.$this->translations['learn_animals']['translation_text'].'</button>';

print '</div>';



print '<div class="col-md-4">';

print '	<button id="kiosk_classify" class="btn btn-lg btn-block btn-success h2" >'.$this->translations['classify']['translation_text'].'</button>';

print '</div>';



print '<div class="col-md-4">';

print '	<button id="kiosk_quiz" class="btn btn-lg btn-block btn-success h2" >'.$this->translations['take_quiz']['translation_text'].'</button>';

print '</div>';



print '<div class="col-md-4">';

print '	<button id="kiosk_map" class="btn btn-lg btn-block btn-success h2" >'.$this->translations['map']['translation_text'].'</button>';

print '</div>';



print '<div class="col-md-4">';

print '	<button id="kiosk_project" class="btn btn-lg btn-block btn-success h2" >'.$this->translations['learn_project']['translation_text'].'</button>';

print '</div>';



// Add logos
print '<div class="row opaque-logo-row">';
foreach ( $this->logos as $logo ) {
	print '<img src="' . $logo . '">';
}
print '</div>'; // opaque-logo-row


print '</div>'; // start-kiosk-jumbotron



?>


