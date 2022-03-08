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
class BioDivViewResourceFile extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		error_log ( "ResourceFile display function called" );
		
		// Get all the text snippets for this view in the current language
		$this->translations = getTranslations("resourcefile");
		
		$this->personId = userID();
		
		$this->resourceId = null;
		
		if ( $this->personId ) {
	
			// Check whether set id 
			$input = JFactory::getApplication()->input;
			
			$this->resourceId = $input->getInt('resource_id', 0);
			
			error_log ( "ResourceFile resouce id = " . $this->resourceId );
			
			if ( $this->resourceId ) {
				
				$this->resourceFile = Biodiv\ResourceFile::createResourceFileFromId($this->resourceId);
				
				error_log ( "Constructed ResourceFile object" );
				
				$this->resourceFiletype = $this->resourceFile->getFiletype ();
				
				$fileTypeBits = explode('/', $this->resourceFiletype );
				
				$this->mainType = $fileTypeBits[0];
				
				error_log ( "File type main type = " . $this->mainType );
				
				$this->resourceUrl = $this->resourceFile->getUrl ();
				
			}

		}

		// Display the view
		parent::display($tpl);
		
    }
}



?>