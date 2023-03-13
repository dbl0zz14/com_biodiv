<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;



print '<h4>'.JText::_("COM_BIODIV_NEWRESOURCESET_TAP_BUTTON").'</h4>';

if ( $this->isPost ) {
	print '<h4>'.JText::_("COM_BIODIV_NEWRESOURCESET_FILES").'</h4>';
}
else if ( !$this->isBadge ) {
	print '<h4>'.JText::_("COM_BIODIV_NEWRESOURCESET_ADD_MORE").'</h4>';
}

print '<div id="resourceSet" data-set_id="'.$this->newSetId.'" data-badge_id="'.$this->badgeId.'"></div>';
print '<div class="row">';

print '<div class="col-md-12">';

print '<button id="resourceuploader" >'.JText::_("COM_BIODIV_NEWRESOURCESET_CHOOSE_FILES").'</button>';
print '<div id="fileuploadspinner"  style="display:none"><i class="fa fa-spinner fa-spin fa-4x"></i></div>';


print '</div>'; // col-md-12


print '</div>'; // row

?>