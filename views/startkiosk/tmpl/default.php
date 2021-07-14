<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

$document = JFactory::getDocument();
//$document->addScriptDeclaration("BioDiv.next_photo = ".$this->photoDetails['next_photo'].";");
/*
if ( $this->user_key) {
  $document->addScriptDeclaration("BioDiv.user_key = ".$this->user_key.";");
}
*/

// Just want an image with a start button, at least for now.
//print '<div id="start-kiosk-jumbotron" data-project-img="'.$this->projectImageUrl.' class="jumbotron" >';
print '<div id="start-kiosk-jumbotron" class="jumbotron text-center" data-project-img="'.$this->projectImageUrl.'" data-project-id="'.$this->project_id.'" data-user-key="'.$this->user_key.'" >';
print '<div class="opaque-bg">';
print '  <h1>'.$this->project->project_prettyname.'</h1>';  
print '  <div>';  
print '  <h3>We need your help to identify animals on camera trap images. What can you see on each sequence of photos?</h3>';      
print '  <!-- h3>Touch the button to start exploring</h3 -->';   
print '  </div>'; 
print '</div>'; // opaque-bg
  

?>

<form action = "http://localhost/rhombus/en/hancock-urban-kiosk" method = 'GET'>
   <button  id='start-kiosk-btn' class='btn btn-success' type='submit'></i> Enter</button>
</form>


<?php

print '<div class="row opaque-logo-row">';
foreach ( $this->logos as $logo ) {
	print '<img src="' . $logo . '">';
}
print '</div>';

print '</div>';

JHTML::script("com_biodiv/bootbox.js", true, true);
JHTML::stylesheet("com_biodiv/com_biodiv.css", array(), true);
JHTML::script("com_biodiv/startkiosk.js", true, true);

?>


