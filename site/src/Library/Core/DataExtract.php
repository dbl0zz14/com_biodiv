<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/

namespace MammalWeb\Component\Biodiv\Site\Library\Core;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseFactory;
use Joomla\CMS\Language\Text;

 
// No direct access to this file
defined('_JEXEC') or die;


class FlxZipArchive extends \ZipArchive 
{
	// Max zip file size
	const ZIP_SIZE_THRESHOLD = 400000000;
	
	private $zipFull;
	private $nextLocation;
	private $nextName;
	
	private $oversizedLocations;
	
	public static function createArrayOfZipFiles ( $projectZipFileStem, $location, $name ) {
		
		$zips = array();
		$moreToDo = true;
		$label = 1;
		$isContinue = false;
		$nextL = null;
		$nextN = null;
		
		set_time_limit ( 300 );
		
		$allOversizedLocations = null;
		
		while ( $moreToDo ) { 
		
			$zipFile = $projectZipFileStem . "_" . $label . ".zip";
			
			error_log ( "Creating zip file " . $zipFile );
			
			$zip = new FlxZipArchive;
			
			if ( $zip->open($zipFile, \ZipArchive::CREATE ) === TRUE ) {
				
				$zip->oversizedLocations = $allOversizedLocations;
			
				$zip->startAddDir($location, $name, $isContinue, $nextL, $nextN);
				$zip->close();
				
				$allOversizedLocations = $zip->oversizedLocations;
				
				$zips[] = $zipFile;
				
				if ( !$zip->zipFull ) {
					$moreToDo = false;
				}
				else {
					$isContinue = true;
					$nextL = $zip->nextLocation;
					$nextN = $zip->nextName;
				}
			}
			
			$label += 1;
		}
		return $zips;
	}
	
	
	public static function removeDir($dir)
	{
		if (is_dir($dir)) {
			$files = scandir($dir);
			foreach ($files as $file) {
				if ($file != "." && $file != "..") {
					if (filetype($dir . "/" . $file) == "dir")
						self::removeDir($dir . "/" . $file);
					else
						unlink($dir . "/" . $file);
				}
			}
			reset($files);
			rmdir($dir);
		}
	}
	
	public function startAddDir ( $location, $name, $isContinuation = false, $continueLocation = null, $continueName = null ) {
		
		$name .= '/';
		$location .= '/';
		$dir = opendir ($location);
		$reachedContinue = false;
		
		if ( !$this->oversizedLocations ) {
			$this->oversizedLocations = array();
		}
						
		while ($projectDir = readdir($dir))	{
			
			if ($projectDir == '.' || $projectDir == '..') continue;
			
			$newLocation = $location.$projectDir;
			$newName = $name.$projectDir;
			
			if ( $isContinuation && in_array($newLocation, $this->oversizedLocations) ) {
				
				error_log ( $newLocation . " is over sized so skipping" );
				continue;
			}
		
						
			if ( !$isContinuation || $reachedContinue || ($isContinuation && ($continueLocation == $newLocation) && ($continueName == $newName)) ) {
			
				$reachedContinue = true;
				
				$this->addDir($newLocation, $newName);		
			
				if ( $this->zipFull ) {
					
					$nameToRemove = $newName . '/';
					
					error_log ( "Zip file full, removing current project from zip: " . $nameToRemove );
					
					$fileCount = 0;
					
					$classifyTypes = array('CAI','HUMAN');
					$dataFileNames = array('datapackage.json', 'deployments.csv', 'media.csv', 'observations.csv' );
					foreach ( $classifyTypes as $classifyType ) {
						foreach ( $dataFileNames as $dataFileName ) {
							
							$fileToRemove = $nameToRemove . $classifyType . '/' .$dataFileName;
							if ( $indexToRemove = $this->locateName ( $fileToRemove ) ) {
								error_log ( "Located " . $fileToRemove . ", removing from zip" );
								$this->deleteIndex ( $indexToRemove );	
							}
							$fileCount += 1;
							if ( $fileCount > 8 ) {
								error_log ( "Reached max file count" );
								break;
							}
						}
						
						error_log ( "Removing directory " . $nameToRemove . $classifyType . '/' . " from zip" );
						$this->deleteName ( $nameToRemove . $classifyType . '/' );
						
					}
					$this->deleteName ( $nameToRemove );
					
					// If zip file is now empty, whole project is too big for archive so treat separately.....
					$anyFiles = false;
					for ( $i = 0; $i < $this->count(); $i++ ) {
						if ( strlen($this->getNameIndex($i)) > 0 ) {
							$anyFiles = true;
							break;
						}
					}
					if ( !$anyFiles ) {
						$this->oversizedLocations[] = $newLocation;
					}
					
					$this->nextLocation = $newLocation;
					$this->nextName = $newName;
					break;
				}
			}
		}
	}
	
	private function getZipSize () {
		
		$totalSize = 0;
		for ($i = 0; $i < $this->numFiles; $i++) {
			$stat = $this->statIndex($i);
			$totalSize += $stat['size'];
		}
		return $totalSize;
	}
	
	
	public function addDir($location, $name) {
		
		$this->addEmptyDir($name);
		$this->addDirDo($location, $name);
		if ( $this->zipFull ) {
			
			return false;
		}
		
	} 
	private function addDirDo($location, $name) 
	{
		$name .= '/';
		$location .= '/';
		$dir = opendir ($location);
		while ($file = readdir($dir))
		{
			if ($file == '.' || $file == '..') continue;
			
			$do = (filetype( $location . $file) == 'dir') ? 'addDir' : 'addFile';
			
			if ( $do == 'addFile' ) {
				
				$zipSize = $this->getZipSize();
				$fileSize = filesize($location . $file);
				if ( $zipSize + $fileSize > self::ZIP_SIZE_THRESHOLD ) {
					
					$this->zipFull = true;
				}
			}
			if ( !$this->zipFull ) {
				$this->$do($location . $file, $name . $file);	
			}				
			
		}
		
	} 
}




class DataExtract {
	
