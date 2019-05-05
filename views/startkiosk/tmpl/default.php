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
if ( $this->photo_id ) {
  $document->addScriptDeclaration("BioDiv.curr_photo = ".$this->photo_id.";");
}
/*
if ( $this->user_key) {
  $document->addScriptDeclaration("BioDiv.user_key = ".$this->user_key.";");
}
*/

// Just want an image with a start button, at least for now.
//print '<div id="start-kiosk-jumbotron" data-project-img="'.$this->projectImageUrl.' class="jumbotron" >';
print '<div id="start-kiosk-jumbotron" class="jumbotron text-center" data-project-img="'.$this->projectImageUrl.'" >';
print '  <h1>'.$this->project->project_prettyname.'</h1>';      
//print '  <a id="start-kiosk-spotting" class="btn btn-danger" src="">Start Spotting</a>';
?>

<form action = "<?php print BIODIV_ROOT.'&'.$this->user_key;?>" method = 'GET'>
    <input type='hidden' name='view' value='kiosk'/>
    <input type='hidden' name='option' value='<?php print BIODIV_COMPONENT;?>'/>
    <input type='hidden' name='classify_only_project' value='1'/>
	<input type='hidden' name='my_project' value='<?php print $this->project->project_prettyname; ?>' />
	<input type='hidden' name='user_key' value='<?php print $this->user_key; ?>' />
	<button  id='start-kiosk-btn' class='btn btn-danger' type='submit'><i class='fa fa-search'></i> Start Spotting</button>
    
</form>



<?php
//print '<p>user key: '.$this->user_key.'</p>';
//print '<p>project_id: '.$this->project_id.'</p>';
//print '<p>project: </p>';
//print_r($this->project);
print '</div>';




JHTML::script("com_biodiv/bootbox.js", true, true);
JHTML::stylesheet("com_biodiv/com_biodiv.css", array(), true);
JHTML::script("com_biodiv/startkiosk.js", true, true);

?>


