<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;
//print "<div>\n";
$nothingDisabled = false;
if ( !$this->person_id ) {
	print "<div id='no_user_id'></div>";
}
else {
  print "<div><h5>" . $this->text . "</h5></div>";
 }
 
?>