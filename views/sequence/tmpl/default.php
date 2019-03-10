<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

?>
<h1>Sequencing</h1>
<?php
print_r($this->uploadDetails);
$lastOne = "None uploaded";
if ( sizeof($this->uploadDetails) > 0 ) {
	$lastOne = $this->uploadDetails[sizeof($this->uploadDetails)-1]['upload_id'];
}

print "last one $lastOne";
sequencePhotos($lastOne);
?>


