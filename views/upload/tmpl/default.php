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

<h1>Upload images from <?php print $this->site_name;?> ID <?php print $this->site_id;?></h1>

<?php if ($this->previous_upload_id){
  print "<h3>Last upload</h3>\n";
  print "<p>The last upload was at ". $this->previous_upload_date;
  if($this->previous_collection_date){
    print " with a collection date of ". $this->previous_collection_date;
  }
  print ".</p> <p><a class='btn btn-primary btn-lg active' role='button' href='$action&upload_id=". $this->previous_upload_id. "'>Upload more files within these dates</a></p>";
 }


?>

  <div class='lead'><a href='<?php print BIODIV_ROOT."&view=uploaded&site_id=". $this->site_id;?>'>List photos already uploaded</a></p>

<h1>Start new upload</h1>

<form role='form' action='<?php print $this->root . "&task=add_upload";?>&site_id=<?php print $this->site_id;?>'
 method='post' class='form-inline'>
<div class='container'>

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
?>
<div class='row'>

<div class='col-sm-6 col-md-2'>
   <div class='form-group'>
  <?php print  "<label for='${lfield}_date' style='width: 10em'>$field date</label>\n";?>
  <?php print "<input type='text' size='10' class='form-control' name='${lfield}_date' id='${lfield}_date'/>";?>
  </div>
</div> 

<div class='col-sm-6 col-md-2'>
      <div class='form-group'>
      <?php  
      print  "<label for='${lfield}_time' style='width:10em'>$field time</label>\n";


  $hours = $defaultHours[$lfield];
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

  $mins = $defaultMins[$lfield];
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



  <div><button type='submit' id='add_upload' class='btn btn-primary'><?php print biodiv_label("upload");?></button></div>

</div> <!-- /.container -->  
</form>

<?php
JHTML::script("com_biodiv/upload.js", true, true);
JHTML::script("bootstrap-datepicker-master/bootstrap-datepicker.js", true, true);

?>


