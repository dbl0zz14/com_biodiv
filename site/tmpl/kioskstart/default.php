<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;


use Joomla\CMS\Language\Text;


// Just want an image with a start button, at least for now.
//print '<div id="start-kiosk-jumbotron" data-project-img="'.$this->projectImageUrl.' class="jumbotron" >';
print '<div id="start-kiosk-jumbotron" class="jumbotron text-center" data-project-img="'.$this->projectImageUrl.'" data-project-id="'.$this->projectId.'" data-user-key="'.$this->user_key.'" >';

//print '<div class="opaque-heading">';
print '<div class="start_header pt-5">';
print '  <h1 class="display-3">'.$this->project->project_prettyname.'</h1>';  
print '</div>'; // opaque-bg


print '<div class="d-flex justify-content-evenly p-5">';

print '	<button id="kiosk_start" class="btn btn-lg btn-primary h2 w-25 pt-5 pb-5" type="submit">'.Text::_("COM_BIODIV_KIOSKSTART_START_HERE").'</button>';

if ( $this->birdsOnly ) {
	print '	<button id="kiosk_birds" class="btn btn-lg btn-primary h2 w-25 pt-5 pb-5" type="submit">'.Text::_("COM_BIODIV_KIOSKSTART_LEARN_BIRDS").'</button>';
}
else {
	print '	<button id="kiosk_animals" class="btn btn-lg btn-primary h2 w-25 pt-5 pb-5" type="submit">'.Text::_("COM_BIODIV_KIOSKSTART_LEARN_ANIMALS").'</button>';
}

print '	<button id="kiosk_classify" class="btn btn-lg btn-primary h2 w-25 pt-5 pb-5" >'.Text::_("COM_BIODIV_KIOSKSTART_CLASSIFY").'</button>';

print '</div>';

print '<div class="d-flex justify-content-evenly p-5">';

print '	<button id="kiosk_quiz" class="btn btn-lg btn-primary h2 w-25 pt-5 pb-5" >'.Text::_("COM_BIODIV_KIOSKSTART_TAKE_QUIZ").'</button>';

print '	<button id="kiosk_map" class="btn btn-lg btn-primary h2 w-25 pt-5 pb-5" >'.Text::_("COM_BIODIV_KIOSKSTART_MAP").'</button>';

print '	<button id="kiosk_project" class="btn btn-lg btn-primary h2 w-25 pt-5 pb-5" >'.Text::_("COM_BIODIV_KIOSKSTART_LEARN_PROJECT").'</button>';

print '</div>';



// Add logos
print '<div class="row opaque-logo-row">';
print '<div class="col-md-12">';
foreach ( $this->logos as $logo ) {
	print '<img src="' . $logo . '">';
}
print '</div>'; // col-12
print '</div>'; // opaque-logo-row


print '</div>'; // start-kiosk-jumbotron



?>


