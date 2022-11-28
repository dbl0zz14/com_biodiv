<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

if ( $this->sequence == null ) {
	print '  <h2 class="text-center classify_heading">'.JText::_("COM_BIODIV_KIOSKMEDIACAROUSEL_NO_SEQUENCES").'</h2>';
}

else {
	
	// If it's a photo, add columns so that controls can be placed outside photo
	if ( $this->sequence->getMedia() == "photo" ) {
		print '<div class="col-md-12">';
		print '<div class="col-md-12">';

		$this->mediaCarousel->generateMediaCarousel($this->sequence);
		
		print '</div>'; // col-12
		print '</div>'; // col-12
	}
	else {
		$this->mediaCarousel->generateMediaCarousel($this->sequence);
	}

}






?>


