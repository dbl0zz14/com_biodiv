<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/

namespace MammalWeb\Component\Biodiv\Site\Library\Core;

 
// No direct access to this file
defined('_JEXEC') or die;

#require_once "local.php";

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseFactory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;


class Project {
	
	var $name;
	var $id;
	var $description;
	var $level;
	var $priority;
	var $imageFile;
	var $region;
	
	// The parent project of this one
	var $parentProject;
	
	// An array of the children of this project.
	var $childProjects;
	
	
	function __construct($name, $id, $description, $level, $priority, $imageFile, $region)
	{
		$this->name = $name;
		$this->id = $id;
		$this->description = $description;
		$this->level = $level;
		$this->priority = $priority;
		$this->imageFile = $imageFile;
		$this->region = $region;
		
		$childProjects = [];
	}
	
	function addChild ( $child ) {
		$childProjects[] = $child;
	}

	public static function listedProjects(){

	  	//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();
		$query = $db->getQuery(true);
		$query->select("DISTINCT P.project_id, P.project_prettyname, P.project_description, P.article_id, P.listing_level as level, O.option_name as priority")->from("Project P");
		$query->innerJoin("ProjectOptions PO on PO.project_id = P.project_id");
		$query->innerJoin("Options O on PO.option_id = O.option_id");
		$query->where("O.struc = 'priority'");
		$query->where("P.access_level < 3");
		$query->where("P.parent_project_id is null" );
		$query->where("P.project_id not in (380,183,381)" );

		// Include second level non private...
		$query2 = $db->getQuery(true);
		$query2->select("DISTINCT P.project_id, P.project_prettyname, P.project_description, P.article_id, P.listing_level as level, O.option_name as priority")->from("Project P");
		$query2->innerJoin("ProjectOptions PO on PO.project_id = P.project_id");
		$query2->innerJoin("Options O on PO.option_id = O.option_id");
		$query2->where("O.struc = 'priority'");
		$query2->where("P.access_level < 3");
		$query2->where("P.parent_project_id in (select project_id from Project where parent_project_id is null and access_level > 2)" );
		$query2->where("P.project_id not in (380,183,381)" );

		$db->setQuery($query);

		$q3 = $db->getQuery(true)
				 ->select('a.*')
				 ->from('(' . $query->union($query2) . ') a')
				 ->order("a.level");
				 
		$listedprojects = $db->loadObjectList();		

		usort($listedprojects, "MammalWeb\Component\Biodiv\Site\Library\Core\Project::cmpListedProjects");
			
		
		return $listedprojects;
	}

	public static function listedSchoolProjects(){

	  	//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();
		$query = $db->getQuery(true);
		$query->select("DISTINCT P.project_id, P.project_prettyname, P.project_description, P.article_id, P.listing_level as level, O.option_name as priority")->from("Project P");
		$query->innerJoin("ProjectOptions PO on PO.project_id = P.project_id");
		$query->innerJoin("Options O on PO.option_id = O.option_id");
		$query->where("O.struc = 'priority'");
		$query->where("P.access_level < 3");
		$query->where("P.project_id not in (select parent_project_id from Project where parent_project_id IS NOT NULL)" );
		$query->where("P.project_id in (select project_id from School)" );
		$query->order("P.project_prettyname" );

		$db->setQuery($query);
		
		error_log ( "Project::listedSchoolProjects, get schools query: " . $query->dump() );


		$listedprojects = $db->loadObjectList();		

		return $listedprojects;
	}
	
	// Return the details for a single project id as an object
	public static function projectDetails ( $project_id ) {
	  
		// $db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();
		$query = $db->getQuery(true);
		$query->select("DISTINCT P.project_id, P.project_prettyname, P.project_description, P.dirname, P.image_file, P.project_text")->from("Project P");
		$query->where("P.project_id = " . $project_id );
		$query->where("(P.access_level < 3) or (P.parent_project_id = 194)" );
		$db->setQuery($query);
		$projectdetails = $db->loadObject();

		return $projectdetails;
	}


	public static function projectImageURL($proj_id) {
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();
		$query = $db->getQuery(true);
		$query->select("dirname, image_file")->from("Project");
		$query->where("project_id = " . (int)$proj_id); 
		$db->setQuery($query);
		$row = $db->loadAssoc();

		return Uri::root().$row['dirname']."/".$row['image_file'];
	}
	
	// return two values: number of classifications and total number of sequences uploaded for this project
	// and all subprojects...
	public static function projectProgress ( $project_id ) {
		
		$thisAndSubs = self::getSubProjectsById ( $project_id );
				
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();
		
		$id_string = implode(",", array_keys($thisAndSubs));
		
		$endDate = date('Ymd', strtotime("last day of this month"));
		
		$query = $db->getQuery(true);
		$query->select("sum(num_uploaded), sum(queued), sum(screened_out),  sum(part_classified), sum(fully_classified) from Statistics");
		$query->where("project_id in (" . $id_string . ")" );
		$query->where("end_date = " . $endDate );
		$db->setQuery($query);
		
		$row = $db->loadRow();
		
		$sequences = $row['0'];
		$queued = $row['1'];
		$screenedOut = $row['2'];
		$partClassified = $row['3'];
		$fullyClassified = $row['4'];
		
		$percentComplete = 0;
		//if ( $sequences > 0 ) $percentComplete = (int)(($classifications*100.0)/$sequences);
		//if ( $sequences > 0 ) $percentComplete = (int)(($classifications*100.0)/$sequences);
	  
		//return array("numClassifications"=>$classifications, "numSequences"=>$sequences, "percentComplete"=>$percentComplete);
		return array("queued"=>$queued,  
					"screenedOut"=>$screenedOut,
					"partClassified"=>$partClassified,
					"fullyClassified"=>$fullyClassified,
					"numSequences"=>$sequences);
	}	
	
	// Note for performance we will only consider available sequences as change of threshold is a rare occurrence
	public static function retire ( $projectId ) {
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();
		$projects = self::getProjectsWithThreshold ();
		
		$retired = array();
		if ( !$projectId ) {
			
			foreach ( $projects as $projId=>$details ) {
				
				if ( array_key_exists( 'threshold', $details ) && $details['threshold'] !== null ) {
					$moreRetired = self::retireProject( $projId, $details['threshold'] );
					$retired = [...$retired,...$moreRetired];
				}
			}
		}
		else {
			
			if ( array_key_exists($projectId, $projects) && array_key_exists('threshold', $projects[$projectId]) ) {
				$threshold = $projects[$projectId]['threshold'];
				
				$retired = self::retireProject( $projectId, $threshold );
			}	
		}
		return $retired;
	}
	
