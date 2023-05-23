<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;


print "<h1>Creating NBN file</h1>";

print '<h1>Transferring NBN file from to S3 bucket ' . get_mammalweb_bucket() . '</h1>';

if ( !$this->newCsvFile ) {
	print "<h2>No file to transfer</h2>";
}
else {
		
	$key = "NBN/" . $this->filename ;
	print ( "<br>Key = " . $key );
	$file = $this->newCsvFile;
	print ( "<br>File = " . $file );
	try {
		upload_to_s3 ($key, $file);
		print ( "<br>NBN file transferred successfully, removing original" );
		unlink($file);
	}
	catch(Exception $e) {
		print ( "br>" . $e->getMessage() );
	}
	
}
print '<h1>Transfer complete</h1>';



print "<h1>End of NBN script</h1>";

?>


