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
class BioDivViewDiscoverUserNothingHuman extends JViewLegacy
{
        /**
         *
         * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
         *
         * @return  void
         */

    public function display($tpl = null) 
    {
		// Assign data to the view
		//($person_id = (int)userID()) or die("No person_id");
		
		error_log("DiscoverUserNothingHuman display called");
		
		
		
		$app = JFactory::getApplication();
		
		$input = $app->input;
		
		$this->siteId = $input->get('site', 0, 'INT');
		error_log ( "DashCharts view.  Site id = " . $this->siteId );

		$this->rare = $input->get('rare', 0, 'INT');
		error_log ( "DiscoverUserNothingHuman view.  Rare = " . $this->rare );

		$this->colormap = getSetting('colormap');
		
		
		
		$this->data = discoverUserNothingHuman ( $this->siteId, $this->rare == 0 );
		
		$this->data["colormap"] = json_decode($this->colormap);
		
		error_log("DiscoverUserNothingHuman data set");
		

		// Display the view
		parent::display($tpl);
    }
}



?>