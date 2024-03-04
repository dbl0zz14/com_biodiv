<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;


print '<h1>Transferring CamtrapDP files  to S3 bucket ' . get_mammalweb_bucket() . '</h1>';

foreach ( $this->filesToTransfer as $topLevelName=>$fileToTransfer ) {
	
	print '<h3>Transferring CamptrapDP file ' . $fileToTransfer . '</h3>';

			
	$key = "camtrapdp/" . $topLevelName . "/" . $topLevelName . '_' . $this->startDate . '_' . $this->endDate . ".zip";
	print ( "<br>Key = " . $key );
	
	print ( "<br>File = " . $fileToTransfer );
	try {
		upload_to_s3 ($key, $fileToTransfer);
		print ( "<p>CamtrapDP file transferred successfully, removing original</p>" );
		
		$path_parts = pathinfo($fileToTransfer);

		$fileDir = $path_parts['dirname'];
		$fileNameNoExt = $path_parts['filename']; 

		$dir = $fileDir . "/" . $fileNameNoExt;
		
		print ( "<p>Removing dir " . $dir . "</p>");
		FlxZipArchive::removeDir($dir);
		
		print ( "<p>Removing file " . $fileToTransfer . "</p>" );
		unlink($fileToTransfer);
	}
	catch(Exception $e) {
		print ( "br>" . $e->getMessage() );
	}
	
}
print '<h1>Transfer complete</h1>';



?>


