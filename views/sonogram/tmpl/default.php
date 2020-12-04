<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;


print "<h1>Generating sonograms</h1>";

$ff = new BiodivFFMpeg();

if ( $this->sonograms ) {
	
	foreach ( $this->files as $fileToUse ) {
	
		$photoId = $fileToUse['photo_id'];
		
		print "<h2>Generating sonogram for file " . $fileToUse['upload_filename'] . "</h2>";
		print "<h2>File id = " . $photoId . ", filename = " . $fileToUse['filename'] . "</h2>";
		
		// Check this is an audio file, skip if not.
		if ( !isAudio($photoId) ) {
			print "<p>Sonogram generation failed as not an audio file - skipping " . $photoId . "</p>";
			continue;
		}
		
		$filestem = JFile::stripExt($fileToUse['filename']);
		$newFile = $filestem . "_sono.mp4";
		$newFullFile = $fileToUse['dirname'] . "/" . $newFile;
		
		// Want to use PhotoURL here...
		$phUrl = photoURL($photoId);
				
		print "<p>Old file URL = " . $phUrl . "</p>";
		print "<p>New file name = " . $newFullFile . "</p>";
		
		$success = $ff->generateSonogram( $phUrl, $newFullFile );
		
		if ( $success ) {
			// Update row in Photo with sonogram filename and s3_status = 0
			$db = JDatabase::getInstance(dbOptions());
	
			$fields = new stdClass();
			$fields->photo_id = $photoId;
			$fields->filename = $newFile;
			$fields->size = filesize($newFullFile);
			$fields->s3_status = 0;
			$fields->status = 1;
			$db->updateObject('Photo', $fields, 'photo_id');
			
			// DON'T REMOVE ANY SPLIT FILES HERE AS WE DIDN'T KEEP THE ORIGINALS
		}
		else {
			print "<p>Sonogram generation failed for file id " . $photoId . "</p>";
			$problem = true;
		}
	}
	
	print "<h1>Sonogram generation complete</h1>";

}
else {
	print "<h1>Songrams setting is not on, exitting</h1>";
}



?>


