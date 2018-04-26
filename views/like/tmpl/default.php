<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;
if($this->likes > 0){
    $favDisp = 'block';
    $nonFavDisp = 'none';
 }
 else{
	$favDisp = 'none';
    $nonFavDisp = 'block';
 }
 print "<button  id='favourite' type='button' class='btn btn-warning pull-right'\n";
 print "style='display:$favDisp'";
 print "><span class='fa fa-thumbs-up fa-2x'></span></button>\n";
 print "<button id='not-favourite' type='button' class='btn btn-warning pull-right'\n";
 print "style='display:$nonFavDisp'";
 print "><span class='fa fa-thumbs-o-up fa-2x'></span></button>\n";
 
?>