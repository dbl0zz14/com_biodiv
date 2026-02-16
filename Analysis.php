<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/

// No direct access to this file
defined('_JEXEC') or die;
 


class Analysis {
	
	public static function runAnalysis () {
		
		// Get outstanding requests
		$analysisRequests = self::getAnalysisRequests();
		
		foreach ( $analysisRequests as $analysisRequest ) {
			
			$type = $analysisRequest->type;
				
			if ( $type == 'DENSITY' ) {
				
				self::runDensityAnalysis ( $analysisRequest );
				
			}
		}
	}
	
	// List of the available user / project analysis options for the current user
	public static function listUserAnalysis () {
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
                $db = JDatabase::getInstance(dbOptions());

		$analysisProjects = myAnalysisProjects();

		$projectIds = array_keys ( $analysisProjects );

		$projectStr = implode ( ',', $projectIds );
		
		// Set up the select query for the report
		$query = $db->getQuery(true)
			->select( "distinct O.option_id, O.option_name")
			->from("Options O")
			->innerJoin("ProjectOptions PO on O.option_id = PO.option_id and O.struc='useroptinanalysis'" ) 
			->where("PO.project_id in ( " . $projectStr . " )" )
			->order("O.seq");
			
		$db->setQuery($query);

		error_log(" listUserAnalysis select query created " . $query->dump());

		$analyses = $db->loadRowList();
	
		// Translate if necessary
		$langObject = JFactory::getLanguage();
		$languageTag = $langObject->getTag();
		if ( $languageTag != 'en-GB' ) {
			foreach ( $analyses as $a ) {
				$nameTran = codes_getOptionTranslation($a[0]);
				$a[1] = $nameTran;
			}
		}
		
		return $analyses;
	}
	
