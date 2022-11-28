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

printAdminMenu("PROJECT");

print '<div id="j-main-container" class="span10 j-toggle-main">';

print '<h2>Edit project: '.$this->project->project_prettyname.' ( id = '.$this->projectId.' )</h2>';


print '<form id="createProject" class="biodivForm" action = "'.BIODIV_ADMIN_ROOT.'&task=editproject" method = "POST">';

printProjectForm( $this->projectId );

print '<p>';
print '<input class="btn btn-primary projectSave" type="submit" value="Save changes"  style="display:none"/>';
print '</p>';

print '</form>';

print '<div id="editProjectMsg"></div>';

print '</div>';

echo JHtml::_('form.token'); 



JHTML::script("com_biodiv/admin.js", true, true);

?>