	// Unretire written as separate function as is a rarely needed function, only used when a threshold is changed
	public static function unretire ( $projectId ) {
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();
		$projects = self::getProjectsWithThreshold ();
		
		$unretired = array();
		if ( !$projectId ) {
			
			foreach ( $projects as $projId=>$details ) {
				
				if ( array_key_exists( 'threshold', $details ) ) {
					$moreRetired = self::unretireProject( $projId, $details['threshold'] );
					$unretired = [...$unretired,...$moreRetired];
				}
			}
		}
		else {
			
			if ( array_key_exists($projectId, $projects) && array_key_exists('threshold', $projects[$projectId]) ) {
				$threshold = $projects[$projectId]['threshold'];
				
				$unretired = self::unretireProject( $projectId, $threshold );
			}	
		}
		return $unretired;
		
	}
	
	
	public static function getFullyClassified ( $projectId, $threshold, $calcDate = null ) {
		
		$candidates = array();
		$fullyClassified = array();
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();
		
		if ( $calcDate ) {
			$numClassifiersStr = "(select photo_id, count( distinct person_id) as num_classifiers from Animal where species != 97 and timestamp < " . $calcDate . " group by photo_id)";
		}
		else {
			$numClassifiersStr = "(select photo_id, count( distinct person_id) as num_classifiers from Animal where species != 97 group by photo_id)";
		}
		
		if ( $threshold ) {
			
			$query = $db->getQuery(true);
			$query->select("P.sequence_id, P.photo_id, P.site_id from Photo P")
				->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id")
				->innerJoin($numClassifiersStr . " as AC on AC.photo_id = P.photo_id")
				->where("P.sequence_num=1")
				->where("AC.num_classifiers >= " . $threshold)
				->where("PSM.project_id = " . $projectId )
				->where("P.photo_id >= PSM.start_photo_id and ( P.photo_id <= PSM.end_photo_id or PSM.end_photo_id is NULL )"  );
				
			if ( !$calcDate ) {
				$query->where("P.status = 1");
			}
			
			$db->setQuery($query);
			
			//error_log ( "Project::getFullyClassified, num classified to threshold query: " . $query->dump() );

			$candidates = $db->loadObjectList();
		}

		// Need to check whether each sequence is in any other project with a higher threshold or none.
		// Note that we have to use current thresholds for this as we don;t hold the history of any changes in the db
		foreach ( $candidates as $candidate ) {
			$projectRetireList = self::getProjectRetireThresholds($candidate->site_id, $candidate->photo_id);
			$canRetire = true;
			if ( count($projectRetireList) > 1 ) {
				if ( in_array(0, $projectRetireList) ) {
					$canRetire = false;
				}
				else {
					$thresholds = array_values($projectRetireList);
					foreach ( $thresholds as $nextThreshold ) {
						if ( $nextThreshold > $threshold ) {
							$canRetire = false;
							break;
						}
					}
				}
			}
			if ( $canRetire ) {
				
				$fullyClassified[] = $candidate;
			}
			
		}
		
		return $fullyClassified;
	}
	
	public static function getSiteFullyClassified ( $siteId, $threshold, $calcDate = null ) {
		
		$candidates = array();
		$fullyClassified = array();
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();
		
		$projects = self::getProjectsWithThreshold ();
		
		if ( $calcDate ) {
			$numClassifiersStr = "(select photo_id, count( distinct person_id) as num_classifiers from Animal where species != 97 and timestamp < " . $calcDate . " group by photo_id)";
		}
		else {
			$numClassifiersStr = "(select photo_id, count( distinct person_id) as num_classifiers from Animal where species != 97 group by photo_id)";
		}
		
		if ( $threshold ) {
			
			$query = $db->getQuery(true);
			$query->select("P.sequence_id, P.photo_id, P.site_id from Photo P")
				->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id")
				->innerJoin($numClassifiersStr . " as AC on AC.photo_id = P.photo_id")
				->where("P.sequence_num=1")
				->where("AC.num_classifiers >= " . $threshold)
				->where("PSM.site_id = " . $siteId )
				->where("P.photo_id >= PSM.start_photo_id and ( P.photo_id <= PSM.end_photo_id or PSM.end_photo_id is NULL )"  );
				
			if ( !$calcDate ) {
				$query->where("P.status = 1");
			}
			
			$db->setQuery($query);
			
			error_log ( "Project::getFullyClassified, num classified to threshold query: " . $query->dump() );

			$candidates = $db->loadObjectList();
		}

		// Need to check whether each sequence is in any other project with a higher threshold or none.
		// Note that we have to use current thresholds for this as we don;t hold the history of any changes in the db
		foreach ( $candidates as $candidate ) {
			$projectRetireList = self::getProjectRetireThresholds($candidate->site_id, $candidate->photo_id);
			$canRetire = true;
			if ( count($projectRetireList) > 1 ) {
				if ( in_array(0, $projectRetireList) ) {
					$canRetire = false;
				}
				else {
					$thresholds = array_values($projectRetireList);
					foreach ( $thresholds as $nextThreshold ) {
						if ( $nextThreshold > $threshold ) {
							$canRetire = false;
							break;
						}
					}
				}
			}
			if ( $canRetire ) {
				
				$fullyClassified[] = $candidate;
			}
			
		}
		
		return $fullyClassified;
	}
	
