<?php

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use MammalWeb\Component\Biodiv\Site\Library\Core\Biodiv;


/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

//$document = JFactory::getDocument();
//$document->addScriptDeclaration("BioDiv.next_photo = ".$this->photoDetails['next_photo'].";");

if ( $this->photo_id ) {
	$this->document->addScriptDeclaration("BioDiv.curr_photo = ".$this->photo_id.";");
	$this->document->addScriptDeclaration("BioDiv.south = ".$this->location->getSouth().";");
	$this->document->addScriptDeclaration("BioDiv.west = ".$this->location->getWest().";");
	$this->document->addScriptDeclaration("BioDiv.north = ".$this->location->getNorth().";");
	$this->document->addScriptDeclaration("BioDiv.east = ".$this->location->getEast().";");

	$this->document->addScriptDeclaration("BioDiv.eggsId = ".$this->eggsId.";");
	$this->document->addScriptDeclaration("BioDiv.nestId = ".$this->nestId.";");
}

$this->document->addScriptDeclaration("BioDiv.classifyProject = ".$this->project_id.";");


if ( $this->projectPage ) {
	print '<div class="container">';
	print '<div class="row">';
	print '<div class="col-md-12"><a href="'.$this->projectPage.'" class="btn btn-primary">'.Text::_("COM_BIODIV_CLASSIFY_PROJECT_PAGE").'</a></div>';
	print '</div>'; // row
	print '</div>'; // container
}

if(!$this->photo_id){
	
	print "<h2>" . Text::_("COM_BIODIV_CLASSIFY_NO_PHO") . "</h2>\n";
	print "<h3>" . Text::_("COM_BIODIV_CLASSIFY_AVAIL") . "</h3>\n";
	print "<h3>" . Text::_("COM_BIODIV_CLASSIFY_CHECK") . "</h3>\n";

	return;
}
 
$this->document->addScriptDeclaration("BioDiv.maxclass = ".$this->maxClassifications.";");
$this->document->addScriptDeclaration("BioDiv.loadingMsg = '".Text::_("COM_BIODIV_CLASSIFY_LOADING")."';");
	

print '<div id="debug" style="display:none">Debug <p>(photo_id  ' 
		. $this->photo_id .')</p>\n';
print '</div>';

//print '<div class="container-fluid" id="photo-container">';
print '<div class="row">';
print '	<div class="col-md-8 cls-xs-12">';

print '	<div class="mb-4">';
if ( $this->isVideo === true ) {
	print "<h2>" . Text::_("COM_BIODIV_CLASSIFY_WHAT_VID") . "</h2>";
}
else if ( $this->isAudio === true ) {
	print "<h2>" . Text::_("COM_BIODIV_CLASSIFY_WHAT_HEAR") . "</h2>";
}
else {
	print "<h2>" . Text::_("COM_BIODIV_CLASSIFY_WHAT_SEE") . "</h2>";
}
print '</div>'; // mb-4

print '	<div class="row">';
if($this->photoDetails['person_id'] == $this->personId){
	print "<div class='col-xs-12 col-sm-12 col-md-12 col-lg-4 text-info'>" . Text::_("COM_BIODIV_CLASSIFY_YOU_UP") . "</div>";
}
else {
	print "<div class='col-xs-12 col-sm-12 col-md-12 col-lg-4'></div>";
}

	
print '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-8">';
print '<div class="btn-group pull-right" role="group">';
// print "<button type='button' class='btn btn-secondary' id='invert_image' ";
// print " data-bs-toggle='tooltip' title='".Text::_("COM_BIODIV_CLASSIFY_INVERT_TOOLTIP")."'>".$this->invertimage."</button>";
// print "<button type='button' class='btn btn-secondary' id='sequence_info' data-toggle='modal' data-target='#info_modal' ";
// print " data-bs-toggle='tooltip' title='".Text::_("COM_BIODIV_CLASSIFY_SEQINFO_TOOLTIP")."'>".$this->sequenceinfo."</button>";
// print "<button type='button' class='btn btn-secondary' id='control_map' ";
// print " data-bs-toggle='tooltip' title='".Text::_("COM_BIODIV_CLASSIFY_LOCATION_TOOLTIP")."'>".$this->showmap."</button>";
// print "<button type='button' class='btn btn-primary loader' id='control_nextseq'>".$this->nextseq."</button>";

