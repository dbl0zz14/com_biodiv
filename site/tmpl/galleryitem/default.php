<?php

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

 
// No direct access to this file
defined('_JEXEC') or die;

$this->document->getWebAssetManager()->usePreset('com_biodiv.galleryitem');

if ( $this->sequence ) {
	$this->mediaCarousel->generateMediaCarousel($this->sequence);
	
	print '<p><textarea id="shareText">'.Text::_("COM_BIODIV_GALLERYITEM_ENTER_TEXT").'</textarea></p>';
	print '<p><button id="shareBtn" class="btn btn-outline-primary">'.Text::_("COM_BIODIV_GALLERYITEM_SHARE").'</button>';
	print '<button id="copyBtn" class="btn btn-outline-primary">'.Text::_("COM_BIODIV_GALLERYITEM_COPY_LINK").'</button></p>';
}
else {
	print '<p>'.Text::_("COM_BIODIV_GALLERYITEM_NO_ACCESS").'</p>';
}	

//HTMLHelper::_('bootstrap.modal', '.selector', []);

HTMLHelper::_('bootstrap.carousel', '#photoCarousel', []);


?>