	public static function generateNbn ( $region, $filename ) {
	
		$nbnSettings = json_decode(Biodiv::getSetting("nbn"));
		$excludeProjects = implode(',', $nbnSettings->exclude_projects);
		$excludeUsers = implode(',', $nbnSettings->exclude_users);
		$intervals = $nbnSettings->intervals;
		$thresholds = $nbnSettings->thresholds;
		
		$regionId = codes_getCode ( $region, 'region' );
		
		if ( $regionId ) {
		
			$db = db();
			
			$query = $db->getQuery(true);

			$query->delete($db->quoteName('NBNVotes'));
			
			$db->setQuery($query);

			$result = $db->execute();
			
			
			$query = $db->getQuery(true);
			$query->select("MAX(photo_id) from Animal");
			$db->setQuery($query);
			$maxPhotoId = $db->loadResult();
			
			if ( $maxPhotoId > 0 ) {
				
				$numThresholds = count($thresholds);
				
				$maxIndex = $numThresholds - 1;
				for ( $i=$numThresholds-1; $i > 0; $i-- ) {
					if ( $maxPhotoId < $thresholds[$i] ) {
						unset ( $thresholds[$i] );
					}
				}
				
				$numThresholds = count($thresholds);
				
				for ( $i=0; $i < $numThresholds; $i++ ) {
					
					$start = $thresholds[$i];
					$interval = $intervals[$i];
					
					if ( $i+1 < $numThresholds ) {
						$end = $thresholds[$i+1];
					}
					else {
						$end = $maxPhotoId;
					}
				
					
					for ( $j=$start; $j<=$end; $j+=$interval) {
						
						$intervalMax = min($end, $j + $interval);
						
						
						$query = $db->getQuery(true);
			  
						$columns = array('photo_id', 'species_id', 'num_votes', 'num_users', 'modal_number', 'id_by');
						
						$query->select("distinct A.photo_id, A.species, ".
								"(SELECT count(distinct A3.person_id) from Animal A3 WHERE A3.photo_id = A.photo_id and A3.species = A.species and A3.number<51 and A3.person_id not in (".$excludeUsers.") ) as num_votes, ".
								"(SELECT count(distinct A2.person_id) from Animal A2 WHERE A2.photo_id = A.photo_id and A2.number<51 and A2.person_id not in (".$excludeUsers.")) as num_users, ".
								"( SELECT number FROM Animal A4 WHERE A4.photo_id = A.photo_id and A4.species = A.species and A4.number<51 and A4.person_id not in (".$excludeUsers.") GROUP BY number ORDER BY COUNT(*) DESC LIMIT 1 ) as modal_number, ".
								"( SELECT GROUP_CONCAT(DISTINCT A5.person_id ORDER BY A5.person_id SEPARATOR ".$db->quote('|').") FROM Animal A5 WHERE A5.photo_id = A.photo_id and A5.species = A.species and A5.number<51 and A5.person_id not in (".$excludeUsers.")) as id_by")
							->from("Animal A")
							->where("A.photo_id > " . $j . " and A.photo_id <= " . $intervalMax)
							->where("A.person_id not in (".$excludeUsers.")")
							->where("A.number<51");
							
						error_log("NBN select votes query created: " . $query->dump());
						
						$insertQuery = $db->getQuery(true);
						
						$insertQuery
							->insert($db->quoteName('NBNVotes'))
							->columns($db->quoteName($columns))
							->values($query);

						// Set the query using our newly populated query object and execute it.
						$db->setQuery($insertQuery);
						
						
						//error_log("NBNVotes insert query created: " . $insertQuery->dump());
						
						$db->execute();
						
					}
				}
			}
			
		  
			$query = $db->getQuery(true);

			$query->delete($db->quoteName('NBNData'));
			
			$db->setQuery($query);

			$result = $db->execute();
			
			
		  
			$query = $db->getQuery(true);
		  
			$columns = array('project_name', 'site_id', 'sequence_id', 'photo_id', 'species_id', 'num_individuals', 'sci_name', 'nbn_code',
							'id_by', 'num_votes', 'poss_votes', 'confidence', 'remarks', 'status', 'priority', 'taken', 'site_owner', 'lat', 'lon', 'media_type', 'site_purpose');
		  
			$query->select(' GROUP_CONCAT(DISTINCT PR.project_name SEPARATOR '.$db->quote('|').') as project_name, P.site_id, P.sequence_id, V.photo_id, V.species_id, ' . 
							'V.modal_number , ' .
							'N.recommended_name, '.
							'N.TAXON_VERSION_KEY,  ' .
							'V.id_by, ' .
							'V.num_votes, '.
							'V.num_users, '.
							'V.num_votes/V.num_users, '.
							'CONCAT(V.num_votes, " of ", V.num_users, ", confidence = ", V.num_votes/V.num_users), '.
							'CASE
								WHEN V.num_users=1 THEN "Unconfirmed - not reviewed"
								WHEN V.num_users>1 and V.num_votes/V.num_users>0.5 THEN "Accepted - considered correct"
								ELSE "Rejected, low confidence"
							END AS status, '.
							'CASE
								WHEN V.num_users=1 THEN 1
								WHEN V.num_users>1 and V.num_votes/V.num_users>0.5 THEN 2
								ELSE 0
							END AS priority, '.
							'DATE(P.taken), S.person_id, S.latitude, S.longitude, CASE WHEN P.filename LIKE "%jpg" THEN "pictured" ELSE "video" END as media_type, O.option_name as purpose')
				->from("NBNVotes V")
				->innerJoin("Photo P on P.photo_id = V.photo_id")
				->innerJoin("Site S on P.site_id = S.site_id")
				->innerJoin("NBN_codes N on V.species_id = N.option_id")
				->innerJoin("Options O on O.option_id = S.purpose_id") 
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id")
				->innerJoin("Project PR on PSM.project_id = PR.project_id")
				->where("P.sequence_id > 0 and P.sequence_id IS NOT NULL")
				->where("(P.photo_id >= PSM.start_photo_id and ((PSM.end_photo_id is NULL) or (P.photo_id <= PSM.end_photo_id)))")
				//->where("S.latitude > 50 AND S.latitude < 60.15 AND S.longitude > -7.64 AND S.longitude < 2")
				->where("V.species_id NOT IN (86,87,95,96,97)")
				->where("PR.project_id not in (".$excludeProjects.")")
				->where("PR.project_id in (select project_id from ProjectOptions where option_id = " . $regionId . ")" )
				->group("V.photo_id, V.species_id");
				
			error_log("NBN select query created: " . $query->dump());
			
			$insertQuery = $db->getQuery(true);
			
			$insertQuery
				->insert($db->quoteName('NBNData'))
				->columns($db->quoteName($columns))
				->values($query);

			// Set the query using our newly populated query object and execute it.
			$db->setQuery($insertQuery);
			
			//error_log("NBN insert query created: " . $insertQuery->dump());
			
			$db->execute();
			
			
			$headings = array('occurrenceID', 'collectionCode', 'datasetName', 
								'institutionCode', 'license', 'rightsHolder',
								'scientificName', 'taxonID', 'identificationRemarks', 
								'identificationVerificationStatus', 'eventDate',
								'recordedBy', 'identifiedBy',
								'coordinateUncertaintyInMeters', 'geodeticDatum',
								'decimalLatitude', 'decimalLongitude',
								'locationID', 'locality', 'basisOfRecord',
								'occurrenceStatus', 'occurrenceRemarks',
								'samplingProtocol', 'individualCount','references');
								
					
			$folder = 'nbn';
			
			$reportRoot = JPATH_SITE."/biodivimages/reports";
			$filePath = $reportRoot."/".$folder."/";
			
			$tmpCsvFile = $filePath . "/tmp_" . $filename;
			$newCsvFile = $filePath . "/" . $filename;
			
			// Has the report already been created?
			if ( !file_exists($newCsvFile) ) {
				
				// Creates a new csv file and store it in directory
				// Rename once finished writing to file
				if (!file_exists($filePath)) {
					mkdir($filePath, 0755, true);
				}
				
				$tmpCsv = fopen ( $tmpCsvFile, 'w');
				
				// First put the headings
				if ( $headings ) {
					fputcsv($tmpCsv, $headings);
				}
				
				$query = $db->getQuery(true);
			
				$query->select('count(distinct site_id, species_id, taken)')
					->from("NBNData N")
					->where("N.confidence > 0.5");
					
				$db->setQuery($query);
				
				//error_log("NBN count query created: " . $query->dump());
				
				$totalRows = $db->loadResult();
				
				$numPerQuery = 1000;
				
				for ( $i=0; $i < $totalRows; $i+=$numPerQuery ) {
					
					$query = $db->getQuery(true);
					
								
					$query->select('CONCAT(N.sequence_id, "_", N.photo_id, "_", N.species_id), '.
								'N.project_name, '.
								'"MammalWeb records", '.
								'"MammalWeb", '.
								'IF(N.taken < "20240101", "CC-BY-NC", "CC-BY"), '.
								'CONCAT("MammalWeb & user id ", N.site_owner), '.
								'N.sci_name, '.
								'N.nbn_code, '.
								'N.remarks, '.
								'N.status, '.
								'N.taken, '.
								'N.site_owner, '.
								'N.id_by, '.
								'40, '.
								'"WGS84", '.
								'ROUND(N.lat,3), '.
								'ROUND(N.lon,3), '.
								'N.site_id, '.
								'CONCAT("MammalWeb site number ", N.site_id), '.
								'"MachineObservation", '.
								'"present", '.
								'N.media_type, '.
								'CONCAT("Camera trap | ", N.site_purpose), '.
								'N.num_individuals, '.
								'CONCAT("https://www.mammalweb.org/en/?option=com_biodiv&view=classify&photo_id=", N.photo_id)')
						->from("NBNData N")
						->innerJoin( '(SELECT DISTINCT site_id, species_id, taken FROM NBNData ) AS dt ON N.id = ( SELECT tt.id FROM NBNData AS tt WHERE tt.site_id = dt.site_id AND tt.species_id = dt.species_id AND tt.taken = dt.taken AND tt.confidence > 0.5 AND tt.taken != "0000-00-00" ORDER BY priority, confidence DESC LIMIT 1)' )
						->where("N.taken BETWEEN '2011-01-01' and CURRENT_DATE")
						->order("N.site_id, N.taken");
						
						
					$db->setQuery($query, $i, $numPerQuery);
					
					//error_log("NBN select query created: " . $query->dump());
					
					$nbnRows = $db->loadAssocList();
				
					// Then each row
					foreach ( $nbnRows as $row ) {
						fputcsv($tmpCsv, $row);
					}
				
				}
				
				
				fclose($tmpCsv);
				
				rename ( $tmpCsvFile, $newCsvFile );
				

			}
			
			return $newCsvFile;
		}
		else {
			return null;
		}
		
	}
		
	
	public static function generateCamtrapDp ( $startDate, $endDate, $zipFileTag ) {
		
		$startDateMinus1 = date('Ymd', strtotime($startDate . ' -1 day'));
		$endDatePlus1 = date('Ymd', strtotime($endDate . ' +1 day'));
		
		print ( "<br>Using start date minus 1 " . $startDateMinus1 );
		print ( "<br>Using end date plus 1 " . $endDatePlus1 );

		$options = awsOptions();
		$bucketUrl = $options['s3url'];
		
		$filesToTransfer = array();	  
	 
		$db = db();
		
		$query = $db->getQuery(true);
		$query->select("distinct name from CamtrapDP")
			->where("active = 1");
		$db->setQuery($query);
		$topLevelNames = $db->loadColumn();
		
		
		$query = $db->getQuery(true);
		$query->select("cdp_id, name, project_id, type, keywords, sampling_design, capture_method from CamtrapDP")
			->where("active = 1");
		$db->setQuery($query);
		$topLevelProjects = $db->loadObjectList( "cdp_id");
		
		$humanId = codes_getCode ( "Human", "content" );
		$vehicleId = codes_getCode ( "Vehicle", "content" );
		$nothingId = codes_getCode ( "Nothing", "content" );
		$dontKnowId = codes_getCode ( "Don\'t Know", "content" );
		$unclassifiedId = codes_getCode ( "Unclassified", "aiclass" );
		$calibrationPoleId = codes_getCode ( "Calibration pole", "aiclass" );
		$adultId = codes_getCode ( "Adult", "age" );
		$juvenileId = codes_getCode ( "Juvenile", "age" );
		$femaleId = codes_getCode ( "Female", "gender" );
		$maleId = codes_getCode ( "Male", "gender" );
					
					
		
		foreach ( $topLevelProjects as $cdpId=>$topLevelProject ) {
			
			$projectId = $topLevelProject->project_id;
			$projectSetName = $topLevelProject->name;
			$projectSetType = $topLevelProject->type;
			$keywords = $topLevelProject->keywords;
			$samplingDesign = $topLevelProject->sampling_design;
			$captureMethod = $topLevelProject->capture_method;
			$observationLevel = $projectSetType == 'HUMAN' ? 'event' : 'media';
			
			$query = $db->getQuery(true);
			$query->select("S.species_id, O.option_name as eng_name, OD.value as sci_name")
				->from("CamtrapDPSpecies S")
				->innerJoin("Options O on O.option_id = S.species_id")
				->innerJoin("OptionData OD on OD.option_id = S.species_id and OD.data_type = " . $db->quote('SCI'))
				->where("S.cdp_id = " . $cdpId);
			$db->setQuery($query);
			
			//error_log("CamtrapDP species select query created: " . $query->dump());
			
			$speciesList = $db->loadObjectList("species_id");
			$speciesIds = array_keys($speciesList);
			
						
			// Get all subprojects - NB just include subprojects with no children - and exclude private projects
			$leafProjects = Project::getLeafProjectsById($projectId);
			$metaFilename = 'datapackage.json';
			$deploymentsFilename = 'deployments.csv';
			$mediaFilename = 'media.csv';
			$observationsFilename = 'observations.csv';
			
			$reportRoot = JPATH_SITE."/biodivimages/reports";
			$projectFolder = 'camtrapdp/' . $projectSetName . '/';
			$projectDateName = $projectSetName . '_' . $startDate . '_' . $endDate;
			$projectDateFolder = $projectFolder . $projectDateName . '/';
				
				
			// For each subproject
			foreach ( $leafProjects as $projectId=>$projectPrettyName ) {
				
				print ( "<br>CDP id: " . $cdpId . ", Project: " . $projectPrettyName . ", Set type: " . $projectSetType  );
				// Get project details
				$projectDetails = codes_getDetails($projectId, 'project');
				
				$allSequenceIds = null;
				$sequenceIds = null;
				$photoIds = null;
				$uploadIds = null;
				
				$query = $db->getQuery(true);
				$query->select("distinct P.upload_id")
							->from("Photo P")
							->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id")
							->where("P.photo_id >= PSM.start_photo_id")
							->where("(P.photo_id <= PSM.end_photo_id or PSM.end_photo_id is null)")
							->where("PSM.project_id = " . $projectId)
							->where("P.taken > " . $startDateMinus1)
						 	->where("P.taken < " . $endDatePlus1);
				
				$db->setQuery($query);
				
				//error_log ("Get upload ids query: " . $query->dump());

				$uploadIds = $db->loadColumn();
				
				if ( $uploadIds ) {
					// Get the bounding box for the project sites
					$query = $db->getQuery(true);
					$query->select("MIN(S.longitude) as min_lon, MIN(S.latitude) as min_lat, MAX(S.longitude) as max_lon, MAX(S.latitude) as max_lat " )
						->from("Site S")
						->innerJoin("Upload U using (site_id) ")
						->where("U.upload_id in ( " . implode(',', $uploadIds) . ")");
					$db->setQuery($query);
					$bbox = $db->loadObject();
				
				
					// rounding here
					$minLon = $bbox->min_lon;
					$minLat = $bbox->min_lat;
					$maxLon = $bbox->max_lon;
					$maxLat = $bbox->max_lat;
				}
				else {
					$minLon = 0;
					$minLat = 0;
					$maxLon = 0;
					$maxLat = 0;
				}
				
				
				$t=time();
				$monthStr = date("Y-m",$t);
				$dateStr = date("Y-m-d",$t);
				$timezoneDate = date(DATE_ATOM,$t);
				$projectName = str_replace(' ', '_', $projectDetails['project_name']);
				$projectPrettyName = $projectDetails['project_prettyname'];
				$projectDesc = $projectDetails['project_description'];
				
			
				// ------------------------------ Create meta file
				
				$filename = $metaFilename;
				$fileSetName = $projectName . '_' . $projectId;
				$folder = $projectDateFolder . $fileSetName . '/' . $projectSetType;
				
				$filePath = $reportRoot."/".$folder;
				
				$tmpMetaFile = $filePath . "/tmp_" . $filename;
				$newMetaFile = $filePath . "/" . $filename;
				
				// Has the report already been created?
				if ( !file_exists($newMetaFile) ) {
					
					// Creates a new csv file and store it in directory
					// Rename once finished writing to file
					if (!file_exists($filePath)) {
						mkdir($filePath, 0755, true);
					}
					
					if ( file_exists($tmpMetaFile) ) {
						
						// We are recovering from a stalled process so remove file to start again.
						if (!unlink($tmpMetaFile)) { 
							error_log ("$tmpMetaFile cannot be deleted due to an error"); 
						} 
					}
					
					$tmpMeta = fopen ( $tmpMetaFile, 'w');
					
					fputs ( $tmpMeta, "{\n" );
					fputs ( $tmpMeta, "  \"resources\": [" );
					fputs ( $tmpMeta, "    {\n" );
					fputs ( $tmpMeta, "    \"name\": \"deployments\",\n" );
					fputs ( $tmpMeta, "    \"path\": \"".$deploymentsFilename."\",\n" );
					fputs ( $tmpMeta, "    \"profile\": \"tabular-data-resource\",\n" );
					fputs ( $tmpMeta, "    \"format\": \"csv\",\n" );
					fputs ( $tmpMeta, "    \"mediatype\": \"text/csv\",\n" );
					fputs ( $tmpMeta, "    \"encoding\": \"utf-8\",\n" );
					fputs ( $tmpMeta, "    \"schema\": \"https://raw.githubusercontent.com/tdwg/camtrap-dp/1.0/deployments-table-schema.json\"\n" );
					fputs ( $tmpMeta, "    },\n" );
					fputs ( $tmpMeta, "    {\n" );
					fputs ( $tmpMeta, "    \"name\": \"media\",\n" );
					fputs ( $tmpMeta, "    \"path\": \"".$mediaFilename."\",\n" );
					fputs ( $tmpMeta, "    \"profile\": \"tabular-data-resource\",\n" );
					fputs ( $tmpMeta, "    \"format\": \"csv\",\n" );
					fputs ( $tmpMeta, "    \"mediatype\": \"text/csv\",\n" );
					fputs ( $tmpMeta, "    \"encoding\": \"utf-8\",\n" );
					fputs ( $tmpMeta, "    \"schema\": \"https://raw.githubusercontent.com/tdwg/camtrap-dp/1.0/media-table-schema.json\"\n" );
					fputs ( $tmpMeta, "    },\n" );
					fputs ( $tmpMeta, "    {\n" );
					fputs ( $tmpMeta, "    \"name\": \"observations\",\n" );
					fputs ( $tmpMeta, "    \"path\": \"".$observationsFilename."\",\n" );
					fputs ( $tmpMeta, "    \"profile\": \"tabular-data-resource\",\n" );
					fputs ( $tmpMeta, "    \"format\": \"csv\",\n" );
					fputs ( $tmpMeta, "    \"mediatype\": \"text/csv\",\n" );
					fputs ( $tmpMeta, "    \"encoding\": \"utf-8\",\n" );
					fputs ( $tmpMeta, "    \"schema\": \"https://raw.githubusercontent.com/tdwg/camtrap-dp/1.0/observations-table-schema.json\"\n" );
					fputs ( $tmpMeta, "    }\n" );
					fputs ( $tmpMeta, "  ],\n" );
					
					fputs ( $tmpMeta, "  \"profile\": \"https://raw.githubusercontent.com/tdwg/camtrap-dp/1.0/camtrap-dp-profile.json\",\n" );
					fputs ( $tmpMeta, "  \"name\": \"MammalWeb_".$fileSetName."\",\n" );
					fputs ( $tmpMeta, "  \"id\": \"MammalWeb_".$fileSetName."\",\n" );
					fputs ( $tmpMeta, "  \"created\": \"".$timezoneDate."\",\n" );
					fputs ( $tmpMeta, "  \"title\": \"".$projectSetType." classification data from the ".$projectPrettyName." project on MammalWeb\",\n" );
					
					fputs ( $tmpMeta, "  \"contributors\": [\n" );
					fputs ( $tmpMeta, "    {\n" );
					fputs ( $tmpMeta, "      \"title\": \"MammalWeb\",\n" );
					fputs ( $tmpMeta, "      \"email\": \"info@mammalweb.org\",\n" );
					fputs ( $tmpMeta, "      \"path\": \"https://www.mammalweb.org\",\n" );
					fputs ( $tmpMeta, "      \"role\": \"contributor\",\n" );
					fputs ( $tmpMeta, "      \"organization\": \"MammalWeb Limited\"\n" );
					fputs ( $tmpMeta, "    }\n" );
					fputs ( $tmpMeta, "  ],\n" );
					
					fputs ( $tmpMeta, "  \"description\": \"".$projectSetType." classifications of camera trap data from the ".$projectPrettyName." project on the MammalWeb Citizen Science platform\",\n" );
					fputs ( $tmpMeta, "  \"version\": \"1.0\",\n" );
					
					fputs ( $tmpMeta, "  \"keywords\": " );
					fputs ( $tmpMeta, "  ".$keywords . ",\n" );
					
					fputs ( $tmpMeta, "  \"licenses\": [\n" );
					fputs ( $tmpMeta, "    {\n" );
					fputs ( $tmpMeta, "      \"name\": \"CC-BY-SA 4.0\",\n" );
					fputs ( $tmpMeta, "      \"path\": \"https://creativecommons.org/licenses/by-sa/4.0/\",\n" );
					fputs ( $tmpMeta, "      \"scope\": \"data\"\n" );
					fputs ( $tmpMeta, "    },\n" );
					fputs ( $tmpMeta, "    {\n" );
					fputs ( $tmpMeta, "      \"name\": \"CC-BY-SA 4.0\",\n" );
					fputs ( $tmpMeta, "      \"path\": \"https://creativecommons.org/licenses/by-sa/4.0/\",\n" );
					fputs ( $tmpMeta, "      \"scope\": \"media\"\n" );
					fputs ( $tmpMeta, "    }\n" );
					fputs ( $tmpMeta, "  ],\n" );
					
					fputs ( $tmpMeta, "  \"project\": {\n" );
					fputs ( $tmpMeta, "    \"id\": \"".$projectId."\",\n" );
					fputs ( $tmpMeta, "    \"title\": \"".$projectPrettyName."\",\n" );
					fputs ( $tmpMeta, "    \"acronym\": \"".$projectName."\",\n" );
					fputs ( $tmpMeta, "    \"description\": \"".$projectDesc."\",\n" );
					fputs ( $tmpMeta, "    \"samplingDesign\": \"".$samplingDesign."\",\n" );
					fputs ( $tmpMeta, "    \"path\": \"https://www.mammalweb.org/en/?view=projecthome&option=com_biodiv&project_id=".$projectId."\",\n" );
					fputs ( $tmpMeta, "    \"captureMethod\": [\n" );
					fputs ( $tmpMeta, "      \"".$captureMethod."\"\n" );
					fputs ( $tmpMeta, "    ],\n" );
					fputs ( $tmpMeta, "    \"individualAnimals\": \"false\",\n" );
					fputs ( $tmpMeta, "    \"observationLevel\": \"".$observationLevel."\"\n" );
					fputs ( $tmpMeta, "  },\n" );
					
					fputs ( $tmpMeta, "  \"coordinatePrecision\": \"40\",\n" );
					
					fputs ( $tmpMeta, "  \"spatial\": {\n" );
					fputs ( $tmpMeta, "    \"type\": \"Polygon\",\n" );
					fputs ( $tmpMeta, "    \"bbox\": [\n" );
					fputs ( $tmpMeta, "     ".$minLon.",\n" );
					fputs ( $tmpMeta, "     ".$minLat.",\n" );
					fputs ( $tmpMeta, "     ".$maxLon.",\n" );
					fputs ( $tmpMeta, "     ".$maxLat."\n" );
					fputs ( $tmpMeta, "    ]\n" );
					fputs ( $tmpMeta, "  },\n" );
					
					fputs ( $tmpMeta, "  \"temporal\": {\n" );
					fputs ( $tmpMeta, "    \"start\": \"".date("d-m-Y", strtotime($startDate))."\",\n" ); 
					fputs ( $tmpMeta, "    \"end\": \"".date("d-m-Y", strtotime($endDate))."\"\n" );
					fputs ( $tmpMeta, "  },\n" );
					
					fputs ( $tmpMeta, "  \"taxonomic\": [\n" );
					
					$speciesCount = count($speciesList);
					$speciesNumber = 1;
					foreach ( $speciesList as $speciesId=>$species ) {
						
						fputs ( $tmpMeta, "    {\n" );	
						fputs ( $tmpMeta, "      \"scientificName\": \"".$species->sci_name."\",\n" );
						fputs ( $tmpMeta, "      \"taxonID\": \"".$speciesId."\",\n" );
						fputs ( $tmpMeta, "      \"taxonRank\": \"species\",\n" );
						fputs ( $tmpMeta, "      \"vernacularNames\": {\n" );
						fputs ( $tmpMeta, "        \"eng\":\"".$species->eng_name."\"\n" );
						fputs ( $tmpMeta, "      }\n" );
						if ( $speciesNumber == $speciesCount ) {
							fputs ( $tmpMeta, "    }\n" );
						}
						else {
							fputs ( $tmpMeta, "    },\n" );
						}	
						$speciesNumber++;
					
					}
					
					fputs ( $tmpMeta, "  ],\n" );
					
					fputs ( $tmpMeta, "  \"relatedIdentifiers\": [ ],\n" );
					fputs ( $tmpMeta, "  \"references\": [ ]\n" );
					
					fputs ( $tmpMeta, "}\n" );
					
					fclose($tmpMeta);
					
					rename ( $tmpMetaFile, $newMetaFile );
					
					//$filesToTransfer[] = $newMetaFile;
					
				}
				
				// -------------------  Create deployments file
				
				$filename = $deploymentsFilename;
				
				$tmpCsvFile = $filePath . "/tmp_" . $filename;
				$newCsvFile = $filePath . "/" . $filename;
				
				// Has the report already been created?
				if ( !file_exists($newCsvFile) ) {
					
					// Creates a new csv file and store it in directory
					// Rename once finished writing to file
					if (!file_exists($filePath)) {
						mkdir($filePath, 0755, true);
					}
					
					if ( file_exists($tmpCsvFile) ) {
						
						// We are recovering from a stalled process so remove file to start again.
						if (!unlink($tmpCsvFile)) { 
							error_log ("$tmpCsvFile cannot be deleted due to an error"); 
						} 
					}
					
					$tmpCsv = fopen ( $tmpCsvFile, 'w');
					
					// First put the headings
					$headings = array('deploymentID', 'locationID', 'locationName', 
							'latitude', 'longitude', 'coordinateUncertainty',
							'deploymentStart', 'deploymentEnd', 'setupBy', 
							'cameraID', 'cameraModel',
							'cameraDelay', 'cameraHeight',
							'cameraDepth', 'cameraTilt',
							'cameraHeading', 'detectionDistance',
							'timestampIssues', 'baitUse', 'featureType',
							'habitat', 'deploymentGroups',
							'deploymentTags', 'deploymentComments');
							
					if ( $headings ) {
						fputcsv($tmpCsv, $headings);
					}
					
					$query = $db->getQuery(true);
				
					if ( $uploadIds ) {
						
						$totalRows = count($uploadIds);
					}
					else {
				
						$totalRows = 0;
					}
					
					$numPerQuery = 1000;
					
					
					for ( $i=0; $i < $totalRows ; $i+=$numPerQuery ) {
						
						$query = $db->getQuery(true);
						
						$query->select('U.upload_id, '.
									'U.site_id, '.
									'S.site_name, '.
									'S.latitude, '.
									'S.longitude, '.
									'"40", '.
									'IF(U.utc_offset < 0, CONCAT(DATE_FORMAT( U.deployment_date,\'%Y-%m-%dT%T-\'), lpad(FLOOR(-U.utc_offset/60),2,\'0\'), \':\', lpad(-U.utc_offset%60,2,\'0\') ), CONCAT(DATE_FORMAT( U.deployment_date,\'%Y-%m-%dT%T+\'), lpad(FLOOR(U.utc_offset/60),2,\'0\'), \':\', lpad(U.utc_offset%60,2,\'0\') )) AS deployment_date, '.
									'IF(U.utc_offset < 0, CONCAT(DATE_FORMAT( U.collection_date,\'%Y-%m-%dT%T-\'), lpad(FLOOR(-U.utc_offset/60),2,\'0\'), \':\', lpad(-U.utc_offset%60,2,\'0\') ), CONCAT(DATE_FORMAT( U.collection_date,\'%Y-%m-%dT%T+\'), lpad(FLOOR(U.utc_offset/60),2,\'0\'), \':\', lpad(U.utc_offset%60,2,\'0\') )) AS collection_date, '.
									'U.person_id, '.
									'S.camera_id, '.
									'IFNULL(O.option_name,"") as cameraModel, '. // camera model
									'"" as cameraDelay, '.
									'ROUND(S.camera_height/100, 2) as cameraHeight, '.
									'"" as cameraDepth, '.
									'"" as cameraTilt, '.
									'"" as cameraHeading, '.
									'"" as detectionDistance, '.
									'"" as timestamp_issues, '.
									'"" as baitUse, '.
									'"" as featureType, '.
									'IFNULL(O2.option_name,"") as habitat, '. // habitat
									'"" as deploymentGroups, '.
									'"" as deplymentTags, '.
									'S.notes as deploymentComments ')
							->from("Upload U")
							->innerJoin("Site S using (site_id)")
							->leftJoin("Options O on O.option_id = S.camera_id")
							->leftJoin("Options O2 on O.option_id = S.habitat_id")
							->where("U.upload_id in (".implode(',', $uploadIds).")");
						
													
						$db->setQuery($query, $i, $numPerQuery);
						
						//error_log("Deployments select query created: " . $query->dump());
						
						$deploymentRows = $db->loadAssocList();
					
						// Then each row
						foreach ( $deploymentRows as $row ) {
							fputcsv($tmpCsv, $row);
						}
					
					}
					
					fclose($tmpCsv);
					
					rename ( $tmpCsvFile, $newCsvFile );
					
					//$filesToTransfer[] = $newCsvFile;
					
				}
				
				// -------------------- Create media file
				
				$filename = $mediaFilename;
				
				$tmpCsvFile = $filePath . "/tmp_" . $filename;
				$newCsvFile = $filePath . "/" . $filename;
				
				// Has the report already been created?
				if ( !file_exists($newCsvFile) ) {
					
					// Creates a new csv file and store it in directory
					// Rename once finished writing to file
					if (!file_exists($filePath)) {
						mkdir($filePath, 0755, true);
					}
					
					if ( file_exists($tmpCsvFile) ) {
						
						// We are recovering from a stalled process so remove file to start again.
						if (!unlink($tmpCsvFile)) { 
							error_log ("$tmpCsvFile cannot be deleted due to an error"); 
						} 
					}
					
					$tmpCsv = fopen ( $tmpCsvFile, 'w');
					
					// First put the headings
					$headings = array('mediaID', 'deploymentID', 'captureMethod', 
							'timestamp', 'filePath', 'filePublic',
							'fileName', 'fileMediatype', 'exifData', 
							'favorite', 'mediaComments');
					if ( $headings ) {
						fputcsv($tmpCsv, $headings);
					}
					
					foreach ( $uploadIds as $uploadId ) {
						
						$query = $db->getQuery(true);
						
						$query->select('count(*)')
							->from("Photo P")
							->where("P.upload_id = " . $uploadId )
							->where("P.taken > " . $startDateMinus1)
							->where("P.taken < " . $endDatePlus1);
			
							
						$db->setQuery($query);
						
						$totalRows = $db->loadResult();
						
						$numPerQuery = 1000;
						
						for ( $i=0; $i < $totalRows; $i+=$numPerQuery ) {
							
							$query = $db->getQuery(true);
							
							$query->select('P.photo_id, '.  
										'P.upload_id, '.
										'"'.$captureMethod.'", '.
										'IF(U.utc_offset < 0, CONCAT(DATE_FORMAT( P.taken ,\'%Y-%m-%dT%T-\'), lpad(FLOOR(-U.utc_offset/60),2,\'0\'), \':\', lpad(-U.utc_offset%60,2,\'0\') ), CONCAT(DATE_FORMAT( P.taken ,\'%Y-%m-%dT%T+\'), lpad(FLOOR(U.utc_offset/60),2,\'0\'), \':\', lpad(U.utc_offset%60,2,\'0\') )) AS date_formatted, ' .
										'CONCAT("'.$bucketUrl.'/person_", P.person_id, "/site_", P.site_id, "/", P.filename), '.
										'"true" as filePublic, '.
										'P.upload_filename, '.
										'(
											CASE
											WHEN substring_index(P.filename,\'.\',-1)=\'jpg\' THEN \'image/jpg\'
											WHEN substring_index(P.filename,\'.\',-1)=\'jpeg\' THEN \'image/jpeg\'
											WHEN substring_index(P.filename,\'.\',-1)=\'mp4\' THEN \'video/mp4\'
											WHEN substring_index(P.filename,\'.\',-1)=\'mp3\' THEN \'audio/mp3\'
											ELSE \'video/avi\'
											END ) AS filetype, '.
										'"" as exifData, '.
										'"" as favorite, '.
										'CONCAT(\'sequence_id = \', P.sequence_id) as mediaComments ')
								->from("Photo P")
								->innerJoin("Upload U using (upload_id)")
								->where("P.upload_id = " . $uploadId )
								->where("P.taken > " . $startDateMinus1)
								->where("P.taken < " . $endDatePlus1);
														
							$db->setQuery($query, $i, $numPerQuery);
							
							//error_log("Media select query created: " . $query->dump());
							
							$mediaRows = $db->loadAssocList();
						
							// Then each row
							foreach ( $mediaRows as $row ) {
								fputcsv($tmpCsv, $row);
							}
						
						}
					
					}
					
					
					fclose($tmpCsv);
					
					rename ( $tmpCsvFile, $newCsvFile );
					
					//$filesToTransfer[] = $newCsvFile;
					
				}
				
				// -------------------------  Create observations file
				
				$filename = $observationsFilename;
				
				$tmpCsvFile = $filePath . "/tmp_" . $filename;
				$newCsvFile = $filePath . "/" . $filename;
				
				// Has the report already been created?
				if ( !file_exists($newCsvFile) ) {
					
					// Creates a new csv file and store it in directory
					// Rename once finished writing to file
					if (!file_exists($filePath)) {
						mkdir($filePath, 0755, true);
					}
					
					if ( file_exists($tmpCsvFile) ) {
						
						// We are recovering from a stalled process so remove file to start again.
						if (!unlink($tmpCsvFile)) { 
							error_log ("$tmpCsvFile cannot be deleted due to an error"); 
						} 
					}
					$tmpCsv = fopen ( $tmpCsvFile, 'w');
					
					// First put the headings
					$headings = array('observationID', 'deploymentID', 'mediaID', 
							'eventID', 'eventStart', 'eventEnd',
							'observationLevel', 'observationType', 'cameraSetupType', 
							'scientificName', 'count', 'lifeStage', 'sex', 'behavior', 'individualID', 'individualPositionRadius', 'individualPositionAngle', 'individualSpeed', 'bboxX', 'bboxY', 'bboxWidth', 'bboxHeight', 'classificationMethod', 'classifiedBy', 'classificationTimestamp', 'classificationProbability', 'observationTags', 'observationComments');
					if ( $headings ) {
						fputcsv($tmpCsv, $headings);
					}
					
					foreach ( $speciesList as $speciesId=>$species ) {
						
						$speciesEngName = $species->eng_name;
						$speciesSciName = $species->sci_name;
					
						if ( $projectSetType == 'CAI' ) {
							
							foreach ( $uploadIds as $uploadId ) {
						
								$query = $db->getQuery(true);
								
								$query->select('count(*)')
									->from("Classify C")
									->innerJoin("Photo P using (photo_id)")
									->where("C.origin = " . $db->quote('CAI'))
									->where("C.species_id = " . $speciesId)
									->where("P.upload_id = " . $uploadId )
									->where("P.taken > " . $startDateMinus1)
									->where("P.taken < " . $endDatePlus1);
									
								$db->setQuery($query);
								
								$totalRows = $db->loadResult();
							
								$numPerQuery = 1000;
								
								for ( $i=0; $i < $totalRows; $i+=$numPerQuery ) {
									
									$query = $db->getQuery(true);
									
												
									$query->select('C.classify_id, '.
												'P.upload_id, '.
												'P.photo_id, '.
												'P.sequence_id, '.
												
												'IF(U.utc_offset < 0, CONCAT(DATE_FORMAT( P.taken ,\'%Y-%m-%dT%T-\'), lpad(FLOOR(-U.utc_offset/60),2,\'0\'), \':\', lpad(-U.utc_offset%60,2,\'0\') ), CONCAT(DATE_FORMAT( P.taken ,\'%Y-%m-%dT%T+\'), lpad(FLOOR(U.utc_offset/60),2,\'0\'), \':\', lpad(U.utc_offset%60,2,\'0\') )) AS start_formatted, '.
												'IF(U.utc_offset < 0, CONCAT(DATE_FORMAT( P1.taken ,\'%Y-%m-%dT%T-\'), lpad(FLOOR(-U.utc_offset/60),2,\'0\'), \':\', lpad(-U.utc_offset%60,2,\'0\') ), CONCAT(DATE_FORMAT( P1.taken ,\'%Y-%m-%dT%T+\'), lpad(FLOOR(U.utc_offset/60),2,\'0\'), \':\', lpad(U.utc_offset%60,2,\'0\') )) AS end_formatted, '.
												'"media", '.
												'(
													CASE
														WHEN C.species_id = '.$humanId.' THEN \'human\'
														WHEN C.species_id = '.$vehicleId.' THEN \'vehicle\'
														WHEN C.species_id = '.$nothingId.' THEN \'blank\'
														WHEN C.species_id = '.$dontKnowId.' THEN \'unknown\'
														WHEN C.species_id = '.$unclassifiedId.' THEN \'unclassified\'
														WHEN C.species_id = '.$calibrationPoleId.' THEN \'unclassified\' 
														ELSE \'animal\'
													END ) AS obsType, '. // animal, human, vehicle, blank, unknown, unclassified calibration pole set to unclassified for completeness
												'"" as cameraSetupType, '.
												$db->quote($speciesSciName).', '. // scientific name
												'"1", '.
												'"" AS lifeStage, '.
												'"" AS sex, '.
												'"" as behavior, '. // behavior
												'"" as individualID, '. // individualID
												'"" as individualPositionRadius, '. // individualPositionRadius
												'"" as individualPositionAngle, '. // individualPositionAngle
												'"" as individualSpeed, '. // individualSpeed
												'ROUND(C.xmin, 6) as bboxX, '. //bboxX
												'ROUND(C.ymin, 6) as bboxY, '. //bboxY
												'ROUND(C.xmax - C.xmin, 6) as bboxWidth, '. //bboxWidth
												'ROUND(C.ymax - C.ymin, 6) as bboxHeight, '. //bboxHeight
												'"machine", '. // classificationMethod
												'CONCAT(C.origin, \' \', C.model), '. // classifiedBy
												'DATE_FORMAT( C.timestamp ,\'%Y-%m-%dT%TZ\') as ts_formatted, '.
												'C.prob as classificationProbability, '. //classificationProbability
												'"" as observationTags, '. //observationTags
												'"" as observationComments' ) //observationComments
												
										->from("Classify C")
										->innerJoin("Photo P on C.photo_id = P.photo_id")
										->innerJoin("Upload U on P.upload_id = U.upload_id")
										->innerJoin("Photo P1 on P.sequence_id = P1.sequence_id")
										->where("C.origin = " . $db->quote('CAI'))
										->where("C.species_id = " . $speciesId)
										->where("P.upload_id = " . $uploadId )
										->where("P.taken > " . $startDateMinus1)
										->where("P.taken < " . $endDatePlus1)
										->where("P1.photo_id in (select end_photo_id from PhotoSequence)");
										
										
									$db->setQuery($query, $i, $numPerQuery);
									
									//error_log("CAI observations select query created: " . $query->dump());
									
									$obsRows = $db->loadAssocList();
								
									// Then each row
									foreach ( $obsRows as $row ) {
										fputcsv($tmpCsv, $row);
									}
								}
							}
						}
						else if ( $projectSetType == 'HUMAN' ) {
							
							foreach ( $uploadIds as $uploadId ) {
						
								$query = $db->getQuery(true);
								
								$query->select('count(*)')
									->from("Animal A")
									->innerJoin("Photo P using (photo_id)")
									->where("A.species = " . $db->quote($speciesId))
									->where("P.upload_id = " . $uploadId )
									->where("P.taken > " . $startDateMinus1)
									->where("P.taken < " . $endDatePlus1);
									
								$db->setQuery($query);
								
								$totalRows = $db->loadResult();
							
								$numPerQuery = 1000;
								
								for ( $i=0; $i < $totalRows; $i+=$numPerQuery ) {
									
									$query = $db->getQuery(true);
									
												
									$query->select('A.animal_id, '.
												'P.upload_id, '.
												'"" as mediaID, '.
												'P.sequence_id, '.
												
												'IF(U.utc_offset < 0, CONCAT(DATE_FORMAT( P.taken ,\'%Y-%m-%dT%T-\'), lpad(FLOOR(-U.utc_offset/60),2,\'0\'), \':\', lpad(-U.utc_offset%60,2,\'0\') ), CONCAT(DATE_FORMAT( P.taken ,\'%Y-%m-%dT%T+\'), lpad(FLOOR(U.utc_offset/60),2,\'0\'), \':\', lpad(U.utc_offset%60,2,\'0\') )) AS start_formatted, '.
												'IF(U.utc_offset < 0, CONCAT(DATE_FORMAT( P1.taken ,\'%Y-%m-%dT%T-\'), lpad(FLOOR(-U.utc_offset/60),2,\'0\'), \':\', lpad(-U.utc_offset%60,2,\'0\') ), CONCAT(DATE_FORMAT( P1.taken ,\'%Y-%m-%dT%T+\'), lpad(FLOOR(U.utc_offset/60),2,\'0\'), \':\', lpad(U.utc_offset%60,2,\'0\') )) AS end_formatted, '.
												'"event", '.
												'(
													CASE
														WHEN A.species = '.$humanId.' THEN \'human\'
														WHEN A.species = '.$vehicleId.' THEN \'vehicle\'
														WHEN A.species = '.$nothingId.' THEN \'blank\'
														WHEN A.species = '.$dontKnowId.' THEN \'unknown\'
														ELSE \'animal\'
													END ) AS obsType, '. // animal, human, vehicle, blank, unknown, unclassified (not relevant for our human classifications)
												'"" as cameraSetupType, '.
												$db->quote($speciesSciName).', '. // scientific name
												'A.number, '.
												'(
													CASE
														WHEN A.age = '.$adultId.' THEN \'adult\'
														WHEN A.age = '.$juvenileId.' THEN \'juvenile\'
														ELSE \'\'
													END ) AS lifeStage, '.
												'(
													CASE
														WHEN A.gender = '.$femaleId.' THEN \'female\'
														WHEN A.gender = '.$maleId.' THEN \'male\'
														ELSE \'\'
													END ) AS sex, '.
												'"" as behavior, '. // behavior
												'"" as individualID, '. // individualID
												'"" as individualPositionRadius, '. // individualPositionRadius
												'"" as individualPositionAngle, '. // individualPositionAngle
												'"" as individualSpeed, '. // individualSpeed
												'"" as bboxX, '. //bboxX
												'"" as bboxY, '. //bboxY
												'"" as bboxWidth, '. //bboxWidth
												'"" as bboxHeight, '. //bboxHeight
												'"human", '. // classificationMethod
												'A.person_id, '. // classifiedBy
												'DATE_FORMAT( A.timestamp ,\'%Y-%m-%dT%TZ\') as ts_formatted, '.
												'"" as classificationProbability, '. //classificationProbability
												'"" as observationTags, '. //observationTags
												'A.notes' ) //observationComments
												
										->from("Animal A")
										->innerJoin("Photo P on A.photo_id = P.photo_id")
										->innerJoin("Upload U on P.upload_id = U.upload_id")
										->innerJoin("Photo P1 on P.sequence_id = P1.sequence_id")
										->leftJoin("Photo P2 on P1.sequence_id = P2.sequence_id and P1.sequence_num < P2.sequence_num")
										//->where("P.sequence_id in (".implode(',', $speciesSequenceIds).")")
										->where("A.species = " . $db->quote($speciesId))
										->where("P.upload_id = " . $uploadId )
										->where("P.taken > " . $startDateMinus1)
										->where("P.taken < " . $endDatePlus1)
										->where("P2.sequence_id is NULL");
										
										
									$db->setQuery($query, $i, $numPerQuery);
									
									//error_log("Human observations select query created: " . $query->dump());
									
									$obsRows = $db->loadAssocList();
								
									// Then each row
									foreach ( $obsRows as $row ) {
										fputcsv($tmpCsv, $row);
									}
								}
							}
						}
					}
				
					fclose($tmpCsv);
					
					rename ( $tmpCsvFile, $newCsvFile );
					
				}
				
			}
									
		}
		
