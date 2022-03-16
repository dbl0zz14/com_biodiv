<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Needed to ensure pick up the correct component files
set_include_path(JPATH_COMPONENT_SITE . PATH_SEPARATOR . get_include_path());

include_once "local.php";
include_once "BiodivHelper.php";


// Get an instance of the controller prefixed by Biodiv
$controller = JControllerLegacy::getInstance('BioDiv');

// Perform the Request task
$controller->execute(JFactory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();