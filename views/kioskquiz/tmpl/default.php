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
print '  <h2><strong>'.JText::_("COM_BIODIV_KIOSKQUIZ_QUIZ_TIME").'</strong></h2>';  
print '  <div class=" h3 explain">'.JText::_("COM_BIODIV_KIOSKQUIZ_CHOOSE_LEVEL").'</div>';  
print '</div>'; // opaque-bg



print '<div class="col-md-4">';

if ( $this->isCamera ) {
	print '	<button id="beginner_quiz" class="btn btn-lg btn-block btn-success h2 control_btn" type="submit">'.JText::_("COM_BIODIV_KIOSKQUIZ_BEGINNER").'</button>';
}
else {
	print '	<button id="beginner_quiz_audio" class="btn btn-lg btn-block btn-success h2 control_btn" type="submit">'.JText::_("COM_BIODIV_KIOSKQUIZ_BEGINNER").'</button>';
}

print '</div>';



print '<div class="col-md-4">';

if ( $this->isCamera ) {
	print '	<button id="intermediate_quiz" class="btn btn-lg btn-block btn-success h2 control_btn">'.JText::_("COM_BIODIV_KIOSKQUIZ_INTERMEDIATE").'</button>';
}
else {
	print '	<button id="intermediate_quiz_audio" class="btn btn-lg btn-block btn-success h2 control_btn">'.JText::_("COM_BIODIV_KIOSKQUIZ_INTERMEDIATE").'</button>';
}

print '</div>';



print '<div class="col-md-4">';

if ( $this->isCamera ) {
	print '	<button id="expert_quiz" class="btn btn-lg btn-block btn-success h2 control_btn" >'.JText::_("COM_BIODIV_KIOSKQUIZ_EXPERT").'</button>';
}
else {
	print '	<button id="expert_quiz_audio" class="btn btn-lg btn-block btn-success h2 control_btn" >'.JText::_("COM_BIODIV_KIOSKQUIZ_EXPERT").'</button>';
}
print '</div>';



print '<div class="opaque-heading">';
print '<p></p>';
print '  <div class="h3 explain biodiv-info"><span class="fa fa-info-circle"></span>  '.JText::_("COM_BIODIV_KIOSKQUIZ_HOW_CHOOSE").'</div>';  
print '</div>'; // opaque-bg


print '</div>'; // start-kiosk-jumbotron




?>


