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

printAdminMenu("HOME");
	
print '<div id="j-main-container" class="span10 j-toggle-main">';
print '<h2>MammalWeb photos deleted</h2>';
print '<p>' . $this->numDeleted . ' photos deleted from S3 and status updated to DELETED </p>';
print '<p>Please compare to the number in the file in case there were errors</p>';
print '</div>';




?>
