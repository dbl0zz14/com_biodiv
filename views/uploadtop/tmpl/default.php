<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

//print '<h1>' . $this->translations['what_do']['translation_text']. '</h1>';
//print '<h5></h5>';

//print '<div class="jumbotron jumbotron-primary">';

print '<div class="row">';

print '<div class="col-md-6">';
print '<button type="button" id="add_site" class="btn btn-primary btn-xl btn-block"><p><i class="fa fa-upload text-warning" aria-hidden="true"></i></p><p>'.$this->translations['upload_new']['translation_text'].'</p></button>';
print '</div>';

print '<div class="col-md-6">';
print '<button type="button" id="select_site" class="btn btn-primary btn-xl btn-block"  data-toggle="modal" data-target="#select_site_modal"><p><i class="fa fa-upload text-warning" aria-hidden="true"></i></p><p>'.$this->translations['upload_exist']['translation_text'].'</p></button>';
print '</div>';

/*
print '<div class="col-md-6">';
print '<form action = "'.BIODIV_ROOT.'" method = "GET">';
print '    <input type="hidden" name="option" value="'.BIODIV_COMPONENT.'"/>';
print '    <input type="hidden" name="view" value="trapper"/>';
print '    <button  class="btn btn-primary btn-xl btn-block" type="submit"><p><i class="fa fa-upload text-warning" aria-hidden="true"></i></p><p>'.$this->translations['upload_exist']['translation_text'].'</p></a></button>';
print '</form>';
print '</div>';
*/
print '</div>';  // row

print '<div class="row">';

print '<div class="col-md-6">';
print '<form action = "'.BIODIV_ROOT.'" method = "GET">';
print '    <input type="hidden" name="option" value="'.BIODIV_COMPONENT.'"/>';
print '    <input type="hidden" name="view" value="trapper"/>';
print '    <button  class="btn btn-primary btn-xl btn-block" type="submit"><p><i class="fa fa-edit text-warning" aria-hidden="true"></i></p><p>'.$this->translations['man_sites']['translation_text'].'</p></a></button>';
print '</form>';
print '</div>';

print '</div>'; // row


?>


</ >
<?php
//"<button type='button' id='species_select_${species_id}' class='btn $btnClass btn-block btn-wrap-text species-btn-large species_select' data-toggle='modal' data-target='#classify_modal'>".$imageText.
$this->siteHelper->generateSiteCreationModal();

$this->siteHelper->generateSiteSelectionModal();


?>
<?php
JHTML::script("https://maps.googleapis.com/maps/api/js?key=AIzaSyAEq1lqv5U0cu2NObRiHnSlbkkynsiRcHY"); // Live site or test env
//JHTML::script("https://maps.googleapis.com/maps/api/js?key="); // For dev
JHTML::script("com_biodiv/geodesy-master/vector3d.js", true, true);
JHTML::script("com_biodiv/geodesy-master/latlon-ellipsoidal.js", true, true);
JHTML::script("com_biodiv/geodesy-master/osgridref.js", true, true);
JHTML::script("com_biodiv/geodesy-master/dms.js", true, true);
JHTML::stylesheet("com_biodiv/com_biodiv.css", array(), true);
JHTML::script("com_biodiv/trapper.js", true, true);
JHTML::script("com_biodiv/mapselect.js", true, true);
?>



