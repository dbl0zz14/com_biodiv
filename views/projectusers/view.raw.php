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
class BioDivViewProjectUsers extends JViewLegacy
{
	/**
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */

    public function display($tpl = null) 
    {
		//Assign data to the view
		$this->personId = (int)userID();
		
		$this->translations = getTranslations("projectusers");
		
		if ( $this->personId ) {
		
			// CHECK admin user for this project
			// Check user is project admin for this project
			$allProjects = myAdminProjects();
			$allIds = array_keys ( $allProjects );
				
			
			$app = JFactory::getApplication();
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
				
				$options = dbOptions();
				$userDb = $options['userdb'];
				$prefix = $options['userdbprefix'];
				
				$db = \JDatabaseDriver::getInstance(dbOptions());
				
				// For now, simple, but would be good to add any users of parent projects...
				$query = $db->getQuery(true)
					->select("PUM.person_id, U.username, U.name, U.email, R.display_text as role from ProjectUserMap PUM")
					->innerJoin($userDb . "." . $prefix ."users U on PUM.person_id = U.id")
					->innerJoin("Role R on R.role_id = PUM.role_id")
					->where("PUM.project_id = " . $this->projectId)
					->order("U.email");
					
				
				$db->setQuery($query);
				
				//error_log("ProjectUsers select query created: " . $query->dump());
				
				$this->users = $db->loadObjectList();
			
				
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