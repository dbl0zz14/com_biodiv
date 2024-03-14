<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

$document = JFactory::getDocument();
//$document->addScriptDeclaration("BioDiv.next_photo = ".$this->photoDetails['next_photo'].";");
if ( $this->photo_id ) {
  $document->addScriptDeclaration("BioDiv.curr_photo = ".$this->photo_id.";");
  //$document->addScriptDeclaration("BioDiv.latitude = ".$this->location->getAreaLat().";");
  //$document->addScriptDeclaration("BioDiv.longitude = ".$this->location->getAreaLon().";");
  $document->addScriptDeclaration("BioDiv.south = ".$this->location->getSouth().";");
  $document->addScriptDeclaration("BioDiv.west = ".$this->location->getWest().";");
  $document->addScriptDeclaration("BioDiv.north = ".$this->location->getNorth().";");
  $document->addScriptDeclaration("BioDiv.east = ".$this->location->getEast().";");
  
  $document->addScriptDeclaration("BioDiv.eggsId = ".$this->eggsId.";");
  $document->addScriptDeclaration("BioDiv.nestId = ".$this->nestId.";");
}

$document->addScriptDeclaration("BioDiv.classifyProject = ".$this->project_id.";");


if ( $this->projectPage ) {
	print '<div class="col-md-12"><a href="'.$this->projectPage.'" class="btn btn-primary">'.JText::_("COM_BIODIV_CLASSIFY_PROJECT_PAGE").'</a></div>';
}

if(!$this->photo_id){
	
  print "<h2>" . JText::_("COM_BIODIV_CLASSIFY_NO_PHO") . "</h2>\n";
  print "<h3>" . JText::_("COM_BIODIV_CLASSIFY_AVAIL") . "</h3>\n";
  print "<h3>" . JText::_("COM_BIODIV_CLASSIFY_CHECK") . "</h3>\n";
  
  return;
 }
 
 $document->addScriptDeclaration("BioDiv.maxclass = ".$this->maxClassifications.";");
	

?>


<div id='debug' style='display:none'>Debug <?php
	      print "<p>(photo_id  " . $this->photo_id .")</p>\n";
?>
</div>

<div class="container-fluid" id="photo-container">
	<div class='col-md-9 cls-xs-12'>
	
<?php
	if ( $this->isVideo === true ) {
	print "<h2>" . JText::_("COM_BIODIV_CLASSIFY_WHAT_VID") . "</h2>";
}
else if ( $this->isAudio === true ) {
	print "<h2>" . JText::_("COM_BIODIV_CLASSIFY_WHAT_HEAR") . "</h2>";
}
else {
	print "<h2>" . JText::_("COM_BIODIV_CLASSIFY_WHAT_SEE") . "</h2>";
    //print '<h5 class="bg-warning clashing add-padding-all">Look through the whole sequence before providing your classification of all animals that appear in it. Remember: you do not need to classify images individually.</h5>';
}
?>
	<div class='row'>
 
<?php
	if($this->photoDetails['person_id'] == userID()){
		print "<div class='col-xs-12 col-sm-6 col-md-6 text-info'>" . JText::_("COM_BIODIV_CLASSIFY_YOU_UP") . "</div>";
	}
	else {
		print "<div class='col-xs-12 col-sm-6 col-md-6'></div>";
	}
?>
	
     <div class='col-xs-12 col-sm-6 col-md-6'>
	<div class='btn-group pull-right' role='group'>
  <?php
  // foreach($this->rcontrols as $control_id => $control){
    // makeControlButton($control_id, $control);  
  // }
  print "<button type='button' class='btn btn-primary' id='invert_image'>".$this->invertimage."</button>";
  print "<button type='button' class='btn btn-primary' id='control_map'>".$this->showmap."</button>";
  print "<button type='button' class='btn btn-success' id='control_nextseq'>".$this->nextseq."</button>";
?>
        </div> <!-- /.btn-group -->
     </div> <!-- /.col-md-6 -->

  </div> <!-- /.row -->

  <div class='row'>
  <div class='col-md-12 photo-col'>


  <div class="row">
    <!-- div id='photo_img' class='col-md-12' -->
<div class='col-md-12' >

<!-- either do photo carousel or video here -->
<?php 

