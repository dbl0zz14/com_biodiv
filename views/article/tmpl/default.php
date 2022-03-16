<?php 

if ( !$this->personId ) {
	print '<a type="button" href="'.JURI::root().'/'.$this->translations['page']['translation_text'].'" class="list-group-item btn btn-block" >'.$this->translations['login']['translation_text'].'</a>';
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