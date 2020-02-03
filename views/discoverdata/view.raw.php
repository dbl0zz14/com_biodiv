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
class BioDivViewDiscoverData extends JViewLegacy
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
		
		//print("DiscoverData display called");
		
		$app = JFactory::getApplication();
		$this->lat_start =
		(int)$app->getUserStateFromRequest('com_biodiv.latstart', 'latstart');
		$this->lat_end =
		(int)$app->getUserStateFromRequest('com_biodiv.latend', 'latend');
		$this->lon_start =
		(int)$app->getUserStateFromRequest('com_biodiv.lonstart', 'lonstart');
		$this->lon_end =
		(int)$app->getUserStateFromRequest('com_biodiv.lonend', 'lonend');
		
		// This defaults to 12 months
		$this->data = discoverData ( $this->lat_start, $this->lat_end, $this->lon_start, $this->lon_end );
		
		//print("DiscoverData data set");
		

		// Display the view
		parent::display($tpl);
    }
}



?>