<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/

namespace MammalWeb\Component\Biodiv\Site\Library\Core;

// No direct access to this file
defined('_JEXEC') or die;
 
use Joomla\Database\DatabaseFactory;
use Joomla\CMS\Language\Text;


class Statistics {
	
	public static function getSpotterStatistics () {
	
		// Get all the text snippets for status view
		//$translations = getTranslations("status");

		$db = db();
		
		$statRows = array();

		$query = $db->getQuery(true);
		$query->select("end_date, num_uploaded as uploaded, num_classified as classified ")
			->from("Statistics")
			->where("project_id = 0")
			->order("end_date DESC");
		$db->setQuery($query, 0, 1); // LIMIT 1
		$row = $db->loadAssoc();
		
		if ( $row ) {
			array_push( $statRows, array(Text::_("COM_BIODIV_STATUS_TOT_SYSTEM"), $row['uploaded']) );
			array_push( $statRows, array(Text::_("COM_BIODIV_STATUS_TOT_CLASS"), $row['classified']) );
		}
		else {
			array_push( $statRows, array(Text::_("COM_BIODIV_STATUS_TOT_SYSTEM"), 0) );
			array_push( $statRows, array(Text::_("COM_BIODIV_STATUS_TOT_CLASS"), 0) );
		}
		
		$query = $db->getQuery(true);
		$query->select("person_id, num_classified")
			->from("LeagueTable")
			->order("num_classified desc");
		$db->setQuery($query);
		
		$leagueTable = $db->loadAssocList();
		
		$lTable = array_column($leagueTable, 'person_id');
		$tSpotters = count($lTable);
		
		$personId = Biodiv::userID();
		$userPos = array_search ( $personId, $lTable );
		
		if ( $userPos === False  ) {
			//print ( "Not in league table" );
			$userPos = count($lTable);
			$tSpotters += 1;
			
			array_push( $statRows, array(Text::_("COM_BIODIV_STATUS_NUM_YOU"), 0) );
		
		}
		else {
			
			array_push( $statRows, array(Text::_("COM_BIODIV_STATUS_NUM_YOU"), $leagueTable[$userPos]['num_classified']) );
		}
		
		array_push( $statRows, array(Text::_("COM_BIODIV_STATUS_TOT_SPOT"), $tSpotters) );
		
		array_push( $statRows, array(Text::_("COM_BIODIV_STATUS_YOU_CURR"), Biodiv::getOrdinal($userPos + 1)) );
		
		
		$endDate = date('Ymd', strtotime("last day of this month"));
		
		$query = $db->getQuery(true);
		$query->select("person_id, month_classified")
			->from("LeagueTableByMonth")
			->where("project_id = 0")
			->where("end_date = " . $endDate)
			->order("month_classified DESC");
		$db->setQuery($query);
		
		//error_log ( "getSpotterStatistics, this month query: " . $query->dump() );
		
		$thisMonth = $db->loadAssocList("person_id");
		
		$userThisMonthPos = array_search ( $personId, array_keys($thisMonth) );
		$thisMonthZero = true;
		
		if ( array_key_exists ( $personId, $thisMonth ) && $thisMonth[$personId]["month_classified"] > 0 ) {
			$thisMonthZero = false;
		};
		
		$prevMonthEnd = date('Ymd', strtotime("last day of last month"));
		
		$query = $db->getQuery(true);
		$query->select("person_id, month_classified")
			->from("LeagueTableByMonth")
			->where("project_id = 0")
			->where("end_date = " . $prevMonthEnd)
			->order("month_classified DESC");
		$db->setQuery($query);
		
		$prevMonth = $db->loadAssocList("person_id");
		
		$userPrevMonthPos = array_search ( $personId, array_keys($prevMonth) );
		
		$prevMonthZero = true;
		
		if ( array_key_exists ( $personId, $prevMonth ) && $prevMonth[$personId]["month_classified"] > 0 ) {
			$prevMonthZero = false;
		};
		
		
		if ( $prevMonthZero ) {
			array_push( $statRows, array(Text::_("COM_BIODIV_STATUS_PREV_MONTH"), '-') );
		}
		else {
			array_push( $statRows, array(Text::_("COM_BIODIV_STATUS_PREV_MONTH"), Biodiv::getOrdinal($userPrevMonthPos + 1)) );
		}
		if ( $thisMonthZero ) {
			array_push( $statRows, array(Text::_("COM_BIODIV_STATUS_THIS_MONTH"), '-') );
		}
		else {
			array_push( $statRows, array(Text::_("COM_BIODIV_STATUS_THIS_MONTH"), Biodiv::getOrdinal($userThisMonthPos + 1)) );
		}
			
		return $statRows;
	
	}	
	
	
	public static function getSpotterBriefStatistics () {
	
		// Get all the text snippets for status view
		//$translations = getTranslations("status");

		$db = db();
		
		$statRows = array();

		$query = $db->getQuery(true);
		$query->select("person_id, num_classified")
			->from("LeagueTable")
			->order("num_classified desc");
		$db->setQuery($query);
		
		$leagueTable = $db->loadAssocList();
		
		$lTable = array_column($leagueTable, 'person_id');
		$tSpotters = count($lTable);
		
		$personId = Biodiv::userID();
		$userPos = array_search ( $personId, $lTable );
		
		if ( $userPos === False  ) {
			//print ( "Not in league table" );
			$userPos = count($lTable);
			$tSpotters += 1;
			
			array_push( $statRows, array(Text::_("COM_BIODIV_STATUS_NUM_YOU"), 0) );
		
		}
		else {
			
			array_push( $statRows, array(Text::_("COM_BIODIV_STATUS_NUM_YOU"), $leagueTable[$userPos]['num_classified']) );
		}
		
		//array_push( $statRows, array(Text::_("COM_BIODIV_STATUS_YOU_CURR"), Biodiv::getOrdinal($userPos + 1)) );
		
		
		$endDate = date('Ymd', strtotime("last day of this month"));
		
		$query = $db->getQuery(true);
		$query->select("person_id, month_classified")
			->from("LeagueTableByMonth")
			->where("project_id = 0")
			->where("end_date = " . $endDate)
			->order("month_classified DESC");
		$db->setQuery($query);
		
		//error_log ( "getSpotterStatistics, this month query: " . $query->dump() );
		
		$thisMonth = $db->loadAssocList("person_id");
		
		$userThisMonthPos = array_search ( $personId, array_keys($thisMonth) );
		$thisMonthZero = true;
		
		if ( array_key_exists ( $personId, $thisMonth ) && $thisMonth[$personId]["month_classified"] > 0 ) {
			$thisMonthZero = false;
		};
		
		if ( $thisMonthZero ) {
			array_push( $statRows, array(Text::_("COM_BIODIV_STATUS_THIS_MONTH"), '-') );
		}
		else {
			array_push( $statRows, array(Text::_("COM_BIODIV_STATUS_THIS_MONTH"), Biodiv::getOrdinal($userThisMonthPos + 1)) );
		}
			
		return $statRows;
	
	}	
	
	
	public static function getTrapperStatistics () {
	
		// Get all the text snippets for trapperdash view
		//$translations = getTranslations("dashtrapper");

		$personId = Biodiv::userID();
		
		$db = db();
		
		$statRows = array();

		$query = $db->getQuery(true);
		$query->select("count(*) ")
			->from("Site");
		$db->setQuery($query); 
		$numSites = $db->loadResult();
		
		array_push( $statRows, array(Text::_("COM_BIODIV_DASHTRAPPER_TOT_SITES"), $numSites) );
		
		$query = $db->getQuery(true);
		$query->select("count(*) ")
			->from("Site")
			->where("person_id = " . $personId);
		$db->setQuery($query); 
		$numSites = $db->loadResult();
		
		array_push( $statRows, array(Text::_("COM_BIODIV_DASHTRAPPER_YOUR_SITES"), $numSites) );
		
		$query = $db->getQuery(true);
		$query->select("end_date, num_uploaded as uploaded, num_classified as classified ")
			->from("Statistics")
			->where("project_id = 0")
			->order("end_date DESC");
		$db->setQuery($query, 0, 1); // LIMIT 1
		$row = $db->loadAssoc();
		
		$numUploads = 0;
		if ( is_array($row) && array_key_exists('uploaded', $row) ) {
			$numUploads = $row['uploaded'];
		}
			
		array_push( $statRows, array(Text::_("COM_BIODIV_DASHTRAPPER_TOT_SEQ"), $numUploads) );
		
		$query = $db->getQuery(true);
		
		$endDate = date('Ymd', strtotime("last day of this month"));
		$query->select("sum(num_uploaded) ")
			->from("SiteStatistics SS")
			->innerJoin("Site S on S.site_id = SS.site_id and SS.end_date = " . $endDate)
			->where("S.person_id = " . $personId);
			
		$db->setQuery($query); 
		
		//error_log("Seq select query created: " . $query->dump());
		
		$mySeqs = $db->loadResult();
		
		$query = $db->getQuery(true);
		$query->select("sum(num_uploaded) ")
			->from("PrivateSiteStatistics SS")
			->innerJoin("Site S on S.site_id = SS.site_id and SS.end_date = " . $endDate)
			->where("S.person_id = " . $personId);
			
		$db->setQuery($query); 
		
		//error_log("Seq select query created: " . $query->dump());
		
		$myPrivateSeqs = $db->loadResult();
		
		
		array_push( $statRows, array(Text::_("COM_BIODIV_DASHTRAPPER_YOUR_SEQ"), $mySeqs+$myPrivateSeqs) );
		
			
		return $statRows;
	}	
	
	
	public static function getTrapperBriefStatistics () {
	
		// Get all the text snippets for trapperdash view
		//$translations = getTranslations("dashtrapper");

		$personId = Biodiv::userID();
		
		$db = db();
		
		$statRows = array();

		$query = $db->getQuery(true);
		$query->select("count(*) ")
			->from("Site")
			->where("person_id = " . $personId);
		$db->setQuery($query); 
		$numSites = $db->loadResult();
		
		array_push( $statRows, array(Text::_("COM_BIODIV_DASHTRAPPER_YOUR_SITES"), $numSites) );
		
		$query = $db->getQuery(true);
		
		$endDate = date('Ymd', strtotime("last day of this month"));
		$query->select("sum(num_uploaded) ")
			->from("SiteStatistics SS")
			->innerJoin("Site S on S.site_id = SS.site_id and SS.end_date = " . $endDate)
			->where("S.person_id = " . $personId);
			
		$db->setQuery($query); 
		
		//error_log("Seq select query created: " . $query->dump());
		
		$mySeqs = $db->loadResult();
		
		$query = $db->getQuery(true);
		$query->select("sum(num_uploaded) ")
			->from("PrivateSiteStatistics SS")
			->innerJoin("Site S on S.site_id = SS.site_id and SS.end_date = " . $endDate)
			->where("S.person_id = " . $personId);
			
		$db->setQuery($query); 
		
		//error_log("Seq select query created: " . $query->dump()); 
		
		$myPrivateSeqs = $db->loadResult();
		
		
		array_push( $statRows, array(Text::_("COM_BIODIV_DASHTRAPPER_YOUR_SEQ"), $mySeqs+$myPrivateSeqs) );
		
			
		return $statRows;
	}	
	
	
	// Calculate the number of sequences uploaded and the number of sequences with at least one classification for the project given (and children)
	// or for all projects.  Store the results in the Statistics table.  Overwrite a row if it already exists.
	public static function calculateStats ( $projectId = null, $end_date = null ) {
	
		$projects = Project::getProjectsWithThreshold ( $projectId );
		
		$endDatePlus1 = strtotime("first day of next month" );
		$endDate = date('Ymd', strtotime("last day of this month"));
		if ( $end_date ) {
			$endDatePlus1 = strtotime("+1 day", strtotime($end_date));
			$endDate = date('Ymd', strtotime($end_date));
		}
		
		$dateToUse = date('Ymd', $endDatePlus1);
		
		$isHistory = false;
		if ( $endDate <  date('Ymd') ) {
			$isHistory = true;
		}
		
		// Work through project by project as we don't want to include the children here.
		foreach ( $projects as $proj_id=>$project) {
			
			// Get fully classified threshold
			$threshold = $project['threshold'];
			
			self::calculateProjectStats ( $proj_id, $threshold, $endDate, $dateToUse, $isHistory );
			
		}
				
	}
	