	private static function retireProject ( $projectId, $threshold ) {
		
		$toRetireArray = self::getFullyClassified ( $projectId, $threshold );
		$retired = array();
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();
		
		foreach ( $toRetireArray as $toRetire ) {
			
			$fields = array(
					$db->quoteName('status') . ' = ' . Sequence::STATUS_FULLY_CLASSIFIED
			);
								
			// Conditions for which records should be updated.
			$conditions = array(
				$db->quoteName('sequence_id') . ' = ' . $toRetire->sequence_id, 
				$db->quoteName('status') . ' = ' . Sequence::STATUS_AVAILABLE
			);
			
			$query = $db->getQuery(true);

			$query->update($db->quoteName('Photo'))->set($fields)->where($conditions);

			$db->setQuery($query);

			$result = $db->execute();
		
			if ( !$result ) {
				error_log ("Retire error - failed to update status to fully classified for Sequence " . $candidate->sequence_id );
			}
			else {
				$retired[] = $toRetire;
			}
		}
		
		return $retired;
		
	}
	
	
	private static function unretireProject ( $projectId, $threshold ) {
		
		$unretireCandidates = array();
		$unretired = array();
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();
		
		if ( $threshold ) {
			
			$query = $db->getQuery(true);
			$query->select("P.sequence_id, P.photo_id, P.site_id from Photo P")
				->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id")
				->innerJoin("(select photo_id, count( distinct person_id) as num_classifiers from Animal where species != 97 group by photo_id) as AC on AC.photo_id = P.photo_id")
				->where("P.status = " . Sequence::STATUS_FULLY_CLASSIFIED)
				->where("P.sequence_num=1")
				->where("AC.num_classifiers < " . $threshold)
				->where("PSM.project_id = " . $projectId )
				->where("P.photo_id >= PSM.start_photo_id and ( P.photo_id <= PSM.end_photo_id or PSM.end_photo_id is NULL )"  );
			$db->setQuery($query);
			$unretireCandidates = $db->loadObjectList();
		}
		else if ( $threshold === null ) {
			
			// Unretire all sequences
			$query = $db->getQuery(true);
			$query->select("P.sequence_id, P.photo_id, P.site_id from Photo P")
				->innerJoin("ProjectSiteMap PSM on PSM.site_id = P.site_id")
				->where("P.status = " . Sequence::STATUS_FULLY_CLASSIFIED)
				->where("P.sequence_num=1")
				->where("AC.num_animals < " . $threshold)
				->where("PSM.project_id = " . $projectId )
				->where("P.photo_id >= PSM.start_photo_id and ( P.photo_id <= PSM.end_photo_id or PSM.end_photo_id is NULL )"  );
			$db->setQuery($query);
			$unretireCandidates = $db->loadObjectList();
			
		}
			
		// Need to check whether each sequence is in any other project with a higher threshold or none.
		foreach ( $unretireCandidates as $candidate ) {

			// No need to check other projects as only one project needed to unretire
			$fields = array(
					$db->quoteName('status') . ' = ' . Sequence::STATUS_AVAILABLE
			);
								
			// Conditions for which records should be updated.
			$conditions = array(
				$db->quoteName('sequence_id') . ' = ' . $candidate->sequence_id, 
				$db->quoteName('status') . ' = ' . Sequence::STATUS_FULLY_CLASSIFIED
			);

			$query = $db->getQuery(true);
			
			$query->update($db->quoteName('Photo'))->set($fields)->where($conditions);

			$db->setQuery($query);

			$result = $db->execute();
		
			if ( !$result ) {
				error_log ("Retire error - failed to update status to available for Sequence " . $candidate->sequence_id );
			}
			else {
				$unretired[] = $candidate;
			}
		}
		return $unretired;
	}
	
		
	public static function myTrappingProjects () {
  
		return self::getProjects ( 'TRAP' );
	
	}
	
	
	public static function mySpottingProjects ($reduce = false) {
	
		return self::getProjects ( 'SPOT', $reduce );
	
	}


	public static function myAdminProjects () {
	
		return self::getProjects ( 'ADMIN' );
	
	}
	
	public static function myAnalysisProjects () {
		
		$adminProjects = self::myAdminProjects();
		
		$trapperProjects = self::myTrappingProjects();
		
		$analysisProjects = array_replace($adminProjects, $trapperProjects);
		
		return $analysisProjects;
	}
	
	public static function getFeaturedProject () {
		
		$projectId = Biodiv::getSetting ( 'featuredproject' );
		$featuredProject = null;
		if ( $projectId ) {
			$projectDetails = codes_getDetails ( $projectId, 'project');
			$featuredProject = new \StdClass();
			$featuredProject->id = $projectId;
			$featuredProject->name = $projectDetails['project_prettyname'];
		}
		return $featuredProject;
	}
	
	// Get all options for a single project by project id or just a particular option type if set
	public static function getSingleProjectOptions ( $project_id, $option_type ) {
		
		$projectoptions = array();
	  
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();
		$query = $db->getQuery(true);
		$query->select("DISTINCT P.project_id, P.project_prettyname, O.struc, O.option_name")->from("Project P");
		$query->innerJoin("ProjectOptions PO on PO.project_id = P.project_id");
		$query->innerJoin("Options O on PO.option_id = O.option_id" );
		$where_str = "P.project_id = ".$project_id;
		if ( $option_type ) $where_str .= " AND O.struc = '" . $option_type . "'";
		$query->where($where_str);
		$query->order("P.project_id" );
		$db->setQuery($query);
		$projectoptions = $db->loadAssocList();
	  
		return $projectoptions;
	}	
	
	// public static function getProjectsWithThreshold ( $projectId = null ) {
		
		// $db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		
		// $projects = array();
		
		// if ( $projectId ) {
			
			// $projectAndSubs = self::getSubProjectsById( $projectId );	
			// $query = $db->getQuery(true);
			// $query->select("DISTINCT P.project_id, P.project_prettyname, OD.value as threshold from Project P")
				// ->leftJoin("ProjectOptions PO on P.project_id = PO.project_id")
				// ->leftJoin("Options O on PO.option_id = O.option_id and O.struc = " . $db->quote('numtoretire'))
				// ->leftJoin("OptionData OD on OD.option_id = O.option_id and OD.data_type = " . $db->quote('threshold'))
				// ->where("P.project_id in (" . implode(',', array_keys($projectAndSubs)) . ")" )
				// ->order("threshold");
			
			// $db->setQuery($query);
			
			// //error_log ( "Project::getProjectsWithThreshold query: " . $query->dump() );

			// $projects = $db->loadAssocList('project_id');
		// }
		// else {
			
			// $query = $db->getQuery(true);
			// $query->select("DISTINCT P.project_id, P.project_prettyname, OD.value as threshold from Project P")
				// ->leftJoin("ProjectOptions PO on P.project_id = PO.project_id")
				// ->leftJoin("Options O on PO.option_id = O.option_id and O.struc = " . $db->quote('numtoretire'))
				// ->leftJoin("OptionData OD on OD.option_id = O.option_id and OD.data_type = " . $db->quote('threshold'))
				// ->order("threshold");
			// $db->setQuery($query);
			// $projects = $db->loadAssocList('project_id');
			
		// }
		
		// return $projects;
	// }
	
	public static function getProjectsWithThreshold ( $projectId = null ) {

		$db = db();

		$projects = array();

		if ( $projectId ) {
			
			$projectAndSubs = self::getSubProjectsById( $projectId );	
			$query = $db->getQuery(true);
			$query->select("DISTINCT P.project_id, P.project_prettyname, OD.value as threshold from Project P")
				->leftJoin("ProjectOptions PO on P.project_id = PO.project_id")
				->leftJoin("Options O on PO.option_id = O.option_id and O.struc = " . $db->quote('numtoretire'))
				->leftJoin("OptionData OD on OD.option_id = O.option_id and OD.data_type = " . $db->quote('threshold'))
				->where("P.project_id in (" . implode(',', array_keys($projectAndSubs)) . ")" )
				->order("threshold");
			
			$db->setQuery($query);
			
			//error_log ( "Project::getProjectsWithThreshold query: " . $query->dump() );

			$projects = $db->loadAssocList('project_id');
		}
		else {
			
			$query = $db->getQuery(true);
			$query->select("DISTINCT P.project_id, P.project_prettyname, OD.value as threshold from Project P")
				->leftJoin("ProjectOptions PO on P.project_id = PO.project_id")
				->leftJoin("Options O on PO.option_id = O.option_id and O.struc = " . $db->quote('numtoretire'))
				->leftJoin("OptionData OD on OD.option_id = O.option_id and OD.data_type = " . $db->quote('threshold'))
				->order("threshold");
			$db->setQuery($query);
			$projects = $db->loadAssocList('project_id');
			
		}

		return $projects;
	}

