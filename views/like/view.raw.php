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
class BioDivViewLike extends JViewLegacy
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
	  ($person_id = (int)userID()) or die("No person_id");
	  $app = JFactory::getApplication();
	  $this->photo_id =
	    (int)$app->getUserStateFromRequest('com_biodiv.like_photo_id', 'photo_id');

	  $db = JDatabase::getInstance(dbOptions());

	  $query = $db->getQuery(true);
	  $query->select("count(*)")
	    ->from("Animal")
	    ->where("photo_id = ".$this->photo_id)
		->where("species = 97" )
	    ->where("person_id = ".$person_id);

	  $db->setQuery($query);
	  $this->likes = $db->loadResult();

	  // Display the view
	  parent::display($tpl);
        }
}



?>