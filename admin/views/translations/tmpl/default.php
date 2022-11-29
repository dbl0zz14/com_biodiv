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

print '<h3>Send articles to Transifex</h3>';
print '<p>Get a list of articles, most recently edited first.  You can then choose a set of articles and send to Transifex for translation.  Use this for any new article content, in particular new species articles.</p>';
print '<p><a href="index.php?option=com_biodiv&amp;view=selectarticle&purpose=sendtranslations"><button>Send Joomla articles for translation</button></a></p>';

print '<h3>Get translated articles from Transifex</h3>';
print '<p>Get a list of articles, most recently edited first.  You can then choose a set of articles and language(s) for which to to download the translations, and then click to update/create the articles in Joomla.</p>';
print '<p><a href="index.php?option=com_biodiv&amp;view=selectarticle&purpose=gettranslations"><button>Get translated Joomla articles</button></a></p>';

print '<h3>Generate Options translation file</h3>';
print '<p>This option generates a csv file of species names, which you then download to your local machine and import into Excel before manually uploading to Transifex.  Used when new species name has to be translated, or (by developer) when a new language is installed.</p>';
print '<p><a href="index.php?option=com_biodiv&amp;view=generate&species=1"><button>Generate species (Options) data for translation</button></a></p>';

print '<p>Smilar to the above, for non-species options.  This option generates a csv file of Options, which are not species, which you then download to your local machine and import into Excel before manually uploading to Transifex.  Used when new option has to be translated, or (by developer) when a new language is installed.</p>';
print '<p><a href="index.php?option=com_biodiv&amp;view=generate&opt=1"><button>Generate non-species (Options) data for translation</button></a></p>';
print '</div>';





?>


