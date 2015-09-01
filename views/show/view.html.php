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
* HTML View class for the BioDiv Component
*
* @since 0.0.1
*/
class BioDivViewShow extends JViewLegacy
{
        /**
         *
         * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
         *
         * @return  void
         */

        public function display($tpl = null) 
        {
               // Assign data to the view
	  $app = JFactory::getApplication();
	  $this->photo_id =
	    (int)$app->getUserStateFromRequest('com_biodiv.photo_id', 'photo_id', 0);

	  if(!$this->photo_id){
	    die("No photo_id specified");
	  }

	  $db = JDatabase::getInstance(dbOptions());

	  // make sure it has been looked at by somebody

	  $query = $db->getQuery(true);
	  $query->select("COUNT(*)");
	  $query->from("Animal");
	  $query->where("photo_id = " . (int)$this->photo_id);
	  $db->setQuery($query);
	  $classified = $db->loadResult();

	  if(!$classified){
	    die("Photo has not been looked at before");
	  }

	  $photoDetails = codes_getDetails($this->photo_id, "photo");
	  if($photoDetails['contains_human']){
	    die("Photo contains a human");
	  }

	  $this->photoDetails = codes_getDetails($this->photo_id, 'photo');

	  // Display the view
	  parent::display($tpl);
        }
}



?>