<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;
?>

<?php

	$this->mediaCarousel->generateMediaCarousel($this->sequence->getId(), $this->sequence->getMediaFiles(), $this->sequence->getMedia(), $this->sequence->getMediaType());

?>


<?php
JHTML::stylesheet("com_biodiv/com_biodiv.css", array(), true);
//JHTML::stylesheet("com_biodiv/mediacarousel.css", array(), true);
JHTML::script("com_biodiv/commonclassify.js", true, true);
JHTML::script("com_biodiv/trainingtopic.js", true, true);

?>



