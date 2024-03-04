<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;


print '<h1>Transferring ' . count($this->photos) . ' files from Photo table to S3 bucket ' . get_mammalweb_bucket() . '</h1>';

if ( !$this->photos ) {
	print "<h2>No photo list - perhaps all have been transferred, you don't have permission for this or have not logged in</h2>";
}
else {
	foreach ( $this->photos as $photo_id=>$photo_details ) {
		print ( "<br>Putting photo " . $photo_id );
		$key = "person_" . $photo_details['person_id'] . "/site_" . $photo_details['site_id'] . "/" . $photo_details['filename'] ;
		print ( "<br>Key = " . $key );
		$file = $photo_details['dirname'] . "/" . $photo_details['filename'];
		print ( "<br>File = " . $file );
		try {
			upload_to_s3 ($key, $file);
			print ( "<br>Photo " . $photo_id . " transferred successfully" );
			post_s3_upload_actions ( $photo_id, $file );
			print ( "<br>Photo " . $photo_id . " post transfer actions complete (s3_status update plus file removal)" );
		}
		catch(Exception $e) {
			print ( "<br>" . $e->getMessage() );
			post_s3_upload_fail ( $photo_id, $file );
		}
	}
}
print '<h1>Transfer complete</h1>';

print '<h1>Transferring ' . count($this->originalFiles) . ' files from OriginalFiles table to S3 bucket ' . get_mammalweb_bucket() . '</h1>';

if ( !$this->originalFiles ) {
	print "<h2>No original files list - perhaps all have been transferred, you don't have permission for this or have not logged in</h2>";
}
else {
	foreach ( $this->originalFiles as $of_id=>$of_details ) {
		print ( "<br>Putting original file " . $of_id );
		$key = "person_" . $of_details['person_id'] . "/site_" . $of_details['site_id'] . "/" . $of_details['filename'] ;
		print ( "<br>Key = " . $key );
		$file = $of_details['dirname'] . "/" . $of_details['filename'];
		print ( "<br>File = " . $file );
		try {
			upload_to_s3 ($key, $file);
			print ( "<br>Original file " . $of_id . " transferred successfully" );
			post_s3_upload_actions_orig ( $of_id, $file );
			print ( "<br>Original file " . $of_id . " post transfer actions complete (s3_status update plus file removal)" );
		}
		catch(Exception $e) {
			print ( "<br>" . $e->getMessage() );
			post_s3_upload_fail_orig ( $of_id, $file );
			
		}
	}
}
print '<h1>Transfer of OriginalFiles complete</h1>';

?>

