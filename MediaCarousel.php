<?php

// Also need mediacarousel.js and mediacarousel.css to use this class.
// Use the classify translations


// No direct access to this file
defined('_JEXEC') or die;

class MediaCarousel {
	
	// An array of the children of this project.
	var $translations;
	
	function __construct()
	{
		$this->translations = getTranslations("classify");
		
		$this->lcontrols = array();
		foreach(codes_getList("noanimaltran") as $stuff){
			list($id, $name) = $stuff;
			// Handle special cases
			if ( $id == 86 )
				$this->lcontrols["control_content_" . $id] = biodiv_label_icons( "nothing", $name );
			else if ( $id == 87 )
				$this->lcontrols["control_content_" . $id] = biodiv_label_icons( "human", $name );
			else
				$this->lcontrols["control_content_" . $id] = $name;
		}
	}
	
	function generateLocationButton () {
		$showmap = $this->translations['show_map']['translation_text'] . " <span class='fa fa-map-marker'/>";
		print "<button type='button' class='btn btn-primary' id='control_map'>".$showmap."</button>";
	}

	function generateNextButton () {
		$nextseq = $this->translations['next_seq']['translation_text'] . " <span class='fa fa-arrow-circle-right'/>";
		print "<button type='button' class='btn btn-primary' id='control_nextseq'>".$nextseq."</button>";
	}

	function generateLeftControls () {
		foreach($this->lcontrols as $control_id => $control){
		  makeControlButton($control_id, $control);
		}
	}
	
	// photoUrls is an array of the Urls indexed by photo_id but in the order of the sequence
	// sequenceId is here for future use really.
	function generateMediaCarousel($sequenceId, $photoUrls, $media, $type = null ) {
		//print "Media Carousel, media = " . $media;
		if ( $media === "video" ) {
			$photoId = array_keys($photoUrls)[0];
			$mediaUrl = $photoUrls[$photoId];
			print '<div id="videoContainer" data-seq-id="'.$sequenceId.'" data-photo-id="'.$photoId.'"><video id="classify-video" oncontextmenu="return false;" controls controlsList="nodownload" ><source src="'.$mediaUrl.'" type="'.$media.'/'.$type.'">' . $this->translations['no_vid']['translation_text'] . '</video></div>';
		}
		else if ( $media === "audio" ) {
			$photoId = array_keys($photoUrls)[0];
			$mediaUrl = $photoUrls[$photoId];
			print '<div id="audioContainer" data-seq-id="'.$sequenceId.'" data-photo-id="'.$photoId.'"><audio id="classify-audio" oncontextmenu="return false;" controls controlsList="nodownload" ><source src="'.$mediaUrl.'" type="'.$media.'/'.$type.'">' . $this->translations['no_vid']['translation_text'] . '</audio></div>';
		}
		else if ( $media === "photo" ) {
			
			print '<div id="photoCarousel" data-seq-id="'.$sequenceId.'" class="carousel slide carousel-fade contain" data-ride="carousel" data-interval="false" data-wrap="false">';
			print '<!-- Indicators -->';
			print '<ol id="photo-indicators" class="carousel-indicators">';
			  
			$numphotos = count($photoUrls);
			for ($i = 0; $i < $numphotos; $i++) {
				$class_extras = "";
				if ($i == 0) $class_extras = ' class="active spb" ';
				else $class_extras = ' class="spb" id = "sub-photo-'.$i.'"';
				print '<li data-target="#photoCarousel" data-slide-to="'.$i.'"'.$class_extras.'></li>';
			}
			  
			print '</ol>';

			print '<button  id="fullscreen-button" type="button" class="right" ><span class="fa fa-expand fa-2x"></span></button>';
			print '<button  id="fullscreen-exit-button" type="button" class="right" ><span class="fa fa-compress fa-3x"></span></button>';
			  
			print '<!-- Wrapper for slides -->';
			print '<div id="photoCarouselInner" class="carousel-inner contain">';

			$j = 1;
			foreach($photoUrls as $photoId=>$photoUrl  ){
				$lastclass = "";
				if ( $j == $numphotos ) $lastclass .= 'last-photo';
				if ($j==1) {
					print '<div class="item active '.$lastclass.'" data-photo-id="'.$photoId.'">';
				}
				else {
					print '<div class="item '.$lastclass.'" data-photo-id="'.$photoId.'">';
				}
				print JHTML::image($photoUrl, 'Photo ' . $photoId, array('class' =>'img-responsive contain'));
				print '</div>';
				$j++;
			 }

			  print '</div> <!-- /.carousel-inner -->';
			  
			  print '<!-- Left and right controls -->';
			  
			  if ( $numphotos > 1 ) {
			  print '<a class="left carousel-control photo-carousel-control" href="#photoCarousel" data-slide="prev">';
			  print '  <span class="glyphicon glyphicon-chevron-left"></span>';
			  print '  <span class="sr-only">Previous</span>';
			  print '</a>';
			  print '<a id="photo-carousel-control-right" class="right carousel-control photo-carousel-control" href="#photoCarousel" data-slide="next">';
			  print '  <span class="glyphicon glyphicon-chevron-right"></span>';
			  print '  <span class="sr-only">Next</span>';
			  print '</a>';
			  }
			  
			print '</div> <!-- /.photoCarousel -->';
			
		}
	}
}



?>

