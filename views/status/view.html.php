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
	
	// Set the photo to zero on load and the classify option back to default 0
	$app = JFactory::getApplication();
    $app->setUserState('com_biodiv.photo_id', 0);
	$app->setUserState('com_biodiv.classify_only_project', 0);
    $app->setUserState('com_biodiv.classify_project', 0);
    $app->setUserState('com_biodiv.classify_self', 0);
    $app->setUserState('com_biodiv.my_project', 0);
    

    $db = JDatabase::getInstance(dbOptions());

    $query = $db->getQuery(true);
    $query->select("COUNT(DISTINCT photo_id)");
    $query->from("Animal");
    $query->where("person_id = " . $person_id);
    $db->setQuery($query);
    $this->status['Number of classifications'] = $db->loadResult();

    $query = $db->getQuery(true);
    $query->select("COUNT(*)");
    $query->from("Animal");
    $query->where("person_id = " . $person_id);
    $query->where("species not in (86,87,97)");
    $db->setQuery($query);
    $this->status['Number of animals identified'] = $db->loadResult();

    $query = $db->getQuery(true);
    $query->select("COUNT(DISTINCT species)");
    $query->from("Animal");
    $query->where("person_id = " . $person_id);
    $query->where("species not in (86,97)");
    $db->setQuery($query);
    $this->status['Number of species identified'] = $db->loadResult();

    $query = $db->getQuery(true);
    $query->select("COUNT(*)");
    $query->from("Photo");
    $db->setQuery($query);
    $this->status['Total photos in system'] = $db->loadResult();

    $query = $db->getQuery(true);
    $query->select("COUNT(DISTINCT person_id)");
    $query->from("Animal");
    $db->setQuery($query);
    $this->status['Total spotters in system'] = $db->loadResult();
	
	// call new biodiv.php function myProjects()
	$this->projects = myProjects();
	$this->mylikes = getLikes(1);
	
    // Display the view
    parent::display($tpl);
  }
}



?>