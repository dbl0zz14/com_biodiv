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

if ( $this->purpose == "gettranslations" ) {
	
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
}
else if ( $this->purpose == "sendtranslations" ) {
	
	print '<h2>Select which articles to submit for translation</h2>';

	print '<form id="translateArticles" action="'. BIODIV_ADMIN_ROOT . '&view=sendtranslations" method="post">';

	print '<button type="submit" class="btn js-stools-btn-clear" >Submit selected articles for translation</button>';
}

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

print '<div class="pagination pagination-toolbar clearfix">';
print '<nav role="navigation" aria-label="Pagination">';
print '<ul class="pagination-list">';

for ( $i=1; $i <= $this->totalNumPages; $i++ ) {
	if ( $i == $this->page ) {
		print '  <li class="active"><a aria-label="Go to page '.$i.'" href="index.php?option=com_biodiv&view=selectarticle&purpose='.$this->purpose.'$page='.$i.'">'.$i.'</a></li>';
	}
	else {
		print '  <li><a aria-label="Go to page '.$i.'" href="index.php?option=com_biodiv&view=selectarticle&purpose='.$this->purpose.'&page='.$i.'">'.$i.'</a></li>';
	}
}

print '</ul>';
print '</nav>';
print '</div>';

print '</div>';



JHTML::script("com_biodiv/admin.js", true, true);

?>
