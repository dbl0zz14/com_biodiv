<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;


print '<h1>Running tests</h1>';

if ( $this->personId ) {
	print '<h2>Running tests requiring logged in user</h2>';
	

	// -----------------------SPOTTING PROJECTS-------------------------
	
	print '<h2>Testing getAccessProjectsWithSubs SPOT</h2>';

	$spotProjects = getAccessProjectsWithSubs ( 'SPOT' );

	foreach ( $spotProjects as $projectId=>$row ) {
		print ("<h3>Spot projects for project id " . $projectId . ":</h3>" );
		foreach ($row as $pid=>$name) {
			print ("<p>" . $name . " (" . $pid . ")</p>" );
		}
		//print_r ( $row );
	}
	//print_r ( $spotProjects );

	$spotProjects = getProjects ( 'SPOT' );
	print ("<h3>All spot projects:</h3>" );
	
	foreach ( $spotProjects as $pid=>$name ) {
		print ("<p>" . $name . " (" . $pid . ")</p>" );
	}
	
	// Compare with mySpottingProjects:
	$mySP = mySpottingProjects();
	print ("<h3>All mySPottingProjects:</h3>" );
	
	foreach ( $mySP as $pid=>$name ) {
		print ("<p>" . $name . " (" . $pid . ")</p>" );
	}
	
	print ( "<p>Number of old spotting projects: " . count($mySP ) . "</p>" );
	print ( "<p>Number of new spotting projects: " . count($spotProjects ) . "</p>" );
	
	$spotDiff = array_diff_key($spotProjects, $mySP );
	print ("<h3>Spot projects diffs in new list but not old:</h3>" );
	print_r ( $spotDiff );
	
	$spotDiff = array_diff_key($mySP, $spotProjects);
	print ("<h3>Spot projects diffs in old list but not new:</h3>" );
	print_r ( $spotDiff );
	

	$spotProjects = getProjects ( 'SPOT', false, 3 );
	print ("<h3>All spot projects for project 3:</h3>" );
	foreach ( $spotProjects as $pid=>$name ) {
		print ("<p>" . $name . " (" . $pid . ")</p>" );
	}
	
	$spotProjects = getProjects ( 'SPOT', false, 24 );
	print ("<h3>All spot projects for project 24:</h3>" );
	foreach ( $spotProjects as $pid=>$name ) {
		print ("<p>" . $name . " (" . $pid . ")</p>" );
	}
	
	$spotProjects = getProjects ( 'SPOT', false, 16 );
	print ("<h3>All spot projects for project 16:</h3>" );
	foreach ( $spotProjects as $pid=>$name ) {
		print ("<p>" . $name . " (" . $pid . ")</p>" );
	}
	
	


	// -----------------------SPOTTING PROJECTS, REDUCED-------------------------
	
	print '<h2>Testing getAccessProjectsWithSubs SPOT, reduce = true </h2>';

	$spotProjects = getProjects ( 'SPOT', true );
	print ("<h3>All reduced spot projects:</h3>" );
	
	foreach ( $spotProjects as $pid=>$name ) {
		print ("<p>" . $name . " (" . $pid . ")</p>" );
	}
	
	// Compare with mySpottingProjects:
	$mySP = mySpottingProjects( true );
	print ("<h3>All reduced mySPottingProjects:</h3>" );
	
	foreach ( $mySP as $pid=>$name ) {
		print ("<p>" . $name . " (" . $pid . ")</p>" );
	}
	
	print ( "<p>Number of old reduced spotting projects: " . count($mySP ) . "</p>" );
	print ( "<p>Number of new reduced spotting projects: " . count($spotProjects ) . "</p>" );
	
	$spotDiff = array_diff_key($spotProjects, $mySP );
	print ("<h3>Reduced spot projects diffs in new list but not old:</h3>" );
	print_r ( $spotDiff );
	
	$spotDiff = array_diff_key($mySP, $spotProjects);
	print ("<h3>Reduced spot projects diffs in old list but not new:</h3>" );
	print_r ( $spotDiff );


	// -----------------------TRAPPING PROJECTS-------------------------
	
	print '<h2>Testing getAccessProjectsWithSubs TRAP</h2>';

	$trapProjects = getAccessProjectsWithSubs ( 'TRAP' );

	print_r ( $trapProjects );

	$trapProjects = getProjects ( 'TRAP' );
	print ("<h3>All trap projects:</h3>" );
	
	foreach ( $trapProjects as $pid=>$name ) {
		print ("<p>" . $name . " (" . $pid . ")</p>" );
	}
	
	// Compare with mySpottingProjects:
	$myTR = myTrappingProjects();
	print ("<h3>All myTrappingProjects:</h3>" );
	
	foreach ( $myTR as $pid=>$name ) {
		print ("<p>" . $name . " (" . $pid . ")</p>" );
	}
	
	print ( "<p>Number of old trapping projects: " . count($myTR ) . "</p>" );
	print ( "<p>Number of new trapping projects: " . count($trapProjects ) . "</p>" );
	
	$spotDiff = array_diff_key($trapProjects, $myTR );
	print ("<h3>Trap projects diffs in new list but not old:</h3>" );
	print_r ( $spotDiff );
	
	$spotDiff = array_diff_key($myTR, $trapProjects);
	print ("<h3>Trap projects diffs in old list but not new:</h3>" );
	print_r ( $spotDiff );
	
	
	// -----------------------LIST PROJECTS-------------------------
	
	print '<h2>Testing getAccessProjectsWithSubs LIST</h2>';

	$listProjects = getAccessProjectsWithSubs ( 'LIST' );

	print_r ( $listProjects );


	// -----------------------ADMIN PROJECTS-------------------------
	
	print '<h2>Testing getAccessProjectsWithSubs ADMIN</h2>';

	$adminProjects = getAccessProjectsWithSubs ( 'ADMIN' );

	print_r ( $adminProjects );
	
	$adminProjects = getProjects ( 'ADMIN' );
	print ("<h3>All admin projects:</h3>" );
	
	foreach ( $adminProjects as $pid=>$name ) {
		print ("<p>" . $name . " (" . $pid . ")</p>" );
	}
	
	// Compare with mySpottingProjects:
	$myAD = myAdminProjects();
	print ("<h3>All myAdminProjects:</h3>" );
	
	foreach ( $myAD as $row ) {
		print ("<p>" . $row['project_name'] . " (" . $row['project_id'] . ")</p>" );
	}
	
	
	print ( "<p>Number of old admin projects: " . count($myAD ) . "</p>");
	print ( "<p>Number of new admin projects: " . count($adminProjects ) . "</p>" );
	
	//$spotDiff = array_diff_key($adminProjects, $myAD );
	//print ("<h3>Admin projects diffs in new list but not old:</h3>" );
	//print_r ( $spotDiff );
	
	//$spotDiff = array_diff_key($myAD, $adminProjects);
	//print ("<h3>Admin projects diffs in old list but not new:</h3>" );
	//print_r ( $spotDiff );
	
	$adminProjects = getProjects ( 'ADMIN', false, 3 );
	print ("<h3>Admin projects for project 3:</h3>" );
	
	foreach ( $adminProjects as $pid=>$name ) {
		print ("<p>" . $name . " (" . $pid . ")</p>" );
	}
	
	$adminProjects = getProjects ( 'ADMIN', false, 4 );
	print ("<h3>Admin projects for project 4:</h3>" );
	
	foreach ( $adminProjects as $pid=>$name ) {
		print ("<p>" . $name . " (" . $pid . ")</p>" );
	}
	
}
else {
	print '<h2>User not logged in, no tests here yet</h2>';
}


?>

