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
class BioDivViewFavouriteResourceSet extends JViewLegacy
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
		
		if ( $this->personId ) {
	
			// Check whether set id 
			$input = JFactory::getApplication()->input;
			
			$this->setId = $input->getInt('id', 0);
			
			$this->isFave = $input->getInt('fave', 0);
			
			if ( $this->setId ) {
				
				$db = JDatabaseDriver::getInstance(dbOptions());
				
				$query = $db->getQuery(true)
						->select("FRS.fav_id, FRS.set_id from FavouriteResourceSet FRS")
						->where("set_id = " . $this->setId )
						->where("person_id = " . $this->personId );
						
				$db->setQuery($query);
				
				$existingFaves = $db->loadAssocList();
				
				if ( $this->isFave ) {
					
					// add to favourites for this person if not already
					if ( count($existingFaves) == 0 ) {
						
						$fave = new stdClass();
						$fave->person_id = $this->personId;
						$fave->set_id = $this->setId;

						// Insert the object into the table.
						$result = $db->insertObject('FavouriteResourceSet', $fave);
						
					}
				}
				else {
					// remove from favourites for this person
					if ( count($existingFaves) > 0 ) {
						
						$query = $db->getQuery(true);

						// delete all custom keys for user 1001.
						$conditions = array(
							$db->quoteName('person_id') . ' = ' . $this->personId, 
							$db->quoteName('set_id') . ' = ' . $this->setId,
						);

						$query->delete($db->quoteName('FavouriteResourceSet'));
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