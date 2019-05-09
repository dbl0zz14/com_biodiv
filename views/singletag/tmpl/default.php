<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;
//print "<div>\n";
$nothingDisabled = false;
if ( !$this->person_id ) {
	print "<div id='no_user_id'></div>";
}
else if(!$this->animal){
  print "";
 }
 else{
	 $label = codes_getName($this->animal->species, 'content');
     $contentDetails = codes_getDetails($this->animal->species, 'content');
     $type = $contentDetails['struc'];
     $features = array();
     if($type == 'like'){
       // do nothing
     }
     if($type == 'noanimal'){
       $btnClass = 'btn-primary';
	   /*
	   if ( $label != 'Nothing' ) {
		   $nothingDisabled = true;
	   }
	   */
	   if ( $this->animal->species != 86 ) {
		   $nothingDisabled = true;
	   }
	   else {
		   $btnClass .= ' nothing-classification';
	   }
	 }
     else if($type== 'notinlist'){
       $btnClass = 'btn-primary';
	   $nothingDisabled = true;
     }
     else{
       if($this->animal->number >1){
	     $features[] = $this->animal->number;
       }
       foreach(array("gender", "age") as $struc){
	     $featureName = codes_getName($this->animal->$struc, $struc);
	     if($featureName != "Unknown"){
	       $features[] = $featureName;
	     }
       }
	   if ( $type == 'mammal' ) {
			$btnClass = 'btn-warning';
	   }
	   else {
		   $btnClass = 'btn-info';
	   }
       if(count($features) >0){
	     $label .= " (" . implode(",", $features) . ")";
       }
	   $nothingDisabled = true;
     }

     print "<button id='remove_animal_". $this->animal->animal_id."' type='button' class='remove_animal btn $btnClass'>$label <span aria-hidden='true' class='fa fa-times-circle'></span><span class='sr-only'>Close</span></button>\n";
 
   
 }
//print "</div> \n";
if ( $nothingDisabled == true ) {
	print "<div id='nothingDisabled'></div>\n";
}
else {
	print "<div id='nothingEnabled'></div>\n";
}
?>