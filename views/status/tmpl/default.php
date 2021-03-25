<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

$document = JFactory::getDocument();
$document->addScriptDeclaration("BioDiv.loadingMsg = '".$this->translations['loading']['translation_text']."';");

?>
<h1><?php print $this->translations['spot_stat']['translation_text'] ?></h1>
<div class='row'>
<div class='col-md-6'>
<p>
<table class="table">
<?php
/*
foreach($this->status as $msg => $count){
  print "<tr><td>$msg</td><td>$count</td></tr>\n";
 }
 */
 foreach ( $this->status as $row ) {
		
	print '<tr>';
	
	foreach ( $row as $rowField ) {
		print '<td>'.$rowField.'</td>';
	}
	
	print '</tr>';
}
 
 $isCamera = getSetting("camera") == "yes";
 $classifyView = $isCamera ? "classify" : "classifybirds";
?>
</table>
</p>
<p>
<form action = "<?php print BIODIV_ROOT;?>" method = 'GET'>
    <input type='hidden' name='option' value='<?php print BIODIV_COMPONENT;?>'/>
<?php print "    <input type='hidden' name='view' value='" . $classifyView . "'/>"; ?>
    <button  class='btn btn-success btn-block classify_btn' type='submit'><i class='fa fa-search'></i> <?php print $this->translations['class_all']['translation_text'] ?></button>
</form>
</p>
<p>
<form action = "<?php print BIODIV_ROOT;?>" method = 'GET'>
<?php print "    <input type='hidden' name='view' value='" . $classifyView . "'/>"; ?>
    <input type='hidden' name='option' value='<?php print BIODIV_COMPONENT;?>'/>
    <input type='hidden' name='classify_self' value='1'/>
    <button  class='btn btn-success btn-block classify_btn' type='submit'><i class='fa fa-search'></i> <?php print $this->translations['class_my']['translation_text']?></button>
    
</form>
</p>
<p>
<form action = "<?php print BIODIV_ROOT;?>" method = 'GET'>
<div class="input-group">
<?php print "    <input type='hidden' name='view' value='" . $classifyView . "'/>"; ?>
    <input type='hidden' name='option' value='<?php print BIODIV_COMPONENT;?>'/>
    <input type='hidden' name='classify_only_project' value='1'/>
	<select name = 'project_id' class = 'form-control'>
	  <option value="" disabled selected hidden><?php print $this->translations['sel_proj']['translation_text']?>...</option>
    
      <?php
        foreach($this->projects as $proj_id=>$proj){
          print "<option value='$proj_id'>$proj</option>";
        }
      ?>
    </select>
	<span class="input-group-btn">
      <button  class='btn btn-success classify_btn' type='submit'><i class='fa fa-search'></i> <?php print $this->translations['class_proj']['translation_text']?></button>
	</span>
	
</div>
</form>
</p>
</div>
<div class="loader invisible"></div>
<?php
//print "<p><b>Projects:";
//foreach($this->projects as $project_name  ){
//  print " <span class='badge'>$project_name</span> ";
// }
//print "</b></p>\n";
?>
<div class='col-md-6'>
<!-- div id="myCarousel" class="carousel slide" data-ride="carousel"  data-wrap="false" -->
  <!-- Indicators -->
  

  <!-- Wrapper for slides -->
  <!-- div class="carousel-inner" -->
<?php
/*

$first = true;
foreach($this->mylikes as $photo_id  ){
	
	if ($first) {
		print '<div class="item active">';
		$first = false;
	}
	else {
		print '<div class="item">';
	}
	
	if ( isVideo($photo_id) ) {
		print '<video  oncontextmenu="return false;" controls controlsList="nodownload" width="100%"><source src="'.photoURL($photo_id).'" type="video/mp4">Your browser does not support the video tag.</video>';
		//print "Found video: ".photoURL($photo_id);
	}
	if ( isAudio($photo_id) ) {
		print '<audio oncontextmenu="return false;" controls controlsList="nodownload" width="100%" ><source src="'.photoURL($photo_id).'" >' . $this->translations['no_aud']['translation_text'] . '</audio>';
		//print "Found video: ".photoURL($photo_id);
	}
	else {
		print JHTML::image(photoURL($photo_id), 'Photo ' . $photo_id, array('class' =>'img-responsive'));
	}
	print '</div>';
 }
 if ( $first == true ) {
	// no likes so use a default image
/print '<div class="item active">';
	print JHTML::image(projectImageURL(1), 'Default Photo', array('class' =>'img-responsive'));
	print '</div>';
	 
 }
*/

