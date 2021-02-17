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
class BioDivViewSonogram extends JViewLegacy
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

	  $db = JDatabase::getInstance(dbOptions());
	  
	  // Limit to x number of files at a time...
	  
	  $query = $db->getQuery(true);
	  $query->select("photo_id, dirname, filename, upload_filename")
	    ->from($db->quoteName("Photo"))
	    ->where("status = -1")
		->where("sequence_id != 0");
		
	  $db->setQuery($query, 0, 25);
	  $this->files = $db->loadAssocList();
	  
	  // Are we generating sonograms?  If not then we'll do nothing
	  $this->sonograms = getSetting("generate_sonograms") == "yes";
	  
	  

	  // Display the view
	  parent::display($tpl);
    }
}



?>
