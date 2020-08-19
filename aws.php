<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/

// This file contains common functions used to work with AWS.
// We use it for S3 and Cognito.

// No direct access to this file
defined('_JEXEC') or die;


require_once('libraries/aws/aws-autoloader.php');

use Aws\S3\S3Client;  
use Aws\Exception\AwsException;
use Aws\Common\Exception\MultipartUploadException;
use Aws\S3\MultipartUploader;


function s3(){
	static $s3;
	$options = awsOptions();
	if (!$s3) {
		try {
			$s3 = new S3Client([
				'profile' => $options['profile'],
				'region' => 'eu-west-1',
				'version' => 'latest'
			]);
		}
		catch(Exception $e) {
			error_log ( $e->getMessage() );
		}
	}
	
	return $s3;
}


function list_aws_buckets () {

	$s3Client = s3();
	
	//Listing all S3 Bucket
	$buckets = $s3Client->listBuckets();
	foreach ($buckets['Buckets'] as $bucket) {
		echo $bucket['Name'] . "<br>";
	}

}

function get_mammalweb_bucket () {
	$options = awsOptions();
	return $options['bucket'];
}

function get_mammalweb_folder () {
	$options = awsOptions();
	if ( array_key_exists('folder', $options) ) {
		return $options['folder'];
	}
	else {
		return null;
	}
}

function upload_to_s3 ( $key, $file ) {
	
	$folder = get_mammalweb_folder();
	
	if ( $folder ) {
		$full_key = get_mammalweb_folder() . "/" . $key;
	}
	else {
		$full_key = $key;
	}
	
	
	// Prepare the upload parameters.
	$uploader = new MultipartUploader(s3(), $file, [
		'bucket' => get_mammalweb_bucket (),
		'key'    => $full_key
	]);

	// Perform the upload.
	try {
		$result = $uploader->upload();
		print "<br>Upload complete: {$result['ObjectURL']}";
	} catch (MultipartUploadException $e) {
		$uploader->abort();
		print "<br>Upload failed.";
		throw($e);
	} catch (Exception $e){
		print "<br>Problem with upload.";
		throw $e;
	}
	
	
}

function is_wave ( $filename ) {
	
	if ( preg_match('/_wave.png$/', $filename) === 1 ) {
		return true;
	}
	return false;
}

function post_s3_upload_actions ( $photo_id, $filename ) {
	// update the s3_status to 1 for this photo_id
	$db = JDatabase::getInstance(dbOptions());	
	
	// if file is a waveform do not update s3 status
	if ( is_wave($filename) === false ) {
		$fields = new stdClass();
		$fields->photo_id = $photo_id;
		$fields->s3_status = 1;
		$db->updateObject('Photo', $fields, 'photo_id');
	}

	// remove the file from the fileserver
	try {
		unlink($filename);
	}
	catch (Exception $e) {
		print ("<br>Couldn't delete file: " . $filename);
		throw $e;
	}
	
}

function s3URL ( $details ) {
	$options = awsOptions();
	$urlstem = $options['s3url'];
	
	$folder = get_mammalweb_folder();
	$folder_extra = "";
	
	if ( $folder ) {
		$folder_extra = "/" . $folder;
	}
	
	$person = $details['person_id'];
	$site = $details['site_id'];
	$filename = $details['filename'];
	return $urlstem . $folder_extra . '/person_' . $person . '/site_' . $site . '/' . $filename;
}


function s3WaveURL ( $details ) {
	$options = awsOptions();
	$urlstem = $options['s3url'];
	
	$folder = get_mammalweb_folder();
	$folder_extra = "";
	
	if ( $folder ) {
		$folder_extra = "/" . $folder;
	}
	
	$person = $details['person_id'];
	$site = $details['site_id'];
	$filename = $details['filename'];
	$wavefilename = JFile::stripExt($filename) . "_wave.png";
			
	return $urlstem . $folder_extra . '/person_' . $person . '/site_' . $site . '/' . $filename;
}

?>