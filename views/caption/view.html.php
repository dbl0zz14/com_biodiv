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
class BioDivViewCaption extends JViewLegacy
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
	  $query->select("C.photo_id, C.text, P.dirname, P.upload_filename, P.filename")
	    ->from("Caption C" )
		->innerJoin("Photo P on C.photo_id = P.photo_id")
	    ->where("C.processed = 0");
		
	  $db->setQuery($query, 0, 15);
	  $this->files = $db->loadAssocList();
	  
	  

	  // Display the view
	  parent::display($tpl);
    }
}



?>
