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
?>

<h1><?php print $this->translations['upload_im']['translation_text'] . ' ' . $this->site_name . ' ' . $this->translations['id']['translation_text'] . ' ' . $this->site_id; ?></h1>



<?php 

print "<button type='button' class='btn btn-danger mw_help' style='margin-top:10px' data-dismiss='modal'>".biodiv_label_icons("help", $this->translations['help']['translation_text'])."</button>";


if ($this->previous_upload_id){
  print "<h3>" . $this->translations['last_up']['translation_text'] . "</h3>\n";
  print "<p>" . $this->translations['last_at']['translation_text'] . " ". $this->previous_upload_date;
  if($this->previous_collection_date){
    print " " . $this->translations['with_coll']['translation_text'] . " ". $this->previous_collection_date;
  }
  print ".</p> <p><a class='btn btn-primary btn-lg active' role='button' href='$action&upload_id=". $this->previous_upload_id. "'>" . $this->translations['up_more']['translation_text'] . "</a></p>";
 }


?>

  <div class='lead'><a href='<?php print BIODIV_ROOT."&view=uploaded&site_id=". $this->site_id;?>'><?php print $this->translations['list_up']['translation_text'] ?></a></p>

<h1><?php print $this->translations['start_new']['translation_text'] ?></h1>


<form role='form' action='<?php print $this->root . "&task=add_upload";?>&site_id=<?php print $this->site_id;?>'
 method='post' class='form-inline'>
<div class='container'>
<div class='row' style="margin-top:10px;">
  <div class='col-sm-6 col-md-8'>
    <div class='form-group'>
    <?php  
      $timezone_text = $this->translations['timezone']['translation_text'];
	  $dst_text = $this->translations['dst']['translation_text'];
	  $dst_yes = $this->translations['dst_yes']['translation_text'];
	  $dst_no = $this->translations['dst_no']['translation_text'];
	  $timezone_abbreviations = DateTimeZone::listAbbreviations();
	  $timezone_ids = DateTimeZone::listIdentifiers();
	  
	  print  "<label for='timezone' style='width:30em'>$timezone_text</label>";
	  
	  print  "<select id='timezone' name ='timezone' class='form-control'/>\n";
	  print  "<option value='' disabled selected hidden>".$this->translations['pls_select']['translation_text']."</option>";
	  
	  // Make UTC the first option:
	  $utc = array_pop($timezone_ids);
	  print "<option id='UTC'>$utc</option>\n"; // - UTC+00:00</option>\n";
	  foreach ($timezone_ids as $zone_id) {
		print "<option id='$zone_id'>" . $zone_id . "</option>\n";
	  }
	  print "</select>";
	  
	  print  "<label for='dst' style='width:30em; margin-top:10px;'>$dst_text</label>\n";
	  
	  print  "<div><input type='radio' id='dst_yes' name ='dst' value='1'> " . $dst_yes . "</div>";
	  
	  print  "<div><input type='radio' id='dst_no' name ='dst' value='0' checked> " . $dst_no . "</div>";
	  
	?>

    </div> <!-- /.form-group -->
  </div> <!-- /.col -->
  

</div>  <!--div class='row' -->

<?php

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


$document = JFactory::getDocument();
foreach($defaultDate as $what => $date){
  $hours = $defaultHours[$what];
  $mins = $defaultMins[$what];
  //  $document->addScriptDeclaration("BioDiv.${what}_date = '$date';\n");
  //  $document->addScriptDeclaration("BioDiv.${what}_hours = '$hours';\n");
  //  $document->addScriptDeclaration("BioDiv.${what}_mins = '$mins';\n");
 }

foreach(array("Deployment", "Collection") as $field){
  $lfield = strtolower($field);
  
  $date_text = $this->translations['dep_date']['translation_text'];
  $time_text = $this->translations['dep_time']['translation_text'];
  if ( $field == "Collection" ) {
	  $date_text = $this->translations['coll_date']['translation_text'];
      $time_text = $this->translations['coll_time']['translation_text'];
  
  }
  
  
?>
<div class="row" style="margin-top:10px;">
<div class='col-sm-6 col-md-2'>
   <div class='form-group'>
  <?php print  "<label for='${lfield}_date' style='width: 10em'>$date_text</label>\n";?>
  <?php print "<input type='text' size='10' class='form-control' name='${lfield}_date' id='${lfield}_date'/>";?>
  </div>
</div> <!-- /.col -->

<div class='col-sm-6 col-md-6'>
      <div class='form-group'>
      <?php  
      print  "<label for='${lfield}_time' style='width:30em'>$time_text</label>\n";


  //$hours = $defaultHours[$lfield];
  $hours = $defaultHours['max'];
  print  "<select id='${lfield}_hours' name ='${lfield}_hours' class='form-control'/>\n";
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
  print "</select> :\n";
?>

 <?php

  //$mins = $defaultMins[$lfield];
  $mins = $defaultMins['max'];
  print  "<select id='${lfield}_mins' name='${lfield}_mins' class='form-control'/>\n";
  print "<option/>\n";
  for($i = 0; $i<60;  $i+=15){
    if($i == $mins){
      $selected = " selected='selected'";
    }
    else{
      $selected = "";
    }
    print "<option $selected>".sprintf("%'02u",$i)."</option>\n";
  }
  print "</select>\n";
  
  ?>
  </div> <!-- /.form-group -->
</div> <!-- /.col -->

</div> <!-- .row -->
<?php
}
?>



  <div style="margin-top:15px;">
	<button type='submit' id='add_upload' class='btn btn-primary'><?php print biodiv_label_icons("upload", $this->translations['upload']['translation_text']);?></button>
	
  </div>

    

</div> <!-- /.container -->  
</form>

<div id="help_modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?php print $this->translations['help_title']['translation_text']; ?> </h4>
      </div>
      <div class="modal-body">
<?php
		if($this->title ){
			print "<div class='well'>\n";
			if ( $this->introtext ) {
				print "<div class='help-article'>".$this->introtext."</div>"; 
			}
			print "</div>\n";
		}
 ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger classify-modal-button" data-dismiss="modal"><?php print $this->translations['close']['translation_text']; ?></button>
      </div>
    </div>

  </div>
</div>

<?php
JHTML::script("com_biodiv/upload.js", true, true);
JHTML::script("bootstrap-datepicker-master/bootstrap-datepicker.js", true, true);

?>


