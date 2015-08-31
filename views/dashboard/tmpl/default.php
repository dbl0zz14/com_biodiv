<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

$document = JFactory::getDocument();
$document->addScriptDeclaration("BioDiv.next_photo = ".$this->photoDetails['next_photo'].";");


print "<h2>Spotters</h2>\n";

print "<table><thead><tr>\n";
foreach($this->spotterFields as $field=>$title){
  print "<th>$title</th>\n";
}
print "</tr></thead>\n";

print "<tbody>\n";
foreach($this->spotters as $spotter){
  print "<tr>";
  foreach($this->spotterFields as $field=>$title){
    print "<td>".$spotter[$field]."</td>\n";
  }
  print "</tr>\n";
}
print "</tbody></table>\n";


print "<h2>Trappers</h2>\n";

print "<table><thead><tr>\n";
foreach($this->trapperFields as $field=>$title){
  print "<th>$title</th>\n";
}
print "</tr></thead>\n";

print "<tbody>\n";
foreach($this->trappers as $trapper){
  print "<tr>";
  foreach($this->trapperFields as $field=>$title){
    print "<td>".$trapper[$field]."</td>\n";
  }
  print "</tr>\n";
}
print "</tbody></table>\n";
?>