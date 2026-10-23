<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;


print '<h1>Sequencing</h1>';


if ( !$this->canRun ) {

	print '<h2>Already running, exitting</h2>';
}
else {

	print_r($this->uploadDetails);
	$lastOne = "None uploaded";
	$firstOne = "None uploaded";
	if ( sizeof($this->uploadDetails) > 0 ) {
		$lastOne = $this->uploadDetails[sizeof($this->uploadDetails)-1]['upload_id'];
		$firstOne = $this->uploadDetails[0]['upload_id'];
	}

	print "last one $lastOne";
	print "<br />first one $firstOne";
	
	$this->trapperModel->sequencePhotos($firstOne, $this->sequenceThreshold);

	$this->trapperModel->setRunning ( 'sequence', 0 );
}
?>


