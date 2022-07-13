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
class BioDivViewResourceDelete extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		
		//error_log ( "ResourceDelete display called" );
		
		// Get all the text snippets for this view in the current language
		$this->translations = getTranslations("resourcedelete");
	
		$this->personId = (int)userID();
		
		if ( $this->personId ) {
			
			$input = JFactory::getApplication()->input;
			$this->resourceId = $input->getInt('id', 0);
			
			$this->deleted = Biodiv\ResourceFile::deleteResource($this->resourceId);
			
		}
		

		// Display the view
		parent::display($tpl);
		
    }
}



?>