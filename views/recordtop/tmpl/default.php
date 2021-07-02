<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

/*
if ( !$this->personId ) {
	print '<div class="col-md-4">';
	print '<form action = "'.BIODIV_ROOT.'" method = "GET">';
	print '    <input type="hidden" name="option" value="'.BIODIV_COMPONENT.'"/>';
	print '    <input type="hidden" name="view" value="recordtop"/>';
	print '    <button class="btn btn-info btn-xl btn-block btn-wrap-text" type="submit" style="font-size:30px;">'.$this->translations['login']['translation_text'].'</button>';
	print '</form>';
	print '</div>';
}
*/
if ( !$this->personId ) {
	print '<a type="button" href="'.JURI::root().'/'.$this->translations['record_page']['translation_text'].'" class="btn btn-info btn-xl btn-block btn-wrap-text" >'.$this->translations['login']['translation_text'].'</a>';
}

else {


	$document = JFactory::getDocument();
	$document->addScriptDeclaration("BioDiv.maxClipLength = ".$this->maxClipLength.";");
	  

	print '<div id="record_text">';
	print '<h2>' . $this->translations['record_heading']['translation_text']. '</h2>';
	print '<h4>' . $this->translations['record_info']['translation_text']. '</h4>';
	print '</div>';

	print '<div id="upload_text" style="display:none;">';
	print '<h2>' . $this->translations['upload_heading']['translation_text']. '</h2>';
	print '<h4>' . $this->translations['upload_info']['translation_text']. '</h4>';
	print '</div>';

	print '<div id="not_supported" style="display:none;">';
	print '<h2>' . $this->translations['no_support']['translation_text']. '</h2>';
	print '<h4>' . $this->translations['use_app']['translation_text']. '</h4>';
	print '</div>';





	print '<div class="row">';

	print '<div class="col-md-4 text-center">';

	//print '<button id="start_recording" class="btn btn-success btn-xl" >'.$this->translations['start']['translation_text'].'</button>';

	print '<button id="start_recording" class="btn" style="color:red; border-radius:50%; margin-top:2vh;"><i class="fa fa-circle fa-4x" aria-hidden="true"></i></button>';

	//print '<button id="end_recording" class="btn btn-success btn-xl" style="display:none;">'.$this->translations['end']['translation_text'].'</button>';

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

	print '<div class="col-md-4"><button id="upload_to_new" class="btn btn-success upload_button btn-xl btn-block btn-wrap-text" style="display:none; font-size:30px;">'.$this->translations['upload_new']['translation_text'].'</button></div>';

	print '</div>';  // row



	print '<div class="row">';

	print '<div class="col-md-4"><button id="upload_to_existing" class="btn btn-success btn-xl upload_button btn-block btn-wrap-text" style="display:none; font-size:30px;">'.$this->translations['upload_existing']['translation_text'].'</button></div>';

	print '</div>';  // row



	print '<div class="row">';

	print '<div id="uploaded" ></div>';

	print '</div>';  // row



	print '<div class="row">';

	print '<div class="col-md-4"><a id="download" class="btn btn-info btn-xl btn-block btn-wrap-text" download="" href="" style="display:none; font-size:30px;">'.$this->translations['download']['translation_text'].'</a></div>';

	print '</div>';  // row



	print '<div class="row">';

	print '<div class="col-md-4">';
	print '<form action = "'.BIODIV_ROOT.'" method = "GET">';
	print '    <input type="hidden" name="option" value="'.BIODIV_COMPONENT.'"/>';
	print '    <input type="hidden" name="view" value="uploadtop"/>';
	print '    <button class="btn btn-primary btn-xl btn-block btn-wrap-text" type="submit" style="font-size:30px;">'.$this->translations['cancel']['translation_text'].'</button>';
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



