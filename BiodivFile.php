<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

include_once "local.php";

// Represent a file, initially for parsing meta data when uploading.
// NB We have a filename and an originalFilename which will have the datetime formatting if applicable

class BiodivFile {
	
	const IMAGE = 0;
    const VIDEO = 1;
    const AUDIO = 2;
	
	private $filename;
	private $originalFilename;
	private $ext;
	private $type;
	private $exif;
	private $taken;
	
	
	function __construct( $filename, $originalFilename )
	{
		$this->filename = $filename;
		$this->originalFilename = $originalFilename;
		$this->ext = strtolower(JFile::getExt($originalFilename));
		
		switch ( $this->ext ) {
			case 'mp3':
			case 'm4a':
			case 'wav':
				$this->type = self::AUDIO;
				break;
			case 'mp4':
			case 'avi':
				$this->type = self::VIDEO;
				break;
			default:
				$this->type = self::IMAGE;
		}
				
		$this->taken = null;
		$this->exif = null;
	}
	
	// Return the date the photo was originally taken or video/audio originally recorded
	function takenDate () {
		if ( $this->taken == null ) {
			$this->generateMeta();
		}
		return $this->taken;
	}
	
	// Return the serialized exif for this image/video/audio
	function exif () {
		if ( $this->exif == null ) {
			$this->generateExif();
		}
		if ( $this->exif ) {
			return serialize($this->exif);
		}
		else {
			return null;
		}
	}
	
	// Return whether or not this is an audio (only) file, based on extension
	function isAudio () {
		return $this->type == self::AUDIO;
	}
	
	function isImage () {
		return $this->type == self::IMAGE;
	}
	
	function generateExif () {
		if ( $this->isImage() ) {
			$this->exif = exif_read_data($this->filename);
		}
		else {
			$this->exif = $this->getVideoMeta();
		}
	}
	
	function generateMeta () {
		if ( $this->isImage() ) {
			$this->exif = exif_read_data($this->filename);
			$this->taken = $this->exif['DateTimeOriginal'];
		}
		else {
			$this->exif = $this->getVideoMeta();
			
			$err_str = print_r ( array_keys($this->exif), true );
			error_log ( "generateMeta - exif array keys: " . $err_str );
			
			if ( array_key_exists ( 'error', $this->exif ) ) {
				$err_str = print_r ( $this->exif['error'] );
				error_log ( "Error generating meta for file " . $this->filename . ": " . $err_str );
			}
			
			switch ( $this->ext ) {
				
				case 'mp4':
				case 'm4a':
					$creation_time_unix = $this->exif['quicktime']['moov']['subatoms'][0]['creation_time_unix'];
					$this->taken = date('Y-m-d H:i:s', $creation_time_unix);
					
					break;
					
				case 'wav':
					error_log ( "Found wav audio file, ext is " . $this->ext );
					$date_found = false;
					
					// Test for a wamd or guan chunk (Wildlife Acoustics Songmeter) and if exists get datetime
					$this->setDateFromWavMeta ();
					
					error_log ( "generateMeta, taken from meta data = " . $this->taken );
					
					if ( $this->taken == null )
					{
						error_log ("Unable to get date from wav meta data eg wamd, guan or INFO chunk");
					}
					
					if ( $this->taken == null )
					{
						$this->setDateFromFilename( true );
						if ( !$this->taken ) {
							addMsg("error","File upload unsuccessful for $this->filename. Incorrect filename format.  Should be similar to myfile_YYYYMMDD_HHmmss.wav or myfileYYYY-MM-DD_HH-mm-ss.wav");
						}
					}
					break;
				
				default:		// mp3, avi
					$this->setDateFromFilename( true );
					if ( !$this->taken ) {
						addMsg("error","File upload unsuccessful for $this->filename. Incorrect filename format.  Should be similar to myfile_YYYYMMDD_HHmmss.mp3 or myfileYYYY-MM-DD_HH-mm-ss.wav");
					}
					break;
			}	
		}
	}
	
