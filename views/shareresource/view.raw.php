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
		error_log ( "ShareResource display function called" );
		
		// Get all the text snippets for this view in the current language
		$this->translations = getTranslations("shareresource");
		
		$this->personId = userID();
		
		$this->resourceId = null;
		
		if ( $this->personId ) {
	
			// Check for resource id 
			$input = JFactory::getApplication()->input;
			
			$this->resourceId = $input->getInt('id', 0);
			
			$this->shareLevel = $input->getInt('share', 0);
			
			error_log ( "ShareResource resource id = " . $this->resourceId );
			error_log ( "ShareResource share = " . $this->shareLevel );
			
			if ( $this->resourceId ) {
				
				error_log ( "Got a resource id" );
				
				$db = JDatabaseDriver::getInstance(dbOptions());
				
				error_log ( "Got db" );
				
				$query = $db->getQuery(true)
						->select("R.resource_id, R.access_level from Resource R")
						->where("resource_id = " . $this->resourceId )
						->where("person_id = " . $this->personId );
						
				$db->setQuery($query);
				
				$existingResource = $db->loadAssoc();
				
				if ( count($existingResource) > 0 ) {
					
					$currentLevel = $existingResource['access_level'];
					
					error_log ( "Got existing resource for this resource and person with access level " . $currentLevel );
				
					if ( $this->shareLevel != $currentLevel ) {
						
						error_log ( "Updating with new access level " . $share );
						
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