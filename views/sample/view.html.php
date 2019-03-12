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
class BioDivViewSample extends JViewLegacy
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

	  // Determine which projects to sample
	  $db = JDatabase::getInstance(dbOptions());
	  $query = $db->getQuery(true);
	  $query->select("distinct(project_id)")
	    ->from($db->quoteName("ProjectOptions") . " PO")
		->innerJoin($db->quoteName("Options") . " O on PO.option_id = O.option_id where O.option_name = 'sample'");
	    
	  $db->setQuery($query);
	  $this->projects = $db->loadColumn();

	  // Display the view


	  parent::display($tpl);
    }
}



?>
