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
class BioDivViewUploadM extends JViewLegacy
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
	  $this->upload_id = $app->getUserStateFromRequest('com_biodiv.upload_id', 'upload_id', 0);
	  if(!$this->upload_id){
	    die("No upload_id");
	  }

	  $uploadDetails = codes_getDetails($this->upload_id, 'upload');
	  $this->site_id = $uploadDetails['site_id'];
	  $this->site_name = codes_getName($this->site_id, "site");
	  if($uploadDetails['person_id'] != userID()){
	    die("You cannot upload here: should be");
	  }
	  $db = JDatabase::getInstance(dbOptions());
	  $query = $db->getQuery(true);
	  $query->select("photo_id, taken, upload_filename")
	    ->from($db->quoteName("Photo"))
	    ->where("upload_id = " .(int)$this->upload_id)
	    ->where("person_id = " .(int)userID())
	    ->order("taken");
	  $db->setQuery($query);
	  $this->prev_photos = $db->loadAssocList();

			 
  
	  // Display the view


	  parent::display($tpl);
        }
}



?>