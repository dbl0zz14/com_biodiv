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
}
if ( $this->toggled ) {
	print '<div id="wrapper" class="toggled">';
}
else {
	print '<div id="wrapper">';
}

print '<div id="sidebar-wrapper">';
print '    <div class="classify-header">';
print '        <h1>' . $this->my_project . '</h1>';
print '    </div> <!-- classify-header -->';
print "    <div class='project-sidebar-image'><img src='".$this->projectImageUrl."' /></div>";
print "    <canvas id='animalsBarChartKiosk' class='animals-bar' data-project-id='".$this->project_id."' height='250px' ></canvas>";
print ' <div class="mwlogos">';
//print ' <div class="logo-image"><img src="images/logos/Hancock.png"></div>';
//print ' <div class="logo-image"><img src="images/logos/MammalWebSquareBlackExt2.png"></div>';
//print ' <div class="logo-image"><img src="images/logos/dulogo.png"></div>';
//print ' <div class="logo-image"><img src="images/logos/esrc-logo.jpg"></div>';
print '</div>';
//print '   <ul class="sidebar-nav">';
//print '       <li class="sidebar-brand">';
//print '           <h1>' . $this->my_project . '</h1>';
//print '       </li>';
//print '       <li class = "sidebar-brand">';
//print "           <canvas id='animalsBarChart' class='animals-bar' data-project-id='".$this->project_id."' height='300px' ></canvas>";
//print '       </li>';
//print '   </ul>';

//print '<div href="#menu-toggle" id="menu-toggle" class="btn slide-out-tab">';
//print "" . $this->my_project . " Project Details";
//print '</div>';
print '</div>';
print '<!-- /#sidebar-wrapper -->';
print '<div href="#menu-toggle" id="menu-toggle" class="btn slide-out-tab">';
print "What has been spotted in " . $this->my_project . "?";
print '</div>';

print '<div id="page-content-wrapper">';
//print "<div class='row'>";

//print "<div class='col-md-1 no-padding well-background'>";
//print '<div class="classify-header">';
//print '<h1>' . $this->my_project . '</h1>';
//print '</div> <!-- classify-header -->';
//print "<div class='spacer-2em'>";
//print "</div>";

//<div class='crop-width'><img class='project-col-image cover scale2' alt = 'project image' src='".$url."' /></div>
//print "<img class='logo-project-image' src='".$this->projectImageUrl."' />";
//print "<canvas id='animalsBarChart' class='animals-bar' data-project-id='".$this->project_id."' height='300px' ></canvas>";
//print "<div class='spacer-2em'></div>";
?>
<!-- div class="logos">
<img src="images/logos/MammalWebSquareBlackExt2.png">
<div class="spacer-2em"></div>
<img src="images/logos/dulogo.png">
<div class="spacer-2em"></div>
<img src="images/logos/esrc-logo.jpg">
</div --> <!-- /div logos -->
<!-- /div --> <!-- col-md-1 logos -->
<?php
//print "<div class='col-md-11'>";

if(!$this->photo_id){
  print "<h2>No photos for you to classify</h2>\n";
  print "<h3>You have classified all the images currently available for this project</h3>\n";
  print "<h3>If you have recently uploaded some images, please check back in 10 minutes, by which time they will be available for classification</h3>\n";
  return;
 }
/*
function makeControlButton($control_id, $control){
  $disabled = strpos($control, "disabled");
  if($disabled !== false){
    $extras = array('disabled');
  }
  else{
    $extras = array('classify_control');
  }

  $confirm = strpos($control, "biodiv-confirm");

  if($confirm !== false){
    $extras[] = "biodiv-confirm";
  }

  $extraText = implode(" ", $extras);
  print "<button type='button' class='btn btn-warning btn-block $extraText' id='$control_id'>$control</button>";
}
*/

?>


<div id='debug' style='display:none'>Debug <?php
	      print "<p>(photo_id  " . $this->photo_id .")</p>\n";
?>
</div>

<div class="container-fluid" id="photo-container">
	<div class='col-md-8 cls-xs-12 kiosk-photos'>
	<!-- div href="#menu-toggle" id="menu-toggle" class="btn slide-out-tab" -->
	<!-- ?php print "" . $this->my_project . " Project Details"; ? -->
	<!-- a href="#menu-toggle" class="btn btn-default" id="menu-toggle">Project Details</a -->
	<!-- /div -->

	<!-- row containing buttons was here -->
	<h2>What do you see in this sequence?</h2>
	
	<h3>Look through all the images then choose from the list.</h3>
	
	<!-- div class='spacer-2em'></div -->	

  <div class='row'>
  <div class='col-md-12 photo-col'>


  <div class="row">
    <!-- div id='photo_img' class='col-md-12' -->