		foreach ( $topLevelNames as $topLevelName ) {
			
			error_log ( "Creating archive for " . $topLevelName );
			
			$reportRoot = JPATH_SITE."/biodivimages/reports";
			$projectFolder = 'camtrapdp/' . $topLevelName . '/';
			$projectDateName = $topLevelName . '_' . $startDate . '_' . $endDate;
			$projectDateFolder = $projectFolder . $projectDateName . '/';
			$projectZipPath = $reportRoot."/".$projectDateFolder;
			if ( $zipFileTag ) {
				$projectZipFile = $reportRoot."/".$projectFolder.$projectDateName."_".$zipFileTag.".zip";
			}
			else {
				$projectZipFile = $reportRoot."/".$projectFolder.$projectDateName.".zip";
			}
			if ( $zipFileTag ) {
				$projectZipFileStem = $reportRoot."/".$projectFolder.$projectDateName."_".$zipFileTag;
			}
			else {
				$projectZipFileStem = $reportRoot."/".$projectFolder.$projectDateName;
			}
			
			// // To overcome ZipArchive stalling.
			// try {
				// exec ( "zip " . $projectZipFile . " " . $projectDateName );
				
				// $filesToTransfer[$topLevelName] = $projectZipFile;
			// }
			// catch (Exception $e) {
				// error_log ( "Problem zipping CamtrapDP extract: " . $e->getMessage() );
			// }
			
			// Create new zip class 
			//$zip = new FlxZipArchive; 

			//if($zip -> open($projectZipFile, ZipArchive::CREATE ) === TRUE) { 
	;		$zipArray = FlxZipArchive::createArrayOfZipFiles ( $projectZipFileStem, $projectZipPath, $projectDateName );
			if ( $zipArray ) {
				
				$filesToTransfer[$topLevelName] = $zipArray;
				
			} 
			
		}
		
		return $filesToTransfer;
	}


	public static function removeExtractDir ( $dir ) {
		
		FlxZipArchive::removeDir($dir);
		
	}

}

?>
