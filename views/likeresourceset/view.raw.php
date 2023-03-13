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
class BioDivViewLikeResourceSet extends JViewLegacy
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
		
		$this->setId = null;
		
		$this->totalLikes = 0;
		
		if ( $this->personId ) {
	
			// Check whether set id 
			$input = JFactory::getApplication()->input;
			
			$this->setId = $input->getInt('id', 0);
			
			$this->isLike = $input->getInt('like', 0);
			
			if ( $this->setId ) {
				
				$db = JDatabaseDriver::getInstance(dbOptions());
				
				$query = $db->getQuery(true)
						->select("L.like_id, L.set_id from LikedResourceSet L")
						->where("set_id = " . $this->setId )
						->where("person_id = " . $this->personId );
						
				$db->setQuery($query);
				
				$existingLikes = $db->loadAssocList();
				
							
				if ( $this->isLike ) {
					// add to favourites for this person if not already
					if ( count($existingLikes) == 0 ) {
						
						$like = new stdClass();
						$like->person_id = $this->personId;
						$like->set_id = $this->setId;

						// Insert the object into the table.
						$result = $db->insertObject('LikedResourceSet', $like);
						
					}
					
					
				}
				else {
					// remove from favourites for this person
					if ( count($existingLikes) > 0 ) {
						
						$query = $db->getQuery(true);

						$conditions = array(
							$db->quoteName('person_id') . ' = ' . $this->personId, 
							$db->quoteName('set_id') . ' = ' . $this->setId,
						);

						$query->delete($db->quoteName('LikedResourceSet'));
						$query->where($conditions);

						$db->setQuery($query);

						$result = $db->execute();
					}
				}
				 
				$query = $db->getQuery(true)
						->select("count(*) from LikedResourceSet L")
						->where("set_id = " . $this->setId );
						
				$db->setQuery($query);
				
				$this->totalLikes = $db->loadResult();
				
			}

		}

		// Display the view
		parent::display($tpl);
		
    }
}



?>