	public static function getLeafProjectsById($project_id, $include_private = false){

		// first select all project/parent pairs into memory
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();
		$query = $db->getQuery(true);
		$query->select("project_id, parent_project_id, project_prettyname")->from("Project")
			->where("parent_project_id is not NULL");

		// exclude private projects
		if ( $include_private == false ) {
			$query->where("access_level < 3");
		}
		$db->setQuery($query);
		$allpairs = $db->loadAssocList();

		// Need to get the proj id and name of the top level project
		$query = $db->getQuery(true);
		$query->select("project_id AS proj_id, project_prettyname AS proj_prettyname")->from("Project");
		$query->where("project_id = '" . $project_id . "'");
		$db->setQuery($query);
		$subprojects = $db->loadAssocList('proj_id', 'proj_prettyname');

		// add to project list by working through $allpairs to find the children, repeatedly
		self::addSubProjects( $subprojects, $allpairs );

		// remove any that is a parent project
		foreach ( $allpairs as $pair ) {
			$aParent = $pair['parent_project_id'];
			if ( array_key_exists($aParent, $subprojects) ) {
				unset($subprojects[$aParent]);
			}
		}

		return $subprojects;

	}
	
	// public static function getProjectsWithThreshold ( $projectId = null ) {
		
		// $db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		
		// $projects = array();
		
		// if ( $projectId ) {
			
			// $projectAndSubs = self::getSubProjectsById( $projectId );	
			// $query = $db->getQuery(true);
			// $query->select("DISTINCT P.project_id, P.project_prettyname, OD.value as threshold from Project P")
				// ->leftJoin("ProjectOptions PO on P.project_id = PO.project_id")
				// ->leftJoin("Options O on PO.option_id = O.option_id and O.struc = " . $db->quote('numtoretire'))
				// ->leftJoin("OptionData OD on OD.option_id = O.option_id and OD.data_type = " . $db->quote('threshold'))
				// ->where("P.project_id in (" . implode(',', array_keys($projectAndSubs)) . ")" )
				// ->order("threshold");
			
			// $db->setQuery($query);
			
			// //error_log ( "Project::getProjectsWithThreshold query: " . $query->dump() );

			// $projects = $db->loadAssocList('project_id');
		// }
		// else {
			
			// $query = $db->getQuery(true);
			// $query->select("DISTINCT P.project_id, P.project_prettyname, OD.value as threshold from Project P")
				// ->leftJoin("ProjectOptions PO on P.project_id = PO.project_id")
				// ->leftJoin("Options O on PO.option_id = O.option_id and O.struc = " . $db->quote('numtoretire'))
				// ->leftJoin("OptionData OD on OD.option_id = O.option_id and OD.data_type = " . $db->quote('threshold'))
				// ->order("threshold");
			// $db->setQuery($query);
			// $projects = $db->loadAssocList('project_id');
			
		// }
		
		// return $projects;
	// }
	
	private static function getMaxThreshold ( $siteId ) {
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();
		
		$threshold = null;
		
		// NB we do not keep a history of threshold changes
		if ( $siteId ) {
			
			// $projectAndSubs = self::getSubProjectsById( $projectId );	
			// $query = $db->getQuery(true);
			// $query->select("DISTINCT P.project_id, P.project_prettyname, OD.value as threshold from Project P")
				// ->leftJoin("ProjectOptions PO on P.project_id = PO.project_id")
				// ->leftJoin("Options O on PO.option_id = O.option_id and O.struc = " . $db->quote('numtoretire'))
				// ->leftJoin("OptionData OD on OD.option_id = O.option_id and OD.data_type = " . $db->quote('threshold'))
				// ->where("P.project_id in (" . implode(',', array_keys($projectAndSubs)) . ")" )
				// ->order("threshold");
			
			// $db->setQuery($query);
			
			// //error_log ( "Project::getProjectsWithThreshold query: " . $query->dump() );

			// $threshold = $db->loadResult();
		}
		
		return $threshold;
	}
	
	//Photo id included for performance
	private static function getProjectRetireThresholds ( $siteId, $photoId ) {
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();
		
		$query = $db->getQuery(true)
			->select("PO.project_id, MAX(IFNULL(A.value, 0)) as value")
			->from("ProjectOptions PO")
			->leftJoin("(select OD.option_id, OD.value from Options O ".
					"inner join OptionData OD on O.option_id = OD.option_id ".
					"where O.struc = ".$db->quote('numtoretire')." and OD.data_type = ".$db->quote('threshold').
					") A on PO.option_id = A.option_id")
			->innerJoin("ProjectSiteMap PSM on PSM.project_id = PO.project_id and PSM.site_id = " . $siteId)
			->where("PSM.start_photo_id < " . $photoId )
			->where("(PSM.end_photo_id is NULL or PSM.end_photo_id > " . $photoId . ")" )
			->group("PO.project_id");
			
		$db->setQuery($query);
		
		//error_log ( "Project::getProjectRetireThresholds query: " . $query->dump() );

		$projectThresholds = $db->loadAssocList("project_id", "value");
		
		return $projectThresholds;

	}
	
	
	
	// If project_id is null return for all mySpottingProjects.  Option_type is the struc in the Options table.  If null return all.
	public static function getProjectOptions( $project_id, $option_type, $use_exclusions ){
		
		// Call myprojects to get the project list, then get details for each.
		$myprojects = null;
		
		if ( $project_id ) {
			$myprojects = self::getProjects( 'SPOT', false, $project_id );
		}
		else {
			$myprojects = self::mySpottingProjects();
		}

		// if we need to exclude some projects then remove them from the project list here
		if ( $use_exclusions ) {
			
			//$db = (new DatabaseFactory)->getDriver('mysqli', readDbOptions());
			$db = readDb();
			$query = $db->getQuery(true);
			$query->select("DISTINCT P.project_id, P.project_prettyname")->from("Project P");
			$query->innerJoin("ProjectOptions PO on PO.project_id = P.project_id");
			$query->innerJoin("Options O on PO.option_id = O.option_id" );
			$query->where("O.struc = 'exclude'");
			$query->order("P.project_id" );
			$db->setQuery($query);
			$excludeprojects = $db->loadAssocList("project_id");

			$myprojects = array_diff_key ( $myprojects, $excludeprojects );

		}

		$projectdetails = array();

		if ( count($myprojects) > 0 ) {

			$id_string = implode(",", array_keys($myprojects));

			//$db = (new DatabaseFactory)->getDriver('mysqli', readDbOptions());
			$db = readDb();
			$query = $db->getQuery(true);
			$query->select("DISTINCT P.project_id, P.project_prettyname, O.struc, O.option_name")->from("Project P");
			$query->innerJoin("ProjectOptions PO on PO.project_id = P.project_id");
			$query->innerJoin("Options O on PO.option_id = O.option_id" );
			$where_str = "P.project_id in (".$id_string.")";
			if ( $option_type ) $where_str .= " AND O.struc = '" . $option_type . "'";
			$query->where($where_str);
			$query->order("P.project_id" );
			$db->setQuery($query);
			$projectdetails = $db->loadAssocList("project_id");

		}

		return $projectdetails;
	}


