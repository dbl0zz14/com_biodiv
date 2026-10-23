<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

$this->model->printAdminMenu("TRANSLATIONS");
	
print '<div id="j-main-container" class="span10 j-toggle-main">';


print '<h2>'.$this->title.'</h2>';

print '<h4>Once downloaded, open a new file in Excel and load using the "Get data from text" function, ensuring file origin is set to utf8 and delimiter is ",".  Then save locally and upload to Transifex manually</h4>';

print '<a href="'.$this->reportURL.'"><button type="button" class="btn btn-primary" title="Download report" >Download here</button></a>';

print '</div>';


?>


