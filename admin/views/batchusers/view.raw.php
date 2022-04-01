<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Biodivs View
 *
 * @since  0.0.1
 */
class BioDivViewBatchUsers extends JViewLegacy
{
	/**
	 * Display the Biodivs view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		
		$app = JFactory::getApplication();

		$input = $app->input;
				
		$tandCsChecked = $input->getInt('tandCsChecked', 0);
		$fileStem = $input->getString('fileStem', 0);
		$userStem = $input->getString('userStem', 0);
		$passwordStem = $input->getString('passwordStem', 0);
		$emailDomain = $input->getString('emailDomain', 0);
		$numUsers = $input->getInt('numUsers', 0);
		$userGroup = $input->getInt('userGroup', 0);
		$startingNum = $input->getInt('startingNum', 1);
		$project = $input->getInt('project', 0);
		$addToSchool = $input->getInt('addToSchool', 0);
		$schoolId = $input->getInt('school', 0);
		
		$this->newUsers = array();
		$this->newUsers["filename"] = $fileStem;
		$this->newUsers["users"] = array();
		$this->newUsers["errors"] = array();
		

		if ( $tandCsChecked ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
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
				
				$username = $userStem . $usernames[$i] . $i;	
				
				$ind = rand(0,$numThirds-1);
				$word = $thirds[$ind];
				$num = rand(1,999);
				$password = $passwordStem . $word . $num;	
				$email = $userStem . $i . '@' . $emailDomain;
			
				$existingUserEmail = $helper->getUser ( $email );
				if ( $existingUserEmail ) {
					error_log ( "Email " . $email . " already in use, cannot create" );
					//fputcsv($tmpCsv, array("User num ".$i." already exists - cannot create - use starting number for additional users"));
					$this->newUsers["errors"][] = array("error"=>"User num ".$i." already exists - cannot create - use starting number for additional users");
					
				}
				else if ( JUserHelper::getUserId($username) ) {
					error_log ( "username " . $username . " already in use, cannot create" );
					//fputcsv($tmpCsv, array("User num ".$i." already exists - cannot create - use starting number for additional users"));
					$this->newUsers["errors"][] = array("error"=>"User num ".$i." already exists - cannot create - use starting number for additional users");
				}
				else {
				
					error_log("Creating user " . $email);
					
				
					$profileMW = array( 
						'tos'=>$tandCsChecked,
						'wherehear'=>$project,	
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
					
					$user = new JUser;
					
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
							error_log("User saved");
							
							$userCreated = true;
						}
						
					}
					catch(Exception $e){
						error_log($e->getMessage());
						$this->newUsers["errors"][] = array("error"=>"User num ".$i." - problem creating user");
					}
					
					if ( $userCreated ) {
						
						if ( $userGroup > 0 ) {
							JUserHelper::addUSerToGroup ( $user->id, $userGroup );
						}
						
						//fputcsv($tmpCsv, array($username, $password));
						$this->newUsers["users"][] = array("username"=>$username, "password"=>$password); 
						
						// Link to school project
						$fields = new \StdClass();
						$fields->person_id = $user->id;
						$fields->project_id = $project;
						$fields->role_id = 2;
						
						$success = $db->insertObject("ProjectUserMap", $fields);
						if(!$success){
							error_log ( "ProjectUserMap insert failed" );
						}	
		
						
						// Link to school in BES
						$db = \JDatabaseDriver::getInstance(dbOptions());
		
						$fields = new \StdClass();
						$fields->person_id = $user->id;
						$fields->school_id = $schoolId;
						$fields->role_id = 5;
						
						$success = $db->insertObject("SchoolUsers", $fields);
						if(!$success){
							error_log ( "SchoolUsers insert failed" );
						}	
					}
					else {
						$this->newUsers["errors"][] = array("error"=>"User num ".$i." - problem creating user");
					}
				}			
			}
			
			
			
		}
		else {
			error_log ("Please ensure T&Cs agreement is in place");
			$this->newUsers[] = array("error"=>"Please ensure T&Cs agreement is in place"); 
							
		}
		
		$errMsg = print_r ( $this->newUsers, true );
		error_log ( "New users array: " . $errMsg );
		
		// Display the template
		parent::display($tpl);
	}
	
	
}