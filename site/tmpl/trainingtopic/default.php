<?php

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
 
// No direct access to this file
defined('_JEXEC') or die;


	$document = Factory::getDocument();
	
	$seq_json = json_encode($this->sequences);
	$document->addScriptDeclaration("BioDiv.sequences = '".$seq_json."';");
	$document->addScriptDeclaration("BioDiv.topic = '".$this->topic_id."';");
	
	if ( $this->detail == 1 ) {
		$document->addScriptDeclaration("BioDiv.detail = ".$this->detail.";");
	}
	
	$num_sequences = count($this->sequences);
	if ( $num_sequences == 0 ) {
		print "<h1>".Text::_("COM_BIODIV_TRAINING_NO_SEQS")."</h1>";
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
		
		print "<h1>".Text::_("COM_BIODIV_TRAINING_WHAT_SEE")."</h1>";
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
		print "<div class='col-md-8'>";
		
		print "<div class='row'>";
		print "<div class='col-md-12'>"; // To get the indent back?
		//print "<div class='btn-group pull-left' role='group'>";
		//$this->mediaCarousel->generateLeftControls();
		//print "</div> <!-- /.btn-group -->";
		print "<div class='btn-group pull-right' role='group'>";
		if ( $this->currentSequence->getMedia() == "photo" ) {
			$this->mediaCarousel->generateInvertButton( true );
		}
		else {
			$this->mediaCarousel->generateInvertButton( false );
		}
		$this->mediaCarousel->generateLocationButton();
		$this->mediaCarousel->generateNextButton();
		
		// Add a hidden Results button which is displayed instead of Next when all sequences have been done.
		// Could be in-page for mini sites eg UAE
		if ( $this->inPageResults ) {
			print "	      <form id='control_finish_inpage' class='inline' style='display:none' action = '".BIODIV_ROOT."' method = 'POST'>";
			print "		  <input type='hidden' name='view' value='trainingresultsajax'/>";
			print "		  <input type='hidden' name='option' value='".BIODIV_COMPONENT."'/>";
			print "		  <input type='hidden' name='topic_id' value='".$this->topic_id."'/>";
			if ( $this->detail ) print "		  <input type='hidden' name='detail' value='1'/>";
			print "		  <input type='hidden' name='sequences' value='".$seq_json."'/>";
			print "		  <input id='user_animals' type='hidden' name='animals' value=''/>";
			print "		  <button class='btn btn-danger' type='submit' >".
							  Text::_("COM_BIODIV_TRAINING_RESULTS")." <span class='fa fa-arrow-circle-right'/></button>";
			print "		  </form>";
		}
		else {
			print "	      <form id='control_finish' class='inline' style='display:none' action = '".BIODIV_ROOT."' method = 'POST'>";
			print "		  <input type='hidden' name='view' value='trainingresults'/>";
			print "		  <input type='hidden' name='option' value='".BIODIV_COMPONENT."'/>";
			print "		  <input type='hidden' name='topic_id' value='".$this->topic_id."'/>";
			if ( $this->detail ) print "		  <input type='hidden' name='detail' value='1'/>";
			print "		  <input type='hidden' name='sequences' value='".$seq_json."'/>";
			print "		  <input id='user_animals' type='hidden' name='animals' value=''/>";
			print "		  <button class='btn btn-danger' type='submit' >".
							  Text::_("COM_BIODIV_TRAINING_RESULTS")." <span class='fa fa-arrow-circle-right'/></button>";
			print "		  </form>";
		}
			
		print "</div>"; // btn group
		print "</div>"; // col-12
		print "</div>"; // row
		
		print "<div class='row'>";
		print "<div class='col-md-12'>"; // To get the indent back?
		
		$this->mediaCarousel->generateMediaCarousel($this->currentSequence);
		
		print "</div>"; // col-12
		print "</div>"; // row
		print "<div id='classifications'></div>";
		
		print "</div>"; // col-md-8
		
		print "<div class='col-md-4'>";
		
		$this->speciesCarousel->generateSpeciesCarousel();
		print "</div>"; // col-md-4
		print "</div>"; // row
		$this->speciesCarousel->generateClassifyModal();
	}
	


print '<div id="map_modal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog modal-sm">';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header">';
print '        <button type="button" class="close" data-dismiss="modal">&times;</button>';
print '        <h4 class="modal-title"> '.Text::_("COM_BIODIV_TRAINING_MAP_MODAL").' </h4>';
print '      </div>';
print '      <div class="modal-body">';
print '	    <div id="no_map"><h5> '.Text::_("COM_BIODIV_TRAINING_NO_MAP").' </h5></div>';
print '        <div id="map_canvas" style="width:100%;height:500px;"></div>';
print '      </div>';
print '	  <div class="modal-footer">';
print '        <button type="button" class="btn btn-primary" data-dismiss="modal">'.Text::_("COM_BIODIV_TRAINING_CLOSE").'</button>';
print '      </div>	  ';	  
print '    </div>';
print '  </div>';
print '</div>';




$mapOptions = mapOptions();
$key = $mapOptions['key'];

print '<script>
  (g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",m=document,b=window;b=b[c]||(b[c]={});var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));e.set("libraries",[...r]+"");for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);e.set("callback",c+".maps."+q);a.src=`https://maps.${c}apis.com/maps/api/js?`+e;d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)}));d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})({
    key: "'.$key.'",
    v: "weekly",
  });
</script>';

$this->document->getWebAssetManager()->usePreset('com_biodiv.trainingtopic');

HTMLHelper::_('bootstrap.modal', '.selector', []);
HTMLHelper::_('bootstrap.carousel', '#photoCarousel', []);
HTMLHelper::_('bootstrap.tab', '.tab-content', []);

// JHTML::script("https://maps.googleapis.com/maps/api/js?key=" . $key);
// //JHTML::script("https://maps.googleapis.com/maps/api/js?key="); // For dev
// JHTML::stylesheet("com_biodiv/com_biodiv.css", array(), true);
// JHTML::script("com_biodiv/commonclassify.js", true, true);
// JHTML::script("com_biodiv/trainingfunctions.js", true, true);
// JHTML::script("com_biodiv/trainingtopic.js", true, true);



?>



