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
class BioDivViewUpload extends JViewLegacy
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
	  
	  // Get all the text snippets for this view in the current language
	  $this->translations = getTranslations("upload");

	  $this->root = JURI::root() . "?option=com_biodiv";
	  $this->site_id = $app->getUserStateFromRequest('com_biodiv.site_id', 'site_id',0);
	  $this->site_name = codes_getName($this->site_id, "site");
	  $this->start_date = "";
	  $this->end_date = "";

	  $db = JDatabase::getInstance(dbOptions());
	  $query = $db->getQuery(true);
	  $query->select("upload_id, collection_date, timestamp")
	    ->from($db->quoteName("Upload"))
	    ->where("site_id = " .(int)$this->site_id)
	    ->where("person_id = " .(int)userID())
	    ->order("collection_date DESC, upload_id DESC");
	  $db->setQuery($query, 0, 1); // LIMIT 1
	  $uploadDetails = $db->loadAssoc();
	  $this->previous_upload_id = $uploadDetails['upload_id'];
	  $this->previous_collection_date = new Jdate($uploadDetails['collection_date']);
	  $this->previous_upload_date = new JDate($uploadDetails['timestamp']);

	  // Display the view


	  parent::display($tpl);
    }
}



?>