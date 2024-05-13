<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;


print '<div class="row">';

print '<div class="col-md-6">';
print '<form action = "'.BIODIV_ROOT.'" method = "GET">';
print '    <input type="hidden" name="option" value="'.BIODIV_COMPONENT.'"/>';
print '    <input type="hidden" name="view" value="recordtop"/>';
print '    <button  class="btn btn-primary btn-xl btn-block" type="submit"><p><i class="fa fa-microphone text-warning" aria-hidden="true"></i></p><p>'.JText::_("COM_BIODIV_UPLOADTOP_RECORD_NOW").'</p></a></button>';
print '</form>';
print '</div>';

print '<div class="col-md-6">';
print '<button type="button" id="add_site" class="btn btn-primary btn-xl btn-block"><p><i class="fa fa-upload text-warning" aria-hidden="true"></i></p><p>'.JText::_("COM_BIODIV_UPLOADTOP_UPLOAD_NEW").'</p></button>';
print '</div>';

print '</div>';  // row

print '<div class="row">';

print '<div class="col-md-6">';
print '<button type="button" id="select_site" class="btn btn-primary btn-xl btn-block"  data-toggle="modal" data-target="#select_site_modal"><p><i class="fa fa-upload text-warning" aria-hidden="true"></i></p><p>'.JText::_("COM_BIODIV_UPLOADTOP_UPLOAD_EXIST").'</p></button>';
print '</div>';


print '<div class="col-md-6">';
print '<form action = "'.BIODIV_ROOT.'" method = "GET">';
print '    <input type="hidden" name="option" value="'.BIODIV_COMPONENT.'"/>';
print '    <input type="hidden" name="view" value="trapper"/>';
print '    <button  class="btn btn-primary btn-xl btn-block" type="submit"><p><i class="fa fa-edit text-warning" aria-hidden="true"></i></p><p>'.JText::_("COM_BIODIV_UPLOADTOP_MAN_SITES").'</p></button>';
print '</form>';
print '</div>';



print '</div>'; // row


?>


<?php

$this->siteHelper->generateSiteCreationModal(true);

$this->siteHelper->generateSiteSelectionModal();


?>
<?php

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
	//JHtml::_('script', 'com_biodiv/record.js', array('version' => 'auto', 'relative' => true), array());
	

?>



