<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/

 
// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;


print '<div class="container">';
print '<div class="row">';

print "<div class='col-md-12' >";
print "<h1>".Text::_("COM_BIODIV_TRAINING_CHOOSE_TOPIC")."</h1>";
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
	
	$topic_level = null;
	if ( key_exists($topic_id, $this->currentScores) ) {
		error_log("topic " . $topic_id . " exists");
		$topic_level = $this->currentScores[$topic_id]["level"];
	}
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
			
	$url = $this->spotterModel->imageURL($topic_id);
	
	// print "<button class='image-btn topic-btn' type='button' data-topic='".$topic_id."' data-bs-tooltip='tooltip' title='".Text::_("COM_BIODIV_TRAINING_TOPIC_TIP").
		// "' data-toggle='modal' data-target='intro_modal_".$topic_id."' ><div class='crop-width-col4'><img class='cover img-responsive' style='min-height:100%' alt = 'topic image' src='".$url."' /></div></button>";
	print "<button class='image-btn topic-btn' type='button' data-topic='".$topic_id."' ".
		 "' data-bs-toggle='modal' data-bs-target='#intro_modal_".$topic_id."' ><div class='crop-width-col4'><img class='cover img-responsive' style='min-height:100%' alt = 'topic image' src='".$url."' /></div></button>";
	
	print '</div>'; // col-md-4
	
	// Generate a modal for each topic so we can pass the topic id easily
	print "<div id='intro_modal_".$topic_id."' class='modal fade' tabindex='-1'>";
	print "  <div class='modal-dialog modal-dialog-scrollable modal-xl'>";

	print "    <!-- Modal content-->";
	print "    <div class='modal-content'>";
	print "      <div class='modal-header'>";
	print '        <h2 class="modal-title">'.$topic_name.'</h2>';
	print "        <button type='button' class='close' data-dismiss='modal'></button>";
	print "      </div>";
	print "      <div class='modal-body p-4'>";
	print"       <div id='topic_helplet_" . $topic_id . "'></div>";
	print "      </div>";
	print "		<div class='modal-footer'>";
	
	print "	    <form action = '".BIODIV_ROOT."' method = 'GET'>";
	print "		<input type='hidden' name='view' value='trainingtopic'/>";
	print "		<input type='hidden' name='option' value='".BIODIV_COMPONENT."'/>";
	print "		<input type='hidden' name='topic_id' value='".$topic_id."'/>";
	
	// This field causes age, gender and number to be included in the classification test/training.
	//print "		<input type='hidden' name='detail' value='1'/>";
	
	print "		<button class='btn btn-primary btn-lg classify-modal-button' type='submit' data-tooltip='".Text::_("COM_BIODIV_TRAINING_SPOT_TIP")."'>".
				Text::_("COM_BIODIV_TRAINING_START_SPOT")."</button>";
	print "		<button type='button' class='btn btn-outline-secondary btn-lg classify-modal-button' data-dismiss='modal'>".Text::_("COM_BIODIV_TRAINING_CANCEL")."</button>";
	print "		</form>";
	
	print "      </div>"; // modal footer
	print "    </div>"; // modal content

	print "  </div>"; // modal dialog
	print "</div>"; // modal
	
}

print '</div>'; // row
print '</div>'; // container
	
$this->document->getWebAssetManager()->usePreset('com_biodiv.training');

HTMLHelper::_('bootstrap.modal', '.selector', []);


?>





