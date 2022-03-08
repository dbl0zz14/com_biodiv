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
class BioDivViewPinResource extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		error_log ( "LikeResource display function called" );
		
		// Get all the text snippets for this view in the current language
		$this->translations = getTranslations("likeresource");
		
		$this->personId = userID();
		
		$this->resourceId = null;
		
		$this->totalLikes = 0;
		
		if ( $this->personId ) {
			
			// Only ecologists can pin
			if ( Biodiv\SchoolCommunity::isEcologist() ) {
	
				// Check whether got id 
				$input = JFactory::getApplication()->input;
				
				$this->resourceId = $input->getInt('id', 0);
				
				$this->isPin = $input->getInt('pin', 0);
				
				error_log ( "LikeResource resource id = " . $this->resourceId );
				error_log ( "LikeResource is pin = " . $this->isPin );
				
				if ( $this->resourceId ) {
					
					error_log ( "Got a resource id" );
					
					$db = JDatabaseDriver::getInstance(dbOptions());
					
					error_log ( "Got db" );
					
					$query = $db->getQuery(true)
							->select("PR.pr_id, PR.resource_id from PinnedResource PR")
							->where("resource_id = " . $this->resourceId );
							
					$db->setQuery($query);
					
					$existingPin = $db->loadAssocList();
					
								
					error_log ( "Got " . count($existingPin) . " existing pin for this resource " );
						
					if ( $this->isPin ) {
						// add to pins if not already
						if ( count($existingPin) == 0 ) {
							
							error_log ( "Inserting new pin" );
							
							$pin = new stdClass();
							$pin->person_id = $this->personId;
							$pin->resource_id = $this->resourceId;

							// Insert the object into the table.
							$result = $db->insertObject('PinnedResource', $pin);
							
						}
						
						
					}
					else {
						// remove from pins
						if ( count($existingPin) > 0 ) {
							
							$query = $db->getQuery(true);

							// delete all pins for this resource
							$conditions = array(
								$db->quoteName('resource_id') . ' = ' . $this->resourceId,
							);

							$query->delete($db->quoteName('PinnedResource'));
							$query->where($conditions);

							$db->setQuery($query);

							$result = $db->execute();
						}
					}
				}
			}
		}

		// Display the view
		parent::display($tpl);
		
    }
}



?>