	private static function calculateProjectStats ( $proj_id, $threshold, $endDate, $dateToUse, $isHistory ) {
		
		print "project_id = " . $proj_id . ", dateToUse = " . $dateToUse . "<br>";
			
		$db = db();
		
		$query = $db->getQuery(true);
		$query->select("distinct sequence_id from Photo P");
		$query->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id");
		$query->where("P.sequence_id > 0");
		$query->where("P.uploaded < " . $dateToUse );
		$query->where("PSM.project_id = " . $proj_id );
		$query->where("P.photo_id >= PSM.start_photo_id and PSM.end_photo_id is NULL"  );
		
		$query2 = $db->getQuery(true);
		$query2->select("distinct sequence_id from Photo P");
		$query2->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id");
		$query2->where("P.sequence_id > 0");
		$query2->where("P.uploaded < " . $dateToUse );
		$query2->where("PSM.project_id = " . $proj_id );
		$query2->where("P.photo_id >= PSM.start_photo_id and P.photo_id <= PSM.end_photo_id"  );
		
		$db->setQuery($query2);
		
		$q3 = $db->getQuery(true)
			 ->select('count(distinct a.sequence_id)')
			 ->from('(' . $query->union($query2) . ') a');
			 
		$db->setQuery($q3);

		$numLoaded = $db->loadResult();
	
		$query = $db->getQuery(true);
		$query->select("distinct sequence_id from Photo P");
		$query->innerJoin("Animal A on P.photo_id = A.photo_id");
		$query->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id");
		$query->where("A.species != 97");
		$query->where("A.timestamp < " . $dateToUse );
		$query->where("PSM.project_id = " . $proj_id );
		$query->where("P.photo_id >= PSM.start_photo_id and P.photo_id <= PSM.end_photo_id"  );
		$db->setQuery($query);
		
		$query2 = $db->getQuery(true);
		$query2->select("distinct sequence_id from Photo P");
		$query2->innerJoin("Animal A on P.photo_id = A.photo_id");
		$query2->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id");
		$query2->where("A.species != 97");
		$query2->where("A.timestamp < " . $dateToUse );
		$query2->where("PSM.project_id = " . $proj_id );
		$query2->where("P.photo_id >= PSM.start_photo_id and PSM.end_photo_id is NULL"  );
		$db->setQuery($query2);
		
		$q3 = $db->getQuery(true)
			 ->select('count(distinct a.sequence_id)')
			 ->from('(' . $query->union($query2) . ') a');
			 
		$db->setQuery($q3);
		
		//error_log ( "calculateStats, num classifications query: " . $query->dump() );

		$numClassified = $db->loadResult();
		
		// To calculate how many still queued for the date in question have to look at Classify table
		// BUT what if we archive...
		// If isHistory, try for the best we can do using Classify table.  If not, can just use status
		$numQueued = 0;
		if ( $isHistory ) {
			$query = $db->getQuery(true);
			$query->select("count(distinct P.sequence_id) from Photo P")
				->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id")
				->innerJoin("Classify C on C.photo_id = P.photo_id" )
				->where("P.sequence_num = 1 and P.uploaded < " . $dateToUse )
				->where("PSM.project_id = " . $proj_id )
				->where("P.photo_id >= PSM.start_photo_id and ( P.photo_id <= PSM.end_photo_id or PSM.end_photo_id is NULL)"  )
				->where("P.photo_id not in (select photo_id from Classify where timestamp < " . $dateToUse . ")" );
			
			$db->setQuery($query);
			//error_log ( "calculateStats, num queued history query: " . $query->dump() );

			$numQueued = $db->loadResult();
		}
		else {
			$query = $db->getQuery(true);
			$query->select("count(distinct P.sequence_id) from Photo P")
				->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id")
				->where("P.status = 0")
				->where("P.contains_human = 0")
				->where("PSM.project_id = " . $proj_id )
				->where("P.sequence_num = 1")
				->where("P.photo_id >= PSM.start_photo_id and ( P.photo_id <= PSM.end_photo_id or PSM.end_photo_id is NULL)"  );
			$db->setQuery($query);
			
			//error_log ( "calculateStats, num queued query: " . $query->dump() );

			$numQueued = $db->loadResult();
		}
		
		
		// To calculate how many screened out on the date in question check Classify table?
		// Screened out is:
		// - a human flag with no Animal classification
		// - STATUS_NOTHING => Megadetector signalled Nothing
		// - STATUS_UNCLASSIFIED and photo_id before Megadetector introduced
		// 
		$numScreened = 0;
		$screenedOutStr = "" . Sequence::STATUS_UNCLASSIFIED .", " . Sequence::STATUS_CALIBRATION_POLE . ", " . Sequence::STATUS_NOTHING;
		if ( $isHistory ) {
			
			// Rely on current status and check Classify table for date of Megadetector classify or CAI if older
			
			$query = $db->getQuery(true);
			$query->select("P.sequence_id from Photo P")
				->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id")
				->innerJoin("Classify C on C.sequence_id = P.sequence_id and C.timestamp < " . $dateToUse )
				->where("C.origin = " . $db->quote('CAI') . " and C.photo_id = P.photo_id and C.classify_id <= 6176168")
				->where("P.sequence_num = 1 and P.status in (" . $screenedOutStr . ")" )
				->where("PSM.project_id = " . $proj_id )
				->where("P.photo_id >= PSM.start_photo_id and ( P.photo_id <= PSM.end_photo_id or PSM.end_photo_id is NULL)"  );
			
			$query2 = $db->getQuery(true);
			$query2->select("P.sequence_id from Photo P")
				->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id")
				->innerJoin("Classify C on C.sequence_id = P.sequence_id and C.timestamp < " . $dateToUse )
				->where("C.origin = " . $db->quote('MEGA') . " and C.photo_id = P.photo_id and C.classify_id > 6176168")
				->where("P.sequence_num = 1 and P.status in (" . $screenedOutStr . ")" )
				->where("PSM.project_id = " . $proj_id )
				->where("P.photo_id >= PSM.start_photo_id and ( P.photo_id <= PSM.end_photo_id or PSM.end_photo_id is NULL)"  );
			
			$query3 = $db->getQuery(true);
			$query3->select("P.sequence_id from Photo P")
				->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id")   
				->innerJoin("Classify C on C.sequence_id = P.sequence_id and C.timestamp < " . $dateToUse )
				->where("P.sequence_num = 1 and P.contains_human = 1 and status = " .  Sequence::STATUS_UNAVAILABLE)
				->where("PSM.project_id = " . $proj_id )
				->where("P.photo_id >= PSM.start_photo_id and ( P.photo_id <= PSM.end_photo_id or PSM.end_photo_id is NULL)"  )
				->where("P.photo_id not in (select photo_id from Animal)");
			
			
			$q3 = $db->getQuery(true)
			 ->select('count(distinct a.sequence_id)')
			 ->from('(' . $query->union($query2->union($query3)) . ') a');
			 
			$db->setQuery($q3);
			
			//error_log ( "calculateStats, num screened history query: " . $query->dump() );

			$numScreened = $db->loadResult();
		
		}		
		
		// Count part classified sequences (available and have an animal)
		$numPartClassified = 0;
		if ( $isHistory ) {
			
			$query = $db->getQuery(true);
			
			if ( $dateToUse < 20250602 ) {
				$query->select("sequence_id from Photo P")
					->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id")
					->where("P.status in (1, 18)")
					->where("P.contains_human = 0")
					->where("PSM.project_id = " . $proj_id )
					->where("P.sequence_num = 1")
					->where("P.photo_id >= PSM.start_photo_id and ( P.photo_id <= PSM.end_photo_id or PSM.end_photo_id is NULL)"  )
					->where("P.photo_id in (select photo_id from Animal where species!=97 and timestamp < " . $dateToUse . ")");
			}
			else {
				$query->select("sequence_id from Photo P")
					->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id")
					->where("P.status = 1")
					->where("P.contains_human = 0")
					->where("PSM.project_id = " . $proj_id )
					->where("P.sequence_num = 1")
					->where("P.photo_id >= PSM.start_photo_id and ( P.photo_id <= PSM.end_photo_id or PSM.end_photo_id is NULL)"  )
					->where("P.photo_id in (select photo_id from Animal where species!=97 and timestamp < " . $dateToUse . ")");
			
			}	
			$q3 = $db->getQuery(true)
			 ->select('count(distinct a.sequence_id)')
			 ->from('(' . $query . ') a');
			 
			$db->setQuery($q3);
			
			//error_log ( "calculateStats, num screened classed query: " . $query->dump() );

			$numPartClassified = $db->loadResult();

		
		}
		else {
			$query = $db->getQuery(true);
			$query->select("sequence_id from Photo P")
				->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id")
				->where("P.status = 1")
				->where("P.contains_human = 0")
				->where("PSM.project_id = " . $proj_id )
				->where("P.sequence_num = 1")
				->where("P.photo_id >= PSM.start_photo_id and ( P.photo_id <= PSM.end_photo_id or PSM.end_photo_id is NULL)"  )
				->where("P.photo_id in (select photo_id from Animal where species!=97)");
			
			$q3 = $db->getQuery(true)
			 ->select('count(distinct a.sequence_id)')
			 ->from('(' . $query . ') a');
			 
			$db->setQuery($q3);
			
			// error_log ( "calculateStats, num screened classed query: " . $query->dump() );

			$numPartClassified = $db->loadResult();
		}
		
		
		// Count unclassified sequences (available and no animal)
		$numUnclassified = 0;
		if ( !$isHistory ) {
			
			$query = $db->getQuery(true);
			$query->select("sequence_id from Photo P")
				->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id")
				->where("P.status = 1")
				->where("P.contains_human = 0")
				->where("PSM.project_id = " . $proj_id )
				->where("P.sequence_num = 1")
				->where("P.photo_id >= PSM.start_photo_id and ( P.photo_id <= PSM.end_photo_id or PSM.end_photo_id is NULL)"  )
				->where("P.photo_id not in (select photo_id from Animal where species!=97)");
			
			$q3 = $db->getQuery(true)
			 ->select('count(distinct a.sequence_id)')
			 ->from('(' . $query . ') a');
			 
			$db->setQuery($q3);
			
			//error_log ( "calculateStats, num screened classed query: " . $query->dump() );

			$numUnclassified = $db->loadResult();
		}
		
		
		// Calculate those classified to project threshold 
		if ( $isHistory ) {
			
			// This reflects that full classification started to be tracked in June 2025
			if ( $dateToUse > "20250630" ) {
				
				$fullyClassified = Project::getFullyClassified ( $proj_id, $threshold, $dateToUse ); 
				$numFullyClassified = count($fullyClassified);
			}
			else {
				$numFullyClassified = 0;
			}
		}
		else {
			$query = $db->getQuery(true);
			
			$query->select("count(distinct sequence_id) from Photo P")
				->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id")
				->where("P.status = 18")
				->where("PSM.project_id = " . $proj_id )
				->where("P.sequence_num = 1")
				->where("P.photo_id >= PSM.start_photo_id and ( P.photo_id <= PSM.end_photo_id or PSM.end_photo_id is NULL)"  );
			
			$db->setQuery($query);
						
			//error_log ( "calculateStats, num classified to threshold query: " . $query->dump() );
			
			$numFullyClassified = $db->loadResult();
		}
		
		if ( $isHistory ) {
			$numUnclassified = $numLoaded - $numQueued - $numFullyClassified - $numPartClassified - $numScreened;
		}
		else {
			$numScreened = $numLoaded - $numQueued - $numFullyClassified - $numPartClassified - $numUnclassified;
		}
		
		print ("For project $proj_id, numLoaded = " . $numLoaded . ", numClassified = " . $numClassified . 
				", numQueued = " . $numQueued . ", numScreened = " . $numScreened . 
				", numFullyClassified = " . $numFullyClassified. ", numPartClassified = " . $numPartClassified . 
				", numUnclassified = " . $numUnclassified );
	
		// Now update or insert - this would all be better with a stored procedure
		$query = $db->getQuery(true);
		$query->select("project_id, end_date from Statistics")
			->where("project_id = " . $proj_id )
			->where("end_date = '" . $endDate . "'" );
		$db->setQuery($query);
		$resultRow = $db->loadRowList();
		
		if ( count($resultRow) > 0 ) {
			print "<br>Existing entry for project $proj_id, end_date $endDate - updating<br/>";
			
			$query = $db->getQuery(true);
			$query->update("Statistics");
			$query->set("num_uploaded = " . $numLoaded . ", num_classified = " . $numClassified .
				", queued = " . $numQueued . ", screened_out = " . $numScreened . ", fully_classified = " . $numFullyClassified .
				", part_classified = " . $numPartClassified . ", unclassified = " . $numUnclassified );
			$query->where("project_id = " . $proj_id );
			$query->where("end_date = '" . $endDate . "'" );
			$db->setQuery($query);
			$result = $db->execute();
		}
		else {
			print "<br>New entry in statistics for project $proj_id, end_date $endDate<br/>";
			
			$query = $db->getQuery(true);
			$query->insert("Statistics");
			$query->columns($db->quoteName(array('project_id', 'end_date', 'num_uploaded', 'num_classified', 'queued', 
							'screened_out', 'fully_classified', 'part_classified', 'unclassified')));
			$query->values("" . $proj_id . ", '" . $endDate . "', " . $numLoaded . ", " . $numClassified . ", " . $numQueued .
							", " . $numScreened . ", " . $numFullyClassified . ", " . $numPartClassified .
							", " . $numUnclassified );
			$db->setQuery($query);
			$result = $db->execute();
		}
	}

