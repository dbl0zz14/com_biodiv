<?php

namespace Biodiv;


// No direct access to this file
defined('_JEXEC') or die;



class EventLog {
	
	private $personId;
	
	private $today;
	
	private $yesterday;
	
	private $thisWeek;
	
	private $earlier;
	
	
	function __construct()
	{
		$this->personId = userID();
		
		$schoolRoles = SchoolCommunity::getSchoolRoles();
		
		$this->mySchools = array_column ( $schoolRoles, "school_id" );
		
	}
	
	public function todaysEvents () {
		
		if ( $this->personId ) {
			
			if ( !$this->today ) {
			
		
				$problem = true;
				
				$options = dbOptions();
				$userDb = $options['userdb'];
				$prefix = $options['userdbprefix'];
				
				$schoolString = implode ( ',', $this->mySchools );
			
				$db = \JDatabaseDriver::getInstance($options);
			
				$query1 = $db->getQuery(true)
						->select("U.username, U.name, EL.*, A.image as avatar, S.image as school_image, S.name as school_name from EventLog EL")
						->innerJoin($userDb . "." . $prefix ."users U on EL.person_id = U.id")
						->innerJoin("SchoolUsers SU on SU.person_id = EL.person_id")
						->innerJoin("School S on S.school_id = SU.school_id")
						->innerJoin("Avatar A on A.avatar_id = SU.avatar")
						->where("EL.person_id = " . $this->personId)
						->where("date(EL.event_date)= DATE(NOW())"); 
						
						// - INTERVAL 1 DAY)
						//->where("DATEDIFF(EL.event_date, CURRENT_DATE()) = 1");
						
				$query2 = $db->getQuery(true)
						->select("U.username, U.name, EL.*, A.image as avatar, S.image as school_image, S.name as school_name from EventLog EL")
						->innerJoin($userDb . "." . $prefix ."users U on EL.person_id = U.id")
						->innerJoin("SchoolUsers SU on SU.person_id = EL.person_id")
						->innerJoin("School S on S.school_id = SU.school_id")
						->innerJoin("Avatar A on A.avatar_id = SU.avatar")
						->where("EL.access_level = " . SchoolCommunity::SCHOOL . " and EL.school_id in (".$schoolString.")")
						->where("date(EL.event_date)= DATE(NOW())");
						
				$query3 = $db->getQuery(true)
						->select("U.username, U.name, EL.*, A.image as avatar, S.image as school_image, S.name as school_name from EventLog EL")
						->innerJoin($userDb . "." . $prefix ."users U on EL.person_id = U.id")
						->innerJoin("SchoolUsers SU on SU.person_id = EL.person_id")
						->innerJoin("School S on S.school_id = SU.school_id")
						->innerJoin("Avatar A on A.avatar_id = SU.avatar")
						->where("EL.access_level = " . SchoolCommunity::COMMUNITY)
						->where("date(EL.event_date)= DATE(NOW())");
						
				if ( SchoolCommunity::isEcologist() ) {
					
					$query4 = $db->getQuery(true)
						->select("U.username, U.name, EL.*, A.image as avatar, S.image as school_image, S.name as school_name from EventLog EL")
						->innerJoin($userDb . "." . $prefix ."users U on EL.person_id = U.id")
						->innerJoin("SchoolUsers SU on SU.person_id = EL.person_id")
						->innerJoin("School S on S.school_id = SU.school_id")
						->innerJoin("Avatar A on A.avatar_id = SU.avatar")
						->where("EL.access_level = " . SchoolCommunity::ECOLOGISTS)
						->where("date(EL.event_date)= DATE(NOW())");
						
					$query1->union($query2)->union($query3)->union($query4)->order("event_date DESC");
				
				}
				else {
					$query1->union($query2)->union($query3)->order("event_date DESC");
					
				}
						
				$db->setQuery($query1);
					
				//error_log("EventLog::todaysEvents select query created: " . $query1->dump());
					
				$this->today = $db->loadObjectList("event_id");
				
			}
		}
			
		return $this->today;

	}
	
	
	public function yesterdaysEvents () {
		
		if ( $this->personId ) {
			
			if ( !$this->yesterday ) {
			
		
				$problem = true;
				
				$options = dbOptions();
				$userDb = $options['userdb'];
				$prefix = $options['userdbprefix'];
				
				$schoolString = implode ( ',', $this->mySchools );
			
				$db = \JDatabaseDriver::getInstance($options);
			
				$query1 = $db->getQuery(true)
						->select("U.username, U.name, EL.*, A.image as avatar, S.image as school_image, S.name as school_name from EventLog EL")
						->innerJoin($userDb . "." . $prefix ."users U on EL.person_id = U.id")
						->innerJoin("SchoolUsers SU on SU.person_id = EL.person_id")
						->innerJoin("School S on S.school_id = SU.school_id")
						->innerJoin("Avatar A on A.avatar_id = SU.avatar")
						->where("EL.person_id = " . $this->personId)
						->where("date(EL.event_date)= DATE(NOW()- INTERVAL 1 DAY)"); 
						
				$query2 = $db->getQuery(true)
						->select("U.username, U.name, EL.*, A.image as avatar, S.image as school_image, S.name as school_name from EventLog EL")
						->innerJoin($userDb . "." . $prefix ."users U on EL.person_id = U.id")
						->innerJoin("SchoolUsers SU on SU.person_id = EL.person_id")
						->innerJoin("School S on S.school_id = SU.school_id")
						->innerJoin("Avatar A on A.avatar_id = SU.avatar")
						->where("EL.access_level = " . SchoolCommunity::SCHOOL . " and EL.school_id in (".$schoolString.")")
						->where("date(EL.event_date)= DATE(NOW()- INTERVAL 1 DAY)");
						
				$query3 = $db->getQuery(true)
						->select("U.username, U.name, EL.*, A.image as avatar, S.image as school_image, S.name as school_name from EventLog EL")
						->innerJoin($userDb . "." . $prefix ."users U on EL.person_id = U.id")
						->innerJoin("SchoolUsers SU on SU.person_id = EL.person_id")
						->innerJoin("School S on S.school_id = SU.school_id")
						->innerJoin("Avatar A on A.avatar_id = SU.avatar")
						->where("EL.access_level = " . SchoolCommunity::COMMUNITY)
						->where("date(EL.event_date)= DATE(NOW()- INTERVAL 1 DAY)");
						
				if ( SchoolCommunity::isEcologist() ) {
					
					$query4 = $db->getQuery(true)
						->select("U.username, U.name, EL.*, A.image as avatar, S.image as school_image, S.name as school_name from EventLog EL")
						->innerJoin($userDb . "." . $prefix ."users U on EL.person_id = U.id")
						->innerJoin("SchoolUsers SU on SU.person_id = EL.person_id")
						->innerJoin("School S on S.school_id = SU.school_id")
						->innerJoin("Avatar A on A.avatar_id = SU.avatar")
						->where("EL.access_level = " . SchoolCommunity::ECOLOGISTS)
						->where("date(EL.event_date)= DATE(NOW()- INTERVAL 1 DAY)");
						
					$query1->union($query2)->union($query3)->union($query4);
				
				}
				else {
					$query1->union($query2)->union($query3);
					
				}
						
				$db->setQuery($query1);
					
				//error_log("EventLog::yesterdaysEvents select query created: " . $query1->dump());
					
				$this->yesterday = $db->loadObjectList("event_id");
				
			}
		}
			
		return $this->yesterday;

	}
	
	
	// This week but before yesterday
	public function thisWeeksEvents () {
		
		if ( $this->personId ) {
			
			if ( !$this->thisWeek ) {
			
		
				$problem = true;
				
				$options = dbOptions();
				$userDb = $options['userdb'];
				$prefix = $options['userdbprefix'];
				
				$schoolString = implode ( ',', $this->mySchools );
			
				$db = \JDatabaseDriver::getInstance($options);
			
				$query1 = $db->getQuery(true)
						->select("U.username, U.name, EL.*, A.image as avatar, S.image as school_image, S.name as school_name from EventLog EL")
						->innerJoin($userDb . "." . $prefix ."users U on EL.person_id = U.id")
						->innerJoin("SchoolUsers SU on SU.person_id = EL.person_id")
						->innerJoin("School S on S.school_id = SU.school_id")
						->innerJoin("Avatar A on A.avatar_id = SU.avatar")
						->where("EL.person_id = " . $this->personId)
						->where("date(EL.event_date) between DATE(NOW()- INTERVAL 7 DAY) and DATE(NOW()- INTERVAL 2 DAY)"); 
						
				$query2 = $db->getQuery(true)
						->select("U.username, U.name, EL.*, A.image as avatar, S.image as school_image, S.name as school_name from EventLog EL")
						->innerJoin($userDb . "." . $prefix ."users U on EL.person_id = U.id")
						->innerJoin("SchoolUsers SU on SU.person_id = EL.person_id")
						->innerJoin("School S on S.school_id = SU.school_id")
						->innerJoin("Avatar A on A.avatar_id = SU.avatar")
						->where("EL.access_level = " . SchoolCommunity::SCHOOL . " and EL.school_id in (".$schoolString.")")
						->where("date(EL.event_date) between DATE(NOW()- INTERVAL 7 DAY) and DATE(NOW()- INTERVAL 2 DAY)");
						
				$query3 = $db->getQuery(true)
						->select("U.username, U.name, EL.*, A.image as avatar, S.image as school_image, S.name as school_name from EventLog EL")
						->innerJoin($userDb . "." . $prefix ."users U on EL.person_id = U.id")
						->innerJoin("SchoolUsers SU on SU.person_id = EL.person_id")
						->innerJoin("School S on S.school_id = SU.school_id")
						->innerJoin("Avatar A on A.avatar_id = SU.avatar")
						->where("EL.access_level = " . SchoolCommunity::COMMUNITY)
						->where("date(EL.event_date) between DATE(NOW()- INTERVAL 7 DAY) and DATE(NOW()- INTERVAL 2 DAY)");
						
				if ( SchoolCommunity::isEcologist() ) {
					
					$query4 = $db->getQuery(true)
						->select("U.username, U.name, EL.*, A.image as avatar, S.image as school_image, S.name as school_name from EventLog EL")
						->innerJoin($userDb . "." . $prefix ."users U on EL.person_id = U.id")
						->innerJoin("SchoolUsers SU on SU.person_id = EL.person_id")
						->innerJoin("School S on S.school_id = SU.school_id")
						->innerJoin("Avatar A on A.avatar_id = SU.avatar")
						->where("EL.access_level = " . SchoolCommunity::ECOLOGISTS)
						->where("date(EL.event_date) between DATE(NOW()- INTERVAL 7 DAY) and DATE(NOW()- INTERVAL 2 DAY)");
						
					$query1->union($query2)->union($query3)->union($query4);
				
				}
				else {
					$query1->union($query2)->union($query3);
					
				}
						
				$db->setQuery($query1);
					
				//error_log("EventLog::thisWeeksEvents select query created: " . $query1->dump());
					
				$this->thisWeek = $db->loadObjectList("event_id");
				
			}
		}
			
		return $this->thisWeek;

	}
	
	
	// This week but before yesterday
	public function earlierEvents () {
		
		if ( $this->personId ) {
			
			if ( !$this->earlier ) {
			
		
				$problem = true;
				
				$options = dbOptions();
				$userDb = $options['userdb'];
				$prefix = $options['userdbprefix'];
				
				$schoolString = implode ( ',', $this->mySchools );
			
				$db = \JDatabaseDriver::getInstance($options);
			
				$query1 = $db->getQuery(true)
						->select("U.username, U.name, EL.*, A.image as avatar, S.image as school_image, S.name as school_name from EventLog EL")
						->innerJoin($userDb . "." . $prefix ."users U on EL.person_id = U.id")
						->innerJoin("SchoolUsers SU on SU.person_id = EL.person_id")
						->innerJoin("School S on S.school_id = SU.school_id")
						->innerJoin("Avatar A on A.avatar_id = SU.avatar")
						->where("EL.person_id = " . $this->personId)
						->where("date(EL.event_date) < DATE(NOW()- INTERVAL 7 DAY)"); 
						
				$query2 = $db->getQuery(true)
						->select("U.username, U.name, EL.*, A.image as avatar, S.image as school_image, S.name as school_name from EventLog EL")
						->innerJoin($userDb . "." . $prefix ."users U on EL.person_id = U.id")
						->innerJoin("SchoolUsers SU on SU.person_id = EL.person_id")
						->innerJoin("School S on S.school_id = SU.school_id")
						->innerJoin("Avatar A on A.avatar_id = SU.avatar")
						->where("EL.access_level = " . SchoolCommunity::SCHOOL . " and EL.school_id in (".$schoolString.")")
						->where("date(EL.event_date)< DATE(NOW()- INTERVAL 7 DAY)");
						
				$query3 = $db->getQuery(true)
						->select("U.username, U.name, EL.*, A.image as avatar, S.image as school_image, S.name as school_name from EventLog EL")
						->innerJoin($userDb . "." . $prefix ."users U on EL.person_id = U.id")
						->innerJoin("SchoolUsers SU on SU.person_id = EL.person_id")
						->innerJoin("School S on S.school_id = SU.school_id")
						->innerJoin("Avatar A on A.avatar_id = SU.avatar")
						->where("EL.access_level = " . SchoolCommunity::COMMUNITY)
						->where("date(EL.event_date)< DATE(NOW()- INTERVAL 7 DAY)");
						
				if ( SchoolCommunity::isEcologist() ) {
					
					$query4 = $db->getQuery(true)
						->select("U.username, U.name, EL.*, A.image as avatar, S.image as school_image, S.name as school_name from EventLog EL")
						->innerJoin($userDb . "." . $prefix ."users U on EL.person_id = U.id")
						->innerJoin("SchoolUsers SU on SU.person_id = EL.person_id")
						->innerJoin("School S on S.school_id = SU.school_id")
						->innerJoin("Avatar A on A.avatar_id = SU.avatar")
						->where("EL.access_level = " . SchoolCommunity::ECOLOGISTS)
						->where("date(EL.event_date)< DATE(NOW()- INTERVAL 7 DAY)");
						
					$query1->union($query2)->union($query3)->union($query4);
				
				}
				else {
					$query1->union($query2)->union($query3);
					
				}
						
				$db->setQuery($query1);
					
				//error_log("EventLog::earlierEvents select query created: " . $query1->dump());
					
				$this->thisWeek = $db->loadObjectList("event_id");
				
			}
		}
			
		return $this->thisWeek;

	}
}



?>