	private static function cmpListedProjects($a, $b) {
		if ($a->level == $b->level) {
			
			return ($a->project_prettyname < $b->project_prettyname) ? -1 : 1;
		}
		return ($a->level < $b->level) ? -1 : 1;
	}
	
	
	public static function getChildProjectsById($project_id, $include_private = false){

		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();
        $query = $db->getQuery(true);
        $query->select("project_id as proj_id, project_prettyname as proj_prettyname")->from("Project")
                ->where("parent_project_id = " . (int)$project_id);
        // exclude private projects
        if ( $include_private == false ) {
          $query->where("access_level < 3");
        }
        $query->order("listing_level");
        $db->setQuery($query);

        $children = $db->loadAssocList('proj_id', 'proj_prettyname');
        return $children;
	}
	
	
	public static function getSubProjectsById($project_id, $include_private = false){

		// first select all project/parent pairs into memory
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();
		$query = $db->getQuery(true);
		$query->select("project_id, parent_project_id, project_prettyname")->from("Project")
			->where("parent_project_id is not NULL");
		// exclude private projects
		if ( $include_private == false ) {
			$query->where("access_level < 3");
		}
		$query->order("listing_level");
        $db->setQuery($query);
		$allpairs = $db->loadAssocList();
		
		// Need to get the proj id and name of the top level project
		$query = $db->getQuery(true);
		$query->select("project_id AS proj_id, project_prettyname AS proj_prettyname")->from("Project");
		$query->where("project_id = '" . $project_id . "'");
		$query->order("listing_level");
        $db->setQuery($query);
		$subprojects = $db->loadAssocList('proj_id', 'proj_prettyname');

		// add to project list by working through $allpairs to find the children, repeatedly
		self::addSubProjects( $subprojects, $allpairs );

		return $subprojects;

	}	
	
	private static function addSubProjects (&$projects, &$pairs) {

		foreach ($projects as $proj_id=>$proj_prettyname){

			$project_id = null;
			$parent_project_id = null;
			$project_prettyname = null;
			$addedNew = False;
			foreach ($pairs as $allpairsline){
				extract($allpairsline);
				if ( $parent_project_id == $proj_id and $project_id != null ) {
					if ( !array_key_exists($project_id, $projects) ) {
						$projects += [ $project_id => $project_prettyname ];

						// Added new subproject so flag we will need to call recursively
						$addedNew = True;
					}
				}
			} // foreach $pairs
			if ($addedNew) {
				
				//echo "Calling addSubProjects recursively as added more.<br>";
				self::addSubProjects ( $projects, $pairs );
			}
		}
	}
	
	
	public static function getProjectTree ( $project_id ) {
	
	
	$db = db();
	$query = $db->getQuery(true);
	$regionSub = "select O2.option_name from Options O2 inner join ProjectOptions PO2 on O2.option_id = PO2.option_id and O2.struc = 'region' and PO2.project_id = " . $project_id;
	$query->select("DISTINCT P.project_id, P.project_prettyname, P.project_description, CONCAT(P.dirname,P.image_file) as image_file, P.access_level, O.option_name as priority, (".$regionSub.") as region")->from("Project P");
	$query->innerJoin("ProjectOptions PO on PO.project_id = P.project_id");
	$query->innerJoin("Options O on PO.option_id = O.option_id");
	$query->where("O.struc = 'priority'");
	$query->where("P.project_id = ".$project_id);
	
	$db->setQuery($query);

	$result = $db->loadObject();
	
	return new Project ( $result->project_prettyname, $result->project_id, $result->project_description, 
						$result->access_level, $result->priority, $result->image_file, $result->region );
}

	
	
	// $access can be SPOT, TRAP, ADMIN, LIST
	// Get all projects for the given access.  If project id is null get all for this user.  If project id is set get that plus subs
	public static function getProjects ( $access, $reduce=false, $projectId = null ) {
		
		$returnArray = array();
		
		$allProjects = self::getAccessProjectsWithSubs ( $access );
		
		if ( $projectId ) {
			$returnArray = $allProjects[$projectId];
		}
		else {
			foreach ( $allProjects as $id=>$projects ) {
				$returnArray +=  $projects;
			}
		}
		
		if ( $reduce ) {
		  
			// Only want the private projects plus the projects that don't have a parent already in the list.
			//$db = (new DatabaseFactory)->getDriver('mysqli', readDbOptions());
			$db = readDb();

			$query = $db->getQuery(true);
			$query->select("DISTINCT P.project_id AS proj_id, P.project_prettyname AS proj_prettyname, P.parent_project_id as parent_id, P.access_level as access_level")->from("Project P");
			$db->setQuery($query);
			$fullList = $db->loadAssocList('proj_id');
			
			$allreduced = array();
			
			foreach ( $fullList as $projId=>$row ) {
				$parent = $row['parent_id'];
				$level = $row['access_level'];
				if ( $level == 3 ) {
					$allreduced[$projId] = $row;
				}
				else if ( !$parent or !array_key_exists($parent, $returnArray ) ) {
					$allreduced[$projId] = $row;
				}
			}
			
			$returnArray = array_intersect_key ( $returnArray, $allreduced );
		}
		
		asort($returnArray);
		
		return $returnArray;
	}
	
	
	