/* Should refactor this as below to use a Sequence created in the view and the MediaCarousel object but no time now...
$mediaCarousel = new MediaCarousel();
$sequence_id = $this->sequence[0]['sequence_id'];
$mediaCarousel->generateMediaCarousel($sequence_id, etc);
*/


if ( $this->isVideo === true ) {
	$photoUrl = photoURL($this->photoDetails["photo_id"]);
	$ext = strtolower(JFile::getExt($photoUrl));
	error_log("Classify View: ext = " . $ext );
	
print '<div id="videoContainer" data-photo-id="'.$this->photo_id.'"><video id="classify-video" oncontextmenu="return false;" controls controlsList="nodownload" ><source src="'.$photoUrl.'" type="video/'.$ext.'">' . JText::_("COM_BIODIV_CLASSIFY_NO_VID") . '</video></div>';
	
}
else if ( $this->isAudio === true ) {
	print '<div id="audioContainer" data-photo-id="'.$this->photo_id.'"><audio id="classify-video" oncontextmenu="return false;" controls controlsList="nodownload" ><source src="'.photoURL($this->photoDetails["photo_id"]).'" >' . JText::_("COM_BIODIV_CLASSIFY_NO_AUD") . '</video></div>';
}
else {
	

print '<div id="photoCarousel" class="carousel slide carousel-fade contain" data-ride="carousel" data-interval="false" data-wrap="false">';
  print '<!-- Indicators -->';
  print '<ol id="photo-indicators" class="carousel-indicators">';
  
  $numphotos = count($this->sequence);
  for ($i = 0; $i < $numphotos; $i++) {
	$class_extras = "";
	if ($i == 0) $class_extras = ' class="active spb" ';
	else $class_extras = ' class="spb" id = "sub-photo-'.$i.'"';
    print '<li data-target="#photoCarousel" data-slide-to="'.$i.'"'.$class_extras.'></li>';
  }
  
  print '</ol>';

  print '<button  id="fullscreen-button" type="button" class="right" ><span class="fa fa-expand fa-2x"></span></button>';
  print '<button  id="fullscreen-exit-button" type="button" class="right" ><span class="fa fa-compress fa-3x"></span></button>';
  
  print '<!-- Wrapper for slides -->';
  print '<div id="photoCarouselInner" class="carousel-inner contain">';

$numphotos = count($this->sequence);
$j = 1;
//foreach($this->sequence as $photo_id  ){
foreach($this->sequence as $photo_details  ){
	$lastclass = "";
	if ( $j == $numphotos ) $lastclass .= 'last-photo';
	if ($j==1) {
		print '<div class="item active '.$lastclass.'" data-photo-id="'.$photo_details["photo_id"].'">';
	}
	else {
		print '<div class="item '.$lastclass.'" data-photo-id="'.$photo_details["photo_id"].'">';
	}
	//print JHTML::image(photoURL($photo_details["photo_id"]), 'Photo ' . $photo_details["photo_id"], array('class' =>'img-responsive'));
	print JHTML::image(photoURL($photo_details["photo_id"]), 'Photo ' . $photo_details["photo_id"], array('class' =>'img-responsive contain'));
	print '</div>';
	$j++;
 }
 
  
  print '</div> <!-- /.carousel-inner -->';
    
  print '<!-- Left and right controls -->';
  
  if (count($this->sequence) > 1 ) {
  //print '<a class="left carousel-control" href="#photoCarousel" data-slide="prev">';
  print '<a class="left carousel-control photo-carousel-control" href="#photoCarousel" data-slide="prev">';
  print '  <span class="glyphicon glyphicon-chevron-left"></span>';
  print '  <span class="sr-only">Previous</span>';
  print '</a>';
  //print '<a class="right carousel-control" href="#photoCarousel" data-slide="next" style="background:none !important">';
  print '<a id="photo-carousel-control-right" class="right carousel-control photo-carousel-control" href="#photoCarousel" data-slide="next">';
  print '  <span class="glyphicon glyphicon-chevron-right"></span>';
  print '  <span class="sr-only">Next</span>';
  print '</a>';
  }
  
print '</div> <!-- /.photoCarousel -->';
}

?>

