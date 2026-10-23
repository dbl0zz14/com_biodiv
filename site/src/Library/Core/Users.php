<?php

namespace MammalWeb\Component\Biodiv\Site\Library\Core;


// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseFactory;
use Joomla\CMS\User\UserHelper;

set_include_path('' . JPATH_COMPONENT_SITE . ';' . JPATH_COMPONENT_SITE . '/src/Library;' . get_include_path());

require_once "local.php";
require_once "codes.php";
require_once "biodiv.php";

// Encapsulation class for user admin tasks
class Users {
	
	
	function __construct( $id )
	{
		
	}
	
	public static function batchCreateUsers () {
		
		$app = Factory::getApplication();

		$input = $app->input;
				
		$tandCsChecked = $input->getInt('tandCsChecked', 0);
		$fileStem = $input->getString('fileStem', 0);
		$userStem = $input->getString('userStem', 0);
		$passwordStem = $input->getString('passwordStem', 0);
		$emailDomain = $input->getString('emailDomain', 0);
		$numUsers = $input->getInt('numUsers', 0);
		$userGroup = $input->getInt('userGroup', 0);
		$startingNum = $input->getInt('startingNum', 0);
		$projectId = $input->getInt('project', 0);
		$addToSchool = $input->getInt('addToSchool', 0);
		$schoolId = $input->getInt('school', 0);
		$classId = $input->getInt('batchClassId', 0);
		
		if ( $startingNum == 0 ) {
			
			$startingNum = 1;
			
			// Check existing users with this stem
			$joomlaDb = Factory::getDbo();
			$joomlaDb->setQuery( 'SELECT username' .
							' FROM `#__users`' .
							' WHERE username like "'.$userStem.'%"' );
									
			$similarUsers = $joomlaDb->loadColumn ();
			
			if ( count ( $similarUsers) > 0 ) {
				$userStemLen = strlen($userStem);
				$nums = array();
				foreach ( $similarUsers as $simUser ) {
					$num = substr($simUser, $userStemLen);
					if ( is_numeric ($num) ) {
						$nums[] = (int)$num;
					}
				}
				$maxNum = 0;
				if ( count($nums) > 0 ) {
					$maxNum = max($nums);
				}
				$startingNum = $maxNum + 1;
			}
		}
		
		$whereHear = 'Batch user creation';
		if ( $projectId ) {
			$whereHear .= ', ' . codes_getName( $projectId, 'project' );
		}
		
		$newUsers = array();
		$newUsers["filename"] = $fileStem;
		$newUsers["users"] = array();
		$newUsers["errors"] = array();
		
		if ( $tandCsChecked ) {
			
			$db = db();
			
			$query = $db->getQuery(true)
				->select("type, value from Generate" )
				->order("type");
		
			$db->setQuery($query);
			
			//error_log("Task getAllStudentTasks select query created: " . $query->dump());
			
			$allGen = $db->loadAssocList();
			
			$firsts = array();
			$seconds = array();
			$thirds= array();
			foreach ( $allGen as $genRow ) {
				if ( $genRow["type"] == "first" ) {
					$firsts[] = $genRow["value"];
				}
				else if ( $genRow["type"] == "second" ) {
					$seconds[] = $genRow["value"];
				}
				else if ( $genRow["type"] == "third" ) {
					$thirds[] = $genRow["value"];
				}
			}
		
			$usernames = array();
			foreach ( $firsts as $first ) {
				foreach ( $seconds as $second ) {
					$usernames[] = $first . $second;
				}
			}
		
			shuffle ( $usernames );
			
			$numThirds = count ( $thirds );
			
			$helper = new BiodivHelper();
			
			for ( $i=$startingNum; $i < $startingNum + $numUsers; $i++ ) {
				
				//$username = $userStem . $usernames[$i] . $i;	
				$username = $userStem . $i;	
				
				$ind = rand(0,$numThirds-1);
				$word = $thirds[$ind];
				$num = rand(1,999);
				$password = $passwordStem . $word . $num;	
				$email = $userStem . $i . '@' . $emailDomain;
			
				$existingUserEmail = $helper->getUser ( $email );
				if ( $existingUserEmail ) {
					error_log ( "Email " . $email . " already in use, cannot create" );
					$newUsers["errors"][] = array("error"=>"User num ".$i." already exists - cannot create - use starting number for additional users");
					
				}
				else if ( UserHelper::getUserId($username) ) {
					error_log ( "username " . $username . " already in use, cannot create" );
					$newUsers["errors"][] = array("error"=>"User num ".$i." already exists - cannot create - use starting number for additional users");
				}
				else {
					
					$profileMW = array( 
						'tos'=>$tandCsChecked,
						'wherehear'=>$whereHear,	
						'subscribe'=>0
						);
					
					// Add to Registered group
					$groups = array("2"=>"2");
					
					$data = array(
					'name'=>$username,
					'username'=>$username,
					'password'=>$password,
					'email'=>$email,
					'sendEmail'=>0,
					'block'=>0,
					'profileMW'=>$profileMW,
					'groups'=>$groups,
					);
					
					$user = new \JUser;
					//$user = new \Joomla\CMS\User\User;
					
					$userCreated = false;
					
					try{
						if (!$user->bind($data)){
							error_log("User bind returned false");
							error_log($user->getError());
							
						}
						if (!$user->save()) {
							error_log("User save returned false");
							error_log($user->getError());
							
						}
						if ( !$user->getError() ) {
							
							$userCreated = true;
						}
						
					}
					catch(\Exception $e){
						error_log($e->getMessage());
						$newUsers["errors"][] = array("error"=>"User num ".$i." - problem creating user");
					}
					
					if ( $userCreated ) {
						
						if ( $userGroup > 0 ) {
							UserHelper::addUSerToGroup ( $user->id, $userGroup );
						}
						
						//fputcsv($tmpCsv, array($username, $password));
						$newUsers["users"][] = array("username"=>$username, "password"=>$password); 
						
						// Link to school project
						$fields = new \StdClass();
						$fields->person_id = $user->id;
						$fields->project_id = $projectId;
						$fields->role_id = 2;
						
						$success = $db->insertObject("ProjectUserMap", $fields);
						if(!$success){
							error_log ( "ProjectUserMap insert failed" );
						}	
		
						if ( $addToSchool ) {
							// Link to school in BES
							$db = db();
			
							$fields = new \StdClass();
							$fields->person_id = $user->id;
							$fields->school_id = $schoolId;
							if ( $classId ) {
								$fields->class_id = $classId;
							}
							$fields->role_id = 5;
							
							$success = $db->insertObject("SchoolUsers", $fields);
							if(!$success){
								error_log ( "SchoolUsers insert failed" );
							}	
						}
					}
					else {
						$newUsers["errors"][] = array("error"=>"User num ".$i." - problem creating user");
					}
				}			
			}
			
		}
		else {
			error_log ("Please ensure T&Cs agreement is in place");
			$newUsers[] = array("error"=>"Please ensure T&Cs agreement is in place"); 
							
		}
		
		return $newUsers;
	}
	
	
}



?>

