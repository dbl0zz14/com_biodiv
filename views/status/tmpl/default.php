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
<?php
print "<p><b>My Projects ";
foreach($this->projects as $project_name  ){
  print " <span class='badge'>$project_name</span> ";
 }
print "</b></p>\n";
?>

<form action = "<?php print BIODIV_ROOT;?>" method = 'GET'>
    <input type='hidden' name='view' value='classify'/>
    <input type='hidden' name='option' value='<?php print BIODIV_COMPONENT;?>'/>
    <p>
    <b>My Current Project:</b>
    <select name = 'my_project'>
      <?php
        foreach($this->projects as $proj){
          print "<option value='$proj'>$proj</option>";
        }
      ?>
    </select>
    </p>
	<button  class='btn btn-primary' type='submit'><i class='fa fa-search'></i> Get Spotting</a></button>
    <input type='checkbox' name = 'classify_self' value='1'/> Classify my images first</input>
	<input type='checkbox' name = 'classify_project' value='1'/> Classify my current project first</input>
	<input type='checkbox' name = 'classify_only_project' value='1'/> Classify only my current project</input>

</form>



