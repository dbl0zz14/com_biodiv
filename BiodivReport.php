<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

include_once "local.php";


class BiodivReport {
	
	// Default page length
	const PAGE_LENGTH = 25;
	
	// If a report has fewer rows then returned as JSON, otherwise a file is created and link sent.
	// This is also used as the max num of rows to write at a time to avoid memory limits
	const REPORT_FILE_THRESHOLD = 4000;
	
	private $projectId;
	private $projectName;
	
	// The option_id of this report type in the database
	private $reportType;
	private $reportTypeName;
	
	private $reportId;
	private $personId;
	private $languageTag;
	
	private $totalRows;
	private $pageLength;
	
	private $filename;
	
	
	// Note that projectId can be null if this is a user report
	function __construct( $projectId, $reportType, $personId, $reportId = null )
	{
		$this->personId = $personId;
		$this->reportType = $reportType;
		
		$typeName = getOptionData($reportType, 'reporttype');
		$this->reportTypeName = $typeName[0];
		$this->reportTypeText = codes_getName( $reportType, 'reporttypetran');
		
		$this->projectId = $projectId;
		
		// This will be an enhancement
		//$allAdmin = myAdminProjects();
		//$allSubs = getSubProjectsById( $projectId );
		
		// Set language
		$langObject = JFactory::getLanguage();
		$this->languageTag = $langObject->getTag();
		
		$this->pageLength = BiodivReport::PAGE_LENGTH;
		
		if ( $reportId == null ) {
			
			error_log ("New report, creating Report row");
			
			$t=time();
			$dateStr = date("Ymd_His",$t);
			
			if ( $this->projectId ) {
				$projectDetails = codes_getDetails ( $this->projectId, 'project' );
			
				// Replace _s for clarity of filename
				$this->projectName = str_replace('_', '-', $projectDetails['project_prettyname']);
				$projectTag = substr(str_replace(' ', '-', $this->projectName), 0, 15);   
				
				$reportTypeStr = substr(str_replace(' ', '-', $this->reportTypeText), 0, 10);   
			
				$this->filename = $projectTag . '_' . $reportTypeStr . '_' . $dateStr . ".csv";
			}
			else {
				$reportTypeStr = substr(str_replace(' ', '-', $this->reportTypeText), 0, 25);   
			
				$this->filename = $reportTypeStr . '_' . $dateStr . ".csv";
			}
			
			error_log ( "Report filename = " . $this->filename );
			
			$reportFields = new stdClass();
			$reportFields->person_id = $this->personId;
			$reportFields->project_id = $this->projectId;
			$reportFields->report_type = $this->reportType;
			$reportFields->filename = $this->filename;
			
			$this->reportId = codes_insertObject($reportFields, 'report');
			
			error_log ("New report, id = " . $this->reportId . ", generating Report row data for type " . $this->reportTypeName);
			
			// And generate the data (in the database) here.
			// Like this to avoid multiple database queries when paging through
			switch ( $this->reportTypeName ) {
				case "SITE":
					$this->generateSiteData ();
					break;
				case "UPLOAD":
					$this->generateUploadData ();
					break;
				case "UPLOADAUDIO":
					$this->generateUploadDataAudio ();
					break;
				case "SPECIES":
					$this->generateAnimalData ();
					break;
				// Specific to audio sites - notes show SONG/CALL - audio only so no start/end
				case "SPECIESAUDIO":
					$this->generateAnimalDataAudio ();
					break;
				case "NOSPECIES":
					$this->generateNoAnimalData ();
					break;
				case "NOSPECIESAUDIO":
					$this->generateNoAnimalDataAudio ();
					break;
				case "SEQUENCE":
					$this->generateSequenceData ();
					break;
				// Equivalent to sequence for audio sites
				case "RECORDING":
					$this->generateSequenceData ();
					break;
				// Number of sequences uploaded by user and how many classified
				case "USERSEQUENCE":
					$this->generateUserSequenceData ();
					break;
				// List of uploads by user with deployment details
				case "USERUPLOAD":
					$this->generateUserUploadData ();
					break;
				// Classifications by user of user sequences
				case "USERSPECIES":
					$this->generateUserAnimal ();
					break;
				// Classifications by others of user sequences
				case "USERSPECIESOTHERS":
					$this->generateUserAnimalOthers ();
					break;
				// This user's sequences which have no classification
				case "USERNOSPECIES":
					$this->generateUserNoAnimal ();
					break;
				// All classifications by this user, including sequences uploaded by others
				case "USERALLSPECIES":
					$this->generateUserAnimalAll ();
					break;
				default:
					error_log ("No report type found for " . $this->reportTypeName );
			}
			
		}
		else {
			$this->reportId = $reportId;
			
			$details = codes_getDetails($reportId, 'report');
			
			$this->filename = $details['filename'];
		}
	}
	
	
	
	public static function createFromId ( $reportId ) {
		
		$reportDetails = codes_getDetails ( $reportId, 'report' );
		
		$instance = new self( $reportDetails['project_id'], $reportDetails['report_type'], $reportDetails['person_id'], $reportId );
		
        return $instance;
	}
	
		
		
	// List of the available reports
	public static function listReports () {
		
		$reports = codes_getList('reporttypetran');
		
		return $reports;
	}
	
	
	
	// List of the available reports
	public static function listUserReports () {
		
		$reports = codes_getList('userreporttypetran');
		
		return $reports;
	}
	
	
	// Remove any reports created for this person
	public static function removeExistingReports ( $personId ) {
		
		$filePath = reportRoot()."/person_".$personId."/project_*/";
		
		foreach (glob($filePath."*.csv") as $fileName) {
			$success = unlink($fileName);
			if ( $success ) {
				error_log ($fileName." was deleted!");
			}
			else {
				error_log($fileName." delete failed!");
			}
		}
	}
	
	
	
	// Return URL of report file - always on local filesystem as we don't copy to s3
	public function reportURL () {
		
		$details = codes_getDetails($this->reportId, 'report');
		
		$stem = JURI::root()."/biodivimages/reports/person_".$this->personId."/project_".$this->projectId;
		return $stem . "/". $details['filename'];
		
		
	}
	
	
	
	public function getReportId() {
		return $this->reportId;
	}
	
	
	
	
	public function getFilename() {
		return $this->filename;
	}
	
	
	
	
	public function rows ( $pageNum, $pageLength = null ) {
		
		if ( $pageLength != null ) $this->pageLength = $pageLength;
		
		$db = JDatabase::getInstance(dbOptions());
		
		$query = $db->getQuery(true);
		$query->select("row_csv")
			->from("ReportRows RR")
			->where("RR.report_id = " . $this->reportId);
			
		$start = ($pageNum) * $this->pageLength;
		
		$db->setQuery($query, $start, $this->pageLength);
		$rows = $db->loadColumn();
		
		return $rows;
	}
		
	
	
	public function headings() {
		
		error_log ("BiodivReport::getHeadings called");
		
		// Select directly as need to order by Options.seq
		$db = JDatabase::getInstance(dbOptions());
		
		// Set up the select query for the report
		$query = $db->getQuery(true)
			->select( "O.option_id, O.option_name")
			->from("Options O")
			->innerJoin("OptionData OD on O.option_id = OD.option_id and data_type='reportheading'" ) //. $this->reportType )
			->where("OD.value = '".$this->reportType."'")
			->order("O.seq");
			
		$db->setQuery($query);
		$headings = $db->loadAssocList('option_id', 'option_name');
	
		// Translate if necessary
		if ( $this->languageTag != 'en-GB' ) {
			foreach ( $headings as $option_id=>$option_name ) {
				$nameTran = codes_getOptionTranslation($option_id);
				$headings[$option_id] = $nameTran;
			}
		}
		
		
		/*
		$restrict = array();
		$restrict['restriction'] = "value = '" . $this->reportType . "'";
		//$species = codes_getList ( "filterspecies", $restrict );
		$headings = codes_getList ( "reportheadingtran", $restrict );
		*/
		$err_msg = print_r ( $headings, true );
		error_log ( "Report headings: " . $err_msg );
		
		return $headings;
		
	}
	