	// Recalculate statistics for all projects
	// Use with care this will overwrite existing figures.
	public static function calculateStatsHistory ( $project_id = null, $num_months = null) {
		
		$endDate = strtotime("last day of this month");
		$endDatePlus1 = strtotime("first day of next month" );

		$datesArray = array();
		
		// Default to 3 years of data.
		$numDisplayedMonths = 39;
		if ( $num_months ) {
			$numDisplayedMonths = $num_months + 3;
		}
	  
		for ( $i=$numDisplayedMonths; $i>=0; $i-- ) {
			$minusMonths = "-" . $i . " months";
			$firstOfMonth = strtotime($minusMonths, $endDatePlus1);
			self::calculateStats($project_id, date('Ymd', strtotime("-1 day", $firstOfMonth)) );
		}
	}

	public static function calculateStatsTotals ( $end_date = null ) {
		
		$db = db();
		
		$endDatePlus1 = strtotime("first day of next month" );
		$endDate = date('Ymd', strtotime("last day of this month"));
		if ( $end_date ) {
			$endDatePlus1 = strtotime("+1 day", strtotime($end_date));
			$endDate = date('Ymd', strtotime($end_date));
		}
		
		$dateToUse = date('Ymd', $endDatePlus1);
		
		$isHistory = false;
		if ( $endDate <  date('Ymd') ) {
			$isHistory = true;
		}
		
		$query = $db->getQuery(true);
		$query->select("count(distinct sequence_id) from Photo");
		$query->where("sequence_id > 0");
		$query->where("uploaded < " . $dateToUse );
		$db->setQuery($query);
		$numLoaded = $db->loadResult();
		
		$query = $db->getQuery(true);
		$query->select("count(distinct sequence_id) from Photo P");
		$query->innerJoin("Animal A on P.photo_id = A.photo_id");
		$query->where("A.species != 97");
		$query->where("P.sequence_id > 0");
		$query->where("A.timestamp < " . $dateToUse );
		$db->setQuery($query);
		
		$numClassified = $db->loadResult();
		
		$numQueued = 0;
		if ( $isHistory ) {
			$query = $db->getQuery(true);
			$query->select("count(distinct P.sequence_id) from Photo P")
				->innerJoin("Classify C on C.photo_id = P.photo_id" )
				->where("P.sequence_num = 1 and P.uploaded < " . $dateToUse )
				->where("P.photo_id not in (select photo_id from Classify where timestamp < " . $dateToUse . ")" );
			
			$db->setQuery($query);
			//error_log ( "calculateStats, num queued history query: " . $query->dump() );

			$numQueued = $db->loadResult();
		}
		else {
			$query = $db->getQuery(true);
			$query->select("count(distinct P.sequence_id) from Photo P")
				->where("P.status = 0")
				->where("P.contains_human = 0")
				->where("P.sequence_num = 1");
			$db->setQuery($query);
			
			//error_log ( "calculateStats, num queued query: " . $query->dump() );

			$numQueued = $db->loadResult();
		}
		
		$numScreened = 0;
		$screenedOutStr = "" . Sequence::STATUS_UNCLASSIFIED .", " . Sequence::STATUS_CALIBRATION_POLE . ", " . Sequence::STATUS_NOTHING;
		if ( $isHistory ) {
			
			// Rely on current status and check Classify table for date of Megadetector classify or CAI if older
			
			$query = $db->getQuery(true);
			$query->select("P.sequence_id from Photo P")
				->innerJoin("Classify C on C.sequence_id = P.sequence_id and C.timestamp < " . $dateToUse )
				->where("C.origin = " . $db->quote('CAI') . " and C.photo_id = P.photo_id and C.classify_id <= 6176168")
				->where("P.sequence_num = 1 and P.status in (" . $screenedOutStr . ")" );
			
			$query2 = $db->getQuery(true);
			$query2->select("P.sequence_id from Photo P")
				->innerJoin("Classify C on C.sequence_id = P.sequence_id and C.timestamp < " . $dateToUse )
				->where("C.origin = " . $db->quote('MEGA') . " and C.photo_id = P.photo_id and C.classify_id > 6176168")
				->where("P.sequence_num = 1 and P.status in (" . $screenedOutStr . ")" );
			
			$query3 = $db->getQuery(true);
			$query3->select("P.sequence_id from Photo P")
				->innerJoin("Classify C on C.sequence_id = P.sequence_id and C.timestamp < " . $dateToUse )
				->where("P.sequence_num = 1 and P.contains_human = 1 and status = " .  Sequence::STATUS_UNAVAILABLE)
				->where("P.photo_id not in (select photo_id from Animal)");
			
			
			$q3 = $db->getQuery(true)
			 ->select('count(distinct a.sequence_id)')
			 ->from('(' . $query->union($query2->union($query3)) . ') a');
			 
			$db->setQuery($q3);
			
			//error_log ( "calculateStats, num screened history query: " . $query->dump() );

			$numScreened = $db->loadResult();
		
		}
		// else {
			// $query = $db->getQuery(true);
			// $query->select("sequence_id from Photo P")
				// ->where("P.status not in (1,18)")
				// ->where("P.sequence_num = 1");
			
			// $query2 = $db->getQuery(true);
			// $query2->select("sequence_id from Photo P")
				// ->leftJoin("Animal A on P.photo_id = A.photo_id")
				// ->where("A.photo_id is NULL" )
				// ->where("P.status = 0")
				// ->where("P.contains_human = 1")
				// ->where("P.sequence_num = 1");
			
			
			// $q3 = $db->getQuery(true)
			 // ->select('count(distinct a.sequence_id)')
			 // ->from('(' . $query->union($query2) . ') a');
			 
			// $db->setQuery($q3);
			
			// //error_log ( "calculateStats, num classifications query: " . $query->dump() );

			// $numScreened = $db->loadResult();
		// }
		
		
		$numPartClassified = 0;
		if ( $isHistory ) {
			
			$query = $db->getQuery(true);
			
			if ( $dateToUse < 20250602 ) {
				$query->select("sequence_id from Photo P")
					->where("P.status in (1, 18)")
					->where("P.contains_human = 0")
					->where("P.sequence_num = 1")
					->where("P.photo_id in (select photo_id from Animal where species!=97 and timestamp < " . $dateToUse . ")");
			}
			else {
				$query->select("sequence_id from Photo P")
					->where("P.status = 1")
					->where("P.contains_human = 0")
					->where("P.sequence_num = 1")
					->where("P.photo_id in (select photo_id from Animal where species!=97 and timestamp < " . $dateToUse . ")");
			
			}	
			$q3 = $db->getQuery(true)
			 ->select('count(distinct a.sequence_id)')
			 ->from('(' . $query . ') a');
			 
			$db->setQuery($q3);
			
			//error_log ( "calculateStats, num screened classed query: " . $query->dump() );

			$numPartClassified = $db->loadResult();

		
		}
		else {
			$query = $db->getQuery(true);
			$query->select("sequence_id from Photo P")
				->where("P.status = 1")
				->where("P.contains_human = 0")
				->where("P.sequence_num = 1")
				->where("P.photo_id in (select photo_id from Animal where species!=97)");
			
			$q3 = $db->getQuery(true)
			 ->select('count(distinct a.sequence_id)')
			 ->from('(' . $query . ') a');
			 
			$db->setQuery($q3);
			
			//error_log ( "calculateStats, num screened classed query: " . $query->dump() );

			$numPartClassified = $db->loadResult();
		}
		
		
		// Count unclassified sequences (available and no animal)
		$numUnclassified = 0;
		if ( !$isHistory ) {

			$query = $db->getQuery(true);
			$query->select("sequence_id from Photo P")
				->where("P.status = 1")
				->where("P.contains_human = 0")
				->where("P.sequence_num = 1")
				->where("P.photo_id not in (select photo_id from Animal where species!=97)");
			
			$q3 = $db->getQuery(true)
			 ->select('count(distinct a.sequence_id)')
			 ->from('(' . $query . ') a');
			 
			$db->setQuery($q3);
			
			//error_log ( "calculateStats, num screened classed query: " . $query->dump() );

			$numUnclassified = $db->loadResult();
		}
		
		
		
		// Calculate those classified to project threshold 
		if ( $isHistory ) {
			
			// This reflects that full classification started to be tracked in June 2025
			if ( $dateToUse > "20250630" ) {
				
				$fullyClassified = Project::getFullyClassified ( $proj_id, $threshold, $dateToUse ); 
				$numFullyClassified = count($fullyClassified);
			}
			else {
				$numFullyClassified = 0;
			}
		}
		else {
			$query = $db->getQuery(true);
			
			$query->select("count(*) from Photo P")
				->where("P.status = 18")
				->where("P.sequence_num = 1");
			
			$db->setQuery($query);
						
			//error_log ( "calculateStats, num classified to threshold query: " . $query->dump() );
			
			$numFullyClassified = $db->loadResult();
		}
		
		
		if ( $isHistory ) {
			$numUnclassified = $numLoaded - $numQueued - $numFullyClassified - $numPartClassified - $numScreened;
		}
		else {
			$numScreened = $numLoaded - $numQueued - $numFullyClassified - $numPartClassified - $numUnclassified;
		}
		
		print ("For project totals, numLoaded = " . $numLoaded . ", numClassified = " . $numClassified . 
				", numQueued = " . $numQueued . ", numScreened = " . $numScreened . 
				", numFullyClassified = " . $numFullyClassified. ", numPartClassified = " . $numPartClassified . 
				", numUnclassified = " . $numUnclassified );
			
		
		// Now update or insert - this would all be better with a stored procedure
		$query = $db->getQuery(true);
		$query->select("project_id, end_date from Statistics")
			->where("project_id = 0" )
			->where("end_date = '" . $endDate . "'" );
		$db->setQuery($query);
		$resultRow = $db->loadRowList();
			
		if ( count($resultRow) > 0 ) {
			print "<br>Existing entry for project 0 (ie total) , end_date $endDate - updating<br/>";
				
			$query = $db->getQuery(true);
			$query->update("Statistics");
			$query->set("num_uploaded = " . $numLoaded . ", num_classified = " . $numClassified .
						", queued = " . $numQueued . ", screened_out = " . $numScreened . 
						", fully_classified = " . $numFullyClassified . 
						", part_classified = " . $numPartClassified . 
						", unclassified = " . $numUnclassified);
			$query->where("project_id = 0" );
			$query->where("end_date = '" . $endDate . "'" );
			$db->setQuery($query);
			$result = $db->execute();
		}
		else {
			print "<br>New entry in statistics for project 0 (ie total) , end_date $endDate<br/>";
				
			$query = $db->getQuery(true);
			$query->insert("Statistics");
			$query->columns($db->quoteName(array('project_id', 'end_date', 'num_uploaded', 'num_classified', 
							'queued', 'screened_out', 'fully_classified', 'part_classified', 'unclassified')));
			$query->values("0, '" . $endDate . "', " . $numLoaded . ", " . $numClassified . ", " . $numQueued . ", " . 
							$numScreened . ", " . $numFullyClassified . ", " . $numPartClassified .
							", " . $numUnclassified); 
							
			//error_log ( "calculateStatsTotals, new entry sql: " . $query->dump() );
			
			$db->setQuery($query);
			$result = $db->execute();
		}

	}

	public static function calculateLeagueTable () {
		
		$db = db();
		
		$query = $db->getQuery(true);
		$query->select("A.person_id as person_id, count(distinct P.sequence_id) as num_sequences")
			->from("Animal A use index (classification_id)")
			->innerJoin("Photo P on P.photo_id = A.photo_id")
			->where("A.species != 97")
			->group("A.person_id ")
			->order("count(distinct P.sequence_id) desc");
		$db->setQuery($query);
		
		$leagueTable = $db->loadAssocList('person_id','num_sequences');
		
		// Work through person by person.
		foreach ( $leagueTable as $person_id=>$num_sequences ) {
			
			print "person_id = " . $person_id . ", num_sequences = " . $num_sequences . "<br>";
			
			// Now update or insert - this would all be better with a stored procedure
			$query = $db->getQuery(true);
			$query->select("person_id, num_classified from LeagueTable")
				->where("person_id = " . $person_id );
			$db->setQuery($query);
			$resultRow = $db->loadRowList();
			
			if ( count($resultRow) > 0 ) {
				print "Existing entry for person_id $person_id - updating<br/>";
				
				$query = $db->getQuery(true);
				$query->update("LeagueTable");
				$query->set("num_classified = " . $num_sequences . ", timestamp = CURRENT_TIMESTAMP" );
				$query->where("person_id = " . $person_id );
				$db->setQuery($query);
				$result = $db->execute();
			}
			else {
				print "New entry in LeagueTable for person_id $person_id<br/>";
				
				$query = $db->getQuery(true);
				$query->insert("LeagueTable");
				$query->columns($db->quoteName(array('person_id', 'num_classified')));
				$query->values("" . $person_id . ", " . $num_sequences );
				$db->setQuery($query);
				$result = $db->execute();
			}
		}
	}

	// Recalculate statistics for all projects
	// Use with care this will overwrite existing figures.
	public static function calculateLeagueTableByMonthHistory ( $project_id = null, $num_months = null) {
		
		$endDate = strtotime("last day of this month");
		$endDatePlus1 = strtotime("first day of next month" );

		$datesArray = array();
		
		// Default to 3 months of data.
		$numDisplayedMonths = 4;
		if ( $num_months ) {
			$numDisplayedMonths = $num_months + 1;
		}
	  
		for ( $i=$numDisplayedMonths; $i>=0; $i-- ) {
			$minusMonths = "-" . $i . " months";
			$firstOfMonth = strtotime($minusMonths, $endDatePlus1);
			self::calculateLeagueTableByMonth ( $project_id, date('Ymd', strtotime("-1 day", $firstOfMonth)) );
		}
	}



