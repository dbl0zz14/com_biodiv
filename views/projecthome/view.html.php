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
class BioDivViewProjecthome extends JViewLegacy
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

    $this->root = 
    $this->status = array();
	
	$app = JFactory::getApplication();
	
	// Remove any stored photo id on project load.
	$app->setUserState('com_biodiv.photo_id', null);
	
	$this->project_id =
	    (int)$app->getUserStateFromRequest('com_biodiv.project_id', 'project_id', 0);
		
	$this->project = projectDetails($this->project_id);
	
	$displayOptionArray = getSingleProjectOptions($this->project_id, 'projectdisplay');
	
	// Just get the option names.
	$this->displayOptions = array_column($displayOptionArray, 'option_name');
	
	$this->subProjects = getSubProjects($this->project->project_prettyname, true);
	
	// Remove this project from the sub projects list...
	unset($this->subProjects[$this->project_id]);
	
	$this->projectTree = getProjectTree($this->project_id);
	
	
	$article = JTable::getInstance("content");
	$project_id = JRequest::getInt("project_id");
	$project = codes_getDetails($project_id, "project");
	$article_id = $project['article_id'];
	$article->load($article_id); 
  //	  print_r($article);
	$this->title = $article->title;
	$this->introtext = $article->introtext;
	$this->access_level = $project['access_level'];
	
	

    // Display the view
    parent::display($tpl);
  }
}



?>