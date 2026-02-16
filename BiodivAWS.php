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


require_once __DIR__ . '/libraries/vendor/autoload.php';


use Aws\S3\S3Client;  
use Aws\Exception\AwsException;
use Aws\Common\Exception\MultipartUploadException;
use Aws\S3\MultipartUploader;

use Joomla\CMS\Filesystem\File;


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

function get_mammalweb_large_files_bucket () {
	$options = awsOptions();
	return $options['largefilesbucket'];
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

function upload_to_s3 ( $key, $file, $id = 0, $isLargeFile = false ) {
	
	$folder = get_mammalweb_folder();
	
	if ( $folder ) {
		$full_key = get_mammalweb_folder() . "/" . $key;
	}
	else {
		$full_key = $key;
	}
	
	$uploader = null;

        if ( $isLargeFile ) {
                $bucket = get_mammalweb_large_files_bucket();
                // Tags to apply to the uploaded object
                $tags = [
                        'TagSet' => [
                        [
                                'Key'   => 'olf_id',
                                'Value' => $id
                        ]
                        ],
                ];

                // Convert tags to a query string format
                $taggingHeader = http_build_query(array_column($tags['TagSet'], 'Value', 'Key'));

                // Prepare the upload parameters.
                $uploader = new MultipartUploader(s3(), $file, [
                        'bucket' => $bucket,
                        'key'    => $full_key,
                        'params' => [
                                'Tagging' => $taggingHeader, // Add tags to the upload
                                ]
                ]);

                //$tags = http_build_query([
                        //'olf_id' => $id
                //]);
        }
        else {
                $bucket = get_mammalweb_bucket ();

                // Prepare the upload parameters.
		        $uploader = new MultipartUploader(s3(), $file, [
                        'bucket' => $bucket,
                        'key'    => $full_key
                ]);

        }

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


function delete_from_s3 ( $key, $isLargeFile = false ) {
	
	try {
    		// Create an S3 client
		$s3Client = s3();
		
    		// Define bucket and object key
		if ( $isLargeFile ) {
			$bucket = get_mammalweb_large_files_bucket();
		}
		else {
    			$bucket = get_mammalweb_bucket();;
		}
		
    		// Delete the object
    		$result = $s3Client->deleteObject([
        		'Bucket' => $bucket,
        		'Key'    => $key,
    		]);

		return true;
		
		
	} catch (AwsException $e) {
    		// Output error message if fails
    		error_log ( "Error deleting object: " . $e->getAwsErrorMessage() );
		return false;
	}

}


function move_deletions_file_to_s3 ( $uploadPath ) {

	$fileExt = File::getExt($uploadPath);
        $fileStem = basename($uploadPath, '.'.$fileExt);
	$now = date('Y-m-d_H:i:s');
        $deletionsFileKey = 'deleted/'.$fileStem.'_'.$now.'.'.$fileExt;

	$bucket = get_mammalweb_bucket ();

        // Prepare the upload parameters.
        $uploader = new MultipartUploader(s3(), $uploadPath, [
                'bucket' => $bucket,
                'key'    => $deletionsFileKey
        ]);

	 // Perform the upload.
        try {
                $result = $uploader->upload();
        } catch (MultipartUploadException $e) {
                $uploader->abort();
                error_log ( "Deletions file move to S3 failed" );
                throw($e);
        } catch (Exception $e){
                error_log ( "Deletions file move to S3 failed" );
                throw $e;
        }

	// remove the file from the fileserver
        try {
                unlink($uploadPath);
        }
        catch (Exception $e) {
                print ("<br>Couldn't delete file: " . $uploadPath);
                throw $e;
        }


}
	

function post_s3_upload_actions ( $photo_id, $filename ) {
	// update the s3_status to 1 for this photo_id
	$db = JDatabase::getInstance(dbOptions());	
	
	$fields = new stdClass();
	$fields->photo_id = $photo_id;
	$fields->s3_status = 1;
	$db->updateObject('Photo', $fields, 'photo_id');

	// remove the file from the fileserver
	try {
		unlink($filename);
	}
	catch (Exception $e) {
		print ("<br>Couldn't delete file: " . $filename);
		throw $e;
	}
	
}

function post_s3_upload_actions_orig ( $of_id, $filename ) {
	// update the s3_status to 1 for this of_id
	$db = JDatabase::getInstance(dbOptions());	
	
	$fields = new stdClass();
	$fields->of_id = $of_id;
	$fields->s3_status = 1;
	$db->updateObject('OriginalFiles', $fields, 'of_id');

	// remove the file from the fileserver
	try {
		unlink($filename);
	}
	catch (Exception $e) {
		print ("<br>Couldn't delete file: " . $filename);
		throw $e;
	}
}

function post_s3_upload_actions_large ( $olf_id, $filename ) {
        // update the s3_status to 1 for this of_id
        $db = JDatabase::getInstance(dbOptions());

        $fields = new stdClass();
        $fields->olf_id = $olf_id;
        $fields->s3_status = 1;
        $db->updateObject('OriginalLargeFiles', $fields, 'olf_id');

        // remove the file from the fileserver
        try {
                unlink($filename);
        }
        catch (Exception $e) {
                print ("<br>Couldn't delete file: " . $filename);
                throw $e;
        }
}


function post_s3_upload_fail ( $photo_id, $filename ) {
	// update the s3_status to 2 for this photo_id
	$db = JDatabase::getInstance(dbOptions());	
	
	$fields = new stdClass();
	$fields->photo_id = $photo_id;
	$fields->s3_status = 2;
	$db->updateObject('Photo', $fields, 'photo_id');

	
}

function post_s3_upload_fail_orig ( $of_id, $filename ) {
	// update the s3_status to 2 for this of_id
	$db = JDatabase::getInstance(dbOptions());	
	
	$fields = new stdClass();
	$fields->of_id = $of_id;
	$fields->s3_status = 2;
	$db->updateObject('OriginalFiles', $fields, 'of_id');

	
}

function post_s3_upload_fail_large ( $olf_id, $filename ) {
        // update the s3_status to 2 for this of_id
        $db = JDatabase::getInstance(dbOptions());

        $fields = new stdClass();
        $fields->olf_id = $olf_id;
        $fields->s3_status = 2;
        $db->updateObject('OriginalLargeFiles', $fields, 'olf_id');


}


function post_s3_upload_actions_resource ( $resource_id, $filename, $key ) {
	
	$folder = get_mammalweb_folder();
	
	if ( $folder ) {
		$full_key = get_mammalweb_folder() . "/" . $key;
	}
	else {
		$full_key = $key;
	}
	
	// update the s3_status to 1 for this of_id
	$db = JDatabase::getInstance(dbOptions());	
	
	$fields = new stdClass();
	$fields->resource_id = $resource_id;
	$fields->s3_status = 1;
	$fields->url = s3ResourceURLFromKey($full_key);
	$db->updateObject('Resource', $fields, 'resource_id');

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


function s3Key ( $details ) {
	$options = awsOptions();
	
	$folder = get_mammalweb_folder();

	$folder_extra = "";
	
	if ( $folder ) {
		$folder_extra = $folder . "/" ;
	}
	
	$person = $details['person_id'];
	$site = $details['site_id'];
	$filename = $details['filename'];
	return $folder_extra . 'person_' . $person . '/site_' . $site . '/' . $filename;
}


function s3ReportURL ( $details ) {
	$options = awsOptions();
	$urlstem = $options['s3url'];
	
	$folder = get_mammalweb_folder();
	$folder_extra = "";
	
	if ( $folder ) {
		$folder_extra = "/" . $folder;
	}
	
	$person = $details['person_id'];
	$project = $details['project_id'];
	$filename = $details['filename'];
			
	return $urlstem . $folder_extra . '/reports/person_' . $person . '/project_' . $project . '/' . $filename;
}


function s3ResourceURLFromKey ( $key ) {
	
	$options = awsOptions();
	$urlstem = $options['s3url'];
	
	return $urlstem . '/' . $key; 
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