$this->mediaCarousel->generateInvertButton ( true );
$this->mediaCarousel->generateInfoButton ( true );
$this->mediaCarousel->generateLocationButton ();
$this->mediaCarousel->generateNextButton ();

print '        </div> <!-- /.btn-group -->';
print '     </div> <!-- /.col-md-8 -->';

print '  </div> <!-- /.row -->';

print '  <div class="row">';
print '  <div class="col-md-12 photo-col">';


print '<div class="row">';
print '<!-- div id="photo_img" class="col-md-12" -->';
print '<div class="col-md-12" >';


$this->mediaCarousel->generateMediaCarousel($this->sequenceObj);


print '</div> <!-- /.col-md-12 carousel-->';
print '</div> <!-- /.row -->';

print '<div class="row">';
print '<div id="classify_tags" class="col-md-9 pull-left">';


// Check for existing animals, ie refreshing the page
$animals = 0;
if ( $this->animal_ids ) {
	$animals = explode("_", $this->animal_ids);
}
if ( $animals ) {
	foreach ( $animals as $next_id ) {
		print "<div class='tagcontainer singletag-classification'>";
		$button_code = $this->spotterModel->getClassificationButton ( $next_id, $this->animals );
		print $button_code;
		print "</div>";
	}
}

print '</div>'; // col-md-9

if($this->spotterModel->isFavourite($this->photo_id)){
    $favDisp = 'block';
    $nonFavDisp = 'none';
}
else{
    $favDisp = 'none';
    $nonFavDisp = 'block';
}


print "<div class='pull-right col-md-3'> ";
//print '<div class="btn-group pull-right" role="group">';
print "<div>";
print "  <button  id='report_media' type='button' class='btn btn-outline-primary pull-right' ";
print "  data-bs-toggle='tooltip' title='".Text::_("COM_BIODIV_CLASSIFY_REPORT_TOOLTIP")."'>";
print "  <span class='fa fa-flag-o'></span></button>";
print "</div>";
print "<div id='like_image_container' > ";
print "  <button  id='favourite' type='button' class='btn btn-outline-primary pull-right'  ";
print "  data-bs-toggle='tooltip' title='".Text::_("COM_BIODIV_CLASSIFY_UNFAV_TOOLTIP")."'";
print "  style='display:".$favDisp."'><span class='fa fa-thumbs-up'></span></button> ";
print "  <button id='not-favourite' type='button' class='btn btn-outline-primary pull-right' ";
print "  data-bs-toggle='tooltip' title='".Text::_("COM_BIODIV_CLASSIFY_FAV_TOOLTIP")."'";
print "  style='display:".$nonFavDisp."'><span class='fa fa-thumbs-o-up'></span></button> ";
print "</div>"; // like_image_container
print "</div> <!-- /.col-md-3 --> ";


print '</div> <!-- /.row -->';
print '</div> <!-- /.col-md-12 -->';

print '</div> ';

print '</div> <!-- /.col-md-9 -->'; // LHS


print '<div class="col-md-4 cls-xs-12 species-carousel-col">';


// print '<div class="input-group">';
// print '    <span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>';
// print '    <input id="search_species" type="text" class="form-control" name="speciesfilter" placeholder="Search..">';
// print '</div>';

// print "<ul id = 'species-nav' class='nav nav-tabs' role='tablist'>";
// $first = true;

// foreach ( $this->projectFilters as $filterId=>$filter ) {
	// if ( $first == true ) {
		// print "  <li class='nav-item species-tab' role='presentation'><button data-bs-toggle='tab' class='nav-link active' data-bs-target='#filter_${filterId}' type='button' role='tab'>".$filter["label"]."</button></li>";
		// $first = false;
	// } else {
		// print "  <li class='nav-item species-tab' role='presentation'><button data-bs-toggle='tab' class='nav-link' data-bs-target='#filter_${filterId}' type='button' role='tab'>".$filter["label"]."</button></li>";
	// }
