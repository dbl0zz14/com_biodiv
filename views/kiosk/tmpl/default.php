<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

// Add variables for use by the Javascript
$document = JFactory::getDocument();
$document->addScriptDeclaration("BioDiv.kiosk = '".$this->kiosk."';");

print '	<button id="home_button" class="btn btn-success h2" data-dismiss="modal"> <span class="fa fa-3x fa-home"> </button>';

if ( $this->isSchoolUser and $this->logoPath ) {
	
	print '<div id="besButton"><a href="'.JText::_("COM_BIODIV_KIOSK_BES_LINK").'" class="noLineLink"><button class="btn btn-default h3" ><img src="'.$this->logoPath.'" class="img-responsive besButtonImg" /></button></a></div>';

}


print '<div id="kiosk"></div>';

//print '<div id="discovermap" style="width:100%; height:500px;"></div>';


JHtml::_('script', 'com_biodiv/commonbiodiv.js', array('version' => 'auto', 'relative' => true), array());
JHtml::_('script', 'com_biodiv/mediacarousel.js', array('version' => 'auto', 'relative' => true), array());
JHtml::_('script', 'com_biodiv/kioskcommon.js', array('version' => 'auto', 'relative' => true), array());
JHtml::_('script', 'com_biodiv/kiosk.js', array('version' => 'auto', 'relative' => true), array());
JHtml::_('script', 'com_biodiv/kioskclassify.js', array('version' => 'auto', 'relative' => true), array());
JHtml::_('script', 'com_biodiv/kioskquiz.js', array('version' => 'auto', 'relative' => true), array());
JHtml::_('script', 'com_biodiv/kiosklearn.js', array('version' => 'auto', 'relative' => true), array());
JHtml::_('script', 'com_biodiv/kiosktutorial.js', array('version' => 'auto', 'relative' => true), array());
JHtml::_('script', 'com_biodiv/kioskabout.js', array('version' => 'auto', 'relative' => true), array());

JHtml::_('script', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js', array(), array());

JHtml::_('stylesheet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', array(), array('integrity'=>'sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=', 'crossorigin'=>''));

JHtml::_('script', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), array('integrity'=>'sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=', 'crossorigin'=>''));


JHtml::_('script', 'com_biodiv/discovermap.js', array('version' => 'auto', 'relative' => true), array());
JHtml::_('script', 'com_biodiv/kioskmap.js', array('version' => 'auto', 'relative' => true), array());


?>


