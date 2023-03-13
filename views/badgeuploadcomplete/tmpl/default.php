<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;



if ( !$this->personId ) {
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_BADGEUPLOADCOMPLETE_LOGIN").'</div>';
}
else {
	
	print '<div class="row">';
	
	print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	Biodiv\SchoolCommunity::generateNav($this->schoolUser, $this->classId, "badges");
	
	print '</div>'; // col-12
	
	print '</div>'; // row
	
	
	print '<div class="row>';


	$badgeComplete = false;
	if ( $this->badge > 0  ) {
		if ( $this->badgeResult ) {
			if ( $this->badgeResult->isComplete ) {
				$badgeComplete = true;
				print '<div class="col-md-12 text-center lower_heading"><h2>'. JText::_("COM_BIODIV_BADGEUPLOADCOMPLETE_COMPLETED") .'</h2></div>';
			}
			else {
				if ( $this->badgeResult->numAchieved == 1 ) {
					$compStr = "" . $this->badgeResult->numAchieved . " " . JText::_("COM_BIODIV_BADGEUPLOADCOMPLETE_UPLOAD");
				}
				else {
					$compStr = "" . $this->badgeResult->numAchieved . " " . JText::_("COM_BIODIV_BADGEUPLOADCOMPLETE_UPLOADS");
				}
				$toGo = $this->badgeResult->numRequired - $this->badgeResult->numAchieved;
				print '<div class="col-md-12 text-center lower_heading"><h2>'. JText::_("COM_BIODIV_BADGEUPLOADCOMPLETE_YOU") .' '
							.$compStr.' '. JText::_("COM_BIODIV_BADGEUPLOADCOMPLETE_TOWARDS") .' - '. $toGo . ' ' . 
							JText::_("COM_BIODIV_BADGEUPLOADCOMPLETE_TO_GO") . '</h2></div>';
			}
		}
	}

	print '</div>'; // row



	print '<div class="row">';
	print '<div class="col-md-4">';
	$href = "bes-badges";
	if ( $this->classId ) {
		$href .= "?class_id=".$this->classId;
	}
	print '<a href="'.$href.'">';
	if ( $badgeComplete ) {
		$btnText = JText::_("COM_BIODIV_BADGEUPLOADCOMPLETE_COLLECT");
	}
	else {
		$btnText = JText::_("COM_BIODIV_BADGEUPLOADCOMPLETE_BACK_BES");
	}
	print '	<button class="btn btn-lg btn-primary vSpaced" type="button">'.$btnText.'</button>';
	print '</a>';
	print '</div>'; // col-4
	print '</div>'; // row

}

?>


