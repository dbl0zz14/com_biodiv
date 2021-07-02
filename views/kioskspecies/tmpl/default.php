<?php 
if ( !$this->person_id ) {
	print "<div id='no_user_id'></div>";
}
/*
if($this->title ){
  print "<h3>" . $this->title . "</h3>\n";
  if ( $this->introtext ) {
	print "<div id=species-article>".$this->introtext."</div>"; 
  }
 }
 */
	
	//error_log ( "image = " . $this->im[0]->textContent );
	
	
	if ( $this->scientificName ) {
		print '<h3>'.$this->title.'  <i><small>('.$this->scientificName.')</small></i></h3>';
	}
	else {
		print '<h3>'.$this->title.'</h3>';
	}

 
	//print '<h3>'.$this->title.'  '.$sciText.'</h3>';
	
	// foreach ( $this->imageNodes as $imageNode ) {
		// $errStr = print_r ( $imageNode->attributes ) ;
		// print ( "imageNode: " . $errStr );
	// }
	
	if ( $this->imageSrc ) {
		print '<img style="max-height:48vh; max-width:100%; margin:0;, padding:0;" src="' . $this->imageSrc . '" />';
	}
	
	if ( $this->appearance ) {
		print '<div style="padding-left:0; padding-right:0; margin-top:15px;">' . $this->appearance . '</div>';
	}
	
	if ( $this->photoAttribution ) {
		print '<div class="image_attribution">' . $this->translations['species_image']['translation_text'] . ' ' . $this->photoAttribution . '</div>';
	}

/*
<h3>Grey squirrel <i><small>(Sciurus carolinensis)</small></i></h3>
<div class="col-md-12 text-center" style="padding-left:0; padding-right:0;">

<img style="max-height:48vh; max-width:100%; margin:0;, padding:0;" src="images/animals/Grey_squirrel.jpg" alt="" />

<!-- div class="image_attribution" style="background: lightgray; padding-left:5px; padding-right:5px; padding-bottom: 2px"><small>(C) <a href="https://www.flickr.com/photos/30107812@N05/5912290335/in/photolist-a1s2Wg-4W6Tmg-7eW1bM-2eCBEiS-s873pu-4CnDqd-Z5w6Cf-aNnsFP-dHvrKR-mUtUNz-23QcXk3-5oKTsV-6Q1fN5-r9G4sG-bwKHGa-quu4m6-cSLTrA-26zEqsw-dFDZmY-opp3BM-9TJGUT-26QnGK5-25wMFZi-cxpgpq-dvMKQ2-g4zWZP-27T5Xcb-ki4nQC-4TaSF1-5LAZ54-6JDJha-q1evXz-WSb4tG-UA1q77-HS6B5M-bMHd5i-hTZcrU-oQSgmx-q6WZHG-9ZWiS1-dMQpFa-9f8tVW-aNnz9t-26KCijR-2duREXY-2dCAepo-r7X7EV-7attju-4pK5Wj-8ToPAT">Jimmy Edmonds</a> (shared under a <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC BY-NC-ND 2.0</a> license)</small></div -->

</div>
<div class="image_attribution" style="padding-left:5px; padding-right:5px; padding-bottom: 2px;"><small>Species image (C) <a href="https://www.flickr.com/photos/30107812@N05/5912290335/in/photolist-a1s2Wg-4W6Tmg-7eW1bM-2eCBEiS-s873pu-4CnDqd-Z5w6Cf-aNnsFP-dHvrKR-mUtUNz-23QcXk3-5oKTsV-6Q1fN5-r9G4sG-bwKHGa-quu4m6-cSLTrA-26zEqsw-dFDZmY-opp3BM-9TJGUT-26QnGK5-25wMFZi-cxpgpq-dvMKQ2-g4zWZP-27T5Xcb-ki4nQC-4TaSF1-5LAZ54-6JDJha-q1evXz-WSb4tG-UA1q77-HS6B5M-bMHd5i-hTZcrU-oQSgmx-q6WZHG-9ZWiS1-dMQpFa-9f8tVW-aNnz9t-26KCijR-2duREXY-2dCAepo-r7X7EV-7attju-4pK5Wj-8ToPAT">Jimmy Edmonds</a> (shared under a <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC BY-NC-ND 2.0</a> license)</small></div>

<div  class="col-md-12"  style="padding-left:0; padding-right:0; margin-top:15px;">Larger than the red squirrel, greys are coloured as their name suggests. White individuals are occasionally reported.</div>
*/
?>