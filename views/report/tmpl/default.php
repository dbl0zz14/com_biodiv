<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

if ( !$this->personId ) {
	print '<a type="button" href="'.$this->translations['dash_page']['translation_text'].'" class="list-group-item btn btn-block" >'.$this->translations['login']['translation_text'].'</a>';
}
else {

	//error_log("report tmpl printing data");

	print '<div class="col-xs-12 col-sm-12 col-md-12">';
	
	// Either download without creating file (if num rows relatively small) or create large file for download 
	if ( $this->totalRows < BiodivReport::REPORT_FILE_THRESHOLD ) {
		print '<button id="reportdownload" type="button" class="list-group-item btn btn-block" data-report-id="'.$this->report_id.'" >';
			
		print '<h4 class="list-group-item-heading">'.$this->translations['download']['translation_text'].'</h4>';

		print '</button>';
	}
	else {
	
		print '<button id="rptfiledownload" type="button" class="list-group-item btn btn-block" data-report-id="'.$this->report_id.'" >';
			
		print '<h4 class="list-group-item-heading">'.$this->translations['download']['translation_text'].'</h4>';

		print '</button>';
	}
	
	print '</div>';
			
	//print $this->data ;

	$data = "";
			
	$db = JDatabase::getInstance(dbOptions());

	//error_log ( "report template, totalRows = " . $this->totalRows );

	//error_log ( "getData, pageLength = " . $this->pageLength );
		
	$numPages = ceil($this->totalRows/$this->pageLength);

	//error_log ("getData: num pages = " . $numPages );

	// This bit gives a max pagination displayed system ie automatically move the pages on
	$numPagesDisplayed = 16;

	$startPage = 0;
	$endPage = $numPages;

	if ( $numPages > $numPagesDisplayed ) {

		$startPage = $this->page - ceil($numPagesDisplayed/2);
		if ( $startPage < 0 ) $startPage = 0;
		
		$endPage = $startPage + $numPagesDisplayed;
		if ( $endPage > $numPages ) {
			$endPage = $numPages;
			$startPage = max($numPages - $numPagesDisplayed, 0);
		}

	}

	if ( $numPages > 1 ) {
		print '<div class="report_pagination col-xs-12 col-sm-12 col-md-12">'.
		'<nav aria-label="Report pagination">'.
		'<ul class="pagination btn-group">'.
		'<li class="btn btn-info prev-page">'.
		'<i class="fa fa-backward"></i>'.
		'</li>';
		/*
		for ( $i = 0; $i < $numPages; $i++ ) {	
			$activeFlag = "";
			$lastFlag = "";
			if ( $i == $this->page ) {
				$activeFlag = "active";
			}
			if ( $i == $numPages-1 ) {
				$lastFlag = "last-page";
			}
			print '    <li class="btn btn-info '. $activeFlag . ' ' . $lastFlag . '">'.
			strVal($i+1).
			'   </li>';
		}
		*/
		for ( $i = $startPage; $i < $endPage; $i++ ) {	
			$activeFlag = "";
			$lastFlag = "";
			if ( $i == $this->page ) {
				$activeFlag = "active";
			}
			if ( $i == $numPages-1 ) {
				$lastFlag = "last-page";
			}
			print '    <li class="btn btn-info '. $activeFlag . ' ' . $lastFlag . '">'.
			strVal($i+1).
			'   </li>';
		}
		print '<li class="btn btn-info next-page">'.
		'<i class="fa fa-forward"></i>'.
		'</li>'.
		
		'</ul>'.
		'</nav>'.
		'</div>';
	}



	//$err_msg = print_r ( $this->rows, true );
	//error_log ( "report template, rows: " . $err_msg );

	// Add the table and headings
	print  '<div class="table-responsive col-xs-12 col-sm-12 col-md-12">';
	print  '<table class="table" style="white-space:nowrap">
	  <thead>
		<tr>';
		
	foreach ( $this->headings as $heading_id=>$heading_name ) {
		print '<th scope="col">'.$heading_name.'</th>';
	}

	print '</tr>
	  </thead>
	  <tbody>';

	// Add the rows of data  
	foreach ( $this->rows as $row ) {
		$rowData = explode(',', $row);
		print '<tr>';
		
		foreach ( $rowData as $rowField ) {
			if ( strpos($rowField,"PlaySeq") === 0 ) {
				$seqId = 0;
				if ( strlen($rowField) > 7 ) $seqId = substr($rowField,7);
				print '<td><button class="media-btn" data-seq_id="'. $seqId . '"><i class="fa fa-play"></i></button></td>';
			}
			else {
				print '<td class="text-nowrap">'.$rowField.'</td>';
			}
		}
		
		print '</tr>';
	}

	print '</tbody>';
	print '</table>';
	print '</div>';
}



?>