	function setDateFromWavMeta () {
		
		// The idea here is to add different file formats from the various recording devices as we meet them.
		// wamd is found in some Wildlife Acoustics Songmeters, guan in newer ones
		// AudioMoths store a comment with the datetime in an INFO chunk
		if ( $this->exif == null ) {
			$this->generateMeta();
		}
		
		$isWamd = array_key_exists ( 'wamd', $this->exif['riff']['WAVE'] );
					
		if ( $isWamd ) {
			error_log ("Found wamd chunk");
			$this->setDateFromWamd();
		}
		
		if ( !$this->taken ) {
			$isGuan = array_key_exists ( 'guan', $this->exif['riff']['WAVE'] );
			
			if ( $isGuan ) {
				error_log ("Found guan chunk");
				$this->setDateFromGuan();
			}
		}
		
		if ( !$this->taken ) {
			
			// Is there an INFO chunk?
			$isInfo = array_key_exists ( 'INFO', $this->exif['riff']['WAVE'] );
			
			if ( $isInfo ) {
				error_log ("Found info chunk");
				$this->setDateFromInfo();
			}
		}
		if ( !$this->taken ) {
			error_log ( "No taken datetime found in wamd, guan or INFO chunk - what is in the WAVE?" );
			
			$err_str = print_r ( array_keys($this->exif['riff']['WAVE']), true );
			error_log ( "wave = " . $err_str );
		}
	}
	
	function setDateFromWamd () {
		
		error_log ( "setDateFromWamd called" );
		
		if ( $this->exif == null ) {
			$this->generateMeta();
		}
		
		error_log ( "About to find offset" );
		
		$wamd_offset = $this->exif['riff']['WAVE']['wamd'][0]['offset'];
		$wamd_size = $this->exif['riff']['WAVE']['wamd'][0]['size'];
			
		error_log ( "WAMD offset = " . $wamd_offset . ", WAMD size = " . $wamd_size );
			
		error_log ("About to open " . $this->filename . " for direct reading" );
			
		if(!$fh = fopen ($this->filename, 'r')) {
			error_log ( "Can't open file " . $this->filename . " for reading" );
			$this->taken = null;
			return false;
		}
		
		fseek ( $fh, $wamd_offset );
		
		error_log ("About to read chunkId from " . $this->filename  );
		
		$chunkId = fread($fh, 4);
		
		error_log ( "Chunk id: {$chunkId}" );

		if ($chunkId === 'wamd') {

			error_log ("wamd found.  About to read and unpack size from " . $this->filename  );
		  
			$size = unpack('V', fread($fh, 4));
			$wamd_size = $size[1];
		  
			$size_str = print_r ( $size, true );
	
			error_log ( "Size: {$size_str}" );
		  
			$size_read = 0;
			$timestampFound = false;
		  
			while ( $size_read < $wamd_size && !$timestampFound ) {
		  
				$id = unpack('C', fread($fh, 2));
				$size_read += 2;
				$err_str = print_r ( $id, true );
				error_log ( "id: " . $err_str );
			  
			  
				$format = array(
					0x00 => 'version',
					0x01 => 'model',
					0x02 => 'serial',
					0x03 => 'firmware',
					0x04 => 'prefix',
					0x05 => 'timestamp',
					0x06 => 'gpsfirst',
					0x07 => 'gpstrack',
					0x08 => 'software',
					0x09 => 'license',
					0x0A => 'notes',
					0x0B => 'auto_id',
					0x0C => 'manual_id',
					0x0D => 'voicenotes',
					0x0E => 'auto_id_stats',
					0x0F => 'time_expansion',
					0x10 => 'program',
					0x11 => 'runstate',
					0x12 => 'microphone',
					0x13 => 'sensitivity',
					0x14 => 'position',
					0x15 => 'internaltemp',
					0x16 => 'externaltemp',
					0x17 => 'humidity',
					0x18 => 'light',
					0xff => 'padding',


					);
					
				$id_str = $format[$id[1]];

				error_log ("Id from format: {$format[$id[1]]}");
			  
				$len = unpack('V', fread($fh, 4));		
				$size_read += 4;		  
				$err_str = print_r ( $len, true );
				error_log ( "len: " . $err_str );
			  
				if ( $len[1] == 2 ) {
				  $val = unpack('v', fread($fh, $len[1]));		
				  $size_read += 2;		  
				  $err_str = print_r ( $val, true );
				  error_log ( "val: " . $err_str );
				}
				else if ( $len[1] == 4 ) {
				  $val = unpack('V', fread($fh, $len[1]));		
				  $size_read += 4;		  
				  $err_str = print_r ( $val, true );
				  error_log ( "val: " . $err_str );
				}
				else {
				  $val_format = 'A'.$len[1].$id_str;
				  $val = unpack($val_format, fread($fh, $len[1]));		
				  $size_read += $len[1];		  
				  $err_str = print_r ( $val, true );
				  error_log ( "val: " . $err_str );
				}
			  
				// If it was the timestamp store it and exit the while loop
				if ( $id[1] == 5 ) {
				  error_log ( "Got timestamp" );
				  
				  $this->taken = $val['timestamp'];
				  
				  error_log ( "Set taken to " . $this->taken );
				  $timestampFound = true;
				}
			  
			  //$val = unpack('v', fread($fh, $len[1]));				  
			  //$err_str = print_r ( $val, true );
			  //error_log ( "val: " . $err_str );
			}
		  
			error_log ("End of wamd chunk" );
		}
		fclose ($fh);
		
	}
	
