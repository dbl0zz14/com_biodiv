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
* HTML View class for viewing all uploaded files
*
* @since 0.0.1
*/
class BioDivViewUploadErrors extends JViewLegacy
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

	$this->root = JURI::root() . "?option=com_biodiv";
	$this->uploadId = $app->input->getInt('upload_id',0);
	$this->siteId = $app->getUserStateFromRequest('com_biodiv.site_id', 'site_id',0);
	$this->siteName = codes_getName($this->siteId, "site");

	if ( !$this->siteId ) {

		$details = codes_getDetails($this->uploadId, 'upload');
		if ( $details ) {

			$this->siteId = $details['site_id'];
		}
	}

	if(!canEdit($this->siteId, "site")){
		die("No permission for site " . $this->siteId);
	}

	$this->photos = getUploadErrors ( $this->uploadId );

	if ( !$this->photos ) {
		$this->photos = array();
	}

	// Display the view
	parent::display($tpl);

    }
}



?>
