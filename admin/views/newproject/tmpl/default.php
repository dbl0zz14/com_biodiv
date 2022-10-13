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

printAdminMenu("NEWPROJECT");

//print '<a href="?option=com_biodiv"><button type="button">MammalWeb Admin Home</button></a>';

print '<div id="j-main-container" class="span10 j-toggle-main">';

print '<h2>New project</h2>';

print '<p>';
print 'Please upload a project image before continuing (usually into the images/projects folder in Content->Media).  You can add this later if you do not have an image yet.';
print '</p>';
print '<p>';
print '<a href="?option=com_media"><button type="button" class="btn hasTooltip js-stools-btn-clear" title="Cancel and upload image" data-original-title="Cancel and upload image">Navigate to Content->Media</button></a>';
print '</p>';


print '<form id="createProject" class="biodivForm" action = "'.BIODIV_ADMIN_ROOT.'&task=createproject" method = "POST">';

printProjectForm();

print '<div class="projectSave"  style="display:none">';
print '<div class="span1"><input class="btn btn-primary" type="submit" value="Create project"/></div>';
print '</div>';

print '</form>';

print '<div id="newProjectMsg"></div>';
// print '<div id="newUsers"></div>';

print '</div>';

//print '<input type="hidden" name="task" value="createusers"/>';
echo JHtml::_('form.token'); 



JHTML::script("com_biodiv/admin.js", true, true);

?>
