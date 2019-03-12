<?php 
if($this->title || $this->introtext){
  print "<div class='well'>\n";
  print "<h2>" . $this->title . "</h2>\n";
  print $this->introtext; 
  print "</div>\n";
 }
?>