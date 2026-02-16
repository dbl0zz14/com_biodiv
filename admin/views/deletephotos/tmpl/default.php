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

printAdminMenu("DELETEPHOTOS");

//print '<a href="?option=com_biodiv"><button type="button">MammalWeb Admin Home</button></a>';

print '<div id="j-main-container" class="span10 j-toggle-main">';

print '<h2>Delete photos</h2>';

print '<p>';
print 'Use this page to delete uploaded media (photo or video) from the database and S3';
print '</p>';
print '<p>';
print 'Note that media files will be deleted from S3 but will get a status of DELETED in the Photo table';
print '</p>';

print '<h3>Instructions</h3>';
print '<p>';
print 'Please prepare a file containing a list of photo_ids which you would like to be deleted.  This process is irreversible so prepare with care :)';
print '</p>';
print '<p>';
print 'The file should be in csv format with a photo_id per line - probably exported from an sql query in phpMyAdmin';
print '</p>';
print '<p>';
print 'Once the file is created, click Choose file to locate it in your file system, then Upload to upload and delete the images or videos for all the sequences it contains';
print '</p>';

print '<form id="deletePhotos" class="biodivForm" action = "'.BIODIV_ADMIN_ROOT.'&task=deletephotos" method = "POST" enctype="multipart/form-data">';
print '<label for="file">Choose file of photo_ids:</label>';
print '<input type="file" name="file" id="file" required>';
print '<input class="btn btn-primary" type="submit" value="Upload File and Delete Photos">';
print '</form>';


echo JHtml::_('form.token'); 



//JHTML::script("com_biodiv/commonbiodiv.js", true, true);
//JHTML::script("com_biodiv/admin.js", true, true);

?>
