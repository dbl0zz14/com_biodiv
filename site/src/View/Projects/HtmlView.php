<?php

namespace MammalWeb\Component\Biodiv\Site\View\Projects;
 
// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * @package     Joomla.Site
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2024 MammalWeb Limited. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

/**
 * View for the projects list display page
 */

class HtmlView extends BaseHtmlView {

	/**
     * Display the view
     *
     * @param   string  $template  The name of the layout file to parse.
     * @return  void
     */
    
	public function display($tpl = null) 
	{
		
		$projectModel = $this->getModel();
		
		$this->projects = $projectModel->listedProjects();
		
		parent::display($tpl);
	  
	}
	
  // /**
   // *
   // * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
   // *
   // * @return  void
   // */
  
  // public function display($tpl = null) 
  // {
    // $person_id = (int)userID();
    // //$person_id or die("No person_id");
	
	// $this->root = 
    // $this->status = array();

    // $db = JDatabase::getInstance(dbOptions());

    // $query = $db->getQuery(true);
    // $query->select("COUNT(DISTINCT person_id)");
    // $query->from("Animal");
    // $db->setQuery($query);
    // $this->status['Total spotters in system'] = $db->loadResult();
	
	// // call new biodiv.php function myProjects() for now need to replace with a 
	// // function to display all (top level?) projects.  Or all those marked for display? 
	// // (Actually all except private ones.)
	// $this->projects = listedProjects();
	
	    

    // // Display the view
    // parent::display($tpl);
  // }
}



?>