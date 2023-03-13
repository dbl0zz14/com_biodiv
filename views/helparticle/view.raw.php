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
class BioDivViewHelpArticle extends JViewLegacy
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
			
			$this->type = $input->getString('type', 0);
			
			$articleId = codes_getCode ( $this->type, "beshelp" );
			$article = getArticle ( $articleId );
			
			if ( $article ) {
				$this->title = $article->title;
				$this->introtext = $article->introtext;
			}
			
			$this->showLink = "";
			
			if ( $this->type == "badges" ) {
				$this->showLink = "bes-badges?help=1";
				$this->title = JText::_("COM_BIODIV_HELPARTICLE_BADGES_TITLE");
				$this->line = JText::_("COM_BIODIV_HELPARTICLE_BADGES_LINE");
			}
			else if ( $this->type == "schoolcommunity" ) {
				$this->showLink = "bes-community?help=1";
				$this->title = JText::_("COM_BIODIV_HELPARTICLE_COMMUNITY_TITLE");
				$this->line = JText::_("COM_BIODIV_HELPARTICLE_COMMUNITY_LINE");
			}
			else if ( $this->type == "teacherzone" ) {
				$this->showLink = "bes-educator-zone?help=1";
				$this->title = JText::_("COM_BIODIV_HELPARTICLE_TEACHER_ZONE_TITLE");
				$this->line = JText::_("COM_BIODIV_HELPARTICLE_TEACHER_ZONE_LINE");
			}
			else if ( $this->type == "badgescheme" ) {
				$this->showLink = "bes-badge-scheme?help=1";
				$this->title = JText::_("COM_BIODIV_HELPARTICLE_BADGE_SCHEME_TITLE");
				$this->line = JText::_("COM_BIODIV_HELPARTICLE_BADGE_SCHEME_LINE");
			}
			else if ( $this->type == "schooladmin" ) {
				$this->showLink = "bes-school-admin?help=1";
				$this->title = JText::_("COM_BIODIV_HELPARTICLE_SCHOOL_ADMIN_TITLE");
				$this->line = JText::_("COM_BIODIV_HELPARTICLE_SCHOOL_ADMIN_LINE");
			}
			else if ( $this->type == "studentprogress" ) {
				$this->showLink = "bes-student-progress?help=1";
				$this->title = JText::_("COM_BIODIV_HELPARTICLE_STUDENT_PROGRESS_TITLE");
				$this->line = JText::_("COM_BIODIV_HELPARTICLE_STUDENT_PROGRESS_LINE");
			}
			else if ( $this->type == "schoolwork" ) {
				$this->showLink = "bes-school-work?help=1";
				$this->title = JText::_("COM_BIODIV_HELPARTICLE_SCHOOL_WORK_TITLE");
				$this->line = JText::_("COM_BIODIV_HELPARTICLE_SCHOOL_WORK_LINE");
			}
			else if ( $this->type == "resourcehub" ) {
				$this->showLink = "bes-search-resources?help=1";
				$this->title = JText::_("COM_BIODIV_HELPARTICLE_RESOURCE_HUB_TITLE");
				$this->line = JText::_("COM_BIODIV_HELPARTICLE_RESOURCE_HUB_LINE");
			}
			else if ( $this->type == "resourceset" ) {
				$this->showLink = "bes-resource-set?help=1";
				$this->title = JText::_("COM_BIODIV_HELPARTICLE_RESOURCE_SET_TITLE");
				$this->line = JText::_("COM_BIODIV_HELPARTICLE_RESOURCE_SET_LINE");
			}
			else if ( $this->type == "resource" ) {
				$this->showLink = "bes-resource?help=1";
				$this->title = JText::_("COM_BIODIV_HELPARTICLE_RESOURCE_TITLE");
				$this->line = JText::_("COM_BIODIV_HELPARTICLE_RESOURCE_LINE");
			}
		  
		}
	  
		parent::display($tpl);
		
    }
}



?>