// }
// print "</ul>";

// print "<div class='tab-content no-padding'>";

// $extra = "show active";
// foreach ( $this->projectFilters as $filterId=>$filter ) {
	// print "  <div id='filter_${filterId}' class='tab-pane fade $extra' role='tab-panel'>";
	// print "<div id='carousel-species-${filterId}' class='carousel slide' data-ride='carousel' data-interval='false' data-wrap='false'>";
	
	// $this->spotterModel->printSpeciesListSearch ( $filterId, $filter['species'], false );

	// print "</div> <!-- /carousel-species carousel--> \n";
	// print "  </div>";
	
	// $extra = "";
// }

// print "</div>";

$this->speciesCarousel->generateSpeciesCarousel ();


print '</div>'; //col-md-3
print '</div>'; // row
//print '</div>'; // container

print '<div id="map_modal" class="modal fade" tabindex="-1" aria-labelledby="mapModalLabel">';
print '  <div class="modal-dialog modal-dialog-scrollable">';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header">';
print '        <button type="button" class="close" data-dismiss="modal">&times;</button>';
print '        <h4 class="modal-title" id="mapModalLabel"> '.Text::_("COM_BIODIV_CLASSIFY_MAP_MODAL").' </h4>';
print '      </div>';
print '      <div class="modal-body p-4">';
print '	    <div id="no_map"><h5> '.Text::_("COM_BIODIV_CLASSIFY_NO_MAP").' </h5></div>';
print '        <div id="map_canvas" style="width:100%;height:500px;"></div>';
print '      </div>';
print '      <div class="modal-footer">';
print '        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>';
print '      </div>';
print '    </div>';

print '  </div>';
print '</div>';

print '<div id="info_modal" class="modal fade" tabindex="-1" aria-labelledby="infoModalLabel">';
print '  <div class="modal-dialog modal-sm modal-dialog-scrollable">';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header">';
print '       <button type="button" class="close" data-dismiss="modal">&times;</button>';
print '       <h4 class="modal-title" id="infoModalLabel"> ';
if ( $this->isVideo === true ) {
	print Text::_("COM_BIODIV_CLASSIFY_VIDEO_INFO"); 
}
else if ( $this->isAudio === true ) {
	
	print Text::_("COM_BIODIV_CLASSIFY_AUDIO_INFO"); 
}
else {
	print Text::_("COM_BIODIV_CLASSIFY_SEQUENCE_INFO"); 
}
print '		</h4>';
print '      </div>';
print '      <div class="modal-body p-4">';
if ( $this->isVideo === true ) {
	print "<p>" . Text::_("COM_BIODIV_CLASSIFY_SEQUENCE_ID") . ": " . $this->sequenceId . "</p>"; 
	print "<p>" . Text::_("COM_BIODIV_CLASSIFY_VIDEO_ID") . ": " . $this->photo_id . "</p>"; 
}
else if ( $this->isAudio === true ) {
	print "<p>" . Text::_("COM_BIODIV_CLASSIFY_SEQUENCE_ID") . ": " . $this->sequenceId . "</p>"; 
	print "<p>" . Text::_("COM_BIODIV_CLASSIFY_AUDIO_ID") . ": " . $this->photo_id . "</p>"; 
}
else {
	print "<p>" . Text::_("COM_BIODIV_CLASSIFY_SEQUENCE_ID") . ": " . $this->sequenceId . "</p>"; 
	print "<p>" . Text::_("COM_BIODIV_CLASSIFY_PHOTO_ID") . ": " . $this->photo_id . "</p>"; 
}
print '      </div>';
print '      <div class="modal-footer">';
print '        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>';
print '      </div>';
print '    </div>';

print '  </div>';
print '</div>';

print '<div id="too_many_modal" class="modal fade" tabindex="-1" aria-labelledby="tooManyModalLabel">';
print '  <div class="modal-dialog modal-sm">';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header">';
print '        <button type="button" class="close" data-dismiss="modal">&times;</button>';
print '        <h4 class="modal-title" id="tooManyModalLabel"> '.Text::_("COM_BIODIV_CLASSIFY_MAX_CLASS").' </h4>';
print '      </div>';
print '      <div class="modal-body">';
print '        <p></p>';
print '      </div>';
print '      <div class="modal-footer">';
print '        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>';
print '      </div>';
print '    </div>';
print '  </div>';
print '</div>';

