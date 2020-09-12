<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;
?>

<?php

	$document = JFactory::getDocument();
	
	$seq_json = json_encode($this->sequences);
	$document->addScriptDeclaration("BioDiv.sequences = '".$seq_json."';");
	$document->addScriptDeclaration("BioDiv.topic = '".$this->topic_id."';");
	
	if ( $this->detail == 1 ) {
		$document->addScriptDeclaration("BioDiv.detail = ".$this->detail.";");
	}
	
	$num_sequences = count($this->sequences);
	if ( $num_sequences == 0 ) {
		print "<h1>".$this->translations["no_seqs"]["translation_text"]."</h1>";
	}
	else {
		$loc = $this->currentSequence->getLocation();
		$document->addScriptDeclaration("BioDiv.south = ".$loc->getSouth().";");
		$document->addScriptDeclaration("BioDiv.west = ".$loc->getWest().";");
		$document->addScriptDeclaration("BioDiv.north = ".$loc->getNorth().";");
		$document->addScriptDeclaration("BioDiv.east = ".$loc->getEast().";");
		
		$prog_width = 100/$num_sequences;
		
		print "<div class='row' >";
		print "<div class='col-md-8' >";
		
		print "<h1>".$this->translations["what_see"]["translation_text"]."</h1>";
		print "</div>";
		print "<div class='col-md-4' >";
		print "<h4>".$this->topicName."</h4>";
		
		print "<div class='progress'>";
		print "  <div id='seq_progress_bar' class='progress-bar progress-bar-info' role='progressbar' aria-valuenow='".$prog_width."' aria-valuemin='0' aria-valuemax='100' style='width:".$prog_width."%'>";
		print "  1/" . $num_sequences;
		print "  </div>";
		print "</div>"; // progress bar
		print "</div>"; // col-5
		print "</div>"; //row
		
		print "<p></p>";
		
		// Display all the classification stuff...
		print "<div id='carouselRow' class='row'>";
		print "<div class='col-md-9'>";
		
		print "<div class='row'>";
		print "<div class='col-md-12'>"; // To get the indent back?
		print "<div class='btn-group pull-left' role='group'>";
		$this->mediaCarousel->generateLeftControls();
		print "</div> <!-- /.btn-group -->";
		print "<div class='btn-group pull-right' role='group'>";
		$this->mediaCarousel->generateLocationButton();
		$this->mediaCarousel->generateNextButton();
		
		// Add a hidden Results button which is displayed instead of Next when all sequences have been done.
		print "	      <form id='control_finish' class='inline' style='display:none' action = '".BIODIV_ROOT."' method = 'POST'>";
		print "		  <input type='hidden' name='view' value='trainingresults'/>";
		print "		  <input type='hidden' name='option' value='".BIODIV_COMPONENT."'/>";
		print "		  <input type='hidden' name='topic_id' value='".$this->topic_id."'/>";
		if ( $this->detail ) print "		  <input type='hidden' name='detail' value='1'/>";
		print "		  <input type='hidden' name='sequences' value='".$seq_json."'/>";
		print "		  <input id='user_animals' type='hidden' name='animals' value=''/>";
		print "		  <button class='btn btn-danger' type='submit' >".
						  $this->translations["results"]["translation_text"]." <span class='fa fa-arrow-circle-right'/></button>";
		print "		  </form>";
			
		print "</div>"; // btn group
		print "</div>"; // col-12
		print "</div>"; // row
		
		print "<div class='row'>";
		print "<div class='col-md-12'>"; // To get the indent back?
		
		$this->mediaCarousel->generateMediaCarousel($this->currentSequence);
		
		print "</div>"; // col-12
		print "</div>"; // row
		print "<div id='classifications'></div>";
		
		print "</div>"; // col-md-9
		
		print "<div class='col-md-3'>";
		
		
		$this->speciesCarousel->generateSpeciesCarousel();
		print "</div>"; // col-md-3
		print "</div>"; // row
		$this->speciesCarousel->generateClassifyModal();
	}
	
?>

<div id="map_modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"> <?php print $this->translations['map_modal']['translation_text']; ?> </h4>
      </div>
      <div class="modal-body">
	    <div id="no_map"><h5> <?php print $this->translations['no_map']['translation_text']; ?> </h5></div>
        <div id="map_canvas" style="width:500px;height:500px;"></div>
      </div>
	  <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
      </div>
	  	  
    </div>

  </div>
</div>


<?php

$mapOptions = mapOptions();
$key = $mapOptions['key'];

JHTML::script("https://maps.googleapis.com/maps/api/js?key=" . $key);
//JHTML::script("https://maps.googleapis.com/maps/api/js?key="); // For dev
JHTML::stylesheet("com_biodiv/com_biodiv.css", array(), true);
JHTML::script("com_biodiv/commonclassify.js", true, true);
JHTML::script("com_biodiv/trainingtopic.js", true, true);

?>



