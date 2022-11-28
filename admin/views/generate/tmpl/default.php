<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

printAdminMenu("TRANSLATIONS");
	
print '<div id="j-main-container" class="span10 j-toggle-main">';


print '<h2>'.$this->title.'</h2>';

print '<h4>Once downloaded, open a new file in Excel and load using the "Get data from text" function, ensuring file origin is set to utf8 and delimiter is ",".  Then save locally and upload to Transifex manually</h4>';

print '<a href="'.$this->reportURL.'"><button type="button" class="btn js-stools-btn-clear" title="Download report" >Download here</button></a>';

print '</div>';



// if ( $this->species ) {
	// print_r ( $this->speciesToTranslate );
// }

// foreach ( $this->species as $optionId=>$species ) {
	
	// print $optionId . ',' . $species->scientificName . ',' . $species->option_name;
	
// }

/*
$ff = new BiodivFFMpeg();

$err_msg = print_r ( $this->files, true );
error_log ( "Files to split: " . $err_msg );

foreach ( $this->files as $bigFile ) {
	
	$ofId = $bigFile['of_id'];
	
	print "<h2>Splitting file " . $bigFile['upload_filename'] . "</h2>";
	print "<h2>File id = " . $ofId . ", filename = " . $bigFile['filename'] . "</h2>";
	
	// get the duration.
	$fullfilename = $bigFile['dirname'] . "/" . $bigFile['filename'];
	
	print "<h3>Full filename: " . $fullfilename . "</h3>";
	error_log ( "Full filename:  " . $fullfilename );
	error_log ( "calling getDuration with file " . $fullfilename );
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
		error_log ( "Length left = " . $lengthLeft );
		$tag = 1;
		$start = 0;
		$success = true;
		$ext = JFile::getExt($fullfilename); 
		$filestem = JFile::stripExt($fullfilename);
		
		while ( $lengthLeft >= $twoFiles ) {
			error_log ( "Length left = " . $lengthLeft );
			error_log ( "More than two files left, processing next one" );
			$fname = $filestem . "_" . $tag;
			$newFile = $fname . "." . $ext;
			print "<p>New file name = " . $newFile . "</p>";
			$end = $start + $oneFile;
			
			error_log ( "About to split in >2 files section" );
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
			
			error_log ( "oneFile = " . $oneFile );
			//$lengthLeft -= $oneFile;
			$lengthLeft = $lengthLeft - $oneFile;
			error_log ( "Length left = " . $lengthLeft );
			$start = $end;
			$tag += 1;
		}
		
		// If the last length is only just over a minute, no need to split into two
		if ( $lengthLeft < $oneFile + 1 ) {
			error_log ( "Length left = " . $lengthLeft );
			error_log ( "Length left is < 31 secs so process as single final file" );
			
			$fname = $filestem . "_" . $tag;
			$newFile = $fname . "." . $ext;
			print "<p>New file name = " . $newFile . "</p>";
			
			error_log ( "About to split final file" );
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
			error_log ( "Length left is enough for two files, processing last two.." );
			error_log ( "Length left = " . $lengthLeft );
			$firstLength = intVal($lengthLeft/2);
			$end = $start + $firstLength;
			$fname = $filestem . "_" . $tag;
			$newFile = $fname . "." . $ext;
			
			print "<p>New file name = " . $newFile . "</p>";
			error_log ( "About to split first of last two" );
			error_log ( "New file name: " . $newFile );
			$success = $ff->splitFile ( $fullfilename, $newFile, $start, $firstLength );
				
			if ( $success ) {
				if ( $this->sonograms ) {
					$newstem = JFile::stripExt($newFile);
					
					$fname = $newstem . "_sono";
					$newSonoFile = $fname . ".mp4";
					print "<p>New file name = " . $newSonoFile . "</p>";
					
					error_log ( "About to call generateSonogram for first of last two files " . $newFile);
					
					$success = $ff->generateSonogram( $newFile, $newSonoFile );
					
					error_log ( "generateSonogram for first of last two, success = " . $success );
					
					if ( $success ) {
						// Remove the intermediate file
						error_log ( "Removing intermediate file " . $newFile );
						print "<p>Removing intermediate file " . $newFile . "</p>";
						try {
							unlink($newFile);
						}
						catch (Exception $e) {
							print ("<br>Couldn't delete file: " . $newFile);
							throw $e;
						}
						
						error_log ( "Writing split file " . $newSonoFile );
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
					error_log ( "Writing to Photo table " . newFile );
					$photoId = writeSplitFile($ofId, $newFile, $start);
					print "<p>Written to Photo table - photo_id = " . $photoId . "</p>";
					if ( !$photoId ) {
						print "<p>Write to Photo table failed< for file " . $newFile . "/p>";
						$problem = true;
					}
				}
			}
			else {
				error_log ( "Problem writing with call to splitFile "  );
				$problem = true;
			}
			
			error_log ( "Shifting start" );
			
			$tag += 1;
			$start = $end;
			$fname = $filestem . "_" . $tag;
			$newFile = $fname . "." . $ext;
			print "<p>New file name = " . $newFile . "</p>";
			
			error_log ( "About to split second of last two" );
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
		
		// if ( !$problem ) {
			// print "<p>Removing file " . $fullfilename . "</p>";
			
			// try {
				// unlink($fullfilename);
			// }
			// catch (Exception $e) {
				// print ("<br>Couldn't delete file: " . $fullfilename);
				// throw $e;
			// }
		// }
		
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
*/

?>


