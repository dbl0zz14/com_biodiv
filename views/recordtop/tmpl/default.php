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


	$mapOptions = mapOptions();
	$key = $mapOptions['key'];
	print '<script>
	  (g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",m=document,b=window;b=b[c]||(b[c]={});var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));e.set("libraries",[...r]+"");for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);e.set("callback",c+".maps."+q);a.src=`https://maps.${c}apis.com/maps/api/js?`+e;d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)}));d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})({
		key: "'.$key.'",
		v: "weekly",
	  });
	</script>';
	
	JHtml::_('script', 'com_biodiv/mapupdate.js', array('version' => 'auto', 'relative' => true), array());
	JHtml::_('script', 'com_biodiv/mapselect.js', array('version' => 'auto', 'relative' => true), array());
	JHtml::_('script', 'com_biodiv/geodesy-master/vector3d.js', array('version' => 'auto', 'relative' => true), array());
	JHtml::_('script', 'com_biodiv/geodesy-master/latlon-ellipsoidal.js', array('version' => 'auto', 'relative' => true), array());
	JHtml::_('script', 'com_biodiv/geodesy-master/osgridref.js', array('version' => 'auto', 'relative' => true), array());
	JHtml::_('script', 'com_biodiv/geodesy-master/dms.js', array('version' => 'auto', 'relative' => true), array());
	JHtml::_('stylesheet', 'bootstrap3-editable/bootstrap-editable.css', array('version' => 'auto', 'relative' => true), array());
	JHtml::_('script', 'bootstrap3-editable/bootstrap-editable.js', array('version' => 'auto', 'relative' => true), array());
	JHtml::_('script', 'com_biodiv/trapper.js', array('version' => 'auto', 'relative' => true), array());
	JHtml::_('script', 'com_biodiv/record.js', array('version' => 'auto', 'relative' => true), array());
	

}

?>



