<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;


if ( $this->test ) {
	
	print "<h1>Adding test list with type " . $this->testTag . " to AI queue</h1>";
	
	print "<h2>" . count($this->photoIds) . " photo_ids to add</h2>";
	
	print_r ( $this->photoIds );
	
	foreach ( $this->photoIds as $photoId ) {
		
		addToAIQueue($this->aiType, $photoId, $this->priority);
	}
	
}
else if ( $this->uploadId ) {
	
	print "<h1>Adding upload " . $this->uploadId . " to AI queue</h1>";
	
	addUploadToAIQueue($this->aiType, $this->uploadId, $this->priority);
	
}
else {
	"<h2>Cannot find an upload_id and not test</h2>";
}

	

print "<h1>Add to queue complete</h1>";

?>


