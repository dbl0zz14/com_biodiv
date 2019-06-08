<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

?>
<h1>Updating sites</h1>
<button id="calculate_lat_long">Calculate</button>
<form id='update_latlong_form' action='<?php print BIODIV_ROOT . "&task=update_lat_long"; ?>' method='post'>

<?php
	// Create a form with lots of rows of sites, then use the javascript library to update the lat long.
	// The Apply button submits the data and calls the new controller function to write the new values.
	foreach ($this->sites as $site_id=>$site) {
		// write a row of the form
		print "<input class='site_input_field' name='site[".$site_id."]' value='".$site_id."'/>";
		print "<input name='grid[".$site_id."]' value='".$site['grid_ref']."'/>";
		print "<input name='lat[".$site_id."]'/>";
		print "<input name='lon[".$site_id."]'/>";
		print "<br>";
	}

?>

<input type="submit" value="Write to Db">
</form>

<?php

JHTML::script("com_biodiv/geodesy-master/vector3d.js", true, true);
JHTML::script("com_biodiv/geodesy-master/latlon-ellipsoidal.js", true, true);
JHTML::script("com_biodiv/geodesy-master/osgridref.js", true, true);
JHTML::script("com_biodiv/geodesy-master/dms.js", true, true);
JHTML::script("com_biodiv/updatesites.js", true, true);

?>

