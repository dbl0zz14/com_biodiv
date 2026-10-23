<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use MammalWeb\Component\BiodivProjects\Site\Library\Core\Biodiv;


//$this->document->getWebAssetManager()->usePreset('com_biodiv.uploaderrors');

$this->document->addScriptDeclaration("BioDiv.waitText = '".Text::_("COM_BIODIV_USERDASHBOARD_WAIT_TEXT")."';");
$this->document->addScriptDeclaration("BioDiv.doneText = '".Text::_("COM_BIODIV_USERDASHBOARD_DONE_TEXT")."';");

$action = $this->root . "&view=uploadm";

print '<h1>'.Text::_("COM_BIODIV_UPLOAD_ERRORS") . ' ' . $this->uploadId.'</h1>';

print "<table class='table'>";
print "<thead>";
print "<tr>";
print "<th>".Text::_("COM_BIODIV_UPLOAD_FILE_NAME")."</th>";
print "<th>".Text::_("COM_BIODIV_UPLOAD_REASON")."</th>";
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
		// .$this->trapperModel->biodivLabelIcons('download', Text::_("COM_BIODIV_UPLOAD_DOWNLOAD_LIST"))
		// ."</a>";
		
print "  <div class='btn btn-primary btn-lg' role='button' id='failsdownload' data-upload-id='".$this->uploadId."'>"
		.$this->trapperModel->biodivLabelIcons('download', Text::_("COM_BIODIV_UPLOAD_DOWNLOAD_LIST"))
		."</div>";

print "  <a class='btn btn-outline-secondary btn-lg' role='button' href='"
		.BIODIV_ROOT."&view=upload&site_id=". $this->siteId."'>"
		.$this->trapperModel->biodivLabelIcons('upload', Text::_("COM_BIODIV_UPLOAD_MORE_PH"))
		."</a>";

$this->document->getWebAssetManager()->usePreset('com_biodiv.uploaderrors');

?>