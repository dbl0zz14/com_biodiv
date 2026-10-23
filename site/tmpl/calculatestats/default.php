<?php

use MammalWeb\Component\Biodiv\Site\Library\Core\Biodiv;
 
// No direct access to this file
defined('_JEXEC') or die;

$statsModel = $this->getModel();


print "<h1>Calculating Stats</h1>";

if ($this->calcLeagueTable == 1 ) {
	print "Calculating league table <br>";
	$statsModel->calculateLeagueTable();
}
else if ($this->calcLeagueMonth == 1 and $this->calcMonths > 0) {
	if ( $this->allProjects ) {
		print "Calculating league table by month history for all projects<br>";
		$statsModel->calculateLeagueTableByMonthHistory ( null, $this->calcMonths );
	}
	else {
		print "Calculating league table by month history for project " . $this->projectId . "<br>";
		$statsModel->calculateLeagueTableByMonthHistory ( $this->projectId, $this->calcMonths );
	}
}
else if ($this->calcLeagueMonth == 1 ) {
	if ( $this->allProjects ) {
		print "Calculating league table by month<br>";
		$statsModel->calculateLeagueTableByMonth(null);
	}
	else {
		print "Calculating league table by month<br>";
		$statsModel->calculateLeagueTableByMonth($this->projectId);
	}
}
else if ($this->calcAnimals == 1 ) {
	print "Calculating animal statistics <br>";
	$statsModel->calculateAnimalStatistics();
}
else if ($this->calcSiteStats == 1 ) {
	print "Calculating site statistics <br>";
	$statsModel->calculateSiteStats();
}
else if ($this->calcSiteHistory == 1 ) {
	print "Calculating site statistics history<br>";
	$statsModel->calculateSiteStatsHistory();
}
else if ($this->calcSiteAnimals == 1 ) {
	print "Calculating site animal statistics <br>";
	$statsModel->calculateSiteAnimalStatistics();
}
else if ($this->calcExpertise == 1 ) {
	print "Calculating user expertise <br>";
	$statsModel->calculateUserExpertise();
}
else if ($this->calcTests == 1 and $this->calcMonths > 0) {
	print "Calculating league table by month history<br>";
	$statsModel->calculateUserTestStatisticsHistory ( $this->calcMonths );
}
else if ($this->calcTests == 1 ) {
	print "Calculating user test totals <br>";
	$statsModel->calculateUserTestStatistics();
}
else if ($this->calcTotals == 1) {
	print "Calculating totals.<br>";
	$statsModel->calculateStatsTotals( $this->calcDate );
}
else if ($this->calcAll == 1 and $this->calcMonths > 0) {
	print "Calculating history for $this->calcMonths months. <br>";
	$statsModel->calculateStatsHistory($this->projectId, $this->calcMonths);
}
else if ($this->calcAll == 1) {
	print "Calculating history. Project = $this->projectId.<br>";
	$statsModel->calculateStatsHistory($this->projectId);
}
else {
	print "Calculating stats , date = $this->calcDate. <br>";
	$statsModel->calculateStats($this->projectId, $this->calcDate);
}
?>


