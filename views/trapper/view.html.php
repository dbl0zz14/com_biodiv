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
class BioDivViewTrapper extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
	  
	  $isCamera = getSetting("camera") == "yes";
	  $this->siteHelper = new SiteHelper($isCamera);
	  
	  $this->fields = $this->siteHelper->getFieldsArray();
	  $this->help = $this->siteHelper->getHelpArray();
	
      $this->sites = $this->siteHelper->getSites();

	  
	  $this->siteCount = $this->siteHelper->getSitePhotoCount();
	  
	  // Projects additions.
	  $this->projecthelp = "All projects which this site and this user are members of.";
	  
	  $this->userprojects = $this->siteHelper->getUserProjects();
	  
	  // For each user project get any additional data required
	  $this->projectsitedata = $this->siteHelper->getProjectSiteData();
	  
	  $this->projectsitedataJSON = $this->siteHelper->getProjectSiteDataJSON();
	  
	  
	  $this->projects = $this->siteHelper->getProjects();
	  
	  // Display the view
	  parent::display($tpl);

	}
}



?>
