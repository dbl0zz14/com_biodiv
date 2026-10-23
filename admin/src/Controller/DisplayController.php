<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 
namespace MammalWeb\Component\Biodiv\Administrator\Controller;

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;

use MammalWeb\Component\Biodiv\Administrator\Library\Biodiv;

/**
 * Biodiv Component Admin Controller
 * @package     Joomla.Administrator
 * @subpackage  com_biodiv
 * @since  4.0.0
 */
class DisplayController extends BaseController {
	
	/**
	 * The default view for the display method.
	 *
	 * @var string
	 * @since 12.2
	 */
	//protected $default_view = 'biodivs';
	//protected $default_view = 'home';
	
	
	public function display($cachable = false, $urlparams = array()) {        
	
        $document = Factory::getDocument();
		
        $viewName = $this->input->getCmd('view', 'home');
		$viewFormat = $document->getType();
		
		if ($view = $this->getView($viewName, $viewFormat)) {
			
			// Do any specific processing by view.
            switch ($viewName) {
				
				default:
                    
					$view->setModel($this->getModel('Biodiv'), true);
                    break;
            }
			
		}
		
        $view->document = $document;
        $view->display();
	}
		
	
	function createproject() {
		
		$app = Factory::getApplication();

		$input = $app->input;
				
		$projectName = $input->getString('projectName', 0);
		$prettyName = $input->getString('prettyName', 0);
		$projectDescription = $input->getString('projectDescription', 0);
		$accessLevel = $input->getInt('accessLevel', 0);
		$parentProject = $input->getInt('parentProject', 0);
		$imageDir = $input->getString('imageDir', 'images/projects');
		$imageFile = $input->getString('imageFile', 0);
		$articleId = $input->getInt('articleId', 0);
		$listingLevel = $input->getInt('listingLevel', 10000);
		$priority = $input->getInt('priority', 0);
		$fullyClassed = $input->getInt('fullyClassed', 0);
		$region = $input->getInt('region', 0);
		$displayOptions = $input->get('displayOptions', array(), 'ARRAY');
		$speciesLists = $input->get('speciesLists', array(), 'ARRAY');
		$projectAdmins = $input->get('projectAdmins', array(), 'ARRAY');
		$isSchoolProject = $input->getInt('isSchoolProject', 0);
		$existingSchoolId = $input->getInt('school', 0);
		$newSchoolName = $input->getString('newSchoolName', 0);
		
		$projectId = Biodiv::createProject ($projectName,
										$prettyName,
										$projectDescription,
										$accessLevel,
										$parentProject,
										$imageDir,
										$imageFile,
										$articleId,
										$listingLevel,
										$priority,
										$fullyClassed,
										$region,
										$displayOptions,
										$speciesLists,
										$projectAdmins,
										$isSchoolProject,
										$existingSchoolId,
										$newSchoolName
									);
		
		

		
		$this->input->set('view', 'project');
		$this->input->set('id', $projectId);
		
		$this->display();
	}
	
	
	function editproject() {
		
		$app = Factory::getApplication();

		$input = $app->input;
		
		$projectId = $input->getInt('id', 0);
		$projectName = $input->getString('projectName', 0);
		$prettyName = $input->getString('prettyName', 0);
		$projectDescription = $input->getString('projectDescription', 0);
		$accessLevel = $input->getInt('accessLevel', 0);
		$parentProject = $input->getInt('parentProject', 0);
		$imageDir = $input->getString('imageDir', 'images/projects');
		$imageFile = $input->getString('imageFile', 0);
		$articleId = $input->getInt('articleId', 0);
		$listingLevel = $input->getInt('listingLevel', 10000);
		$priority = $input->getInt('priority', 0);
		$fullyClassed = $input->getInt('fullyClassed', 0);
		$region = $input->getInt('region', 0);
		$displayOptions = $input->get('displayOptions', array(), 'ARRAY');
		$speciesLists = $input->get('speciesLists', array(), 'ARRAY');
		$projectAdmins = $input->get('projectAdmins', array(), 'ARRAY');
		$isSchoolProject = $input->getInt('isSchoolProject', 0);
		$existingSchoolId = $input->getInt('school', 0);
		$newSchoolName = $input->getString('newSchoolName', 0);
		
		$result = Biodiv::editProject ($projectId,
										$projectName,
										$prettyName,
										$projectDescription,
										$accessLevel,
										$parentProject,
										$imageDir,
										$imageFile,
										$articleId,
										$listingLevel,
										$priority,
										$fullyClassed,
										$region,
										$displayOptions,
										$speciesLists,
										$projectAdmins,
										$isSchoolProject,
										$existingSchoolId,
										$newSchoolName
									);
		
		$this->input->set('view', 'project');
		$this->input->set('id', $projectId);
		
		$this->display();
	}
	
	
	function createschool() {
		
		$app = Factory::getApplication();

		$input = $app->input;
				
		$schoolName = $input->getString('schoolName', 0);
		$image = $input->getString('image', 0);
		$projectId = $input->getInt('projectId', 0);
		
		
		$schoolId = Biodiv::createSchool ( $schoolName, $image, $projectId );
		
		$this->input->set('view', 'school');
		$this->input->set('id', $schoolId);
		
		$this->display();
	}
	
	
	function editschool() {
		
		$app = Factory::getApplication();

		$input = $app->input;
		
		$schoolId = $input->getInt('id', 0);
		$schoolName = $input->getString('schoolName', 0);
		$image = $input->getString('image', 0);
		$projectId = $input->getInt('projectId', 0);
		
		$success = Biodiv::editSchool ( $schoolId, $schoolName, $image, $projectId );
		
		$this->input->set('view', 'school');
		$this->input->set('id', $schoolId);
		
		$this->display();
	}


