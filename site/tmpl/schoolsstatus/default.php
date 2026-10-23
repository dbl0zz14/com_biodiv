<?php

 
// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use MammalWeb\Component\Biodiv\Site\Library\Core\Biodiv;
use MammalWeb\Component\Biodiv\Site\Library\Core\BiodivSurvey;
use MammalWeb\Component\Biodiv\Site\Library\Core\Project;

//$document = Factory::getDocument();
$this->document->addScriptDeclaration("BioDiv.loadingMsg = '".Text::_("COM_BIODIV_SCHOOLSSTATUS_LOADING")."';");

$this->document->getWebAssetManager()->usePreset('com_biodiv.schoolsstatus');


print '<h1>'.Text::_("COM_BIODIV_SCHOOLSSTATUS_SPOT_STAT").'</h1>'; 

print '<div class="loader invisible"></div>';

print '<div class="container">';
print '<div class="row">';
print '<div class="col-md-6">';
//print '<p>';
print '<table class="table">';

foreach ( $this->status as $row ) {
		
	print '<tr>';
	
	foreach ( $row as $rowField ) {
		print '<td>'.$rowField.'</td>';
	}
	
	print '</tr>';
}
 
print '</table>';
//print '</p>';
print '<div class="d-flex flex-column mb-3">';
//print '<div class="d-grid gap-2">';
print '<div class="mb-2">';
print '<form action = "'.Text::_("COM_BIODIV_SCHOOLSSTATUS_CLASSIFY_PAGE").'" method = "GET">';
print HTMLHelper::_('form.token');
print '    <button  class="btn btn-outline-primary btn-block classify_btn" type="submit"><i class="fa fa-search"></i> ';
print Text::_("COM_BIODIV_SCHOOLSSTATUS_CLASS_ALL");  
print '</button>';
print '</form>';
print '</div>';

if ( $this->mySchoolProjectId ) {
	print '<div class="mb-2">';
	print '<form action = "'.Text::_("COM_BIODIV_SCHOOLSSTATUS_CLASSIFY_PAGE").'" method = "GET">';
	print HTMLHelper::_('form.token');
	print '    <input type="hidden" name="classify_only_project" value="1"/>';
	print '    <input type="hidden" name="project_id" value="'.$this->mySchoolProjectId.'"/>';
	print '    <button  class="btn btn-outline-primary btn-block classify_btn" type="submit"><i class="fa fa-search"></i> ';
	print Text::_("COM_BIODIV_SCHOOLSSTATUS_CLASS") . " " . $this->mySchoolProjectName;  
	print '</button>';
	print '</form>';
	print '</div>';
}
if ( $this->featuredProject ) {
	print '<div class="mb-2">';
	print '<form action = "'.Text::_("COM_BIODIV_SCHOOLSSTATUS_CLASSIFY_PAGE").'" method = "GET">';
	print HTMLHelper::_('form.token');
	print '    <input type="hidden" name="classify_only_project" value="1"/>';
	print '    <input type="hidden" name="project_id" value="'.$this->featuredProject->id.'"/>';
	print '    <button  class="btn btn-outline-primary btn-block classify_btn" type="submit"><i class="fa fa-search"></i> ';
	print Text::_("COM_BIODIV_SCHOOLSSTATUS_CLASS") . " " . $this->featuredProject->name;
	print '</button>';
	print '</form>';
	print '</div>';
}
print '<div class="mb-2">';
print '<form action = "'.Text::_("COM_BIODIV_SCHOOLSSTATUS_CLASSIFY_PAGE").'" method = "GET">';
print HTMLHelper::_('form.token');
print '<div class="input-group">';
print '    <input type="hidden" name="classify_only_project" value="1"/>';
print '	<select name = "project_id" class = "form-control">';
print '	  <option value="" disabled selected hidden>';
print Text::_("COM_BIODIV_SCHOOLSSTATUS_SEL_PROJ"); 
print '...</option>';
    
$excludeProjects = array(380,183,381);
foreach($this->projects as $proj_id=>$proj){
	if ( in_array($proj_id , $excludeProjects) ) {
		continue;
	}
	print "<option value='$proj_id'>$proj</option>";
}
   
print '    </select>';
print '	<span class="input-group-btn">';
print '      <button  class="btn btn-primary classify_btn" type="submit"><i class="fa fa-search"></i> ';
print Text::_("COM_BIODIV_SCHOOLSSTATUS_CLASS_PROJ"); 
print '</button>';
print '	</span>';
	
print '</div>'; // input group
print '</form>';
print '</div>';

print '</div>'; // button grouping
//print '</p>';
print '</div>'; // col-md-6

print '<div class="col-md-6">';

if ( $this->isCameraWebsite ) {
	if ( count($this->mylikes) > 0 ) {
		$likePhoto = $this->mylikes[0];
		$photoUrl = $this->spotterModel->photoURL($likePhoto);
		
		if ( $this->spotterModel->isVideo($likePhoto) ) {
			print '<video  oncontextmenu="return false;" controls controlsList="nodownload" width="100%" data-photo-id="'.$likePhoto.'"><source src="'.$photoUrl.'" >Your browser does not support the video tag.</video>';
		}
		else if ( $this->spotterModel->isAudio($likePhoto) ) {
			
			print '<audio oncontextmenu="return false;" controls controlsList="nodownload"><source src="'.$photoUrl.'">' . Text::_("COM_BIODIV_SCHOOLSSTATUS_NO_AUD") . '</audio>';
		}
		else {
			print HTMLHelper::image($photoUrl, 'Photo ' . $likePhoto, array('class' =>'img-responsive'));
		}

		
	}
	else {
		print HTMLHelper::image($this->spotterModel->projectImageURL(1), 'Default Photo', array('class' =>'img-responsive'));
	}
}

  
print '</div>';

print '</div>'; // row

print '</div>'; // container


$this->spotterModel->generateSurveyModal();


// JHTML::script("com_biodiv/status.js", true, true);
// JHTML::script("com_biodiv/triggersurvey.js", true, true);

?>

