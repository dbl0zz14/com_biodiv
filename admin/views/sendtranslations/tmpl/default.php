<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

printAdminMenu("TRANSLATIONS");
	
print '<div id="j-main-container" class="span10 j-toggle-main">';


print '<h2>Translations sent</h2>';


foreach ( $this->responses as $response ) {
	print '<div>';
	print_r ( $response );
	print '</div>';
}

//print '<a href="'.$this->reportURL.'"><button type="button" class="btn js-stools-btn-clear" title="Download report" >Download here</button></a>';

print '</div>';


?>


