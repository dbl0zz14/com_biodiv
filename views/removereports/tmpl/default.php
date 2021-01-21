<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

?>
<h1>Removing reports</h1>
<?php

	//print_r ( $this->reportPeople );

	foreach ( $this->reportPeople as $person ) {
		
		print ( "Removing reports for person id " . $person );
		
		BiodivReport::removeExistingReports ( $person );
	}


?>


