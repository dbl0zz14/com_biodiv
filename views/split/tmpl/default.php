<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;


print "<h1>Splitting files</h1>";

$ff = new BiodivFFMpeg();

foreach ( $this->files as $bigFile ) {
	
	$ofId = $bigFile['of_id'];
	
	print "<h2>Splitting file " . $bigFile['upload_filename'] . "</h2>";
	print "<h2>File id = " . $ofId . ", filename = " . $bigFile['filename'] . "</h2>";
	
	// get the duration.
	$fullfilename = $bigFile['dirname'] . "/" . $bigFile['filename'];
	
	print "<h3>Full filename: " . $fullfilename . "</h3>";
	$duration = $ff->getDuration($fullfilename);
	print "<h3>Duration: " . $duration . "</h3>";
	
	$oneFile = $this->fileLength;
	$twoFiles = $oneFile * 2;
	$problem = false;
	
	// Horrendous repetition in here - should take out as a function...
	
	// If file less than 30 secs, leave complete and copy details to upload table.  Add a 1 second tolerance
	if ( $duration < $oneFile + 1 ) {
		print "<h4>Just one file</h4>";
		
		if ( $this->sonograms ) {
			$filestem = JFile::stripExt($fullfilename);
			
			$fname = $filestem . "_sono";
			$newFile = $fname . ".mp4";
			print "<p>New file name = " . $newFile . "</p>";
			
			$success = $ff->generateSonogram( $fullfilename, $newFile );
			
			if ( $success ) {
				$photoId = writeSplitFile($ofId, $newFile, 0);
				print "<p>Written to Photo table - photo_id = " . $photoId . "</p>";
				if ( !$photoId ) {
					print "<p>Write to Photo table failed< for file " . $fullfilename . "/p>";
					$problem = true;
				}
			}
			else {
				print "<p>Sonogram generation failed< for file " . $fullfilename . "/p>";
				$problem = true;
			}
		}
		else {
			$photoId = writeSplitFile($ofId, $fullfilename, 0);
			print "<p>Written to Photo table - photo_id = " . $photoId . "</p>";
			if ( !$photoId ) {
				print "<p>Write to Photo table failed< for file " . $fullfilename . "/p>";
				$problem = true;
			}
		}
	}
	
	// If file > 30 secs, create 30 sec clips until have less than 60 secs left, then create the last two files with duration half of that
	else {
		print "<h4>2 or more files</h4>";
		$lengthLeft = $duration;
		$tag = 1;
		$start = 0;
		$success = true;
		$ext = JFile::getExt($fullfilename); 
		$filestem = JFile::stripExt($fullfilename);
		
		while ( $lengthLeft >= $twoFiles ) {
			$fname = $filestem . "_" . $tag;
			$newFile = $fname . "." . $ext;
			print "<p>New file name = " . $newFile . "</p>";
			$end = $start + $oneFile;
			
			$success = $ff->splitFile ( $fullfilename, $newFile, $start, $oneFile );
			
			if ( $success ) {
				
				if ( $this->sonograms ) {
					$newstem = JFile::stripExt($newFile);
					
					$fname = $newstem . "_sono";
					$newSonoFile = $fname . ".mp4";
					print "<p>New file name = " . $newSonoFile . "</p>";
					
					$success = $ff->generateSonogram( $newFile, $newSonoFile );
					
					if ( $success ) {
						// Remove the intermediate file
						print "<p>Removing intermediate file " . $newFile . "</p>";
						try {
							unlink($newFile);
						}
						catch (Exception $e) {
							print ("<br>Couldn't delete file: " . $newFile);
							throw $e;
						}
						
						$photoId = writeSplitFile($ofId, $newSonoFile, $start);
						print "<p>Written to Photo table - photo_id = " . $photoId . "</p>";
						if ( !$photoId ) {
							print "<p>Write to Photo table failed< for file " . $newSonoFile . "/p>";
							$problem = true;
						}
					}
					else {
						print "<p>Sonogram generation failed< for file " . $newFile . "/p>";
						$problem = true;
					}
				}
				else {
					print "<p>Writing to Photo table</p>";
					$photoId = writeSplitFile($ofId, $newFile, $start);
					print "<p>Written to Photo table - photo_id = " . $photoId . "</p>";
					if ( !$photoId ) {
						print "<p>Write to Photo table failed< for file " . $newFile . "/p>";
						$problem = true;
					}
				}
			}
			else {
				$problem = true;
			}
			$lengthLeft -= $oneFile;
			$start = $end;
			$tag += 1;
		}
		
		// If the last length is only just over a minute, no need to split into two
		if ( $lengthLeft < $oneFile + 1 ) {
			$fname = $filestem . "_" . $tag;
			$newFile = $fname . "." . $ext;
			print "<p>New file name = " . $newFile . "</p>";
			$success = $ff->splitFile ( $fullfilename, $newFile, $start );
				
			if ( $success ) {
				if ( $this->sonograms ) {
					$newstem = JFile::stripExt($newFile);
					
					$fname = $newstem . "_sono";
					$newSonoFile = $fname . ".mp4";
					print "<p>New file name = " . $newSonoFile . "</p>";
					
					$success = $ff->generateSonogram( $newFile, $newSonoFile );
					
					if ( $success ) {
						// Remove the intermediate file
						print "<p>Removing intermediate file " . $newFile . "</p>";
						try {
							unlink($newFile);
						}
						catch (Exception $e) {
							print ("<br>Couldn't delete file: " . $newFile);
							throw $e;
						}
						
						$photoId = writeSplitFile($ofId, $newSonoFile, $start);
						print "<p>Written to Photo table - photo_id = " . $photoId . "</p>";
						if ( !$photoId ) {
							print "<p>Write to Photo table failed< for file " . $newSonoFile . "/p>";
							$problem = true;
						}
					}
					else {
						print "<p>Sonogram generation failed< for file " . $newFile . "/p>";
						$problem = true;
					}
				}
				else {
					print "<p>Writing to Photo table</p>";
					$photoId = writeSplitFile($ofId, $newFile, $start);
					print "<p>Written to Photo table - photo_id = " . $photoId . "</p>";
					if ( !$photoId ) {
						print "<p>Write to Photo table failed< for file " . $newFile . "/p>";
						$problem = true;
					}
				}
			}
			else {
				$problem = true;
			}
		}
		else {
			$firstLength = intVal($lengthLeft/2);
			$end = $start + $firstLength;
			$fname = $filestem . "_" . $tag;
			$newFile = $fname . "." . $ext;
			print "<p>New file name = " . $newFile . "</p>";
			$success = $ff->splitFile ( $fullfilename, $newFile, $start, $firstLength );
				
			if ( $success ) {
				if ( $this->sonograms ) {
					$newstem = JFile::stripExt($newFile);
					
					$fname = $newstem . "_sono";
					$newSonoFile = $fname . ".mp4";
					print "<p>New file name = " . $newSonoFile . "</p>";
					
					$success = $ff->generateSonogram( $newFile, $newSonoFile );
					
					if ( $success ) {
						// Remove the intermediate file
						print "<p>Removing intermediate file " . $newFile . "</p>";
						try {
							unlink($newFile);
						}
						catch (Exception $e) {
							print ("<br>Couldn't delete file: " . $newFile);
							throw $e;
						}
						
						$photoId = writeSplitFile($ofId, $newSonoFile, $start);
						print "<p>Written to Photo table - photo_id = " . $photoId . "</p>";
						if ( !$photoId ) {
							print "<p>Write to Photo table failed< for file " . $newSonoFile . "/p>";
							$problem = true;
						}
					}
					else {
						print "<p>Sonogram generation failed< for file " . $newFile . "/p>";
						$problem = true;
					}
				}
				else {
					print "<p>Writing to Photo table</p>";
					$photoId = writeSplitFile($ofId, $newFile, $start);
					print "<p>Written to Photo table - photo_id = " . $photoId . "</p>";
					if ( !$photoId ) {
						print "<p>Write to Photo table failed< for file " . $newFile . "/p>";
						$problem = true;
					}
				}
			}
			else {
				$problem = true;
			}
			
			$tag += 1;
			$start = $end;
			$fname = $filestem . "_" . $tag;
			$newFile = $fname . "." . $ext;
			print "<p>New file name = " . $newFile . "</p>";
			$success = $ff->splitFile ( $fullfilename, $newFile, $start );
				
			if ( $success ) {
				if ( $this->sonograms ) {
					$newstem = JFile::stripExt($newFile);
					
					$fname = $newstem . "_sono";
					$newSonoFile = $fname . ".mp4";
					print "<p>New file name = " . $newSonoFile . "</p>";
					
					$success = $ff->generateSonogram( $newFile, $newSonoFile );
					
					if ( $success ) {
						// Remove the intermediate file
						print "<p>Removing intermediate file " . $newFile . "</p>";
						try {
							unlink($newFile);
						}
						catch (Exception $e) {
							print ("<br>Couldn't delete file: " . $newFile);
							throw $e;
						}
						
						$photoId = writeSplitFile($ofId, $newSonoFile, $start);
						print "<p>Written to Photo table - photo_id = " . $photoId . "</p>";
						if ( !$photoId ) {
							print "<p>Write to Photo table failed< for file " . $newSonoFile . "/p>";
							$problem = true;
						}
					}
					else {
						print "<p>Sonogram generation failed< for file " . $newFile . "/p>";
						$problem = true;
					}
				}
				else {
					print "<p>Writing to Photo table</p>";
					$photoId = writeSplitFile($ofId, $newFile, $start);
					print "<p>Written to Photo table - photo_id = " . $photoId . "</p>";
					if ( !$photoId ) {
						print "<p>Write to Photo table failed< for file " . $newFile . "/p>";
						$problem = true;
					}		
				}				
			}
			else {
				$problem = true;
			}
		}
		// Remove original file
		// Decided to keep all original files
		// Need an archiving process for these and they need to be included in upload
		/*
		if ( !$problem ) {
			print "<p>Removing file " . $fullfilename . "</p>";
			
			try {
				unlink($fullfilename);
			}
			catch (Exception $e) {
				print ("<br>Couldn't delete file: " . $fullfilename);
				throw $e;
			}
		}
		*/
	}
	
	if ( $problem ) {
		print "<p>Problem splitting file " . $fullfilename . "</p>";
		// set OriginalFiles file to error status
		setOriginalFileStatus($ofId, false);
	}
	else {
		print "<p>File split successfully " . $fullfilename . "</p>";
		// set ToSplit file to success
		setOriginalFileStatus($ofId, true);
		
		
	}
	
}

print "<h1>File splitting complete</h1>";

?>


