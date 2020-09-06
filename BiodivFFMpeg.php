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
		
		//error_log ( "splitFile returning");
		
		return $success;

	}


	public function generateWaveform ( $infile, $outfile ) {
		
		error_log("generate_waveform: infile = " . $infile . ", outfile = " . $outfile);
/*
		try {
			$audio = $this->ff->open($infile);
			
			$waveform = $audio->waveform(640, 120, array('#444444'));
			
			$waveform->save($outfile);
		}
		catch ( Exception $e ) {
			error_log ( $e->getMessage() );
		}
		
		error_log ( "generate_waveform returning");
*/
	}

	public function generateSonogram ( $infile, $outfile ) {
		
		error_log("generate_sonogram: infile = " . $infile . ", outfile = " . $outfile);
/*
		try {
			//$audio = $ff->open($infile);
			
			$audio = $this->ff->openAdvanced(array($infile));
			//$audio = $ff->open($infile);
			
			error_log("opened advanced");
			
			//$audio->filters()->custom('[0,a]','showspectrum=s=854x480:mode=separate:slide=scroll:color=rainbow:saturation=2:scale=log,format=yuv420p','[v]');
			
			//error_log("set custom filter");
			
			//$audio->setAdditionalParameters(array('-b:v 700k -b:a 360k'));
			
			//error_log("set additional parameters");
			
			//$audio->map(array('[v]', '0:a'), new X264('aac', 'libx264'), $outfile )
			//	->save();
			
			error_log("final command 1: " . $audio->getFinalCommand() );
			
			
			// Try a simple map
			$audio->map(array('0:a'), new Mp3(),"test.mp3" );
			
			error_log("final command: " . $audio->getFinalCommand() );
			
			error_log("mapped");
			
			$audio->save();
			
			//$waveform = $audio->waveform(640, 120, array('#444444'));
			//$waveform->filters()->setDownmix();
			//$waveform->save($outfile);
			
			/*
			$audio = $ff->open($infile);
			
			$audio->filters()->custom("compand");
			
			$waveform = $audio->waveform(640, 360, array('#444444','#666666'));
			//$waveform = $audio->waveform(array('#444444','#666666'));
			$waveform->save($outfile);
			*/
			/*
			$audio = $ff->openAdvanced(array($infile));
			
			$audio->filters()->custom('[0,v]', 'compand,showwavespic=s=640x360', '[v]');
			
			error_log("Applied filters about to save");
			
			//$audio->map(array('[v]'), new Png(), $outfile)
			//->save();
			
			$audio->save($outfile);
			*/
			/*
			error_log("Applied filters, about to create waveform");
			$waveform = $audio->waveform(640, 360, array('#444444','#666666'));
			error_log("Created waveform");
			//$waveform = $audio->waveform(array('#444444','#666666'));
			$waveform->save($outfile);
			*/
/*			
		}
		catch ( Exception $e ) {
			error_log ( $e->getMessage() );
		}
		*/
		error_log ( "generate_sonogram returning");

	}
}

?>