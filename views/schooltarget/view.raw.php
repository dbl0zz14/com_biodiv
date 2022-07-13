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
class BioDivViewSchoolTarget extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		error_log ( "SchoolTarget display function called" );
		
		// Get all the text snippets for this view in the current language
		$this->translations = getTranslations("schooltarget");
	
		$this->personId = (int)userID();
		
		$this->schoolData = array();

		if ( !$this->personId ) {
			
			error_log("SchoolTarget view: no person id" );
			
		}
		else {
			
			$this->modules = Biodiv\Module::getModules();
			
			$input = JFactory::getApplication()->input;
	
			$this->schoolId = $input->getInt('id', 0);
	
			$this->school = Biodiv\SchoolCommunity::getSchool ( $this->schoolId );
			
			$this->schoolStatus = Biodiv\SchoolCommunity::getSchoolStatus ( $this->schoolId );
			
			$this->schoolPoints = $this->schoolStatus->points;
			
			$this->targetAward = $this->schoolStatus->targetAward;
			
			$this->targetFound = false;
			
			if ( $this->targetAward ) {
				$this->awardName = $this->targetAward->award_name;
				$this->pointsNeeded = $this->targetAward->pointsNeeded;
				$this->targetFound = true;
				$this->isLatest = false;
			}
			else {
				$latest = $this->schoolStatus->existingAward;
				if ( $latest ) {
					$this->awardName = $latest->award_name;
					$this->pointsNeeded = 0;
					$this->isLatest = true;
				}
				
			}
		
					
		}

		// Display the view
		parent::display($tpl);
    }
}



?>