	function setDateFromGuan () {
		
		if ( $this->exif == null ) {
			$this->generateMeta();
		}
		
		
		// Look for guan chunk
		$guanChunk = null;
		
		try {
			$guanChunk = $this->exif['riff']['WAVE']['guan'];
		} 
		catch ( Exception $e ) {
			error_log ( "Exception looking for guan chunk in WAVE" );
		}
		
		if ( !$guanChunk ) {
			error_log ( "No guan chunk found" );
		}
		else if ( is_array ( $guanChunk ) ) {
			$err_str = print_r ( $guanChunk, true );
			error_log ( "Guan chunk: " . $err_str );
			
			$guanData = $guanChunk[0]['data'];
			
			/* Songmeter mini example of data is:
				GUANO|Version:1.0
				Firmware Version:1.1
				Make:Wildlife Acoustics, Inc.
				Model:Song Meter Mini
				Serial:SMA00646
				WA|Song Meter|Prefix:SMA00646
				WA|Song Meter|Audio settings:[{"rate":24000,"gain":18}]
				Length:300.000
				Original Filename:SMA00646_20201017_130500.wav
				Timestamp:2020-10-17 13:05:00+1:00
				Loc Position:54.75656 -1.59106
				Temperature Int:21.25
				Samplerate:24000 
			ie it is UTF8 text with each data item on a new line.
			*/
			
			$guanDataArray = explode ( "\n", $guanData );
			error_log ( "guanDataArray[10] = " . $guanDataArray[9] );
			
			// Search through for the timestamp (probably at element 9 but can't guarantee that)
			foreach ( $guanDataArray as $element ) {
				if ( strpos ( $element, 'Timestamp:' ) === 0 ) {
					error_log ( "Found timestamp line" );
					// Everything after the : is the timestamp
					$this->taken = substr ( $element, 10 );
					error_log ( "Timestamp found in guan chunk is " . $this->taken );
				}
			}
		}
		else {
			error_log ( "Guan chunk: " . $guanChunk );
		}		
	}
	
