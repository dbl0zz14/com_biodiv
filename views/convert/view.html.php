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
class BioDivViewConvert extends JViewLegacy
{
	/**
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */

	public function display($tpl = null) 
	{
		
	  error_log ( "BioDivViewConvert display called" );
	  
	  $app = JFactory::getApplication();

	  $db = JDatabase::getInstance(dbOptions());
	  
	  // Limit to x number of files at a time...
	  
	  $query = $db->getQuery(true);
	  $query->select("of_id, dirname, filename, upload_filename, taken")
	    ->from($db->quoteName("OriginalFiles"))
	    ->where("status = 0")
		->where("filename like '%avi'" );
		
	  $db->setQuery($query, 0, 10);
	  $this->files = $db->loadAssocList();
	  
	  // Mark all the files as being worked on - so set status to -1
	  foreach ( $this->files as $origfile ) {
		$fields = new stdClass();
		$fields->of_id = $origfile['of_id'];
		$fields->status = -1;
		$db->updateObject('OriginalFiles', $fields, 'of_id');
	  }

	  // Display the view
	  parent::display($tpl);
    }
}



?>
