<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

showUploadMessages();

print '<div class="fullPageHeight">';


if ( $this->avatar ) {
	
	print '<div class="row">';

	print '<div class="col-md-2 col-md-offset-1 col-sm-2 col-xs-4">';
	print '<img src="'.$this->avatar->image.'" class="img-responsive jumpingAvatar" alt="'.$this->avatar->name.' avatar" />';
	print '</div>';

	print '</div>'; // row
	
}

print '<div class="row">';

print '<div class="col-md-12">';
print '<div class="row">';

if ( $this->feedback ) {
	
	print '<div class="col-md-12 h2">';
	print $this->feedback;
	print '</div>';
	
}

if ( $this->message ) {
	
	print '<div class="col-md-12 h3">';
	print $this->message;
	print '</div>';
	
}

print '</div>'; // row
print '</div>'; // col-12


print '<div class="col-md-12">';


if ( $this->nextStep ) {
	
	print '<div class="row">';
	print '<div class="col-md-12 h3">';
	print $this->nextStep;
	print '</div>';
	print '</div>';
	
}

print '<div class="row">';
print '<div class="col-md-12 h3">';
	
if ( $this->findActivityButton ) {
	
	print '<button class="btn btn-primary btnInSpace browseTasksButton">'.JText::_("COM_BIODIV_UPDATETASK_FIND_ANOTHER").'</button>';
	
}

if ( $this->reloadButton ) {
	
	print '<button class="btn btn-primary btnInSpace reloadBtn">'.JText::_("COM_BIODIV_UPDATETASK_FIND_ANOTHER").'</button>';
	
}

if ( $this->reviewWorkButton ) {
	
	print '<a href="'.JText::_("COM_BIODIV_UPDATETASK_SCHOOLWORK_LINK").'" class="btn btn-primary btnInSpace">';
	print JText::_("COM_BIODIV_UPDATETASK_REVIEW_WORK");
	print '</a>';
	
}

print '</div>'; // col-12
print '</div>'; // row
	
print '</div>'; // col-md-12


print '</div>'; // row

print '</div>'; // fullHeight


?>