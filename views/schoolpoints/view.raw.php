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
class BioDivViewSchoolPoints extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		$this->personId = (int)userID();
		
		$this->data = array();

		if ( !$this->personId ) {
			
			error_log("SchoolPoints view: no person id" );
			
		}
		else {
			
			$this->community = new Biodiv\SchoolCommunity();
			$this->schools = $this->community->getSchools();
			$this->badgeGroups = codes_getList ( "badgegroup" );
			
			foreach ( $this->badgeGroups as $badgeGroup ) {
				
				$badgeGroupId = $badgeGroup[0];
				$badgeGroupName = $badgeGroup[1];
				
				$this->data[$badgeGroupId] = array();
				
				foreach ( $this->schools as $school ) {
					
					$schoolId = $school->schoolId;
					$schoolSummary = Biodiv\BadgeGroup::getSchoolSummary ( $schoolId, $badgeGroupId );
					$this->data[$badgeGroupId][$schoolId] = $schoolSummary;
					
				}
				
				
			}
					
		}

		// Display the view
		parent::display($tpl);
    }
}



?>