print '<div id="timed_out_modal" class="modal fade" tabindex="-1" aria-labelledby="timedOutModalLabel">';
print '  <div class="modal-dialog modal-sm">';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header">';
print '        <button type="button" class="close" data-dismiss="modal">&times;</button>';
print '        <h4 class="modal-title" id="timedOutModalLabel">'.Text::_("COM_BIODIV_CLASSIFY_CLASS_EXP").' </h4>';
print '      </div>';
print '      <div class="modal-body">';
print '        <p></p>';
print '      </div>';
print '      <div class="modal-footer">';
print '        <button type="button" class="btn btn-primary classify-modal-button" data-dismiss="modal">'.Text::_("COM_BIODIV_CLASSIFY_CLOSE").'</button>';
print '      </div>';
print '    </div>';

print '  </div>';
print '</div>';


// print '<div id="report_media_modal" class="modal fade" role="dialog">';
// print '    <!-- Modal content-->';
// print '    <div class="modal-content">';
// print '      <div class="modal-header">';
// print '        <button type="button" class="close" data-dismiss="modal">&times;</button>';
// print '        <h4 class="modal-title"> '.Text::_("COM_BIODIV_CLASSIFY_REPORT_MEDIA").' </h4>';
// print '      </div>';
// print '      <div class="modal-body">';
		
// print '		<h5> '.Text::_("COM_BIODIV_CLASSIFY_REPORT_MEDIA_EXPLAIN").' </h5>';
		
// print '		<form id="reportMediaForm" role="form">';
		
// print '		<input id="currSequenceId" type="hidden" name="sequence_id" value="'.$this->sequenceId.'"/>';
		
// print '		<div class="form-group">';
// print '		<label for="report_media_notes">'.Text::_("COM_BIODIV_CLASSIFY_REPORT_MEDIA_NOTES").'</label>';
// print '		<textarea class="form-control" id="report_media_notes" name="report_media_notes" rows="2" maxlength="200" ></textarea>';
// print '		</div>';
		
// print '      </div>';
// print '	  <div class="modal-footer">';
// print '        <button type="button" class="btn btn-default" data-dismiss="modal">'.Text::_("COM_BIODIV_CLASSIFY_CANCEL").'</button>';
// print '		<button type="button" class="btn btn-success" data-dismiss="modal" id="report_media_save">'.Text::_("COM_BIODIV_CLASSIFY_SUBMIT").'</button>';
// print '	    </form>';
// print '      </div>';
	  	  
// print '    </div>';

// print '  </div>';
// print '</div>';

print '<div id="report_media_modal" class="modal fade hide" data-bs-backdrop="false" tabindex="-1" aria-labelledby="reportMediaModalLabel">';
print '  <div class="modal-dialog">';
print '    <div class="modal-content">';
print '      <div class="modal-header">';
print '        <h4 class="modal-title" id="reportMediaModalLabel">'.Text::_("COM_BIODIV_CLASSIFY_REPORT_MEDIA").'</h4>';
print '        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
print '      </div>';
print '      <div class="modal-body">';
print '        <div class="p-4">';
print '		     <h5> '.Text::_("COM_BIODIV_CLASSIFY_REPORT_MEDIA_EXPLAIN").' </h5>';
print '		   </div>';
print '        <div class="p-4">';
print '		     <form id="reportMediaForm" role="form">';
print HTMLHelper::_('form.token');
print '		       <input id="currSequenceId" type="hidden" name="sequence_id" value="'.$this->sequenceId.'"/>';
print '		         <label for="report_media_notes" class="form-label" aria-describedby="reportMediaNotes">'.Text::_("COM_BIODIV_CLASSIFY_REPORT_MEDIA_NOTES").'</label>';
print '		         <textarea class="form-control" id="report_media_notes" name="report_media_notes" rows="2" maxlength="200" ></textarea>';
print '          </form>';
print '		   </div>';
print '      </div>';
print '      <div class="modal-footer">';
print '        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">'.Text::_("COM_BIODIV_CLASSIFY_CANCEL").'</button>';
print '        <button type="button" id = "report_media_save" class="btn btn-primary" data-bs-dismiss="modal">'.Text::_("COM_BIODIV_CLASSIFY_SUBMIT").'</button>';
print '      </div>';
print '    </div>';
print '  </div>';
print '</div>';


