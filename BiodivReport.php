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
	const PAGE_LENGTH = 10;
	
	// If a report has fewer rows then returned as JSON, otherwise a file is created and link sent.
	// This is also used as the max num of rows to write at a time to avoid memory limits
	const REPORT_FILE_THRESHOLD = 4000;
	
	private $projectId;
	private $projectName;
	
	// The option_id of this report type in the database
	private $reportType;
	private $reportTypeName;
	private $reportTypeText;
	
	private $reportId;
	private $personId;
	private $languageTag;
	
	private $totalRows;
	private $pageLength;
	
	private $filename;
	
	private $reportFilters;
	
	
	// Note that projectId can be null if this is a user report
	function __construct( $projectId, $reportType, $personId, $reportId = null )
	{
		$this->personId = $personId;
		$this->reportType = $reportType;
		
		$typeName = getOptionData($reportType, 'reporttype');
		$this->reportTypeName = $typeName[0];
		$this->reportTypeText = codes_getName( $reportType, 'reporttypetran');
		
		$reportFilters = getOptionData($reportType, 'reportfilters');
		$this->reportFilters = null;
		if ( $reportFilters ) {
			$this->reportFilters = array();
			$filterArray = json_decode ( $reportFilters[0] );
			foreach ( $filterArray as $filter ) {
				$this->reportFilters[$filter->id] = $filter->type;
			}
		}
		
		$this->projectId = $projectId;
		
		// This will be an enhancement
		//$allAdmin = myAdminProjects();
		//$allSubs = getSubProjectsById( $projectId );
		
		// Set language
		$langObject = JFactory::getLanguage();
		$this->languageTag = $langObject->getTag();
		
		$this->pageLength = BiodivReport::PAGE_LENGTH;
		
		$this->userIsAdmin = false;
		
		if ( $reportId == null ) {
			
			//error_log ("New report, creating Report row");
			
			$t=time();
			$dateStr = date("Ymd_His",$t);
			
			if ( $this->projectId ) {
				
				$myAdminProjects = myAdminProjects();
				
				if ( array_key_exists($this->projectId, $myAdminProjects) ) {
					$this->userIsAdmin = true;
				}
				
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
			
			//error_log ( "Report filename = " . $this->filename );
			
			$reportFields = new stdClass();
			$reportFields->person_id = $this->personId;
			$reportFields->project_id = $this->projectId;
			$reportFields->report_type = $this->reportType;
			$reportFields->filename = $this->filename;
			
			$this->reportId = codes_insertObject($reportFields, 'report');
			
			//error_log ("New report, id = " . $this->reportId . ", generating Report row data for type " . $this->reportTypeName);
			
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
				case "USERUPLOADAUDIO":
					$this->generateUserUploadDataAudio ();
					break;
				// Classifications by user of user sequences
				case "USERSPECIES":
					$this->generateUserAnimal ();
					break;
				case "USERSPECIESAUDIO":
					$this->generateUserAnimalAudio ();
					break;
				// Classifications by others of user sequences
				case "USERSPECIESOTHERS":
					$this->generateUserAnimalOthers ();
					break;
				case "USERSPECIESOTHERSAUDIO":
					$this->generateUserAnimalOthersAudio ();
					break;
				// This user's sequences which have no classification
				case "USERNOSPECIES":
					$this->generateUserNoAnimal ();
					break;
				case "USERNOSPECIESAUDIO":
					$this->generateUserNoAnimalAudio ();
					break;
				// All classifications by this user, including sequences uploaded by others
				case "USERALLSPECIES":
					$this->generateUserAnimalAll ();
					break;
				case "USERALLSPECIESAUDIO":
					$this->generateUserAnimalAllAudio ();
					break;
				case "EFFORT":
					$this->generateEffortData ();
					break;
				case "ALLEFFORT":
					$this->generateAllEffortData ();
					break;
				case "RESOURCE":
					$this->generateResourceData ();
					break;
				case "ACTIVITY":
					$this->generateActivityData ();
					break;
				case "BADGECOMPLETE":
					$this->generateLevelActivityData ();
					break;
				case "NHMPSUBSCRIBE":
					$this->generateNHMPSubscribeData ();
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
	
	
	
	public static function getFilterValues ( $type ) {
		
		if ( $type == "module" ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
		
			$query = $db->getQuery(true)
			->select("module_id as id, name as name from Module M" )
			->order("seq");
		
			$db->setQuery($query);
		
			$filterValues = $db->loadAssocList("id", "name");
			
			$filterValues = array(JText::_("COM_BIODIV_BIODIVREPORT_ALL")) + $filterValues;
			
			return $filterValues;
		}
		if ( $type == "school" ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
		
			$query = $db->getQuery(true)
			->select("school_id as id, name as name from School S" )
			->order("name");
		
			$db->setQuery($query);
		
			$filterValues = $db->loadAssocList("id", "name");
			
			$filterValues = array(JText::_("COM_BIODIV_BIODIVREPORT_ALL")) + $filterValues;
			
			return $filterValues;
		}
		if ( $type == "role" ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
		
			$query = $db->getQuery(true)
			->select("role_id as id, display_text as name from Role R" )
			->order("name");
		
			$db->setQuery($query);
		
			$filterValues = $db->loadAssocList("id", "name");
			
			$filterValues = array(JText::_("COM_BIODIV_BIODIVREPORT_ALL")) + $filterValues;
			
			return $filterValues;
		}
		if ( $type == "trappingproject" ) {
			
			$filterValues = myTrappingProjects();
			
			$filterValues = array(JText::_("COM_BIODIV_BIODIVREPORT_ALL")) + $filterValues;
			
			return $filterValues;
		}
		if ( $type == "spottingproject" ) {
			
			$filterValues = mySpottingProjects();
		
			$filterValues = array(JText::_("COM_BIODIV_BIODIVREPORT_ALL")) + $filterValues;
			
			return $filterValues;
		}
		
		if ( $type == "level" ) {
		
			$filterValues = array(1=>"Bronze", 2=>"Silver", 3=>"Gold");
		
			$filterValues = array(JText::_("COM_BIODIV_BIODIVREPORT_ALL")) + $filterValues;
			
			return $filterValues;
		}
	}
	
	
	public static function getSelectedValue ( $type, $filter ) {
		
		$selectedValue = 0;
		
		if ( $type == "module" ) {
			
			$filterObject = json_decode ( $filter );
			
			if ( property_exists($filterObject, "module") ) {
				$selectedValue = $filterObject->module;
			}

		}
		else if ( $type == "school" ) {
			
			$filterObject = json_decode ( $filter );
			
			if ( property_exists($filterObject, "school") ) {
				$selectedValue = $filterObject->school;
			}

		}
		else if ( $type == "role" ) {
			
			$filterObject = json_decode ( $filter );
			
			if ( property_exists($filterObject, "role") ) {
				$selectedValue = $filterObject->role;
			}

		}
		else if ( $type == "trappingproject" ) {
			
			$filterObject = json_decode ( $filter );
			
			if ( $filterObject && property_exists($filterObject, "trappingproject") ) {
				$selectedValue = $filterObject->trappingproject;
			}

		}
		else if ( $type == "spottingproject" ) {
			
			$filterObject = json_decode ( $filter );
			
			if ( $filterObject && property_exists($filterObject, "spottingproject") ) {
				$selectedValue = $filterObject->spottingproject;
			}

		}
		
		return $selectedValue;
	}
	
	
	// List of the available project reports
	public static function listReports () {
		
		$features = array("restriction"=>"seq > 0");
		$reports = codes_getList('reporttypetran', $features);
		
		return $reports;
	}
	
	// List of the available opt-in project reports for the given project
	public static function listOptInReports ( $projectId ) {
		
		$db = JDatabase::getInstance(dbOptions());
		
		// Set up the select query for the report
		$query = $db->getQuery(true)
			->select( "O.option_id, O.option_name")
			->from("Options O")
			->innerJoin("ProjectOptions PO on O.option_id = PO.option_id and O.struc='optinreport'" ) 
			->where("PO.project_id = ".$projectId)
			->order("O.seq");
			
		$db->setQuery($query);
		$reports = $db->loadRowList();
	
		// Translate if necessary
		$langObject = JFactory::getLanguage();
		$languageTag = $langObject->getTag();
		if ( $languageTag != 'en-GB' ) {
			foreach ( $reports as $rpt ) {
				$nameTran = codes_getOptionTranslation($rpt[0]);
				$rpt[1] = $nameTran;
			}
		}
		
		return $reports;
	}
	
	
	
	// List of the available reports
	public static function listUserReports () {
		
		$reports = codes_getList('userreporttypetran');
		
		return $reports;
	}
	
	
	// List of the available school reports
	public static function listSchoolReports () {
		
		$reports = codes_getList('schoolreporttypetran');
		
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
	
	
	public function getReportTypeText() {
		return $this->reportTypeText;
	}
	
	
	
	public function getReportFilters () {
		return $this->reportFilters;
	}
	
	
	
	public function getFilename() {
		return $this->filename;
	}
	
	
	
	private function getFilterClause( $filter ) {
		
		$filterClause = null;
		if ( $filter != null ) {
			$db = JDatabase::getInstance(dbOptions());
			switch ( $this->reportTypeName ) {
					case "ACTIVITY":
						
						$filterObject = json_decode($filter);
						
						if ( property_exists($filterObject, "module") and ($filterObject->module != 0 )) {
							if ( $filterClause != null ) {
								$filterClause .= " and ";
							}
							$filterClause .= "filter1 = " . $db->quote($filterObject->module);
							
						}
						if ( property_exists($filterObject, "school") and ($filterObject->school != 0 ) ) {
							if ( $filterClause != null ) {
								$filterClause .= " and ";
							}
							$filterClause .= "filter2 = " . $db->quote($filterObject->school);
						}
						if ( property_exists($filterObject, "role") and ($filterObject->role != 0 ) ) {
							if ( $filterClause != null ) {
								$filterClause .= " and ";
							}
							$filterClause .= "filter3 = " . $db->quote($filterObject->role);
						}
						if ( $filterClause == null ) {
							$filterClause = "1";
						}
						break;
						
					case "BADGECOMPLETE":
						$filterObject = json_decode($filter);
						
						if ( property_exists($filterObject, "level") and ($filterObject->level != 0 )) {
							if ( $filterClause != null ) {
								$filterClause .= " and ";
							}
							$filterClause .= "filter1 = " . $db->quote($filterObject->level);
							
						}
						if ( property_exists($filterObject, "school") and ($filterObject->school != 0 ) ) {
							if ( $filterClause != null ) {
								$filterClause .= " and ";
							}
							$filterClause .= "filter2 = " . $db->quote($filterObject->school);
						}
						if ( property_exists($filterObject, "role") and ($filterObject->role != 0 ) ) {
							if ( $filterClause != null ) {
								$filterClause .= " and ";
							}
							$filterClause .= "filter3 = " . $db->quote($filterObject->role);
						}
						if ( $filterClause == null ) {
							$filterClause = "1";
						}
						break;
						
						
					case "USERSEQUENCE":
					case "USERUPLOAD":
					case "USERUPLOADAUDIO":
					case "USERSPECIES":
					case "USERSPECIESAUDIO":
					case "USERSPECIESOTHERS":
					case "USERSPECIESOTHERSAUDIO":
					case "USERNOSPECIES":
					case "USERNOSPECIESAUDIO":
					case "USERALLSPECIES":
					case "USERALLSPECIESAUDIO":
						
						$filterObject = json_decode($filter);
						
						if ( property_exists($filterObject, "trappingproject") and ($filterObject->trappingproject != 0 )) {
							if ( $filterClause != null ) {
								$filterClause .= " and ";
							}
							$filterClause .= "filter1 = " . $db->quote($filterObject->trappingproject);
							
						}
						else if ( property_exists($filterObject, "spottingproject") and ($filterObject->spottingproject != 0 )) {
							if ( $filterClause != null ) {
								$filterClause .= " and ";
							}
							$filterClause .= "filter1 = " . $db->quote($filterObject->spottingproject);
							
						}
						
						break;
						
						
					default:
						
						$filterClause = "1";
						break;
			}
		}
		else {
			$filterClause = "1";
		}
		return $filterClause;
	}
	
	public function rows ( $pageNum, $pageLength = null, $filter = null ) {
		
		if ( $pageLength != null ) $this->pageLength = $pageLength;
		
		$filterClause = null;
		if ( $filter ) {
			$filterClause = $this->getFilterClause($filter);
		}
		
		$db = JDatabase::getInstance(dbOptions());
		
		$query = $db->getQuery(true);
		$query->select("row_csv")
			->from("ReportRows RR")
			->where("RR.report_id = " . $this->reportId);
		
		if ( $filterClause ) {
			$query->where($filterClause);
		}
			
		$start = ($pageNum) * $this->pageLength;
		
		$db->setQuery($query, $start, $this->pageLength);
		$rows = $db->loadColumn();
		
		return $rows;
	}
		
	
	
	public function headings() {
		
		//error_log ("BiodivReport::getHeadings called");
		
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
		
		$err_msg = print_r ( $headings, true );
		//error_log ( "Report headings: " . $err_msg );
		
		return $headings;
		
	}
	
	public function totalRows ( $filter = null ) {
		
		$db = JDatabase::getInstance(dbOptions());
		
		$filterClause = null;
		if ( $filter ) {
			$filterClause = $this->getFilterClause($filter);
		}
		
		if ( $this->totalRows == null ) {
			$query = $db->getQuery(true);
			$query->select("count(*)")
				->from("ReportRows RR")
				->where("RR.report_id = " . $this->reportId);
				
			if ( $filterClause ) {
				$query->where($filterClause);
			}
			
			$db->setQuery($query);
			$this->totalRows = $db->loadResult();
			
			//error_log ( "totalRows, numRows = " . $this->totalRows );
		}
		
		return $this->totalRows;
		
	}
	
	
	public function pageLength () {
		
		// For now this is 
		
		return $this->pageLength;
		
	}
	
	
	public function createDownloadFile ( $filter = null ) {
		
		//$filePath = "/person_".$this->personId."/project_".$this->projectId."/".$this->filename;
		$filePath = reportRoot()."/person_".$this->personId."/project_".$this->projectId;
		
		$tmpCsvFile = $filePath . "/tmp_" . $this->filename;
		$newCsvFile = $filePath . "/" . $this->filename;
		
		$filterClause = null;
		if ( $filter ) {
			$filterClause = $this->getFilterClause($filter);
		}
		
		
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
				
				if ( $filterClause ) {
					$query->where($filterClause);
				}
						
				//$db->setQuery($query);
				$db->setQuery($query, $i, BiodivReport::REPORT_FILE_THRESHOLD); // LIMIT query results to avoid memory limits
		
				$rows = $db->loadColumn();
				
				foreach ($rows as $fields) {
					
					fputcsv($tmpCsv, explode(',', $fields));
				}
				
			}
			fclose($tmpCsv);
			
			rename ( $tmpCsvFile, $newCsvFile );
			
			//error_log ( "Report file created" );
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
		
		//error_log(" remove rows select query created " . $query->dump());
		
		$db->setQuery($query);

		$reportIds = $db->loadColumn();
		
		//$err_msg = print_r ( $reportIds, true );
		//error_log ( "removePreviousRows, reportIds: " . $err_msg );
		
		// delete all ReportRows for previous reports.
		if ( count($reportIds) > 0 ) {
			
			//error_log ( "About to delete previous reports" );
			
			//$delQuery = $db->getQuery(true);
			
			$delQuery = $db->getQuery(true)
				->delete('ReportRows')
				->where('report_id in (' . implode(',', $reportIds) . ')' );

			//error_log ( "Got query" );
			
			$db->setQuery($delQuery);

			$result = $db->execute();
			
			//error_log ( "Previous reports deleted" );
			
		}
		
		//error_log("Delete complete");
		
	}
	private function generateSiteData () {
		
		//error_log ( "generateSiteData called" );
		
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
		
		//error_log("querySelect created");
			
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv')))
			->values($querySelect);
		
		//error_log("queryInsert created");
		
		$db->setQuery($queryInsert);
		
		//error_log("About to execute");

		$db->execute();
		
		//error_log("Execution complete");
					

	}
	
	
	private function generateUploadData () {
		
		//error_log ( "generateUploadData called" );
		
		// Delete any existing ReportRows data for this... person, project and report type. 
		$this->removePreviousRows();
		
		//error_log ( "About to get all project ids" );
		
		// Get any subprojects for this project id
		$projects = getProjects ( 'ADMIN', false, $this->projectId );
		
		$projectStr = implode(',', array_keys($projects));
		
		//error_log ( "Project ids string = " . $projectStr );
		
		$db = JDatabase::getInstance(dbOptions());
		$query1 = null;
		$query2 = null;
		
		// Will need to do this differently for non English language...
		if ( $this->languageTag == 'en-GB' ) {
		
			// We have a table for this report to improve performance, but only updated periodically - try getting direct to start with
			// Set up the select queries for the report
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', U.upload_id, U.person_id, U.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.camera_tz, U.is_dst, U.utc_offset, U.deployment_date, U.collection_date, U.timestamp, PSM.project_id, PR.project_prettyname) as report_csv")
				->from("Upload U")
				->innerJoin("Site S on U.site_id = S.site_id")
				->innerJoin("Options O5 on O5.option_id = S.habitat_id")
				->innerJoin("ProjectSiteMap PSM on U.site_id = PSM.site_id and U.timestamp >= PSM.start_time and U.timestamp <= PSM.end_time")
				->innerJoin("Project PR on PSM.project_id = PR.project_id")
				->where("PSM.project_id in (" . $projectStr . ")");
			
			//error_log("query1 created: " . $query1->dump() );
				
			$query2 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', U.upload_id, U.person_id, U.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.camera_tz, U.is_dst, U.utc_offset, U.deployment_date, U.collection_date, U.timestamp, PSM.project_id, PR.project_prettyname) as report_csv")
				->from("Upload U")
				->innerJoin("Site S on U.site_id = S.site_id")
				->innerJoin("Options O5 on O5.option_id = S.habitat_id")
				->innerJoin("ProjectSiteMap PSM on U.site_id = PSM.site_id and U.timestamp >= PSM.start_time and PSM.end_time is null")
				->innerJoin("Project PR on PSM.project_id = PR.project_id")
				->where("PSM.project_id in (" . $projectStr . ")");
		
			//error_log("query2 created: " . $query2->dump());
		}
		else {
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', U.upload_id, U.person_id, U.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(OD5.value, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.camera_tz, U.is_dst, U.utc_offset, U.deployment_date, U.collection_date, U.timestamp, PSM.project_id, PR.project_prettyname) as report_csv")
				->from("Upload U")
				->innerJoin("Site S on U.site_id = S.site_id")
				->leftJoin("OptionData OD5 on OD5.option_id = S.habitat_id and OD5.data_type = '" . $this->languageTag . "'")
				->innerJoin("ProjectSiteMap PSM on U.site_id = PSM.site_id and U.timestamp >= PSM.start_time and U.timestamp <= PSM.end_time")
				->innerJoin("Project PR on PSM.project_id = PR.project_id")
				->where("PSM.project_id in (" . $projectStr . ")");
			
			//error_log("query1 created: " . $query1->dump() );
				
			$query2 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', U.upload_id, U.person_id, U.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(OD5.value, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.camera_tz, U.is_dst, U.utc_offset, U.deployment_date, U.collection_date, U.timestamp, PSM.project_id, PR.project_prettyname) as report_csv")
				->from("Upload U")
				->innerJoin("Site S on U.site_id = S.site_id")
				->leftJoin("OptionData OD5 on OD5.option_id = S.habitat_id and OD5.data_type = '" . $this->languageTag . "'")
				->innerJoin("ProjectSiteMap PSM on U.site_id = PSM.site_id and U.timestamp >= PSM.start_time and PSM.end_time is null")
				->innerJoin("Project PR on PSM.project_id = PR.project_id")
				->where("PSM.project_id in (" . $projectStr . ")");
		
			//error_log("query2 created: " . $query2->dump());
		}
		
		$unionQuery = $db->getQuery(true)
             ->select('*')
             ->from('(' . $query1->union($query2) . ') a');
			 
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv')))
			->values($unionQuery);
		
		//error_log("queryInsert created");
		
		$db->setQuery($queryInsert);
		
		//error_log("About to execute");

		$db->execute();
		
		//error_log("Execution complete");

	}
	
	
	// No deployment and colection time for audio uploads
	private function generateUploadDataAudio () {
		
		//error_log ( "generateUploadDataAudio called" );
		
		// Delete any existing ReportRows data for this... person, project and report type. 
		$this->removePreviousRows();
		
		// Get any subprojects for this project id
		$projects = getProjects ( 'ADMIN', false, $this->projectId );
		
		$projectStr = implode(',', array_keys($projects));
		
		
		$db = JDatabase::getInstance(dbOptions());
		$query1 = null;
		$query2 = null;
		
		// Will need to do this differently for non English language...
		if ( $this->languageTag == 'en-GB' ) {
		
			// We have a table for this report to improve performance, but only updated periodically - try getting direct to start with
			// Set up the select queries for the report
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', U.upload_id, U.person_id, U.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.camera_tz, U.is_dst, U.utc_offset, U.timestamp, PSM.project_id, PR.project_prettyname) as report_csv")
				->from("Upload U")
				->innerJoin("Site S on U.site_id = S.site_id")
				->innerJoin("Options O5 on O5.option_id = S.habitat_id")
				->innerJoin("ProjectSiteMap PSM on U.site_id = PSM.site_id and U.timestamp >= PSM.start_time and U.timestamp <= PSM.end_time")
				->innerJoin("Project PR on PSM.project_id = PR.project_id")
				->where("PSM.project_id in (" . $projectStr . ")");
			
			//error_log("query1 created: " . $query1->dump() );
				
			$query2 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', U.upload_id, U.person_id, U.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.camera_tz, U.is_dst, U.utc_offset, U.timestamp, PSM.project_id, PR.project_prettyname) as report_csv")
				->from("Upload U")
				->innerJoin("Site S on U.site_id = S.site_id")
				->innerJoin("Options O5 on O5.option_id = S.habitat_id")
				->innerJoin("ProjectSiteMap PSM on U.site_id = PSM.site_id and U.timestamp >= PSM.start_time and PSM.end_time is null")
				->innerJoin("Project PR on PSM.project_id = PR.project_id")
				->where("PSM.project_id in (" . $projectStr . ")");
		
			//error_log("query2 created: " . $query2->dump());
		}
		else {
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', U.upload_id, U.person_id, U.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(OD5.value, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.camera_tz, U.is_dst, U.utc_offset, U.timestamp, PSM.project_id, PR.project_prettyname) as report_csv")
				->from("Upload U")
				->innerJoin("Site S on U.site_id = S.site_id")
				->leftJoin("OptionData OD5 on OD5.option_id = S.habitat_id and OD5.data_type = '" . $this->languageTag . "'")
				->innerJoin("ProjectSiteMap PSM on U.site_id = PSM.site_id and U.timestamp >= PSM.start_time and U.timestamp <= PSM.end_time")
				->innerJoin("Project PR on PSM.project_id = PR.project_id")
				->where("PSM.project_id in (" . $projectStr . ")");
			
			//error_log("query1 created: " . $query1->dump() );
				
			$query2 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', U.upload_id, U.person_id, U.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(OD5.value, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.camera_tz, U.is_dst, U.utc_offset, U.timestamp, PSM.project_id, PR.project_prettyname) as report_csv")
				->from("Upload U")
				->innerJoin("Site S on U.site_id = S.site_id")
				->leftJoin("OptionData OD5 on OD5.option_id = S.habitat_id and OD5.data_type = '" . $this->languageTag . "'")
				->innerJoin("ProjectSiteMap PSM on U.site_id = PSM.site_id and U.timestamp >= PSM.start_time and PSM.end_time is null")
				->innerJoin("Project PR on PSM.project_id = PR.project_id")
				->where("PSM.project_id in (" . $projectStr . ")");
		
			//error_log("query2 created: " . $query2->dump());
		}
		
		$unionQuery = $db->getQuery(true)
             ->select('*')
             ->from('(' . $query1->union($query2) . ') a');
			 
		//error_log("generateUploadDataAudio unionQuery created: " . $unionQuery->dump());
			 
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv')))
			->values($unionQuery);
		
		//error_log("queryInsert created");
		
		$db->setQuery($queryInsert);
		
		//error_log("About to execute");

		$db->execute();
		
		//error_log("Execution complete");

	}
	
	
		
	private function generateAnimalData () {
		
		// Delete any existing data 
		$this->removePreviousRows();
		
		// Get any subprojects for this project id
		//$projectIds = getThisAndAllSubs ( $this->projectId );
		$projects = getProjects ( 'ADMIN', false, $this->projectId );
		
		$projectStr = implode(',', array_keys($projects));
		
		
		$db = JDatabase::getInstance(dbOptions());
		$query1 = null;
		$query2 = null;
		
		// Will need to do this differently for non English language...
		if ( $this->languageTag == 'en-GB' ) {
		
			// We have a table for this report to improve performance, but only updated periodically - try getting direct to start with
			// Set up the select queries for the report
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), REPLACE(O.option_name, ',', ' -'), REPLACE(S.site_name, ',', ' '), P.upload_filename, P.taken, SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), P.site_id, A.person_id, A.animal_id, A.timestamp, IFNULL(O2.option_name, 'null'), IFNULL(O3.option_name, 'null'), IFNULL(A.number, 'null'), REPLACE(IFNULL(A.notes, 'null'), ',', ' -'), IFNULL(O4.option_name, 'null'), P.photo_id, P2.sequence_num, P2.upload_filename, P2.taken, U.deployment_date, U.collection_date, PSM.project_id, PR.project_prettyname) as report_csv")
				->from("Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id and P.sequence_num = 1 and P.status in (1,2)")
				->innerJoin("PhotoSequence PS on P.photo_id = PS.start_photo_id")
				->innerJoin("Photo P2 on PS.end_photo_id = P2.photo_id")
				->innerJoin("Site S on P.site_id = S.site_id")
				->innerJoin("Upload U on P.upload_id = U.upload_id")
				->innerJoin("Options O on O.option_id = A.species")
				->leftJoin("Options O2 on O2.option_id = A.age")
				->leftJoin("Options O3 on O3.option_id = A.gender")
				->leftJoin("Options O4 on O4.option_id = A.sure")
				->leftJoin("Options O5 on O5.option_id = S.habitat_id")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and (PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)")
				->innerJoin("Project PR on PSM.project_id = PR.project_id")
				->where("A.species != 97")
				->where("PSM.project_id in (" . $projectStr . ")")
				->order("S.site_name, P.taken, A.timestamp");
			
			//error_log("query1 created: " . $query1->dump() );
		}
		else {
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), REPLACE(IFNULL(OD.value, O.option_name), ',', ' -'), REPLACE(S.site_name, ',', ' '), P.upload_filename, P.taken, SUBSTRING_INDEX(IFNULL(OD5.value,  'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), P.site_id, A.person_id, A.animal_id, A.timestamp, IFNULL(OD2.value, 'null'), IFNULL(OD3.value, 'null'), IFNULL(A.number, 'null'), REPLACE(IFNULL(A.notes, 'null'), ',', ' -'), IFNULL(OD4.value, 'null'), P.photo_id, P2.sequence_num, P2.upload_filename, P2.taken, U.deployment_date, U.collection_date, PSM.project_id, PR.project_prettyname) as report_csv")
				->from("Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id and P.sequence_num = 1 and P.status in (1,2)")
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
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and (PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)")
				->innerJoin("Project PR on PSM.project_id = PR.project_id")
				->where("A.species != 97")
				->where("PSM.project_id in (" . $projectStr . ")")
				->order("S.site_name, P.taken, A.timestamp");
			
			//error_log("query1 created: " . $query1->dump() );
				
		}
		
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv')))
			->values($query1);
		
		//error_log("queryInsert created: " . $queryInsert->dump());
		
		$db->setQuery($queryInsert);
		
		//error_log("About to execute");

		$db->execute();
		
		//error_log("Execution complete");
					
		
		
	}
		
		
private function generateAnimalDataAudio () {
		
		// Delete any existing data 
		$this->removePreviousRows();
		
		// Get any subprojects for this project id
		//$projectIds = getThisAndAllSubs ( $this->projectId );
		$projects = getProjects ( 'ADMIN', false, $this->projectId );
		
		$projectStr = implode(',', array_keys($projects));
		
		
		$db = JDatabase::getInstance(dbOptions());
		$query1 = null;
		$query2 = null;
		
		// Will need to do this differently for non English language...
		if ( $this->languageTag == 'en-GB' ) {
		
			// We have a table for this report to improve performance, but only updated periodically - try getting direct to start with
			// Set up the select queries for the report
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), REPLACE(O.option_name, ',', ' -'), REPLACE(S.site_name, ',', ' '), P.upload_filename, P.taken, REPLACE(IFNULL(A.notes, 'null'), ',', ' -'), SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), P.site_id, A.person_id, A.animal_id, A.timestamp, IFNULL(O2.option_name, 'null'), P.photo_id, PSM.project_id, PR.project_prettyname) as report_csv")
				->from("Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id and P.sequence_num = 1 and P.status in (1,2)")
				->innerJoin("Site S on P.site_id = S.site_id")
				->innerJoin("Options O on O.option_id = A.species")
				->leftJoin("Options O2 on O2.option_id = A.sure")
				->leftJoin("Options O5 on O5.option_id = S.habitat_id")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and (PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)")
				->innerJoin("Project PR on PSM.project_id = PR.project_id")
				->where("A.species != 97")
				->where("PSM.project_id in (" . $projectStr . ")")
				->order("S.site_name, P.taken, A.timestamp");
			
			//error_log("query1 created: " . $query1->dump() );
				
			
		}
		else {
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), REPLACE(IFNULL(OD.value, O.option_name), ',', ' -'), REPLACE(S.site_name, ',', ' '), P.upload_filename, P.taken, REPLACE(IFNULL(A.notes, 'null'), ',', ' -'), SUBSTRING_INDEX(IFNULL(OD5.value, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), P.site_id, A.person_id, A.animal_id, A.timestamp, IFNULL(OD2.value, 'null'), P.photo_id, PSM.project_id, PR.project_prettyname) as report_csv")
				->from("Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id and P.sequence_num = 1 and P.status in (1,2)")
				->innerJoin("Site S on P.site_id = S.site_id")
				->innerJoin("Options O on O.option_id = A.species")
				->leftJoin("OptionData OD on OD.option_id = A.species and OD.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD2 on OD2.option_id = A.sure and OD2.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD5 on OD5.option_id = S.habitat_id and OD5.data_type = '" . $this->languageTag . "'")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and (PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)")
				->innerJoin("Project PR on PSM.project_id = PR.project_id")
				->where("A.species != 97")
				->where("PSM.project_id in (" . $projectStr . ")")
				->order("S.site_name, P.taken, A.timestamp");
			
			//error_log("query1 created: " . $query1->dump() );
				
			
		}
		
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv')))
			->values($query1);
		
		//error_log("queryInsert created: " . $queryInsert->dump());
		
		$db->setQuery($queryInsert);
		
		//error_log("About to execute");

		$db->execute();
		
		//error_log("Execution complete");
					
	}
	
	
	
	private function generateNoAnimalData () {
		
		// Delete any existing data 
		$this->removePreviousRows();
		
		// Get any subprojects for this project id
		//$projectIds = getThisAndAllSubs ( $this->projectId );
		$projects = getProjects ( 'ADMIN', false, $this->projectId );
		
		$projectStr = implode(',', array_keys($projects));
		
		
		$db = JDatabase::getInstance(dbOptions());
		$query1 = null;
		$query2 = null;
		
		// 
		if ( $this->languageTag == 'en-GB' ) {
		
			// We have a table for this report to improve performance, but only updated periodically - try getting direct to start with
			// Set up the select queries for the report
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), 'null', REPLACE(S.site_name, ',', ' '), P.upload_filename, P.taken, SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), P.site_id, 'null', 'null', 'null', 'null', 'null', 'null', 'null', 'null', P.photo_id, P2.sequence_num, P2.upload_filename, P2.taken, U.deployment_date, U.collection_date, PSM.project_id, PR.project_prettyname ) as report_csv")
				->from("Photo P")
				->leftJoin("Animal A on A.photo_id = P.photo_id and P.sequence_num = 1 and P.status in (1,2)")
				->innerJoin("PhotoSequence PS on P.photo_id = PS.start_photo_id")
				->innerJoin("Photo P2 on PS.end_photo_id = P2.photo_id")
				->innerJoin("Site S on P.site_id = S.site_id")
				->innerJoin("Upload U on P.upload_id = U.upload_id")
				->leftJoin("Options O5 on O5.option_id = S.habitat_id")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and (PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)")
				->innerJoin("Project PR on PSM.project_id = PR.project_id")
				->where("A.photo_id is NULL")
				->where("PSM.project_id in (" . $projectStr . ")")
				->order("S.site_name, P.taken");
			
			//error_log("query1 created: " . $query1->dump() );
				
			
		}
		else {
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), 'null', REPLACE(S.site_name, ',', ' '), P.upload_filename, P.taken, SUBSTRING_INDEX(IFNULL(OD5.value, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), P.site_id, 'null', 'null', 'null', 'null', 'null', 'null', 'null', 'null', P.photo_id, P2.sequence_num, P2.upload_filename, P2.taken, U.deployment_date, U.collection_date, PSM.project_id, PR.project_prettyname) as report_csv")
				->from("Photo P")
				->leftJoin("Animal A on A.photo_id = P.photo_id and P.sequence_num = 1 and P.status in (1,2)")
				->innerJoin("PhotoSequence PS on P.photo_id = PS.start_photo_id")
				->innerJoin("Photo P2 on PS.end_photo_id = P2.photo_id")
				->innerJoin("Site S on P.site_id = S.site_id")
				->innerJoin("Upload U on P.upload_id = U.upload_id")
				->leftJoin("OptionData OD5 on OD5.option_id = S.habitat_id and OD5.data_type = '" . $this->languageTag . "'")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and (PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)")
				->innerJoin("Project PR on PSM.project_id = PR.project_id")
				->where("A.photo_id is NULL")
				->where("PSM.project_id in (" . $projectStr . ")")
				->order("S.site_name, P.taken");
			
			//error_log("query1 created: " . $query1->dump() );
				
			
		}
		
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv')))
			->values($query1);
		
		//error_log("queryInsert created: " . $queryInsert->dump());
		
		$db->setQuery($queryInsert);
		
		//error_log("About to execute");

		$db->execute();
		
		//error_log("Execution complete");
	}
	
	
	private function generateNoAnimalDataAudio () {
		
		// Delete any existing data 
		$this->removePreviousRows();
		
		// Get any subprojects for this project id
		//$projectIds = getThisAndAllSubs ( $this->projectId );
		$projects = getProjects ( 'ADMIN', false, $this->projectId );
		
		$projectStr = implode(',', array_keys($projects));
		
		
		$db = JDatabase::getInstance(dbOptions());
		$query1 = null;
		$query2 = null;
		
		// 
		if ( $this->languageTag == 'en-GB' ) {
		
			// We have a table for this report to improve performance, but only updated periodically - try getting direct to start with
			// Set up the select queries for the report
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), 'null', REPLACE(S.site_name, ',', ' '), P.upload_filename, P.taken, 'null', SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), P.site_id, 'null', 'null', 'null', 'null', P.photo_id, PSM.project_id, PR.project_prettyname) as report_csv")
				->from("Photo P")
				->leftJoin("Animal A on A.photo_id = P.photo_id and P.sequence_num = 1 and P.status in (1,2)")
				->innerJoin("Site S on P.site_id = S.site_id")
				->leftJoin("Options O5 on O5.option_id = S.habitat_id")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and (PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)")
				->innerJoin("Project PR on PSM.project_id = PR.project_id")
				->where("A.photo_id is NULL")
				->where("PSM.project_id in (" . $projectStr . ")")
				->order("S.site_name, P.taken");
			
			//error_log("query1 created: " . $query1->dump() );
				
			
		}
		else {
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), 'null', REPLACE(S.site_name, ',', ' '), P.upload_filename, P.taken, 'null', SUBSTRING_INDEX(IFNULL(OD5.value, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), P.site_id, 'null', 'null', 'null', 'null', P.photo_id, PSM.project_id, PR.project_prettyname) as report_csv")
				->from("Photo P")
				->leftJoin("Animal A on A.photo_id = P.photo_id and P.sequence_num = 1 and P.status in (1,2)")
				->innerJoin("Site S on P.site_id = S.site_id")
				->leftJoin("OptionData OD5 on OD5.option_id = S.habitat_id and OD5.data_type = '" . $this->languageTag . "'")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and (PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)")
				->innerJoin("Project PR on PSM.project_id = PR.project_id")
				->where("A.photo_id is NULL")
				->where("PSM.project_id in (" . $projectStr . ")")
				->order("S.site_name, P.taken");
			
			//error_log("query1 created: " . $query1->dump() );
				
			
		}
		
			 
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv')))
			->values($query1);
		
		//error_log("queryInsert created: " . $queryInsert->dump());
		
		$db->setQuery($queryInsert);
		
		//error_log("About to execute");

		$db->execute();
		
		//error_log("Execution complete");
	}
	
	
	
	private function generateSequenceData () {
		
		// Delete any existing data 
		$this->removePreviousRows();
		
		// Get any subprojects for this project id
		//$projectIds = getThisAndAllSubs ( $this->projectId );
		
		$projects = getProjects ( 'ADMIN', false, $this->projectId );
		
		$projectStr = implode(',', array_keys($projects));
		
		$db = JDatabase::getInstance(dbOptions());
		$query1 = null;
		$query2 = null;
		
		
		$query1 = $db->getQuery(true);
		$query1->select("P.site_id as site_id, REPLACE(S.site_name, ',', ' ') as site_name, P.sequence_id as sequence_id, A.animal_id as animal_id, IF(A.animal_id>0, 1, 0) as animal_num, PR.project_id as project_id, PR.project_prettyname as project_name from Photo P")
			->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id")
			->innerJoin("Site S on S.site_id = P.site_id")
			->leftJoin("Animal A on P.photo_id = A.photo_id and A.species != 97 and P.status in (1,2)")
			->innerJoin("Project PR on PSM.project_id = PR.project_id")
			->where("P.sequence_id > 0")
			->where("PSM.project_id in (" . $projectStr . ")")
			->where("((P.photo_id >= PSM.start_photo_id and PSM.end_photo_id is NULL) or (P.photo_id >= PSM.start_photo_id and P.photo_id <= PSM.end_photo_id))"  );
			
		$query2 = $db->getQuery(true)
             ->select( "a.site_id, a.site_name, a.sequence_id, IF(sum(a.animal_num)>0, 1, 0) as num_class, a.project_id, a.project_name" )
			 ->from('(' . $query1 . ') a')
			 ->group('a.site_id, a.site_name, a.sequence_id, a.project_id');
			 
		$query3 = $db->getQuery(true)
             ->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', b.site_id, b.site_name, count(*), sum(b.num_class), b.project_id, b.project_name) as report_csv")
			 ->from('(' . $query2 . ') b')
			 ->group('b.site_id, b.site_name, b.project_id');
			 
		//error_log("generateSequenceData query created: " . $query3->dump() );
		
		
		
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv')))
			->values($query3);
		
		//error_log("queryInsert created");
		
		$db->setQuery($queryInsert);
		
		//error_log("About to execute");

		$db->execute();
		
		//error_log("Execution complete");

	}
	
	private function generateUserUploadData () {
		
		//error_log ( "generateUploadData called" );
		
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
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', P.project_prettyname, U.upload_id, U.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.camera_tz, U.is_dst, U.utc_offset, U.deployment_date, U.collection_date, U.timestamp) as report_csv, P.project_id as filter1")
				->from("Upload U")
				->innerJoin("Site S on U.site_id = S.site_id and S.person_id = " . $this->personId)
				->innerJoin("Options O5 on O5.option_id = S.habitat_id")
				->innerJoin("ProjectSiteMap PSM on U.site_id = PSM.site_id and U.timestamp >= PSM.start_time and (U.timestamp <= PSM.end_time or PSM.end_time is NULL)")
				->innerJoin("Project P on PSM.project_id = P.project_id");
			
			//error_log("query1 created: " . $query1->dump() );
				
		}
		else {
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', P.project_prettyname, U.upload_id, U.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(OD5.value, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.camera_tz, U.is_dst, U.utc_offset, U.deployment_date, U.collection_date, U.timestamp) as report_csv, P.project_id as filter1")
				->from("Upload U")
				->innerJoin("Site S on U.site_id = S.site_id and S.person_id = " . $this->personId)
				->leftJoin("OptionData OD5 on OD5.option_id = S.habitat_id and OD5.data_type = '" . $this->languageTag . "'")
				->innerJoin("ProjectSiteMap PSM on U.site_id = PSM.site_id and U.timestamp >= PSM.start_time and (U.timestamp <= PSM.end_time or PSM.end_time is NULL)")
				->innerJoin("Project P on PSM.project_id = P.project_id");
			
			//error_log("query1 created: " . $query1->dump() );
				
		}
		
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv','filter1')))
			->values($query1);
		
		//error_log("queryInsert created");
		
		$db->setQuery($queryInsert);
		
		//error_log("About to execute");

		$db->execute();
		
		//error_log("Execution complete");

	

	}
	
	
	private function generateUserUploadDataAudio () {
		
		//error_log ( "generateUploadData called" );
		
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
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', P.project_prettyname, U.upload_id, U.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.camera_tz, U.is_dst, U.utc_offset, U.timestamp) as report_csv, P.project_id as filter1")
				->from("Upload U")
				->innerJoin("Site S on U.site_id = S.site_id and S.person_id = " . $this->personId)
				->innerJoin("Options O5 on O5.option_id = S.habitat_id")
				->innerJoin("ProjectSiteMap PSM on U.site_id = PSM.site_id and U.timestamp >= PSM.start_time and (U.timestamp <= PSM.end_time or PSM.end_time is NULL)")
				->innerJoin("Project P on PSM.project_id = P.project_id");
				
			
			//error_log("query1 created: " . $query1->dump() );
				
		}
		else {
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', P.project_prettyname, U.upload_id, U.site_id, REPLACE(S.site_name, ',', ' '), SUBSTRING_INDEX(IFNULL(OD5.value, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), U.camera_tz, U.is_dst, U.utc_offset, U.timestamp) as report_csv, P.project_id as filter1")
				->from("Upload U")
				->innerJoin("Site S on U.site_id = S.site_id and S.person_id = " . $this->personId)
				->leftJoin("OptionData OD5 on OD5.option_id = S.habitat_id and OD5.data_type = '" . $this->languageTag . "'")
				->innerJoin("ProjectSiteMap PSM on U.site_id = PSM.site_id and U.timestamp >= PSM.start_time and (U.timestamp <= PSM.end_time or PSM.end_time is NULL)")
				->innerJoin("Project P on PSM.project_id = P.project_id");
			
			//error_log("query1 created: " . $query1->dump() );
				
		}
		
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv', 'filter1')))
			->values($query1);
		
		//error_log("queryInsert created");
		
		$db->setQuery($queryInsert);
		
		//error_log("About to execute");

		$db->execute();
		
		//error_log("Execution complete");

	

	}
	
	
	
