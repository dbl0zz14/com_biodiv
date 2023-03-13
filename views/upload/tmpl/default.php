<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;
$action = $this->root . "&view=uploadm";


JHTML::stylesheet("bootstrap-datepicker-master/datepicker3.css", array(), true);

showMessages();


print "<h1>" . JText::_("COM_BIODIV_UPLOAD_UPLOAD_IM") . " " . $this->site_name . " " . JText::_("COM_BIODIV_UPLOAD_ID") . " " . $this->site_id . "</h1>";


print "<div class='col-md-6'>";


print "<h2>". JText::_("COM_BIODIV_UPLOAD_START_NEW")."</h2>";


print "<form role='form' action='".$this->root . "&task=add_upload&site_id=".$this->site_id."'";
print " method='post' class='form-inline'>";

if ( $this->badge ) {
	print "<input type='hidden' name='badge' value='".$this->badge."'/>";
}
if ( $this->classId ) {
	print "<input type='hidden' name='class_id' value='".$this->classId."'/>";
}

// Deployment date and times only relevant for camera deployments, as opposed to recordings on phones etc.
// However timezone is relevant for all media types
	print "    <div class='form-group'>";
		 
		  $timezone_text = JText::_("COM_BIODIV_UPLOAD_TIMEZONE");
		  $dst_text = JText::_("COM_BIODIV_UPLOAD_DST");
		  $dst_yes = JText::_("COM_BIODIV_UPLOAD_DST_YES");
		  $dst_no = JText::_("COM_BIODIV_UPLOAD_DST_NO");
		  $timezone_abbreviations = DateTimeZone::listAbbreviations();
		  $timezone_ids = DateTimeZone::listIdentifiers();
		  
		  print  "<label for='timezone' style='display:block; margin-top: 20px;'>$timezone_text</label>";
		  
		  print  "<select id='timezone' name ='timezone' class='form-control'/>\n";
		  print  "<option value='' disabled selected hidden>".JText::_("COM_BIODIV_UPLOAD_PLS_SELECT")."</option>";
		  
		  // Make UTC the first option:
		  $utc = array_pop($timezone_ids);
		  print "<option id='UTC'>$utc</option>\n"; // - UTC+00:00</option>\n";
		  foreach ($timezone_ids as $zone_id) {
			print "<option id='$zone_id'>" . $zone_id . "</option>\n";
		  }
		  print "</select>";
		  
		  print  "<label for='dst' style='display:block; margin-top: 20px;'>$dst_text</label>\n";
		  
		  print  "<div><input type='radio' id='dst_yes' name ='dst' value='1'> " . $dst_yes . "</div>";
		  
		  print  "<div><input type='radio' id='dst_no' name ='dst' value='0' checked> " . $dst_no . "</div>";
		  

	print "    </div> <!-- /.form-group -->";
//	print "  </div> <!-- /.col -->";
	  

//	print "</div>  <!--div class='row' -->";

if ( $this->isCamera ) {

	print "<div class='row' >";

	$now = new JDate();
	$dForm = 'Y-m-d';
	$hForm = 'H';
	$mForm = 'i';
	$defaultDate = array('min' => $this->previous_collection_date->format($dForm),
				 'max' => $now->format($dForm));
	$defaultHours = array('min' => $this->previous_collection_date->format($hForm),
				 'max' => $now->format($hForm));
	$defaultMins = array('min' => $this->previous_collection_date->format($mForm),
				 'max' => $now->format($mForm));
				 
	$err_str = print_r ( $defaultDate, true );
	//error_log ( "default dates: " . $err_str );

	$err_str = print_r ( $defaultHours, true );
	//error_log ( "default hours: " . $err_str );

	$err_str = print_r ( $defaultMins, true );
	//error_log ( "default mins: " . $err_str );



	$document = JFactory::getDocument();
	foreach($defaultDate as $what => $date){
	  $hours = $defaultHours[$what];
	  
	  // Minutes are 00, 15, 30 or 45
	  $mins = sprintf ( "%'02d", floor($defaultMins[$what]/15) * 15 );
	  
	  // For the javascript
	  $document->addScriptDeclaration("BioDiv.${what}_date = '$date';\n");
	  $document->addScriptDeclaration("BioDiv.${what}_hours = '$hours';\n");
	  $document->addScriptDeclaration("BioDiv.${what}_mins = '$mins';\n");
	 }

	foreach(array("Deployment", "Collection") as $field){
		$lfield = strtolower($field);
		  
		$date_text = JText::_("COM_BIODIV_UPLOAD_DEP_DATE");
		$time_text = JText::_("COM_BIODIV_UPLOAD_DEP_TIME");
		if ( $field == "Collection" ) {
			$date_text = JText::_("COM_BIODIV_UPLOAD_COLL_DATE");
			$time_text = JText::_("COM_BIODIV_UPLOAD_COLL_TIME");
		}

		//	print "<div class='row' style='margin-top:10px;'>";
		print "<div class='col-md-4' style='margin-top:20px;'>";
		print "   <div class='form-group'>";
		print "     <label for='${lfield}_date' style='display:block;'>$date_text</label>\n";
		print "     <input type='text' size='10' class='form-control' name='${lfield}_date' id='${lfield}_date'/>";
		print "   </div>";
		print "</div> <!-- /.col -->";

		print "<div class='col-md-8' style='margin-top:20px;'>";
		print "  <div class='form-group'>";
		print "    <label for='${lfield}_time' style='display:block;'>$time_text</label>\n";


		//$hours = $defaultHours[$lfield];
		$hours = $defaultHours['max'];
		print "    <select id='${lfield}_hours' name ='${lfield}_hours' class='form-control'/>\n";
		print "<option/>";
		for($i = 0; $i<24;  $i++){
			if($i == $hours){
				$selected = " selected='selected'";
			}
			else{
				$selected = "";
			}
			print "<option $selected>".sprintf("%'02u",$i)."</option>\n";
		}
		print "    </select> :\n";

		//$mins = $defaultMins[$lfield];
		$mins = $defaultMins['max'];
		print "    <select id='${lfield}_mins' name='${lfield}_mins' class='form-control'/>\n";
		print "      <option/>\n";
		for($i = 0; $i<60;  $i+=15){
			if($i == $mins){
				$selected = " selected='selected'";
			}
			else{
				$selected = "";
			}
			print "      <option $selected>".sprintf("%'02u",$i)."</option>\n";
		}
		print "    </select>\n";
	  
	print "  </div> <!-- /.form-group -->";
	print "</div> <!-- /.col -->";

	

	}

print "</div> <!-- .row -->";

}