$this->speciesCarousel->generateClassifyModal($this->photo_id);

$this->speciesCarousel->generateClassifyNothingModal($this->photo_id);


// print '<div class="modal fade" id="classify_modal" tabindex="-1" aria-labelledby="classifyModalLabel" aria-hidden="true">';
// print '  <div class="modal-dialog modal-dialog-scrollable modal-xl">';
// print '    <div class="modal-content">';
// //print '      <form id="classify-form" role="form">';
// print '      <div class="modal-header">';
// print '    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">'.Text::_("COM_BIODIV_CLASSIFY_CLOSE").'</span></button>';
// print '    <h4 class="modal-title" id="classifyModalLabel">'.Text::_("COM_BIODIV_CLASSIFY_CLASS_ANI").'</h4>';
// print '      </div>';
// print '      <div class="modal-body">';
// print HTMLHelper::_('form.token');
// print '		  <div id="classify-species">';

// foreach ($this->allSpecies as $stuff) {
	// list($species_id, $species_name) = $stuff;
	// print "<h2 id='species_header_${species_id}' class='species_header'>" . $species_name."</h2>\n";
// }

// print "<input type='hidden' name='species' id='species_value'/>\n";
// print "<input id='currPhotoId' type='hidden' name='photo_id' value='".$this->photo_id."'/>\n";
          

// print '  </div>';
  
// print '  <div class="container-fluid">';
// print '<div class="row">';
// print "<div class='col-md-9'>\n";
  
// print "<div id='species_helplet'></div>";

// print "</div>"; // col9



// print "<div class='col-md-3'>\n";

  
// foreach($this->classifyInputs as $formInput){
  // print "<div class='row'>\n";
  // print "<div class='col-md-12'>\n";
  // print "<div class='mb-2 form-group species_classify'>\n";
  // print $formInput;
  // print "</div>\n";
  // print "</div>\n";
  // print "</div> <!-- /.row -->\n";
// }

// //print '<hr/>';
// // print '<button type="button" class="btn btn-default" data-dismiss="modal">'.Text::_("COM_BIODIV_CLASSIFY_CLOSE").'</button>';
// // print '        <button type="button" class="btn btn-success" id="classify-save">'.Text::_("COM_BIODIV_CLASSIFY_SAVE").'</button>';

// print '</div>'; // col-3

// print '</div>'; // row


// print ' </div> <!-- /.container-fluid -->';
// print '     </div>'; // modal-body
// print '       <div class="modal-footer">';

// print '<button type="button" class="btn btn-default" data-dismiss="modal">'.Text::_("COM_BIODIV_CLASSIFY_CLOSE").'</button>';
// print '        <button type="button" class="btn btn-success" id="classify-save">'.Text::_("COM_BIODIV_CLASSIFY_SAVE").'</button>';

// print '       </div>'; // modal-footer
// //print '       </form>';


// print '    </div>'; // modal-content
// print '  </div>'; // modal-dialog


// print '</div>'; // modal


$mapOptions = mapOptions();
$key = $mapOptions['key'];

print '<script>
  (g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",m=document,b=window;b=b[c]||(b[c]={});var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));e.set("libraries",[...r]+"");for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);e.set("callback",c+".maps."+q);a.src=`https://maps.${c}apis.com/maps/api/js?`+e;d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)}));d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})({
    key: "'.$key.'",
    v: "weekly",
  });
</script>';

$this->document->getWebAssetManager()->usePreset('com_biodiv.classify');

HTMLHelper::_('bootstrap.modal', '.selector', []);
HTMLHelper::_('bootstrap.carousel', '#photoCarousel', []);
HTMLHelper::_('bootstrap.tab', '.tab-content', []);

?>