	// Calculate the classification and upload contributions for the project given 
	// or for all projects.  Store the results in the LeagueTableByMonth table.  Overwrite a row if it already exists.
	public static function calculateLeagueTableByMonth ( $project_id = null, $end_date = null ) {
		
		$db = db();
		
		$projects = array();
		$project_ids = array();
		
		if ( $project_id > 0 ) {
			$projects = Project::getSubProjectsById( $project_id );	
			$project_ids = array_keys($projects);
		}
		else {
			if ( $project_id === null ) {
				$query = $db->getQuery(true);
				$query->select("DISTINCT P.project_id, P.project_prettyname from Project P");
				$db->setQuery($query);
				$projects = $db->loadAssocList('project_id');
				$project_ids = array_keys($projects);
			}
			$project_ids[] = 0;
		}
		

		//error_log ( "CalculateLeagueTableByMonth, projects list: " . print_r ($project_ids, true) );
		//print ( "CalculateLeagueTableByMonth, projects list: " . print_r ($project_ids, true) );
		
		// Get users to count for classifications and uploads.
		$query = $db->getQuery(true);
		$query->select("distinct person_id from Animal order by person_id");
		$db->setQuery($query);
		$animalUsers = $db->loadColumn();
		
		$query = $db->getQuery(true);
		$query->select("distinct person_id from Upload order by person_id");
		$db->setQuery($query);
		$uploadUsers = $db->loadColumn();
		
		// Contributing users are made up of all the above
		$contributers = array_unique ( array_merge ( $animalUsers, $uploadUsers ) );
		$personStr = implode ( ',', $contributers );
		
		$endDatePlus1 = strtotime("first day of next month" );
		$endDate = date('Ymd', strtotime("last day of this month"));
		$prevMonthEndTime = strtotime("last day of last month" );
		$prevMonthEnd = date('Ymd', $prevMonthEndTime);
		
		if ( $end_date ) {
			$endDatePlus1 = strtotime("+1 day", strtotime($end_date));
			$endDate = date('Ymd', strtotime($end_date));  
			
			$firstDay = strtotime( date('Y-m-d', $endDatePlus1) . ' - 1 month');
			//print ( "First day of month: " . date('Ymd', $firstDay) . "<br>" );
			
			$prevMonthEndTime = strtotime("-1 day", $firstDay);     
			//print ( "Day before that: " . date('Ymd', $prevMonthEndTime) . "<br>" );
			
			$prevMonthEnd = date('Ymd', $prevMonthEndTime);
		}
		
		$dateToUse = date('Ymd', $endDatePlus1);
		
		
		print ( "Month end = " . $endDate . " Prev month end = " . $prevMonthEnd . "<br>" );
		
		// Work through project by project as we don't want to include the children here.
		foreach ( $project_ids as $proj_id ) {
			
			print "project_id = " . $proj_id . ", dateToUse = " . $dateToUse . "<br>";
			
			foreach ( $contributers as $personId ) {
			
				if ( $proj_id == 0 ) {
					$query = $db->getQuery(true);
					$query->select("count(distinct sequence_id) from Photo P")
						->where("P.sequence_id > 0")
						->where("P.person_id = " . $personId )
						->where("P.uploaded < " . $dateToUse );
					
					
					$db->setQuery($query);
					
					//error_log ( "calculateLeagueTableByMonth, user uploads query: " . $query->dump() );
					
					$numUp = $db->loadResult();
					
					$query = $db->getQuery(true);
					$query->select("count(distinct A.photo_id) from Animal A")
						->innerJoin("Photo P on A.photo_id = P.photo_id")
						->where("A.person_id = " . $personId )
						->where("A.timestamp < " . $dateToUse );
					
					
					$db->setQuery($query);
					
					//error_log ( "calculateLeagueTableByMonth, user animals query: " . $query->dump() );
					
					$numCl = $db->loadResult();	
				}
				else {
				
					$query = $db->getQuery(true);
					$query->select("count(distinct sequence_id) from Photo P")
						->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id")
						->where("P.sequence_id > 0")
						->where("P.person_id = " . $personId )
						->where("P.uploaded < " . $dateToUse )
						->where("PSM.project_id = " . $proj_id )
						->where("P.photo_id >= PSM.start_photo_id and (PSM.end_photo_id is NULL or P.photo_id <= PSM.end_photo_id)"  );
					
					
					$db->setQuery($query);
					
					//error_log ( "calculateLeagueTableByMonth, user uploads query: " . $query->dump() );
					
					$numUp = $db->loadResult();
					
					$query = $db->getQuery(true);
					$query->select("count(distinct A.photo_id) from Animal A")
						->innerJoin("Photo P on A.photo_id = P.photo_id")
						->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id")
						->where("A.person_id = " . $personId )
						->where("A.timestamp < " . $dateToUse )
						->where("PSM.project_id = " . $proj_id )
						->where("P.photo_id >= PSM.start_photo_id and (PSM.end_photo_id is NULL or P.photo_id <= PSM.end_photo_id)"  );
					
					
					$db->setQuery($query);
					
					$numCl = $db->loadResult();
				}
					
				print ( "<br/>Checking project $proj_id, person $personId <br/>" );
			
				
				// Only insert data if the user has participated in this project
				if ( $numUp > 0 or $numCl > 0 ) {
					
					print ( "Person: $personId, Num uploaded: $numUp, num classified: $numCl <br/>" );
		
					// Any data for previous month 
					$query = $db->getQuery(true);
					$query->select("total_uploaded, total_classified from LeagueTableByMonth")
						->where("project_id = " . $proj_id )
						->where("person_id = " . $personId )
						->where("end_date = '" . $prevMonthEnd . "'" );
					$db->setQuery($query);
					
					$prevMonthResults = $db->loadAssocList();
					
					$prevUpld = 0;
					$prevClassd = 0;
					if ( count($prevMonthResults) > 0 ) {
						$prevUpld = $prevMonthResults[0]["total_uploaded"];
						$prevClassd = $prevMonthResults[0]["total_classified"];
					}
					$monthlyClassified = $numCl - $prevClassd;
					$monthlyUploaded = $numUp - $prevUpld;
					
					print ( "Person: $personId, project: $proj_id, monthly classd: $monthlyClassified, monthlyUploaded: $monthlyUploaded <br/>" );
					
					// Update or insert
					$query = $db->getQuery(true);
					$query->select("project_id, end_date from LeagueTableByMonth")
						->where("project_id = " . $proj_id )
						->where("person_id = " . $personId )
						->where("end_date = '" . $endDate . "'" );
					$db->setQuery($query);
					$resultRow = $db->loadRowList();
					
					if ( count($resultRow) > 0 ) {
						print "Existing entry for user $personId, project $proj_id, end_date $endDate - updating<br/>";
						
						$query = $db->getQuery(true);
						$query->update("LeagueTableByMonth");
						$query->set("month_uploaded = " . $monthlyUploaded . ", month_classified = " . $monthlyClassified . ", total_uploaded = " . $numUp . ", total_classified = " . $numCl . ", timestamp = CURRENT_TIMESTAMP");
						$query->where("project_id = " . $proj_id );
						$query->where("person_id = " . $personId );
						$query->where("end_date = '" . $endDate . "'" );
						$db->setQuery($query);
						$result = $db->execute();
					}
					else {
						print "New entry in LeagueTableByMonth for user $personId, project $proj_id, end_date $endDate<br/>";
						
						$query = $db->getQuery(true);
						$query->insert("LeagueTableByMonth");
						$query->columns($db->quoteName(array('project_id', 'person_id', 'end_date', 'month_uploaded', 'month_classified', 'total_uploaded', 'total_classified')));
						$query->values("" . $proj_id . ", " . $personId . ", '" . $endDate . "', " . $monthlyUploaded . ", " . $monthlyClassified . ", " . $numUp . ", " . $numCl );
						$db->setQuery($query);
						$result = $db->execute();
					}
				}
			}
		}
	}

	// Recalculate UserTestStatistics
	// Use with care this will overwrite existing figures.
	public static function calculateUserTestStatisticsHistory ( $num_months = null) {
		
		$endDate = strtotime("last day of this month");
		$endDatePlus1 = strtotime("first day of next month" );

		$datesArray = array();
		
		// Default to 3 months of data.
		$numDisplayedMonths = 4;
		if ( $num_months ) {
			$numDisplayedMonths = $num_months + 1;
		}
	  
		for ( $i=$numDisplayedMonths; $i>=0; $i-- ) {
			$minusMonths = "-" . $i . " months";
			$firstOfMonth = strtotime($minusMonths, $endDatePlus1);
			self::calculateUserTestStatistics ( date('Ymd', strtotime("-1 day", $firstOfMonth)) );
		}
	}



	// Calculate the classification and upload contributions for the project given 
	// or for all projects.  Store the results in the LeagueTableByMonth table.  Overwrite a row if it already exists.
	public static function calculateUserTestStatistics ( $end_date = null ) {
		
		$db = db();
		
		$endDatePlus1 = strtotime("first day of next month" );
		$endDate = date('Ymd', strtotime("last day of this month"));
		$prevMonthEndTime = strtotime("last day of last month" );
		$prevMonthEnd = date('Ymd', $prevMonthEndTime);
		
		if ( $end_date ) {
			$endDatePlus1 = strtotime("+1 day", strtotime($end_date));
			$endDate = date('Ymd', strtotime($end_date));  
			
			$firstDay = strtotime( date('Y-m-d', $endDatePlus1) . ' - 1 month');
			//print ( "First day of month: " . date('Ymd', $firstDay) . "<br>" );
			
			$prevMonthEndTime = strtotime("-1 day", $firstDay);     
			//print ( "Day before that: " . date('Ymd', $prevMonthEndTime) . "<br>" );
			
			$prevMonthEnd = date('Ymd', $prevMonthEndTime);
		}
		
		$dateToUse = date('Ymd', $endDatePlus1);
		
		
		print ( "Month end = " . $endDate . " Prev month end = " . $prevMonthEnd . "<br>" );
		
		print "DateToUse = " . $dateToUse . "<br>";
		
		$query = $db->getQuery(true);
		$query->select("UT.person_id as person_id, count(*) as num_tests, ROUND(AVG(UT.score)) as av_score from UserTest UT")
			->where("UT.timestamp < " . $dateToUse )
			->group("UT.person_id");
		
		$db->setQuery($query);
		
		//error_log ( "calculateLeagueTableByMonth, user uploads query: " . $query->dump() );
		
		$userTotals = $db->loadAssocList("person_id");
		
		foreach ( $userTotals as $userTotal ) {
			
			$personId = $userTotal['person_id'];
			$totalTests = $userTotal['num_tests'];
			$totalAvg = $userTotal['av_score'];
			
			print ( "Person: $personId, Total tests: $totalTests, average score: $totalAvg % <br/>" );

			// Any data for previous month 
			$query = $db->getQuery(true);
			$query->select("total_tests, total_score from UserTestStatistics")
				->where("person_id = " . $personId )
				->where("end_date = '" . $prevMonthEnd . "'" );
			$db->setQuery($query);
			
			//error_log ( "calculateUserTests, prev month query: " . $query->dump() );
			
			$prevMonthResults = $db->loadAssocList();
				
			$prevTotalTests = 0;
			$prevAvg = 0;
			
			if ( count($prevMonthResults) > 0 ) {
				$prevTotalTests = $prevMonthResults[0]["total_tests"];
				$prevAvg = $prevMonthResults[0]["total_score"];
			}
			
			print ( "Person: $personId, prev total: $prevTotalTests, prev average: $prevAvg <br/>" );
			
			$monthlyTotal = $totalTests - $prevTotalTests;
			
			if ( $monthlyTotal > 0 ) {
				$monthlyAvg = round(($totalAvg*$totalTests - $prevAvg*$prevTotalTests)/$monthlyTotal);
			}
			else {
				$monthlyAvg = 0;
			}
			
			print ( "Person: $personId, monthly total: $monthlyTotal, monthly average: $monthlyAvg <br/>" );
			
			// Update or insert
			$query = $db->getQuery(true);
			$query->select("person_id, end_date from UserTestStatistics")
				->where("person_id = " . $personId )
				->where("end_date = '" . $endDate . "'" );
			$db->setQuery($query);
			$resultRow = $db->loadRowList();
				
			if ( count($resultRow) > 0 ) {
				print "Existing entry for user $personId, end_date $endDate - updating<br/>";
				
				$query = $db->getQuery(true);
				$query->update("UserTestStatistics");
				$query->set("month_tests = " . $monthlyTotal . ", month_score = " . $monthlyAvg . ", total_tests = " . $totalTests . ", total_score = " . $totalAvg . ", timestamp = CURRENT_TIMESTAMP");
				$query->where("person_id = " . $personId );
				$query->where("end_date = '" . $endDate . "'" );
				$db->setQuery($query);
				$result = $db->execute();
			}
			else {
				print "New entry in UserTestStatistics for user $personId, end_date $endDate<br/>";
				
				$query = $db->getQuery(true);
				$query->insert("UserTestStatistics");
				$query->columns($db->quoteName(array('person_id', 'end_date', 'month_tests', 'month_score', 'total_tests', 'total_score')));
				$query->values("" . $personId . ", '" . $endDate . "', " . $monthlyTotal . ", " . $monthlyAvg . ", " . $totalTests . ", " . $totalAvg );
				$db->setQuery($query);
				$result = $db->execute();
			}
		}
	}

