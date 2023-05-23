<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;


if ( !$this->personId ) {
	// Please log in button
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_BADGECOMPLETE_LOGIN").'</div>';
	
}

else if ( $this->schoolUser ) {
	
	print '<div class="row">';

	print '<div class="col-md-12">';
	
	//print '<div class="panel badgesPanel">';
	
	print '<div id="uploadFiles">';
	
	$this->badge->printBadgeComplete();
	
	print '<form id="badgeUploadForm">';
	
	print '<div id="resourceMeta_1" class="metaPage">';

	print '<div class="vSpaced">';
	print '<h3>'.JText::_("COM_BIODIV_BADGECOMPLETE_WELL_DONE").'</h3>';
	//print '<h3>'.JText::_("COM_BIODIV_BADGECOMPLETE_UPLOAD").'</h3>';
	print '</div>';
	
	
	$schoolId = $this->schoolUser->school_id;
	$schoolRoleId = $this->schoolUser->role_id;
	
	$resourceTypeId = codes_getCode ( "Task", "resource" );
	
	print "<input type='hidden' name='school' value='" . $schoolId . "'/>";
	print "<input type='hidden' name='resourceType' value='" . $resourceTypeId . "'/>";
	print "<input type='hidden' name='badge' value='" . $this->badgeId . "'/>";
	print "<input type='hidden' name='classId' value='" . $this->classId . "'/>";
	print "<input type='hidden' name='uploadName' value='" . $this->badgeName . "'/>";
	print "<input type='hidden' name='source' value='user'/>";
	//print "<input type='hidden' name='tags' value='[".$this->moduleTagId."]'/>";
	
	
	// Describe the upload
	print '<div class="vSpaced">';
	
	print '<label for="uploadDescription"><h4>'.JText::_("COM_BIODIV_BADGECOMPLETE_UPLOAD_DESC").'</h4></label>';
	print '<textarea id="uploadDescription" name="uploadDescription"></textarea>';
	print '<div id="uploadDescriptionCount" class="text-right" data-maxchars="'.Biodiv\ResourceFile::MAX_DESC_CHARS.'">0/'.Biodiv\ResourceFile::MAX_DESC_CHARS.'</div>';
		
	print '</div>';
	
	print '<div class="vSpaced">';
	
	print '<h4>'.JText::_("COM_BIODIV_BADGECOMPLETE_UPLOAD").'</h4>';
	
	print '<div id="resourceNext_1" class="btn btn-primary btn-lg resourceNextBtn"  >';
	print JText::_("COM_BIODIV_BADGECOMPLETE_CREATE_SET");
	print '</div>'; 
			
	//print '<button type="button" class="btn btn-primary btn-lg spaced">'.JText::_("COM_BIODIV_BADGECOMPLETE_CREATE_SET").'</button>';
	
	print '<button type="button" id="doneNoFiles_'.$this->badgeId.'" class="btn btn-default btn-lg vSpaced doneNoFiles">'.JText::_("COM_BIODIV_BADGECOMPLETE_NO_FILES").'</button>';
	
	
	print '<button type="button" class="btn btn-default btn-lg vSpaced hSpaced reloadPage">'.JText::_("COM_BIODIV_BADGECOMPLETE_CANCEL").'</button>';
	
	print '</div>'; // vSpaced
	print '</div>'; // resourceMeta_1
	
	print '<div id="resourceMeta_2" class="metaPage" style="display:none;">';
	
	if ( $this->schoolUser->role_id == Biodiv\SchoolCommunity::TEACHER_ROLE ) {
		print '<div class="vSpaced" >';
		print '<input type="checkbox" id="post" name="post" value="1" >';
		print '<label for="post" class="uploadLabel">'.\JText::_("COM_BIODIV_BADGECOMPLETE_POST").'</label>';
		print '</div>'; // vSpaced
	}
		
	print '<div id="resourceBack_2" class="btn btn-default btn-lg resourceBackBtn"  >';
	print \JText::_("COM_BIODIV_BADGECOMPLETE_BACK");
	print '</div>'; // resourceBack
		
	print '<button type="submit" id="readytoupload" class="btn btn-primary btn-lg chooseFiles resourceNextBtn">'.JText::_("COM_BIODIV_BADGECOMPLETE_UPLOAD_NOW").'</button>';
	
	print '</div>'; // resourceMeta_2
	
	print '</form>';
	
	print '</div>';  // uploadFiles
	
	// '</div>'; //panel
	
	print '</div>'; // col-12

	print '</div>'; // row
	
	
	
	

}

?>