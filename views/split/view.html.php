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
		
	  error_log ( "BioDivViewSplit display called" );
	  
	  $app = JFactory::getApplication();

	  $db = JDatabase::getInstance(dbOptions());
	  
	  // Limit to x number of files at a time...
	  
	  $query = $db->getQuery(true);
	  $query->select("of_id, dirname, filename, upload_filename")
	    ->from($db->quoteName("OriginalFiles"))
	    ->where("status = 0");
		
	  $db->setQuery($query, 0, 500);
	  $this->files = $db->loadAssocList();
	  
	  // Mark all the files as being worked on - so set status to -1
	  foreach ( $this->files as $origfile ) {
		$fields = new stdClass();
		$fields->of_id = $origfile['of_id'];
		$fields->status = -1;
		$db->updateObject('OriginalFiles', $fields, 'of_id');
	  }
	  
	  
	  // What is the ideal file length
	  $this->fileLength = getSetting("max_clip_length");
	  
	  // set a default just in case - use 20 seconds so it's obvious
	  if ( !$this->fileLength ) $this->fileLength = 20;
	  
	  // Are we generating sonograms?
	  $this->sonograms = getSetting("generate_sonograms") == "yes";
	  
	  

	  // Display the view
	  parent::display($tpl);
    }
}



?>
