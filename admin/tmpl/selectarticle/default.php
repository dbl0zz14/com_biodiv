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

$this->model->printAdminMenu("TRANSLATIONS");

print '<div id="j-main-container" class="span10 j-toggle-main">';

if ( $this->purpose == "gettranslations" ) {
	
	print '<h2>Translations</h2>';
	
	print '<h3>Select which articles to download translations for</h3>';
	
	print '<form id="searchArticles" class="form-inline" action="'. BIODIV_ADMIN_ROOT . '&view=selectarticle&purpose=gettranslations" method = "POST">';
	
	print '<div class="form-group">';	
	if ( $this->searchStr ) {
		
		error_log ( "Got searchStr: " . $this->searchStr );
		print '    <input type="search" name="search" class="form-control" id="search" value="'.$this->searchStr.'">';
		
	
	}
	else {
		print '    <input type="search" name="search" class="form-control" id="search" placeholder="Search articles" >';
	}
	
	//print '<button class="btn btn-default searchResourcesBtn" type="submit" ><span class="glyphicon glyphicon-search" ></span></button>';
	print '<button class="btn btn-primary" type="submit" >Search articles</button>';
	print '</div>';
	
	print '</form>';

	print '<form id="translateArticles" action="'. BIODIV_ADMIN_ROOT . '&view=gettranslations" method="post">';

	print '<div class="form-group">';	
	print '<label for="trLang">Choose a language (or select All):</label>';
	print '<select class="form-control" id="trLang" name="trLang">';
	print '<option value="All" selected>All languages</option>';
	foreach ( $this->languages as $id=>$lang ) {
		print '<option value="'.$lang->tag.'">'.$lang->name.'</option>';
	}
	print '</select>';
	print '</div>';
		
		
	print '<button type="submit" class="btn btn-primary" >Download translations for selected articles</button>';
}
else if ( $this->purpose == "sendtranslations" ) {
	
	print '<h2>Translations</h2>';
	
	print '<h3>Select which articles to submit for translation</h3>';
	
	print "<h4>Hint: If you can't find the article check it has language set to English not All</h4>";
	
	$searchPage = BIODIV_ADMIN_ROOT . '&view=selectarticle&purpose=sendtranslations';
	
	print '<form id="searchArticles" class="form-inline" action="'. BIODIV_ADMIN_ROOT . '&view=selectarticle&purpose=sendtranslations" method = "POST">';
	
	print '<div class="form-group">';	
	if ( $this->searchStr ) {
		
		print '    <input class="form-control" type="search" name="search" class="form-control" id="search" value="'.$this->searchStr.'">';
	
	}
	else {
		print '    <input class="form-control" type="search" name="search" class="form-control" id="search" placeholder="Search articles" >';
	}
	print '<button class="btn btn-primary" type="submit" >Search articles</button>';
	print '</div>';
	
	
	print '</form>';

	print '<form id="translateArticles" action="'. BIODIV_ADMIN_ROOT . '&view=sendtranslations" method="post">';

	print '<div class="form-group">';	
	print '<button type="submit" class="btn btn-primary" >Submit selected articles for translation</button>';
	print '</div>';
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


$this->document->getWebAssetManager()->usePreset('com_biodiv.admin');


?>
