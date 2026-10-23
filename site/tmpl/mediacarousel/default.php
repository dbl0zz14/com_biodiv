<?php

use Joomla\CMS\HTML\HTMLHelper;

 
// No direct access to this file
defined('_JEXEC') or die;

$this->mediaCarousel->generateMediaCarousel($this->sequence);


//$this->document->getWebAssetManager()->usePreset('com_biodiv.mediacarousel');

HTMLHelper::_('bootstrap.carousel', '#photoCarousel', []);

// JHTML::stylesheet("com_biodiv/com_biodiv.css", array(), true);
// JHTML::script("com_biodiv/commonclassify.js", true, true);
// JHTML::script("com_biodiv/trainingtopic.js", true, true);

?>