	public static function generateAnalysis ( $analysisType ) {
		
		$typeString = getOptionData ( $analysisType, 'analysistype' );
		
		if ( $typeString && ($typeString[0] == "SQUIRRELDENSITY") ) {
			
			print '<button type="button" id = "squirrelDensityHelp" class="btn btn-lg btn-outline-primary mt-3 mb-3" data-toggle="modal" data-target="#helpModal">'.JText::_("COM_BIODIV_DASHANALYSIS_HELP").'</button>';

			print '<h5 class="card-subtitle mt-3 mb-3">'.JText::_("COM_BIODIV_DASHANALYSIS_INFO").'</h5>';
			$maxStart = date('Y-m-d');
			$maxTime = strtotime($maxStart);
			$suggestedStart = date('Y-m-d', strtotime("-6 days", $maxTime) );
			
			$analysisProjects = self::getUserProjectsForAnalysis ( $analysisType );
			$numProjects = count($analysisProjects) ;
			
			if ( $numProjects == 0 ) {
				
				print '<p>'.JText::_("COM_BIODIV_DASHANALYSIS_NO_PROJECTS").'</p>';
			}
			else {
	
				print '<form id="squirrelDensityForm" role="form">';

				//print HTMLHelper::_('form.token');
				print JHtml::_('form.token');
				print '<div class="form-group">';
				
				//print '<input id="densityNumDays" type="hidden" name="densityNumDays" value="5"/>';
				print '<input id="densitySpeciesId" type="hidden" name="densitySpeciesId" value="16"/>';
				
				if ( $numProjects == 1 ) {
					$project = $analysisProjects[0];
					$projectId = $project->project_id;
					print '<input id="densityProjectId" type="hidden" name="densityProjectId" value="'.$projectId.'"/>';
				}
				else {
					
					print '<label for="densityProjectId" class="form-label mt-3" style="margin-top:1rem;" aria-describedby="densityProjectId">'.JText::_("COM_BIODIV_DASHANALYSIS_PROJECT").'</label>';
					print '<select name = "densityProjectId" class = "form-control mb-3">';
					print '<option value="" disabled selected hidden>';
					print JText::_("COM_BIODIV_DASHANALYSIS_SEL_PROJ"); 
					print '...</option>';
						
					foreach($analysisProjects as $project){
						print '<option value="'.$project->project_id.'">'.$project->project_prettyname.'</option>';
					}
					   
					print '</select>';
				}

				print '<label for="densityLabel" class="form-label mt-3" style="margin-top:1rem;" aria-describedby="densityLabel">'.JText::_("COM_BIODIV_DASHANALYSIS_LABEL").'</label>';
				print '<input type="text" class="form-control mb-3" style="margin-bottom:1rem;" id="densityLabel" name="densityLabel" value="'.JText::_("COM_BIODIV_DASHANALYSIS_ENTER_LABEL").'"/>';
					
				print '<label for="densityStartDate" class="form-label mt-3" style="margin-top:1rem;">'.JText::_("COM_BIODIV_DASHANALYSIS_START_DATE").'</label>';
				print '<input type="date" class="form-control mb-3"  style="margin-bottom:1rem;" name="densityStartDate" id="densityStartDate" value="'.$suggestedStart.
									'" max="'.$maxStart.'"/>';
				
				print '<label for="densityNumDays" class="form-label mt-3" style="margin-top:1rem;">'.JText::_("COM_BIODIV_DASHANALYSIS_NUM_DAYS").'</label>';
				print '<input type="number" class="form-control mb-3" style="margin-bottom:1rem;" name="densityNumDays" id="densityNumDays" value="5" min="1" max="7"/>';
				
				print '<button type="button" id = "squirrelDensitySave" class="btn btn-lg btn-primary mt-3 mb-3" style="margin-top:1rem; margin-bottom:1rem;">'.JText::_("COM_BIODIV_DASHANALYSIS_SUBMIT").'</button>';

				print '<div id="squirrelDensityRequestDone" class="mt-3 mb-3" style="margin-top:1rem; margin-bottom:1rem;"></div>';
				print '</form>';
				
				print '</div>';

				$helpArticle = getArticle ( $analysisType );
			
				// Generate a modal for the help article
				print '<div id="helpModal" class="modal fade" tabindex="-1">';
				print '  <div class="modal-dialog modal-dialog-scrollable modal-xl">';

				print '    <!-- Modal content-->';
				print '    <div class="modal-content">';
				print '      <div class="modal-header">';
				print '        <h2 class="modal-title">'.JText::_("COM_BIODIV_DASHANALYSIS_HELP").'</h2>';
				print '        <button type="button" class="close" data-dismiss="modal"></button>';
				print '      </div>';
				print '      <div class="modal-body p-4">';
				print '      <div id="article" >';
				if ( $helpArticle and property_exists($helpArticle, "introtext" ) ) {
					
					print $helpArticle->introtext;
				}
				print '      </div>';
				print '      </div>';
				print '		<div class="modal-footer">';
				print '		<button type="button" class="btn btn-outline-secondary btn-lg" data-dismiss="modal">'.JText::_("COM_BIODIV_DASHANALYSIS_CLOSE").'</button>';
				print '      </div>'; // modal footer
				print '    </div>'; // modal content

				print '  </div>'; // modal dialog
				print '</div>'; // modal
	
			}

		}
	}
	
	
	public static function addDensityRequest ( $projectId, $label, $speciesId, $startDate, $numDays ) {
		
		$success = false;
		
		$personId = userID();
		
		if ( $personId ) {
		
			//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
                	$db = JDatabase::getInstance(dbOptions());
					
			$defn = new \StdClass ();
			$defn->species_id = $speciesId;
			$defn->project = $projectId;
			$defn->start = $startDate;
			$defn->num_days = $numDays;
			$defnJson = json_encode($defn);
			
			$newRequest = new \StdClass();
			$newRequest->person_id = $personId;
			$newRequest->type = "DENSITY";
			$newRequest->label = $label;
			$newRequest->json_defn = $defnJson;
			
			$success = $db->insertObject('Analysis', $newRequest);
		}
		
		return $success;
				
	}
	
	
	private static function getUserProjectsForAnalysis ( $analysisType ) {
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
               	$db = JDatabase::getInstance(dbOptions());
		
		$analysisProjects = myAnalysisProjects();

		$projectIds = array_keys ( $analysisProjects );

		$projectStr = implode ( ',', $projectIds );
		
		// Set up the select query for the report
		$query = $db->getQuery(true)
			->select("P.project_id, P.project_prettyname")
			->from("Project P")
			->innerJoin("ProjectOptions PO on PO.project_id = P.project_id " ) 
			->innerJoin("Options O on O.option_id = PO.option_id and O.option_id = " . $analysisType)
			->where("PO.project_id in ( " . $projectStr . " )" )
			->order("P.project_prettyname");
			
			// ->select( "O.option_id, O.option_name")
			// ->from("Options O")
			// ->innerJoin("ProjectOptions PO on O.option_id = PO.option_id and O.struc='useroptinanalysis'" ) 
			// ->where("PO.project_id in (select project_id from ProjectUserMap where person_id = " . $personId . ")" )
			// ->order("O.seq");
			
		$db->setQuery($query);

		error_log(" listUserAnalysis select query created " . $query->dump());

		$projects = $db->loadObjectList();
		
		return $projects;
	}
	
	
	private static function getAnalysisRequests () {
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
               	$db = JDatabase::getInstance(dbOptions());
		
		$query = $db->getQuery(true);
		$query->select("*")
			->from("Analysis")
			->where("result is NULL")
			->setLimit('5'); // LIMIT 5
		$db->setQuery($query); 
		$analysisRequests = $db->loadObjectList();
		
		return $analysisRequests;
	}	
	
