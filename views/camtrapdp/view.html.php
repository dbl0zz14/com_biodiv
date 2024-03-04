<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*/
 
// No direct access to this file
defined('_JEXEC') or die;
 
// import Joomla view library
jimport('joomla.application.component.view');
 


class FlxZipArchive extends ZipArchive 
{
	
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
	
	public function startAddDir($location, $name) 
	{
		$this->addDirDo($location, $name);
	} 
	public function addDir($location, $name) 
	{
		$this->addEmptyDir($name);
		$this->addDirDo($location, $name);
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
			$this->$do($location . $file, $name . $file);
		}
	} 
}




/**
* HTML View class for the HelloWorld Component
*
* @since 0.0.1
*/

class BioDivViewCamtrapDP extends JViewLegacy
{
	/**
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */

	public function display($tpl = null) 
	{
		
		$app = JFactory::getApplication();
		
		$input = $app->input;
			
		$this->startDate = $input->getString('start', 0);
		$this->endDate = $input->getString('end', 0);
		
		if ( !$this->startDate ) {
			
			$this->startDate = date('Ymd', strtotime("last year January 1st"));
			//error_log ( "Using start date " . $this->startDate );
		}
		if ( !$this->endDate ) {
			
			$this->endDate = date('Ymd', strtotime("last year December 31st"));
			//error_log ( "Using end date " . $this->endDate );
		}
		
		$endDatePlus1 = date('Ymd', strtotime($this->endDate . ' +1 day'));
		
		$options = awsOptions();
		$bucketUrl = $options['s3url'];
		
		$this->filesToTransfer = array();	  
	 
		$db = JDatabase::getInstance(dbOptions());
		
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
		$vehicleId = codes_getCode ( "Car", "aiclass" );
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
			$leafProjects = getLeafProjectsById($projectId);
			$metaFilename = 'datapackage.json';
			$deploymentsFilename = 'deployments.csv';
			$mediaFilename = 'media.csv';
			$observationsFilename = 'observations.csv';
			
			$reportRoot = JPATH_SITE."/biodivimages/reports";
			$projectFolder = 'camtrapdp/' . $projectSetName . '/';
			$projectDateName = $projectSetName . '_' . $this->startDate . '_' . $this->endDate;
			$projectDateFolder = $projectFolder . $projectDateName . '/';
				
				
			// For each subproject
			foreach ( $leafProjects as $projectId=>$projectPrettyName ) {
				
				// Get project details
				$projectDetails = codes_getDetails($projectId, 'project');
				
				$sequenceIds = null;
				$photoIds = null;
				$uploadIds = null;
				
				$query = $db->getQuery(true);
				
				if ( $speciesIds && count($speciesIds) > 0 ) {
					if ( $projectSetType == "CAI" ) {
						
						$query->select("P.photo_id, P.upload_id")
							->from("Photo P")
							->innerJoin("Upload U using (upload_id)")
							->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id")
							->innerJoin("Classify C using (sequence_id)")
							->where("C.origin = " . $db->quote('CAI'))
							->where("P.photo_id >= PSM.start_photo_id")
							->where("(P.photo_id <= PSM.end_photo_id or PSM.end_photo_id is null)")
							->where("PSM.project_id = " . $projectId)
							->where("P.taken > " . $this->startDate)
							->where("P.taken < " . $endDatePlus1)
							->where("P.contains_human = 0")
							->where("C.species_id in ( " . implode(',', $speciesIds) . ")");
							
						$query2 = $db->getQuery(true);
						$query2->select("P.photo_id, P.upload_id")
							->from("Photo P")
							->innerJoin("Upload U using (upload_id)")
							->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id")
							->innerJoin("Classify C using (sequence_id)")
							->where("C.origin = " . $db->quote('CAI'))
							->where("P.photo_id >= PSM.start_photo_id")
							->where("(P.photo_id <= PSM.end_photo_id or PSM.end_photo_id is null)")
							->where("PSM.project_id = " . $projectId)
							->where("P.taken > " . $this->startDate)
							->where("P.taken < " . $endDatePlus1)
							->where("C.species_id = " . $calibrationPoleId);
						
						$query->union($query2);
						$db->setQuery($query);
				
						$photoResults = $db->loadAssocList("photo_id");
						$photoIds = array_keys($photoResults);
						$uploadIds = array_unique(array_column($photoResults, "upload_id"));

					}
					else if ( $projectSetType == "HUMAN" ) {
						
						$query->select("distinct P.sequence_id, P.upload_id")
							->from("Photo P")
							->innerJoin("Upload U using (upload_id)")
							->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id")
							->innerJoin("Animal A using (photo_id)")
							->where("P.photo_id >= PSM.start_photo_id")
							->where("(P.photo_id <= PSM.end_photo_id or PSM.end_photo_id is null)")
							->where("PSM.project_id = " . $projectId)
							->where("P.taken > " . $this->startDate)
							->where("P.taken < " . $endDatePlus1)
							->where("P.contains_human = 0")
							->where("A.species in ( " . implode(',', $speciesIds) . ")");
							
						$db->setQuery($query);
				
						//error_log("CamtrapDP sequenceIds select query created: " . $query->dump());
				
						$sequenceResults = $db->loadAssocList("sequence_id");
						$sequenceIds = array_keys($sequenceResults);
						$uploadIds = array_unique(array_column($sequenceResults, "upload_id"));
					}
				}
				
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
					fputs ( $tmpMeta, "    \"start\": \"".date("d-m-Y", strtotime($this->startDate))."\",\n" ); 
					fputs ( $tmpMeta, "    \"end\": \"".date("d-m-Y", strtotime($this->endDate))."\"\n" );
					fputs ( $tmpMeta, "  },\n" );
					
					fputs ( $tmpMeta, "  \"taxonomic\": [\n" );
					
					$speciesCount = count($speciesList);
					foreach ( $speciesList as $speciesId=>$species ) {
						
						fputs ( $tmpMeta, "    {\n" );	
						fputs ( $tmpMeta, "      \"scientificName\": \"".$species->sci_name."\",\n" );
						fputs ( $tmpMeta, "      \"taxonID\": \"".$speciesId."\",\n" );
						fputs ( $tmpMeta, "      \"taxonRank\": \"species\",\n" );
						fputs ( $tmpMeta, "      \"vernacularNames\": {\n" );
						fputs ( $tmpMeta, "        \"eng\":\"".$species->eng_name."\"\n" );
						fputs ( $tmpMeta, "      }\n" );	
						fputs ( $tmpMeta, "    }\n" );	
					
					}
					
					fputs ( $tmpMeta, "  ],\n" );
					
					fputs ( $tmpMeta, "  \"relatedIdentifiers\": [ ],\n" );
					fputs ( $tmpMeta, "  \"references\": [ ]\n" );
					
					fputs ( $tmpMeta, "}\n" );
					
					fclose($tmpMeta);
					
					rename ( $tmpMetaFile, $newMetaFile );
					
					//$this->filesToTransfer[] = $newMetaFile;
					
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
					
					
					for ( $i=0; $i < $totalRows; $i+=$numPerQuery ) {
						
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
									'"" as deploymentComments ')
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
					
					//$this->filesToTransfer[] = $newCsvFile;
					
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
					
					$tmpCsv = fopen ( $tmpCsvFile, 'w');
					
					// First put the headings
					$headings = array('mediaID', 'deploymentID', 'captureMethod', 
							'timestamp', 'filePath', 'filePublic',
							'fileName', 'fileMediatype', 'exifData', 
							'favorite', 'mediaComments');
					if ( $headings ) {
						fputcsv($tmpCsv, $headings);
					}
					
					if ( $photoIds ) {
						$totalRows = count($photoIds);
					}
					else if ( $sequenceIds ) {
				
						$query = $db->getQuery(true);
						
						$query->select('count(*)')
							->from("Photo P")
							->where("P.contains_human = 0")
							->where("sequence_id in ( " . implode(',', $sequenceIds) . ")");
			
							
						$db->setQuery($query);
					
						//error_log("CamtrapDP media count query created: " . $query->dump());
					
						$totalRows = $db->loadResult();
					}
					else {
				
						$totalRows = 0;
					}
					
					$numPerQuery = 1000;
					
					for ( $i=0; $i < $totalRows; $i+=$numPerQuery ) {
						
						$query = $db->getQuery(true);
						
						if ( $projectSetType == "CAI" ) {			
							$query->select('P.photo_id, '.
										'P.upload_id, '.
										'"'.$captureMethod.'", '.
										'IF(U.utc_offset < 0, CONCAT(DATE_FORMAT( P.taken ,\'%Y-%m-%dT%T-\'), lpad(FLOOR(-U.utc_offset/60),2,\'0\'), \':\', lpad(-U.utc_offset%60,2,\'0\') ), CONCAT(DATE_FORMAT( P.taken ,\'%Y-%m-%dT%T+\'), lpad(FLOOR(U.utc_offset/60),2,\'0\'), \':\', lpad(U.utc_offset%60,2,\'0\') )) AS date_formatted, ' .
										'CONCAT("'.$bucketUrl.'/person_", P.person_id, "/site_", P.site_id, "/"), '.
										'"true" as filePublic, '.
										'P.filename, '.
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
								->where("P.photo_id in (".implode(',', $photoIds).")")
								->where("P.contains_human = 0");
						}
						else if ( $projectSetType == "HUMAN" ) {			
							$query->select('P.photo_id, '.  
										'P.upload_id, '.
										'"'.$captureMethod.'", '.
										'IF(U.utc_offset < 0, CONCAT(DATE_FORMAT( P.taken ,\'%Y-%m-%dT%T-\'), lpad(FLOOR(-U.utc_offset/60),2,\'0\'), \':\', lpad(-U.utc_offset%60,2,\'0\') ), CONCAT(DATE_FORMAT( P.taken ,\'%Y-%m-%dT%T+\'), lpad(FLOOR(U.utc_offset/60),2,\'0\'), \':\', lpad(U.utc_offset%60,2,\'0\') )) AS date_formatted, ' .
										'CONCAT("'.$bucketUrl.'/person_", P.person_id, "/site_", P.site_id, "/"), '.
										'"true" as filePublic, '.
										'P.filename, '.
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
								->where("P.sequence_id in (".implode(',', $sequenceIds).")")
								->where("P.contains_human = 0");
						}
							
							
						$db->setQuery($query, $i, $numPerQuery);
						
						//error_log("Media select query created: " . $query->dump());
						
						$mediaRows = $db->loadAssocList();
					
						// Then each row
						foreach ( $mediaRows as $row ) {
							fputcsv($tmpCsv, $row);
						}
					
					}
					
					
					fclose($tmpCsv);
					
					rename ( $tmpCsvFile, $newCsvFile );
					
					//$this->filesToTransfer[] = $newCsvFile;
					
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
					
					$tmpCsv = fopen ( $tmpCsvFile, 'w');
					
					// First put the headings
					$headings = array('observationID', 'deploymentID', 'mediaID', 
							'eventID', 'eventStart', 'eventEnd',
							'observationLevel', 'observationType', 'cameraSetupType', 
							'scientificName', 'count', 'lifeStage', 'sex', 'behavior', 'individualID', 'individualPositionRadius', 'individualPositionAngle', 'individualSpeed', 'bboxX', 'bboxY', 'bboxWidth', 'bboxHeight', 'classificationMethod', 'classifiedBy', 'classificationTimestamp', 'classificationProbability', 'observationTags', 'observationComments');
					if ( $headings ) {
						fputcsv($tmpCsv, $headings);
					}
					
					$query = $db->getQuery(true);
					
					if ( $projectSetType == 'CAI' ) {
						
						$totalRows = 0;
						
						if ( $photoIds and count($photoIds) > 0 ) {
							$query->select('count(*)')
								->from("Classify C")
								->innerJoin("Photo P using (photo_id)")
								->where("C.origin = " . $db->quote('CAI'))
								->where("P.photo_id in (".implode(',', $photoIds).")")
								->where("P.contains_human = 0");
							
							$db->setQuery($query);
							
							//error_log("CAI observation count query created: " . $query->dump());
							
							$totalRows = $db->loadResult();
						}
						
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
										'OD.value, '. // scientific name
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
								->innerJoin("OptionData OD on C.species_id = OD.option_id and OD.data_type = " . $db->quote('SCI'))
								->innerJoin("Photo P on C.photo_id = P.photo_id")
								->innerJoin("Upload U on P.upload_id = U.upload_id")
								->innerJoin("Photo P1 on P.sequence_id = P1.sequence_id")
								->leftJoin("Photo P2 on P1.sequence_id = P2.sequence_id and P1.sequence_num < P2.sequence_num")
								->where("P.photo_id in (".implode(',', $photoIds).")")
								->where("P.contains_human = 0")
								->where("P2.sequence_id is NULL");
								
								
							$db->setQuery($query, $i, $numPerQuery);
							
							//error_log("CAI observations select query created: " . $query->dump());
							
							$obsRows = $db->loadAssocList();
						
							// Then each row
							foreach ( $obsRows as $row ) {
								fputcsv($tmpCsv, $row);
							}
						
						}
					}
					else if ( $projectSetType == 'HUMAN' ) {
						
						$totalRows = 0;
						
						if ( $sequenceIds && count($sequenceIds) > 0 ) {
							
							$query->select('count(*)')
								->from("Animal A")
								->innerJoin("Photo P using (photo_id)")
								->where("P.sequence_id in (".implode(',', $sequenceIds).")")
								->where("P.contains_human = 0");
							
							$db->setQuery($query);
							
							//error_log("Human observation count query created: " . $query->dump());
							
							$totalRows = $db->loadResult();
						}
						
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
										'OD.value, '. // scientific name
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
								->innerJoin("OptionData OD on A.species = OD.option_id and OD.data_type = " . $db->quote('SCI'))
								->innerJoin("Photo P on A.photo_id = P.photo_id")
								->innerJoin("Upload U on P.upload_id = U.upload_id")
								->innerJoin("Photo P1 on P.sequence_id = P1.sequence_id")
								->leftJoin("Photo P2 on P1.sequence_id = P2.sequence_id and P1.sequence_num < P2.sequence_num")
								->where("P.sequence_id in (".implode(',', $sequenceIds).")")
								->where("P.contains_human = 0")
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
				
					
					fclose($tmpCsv);
					
					rename ( $tmpCsvFile, $newCsvFile );
					
					//$this->filesToTransfer[] = $newCsvFile;
					
				}
				
			}
			
			
						
		}
		
		foreach ( $topLevelNames as $topLevelName ) {
			
			error_log ( "Creating archive for " . $topLevelName );
			
			$reportRoot = JPATH_SITE."/biodivimages/reports";
			$projectFolder = 'camtrapdp/' . $topLevelName . '/';
			$projectDateName = $topLevelName . '_' . $this->startDate . '_' . $this->endDate;
			$projectDateFolder = $projectFolder . $projectDateName . '/';
			$projectZipPath = $reportRoot."/".$projectDateFolder;
			$projectZipFile = $reportRoot."/".$projectFolder.$projectDateName.".zip";
			
			
			// Create new zip class 
			$zip = new FlxZipArchive; 

			if($zip -> open($projectZipFile, ZipArchive::CREATE ) === TRUE) { 
				
				//error_log ( "Zip archive created: " . $projectZipFile );
			
				$zip->startAddDir($projectZipPath, $projectDateName);
				$zip->close();
				
				//error_log ( "Zip closed: " . $projectZipFile );
				
				$this->filesToTransfer[$topLevelName] = $projectZipFile;
			} 
			
		}
		
		// Display the view
		parent::display($tpl);
    }
}



?>
