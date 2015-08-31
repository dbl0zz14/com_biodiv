<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;


if(!$this->photo_id){
  die("No photo_id specified");
 }
?>
<h1>Spotted on mammal web</h1>
<div>
<?php
print JHTML::image(photoURL($this->photo_id), 'Photo ' . $this->photo_id, array('class' =>'img-responsive'));
?>
</div>
<p class='lead'>Why not <a href='/'>start spotting?</a></p>
