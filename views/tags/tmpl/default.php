<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;
print "<div>\n";
if(count($this->animals) == 0){
  print "<p class='text-primary lead'>No classification yet</p>";
 }
 else{
   foreach($this->animals as $animal_id => $details){
     $label = codes_getName($details->species, 'content');
     $contentDetails = codes_getDetails($details->species, 'content');
     $type = $contentDetails['struc'];
     $features = array();
     if($type == 'like'){
       continue;
     }
     if($type == 'noanimal' || $type== 'notinlist'){
       $btnClass = 'btn-warning';
     }
     else{
       if($details->number >1){
	 $features[] = $details->number;
       }
       foreach(array("gender", "age") as $struc){
	 $featureName = codes_getName($details->$struc, $struc);
	 if($featureName != "Unknown"){
	   $features[] = $featureName;
	 }
       }
       $btnClass = 'btn-primary';
       if(count($features) >0){
	 $label .= " (" . implode(",", $features) . ")";
       }
     }

     print "<div><button id='remove_animal_${animal_id}' type='button' class='remove_animal btn $btnClass btn-lg'>$label <span aria-hidden='true' class='fa fa-times-circle'></span><span class='sr-only'>Close</span></button></div>\n";
   }
 }
print "</div> \n";
?>