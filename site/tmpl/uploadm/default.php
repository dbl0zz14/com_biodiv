<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/


use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

 
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');


if ( $this->uploadId == 0 ) {
	
	print '<h3>No upload id</h3>';
}
else {
	
	$this->document->getWebAssetManager()->usePreset('com_biodiv.uploadm');

	$action = $this->root . "&task=upload";
	
	print '<h1>'.Text::_("COM_BIODIV_UPLOAD_UPLOAD_IM") . ' ' . $this->siteName.'</h1>';
	print '<div id="fileuploader">'.Text::_("COM_BIODIV_UPLOAD_UPLOAD").'</div>';
	print '<div id="fileuploadspinner"  style="display:none"><i class="fa fa-spinner fa-spin fa-4x"></i></div>';

	$document = Factory::getDocument();
	$document->addScriptDeclaration("BioDiv.upload_id = ".$this->uploadId.";");
	

	if ( $this->badge && $this->classId ) {
		$document->addScriptDeclaration("BioDiv.badgeId = ".$this->badge.";");
		$document->addScriptDeclaration("BioDiv.classId = ".$this->classId.";");
	}
}

?>


