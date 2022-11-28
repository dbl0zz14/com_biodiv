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
class BioDivViewShareResource extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		$this->personId = userID();
		
		$this->resourceId = null;
		
		if ( $this->personId ) {
	
			// Check for resource id 
			$input = JFactory::getApplication()->input;
			
			$this->resourceId = $input->getInt('id', 0);
			
			$this->shareLevel = $input->getInt('share', 0);
			
			if ( $this->resourceId ) {
				
				$db = JDatabaseDriver::getInstance(dbOptions());
				
				$query = $db->getQuery(true)
						->select("R.resource_id, R.access_level from Resource R")
						->where("resource_id = " . $this->resourceId )
						->where("person_id = " . $this->personId );
						
				$db->setQuery($query);
				
				$existingResource = $db->loadAssoc();
				
				if ( count($existingResource) > 0 ) {
					
					$currentLevel = $existingResource['access_level'];
					
					if ( $this->shareLevel != $currentLevel ) {
						
						$resource = new stdClass();
						$resource->person_id = $this->personId;
						$resource->resource_id = $this->resourceId;
						$resource->access_level = $this->shareLevel;

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