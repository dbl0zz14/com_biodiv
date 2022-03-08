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
* Return school summary data for display in charts 
*
* @since 0.0.1
*/
class BioDivViewStudentTarget extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		error_log ( "StudentTarget display function called" );
		
		// Get all the text snippets for this view in the current language
		$this->translations = getTranslations("studenttarget");
	
		$this->personId = (int)userID();
		
		$this->schoolData = array();

		if ( !$this->personId ) {
			
			error_log("StudentTarget view: no person id" );
			
		}
		else {
			
			$input = JFactory::getApplication()->input;
	
			$this->target = Biodiv\SchoolCommunity::getStudentTarget ();
					
		}

		// Display the view
		parent::display($tpl);
    }
}



?>