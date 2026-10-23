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

$this->model->printAdminMenu("SCHOOL");


print '<div id="j-main-container" class="span10 j-toggle-main">';

print '<h2>Add school user for '.$this->school->name.' ( id = '.$this->schoolId.' )</h2>';

print '<p>You can add existing users to the school here.  Please ensure they are registered and have the correct user group (School Teacher or School Advisor)</p>';

print '<form id="addSchoolTeacher" class="biodivForm addSchoolUser">';

print '<input type="hidden" name="role" value="Teacher"/>';

$this->model->printSchoolUserForm( $this->schoolId, "School Teacher" );

print '<div class="form-group">';
print '<input type="submit" class="btn btn-primary" value="Add teacher"/>';
print '</div>';

print '</form>';


print '<form id="addSchoolAdvisor" class="biodivForm addSchoolUser">';

print '<input type="hidden" name="role" value="Ecologist"/>';

$this->model->printSchoolUserForm( $this->schoolId, "School Advisor" );

print '<div class="form-group">';
print '<input type="submit" class="btn btn-primary" value="Add school ecologist"/>';
print '</div>';

print '</form>';

print '<div id="addSchoolUserMsg"></div>';

print '</div>';

echo JHtml::_('form.token'); 


$this->document->getWebAssetManager()->usePreset('com_biodiv.admin');


// JHTML::script("com_biodiv/commonbiodiv.js", true, true);
// JHTML::script("com_biodiv/admin.js", true, true);

?>
