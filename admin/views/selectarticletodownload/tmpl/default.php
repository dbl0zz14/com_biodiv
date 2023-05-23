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

print '<h2>Select which articles to download translations for</h2>';

print '<form id="translateArticles" action="'. BIODIV_ADMIN_ROOT . '&view=gettranslations" method="post">';

print '<p>';
print '<label for="trLang">Choose a language (or select All):</label>';
print '<select id="trLang" name="trLang">';
print '<option value="All" selected>All languages</option>';
foreach ( $this->languages as $id=>$lang ) {
	print '<option value="'.$lang->tag.'">'.$lang->name.'</option>';
}
print '</select><br>';
print '</p>';
	
	
print '<button type="submit" class="btn js-stools-btn-clear" >Download translations for selected articles</button>';

print '<table class="table table-striped" id="schoolList">';
print '<thead>';
print '<tr>';
print '<th class="nowrap">';
print 'Select';
print '</th>';
print '<th class="nowrap">';
print 'Id';
print '</th>';
print '<th class="nowrap">';
print 'Title';
print '</th>';
print '<th class="nowrap">';
print 'Alias';
print '</th>';
print '<th class="nowrap">';
print 'Category';
print '</th>';
print '<th class="nowrap">';
print 'Modified';
print '</th>';
print '</tr>';
print '</thead>';
		

foreach ( $this->articles as $articleId=>$article ) {
	
	print '<tr>';
	print '<td>';
	print '<input type="checkbox" id="article_'.$articleId.'" name="article[]" value="'.$articleId.'" > ';
	print '</td>';
	print '<td>';
	print '<a href="">';
	print $article->article_id;
	print '</a>';
	print '</td>';
	print '<td>';
	print $article->title;
	print '</td>';
	print '<td>';
	print $article->alias;
	print '</td>';
	print '<td>';
	print $article->category;
	print '</td>';
	print '<td>';
	print $article->modified;
	print '</td>';
	print '</tr>';
	
}

print '</table>';


print '</form>';

print '</div>';



JHTML::script("com_biodiv/commonbiodiv.js", true, true);
JHTML::script("com_biodiv/admin.js", true, true);

?>
