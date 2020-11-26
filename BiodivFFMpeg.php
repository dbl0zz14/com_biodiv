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
	
	
	
	function __construct()
	{
		
			
	}
	
	public function getDuration ( $infile ) {
		
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
		
		error_log("splitFile: infile = " . $infile );
		
		$success = true;
		
		// NB might need to add 1 to end
		$command = "ffmpeg -i " . $infile . " -acodec copy -ss " . $start;

		if ( $duration ) {
			//$d = $duration + 1;
			$command .= " -t " . $duration;
		}
		
		$command .= " " . $outfile;
		
		exec($command, $output, $returnVar);
		
		if ( $returnVar !== 0 ) {
			error_log ( "splitFile failed" );
			$success = false;
		}
		
		return $success;

	}


	public function generateSonogram ( $infile, $outfile ) {
		
		error_log("generate_sonogram: infile = " . $infile . ", outfile = " . $outfile);
		
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
		
		$command = 'ffmpeg -i ' . $infile . ' -filter_complex "[0:a]showspectrum=s=720x512:mode=combined:slide=scroll:saturation=0.2:scale=log,format=yuv420p[v]" -map "[v]" -map 0:a -b:v 700k -b:a 360k  ' . $outfile;

		exec($command, $output, $returnVar);
		
		if ( $returnVar !== 0 ) {
			error_log ( "generateSonogram failed" );
			$success = false;
		}
		
		return $success;

	}
}

?>