<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/

// This file contains common functions used to work with the  audio file library for ffmpeg.
// We use it for creating sonograms

// No direct access to this file
defined('_JEXEC') or die;


//require 'libraries/vendor/autoload.php';

class BiodivFFMpeg {
	
	private $redlineFile = null;
	
	function __construct()
	{
		// Hardcoded
		$this->redlineFile = JPATH_SITE."/media/com_biodiv/images/redline.jpg";
	}
	
	public function getDuration ( $infile ) {
		
		error_log ( "getDuration called for file " . $infile );
		
		$success = true;
		
		$command = "ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 " . $infile;
		$duration = exec($command, $output, $returnVar);
		
		if ( $returnVar !== 0 ) {
			error_log ( "getDuration failed: " . $output );
			$success = false;
		}
		
		error_log ( "Duration returned as " . $duration );
			
		return $success ? $duration : null;
	}
	
	
	public function splitFile ( $infile, $outfile, $start, $duration = null ) {
		
		error_log("splitFile: infile = " . $infile . ", outfile = " . $outfile . ", start = " . $start . ", duration = " . $duration );
		
		$success = true;
		
		// NB might need to add 1 to end
		$command = "ffmpeg -i " . $infile . " -acodec copy -ss " . $start;

		if ( $duration ) {
			//$d = $duration + 1;
			$command .= " -t " . $duration;
		}
		
		$command .= " " . $outfile;
		
		error_log ( "splitFile: command = " . $command );
		
		exec($command, $output, $returnVar);
		
		if ( $returnVar !== 0 ) {
			error_log ( "splitFile failed" );
			$success = false;
		}
		
		return $success;

	}


	public function convertAviToMp4 ( $infile, $outfile, $createDate ) {
		
		error_log("convertAviToMp4: infile = " . $infile . ", outfile = " . $outfile . ", createDate = " . $createDate  );
		
		$success = true;
		
		$command = "ffmpeg -i " . $infile . " -metadata creation_time=\"".$createDate."\" -c:av copy  " . $outfile;

		error_log ( "convertAviToMp4: command = " . $command );
		
		exec($command, $output, $returnVar);
		
		if ( $returnVar !== 0 ) {
			error_log ( "convertAviToMp4 failed" );
			$success = false;
		}
		
		return $success;

	}


	public function generateSonogram ( $infile, $outfile ) {
		
		error_log("generateSonogram: infile = " . $infile . ", outfile = " . $outfile);
		
		$success = true;
		
		// Explanation of parameters:
		// [0:a]showspectrum - from the first infile audio, generate a spectrum using the following parameters
		// s=854x480 - size of video output - default is 640x512
		// mode=combined - all channels are displayed in the same row (eg stereo is 2 channels)
		// slide=scroll - the samples scroll from right to left
		// saturation=0.2 - how saturated the displayed colours are, ie more grey or more vivid
		// scale=log - this is the scale used for calculating the colour intensity values
		// legend=1 - draws the axes
		// format=yuv420p - use the yuv420p 'chroma subsampling scheme'
		// [v] - the name of the resulting video
		// -map "[v]" - map the created video into the output
		// -map 0:a - map the audio of the first input into the output
		// -b:v 700k - set the bitrate of video in output to 700kbit/s
		// -b:a 360k - set the bitrate of video in output to 360kbit/s
		
		/* replace with a staged process to get a static sonogram with red line moving across
		
		$command = 'ffmpeg -i ' . $infile . ' -filter_complex "[0:a]showspectrum=s=640x512:mode=combined:slide=replace:saturation=0.2:scale=log,format=yuv420p[v]" -map "[v]" -map 0:a -b:v 70k -b:a 360k  ' . $outfile;

		exec($command, $output, $returnVar);
		
		if ( $returnVar !== 0 ) {
			error_log ( "generateSonogram failed" );
			$success = false;
		}
		
		*/
		
		// Use infile name as a base for interim filenames.
		$ext = JFile::getExt($outfile);
		$outBasename = JFile::stripExt($outfile);
		
		error_log ( "generateSonogram: about to generate pic" );
		
		// First generate the sonogram still
		$stillFilename = $outBasename . '_pic.jpg';
		$command = 'ffmpeg -i ' . $infile . ' -filter_complex "[0:a]showspectrumpic=s=720x512:mode=combined:color=channel:saturation=0.2:scale=log:legend=0" ' . $stillFilename;

		error_log ( "generate sono pic command = " . $command );
			
		exec($command, $output, $returnVar);
		
		error_log ( "pic command completed, returnVar = " . $returnVar);
		
		if ( $returnVar !== 0 ) {
			error_log ( "generateSonogram failed on showspectrumpic" );
			$success = false;
		}
		
		error_log ( "generateSonogram: pic generation complete, success = " . $success );
		
		// Then create a video of the still with the audio as sound track
		if ( $success ) {
			
			error_log ( "About to generate video" );
			$vidFilename = $outBasename . '_vid.mp4';
			$command = 'ffmpeg -loop 1 -i ' . $stillFilename . ' -i ' . $infile . ' -c:v libx264 -tune stillimage -c:a aac -b:a 360k -pix_fmt yuv420p -shortest ' . $vidFilename;

			error_log ( "generate video command = " . $command );
			
			exec($command, $output, $returnVar);
			
			error_log ( "vid command completed, returnVar = " . $returnVar);
			
			if ( $returnVar !== 0 ) {
				error_log ( "generateSonogram failed on video creation" );
				$success = false;
			}
			
			error_log ( "generateSonogram: video generation complete" );
		
		}
		
		// Finally overlay the red line.
		if ( $success ) {
			
			error_log ( "generateSonogram: about to overlay" );
			
			// Get the audio duration:
			error_log ( "calling getDuration with file " . $infile );
			$dur = round($this->getDuration($infile), 2);
						
			// Check the redline file exists
			if ( file_exists ( $this->redlineFile ) ) {
			
				$command = 'ffmpeg -i ' . $vidFilename . ' -i ' . $this->redlineFile . '  -filter_complex "overlay=x=\'if(gte(t,0), w+(t)*W/'.$dur.', NAN)\':y=0"  ' . $outfile;
				
				error_log ( "overlay command = " . $command );

				exec($command, $output, $returnVar);
				
				if ( $returnVar !== 0 ) {
					error_log ( "generateSonogram failed on overlay" );
					$success = false;
				}
			}
			else {
				// skip overlay:
				error_log ( "No redline file ( " . $this->redlineFile . " ), skipping overlay" );
				JFile::copy ( $vidFilename, $outfile );
			}
			
			error_log ( "generateSonogram: overlay complete" );
			
		
		}
		
		error_log ( "generateSonogram: success code = " . $success );
		
		// Remove interim files
		if ( $success ) {
			JFile::delete ( $stillFilename );
			JFile::delete ( $vidFilename );
		}
		
		error_log("generateSonogram: returning");
		
		return $success;

	}
}

?>