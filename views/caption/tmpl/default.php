<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;


print "<h1>Adding captions</h1>";

$ff = new BiodivFFMpeg();

foreach ( $this->files as $fileToUse ) {
	
	
	$photoId = $fileToUse['photo_id'];
	
	print "<h2>Adding caption for file " . $fileToUse['upload_filename'] . "</h2>";
	print "<h2>File id = " . $photoId . ", filename = " . $fileToUse['filename'] . "</h2>";
	
	// Check this is an audio file, skip if not.
	if ( !isVideo($photoId) ) {
		print "<p>Caption addingfailed as not a video file - skipping " . $photoId . "</p>";
		continue;
	}
	
	$filestem = JFile::stripExt($fileToUse['filename']);
	
	if ( strpos($filestem, "_cap") !== false ) {
		error_log ("Already added caption for file " . $fileToUse['upload_filename'] );
		$problem = true;
	}
	else {
		$newFile = $filestem . "_cap.mp4";
		
		// Replace the existing file or create again in filesystem if already loaded to s3
		$newFullFile = $fileToUse['dirname'] . "/" . $newFile;
		
		// Want to use PhotoURL here...
		$phUrl = photoURL($photoId);
				
		print "<p>Old file URL = " . $phUrl . "</p>";
		print "<p>New file name = " . $newFullFile . "</p>";
		
		$success = $ff->addCaption( $phUrl, $newFullFile, $fileToUse['text'] );
		
		if ( $success ) {
			// Update row in Photo s3_status = 0
			$db = JDatabase::getInstance(dbOptions());

			$fields = new stdClass();
			$fields->photo_id = $photoId;
			$fields->filename = $newFile;
			$fields->size = filesize($newFullFile);
			$fields->s3_status = 0;
			$fields->status = 1;
			$db->updateObject('Photo', $fields, 'photo_id');
			
			// Update row in Caption processed = 1
			$db = JDatabase::getInstance(dbOptions());

			$fields = new stdClass();
			$fields->photo_id = $photoId;
			$fields->processed = 1;
			$db->updateObject('Caption', $fields, 'photo_id');

		}
		else {
			print "<p>Caption adding failed for file id " . $photoId . "</p>";
			$problem = true;
		}
	}
}

print "<h1>Caption adding complete</h1>";





?>


