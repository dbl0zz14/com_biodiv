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