	function deletephotos () {

		$app = Factory::getApplication();

		$input = $app->input;

		$num_deleted = 0;

		$uploadedFile = $input->files->get('file');

		if(!isset($uploadedFile['tmp_name'])){
			error_log ("No file uploaded when admin deleting photos");
		}
		else {
		
			$tmpName = $uploadedFile['tmp_name'];  // assuming multiple upload
			$clientName = $uploadedFile['name'];  // assuming multiple upload
			$fileSize = $uploadedFile['size'];  // assuming multiple upload
			$fileType = $uploadedFile['type'];  // assuming multiple upload

			if($fileSize > 8000000){
				error_log ( "File is bigger than 8000000" );
        		}
        		else if(!is_uploaded_file($tmpName)){
                		error_log ( "Not an uploaded file $tmpName ( $clientName ) - check file size is below PHP limits, or there may be a network problem" );
        		}
			else {
				// Handle uploaded file

				try {
					// Allowed file extensions
    				$allowedExtensions = ['csv'];

					// Extract file extension
					$ext = strtolower(File::getExt($uploadedFile['name']));

					if (!in_array($ext, $allowedExtensions)) {
						throw new RuntimeException('Invalid file type.');
					}

					$uploadDir = JPATH_ROOT . '/biodivimages/deletephotos';

					// Create folder if it doesn't exist
					if (!Folder::exists($uploadDir)) {
						Folder::create($uploadDir);
					}

					// Sanitize filename
					$safeFilename = File::makeSafe($uploadedFile['name']);

					// Full path for the uploaded file
					$uploadPath = $uploadDir . '/' . $safeFilename;

					// Move the uploaded file
					if (!File::upload($uploadedFile['tmp_name'], $uploadPath)) {
						throw new RuntimeException('File upload failed.');
					}

					$num_deleted = Biodiv::deletePhotos($uploadPath);

					Biodiv::moveDeletionsFileToS3($uploadPath);

				} catch (Exception $e) {
    					error_log ( $e->getMessage() );
				}
			}
		}

		$this->input->set('view', 'deleted');
		$this->input->set('numDeleted', $num_deleted);
		

        $this->display();

	}
	
}