	private static function runDensityAnalysis ( $analysisRequest ) {
			
		if ( !property_exists($analysisRequest, 'a_id') ) {
			
			error_log ("No a_id given in analysis request " );
			return false;
		}
		
		$requestId = $analysisRequest->a_id;
		
		if ( !property_exists($analysisRequest, 'type') ) {
			
			error_log ("No type given in analysis request " . $requestId );
			return false;
		}
		
		$type = $analysisRequest->type;
		
		$requestDefn = json_decode($analysisRequest->json_defn);
			
		if ( !property_exists($requestDefn, 'species_id') ) {
			
			error_log ("No species id given in analysis request " . $requestId );
			return false;
		}
		
		$speciesId = $requestDefn->species_id;
		
		$siteIds = null;
		$projectId = null;
		
		if ( property_exists($requestDefn, 'sites') ) {
			
			$siteIds = $requestDefn->sites;
		}
		if ( property_exists($requestDefn, 'project') ) {
			
			$projectId = $requestDefn->project;
		}
		
		if ( !$siteIds && !$projectId ) {
			
			error_log ("No sites or project given in analysis request " . $requestId );
			return false;
		}
		
		if ( !property_exists($requestDefn, 'start') ) {
			
			error_log ("No start given in analysis request " . $requestId );
			return false;
		}
		
		$start = $requestDefn->start;
		
		if ( !property_exists($requestDefn, 'num_days') ) {
			
			error_log ("No num days given in analysis request " . $requestId );
			return false;
		}
		
		$numDays = $requestDefn->num_days;
		$numToAdd = (int)$numDays - 1;
		
		$startTime = strtotime($start);
		$addStr = "+" . $numToAdd . " days";
		$endTime = strtotime($addStr, $startTime);
		
		// $dayBeforeUnixTime = strtotime("-1 day", $startTime);
		// $addStr = "+" . $numDays . " days";
		// $dayAfterUnixTime = strtotime($addStr, $startTime);
		
		// $dateBefore = date('Ymd', $dayBeforeUnixTime);
		// $dateAfter = date('Ymd', $dayAfterUnixTime);
		
		$startDate = date('Ymd', $startTime);
		$endDate = date('Ymd', $endTime);
	

		$ready = false;
		
		if ( $siteIds ) {
			
			$ready = self::checkSitesReadyForAnalysis( $type, $startDate, $endDate, $siteIds );
			
		}
		else if ( $projectId ) {
			
			$ready = self::checkProjectReadyForAnalysis( $type, $startDate, $endDate, $projectId );
			
		}
		
		if ( $ready ) {
			
			if ( $siteIds ) {
				
				// Store visits in db
				self::calculateSiteDensityVisits ( $requestId, $type, $speciesId, $startDate, $endDate, $siteIds  );
				
			}
			else if ( $projectId ) {
				
				// Store visits in db
				self::calculateProjectDensityVisits ( $requestId, $type, $speciesId, $startDate, $endDate, $projectId  );
				
			}
			
			// Calculate and store density from the visits
			self::calculateDensity ( $requestId );
			
		}
	}
	
