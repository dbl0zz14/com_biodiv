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
class BioDivViewArticle extends JViewLegacy
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
			
			$optionId = $input->getInt('id', 0);
			
			$this->article = getArticle ( $optionId );
			
			//$joomlaArticle = getArticleById ( $this->article->id );
			$this->title = $this->article->title;
			$this->introtext = $this->article->introtext;
			
		  
		}
	  
		parent::display($tpl);
		
    }
}



?>