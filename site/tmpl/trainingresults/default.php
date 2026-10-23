<?php

 
// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;


$this->helper->generateTrainingResults ();


$this->document->getWebAssetManager()->usePreset('com_biodiv.trainingresults');


HTMLHelper::_('bootstrap.modal', '.selector', []);
HTMLHelper::_('bootstrap.carousel', '#photoCarousel', []);
	
	// JHTML::stylesheet("com_biodiv/com_biodiv.css", array(), true);
// JHTML::script("com_biodiv/trainingfunctions.js", true, true);
// JHTML::script("com_biodiv/trainingresults.js", true, true);
?>