	// $access can be SPOT, TRAP, ADMIN, LIST
	// Return array with access for all project ids.  If there is no access to that project od it will not appear on its own list, if there is it will.
	private static function getAccessProjectsWithSubs ( $access ) {
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', readDbOptions());
		$db = readDb();
		$query = $db->getQuery(true);
		$query->select("project_id, IFNULL(parent_project_id,0) as parent_id, project_prettyname as project_name, access_level")->from("Project P");
		
		$db->setQuery($query);

		$projectRows = $db->loadAssocList('project_id');
		
		$returnArray = array();
		
		// Full access is public plus hybrid for spotting, public for trapping, ?? for admin and public plus hybris plus restricted for list
		$allProjects = array();
		
		// Controlled access depends on access type but these are the sub projects with access level requiring control
		$controlledAccess = array();
		
		// Private access treated separately as have to be admin of parent to have access
		$privateAccess = array();
		
		// First create the children arrays for each project
		foreach ( $projectRows as $projectId=>$row ) {
			
			// create array for possible children of this project, one on full access list and one on controlled list if hybrid or restriced, exclude private as must have explicit access or admin access to an ancestor
			$allProjects[$projectId] = array();
			
			$accessLevel = $row['access_level'];
			
			switch ( $access ) {
				case "TRAP":
					if ( $accessLevel == 3 ) {
						$privateAccess[$projectId] = array();
					}
					else if ( $accessLevel > 0 ) {
						$controlledAccess[$projectId] = array();
					}
					break;
					
				case "SPOT":
					if ( $accessLevel == 3 ) {
						$privateAccess[$projectId] = array();
					}
					else if ( $accessLevel > 1 ) {
						$controlledAccess[$projectId] = array();
					}
					break;
					
				case "LIST":
					break;
				
				case "ADMIN":
					// Must be admin of this or an ancestor to have admin access, for ALL projects
					// Private are treated as restricted for ADMIN projects
					$controlledAccess[$projectId] = array();
					break;
					
				default:
					$controlledAccess[$projectId] = array();
					
			}
			
		}
		
		// Add subs to controlled list where relevant so can get at them based on user access list 
		foreach ( $projectRows as $projectId=>$row ) {
			
			$projectName = $row['project_name'];
			$parentId = $row['parent_id'];
			$accessLevel = $row['access_level'];
			
			$isControlled = false;
			$isPrivate = false;
			
			switch ( $access ) {
				case "TRAP":
					if ( $accessLevel == 3 ) {
						$isPrivate = true;
					}
					else if ( $accessLevel > 0 and $accessLevel < 3 ) {
						$isControlled = true;
					}
					break;
					
				case "SPOT":
					if ( $accessLevel == 3 ) {
						$isPrivate = true;
					}
					else if ( $accessLevel > 1 and $accessLevel < 3 ) {
						$isControlled = true;
					}
					break;
					
				case "LIST":
					$isControlled = false;
					break;
				
				case "ADMIN":
					$isControlled = true;
					break;
					
				default:
					$isControlled = true;
					break;
			}
			
			if ( $isControlled ) {
				if ( $parentId != 0 ) {
					
					$parent = $parentId;
					
					while ( $parent != 0 ) {
						
						if ( array_key_exists ( $parent, $controlledAccess ) ) {
							if ( !array_key_exists($projectId, $controlledAccess[$parent]) ) $controlledAccess[$parent][$projectId] = $projectName;
						}
						else {
							// Add the parent with the child so we can check for access later
							$controlledAccess[$parent] = array();
							$controlledAccess[$parent][$projectId] = $projectName;
						}
										
						// Get the parent's parent
						$parent = $projectRows[$parent]['parent_id'];
					}
				}
			}			
			if ( $isPrivate ) {
				if ( $parentId != 0 ) {
					
					//error_log ( "getProjectsWithSubs parent id not null" );
					
					$parent = $parentId;
					
					while ( $parent != 0 ) {
						
						if ( array_key_exists ( $parent, $privateAccess ) ) {
							if ( !array_key_exists($projectId, $privateAccess[$parent]) ) $privateAccess[$parent][$projectId] = $projectName;
						}
						else {
							// Add the parent with the child so we can check for access later
							$privateAccess[$parent] = array();
							$privateAccess[$parent][$projectId] = $projectName;
						}
										
						// Get the parent's parent
						$parent = $projectRows[$parent]['parent_id'];
					}
				}
			}		
		}
		
		// Now to get the access for this user
		$personId = Biodiv::userID();
		
		$query = $db->getQuery(true);
		$query->select("PUM.project_id as project_id, PUM.role_id as role_id, P.access_level as access_level")
			->from("ProjectUserMap PUM")
			->innerJoin ("Project P on P.project_id = PUM.project_id")
			->where("PUM.person_id = " . $personId );
		
		$db->setQuery($query);

		$userAccess = $db->loadAssocList();
		
		$userAccessList = array();
		$adminAccessList = array();
		
		foreach ( $userAccess as $row ) {
			
			$roleId = $row['role_id'];
			$projId = $row['project_id'];
			$projLevel = $row['access_level'];
			
			// Keep an admin list and a user list
			if ( $roleId == 1 ) {
				$adminAccessList[] = $projId;
				$userAccessList[] = $projId;
			}
			else if ( $projLevel > 0 ) {
				$userAccessList[] = $projId;
			}
						
		}
		
		if ( $access == 'ADMIN' ) {
			$fullUserList = array() + $adminAccessList;
		}
		else {
			$fullUserList = array() + $userAccessList;
		}
		
		
		// Add additional subs to full user list
		foreach ( array_keys($controlledAccess) as $controlledParent ) {
			
			if ($access == 'ADMIN' ) {
				if  (in_array($controlledParent, $adminAccessList) ) {
					
					$controlledSubs = array_keys($controlledAccess[$controlledParent]);
					
					$fullUserList = array_merge ( $fullUserList, $controlledSubs );
				}
			}
			else {
				if  (in_array($controlledParent, $userAccessList) ) {
					
					$controlledSubs = array_keys($controlledAccess[$controlledParent]);
					
					$fullUserList = array_merge ( $fullUserList, $controlledSubs );
				}
			}
		}
		// Add additional from private list to full user list
		foreach ( array_keys($privateAccess) as $privateParent ) {
			
			if  (in_array($privateParent, $adminAccessList) ) {
				
				$privateSubs = array_keys($privateAccess[$privateParent]);
				
				$fullUserList = array_merge ( $fullUserList, $privateSubs );
			}
		}
		
		// Now add all subs to result, only include restricted where they are on the list.
		foreach ( $projectRows as $projectId=>$row ) {
			
			$projectName = $row['project_name'];
			$parentId = $row['parent_id'];
			$accessLevel = $row['access_level'];
			
			$isControlled = false;
			
			switch ( $access ) {
				case "TRAP":
					if ( $accessLevel > 0 ) $isControlled = true;
					break;
					
				case "SPOT":
					if ( $accessLevel > 1 ) $isControlled = true;
					break;
					
				case "LIST":
					if ( $accessLevel > 2 ) $isControlled = true;
					break;
				
				case "ADMIN":
					$isControlled = true;
					break;
					
				default:
					$isControlled = true;
			}
			
			// Add project to its own list if have access
			if ( $isControlled ) {
				// 
				if ( in_array ( $projectId, $fullUserList ) ){
					$allProjects[$projectId][$projectId] = $projectName;
				}
			}
			else {
				$allProjects[$projectId][$projectId] = $projectName;
			}
			
			if ( $parentId != 0 ) {
				
				$parent = $parentId;
				
				while ( $parent != 0 ) {
					
					// Only add this project if it's not controlled or is controlled and on the user list.
					if ( $isControlled ) {
						// Add the child to the array of the parent, if it is on the user list
						if ( in_array ( $projectId, $fullUserList ) ) {
							
							//error_log ( "Adding " . $projectId . " to list for parent " . $parent );
							if ( !array_key_exists($projectId, $allProjects[$parent]) ) $allProjects[$parent][$projectId] = $projectName;
						}
					}
					else {
						//error_log ( "Adding " . $projectId . " to list for parent " . $parent );
						if ( !array_key_exists($projectId, $allProjects[$parent]) ) $allProjects[$parent][$projectId] = $projectName;
					}
									
					// Get the parent's parent
					$parent = $projectRows[$parent]['parent_id'];
				}
			}	
		}
		
		return $allProjects;
		
	}
	
	
	// Return some upload and classification data for the project (used in displaying charts)
	public static function projectData ( $project_id, $num_months, $interval_in_months = 1, $end_date = null ) {
  
		// check project id is public or protected, or that this user can acess this project??
		$fields = new \stdClass();
		$fields->project_id = $project_id;
			  
		if ( !Biodiv::canView('project',$fields) ) {
			return array();
		}

		// Get all the text snippets for this view in the current language
		//$translations = getTranslations("project");

		$title = Text::_("COM_BIODIV_PROJECT_SEQ_DFLT");

		if ( $num_months == 6 ) $title = Text::_("COM_BIODIV_PROJECT_SEQ_SIXM");
		else if ( $num_months == 12 ) $title = Text::_("COM_BIODIV_PROJECT_SEQ_ONEYR");
		else if ( $num_months == 36 ) $title = Text::_("COM_BIODIV_PROJECT_SEQ_THRYR");

		//$numDisplayedMonths = 6;
		$numDisplayedMonths = $num_months + $interval_in_months;
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

		$projects = self::getSubProjectsById( $project_id );
		$id_string = implode(",", array_keys($projects));

		$uploadedArray = array();
		//$classifiedArray = array();
		$queuedArray = array();
		$screenedOutArray = array();
		$partClassifiedArray = array();
		$fullyClassifiedArray = array();
		$unclassifiedArray = array();
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();

		for ( $j=0; $j<$numIntervals; $j++ ) {
			$query = $db->getQuery(true);
			//$query->select("sum(num_uploaded), sum(num_classified) from Statistics");
			//$query->select("sum(num_uploaded), sum(num_classified), sum(queued), sum(screened_out), sum(fully_classified) from Statistics");
			$query->select("sum(num_uploaded), sum(queued), sum(screened_out), sum(part_classified), sum(fully_classified), sum(unclassified) from Statistics");
			$query->where("project_id in (" . $id_string . ")" );
			$query->where("end_date = " . $datesArray[$j+1] );
			$db->setQuery($query);

			$row = $db->loadRow();

			array_push ( $uploadedArray, $row['0'] );
			//array_push ( $classifiedArray, $row['1'] );
			array_push ( $queuedArray, $row['1'] );
			array_push ( $screenedOutArray, $row['2'] );
			array_push ( $partClassifiedArray, ($row['3']) );
			array_push ( $fullyClassifiedArray, $row['4'] );
			array_push ( $unclassifiedArray, $row['5'] );
		}


		$projectData = array ( 
			"labels" => $labelsArray,
			"uploaded" => $uploadedArray,
			//"classified" => $classifiedArray,
			"queued" => $queuedArray,
			"screenedout" => $screenedOutArray,
			"partclassified" => $partClassifiedArray,
			"fullyclassified" => $fullyClassifiedArray,
			"unclassified" => $unclassifiedArray,
			//"cla_label" => Text::_("COM_BIODIV_PROJECT_CLASSIFIED"),
			"upl_label" => Text::_("COM_BIODIV_PROJECT_UPLOADED"),
			"q_label" => Text::_("COM_BIODIV_PROJECT_QUEUED"),
			"scr_label" => Text::_("COM_BIODIV_PROJECT_SCREENED"),
			"pcl_label" => Text::_("COM_BIODIV_PROJECT_PART_CLASSIFIED"),
			"fcl_label" => Text::_("COM_BIODIV_PROJECT_FULLY_CLASSIFIED"),
			"ucl_label" => Text::_("COM_BIODIV_PROJECT_UNCLASSIFIED"),
			"title" => $title
			
			);

		return $projectData;
	}
	
	
	// Return some animal data for the project (used in displaying charts)
	// num_species is the max number of different species to return prioritised by number, if null use all, if not all animals are included the last entry is 
	// a combined number for everything else named "Other"
	public static function projectAnimals ( $project_id, $num_species = null, $include_dontknow = false, $include_human = false, $include_nothing = false ) {

		// check project id is public or protected, or that this user can acess this project??
		$fields = new \StdClass();
		$fields->project_id = $project_id;
			  
		if ( !Biodiv::canView('project',$fields) ) {
			return array();
		}

		// Get all the text snippets for this view in the current language
		//$translations = getTranslations("project");

		$title = Text::_("COM_BIODIV_PROJECT_BY_SP");

		$projects = self::getSubProjectsById( $project_id );
		$id_string = implode(",", array_keys($projects));

		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();
		$query = $db->getQuery(true);
		// count every classification
		//$query->select("species, count(distinct start_photo_id) as num_animals FROM ProjectAnimals");
		$query->select("option_id, species, sum(num_animals) as num_animals FROM AnimalStatistics");
		$query->where("project_id in (" . $id_string . ")" );
		$query->group("species");
		$query->order("num_animals  DESC");
		$db->setQuery($query);

		// Include id...
		$animals_w_id = $db->loadAssocList('species');

		$animals = $db->loadAssocList('species', 'num_animals');
		//$animals = array_column($animals_w_id, 'num_animals', 'species');

		// Little fix to remove the additional text on Nothing and Human.
		$array_changed = false;
		if (isset($animals["Human <span class='fa fa-male'/>"])) {
			$animals['Human'] = $animals["Human <span class='fa fa-male'/>"];
			unset($animals["Human <span class='fa fa-male'/>"]);
			$array_changed = true;
		}
		if (isset($animals["Nothing <span class='fa fa-ban'/>"])) {
			$animals['Nothing'] = $animals["Nothing <span class='fa fa-ban'/>"];
			unset($animals["Nothing <span class='fa fa-ban'/>"]);
			$array_changed = true;
		}

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
			$animals = akrem($animals, "Other");
		}

