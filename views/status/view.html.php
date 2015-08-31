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
class BioDivViewStatus extends JViewLegacy
{
  /**
   *
   * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
   *
   * @return  void
   */
  
  public function display($tpl = null) 
  {
    $person_id = (int)userID();
    $person_id or die("No person_id");

    $this->root = 
    $this->status = array();

    $db = JDatabase::getInstance(dbOptions());

    $query = $db->getQuery(true);
    $query->select("COUNT(DISTINCT photo_id)");
    $query->from("Animal");
    $query->where("person_id = " . $person_id);
    $db->setQuery($query);
    $this->status['Number of photos classifed'] = $db->loadResult();

    $query = $db->getQuery(true);
    $query->select("COUNT(*)");
    $query->from("Animal");
    $query->where("person_id = " . $person_id);
    $db->setQuery($query);
    $this->status['Number of animals identified'] = $db->loadResult();

    $query = $db->getQuery(true);
    $query->select("COUNT(DISTINCT species)");
    $query->from("Animal");
    $query->where("person_id = " . $person_id);
    $db->setQuery($query);
    $this->status['Number of species identified'] = $db->loadResult();

    $query = $db->getQuery(true);
    $query->select("COUNT(*)");
    $query->from("Photo");
    $db->setQuery($query);
    $this->status['Total photos in system'] = $db->loadResult();

    // Display the view
    parent::display($tpl);
  }
}



?>