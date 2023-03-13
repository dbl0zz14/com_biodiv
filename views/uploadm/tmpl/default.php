<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;
$action = $this->root . "&task=upload";
JHTML::stylesheet("jquery-upload-file/uploadfile.css", array(), true);
?>
<h1><?php print JText::_("COM_BIODIV_UPLOAD_UPLOAD_IM") . ' ' . $this->site_name;?></h1>
<div id='fileuploader'><?php print JText::_("COM_BIODIV_UPLOAD_UPLOAD");?></div>
<div id='fileuploadspinner'  style="display:none"><i class='fa fa-spinner fa-spin fa-4x'></i></div>
<?php
  $document = JFactory::getDocument();
$document->addScriptDeclaration("BioDiv.upload_id = ".$this->upload_id.";");

if ( $this->badge && $this->classId ) {
	$document->addScriptDeclaration("BioDiv.badgeId = ".$this->badge.";");
	$document->addScriptDeclaration("BioDiv.classId = ".$this->classId.";");
}
JHTML::script("jquery-upload-file/jquery.uploadfile.min.js", false, true);
JHTML::script("com_biodiv/uploadm.js", false, true);
?>


