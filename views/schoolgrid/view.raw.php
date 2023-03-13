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
class BioDivViewSchoolGrid extends JViewLegacy
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
		
		if ( $this->schoolUser ) {
	
			$input = JFactory::getApplication()->input;
			
			$this->school = $input->getInt('school', 0);
			
			$this->teacher = $input->getInt('teacher', 0);
			
			$this->classGrid = $input->getInt('class', 0);
			
			$this->student = $input->getInt('student', 0);
			
		}

		// Display the view
		parent::display($tpl);
		
    }
}



?>