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
print '  <h1>'.$this->project->project_prettyname.'</h1>';  
print '  <div>';  
print '  <h2>Can you classify 5 sequences?</h2>';      
print '  <!-- h3>Touch the button to start spotting</h3 -->';   
print '  </div>';   
//print '  <a id="start-kiosk-spotting" class="btn btn-danger" src="">Start Spotting</a>';
?>

<form action = "<?php print BIODIV_ROOT.'&'.$this->user_key;?>" method = 'GET'>
    <input type='hidden' name='view' value='kiosk'/>
    <input type='hidden' name='option' value='<?php print BIODIV_COMPONENT;?>'/>
    <input type='hidden' name='classify_only_project' value='1'/>
	<input type='hidden' name='my_project' value='<?php print $this->project->project_prettyname; ?>' />
	<button  id='start-kiosk-btn' class='btn btn-danger' type='submit'><i class='fa fa-search'></i> Start Spotting</button>
    

</form>

<!--button type='button' id='classify_help_button' class='btn btn-danger btn-block' data-toggle='modal' data-target='#help_modal'>Learn How</button -->

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


