<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;
$action = $this->root . "&view=uploadm";

?>

<h1>Uploaded images from <?php print $this->site_name;?></h1>

<table class='table'>
<thead>
<tr>
<th>File name</th>
<th>Date</th>
</tr> 
</thead>
<tbody>
<?php
  foreach($this->photos as $photoLine){
  print "<tr>";
  print "<td>" . $photoLine['upload_filename'] . "</td>";
  print "<td>" . $photoLine['taken'] . "</td>";
  print "</tr>\n";
}

?>

</tbody>
</table>

  <div class='lead'><a href='<?php print BIODIV_ROOT."&view=upload&site_id=". $this->site_id;?>'>Upload more photos</a></p>