</div> <!-- /.col-md-12 carousel-->
</div> <!-- /.row -->

<div class='row'>
<div id='classify_tags' class='col-md-9 pull-left'>

<?php  

// Check for existing animals, ie refreshing the page
$animals = 0;
if ( $this->animal_ids ) {
	$animals = explode("_", $this->animal_ids);
}
if ( $animals ) {
	foreach ( $animals as $next_id ) {
		print "<div class='tagcontainer singletag-classification'>";
		$button_code = getClassificationButton ( $next_id, $this->animals );
		print $button_code;
		print "</div>";
	}
}

?>
</div>

<?php
  if(isFavourite($this->photo_id)){
    $favDisp = 'block';
    $nonFavDisp = 'none';
  }
  else{
    $favDisp = 'none';
    $nonFavDisp = 'block';
  }


print "<div class='pull-right col-md-3'> ";
print "<div>";
print "  <button  id='report_media' type='button' class='btn btn-warning pull-right' > ";
print "  <span class='fa fa-flag-o fa-2x'></span></button>";
print "</div>";
print "<div id='like_image_container' > ";
print "  <button  id='favourite' type='button' class='btn btn-warning pull-right'  ";
print "  style='display:".$favDisp."'><span class='fa fa-thumbs-up fa-2x'></span></button> ";
print "  <button id='not-favourite' type='button' class='btn btn-warning pull-right' ";
print "  style='display:".$nonFavDisp."'><span class='fa fa-thumbs-o-up fa-2x'></span></button> ";
print "</div>"; // like_image_container
print "</div> <!-- /.col-md-2 --> ";

?>


</div> <!-- /.row -->
</div> <!-- /.col-md-12 -->

</div> 

</div> <!-- /.col-md-9 -->


<div class='col-md-3 cls-xs-12 species-carousel-col'>
<!-- only needed if header in column div class='spacer-4em'></div -->

<!-- div id='select_species' class='col-md-3 cls-xs-12 species-carousel-col' -->
<div class="input-group">
    <span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>
    <input id="search_species" type="text" class="form-control" name="speciesfilter" placeholder="Search..">
</div>

	
<?php	
// Use tabs for the filters:
//print "<ul id = 'species-nav' class='nav nav-tabs nav-fill nav-justified'>";
print "<ul id = 'species-nav' class='nav nav-tabs nav-fill'>";
$first = true;

foreach ( $this->projectFilters as $filterId=>$filter ) {
	if ( $first == true ) {
		print "  <li class='nav-link active btn-secondary species-tab'><a data-toggle='tab' href='#filter_${filterId}'>".$filter["label"]."</a></li>";
		$first = false;
	} else {
		print "  <li class='nav-link btn-secondary species-tab'><a data-toggle='tab' href='#filter_${filterId}'>".$filter["label"]."</a></li>";
	}
}
print "</ul>";


print "<div class='tab-content no-padding'>";

$extra = "active";
foreach ( $this->projectFilters as $filterId=>$filter ) {
	print "  <div id='filter_${filterId}' class='tab-pane fade in $extra'>";
	print "<div id='carousel-species-${filterId}' class='carousel slide' data-ride='carousel' data-interval='false' data-wrap='false'>";
	//printSpeciesList ( $this->species, true );
	//printSpeciesList ( $filterId, $filter['species'], false );
	printSpeciesListSearch ( $filterId, $filter['species'], false );

	print "</div> <!-- /carousel-species carousel--> \n";
	print "  </div>";
	$extra = "";
}

print "</div>";


?>

</div>
</div>
</div>

<div id="map_modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"> <?php print JText::_("COM_BIODIV_CLASSIFY_MAP_MODAL"); ?> </h4>
      </div>
      <div class="modal-body">
	    <div id="no_map"><h5> <?php print JText::_("COM_BIODIV_CLASSIFY_NO_MAP"); ?> </h5></div>
        <div id="map_canvas" style="width:100%;height:500px;"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<div id="too_many_modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"> <?php print JText::_("COM_BIODIV_CLASSIFY_MAX_CLASS"); ?> </h4>
      </div>
      <div class="modal-body">
        <p></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<div id="timed_out_modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?php print JText::_("COM_BIODIV_CLASSIFY_CLASS_EXP"); ?> </h4>
      </div>
      <div class="modal-body">
        <p></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary classify-modal-button" data-dismiss="modal"><?php print JText::_("COM_BIODIV_CLASSIFY_CLOSE"); ?></button>
      </div>
    </div>

  </div>
