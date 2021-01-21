<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;


print "<h1>Converting files</h1>";

$ff = new BiodivFFMpeg();

$err_msg = print_r ( $this->files, true );
error_log ( "Files to convert: " . $err_msg );

foreach ( $this->files as $bigFile ) {
	
	$ofId = $bigFile['of_id'];
	
	print "<h2>Converting file " . $bigFile['upload_filename'] . "</h2>";
	print "<h2>File id = " . $ofId . ", filename = " . $bigFile['filename'] . "</h2>";
	
	// get the duration.
	$fullfilename = $bigFile['dirname'] . "/" . $bigFile['filename'];
	
	print "<h3>Full filename: " . $fullfilename . "</h3>";
	error_log ( "Full filename:  " . $fullfilename );
	$problem = false;
	
	$filestem = JFile::stripExt($fullfilename);
			
	$newFile = $filestem . ".mp4";
	print "<p>New file name = " . $newFile . "</p>";
	
	$taken = $bigFile['taken'];
	
	$success = $ff->convertAviToMp4( $fullfilename, $newFile, $taken );
	
	if ( $success ) {
		$photoId = writeSplitFile($ofId, $newFile, 0);
		print "<p>Written to Photo table - photo_id = " . $photoId . "</p>";
		if ( !$photoId ) {
			print "<p>Write to Photo table failed< for file " . $fullfilename . "/p>";
			$problem = true;
		}
	}
	else {
		print "<p>File conversion failed for file " . $fullfilename . "</p>";
		$problem = true;
	}
	
	// Remove original file
	if ( !$problem ) {
		print "<p>Removing file " . $fullfilename . "</p>";
		
		try {
			JFile::delete ( $fullfilename );
		}
		catch (Exception $e) {
			print ("<br>Couldn't delete file: " . $fullfilename);
		}
	}


	if ( $problem ) {
		print "<p>Problem converting file " . $fullfilename . "</p>";
		// set OriginalFiles file to error status
		setOriginalFileStatus($ofId, false);
	}
	else {
		print "<p>File converted successfully " . $fullfilename . "</p>";
		// set OriginalFiles file to success
		setOriginalFileStatus($ofId, true);
	}
}
	


print "<h1>File conversion complete</h1>";

?>