	public function totalRows () {
		
		$db = JDatabase::getInstance(dbOptions());
		
		if ( $this->totalRows == null ) {
			$query = $db->getQuery(true);
			$query->select("count(*)")
				->from("ReportRows RR")
				->where("RR.report_id = " . $this->reportId);
				
			$db->setQuery($query);
			$this->totalRows = $db->loadResult();
			
			error_log ( "totalRows, numRows = " . $this->totalRows );
		}
		
		return $this->totalRows;
		
	}
	
	
	public function pageLength () {
		
		// For now this is 
		
		return $this->pageLength;
		
	}
	
	
	public function createDownloadFile () {
		
		//$filePath = "/person_".$this->personId."/project_".$this->projectId."/".$this->filename;
		$filePath = reportRoot()."/person_".$this->personId."/project_".$this->projectId;
		
		$tmpCsvFile = $filePath . "/tmp_" . $this->filename;
		$newCsvFile = $filePath . "/" . $this->filename;
		
		// Has the report already been created?
		if ( !file_exists($newCsvFile) ) {
			
			// Creates a new csv file and store it in directory
			// Rename once finished writing to file
			if (!file_exists($filePath)) {
				mkdir($filePath, 0755, true);
			}
			
			$tmpCsv = fopen ( $tmpCsvFile, 'w');
			
			// First put the headings
			$headings = $this->headings();
			fputcsv($tmpCsv, $headings);
			
			// Then each row
			$db = JDatabase::getInstance(dbOptions());
			
			$rowCount = $this->totalRows();
			
			for ( $i=0; $i < $rowCount; $i+= BiodivReport::REPORT_FILE_THRESHOLD ) {
			
				$query = $db->getQuery(true);
				$query->select("row_csv")
					->from("ReportRows RR")
					->where("RR.report_id = " . $this->reportId);
					
				//$db->setQuery($query);
				$db->setQuery($query, $i, BiodivReport::REPORT_FILE_THRESHOLD); // LIMIT query results to avoid memory limits
		
				$rows = $db->loadColumn();
				
				foreach ($rows as $fields) {
					
					fputcsv($tmpCsv, explode(',', $fields));
				}
				
			}
			fclose($tmpCsv);
			
			rename ( $tmpCsvFile, $newCsvFile );
			
			error_log ( "Report file created" );
		}
	}
	
	
	private function removePreviousRows () {
		
		$db = JDatabase::getInstance(dbOptions());
		
		// Delete any previous report rows
		// Join to ReportRows here to limit to those report ids where there exist rows.
		
		if ( $this->projectId ) {
			
			$query = $db->getQuery(true)
				->select( "distinct R.report_id" )
				->from("Report R")
				->innerJoin("ReportRows RR on R.report_id = RR.report_id")
				->where("R.person_id = " . $this->personId)
				->where("R.project_id = " . $this->projectId)
				->where("R.report_type = " . $this->reportType);
		}
		else {
			$query = $db->getQuery(true)
				->select( "distinct R.report_id" )
				->from("Report R")
				->innerJoin("ReportRows RR on R.report_id = RR.report_id")
				->where("R.person_id = " . $this->personId)
				->where("R.project_id = NULL" )
				->where("R.report_type = " . $this->reportType);
		}
		
		error_log("query created");
		
		$db->setQuery($query);

		$reportIds = $db->loadColumn();
		
		$err_msg = print_r ( $reportIds, true );
		error_log ( "removePreviousRows, reportIds: " . $err_msg );
		
		// delete all ReportRows for previous reports.
		if ( count($reportIds) > 0 ) {
			
			error_log ( "About to delete previous reports" );
			
			//$delQuery = $db->getQuery(true);
			
			$delQuery = $db->getQuery(true)
				->delete('ReportRows')
				->where('report_id in (' . implode(',', $reportIds) . ')' );

			error_log ( "Got query" );
			
			$db->setQuery($delQuery);

			$result = $db->execute();
			
			error_log ( "Previous reports deleted" );
			
		}
		
		error_log("Delete complete");
		
	}
	private function generateSiteData () {
		
		error_log ( "generateSiteData called" );
		
		// Delete any existing ReportRows data for this... person, project and report type. 
		$this->removePreviousRows();
		
		$db = JDatabase::getInstance(dbOptions());
		
		// Set up the select query for the report
		// NB same for all languages
		$querySelect = $db->getQuery(true)
			->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', PSM.site_id, REPLACE(S.site_name, ',', ' '), S.latitude, S.longitude, PSM.start_time, PSM.end_time) as report_csv")
			->from("ProjectSiteMap PSM")
			->innerJoin("Site S on S.site_id = PSM.site_id")
			->where("PSM.project_id = " . $this->projectId)
			->order("S.site_name");
		
		error_log("querySelect created");
			
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv')))
			->values($querySelect);
		
		error_log("queryInsert created");
		
		$db->setQuery($queryInsert);
		
		error_log("About to execute");

		$db->execute();
		
		error_log("Execution complete");
					

	}
	
	
	private function generateUploadData () {
		
		error_log ( "generateUploadData called" );
		
		// Delete any existing ReportRows data for this... person, project and report type. 
		$this->removePreviousRows();
		
		$db = JDatabase::getInstance(dbOptions());
		$query1 = null;
		$query2 = null;
		
		// Will need to do this differently for non English language...
		if ( $this->languageTag == 'en-GB' ) {
		
			// We have a table for this report to improve performance, but only updated periodically - try getting direct to start with
			// Set up the select queries for the report
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', U.upload_id, U.person_id, U.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.camera_tz, U.is_dst, U.utc_offset, U.deployment_date, U.collection_date, U.timestamp) as report_csv")
				->from("Upload U")
				->innerJoin("Site S on U.site_id = S.site_id")
				->innerJoin("Options O5 on O5.option_id = S.habitat_id")
				->innerJoin("ProjectSiteMap PSM on U.site_id = PSM.site_id and U.timestamp >= PSM.start_time and U.timestamp <= PSM.end_time")
				->where("PSM.project_id = " . $this->projectId);
			
			error_log("query1 created: " . $query1->dump() );
				
			$query2 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', U.upload_id, U.person_id, U.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.camera_tz, U.is_dst, U.utc_offset, U.deployment_date, U.collection_date, U.timestamp) as report_csv")
				->from("Upload U")
				->innerJoin("Site S on U.site_id = S.site_id")
				->innerJoin("Options O5 on O5.option_id = S.habitat_id")
				->innerJoin("ProjectSiteMap PSM on U.site_id = PSM.site_id and U.timestamp >= PSM.start_time and PSM.end_time is null")
				->where("PSM.project_id = " . $this->projectId);
		
			error_log("query2 created: " . $query2->dump());
		}
		else {
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', U.upload_id, U.person_id, U.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(OD5.value, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.camera_tz, U.is_dst, U.utc_offset, U.deployment_date, U.collection_date, U.timestamp) as report_csv")
				->from("Upload U")
				->innerJoin("Site S on U.site_id = S.site_id")
				->leftJoin("OptionData OD5 on OD5.option_id = S.habitat_id and OD5.data_type = '" . $this->languageTag . "'")
				->innerJoin("ProjectSiteMap PSM on U.site_id = PSM.site_id and U.timestamp >= PSM.start_time and U.timestamp <= PSM.end_time")
				->where("PSM.project_id = " . $this->projectId);
			
			error_log("query1 created: " . $query1->dump() );
				
			$query2 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', U.upload_id, U.person_id, U.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(OD5.value, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.camera_tz, U.is_dst, U.utc_offset, U.deployment_date, U.collection_date, U.timestamp) as report_csv")
				->from("Upload U")
				->innerJoin("Site S on U.site_id = S.site_id")
				->leftJoin("OptionData OD5 on OD5.option_id = S.habitat_id and OD5.data_type = '" . $this->languageTag . "'")
				->innerJoin("ProjectSiteMap PSM on U.site_id = PSM.site_id and U.timestamp >= PSM.start_time and PSM.end_time is null")
				->where("PSM.project_id = " . $this->projectId);
		
			error_log("query2 created: " . $query2->dump());
		}
		
		$unionQuery = $db->getQuery(true)
             ->select('*')
             ->from('(' . $query1->union($query2) . ') a');
			 
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv')))
			->values($unionQuery);
		
		error_log("queryInsert created");
		
		$db->setQuery($queryInsert);
		
		error_log("About to execute");

		$db->execute();
		
		error_log("Execution complete");

	}
	
	
	// No deployment and colection time for audio uploads
	private function generateUploadDataAudio () {
		
		error_log ( "generateUploadDataAudio called" );
		
		// Delete any existing ReportRows data for this... person, project and report type. 
		$this->removePreviousRows();
		
		$db = JDatabase::getInstance(dbOptions());
		$query1 = null;
		$query2 = null;
		
		// Will need to do this differently for non English language...
		if ( $this->languageTag == 'en-GB' ) {
		
			// We have a table for this report to improve performance, but only updated periodically - try getting direct to start with
			// Set up the select queries for the report
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', U.upload_id, U.person_id, U.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.camera_tz, U.is_dst, U.utc_offset, U.timestamp) as report_csv")
				->from("Upload U")
				->innerJoin("Site S on U.site_id = S.site_id")
				->innerJoin("Options O5 on O5.option_id = S.habitat_id")
				->innerJoin("ProjectSiteMap PSM on U.site_id = PSM.site_id and U.timestamp >= PSM.start_time and U.timestamp <= PSM.end_time")
				->where("PSM.project_id = " . $this->projectId);
			
			error_log("query1 created: " . $query1->dump() );
				
			$query2 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', U.upload_id, U.person_id, U.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.camera_tz, U.is_dst, U.utc_offset, U.timestamp) as report_csv")
				->from("Upload U")
				->innerJoin("Site S on U.site_id = S.site_id")
				->innerJoin("Options O5 on O5.option_id = S.habitat_id")
				->innerJoin("ProjectSiteMap PSM on U.site_id = PSM.site_id and U.timestamp >= PSM.start_time and PSM.end_time is null")
				->where("PSM.project_id = " . $this->projectId);
		
			error_log("query2 created: " . $query2->dump());
		}
		else {
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', U.upload_id, U.person_id, U.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(OD5.value, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.camera_tz, U.is_dst, U.utc_offset, U.timestamp) as report_csv")
				->from("Upload U")
				->innerJoin("Site S on U.site_id = S.site_id")
				->leftJoin("OptionData OD5 on OD5.option_id = S.habitat_id and OD5.data_type = '" . $this->languageTag . "'")
				->innerJoin("ProjectSiteMap PSM on U.site_id = PSM.site_id and U.timestamp >= PSM.start_time and U.timestamp <= PSM.end_time")
				->where("PSM.project_id = " . $this->projectId);
			
			error_log("query1 created: " . $query1->dump() );
				
			$query2 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', U.upload_id, U.person_id, U.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(OD5.value, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.camera_tz, U.is_dst, U.utc_offset, U.timestamp) as report_csv")
				->from("Upload U")
				->innerJoin("Site S on U.site_id = S.site_id")
				->leftJoin("OptionData OD5 on OD5.option_id = S.habitat_id and OD5.data_type = '" . $this->languageTag . "'")
				->innerJoin("ProjectSiteMap PSM on U.site_id = PSM.site_id and U.timestamp >= PSM.start_time and PSM.end_time is null")
				->where("PSM.project_id = " . $this->projectId);
		
			error_log("query2 created: " . $query2->dump());
		}
		
		$unionQuery = $db->getQuery(true)
             ->select('*')
             ->from('(' . $query1->union($query2) . ') a');
			 
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv')))
			->values($unionQuery);
		
		error_log("queryInsert created");
		
		$db->setQuery($queryInsert);
		
		error_log("About to execute");

		$db->execute();
		
		error_log("Execution complete");

	}
	
	
	private function generateUserUploadData () {
		
		error_log ( "generateUploadData called" );
		
		// Delete any existing ReportRows data for this... person, project and report type. 
		$this->removePreviousRows();
		
		$db = JDatabase::getInstance(dbOptions());
		$query1 = null;
		$query2 = null;
		
		if ( $this->projectId ) {
			// Will need to do this differently for non English language...
			if ( $this->languageTag == 'en-GB' ) {
			
				// We have a table for this report to improve performance, but only updated periodically - try getting direct to start with
				// Set up the select queries for the report
				$query1 = $db->getQuery(true)
					->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', U.upload_id, U.person_id, U.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.camera_tz, U.is_dst, U.utc_offset, U.deployment_date, U.collection_date, U.timestamp) as report_csv")
					->from("Upload U")
					->innerJoin("Site S on U.site_id = S.site_id")
					->innerJoin("Options O5 on O5.option_id = S.habitat_id")
					->innerJoin("ProjectSiteMap PSM on U.site_id = PSM.site_id and U.timestamp >= PSM.start_time and U.timestamp <= PSM.end_time")
					->where("PSM.project_id = " . $this->projectId);
				
				error_log("query1 created: " . $query1->dump() );
					
				$query2 = $db->getQuery(true)
					->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', U.upload_id, U.person_id, U.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.camera_tz, U.is_dst, U.utc_offset, U.deployment_date, U.collection_date, U.timestamp) as report_csv")
					->from("Upload U")
					->innerJoin("Site S on U.site_id = S.site_id")
					->innerJoin("Options O5 on O5.option_id = S.habitat_id")
					->innerJoin("ProjectSiteMap PSM on U.site_id = PSM.site_id and U.timestamp >= PSM.start_time and PSM.end_time is null")
					->where("PSM.project_id = " . $this->projectId);
			
				error_log("query2 created: " . $query2->dump());
			}
			else {
				$query1 = $db->getQuery(true)
					->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', U.upload_id, U.person_id, U.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(OD5.value, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.camera_tz, U.is_dst, U.utc_offset, U.deployment_date, U.collection_date, U.timestamp) as report_csv")
					->from("Upload U")
					->innerJoin("Site S on U.site_id = S.site_id")
					->leftJoin("OptionData OD5 on OD5.option_id = S.habitat_id and OD5.data_type = '" . $this->languageTag . "'")
					->innerJoin("ProjectSiteMap PSM on U.site_id = PSM.site_id and U.timestamp >= PSM.start_time and U.timestamp <= PSM.end_time")
					->where("PSM.project_id = " . $this->projectId);
				
				error_log("query1 created: " . $query1->dump() );
					
				$query2 = $db->getQuery(true)
					->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', U.upload_id, U.person_id, U.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(OD5.value, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.camera_tz, U.is_dst, U.utc_offset, U.deployment_date, U.collection_date, U.timestamp) as report_csv")
					->from("Upload U")
					->innerJoin("Site S on U.site_id = S.site_id")
					->leftJoin("OptionData OD5 on OD5.option_id = S.habitat_id and OD5.data_type = '" . $this->languageTag . "'")
					->innerJoin("ProjectSiteMap PSM on U.site_id = PSM.site_id and U.timestamp >= PSM.start_time and PSM.end_time is null")
					->where("PSM.project_id = " . $this->projectId);
			
				error_log("query2 created: " . $query2->dump());
			}
			
			$unionQuery = $db->getQuery(true)
				 ->select('*')
				 ->from('(' . $query1->union($query2) . ') a');
				 
			$queryInsert = $db->getQuery(true)
				->insert('ReportRows')
				->columns($db->qn(array('report_id','row_csv')))
				->values($unionQuery);
			
			error_log("queryInsert created");
			
			$db->setQuery($queryInsert);
			
			error_log("About to execute");

			$db->execute();
			
			error_log("Execution complete");
		}
		else {
			// Will need to do this differently for non English language...
			if ( $this->languageTag == 'en-GB' ) {
			
				// We have a table for this report to improve performance, but only updated periodically - try getting direct to start with
				// Set up the select queries for the report
				$query1 = $db->getQuery(true)
					->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', U.upload_id, U.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.camera_tz, U.is_dst, U.utc_offset, U.deployment_date, U.collection_date, U.timestamp) as report_csv")
					->from("Upload U")
					->innerJoin("Site S on U.site_id = S.site_id and S.person_id = " . $this->personId)
					->innerJoin("Options O5 on O5.option_id = S.habitat_id");
				
				error_log("query1 created: " . $query1->dump() );
					
			}
			else {
				$query1 = $db->getQuery(true)
					->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', U.upload_id, U.person_id, U.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(OD5.value, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.camera_tz, U.is_dst, U.utc_offset, U.deployment_date, U.collection_date, U.timestamp) as report_csv")
					->from("Upload U")
					->innerJoin("Site S on U.site_id = S.site_id and S.person_id = " . $this->personId)
					->leftJoin("OptionData OD5 on OD5.option_id = S.habitat_id and OD5.data_type = '" . $this->languageTag . "'");
				
				error_log("query1 created: " . $query1->dump() );
					
			}
			
			$queryInsert = $db->getQuery(true)
				->insert('ReportRows')
				->columns($db->qn(array('report_id','row_csv')))
				->values($query1);
			
			error_log("queryInsert created");
			
			$db->setQuery($queryInsert);
			
			error_log("About to execute");

			$db->execute();
			
			error_log("Execution complete");

		}

	}
	
	
	
