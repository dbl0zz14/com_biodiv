<?php 
if ( !$this->person_id ) {
	print "<div id='no_user_id'></div>";
}
	
	
	if ( $this->scientificName ) {
		print '<h3>'.$this->title.'  <i><small>('.$this->scientificName.')</small></i></h3>';
	}
	else {
		print '<h3>'.$this->title.'</h3>';
	}
	
	print '<div class="row half_spaced_row">';
	if ( $this->imageSrc ) {
		print '<div class="col-md-6 "><img style="max-height:48vh; max-width:100%; margin:0;, padding:0;" src="' . $this->imageSrc . '" /></div>';
	}
	
	print '<div class="col-md-6" style="padding-left:0">';
	
	if ( $this->song ) {
		print '<strong>'.JText::_("COM_BIODIV_KIOSKSPECIESAUDIO_SONG").'</strong> '.$this->song;
	}
	
	print '</div>'; // col-6
	
	print '</div>'; // row
	
	if ( $this->sonogram ) {
		print '<div class="row">';
		
		print '<div class="col-md-12" >';
		print '<video oncontextmenu="return false;" disablepictureinpicture="" controls="" controlslist="nodownload noplaybackrate" style="width: 100%"><source src="'.$this->sonogram.'" type="video/mp4">Your browser does not support the video tag</video>';
		print '</div>'; //col-12
		
		
		print '</div>'; // row
	}
	else if ( $this->iframe ) {
		print '<div class="row">';
		
		print '<div class="col-md-12" >';
		print '<div id="kioskIframe" style=" width: 100%; height: 30vh; position: relative;">';
		
		print '<div id="framearea" style="  width: 100%; height: 100%; position: absolute; top: 0; left: 0; z-index: 1;">';
		print '<iframe src="'.$this->iframe.'" width="100%" height="220" frameborder="0" scrolling="no"></iframe>';    
		print '</div>';
		
		print '<div id="framecover" style="  width: 100%; height: 30%; background: transparent; position: absolute; top: 0; left: 0; z-index: 10;">';
		print '</div>';
		
		print '</div>'; //kioskIframe
		print '</div>'; //col-12
		
		
		print '</div>'; // row
	}
	
	$attributionStr = "";
	if ( $this->photoAttribution ) {
		$attributionStr =  JText::_("COM_BIODIV_KIOSKSPECIESAUDIO_SPECIES_IMAGE") . ' ' . $this->photoAttribution;
	}
	if ( $this->audioAttribution ) {
		$attributionStr .=  ' ' . $this->audioAttribution;
	}
	print '<div class="image_attribution">' . $attributionStr . '</div>';
	


?>