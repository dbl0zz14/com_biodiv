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

 
	if ( $this->imageSrc ) {
		//print '<img style="max-height:48vh; max-width:100%; margin:0;, padding:0;" src="' . $this->imageSrc . '" />';
		print '<img src="' . $this->imageSrc . '" class="img-responsive"/>';
	}
	
	if ( $this->appearance ) {
		print '<div style="padding-left:0; padding-right:0; margin-top:15px;">' . $this->appearance . '</div>';
	}
	
	if ( $this->photoAttribution ) {
		print '<div class="image_attribution">' . JText::_("COM_BIODIV_KIOSKSPECIES_SPECIES_IMAGE") . ' ' . $this->photoAttribution . '</div>';
	}


?>