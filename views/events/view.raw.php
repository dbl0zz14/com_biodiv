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
class BioDivViewEvents extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		error_log ( "Events display function called" );
		
		// Default to max
		$this->displayNum = 200;
		$this->totalNumEvents = 0;
		
		$this->personId = userID();
		
		if ( $this->personId ) {
			
			$app = JFactory::getApplication();
			$input = $app->input;
			
			// Display all or a subset with a more button
			$this->displayAll = $input->getInt('all', 0);
			
			if ( !$this->displayAll ) {
			
				$this->displayNum = $input->getInt('num', 0);
				
				if ( !$this->displayNum ) {
					$this->displayNum = 8;
				}
				
			}
			
			$eventLog = new Biodiv\EventLog();
	
			//Just get today, this week and earlier sets of events
			$this->today = $eventLog->todaysEvents();
			
			$this->yesterday = $eventLog->yesterdaysEvents();
			
			$this->thisWeek = $eventLog->thisWeeksEvents();
			
			$this->earlier = $eventLog->earlierEvents();
			
			$this->totalNumEvents = count($this->today) + count($this->yesterday) + count($this->thisWeek) + count($this->earlier);

		}

		// Display the view
		parent::display($tpl);
		
    }
}



?>