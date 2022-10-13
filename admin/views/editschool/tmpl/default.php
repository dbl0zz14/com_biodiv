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

printAdminMenu("SCHOOL");


print '<div id="j-main-container" class="span10 j-toggle-main">';

print '<h2>Edit school: '.$this->school->name.' ( id = '.$this->schoolId.' )</h2>';


print '<form id="editSchool" class="biodivForm" action = "'.BIODIV_ADMIN_ROOT.'&task=editschool" method = "POST">';

printSchoolForm( $this->schoolId );

print '<p>';
print '<input type="submit" class="btn btn-primary" value="Save changes"/>';
print '</p>';

print '</form>';

print '<div id="editSchoolMsg"></div>';

print '</div>';

echo JHtml::_('form.token'); 



JHTML::script("com_biodiv/admin.js", true, true);

?>
