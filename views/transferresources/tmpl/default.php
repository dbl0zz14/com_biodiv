<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;


print '<h1>Transferring ' . count($this->resources) . ' files from Resource table to S3 bucket ' . get_mammalweb_bucket() . '</h1>';

if ( !$this->resources ) {
	print "<h2>No resource list - perhaps you don't have permission for this or have not logged in</h2>";
}
else {
	foreach ( $this->resources as $resource_id=>$resource_details ) {
		print ( "<br>Putting resource " . $resource_id );
		$key = "resources/person_" . $resource_details['person_id'] . "/rtype_" . $resource_details['resource_type'] . 
			"/set_" . $resource_details['set_id'] . "/" . $resource_details['filename'] ;
		print ( "<br>Key = " . $key );
		$file = $resource_details['url'];
		print ( "<br>File = " . $file );
		try {
			upload_to_s3 ($key, $file);
			print ( "<br>Resource " . $resource_id . " transferred successfully" );
			post_s3_upload_actions_resource ( $resource_id, $file, $key );
			print ( "<br>Resource " . $resource_id . " post transfer actions complete (s3_status update plus file removal)" );
		}
		catch(Exception $e) {
			print ( "br>" . $e->getMessage() );
		}
	}
}
print '<h1>Transfer complete</h1>';



?>

