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
	print '<a type="button" href="'.JText::_("COM_BIODIV_DASHLIKES_DASH_PAGE").'" class="list-group-item btn btn-block" >'.JText::_("COM_BIODIV_DASHLIKES_LOGIN").'</a>';
}
else {
	print '<div class="row" style="margin-bottom:5px">';
	
	// ---------------------- Filter by
	print '<div class="col-xs-12 col-sm-12 col-md-3">';
	
	// Create 3 dropdowns of filter options: site, species and year
	
	print "<label for='site_filter'>".JText::_("COM_BIODIV_DASHLIKES_SITE_FILTER")."</label>";
		
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
	
	print "<label for='year_filter'>".JText::_("COM_BIODIV_DASHLIKES_YEAR_FILTER")."</label>";
		
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
	
	
	print "<label for='species_filter'>".JText::_("COM_BIODIV_DASHLIKES_SPECIES_FILTER")."</label>";
		
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
	
	print "<label for='sort'>".JText::_("COM_BIODIV_DASHLIKES_SORT_BY")."</label>";
		
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
	
	print "<label for='numperpage'>".JText::_("COM_BIODIV_DASHLIKES_NUM_PER_PAGE")."</label>";
		
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
			
			$numPages = ceil($this->totalLikes/$this->numPerPage);

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
	
	for ( $i=0; $i<$this->numLikes; $i++ ) {
		
		$currentLike = $this->likesArray[$i];
		
		$seq = new Sequence($currentLike['sequence_id']);
		
		$mediafile = array_values($seq->getMediaFiles())[0];
		
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
			print "<button class='media-btn' data-seq_id='".$seq->getId()."'><i class='fa fa-play'></i> " . JText::_("COM_BIODIV_DASHLIKES_REVIEW") . "<audio src = '" . $mediafile . "' width='100%'></audio></button>";
		}
		
		print '<p></p>';
		print '<div style="word-wrap: break-word;">' . JText::_("COM_BIODIV_DASHLIKES_FILENAME") . ' ' . $currentLike['upload_filename'] . '</div>';
		print '<p></p>';
		print '<p>' . JText::_("COM_BIODIV_DASHLIKES_SITE") . ' ' . $seq->getSiteName() . '</p>';
		print '<p>' . JText::_("COM_BIODIV_DASHLIKES_TAKEN") . ' ' . $currentLike['taken'] . '</p>';
		print '<p>' . JText::_("COM_BIODIV_DASHLIKES_LIKED") . ' ' . $currentLike['like_time'] . '</p>';
		if ( $this->likedByOthers == 1 ) {
			print '<p>' . JText::_("COM_BIODIV_DASHLIKES_SPECIES_OTHERS") . ' ' . $currentLike['species'] . '</p>';
		}
		else {
			print '<p>' . JText::_("COM_BIODIV_DASHLIKES_SPECIES") . ' ' . $currentLike['species'] . '</p>';
		}
		
		
		print '</div>'; // well
		print '</div>'; // col-4
		
		if ( $i%3 == 2 ) {
			print '</div>'; // row
		}
		
		
	}
	
	// ---------------------- Pagination
	
	if ( $this->numPerPageStr != "all" && $this->numPerPage > 3) {
		
		print '<div class="row">';
	
		$numPages = ceil($this->totalLikes/$this->numPerPage);

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






