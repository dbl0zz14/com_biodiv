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

printAdminMenu("TRANSLATIONS");

print '<div id="j-main-container" class="span10 j-toggle-main">';


print '<h2>'.$this->title.'</h2>';

print '<h4>Once downloaded, upload to Transifex as a translation manually</h4>';

print '<a href="'.$this->reportURL.'" download><button type="button" class="btn js-stools-btn-clear" title="Download report" >Download here</button></a>';


print '</div>';



JHTML::script("com_biodiv/commonbiodiv.js", true, true);
JHTML::script("com_biodiv/admin.js", true, true);

?>
