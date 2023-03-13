<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

if ( !$this->personId ) {
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_AWARDS_LOGIN").'</div>';
}
else {

	if ( $this->collect ) {
		print '<div class="awards">';
		
		if ( $this->award ) {
			
			$whoFor = $this->award->getWhoFor();
			
			print '<h2 class="text-center">'.JText::_("COM_BIODIV_AWARDS_CONGRATS").' '.$whoFor.'!</h2>';
		
			print '<h2 class="text-center">'.$this->award->getName().'</h2>';
			
			print '<div class="row">';
			print '<div class="col-md-4 col-sm-4 col-xs-6 col-md-offset-4 col-sm-offset-4 col-xs-offset-3">';
			print '<div class="besaward">';
			$this->award->printAward( false );
			print '</div>';
				
		}
		
		print '</div>'; // awards
	}
}



?>