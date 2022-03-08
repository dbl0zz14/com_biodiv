<?php

namespace Biodiv;


// No direct access to this file
defined('_JEXEC') or die;



class SchoolSpecies {
	
	private $name;
	private $article;
	private $image;
	
	
	
	function __construct( $name, $article, $image )
	{
		
		$this->name = $name;
		$this->article = $article;
		$this->image;
	}
	
	
	public static function getUnlockedSpecies () {
		
		$personId = userID();
		
		$userTaskTable = "StudentTasks";
		if ( SchoolCommunity::isTeacher( $personId ) ) {
			$userTaskTable = "TeacherTasks";
		}
		
		$species = array();
		
		if ( $personId ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true)
				->select("T.task_id as task_id, T.species as name, T.species_article_id as article, T.image as image from Task T ")
				->innerJoin($userTaskTable . " UT on T.task_id = UT.task_id and UT.species_unlocked = 1 and UT.person_id = " . $personId )
				->order("T.species");		
			
			$db->setQuery($query);
			
			//error_log("SchoolSpecies::getUnlockedSpecies  select query created: " . $query->dump());

			$species = $db->loadObjectList();
		}
		
		return $species;
	}
	
	
	
	
	
}



?>

