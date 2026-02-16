<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;


$this->document->addScriptDeclaration("BioDiv.waitText = '".JText::_("COM_BIODIV_USERDASHBOARD_WAIT_TEXT")."';");
$this->document->addScriptDeclaration("BioDiv.doneText = '".JText::_("COM_BIODIV_USERDASHBOARD_DONE_TEXT")."';");

$action = $this->root . "&view=uploadm";

print '<h1>'.JText::_("COM_BIODIV_UPLOAD_ERRORS") . ' ' . $this->uploadId.'</h1>';

print "<table class='table'>";
print "<thead>";
print "<tr>";
print "<th>".JText::_("COM_BIODIV_UPLOAD_FILE_NAME")."</th>";
print "<th>".JText::_("COM_BIODIV_UPLOAD_REASON")."</th>";
print "</tr> ";
print "</thead>";
print "<tbody>";

foreach($this->photos as $photoLine){
	print "<tr>";
	print "<td>" . $photoLine['upload_filename'] . "</td>";
	print "<td>" . $photoLine['reason'] . "</td>";
	print "</tr>\n";
}

print "</tbody>";
print "</table>";



// print "  <a class='btn btn-primary btn-lg' role='button' href='"
		// .BIODIV_ROOT."&view=upload&site_id=". $this->siteId."'>"
		// .$this->trapperModel->biodivLabelIcons('download', JText::_("COM_BIODIV_UPLOAD_DOWNLOAD_LIST"))
		// ."</a>";
		
print "  <div class='btn btn-success btn-lg' role='button' id='failsdownload' data-upload-id='".$this->uploadId."'>"
		.biodiv_label_icons('download', JText::_("COM_BIODIV_UPLOAD_DOWNLOAD_LIST"))
		."</div>";

print "  <a class='btn btn-primary btn-lg' role='button' href='"
		.BIODIV_ROOT."&view=upload&site_id=". $this->siteId."'>"
		.biodiv_label_icons('upload', JText::_("COM_BIODIV_UPLOAD_MORE_PH"))
		."</a>";

JHtml::_('script', 'com_biodiv/uploaderrors.js', array('version' => 'auto', 'relative' => true), array());

?>
