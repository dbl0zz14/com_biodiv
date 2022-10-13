<?php 

if ( !$this->personId ) {
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.$this->translations['login']['translation_text'].'</div>';
}
if($this->title ){
	//print "<div class='well'>\n";
	print "<h2>" . $this->title . "</h2>\n";
	if ( $this->introtext ) {
		print "<div>".$this->introtext."</div>"; 
	}
	//print "</div>\n";
 }
 
?>