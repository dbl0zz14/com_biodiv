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
class BioDivViewNbn extends JViewLegacy
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

		$db = JDatabase::getInstance(dbOptions());
	  
	 
		$nbnSettings = json_decode(getSetting("nbn"));
		$excludeProjects = implode(',', $nbnSettings->exclude_projects);
		$excludeUsers = implode(',', $nbnSettings->exclude_users);
		$intervals = $nbnSettings->intervals;
		$thresholds = $nbnSettings->thresholds;
		
	
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
						
					//error_log("NBN select votes query created: " . $query->dump());
					
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
							WHEN V.num_users=1 THEN "Unconfirmed, not reviewed"
							WHEN V.num_users>1 and V.num_votes/V.num_users>0.5 THEN "Accepted, considered correct"
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
			->where("S.latitude > 50 AND S.latitude < 60.15 AND S.longitude > -7.64 AND S.longitude < 2")
			->where("V.species_id NOT IN (86,87,95,96,97)")
			->where("PR.project_id not in (".$excludeProjects.")")
			->group("V.photo_id, V.species_id");
			
		//error_log("NBN select query created: " . $query->dump());
		
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
							
				
		$t=time();
		$dateStr = date("Y-m",$t);
		$this->filename = 'MammalWebNBN_' . $dateStr . ".csv";
		$folder = 'nbn';
		
		$reportRoot = JPATH_SITE."/biodivimages/reports";
		$filePath = $reportRoot."/".$folder."/";
		
		$tmpCsvFile = $filePath . "/tmp_" . $this->filename;
		$this->newCsvFile = $filePath . "/" . $this->filename;
		
		// Has the report already been created?
		if ( !file_exists($this->newCsvFile) ) {
			
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
							'N.lat, '.
							'N.lon, '.
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
			
			rename ( $tmpCsvFile, $this->newCsvFile );

		}

		// Display the view
		parent::display($tpl);
    }
}



?>
