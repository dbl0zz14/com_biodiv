<?php
namespace MammalWeb\Component\Biodiv\Site\View\ProjectUsers;
  
// No direct access to this file
defined('_JEXEC') or die;
 
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
 
/**
 * @package     Joomla.Site
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2024 MammalWeb Limited. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

/**
 * View for project users raw view, used eg in project admin dashboard
 */
class RawView extends BaseHtmlView {
	/**
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */

    public function display($tpl = null) 
    {
		$reportModel = $this->getModel();

		$this->personId = $reportModel->userID();

		if ( $this->personId ) {
		
			// CHECK admin user for this project
			// Check user is project admin for this project
			$allProjects = $reportModel->getAdminProjects();
			$allIds = array_keys ( $allProjects );
				
			$app = Factory::getApplication();
			$input = $app->input;
				
			$this->projectId = $input->getInt('id', 0);
			
			$this->projectName = codes_getName($this->projectId, "project");
			
			$this->userMessages = null;
			
			$messages = $input->getString('message', 0);
			
			if ( $messages ) {
				$this->userMessages = json_decode($messages);
			}

			if ( in_array ($this->projectId, $allIds ) ) {
				
				$this->access = true;
				
				$this->users = $reportModel->getProjectUsers ( $this->projectId );
				
				// $options = dbOptions();
				// $userDb = $options['userdb'];
				// $prefix = $options['userdbprefix'];
				
				// $db = \JDatabaseDriver::getInstance(dbOptions());
				
				// // For now, simple, but would be good to add any users of parent projects...
				// $query = $db->getQuery(true)
					// ->select("PUM.person_id, U.username, U.name, U.email, R.display_text as role from ProjectUserMap PUM")
					// ->innerJoin($userDb . "." . $prefix ."users U on PUM.person_id = U.id")
					// ->innerJoin("Role R on R.role_id = PUM.role_id")
					// ->where("PUM.project_id = " . $this->projectId)
					// ->order("U.email");
					
				
				// $db->setQuery($query);
				
				// //error_log("ProjectUsers select query created: " . $query->dump());
				
				// $this->users = $db->loadObjectList();
			
				
			}
			else {
				
				$this->access = false;
				
				
			}
		}
		// Display the view
		parent::display($tpl);
    }
}



?>