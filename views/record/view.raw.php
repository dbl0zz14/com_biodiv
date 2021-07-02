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
class BioDivViewRecord extends JViewLegacy
{
        /**
         *
         * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
         *
         * @return  void
         */

    public function display($tpl = null) 
    {
		($person_id = (int)userID()) or die("Please log in");
		
		$this->translations = getTranslations("record");

		
		// Check whether site id is in form
		$input = JFactory::getApplication()->input;
		
		$this->loaded = false;
		
		$this->siteId = $clientName = $input->getInt('site_id', 0);
		
		//error_log("BioDivViewRecord site id = " . $this->siteId);
		
		if ( $this->siteId == 0 ) {
			
			$newSiteId = addSite();
		
			if ( $newSiteId ) {		
			
				//error_log("New site added = ". $newSiteId);
				
				$this->siteId = $newSiteId;
				
				//$this->input->set('site_id', $newSiteId);
				
			}
			else {
				$this->loaded = false;
			}
		}
		
		
		if ( $this->siteId ) {
			
			//error_log ( "BioDivViewRecord got site id: " . $this->siteId );
			
			$this->siteName = codes_getName ( $this->siteId, 'site' );
    
			// Get setting that shows whether this is camera deployment site (eg MammalWeb) or audio (eg NaturesAudio)
			$isCamera = getSetting("camera") == "yes";	  
 
			// Add to the Upload table
			$uploadId = addUpload ( $isCamera, $this->siteId );
			
			if ( $uploadId ) {
				
				$file = $input->files->get('data');
				//$clientName = $input->get('fname', 0, "string"); 
				$clientName = $input->getString('fname'); 

				$errStr = print_r ( $file, true );
				//error_log ( "BioDivViewRecord file details: " . $errStr );

				$tmpName = $file['tmp_name'];
				
				$fileSize = $file['size'];
				$fileType = $file['type'];
				
				$success = uploadFile ( $uploadId, $this->siteId, $tmpName, $clientName, $fileSize, $fileType, true );
				
				$this->loaded = $success;
			}
			
			/*
			$file = $input->files->get('data');
			$clientName = $input->get('fname', 0, "string"); 

			$errStr = print_r ( $file, true );
			//error_log ( "BioDivViewRecord file details: " . $errStr );

			$tmpName = $file['tmp_name'];


			$ext = strtolower(JFile::getExt($clientName));
			$newName = md5_file($tmpName) . "." . $ext;

			//error_log ( "Uploading file " . $tmpName . ", extension is " . $ext );

			$dirName = 'biodivimages';
			$newFullName = "$dirName/$newName";


			$this->loaded =	JFile::upload($tmpName, $newFullName);
			
			*/
		}


  
		// Display the view
		parent::display($tpl);
    }
}



?>