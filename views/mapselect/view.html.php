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
class BioDivViewMapSelect extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
			
	  $app = JFactory::getApplication();
	  
	  

	  $this->root = JURI::root() . "?option=com_biodiv";
	  $this->site_id = $app->getUserStateFromRequest('com_biodiv.site_id', 'site_id',0);
	  $this->site_name = codes_getName($this->site_id, "site");
	  $this->site_details = codes_getDetails($this->site_id, "site");
	  $this->grid_ref = $this->site_details['grid_ref'];
	  $this->latitude = $this->site_details['latitude'];
	  $this->longitude = $this->site_details['longitude'];
	  // Display the view
	  parent::display($tpl);
    }
}



?>