		if ( $num_species && (count($animals) > $num_species) ) {
			$animals_to_return_en = array_slice($animals, 0, $num_species-1);
			$total_other = array_sum(array_slice($animals, $num_species-1)) + $num_other;
			$animals_to_return_en['Other Species'] = strval( $total_other );
		}
		else {
			$animals_to_return_en = $animals;
		}

		$animals_to_return = array();
		foreach ( $animals_to_return_en as $sp=>$num ) {
			//error_log( "animal row: " . $sp . ", " . $num );
			if ( $sp == "Other Species" ) {
			 
				$animals_to_return[Text::_("COM_BIODIV_PROJECT_OTHER_SP")] = $num;
			  
			}
			else {
				//error_log ( "key = " . codes_getName($animals_w_id[$sp]["option_id"], "speciestran") );
				$animals_to_return[codes_getName($animals_w_id[$sp]["option_id"], "speciestran")] = $num;
			}
		}

		return array (
			"labels" => array_keys($animals_to_return),
			"animals" => array_values($animals_to_return),
			"title" => $title
			);
		
	}

	
	public static function getProjectUsers ( $projectId ) {
		
		$options = dbOptions();
		$userDb = $options['userdb'];
		$prefix = $options['userdbprefix'];
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', $options);
		$db = db();
			
		// For now, simple, but would be good to add any users of parent projects...
		$query = $db->getQuery(true)
			->select("PUM.person_id, U.username, U.name, U.email, R.display_text as role from ProjectUserMap PUM")
			->innerJoin($userDb . "." . $prefix ."users U on PUM.person_id = U.id")
			->innerJoin("Role R on R.role_id = PUM.role_id")
			->where("PUM.project_id = " . $projectId)
			->order("U.email");
			
		
		$db->setQuery($query);
		
		//error_log("ProjectUsers select query created: " . $query->dump());
		
		$users = $db->loadObjectList();
		
		return $users;
	}
	
	
	public static function addProjectUsers ( $projectId, $userEmails ) {
		
		$adminProjects = self::myAdminProjects();
	
		$messages = array();
		
		if ( array_key_exists($projectId, $adminProjects ) ) {
			
			if ( $userEmails ) {
				
				//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
				$db = db();
				$query = $db->getQuery(true);
				$query->select("role_id from Role where role_name = " . $db->quote("User"));
				$db->setQuery($query);			
				$userRole = $db->loadResult();			
				
				$emailArray = explode(',', $userEmails);
				
				$helper = new BiodivHelper();
				
				foreach ( $emailArray as $email ) {
					
					// Sanitise and validate email format
					$email = filter_var($email, FILTER_SANITIZE_EMAIL);
					
					if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
						$messages[] = Text::_("COM_BIODIV_PROJECTUSERS_ADD_FAIL") . ' ' . $email . ' ' . JText::_("COM_BIODIV_PROJECTUSERS_NOT_EMAIL");
					}
					else {
					
						// Check whether an existing user
						try {
							$existingUserEmail = $helper->getUser ( $email );
						}
						catch (Exception $e ) {
							$messages[] = Text::_("COM_BIODIV_PROJECTUSERS_ADD_FAIL") . " - " . $e->getMessage();
						}
						if ( $existingUserEmail ) {
							
							$newProjectUser = new \StdClass;
							$newProjectUser->person_id = $existingUserEmail->id;
							$newProjectUser->project_id = $projectId;
							$newProjectUser->role_id = $userRole;
							
							$result = false;
							try {
								$result = $db->insertObject("ProjectUserMap", $newProjectUser);
							} 
							catch ( Exception $e ) {
								$messages[] = $email . ': ' . $e->getMessage();
							}
							
							if ( $result ) {
								$messages[] = Text::_("COM_BIODIV_PROJECTUSERS_USER_ADDED") . ' ' . $email;
							}
							else {
								$messages[] = Text::_("COM_BIODIV_PROJECTUSERS_EXISTING_FAIL") . ' ' . $email;
							}
						}
						else {
							$messages[] = Text::_("COM_BIODIV_PROJECTUSERS_ADD_FAIL") . ' ' . $email . ' ' . Text::_("COM_BIODIV_PROJECTUSERS_NOT_USER");
						}
					}
				}
				
			}
		}
		else {
			$messages[] = Text::_("COM_BIODIV_PROJECTUSERS_NOT_ADMIN");
		}
		return $messages;
	}
	
	
	public static function getSiteDataStrucs ( $projectIds ) {
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();
			
		$project_ids = implode(',', $projectIds);
		$query = $db->getQuery(true);
		$query->select("DISTINCT PO.project_id, O.option_id, O.option_name as struc")
			->from("ProjectOptions PO")
			->innerJoin("Options O on O.option_id = PO.option_id and O.struc = 'sitedatastruc'")
			->where("PO.project_id in (" . $project_ids . ")");
		$db->setQuery($query);
		$sitedatastrucs = $db->loadAssocList();
		return $sitedatastrucs;
	}


	public static function getLogos ( $projectId ) {
		
		$logos = array();
		
		if ( $projectId ) {
			// Find logos for this project
			//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
			$db = db();
			$query = $db->getQuery(true);
			$query->select("OD.value")->from("OptionData OD");
			$query->innerJoin("ProjectOptions PO on PO.option_id = OD.option_id");
			$query->innerJoin("Options O on PO.option_id = O.option_id");
			$query->where("PO.project_id = " . $projectId );
			$query->where("OD.data_type = 'logo'" );
			$query->order("O.seq");
			$db->setQuery($query);
			$logos = $db->loadColumn();
		}
		return $logos;
	}
	
	public static function getKioskIntroCategory ( $projectId ) {
		
		//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		$db = db();
		
		$query = $db->getQuery(true)
				->select("OD.value")
				->from("OptionData OD")
				->innerJoin("Options O on O.option_id = OD.option_id and O.struc = 'kiosk'")
				->innerjoin("ProjectOptions PO on PO.option_id = OD.option_id and OD.data_type = 'introcategory'")
				->where("PO.project_id = " . $projectId);
		$db->setQuery($query); 
		
		$categoryId = $db->loadResult();
		
		return $categoryId;
	}
	
	public static function getCategoryContent ( $categoryId ) {
		
		$dbo = Factory::getContainer()->get('DatabaseDriver');
		
		$query1 = $dbo->getQuery(true)
			->select('*')
			->from('#__content')
			->where('catid = ' . $categoryId )
			->where('state = 1')
			->order('ordering');
		
		
		$dbo->setQuery($query1);
		
		$res = $dbo->loadObjectList();
		
		return $res;
		
	}

	public static function getMapSettings ( $projectId ) {
		
		// Get the map settings for the project
		$projectMapSettings = null;
		
		if ( $projectId ) {
			
			//$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
			$db = db();
			$query = $db->getQuery(true)
				->select("OD.data_type, OD.value")
				->from("OptionData OD")
				->innerJoin("Options O on O.option_id = OD.option_id and O.struc = 'kiosk'")
				->innerjoin("ProjectOptions PO on PO.option_id = OD.option_id and OD.data_type in ('mapcentre', 'mapzoom')")
				->where("PO.project_id = " . $projectId);
			$db->setQuery($query); 

			$projectMapSettings = $db->loadAssocList('data_type', 'value');
		}
		
		return $projectMapSettings;
	}
}

?>
