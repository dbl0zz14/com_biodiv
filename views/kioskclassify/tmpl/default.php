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
print '  <h2><strong>'.JText::_("COM_BIODIV_KIOSKCLASSIFY_WHAT_DO").'</strong></h2>';  
print '  <div class=" h3 explain">'.JText::_("COM_BIODIV_KIOSKCLASSIFY_HOW_CHOOSE").'</div>';  
print '</div>'; // opaque-bg


// Use alternative views for audio sites
print '<div class="col-md-4">';
if ( $this->isCamera ) {
	print '	<button id="classify_tutorial" class="btn btn-lg btn-block btn-success h2 control_btn" type="submit">'.JText::_("COM_BIODIV_KIOSKCLASSIFY_TUTORIAL").'</button>';
}
else {
	print '	<button id="classify_audio_tutorial" class="btn btn-lg btn-block btn-success h2 control_btn" type="submit">'.JText::_("COM_BIODIV_KIOSKCLASSIFY_TUTORIAL").'</button>';
}
print '</div>';



print '<div class="col-md-4">';
if ( $this->isCamera ) {
	print '	<button id="classify_project" class="btn btn-lg btn-block btn-success h2 control_btn">'.JText::_("COM_BIODIV_KIOSKCLASSIFY_CLASSIFY_PROJECT").'</button>';
}
else {
	print '	<button id="classify_audio_project" class="btn btn-lg btn-block btn-success h2 control_btn">'.JText::_("COM_BIODIV_KIOSKCLASSIFY_CLASSIFY_PROJECT").'</button>';
}
print '</div>';



print '<div class="col-md-4">';
if ( $this->isCamera ) {
	print '	<button id="classify_wider" class="btn btn-lg btn-block btn-success h2 control_btn" >'.JText::_("COM_BIODIV_KIOSKCLASSIFY_CLASSIFY_WIDER").'</button>';
}
else {
	print '	<button id="classify_audio_wider" class="btn btn-lg btn-block btn-success h2 control_btn" >'.JText::_("COM_BIODIV_KIOSKCLASSIFY_CLASSIFY_WIDER").'</button>';
}
print '</div>';



print '<div class="opaque-heading">';
print '<p></p>';
print '  <div class="h3 explain biodiv-info"><span class="fa fa-info-circle"></span>'.JText::_("COM_BIODIV_KIOSKCLASSIFY_CLASSIFY_DEFN").'</div>';  
print '</div>'; // opaque-bg


print '</div>'; // start-kiosk-jumbotron




?>


