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
* Browse tasks by badge group
*
* @since 0.0.1
*/
class BioDivViewSchoolWork extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		// Get all the text snippets for this view in the current language
		$this->translations = getTranslations("schoolwork");
	
		$this->personId = (int)userID();
		
		$this->badgeGroupId = 0;
		$this->data = "";

		if ( !$this->personId ) {
			
			error_log("SchoolWork view: no person id" );
			
		}
		else {
			
			$this->helpOption = codes_getCode ( "schoolwork", "beshelp" );
			
			$this->schoolUser = Biodiv\SchoolCommunity::getSchoolUser();
	
			$app = JFactory::getApplication();
			
		}

		// Display the view
		parent::display($tpl);
    }
}



?>