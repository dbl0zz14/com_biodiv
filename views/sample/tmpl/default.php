<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

?>
<h1>Sampling</h1>
<?php
  print("<h2>Found the following projects for sampling:</h2>");
  print_r($this->projects);

  foreach ( $this->projects as $project_id ) {
	print ("<h3>Sampling for project id " . $project_id . "</h3>" );
    sampleSequences($project_id);
  }
?>


