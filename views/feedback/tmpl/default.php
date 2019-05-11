<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;
print '<div id="feedback" class="jumbotron text-center" data-project-img="'.$this->projectImageUrl.'" >';

print "<h2>Thank you for being a citizen scientist and classifying 10 sequences!</h2>";

//print "<p>Species league table</p>";

//print "<p>You spotted....</p>";

print "<form action = '".BIODIV_ROOT."&".$this->user_key."' method = 'GET'>";
print "    <input type='hidden' name='view' value='startkiosk'/>";
print "    <input type='hidden' name='option' value='".BIODIV_COMPONENT."'/>";
print "    <input type='hidden' name='project_id' value='".$this->project_id."' />";
print "	<button  id='start-again-btn' class='btn btn-danger' type='submit'>Start Again</button>";
print "</form>";


print "</div>";



JHTML::stylesheet("com_biodiv/com_biodiv.css", array(), true);
JHTML::script("com_biodiv/feedback.js", true, true);

?>