	// Update the AnimalStatistics table with count of classifications by project and species
	public static function calculateAnimalStatistics () {

		$db = db();

		print "<br>Truncating table";
		// Truncate the table

		$truncate = $db->getQuery(true);
		
		$db->truncateTable('AnimalStatistics');

		print "<br>Inserting new data";

		$query = $db->getQuery(true);
		$query2 = $db->getQuery(true);
		// count every classification but exclude Likes
		$query->select("PSM.project_id as project_id, A.species as option_id, O.option_name as species, count(A.animal_id) as num_animals from Animal A")
			->innerJoin("Photo P on P.photo_id = A.photo_id and P.sequence_num = 1")
			->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id and P.photo_id >= PSM.start_photo_id and P.photo_id <= PSM.end_photo_id")
			->innerJoin("Options O on A.species = O.option_id")
			->where("A.species!= 97")
			->group("PSM.project_id, A.species");
			
		$query2->select("PSM.project_id as project_id, A.species as option_id, O.option_name as species, count(A.animal_id) as num_animals from Animal A")
			->innerJoin("Photo P on P.photo_id = A.photo_id and P.sequence_num = 1")
			->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id and P.photo_id >= PSM.start_photo_id and PSM.end_photo_id is NULL")
			->innerJoin("Options O on A.species = O.option_id")
			->where("A.species!= 97")
			->group("PSM.project_id, A.species");
			

		$q3 = $db->getQuery(true)
				->select('a.project_id,  a.option_id, a.species, sum(a.num_animals)')
				->from('(' . $query->union($query2) . ') a')
				->group("a.project_id, a.option_id");


		$queryInsert = $db->getQuery(true)
			->insert('AnimalStatistics')
			->columns($db->qn(array('project_id','option_id','species','num_animals')))
			->values($q3);

		$db->setQuery($queryInsert);
		$result = $db->execute();

		print "<br>Complete";
	  
	  
	}


