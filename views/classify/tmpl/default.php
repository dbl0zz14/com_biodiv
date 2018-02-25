<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

fbInit();

$document = JFactory::getDocument();
//$document->addScriptDeclaration("BioDiv.next_photo = ".$this->photoDetails['next_photo'].";");
$document->addScriptDeclaration("BioDiv.curr_photo = ".$this->photo_id.";");

if(!$this->photo_id){
  print "<h2>No photos for you to classify</h2>\n";
  return;
 }

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
  print "<button type='button' class='btn btn-warning $extraText' id='$control_id'>$control</button>";
}
?>
<h1>What do you see?</h1>
<?php
	      if($this->photoDetails['person_id'] == userID()){
    print "<div class='alert lead alert-info'>You uploaded this!</div>";
  }
?>


<div id='debug' style='display:none'>Debug <?php
	      print "<p>(photo_id  " . $this->photo_id .")</p>\n";
?>
</div>

<div class="container-fluid">
	
    <div class='row'>
     <div class='col-md-3'>
	<div class='btn-group pull-left' role='group'>
<?php
foreach($this->lcontrols as $control_id => $control){
  makeControlButton($control_id, $control);
}
?>
        </div> <!-- /.btn-group -->
     </div> <!-- /.col-md-3 -->



     <div class='col-md-6'>
	<div class='btn-group pull-right' role='group'>
  <?php
  foreach($this->rcontrols as $control_id => $control){
    makeControlButton($control_id, $control);  
  }
  print "<button type='button' class='btn btn-warning' id='control_nextseq'>".$this->nextseq."</button>";
?>
        </div> <!-- /.btn-group -->
     </div> <!-- /.col-md-6 -->

     <div class='col-md-3'>
  <?php
?>
     </div> <!-- /.col-md-3 -->

  </div> <!-- /.row -->

  <div class='row'>
  <div class='col-md-9'>


  <div class="row">
    <!-- div id='photo_img' class='col-md-12' -->
<div class='col-md-12' >
<div id="photoCarousel" class="carousel slide" data-ride="carousel" data-interval='false' data-wrap="false">
  <!-- Indicators -->
  <ol class="carousel-indicators">
  <?php
  $numphotos = count($this->sequence);
  for ($i = 0; $i < $numphotos; $i++) {
	$class_extras = "";
	if ($i == 0) $class_extras = ' class="active"';
    print '<li data-target="#photoCarousel" data-slide-to="'.$i.'"'.$class_extras.'></li>';
  }
  ?>
  </ol>

  <!-- Wrapper for slides -->
  <div id="photoCarouselInner" class="carousel-inner">
<?php
$first = true;
foreach($this->sequence as $photo_id  ){
	if ($first) {
		print '<div class="item active" data-photo-id="'.$photo_id.'">';
		$first = false;
	}
	else {
		print '<div class="item" data-photo-id="'.$photo_id.'">';
	}
	print JHTML::image(photoURL($photo_id), 'Photo ' . $photo_id, array('class' =>'img-responsive'));
	print '</div>';
 }
?>

  </div> <!-- /.carousel-inner -->
  
  <!-- Left and right controls -->
  <?php
  if (count($this->sequence) > 1 ) {
  //print '<a class="left carousel-control" href="#photoCarousel" data-slide="prev">';
  print '<a class="left carousel-control photo-carousel-control" href="#photoCarousel" data-slide="prev">';
  print '  <span class="glyphicon glyphicon-chevron-left"></span>';
  print '  <span class="sr-only">Previous</span>';
  print '</a>';
  //print '<a class="right carousel-control" href="#photoCarousel" data-slide="next" style="background:none !important">';
  print '<a class="right carousel-control photo-carousel-control" href="#photoCarousel" data-slide="next">';
  print '  <span class="glyphicon glyphicon-chevron-right"></span>';
  print '  <span class="sr-only">Next</span>';
  print '</a>';
  }
  ?>
