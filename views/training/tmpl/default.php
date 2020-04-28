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
	print "<div class='col-md-12' >";
	print "<h1>Choose a topic to get started</h1>";
	print "</div>";
		
	
	$nostars = "<span class='fa fa-star-o'></span></span><span class='fa fa-star-o'></span></span><span class='fa fa-star-o'></span></span><span class='fa fa-star-o'></span></span><span class='fa fa-star-o'></span>";
	$onestar = "<span class='fa fa-star'></span></span><span class='fa fa-star-o'></span></span><span class='fa fa-star-o'></span></span><span class='fa fa-star-o'></span></span><span class='fa fa-star-o'></span>";
	$twostar = "<span class='fa fa-star'></span><span class='fa fa-star'></span></span><span class='fa fa-star-o'></span></span><span class='fa fa-star-o'></span></span><span class='fa fa-star-o'></span>";
	$threestar = "<span class='fa fa-star'></span><span class='fa fa-star'></span><span class='fa fa-star'></span></span><span class='fa fa-star-o'></span></span><span class='fa fa-star-o'></span>";
	$fourstar = "<span class='fa fa-star'></span><span class='fa fa-star'></span><span class='fa fa-star'></span><span class='fa fa-star'></span></span><span class='fa fa-star-o'></span>";
	$fivestar = "<span class='fa fa-star'></span><span class='fa fa-star'></span><span class='fa fa-star'></span><span class='fa fa-star'></span><span class='fa fa-star'></span>";
	
	// Use similar method to projects..
	foreach ( $this->topics as list($topic_id, $topic_name) ) {
		
		$details = codes_getDetails($topic_id, "topic");
		
		//print("topic details: ");
		//print_r($details);
		
		// Get the associated articles for this language, if there is one.
		
		print "<div class='col-md-4' >";
		
		$topic_level = $this->currentScores[$topic_id]->level;
		$stars = $nostars;
		if ( $topic_level != null ) {
			if ( $topic_level >80 ) $stars = $fivestar;
			else if ( $topic_level > 60 ) $stars = $fourstar;
			else if ( $topic_level > 40 ) $stars = $threestar;
			else if ( $topic_level > 20 ) $stars = $twostar;
			else $stars = $onestar;
		}
		
		print '<h3 itemprop="name">';
		print $topic_name . " <div class='pull-right'><small>" . $stars . "</small></div>";
		print '</h3>';
				
		$url = imageURL($topic_id);
		
		print "<button class='image-btn topic-btn' type='button' data-topic='".$topic_id."' data-tooltip='".$this->translations['topic_tip']['translation_text'].
			"'><div class='crop-width-col4'><img class='project-col-image cover scale2' alt = 'topic image' src='".$url."' /></div></button>";
		
		print '</div>'; // col-md-4
		
		
		// Generate a modal for each topic so we can pass the topic id easily
		print "<div id='intro_modal_".$topic_id."' class='modal fade' role='dialog'>";
		print "  <div class='modal-dialog modal-sm'>";

		print "    <!-- Modal content-->";
		print "    <div class='modal-content'>";
		print "      <div class='modal-header'>";
		print "        <button type='button' class='close' data-dismiss='modal'></button>";
		//print "        <h4 class='modal-title'>".$this->translations['intro_h']['translation_text']."</h4>";
		print "      </div>";
		print "      <div class='modal-body'>";
		print"       <div id='topic_helplet_" . $topic_id . "'></div>";
		//print "        <p>Link to an article with instructions...</p>";
		print "      </div>";
		print "		<div class='modal-footer'>";
		
		print "	    <form action = '".BIODIV_ROOT."' method = 'GET'>";
		print "		<input type='hidden' name='view' value='trainingtopic'/>";
		print "		<input type='hidden' name='option' value='".BIODIV_COMPONENT."'/>";
		print "		<input type='hidden' name='topic_id' value='".$topic_id."'/>";
		
		// This field causes age, gender and number to be included in the classification test/training.
		//print "		<input type='hidden' name='detail' value='1'/>";
		
		print "		<button class='btn btn-danger classify-modal-button' type='submit' data-tooltip='".$this->translations["spot_tip"]["translation_text"]."'>".
					$this->translations["start_spot"]["translation_text"]."</button>";
		print "		<button type='button' class='btn btn-danger classify-modal-button' data-dismiss='modal'>".$this->translations['cancel']['translation_text']."</button>";
		print "		</form>";
		
		print "      </div>"; // modal footer
		print "    </div>"; // modal content

		print "  </div>"; // modal dialog
		print "</div>"; // modal
		
	}
	
	
	
	
	
	


?>

<?php
JHTML::stylesheet("com_biodiv/com_biodiv.css", array(), true);
JHTML::stylesheet("com_biodiv/mediacarousel.css", array(), true);
JHTML::script("com_biodiv/mediacarousel.js", true, true);
JHTML::script("com_biodiv/training.js", true, true);

?>



