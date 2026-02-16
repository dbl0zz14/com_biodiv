<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

?>
<h1>Calculating Stats</h1>
<?php
if ($this->calcLeagueTable == 1 ) {
	print "Calculating league table <br>";
	calculateLeagueTable();
}
else if ($this->calcLeagueMonth == 1 and $this->calcMonths > 0) {
	if ( $this->allProjects ) {
		print "Calculating league table by month history for all projects<br>";
		calculateLeagueTableByMonthHistory ( null, $this->calcMonths );
	}
	else {
		print "Calculating league table by month history for project " . $this->projectId . "<br>";
		calculateLeagueTableByMonthHistory ( $this->projectId, $this->calcMonths );
	}
}
else if ($this->calcLeagueMonth == 1 ) {
	if ( $this->allProjects ) {
		print "Calculating league table by month<br>";
		calculateLeagueTableByMonth(null);
	}
	else {
		print "Calculating league table by month<br>";
		calculateLeagueTableByMonth($this->projectId);
	}
}
else if ($this->calcAnimals == 1 ) {
	print "Calculating animal statistics <br>";
	calculateAnimalStatistics();
}
else if ($this->calcSiteStats == 1 ) {
	print "Calculating site statistics <br>";
	calculateSiteStats();
}
else if ($this->calcSiteHistory == 1 ) {
	print "Calculating site statistics history<br>";
	calculateSiteStatsHistory();
}
else if ($this->calcSiteAnimals == 1 ) {
	print "Calculating site animal statistics <br>";
	calculateSiteAnimalStatistics();
}
else if ($this->calcExpertise == 1 ) {
	print "Calculating user expertise <br>";
	calculateUserExpertise();
}
else if ($this->calcTests == 1 and $this->calcMonths > 0) {
	print "Calculating league table by month history<br>";
	calculateUserTestStatisticsHistory ( $this->calcMonths );
}
else if ($this->calcTests == 1 ) {
	print "Calculating user test totals <br>";
	calculateUserTestStatistics();
}
else if ($this->calcTotals == 1) {
	print "Calculating totals.<br>";
	calculateStatsTotals();
}
else if ($this->calcAll == 1 and $this->calcMonths > 0) {
	print "Calculating history for $this->calcMonths months. <br>";
	calculateStatsHistory($this->projectId, $this->calcMonths);
}
else if ($this->calcAll == 1) {
	print "Calculating history. Project = $this->projectId.<br>";
	calculateStatsHistory($this->projectId);
}
else {
	print "Calculating stats , date = $this->calcDate. <br>";
	calculateStats($this->projectId, $this->calcDate);
}
?>


