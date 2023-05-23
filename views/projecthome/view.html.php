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
	
	$input = $app->input;
		
	$this->includePrivate = $input->getInt('private', 0);
		
	
	// Determine whether audio site
	$isCamera = getSetting("camera") == "yes";
	$this->classifyView = $isCamera ? "classify" : "classifybirds";

	
	// Remove any stored photo id or animal ids on project load.
	$app->setUserState('com_biodiv.photo_id', null);
	$app->setUserState('com_biodiv.animal_ids', 0);
    
	
	$this->project_id =
	    (int)$app->getUserStateFromRequest('com_biodiv.project_id', 'project_id', 0);
		
	$displayOptionArray = getSingleProjectOptions($this->project_id, 'projectdisplay');
	
	// Just get the option names.
	$this->displayOptions = array_column($displayOptionArray, 'option_name');
	
	if ( $this->includePrivate ) {
		$this->subProjects = getSubProjectsById($this->project_id, false);
	}
	else {
		$this->subProjects = getSubProjectsById($this->project_id, true);
	}
	
	// Remove this project from the sub projects list...
	unset($this->subProjects[$this->project_id]);
	
	$this->projectTree = getProjectTree($this->project_id);
	
	
	$article = JTable::getInstance("content");
	
	$this->project = codes_getDetails($this->project_id, "project");
	
	// Get the associated article, if there is one, in the urrent language if possible
	$article_id = getAssociatedArticleId($this->project['article_id']);
	
	
	$article->load($article_id); 
	
  	$this->title = $article->title;
	$this->introtext = $article->introtext;
	$this->fulltext = $article->fulltext;
	
	
	$this->access_level = $this->project['access_level'];
		

    // Display the view
    parent::display($tpl);
  }
}



?>