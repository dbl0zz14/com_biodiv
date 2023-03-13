<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

if ( $this->done ) {
	//showUploadMessages();

	print '<div class="row">';

	print '<div class="col-md-12 h3">';
		
	print '<div class="panel badgesPanel">';

	print '<div class="vSpaced">';
	print '<h3>'.JText::_("COM_BIODIV_UPDATEBADGE_UPDATED").'</h3>';
	print '</div>';
	
	print '<button class="btn btn-primary btn-lg btnInSpace reloadPage">'.JText::_("COM_BIODIV_UPDATEBADGE_CONTINUE").'</button>';
	
	print '</div>'; // panel
	print '</div>'; // col-12
	print '</div>'; // row	

}
else if ( $this->collect ) {
	
	if ( $this->className ) {
		print '<h2 class="text-center">'.JText::_("COM_BIODIV_UPDATEBADGE_CONGRATS").' '.$this->className.'!</h2>';
	}
	else {
		print '<h2 class="text-center">'.JText::_("COM_BIODIV_UPDATEBADGE_CONGRATS").' '.$this->schoolUser->username.'!</h2>';
	}
		
	
	print '<div class="row">';
	print '<div class="col-md-4 col-sm-4 col-xs-6 col-md-offset-4 col-sm-offset-4 col-xs-offset-3">';
	
	print '<div class="besaward">';
	$this->newBadge->printBadgeOnly();
	print '</div>'; // besaward
	
	print '</div>'; //col-4
	print '</div>'; // row	
	
	print '<h2 class="text-center">'.$this->newBadge->getBadgeName().'</h2>';
}
	




?>