print " <div style='display: block; margin-top: 20px;'>";
print "	<button type='submit' id='add_upload' class='btn btn-success btn-lg'>".biodiv_label_icons('upload', JText::_("COM_BIODIV_UPLOAD_UPLOAD"))."</button>";
print " <button type='button' class='btn btn-danger btn-lg mw_help' data-dismiss='modal'>".biodiv_label_icons('help', JText::_("COM_BIODIV_UPLOAD_HELP"))."</button>";
	
print "  </div>";

    

print "</form>";



print "</div>"; // class='col-md-6'

print "<div class='col-md-6'>";



if ($this->previous_upload_id){
  print "<h2>" . JText::_("COM_BIODIV_UPLOAD_LAST_UP") . "</h2>\n";
  print "<strong><p  style='margin-top:30px;'>" . JText::_("COM_BIODIV_UPLOAD_LAST_AT") . " ". $this->previous_upload_date . "</p>";
  if($this->isCamera && $this->previous_collection_date){
    print "<p>" . JText::_("COM_BIODIV_UPLOAD_WITH_DEPL") . " ". $this->previous_deployment_date . "</p>";
	print "<p>" . JText::_("COM_BIODIV_UPLOAD_WITH_COLL") . " ". $this->previous_collection_date . "</p>";
	
	$action_more = $this->root . "&task=upload_more";
	print "<p  style='margin-top:20px;'><a class='btn btn-success btn-lg' role='button' href='$action_more&upload_id=". $this->previous_upload_id. "'>" . biodiv_label_icons('upload', JText::_("COM_BIODIV_UPLOAD_UP_MORE")) . "</a></p>";
  }
  print "</strong>";
 }


print "  <div style='margin-top:20px;'>";
print "  <a class='btn btn-primary btn-lg' role='button' href='" . BIODIV_ROOT."&view=uploaded&site_id=". $this->site_id."'>".biodiv_label_icons('list', JText::_("COM_BIODIV_UPLOAD_LIST_UP"))."</a>";
print "  </div>";


print "</div>"; // class col-md-6


print "<div id='help_modal' class='modal fade' role='dialog'>";
print "  <div class='modal-dialog modal-sm'>";

print "    <!-- Modal content-->";
print "    <div class='modal-content'>";
print "      <div class='modal-header'>";
print "        <button type='button' class='close' data-dismiss='modal'>&times;</button>";
print "        <h4 class='modal-title'>".JText::_("COM_BIODIV_UPLOAD_HELP_TITLE")." </h4>";
print "      </div>";
print "      <div class='modal-body'>";
		if($this->title ){
			print "<div class='well'>\n";
			if ( $this->introtext ) {
				print "<div class='help-article'>".$this->introtext."</div>"; 
			}
			print "</div>\n";
		}
print "      </div>";
print "      <div class='modal-footer'>";
print "        <button type='button' class='btn btn-danger classify-modal-button' data-dismiss='modal'>".JText::_("COM_BIODIV_UPLOAD_CLOSE")."</button>";
print "      </div>";
print "    </div>";

print "  </div>";
print "</div>";


JHTML::script("com_biodiv/upload.js", true, true);
JHTML::script("bootstrap-datepicker-master/bootstrap-datepicker.js", true, true);



?>