	// Update the SiteAnimals table with count of sightings by site and species where a sighting is at least one classification on a sequence
	public static function calculateSiteAnimalStatistics () {

		$db = db();

		print "<br>Inserting new data";

		$query = $db->getQuery(true);

		// Call the stored procedure to calculate the stats and populate the SiteAnimals table.
		$query->call("CalculateSiteAnimals");
		$db->setQuery($query);
		$result = $db->execute();

		// Truncate the FeatureSites table as we're about to recalculate 
		$db->truncateTable('FeatureSites');

		// get all the sites we are displaying data for
		// Don't include any which have ever been in a private project
		$query = $db->getQuery(true)
				 ->select('S.site_id as site_id, S.latitude as lat, S.longitude as lon')
				 ->from('Site S')
				 ->where('S.site_id not in (select PSM.site_id from ProjectSiteMap PSM inner join Project P on P.project_id = PSM.project_id and P.access_level = 3)');
		$db->setQuery($query);		 
		$sites = $db->loadAssocList('site_id');

		// For each site, work out which feature it is in and store in the FeatureSites table
		// Currently only have display_type "site" but this could be extended for different zoom levels or other types of display eg by country
		foreach ($sites as $site_id=>$coords) {
			print ("<br>site_id: " . $site_id . "coords: " . $coords['lat'] . "," . $coords['lon'] );

			$query = $db->getQuery(true)
				->select("F.feature_id")
				->from("Features F")
				->where("(".$coords['lon']." >= F.west and " . $coords['lon'] . " < F.east)")
				->where("(".$coords['lat']." >= F.south and " . $coords['lat'] . " < F.north)")
				->where("F.display_type = 'site'");
				 
			$db->setQuery($query);		 
			$feature_ids = $db->loadColumn();

			$num_features = count($feature_ids);
			$feature_site_id = null;

			if ( $num_features == 0 ) {
			  
				print("No existing feature for site,creating new one");

				// Use Location class to get ewns
				$loc = new Location($coords['lat'],$coords['lon']);
				$e = $loc->getEast();
				$w = $loc->getWest();
				$s = $loc->getSouth();
				$n = $loc->getNorth();

				// Create and populate an object.
				$feature = new stdClass();
				$feature->feature_id = null;
				$feature->east = $e;
				$feature->west = $w;
				$feature->south = $s;
				$feature->north = $n;
				$feature->display_type = 'site';

				// Insert the object into the Features table.
				$result = $db->insertObject('Features', $feature, 'feature_id');

				$feature_site_id = $feature->feature_id;

				print ( "<br>Inserted new Feature " . $feature_site_id );
			}
			else if ( $num_features > 1 ) {
				print("<br> Site " . $site_id . " appears in more than one site feature!  Just using first in stats." );
				print_r($feature_ids);
				$feature_site_id = $feature_ids[0];
			}
			else {
				$feature_site_id = $feature_ids[0];
			}

			// Now can insert into FeatureSites
			print ( "<br>inserting " . $feature_site_id . ", " . $site_id);

			$query = $db->getQuery(true)
				->insert("FeatureSites")
				->columns($db->quoteName(array('feature_id', 'site_id')))
				->values("" . $feature_site_id . ", " . $site_id );
			$db->setQuery($query);
			$result = $db->execute();

		}

		print "<br>Complete";
	  
	  
	}

		
	// Calculate the number of sequences uploaded and the number of sequences with at least one classification for every site for end date given or end of this month
	// Keep it simple by removing all enries for this end date then insert new.
	// Miss out any sites which have ever been in a private project.
	public static function calculateSiteStats ( $end_date = null ) {
		
		$db = db();
		
		$endDatePlus1 = strtotime("first day of next month" );
		$endDate = date('Ymd', strtotime("last day of this month"));
		if ( $end_date ) {
			$endDatePlus1 = strtotime("+1 day", strtotime($end_date));
			$endDate = date('Ymd', strtotime($end_date));
		}
		
		$dateToUse = date('Ymd', $endDatePlus1);
		
		// $isHistory = false;
		// if ( $endDate <  date('Ymd') ) {
			// $isHistory = true;
		// }
		
		$query = $db->getQuery(true)
			->select("site_id, count(distinct sequence_id) as num from Photo P")
			->where("uploaded < " . $dateToUse )
			->where('site_id not in (select PSM.site_id from ProjectSiteMap PSM inner join Project P on P.project_id = PSM.project_id and P.access_level = 3)')
			->group("site_id");
			
		$db->setQuery($query);
		
		$uploaded = $db->loadAssocList("site_id", "num");
		
		// Handle private sites - this data goes in a different table
		$query = $db->getQuery(true)
			->select("site_id, count(distinct sequence_id) as num from Photo P")
			->where("uploaded < " . $dateToUse )
			->where('site_id in (select PSM.site_id from ProjectSiteMap PSM inner join Project P on P.project_id = PSM.project_id and P.access_level = 3)')
			->group("site_id");
			
		$db->setQuery($query);
		
		$privateUploaded = $db->loadAssocList("site_id", "num");
		
		$query = $db->getQuery(true)
			->select("P.site_id, count(distinct P.sequence_id) as num from Photo P")
			->innerJoin("Animal A on A.photo_id = P.photo_id")
			->where("A.species != 97")
			->where("A.timestamp < " . $dateToUse )
			->where('P.site_id not in (select PSM.site_id from ProjectSiteMap PSM inner join Project P on P.project_id = PSM.project_id and P.access_level = 3)')
			->group("site_id");
			
		$db->setQuery($query);
		
		$classified = $db->loadAssocList("site_id", "num");
		
		// Handle private sites
		$query = $db->getQuery(true)
			->select("P.site_id, count(distinct P.sequence_id) as num from Photo P")
			->innerJoin("Animal A on A.photo_id = P.photo_id")
			->where("A.species != 97")
			->where("A.timestamp < " . $dateToUse )
			->where('P.site_id in (select PSM.site_id from ProjectSiteMap PSM inner join Project P on P.project_id = PSM.project_id and P.access_level = 3)')
			->group("site_id");
			
		$db->setQuery($query);
		
		$privateClassified = $db->loadAssocList("site_id", "num");
		
		// $numQueued = 0;
		// if ( $isHistory ) {
			// $query = $db->getQuery(true);
			// $query->select("P.site_id, count(*) from Photo P")
				// ->innerJoin("Classify C on C.photo_id = P.photo_id" )
				// ->where("P.sequence_num = 1 and P.uploaded < " . $dateToUse )
				// ->where("P.photo_id not in (select photo_id from Classify where timestamp < " . $dateToUse . ")" )
				// ->where('P.site_id not in (select PSM.site_id from ProjectSiteMap PSM inner join Project P on P.project_id = PSM.project_id and P.access_level = 3)')
				// ->group("site_id");
			
			// $db->setQuery($query);
			// //error_log ( "calculateStats, num queued history query: " . $query->dump() );

			// $queued = $db->loadAssocList("site_id", "num");
			
			// $query = $db->getQuery(true);
			// $query->select("P.site_id, count(*) as num from Photo P")
				// ->innerJoin("Classify C on C.photo_id = P.photo_id" )
				// ->where("P.sequence_num = 1 and P.uploaded < " . $dateToUse )
				// ->where("P.photo_id not in (select photo_id from Classify where timestamp < " . $dateToUse . ")" )
				// ->where('P.site_id in (select PSM.site_id from ProjectSiteMap PSM inner join Project P on P.project_id = PSM.project_id and P.access_level = 3)')
				// ->group("site_id");
			
			// $db->setQuery($query);
			// //error_log ( "calculateStats, num queued history query: " . $query->dump() );

			// $privateQueued = $db->loadAssocList("site_id", "num");
		// }
		// else {
			// $query = $db->getQuery(true);
			// $query->select("P.site_id, count(*) as num from Photo P")
				// ->where("P.status = 0")
				// ->where("P.contains_human = 0")
				// ->where("P.sequence_num = 1")
				// ->where('P.site_id not in (select PSM.site_id from ProjectSiteMap PSM inner join Project P on P.project_id = PSM.project_id and P.access_level = 3)')
				// ->group("site_id");
			// $db->setQuery($query);
			
			// //error_log ( "calculateStats, num queued query: " . $query->dump() );

			// $queued = $db->loadAssocList("site_id", "num");
			
			// $query = $db->getQuery(true);
			// $query->select("P.site_id, count(*) as num from Photo P")
				// ->where("P.status = 0")
				// ->where("P.contains_human = 0")
				// ->where("P.sequence_num = 1")
				// ->where('P.site_id in (select PSM.site_id from ProjectSiteMap PSM inner join Project P on P.project_id = PSM.project_id and P.access_level = 3)')
				// ->group("site_id");
			// $db->setQuery($query);
			
			// //error_log ( "calculateStats, num queued query: " . $query->dump() );

			// $privateQueued = $db->loadAssocList("site_id", "num");
			
			
		// }
		
		
		// // To calculate how many screened out on the date in question check Classify table?
		// // Screened out is:
		// // - a human flag with no Animal classification
		// // - STATUS_NOTHING => Megadetector signalled Nothing
		// // - STATUS_UNCLASSIFIED and photo_id before Megadetector introduced
		// // 
		// $numScreened = 0;
		// $screenedOutStr = "" . Sequence::STATUS_UNCLASSIFIED .", " . Sequence::STATUS_CALIBRATION_POLE . ", " . Sequence::STATUS_NOTHING;
		// if ( $isHistory ) {
			
			// // Rely on current status and check Classify table for date of Megadetector classify or CAI if older
			
			// $query = $db->getQuery(true);
			// $query->select("P.site_id, P.sequence_id from Photo P")
				// ->innerJoin("Classify C on C.sequence_id = P.sequence_id and C.timestamp < " . $dateToUse )
				// ->where("C.origin = " . $db->quote('CAI') . " and C.photo_id = P.photo_id and C.classify_id <= 6176168")
				// ->where("P.sequence_num = 1 and P.status in (" . $screenedOutStr . ")" )
				// ->where('P.site_id not in (select PSM.site_id from ProjectSiteMap PSM inner join Project P on P.project_id = PSM.project_id and P.access_level = 3)');
			
			// $query2 = $db->getQuery(true);
			// $query2->select("P.site_id, P.sequence_id from Photo P")
				// ->innerJoin("Classify C on C.sequence_id = P.sequence_id and C.timestamp < " . $dateToUse )
				// ->where("C.origin = " . $db->quote('MEGA') . " and C.photo_id = P.photo_id and C.classify_id > 6176168")
				// ->where("P.sequence_num = 1 and P.status in (" . $screenedOutStr . ")" )
				// ->where('P.site_id not in (select PSM.site_id from ProjectSiteMap PSM inner join Project P on P.project_id = PSM.project_id and P.access_level = 3)');
			
			// $query3 = $db->getQuery(true);
			// $query3->select("P.site_id, P.sequence_id from Photo P")
				// ->innerJoin("Classify C on C.sequence_id = P.sequence_id and C.timestamp < " . $dateToUse )
				// ->where("P.sequence_num = 1 and P.contains_human = 1 and status = " .  Sequence::STATUS_UNAVAILABLE)
				// ->where("P.photo_id not in (select photo_id from Animal)")
				// ->where('P.site_id not in (select PSM.site_id from ProjectSiteMap PSM inner join Project P on P.project_id = PSM.project_id and P.access_level = 3)');
			
			
			// $q3 = $db->getQuery(true)
			 // ->select('site_id, count(distinct a.sequence_id) as num')
			 // ->from('(' . $query->union($query2->union($query3)) . ') a order by site_id')
			 // ->group('site_id');
			 
			// $db->setQuery($q3);
			
			// $screened = $db->loadAssocList("site_id", "num");
			
			// $query = $db->getQuery(true);
			// $query->select("P.site_id, P.sequence_id from Photo P")
				// ->innerJoin("Classify C on C.sequence_id = P.sequence_id and C.timestamp < " . $dateToUse )
				// ->where("C.origin = " . $db->quote('CAI') . " and C.photo_id = P.photo_id and C.classify_id <= 6176168")
				// ->where("P.sequence_num = 1 and P.status in (" . $screenedOutStr . ")" )
				// ->where('P.site_id in (select PSM.site_id from ProjectSiteMap PSM inner join Project P on P.project_id = PSM.project_id and P.access_level = 3)');
			
			// $query2 = $db->getQuery(true);
			// $query2->select("P.site_id, P.sequence_id from Photo P")
				// ->innerJoin("Classify C on C.sequence_id = P.sequence_id and C.timestamp < " . $dateToUse )
				// ->where("C.origin = " . $db->quote('MEGA') . " and C.photo_id = P.photo_id and C.classify_id > 6176168")
				// ->where("P.sequence_num = 1 and P.status in (" . $screenedOutStr . ")" )
				// ->where('P.site_id in (select PSM.site_id from ProjectSiteMap PSM inner join Project P on P.project_id = PSM.project_id and P.access_level = 3)');
			
			// $query3 = $db->getQuery(true);
			// $query3->select("P.site_id, P.sequence_id from Photo P")
				// ->innerJoin("Classify C on C.sequence_id = P.sequence_id and C.timestamp < " . $dateToUse )
				// ->where("P.sequence_num = 1 and P.contains_human = 1 and status = " .  Sequence::STATUS_UNAVAILABLE)
				// ->where("P.photo_id not in (select photo_id from Animal)")
				// ->where('P.site_id in (select PSM.site_id from ProjectSiteMap PSM inner join Project P on P.project_id = PSM.project_id and P.access_level = 3)');
			
			
			// $q3 = $db->getQuery(true)
			 // ->select('site_id, count(distinct a.sequence_id)')
			 // ->from('(' . $query->union($query2->union($query3)) . ') a')
			 // ->group('site_id');
			 
			// $db->setQuery($q3);
			
			// //error_log ( "calculateStats, num screened history query: " . $query->dump() );
			
			// $privateScreened = $db->loadAssocList("site_id", "num");
		
		// }
		// else {
			// $query = $db->getQuery(true);
			// $query->select("site_id, sequence_id from Photo P")
				// ->where("P.status not in (1,18)")
				// ->where("P.sequence_num = 1")
				// ->where('P.site_id not in (select PSM.site_id from ProjectSiteMap PSM inner join Project P on P.project_id = PSM.project_id and P.access_level = 3)');
			
			// $query2 = $db->getQuery(true);
			// $query2->select("site_id, sequence_id from Photo P")
				// ->leftJoin("Animal A on P.photo_id = A.photo_id")
				// ->where("A.photo_id is NULL" )
				// ->where("P.status = 0")
				// ->where("P.contains_human = 1")
				// ->where("P.sequence_num = 1")
				// ->where('P.site_id not in (select PSM.site_id from ProjectSiteMap PSM inner join Project P on P.project_id = PSM.project_id and P.access_level = 3)');
			
			
			// $q3 = $db->getQuery(true)
			 // ->select('site_id, count(distinct a.sequence_id) as num')
			 // ->from('(' . $query->union($query2) . ') a group by site_id');
			 
			// $db->setQuery($q3);
			
			// //error_log ( "calculateStats, num classifications query: " . $query->dump() );

			// $screened = $db->loadAssocList("site_id", "num");
			
			
			// $query = $db->getQuery(true);
			// $query->select("site_id, sequence_id from Photo P")
				// ->where("P.status not in (1,18)")
				// ->where("P.sequence_num = 1")
				// ->where('P.site_id in (select PSM.site_id from ProjectSiteMap PSM inner join Project P on P.project_id = PSM.project_id and P.access_level = 3)');
			
			// $query2 = $db->getQuery(true);
			// $query2->select("site_id, sequence_id from Photo P")
				// ->leftJoin("Animal A on P.photo_id = A.photo_id")
				// ->where("A.photo_id is NULL" )
				// ->where("P.status = 0")
				// ->where("P.contains_human = 1")
				// ->where("P.sequence_num = 1")
				// ->where('P.site_id in (select PSM.site_id from ProjectSiteMap PSM inner join Project P on P.project_id = PSM.project_id and P.access_level = 3)');
			
			
			// $q3 = $db->getQuery(true)
			 // ->select('site_id, count(distinct a.sequence_id) as num')
			 // ->from('(' . $query->union($query2) . ') a group by site_id');
			 
			// $db->setQuery($q3);
			
			// //error_log ( "calculateStats, num classifications query: " . $query->dump() );

			// $privateScreened = $db->loadAssocList("site_id", "num");
			
		// }
		
		// // Calculate those classified to project threshold 
		// if ( $isHistory ) {
			
			// // $fClassified = Project::getFullyClassified ( $proj_id, $threshold, $dateToUse ); 
						
			// // $fullyClassified = count($fullyClassified);
		// }
		// else {
			// $query = $db->getQuery(true);
			
			// $query->select("site_id, count(*) from Photo P")
				// ->where("P.status = 18")
				// ->where("P.sequence_num = 1")
				// ->where('P.site_id not in (select PSM.site_id from ProjectSiteMap PSM inner join Project P on P.project_id = PSM.project_id and P.access_level = 3)');
			
			// $db->setQuery($query);
						
			// //error_log ( "calculateStats, num fully classified to threshold query: " . $query->dump() );
			
			// $fullyClassified = $db->loadAssocList("site_id", "num");
			
			// $query = $db->getQuery(true);
			
			// $query->select("site_id, count(*) from Photo P")
				// ->where("P.status = 18")
				// ->where("P.sequence_num = 1")
				// ->where('P.site_id not in (select PSM.site_id from ProjectSiteMap PSM inner join Project P on P.project_id = PSM.project_id and P.access_level = 3)');
			
			// $db->setQuery($query);
						
			// //error_log ( "calculateStats, num fully classified to threshold query: " . $query->dump() );
			
			// $privateFullyClassified = $db->loadAssocList("site_id", "num");
			
		// }
		
		$query = $db->getQuery(true)
			->delete("SiteStatistics")
			->where("end_date = " . $endDate);
			
		$db->setQuery($query);
		$result = $db->execute();
		
		// Insert using the uploaded sites so that get them all
		foreach ($uploaded as $site_id=>$numLoaded) {
			$query = $db->getQuery(true)
				->insert("SiteStatistics")
				->columns($db->quoteName(array('site_id', 'end_date', 'num_uploaded')))
				->values("" . $site_id . ", '" . $endDate . "', " . $numLoaded );
				
			$db->setQuery($query);
			$result = $db->execute();
		}
		
		// // And update with the num queued
		// foreach ($queued as $site_id=>$numQueued) {
			// $query = $db->getQuery(true)
				// ->update("SiteStatistics")
				// ->set("queued = " . $numQueued )
				// ->where("site_id = " . $site_id)
				// ->where("end_date = '" . $endDate . "'" );
				
			// $db->setQuery($query);
			// $result = $db->execute();
		// }
		
		// // And update with the num screened out
		// foreach ($screened as $site_id=>$numScreened) {
			// $query = $db->getQuery(true)
				// ->update("SiteStatistics")
				// ->set("screened_out = " . $numScreened )
				// ->where("site_id = " . $site_id)
				// ->where("end_date = '" . $endDate . "'" );
				
			// $db->setQuery($query);
			// $result = $db->execute();
		// }
		
		// And update with the num classified
		foreach ($classified as $site_id=>$numClassified) {
			$query = $db->getQuery(true)
				->update("SiteStatistics")
				->set("num_classified = " . $numClassified )
				->where("site_id = " . $site_id)
				->where("end_date = '" . $endDate . "'" );
				
			$db->setQuery($query);
			$result = $db->execute();
		}
		
		// // And update with the num fully classified
		// foreach ($fullyClassified as $site_id=>$numFullyClassified) {
			// $query = $db->getQuery(true)
				// ->update("SiteStatistics")
				// ->set("fully_classified = " . $numFullyClassified )
				// ->where("site_id = " . $site_id)
				// ->where("end_date = '" . $endDate . "'" );
				
			// $db->setQuery($query);
			// $result = $db->execute();
		// }
		
		// Do similar for private sites
		$query = $db->getQuery(true)
			->delete("PrivateSiteStatistics")
			->where("end_date = " . $endDate);
			
		$db->setQuery($query);
		$result = $db->execute();
		
		// Insert using the uploaded sites.
		foreach ($privateUploaded as $site_id=>$numLoaded) {
			$query = $db->getQuery(true)
				->insert("PrivateSiteStatistics")
				->columns($db->quoteName(array('site_id', 'end_date', 'num_uploaded')))
				->values("" . $site_id . ", '" . $endDate . "', " . $numLoaded  );
				
			$db->setQuery($query);
			$result = $db->execute();
		}
		
		// // And update with the num queued
		// foreach ($privateQueued as $site_id=>$numQueued) {
			// $query = $db->getQuery(true)
				// ->update("PrivateSiteStatistics")
				// ->set("queued = " . $numQueued )
				// ->where("site_id = " . $site_id)
				// ->where("end_date = '" . $endDate . "'" );
				
			// $db->setQuery($query);
			// $result = $db->execute();
		// }
		
		// // And update with the num screened out
		// foreach ($privateScreened as $site_id=>$numScreened) {
			// $query = $db->getQuery(true)
				// ->update("PrivateSiteStatistics")
				// ->set("screened_out = " . $numScreened )
				// ->where("site_id = " . $site_id)
				// ->where("end_date = '" . $endDate . "'" );
				
			// $db->setQuery($query);
			// $result = $db->execute();
		// }
		
		// And update with the num classified
		foreach ($privateClassified as $site_id=>$numClassified) {
			$query = $db->getQuery(true)
				->update("PrivateSiteStatistics")
				->set("num_classified = " . $numClassified )
				->where("site_id = " . $site_id)
				->where("end_date = '" . $endDate . "'" );
				
			$db->setQuery($query);
			$result = $db->execute();
		}
		
		// // And update with the num fully classified
		// foreach ($privateFullyClassified as $site_id=>$numFullyClassified) {
			// $query = $db->getQuery(true)
				// ->update("SiteStatistics")
				// ->set("fully_classified = " . $numFullyClassified )
				// ->where("site_id = " . $site_id)
				// ->where("end_date = '" . $endDate . "'" );
				
			// $db->setQuery($query);
			// $result = $db->execute();
		// }
	}

	// Recalculate site statistics for all sites
	// Use with care this will overwrite existing figures.
	public static function calculateSiteStatsHistory ( $num_months = null) {
		
		$endDate = strtotime("last day of this month");
		$endDatePlus1 = strtotime("first day of next month" );

		$datesArray = array();
		
		// Default to 3 years of data.
		$numDisplayedMonths = 13;
		if ( $num_months ) {
			$numDisplayedMonths = $num_months + 3;
		}
	  
		for ( $i=$numDisplayedMonths; $i>=0; $i-- ) {
			$minusMonths = "-" . $i . " months";
			$firstOfMonth = strtotime($minusMonths, $endDatePlus1);
			print("<br>Calculating for end date " . date('Ymd', strtotime("-1 day", $firstOfMonth)) );
			self::calculateSiteStats(date('Ymd', strtotime("-1 day", $firstOfMonth)) );
		}
	}

