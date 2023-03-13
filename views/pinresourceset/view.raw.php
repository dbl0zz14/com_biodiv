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
class BioDivViewPinResourceSet extends JViewLegacy
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
		
		if ( $this->personId ) {
			
			// Only admin can pin
			if ( Biodiv\SchoolCommunity::isAdmin() ) {
	
				// Check whether set id 
				$input = JFactory::getApplication()->input;
				
				$this->setId = $input->getInt('id', 0);
				
				$this->isPin = $input->getInt('pin', 0);
				
				if ( $this->setId ) {
					
					$db = JDatabaseDriver::getInstance(dbOptions());
					
					$query = $db->getQuery(true)
							->select("PRS.pin_id, PRS.set_id from PinnedResourceSet PRS")
							->where("set_id = " . $this->setId );
							
					$db->setQuery($query);
					
					$existingPin = $db->loadAssocList();
					
								
					if ( $this->isPin ) {
						// add to pins if not already
						if ( count($existingPin) == 0 ) {
							
							$pin = new stdClass();
							$pin->person_id = $this->personId;
							$pin->set_id = $this->setId;

							// Insert the object into the table.
							$result = $db->insertObject('PinnedResourceSet', $pin);
							
						}
						
						
					}
					else {
						// remove from pins
						if ( count($existingPin) > 0 ) {
							
							$query = $db->getQuery(true);

							// delete all pins for this resource
							$conditions = array(
								$db->quoteName('set_id') . ' = ' . $this->setId,
							);

							$query->delete($db->quoteName('PinnedResourceSet'));
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