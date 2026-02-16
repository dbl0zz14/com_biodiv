<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*/
 
// No direct access to this file
defined('_JEXEC') or die;
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
* HTML View class for the HelloWorld Component
*
* @since 0.0.1
*/
class BioDivViewRemoveOrphans extends JViewLegacy
{
	/**
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */

	public function display($tpl = null) 
	{
		
		error_log ( "BioDivViewRemoveOrphans display called" );
		
		$reportRoot = JPATH_SITE."/biodivimages/reports";
		$filePath = $reportRoot."/orphans/";
		
		$t=time();
		$dateStr = date("Ymd_His",$t);
		$logfile = 'orphan_log_' . $dateStr . ".txt";
		
		$newFile = $filePath . "/" . $logfile;
		
		// Has the report already been created?
		if ( !file_exists($newFile) ) {
			
			error_log ("File " . $newFile . " created for writing" );
			
			// Creates a new csv file and store it in directory
			// Rename once finished writing to file
			if (!file_exists($filePath)) {
				mkdir($filePath, 0755, true);
			}
			
			$log = fopen ( $newFile, 'w');
		}
			
		
		print "<h1>Removing orphans</h1>";
		fwrite($log,"Removing orphans\n");
		print "<h3>Note that an orphan is a file over a day old which is on local disk but not found in database - happens if disk space issues</h3>";
		fwrite($log,"Note that an orphan is a file over a day old which is on local disk but not found in database - happens if disk space issues\n");
		
		$app = JFactory::getApplication();
		
		$input = $app->input;
		$this->siteId = $input->getInt('site_id', 0);
		$this->actuallyDelete = $input->getInt('delete', 0);
		
		$db = JDatabase::getInstance(dbOptions());
		
		// Get all site_id and person_id
		$query = $db->getQuery(true);
		$query->select("site_id, person_id, site_name")
		->from($db->quoteName("Site"));
		
		if ( $this->siteId ) {
			$query->where("site_id = " . (int)$this->siteId);
		}

		$db->setQuery($query);
		$sites = $db->loadObjectList('site_id');
	  
		foreach ( $sites as $site_id=>$site_details ) {
			
			$site_name = $site_details->site_name;
			$person_id = $site_details->person_id;
		
			// Get list of all files on local disk
			$rootDir = '/var/www/html/biodivimages/person_' . $person_id . '/site_' . $site_id;
			
			if ( !is_dir($rootDir) ) {
				fwrite($log,"Directory " .$rootDir. " not found\n");
				continue;
			}
			
			$fileList = array();
		  
			// set filenames invisible if you want
			$invisibleFileNames = array(".", "..", ".htaccess", ".htpasswd");
			
			// run through content of root directory
			$dirContent = scandir($rootDir);

			if ( !$dirContent ) {
				print "Directory not scanned";
				fwrite($log,"Directory " .$rootDir. " not scanned\n");
				continue;
			}
			foreach($dirContent as $key => $content) {
				// filter all files not accessible
				$path = $rootDir.'/'.$content;
				if(!in_array($content, $invisibleFileNames)) {
					// if content is file & readable, add to array
					if(is_file($path) && is_readable($path)) {
						// save file name with path
						$fileList[] = $path;
					}
				}
			}
			
			print "<h3>Found " . count($fileList) . " potential orphans for site " . $site_id . " </h3>";
			fwrite($log, "Found " . count($fileList) . " potential orphans for site " . $site_id . "\n");

			$numSiteDeletions = 0;
			foreach ( $fileList as $filename ) {

				// For each file, check > 1 day old
				//$fileTime = new DateTime(filemtime($filename));
				//$timeNow = new DateTime();
				
				//$interval = date_diff ( $fileTime, $timeNow );

				$diff = time() - filemtime($filename);

				
				if ( $diff > 172800 ) {
				
					// Check whether filename exists for that site in database.
					// Check Photo and OriginalFiles
					$query = $db->getQuery(true);
					$query->select("count(*)")
					->from($db->quoteName("Photo"))
					->where("site_id = " . $site_id)
					->where("filename = " . $db->quote($filename));

					$db->setQuery($query);
					$numPhotos = $db->loadResult();
					
					if ( $numPhotos == 0 ) {
						
						$query = $db->getQuery(true);
						$query->select("count(*)")
						->from($db->quoteName("OriginalFiles"))
						->where("site_id = " . $site_id)
						->where("filename = " . $db->quote($filename));

						$db->setQuery($query);
						$numPhotos = $db->loadResult();
					}
	  
					// If not delete file
					if ( $numPhotos == 0 ) {
						$numSiteDeletions += 1;
						// print "Found orphan to delete: " . $filename;
						// fwrite($log,"Found orphan to delete: " . $filename . "\n");
						
						if ( $this->actuallyDelete ) {
							if (!unlink($filename)) { 
								print $filename . " cannot be deleted due to an error"; 
							} 
						}
				
					}
					else {
						// print "Found file " . $filename . " in database - not orphan";
						// fwrite($log,"Found file " . $filename . " in database - not orphan\n");
				
					}
				}	
			}
			fwrite($log,"Found " . $numSiteDeletions . " orphans to delete from site: " . $site_name . ", id: " . $site_id . "\n");		
		}
		
		fclose($log);

		// Display the view
		parent::display($tpl);
    }
	
}



?>

