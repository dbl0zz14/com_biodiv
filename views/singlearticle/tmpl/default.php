<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;


if($this->title ){
	//print "<div class='well'>\n";
	print "<h2>" . $this->title . "</h2>\n";
}
if ( $this->introtext ) {
	print "<div>".$this->introtext."</div>"; 
}


?>



