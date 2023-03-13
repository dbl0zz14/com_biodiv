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
class BioDivViewShareResourceSet extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		$this->schoolUser = Biodiv\SchoolCommunity::getSchoolUser();
		
		$this->personId = $this->schoolUser->person_id;
		
		$this->setId = null;
		
		if ( $this->personId ) {
	
			// Check for resource id 
			$input = JFactory::getApplication()->input;
			
			$this->setId = $input->getInt('id', 0);
			
			$this->shareLevel = $input->getInt('share', 0);
			
			if ( $this->setId ) {
				
				$db = JDatabaseDriver::getInstance(dbOptions());
				
				$query = $db->getQuery(true)
						->select("R.resource_id, R.access_level from Resource R")
						->where("set_id = " . $this->setId );
						
				if ( $this->schoolUser->role_id != Biodiv\SchoolCommunity::ADMIN_ROLE ) {
					$query->where("person_id = " . $this->personId );
				}
						
				$db->setQuery($query);
				
				$existingResources = $db->loadObjectList("resource_id");
				
				foreach ( $existingResources as $resourceId=>$resource ) {
					
					$currentLevel = $resource->access_level;
					
					if ( $this->shareLevel != $currentLevel ) {
						
						$resource = new stdClass();
						$resource->person_id = $this->personId;
						$resource->resource_id = $resourceId;
						$resource->access_level = $this->shareLevel;
						$resource->timestamp = "CURRENT_TIMESTAMP";

						// Insert the object into the table.
						$result = $db->updateObject('Resource', $resource, 'resource_id');
					}
				}
			}

		}

		// Display the view
		parent::display($tpl);
		
    }
}



?>