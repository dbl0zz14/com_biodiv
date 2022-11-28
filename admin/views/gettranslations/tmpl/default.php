<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

printAdminMenu("TRANSLATIONS");
	
print '<div id="j-main-container" class="span10 j-toggle-main">';


print '<h2>Translated articles</h2>';

// print '<h3>Responses</h3>';
// foreach ( $this->responses as $response ) {
	// print '<p>';
	// print_r ( $response );
	// print '</p>';
// }
print '<h3>Please check and confirm any articles to be added or updated in Joomla</h3>';
//print '<p id="updateArticleMsg"></p>';
foreach ( $this->statusResponses as $articleId=>$articleStatusArray ) {
	print '<div class="span12">';
	print '<h4>Article '.$articleId.'</h4>';
	
	foreach ( $articleStatusArray as $lang=>$articleStatus ) {
		$isError = false;
		print '<h5>Language: '.$lang.'</h5>';
		if ( property_exists ( $articleStatus, "warning" ) ) {
			print '<p>Warning: '.$articleStatus->warning.'</p>';
		}
		else if ( property_exists ( $articleStatus, "error" ) ) {
			print '<p>Error: '.$articleStatus->error.'</p>';
			$isError = true;
		}
		print '<form id="updateArticleForm_'.$articleId.'_'.$lang.'" class="updateArticle">';
		print '<input type="hidden" name="trLang" value="'.$lang.'"/>';
		print '<input type="hidden" name="article" value="'.$articleId.'"/>';
		if ( property_exists ( $articleStatus, "escapedText" ) ) {
			print '<div class="well">'. $articleStatus->escapedText . '</div>';
		}
		if ( !$isError ) {
			print '<input type="hidden" name="title" value="'.base64_encode($articleStatus->title).'"/>';
			print '<input type="hidden" name="text" value="'.base64_encode($articleStatus->text).'"/>';
			print '<p id="updateArticle_'.$lang.'_'.$articleId.'"><button type="submit" class="btn js-stools-btn-clear">Update article in Joomla</button></p>';
		}
		print '</form>';
	}
	print '</div>';
}

//print '<a href="'.$this->reportURL.'"><button type="button" class="btn js-stools-btn-clear" title="Download report" >Download here</button></a>';

print '</div>';

JHTML::script("com_biodiv/admin.js", true, true);

?>


