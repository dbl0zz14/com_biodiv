<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

$document = JFactory::getDocument();
$document->addScriptDeclaration("BioDiv.loadingMsg = '".JText::_("COM_BIODIV_STATUS_LOADING")."';");

?>
<?php 
print '<h1>'.JText::_("COM_BIODIV_STATUS_SPOT_STAT").'</h1>'; 
?>
<div class='row'>
<div class='col-md-6'>
<p>
<table class="table">
<?php
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
    <button  class='btn btn-success btn-block classify_btn' type='submit'><i class='fa fa-search'></i> 
<?php 
	print JText::_("COM_BIODIV_STATUS_CLASS_ALL");  
?>
</button>
</form>
</p>
<p>
<form action = "<?php print BIODIV_ROOT;?>" method = 'GET'>
<?php print "    <input type='hidden' name='view' value='" . $classifyView . "'/>"; ?>
    <input type='hidden' name='option' value='<?php print BIODIV_COMPONENT;?>'/>
    <input type='hidden' name='classify_self' value='1'/>
    <button  class='btn btn-success btn-block classify_btn' type='submit'><i class='fa fa-search'></i> 
<?php 
print JText::_("COM_BIODIV_STATUS_CLASS_MY"); 
?>
</button>
    
</form>
</p>
<p>
<form action = "<?php print BIODIV_ROOT;?>" method = 'GET'>
<div class="input-group">
<?php print "    <input type='hidden' name='view' value='" . $classifyView . "'/>"; ?>
    <input type='hidden' name='option' value='<?php print BIODIV_COMPONENT;?>'/>
    <input type='hidden' name='classify_only_project' value='1'/>
	<select name = 'project_id' class = 'form-control'>
	  <option value="" disabled selected hidden>
<?php 
print JText::_("COM_BIODIV_STATUS_SEL_PROJ"); 
?>
...</option>
    
      <?php
        foreach($this->projects as $proj_id=>$proj){
          print "<option value='$proj_id'>$proj</option>";
        }
      ?>
    </select>
	<span class="input-group-btn">
      <button  class='btn btn-success classify_btn' type='submit'><i class='fa fa-search'></i> 
<?php 
print JText::_("COM_BIODIV_STATUS_CLASS_PROJ"); 
?>
</button>
	</span>
	
</div>
</form>
</p>
</div>
<div class="loader invisible"></div>

<div class='col-md-6'>

<?php


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
			
			print '<audio oncontextmenu="return false;" controls controlsList="nodownload"><source src="'.$photoUrl.'">' . JText::_("COM_BIODIV_STATUS_NO_AUD") . '</audio>';
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
    print '      	         <div class="col-md-10">'.JText::_("COM_BIODIV_STATUS_PARTI_INFO").'</div>';
    print '      	         <div id="show_partic_info" class="col-md-2 text-right" data-toggle="tooltip" title="'.JText::_("COM_BIODIV_STATUS_SHOW_PARTIC").'"><i class="fa fa-angle-down fa-lg"></i></div>';
    print '      	         <div id="hide_partic_info" class="col-md-2 text-right" data-toggle="tooltip" title="'.JText::_("COM_BIODIV_STATUS_HIDE_PARTIC").'"><i class="fa fa-angle-up fa-lg"></i></div>';
	print '              </div>';
	print '          </div>';
	
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
		print '<p>'.JText::_("COM_BIODIV_STATUS_ALREADY_CONSENTED").'</p>';
	}
	print $this->consentText;
	
	print '          <form id="take_survey" action = "' . BIODIV_ROOT . '" method = "GET">';
	
	if ( $this->requireSurveyConsent ) {
		
		print '          <div id="require_consent">';
		print '          <h4 id ="consent_reminder" class="text-danger">' . JText::_("COM_BIODIV_STATUS_INDICATE_CONSENT") . '</h4>';
			
		print '          <div class="checkbox">';
		print '          <label><input id="consent_checkbox" type="checkbox" name="consent" value="1">'.JText::_("COM_BIODIV_STATUS_CONSENT_TEXT") . '</label>';
		print '          </div>';
		print '          </div>';
	}
  
    print '          </div>';
	print '      </div>'; // panel
	
	print '      </div>'; // panel-group
	
	print '      </div>'; // modal body
	print '      	<div class="modal-footer">';
	print '              <input type="hidden" name="option" value="'.BIODIV_COMPONENT.'"/>';
	print '              <input type="hidden" name="task" value="take_survey"/>';
    print '              <input type="hidden" name="survey" value="'.$this->surveyId.'"/>';
	
	print '              <div class="col-md-4 col-sm-12 col-xs-12" style="margin-bottom:16px;"><button  class="btn btn-warning btn-block" type="submit">'.JText::_("COM_BIODIV_STATUS_CONTRIBUTE").'</button></div>';
	
	print '              <div class="col-md-4 col-sm-12 col-xs-12" style="margin-bottom:16px;"><button type="button" class="btn btn-danger btn-block classify-modal-button" data-dismiss="modal" >'.JText::_("COM_BIODIV_STATUS_MAYBE_LATER").'</button></div>';
	
	print '          </form>';
	print '          </div>';

	
	
	print '    </div>';

	print '  </div>';
	print '</div>';
}




JHTML::script("com_biodiv/status.js", true, true);
?>

