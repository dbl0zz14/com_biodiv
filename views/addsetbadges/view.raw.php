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
* HTML View class for the Biodiversity Monitoring component
* Display task details and article
*
* @since 0.0.1
*/
class BioDivViewAddSetBadges extends JViewLegacy
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
		
		if ( $this->schoolUser ) {
			
			$input = JFactory::getApplication()->input;
			$this->setId = $input->getInt ( 'set', 0 );
			
			$this->badgeIds = $input->get('badge', array(), 'ARRAY');
			
			if ( $this->setId ) {
				
				$this->setBadges = Biodiv\ResourceSet::setResourceSetBadges ( $this->schoolUser, $this->setId, $this->badgeIds );
				
			}
		}
	  
		parent::display($tpl);
		
    }
}



?>