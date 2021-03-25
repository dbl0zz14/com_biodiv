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


print "<h1>" . $this->translations['upload_im']['translation_text'] . " " . $this->site_name . " " . $this->translations['id']['translation_text'] . " " . $this->site_id . "</h1>";


print "<div class='col-md-6'>";


print "<h2>". $this->translations['start_new']['translation_text']."</h2>";


print "<form role='form' action='".$this->root . "&task=add_upload&site_id=".$this->site_id."'";
print " method='post' class='form-inline'>";


//print "<div class='container'>";

// Deployment date and times only relevant for camera deployments, as opposed to recordings on phones etc.
// However timezone is relevant for all media types
//	print "<div class='row' style='margin-top:10px;'>";
//	print "  <div class='col-sm-6 col-md-8'>";
	print "    <div class='form-group'>";
		 
		  $timezone_text = $this->translations['timezone']['translation_text'];
		  $dst_text = $this->translations['dst']['translation_text'];
		  $dst_yes = $this->translations['dst_yes']['translation_text'];
		  $dst_no = $this->translations['dst_no']['translation_text'];
		  $timezone_abbreviations = DateTimeZone::listAbbreviations();
		  $timezone_ids = DateTimeZone::listIdentifiers();
		  
		  print  "<label for='timezone' style='display:block; margin-top: 20px;'>$timezone_text</label>";
		  
		  print  "<select id='timezone' name ='timezone' class='form-control'/>\n";
		  print  "<option value='' disabled selected hidden>".$this->translations['pls_select']['translation_text']."</option>";
		  
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
	error_log ( "default dates: " . $err_str );

	$err_str = print_r ( $defaultHours, true );
	error_log ( "default hours: " . $err_str );

	$err_str = print_r ( $defaultMins, true );
	error_log ( "default mins: " . $err_str );



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
		  
		$date_text = $this->translations['dep_date']['translation_text'];
		$time_text = $this->translations['dep_time']['translation_text'];
		if ( $field == "Collection" ) {
			$date_text = $this->translations['coll_date']['translation_text'];
			$time_text = $this->translations['coll_time']['translation_text'];
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
print "	<button type='submit' id='add_upload' class='btn btn-success btn-lg'>".biodiv_label_icons('upload', $this->translations['upload']['translation_text'])."</button>";
print " <button type='button' class='btn btn-danger btn-lg mw_help' data-dismiss='modal'>".biodiv_label_icons('help', $this->translations['help']['translation_text'])."</button>";
	
print "  </div>";

    

//print "</div> <!-- /.container -->  ";
print "</form>";

print "</div>"; // class='col-md-6'

print "<div class='col-md-6'>";



if ($this->previous_upload_id){
  print "<h2>" . $this->translations['last_up']['translation_text'] . "</h2>\n";
  print "<strong><p  style='margin-top:30px;'>" . $this->translations['last_at']['translation_text'] . " ". $this->previous_upload_date . "</p>";
  if($this->isCamera && $this->previous_collection_date){
    print "<p>" . $this->translations['with_depl']['translation_text'] . " ". $this->previous_deployment_date . "</p>";
	print "<p>" . $this->translations['with_coll']['translation_text'] . " ". $this->previous_collection_date . "</p>";
	
	//print ".</p> <p  style='margin-top:20px;'><a class='btn btn-primary' role='button' href='$action&upload_id=". $this->previous_upload_id. "'>" . biodiv_label_icons('upload', $this->translations['up_more']['translation_text']) . "</a>";
	
	$action_more = $this->root . "&task=upload_more";
	print "<p  style='margin-top:20px;'><a class='btn btn-success btn-lg' role='button' href='$action_more&upload_id=". $this->previous_upload_id. "'>" . biodiv_label_icons('upload', $this->translations['up_more']['translation_text']) . "</a></p>";
  }
  print "</strong>";
 }




//print "  <div class='lead'><a href='" . BIODIV_ROOT."&view=uploaded&site_id=". $this->site_id."'>".$this->translations['list_up']['translation_text']."</a></p>";
print "  <div style='margin-top:20px;'>";
print "  <a class='btn btn-primary btn-lg' role='button' href='" . BIODIV_ROOT."&view=uploaded&site_id=". $this->site_id."'>".biodiv_label_icons('list', $this->translations['list_up']['translation_text'])."</a>";
print "  </div>";


print "</div>"; // class col-md-6


print "<div id='help_modal' class='modal fade' role='dialog'>";
print "  <div class='modal-dialog modal-sm'>";

print "    <!-- Modal content-->";
print "    <div class='modal-content'>";
print "      <div class='modal-header'>";
print "        <button type='button' class='close' data-dismiss='modal'>&times;</button>";
print "        <h4 class='modal-title'>".$this->translations['help_title']['translation_text']." </h4>";
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
print "        <button type='button' class='btn btn-danger classify-modal-button' data-dismiss='modal'>".$this->translations['close']['translation_text']."</button>";
print "      </div>";
print "    </div>";

print "  </div>";
print "</div>";


JHTML::script("com_biodiv/upload.js", true, true);
JHTML::script("bootstrap-datepicker-master/bootstrap-datepicker.js", true, true);

?>


