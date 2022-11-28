<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

printAdminMenu("TRANSLATIONS");
	
print '<div id="j-main-container" class="span10 j-toggle-main">';
print '<h2>MammalWeb translations page</h2>';
print '<p>What would you like to do?</p>';
print '<p><a href="index.php?option=com_biodiv&amp;view=generate&species=1"><button>Generate species (Options) data for translation</button></a></p>';
print '<p><a href="index.php?option=com_biodiv&amp;view=generate&opt=1"><button>Generate non-species (Options) data for translation</button></a></p>';
print '<p><a href="index.php?option=com_biodiv&amp;view=selectarticle&purpose=sendtranslations"><button>Send Joomla articles for translation</button></a></p>';
print '<p><a href="index.php?option=com_biodiv&amp;view=selectarticle&purpose=gettranslations"><button>Get translated Joomla articles</button></a></p>';
print '</div>';





?>


