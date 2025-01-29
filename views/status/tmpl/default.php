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
		$excludeProjects = array(380,183,381);
		foreach($this->projects as $proj_id=>$proj){
			if ( in_array($proj_id , $excludeProjects) ) {
                continue;
			}
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

BiodivSurvey::generateSurveyModal();


JHTML::script("com_biodiv/status.js", true, true);
JHTML::script("com_biodiv/triggersurvey.js", true, true);

?>