	private static function checkSitesReadyForAnalysis( $type, $startDate, $endDate, $siteIds ) {
		
		$isReady = false;
		
		if ( $type == 'DENSITY' ) {
			
			if ( $siteIds ) {
				
				//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
               			$db = JDatabase::getInstance(dbOptions());
				$query = $db->getQuery(true);
		
				$siteStr = implode ( ",", $siteIds );
				$query->select("count(*)")
					->from("Photo P")
					->where("P.site_id in (".$siteStr.")")
					->where("P.sequence_num = 1")
					->where("DATE(P.taken) between " . $startDate . " and " . $endDate)
					->where("P.photo_id not in ( select photo_id from Classify where origin = " . $db->quote("CAI") . ")" );
				$db->setQuery($query); 
				$numUnprocessed = $db->loadResult();
				
				if ( $numUnprocessed > 0 ) {
					$isReady = false;
				}
				else {
					$isReady = true;
				}
			}
		}
		
		return $isReady;
	}
	
	private static function checkProjectReadyForAnalysis( $type, $startDate, $endDate, $projectId ) {
		
		$isReady = false;
		
		if ( $type == 'DENSITY' ) {
			
			if ( $projectId ) {
				
				//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
               			$db = JDatabase::getInstance(dbOptions());
				$query = $db->getQuery(true);
		
				$query->select("count(*)")
					->from("Photo P")
					->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id AND PSM.project_id = " . $projectId )
					->where("P.sequence_num = 1")
					->where("P.photo_id >= PSM.start_photo_id")
					->where("(PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)")
					->where("DATE(P.taken) between " . $startDate . " and " . $endDate)
					->where("P.photo_id not in ( select photo_id from Classify where origin = " . $db->quote("CAI") . ")" );
				$db->setQuery($query); 
				$numUnprocessed = $db->loadResult();
				
				if ( $numUnprocessed > 0 ) {
					$isReady = false;
				}
				else {
					$isReady = true;
				}
			}
		}
		
		return $isReady;
	}
	
	private static function calculateSiteDensityVisits( $requestId, $type, $speciesId, $startDate, $endDate, $siteIds ) {
		
		if ( $type == 'DENSITY' ) {
			
			if ( $siteIds ) {
				
				//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
               			$db = JDatabase::getInstance(dbOptions());
				$query = $db->getQuery(true);
		
				$siteStr = implode ( ",", $siteIds );
				$query->select("P.photo_id, P.site_id, P.taken, DATE(P.taken) as taken_date")
					->from("Photo P")
					->innerJoin("Classify C on C.photo_id = P.photo_id and C.origin = " . $db->quote('CAI') . " and C.species_id = " . $speciesId )
					->where("P.site_id in (".$siteStr.")")
					->where("DATE(P.taken) between " . $startDate . " and " . $endDate)
					->order("P.site_id, P.taken");
				$db->setQuery($query); 
				$speciesSightings = $db->loadObjectList();
				
				self::calculateVisits ( $requestId, $speciesSightings );
			}
		}
	}
	
	private static function calculateProjectDensityVisits( $requestId, $type, $speciesId, $startDate, $endDate, $projectId ) {
		
		if ( $type == 'DENSITY' ) {
			
			if ( $projectId ) {
				
				//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
               			$db = JDatabase::getInstance(dbOptions());
				$query = $db->getQuery(true);
		
				$query->select("P.photo_id, P.site_id, P.taken, DATE(P.taken) as taken_date")
					->from("Photo P")
					->innerJoin("ProjectSiteMap PSM on P.site_id = PSM.site_id AND PSM.project_id = " . $projectId )
					->innerJoin("Classify C on C.photo_id = P.photo_id and C.origin = " . $db->quote('CAI') . " and C.species_id = " . $speciesId )
					->where("P.photo_id >= PSM.start_photo_id")
					->where("(PSM.end_photo_id is null or P.photo_id <= PSM.end_photo_id)")
					->where("DATE(P.taken) between " . $startDate . " and " . $endDate)
					->order("PSM.site_id, P.taken");
				$db->setQuery($query); 
				$speciesSightings = $db->loadObjectList();
				
				self::calculateVisits ( $requestId, $speciesSightings );
			}
		}
	}

