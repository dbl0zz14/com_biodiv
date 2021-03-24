<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;
?>

<?php

if ( !$this->personId ) {
	print '<a type="button" href="'.JURI::root().'/'.$this->translations['dash_page']['translation_text'].'" class="list-group-item btn btn-block" >'.$this->translations['login']['translation_text'].'</a>';
}
else {
	print '<div class="row" style="margin-bottom:5px">';
	
	// ---------------------- Filter by
	print '<div class="col-xs-12 col-sm-12 col-md-3">';
	
	// Create 3 dropdowns of filter options: site, species and year
	
	print "<label for='site_filter'>".$this->translations['site_filter']['translation_text']."</label>";
		
	print "<select id = 'site_select' name = 'site_filter' class = 'form-control likes_select'>";
	
	foreach( $this->siteSelect as $selVal=>$selStr ){
		if ( $selVal == $this->siteId ) {
			// Show this as selected
			print "<option value='".$selVal."' selected>".$selStr."</option>";
		}
		else {
			print "<option value='".$selVal."'>".$selStr."</option>";
		}
	}

	print "</select>";
	
	print '</div>';
	print '<div class="col-xs-12 col-sm-12 col-md-2">';
	
	print "<label for='year_filter'>".$this->translations['year_filter']['translation_text']."</label>";
		
	print "<select id = 'year_select' name = 'year_filter' class = 'form-control likes_select'>";
	
	foreach( $this->yearSelect as $selVal=>$selStr ){
		if ( $selVal == $this->year ) {
			// Show this as selected
			print "<option value='".$selVal."' selected>".$selStr."</option>";
		}
		else {
			print "<option value='".$selVal."'>".$selStr."</option>";
		}
	}

	print "</select>";
	
	print '</div>';
	print '<div class="col-xs-12 col-sm-12 col-md-2">';
	
	
	print "<label for='species_filter'>".$this->translations['species_filter']['translation_text']."</label>";
		
	print "<select id = 'species_select' name = 'species_filter' class = 'form-control likes_select'>";
	
	foreach( $this->speciesSelect as $selVal=>$selStr ){
		if ( $selVal == $this->speciesId ) {
			// Show this as selected
			print "<option value='".$selVal."' selected>".$selStr."</option>";
		}
		else {
			print "<option value='".$selVal."'>".$selStr."</option>";
		}
	}

	print "</select>";
	
	print '</div>';
	
	// ---------------------- Sort by
	print '<div class="col-xs-12 col-sm-12 col-md-3">';
	
	
	// Create dropdown of options
	
	print "<label for='sort'>".$this->translations['sort_by']['translation_text']."</label>";
		
	print "<select id = 'sort_select' name = 'sort' class = 'form-control likes_select'>";
	
	foreach( $this->sortSelect as $selVal=>$selStr ){
		if ( $selVal == $this->sortBy ) {
			// Show this as selected
			print "<option value='".$selVal."' selected>".$selStr."</option>";
		}
		else {
			print "<option value='".$selVal."'>".$selStr."</option>";
		}
	}

	print "</select>";
	
	
	print '</div>'; // Sort col
	
	
	// ---------------------- Num per page
	print '<div class="col-xs-12 col-sm-12 col-md-2">';
	
	// Create dropdown of options
	
	print "<label for='numperpage'>".$this->translations['num_per_page']['translation_text']."</label>";
		
	print "<select id = 'num_select' name = 'numperpage' class = 'form-control likes_select'>";
	
	foreach( $this->numberSelect as $numVal=>$numStr ){
		if ( $numVal == $this->numPerPageStr ) {
			// Show this as selected
			print "<option value='".$numVal."' selected>".$numStr."</option>";
		}
		else {
			print "<option value='".$numVal."'>".$numStr."</option>";
		}
	}

	print "</select>";
	
	print '</div>'; // Num per page col
		
	print '</div>'; // filter/sort row
	
	// ---------------------- Pagination
	
	if ( $this->numPerPageStr != "all") {
		
		if ( $this->totalLikes > $this->numPerPage ) {
		
			print '<div class="row" style="margin-bottom:5px">';
			
			error_log ( "likes template: totalLikes = " . $this->totalLikes );

			error_log ( "likes template: numPerPage = " . $this->numPerPage );
				
			$numPages = ceil($this->totalLikes/$this->numPerPage);

			error_log ("likes template: num pages = " . $numPages );

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
			
		
			print '</div>'; // pagination row
		
		}
	
	}
	
	// ---------------------- The likes
	
	error_log ( "Num likes = " . $this->numLikes );
	
	for ( $i=0; $i<$this->numLikes; $i++ ) {
		
		$currentLike = $this->likesArray[$i];
		
		error_log ( "Liked sequence: " . $currentLike['sequence_id'] );
		
		$seq = new Sequence($currentLike['sequence_id']);
		
		error_log ("Sequence created");
		
		$mediafile = array_values($seq->getMediaFiles())[0];
		
		error_log ("Media file: " . $mediafile);
		
		if ( $i%3 == 0 ) {
			print '<div class="row">';
		}
		
		print '<div class="col-md-4">';
		
		print '<div class="well">';
		
		if ( $seq->getMedia() == "photo" ) {
			print "<button class='media-btn' data-seq_id='".$seq->getId()."'><img src = '" . $mediafile . "' width='100%'></button>";
		}
		else if ( $seq->getMedia() == "video" ) {
			print "<button class='media-btn' data-seq_id='".$seq->getId()."'><video src = '" . $mediafile . "' width='100%'></video></button>";
		}
		else if ( $seq->getMedia() == "audio" ) {
			print "<button class='media-btn' data-seq_id='".$seq->getId()."'><i class='fa fa-play'></i> " . $this->translations['review']['translation_text'] . "<audio src = '" . $mediafile . "' width='100%'></audio></button>";
		}
		
		print '<p></p>';
		print '<div style="word-wrap: break-word;">' . $this->translations['filename']['translation_text'] . ' ' . $currentLike['upload_filename'] . '</div>';
		print '<p></p>';
		print '<p>' . $this->translations['site']['translation_text'] . ' ' . $seq->getSiteName() . '</p>';
		print '<p>' . $this->translations['taken']['translation_text'] . ' ' . $currentLike['taken'] . '</p>';
		print '<p>' . $this->translations['liked']['translation_text'] . ' ' . $currentLike['like_time'] . '</p>';
		if ( $this->likedByOthers == 1 ) {
			print '<p>' . $this->translations['species_others']['translation_text'] . ' ' . $currentLike['species'] . '</p>';
		}
		else {
			print '<p>' . $this->translations['species']['translation_text'] . ' ' . $currentLike['species'] . '</p>';
		}
		
		
		error_log ("Buttons printed" );
		
		print '</div>'; // well
		print '</div>'; // col-4
		
		if ( $i%3 == 2 ) {
			print '</div>'; // row
		}
		
		
	}
	
	// ---------------------- Pagination
	
	if ( $this->numPerPageStr != "all" && $this->numPerPage > 3) {
		
		print '<div class="row">';
	
		//print '<div class="col-xs-12 col-sm-12 col-md-12">';
		
		error_log ( "likes template: totalLikes = " . $this->totalLikes );

		error_log ( "likes template: numPerPage = " . $this->numPerPage );
			
		$numPages = ceil($this->totalLikes/$this->numPerPage);

		error_log ("likes template: num pages = " . $numPages );

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

		
		//print '</div>';
		
		print '</div>'; // pagination row
	
	}

}


?>






