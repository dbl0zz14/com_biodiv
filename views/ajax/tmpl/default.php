<?php 
if($this->title || $this->introtext){
  print "<div class='well'>\n";
  print "<h2>" . $this->title . "</h2>\n";
  print "<div id=species-article>".$this->introtext."</div>"; 
  print "</div>\n";
 }
?>