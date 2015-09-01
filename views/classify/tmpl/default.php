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
$document->addScriptDeclaration("BioDiv.next_photo = ".$this->photoDetails['next_photo'].";");

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
    <div id='photo_img' class='col-md-12'>
<div>
<?php
print JHTML::image(photoURL($this->photo_id), 'Photo ' . $this->photo_id, array('class' =>'img-responsive'));
?>
</div>
</div> <!-- /.col-md-9 -->
</div> <!-- /.row -->

<div class='row'>
<div class='col-md-4 pull-left'>

<div class='progress'>
<div class='progress-bar progress-bar-primary' role='progress-bar' style='width: <?php print $this->sequenceProgress;?>%'>
  <?php print $this->sequencePosition . "/" . $this->sequenceLength; ?>
</div>
</div>
<div id='classify_tags'></div>
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

<ol class="carousel-indicators spb">
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

print "<div class='carousel-inner'>";
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
  <a class="left carousel-control" href="#carousel-species" role="button" data-slide="prev">
    <span class="fa fa-chevron-left"></span>
  </a>
  <a class="right carousel-control" href="#carousel-species" role="button" data-slide="next">
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