</div> <!-- /.photoCarousel -->
</div> <!-- /.col-md-12 carousel-->
</div> <!-- /.row -->

<div class='row'>
<div class='col-md-4 pull-left'>

<div>
<div id='classify_tags'></div>
</div>
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
<div id='like_image_container' class='pull-right col-md-4'>
  <button  id='favourite' type='button' class='btn btn-warning pull-right' 
  <?php print "style='display:$favDisp'";?>><span class='fa fa-thumbs-up fa-2x'></span></button>
  <button id='not-favourite' type='button' class='btn btn-warning pull-right'
  <?php print "style='display:$nonFavDisp'";?>><span class='fa fa-thumbs-o-up fa-2x'></span></button>
</div> <!-- /.col-md-4 -->
<div class='pull-right col-md-4'><?php fbLikePhoto($this->photo_id); ?> </div>

</div> <!-- /.row -->
</div> <!-- /.col-md-9 -->

    <div class='col-md-3 cls-xs-12'>

<div id='carousel-species' class='carousel slide' data-ride='carousel' data-interval='false' data-wrap='false'>

<ol id="species-indicators" class="carousel-indicators spb">
    <li data-target="#carousel-species" data-slide-to="0" class="active spb"></li>
    <li data-target="#carousel-species" data-slide-to="1" class="spb"></li>
    <li data-target="#carousel-species" data-slide-to="2" class="spb"></li>
  </ol>

<?php

    $carouselItems = array(); // 2D array [page][item]
foreach($this->species as $species_id => $species){
  $page = $species['page'];
  if(!in_array($page, array_keys($carouselItems))){
    $carouselItems[$page] = array();
  }
  
  $name = $species['name'];
  switch($species['type']){
  case 'mammal':
    $btnClass = 'btn-primary';
    break;

  case 'bird':  
    $btnClass = 'btn-info';
    break;

  case 'notinlist':
    $btnClass = 'btn-warning';
    break;

  }
  $carouselItems[$page][] =
    "<button type='button' id='species_select_${species_id}' class='btn $btnClass btn-block species_select' data-toggle='modal' data-target='#classify_modal'>$name</button> \n";
}

print "<div id='species-carousel-inner' class='carousel-inner'>";
foreach($carouselItems as $pageNum => $carouselPage){
  if($pageNum<0){
    continue;
  }
  // add notinlist items to every page
  $carouselPage = array_merge($carouselPage, $carouselItems[-1]);
  $active = ($pageNum==1)?" active":"";
  print "<div class='item $active'>\n";
  print implode("\n", $carouselPage);
  print "</div> <!-- / item -->\n";

}
print "</div> <!-- /carousel-inner--> \n";
?>
 <!-- Controls -->
  <a class="left carousel-control species-carousel-control" href="#carousel-species" role="button" data-slide="prev">
    <span class="fa fa-chevron-left"></span>
  </a>
  <a class="right carousel-control species-carousel-control" href="#carousel-species" role="button" data-slide="next">
    <span class="fa fa-chevron-right"></span>
  </a>

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
  foreach($this->species as $species_id => $species){
  print "<h2 id='species_header_${species_id}' class='species_header'>" . $species['name']."</h2>\n";
}

print "<input type='hidden' name='species' id='species_value'/>\n";
print "<input id='currPhotoId' type='hidden' name='photo_id' value='".$this->photo_id."'/>\n";
          
?>

  </div>
  
  <div class='container-fluid'>
<?php
  
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
 </div> <!-- /.container-fluid -->
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id='classify-save'>Save changes</button>
      </div>
<div id='species_helplet'>
</div>


    </div>
  </div>


</div>

<?php
JHTML::stylesheet("com_biodiv/com_biodiv.css", true, true);
JHTML::script("com_biodiv/bootbox.js", true, true);
JHTML::script("com_biodiv/classify.js", true, true);
?>


