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
class BioDivViewBadgeArticle extends JViewLegacy
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
			
			$this->badgeId = $input->getInt('id', 0);
			$this->classId = $input->getInt('class_id', 0);
			$this->readonly = $input->getInt('readonly', 0);
			$this->complete = $input->getInt('complete', 0);
			
			$this->badge = Biodiv\Badge::createFromId ( $this->schoolUser, $this->classId, $this->badgeId );
			$article = getArticleById ( $this->badge->getArticleId() );
			
			if ( $article ) {
				$this->title = $article->title;
				$this->introtext = $article->introtext;
			}
			
		  
		}
	  
		parent::display($tpl);
		
    }
}



?>