$isCameraWebsite =  getSetting("camera") === "yes";
if ( $isCameraWebsite ) {
	if ( count($this->mylikes) > 0 ) {
		$likePhoto = $this->mylikes[0];
		$photoUrl = photoURL($likePhoto);
		$ext = strtolower(JFile::getExt($photoUrl));
		
		if ( isVideo($likePhoto) ) {
			print '<video  oncontextmenu="return false;" controls controlsList="nodownload" width="100%" data-photo-id="'.$likePhoto.'"><source src="'.$photoUrl.'" >Your browser does not support the video tag.</video>';
		}
		else if ( isAudio($likePhoto) ) {
			
			print '<audio oncontextmenu="return false;" controls controlsList="nodownload"><source src="'.$photoUrl.'">' . $this->translations['no_aud']['translation_text'] . '</audio>';
		}
		else {
			print JHTML::image($photoUrl, 'Photo ' . $likePhoto, array('class' =>'img-responsive'));
		}

		
	}
	else {
		print JHTML::image(projectImageURL(1), 'Default Photo', array('class' =>'img-responsive'));
	}
}
?>

  <!-- /div -->

  <!-- Left and right controls -->
  <?php
  /*
  if (count($this->mylikes) > 1 ) {
  print '<a class="left carousel-control" href="#myCarousel" data-slide="prev">';
  print '  <span class="glyphicon glyphicon-chevron-left"></span>';
  print '  <span class="sr-only">Previous</span>';
  print '</a>';
  print '<a class="right carousel-control" href="#myCarousel" data-slide="next">';
  print '  <span class="glyphicon glyphicon-chevron-right"></span>';
  print '  <span class="sr-only">Next</span>';
  print '</a>';
  }
  */
  ?>
<!-- /div -->
</div>

</div>



<?php

// New version using class
if ( $this->survey != null ) {
	print '<div id="survey_modal" class="modal fade" role="dialog">';
	print '  <div class="modal-dialog" style="width:95%;">';

	print '    <!-- Modal content-->';
	print '    <div class="modal-content">';
	print '      <div class="modal-header">';
	print '        <button type="button" class="close" data-dismiss="modal">&times;</button>';
	//print '        <h4 class="modal-title">'.$this->surveyHook.'</h4>';
	print '      </div>';
	print '      <div class="modal-body">';
	
	print '      <div class="panel-group">';
	
	print '      <div class="panel panel-warning">';
	print '          <div class="panel-body">';
	print $this->surveyIntro;
	print '          </div>';
	print '      </div>'; // panel
	
	print '      <div class="panel panel-warning">';
	print '          <div class="panel-heading">';
  	print '              <div class="row">';
    print '      	         <div class="col-md-10">'.$this->translations['parti_info']['translation_text'].'</div>';
    print '      	         <div id="show_partic_info" class="col-md-2 text-right" data-toggle="tooltip" title="'.$this->translations['show_partic']['translation_text'].'"><i class="fa fa-angle-down fa-lg"></i></div>';
    print '      	         <div id="hide_partic_info" class="col-md-2 text-right" data-toggle="tooltip" title="'.$this->translations['hide_partic']['translation_text'].'"><i class="fa fa-angle-up fa-lg"></i></div>';
	print '              </div>';
	print '          </div>';
	
	//print '          <div class="panel-body" style="display:none;">';
	print '          <div id="partic_info" class="panel-body">';
	print $this->participantInfo;
	print '          </div>';
	print '      </div>'; //panel
	
	print '      <div class="panel panel-warning">';
	print '          <div class="panel-heading">';
  	print            $this->consentHeading;
	print '          </div>';
	
	print '          <div class="panel-body">';
	
	if ( $this->requireSurveyConsent ) {
		print $this->consentInstructions;
	}
	else {
		print '<p>'.$this->translations['already_consented']['translation_text'].'</p>';
	}
	print $this->consentText;
	
	print '          <form id="take_survey" action = "' . BIODIV_ROOT . '" method = "GET">';
	
	if ( $this->requireSurveyConsent ) {
		
		print '          <div id="require_consent">';
		print '          <h4 id ="consent_reminder" class="text-danger">' . $this->translations['indicate_consent']['translation_text'] . '</h4>';
			
		print '          <div class="checkbox">';
		print '          <label><input id="consent_checkbox" type="checkbox" name="consent" value="1">'.$this->translations['consent_text']['translation_text'] . '</label>';
		print '          </div>';
		print '          </div>';
	}
  
    print '          </div>';
	print '      </div>'; // panel
	
	print '      </div>'; // panel-group
	
	print '      </div>'; // modal body
	print '      	<div class="modal-footer">';
	//print '          <form id="take_survey" action = "' . BIODIV_ROOT . '" method = "GET">';
	print '              <input type="hidden" name="option" value="'.BIODIV_COMPONENT.'"/>';
	print '              <input type="hidden" name="task" value="take_survey"/>';
    print '              <input type="hidden" name="survey" value="'.$this->surveyId.'"/>';
	print '              <div class="col-md-4 col-sm-12 col-xs-12" style="margin-bottom:16px;"><button  class="btn btn-warning btn-block" type="submit">'.$this->translations['contribute']['translation_text'].'</button></div>';
	print '              <div class="col-md-4 col-sm-12 col-xs-12" style="margin-bottom:16px;"><button type="button" class="btn btn-danger btn-block classify-modal-button" data-dismiss="modal" >'.$this->translations['maybe_later']['translation_text'].'</button></div>';
	print '              <div class="col-md-4 col-sm-12 col-xs-12" style="margin-bottom:16px;"><button id="no_survey" type="button" class="btn btn-danger btn-block classify-modal-button" data-dismiss="modal" data-survey-id="'.$this->surveyId.'">'.$this->translations['no_survey']['translation_text'].'</button></div>';
	print '          </form>';
	print '          </div>';

	
	//print '      <div class="modal-footer">';
	//print '        <button type="button" class="btn btn-danger classify-modal-button" data-dismiss="modal">'.$this->translations['not_now']['translation_text'].'</button>';
	//print '      </div>';
	print '    </div>';

	print '  </div>';
	print '</div>';
}




JHTML::script("com_biodiv/status.js", true, true);
?>

