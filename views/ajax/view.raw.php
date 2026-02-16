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
*
* @since 0.0.1
*/
class BioDivViewAjax extends JViewLegacy
{
	/**
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */

	public function display($tpl = null) 
	{
	  $this->person_id = (int)userID();
	  
	  $option_id = JRequest::getInt("option_id");
	  
	  $article = getArticle ( $option_id );
	  
	  $this->title = $article->title;
	  
	  $this->introtext = $article->introtext;
	  
	  /*
	  $article = JTable::getInstance("content");
	  $option_id = JRequest::getInt("option_id");
	  $option = codes_getDetails($option_id, "optiontran");
	  
	  $associations = JLanguageAssociations::getAssociations('com_content', '#__content', 'com_content.item', $option['article_id']);

	  $langObject = JFactory::getLanguage();
	  //print ("Tag = " . $langObject->getTag() );
	  $article_id = $associations[$langObject->getTag()]->id;
	  
	  $article->load($article_id); 
  //	  print_r($article);
	  
	  // Default the title and introtext
	  $this->title = $option['option_name'];
	  $this->introtext = 0;
	  
      if ( $article_id ) {
		$this->title = $article->title;
		$this->introtext = $article->introtext;
	  }
	  
	  // Catch all in case the article id is not available
	  if ( !$this->title ) {
		$this->title = $option['option_name'];
	  }
	  */
	  
	  parent::display($tpl);
    }
}



?>