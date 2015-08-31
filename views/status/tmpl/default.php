<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;
?>
<h1>Spotter Status</h1>
<?php
foreach($this->status as $msg => $count){
  print "<p><b>$msg <span class='badge'>$count</span></b></p>\n";
 }
?>
<form action = "<?php print BIODIV_ROOT?>" method = 'GET'>
    <input type='hidden' name='view' value='classify'/>
    <input type='hidden' name='option' value='com_biodiv'/>
    <button  class='btn btn-primary' type='submit'><i class='fa fa-search'></i> Get Spotting</a></button>
<!--    <input type='checkbox' name = 'self'/>Classify my images first</input> -->
</form>



