<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;
$action = $this->root . "&view=uploadm";

?>

<h1><?php print JText::_("COM_BIODIV_UPLOADED_UP_FROM") . ' ' . $this->site_name;?></h1>

<table class='table'>
<thead>
<tr>
<th><?php print JText::_("COM_BIODIV_UPLOADED_FILE_NAME");?></th>
<th><?php print JText::_("COM_BIODIV_UPLOADED_DATE");?></th>
</tr> 
</thead>
<tbody>
<?php
  foreach($this->photos as $photoLine){
  print "<tr>";
  print "<td>" . $photoLine['upload_filename'] . "</td>";
  print "<td>" . $photoLine['taken'] . "</td>";
  print "</tr>\n";
}


?>

</tbody>
</table>

<?php
if ( count($this->tosplit) > 0 ) {
print "<h3>" . JText::_("COM_BIODIV_UPLOADED_BEFORE_PROC") . "</h3>";
print "<table class='table'>";
print "<thead>";
print "<tr>";
print "<th>".JText::_("COM_BIODIV_UPLOADED_FILE_NAME")."</th>";
print "<th>".JText::_("COM_BIODIV_UPLOADED_DATE")."</th>";
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


print "  <a class='btn btn-success btn-lg' role='button' href='".BIODIV_ROOT."&view=upload&site_id=". $this->site_id."'>".biodiv_label_icons('upload', JText::_("COM_BIODIV_UPLOADED_MORE_PH"))."</a>";

?>