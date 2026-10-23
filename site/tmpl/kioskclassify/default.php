<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;


use Joomla\CMS\Language\Text;


print '<div id="start-kiosk-jumbotron" class="jumbotron text-center" data-project-img="'.$this->projectImageUrl.'" data-project-id="'.$this->projectId.'" data-user-key="'.$this->user_key.'" >';

print '<div class="opaque-heading">';
print '  <h1 class="display-4">'.Text::_("COM_BIODIV_KIOSKCLASSIFY_WHAT_DO").'</h1>';  
print '  <h3 class="explain">'.Text::_("COM_BIODIV_KIOSKCLASSIFY_HOW_CHOOSE").'</h3>';  
print '</div>'; // opaque-bg

print '<div class="d-flex justify-content-evenly p-5">';

// Use alternative views for audio sites
if ( $this->isCamera ) {
	print '	<button id="classify_tutorial" class="btn btn-lg btn-primary h2 control_btn w-25 pt-5 pb-5" type="submit">'.Text::_("COM_BIODIV_KIOSKCLASSIFY_TUTORIAL").'</button>';
}
else {
	print '	<button id="classify_audio_tutorial" class="btn btn-lg btn-primary h2 control_btn w-25 pt-5 pb-5" type="submit">'.Text::_("COM_BIODIV_KIOSKCLASSIFY_TUTORIAL").'</button>';
}


if ( $this->isCamera ) {
	print '	<button id="classify_project" class="btn btn-lg btn-primary h2 control_btn w-25 pt-5 pb-5">'.Text::_("COM_BIODIV_KIOSKCLASSIFY_CLASSIFY_PROJECT").'</button>';
}
else {
	print '	<button id="classify_audio_project" class="btn btn-lg btn-primary h2 control_btn w-25 pt-5 pb-5">'.Text::_("COM_BIODIV_KIOSKCLASSIFY_CLASSIFY_PROJECT").'</button>';
}


if ( $this->isCamera ) {
	print '	<button id="classify_wider" class="btn btn-lg btn-primary h2 control_btn w-25 pt-5 pb-5" >'.Text::_("COM_BIODIV_KIOSKCLASSIFY_CLASSIFY_WIDER").'</button>';
}
else {
	print '	<button id="classify_audio_wider" class="btn btn-lg btn-primary h2 control_btn w-25 pt-5 pb-5" >'.Text::_("COM_BIODIV_KIOSKCLASSIFY_CLASSIFY_WIDER").'</button>';
}

print '</div>'; // d-flex

print '<div class="opaque-heading">';
print '  <h3 class="explain biodiv-info"><span class="fa fa-info-circle"></span>'.Text::_("COM_BIODIV_KIOSKCLASSIFY_CLASSIFY_DEFN").'</h3>';  
print '</div>'; // opaque-bg


print '</div>'; // start-kiosk-jumbotron




?>


