<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

	
$seq_json = json_encode($this->sequenceIds);


print "<div id='seq_ids' data-seq-ids='".$seq_json."'></div>";

	

if ( count($this->sequences) == 0 ) {
	print '  <h2 class="text-center classify_heading">'.JText::_("COM_BIODIV_KIOSKQUIZBEGINNERAUDIO_NO_SEQUENCES").'</h2>';
}

else {

	print '  <h1 id="quiz_whatsee" class="text-center classify_heading">'.JText::_("COM_BIODIV_KIOSKQUIZBEGINNERAUDIO_WHAT_SEE").'</h1>';  

	
	
	
	// --------------------- Photos or video,  LHS --------------------------

	
	print '<div id="beginner_quiz_lhs" class="col-md-7">';
	
	
	print '<div class="classify_panel_left" >';


	$numSeqs = count($this->sequences);
	
	for ($i = 0; $i < $numSeqs; $i++) {
		
		$seq = $this->sequences[$i];
		
		$styleText="";
	
		if ( $i > 0 ) {
			$styleText = 'style="display:none"';
		}
	
		// Look through photos or play video message
		if ( $seq->getMedia() == "video" ) {
			print '  <h2 id="look_thro_'.$i.'" class="text-center look_thro quiz_h2" '.$styleText.'>'.JText::_("COM_BIODIV_KIOSKQUIZBEGINNERAUDIO_PLAY_CLIP").'</h2>';
		}
		else if ( $seq->getMedia() == "audio" ) {
			print '  <h2 id="look_thro_'.$i.'" class="text-center look_thro quiz_h2" '.$styleText.'>'.JText::_("COM_BIODIV_KIOSKQUIZBEGINNERAUDIO_PLAY_CLIP").'</h2>';
		}
		else {
			print '  <h2 id="look_thro_'.$i.'" class="text-center look_thro quiz_h2" '.$styleText.'>'.JText::_("COM_BIODIV_KIOSKQUIZBEGINNERAUDIO_LOOK_THRO").'</h2>';
		}
	
	}
	
	print '<div id="media_carousel" class="beginner-audio-media">';

	$seq = $this->sequences[0];
	
	// Add extra col to give space for controls outside photos
	if ( $seq->getMedia() == "photo" ) {
	
		print '<div class="col-md-12">';
		print '<div class="col-md-12">';
		
		$this->mediaCarousel->generateMediaCarousel($seq);

		print '</div>'; // col-12
		print '</div>'; // col-12
	}
	else {
		
		$this->mediaCarousel->generateMediaCarousel($seq);
		
	}

	print '</div>'; // media carousel
	
	print '</div>'; // classify_panel_left
	
	print '</div>'; // col-7


	
	// --------------------- Species choices and correct species display, RHS --------------------------

	
	print '<div id="beginner_quiz_rhs" class="col-md-5">';
	
	print '<div class="classify_panel_right">';
	
	
	print '  <h2 id="try_again" class="text-center quiz_h2"  style="display:none">'.JText::_("COM_BIODIV_KIOSKQUIZBEGINNERAUDIO_TRY_AGAIN").'</h2>';
	
		



	// --------------------- Species choices and correct choice panels --------------------------
	
	$numSeqs = count($this->sequences);
	
	$attributionStr = "";
		
	for ($i = 0; $i < $numSeqs; $i++) {
		
		$seq = $this->sequences[$i];
	
		// Match species to photo or video
		$styleText="";	
		if ( $i > 0 ) {
			$styleText = 'style="display:none"';
		}
	
		// Look through photos or play video message
		if ( $seq->getMedia() == "video" ) {
			print '  <h2 id="match_with_'.$i.'" class="text-center match_with quiz_h2" '.$styleText.'>'.JText::_("COM_BIODIV_KIOSKQUIZBEGINNERAUDIO_MATCH_VIDEO").'</h2>';
		}
		else if ( $seq->getMedia() == "audio" ) {
			print '  <h2 id="match_with_'.$i.'" class="text-center match_with quiz_h2" '.$styleText.'>'.JText::_("COM_BIODIV_KIOSKQUIZBEGINNERAUDIO_MATCH_AUDIO").'</h2>';
		}
		else {
			print '  <h2 id="match_with_'.$i.'" class="text-center match_with quiz_h2" '.$styleText.'>'.JText::_("COM_BIODIV_KIOSKQUIZBEGINNERAUDIO_MATCH_PHOTOS").'</h2>';
		}
		
		
		if ( $i == 0 ) {
			print '<div id="species_choices_'.$i.'" class="species_group">';
		}
		else {
			print '<div id="species_choices_'.$i.'" class="species_group" style="display:none">';
		}
		
		$correct = $seq->getPrimarySpecies();
		$correctId = $correct[0]->id;
		
		
		$this->options = $this->quiz->getIncorrectSpecies($i, 1);
		
		$this->options[] = $correctId;
		
		shuffle ( $this->options );
		
		foreach ( $this->options as $speciesId ) {
		
			$speciesName = codes_getName($speciesId,'contenttran');
			
			$longSpeciesNameClass = '';
			if ( strlen($speciesName) > 13 ) $longSpeciesNameClass = 'long_species_name';
			
			error_log ( "Long species name class = " . $longSpeciesNameClass );
		
		
			$image = codes_getName($speciesId,'kioskimg');
			$imageText = "";
			$imageURL = "";
			if ( $image ) {
				$imageURL = JURI::root().$image;
				$imageText = "<img width='100%' src='".$imageURL."'>";
			}
			
			$correctClass = "";
			if ( $speciesId == $correctId ) {
				$correctClass = "correct-species";
				
				// Store these for the correct choice panel
				$correctName = $speciesName;
				$correctImageURL = $imageURL;
			}
			
			print '<div class="row">';
			print '<div class="col-md-4 beginner-audio-image">';
			print '	<button id="quiz_select_'.$speciesId.'" class="btn btn-lg btn-block btn-wrap-text btn-success  full-img-btn beginner-quiz-audio-btn btn ' . $correctClass . '">'.$imageText.'<div class="species_name ' . $longSpeciesNameClass . '">'.$speciesName.'</div></button>';
			print '</div>'; // col-4
			
			/*
			$iframeURLs = getOptionData($speciesId, 'xenocantourl');
			$iframeURL = null;
			if ( count($iframeURLs) > 0 ) {
				$iframeURL = $iframeURLs[0];
			}
			
			print '<div class="col-md-8 beginner-audio-sono">';
			
			if ( $iframeURL ) {
				//print '<iframe src="'.$iframeURL.'" width="100%" height="220" frameborder="0" scrolling="no"></iframe>';
				
				print '<div id="kioskIframe" class="col-md-12" style=" width: 27vw; height: 31vh; position: relative;">';
				print '<div id="framearea" style="  width: 100%; height: 100%; position: absolute; top: 0; left: 0; z-index: 1;">';
				print '<iframe src="'.$iframeURL.'" width="100%" height="220" frameborder="0" scrolling="no"></iframe>';
				
				print '</div>';
				print '<div id="framecover" style="  width: 100%; height: 30%; background: transparent; position: absolute; top: 0; left: 0; z-index: 10;">';
				print '</div>';
				print '</div>'; //kioskIframe
			}
			print '</div>'; // col-8
			*/
			/*
			$sonoURLs = getOptionData($speciesId, 'xenocantourl');
			$sonoURL = null;
			if ( count($sonoURLs) > 0 ) {
				$sonoURL = $sonoURLs[0];
			}
			*/
			$sonoURL = null;
			$audioAttribution = null;
			
			$sonoJSON = getOptionData ( $speciesId, 'sonogram' );
			//$errStr = print_r ( $sonoJSON, true );
			//error_log ( "Sonogram JSON: " . $errStr ); 
				
			if ( count($sonoJSON) > 0 ) {
				$sonoObj = json_decode ( $sonoJSON[0] );
				
				//$errStr = print_r ( $sonoObj, true );
				//error_log ( "Sonogram Object: " . $errStr ); 
				
				$sonoURL = $sonoObj->video;
				$audioAttribution = $sonoObj->attribution;
			}
			
			print '<div class="col-md-8 beginner-audio-sono">';
			
			if ( $sonoURL ) {
				
				//print '<iframe src="'.$iframeURL.'" width="100%" height="220" frameborder="0" scrolling="no"></iframe>';
				
				print '<div class="col-md-12" style="margin-top:4vh; width: 27vw; height: 14vh;">';
				print '<video oncontextmenu="return false;" disablepictureinpicture="" controls="" controlslist="nodownload noplaybackrate" style="height:18vh"><source src="'.$sonoURL.'" type="video/mp4">Your browser does not support the video tag</video>';
				print '</div>'; // col-12
			}
			print '</div>'; // col-8
			
			/*
			print '<div id="kioskIframe" class="col-md-12" style=" width: 700px; height: 300px; position: relative;">';
			//print '<div id="kioskIframe" class="col-md-12" >';
			print '<div id="framearea" style="  width: 100%; height: 100%; position: absolute; top: 0; left: 0; z-index: 1;">';
			print '<iframe src="https://www.xeno-canto.org/465113/embed" width="100%" height="220" frameborder="0" scrolling="no"></iframe>';
			
			print '</div>';
			print '<div id="framecover" style="  width: 100%; height: 30%; background: transparent; position: absolute; top: 0; left: 0; z-index: 10;">';
			//print '<img src="dot.gif" width="100%" height="100%" border="0">';
			print '</div>';
			print '</div>'; //kioskIframe
			*/
			
			print '</div>'; // row
			
			// Audio attribution 
			print '<div class="attrib_'.$i.'" style="display:none;">';
			if ( $audioAttribution ) {
				
				$attrStr = $speciesName . ': ' . $audioAttribution . ' ';
				error_log ("Adding attribution " . $attrStr );
				print $attrStr . ' ';
				
				error_log ( "i = " . $i );
				if ( $i == 0 ) {
					error_log ( "First question, adding to displayed string - " . $attrStr );
					$attributionStr .= $attrStr;
					
					error_log ( "Attribution string now: " . $attributionStr );
				}
			}
			print '</div>';
		
		}
		
		print '</div>'; // species_choices_i
		
		
		// --------------------- Correct species panels --------------------------

		print '<div id="correct_species_'. $i . '" class="species_group" style="display:none">';
		
		print '<h2 class="text-center quiz_h2"">'.JText::_("COM_BIODIV_KIOSKQUIZBEGINNERAUDIO_SPECIES_IS").' ' .$correctName.'</h2>';
		
		
		print '<img class="img-responsive center-block" style="max-height:48vh;" src="' . $correctImageURL . '" />';
		
		if ( $i  < $numSeqs - 1 ) {
			
			print '<div class="col-md-6 col-md-offset-3">';
			print '	<button class="btn btn-lg btn-block btn-success h3 control_btn beginner_next" >'.JText::_("COM_BIODIV_KIOSKQUIZBEGINNERAUDIO_NEXT").'</button>';
			print '</div>';
		}
		else {

			print '<div class="col-md-6 col-md-offset-3">';
			print '	<button id="beginner_results" class="btn btn-lg btn-block btn-success h3 control_btn " >'.JText::_("COM_BIODIV_KIOSKQUIZBEGINNERAUDIO_SHOW_RESULTS").'</button>';
			print '</div>';
		
		}

		
		print '</div>'; // correct_species_i

	
	}
	
	
	
	// ----------------------------- Progress bar ---------------------------

	$progWidth = 100/$numSeqs;
		
	print '<div id="quiz_audio_progress" class="species_group">';
	print '<div class="row">';
	print '<div class="col-md-12">';
	print "<div class='progress'>";
	print "  <div id='seq_progress_bar' class='progress-bar progress-bar-info' role='progressbar' aria-valuenow='".$progWidth."' aria-valuemin='0' aria-valuemax='100' style='width:".$progWidth."%'>";
	print "  1/" . $numSeqs;
	print "  </div>";
	print "</div>"; // progress bar
	print '</div>'; // col-12
	print '</div>'; // row
	print '</div>'; // quiz_progress


	print '</div>'; // classify_panel_right
	print '</div>'; // col-5 
	
	
	// Audio attribution 
	print '<div id="audioAttribution" class="image_attribution">' . $attributionStr . '</div>';
	
	
	
	// -------------------------------- Results ------------------------
	
	print '<div id="beginner_quiz_results" class="col-md-12"  style="display:none">';
	print '<div class="col-md-12">';
	
	print '<h1 class="text-center lower_heading">'.JText::_("COM_BIODIV_KIOSKQUIZBEGINNERAUDIO_FINISHED") . '</h1>';
	
	print '<h1 class="text-center classify_heading">'.JText::_("COM_BIODIV_KIOSKQUIZBEGINNERAUDIO_YOU_SPOTTED") . '</h1>';
	
	print '<div class="row spaced_row">';
	
	print '<div class="row">';
	
	$offset = 'col-md-offset-1';
	foreach ( $this->sequences as $seq ) {
		
		$correct = $seq->getPrimarySpecies();
		$correctId = $correct[0]->id;
		
		
		$speciesName = codes_getName($correctId,'contenttran');
		
		$longSpeciesNameClass = '';
		if ( strlen($speciesName) > 13 ) $longSpeciesNameClass = 'long_species_name';
			
		print '<div class="col-md-2 '. $offset .'">';
		
		print '<h3 class="text-center"><div class=" '.$longSpeciesNameClass.'">'.$speciesName.'</div></h3>';
		
		print '</div>'; // col-2
		
		$offset = '';
	}
	
	print '</div>';
	
	print '<div class="row">';
	
	$offset = 'col-md-offset-1';
	foreach ( $this->sequences as $seq ) {
		
		$correct = $seq->getPrimarySpecies();
		$correctId = $correct[0]->id;
		
		
		$image = codes_getName($correctId,'kioskimg');
		$imageText = "";
		$imageURL = "";
		if ( $image ) {
			$imageURL = JURI::root().$image;
		}
			
		
		print '<div class="col-md-2 '. $offset .'">';
		
		print '<img class="img-responsive center-block" style="max-height:48vh;" src="' . $imageURL . '" />';
		
		print '</div>';
		
		$offset = '';
	}
	
	print '</div>'; // row
	
	print '</div>'; // spaced_row
	
	print '<div class="row spaced_row">';
	
	print '<div class="col-md-4 col-md-offset-2">';
	print '	<button id="play_again_audio" class="btn btn-lg btn-block btn-success h3 control_btn" >'.JText::_("COM_BIODIV_KIOSKQUIZBEGINNERAUDIO_PLAY_AGAIN").'</button>';
	print '</div>';

	print '<div class="col-md-4">';
	print '	<button class="btn btn-lg btn-block btn-success h3 control_btn back_to_home" >'.JText::_("COM_BIODIV_KIOSKQUIZBEGINNERAUDIO_BACK_HOME").'</button>';
	print '</div>';
	
	print '</div>'; // row
	
	print '</div>'; // col-12
	print '</div>'; // col-12
	

	
}






?>


