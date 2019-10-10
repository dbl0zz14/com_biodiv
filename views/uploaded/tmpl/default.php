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

<h1><?php print $this->translations['up_from']['translation_text'] . ' ' . $this->site_name;?></h1>

<table class='table'>
<thead>
<tr>
<th><?php print $this->translations['file_name']['translation_text'];?></th>
<th><?php print $this->translations['date']['translation_text'];?></th>
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

  <div class='lead'><a href='<?php print BIODIV_ROOT."&view=upload&site_id=". $this->site_id;?>'><?php print $this->translations['more_ph']['translation_text'];?></a></p>

