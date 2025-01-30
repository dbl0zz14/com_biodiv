<?php 
if ( !$this->personId ) {
	print "<div id='no_user_id'></div>";
}
if($this->title ){
  print "<div class='well'>\n";
  print "<h2>" . $this->title . "</h2>\n";
  if ( $this->introtext ) {
	print "<div id=species-article>".$this->introtext."</div>"; 
  }
  print "</div>\n";
 }
?>