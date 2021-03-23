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
* HTML View class for the Projects page 
*
* @since 0.0.1
*/
class BioDivViewDashLikes extends JViewLegacy
{
 
  
   /**
   *
   * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
   *
   * @return  void
   */
  public function display($tpl = null) 
  {
    error_log ( "Likes view display called" );
		
	$this->personId = (int)userID();
	
	// Get all the text snippets for this view in the current language
	$this->translations = getTranslations("dashlikes");
	
	
	if ( $this->personId ) {
		
		
		$app = JFactory::getApplication();
		
		$input = $app->input;
		
		$this->siteId = $input->get('site', 0, 'INT');
		error_log ( "Likes view.  Site id = " . $this->siteId );

		$this->speciesId = $input->get('species', 0, 'INT');
		error_log ( "Likes view.  Species id = " . $this->speciesId );
		
		$this->year = $input->get('year', 0, 'INT');
		error_log ( "Likes view.  Year = " . $this->year );
		
		$this->likedByOthers = $input->get('other', 0, 'INT');
		error_log ( "Likes view.  Liked by others = " . $this->likedByOthers );
		
		$this->sortBy = $input->get('sort', 'liked', 'string');
		error_log ( "Likes view.  Sort = " . $this->sortBy );
		
		$this->page = $input->get('page', 0, 'INT');
		error_log ( "Likes view.  Page = " . $this->page );
		
		$this->numPerPageStr = $input->get('number', 3, 'string');
		error_log ( "Likes view.  Num per page = " . $this->numPerPageStr );	

		if ( $this->numPerPageStr == "all" ) {
			$start = 0;
		}
		else {
			$this->numPerPage = intval($this->numPerPageStr);
			$start = ($this->page) * $this->numPerPage;	
		}			
				
		$this->userTimezone = userTimezone();
		
		$errMsg = print_r ( $this->userTimezone, true );
		error_log ( "tz detail: " . $errMsg );
		
		//Makes sure the timezone is available
		get_object_vars($this->userTimezone);
		
		$this->languageTag = langTag();
		
		
		
		$db = JDatabase::getInstance(dbOptions());
		
		// Are these liked by the user or by others?
		if ( $this->likedByOthers == 1 ) {
			$likeText = "A.species = 97 and A.person_id != " . $this->personId;
			$speciesExistsText = "A2.species != 97 and A2.person_id = A.person_id";
			$speciesFilterText = "A3.species = " . $this->speciesId . " and A3.person_id = A.person_id" ;

		}
		else {
			$likeText = "A.species = 97 and A.person_id = " . $this->personId;
			$speciesExistsText = "A2.species != 97 and A2.person_id = " . $this->personId;
			$speciesFilterText = "A3.species = " . $this->speciesId . " and A3.person_id = " . $this->personId;
		}
		
		
		// Set up the select arrays in value=>displayedValue form
		
		// Number per page
		$this->numberSelect = array("3"=>"3", "6"=>"6", "all"=>$this->translations['all']['translation_text']);
		
		$errMsg = print_r ( $this->numberSelect, true );
		error_log ( "numberSelect: " . $errMsg );
		
		// Sort by
		$this->sortSelect = array("site"=>$this->translations['sort_site']['translation_text'],
								"liked"=>$this->translations['sort_liked']['translation_text'],
								"taken"=>$this->translations['sort_taken']['translation_text']
								);
		
		$errMsg = print_r ( $this->sortSelect, true );
		error_log ( "sortSelect: " . $errMsg );
		
		// Filter by site
		$query = $db->getQuery(true);
		$query->select("distinct S.site_name, S.site_id" )
			->from("Site S")
			->innerJoin("Photo P on P.site_id = S.site_id and P.person_id = " . $this->personId)
			->innerJoin("Animal A on A.photo_id = P.photo_id")
			->where($likeText)
			->order("S.site_name");
			
		$db->setQuery($query);
		
		error_log("Site select query created: " . $query->dump());
				
		$this->siteSelect = $db->loadAssocList("site_id", "site_name");
		$this->siteSelect = array($this->translations['all']['translation_text']) + $this->siteSelect;
		
		$errMsg = print_r ( $this->siteSelect, true );
		error_log ( "siteSelect: " . $errMsg );
			
		
		// Filter by year taken
		$query = $db->getQuery(true);
		$query->select("distinct YEAR(P.taken) as year_taken" )
			->from("Photo P")
			->innerJoin("Animal A on A.photo_id = P.photo_id and P.person_id = " . $this->personId)
			->where($likeText)
			->order("year_taken");
			
		$db->setQuery($query);
		
		error_log("Year select query created: " . $query->dump());
		
		$this->yearSelect = $db->loadAssocList("year_taken", "year_taken");
		$this->yearSelect = array($this->translations['all']['translation_text']) + $this->yearSelect;
		
		$errMsg = print_r ( $this->yearSelect, true );
		error_log ( "yearSelect: " . $errMsg );
				
		
		// Filter by species
		$query = $db->getQuery(true);
		
		if ( $this->languageTag == 'en-GB' ) {
		
			$query->select("distinct A2.species as species_id, O.option_name as species_name" )
				->from("Animal A")
				->innerJoin("Animal A2 on A.photo_id = A2.photo_id ")
				->innerJoin("Options O on O.option_id = A2.species")
				->where($likeText)
				->where($speciesExistsText)
				->order("O.option_name");
				
			$db->setQuery($query);
		}
		else {
		
			$query->select("distinct A2.species as species_id, OD.value as species_name" )
				->from("Animal A")
				->innerJoin("Animal A2 on A.photo_id = A2.photo_id ")
				->innerJoin("OptionData OD on OD.option_id = A2.species and OD.data_type = " . $this->languageTag)
				->where($likeText)
				->where($speciesExistsText)
				->order("OD.value");
				
			$db->setQuery($query);
		}
		
		$this->speciesSelect = $db->loadAssocList("species_id", "species_name");
		$this->speciesSelect = array($this->translations['all']['translation_text']) + $this->speciesSelect;
		
		$errMsg = print_r ( $this->speciesSelect, true );
		error_log ( "speciesSelect: " . $errMsg );
		
		
		// Set up the query
		
		// Additional clauses based on params
		switch ( $this->sortBy ) {
			case "site":
				$orderStr = "S.site_name";
				break;
				
			case "taken":
				$orderStr = "P.taken desc";
				break;
				
			case "liked":
				$orderStr = "A.timestamp desc";
				break;
				
			default:
				$orderStr = "A.timestamp desc";
		}
		
		
		
		$this->likesArray = array();
		
		error_log ("Timezone string = " . $this->userTimezone->timezone );
	
		if ( $this->languageTag == 'en-GB' ) {
			
			$query = $db->getQuery(true);
			$query->select("S.site_name, P.sequence_id, P.upload_filename, P.taken, CONVERT_TZ(A.timestamp,'UTC','".$this->userTimezone->timezone."') as like_time, GROUP_CONCAT(DISTINCT O.option_name ORDER BY  O.option_name SEPARATOR ', ') as species" )
				->from("Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id and P.person_id = " . $this->personId)
				->innerJoin("Site S on S.site_id = P.site_id")
				->innerJoin("Animal A2 on A2.photo_id = A.photo_id")
				->innerJoin("Options O on O.option_id = A2.species")
				->where($likeText)
				->where($speciesExistsText);
			
			if ( $this->speciesId != 0 ) {
				$query->innerJoin("Animal A3 on A3.photo_id = A.photo_id")
				->where("A3.species = " . $this->speciesId . " and A3.person_id = " . $this->personId);
			}
				
			if ( $this->siteId != 0 ) {
				$query->where("S.site_id = " . $this->siteId);
			}
				
			if ( $this->year != 0 ) {
				$query->where("YEAR(P.taken) = " . $this->year);
			}
				
			$query->group("S.site_name, P.sequence_id, P.filename, P.taken, A.timestamp")
				->order($orderStr);
				
			if ( $this->numPerPageStr == "all" ) {
				$db->setQuery($query);
			}
			else {
				$db->setQuery($query, $start, $this->numPerPage);
			}
			
			error_log("Likes query created: " . $query->dump());
			
			$this->likesArray = $db->loadAssocList();
		}
		else {
			$query = $db->getQuery(true);
			$query->select("S.site_name, P.sequence_id, P.upload_filename, P.taken, CONVERT_TZ(A.timestamp,'UTC','".$this->userTimezone->timezone."') as like_time, GROUP_CONCAT(DISTINCT OD.value ORDER BY  OD.value SEPARATOR ', ') as species" )
				->from("Animal A")
				->innerJoin("Photo P on A.photo_id = P.photo_id and P.person_id = " . $this->personId)
				->innerJoin("Site S on S.site_id = P.site_id")
				->innerJoin("Animal A2 on A2.photo_id = A.photo_id")
				->innerJoin("OptionData OD on OD.option_id = A2.species and OD.data_type = '" . $this->languageTag . "'" )
				->where($likeText)
				->where($speciesExistsText);
				
			if ( $this->speciesId != 0 ) {
				$query->innerJoin("Animal A3 on A3.photo_id = A.photo_id")
				->where("A3.species = " . $this->speciesId . " and A3.person_id = " . $this->personId);
			}
				
			if ( $this->siteId != 0 ) {
				$query->where("S.site_id = " . $this->siteId);
			}
				
			if ( $this->year != 0 ) {
				$query->where("YEAR(P.taken) = " . $this->year);
			}
			
			$query->group("S.site_name, P.sequence_id, P.filename, P.taken, A.timestamp")
				->order($orderStr);
			
			if ( $this->numPerPageStr == "all" ) {
				$db->setQuery($query);
			}
			else {
				$db->setQuery($query, $start, $this->numPerPage);
			}
			error_log("Likes query created: " . $query->dump());
			
			$this->likesArray = $db->loadAssocList();
			
		}
		
		
		$errMsg = print_r ( $this->likesArray, true );
		error_log ( "Likes array: " . $errMsg );
		
		$this->numLikes = count($this->likesArray);	
		
		
		
		// Get the total number (without pagination)
		$query = $db->getQuery(true);
		$query->select("count(distinct A.photo_id)")
			->from("Animal A")
			->innerJoin("Photo P on A.photo_id = P.photo_id and P.person_id = " . $this->personId)
			->innerJoin("Site S on S.site_id = P.site_id")
			->innerJoin("Animal A2 on A2.photo_id = A.photo_id")
			->innerJoin("Options O on O.option_id = A2.species")
			->where($likeText)
			->where($speciesExistsText);
		
		if ( $this->speciesId != 0 ) {
			$query->innerJoin("Animal A3 on A3.photo_id = A.photo_id")
			->where($speciesFilterText);
		}
			
		if ( $this->siteId != 0 ) {
			$query->where("S.site_id = " . $this->siteId);
		}
			
		if ( $this->year != 0 ) {
			$query->where("YEAR(P.taken) = " . $this->year);
		}		
		
		$db->setQuery($query);
		
		error_log("Total likes query created: " . $query->dump());
		
		$this->totalLikes = $db->loadResult();
	
	}
	
	
    // Display the view
    parent::display($tpl);
  }
}



?>