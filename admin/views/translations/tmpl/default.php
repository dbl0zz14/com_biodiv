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

print '<h3>Generate Species translation files</h3>';
print '<p>Here you can generate a csv file of species names, which you then download to your local machine and import into Excel before manually uploading to Transifex.  Used when new species name has to be translated, or (by developer) when a new language is installed.</p>';

foreach ( $this->speciesTrans as $spTran ) {
	print '<p><a href="index.php?option=com_biodiv&amp;view=generate&species=1&tranid='.$spTran->id.'"><button>Generate '.$spTran->list_name.' species data for translation</button></a></p>';
}

print '<h3>Generate Options translation files</h3>';
print '<p>Smilar to the above, for non-species options.  This option generates a csv file of Options, which are not species, which you then download to your local machine and import into Excel before manually uploading to Transifex.  Used when new option has to be translated, or (by developer) when a new language is installed.</p>';
print '<p><a href="index.php?option=com_biodiv&amp;view=generate&opt=1"><button>Generate non-species (Options) data for translation</button></a></p>';



print '<h3>Translate using DeepL</h3>';
print '<p>Translate the Joomla text snippets file to your chosen language using DeepL.  Can then manually upload to Transifex for review</p>';
print '<p><a href="index.php?option=com_biodiv&amp;view=deepl"><button>Translate Joomla text snippets using DeepL</button></a></p>';
print '</div>';





?>