private function generateUserSequenceData () {
		
		// Delete any existing data 
		$this->removePreviousRows();
		
		$db = JDatabase::getInstance(dbOptions());
		$query1 = null;
		$query2 = null;
		
		$query1 = $db->getQuery(true);
		$query1->select("PR.project_id, PR.project_prettyname, P.site_id as site_id, REPLACE(S.site_name, ',', ' ') as site_name, P.sequence_id as sequence_id, A.animal_id as animal_id, IF(A.animal_id>0, 1, 0) as animal_num from Photo P")
			->innerJoin("Site S on S.site_id = P.site_id and S.person_id = " . $this->personId )
			->leftJoin("Animal A on P.photo_id = A.photo_id and A.species != 97 and P.status in (1,2)")
			->where("P.sequence_id > 0")
			->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and (PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)")
			->innerJoin("Project PR on PSM.project_id = PR.project_id");
			
		//error_log("USERSEQUENCE query1 created: " . $query1->dump());
			
		$query2 = $db->getQuery(true)
             ->select( "a.project_id, a.project_prettyname, a.site_id, a.site_name, a.sequence_id, IF(sum(a.animal_num)>0, 1, 0) as num_class" )
			 ->from('(' . $query1 . ') a')
			 ->group('a.site_id, a.site_name, a.sequence_id');
			 
		//error_log("USERSEQUENCE query2 created: " . $query2->dump());
		
		$query3 = $db->getQuery(true)
             ->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', b.project_prettyname, b.site_id, b.site_name, count(*), sum(b.num_class)) as report_csv, b.project_id as filter1")
			 ->from('(' . $query2 . ') b')
			 ->group('b.site_id, b.site_name');
		
		//error_log("USERSEQUENCE query3 created: " . $query3->dump());
				
		//$db->setQuery($query3);
		
		//$results = $db->loadAssocList('site_id');
		
		//$err_msg = print_r ( $results, true );
		//error_log ( "BiodivReport::generateSequenceData, query results: " . $err_msg );
		
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv','filter1')))
			->values($query3);
		
		//error_log("queryInsert created");
		
		$db->setQuery($queryInsert);
		
		//error_log("About to execute");

		$db->execute();
		
		//error_log("Execution complete");

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
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), PR.project_prettyname, REPLACE(O.option_name, ',', ' -'), REPLACE(S.site_name, ',', ' '), P.upload_filename, P.taken, SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), P.site_id, A.animal_id, A.timestamp, IFNULL(O2.option_name, 'null'), IFNULL(O3.option_name, 'null'), IFNULL(A.number, 'null'), REPLACE(IFNULL(A.notes, 'null'), ',', ' -'), IFNULL(O4.option_name, 'null'), P.photo_id, P2.sequence_num, P2.upload_filename, P2.taken, U.deployment_date, U.collection_date) as report_csv, PR.project_id as filter1")
				->from("Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id and P.sequence_num = 1 and P.status in (1,2)")
				->innerJoin("PhotoSequence PS on P.photo_id = PS.start_photo_id")
				->innerJoin("Photo P2 on PS.end_photo_id = P2.photo_id")
				->innerJoin("Site S on P.site_id = S.site_id and S.person_id = " . $this->personId)
				->innerJoin("Upload U on P.upload_id = U.upload_id")
				->innerJoin("Options O on O.option_id = A.species")
				->leftJoin("Options O2 on O2.option_id = A.age")
				->leftJoin("Options O3 on O3.option_id = A.gender")
				->leftJoin("Options O4 on O4.option_id = A.sure")
				->leftJoin("Options O5 on O5.option_id = S.habitat_id")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and (PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)")
				->innerJoin("Project PR on PSM.project_id = PR.project_id")
				->where("A.species != 97")
				->where("A.person_id = " . $this->personId)
				->order("S.site_name, P.taken, A.timestamp");
			
			//error_log("query1 created: " . $query1->dump() );
		}
		else {
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), PR.project_prettyname, REPLACE(IFNULL(OD.value, O.option_name), ',', ' -'), REPLACE(S.site_name, ',', ' '), P.upload_filename, P.taken, SUBSTRING_INDEX(IFNULL(OD5.value, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), P.site_id, A.animal_id, A.timestamp, IFNULL(OD2.value, 'null'), IFNULL(OD3.value, 'null'), IFNULL(A.number, 'null'), REPLACE(IFNULL(A.notes, 'null'), ',', ' -'), IFNULL(OD4.value, 'null'), P.photo_id, P2.sequence_num, P2.upload_filename, P2.taken, U.deployment_date, U.collection_date) as report_csv, PR.project_id as filter1")
				->from("Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id and P.sequence_num = 1 and P.status in (1,2)")
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
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and (PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)")
				->innerJoin("Project PR on PSM.project_id = PR.project_id")
				->where("A.species != 97")
				->where("A.person_id = " . $this->personId)
				->order("S.site_name, P.taken, A.timestamp");
			
			//error_log("query1 created: " . $query1->dump() );
		}
		
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv','filter1')))
			->values($query1);
		
		//error_log("queryInsert created");
		
		$db->setQuery($queryInsert);
		
		//error_log("About to execute");

		$db->execute();
		
		//error_log("Execution complete");

	}
	
	private function generateUserAnimalAudio () {
		
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
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), PR.project_prettyname, REPLACE(O.option_name, ',', ' -'), REPLACE(S.site_name, ',', ' '), P.upload_filename, P.taken, REPLACE(IFNULL(A.notes, 'null'), ',', ' -'), SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), P.site_id, A.animal_id, A.timestamp, IFNULL(O4.option_name, 'null'), P.photo_id ) as report_csv, PR.project_id as filter1")
				->from("Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id and P.sequence_num = 1 and P.status in (1,2)")
				->innerJoin("Site S on P.site_id = S.site_id and S.person_id = " . $this->personId)
				->innerJoin("Options O on O.option_id = A.species")
				->leftJoin("Options O4 on O4.option_id = A.sure")
				->leftJoin("Options O5 on O5.option_id = S.habitat_id")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and (PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)")
				->innerJoin("Project PR on PSM.project_id = PR.project_id")
				->where("A.species != 97")
				->where("A.person_id = " . $this->personId)
				->order("S.site_name, P.taken, A.timestamp");
			
			//error_log("query1 created: " . $query1->dump() );
		}
		else {
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), PR.project_prettyname, REPLACE(IFNULL(OD.value, O.option_name), ',', ' -'), REPLACE(S.site_name, ',', ' '), P.upload_filename, P.taken, REPLACE(IFNULL(A.notes, 'null'), ',', ' -'), SUBSTRING_INDEX(IFNULL(OD5.value, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), P.site_id, A.animal_id, A.timestamp, IFNULL(OD4.value, 'null'), P.photo_id) as report_csv, PR.project_id as filter1")
				->from("Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id and P.sequence_num = 1 and P.status in (1,2)")
				->innerJoin("Site S on P.site_id = S.site_id and S.person_id = " . $this->personId)
				->innerJoin("Options O on O.option_id = A.species")
				->leftJoin("OptionData OD on OD.option_id = A.species and OD.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD4 on OD4.option_id = A.sure and OD4.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD5 on OD5.option_id = S.habitat_id and OD5.data_type = '" . $this->languageTag . "'")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and (PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)")
				->innerJoin("Project PR on PSM.project_id = PR.project_id")
				->where("A.species != 97")
				->where("A.person_id = " . $this->personId)
				->order("S.site_name, P.taken, A.timestamp");
			
			//error_log("query1 created: " . $query1->dump() );
		}
		
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv','filter1')))
			->values($query1);
		
		//error_log("queryInsert created");
		
		$db->setQuery($queryInsert);
		
		//error_log("About to execute");

		$db->execute();
		
		//error_log("Execution complete");

	}
		
	private function generateUserAnimalOthers () {
		
		// Delete any existing data 
		$this->removePreviousRows();
		
		// Choose a random integer to add to person_ids
		$addRand = rand(1,1000);
		
		$db = JDatabase::getInstance(dbOptions());
		$query1 = null;
		$query2 = null;
		
		// Will need to do this differently for non English language...
		if ( $this->languageTag == 'en-GB' ) {
		
			// We have a table for this report to improve performance, but only updated periodically - try getting direct to start with
			// Set up the select queries for the report
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), PR.project_prettyname, REPLACE(O.option_name, ',', ' -'), REPLACE(S.site_name, ',', ' '), P.upload_filename, P.taken, SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), P.site_id, A.person_id+".$addRand.", A.animal_id, A.timestamp, IFNULL(O2.option_name, 'null'), IFNULL(O3.option_name, 'null'), IFNULL(A.number, 'null'), REPLACE(IFNULL(A.notes, 'null'), ',', ' -'), IFNULL(O4.option_name, 'null'), P.photo_id, P2.sequence_num, P2.upload_filename, P2.taken, U.deployment_date, U.collection_date ) as report_csv, PR.project_id as filter1")
				->from("Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id and P.sequence_num = 1 and P.status in (1,2)")
				->innerJoin("PhotoSequence PS on P.photo_id = PS.start_photo_id")
				->innerJoin("Photo P2 on PS.end_photo_id = P2.photo_id")
				->innerJoin("Site S on P.site_id = S.site_id and S.person_id = " . $this->personId)
				->innerJoin("Upload U on P.upload_id = U.upload_id")
				->innerJoin("Options O on O.option_id = A.species")
				->leftJoin("Options O2 on O2.option_id = A.age")
				->leftJoin("Options O3 on O3.option_id = A.gender")
				->leftJoin("Options O4 on O4.option_id = A.sure")
				->leftJoin("Options O5 on O5.option_id = S.habitat_id")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and (PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)")
				->innerJoin("Project PR on PSM.project_id = PR.project_id")
				->where("A.species != 97")
				->where("A.person_id != " . $this->personId)
				->order("S.site_name, P.taken, A.timestamp");
			
			//error_log("query1 created: " . $query1->dump() );
		}
		else {
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), PR.project_prettyname, REPLACE(IFNULL(OD.value, O.option_name), ',', ' -'), REPLACE(S.site_name, ',', ' '), P.upload_filename, P.taken, SUBSTRING_INDEX(IFNULL(OD5.value, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), P.site_id, A.person_id+".$addRand.", A.animal_id, A.timestamp, IFNULL(OD2.value, 'null'), IFNULL(OD3.value, 'null'), IFNULL(A.number, 'null'), REPLACE(IFNULL(A.notes, 'null'), ',', ' -'), IFNULL(OD4.value, 'null'), P.photo_id, P2.sequence_num, P2.upload_filename, P2.taken, U.deployment_date, U.collection_date ) as report_csv, PR.project_id as filter1")
				->from("Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id and P.sequence_num = 1 and P.status in (1,2)")
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
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and (PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)")
				->innerJoin("Project PR on PSM.project_id = PR.project_id")
				->where("A.species != 97")
				->where("A.person_id != " . $this->personId)
				->order("S.site_name, P.taken, A.timestamp");
			
			//error_log("query1 created: " . $query1->dump() );
		}
		
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv','filter1')))
			->values($query1);
		
		//error_log("queryInsert created");
		
		$db->setQuery($queryInsert);
		
		//error_log("About to execute");

		$db->execute();
		
		//error_log("Execution complete");

	}
	
	private function generateUserAnimalOthersAudio () {
		
		// Delete any existing data 
		$this->removePreviousRows();
		
		// Choose a random integer to add to person_ids
		$addRand = rand(1,1000);
		
		$db = JDatabase::getInstance(dbOptions());
		$query1 = null;
		$query2 = null;
		
		// Will need to do this differently for non English language...
		if ( $this->languageTag == 'en-GB' ) {
		
			// We have a table for this report to improve performance, but only updated periodically - try getting direct to start with
			// Set up the select queries for the report
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), PR.project_prettyname, REPLACE(O.option_name, ',', ' -'), REPLACE(S.site_name, ',', ' '), P.upload_filename, P.taken, REPLACE(IFNULL(A.notes, 'null'), ',', ' -'), SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), P.site_id, A.person_id+".$addRand.", A.animal_id, A.timestamp, IFNULL(O4.option_name, 'null'), P.photo_id) as report_csv, PR.project_id as filter1")
				->from("Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id and P.sequence_num = 1 and P.status in (1,2)")
				->innerJoin("Site S on P.site_id = S.site_id and S.person_id = " . $this->personId)
				->innerJoin("Options O on O.option_id = A.species")
				->leftJoin("Options O4 on O4.option_id = A.sure")
				->leftJoin("Options O5 on O5.option_id = S.habitat_id")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and (PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)")
				->innerJoin("Project PR on PSM.project_id = PR.project_id")
				->where("A.species != 97")
				->where("A.person_id != " . $this->personId)
				->order("S.site_name, P.taken, A.timestamp");
			
			//error_log("query1 created: " . $query1->dump() );
		}
		else {
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), PR.project_prettyname, REPLACE(IFNULL(OD.value, O.option_name), ',', ' -'), REPLACE(S.site_name, ',', ' '), P.upload_filename, P.taken, REPLACE(IFNULL(A.notes, 'null'), ',', ' -'), SUBSTRING_INDEX(IFNULL(OD5.value, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), P.site_id, A.person_id+".$addRand.", A.animal_id, A.timestamp, IFNULL(OD4.value, 'null'), P.photo_id) as report_csv, PR.project_id as filter1")
				->from("Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id and P.sequence_num = 1 and P.status in (1,2)")
				->innerJoin("Site S on P.site_id = S.site_id and S.person_id = " . $this->personId)
				->innerJoin("Options O on O.option_id = A.species")
				->leftJoin("OptionData OD on OD.option_id = A.species and OD.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD4 on OD4.option_id = A.sure and OD4.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD5 on OD5.option_id = S.habitat_id and OD5.data_type = '" . $this->languageTag . "'")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and (PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)")
				->innerJoin("Project PR on PSM.project_id = PR.project_id")
				->where("A.species != 97")
				->where("A.person_id != " . $this->personId)
				->order("S.site_name, P.taken, A.timestamp");
			
			//error_log("query1 created: " . $query1->dump() );
		}
		
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv','filter1')))
			->values($query1);
		
		//error_log("queryInsert created");
		
		$db->setQuery($queryInsert);
		
		//error_log("About to execute");

		$db->execute();
		
		//error_log("Execution complete");

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
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), PR.project_prettyname, 'null', REPLACE(S.site_name, ',', ' '), P.upload_filename, P.taken, SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), P.site_id, 'null', 'null', 'null', 'null', 'null', 'null', 'null', P.photo_id, P2.sequence_num, P2.upload_filename, P2.taken, U.deployment_date, U.collection_date) as report_csv, PR.project_id as filter1")
				->from("Photo P")
				->leftJoin("Animal A on A.photo_id = P.photo_id and P.sequence_num = 1 and P.status in (1,2)")
				->innerJoin("PhotoSequence PS on P.photo_id = PS.start_photo_id")
				->innerJoin("Photo P2 on PS.end_photo_id = P2.photo_id")
				->innerJoin("Site S on P.site_id = S.site_id and S.person_id = " . $this->personId )
				->innerJoin("Upload U on P.upload_id = U.upload_id")
				->leftJoin("Options O5 on O5.option_id = S.habitat_id")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and (PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)")
				->innerJoin("Project PR on PSM.project_id = PR.project_id")
				->where("A.photo_id is NULL")
				->order("S.site_name, P.taken");
			
			//error_log("query1 created: " . $query1->dump() );
				
			
		}
		else {
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), PR.project_prettyname, 'null', REPLACE(S.site_name, ',', ' '), P.upload_filename, P.taken, SUBSTRING_INDEX(IFNULL(OD5.value, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), P.site_id, 'null', 'null', 'null', 'null', 'null', 'null', 'null', P.photo_id, P2.sequence_num, P2.upload_filename, P2.taken, U.deployment_date, U.collection_date) as report_csv, PR.project_id as filter1")
				->from("Photo P")
				->leftJoin("Animal A on A.photo_id = P.photo_id and P.sequence_num = 1 and P.status in (1,2)")
				->innerJoin("PhotoSequence PS on P.photo_id = PS.start_photo_id")
				->innerJoin("Photo P2 on PS.end_photo_id = P2.photo_id")
				->innerJoin("Site S on P.site_id = S.site_id and S.person_id = " . $this->personId)
				->innerJoin("Upload U on P.upload_id = U.upload_id")
				->leftJoin("OptionData OD5 on OD5.option_id = S.habitat_id and OD5.data_type = '" . $this->languageTag . "'")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and (PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)")
				->innerJoin("Project PR on PSM.project_id = PR.project_id")
				->where("A.photo_id is NULL")
				->order("S.site_name, P.taken");
			
			//error_log("query1 created: " . $query1->dump() );
				
			
		}
		
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv','filter1')))
			->values($query1);
		
		//error_log("queryInsert created");
		
		$db->setQuery($queryInsert);
		
		//error_log("About to execute");

		$db->execute();
		
		//error_log("Execution complete");
	}
	
	
	private function generateUserNoAnimalAudio () {
		
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
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), PR.project_prettyname, 'null', REPLACE(S.site_name, ',', ' '), P.upload_filename, P.taken, 'null', SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), P.site_id, 'null', 'null', 'null', P.photo_id) as report_csv, PR.project_id as filter1")
				->from("Photo P")
				->leftJoin("Animal A on A.photo_id = P.photo_id and P.sequence_num = 1 and P.status in (1,2)")
				->innerJoin("Site S on P.site_id = S.site_id and S.person_id = " . $this->personId )
				->leftJoin("Options O5 on O5.option_id = S.habitat_id")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and (PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)")
				->innerJoin("Project PR on PSM.project_id = PR.project_id")
				->where("A.photo_id is NULL")
				->order("S.site_name, P.taken");
			
			//error_log("query1 created: " . $query1->dump() );
				
			
		}
		else {
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), PR.project_prettyname, 'null', REPLACE(S.site_name, ',', ' '), P.upload_filename, P.taken, 'null', SUBSTRING_INDEX(IFNULL(OD5.value, 'null'), ' ', 1), S.latitude, S.longitude, REPLACE(S.grid_ref, ',', ' '), P.site_id, 'null', 'null', 'null', P.photo_id) as report_csv, PR.project_id as filter1")
				->from("Photo P")
				->leftJoin("Animal A on A.photo_id = P.photo_id and P.sequence_num = 1 and P.status in (1,2)")
				->innerJoin("Site S on P.site_id = S.site_id and S.person_id = " . $this->personId)
				->leftJoin("OptionData OD5 on OD5.option_id = S.habitat_id and OD5.data_type = '" . $this->languageTag . "'")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and (PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)")
				->innerJoin("Project PR on PSM.project_id = PR.project_id")
				->where("A.photo_id is NULL")
				->order("S.site_name, P.taken");
			
			//error_log("query1 created: " . $query1->dump() );
				
			
		}
		
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv','filter1')))
			->values($query1);
		
		//error_log("queryInsert created");
		
		$db->setQuery($queryInsert);
		
		//error_log("About to execute");

		$db->execute();
		
		//error_log("Execution complete");
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
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), PR.project_prettyname, REPLACE(O.option_name, ',', ' -'), P.taken, SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), A.animal_id, A.timestamp, IFNULL(O2.option_name, 'null'), IFNULL(O3.option_name, 'null'), IFNULL(A.number, 'null'), REPLACE(IFNULL(A.notes, 'null'), ',', ' -'), IFNULL(O4.option_name, 'null'), P.photo_id) as report_csv, PR.project_id as filter1")
				->from("Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id and P.sequence_num = 1 and P.status in (1,2)")
				->innerJoin("PhotoSequence PS on P.photo_id = PS.start_photo_id")
				->innerJoin("Photo P2 on PS.end_photo_id = P2.photo_id")
				->innerJoin("Site S on P.site_id = S.site_id")
				->innerJoin("Upload U on P.upload_id = U.upload_id")
				->innerJoin("Options O on O.option_id = A.species")
				->leftJoin("Options O2 on O2.option_id = A.age")
				->leftJoin("Options O3 on O3.option_id = A.gender")
				->leftJoin("Options O4 on O4.option_id = A.sure")
				->leftJoin("Options O5 on O5.option_id = S.habitat_id")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and (PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)")
				->innerJoin("Project PR on PSM.project_id = PR.project_id")
				->where("A.species != 97")
				->where("A.person_id = " . $this->personId)
				->order("S.site_name, P.taken, A.timestamp");
			
			//error_log("query1 created: " . $query1->dump() );
		}
		else {
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), PR.project_prettyname, REPLACE(IFNULL(OD.value, O.option_name), ',', ' -'), P.taken, SUBSTRING_INDEX(IFNULL(OD5.value, 'null'), ' ', 1), A.animal_id, A.timestamp, IFNULL(OD2.value, 'null'), IFNULL(OD3.value, 'null'), IFNULL(A.number, 'null'), REPLACE(IFNULL(A.notes, 'null'), ',', ' -'), IFNULL(OD4.value, 'null'), P.photo_id) as report_csv, PR.project_id as filter1")
				->from("Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id and P.sequence_num = 1 and P.status in (1,2)")
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
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and (PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)")
				->innerJoin("Project PR on PSM.project_id = PR.project_id")
				->where("A.species != 97")
				->where("A.person_id = " . $this->personId)
				->order("S.site_name, P.taken, A.timestamp");
			
			//error_log("query1 created: " . $query1->dump() );
		}
		
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv','filter1')))
			->values($query1);
		
		//error_log("queryInsert created: " . $queryInsert->dump());
		
		$db->setQuery($queryInsert);
		
		//error_log("About to execute");

		$db->execute();
		
		//error_log("Execution complete");

	}
	
	
	private function generateUserAnimalAllAudio () {
		
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
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), PR.project_prettyname, REPLACE(O.option_name, ',', ' -'),P.taken, REPLACE(IFNULL(A.notes, 'null'), ',', ' -'), SUBSTRING_INDEX(IFNULL(O5.option_name, 'null'), ' ', 1), A.animal_id, A.timestamp, IFNULL(O4.option_name, 'null'), P.photo_id) as report_csv, PR.project_id as filter1")
				->from("Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id and P.sequence_num = 1 and P.status in (1,2)")
				->innerJoin("Site S on P.site_id = S.site_id")
				->innerJoin("Options O on O.option_id = A.species")
				->leftJoin("Options O4 on O4.option_id = A.sure")
				->leftJoin("Options O5 on O5.option_id = S.habitat_id")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and (PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)")
				->innerJoin("Project PR on PSM.project_id = PR.project_id")
				->where("A.species != 97")
				->where("A.person_id = " . $this->personId)
				->order("S.site_name, P.taken, A.timestamp");
			
			//error_log("query1 created: " . $query1->dump() );
		}
		else {
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('PlaySeq',P.sequence_id), PR.project_prettyname, REPLACE(IFNULL(OD.value, O.option_name), ',', ' -'), P.taken, REPLACE(IFNULL(A.notes, 'null'), ',', ' -'), SUBSTRING_INDEX(IFNULL(OD5.value, 'null'), ' ', 1), A.animal_id, A.timestamp, IFNULL(OD4.value, 'null'), P.photo_id) as report_csv, PR.project_id as filter1")
				->from("Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id and P.sequence_num = 1 and P.status in (1,2)")
				->innerJoin("Site S on P.site_id = S.site_id")
				->innerJoin("Options O on O.option_id = A.species")
				->leftJoin("OptionData OD on OD.option_id = A.species and OD.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD4 on OD4.option_id = A.sure and OD4.data_type = '" . $this->languageTag . "'")
				->leftJoin("OptionData OD5 on OD5.option_id = S.habitat_id and OD5.data_type = '" . $this->languageTag . "'")
				->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and (PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)")
				->innerJoin("Project PR on PSM.project_id = PR.project_id")
				->where("A.species != 97")
				->where("A.person_id = " . $this->personId)
				->order("S.site_name, P.taken, A.timestamp");
			
			//error_log("query1 created: " . $query1->dump() );
		}
		
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv','filter1')))
			->values($query1);
		
		//error_log("queryInsert created");
		
		$db->setQuery($queryInsert);
		
		//error_log("About to execute");

		$db->execute();
		
		//error_log("Execution complete");

	}
		
	//Effort data for the most recent month
	private function generateEffortData () {
		
		// Delete any existing data 
		$this->removePreviousRows();
		
		$endDate = date('Ymd', strtotime("last day of this month"));
		
		// Get any subprojects for this project id
		//$projectIds = getThisAndAllSubs ( $this->projectId );
		$projects = getProjects ( 'ADMIN', false, $this->projectId );
		
		$projectStr = implode(',', array_keys($projects));
		
		$options = dbOptions();
		$userDb = $options['userdb'];
		$prefix = $options['userdbprefix'];
		
		$db = JDatabase::getInstance(dbOptions());
		
		$query1 = null;
		
		// No need to do this differently for non English language as no text...
		$query1 = $db->getQuery(true)
			->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', PUM.person_id, U.username, U.email, '". $endDate . "', IFNULL(LTM.month_classified,0), IFNULL(LTM.month_uploaded,0), IFNULL(UTS.month_tests,0), IFNULL(UTS.month_score,0), IFNULL(LTM.total_classified,0), IFNULL(LTM.total_uploaded,0), IFNULL(UTS.total_tests,0), IFNULL(UTS.total_score,0)) as report_csv")
			->from("ProjectUserMap PUM")
			->leftJoin("LeagueTableByMonth LTM on PUM.person_id = LTM.person_id and PUM.project_id = LTM.project_id and LTM.end_date = '".$endDate."'")
			->leftJoin("UserTestStatistics UTS on PUM.person_id = UTS.person_id and UTS.end_date = '".$endDate."'")
			->innerJoin($userDb . "." . $prefix ."users U on PUM.person_id = U.id")
			->where("PUM.project_id in (" . $projectStr . ")")
			->order("PUM.person_id");
			
		//error_log("query1 created: " . $query1->dump() );
		
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv')))
			->values($query1);
		
		//error_log("queryInsert created: " . $queryInsert->dump());
		
		$db->setQuery($queryInsert);
		
		//error_log("About to execute");

		$db->execute();
	

	}
	
	// This version gets all months not just the most recent one
	private function generateAllEffortData () {
		
		// Delete any existing data 
		$this->removePreviousRows();
		
		$options = dbOptions();
		$db = JDatabaseDriver::getInstance($options);
		
		// Get any subprojects for this project id
		$projects = getProjects ( 'ADMIN', false, $this->projectId );
		
		$projectStr = implode(',', array_keys($projects));
		
		$options = dbOptions();
		$userDb = $options['userdb'];
		$prefix = $options['userdbprefix'];
		
		
		$db = JDatabase::getInstance(dbOptions());
		
		$query1 = null;
		
		$query1 = $db->getQuery(true)
			->select( "distinct " . $this->reportId . " as report_id, CONCAT_WS(',', PUM.person_id, U.username, U.email, LTM.end_date, IFNULL(LTM.month_classified,0), IFNULL(LTM.month_uploaded,0), IFNULL(UTS.month_tests,0), IFNULL(UTS.month_score,0), IFNULL(LTM.total_classified,0), IFNULL(LTM.total_uploaded,0), IFNULL(UTS.total_tests,0), IFNULL(UTS.total_score,0)) as report_csv")
			->from("ProjectUserMap PUM")
			->innerJoin("LeagueTableByMonth LTM on PUM.person_id = LTM.person_id and PUM.project_id = LTM.project_id")
			->innerJoin("UserTestStatistics UTS on PUM.person_id = UTS.person_id and UTS.end_date = LTM.end_date")
			->innerJoin($userDb . "." . $prefix ."users U on PUM.person_id = U.id")
			->where("PUM.project_id in (" . $projectStr . ")");
		
		//error_log("query1 created: " . $query1->dump() );
		
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv')))
			->values($query1);
		
		//error_log("queryInsert created: " . $queryInsert->dump());
		
		$db->setQuery($queryInsert);
		
		//error_log("About to execute");

		$db->execute();
		
		
		$query2 = $db->getQuery(true)
			->select( "distinct " . $this->reportId . " as report_id, CONCAT_WS(',', PUM.person_id, U.username, U.email, UTS.end_date, IFNULL(LTM.month_classified,0), IFNULL(LTM.month_uploaded,0), IFNULL(UTS.month_tests,0), IFNULL(UTS.month_score,0), IFNULL(LTM.total_classified,0), IFNULL(LTM.total_uploaded,0), IFNULL(UTS.total_tests,0), IFNULL(UTS.total_score,0)) as report_csv")
			->from("ProjectUserMap PUM")
			->leftJoin("LeagueTableByMonth LTM on PUM.person_id = LTM.person_id and PUM.project_id = LTM.project_id")
			->innerJoin("UserTestStatistics UTS on PUM.person_id = UTS.person_id")
			->innerJoin($userDb . "." . $prefix ."users U on PUM.person_id = U.id")
			->where("PUM.project_id in (" . $projectStr . ")")
			->where("LTM.end_date is NULL");
			
		//error_log("query2 created: " . $query2->dump() );
		
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv')))
			->values($query2);
		
		//error_log("queryInsert created: " . $queryInsert->dump());
		
		$db->setQuery($queryInsert);
		
		//error_log("About to execute");

		$db->execute();
		
		
		$query3 = $db->getQuery(true)
			->select( "distinct " . $this->reportId . " as report_id, CONCAT_WS(',', PUM.person_id, U.username, U.email, LTM.end_date, IFNULL(LTM.month_classified,0), IFNULL(LTM.month_uploaded,0), IFNULL(UTS.month_tests,0), IFNULL(UTS.month_score,0), IFNULL(LTM.total_classified,0), IFNULL(LTM.total_uploaded,0), IFNULL(UTS.total_tests,0), IFNULL(UTS.total_score,0)) as report_csv")
			->from("ProjectUserMap PUM")
			->innerJoin("LeagueTableByMonth LTM on PUM.person_id = LTM.person_id and PUM.project_id = LTM.project_id")
			->leftJoin("UserTestStatistics UTS on PUM.person_id = UTS.person_id")
			->innerJoin($userDb . "." . $prefix ."users U on PUM.person_id = U.id")
			->where("PUM.project_id in (" . $projectStr . ")")
			->where("UTS.end_date is NULL");
			
		//error_log("query3 created: " . $query3->dump() );
		
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv')))
			->values($query3);
		
		//error_log("queryInsert created: " . $queryInsert->dump());
		
		$db->setQuery($queryInsert);
		
		//error_log("About to execute");

		$db->execute();

	}
		
	// Get resources uploaded
	private function generateResourceData () {
		
		// Delete any existing data 
		$this->removePreviousRows();
		
		$options = dbOptions();
		$db = JDatabaseDriver::getInstance($options);
		
		$options = dbOptions();
		$userDb = $options['userdb'];
		$prefix = $options['userdbprefix'];
		
		$db = JDatabase::getInstance(dbOptions());
		
		$query1 = null;
		
		$query1 = $db->getQuery(true)
			->select( "distinct " . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('ViewSet',R.set_id), O.option_name, R.title, S.name, U.username, R.timestamp) as report_csv")
			->from("Resource R")
			->innerJoin("Options O on O.option_id = R.resource_type")
			->innerJoin("School S on S.school_id = R.school_id")
			->innerJoin($userDb . "." . $prefix ."users U on R.person_id = U.id")
			->order("R.timestamp DESC");
		
		//error_log("query1 created: " . $query1->dump() );
		
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv')))
			->values($query1);
		
		//error_log("queryInsert created: " . $queryInsert->dump());
		
		$db->setQuery($queryInsert);
		
		//error_log("About to execute");

		$db->execute();

	}
	
	// Get activities completed
	private function generateActivityData () {
		
		// Delete any existing data 
		$this->removePreviousRows();
		
		
		$options = dbOptions();
		$db = JDatabaseDriver::getInstance($options);
		
		$options = dbOptions();
		$userDb = $options['userdb'];
		$prefix = $options['userdbprefix'];
		
		
		$db = JDatabase::getInstance(dbOptions());
		
		$query1 = $db->getQuery(true)
			->select( "IFNULL(ST.set_id,0) as set_id, M.name as module_name, M.module_id as module_id, O.option_name as badge_group, B.name as badge_name, T.points as points, S.school_id as school_id, S.name as school, R.role_id as role_id, R.display_text as role, U.username as person, ST.complete_date as date")
			->from("StudentTasks ST")
			->innerJoin("Task T on T.task_id = ST.task_id")
			->innerJoin("Badge B on B.badge_id = T.badge_id")
			->innerJoin("Options O on O.option_id = B.badge_group")
			->innerJoin("SchoolUsers SU on SU.person_id = ST.person_id") 
			->innerJoin("School S on S.school_id = SU.school_id")
			->innerJoin("Role R on R.role_id = SU.role_id")
			->innerJoin("Module M on M.module_id = B.module_id")
			->innerJoin($userDb . "." . $prefix ."users U on ST.person_id = U.id")
			->where("ST.status > " . Biodiv\Badge::PENDING);
		
			
		$query2 = $db->getQuery(true)
			->select( "IFNULL(TT.set_id,0) as set_id, M.name as module_name, M.module_id as module_id, O.option_name as badge_group, B.name as badge_name, T.points as points, S.school_id as school_id, S.name as school, R.role_id as role_id, R.display_text as role, U.username as person, TT.complete_date as date")
			->from("TeacherTasks TT")
			->innerJoin("Task T on T.task_id = TT.task_id")
			->innerJoin("Badge B on B.badge_id = T.badge_id")
			->innerJoin("Options O on O.option_id = B.badge_group")
			->innerJoin("SchoolUsers SU on SU.person_id = TT.person_id") 
			->innerJoin("School S on S.school_id = SU.school_id")
			->innerJoin("Role R on R.role_id = SU.role_id")
			->innerJoin("Module M on M.module_id = B.module_id")
			->innerJoin($userDb . "." . $prefix ."users U on TT.person_id = U.id")
			->where("TT.status > " . Biodiv\Badge::PENDING);
		
			
		$query = $query1->union($query2)->order("date DESC");
		
		//error_log("Activity report query created: " . $query->dump() );
		
		$concatQuery = $db->getQuery(true)
			//->select( "distinct " . $this->reportId . " as report_id, CONCAT_WS(',', O.option_name, B.name, S.name, R.display_text, U.username, ST.complete_date) as report_csv")
			
			->select( "distinct " . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('ViewSet',set_id), module_name, badge_group, badge_name, points, school, role, person, date) as report_csv, module_id as filter1, school_id as filter2, role_id as filter3")
			->from("(" . (string)$query . " ) AS Temp");
			
		
		//error_log("concatQuery created: " . $concatQuery->dump() );
		
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv','filter1','filter2','filter3')))
			->values($concatQuery);
		
		//error_log("queryInsert created: " . $queryInsert->dump());
		
		$db->setQuery($queryInsert);
		
		$db->execute();

	}
		
	// Get activities completed
	private function generateLevelActivityData () {
		
		// Delete any existing data 
		$this->removePreviousRows();
		
		
		$options = dbOptions();
		$db = JDatabaseDriver::getInstance($options);
		
		$options = dbOptions();
		$userDb = $options['userdb'];
		$prefix = $options['userdbprefix'];
		
		
		$db = JDatabase::getInstance(dbOptions());
		
		$query1 = $db->getQuery(true)
			->select( "IFNULL(SB.set_id,0) as set_id, B.lock_level as level, M.name as module_name, M.module_id as module_id, BG.name as badge_group, B.name as badge_name, S.school_id as school_id, S.name as school, R.role_id as role_id, R.display_text as role, U.username as person, SB.complete_date as date")
			->from("StudentBadges SB")
			->innerJoin("Badge B on B.badge_id = SB.badge_id")
			->innerJoin("BadgeGroup BG on BG.group_id = B.badge_group")
			->innerJoin("SchoolUsers SU on SU.person_id = SB.person_id") 
			->innerJoin("School S on S.school_id = SU.school_id")
			->innerJoin("Role R on R.role_id = SU.role_id")
			->innerJoin("Module M on M.module_id = B.module_id")
			->innerJoin($userDb . "." . $prefix ."users U on SB.person_id = U.id")
			->where("SB.status > " . Biodiv\Badge::PENDING);
		
		$query2 = $db->getQuery(true)
			->select( "IFNULL(TB.set_id,0) as set_id, B.lock_level as level, M.name as module_name, M.module_id as module_id, BG.name as badge_group, B.name as badge_name, S.school_id as school_id, S.name as school, R.role_id as role_id, R.display_text as role, SC.name as person, TB.complete_date as date")
			->from("TeacherBadges TB")
			->innerJoin("Badge B on B.badge_id = TB.badge_id")
			->innerJoin("BadgeGroup BG on BG.group_id = B.badge_group")
			->innerJoin("SchoolUsers SU on SU.person_id = TB.person_id") 
			->innerJoin("School S on S.school_id = SU.school_id")
			->innerJoin("Role R on R.role_id = SU.role_id")
			->innerJoin("Module M on M.module_id = B.module_id")
			->innerJoin("SchoolClass SC on TB.class_id = SC.class_id")
			->where("TB.status > " . Biodiv\Badge::PENDING);
		
			
		// $query2 = $db->getQuery(true)
			// ->select( "IFNULL(TT.set_id,0) as set_id, M.name as module_name, M.module_id as module_id, O.option_name as badge_group, B.name as badge_name, T.points as points, S.school_id as school_id, S.name as school, R.role_id as role_id, R.display_text as role, U.username as person, TT.complete_date as date")
			// ->from("TeacherTasks TT")
			// ->innerJoin("Task T on T.task_id = TT.task_id")
			// ->innerJoin("Badge B on B.badge_id = T.badge_id")
			// ->innerJoin("Options O on O.option_id = B.badge_group")
			// ->innerJoin("SchoolUsers SU on SU.person_id = TT.person_id") 
			// ->innerJoin("School S on S.school_id = SU.school_id")
			// ->innerJoin("Role R on R.role_id = SU.role_id")
			// ->innerJoin("Module M on M.module_id = B.module_id")
			// ->innerJoin($userDb . "." . $prefix ."users U on TT.person_id = U.id")
			// ->where("TT.status > " . Biodiv\Badge::PENDING);
		
			
		$query = $query1->union($query2)->order("date DESC");
		
		error_log("LevelActivity report query created: " . $query->dump() );
		
		$concatQuery = $db->getQuery(true)
			//->select( "distinct " . $this->reportId . " as report_id, CONCAT_WS(',', O.option_name, B.name, S.name, R.display_text, U.username, ST.complete_date) as report_csv")
			
			->select( "distinct " . $this->reportId . " as report_id, CONCAT_WS(',', CONCAT('ViewSet',set_id), level, module_name, badge_group, badge_name, school, role, person, date) as report_csv, level as filter1, school_id as filter2, role_id as filter3")
			->from("(" . (string)$query . " ) AS Temp");
			
		
		//error_log("concatQuery created: " . $concatQuery->dump() );
		
		$queryInsert = $db->getQuery(true)
			->insert('ReportRows')
			->columns($db->qn(array('report_id','row_csv','filter1','filter2','filter3')))
			->values($concatQuery);
		
		//error_log("queryInsert created: " . $queryInsert->dump());
		
		$db->setQuery($queryInsert);
		
		$db->execute();

	}
		
	
	// Get resources uploaded
	private function generateNHMPSubscribeData () {
		
		// Delete any existing data 
		$this->removePreviousRows();
		
		if ( $this->userIsAdmin ) {
		
			$options = dbOptions();
			$db = JDatabaseDriver::getInstance($options);
			
			$options = dbOptions();
			$userDb = $options['userdb'];
			$prefix = $options['userdbprefix'];
			
			$db = JDatabase::getInstance(dbOptions());
			
			$query1 = null;
			
			$query1 = $db->getQuery(true)
				->select( "" . $this->reportId . " as report_id, CONCAT_WS(',', DATE(UC.timestamp), U.name, U.email) as report_csv")
				->from("UserConsent UC")
				->innerJoin($userDb . "." . $prefix ."users U on UC.person_id = U.id")
				->where("consent_given = 1")
				->order("UC.timestamp DESC");
			
			error_log("query1 created: " . $query1->dump() );
			
			$queryInsert = $db->getQuery(true)
				->insert('ReportRows')
				->columns($db->qn(array('report_id','row_csv')))
				->values($query1);
			
			//error_log("queryInsert created: " . $queryInsert->dump());
			
			$db->setQuery($queryInsert);
			
			//error_log("About to execute");

			$db->execute();
		}

	}
	
	
	// // Standalone report creation utility function
	// public static function createReportFile ( $folder, $filename, $headings, $rows ) {
	
		// $reportRoot = JPATH_SITE."/biodivimages/reports";
		// $filePath = $reportRoot."/".$folder."/";
		
		// // $t=time();
		// // $dateStr = date("Ymd_His",$t);
		// // $filename = $type . '_' . $dateStr . ".csv";
		
		// $tmpCsvFile = $filePath . "/tmp_" . $filename;
		// $newCsvFile = $filePath . "/" . $filename;
		
		// // Has the report already been created?
		// if ( !file_exists($newCsvFile) ) {
			
			// error_log ("File " . $newCsvFile . " created for writing" );
			
			// // Creates a new csv file and store it in directory
			// // Rename once finished writing to file
			// if (!file_exists($filePath)) {
				// mkdir($filePath, 0755, true);
			// }
			
			// $tmpCsv = fopen ( $tmpCsvFile, 'w');
			
			// // First put the headings
			// if ( $headings ) {
				// fputcsv($tmpCsv, $headings);
			// }
			
			// // Then each row
			// foreach ( $rows as $row ) {
				// fputcsv($tmpCsv, $row);
			// }
			
			// fclose($tmpCsv);
			
			// rename ( $tmpCsvFile, $newCsvFile );

		// }
		
		// $url = JURI::root()."/biodivimages/reports/".$folder."/".$filename;
		// return $url;
	// }


}

?>