	private function generateAnimalData () {
		
		// Delete any existing data 
		$this->removePreviousRows();
		
		$db = JDatabase::getInstance(dbOptions());
		$query1 = null;
		$query2 = null;
		
		// Will need to do this differently for non English language...
		if ( $this->languageTag == 'en-GB' ) {
		
			// We have a table for this report to improve performance, but only updated periodically - try getting direct to start with
			// Set up the select queries for the report
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), A.animal_id, A.person_id, P.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), P.photo_id, P.upload_filename, P.taken, P2.upload_filename, P2.taken, P2.sequence_num, S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.deployment_date, U.collection_date, REPLACE(O.option_name, ',', ' -'), IFNULL(O2.option_name, 'null'), IFNULL(O3.option_name, 'null'), IFNULL(A.number, 'null'), IFNULL(A.notes, 'null'), IFNULL(O4.option_name, 'null'), A.timestamp) as report_csv")
				->from("Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id and P.sequence_num = 1")
				->innerJoin("PhotoSequence PS on P.photo_id = PS.start_photo_id")
				->innerJoin("Photo P2 on PS.end_photo_id = P2.photo_id")
				->innerJoin("Site S on P.site_id = S.site_id")
				->innerJoin("Upload U on P.upload_id = U.upload_id")
				->innerJoin("Options O on O.option_id = A.species")
				->leftJoin("Options O2 on O2.option_id = A.age")
				->leftJoin("Options O3 on O3.option_id = A.gender")
				->leftJoin("Options O4 on O4.option_id = A.sure")
				->leftJoin("Options O5 on O5.option_id = S.habitat_id")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and P.photo_id <= PSM.end_photo_id")
				->where("A.species != 97")
				->where("PSM.project_id = " . $this->projectId);
			
			error_log("query1 created: " . $query1->dump() );
				
			$query2 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), A.animal_id, A.person_id, P.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), P.photo_id, P.upload_filename, P.taken, P2.upload_filename, P2.taken, P2.sequence_num, S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.deployment_date, U.collection_date, REPLACE(O.option_name, ',', ' -'), IFNULL(O2.option_name, 'null'), IFNULL(O3.option_name, 'null'), IFNULL(A.number, 'null'), IFNULL(A.notes, 'null'), IFNULL(O4.option_name, 'null'), A.timestamp) as report_csv")
				->from("Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id and P.sequence_num = 1")
				->innerJoin("PhotoSequence PS on P.photo_id = PS.start_photo_id")
				->innerJoin("Photo P2 on PS.end_photo_id = P2.photo_id")
				->innerJoin("Site S on P.site_id = S.site_id")
				->innerJoin("Upload U on P.upload_id = U.upload_id")
				->innerJoin("Options O on O.option_id = A.species")
				->leftJoin("Options O2 on O2.option_id = A.age")
				->leftJoin("Options O3 on O3.option_id = A.gender")
				->leftJoin("Options O4 on O4.option_id = A.sure")
				->leftJoin("Options O5 on O5.option_id = S.habitat_id")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and PSM.end_photo_id is null")
				->where("A.species != 97")
				->where("PSM.project_id = " . $this->projectId);
		
			error_log("query2 created: " . $query2->dump());
		}
		else {
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), A.animal_id, A.person_id, P.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(OD5.value, O.option_name), ' ', 1), P.photo_id, P.upload_filename, P.taken, P2.upload_filename, P2.taken, P2.sequence_num, S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.deployment_date, U.collection_date, REPLACE(IFNULL(OD.value, O.option_name), ',', ' -'), IFNULL(OD2.value, 'null'), IFNULL(OD3.value, 'null'), IFNULL(A.number, 'null'), IFNULL(A.notes, 'null'), IFNULL(OD4.value, 'null'), A.timestamp) as report_csv")
				->from("Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id and P.sequence_num = 1")
				->innerJoin("PhotoSequence PS on P.photo_id = PS.start_photo_id")
				->innerJoin("Photo P2 on PS.end_photo_id = P2.photo_id")
				->innerJoin("Site S on P.site_id = S.site_id")
				->innerJoin("Upload U on P.upload_id = U.upload_id")
				->innerJoin("Options O on O.option_id = A.species")
				->leftJoin("OptionData OD on OD.option_id = A.species and OD.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD2 on OD2.option_id = A.age and OD2.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD3 on OD3.option_id = A.gender and OD3.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD4 on OD4.option_id = A.sure and OD4.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD5 on OD5.option_id = S.habitat_id and OD5.data_type = '" . $this->languageTag . "'")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and P.photo_id <= PSM.end_photo_id")
				->where("A.species != 97")
				->where("PSM.project_id = " . $this->projectId);
			
			error_log("query1 created: " . $query1->dump() );
				
			$query2 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), A.animal_id, A.person_id, P.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(OD5.value, O.option_name), ' ', 1), P.photo_id, P.upload_filename, P.taken, P2.upload_filename, P2.taken, P2.sequence_num, S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.deployment_date, U.collection_date, REPLACE(IFNULL(OD.value, O.option_name), ',', ' -'), IFNULL(OD2.value, 'null'), IFNULL(OD3.value, 'null'), IFNULL(A.number, 'null'), IFNULL(A.notes, 'null'), IFNULL(OD4.value, 'null'), A.timestamp) as report_csv")
				->from("Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id and P.sequence_num = 1")
				->innerJoin("PhotoSequence PS on P.photo_id = PS.start_photo_id")
				->innerJoin("Photo P2 on PS.end_photo_id = P2.photo_id")
				->innerJoin("Site S on P.site_id = S.site_id")
				->innerJoin("Upload U on P.upload_id = U.upload_id")
				->innerJoin("Options O on O.option_id = A.species")
				->leftJoin("OptionData OD on OD.option_id = A.species and OD.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD2 on OD2.option_id = A.age and OD2.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD3 on OD3.option_id = A.gender and OD3.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD4 on OD4.option_id = A.sure and OD4.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD5 on OD5.option_id = S.habitat_id and OD5.data_type = '" . $this->languageTag . "'")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and PSM.end_photo_id is null")
				->where("A.species != 97")
				->where("PSM.project_id = " . $this->projectId);
		
			error_log("query2 created: " . $query2->dump());
		}
		
		$unionQuery = $db->getQuery(true)
             ->select('*')
             ->from('(' . $query1->union($query2) . ') a');
			 
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv')))
			->values($unionQuery);
		
		error_log("queryInsert created");
		
		$db->setQuery($queryInsert);
		
		error_log("About to execute");

		$db->execute();
		
		error_log("Execution complete");
					
		
		/*
		
		select A.animal_id, PSM.project_id, A.person_id, P.site_id, S.site_name, P.upload_filename as start_filename, P.photo_id as start_photo_id, P.taken as start_taken, P2.photo_id as end_photo_id, P2.taken as end_taken, S.grid_ref, O.option_name as species, O2.option_name as age, O3.option_name as gender, A.number, A.timestamp as classify_time 
		from Animal A 
		inner join Photo P on A.photo_id = P.photo_id and P.sequence_num = 1
		inner join PhotoSequence PS on P.photo_id = PS.start_photo_id
		inner join Photo P2 on PS.end_photo_id = P2.photo_id
		inner join Site S on P.site_id = S.site_id
		inner join Options O on O.option_id = A.species
		left join Options O2 on O2.option_id = A.age
		left join Options O3 on O3.option_id = A.gender
		inner join ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and P.photo_id <= PSM.end_photo_id 
		UNION 
		select A.animal_id, PSM.project_id, A.person_id, P.site_id, S.site_name, P.upload_filename as start_filename, P.photo_id as start_photo_id, P.taken as start_taken, P2.photo_id as end_photo_id, P2.taken as end_taken, S.grid_ref, O.option_name as species, O2.option_name as age, O3.option_name as gender, A.number, A.timestamp as classify_time 
		from Animal A 
		inner join Photo P on A.photo_id = P.photo_id and P.sequence_num = 1
		inner join PhotoSequence PS on P.photo_id = PS.start_photo_id
		inner join Photo P2 on PS.end_photo_id = P2.photo_id
		inner join Site S on P.site_id = S.site_id
		inner join Options O on O.option_id = A.species
		left join Options O2 on O2.option_id = A.age
		left join Options O3 on O3.option_id = A.gender
		inner join ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and PSM.end_photo_id is null 

*/

	}
		
		
	private function generateAnimalDataOrig () {
		
		// Delete any existing data 
		$this->removePreviousRows();
		
		$db = JDatabase::getInstance(dbOptions());
		$query1 = null;
		$query2 = null;
		
		// Will need to do this differently for non English language...
		if ( $this->languageTag == 'en-GB' ) {
		
			// We have a table for this report to improve performance, but only updated periodically - try getting direct to start with
			// Set up the select queries for the report
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), A.animal_id, A.person_id, P.site_id, REPLACE(S.site_name, ',', ' '), REPLACE(IFNULL(O5.option_name, 'null'), ',', ' -'), P.photo_id, P.upload_filename, P.taken, P2.upload_filename, P2.taken, P2.sequence_num, S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), REPLACE(O.option_name, ',', ' -'), IFNULL(O2.option_name, 'null'), IFNULL(O3.option_name, 'null'), IFNULL(A.number, 'null'), IFNULL(A.notes, 'null'), IFNULL(O4.option_name, 'null'), A.timestamp) as report_csv")
				->from("Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id and P.sequence_num = 1")
				->innerJoin("PhotoSequence PS on P.photo_id = PS.start_photo_id")
				->innerJoin("Photo P2 on PS.end_photo_id = P2.photo_id")
				->innerJoin("Site S on P.site_id = S.site_id")
				->innerJoin("Options O on O.option_id = A.species")
				->leftJoin("Options O2 on O2.option_id = A.age")
				->leftJoin("Options O3 on O3.option_id = A.gender")
				->leftJoin("Options O4 on O4.option_id = A.sure")
				->leftJoin("Options O5 on O5.option_id = S.habitat_id")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and P.photo_id <= PSM.end_photo_id")
				->where("PSM.project_id = " . $this->projectId);
			
			error_log("query1 created: " . $query1->dump() );
				
			$query2 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), A.animal_id, A.person_id, P.site_id, REPLACE(S.site_name, ',', ' '), REPLACE(IFNULL(O5.option_name, 'null'), ',', ' -'), P.photo_id, P.upload_filename, P.taken, P2.upload_filename, P2.taken, P2.sequence_num, S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), REPLACE(O.option_name, ',', ' -'), IFNULL(O2.option_name, 'null'), IFNULL(O3.option_name, 'null'), IFNULL(A.number, 'null'), IFNULL(A.notes, 'null'), IFNULL(O4.option_name, 'null'), A.timestamp) as report_csv")
				->from("Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id and P.sequence_num = 1")
				->innerJoin("PhotoSequence PS on P.photo_id = PS.start_photo_id")
				->innerJoin("Photo P2 on PS.end_photo_id = P2.photo_id")
				->innerJoin("Site S on P.site_id = S.site_id")
				->innerJoin("Options O on O.option_id = A.species")
				->leftJoin("Options O2 on O2.option_id = A.age")
				->leftJoin("Options O3 on O3.option_id = A.gender")
				->leftJoin("Options O4 on O4.option_id = A.sure")
				->leftJoin("Options O5 on O5.option_id = S.habitat_id")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and PSM.end_photo_id is null")
				->where("PSM.project_id = " . $this->projectId);
		
			error_log("query2 created: " . $query2->dump());
		}
		else {
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), A.animal_id, A.person_id, P.site_id, REPLACE(S.site_name, ',', ' '), IFNULL(OD5.value, O.option_name), P.photo_id, P.upload_filename, P.taken, P2.upload_filename, P2.taken, P2.sequence_num, S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), REPLACE(IFNULL(OD.value, O.option_name), ',', ' -'), IFNULL(OD2.value, 'null'), IFNULL(OD3.value, 'null'), IFNULL(A.number, 'null'), IFNULL(A.notes, 'null'), IFNULL(OD4.value, 'null'), A.timestamp) as report_csv")
				->from("Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id and P.sequence_num = 1")
				->innerJoin("PhotoSequence PS on P.photo_id = PS.start_photo_id")
				->innerJoin("Photo P2 on PS.end_photo_id = P2.photo_id")
				->innerJoin("Site S on P.site_id = S.site_id")
				->innerJoin("Options O on O.option_id = A.species")
				->leftJoin("OptionData OD on OD.option_id = A.species and OD.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD2 on OD2.option_id = A.age and OD2.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD3 on OD3.option_id = A.gender and OD3.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD4 on OD4.option_id = A.sure and OD4.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD5 on OD5.option_id = S.habitat_id and OD5.data_type = '" . $this->languageTag . "'")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and P.photo_id <= PSM.end_photo_id")
				->where("PSM.project_id = " . $this->projectId);
			
			error_log("query1 created: " . $query1->dump() );
				
			$query2 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), A.animal_id, A.person_id, P.site_id, REPLACE(S.site_name, ',', ' '), IFNULL(OD5.value, O.option_name), P.photo_id, P.upload_filename, P.taken, P2.upload_filename, P2.taken, P2.sequence_num, S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), REPLACE(IFNULL(OD.value, O.option_name), ',', ' -'), IFNULL(OD2.value, 'null'), IFNULL(OD3.value, 'null'), IFNULL(A.number, 'null'), IFNULL(A.notes, 'null'), IFNULL(OD4.value, 'null'), A.timestamp) as report_csv")
				->from("Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id and P.sequence_num = 1")
				->innerJoin("PhotoSequence PS on P.photo_id = PS.start_photo_id")
				->innerJoin("Photo P2 on PS.end_photo_id = P2.photo_id")
				->innerJoin("Site S on P.site_id = S.site_id")
				->innerJoin("Options O on O.option_id = A.species")
				->leftJoin("OptionData OD on OD.option_id = A.species and OD.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD2 on OD2.option_id = A.age and OD2.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD3 on OD3.option_id = A.gender and OD3.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD4 on OD4.option_id = A.sure and OD4.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD5 on OD5.option_id = S.habitat_id and OD5.data_type = '" . $this->languageTag . "'")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and PSM.end_photo_id is null")
				->where("PSM.project_id = " . $this->projectId);
		
			error_log("query2 created: " . $query2->dump());
		}
		
		$unionQuery = $db->getQuery(true)
             ->select('*')
             ->from('(' . $query1->union($query2) . ') a');
			 
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv')))
			->values($unionQuery);
		
		error_log("queryInsert created");
		
		$db->setQuery($queryInsert);
		
		error_log("About to execute");

		$db->execute();
		
		error_log("Execution complete");
					
		
		/*
		
		select A.animal_id, PSM.project_id, A.person_id, P.site_id, S.site_name, P.upload_filename as start_filename, P.photo_id as start_photo_id, P.taken as start_taken, P2.photo_id as end_photo_id, P2.taken as end_taken, S.grid_ref, O.option_name as species, O2.option_name as age, O3.option_name as gender, A.number, A.timestamp as classify_time 
		from Animal A 
		inner join Photo P on A.photo_id = P.photo_id and P.sequence_num = 1
		inner join PhotoSequence PS on P.photo_id = PS.start_photo_id
		inner join Photo P2 on PS.end_photo_id = P2.photo_id
		inner join Site S on P.site_id = S.site_id
		inner join Options O on O.option_id = A.species
		left join Options O2 on O2.option_id = A.age
		left join Options O3 on O3.option_id = A.gender
		inner join ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and P.photo_id <= PSM.end_photo_id 
		UNION 
		select A.animal_id, PSM.project_id, A.person_id, P.site_id, S.site_name, P.upload_filename as start_filename, P.photo_id as start_photo_id, P.taken as start_taken, P2.photo_id as end_photo_id, P2.taken as end_taken, S.grid_ref, O.option_name as species, O2.option_name as age, O3.option_name as gender, A.number, A.timestamp as classify_time 
		from Animal A 
		inner join Photo P on A.photo_id = P.photo_id and P.sequence_num = 1
		inner join PhotoSequence PS on P.photo_id = PS.start_photo_id
		inner join Photo P2 on PS.end_photo_id = P2.photo_id
		inner join Site S on P.site_id = S.site_id
		inner join Options O on O.option_id = A.species
		left join Options O2 on O2.option_id = A.age
		left join Options O3 on O3.option_id = A.gender
		inner join ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and PSM.end_photo_id is null 

*/

	}
		
		