	function setDateFromInfo () {
		
		if ( $this->exif == null ) {
			$this->generateMeta();
		}
		
		// Look for INFO chunk
		$infoChunk = null;
		
		try {
			$infoChunk = $this->exif['riff']['WAVE']['INFO'];
		} 
		catch ( Exception $e ) {
			error_log ( "Exception looking for INFO chunk in WAVE" );
		}
		
		if ( !$infoChunk ) {
			error_log ( "No INFO chunk found" );
		}
		else if ( is_array ( $infoChunk ) ) {
			$err_str = print_r ( $infoChunk, true );
			error_log ( "INFO chunk: " . $err_str );
			
			// AudioMoth stores the details as a comment eg “Recorded at 20:28:00 28/04/2020 (UTC) by AudioMoth 0FE081F80FE081F0 at gain setting 2 while battery state was 3.9V”
			if ( array_key_exists ( "ICMT", $infoChunk ) ) {
				error_log ( "Found ICMT comment" );
				$comment = $infoChunk['ICMT'][0]['data'];
				
				$rec_at = strpos ( $comment, 'Recorded at ' );
				$stop = strpos ( $comment, ' by AudioMoth' );
				
				if ( $rec_at === false || $stop === false ) {
					error_log ( "AudioMoth comment string not found - cannot read recorded datetime" );
				}
				else {
				
					$start = $rec_at + 12;
					
					$len = 19;
					
					$rec_date = substr ( $comment, $start, $len );
					
					error_log ( "Found recorded date: " . $rec_date );
					
					// Need to reformat the datetime string
					$date = date_create_from_format ( 'H:i:s d/m/Y', $rec_date);
					
					if ( $date ) {
						$this->taken = date_format($date, 'Y-m-d H:i:s');
					}
				}
			}
			else {
				error_log ( "No ICMT chunk in WAVE INFO chunk" );
			}
		}
		else {
			error_log ( "INFO chunk: " . $infoChunk );
		}		
	}
	
	
	function setDateFromWamdOrig () {
		/*
		
		if ( $this->exif == null ) {
			$this->generateMeta();
		}
		
		$isWamd = array_key_exists ( 'wamd', $this->exif['riff']['WAVE'] );
					
		if ( !$isWamd ) {
			$this->taken = null;
			return false;
		}
					
		error_log ("Found wamd chunk");
			
		$wamd_offset = $this->exif['riff']['WAVE']['wamd'][0]['offset'];
		$wamd_size = $this->exif['riff']['WAVE']['wamd'][0]['size'];
			
		error_log ( "WAMD offset = " . $wamd_offset . ", WAMD size = " . $wamd_size );
			
		//$section = file_get_contents($this->filename, FALSE, NULL, $this->exif['riff']['WAVE']['wamd'][0]['offset'], $this->exif['riff']['WAVE']['wamd'][0]['size']);
		//error_log ( "wamd section: " . $section );
			
		error_log ("About to open " . $this->filename . " for direct reading" );
			
		if(!$fh = fopen ($this->filename, 'r')) {
			error_log ( "Can't open file " . $this->filename . " for reading" );
			$this->taken = null;
			return false;
		}
			
		error_log ("About to read typeId from " . $this->filename  );

		$typeId = fread($fh, 4);

		error_log ("About to unpack " . $this->filename  );

		$lenP = unpack('V', fread($fh, 4));

		$len = $lenP[1];

		error_log ("About to read waveId from " . $this->filename  );

		$waveId = fread($fh, 4);			 

		if ($typeId === 'RIFF' && $waveId === 'WAVE') {

			  error_log ( "We have a WAV file" );

			  error_log ( "Chunk length: {$len}" );

		} else {

			error_log ( "File " . $this->filename . " not wav file" );
			return;

		}
		
		
		while ( !feof( $fh ) ) {
			error_log ("About to read chunkId from " . $this->filename  );
		
			$chunkId = fread($fh, 4);
			
			error_log ( "Chunk id: {$chunkId}" );
			
			// Handle wamd or guan chunk, read through others.
			if ($chunkId === 'wamd') {
			
		}
		
		
		error_log ("About to read chunkId from " . $this->filename  );
		
		$chunkId = fread($fh, 4);
		
		error_log ( "Chunk id: {$chunkId}" );

		if ($chunkId === 'fmt ') {

		  error_log ("About to read and unpack size from " . $this->filename  );
		  
		  $size = unpack('V', fread($fh, 4));
		  
		  $size_str = print_r ( $size, true );
	
		  error_log ( "Size: {$size_str}" );
		  
		  $d = fread($fh, 18);


		  if ($size[1] == 18) {

				$d = fread($fh, 18);

				$data = unpack('vfmt/vch/Vsr/Vdr/vbs/vbis/vext', $d);

				$format = array(

					  0x0001 => 'PCM',

					  0x0003 => 'IEEE Float',

					  0x0006 => 'ALAW',

					  0x0007 => 'MuLAW',

					  0xFFFE => 'Extensible',

				);

				error_log ("Format: {$format[$data['fmt']]}");

				error_log ("Channels: {$data['ch']}");

				error_log ("Sample Rate: {$data['sr']}");

				error_log ("Data Rate: {$data['dr']}");

				error_log ("Block Size: {$data['bs']}");

				error_log ("Bits/Sample: {$data['bs']}");

				error_log ("Extension Size: {$data['ext']}");

		  }
		  
		  if ($size[1] == 16) {
			  
			  error_log ("Got size 16 from fmt");

				$d = fread($fh, 16);

				//$data = unpack('vfmt/vch/Vsr/Vdr/vbs/vbis/vext', $d);
				$data = unpack('vfmt/vch/Vsr/Vdr/vbis/vext', $d);

				$format = array(

					  0x0001 => 'PCM',

					  0x0003 => 'IEEE Float',

					  0x0006 => 'ALAW',

					  0x0007 => 'MuLAW',

					  0xFFFE => 'Extensible',

				);

				error_log ("Format: {$format[$data['fmt']]}");

				error_log ("Channels: {$data['ch']}");

				error_log ("Sample Rate: {$data['sr']}");

				error_log ("Data Rate: {$data['dr']}");

				//error_log ("Block Size: {$data['bs']}");

				error_log ("Bits/Sample: {$data['bis']}");

				error_log ("Extension Size: {$data['ext']}");

		  }

		}
		
			
		// Continue readung chunks.  Can I fnd the Guan chunk? Get this is a loop eventually
		error_log ("About to read chunkId from " . $this->filename  );
		
		$chunkId = fread($fh, 4);
		
		error_log ( "Chunk id: {$chunkId}" );

		if ($chunkId === 'junk') {

		  error_log ("junk found.  About to read and unpack size from " . $this->filename  );
		  
		  $size = unpack('V', fread($fh, 4));
		  
		  $size_str = print_r ( $size, true );
	
		  error_log ( "Size: {$size_str}" );
		  
		  $d = fread($fh, $size[1]);
		}
		else {
			error_log ("junk not found");
		}
		
			
		error_log ("About to read chunkId from " . $this->filename  );
		
		$chunkId = fread($fh, 4);
		
		error_log ( "Chunk id: {$chunkId}" );

		if ($chunkId === 'data') {

		  error_log ("data found.  About to read and unpack size from " . $this->filename  );
		  
		  $size = unpack('V', fread($fh, 4));
		  
		  $size_str = print_r ( $size, true );
	
		  error_log ( "Size: {$size_str}" );
		  
		  $d = fread($fh, $size[1]);
		}
		else {
			error_log ("data not found");
		}
		
		
		error_log ("About to read chunkId from " . $this->filename  );
		
		$chunkId = fread($fh, 4);
		
		error_log ( "Chunk id: {$chunkId}" );

		if ($chunkId === 'wamd') {

		  error_log ("wamd found.  About to read and unpack size from " . $this->filename  );
		  
		  $size = unpack('V', fread($fh, 4));
		  $wamd_size = $size[1];
		  
		  $size_str = print_r ( $size, true );
	
		  error_log ( "Size: {$size_str}" );
		  
		  $size_read = 0;
		  $timestampFound = false;
		  
		  while ( $size_read < $wamd_size && !$timestampFound ) {
		  
			  $id = unpack('C', fread($fh, 2));
			  $size_read += 2;
			  $err_str = print_r ( $id, true );
			  error_log ( "id: " . $err_str );
			  
			  
			  $format = array(
					0x00 => 'version',
					0x01 => 'model',
					0x02 => 'serial',
					0x03 => 'firmware',
					0x04 => 'prefix',
					0x05 => 'timestamp',
					0x06 => 'gpsfirst',
					0x07 => 'gpstrack',
					0x08 => 'software',
					0x09 => 'license',
					0x0A => 'notes',
					0x0B => 'auto_id',
					0x0C => 'manual_id',
					0x0D => 'voicenotes',
					0x0E => 'auto_id_stats',
					0x0F => 'time_expansion',
					0x10 => 'program',
					0x11 => 'runstate',
					0x12 => 'microphone',
					0x13 => 'sensitivity',
					0x14 => 'position',
					0x15 => 'internaltemp',
					0x16 => 'externaltemp',
					0x17 => 'humidity',
					0x18 => 'light',
					0xff => 'padding',


					);
					
			  $id_str = $format[$id[1]];

			  error_log ("Id from format: {$format[$id[1]]}");
			  
			  $len = unpack('V', fread($fh, 4));		
			  $size_read += 4;		  
			  $err_str = print_r ( $len, true );
			  error_log ( "len: " . $err_str );
			  
			  if ( $len[1] == 2 ) {
				  $val = unpack('v', fread($fh, $len[1]));		
				  $size_read += 2;		  
				  $err_str = print_r ( $val, true );
				  error_log ( "val: " . $err_str );
			  }
			  else if ( $len[1] == 4 ) {
				  $val = unpack('V', fread($fh, $len[1]));		
				  $size_read += 4;		  
				  $err_str = print_r ( $val, true );
				  error_log ( "val: " . $err_str );
			  }
			  else {
				  $val_format = 'A'.$len[1].$id_str;
				  $val = unpack($val_format, fread($fh, $len[1]));		
				  $size_read += $len[1];		  
				  $err_str = print_r ( $val, true );
				  error_log ( "val: " . $err_str );
			  }
			  
			  // If it was the timestamp store it and exit the while loop
			  if ( $id[1] == 5 ) {
				  error_log ( "Got timestamp" );
				  
				  $this->taken = $val['timestamp'];
				  
				  error_log ( "Set taken to " . $this->taken );
				  $timestampFound = true;
			  }
			  
			  //$val = unpack('v', fread($fh, $len[1]));				  
			  //$err_str = print_r ( $val, true );
			  //error_log ( "val: " . $err_str );
		  }
		  
		  error_log ("End of wamd chunk" );
		  
		  */
		  
		  //$data = fread($fh, $len[1] );				  
		 
		  /* Create a format specifier */
		  /*
			$val_format = 'A'.$len[1].$id_str;  # Get the first len bytes
			error_log ( "val format: " . $val_format );
			
			// Unpack the header data 
			$valArray = unpack ($val_format, $data);
			
			$err_str = print_r ( $valArray, true );
			error_log ( "valArray: " . $err_str );
		  
			$val = $valArray[$id_str];
		  */
		  //$err_str = print_r ( $val, true );
		  //error_log ( "val: " . $val );
		  
		  
		  //error_log ( "id: " . $id[1] . ", len: " . $len[1] . ", val: " . $val );
		  
		  //$d = fread($fh, $size[1]);
		  
		  //error_log ( "wamd chunk before unpackimg gradually: " . $d );
		  
		  /* NB from Python library:  binary WAMD field identifiers
			WAMD_IDS = {
				0x00: 'version',
				0x01: 'model',
				0x02: 'serial',
				0x03: 'firmware',
				0x04: 'prefix',
				0x05: 'timestamp',
				0x06: 'gpsfirst',
				0x07: 'gpstrack',
				0x08: 'software',
				0x09: 'license',
				0x0A: 'notes',
				0x0B: 'auto_id',
				0x0C: 'manual_id',
				0x0D: 'voicenotes',
				0x0E: 'auto_id_stats',
				0x0F: 'time_expansion',
				0x10: 'program',
				0x11: 'runstate',
				0x12: 'microphone',
				0x13: 'sensitivity',
			}
			*/
		  
		  //$data = unpack('vfmt/vch/Vsr/Vdr/vbis/vext', $d);
		  /*
		  $data = unpack('Vid/Ilen/');

				$format = array(

					  0x0001 => 'PCM',

					  0x0003 => 'IEEE Float',

					  0x0006 => 'ALAW',

					  0x0007 => 'MuLAW',

					  0xFFFE => 'Extensible',

				);

				error_log ("Format: {$format[$data['fmt']]}");

				error_log ("Channels: {$data['ch']}");

				error_log ("Sample Rate: {$data['sr']}");

				error_log ("Data Rate: {$data['dr']}");

				//error_log ("Block Size: {$data['bs']}");

				error_log ("Bits/Sample: {$data['bis']}");

				error_log ("Extension Size: {$data['ext']}");
				*/
				/*
		}
		else {
			error_log ("wamd not found");
		}
		*/
			
		/* wamd has to be translated to guan I think
		error_log ("About to read chunkId from " . $this->filename  );
		
		$chunkId = fread($fh, 4);
		
		error_log ( "Chunk id: {$chunkId}" );

		if ($chunkId === 'guan') {

		  error_log ("guan found.  About to read and unpack size from " . $this->filename  );
		  
		  $size = unpack('V', fread($fh, 4));
		  
		  $size_str = print_r ( $size, true );
	
		  error_log ( "Size: {$size_str}" );
		  
		  $d = fread($fh, $size[1]);
		}
		*/
		
		
		
			
		//fclose($fh);
		
	}
	
	
	function setDateFromGuanOrig () {
		// Look for guan chunk
		$guanChunk = null;
		try {
			$guanChunk = $this->exif['riff']['guan'];
		} 
		catch ( Exception $e ) {
			error_log ( "Exception looking for guan chunk in riff" );
		}
		if ( !$guanChunk ) {
			try {
				$guanChunk = $this->exif['riff']['WAVE']['guan'];
			} 
			catch ( Exception $e ) {
				error_log ( "Exception looking for guan chunk in WAVE" );
			}
		}
		if ( !$guanChunk ) {
			error_log ( "No guan chunk found" );
		}
		else if ( is_array ( $guanChunk ) ) {
			$err_str = print_r ( $guanChunk, true );
			error_log ( "Guan chunk: " . $err_str );
			
			$guanData = $guanChunk[0]['data'];
			
			/* Songmeter mini example of data is:
				GUANO|Version:1.0
				Firmware Version:1.1
				Make:Wildlife Acoustics, Inc.
				Model:Song Meter Mini
				Serial:SMA00646
				WA|Song Meter|Prefix:SMA00646
				WA|Song Meter|Audio settings:[{"rate":24000,"gain":18}]
				Length:300.000
				Original Filename:SMA00646_20201017_130500.wav
				Timestamp:2020-10-17 13:05:00+1:00
				Loc Position:54.75656 -1.59106
				Temperature Int:21.25
				Samplerate:24000 
			ie it is UTF8 text with each data item on a new line.
			*/
			
			$guanDataArray = explode ( "\n", $guanData );
			error_log ( "guanDataArray[10] = " . $guanDataArray[9] );
			
			// Search through for the timestamp (probably at element 9 but can't guarantee that)
			foreach ( $guanDataArray as $element ) {
				if ( strpos ( $element, 'Timestamp:' ) === 0 ) {
					error_log ( "Found timestamp line" );
					// Everything after the : is the timestamp
					$this->taken = substr ( $element, 10 );
					error_log ( "Timestamp found in guan chunk is " . $this->taken );
				}
			}
		}
		else {
			error_log ( "Guan chunk: " . $guanChunk );
		}		
	}
	
