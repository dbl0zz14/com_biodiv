<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;


print '<h1>Transferring ' . count($this->photos) . ' files to S3 bucket ' . get_mammalweb_bucket() . '</h1>';

if ( !$this->photos ) {
	print "<h2>No photo list - perhaps you don't have permission for this or have not logged in</h2>";
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
			print ( "br>" . $e->getMessage() );
		}
	}
}
print '<h1>Transfer complete</h1>';

?>

