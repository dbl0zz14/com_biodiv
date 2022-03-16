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
 * General Controller of Biodiv component
 *
 * @package     Joomla.Administrator
 * @subpackage  com_biodiv
 * @since       0.0.7
 */
class BioDivController extends JControllerLegacy
{
	/**
	 * The default view for the display method.
	 *
	 * @var string
	 * @since 12.2
	 */
	protected $default_view = 'biodivs';
	
	
	function createusers() {
		
		$app = JFactory::getApplication();

		$input = $app->input;
				
		$tandCsChecked = $input->getInt('tandCsChecked', 0);
		$fileStem = $input->getString('fileStem', 0);
		$userStem = $input->getString('userStem', 0);
		$passwordStem = $input->getString('passwordStem', 0);
		$emailDomain = $input->getString('emailDomain', 0);
		$numUsers = $input->getInt('numUsers', 0);
		$startingNum = $input->getInt('startingNum', 1);
		$project = $input->getInt('project', 0);
		$addToSchool = $input->getInt('addToSchool', 0);
		$schoolId = $input->getInt('school', 0);

		if ( $tandCsChecked ) {
			
			$db = \JDatabaseDriver::getInstance(dbOptions());
			
			$query = $db->getQuery(true)
				->select("type, value from Generate" )
				->order("type");
		
			$db->setQuery($query);
			
			error_log("Task getAllStudentTasks select query created: " . $query->dump());
			
			$allGen = $db->loadAssocList();
			
			$firsts = array();
			$seconds = array();
			$thirds= array();
			foreach ( $allGen as $genRow ) {
				error_log ( "type = " . $genRow["type"] . ", value = " . $genRow["value"] );
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
			
			$errMsg = print_r ( $usernames, true );
			error_log ( "Usernames = " . $errMsg );
			
			$numThirds = count ( $thirds );
			
			$helper = new BiodivHelper();
			
			$filePath = JPATH_SITE."/biodivimages/reports/school";
			$tmpCsvFile = $filePath . "/tmp_" . $fileStem . '_' . $schoolId . ".csv";
			$newCsvFile = $filePath . "/" . $fileStem . "_" . $schoolId . ".csv";
			
			if ( !file_exists($newCsvFile) ) {
			
				// Creates a new csv file and store it in directory
				// Rename once finished writing to file
				if (!file_exists($filePath)) {
					mkdir($filePath, 0700, true);
				}
				
				$tmpCsv = fopen ( $tmpCsvFile, 'w');
				
			
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
						fputcsv($tmpCsv, array("User num ".$i." already exists - cannot create - use starting number for additional users"));
						
					}
					else if ( JUserHelper::getUserId($username) ) {
						error_log ( "username " . $username . " already in use, cannot create" );
						fputcsv($tmpCsv, array("User num ".$i." already exists - cannot create - use starting number for additional users"));
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
							
						}
						
						if ( $userCreated ) {
							
							fputcsv($tmpCsv, array($username, $password));
							
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
					}			
				}
				
				fclose($tmpCsv);
				
				rename ( $tmpCsvFile, $newCsvFile );
			}
			
		}
		else {
			error_log ("Please ensure T&Cs agreement is in place");
		}

		$this->input->set('view', 'biodivs');

		parent::display();
	}
	
	
	function deletefiles() {
		
		error_log ( "deletefiles called" );
		
		$filePath = JPATH_SITE."/biodivimages/reports/school";
		
		$files = glob($filePath . '/*.csv');

		//Loop through the file list.
		foreach($files as $file){
			//Make sure that this is a file and not a directory.
			if(is_file($file)){
				error_log ( "Removing " . $file );
				//Use the unlink function to delete the file.
				unlink($file);
			}
		}
		
		$this->input->set('deleted', '1');
		$this->input->set('view', 'biodivs');

		parent::display();
		
	}
  
  
}