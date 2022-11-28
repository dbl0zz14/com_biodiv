<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*/
 
// No direct access to this file
defined('_JEXEC') or die;
 
// import Joomla view library
jimport('joomla.application.component.view');
 

/**
* HTML View class for the HelloWorld Component
*
* @since 0.0.1
*/
class BioDivViewRecordTop extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		$this->personId = (int)userID();
		
		if ( $this->personId ) {
		
			$isCamera = getSetting("camera") == "yes";
			$this->siteHelper = new SiteHelper($isCamera);
			
			$this->maxClipLength = getSetting("max_clip_length");
		}
	  
		// Display the view
		parent::display($tpl);

	}
}



?>