	// Return some animal sightings data for the sites within this area
	public static function discoverData ( $lat_start, $lat_end, $lon_start, $lon_end, $num_months = 12  ) {
		
		$coords = Text::_("COM_BIODIV_DISCOVER_LAT"). " " . $lat_start . " " . Text::_("COM_BIODIV_DISCOVER_TO") . " " . $lat_end . ", ";
		$coords .= Text::_("COM_BIODIV_DISCOVER_LON"). " " . $lon_start . " " . Text::_("COM_BIODIV_DISCOVER_TO") . " " . $lon_end ;
		$title = Text::_("COM_BIODIV_DISCOVER_UP_CLASS") . " " . $coords ;
		
		//$numDisplayedMonths = 6;
		$interval_in_months = 1;
		$numDisplayedMonths = $num_months + $interval_in_months;
		$end_date = null;
		if ( $end_date == null ) {
			$endDatePlus1 = strtotime("first day of next month" );
			$endDate = strtotime("last day of this month");
		}
		else {
			$endDatePlus1 = strtotime("+1 day", strtotime($end_date));
			$endDate = strtotime($end_date);
		}
	  
		$datesArray = array();
		$labelsArray = array();
		for ( $i=$numDisplayedMonths; $i>0; $i-=$interval_in_months ) {
			//$dateStr = "first day of next month -" . $i . " months";
			$minusMonths = "-" . $i . " months";
			$months = $i-$interval_in_months+1;
			$labelStr = "- " . $months . " months";
			$dateMinusMonths = strtotime($minusMonths, $endDatePlus1);
			//array_push ( $datesArray, date('Y-m-d H:i:s', strtotime($dateStr)) );
			array_push ( $datesArray, date('Ymd', strtotime("-1 day", $dateMinusMonths)) );
			array_push ( $labelsArray, date('M Y', strtotime($labelStr, $endDatePlus1)) );
			//array_push ( $labelsArray, $i-$interval_in_months+1 );
		}
		//array_push ( $datesArray, date('Ymd', strtotime("first day of next month")) );
		array_push ( $datesArray, date('Ymd', strtotime("-1 day", $endDatePlus1)) );
	  
		// Select number of sequences uploaded and number classified up to each of our dates.
		$numIntervals = count($datesArray)-1;
		//print "num intervals = " . $numIntervals;
	  
		$uploadedArray = array();
		$classifiedArray = array();
		$db = db();
	  
		for ( $j=0; $j<$numIntervals; $j++ ) {
			$query = $db->getQuery(true)
				->select("sum(num_uploaded), sum(num_classified) from SiteStatistics SS")
				->innerJoin("Site S on SS.site_id = S.site_id")
				->where("S.latitude >= " . $lat_start . " and S.latitude < " . $lat_end )
				->where("S.longitude >= " . $lon_start . " and S.longitude < " . $lon_end )
				->where("end_date = " . $datesArray[$j+1] );
			$db->setQuery($query);
		
			$row = $db->loadRow();
			
			array_push ( $uploadedArray, $row['0'] ? $row['0'] : 0 );
			array_push ( $classifiedArray, $row['1'] ? $row['1'] : 0 );
		}
		
		// If all uploaded entries are 0 there are no uploads so reflect in title
		if ( array_sum($uploadedArray) == 0 ) {
			
			$title = Text::_("COM_BIODIV_DISCOVER_NO_UP_CLASS") . " " . $coords ;
		}

	  
		$discoverData = array ( 
			"labels" => $labelsArray,
			"uploaded" => $uploadedArray,
			"classified" => $classifiedArray,
			"cla_label" => Text::_("COM_BIODIV_DISCOVER_CLASSIFIED"),
			"upl_label" => Text::_("COM_BIODIV_DISCOVER_UPLOADED"),
			"title" => $title
			
			);
	  
	  
		return $discoverData;
	}


	// Return some animal sightings data for the sites within this area
	public static function discoverAnimals ( $lat_start, $lat_end, $lon_start, $lon_end, $num_species = null, $include_dontknow = false, $include_human = false, $include_nothing = false  ) {
		
		$coords = Text::_("COM_BIODIV_DISCOVER_LAT"). " " . $lat_start . " " . Text::_("COM_BIODIV_DISCOVER_TO") . " " . $lat_end . ", ";
		$coords .= Text::_("COM_BIODIV_DISCOVER_LON"). " " . $lon_start . " " . Text::_("COM_BIODIV_DISCOVER_TO") . " " . $lon_end ;
		$title = Text::_("COM_BIODIV_DISCOVER_SIGHTS") . " " . $coords ;

		// Note that for now we are summing over the years to get total sightings
		$db = db();
		$query = $db->getQuery(true);

		$query->select("SA.species as option_id, O.option_name as species, sum(num_sightings) as num_sightings FROM SiteAnimals SA")
			->innerJoin("Options O on SA.species = O.option_id")
			->innerJoin("Site S on SA.site_id = S.site_id")
			->where("S.latitude between " . $lat_start . " and " . $lat_end . " and S.longitude between " . $lon_start . " and " . $lon_end)
			->group("SA.species")
			->order("num_sightings  DESC");

		$db->setQuery($query);

		// Include id...
		$animals_w_id = $db->loadAssocList('species');

		$animals = $db->loadAssocList('species', 'num_sightings');
		//$animals = array_column($animals_w_id, 'num_animals', 'species');

		// Ensure there is a variable - getting errors here
		if ( !$animals ) $animals = array();

		// Remove human and nothing if not to be included
		if ( !$include_human ) {
			unset($animals['Human']);
			$array_changed = true;
		}
		if ( !$include_nothing ) {
			unset($animals['Nothing']);
			$array_changed = true;
		}
		if ( !$include_dontknow ) {
			unset($animals["Don't Know"]);
			$array_changed = true;
		}

		// Remove Likes
		unset($animals["Like"]);
		$array_changed = true;

		// If we had to change the key names we need to re-sort the array
		if ( $array_changed ) {
			arsort($animals);
		}

		$animals_to_return_en = array();
		// Finally handle the number of species, if we have more than required
		// NB Other is a possible option so combine this with Other Species if we have both
		$num_other = 0;

		if ( key_exists("Other", $animals) ) {
			$num_other = $animals["Other"];
			$animals = Biodiv::akrem($animals, "Other");
		}

		if ( $num_species && (count($animals) > $num_species) ) {
			$animals_to_return_en = array_slice($animals, 0, $num_species-1);
			$total_other = array_sum(array_slice($animals, $num_species-1)) + $num_other;
			$animals_to_return_en['Other Species'] = "" . $total_other;
		}
		else {
			$animals_to_return_en = $animals;
		}

		$animals_to_return = array();
		foreach ( $animals_to_return_en as $sp=>$num ) {
			//error_log( "animal row: " . $sp . ", " . $num );
			if ( $sp == "Other Species" ) {
			  
				$animals_to_return[Text::_("COM_BIODIV_DISCOVER_OTHER_SP")] = $num;
			}
			else {
				$animals_to_return[codes_getName($animals_w_id[$sp]["option_id"], "speciestran")] = $num;
			}
		}

		// Quick fix?
		if ( count($animals_to_return) == 0 ) {
			$animals_to_return["All"] = 0;

			$title = Text::_("COM_BIODIV_DISCOVER_NO_SIGHTS") . " " . $coords ;

		}

		return array (
			"labels" => array_keys($animals_to_return),
			"animals" => array_values($animals_to_return),
			"title" => $title
			);

	}

	// Return all sites which are in the FeatureSites table - ir they are within a feature and in a non-private project
	public static function discoverSites () {
		
		$sites = Text::_("COM_BIODIV_DISCOVER_SITES");
		  
		$db = db();
		$query = $db->getQuery(true);
		  
		// Want to get a count of sites for each feature
		$query->select("F.feature_id, F.west, F.east, F.south, F.north, count(FS.site_id) as num_sites from FeatureSites FS")
			->innerJoin("Features F on FS.feature_id = F.feature_id and F.display_type = 'site'")
			->group("F.feature_id");

		$db->setQuery($query);
		  
		// Include id...
		$sites_by_feature = $db->loadAssocList();
		  
		$features = array();
		foreach ( $sites_by_feature as $row ) {
			$fid = "" . $row["feature_id"];
			$num = $row["num_sites"];
			$e = $row["east"];
			$w = $row["west"];
			$s = $row["south"];
			$n = $row["north"];
			
			// New feature each row as summed over years.
			$features[$fid] = array();
			$features[$fid]["type"] = "Feature";
			$features[$fid]["properties"] = array();
			$features[$fid]["properties"]["stroke"] = false;
			$features[$fid]["properties"]["site_count"] = $num;
			$features[$fid]["geometry"] = array();
			$features[$fid]["geometry"]["type"] = "Polygon";
			$features[$fid]["geometry"]["coordinates"] = [[[$e,$s],[$w,$s],[$w,$n],[$e,$n],[$e,$s]]];
			  
		}
		  
		return array (
				"features" => array_values($features),
				"sites" => $sites
				);
	}

	// Return some animal sightings data for the sites within this area
	public static function discoverSpecies ( $species_id, $year = null  ) {
	
		$tr_sp_name = codes_getOptionTranslation($species_id);

		$title = Text::_("COM_BIODIV_DISCOVER_SPECIES") . " - " . $tr_sp_name;

		$db = db();
		$query = $db->getQuery(true);

		// Let's do each year plus sum over all years
		// If no year is specified, sum over all years
		$query->select("F.feature_id, F.west, F.east, F.south, F.north, sum(SA.num_sightings) as num_sightings, SA.year_taken from SiteAnimals SA")
			->innerJoin("FeatureSites FS on FS.site_id = SA.site_id")
			->innerJoin("Features F on FS.feature_id = F.feature_id and F.display_type = 'site'")
			->where("SA.species = " . $species_id )
			->group("F.feature_id, SA.year_taken");
		$db->setQuery($query);

		$sightings_by_year = $db->loadAssocList();

		$queryall = $db->getQuery(true)
			->select("F.feature_id, F.west, F.east, F.south, F.north, sum(SA.num_sightings) as num_sightings from SiteAnimals SA")
			->innerJoin("FeatureSites FS on FS.site_id = SA.site_id")
			->innerJoin("Features F on FS.feature_id = F.feature_id and F.display_type = 'site'")
			->where("SA.species = " . $species_id )
			->group("F.feature_id");
		$db->setQuery($queryall);

		$all_sightings = $db->loadAssocList();

		$queryall = $db->getQuery(true)
			->select("SA.year_taken as year, sum(SA.num_sightings) as num_sightings from SiteAnimals SA")
			->where("SA.species = " . $species_id )
			->group("SA.year_taken");

		$db->setQuery($queryall);

		// Include id...
		$totals_by_year = $db->loadAssocList();

		$features = array();
		foreach ( $all_sightings as $sighting ) {
			$fid = "" . $sighting["feature_id"];
			$num = $sighting["num_sightings"];
			$e = $sighting["east"];
			$w = $sighting["west"];
			$s = $sighting["south"];
			$n = $sighting["north"];

			// New feature each row as summed over years.
			$features[$fid] = array();
			$features[$fid]["type"] = "Feature";
			$features[$fid]["properties"] = array();
			$features[$fid]["properties"]["stroke"] = false;
			$features[$fid]["properties"]["all"] = $num;
			$features[$fid]["geometry"] = array();
			$features[$fid]["geometry"]["type"] = "Polygon";
			$features[$fid]["geometry"]["coordinates"] = [[[$e,$s],[$w,$s],[$w,$n],[$e,$n],[$e,$s]]];
		  
		}

		// By this point should have a feature for all years for all features.  Now add the year by year counts.
		foreach ( $sightings_by_year as $sighting ) {
			$fid = "" . $sighting["feature_id"];
			$num = $sighting["num_sightings"];
			$e = $sighting["east"];
			$w = $sighting["west"];
			$s = $sighting["south"];
			$n = $sighting["north"];
			$y = $sighting["year_taken"];
			
			// Just add the year as rest should be there.
			$features[$fid]["properties"]["".$y] = $num;
		}

		$totals = array();
		foreach ( $totals_by_year as $total ) {
			$year = $total["year"];
			$num = $total["num_sightings"];
				  
			// New total for each year, add zeros as unknown
			// Anything pre 2000 probably indicates incorrect date so add to unknown.
			if ( $year == 0 ) {
				$totals["unknown"] = $num;
			}
			if ( $year < 2000 ) {
				$totals["unknown"] = $num;
			}
			else {
				$totals["" . $year] = $num;
			}
		}

		return array (
			"features" => array_values($features),
			"totals" => $totals,
			"title" => $title
			);
	}
	
