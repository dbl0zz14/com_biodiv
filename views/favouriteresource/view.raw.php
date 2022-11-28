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
class BioDivViewFavouriteResource extends JViewLegacy
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
	
			// Check whether set id 
			$input = JFactory::getApplication()->input;
			
			$this->resourceId = $input->getInt('id', 0);
			
			$this->isFav = $input->getInt('fav', 0);
			
			error_log ( "ResourceFile resource id = " . $this->resourceId );
			error_log ( "ResourceFile is fav = " . $this->isFav );
			
			if ( $this->resourceId ) {
				
				error_log ( "Got a resource id" );
				
				$db = JDatabaseDriver::getInstance(dbOptions());
				
				error_log ( "Got db" );
				
				$query = $db->getQuery(true)
						->select("FR.fr_id, FR.resource_id from FavouriteResource FR")
						->where("resource_id = " . $this->resourceId )
						->where("person_id = " . $this->personId );
						
				$db->setQuery($query);
				
				$existingFavs = $db->loadAssocList();
				
				error_log ( "Got " . count($existingFavs) . " existing favourites for this resource and person" );
					
				if ( $this->isFav ) {
					// add to favourites for this person if not already
					if ( count($existingFavs) == 0 ) {
						
						error_log ( "Inserting new favourite" );
						
						$fav = new stdClass();
						$fav->person_id = $this->personId;
						$fav->resource_id = $this->resourceId;

						// Insert the object into the table.
						$result = $db->insertObject('FavouriteResource', $fav);
						
					}
					
					
				}
				else {
					// remove from favourites for this person
					if ( count($existingFavs) > 0 ) {
						
						$query = $db->getQuery(true);

						// delete all custom keys for user 1001.
						$conditions = array(
							$db->quoteName('person_id') . ' = ' . $this->personId, 
							$db->quoteName('resource_id') . ' = ' . $this->resourceId,
						);

						$query->delete($db->quoteName('FavouriteResource'));
						$query->where($conditions);

						$db->setQuery($query);

						$result = $db->execute();
					}
				}
				 
			}

		}

		// Display the view
		parent::display($tpl);
		
    }
}



?>