</div>


<div id="report_media_modal" class="modal fade" role="dialog">
  <div class="modal-dialog "  style='width: 60%;'>

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"> <?php print JText::_("COM_BIODIV_CLASSIFY_REPORT_MEDIA"); ?> </h4>
      </div>
      <div class="modal-body">
		
		<h5> <?php print JText::_("COM_BIODIV_CLASSIFY_REPORT_MEDIA_EXPLAIN"); ?> </h5>
		
		<form id='reportMediaForm' role='form'>
		
		<input id='currSequenceId' type='hidden' name='sequence_id' value='<?php print $this->sequence_id; ?>'/>
		
		<div class="form-group">
		<label for="report_media_notes"><?php print JText::_("COM_BIODIV_CLASSIFY_REPORT_MEDIA_NOTES"); ?></label>
		<textarea class="form-control" id="report_media_notes" name="report_media_notes" rows="2" maxlength='200' ></textarea>
		</div>
		
      </div>
	  <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php print JText::_("COM_BIODIV_CLASSIFY_CANCEL"); ?></button>
		<button type='button' class='btn btn-success' data-dismiss="modal" id='report_media_save'><?php print JText::_("COM_BIODIV_CLASSIFY_SUBMIT")?></button>
	    </form>
      </div>
	  	  
    </div>

  </div>
</div>


<div class="modal fade" id="classify_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php print JText::_("COM_BIODIV_CLASSIFY_CLOSE"); ?></span></button>
    <h4 class="modal-title" id="myModalLabel"><?php print JText::_("COM_BIODIV_CLASSIFY_CLASS_ANI"); ?> </h4>
      </div>
      <div class="modal-body">
        <form id='classify-form' role='form'>
		  <div id='classify-species'>
<?php

foreach ($this->allSpecies as $stuff) {
	list($species_id, $species_name) = $stuff;
	print "<h2 id='species_header_${species_id}' class='species_header'>" . $species_name."</h2>\n";
}

print "<input type='hidden' name='species' id='species_value'/>\n";
print "<input id='currPhotoId' type='hidden' name='photo_id' value='".$this->photo_id."'/>\n";
          
?>

  </div>
  
  <div class='container-fluid'>
  
  
  
  
  
<?php

print "<div class='col-md-9'>\n";
  
print "<div id='species_helplet'></div>";

print "</div>"; // col9



print "<div class='col-md-3'>\n";

  
foreach($this->classifyInputs as $formInput){
  print "<div class='row'>\n";
  print "<div class='col-md-12'>\n";
  print "<div class='form-group species_classify'>\n";
  print $formInput;
  print "</div>\n";
  print "</div>\n";
  print "</div> <!-- /.row -->\n";
}

?>
<hr/>
<button type="button" class="btn btn-default" data-dismiss="modal"><?php print JText::_("COM_BIODIV_CLASSIFY_CLOSE"); ?></button>
        <button type="button" class="btn btn-success" id='classify-save'><?php print JText::_("COM_BIODIV_CLASSIFY_SAVE"); ?></button>

</div> <!--col3 -->




 </div> <!-- /.container-fluid -->
        </form>
      </div>
      <!--div class="modal-footer">
        Buttons were here
      </div-->
<!-- div id='species_helplet'>
</div -->


    </div>
  </div>


</div>

<?php
$mapOptions = mapOptions();
$key = $mapOptions['key'];

//JHTML::script("https://maps.googleapis.com/maps/api/js?key="); // For dev
JHTML::script("com_biodiv/bootbox.js", true, true);
JHTML::stylesheet("com_biodiv/com_biodiv.css", array(), true);
JHTML::script("com_biodiv/commonclassify.js", true, true);
JHTML::script("com_biodiv/classify.js", true, true);
JHTML::script("com_biodiv/userchoice.js", true, true);
JHTML::script("https://maps.googleapis.com/maps/api/js?key=" . $key . "&callback=initMap");

?>



