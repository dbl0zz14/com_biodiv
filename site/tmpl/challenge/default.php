<?php

 
// No direct access to this file
defined('_JEXEC') or die;


$nothingDisabled = false;
if ( !$this->personId ) {
	print "<div id='no_user_id'></div>";
}
else {
  print "<div><h5>" . $this->text . "</h5></div>";
 }
 
?> 