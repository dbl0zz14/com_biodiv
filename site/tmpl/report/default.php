<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/

 
// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;


if ( !$this->personId ) {
	print '<a type="button" href="'.Text::_("COM_BIODIV_REPORT_DASH_PAGE").'" class="list-group-item btn btn-block" >'.Text::_("COM_BIODIV_REPORT_LOGIN").'</a>';
}
else {

	print '<div class="col-xs-12 col-sm-12 col-md-12 h3">'.$this->reportTitle.'</div>';

	print '<div class="col-xs-12 col-sm-12 col-md-12">';
	
	// Either download without creating file (if num rows relatively small) or create large file for download 
	if ( $this->totalRows < $this->jsonThreshold ) {
		//print '<button id="reportdownload" type="button" class="list-group-item btn btn-block btn-outline-primary" data-report-id="'.$this->report_id.'" >';
		print '<button id="reportdownload" type="button" class="btn btn-sm btn-outline-primary" data-report-id="'.$this->report_id.'" >';
		
		print Text::_("COM_BIODIV_REPORT_DOWNLOAD");

		print '</button>';
	}
	else {
	
		print '<button id="rptfiledownload" type="button" class="btn btn-sm btn-outline-primary" data-report-id="'.$this->report_id.'" >';
			
		print Text::_("COM_BIODIV_REPORT_DOWNLOAD");

		print '</button>';
	}
	
	print '</div>';
			
	$data = "";
			
	$numPages = ceil($this->totalRows/$this->pageLength);

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
		'<ul class="pagination">'.
		'<li class="page-item prev-page">'.
		//'<a class="page-link prev-page" aria-label="Previous">'.
		'<a class="page-link btn prev-page" aria-label="Previous">'.
		'<span aria-hidden="true">&laquo;</span>'.
		'</a>'.
		'</li>';
		
		for ( $i = $startPage; $i < $endPage; $i++ ) {	
			$activeFlag = "";
			$lastFlag = "";
			if ( $i == $this->page ) {
				$activeFlag = "active";
			}
			if ( $i == $numPages-1 ) {
				$lastFlag = "last-page";
			}
			print '    <li class="page-item '. $activeFlag . ' ' . $lastFlag . '">'.
			//'<a class="page-link" >'.
			'<a class="page-link btn" >'.
			strVal($i+1).
			'</a>'.
			'   </li>';
		}
		print '<li class="page-item next-page">'.
		//'<a class="page-link next-page" aria-label="Next">'.
		'<a class="page-link btn" aria-label="Next">'.
		'<span aria-hidden="true">&raquo;</span>'.
		'</a>'.
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
		
		print '<th scope="col" class="align-top">';
		
		if ( ( $this->filterColumns ) and ( array_key_exists ( $heading_id, $this->filterColumns ) ) ) {
			
			$filterType = $this->filterColumns[$heading_id];
			print '<label for="filter_'.$filterType.'">'.$heading_name.'</label>';
		
			print '<select id = "filter_'.$filterType.'" name = "filter_'.$filterType.'" class = "form-control report_select">';
			
			foreach( $this->filterValues[$heading_id] as $selVal=>$selStr ){
				if ( $selVal == $this->filterSelected[$heading_id] ) {
					// Show this as selected
					print '<option value="'.$selVal.'" selected>'.$selStr.'</option>';
				}
				else {
					print '<option value="'.$selVal.'">'.$selStr.'</option>';
				}
			}

			print '</select>';
		}
		else {
			print $heading_name;
		}
		
		print '</th>';
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
				print '<td><button class="media-btn" data-bs-toggle="modal" data-bs-target="#carousel_modal" data-seq_id="'. 
					$seqId . '"><i class="fa fa-play"></i></button></td>';
			}
			else if ( strpos($rowField,"ViewSet") === 0 ) {
				$setId = 0;
				if ( strlen($rowField) > 7 ) $setId = substr($rowField,7);
				if ( $setId > 0 ) {
					print '<td><a href="'.Text::_("COM_BIODIV_REPORT_SET_PAGE").'?set_id='.$setId.'" class="resource-set-btn" role="button" ><i class="fa fa-files-o"></i></a></td>';
				}
				else {
					print '<td></td>';
				}
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

