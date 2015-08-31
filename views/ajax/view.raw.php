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
                // Assign data to the view
	  // Display the view

	  $article = JTable::getInstance("content");
	  $option_id = JRequest::getInt("option_id");
	  $option = codes_getDetails($option_id, "option");
	  $article_id = $option['article_id'];
	  $article->load($article_id); 
  //	  print_r($article);
	  $this->title = $article->title;
	  $this->introtext = $article->introtext;
	  
	  parent::display($tpl);
        }
}



?>