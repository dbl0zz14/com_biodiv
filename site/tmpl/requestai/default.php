<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;


//$startTime = microtime(true);
//error_log ( "Calls to AI " . $this->aiType . " start time = " . $startTime );
if ( !$this->canRun ) {

	print '<h2>Already running, exitting</h2>';
}
else {
		
	for ( $i=0; $i < $this->repeat; $i++ ) {
		
		print "<h1>Processing AI queue to request classifications, request " . $i . "</h1>";
		
		$photos = null;
		$siteId = null;
		
		if ( array_key_exists($i, $this->photos) ) {
			$photos = $this->photos[$i];
		}
		if ( array_key_exists($i, $this->siteIds) ) {
			$siteId = $this->siteIds[$i];
		}
		
		if ( $photos == null ) {
			print "<h2>No photo list - could be an empty queue or no permission for this</h2>";
		}
		else if ( count($photos) == 0 ) {
			print "<h2>No s3 photos found in queue for AI</h2>";
		}
		else if ( !$siteId ) {
			print "<h2>No site id found in queue for AI</h2>";
		}
		else {
			
			print '<h2>Requesting '.$this->aiType.' classifications for ' . count($photos) . ' files from Photo table </h2>';

			$this->aiModel->classify ( $this->aiType, $siteId, $photos );
					
		}
		print '<h1>Request to '.$this->aiType.' complete, request ' . $i . '</h1>';

		$this->aiModel->setRunning ( 'requestai', 0 );
	}
}

//$timeElapsedSecs = (microtime(true) - $startTime)*1000;
//error_log ( "AI call time elapsed = " . $timeElapsedSecs . " seconds" );



?>

