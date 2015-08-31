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
<h1>Upload images from <?php print $this->site_name;?></h1>
<div id='fileuploader'>Upload</div>
<div id='fileuploadspinner'  style="display:none"><i class='fa fa-spinner fa-spin fa-4x'></i></div>
<?php
  $document = JFactory::getDocument();
$document->addScriptDeclaration("BioDiv.upload_id = ".$this->upload_id.";");
JHTML::script("jquery-upload-file/jquery.uploadfile.min.js", false, true);
JHTML::script("com_biodiv/uploadm.js", false, true);
?>