	// Output to log file some details of the meta in the file given
	function checkMeta () {
		
		if ( !$this->exif ) {
			generateMeta ();
		}
		
		$top_keys = array_keys($this->exif);
		$err_str = print_r ( $top_keys, true );
		error_log ( "Top keys: " . $err_str );
					
		// Check all keys
		foreach ($top_keys as $key) {
			if ( is_array($this->exif[$key]) )  {
				$ks = array_keys($this->exif[$key]);
				$err_str = print_r ( $ks, true );
				error_log ( "array keys for " . $key . ": " . $err_str );
				
				foreach ($ks as $key2) {
					if ( is_array($this->exif[$key][$key2]) )  {
						$k2ks = array_keys($this->exif[$key][$key2]);
						
						$err_str = print_r ( $k2ks, true );
						error_log ( "array keys for " . $key . ", " . $key2 . ": " . $err_str );
						
					}
					else {
						error_log ( "array value for " . $key . ", " . $key2 . ": " . $this->exif[$key][$key2] );
					}
				}
			}
			else {
				error_log ( "array value for " . $key . ": " . $this->exif[$key] );
			}
		}
					
		$err_str = print_r ( $this->exif["audio"]["streams"], true );
		error_log ("Audio streams: " . $err_str );
		
		// Format same as MP4?
		$riff_keys = array_keys($this->exif['riff']);
		$err_str = print_r ( $riff_keys, true );
		error_log ( "Riff keys: " . $err_str );
		$wave_chunk = $this->exif['riff']['WAVE'];
		$wave_keys = array_keys($this->exif['riff']['WAVE']);
		$err_str = print_r ( $wave_keys, true );
		error_log ( "WAVE keys: " . $err_str );
					
					
		
					
		//$wave_contents = print_r ( array_keys($wave_chunk['INFO']), true );
		//error_log ( "WAVE info keys: " . $wave_contents );
					
					
		foreach ($wave_keys as $wk ) {
			$wk_arr = $this->exif['riff']['WAVE'][$wk];
			if ( is_array($wk_arr) ) {
				$ks = array_keys($wk_arr);
				$err_str = print_r ( $ks, true );
				error_log ( $wk . " array: " . $err_str );
			}
			else {
				error_log ( $wk . " value: " . $wk_arr );
			}
		}
	}
	