	private static function calculateVisits ( $requestId, $sightings ) {
		
		if ( count($sightings) > 0 ) {
					
			//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
               		$db = JDatabase::getInstance(dbOptions());
				
			$prevVisit = null;
			$currSiteId = null;
			$currTakenDate = null;
			$currTrialDay = 1;
			
			foreach ( $sightings as $sighting ) {
				
				$newVisit = 0;
				$newSiteId = $sighting->site_id;
				$newTakenDate = $sighting->taken_date;
				
				if ( !$currTakenDate ) {
					
					$currTakenDate = $newTakenDate;
				}
				else if ( $newTakenDate != $currTakenDate ) {
					
					$currTrialDay += 1;
				}
				// First visit
				if ( !$prevVisit or !$currSiteId) {
					
					$newVisit = 1;
				}
				else if ( $newSiteId != $currSiteId ) {
					
					$newVisit = 1;
				}
				else {
					
					$sightingInterval = strtotime($sighting->taken) - strtotime($prevVisit->taken);
			
					if ( $sightingInterval >= 300) $newVisit = 1;
					
				}
				$newRow = new \StdClass();
				$newRow->a_id = $requestId;
				$newRow->trial_day = $currTrialDay;
				$newRow->photo_id = $sighting->photo_id;
				$newRow->is_visit = $newVisit;
				
				$db->insertObject('AnalysisVisits', $newRow);
				
				$prevVisit = $sighting;
				$currSiteId = $newSiteId;
			}
		}
	}
	
	private static function calculateDensity ( $requestId ) {
		
		// Note we mimic the method used in spreadsheet here
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
               	$db = JDatabase::getInstance(dbOptions());
		
		$query = $db->getQuery(true);
			
		$query->select("count(distinct site_id)")
			->from("Photo P")
			->innerJoin("AnalysisVisits AV on P.photo_id = AV.photo_id")
			->where("AV.a_id = " . $requestId);
		$db->setQuery($query); 
		$totalNumSites = $db->loadResult();
			
		
		$query = $db->getQuery(true);
		
		$query->select("trial_day, SUM(is_visit) as num_visits")
			->from("AnalysisVisits AV")
			->where("AV.a_id = " . $requestId)
			->group("AV.trial_day");
		$db->setQuery($query); 
		$numVisitsByDay = $db->loadAssocList("trial_day", "num_visits");
		
		$avgPhotosPerSite = array();
		$sumOfAvgs = 0;
		foreach ( $numVisitsByDay as $trialDay=>$numVisits ) {
		
			// Any exceptions for eg non working cameras would be checked here
			
			// Any assumptions eg no upload = camera not working
			
			$numSites = $totalNumSites;
			
			if ( $numSites > 0 ) {
				
				$dailyData = new \StdClass ();
				$avg = $numVisits/$numSites;
				$sumOfAvgs += $avg;
				//$avgPhotosPerSite[$trialDay] = $avg;
				 
				$dailyData->day = $trialDay;
				$dailyData->visits = $numVisits;
				$dailyData->sites = $numSites;
				$dailyData->avg = $avg;
				
				$avgPhotosPerSite[$trialDay] = $dailyData;
			}
			// $dailyData->trial_day,
			// $dailyData->num_visits,
			// $dailyData->num_sites,
			// $dailyData->density,
		}
		
		$numTrialDays = count($avgPhotosPerSite);
		$overallAvg = null;
		if ( $numTrialDays > 0 ) {
			$overallAvg = $sumOfAvgs/$numTrialDays;
		}
		
		$density = null;
		
		if ( $overallAvg ) {
			
			$density = 0.4446 * $numVisits/$numSites + 0.8203;
			
		}
		
		$densityResult = new \StdClass ();
		$densityResult->days = $avgPhotosPerSite;
		$densityResult->density = $density;
		
		$result = json_encode($densityResult);
		
		$rowUpdateObj = new \StdClass ();
		$rowUpdateObj->a_id = $requestId;
		$rowUpdateObj->result = $result;
		$rowUpdateObj->when_complete = date('Y-m-d H:i:s');
		
		$db->updateObject('Analysis', $rowUpdateObj, 'a_id');
		
	}
}

