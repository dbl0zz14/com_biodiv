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
class BioDivViewTask extends JViewLegacy
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
		
		if ( $this->personId ) {
			
			//Check whether set id 
			$input = JFactory::getApplication()->input;
			
			$this->heading = "";
			
			$this->taskId = $input->getInt('id', 0);
			
			$this->task = new Biodiv\Task($this->taskId);
			
			$this->articleId = $this->task->getArticleId();
			
			$article = getArticleById ( $this->articleId );
			$this->title = $article->title;
			$this->introtext = $article->introtext;
			
		  
		}
	  
		parent::display($tpl);
		
    }
}



?>