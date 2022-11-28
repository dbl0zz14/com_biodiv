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
		
		$success = true;
		
		$command = "ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 " . $infile;
		$duration = exec($command, $output, $returnVar);
		
		if ( $returnVar !== 0 ) {
			$success = false;
		}
		
		return $success ? $duration : null;
	}
	
	
	public function splitFile ( $infile, $outfile, $start, $duration = null ) {
		
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
			$success = false;
		}
		
		return $success;

	}


	public function convertAviToMp4 ( $infile, $outfile, $createDate ) {
		
		$success = true;
		
		$command = "ffmpeg -i " . $infile . " -metadata creation_time=\"".$createDate."\"  " . $outfile;

		exec($command, $output, $returnVar);
		
		if ( $returnVar !== 0 ) {
			$success = false;
		}
		
		return $success;

	}
	
	
	
	
	public function addCaption ( $infile, $outfile, $textToAdd ) {
		
		$success = true;
		
		$command = "ffmpeg -i " . $infile . "  -vf \"drawtext=text='". $textToAdd ."':x=10:y=H-th-10:fontfile=/usr/share/fonts/dejavu/DejaVuSans.ttf:fontsize=12:fontcolor=white:shadowx=1:shadowy=1\"  " . $outfile;

		exec($command, $output, $returnVar);
		
		if ( $returnVar !== 0 ) {
			$success = false;
		}
		
		return $success;

	}

	


	public function generateSonogram ( $infile, $outfile ) {
		
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
		// -b:a 360k - set the bitrate of audio in output to 360kbit/s
		
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
		
		// First generate the sonogram still
		$stillFilename = $outBasename . '_pic.jpg';
		//$command = 'ffmpeg -i ' . $infile . ' -filter_complex "[0:a]showspectrumpic=s=720x512:mode=combined:color=channel:saturation=0.2:scale=log:legend=0" ' . $stillFilename;
		$command = 'ffmpeg -i ' . $infile . ' -filter_complex "[0:a]showspectrumpic=s=720x256:mode=combined:color=channel:saturation=0.2:scale=log:stop=8500:legend=0" ' . $stillFilename;

		exec($command, $output, $returnVar);
		
		if ( $returnVar !== 0 ) {
			$success = false;
		}
		
		// Then create a video of the still with the audio as sound track
		if ( $success ) {
			
			$vidFilename = $outBasename . '_vid.mp4';
			$command = 'ffmpeg -loop 1 -i ' . $stillFilename . ' -i ' . $infile . ' -c:v libx264 -tune stillimage -c:a aac -b:a 360k -pix_fmt yuv420p -shortest ' . $vidFilename;

			exec($command, $output, $returnVar);
			
			if ( $returnVar !== 0 ) {
				$success = false;
			}
			
		}
		
		// Finally overlay the red line.
		if ( $success ) {
			
			// Get the audio duration:
			$dur = round($this->getDuration($infile), 2);
						
			// Check the redline file exists
			if ( file_exists ( $this->redlineFile ) ) {
			
				$command = 'ffmpeg -i ' . $vidFilename . ' -i ' . $this->redlineFile . '  -filter_complex "overlay=x=\'if(gte(t,0), w+(t)*W/'.$dur.', NAN)\':y=0"  ' . $outfile;
				
				exec($command, $output, $returnVar);
				
				if ( $returnVar !== 0 ) {
					//error_log ( "generateSonogram failed on overlay" );
					$success = false;
				}
			}
			else {
				// skip overlay:
				//error_log ( "No redline file ( " . $this->redlineFile . " ), skipping overlay" );
				JFile::copy ( $vidFilename, $outfile );
			}
			
			error_log ( "generateSonogram: overlay complete" );
			
		
		}
		
		// Remove interim files
		if ( $success ) {
			JFile::delete ( $stillFilename );
			JFile::delete ( $vidFilename );
		}
		
		return $success;

	}
}

?>