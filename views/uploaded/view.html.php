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
* HTML View class for viewing all uploaded files
*
* @since 0.0.1
*/
class BioDivViewUploaded extends JViewLegacy
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
	  $this->start_date = "";
	  $this->end_date = "";

	  $db = JDatabase::getInstance(dbOptions());
	  $query = $db->getQuery(true);
	  $query->select("upload_filename, taken")
	    ->from($db->quoteName("Photo"))
	    ->where("site_id = " .(int)$this->site_id)
	    ->where("person_id = " .(int)userID())
	    ->order("upload_filename");


	  $db->setQuery($query);
	  $this->photos = $db->loadAssocList();

	  // Display the view


	  parent::display($tpl);
        }
}



?>