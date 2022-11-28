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
class BioDivViewLikeResource extends JViewLegacy
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
		
		$this->totalLikes = 0;
		
		if ( $this->personId ) {
	
			// Check whether set id 
			$input = JFactory::getApplication()->input;
			
			$this->resourceId = $input->getInt('id', 0);
			
			$this->isLike = $input->getInt('like', 0);
			
			if ( $this->resourceId ) {
				
				$db = JDatabaseDriver::getInstance(dbOptions());
				
				$query = $db->getQuery(true)
						->select("LR.lr_id, LR.resource_id from LikedResource LR")
						->where("resource_id = " . $this->resourceId )
						->where("person_id = " . $this->personId );
						
				$db->setQuery($query);
				
				$existingLikes = $db->loadAssocList();
				
							
				if ( $this->isLike ) {
					// add to favourites for this person if not already
					if ( count($existingLikes) == 0 ) {
						
						$like = new stdClass();
						$like->person_id = $this->personId;
						$like->resource_id = $this->resourceId;

						// Insert the object into the table.
						$result = $db->insertObject('LikedResource', $like);
						
					}
					
					
				}
				else {
					// remove from favourites for this person
					if ( count($existingLikes) > 0 ) {
						
						$query = $db->getQuery(true);

						// delete all custom keys for user 1001.
						$conditions = array(
							$db->quoteName('person_id') . ' = ' . $this->personId, 
							$db->quoteName('resource_id') . ' = ' . $this->resourceId,
						);

						$query->delete($db->quoteName('LikedResource'));
						$query->where($conditions);

						$db->setQuery($query);

						$result = $db->execute();
					}
				}
				 
				$query = $db->getQuery(true)
						->select("count(*) from LikedResource LR")
						->where("resource_id = " . $this->resourceId );
						
				$db->setQuery($query);
				
				$this->totalLikes = $db->loadResult();
				
			}

		}

		// Display the view
		parent::display($tpl);
		
    }
}



?>