<div class='col-md-12' >

<!-- either do photo carousel or video here -->
<?php 

if ( $this->isVideo === true ) {
	print '<div><video id="classify-video" controls onended="BioDiv.videoEnded()"><source src="'.photoURL($this->photoDetails["photo_id"]).'" type="video/mp4">Your browser does not support the video tag.</video></div>';
}
else {
	

print '<div id="photoCarousel" class="carousel slide carousel-fade" data-ride="carousel" data-interval="false" data-wrap="false">';
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
  print '<div id="photoCarouselInner" class="carousel-inner">';

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
	print JHTML::image(photoURL($photo_details["photo_id"]), 'Photo ' . $photo_details["photo_id"], array('class' =>'img-responsive'));
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
<div class='col-md-9 pull-left'>

<!-- div -->

  
<div id='classify_tags'>
<div id='first_classification' class='singletag-classification'></div>
<div id='second_classification' class='singletag-classification'></div>
<div id='third_classification' class='singletag-classification'></div>
</div>
<!-- /div -->
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

?>
<!-- this etc div id='like_image_container' class='pull-right col-md-6' -->
<div class='col-md-3'>  
<?php
print "<div class='spacer-1em'></div>";
print "<button type='button' class='pull-right btn btn-danger' id='control_nextseq'>".$this->nextseq."</button>";
?>
</div> <!-- /.col-md-6 -->

</div> <!-- /.row -->

<div class="row logo-row">
<img src="images/logos/Hancock.png">
<img src="images/logos/MammalWebSquareBlackExt2.png">
<img src="images/logos/dulogo.png">
<img src="images/logos/esrc-logo.jpg">
</div> <!-- /div logo-row -->
<div class='row'>
<!-- div class='col-md-12' classify_explain>
<h3>Choose a species from the buttons on the right.</h3>
<h3>  Use the tabs to filter the list.</h3>
</div -->
</div>

</div> <!-- /.col-md-12 -->

</div> 

</div> <!-- /.col-md-9 -->


<div class='col-md-4 cls-xs-12 species-carousel-col'>
<!-- div class='spacer-3em'></div -->

<?php	
// Use tabs for the filters:
//print "<ul id = 'species-nav' class='nav nav-tabs nav-fill nav-justified'>";
print "<div class='row'>";
print "<div class='spacer-1em'></div>";
print "<button type='button' class='pull-right btn btn-danger classify-help'>Help</button>";
print "<div class='spacer-3em'></div>";
print "</div>";
print "<ul id = 'kiosk-species-nav' class='nav nav-tabs'>";
$first = true;
$numProjectFilters = count($this->projectFilters);

if ( $this->commonSpeciesFilter ) {
	print "  <li class='nav-link active btn-default species-tab'><a data-toggle='tab' href='#filter_".$this->commonFilterId."'>".$this->commonSpeciesFilter["label"]."</a></li>";
	$first = false;
}

foreach ( $this->filters as $filterId=>$filter ) {
	if ( $first == true ) {
		print "  <li class='nav-link active btn-default species-tab'><a data-toggle='tab' href='#filter_${filterId}'>".$filter["label"]."</a></li>";
		$first = false;
	} else {
		print "  <li class='nav-link btn-default species-tab'><a data-toggle='tab' href='#filter_${filterId}'>".$filter["label"]."</a></li>";
	}
}
foreach ( $this->projectFilters as $filterId=>$filter ) {
		print "  <li class='nav-link btn-default species-tab'><a data-toggle='tab' href='#filter_${filterId}'>".$filter["label"]."</a></li>";
}
print "</ul>";


print "<div class='tab-content no-padding'>";

$extra = "active";

if ( $this->commonSpeciesFilter ) {
	print "  <div id='filter_".$this->commonFilterId."' class='tab-pane fade in $extra'>";
	print "<div id='carousel-species-".$this->commonFilterId."' class='carousel slide' data-ride='carousel' data-interval='false' data-wrap='false'>";
	printSpeciesList ( $this->commonFilterId, $this->commonSpeciesFilter['species'], false, true, true, $this->lcontrols );
	print "</div> <!-- /carousel-species carousel--> \n";
	print "  </div>";
	$extra = "";
}
foreach ( $this->filters as $filterId=>$filter ) {
	print "  <div id='filter_${filterId}' class='tab-pane fade in $extra'>";
	print "<div id='carousel-species-${filterId}' class='carousel slide' data-ride='carousel' data-interval='false' data-wrap='false'>";
	//printSpeciesList ( $this->species, true );
	$isCommon = $filter['label'] == 'Common' or $filter['label'] == 'Common Species' ;
	printSpeciesList ( $filterId, $filter['species'], $isCommon, true, true, $this->lcontrols );
	print "</div> <!-- /carousel-species carousel--> \n";
	print "  </div>";
	$extra = "";
}
foreach ( $this->projectFilters as $filterId=>$filter ) {
	print "  <div id='filter_${filterId}' class='tab-pane fade in $extra'>";
	print "<div id='carousel-species-${filterId}' class='carousel slide' data-ride='carousel' data-interval='false' data-wrap='false'>";
	//printSpeciesList ( $this->species, true );
	printSpeciesList ( $filterId, $filter['species'], false, true, true, $this->lcontrols );
	print "</div> <!-- /carousel-species carousel--> \n";
	print "  </div>";
	$extra = "";
}
/* moving to biodiv.php
print "<div class='row species-row' id='nothing-human-row'>";		
foreach($this->lcontrols as $control_id => $control){
	print "<div class='col-md-6 species-carousel-col'>";
    makeControlButton($control_id, $control);
	print "</div>";
}
print "</div> <!-- /species-row -->\n";
*/


print "</div>";

print "</div>";

print "</div>";

//print "</div> <!-- col-md-11 -->";

//print "</div> <!-- outer row -->";

print '</div>';
print '<!-- /#page-content-wrapper -->';

print '</div><!-- wrapper -->';
?>

<!-- div class="row logo-row">
<img src="images/logos/MammalWebSquareBlackExt2.png">
<img src="images/logos/dulogo.png">
<img src="images/logos/esrc-logo.jpg">
</div --> <!-- /div logo-row -->

<div id="too_many_modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Sorry! You can't have more than three classifications for a sequence.</h4>
      </div>
      <div class="modal-body">
        <p>If you want to make a change, you can remove an existing classification by clicking on it.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger classify-modal-button" data-dismiss="modal">Close</button>
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
        <h4 class="modal-title">Sorry! This classification has expired. Please choose Next Sequence to move on.</h4>
      </div>
      <div class="modal-body">
        <p></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger classify-modal-button" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<div class="modal fade" id="classify_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    <h4 class="modal-title" id="myModalLabel">Classify the animal</h4>
      </div>
      <div class="modal-body">
        <form id='classify-form' role='form'>
		  <div id='classify-species'>
<?php

foreach ($this->allSpecies as $stuff) {
	list($species_id, $species_name) = $stuff;
	print "<h3 id='species_header_${species_id}' class='species_header'>" . $species_name."</h3>\n";
	}

print "<input type='hidden' name='species' id='species_value'/>\n";
print "<input id='currPhotoId' type='hidden' name='photo_id' value='".$this->photo_id."'/>\n";
          
?>

  </div>
  
  <div class='container-fluid'>
<?php
print "<div class='spacer-1em'></div>";
print "<div class='row'>\n";
print "<div class='col-md-8'>\n";
  
print "<div id='species_helplet'></div>";

print "</div>"; // col8

print "<div class='col-md-4 species-modal-side'>\n";
//print "<div class='spacer-2em'></div>";

print '<h2>Can you add more detail?</h2>';

print "<div class='row'>\n";
  
foreach($this->classifyInputs as $formInput){
  //print "<div class='row'>\n";
  print "<div class='col-md-6'>\n";
  print "<div class='form-group species_classify'>\n";
  print $formInput;
  print "</div>\n";
  print "</div>\n";
  //print "</div> <!-- /.row -->\n";
}
print "</div> <!-- /.row -->\n";

print "<div class='spacer-4em'></div>";
print '<h2>Are you happy with your choice?</h2>';
print '<button type="button" class="btn btn-default classify-modal-button" data-dismiss="modal">No, Cancel</button>';
print '<button type="button" class="btn btn-danger classify-modal-button" id="classify-save">Yes, Classify</button>';

print "</div>"; // col4
print "</div>";
?>
 </div> <!-- /.container-fluid -->
        </form>
      </div> <!-- modal-body -->
      <!--div class="modal-footer">
	    <h2> Is this what you see? </h2>
        <button type="button" class="btn btn-default" data-dismiss="modal">No, Try Again</button>
        <button type="button" class="btn btn-primary" id='classify-save'>Yes, Save</button>
      </div -->
	  <!--
<div id='species_helplet'>
</div>
-->

    </div> <!-- modal-content -->
  </div> <!--modal-dialog -->


</div> <!-- modal-fade -->

<?php
JHTML::script("com_biodiv/bootbox.js", true, true);
JHTML::stylesheet("com_biodiv/com_biodiv.css", array(), true);
JHTML::script("com_biodiv/commonclassify.js", true, true);
JHTML::script("com_biodiv/kiosk.js", true, true);
JHTML::script("https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js", true, true);
JHTML::script("com_biodiv/project.js", true, true);

?>