private function generateAnimalDataAudio () {
		
		// Delete any existing data 
		$this->removePreviousRows();
		
		$db = JDatabase::getInstance(dbOptions());
		$query1 = null;
		$query2 = null;
		
		// Will need to do this differently for non English language...
		if ( $this->languageTag == 'en-GB' ) {
		
			// We have a table for this report to improve performance, but only updated periodically - try getting direct to start with
			// Set up the select queries for the report
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), A.animal_id, A.person_id, P.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), P.photo_id, P.upload_filename, P.taken, S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), REPLACE(O.option_name, ',', ' -'), IFNULL(A.notes, 'null'), IFNULL(O2.option_name, 'null'), A.timestamp) as report_csv")
				->from("Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id and P.sequence_num = 1")
				->innerJoin("PhotoSequence PS on P.photo_id = PS.start_photo_id")
				->innerJoin("Site S on P.site_id = S.site_id")
				->innerJoin("Options O on O.option_id = A.species")
				->leftJoin("Options O2 on O2.option_id = A.sure")
				->leftJoin("Options O5 on O5.option_id = S.habitat_id")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and P.photo_id <= PSM.end_photo_id")
				->where("A.species != 97")
				->where("PSM.project_id = " . $this->projectId);
			
			error_log("query1 created: " . $query1->dump() );
				
			$query2 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), A.animal_id, A.person_id, P.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), P.photo_id, P.upload_filename, P.taken, S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), REPLACE(O.option_name, ',', ' -'), IFNULL(A.notes, 'null'), IFNULL(O2.option_name, 'null'), A.timestamp) as report_csv")
				->from("Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id and P.sequence_num = 1")
				->innerJoin("PhotoSequence PS on P.photo_id = PS.start_photo_id")
				->innerJoin("Site S on P.site_id = S.site_id")
				->innerJoin("Options O on O.option_id = A.species")
				->leftJoin("Options O2 on O2.option_id = A.sure")
				->leftJoin("Options O5 on O5.option_id = S.habitat_id")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and PSM.end_photo_id is null")
				->where("A.species != 97")
				->where("PSM.project_id = " . $this->projectId);
		
			error_log("query2 created: " . $query2->dump());
		}
		else {
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), A.animal_id, A.person_id, P.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(OD5.value, 'null'), ' ', 1), P.photo_id, P.upload_filename, P.taken, S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), REPLACE(IFNULL(OD.value, O.option_name), ',', ' -'), IFNULL(A.notes, 'null'), IFNULL(OD2.value, 'null'), A.timestamp) as report_csv")
				->from("Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id and P.sequence_num = 1")
				->innerJoin("PhotoSequence PS on P.photo_id = PS.start_photo_id")
				->innerJoin("Site S on P.site_id = S.site_id")
				->innerJoin("Options O on O.option_id = A.species")
				->leftJoin("OptionData OD on OD.option_id = A.species and OD.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD2 on OD2.option_id = A.sure and OD2.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD5 on OD5.option_id = S.habitat_id and OD5.data_type = '" . $this->languageTag . "'")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and P.photo_id <= PSM.end_photo_id")
				->where("A.species != 97")
				->where("PSM.project_id = " . $this->projectId);
			
			error_log("query1 created: " . $query1->dump() );
				
			$query2 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), A.animal_id, A.person_id, P.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(OD5.value, 'null'), ' ', 1), P.photo_id, P.upload_filename, P.taken, S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), REPLACE(IFNULL(OD.value, O.option_name), ',', ' -'), IFNULL(A.notes, 'null'), IFNULL(OD2.value, 'null'), A.timestamp) as report_csv")
				->from("Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id and P.sequence_num = 1")
				->innerJoin("PhotoSequence PS on P.photo_id = PS.start_photo_id")
				->innerJoin("Site S on P.site_id = S.site_id")
				->innerJoin("Options O on O.option_id = A.species")
				->leftJoin("OptionData OD on OD.option_id = A.species and OD.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD2 on OD2.option_id = A.sure and OD2.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD5 on OD5.option_id = S.habitat_id and OD5.data_type = '" . $this->languageTag . "'")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and PSM.end_photo_id is null")
				->where("A.species != 97")
				->where("PSM.project_id = " . $this->projectId);
		
			error_log("query2 created: " . $query2->dump());
		}
		
		$unionQuery = $db->getQuery(true)
             ->select('*')
             ->from('(' . $query1->union($query2) . ') a');
			 
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv')))
			->values($unionQuery);
		
		error_log("queryInsert created");
		
		$db->setQuery($queryInsert);
		
		error_log("About to execute");

		$db->execute();
		
		error_log("Execution complete");
					
		
		/*
		
		select A.animal_id, PSM.project_id, A.person_id, P.site_id, S.site_name, P.upload_filename as start_filename, P.photo_id as start_photo_id, P.taken as start_taken, P2.photo_id as end_photo_id, P2.taken as end_taken, S.grid_ref, O.option_name as species, O2.option_name as age, O3.option_name as gender, A.number, A.timestamp as classify_time 
		from Animal A 
		inner join Photo P on A.photo_id = P.photo_id and P.sequence_num = 1
		inner join PhotoSequence PS on P.photo_id = PS.start_photo_id
		inner join Photo P2 on PS.end_photo_id = P2.photo_id
		inner join Site S on P.site_id = S.site_id
		inner join Options O on O.option_id = A.species
		left join Options O2 on O2.option_id = A.age
		left join Options O3 on O3.option_id = A.gender
		inner join ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and P.photo_id <= PSM.end_photo_id 
		UNION 
		select A.animal_id, PSM.project_id, A.person_id, P.site_id, S.site_name, P.upload_filename as start_filename, P.photo_id as start_photo_id, P.taken as start_taken, P2.photo_id as end_photo_id, P2.taken as end_taken, S.grid_ref, O.option_name as species, O2.option_name as age, O3.option_name as gender, A.number, A.timestamp as classify_time 
		from Animal A 
		inner join Photo P on A.photo_id = P.photo_id and P.sequence_num = 1
		inner join PhotoSequence PS on P.photo_id = PS.start_photo_id
		inner join Photo P2 on PS.end_photo_id = P2.photo_id
		inner join Site S on P.site_id = S.site_id
		inner join Options O on O.option_id = A.species
		left join Options O2 on O2.option_id = A.age
		left join Options O3 on O3.option_id = A.gender
		inner join ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and PSM.end_photo_id is null 

*/

	}
	
	
	
	private function generateNoAnimalData () {
		
		// Delete any existing data 
		$this->removePreviousRows();
		
		$db = JDatabase::getInstance(dbOptions());
		$query1 = null;
		$query2 = null;
		
		// 
		if ( $this->languageTag == 'en-GB' ) {
		
			// We have a table for this report to improve performance, but only updated periodically - try getting direct to start with
			// Set up the select queries for the report
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), 'null', 'null', P.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), P.photo_id, P.upload_filename, P.taken, P2.upload_filename, P2.taken, P2.sequence_num, S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.deployment_date, U.collection_date, 'null', 'null', 'null', 'null', 'null', 'null', 'null') as report_csv")
				->from("Photo P")
				->leftJoin("Animal A on A.photo_id = P.photo_id and P.sequence_num = 1")
				->innerJoin("PhotoSequence PS on P.photo_id = PS.start_photo_id")
				->innerJoin("Photo P2 on PS.end_photo_id = P2.photo_id")
				->innerJoin("Site S on P.site_id = S.site_id")
				->innerJoin("Upload U on P.upload_id = U.upload_id")
				->leftJoin("Options O5 on O5.option_id = S.habitat_id")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and P.photo_id <= PSM.end_photo_id")
				->where("A.photo_id is NULL")
				->where("PSM.project_id = " . $this->projectId);
			
			error_log("query1 created: " . $query1->dump() );
				
			$query2 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), 'null', 'null', P.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), P.photo_id, P.upload_filename, P.taken, P2.upload_filename, P2.taken, P2.sequence_num, S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.deployment_date, U.collection_date, 'null', 'null', 'null', 'null', 'null', 'null', 'null') as report_csv")
				->from("Photo P")
				->leftJoin("Animal A on A.photo_id = P.photo_id and P.sequence_num = 1")
				->innerJoin("PhotoSequence PS on P.photo_id = PS.start_photo_id")
				->innerJoin("Photo P2 on PS.end_photo_id = P2.photo_id")
				->innerJoin("Site S on P.site_id = S.site_id")
				->innerJoin("Upload U on P.upload_id = U.upload_id")
				->leftJoin("Options O5 on O5.option_id = S.habitat_id")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and PSM.end_photo_id is null")
				->where("A.photo_id is NULL")
				->where("PSM.project_id = " . $this->projectId);
		
			error_log("query2 created: " . $query2->dump());
		}
		else {
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), 'null', 'null', P.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(OD5.value, 'null'), ' ', 1), P.photo_id, P.upload_filename, P.taken, P2.upload_filename, P2.taken, P2.sequence_num, S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.deployment_date, U.collection_date, 'null', 'null', 'null', 'null', 'null', 'null', 'null') as report_csv")
				->from("Photo P")
				->leftJoin("Animal A on A.photo_id = P.photo_id and P.sequence_num = 1")
				->innerJoin("PhotoSequence PS on P.photo_id = PS.start_photo_id")
				->innerJoin("Photo P2 on PS.end_photo_id = P2.photo_id")
				->innerJoin("Site S on P.site_id = S.site_id")
				->innerJoin("Upload U on P.upload_id = U.upload_id")
				->leftJoin("OptionData OD5 on OD5.option_id = S.habitat_id and OD5.data_type = '" . $this->languageTag . "'")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and P.photo_id <= PSM.end_photo_id")
				->where("A.photo_id is NULL")
				->where("PSM.project_id = " . $this->projectId);
			
			error_log("query1 created: " . $query1->dump() );
				
			$query2 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), 'null', 'null', P.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(OD5.value, 'null'), ' ', 1), P.photo_id, P.upload_filename, P.taken, P2.upload_filename, P2.taken, P2.sequence_num, S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.deployment_date, U.collection_date, 'null', 'null', 'null', 'null', 'null', 'null', 'null') as report_csv")
				->from("Photo P")
				->leftJoin("Animal A on A.photo_id = P.photo_id and P.sequence_num = 1")
				->innerJoin("PhotoSequence PS on P.photo_id = PS.start_photo_id")
				->innerJoin("Photo P2 on PS.end_photo_id = P2.photo_id")
				->innerJoin("Site S on P.site_id = S.site_id")
				->innerJoin("Upload U on P.upload_id = U.upload_id")
				->leftJoin("OptionData OD5 on OD5.option_id = S.habitat_id and OD5.data_type = '" . $this->languageTag . "'")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and PSM.end_photo_id is null")
				->where("A.photo_id is NULL")
				->where("PSM.project_id = " . $this->projectId);
		
			error_log("query2 created: " . $query2->dump());
		}
		
		$unionQuery = $db->getQuery(true)
             ->select('*')
             ->from('(' . $query1->union($query2) . ') a');
			 
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv')))
			->values($unionQuery);
		
		error_log("queryInsert created");
		
		$db->setQuery($queryInsert);
		
		error_log("About to execute");

		$db->execute();
		
		error_log("Execution complete");
	}
	
	
	private function generateNoAnimalDataAudio () {
		
		// Delete any existing data 
		$this->removePreviousRows();
		
		$db = JDatabase::getInstance(dbOptions());
		$query1 = null;
		$query2 = null;
		
		// 
		if ( $this->languageTag == 'en-GB' ) {
		
			// We have a table for this report to improve performance, but only updated periodically - try getting direct to start with
			// Set up the select queries for the report
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), 'null', 'null', P.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), P.photo_id, P.upload_filename, P.taken, S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), 'null', 'null', 'null', 'null') as report_csv")
				->from("Photo P")
				->leftJoin("Animal A on A.photo_id = P.photo_id and P.sequence_num = 1")
				->innerJoin("PhotoSequence PS on P.photo_id = PS.start_photo_id")
				->innerJoin("Site S on P.site_id = S.site_id")
				->leftJoin("Options O5 on O5.option_id = S.habitat_id")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and P.photo_id <= PSM.end_photo_id")
				->where("A.photo_id is NULL")
				->where("PSM.project_id = " . $this->projectId);
			
			error_log("query1 created: " . $query1->dump() );
				
			$query2 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), 'null', 'null', P.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), P.photo_id, P.upload_filename, P.taken, S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), 'null', 'null', 'null', 'null') as report_csv")
				->from("Photo P")
				->leftJoin("Animal A on A.photo_id = P.photo_id and P.sequence_num = 1")
				->innerJoin("PhotoSequence PS on P.photo_id = PS.start_photo_id")
				->innerJoin("Site S on P.site_id = S.site_id")
				->leftJoin("Options O5 on O5.option_id = S.habitat_id")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and PSM.end_photo_id is null")
				->where("A.photo_id is NULL")
				->where("PSM.project_id = " . $this->projectId);
		
			error_log("query2 created: " . $query2->dump());
		}
		else {
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), 'null', 'null', P.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(OD5.value, 'null'), ' ', 1), P.photo_id, P.upload_filename, P.taken, S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), 'null', 'null', 'null', 'null') as report_csv")
				->from("Photo P")
				->leftJoin("Animal A on A.photo_id = P.photo_id and P.sequence_num = 1")
				->innerJoin("PhotoSequence PS on P.photo_id = PS.start_photo_id")
				->innerJoin("Site S on P.site_id = S.site_id")
				->leftJoin("OptionData OD5 on OD5.option_id = S.habitat_id and OD5.data_type = '" . $this->languageTag . "'")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and P.photo_id <= PSM.end_photo_id")
				->where("A.photo_id is NULL")
				->where("PSM.project_id = " . $this->projectId);
			
			error_log("query1 created: " . $query1->dump() );
				
			$query2 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), 'null', 'null', P.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(OD5.value, 'null'), ' ', 1), P.photo_id, P.upload_filename, P.taken, S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), 'null', 'null', 'null', 'null') as report_csv")
				->from("Photo P")
				->leftJoin("Animal A on A.photo_id = P.photo_id and P.sequence_num = 1")
				->innerJoin("PhotoSequence PS on P.photo_id = PS.start_photo_id")
				->innerJoin("Site S on P.site_id = S.site_id")
				->leftJoin("OptionData OD5 on OD5.option_id = S.habitat_id and OD5.data_type = '" . $this->languageTag . "'")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and PSM.end_photo_id is null")
				->where("A.photo_id is NULL")
				->where("PSM.project_id = " . $this->projectId);
		
			error_log("query2 created: " . $query2->dump());
		}
		
		$unionQuery = $db->getQuery(true)
             ->select('*')
             ->from('(' . $query1->union($query2) . ') a');
			 
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv')))
			->values($unionQuery);
		
		error_log("queryInsert created");
		
		$db->setQuery($queryInsert);
		
		error_log("About to execute");

		$db->execute();
		
		error_log("Execution complete");
	}
	
	
	
	private function generateSequenceData () {
		
		// Delete any existing data 
		$this->removePreviousRows();
		
		$db = JDatabase::getInstance(dbOptions());
		$query1 = null;
		$query2 = null;
		
		$query1 = $db->getQuery(true);
		$query1->select("P.site_id as site_id, REPLACE(S.site_name, ',', ' ') as site_name, P.sequence_id as sequence_id, A.animal_id as animal_id, IF(A.animal_id>0, 1, 0) as animal_num from Photo P")
			->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id")
			->innerJoin("Site S on S.site_id = P.site_id")
			->leftJoin("Animal A on P.photo_id = A.photo_id and A.species != 97")
			->where("P.sequence_id > 0")
			->where("PSM.project_id = " . $this->projectId )
			->where("((P.photo_id >= PSM.start_photo_id and PSM.end_photo_id is NULL) or (P.photo_id >= PSM.start_photo_id and P.photo_id <= PSM.end_photo_id))"  );
			
		$query2 = $db->getQuery(true)
             ->select( "a.site_id, a.site_name, a.sequence_id, IF(sum(a.animal_num)>0, 1, 0) as num_class" )
			 ->from('(' . $query1 . ') a')
			 ->group('a.site_id, a.site_name, a.sequence_id');
			 
		$query3 = $db->getQuery(true)
             ->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', b.site_id, b.site_name, count(*), sum(b.num_class)) as report_csv")
			 ->from('(' . $query2 . ') b')
			 ->group('b.site_id, b.site_name');
			 
		//$db->setQuery($query3);
		
		//$results = $db->loadAssocList('site_id');
		
		//$err_msg = print_r ( $results, true );
		//error_log ( "BiodivReport::generateSequenceData, query results: " . $err_msg );
		
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv')))
			->values($query3);
		
		error_log("queryInsert created");
		
		$db->setQuery($queryInsert);
		
		error_log("About to execute");

		$db->execute();
		
		error_log("Execution complete");

	}
	
	private function generateUserSequenceData () {
		
		// Delete any existing data 
		$this->removePreviousRows();
		
		$db = JDatabase::getInstance(dbOptions());
		$query1 = null;
		$query2 = null;
		
		$query1 = $db->getQuery(true);
		$query1->select("P.site_id as site_id, REPLACE(S.site_name, ',', ' ') as site_name, P.sequence_id as sequence_id, A.animal_id as animal_id, IF(A.animal_id>0, 1, 0) as animal_num from Photo P")
			->innerJoin("Site S on S.site_id = P.site_id and S.person_id = " . $this->personId )
			->leftJoin("Animal A on P.photo_id = A.photo_id and A.species != 97")
			->where("P.sequence_id > 0");
			
		error_log("USERSEQUENCE query1 created: " . $query1->dump());
			
		$query2 = $db->getQuery(true)
             ->select( "a.site_id, a.site_name, a.sequence_id, IF(sum(a.animal_num)>0, 1, 0) as num_class" )
			 ->from('(' . $query1 . ') a')
			 ->group('a.site_id, a.site_name, a.sequence_id');
			 
		error_log("USERSEQUENCE query2 created: " . $query2->dump());
		
		$query3 = $db->getQuery(true)
             ->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', b.site_id, b.site_name, count(*), sum(b.num_class)) as report_csv")
			 ->from('(' . $query2 . ') b')
			 ->group('b.site_id, b.site_name');
		
		error_log("USERSEQUENCE query3 created: " . $query3->dump());
				
		//$db->setQuery($query3);
		
		//$results = $db->loadAssocList('site_id');
		
		//$err_msg = print_r ( $results, true );
		//error_log ( "BiodivReport::generateSequenceData, query results: " . $err_msg );
		
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv')))
			->values($query3);
		
		error_log("queryInsert created");
		
		$db->setQuery($queryInsert);
		
		error_log("About to execute");

		$db->execute();
		
		error_log("Execution complete");

	}
	
	private function generateUserAnimal () {
		
		// Delete any existing data 
		$this->removePreviousRows();
		
		$db = JDatabase::getInstance(dbOptions());
		$query1 = null;
		$query2 = null;
		
		// Will need to do this differently for non English language...
		if ( $this->languageTag == 'en-GB' ) {
		
			// We have a table for this report to improve performance, but only updated periodically - try getting direct to start with
			// Set up the select queries for the report
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), A.animal_id, P.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), P.photo_id, P.upload_filename, P.taken, P2.upload_filename, P2.taken, P2.sequence_num, S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.deployment_date, U.collection_date, REPLACE(O.option_name, ',', ' -'), IFNULL(O2.option_name, 'null'), IFNULL(O3.option_name, 'null'), IFNULL(A.number, 'null'), IFNULL(A.notes, 'null'), IFNULL(O4.option_name, 'null'), A.timestamp) as report_csv")
				->from("Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id and P.sequence_num = 1")
				->innerJoin("PhotoSequence PS on P.photo_id = PS.start_photo_id")
				->innerJoin("Photo P2 on PS.end_photo_id = P2.photo_id")
				->innerJoin("Site S on P.site_id = S.site_id and S.person_id = " . $this->personId)
				->innerJoin("Upload U on P.upload_id = U.upload_id")
				->innerJoin("Options O on O.option_id = A.species")
				->leftJoin("Options O2 on O2.option_id = A.age")
				->leftJoin("Options O3 on O3.option_id = A.gender")
				->leftJoin("Options O4 on O4.option_id = A.sure")
				->leftJoin("Options O5 on O5.option_id = S.habitat_id")
				->where("A.species != 97")
				->where("A.person_id = " . $this->personId);
			
			error_log("query1 created: " . $query1->dump() );
		}
		else {
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), A.animal_id, P.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(OD5.value, O.option_name), ' ', 1), P.photo_id, P.upload_filename, P.taken, P2.upload_filename, P2.taken, P2.sequence_num, S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.deployment_date, U.collection_date, REPLACE(IFNULL(OD.value, O.option_name), ',', ' -'), IFNULL(OD2.value, 'null'), IFNULL(OD3.value, 'null'), IFNULL(A.number, 'null'), IFNULL(A.notes, 'null'), IFNULL(OD4.value, 'null'), A.timestamp) as report_csv")
				->from("Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id and P.sequence_num = 1")
				->innerJoin("PhotoSequence PS on P.photo_id = PS.start_photo_id")
				->innerJoin("Photo P2 on PS.end_photo_id = P2.photo_id")
				->innerJoin("Site S on P.site_id = S.site_id and S.person_id = " . $this->personId)
				->innerJoin("Upload U on P.upload_id = U.upload_id")
				->innerJoin("Options O on O.option_id = A.species")
				->leftJoin("OptionData OD on OD.option_id = A.species and OD.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD2 on OD2.option_id = A.age and OD2.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD3 on OD3.option_id = A.gender and OD3.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD4 on OD4.option_id = A.sure and OD4.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD5 on OD5.option_id = S.habitat_id and OD5.data_type = '" . $this->languageTag . "'")
				->where("A.species != 97")
				->where("A.person_id = " . $this->personId);
			
			error_log("query1 created: " . $query1->dump() );
		}
		
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv')))
			->values($query1);
		
		error_log("queryInsert created");
		
		$db->setQuery($queryInsert);
		
		error_log("About to execute");

		$db->execute();
		
		error_log("Execution complete");

	}
		
	private function generateUserAnimalOthers () {
		
		// Delete any existing data 
		$this->removePreviousRows();
		
		$db = JDatabase::getInstance(dbOptions());
		$query1 = null;
		$query2 = null;
		
		// Will need to do this differently for non English language...
		if ( $this->languageTag == 'en-GB' ) {
		
			// We have a table for this report to improve performance, but only updated periodically - try getting direct to start with
			// Set up the select queries for the report
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), A.animal_id, P.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), P.photo_id, P.upload_filename, P.taken, P2.upload_filename, P2.taken, P2.sequence_num, S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.deployment_date, U.collection_date, REPLACE(O.option_name, ',', ' -'), IFNULL(O2.option_name, 'null'), IFNULL(O3.option_name, 'null'), IFNULL(A.number, 'null'), IFNULL(A.notes, 'null'), IFNULL(O4.option_name, 'null'), A.timestamp) as report_csv")
				->from("Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id and P.sequence_num = 1")
				->innerJoin("PhotoSequence PS on P.photo_id = PS.start_photo_id")
				->innerJoin("Photo P2 on PS.end_photo_id = P2.photo_id")
				->innerJoin("Site S on P.site_id = S.site_id and S.person_id = " . $this->personId)
				->innerJoin("Upload U on P.upload_id = U.upload_id")
				->innerJoin("Options O on O.option_id = A.species")
				->leftJoin("Options O2 on O2.option_id = A.age")
				->leftJoin("Options O3 on O3.option_id = A.gender")
				->leftJoin("Options O4 on O4.option_id = A.sure")
				->leftJoin("Options O5 on O5.option_id = S.habitat_id")
				->where("A.species != 97")
				->where("A.person_id != " . $this->personId);
			
			error_log("query1 created: " . $query1->dump() );
		}
		else {
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), A.animal_id, P.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(OD5.value, O.option_name), ' ', 1), P.photo_id, P.upload_filename, P.taken, P2.upload_filename, P2.taken, P2.sequence_num, S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.deployment_date, U.collection_date, REPLACE(IFNULL(OD.value, O.option_name), ',', ' -'), IFNULL(OD2.value, 'null'), IFNULL(OD3.value, 'null'), IFNULL(A.number, 'null'), IFNULL(A.notes, 'null'), IFNULL(OD4.value, 'null'), A.timestamp) as report_csv")
				->from("Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id and P.sequence_num = 1")
				->innerJoin("PhotoSequence PS on P.photo_id = PS.start_photo_id")
				->innerJoin("Photo P2 on PS.end_photo_id = P2.photo_id")
				->innerJoin("Site S on P.site_id = S.site_id and S.person_id = " . $this->personId)
				->innerJoin("Upload U on P.upload_id = U.upload_id")
				->innerJoin("Options O on O.option_id = A.species")
				->leftJoin("OptionData OD on OD.option_id = A.species and OD.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD2 on OD2.option_id = A.age and OD2.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD3 on OD3.option_id = A.gender and OD3.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD4 on OD4.option_id = A.sure and OD4.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD5 on OD5.option_id = S.habitat_id and OD5.data_type = '" . $this->languageTag . "'")
				->where("A.species != 97")
				->where("A.person_id != " . $this->personId);
			
			error_log("query1 created: " . $query1->dump() );
		}
		
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv')))
			->values($query1);
		
		error_log("queryInsert created");
		
		$db->setQuery($queryInsert);
		
		error_log("About to execute");

		$db->execute();
		
		error_log("Execution complete");

	}
	
	private function generateUserNoAnimal () {
		
		// Delete any existing data 
		$this->removePreviousRows();
		
		$db = JDatabase::getInstance(dbOptions());
		$query1 = null;
		$query2 = null;
		
		// 
		if ( $this->languageTag == 'en-GB' ) {
		
			// We have a table for this report to improve performance, but only updated periodically - try getting direct to start with
			// Set up the select queries for the report
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), 'null', 'null', P.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), P.photo_id, P.upload_filename, P.taken, P2.upload_filename, P2.taken, P2.sequence_num, S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.deployment_date, U.collection_date, 'null', 'null', 'null', 'null', 'null', 'null', 'null') as report_csv")
				->from("Photo P")
				->leftJoin("Animal A on A.photo_id = P.photo_id and P.sequence_num = 1")
				->innerJoin("PhotoSequence PS on P.photo_id = PS.start_photo_id")
				->innerJoin("Photo P2 on PS.end_photo_id = P2.photo_id")
				->innerJoin("Site S on P.site_id = S.site_id and S.person_id = " . $this->personId )
				->innerJoin("Upload U on P.upload_id = U.upload_id")
				->leftJoin("Options O5 on O5.option_id = S.habitat_id")
				->where("A.photo_id is NULL");
			
			error_log("query1 created: " . $query1->dump() );
				
			
		}
		else {
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), 'null', 'null', P.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(OD5.value, 'null'), ' ', 1), P.photo_id, P.upload_filename, P.taken, P2.upload_filename, P2.taken, P2.sequence_num, S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.deployment_date, U.collection_date, 'null', 'null', 'null', 'null', 'null', 'null', 'null') as report_csv")
				->from("Photo P")
				->leftJoin("Animal A on A.photo_id = P.photo_id and P.sequence_num = 1")
				->innerJoin("PhotoSequence PS on P.photo_id = PS.start_photo_id")
				->innerJoin("Photo P2 on PS.end_photo_id = P2.photo_id")
				->innerJoin("Site S on P.site_id = S.site_id and S.person_id = " . $this->personId)
				->innerJoin("Upload U on P.upload_id = U.upload_id")
				->leftJoin("OptionData OD5 on OD5.option_id = S.habitat_id and OD5.data_type = '" . $this->languageTag . "'")
				->where("A.photo_id is NULL");
			
			error_log("query1 created: " . $query1->dump() );
				
			
		}
		
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv')))
			->values($query1);
		
		error_log("queryInsert created");
		
		$db->setQuery($queryInsert);
		
		error_log("About to execute");

		$db->execute();
		
		error_log("Execution complete");
	}
	
	
	private function generateUserAnimalAll () {
		
		// Delete any existing data 
		$this->removePreviousRows();
		
		$db = JDatabase::getInstance(dbOptions());
		$query1 = null;
		$query2 = null;
		
		// Will need to do this differently for non English language...
		if ( $this->languageTag == 'en-GB' ) {
		
			// We have a table for this report to improve performance, but only updated periodically - try getting direct to start with
			// Set up the select queries for the report
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), A.animal_id, P.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), P.photo_id, P.upload_filename, P.taken, P2.upload_filename, P2.taken, P2.sequence_num, S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.deployment_date, U.collection_date, REPLACE(O.option_name, ',', ' -'), IFNULL(O2.option_name, 'null'), IFNULL(O3.option_name, 'null'), IFNULL(A.number, 'null'), IFNULL(A.notes, 'null'), IFNULL(O4.option_name, 'null'), A.timestamp) as report_csv")
				->from("Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id and P.sequence_num = 1")
				->innerJoin("PhotoSequence PS on P.photo_id = PS.start_photo_id")
				->innerJoin("Photo P2 on PS.end_photo_id = P2.photo_id")
				->innerJoin("Site S on P.site_id = S.site_id")
				->innerJoin("Upload U on P.upload_id = U.upload_id")
				->innerJoin("Options O on O.option_id = A.species")
				->leftJoin("Options O2 on O2.option_id = A.age")
				->leftJoin("Options O3 on O3.option_id = A.gender")
				->leftJoin("Options O4 on O4.option_id = A.sure")
				->leftJoin("Options O5 on O5.option_id = S.habitat_id")
				->where("A.species != 97")
				->where("A.person_id = " . $this->personId);
			
			error_log("query1 created: " . $query1->dump() );
		}
		else {
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), A.animal_id, P.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(OD5.value, O.option_name), ' ', 1), P.photo_id, P.upload_filename, P.taken, P2.upload_filename, P2.taken, P2.sequence_num, S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.deployment_date, U.collection_date, REPLACE(IFNULL(OD.value, O.option_name), ',', ' -'), IFNULL(OD2.value, 'null'), IFNULL(OD3.value, 'null'), IFNULL(A.number, 'null'), IFNULL(A.notes, 'null'), IFNULL(OD4.value, 'null'), A.timestamp) as report_csv")
				->from("Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id and P.sequence_num = 1")
				->innerJoin("PhotoSequence PS on P.photo_id = PS.start_photo_id")
				->innerJoin("Photo P2 on PS.end_photo_id = P2.photo_id")
				->innerJoin("Site S on P.site_id = S.site_id")
				->innerJoin("Upload U on P.upload_id = U.upload_id")
				->innerJoin("Options O on O.option_id = A.species")
				->leftJoin("OptionData OD on OD.option_id = A.species and OD.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD2 on OD2.option_id = A.age and OD2.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD3 on OD3.option_id = A.gender and OD3.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD4 on OD4.option_id = A.sure and OD4.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD5 on OD5.option_id = S.habitat_id and OD5.data_type = '" . $this->languageTag . "'")
				->where("A.species != 97")
				->where("A.person_id = " . $this->personId);
			
			error_log("query1 created: " . $query1->dump() );
		}
		
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv')))
			->values($query1);
		
		error_log("queryInsert created");
		
		$db->setQuery($queryInsert);
		
		error_log("About to execute");

		$db->execute();
		
		error_log("Execution complete");

	}
		
}

?>