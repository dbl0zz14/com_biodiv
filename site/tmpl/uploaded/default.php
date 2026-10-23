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

$action = $this->root . "&view=uploadm";

print '<h1>'.Text::_("COM_BIODIV_UPLOAD_UP_FROM") . ' ' . $this->siteName.'</h1>';

print "<h3>" . JText::_("COM_BIODIV_UPLOAD_LATEST") . "</h3>";

print "<table class='table'>";
print "<thead>";
print "<tr>";
print "<th>".JText::_("COM_BIODIV_UPLOAD_TIMESTAMP")."</th>";
print "<th>".JText::_("COM_BIODIV_UPLOAD_DEPL")."</th>";
print "<th>".JText::_("COM_BIODIV_UPLOAD_COLL")."</th>";
print "<th>".JText::_("COM_BIODIV_UPLOAD_UPLOAD_ID")."</th>";
print "<th>".JText::_("COM_BIODIV_UPLOAD_COUNT_LARGE")."</th>";
print "<th>".JText::_("COM_BIODIV_UPLOAD_COUNT_REGULAR")."</th>";
print "<th>".JText::_("COM_BIODIV_UPLOAD_COUNT_ERROR")."</th>";
print "</tr>";
print "</thead>";
print "<tbody>";


foreach($this->latestUploads as $line){
  print "<tr>";
  print "<td>" . $line['timestamp'] . "</td>";
  print "<td>" . $line['deployment_date'] . "</td>";
  print "<td>" . $line['collection_date'] . "</td>";
  print "<td>" . $line['upload_id'] . "</td>";
  print "<td>" . $line['count_large'] . "</td>";
  print "<td>" . $line['count_regular'] . "</td>";
  print "<td>" . $line['count_error'] . "</td>";
  print "</tr>\n";
}


print "</tbody>";
print "</table>";

print "<h3>" . Text::_("COM_BIODIV_UPLOAD_REGULAR") . "</h3>";
print "<table class='table'>";
print "<thead>";
print "<tr>";
print "<th>".Text::_("COM_BIODIV_UPLOAD_FILE_NAME")."</th>";
print "<th>".Text::_("COM_BIODIV_UPLOAD_DATE")."</th>";
print "</tr> ";
print "</thead>";
print "<tbody>";

foreach($this->photos as $photoLine){
	print "<tr>";
	print "<td>" . $photoLine['upload_filename'] . "</td>";
	print "<td>" . $photoLine['taken'] . "</td>";
	print "</tr>\n";
}

print "</tbody>";
print "</table>";


// Files to split or convert - eg AVI
if ( count($this->tosplit) > 0 ) {
	print "<h3>" . Text::_("COM_BIODIV_UPLOAD_BEFORE_PROC") . "</h3>";
	print "<table class='table'>";
	print "<thead>";
	print "<tr>";
	print "<th>".Text::_("COM_BIODIV_UPLOAD_FILE_NAME")."</th>";
	print "<th>".Text::_("COM_BIODIV_UPLOAD_DATE")."</th>";
	print "</tr> ";
	print "</thead>";
	print "<tbody>";


	foreach($this->tosplit as $photoLine){
		print "<tr>";
		print "<td>" . $photoLine['upload_filename'] . "</td>";
		print "<td>" . $photoLine['taken'] . "</td>";
		print "</tr>\n";
	}



	print "</tbody>";
	print "</table>";
}

// Large files
if ( count($this->toresize) > 0 ) {
	print "<h3>" . Text::_("COM_BIODIV_UPLOAD_LARGE_FILES") . "</h3>";
	print "<table class='table'>";
	print "<thead>";
	print "<tr>";
	print "<th>".Text::_("COM_BIODIV_UPLOAD_FILE_NAME")."</th>";
	print "<th>".Text::_("COM_BIODIV_UPLOAD_DATE")."</th>";
	print "</tr> ";
	print "</thead>";
	print "<tbody>";


	foreach($this->toresize as $photoLine){
		print "<tr>";
		print "<td>" . $photoLine['upload_filename'] . "</td>";
		print "<td>" . $photoLine['taken'] . "</td>";
		print "</tr>\n";
	}



	print "</tbody>";
	print "</table>";
}


print "  <a class='btn btn-primary btn-lg' role='button' href='"
		.BIODIV_ROOT."&view=upload&site_id=". $this->siteId."'>"
		.$this->trapperModel->biodivLabelIcons('upload', Text::_("COM_BIODIV_UPLOAD_MORE_PH"))
		."</a>";

?>