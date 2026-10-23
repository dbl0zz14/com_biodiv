<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

// $myObj = new stdClass();
// $myObj->filename = $this->reportName;
// //$myObj->headings = array_values($this->headings);
// $myObj->data = $this->data;

$myJSON = json_encode($this->fails);

print $myJSON;

?>