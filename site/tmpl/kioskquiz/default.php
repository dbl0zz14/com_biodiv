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
print '  <h1 class="display-4">'.Text::_("COM_BIODIV_KIOSKQUIZ_QUIZ_TIME").'</h2>';  
print '  <h3 class="explain">'.Text::_("COM_BIODIV_KIOSKQUIZ_CHOOSE_LEVEL").'</h3>';  
print '</div>'; // opaque-bg


print '<div class="d-flex justify-content-evenly p-5">';


if ( $this->isCamera ) {
	print '	<button id="beginner_quiz" class="btn btn-lg btn-primary h2 control_btn w-25 pt-5 pb-5" type="submit">'.Text::_("COM_BIODIV_KIOSKQUIZ_BEGINNER").'</button>';
}
else {
	print '	<button id="beginner_quiz_audio" class="btn btn-lg btn-primary h2 control_btn w-25 pt-5 pb-5" type="submit">'.Text::_("COM_BIODIV_KIOSKQUIZ_BEGINNER").'</button>';
}


if ( $this->isCamera ) {
	print '	<button id="intermediate_quiz" class="btn btn-lg btn-primary h2 control_btn w-25 pt-5 pb-5">'.Text::_("COM_BIODIV_KIOSKQUIZ_INTERMEDIATE").'</button>';
}
else {
	print '	<button id="intermediate_quiz_audio" class="btn btn-lg btn-primary h2 control_btn w-25 pt-5 pb-5">'.Text::_("COM_BIODIV_KIOSKQUIZ_INTERMEDIATE").'</button>';
}


if ( $this->isCamera ) {
	print '	<button id="expert_quiz" class="btn btn-lg btn-primary h2 control_btn w-25 pt-5 pb-5" >'.Text::_("COM_BIODIV_KIOSKQUIZ_EXPERT").'</button>';
}
else {
	print '	<button id="expert_quiz_audio" class="btn btn-lg btn-primary h2 control_btn w-25 pt-5 pb-5" >'.Text::_("COM_BIODIV_KIOSKQUIZ_EXPERT").'</button>';
}


print '</div>';


print '<div class="opaque-heading">';
print '<p></p>';
print '  <h3 class="explain"><span class="fa fa-info-circle"></span>  '.Text::_("COM_BIODIV_KIOSKQUIZ_HOW_CHOOSE").'</div>';  
print '</div>'; // opaque-bg


print '</div>'; // start-kiosk-jumbotron




?>


