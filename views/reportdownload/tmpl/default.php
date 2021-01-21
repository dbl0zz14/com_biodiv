<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;
//print $this->data ;

//header("Content-type: text/csv");
//header("Content-disposition: attachment; filename = report.csv");
		
//error_log ( "About to call readFile" );
		
//readfile(reportRoot().'/report.csv');

//print ( '<a id="report_link" href="http://localhost/rhombus/biodivimages/reports/report.csv">Click to download</a>' );


//print "http://localhost/rhombus/biodivimages/reports/report.csv";

//error_log ( "ReportDownload - returning report URL: " . $this->reportURL );
//print $this->reportURL;

// Some test data:
$myObj = new stdClass();
$myObj->filename = $this->reportName;
$myObj->headings = array_values($this->headings);
$myObj->data = $this->data;

$myJSON = json_encode($myObj);

print $myJSON;

?>