	function getVideoMeta () {
		
		// Initialize getID3 engine
		$getID3 = new getID3;
		
		// Analyze file and store returned data in $ThisFileInfo
		$fileinfo = $getID3->analyze($this->filename);
		
		return $fileinfo;

	}
	
	// Two options for file format, first (most common) is myfile_YYYYMMDD_HHmmss.ext
	// Alternative is myfile-YYY-MM-DD_HH-mm-ss.ext
	function setDateFromFilename ( $useOriginalName = false ) {
		$success = true;
		
		if ( $useOriginalName == true ) {
			$fileToUse = $this->originalFilename;
		}
		else {
			$fileToUse = $this->filename;
		}
		$ext = JFile::getExt($fileToUse);
		$no_extension = basename($fileToUse, '.'.$ext);
		error_log ( "Basename = " . $no_extension );
		$file_bits = explode('_', $no_extension);
		
		// Check we have at least 3 bits
		if ( count($file_bits) > 2 ) {
			error_log ( "Got more than two bits:" );
			$bits = print_r ( $file_bits, true );
			error_log ( $bits );
			$filetime = array_pop($file_bits);
			$filedate = array_pop($file_bits);
			if ( is_numeric($filetime) && is_numeric($filedate) ) {
				
				error_log ("Date and time are numeric " );
				
				$dateStr = $filedate . ' ' . $filetime;
				$unixTime = strtotime($dateStr);
				
				if ( $unixTime == 0 ) {
					error_log ("Can't create unix time from date string " . $dateStr );
					$success = false;
				}
				else {
					$this->taken = date('Y-m-d H:i:s', $unixTime );
				}
					
				error_log ("Got taken to be " . $this->taken );
				
				// Check format was ok
				$date_errors = date_get_last_errors();
				if ( $date_errors['warning_count'] > 0 || $date_errors['error_count'] > 0 ) {
					error_log("Errors or warnings when creating date");
					$success = false;
				}
			}
			else {
				error_log ("Date or time not numeric" );
				$success = false;
			}
		}
		else {
			error_log ("Not enough file bits" );
			$success = false;
		}
		if ( !$success ) {
			// Try alternative format.
			// Get the last 19 chars
			$dt = substr ( $no_extension, -19 );
			
			if ( $dt !== false ) {
				$dtArray = explode ( '_', $dt );
				
				if ( count($dtArray) == 2 ) {
					$dateBits = explode ( '-', $dtArray[0] );
					
					$err_str = print_r ( $dateBits, true );
					error_log ( "date arr " . $err_str );
					
					$filedate = intval($dateBits[0])*10000 + intval($dateBits[1])*100 + intval($dateBits[2]);
					
					error_log ("Got date int to be " . $filedate );
					
					$timeBits = explode ( '-', $dtArray[1] );
					
					$err_str = print_r ( $timeBits, true );
					error_log ( "time arr " . $err_str );
					
					$filetime = intval($timeBits[0])*10000 + intval($timeBits[1])*100 + intval($timeBits[2]);
					
					error_log ("Got time int to be " . $filetime );
					
					$dateStr = sprintf("%06d %06d",$filedate,$filetime);
					error_log ( "Date string = " . $dateStr );
					$unixTime = strtotime($dateStr);
				
					if ( $unixTime == 0 ) {
						error_log ("Can't create unix time from date string " . $dateStr );
						$success = false;
					}
					else {
						$this->taken = date('Y-m-d H:i:s', $unixTime );
					}
				
					error_log ("Got taken to be " . $this->taken );
					
					// Check format was ok
					$date_errors = date_get_last_errors();
					if ( $date_errors['warning_count'] == 0 || $date_errors['error_count'] == 0 ) {
						error_log("No errors or warnings when creating date");
						$success = true;
					}
				}
			}
		}
		if ( !$success ) {
			error_log ( "Errors setting time from filename, setting to null" );
			$this->taken = null;
		}
	}
}

?>