	// Return number of each species classified for the user's sites 
	// If top is false the rarest ones (non-zero) are returned 
	// NB here we include private sites as they belong to the user
	public static function discoverUserAnimals ( $siteId = null, $top=true, $numSpecies = 10, $includeDontknow = false, $includeHuman = false, $includeNothing = false  ) {
		
		$title = Text::_("COM_BIODIV_DASHCHARTS_BY_SP");
		
		$personId = Biodiv::userID();

		//$projects = getSubProjectsById( $project_id );
		//$id_string = implode(",", array_keys($projects));
		
		if ( $top === true ) $order = 'DESC';
		else $order = 'ASC';

		$db = db();
		$query = $db->getQuery(true);
		$query->select("SA.species as species_id, O.option_name as species, sum(SA.num_sightings) as num_animals FROM SiteAnimals SA")
			->innerJoin("Site S on SA.site_id = S.site_id and S.person_id = " . $personId)
			->innerJoin("Options O on O.option_id = SA.species")
			->group("SA.species")
			->order("num_animals, species " . $order);
		
		if ( $siteId ) $query->where("S.site_id = " . $siteId);
		
		$db->setQuery($query);
		
		//error_log ( "discoverUserAnimals, query: " . $query->dump() );


		// Include id...
		$animals_w_id = $db->loadAssocList('species');
		
		$animals = $db->loadAssocList('species', 'num_animals');
		
		// Get the same for private sites
		$query = $db->getQuery(true);
		$query->select("SA.species as species_id, O.option_name as species, sum(SA.num_sightings) as num_animals FROM PrivateSiteAnimals SA")
			->innerJoin("Site S on SA.site_id = S.site_id and S.person_id = " . $personId)
			->innerJoin("Options O on O.option_id = SA.species")
			->group("SA.species")
			->order("num_animals, species " . $order);
		
		if ( $siteId ) $query->where("S.site_id = " . $siteId);
		
		$db->setQuery($query);
		
		//error_log ( "discoverUserAnimals, pquery: " . $query->dump() );


		// Include id...
		$p_animals_w_id = $db->loadAssocList('species');
		
		$p_animals = $db->loadAssocList('species', 'num_animals');
		
		// If there are any private sites need to add those numbers in
		foreach ($p_animals_w_id as $pAnimal) {
			$pSpecies = $pAnimal['species'];
			if ( array_key_exists($pSpecies, $animals_w_id) ) {
				$animals[$pSpecies] += $pAnimal['num_animals'];
				$animals_w_id[$pSpecies]['num_animals'] += $pAnimal['num_animals'];
			}
			else {
				$array_changed = true;
				$animals[$pSpecies] = $pAnimal['num_animals'];
				$animals_w_id[$pSpecies] = $pAnimal;
			}
		}
		

		// Remove human and nothing if not to be included
		if ( !$includeHuman ) {
		  unset($animals['Human']);
		  $array_changed = true;
		}
		if ( !$includeNothing ) {
		  unset($animals['Nothing']);
		  $array_changed = true;
		}
		if ( !$includeDontknow ) {
		  unset($animals["Don't Know"]);
		  $array_changed = true;
		}

		// Remove Likes
		unset($animals["Like"]);
		$array_changed = true;

		// If we had to change the key names we need to re-sort the array
		if ( $array_changed ) {
			if ( $top ) {
				arsort($animals);
			}
			else {
				asort($animals);
			}
		}
		
		$animals_to_return_en = array();
		// Finally handle the number of species, if we have more than required
		// NB Other is a possible option so combine this with Other Species if we have both
		$num_other = 0;

		if ( key_exists("Other", $animals) ) {
		$num_other = $animals["Other"];
		$animals = Biodiv::akrem($animals, "Other");
		}
		
		if ( $top ) {
			
			if ( $num_other > 0 ) {
			
				if ( $numSpecies && (count($animals) > $numSpecies-1) ) {
					$animals_to_return_en = array_slice($animals, 0, $numSpecies-1);
					$total_other = array_sum(array_slice($animals, $numSpecies-1)) + $num_other;
					$animals_to_return_en['Other Species'] = "" . $total_other;
				}
				else {
					$animals_to_return_en = $animals ;
				}
			}
			else {
				if ( $numSpecies && (count($animals) > $numSpecies) ) {
					$animals_to_return_en = array_slice($animals, 0, $numSpecies-1);
					$total_other = array_sum(array_slice($animals, $numSpecies-1));
					$animals_to_return_en['Other Species'] = "" . $total_other;
				}
				else {
					$animals_to_return_en = $animals ;
				}
			}
		}
		else {
			// Don't include Other for rare species, number could be large and make graph unreadable, so this is much simpler
			$animals_to_return_en = array_slice($animals, 0, $numSpecies);
		}

		$animals_to_return = array();
		foreach ( $animals_to_return_en as $sp=>$num ) {
		  //error_log( "animal row: " . $sp . ", " . $num );
		  if ( $sp == "Other Species" ) {
			  
			  $animals_to_return[Text::_("COM_BIODIV_DASHCHARTS_OTHER_SP")] = $num;
			  
		  }
		  else {
			  //error_log ( "key = " . codes_getName($animals_w_id[$sp]["option_id"], "speciestran") );
			  $animals_to_return[codes_getName($animals_w_id[$sp]["species_id"], "speciestran")] = $num;
		  }
		}

		return array (
			"labels" => array_keys($animals_to_return),
			"animals" => array_values($animals_to_return),
			"title" => $title
			);
			
	}

	// Return number of sequences where each species has been classified at least once for the user's sites 
	// If top is false the rarest ones (non-zero) are returned 
	// NB here we include private sites as they belong to the user
	public static function discoverUserSeqAnimals ( $siteId = null, $top=true, $numSpecies = 10, $includeDontknow = false, $includeHuman = false, $includeNothing = false  ) {
		
		// Get all the text snippets for this view in the current language
		//$translations = getTranslations("dashcharts");

		$title = Text::_("COM_BIODIV_DASHCHARTS_BY_SP");
		
		$personId = Biodiv::userID();

		if ( $top === true ) $order = 'DESC';
		else $order = 'ASC';

		$db = db();
		$query = $db->getQuery(true);
		$query->select("A.species as species_id, O.option_name as species, count(distinct A.photo_id) as num_sequences FROM `Animal` A")
			->innerJoin("Photo P on P.photo_id = A.photo_id and P.person_id = " . $personId)
			->innerJoin("Options O on O.option_id = A.species")
			->group("A.species")
			->order("num_sequences, species " . $order);
		
		if ( $siteId ) $query->where("P.site_id = " . $siteId);
		
		$db->setQuery($query);
		
		//error_log ( "discoverUserSequenceAnimals, query: " . $query->dump() );


		// Include id...
		$animals_w_id = $db->loadAssocList('species');
		
		$animals = $db->loadAssocList('species', 'num_sequences');
		
		// Remove human and nothing if not to be included
		if ( !$includeHuman ) {
		  unset($animals['Human']);
		  $array_changed = true;
		}
		if ( !$includeNothing ) {
		  unset($animals['Nothing']);
		  $array_changed = true;
		}
		if ( !$includeDontknow ) {
		  unset($animals["Don't Know"]);
		  $array_changed = true;
		}

		// Remove Likes
		unset($animals["Like"]);
		$array_changed = true;

		// If we had to change the key names we need to re-sort the array
		if ( $array_changed ) {
			if ( $top ) {
				arsort($animals);
			}
			else {
				asort($animals);
			}
		}
		
		//$errMsg = print_r($animals, true);
		//error_log ( "Animals after sort: " . $errMsg );

		$animals_to_return_en = array();
		// Finally handle the number of species, if we have more than required
		// NB Other is a possible option so combine this with Other Species if we have both
		$num_other = 0;

		if ( key_exists("Other", $animals) ) {
		$num_other = $animals["Other"];
		$animals = Biodiv::akrem($animals, "Other");
		}
		
		if ( $top ) {
			
			if ( $num_other > 0 ) {
			
				if ( $numSpecies && (count($animals) > $numSpecies-1) ) {
				$animals_to_return_en = array_slice($animals, 0, $numSpecies-1);
					$total_other = array_sum(array_slice($animals, $numSpecies-1)) + $num_other;
					$animals_to_return_en['Other Species'] = "" . $total_other;
				}
				else {
					$animals_to_return_en = $animals ;
				}
			}
			else {
				if ( $numSpecies && (count($animals) > $numSpecies) ) {
					$animals_to_return_en = array_slice($animals, 0, $numSpecies-1);
					$total_other = array_sum(array_slice($animals, $numSpecies-1));
					$animals_to_return_en['Other Species'] = "" . $total_other;
				}
				else {
					$animals_to_return_en = $animals ;
				}
			}
		}
		else {
			// Don't include Other for rare species, number could be large and make graph unreadable, so this is much simpler
			$animals_to_return_en = array_slice($animals, 0, $numSpecies);
		}

		$animals_to_return = array();
		foreach ( $animals_to_return_en as $sp=>$num ) {
			if ( $sp == "Other Species" ) {
				$animals_to_return[Text::_("COM_BIODIV_DASHCHARTS_OTHER_SP")] = $num;
			}
			else {
				$animals_to_return[codes_getName($animals_w_id[$sp]["species_id"], "speciestran")] = $num;
			}
		}

		return array (
			"labels" => array_keys($animals_to_return),
			"animals" => array_values($animals_to_return),
			"title" => $title
			);
			


	}

	// Return number of uploads and classifications for the user's sites
	public static function discoverUserUploads ( $siteId = null, $numMonths = 12  ) {
		
		// Default to All sites
		$siteText = Text::_("COM_BIODIV_DISCOVER_ALL_SITES");
		$personId = Biodiv::userID();
		
		if ( $siteId ) {
			
			$siteDetails = codes_getDetails($siteId, 'site');
			$siteText = Text::_("COM_BIODIV_DISCOVER_FOR_SITE") . ' ' . $siteDetails['site_name'];
		}
		$title = Text::_("COM_BIODIV_DISCOVER_UP_CLASS") . " " . $siteText ;
		
		$interval_in_months = 1;
		$numDisplayedMonths = $numMonths + $interval_in_months;
		$end_date = null;
		if ( $end_date == null ) {
		  $endDatePlus1 = strtotime("first day of next month" );
		  $endDate = strtotime("last day of this month");
		}
		else {
		  $endDatePlus1 = strtotime("+1 day", strtotime($end_date));
		  $endDate = strtotime($end_date);
		}
	  
		$datesArray = array();
		$labelsArray = array();
		for ( $i=$numDisplayedMonths; $i>0; $i-=$interval_in_months ) {
		  $minusMonths = "-" . $i . " months";
		  $months = $i-$interval_in_months+1;
		  $labelStr = "- " . $months . " months";
		  $dateMinusMonths = strtotime($minusMonths, $endDatePlus1);
		  array_push ( $datesArray, date('Ymd', strtotime("-1 day", $dateMinusMonths)) );
		  array_push ( $labelsArray, date('M Y', strtotime($labelStr, $endDatePlus1)) );
		}
		array_push ( $datesArray, date('Ymd', strtotime("-1 day", $endDatePlus1)) );
	  
		// Select number of sequences uploaded and number classified up to each of our dates.
		$numIntervals = count($datesArray)-1;
	  
		$uploadedArray = array();
		$classifiedArray = array();
		$db = db();
		
		// Include data from private sites as this is user data
		for ( $j=0; $j<$numIntervals; $j++ ) {
			
			$query = $db->getQuery(true)
				->select("sum(num_uploaded), sum(num_classified) from SiteStatistics SS")
				->innerJoin("Site S on SS.site_id = S.site_id")
				->where("S.person_id = " . $personId )
				->where("end_date = " . $datesArray[$j+1] );
				
			if ( $siteId ) $query->where("S.site_id = " . $siteId);
			
			$db->setQuery($query);
		
			$row = $db->loadRow();
			
			$query = $db->getQuery(true)
				->select("sum(num_uploaded), sum(num_classified) from PrivateSiteStatistics SS")
				->innerJoin("Site S on SS.site_id = S.site_id")
				->where("S.person_id = " . $personId )
				->where("end_date = " . $datesArray[$j+1] );
				
			if ( $siteId ) $query->where("S.site_id = " . $siteId);
			
			$db->setQuery($query);
		
			$privateRow = $db->loadRow();
			
			$uploadedNum = $row['0'] ? $row['0'] : 0;
			$uploadedNum += $privateRow['0'] ? $privateRow['0'] : 0;
			
			$classifiedNum = $row['1'] ? $row['1'] : 0;
			$classifiedNum += $privateRow['1'] ? $privateRow['1'] : 0;
			
			
			array_push ( $uploadedArray, $uploadedNum );
			array_push ( $classifiedArray, $classifiedNum );
		}
		
		// If all uploaded entries are 0 there are no uploads so reflect in title
		if ( array_sum($uploadedArray) == 0 ) {
			
			$title = Text::_("COM_BIODIV_DISCOVER_NO_UP_CLASS") . " " . $siteText ;
		}

	  
		$discoverData = array ( 
			"labels" => $labelsArray,
			"uploaded" => $uploadedArray,
			"classified" => $classifiedArray,
			"cla_label" => Text::_("COM_BIODIV_DISCOVER_CLASSIFIED"),
			"upl_label" => Text::_("COM_BIODIV_DISCOVER_UPLOADED"),
			"title" => $title
			
			);
	  
	  
		return $discoverData;
	}


	public static function getFeatureSpecies() {
		
		$db = db();
		  
		// return all species which appear in the SiteAnimals table
		$query = $db->getQuery(true)
			->select("distinct O.option_name as name, SA.species as id FROM SiteAnimals SA")
			->innerJoin("Options O on O.option_id = SA.species")
			->order("name");
			
		$db->setQuery($query);
		$fs = $db->loadAssocList("id", "name");
		
		//Update to be the name in correct language
		foreach ( $fs as $id=>$name ) {
			//error_log("Updating $fs[$id]");
			$tr_name = codes_getOptionTranslation ( $id );
			$fs[$id] = $tr_name;
			//error_log(" new name = " . $tr_name . ", in array: " . $fs[$id]);
		}
		
		// Reorder list after translating
		asort($fs);
		
		return $fs;
	}
}

