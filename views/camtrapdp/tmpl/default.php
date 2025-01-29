<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;


print '<h1>Transferring CamtrapDP files  to S3 bucket ' . get_mammalweb_bucket() . '</h1>';

foreach ($this->filesToTransfer as $topLevelName=>$arrayToTransfer) {
	$fileLabel = 1;
	foreach ( $arrayToTransfer as $fileToTransfer ) {
		
		print '<h3>Transferring CamptrapDP file ' . $fileToTransfer . '_' . $fileLabel . '</h3>';

		if ( $this->zipFileTag ) {
			$key = "camtrapdp/" . $topLevelName . "/" . $topLevelName . '_' . $this->startDate . '_' . $this->endDate . "_".$this->zipFileTag. '_' . $fileLabel.".zip";
		}
		else {
			$key = "camtrapdp/" . $topLevelName . "/" . $topLevelName . '_' . $this->startDate . '_' . $this->endDate . '_' . $fileLabel . ".zip";
		}
				
		print ( "<br>Key = " . $key );
		
		print ( "<br>File = " . $fileToTransfer );
		try {
			upload_to_s3 ($key, $fileToTransfer);
			print ( "<p>CamtrapDP file transferred successfully, removing original</p>" );
			error_log ( "<p>CamtrapDP file transferred successfully, removing original</p>" );
			
			print ( "<p>Removing file " . $fileToTransfer . "</p>" );
			error_log ( "<p>Removing file " . $fileToTransfer . "</p>" );
			unlink($fileToTransfer);
		}
		catch(Exception $e) {
			print ( "<br>" . $e->getMessage() );
		}
		$fileLabel = $fileLabel+1;
		
	}
	
	$path_parts = pathinfo($fileToTransfer);

	$fileDir = $path_parts['dirname'];
	$dirToRemove = $topLevelName . '_' . $this->startDate . '_' . $this->endDate;
	
	$dir = $fileDir . "/" . $dirToRemove;
	
	print ( "<p>Removing dir " . $dir . "</p>");
	error_log ( "<p>Removing dir " . $dir . "</p>");
	FlxZipArchive::removeDir($dir);
			
			
}
print '<h1>Transfer complete</h1>';



?>


