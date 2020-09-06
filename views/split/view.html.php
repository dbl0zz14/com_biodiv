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
class BioDivViewSplit extends JViewLegacy
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
	  $query->select("ts_id, dirname, filename")
	    ->from($db->quoteName("ToSplit"))
	    ->where("status = 0");
		
	  $db->setQuery($query, 0, 10);
	  $this->files = $db->loadAssocList();
	  
	  // What is the ideal file length
	  $this->fileLength = getSetting("max_clip_length");
	  
	  // set a default just in case - use 20 seconds so it's obvious
	  if ( !$this->fileLength ) $this->fileLength = 20;
	  

	  // Display the view
	  parent::display($tpl);
    }
}



?>
