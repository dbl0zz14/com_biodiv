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
* HTML View class for the BioDiv Component
*
* @since 0.0.1
*/
class BioDivViewDiscoverUserUploads extends JViewLegacy
{
        /**
         *
         * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
         *
         * @return  void
         */

    public function display($tpl = null) 
    {
		
		error_log("DiscoverUserUploads display called");
		
		$app = JFactory::getApplication();	
		
		$input = $app->input;
		
		$this->siteId = $input->get('site', 0, 'INT');
		error_log ( "DashCharts view.  Site id = " . $this->siteId );

		
		
		if ( $person_id = (int)userID() ) {
		
			// This defaults to 12 months for all sites
			$this->data = discoverUserUploads ($this->siteId);
			
			$this->colormap = getSetting('colormap');
		
			$this->data["colormap"] = json_decode($this->colormap);
		
		
			
			error_log("DiscoverUserUploads data set");
		}
		

		// Display the view
		parent::display($tpl);
    }
}



?>