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
<div class='row'>
<div class='col-md-5'>
<p>
<table class="table">
<?php
foreach($this->status as $msg => $count){
  print "<tr><td>$msg</td><td>$count</td></tr>\n";
 }
?>
</table>
</p>
<p>
<form action = "<?php print BIODIV_ROOT;?>" method = 'GET'>
    <input type='hidden' name='view' value='classify'/>
    <input type='hidden' name='option' value='<?php print BIODIV_COMPONENT;?>'/>
    <button  class='btn btn-warning btn-block' type='submit'><i class='fa fa-search'></i> Classify All</a></button>
</form>
</p>
<p>
<form action = "<?php print BIODIV_ROOT;?>" method = 'GET'>
    <input type='hidden' name='view' value='classify'/>
    <input type='hidden' name='option' value='<?php print BIODIV_COMPONENT;?>'/>
    <input type='hidden' name='classify_self' value='1'/>
    <button  class='btn btn-warning btn-block' type='submit'><i class='fa fa-search'></i> Classify My Images Only</a></button>
    
</form>
</p>
<p>
<form action = "<?php print BIODIV_ROOT;?>" method = 'GET'>
<div class="input-group">
    <input type='hidden' name='view' value='classify'/>
    <input type='hidden' name='option' value='<?php print BIODIV_COMPONENT;?>'/>
    <input type='hidden' name='classify_only_project' value='1'/>
	<select name = 'my_project' class = 'form-control'>
	  <option value="" disabled selected hidden>Select a project...</option>
    
      <?php
        foreach($this->projects as $proj){
          print "<option value='$proj'>$proj</option>";
        }
      ?>
    </select>
	<span class="input-group-btn">
      <button  class='btn btn-warning' type='submit'><i class='fa fa-search'></i> Classify Selected Project Only</a></button>
	</span>
	
</div>
</form>
</p>
</div>
<?php
//print "<p><b>Projects:";
//foreach($this->projects as $project_name  ){
//  print " <span class='badge'>$project_name</span> ";
// }
//print "</b></p>\n";
?>
<div class='col-md-6'>
<div id="myCarousel" class="carousel slide" data-ride="carousel"  data-wrap="false">
  <!-- Indicators -->
  

  <!-- Wrapper for slides -->
  <div class="carousel-inner">
<?php
$first = true;
foreach($this->mylikes as $photo_id  ){
	if ($first) {
		print '<div class="item active">';
		$first = false;
	}
	else {
		print '<div class="item">';
	}
	print JHTML::image(photoURL($photo_id), 'Photo ' . $photo_id, array('class' =>'img-responsive'));
	print '</div>';
 }
 if ( $first == true ) {
	// no likes so use a default image
	print '<div class="item active">';
	print JHTML::image(projectImageURL(1), 'Mammal Web Photo', array('class' =>'img-responsive'));
	print '</div>';
	 
 }

?>

  </div>

  <!-- Left and right controls -->
  <?php
  if (count($this->mylikes) > 1 ) {
  print '<a class="left carousel-control" href="#myCarousel" data-slide="prev">';
  print '  <span class="glyphicon glyphicon-chevron-left"></span>';
  print '  <span class="sr-only">Previous</span>';
  print '</a>';
  print '<a class="right carousel-control" href="#myCarousel" data-slide="next">';
  print '  <span class="glyphicon glyphicon-chevron-right"></span>';
  print '  <span class="sr-only">Next</span>';
  print '</a>';
  }
  ?>
</div>
</div>

</div>



