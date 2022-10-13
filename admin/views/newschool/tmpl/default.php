<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

printAdminMenu("NEWSCHOOL");


print '<div id="j-main-container" class="span10 j-toggle-main">';

print '<h2>New school</h2>';

print '<p>';
print 'Please upload a school image before continuing (usually into the images/projects/BES/schools folder in Content->Media).  You can add this later if you do not have an image yet.';
print '</p>';
print '<p>';
print '<a href="?option=com_media"><button type="button" class="btn hasTooltip js-stools-btn-clear" title="Cancel and upload image" data-original-title="Cancel and upload image">Navigate to Content->Media</button></a>';
print '</p>';


print '<form id="createSchool" class="biodivForm" action = "'.BIODIV_ADMIN_ROOT.'&task=createschool" method = "POST">';

printSchoolForm();

print '<p>';
print '<input class="btn btn-primary schoolSave" type="submit" value="Create school" />';
print '</p>';

print '</form>';

print '<div id="newSchoolMsg"></div>';
// print '<div id="newUsers"></div>';

print '</div>';

//print '<input type="hidden" name="task" value="createusers"/>';
echo JHtml::_('form.token'); 



JHTML::script("com_biodiv/admin.js", true, true);

?>
