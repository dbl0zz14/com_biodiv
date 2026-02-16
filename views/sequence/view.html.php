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
* HTML View class for the HelloWorld Component
*
* @since 0.0.1
*/
class BioDivViewSequence extends JViewLegacy
{
        /**
         *
         * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
         *
         * @return  void
         */

        public function display($tpl = null) 
        {
	  $app = JFactory::getApplication();
	  //$input = $app->getInput();
          //$this->sequenceThreshold = $input->getInt("threshold", 10);

	  $this->canRun = setRunning ( 'sequence', 1 );


	  if ( $this->canRun ) {
	  	$db = JDatabase::getInstance(dbOptions());
	  	$query = $db->getQuery(true);
	  	$query->select("distinct(upload_id)")
	    	->from($db->quoteName("Photo"))
	    	->where("sequence_id = 0");
	  	$db->setQuery($query);
	  	$this->uploadDetails = $db->loadAssocList();

	  }

	  // Display the view


	  parent::display($tpl);
        }
}



?>
