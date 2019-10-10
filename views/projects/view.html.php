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
* HTML View class for the Projects page 
*
* @since 0.0.1
*/
class BioDivViewProjects extends JViewLegacy
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
    //$person_id or die("No person_id");
	
	// Get all the text snippets for this view in the current language
	$this->translations = getTranslations("project");


    $this->root = 
    $this->status = array();

    $db = JDatabase::getInstance(dbOptions());

    $query = $db->getQuery(true);
    $query->select("COUNT(DISTINCT person_id)");
    $query->from("Animal");
    $db->setQuery($query);
    $this->status['Total spotters in system'] = $db->loadResult();
	
	// call new biodiv.php function myProjects() for now need to replace with a 
	// function to display all (top level?) projects.  Or all those marked for display? 
	// (Actually all except private ones.)
	$this->projects = listedProjects();
	
	//print "<br/>Got " . count($this->projects) . " non-private projects <br/>They are:<br>";
	//print_r ( $this->projects );
        

    // Display the view
    parent::display($tpl);
  }
}



?>