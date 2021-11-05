<?php 
if ( !$this->person_id ) {
	print "<div id='no_user_id'></div>";
}
if($this->title ){
	print "<div class='well'>\n";
	print "<h2>" . $this->title . "</h2>\n";
  
	print "<div id=species-article>";
	if ( $this->introtext ) {
		print $this->introtext; 
	}
	if ( $this->sonogram ) {
		print '<div class="row audio-species-sono">';
		
		print '<div class="col-md-12" >';
		print '<video oncontextmenu="return false;" disablepictureinpicture="" controls="" controlslist="nodownload noplaybackrate" style="width: 100%"><source src="'.$this->sonogram.'" type="video/mp4">Your browser does not support the video tag</video>';
		print '</div>'; //col-12
	
	
		print '</div>'; // row
	}
	if ( $this->audioAttribution ) {
		print '<div class="image_attribution">' . $this->audioAttribution . '</div>';
	}
	print "</div>";
	print "</div>\n";
 }
?>