<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;


if ( !$this->personId ) {
	print '<a type="button" href="'.JURI::root().'/'.JText::_("COM_BIODIV_RECORDTOP_RECORD_PAGE").'" class="btn btn-info btn-xl btn-block btn-wrap-text" >'.JText::_("COM_BIODIV_RECORDTOP_LOGIN").'</a>';
}

else {


	$document = JFactory::getDocument();
	$document->addScriptDeclaration("BioDiv.maxClipLength = ".$this->maxClipLength.";");
	  

	print '<div id="record_text">';
	print '<h2>' . JText::_("COM_BIODIV_RECORDTOP_RECORD_HEADING"). '</h2>';
	print '<h4>' . JText::_("COM_BIODIV_RECORDTOP_RECORD_INFO"). '</h4>';
	print '</div>';

	print '<div id="upload_text" style="display:none;">';
	print '<h2>' . JText::_("COM_BIODIV_RECORDTOP_UPLOAD_HEADING"). '</h2>';
	print '<h4>' . JText::_("COM_BIODIV_RECORDTOP_UPLOAD_INFO"). '</h4>';
	print '</div>';

	print '<div id="not_supported" style="display:none;">';
	print '<h2>' . JText::_("COM_BIODIV_RECORDTOP_NO_SUPPORT"). '</h2>';
	print '<h4>' . JText::_("COM_BIODIV_RECORDTOP_USE_APP"). '</h4>';
	print '</div>';





	print '<div class="row">';

	print '<div class="col-md-4 text-center">';

	print '<button id="start_recording" class="btn" style="color:red; border-radius:50%; margin-top:2vh;"><i class="fa fa-circle fa-4x" aria-hidden="true"></i></button>';

	print '<button id="end_recording" class="btn" style="display:none; color:red; border-radius:50%; margin-top:2vh;"><i class="fa fa-stop-circle fa-4x" aria-hidden="true"></i></button>';

	print '</div>';

	print '</div>';  // row



	print '<div class="row">';

	print '<div id="counter" class="col-md-4 text-center" style="margin-top:2vh;"></div>';

	print '</div>';  // row



	print '<div class="row">';

	print '<div id="preview" class="col-md-4 text-center" style="margin-top:1vh; margin-bottom:1vh;"></div>';

	print '</div>';  // row



	print '<div class="row">';

	print '<div class="col-md-4"><button id="upload_to_new" class="btn btn-success upload_button btn-xl btn-block btn-wrap-text" style="display:none; font-size:30px;">'.JText::_("COM_BIODIV_RECORDTOP_UPLOAD_NEW").'</button></div>';

	print '</div>';  // row



	print '<div class="row">';

	print '<div class="col-md-4"><button id="upload_to_existing" class="btn btn-success btn-xl upload_button btn-block btn-wrap-text" style="display:none; font-size:30px;">'.JText::_("COM_BIODIV_RECORDTOP_UPLOAD_EXISTING").'</button></div>';

	print '</div>';  // row



	print '<div class="row">';

	print '<div id="uploaded" ></div>';

	print '</div>';  // row



	print '<div class="row">';

	print '<div class="col-md-4"><a id="download" class="btn btn-info btn-xl btn-block btn-wrap-text" download="" href="" style="display:none; font-size:30px;">'.JText::_("COM_BIODIV_RECORDTOP_DOWNLOAD").'</a></div>';

	print '</div>';  // row



	print '<div class="row">';

	print '<div class="col-md-4">';
	print '<form action = "'.BIODIV_ROOT.'" method = "GET">';
	print '    <input type="hidden" name="option" value="'.BIODIV_COMPONENT.'"/>';
	print '    <input type="hidden" name="view" value="uploadtop"/>';
	print '    <button class="btn btn-primary btn-xl btn-block btn-wrap-text" type="submit" style="font-size:30px;">'.JText::_("COM_BIODIV_RECORDTOP_CANCEL").'</button>';
	print '</form>';
	print '</div>';

	print '</div>';  // row
	
	// Circle of doom
	print '<div class="loader invisible"></div>';


	$this->siteHelper->generateSiteCreationModal(true);

	$this->siteHelper->generateSiteSelectionModal();


	JHTML::script("https://maps.googleapis.com/maps/api/js?key=AIzaSyAEq1lqv5U0cu2NObRiHnSlbkkynsiRcHY"); // Live site or test env
	//JHTML::script("https://maps.googleapis.com/maps/api/js?key="); // For dev
	JHTML::script("com_biodiv/geodesy-master/vector3d.js", true, true);
	JHTML::script("com_biodiv/geodesy-master/latlon-ellipsoidal.js", true, true);
	JHTML::script("com_biodiv/geodesy-master/osgridref.js", true, true);
	JHTML::script("com_biodiv/geodesy-master/dms.js", true, true);
	JHTML::stylesheet("com_biodiv/com_biodiv.css", array(), true);
	JHTML::script("com_biodiv/trapper.js", true, true);
	JHTML::script("com_biodiv/mapselect.js", true, true);

	JHTML::script("com_biodiv/record.js", true, true);
}

?>



