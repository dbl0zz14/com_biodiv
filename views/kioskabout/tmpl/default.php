<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

// Display the first article at the top then others as col-md-4s.
// First version expects one intro article plus 3 cards to guide the user.

print '<div class="col-md-12 spaced_row">';

print '<h1 class="text-center">'.$this->translations['heading']['translation_text'].'</h1>';

$maxArticles = 6;
$articleColClass = "col-md-4";

if ( count( $this->articles ) < 5 ) {
	$maxArticles = 4;
	$articleColClass = "col-md-6";
}


for ($i=0; $i<$maxArticles; $i++) {
	$r = $this->articles[$i];
	print '<div class="'.$articleColClass.'">';
	print $r->introtext;
	print '</div>';
} 

print '</div>'; // col-12




?>


