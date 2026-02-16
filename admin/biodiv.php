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

// Needed to ensure pick up the correct component files
set_include_path(JPATH_COMPONENT_SITE . PATH_SEPARATOR . get_include_path());

include_once "local.php";
include_once "codes.php";

include_once "BiodivHelper.php";
include_once "BiodivReport.php";
include_once "BiodivAWS.php";
include_once "BiodivTransifex.php";
include_once "Biodiv/Users.php";

//require_once 'libraries/vendor/guzzlehttp/guzzle/src/Client.php';
require 'libraries/vendor/autoload.php';

define('BIODIV_ADMIN_ROOT', JURI::base() . '?option=' . BIODIV_COMPONENT);
$document = JFactory::getDocument();
$document->addScriptDeclaration('
var BioDiv = {};
BioDiv.root = "'. BIODIV_ADMIN_ROOT . '";');


JHtml::_('jquery.framework');


function db(){
  static $db;
  $options = dbOptions();
  if(!$db){
    $db = new mysqli($options['host'],
		     $options['user'],
		     $options['password'],
		     $options['database']);
  }
  return $db;
}


$dbOptions = dbOptions();
codes_parameters_addTable("StructureMeta", $dbOptions['database']);



function printAdminMenu($currentPage) {
	
	if ( !$currentPage ) $currentPage = "HOME";
	$activeLi = '<li class="active">';
	
	
	print '<div id="j-sidebar-container" class="j-sidebar-container j-sidebar-visible">';
		
	print '<div id="j-toggle-sidebar-wrapper">';
	print '	<div id="j-toggle-button-wrapper" class="j-toggle-button-wrapper j-toggle-visible">';
	print '		<div id="j-toggle-sidebar-button" class="j-toggle-sidebar-button hidden-phone hasTooltip" onclick="toggleSidebar(false); return false;" data-original-title="Hide the sidebar">';
	print '		<span id="j-toggle-sidebar-icon" class="icon-arrow-left-2 j-toggle-visible" aria-hidden="true"></span>';
	print '</div>';
	print '	</div>';
	print '	<div id="sidebar" class="sidebar">';
	print ' <div class="sidebar-nav">';
	print '	<ul id="submenu" class="nav nav-list">';
	if ( $currentPage == "HOME" ) {
		print $activeLi;
	}
	else {
		print '<li>';
	}
	print '<a href="index.php?option=com_biodiv">MammalWeb admin home</a>';
	print '</li>';
	if ( $currentPage == "PROJECT" ) {
		print $activeLi;
	}
	else {
		print '<li>';
	}
	print '<a href="index.php?option=com_biodiv&amp;view=selectproject">View or edit project</a>';
	print '</li>';
	if ( $currentPage == "NEWPROJECT" ) {
		print $activeLi;
	}
	else {
		print '<li>';
	}
	print '<a href="index.php?option=com_biodiv&amp;view=newproject">New project</a>';
	print '</li>';
	if ( $currentPage == "DELETEPHOTOS" ) {
		print $activeLi;
	}
	else {
		print '<li>';
	}
	print '<a href="index.php?option=com_biodiv&amp;view=deletephotos">Delete photos</a>';
	print '</li>';
	if ( $currentPage == "SCHOOL" ) {
		print $activeLi;
	}
	else {
		print '<li>';
	}
	print '<a href="index.php?option=com_biodiv&amp;view=selectschool">View or edit school</a>';
	print '</li>';
	if ( $currentPage == "NEWSCHOOL" ) {
		print $activeLi;
	}
	else {
		print '<li>';
	}
	print '<a href="index.php?option=com_biodiv&amp;view=newschool">New school</a>';
	print '</li>';
	if ( $currentPage == "USERS" ) {
		print $activeLi;
	}
	else {
		print '<li>';
	}
	print '<a href="index.php?option=com_biodiv&amp;view=biodivs">Batch user creation</a>';
	print '</li>';
	if ( $currentPage == "TRANSLATIONS" ) {
		print $activeLi;
	}
	else {
		print '<li>';
	}
	print '<a href="index.php?option=com_biodiv&amp;view=translations">Translations</a>';
	print '</li>';
	print '</ul>';
	print '</div>';
	print '</div>';
	print '<div id="j-toggle-sidebar"></div>';
	print '</div>';
	print '</div>';	

}


function printProjectForm ( $projectId = null ) {
	
	$accessLevels = getAccessLevels();
									
	$projects = getAllProjects();
	
	$schools = getAllSchools();
		
	$priorities = getPriorities();
						
	$speciesLists = getSpeciesLists();
	
	$displayOptions = getProjectDisplayOptions();
	
	$projectAdmins = getAllProjectAdmins();
	
	$currProject = null;
	if ( $projectId ) {
		$currProject = getProject ( $projectId );
		
		print '<input type="hidden" name="id" value="'.$projectId.'"/>';
		
	}
	
	
	print '<div id="projectForm1" class="projectForm">';
	
	print '<h3>Describe the project</h3>';
	print '<p>';
	print '<label for="projectName">Short project name:</label>';
	if ( $currProject ) {
		print '  <input type="text" id="projectName" name="projectName" value="'.$currProject->project_name.'">';
	}
	else {
		print '  <input type="text" id="projectName" name="projectName">';
	}
	print '</p>';

	print '<p>';
	print '<label for="prettyName">Project display name (pretty name):</label>';
	if ( $currProject ) {
		print '  <input type="text" id="prettyName" name="prettyName" value="'.$currProject->project_prettyname.'">';
	}
	else {
		print '  <input type="text" id="prettyName" name="prettyName">';
	}
	print '</p>';

	print '<p>';
	print '<label for="projectDescription">Project description:</label>';
	if ( $currProject ) {
		print '  <input type="text" class="input-xxlarge" id="projectDescription" name="projectDescription" value="'.$currProject->project_description.'">';
	}
	else {
		print '  <input type="text" class="input-xxlarge" id="projectDescription" name="projectDescription">';
	}
	print '</p>';
	
	print '<div>';
	print '<button id="next_projectForm2" type="button" class="btn btn-primary js-stools-btn-clear projectNextBack" title="Next" data-original-title="Next">Next</button>';
	print '</div>';

	print '</div>';
	
	
	print '<div id="projectForm2" class="projectForm" style="display:none">';

	print '<h3>Set a project image and article</h3>';

	print '<p>';
	print '<label for="imageDir">Project image folder (usually images/projects):</label>';
	if ( $currProject ) {
		print '  <input type="text" id="imageDir" name="imageDir" value="'.$currProject->dirname.'">';
	}
	else {
		print '  <input type="text" id="imageDir" name="imageDir" value="images/projects">';
	}
	print '</p>';

	print '<p>';
	print '<label for="imageFile">Project image filename:</label>';
	if ( $currProject ) {
		print '  <input type="text" id="imageFile" name="imageFile" value="'.$currProject->image_file.'">';
	}
	else {
		print '  <input type="text" id="imageFile" name="imageFile">';
	}
	print '</p>';

	print '<p>';
	print '<label for="articleId">Article id (if you have created a Joomla article for the project, input the id here - you can add it later if not):</label>';
	if ( $currProject ) {
		print '  <input type="number" id="articleId" name="articleId"  value="'.$currProject->article_id.'">';
	}
	else {
		print '  <input type="number" id="articleId" name="articleId" value="0">';
	}
	print '</p>';
	
	print '<div>';
	print '<div class="span1">';
	print '<button id="back_projectForm1" type="button" class="btn js-stools-btn-clear projectNextBack" title="Back" data-original-title="Back">Back</button>';
	print '</div>';
	print '<div class="span1">';
	print '<button id="next_projectForm3" type="button" class="btn btn-primary js-stools-btn-clear projectNextBack" title="Next" data-original-title="Next">Next</button>';
	print '</div>';
	print '</div>';
	
	print '</div>';
	
	
	print '<div id="projectForm3" class="projectForm" style="display:none">';

	print '<h3>Project hierarchy</h3>';

	print '<p>';
	print '<label for="parentProject">Choose a parent project (optional):</label>';
	print '<select id="parentProject" name="parentProject">';
	if ( $currProject && $currProject->parent_project_id != null && $currProject->parent_project_id > 0) {
		
		print '<option value="0">No parent</option>';
		foreach ( $projects as $id=>$project ) {
			if ( $currProject->parent_project_id == $id ) {
				print '<option value="'.$id.'" selected>'.$project->project_prettyname.'</option>';
			}
			else {
				print '<option value="'.$id.'">'.$project->project_prettyname.'</option>';
			}
		}
	}
	else {
		print '<option value="0" selected>No parent</option>';
		foreach ( $projects as $id=>$project ) {
			print '<option value="'.$id.'">'.$project->project_prettyname.'</option>';
		}
	}
	print '</select><br>';
	print '</p>';
	
	print '<div>';
	print '<div class="span1">';
	print '<button id="back_projectForm2" type="button" class="btn js-stools-btn-clear projectNextBack" title="Back" data-original-title="Back">Back</button>';
	print '</div>';
	print '<div class="span1">';
	print '<button id="next_projectForm4" type="button" class="btn btn-primary js-stools-btn-clear projectNextBack" title="Next" data-original-title="Next">Next</button>';
	print '</div>';
	print '</div>';
	
	
	print '</div>';
	
	
	print '<div id="projectForm4" class="projectForm" style="display:none">';
	
	print '<h3>Who can access the project for spotting and trapping?</h3>';

	print '<p>';
	print '<label for="accessLevel">Choose an access level:</label>';
	if ( $currProject ) {
		print '<select class="input-xxlarge" id="accessLevel" name="accessLevel">';
		foreach ( $accessLevels as $id=>$title ) {
			if ( $id == $currProject->access_level ) {
				print '<option value="'.$id.'" selected>'.$title.'</option>';
			}
			else {
				print '<option value="'.$id.'">'.$title.'</option>';
			}
		}
		print '</select><br>';
	}
	else {
		print '<select class="input-xxlarge" id="accessLevel" name="accessLevel">';
		foreach ( $accessLevels as $id=>$title ) {
			print '<option value="'.$id.'">'.$title.'</option>';
		}
		print '</select><br>';
	}
	print '</p>';
	
	print '<div>';
	print '<div class="span1">';
	print '<button id="back_projectForm3" type="button" class="btn js-stools-btn-clear projectNextBack" title="Back" data-original-title="Back">Back</button>';
	print '</div>';
	print '<div class="span1">';
	print '<button id="next_projectForm5" type="button" class="btn btn-primary js-stools-btn-clear projectNextBack" title="Next" data-original-title="Next">Next</button>';
	print '</div>';
	print '</div>';
	
	print '</div>';
	
	
	print '<div id="projectForm5" class="projectForm" style="display:none">';
	
	print '<h3>How should the project be displayed on MammalWeb?</h3>';
	
	print '<p>';
	print '<label for="listingLevel">Listing level (determines ordering on Projects page - normally between 1 and 10,000):</label>';
	if ( $currProject ) {
		print '  <input type="number" id="listingLevel" name="listingLevel" min="1" max="10000" value="'.$currProject->listing_level.'">';
	}
	else {
		print '  <input type="number" id="listingLevel" name="listingLevel" min="1" max="10000" value="100">';
	}
	print '</p>';

	print '<p>';
	print '<label for="displayOptions">What display options should the project have? These are used to construct the project page.</label>';	
	if ( $currProject ) {
		$currDisplayOptions = $currProject->displayOptions;
		foreach ( $displayOptions as $id=>$title ) {
			
			print '<div>';
			if ( array_key_exists($id, $currDisplayOptions) ) {
				print '<input type="checkbox" id="displayOption_'.$id.'" name="displayOptions[]" value="'.$id.'"  style="margin-bottom:7px;" checked> ';
			}
			else {
				print '<input type="checkbox" id="displayOption_'.$id.'" name="displayOptions[]" value="'.$id.'"  style="margin-bottom:7px;"> ';
			}
			print '<label for="displayOption_'.$id.'"  style="display:inline-block;">'.$title.'</label>';
			print '</div>';
			
		}
	}
	else {
		foreach ( $displayOptions as $id=>$title ) {
			
			print '<div>';
			print '<input type="checkbox" id="displayOption_'.$id.'" name="displayOptions[]" value="'.$id.'"  style="margin-bottom:7px;"> ';
			print '<label for="displayOption_'.$id.'"  style="display:inline-block;">'.$title.'</label>';
			print '</div>';
			
		}
	}
	print '</p>';
	
	print '<div>';
	print '<div class="span1">';
	print '<button id="back_projectForm4" type="button" class="btn js-stools-btn-clear projectNextBack" title="Back" data-original-title="Back">Back</button>';
	print '</div>';
	print '<div class="span1">';
	print '<button id="next_projectForm6" type="button" class="btn btn-primary js-stools-btn-clear projectNextBack" title="Next" data-original-title="Next">Next</button>';
	print '</div>';
	print '</div>';

	print '</div>';
	
	
	print '<div id="projectForm6" class="projectForm" style="display:none">';
	
	print '<h3>How should the project be classified?</h3>';

	print '<p>';
	print '<label for="priority">What classifying priority should the project have?</label>';
	print '<select id="priority" name="priority">';
	if ( $currProject ) {
		foreach ( $priorities as $id=>$title ) {
			if ( $id == $currProject->priority_id ) {
				print '<option value="'.$id.'" selected>'.$title.'</option>';
			}
			else {
				print '<option value="'.$id.'">'.$title.'</option>';
			}
		}
	}
	else {
		foreach ( $priorities as $id=>$title ) {
			print '<option value="'.$id.'">'.$title.'</option>';
		}
	}
	print '</select><br>';
	print '</p>';


	print '<p>';
	print '<label for="speciesLists">What species lists should the project have?  These are used to choose species when classifying.</label>';	
	if ( $currProject ) {
		$currSpeciesLists = $currProject->speciesLists;
		foreach ( $speciesLists as $id=>$title ) {
			
			print '<div>';
			if ( array_key_exists($id, $currSpeciesLists) ) {
				print '<input type="checkbox" id="speciesList_'.$id.'" name="speciesLists[]" value="'.$id.'"  style="margin-bottom:7px;" checked> ';
			}
			else {
				print '<input type="checkbox" id="speciesList_'.$id.'" name="speciesLists[]" value="'.$id.'"  style="margin-bottom:7px;"> ';
			}
			print '<label for="speciesList_'.$id.'"  style="display:inline-block;">'.$title.'</label>';
			print '</div>';
			
		}
	}
	else {
		foreach ( $speciesLists as $id=>$title ) {
		
			print '<div>';
			print '<input type="checkbox" id="speciesList_'.$id.'" name="speciesLists[]" value="'.$id.'"  style="margin-bottom:7px;"> ';
			print '<label for="speciesList_'.$id.'"  style="display:inline-block">'.$title.'</label>';
			print '</div>';
			
		}
	}
	
	print '</p>';
	
	print '<div>';
	print '<div class="span1">';
	print '<button id="back_projectForm5" type="button" class="btn js-stools-btn-clear projectNextBack" data-original-title="Back">Back</button>';
	print '</div>';
	print '<div class="span1">';
	print '<button id="next_projectForm7" type="button" class="btn btn-primary js-stools-btn-clear projectNextBack" title="Next" data-original-title="Next">Next</button>';
	print '</div>';
	print '</div>';

	print '</div>';
	
	
	print '<div id="projectForm7" class="projectForm" style="display:none">';
	
	print '<h3>Set project admin user(s)</h3>';

	print '<p>';
	print '<label for="projectAdmins">Choose who should be project administrators for this project (users with Project Admin permissions in Joomla)</label>';	
	if ( $currProject ) {
		$currProjectAdmins = $currProject->projectAdmins;
		foreach ( $projectAdmins as $id=>$user ) {
			
			print '<div>';
			if ( array_key_exists($id, $currProjectAdmins) ) {
				print '<input type="checkbox" id="projectAdmin_'.$id.'" name="projectAdmins[]" value="'.$id.'"  style="margin-bottom:7px;" checked> ';
			}
			else {
				print '<input type="checkbox" id="projectAdmin_'.$id.'" name="projectAdmins[]" value="'.$id.'"  style="margin-bottom:7px;"> ';
			}
			print '<label for="projectAdmin_'.$id.'"  style="display:inline-block;">'.$user->username.' ('. $user->name . ', ' . $user->email . ')</label>';
			print '</div>';
			
		}
	}
	else {
		foreach ( $projectAdmins as $id=>$user ) {
		
			print '<div>';
			print '<input type="checkbox" id="projectAdmin_'.$id.'" name="projectAdmins[]" value="'.$id.'"  style="margin-bottom:7px;"> ';
			print '<label for="projectAdmin_'.$id.'"  style="display:inline-block">'.$user->username.' ('. $user->name . ', ' . $user->email . ')</label>';
			print '</div>';
			
		}
	}
	
	print '</p>';
	
	print '<div>';
	print '<div class="span1">';
	print '<button id="back_projectForm6" type="button" class="btn js-stools-btn-clear projectNextBack" data-original-title="Back">Back</button>';
	print '</div>';
	print '<div class="span1">';
	print '<button id="next_projectForm8" type="button" class="btn btn-primary js-stools-btn-clear projectNextBack showProjectSave" title="Next" data-original-title="Next">Next</button>';
	print '</div>';
	print '</div>';

	print '</div>';
	
	
	
	print '<div id="projectForm8" class="projectForm" style="display:none">';
	
	print '<h3>Is this project a BES Schools project?</h3>';
	
	$existingSchool = false;
	if ( $currProject ) {
		if ( $currProject->school_id ) {
			$existingSchool = true;
			print '<p>';
			print '<input type="radio" id="schoolProjectNo" name="isSchoolProject" value="0"  style="margin-bottom:7px;"> ';
			print '<label for="schoolProjectNo"  style="display:inline-block;">No</label>';
			print '</p>';
			print '<p>';
			print '<input type="radio" id="schoolProjectExists" name="isSchoolProject" value="1"  style="margin-bottom:7px;" checked> ';
			print '<label for="schoolProjectExists"  style="display:inline-block;">Yes, school exists in database</label>';
			print '</p>';
			print '<p>';
			print '<input type="radio" id="schoolProjectCreate" name="isSchoolProject" value="2"  style="margin-bottom:7px;"> ';
			print '<label for="schoolProjectCreate"  style="display:inline-block;">Yes, create new school in database</label>';
			print '</p>';
		}
		else {
			print '<p>';
			print '<input type="radio" id="schoolProjectNo" name="isSchoolProject" value="0"  style="margin-bottom:7px;" checked> ';
			print '<label for="schoolProjectNo"  style="display:inline-block;">No</label>';
			print '</p>';
			print '<p>';
			print '<input type="radio" id="schoolProjectExists" name="isSchoolProject" value="1"  style="margin-bottom:7px;"> ';
			print '<label for="schoolProjectExists"  style="display:inline-block;">Yes, school exists in database</label>';
			print '</p>';
			print '<p>';
			print '<input type="radio" id="schoolProjectCreate" name="isSchoolProject" value="2"  style="margin-bottom:7px;"> ';
			print '<label for="schoolProjectCreate"  style="display:inline-block;">Yes, create new school in database</label>';
			print '</p>';
		}
	}
	else {
		print '<p>';
		print '<input type="radio" id="schoolProjectNo" name="isSchoolProject" value="0"  style="margin-bottom:7px;" checked> ';
		print '<label for="schoolProjectNo"  style="display:inline-block;">No</label>';
		print '</p>';
		print '<p>';
		print '<input type="radio" id="schoolProjectExists" name="isSchoolProject" value="1"  style="margin-bottom:7px;"> ';
		print '<label for="schoolProjectExists"  style="display:inline-block;">Yes, school exists in database</label>';
		print '</p>';
		print '<p>';
		print '<input type="radio" id="schoolProjectCreate" name="isSchoolProject" value="2"  style="margin-bottom:7px;"> ';
		print '<label for="schoolProjectCreate"  style="display:inline-block;">Yes, create new school in database</label>';
		print '</p>';
	}
	
	print '<br/>';
	
	print '<div id="schoolCreate" style="display:none" >';
	
	print '  <label for="newSchoolName">New school name:</label>';
	print '  <input class="input-xlarge" type="text" id="newSchoolName" name="newSchoolName">';
	
	print '</div>';
	
	
	if ( $existingSchool ) {
		print '<div id="schoolExists" >';
	}
	else {
		print '<div id="schoolExists" style="display:none">';
	}
	
	print '<label for="school">School name:</label>';
	if ( $currProject ) {
		print '<select id="school" name="school">';
		print '<option value="0" selected>No school</option>';
		foreach ( $schools as $id=>$school ) {
			if ( $id == $currProject->school_id ) {
				print '<option value="'.$id.'" selected>'.$school->name.'</option>';
			}
			else {
				print '<option value="'.$id.'">'.$school->name.'</option>';
			}
		}
		print '</select><br>';
	}
	else {
		print '<select id="school" name="school">';
		foreach ( $schools as $id=>$school ) {
			print '<option value="'.$id.'">'.$school->name.'</option>';
		}
		print '</select><br>';
	}
	
	print '</div>';
	
	
	print '<div id="projectMsg"></div>';

	
	print '<br/>';
	
	print '<div>';
	print '<div class="span1">';
	print '<button id="back_projectForm7" type="button" class="btn js-stools-btn-clear projectNextBack hideProjectSave" data-original-title="Back">Back</button>';
	print '</div>';
	print '</div>';

	print '</div>';
	

}


function getAllProjects () {
	
	$db = \JDatabaseDriver::getInstance(dbOptions());


	$query = $db->getQuery(true)
		->select("P.* from Project P")
		->order("project_prettyname");
		
	$db->setQuery($query);
	
	//error_log("getAllProjects select query created: " . $query->dump());
	
	$projects = $db->loadObjectList("project_id");
	
	
	
	return $projects;
		
}


function getProject ( $projectId ) {
	
	$options = dbOptions();
	$db = \JDatabaseDriver::getInstance($options);


	$query = $db->getQuery(true)
		->select("P.*, O.option_id as priority_id, O.option_name as priority, P2.project_prettyname as parent_name, S.school_id, S.name as school_name from Project P")
		->leftJoin("ProjectOptions PO on PO.project_id = P.project_id")
		->leftJoin("Options O on O.option_id = PO.option_id and O.struc = 'priority'")
		->leftJoin("Project P2 on P2.project_id = P.parent_project_id")
		->leftJoin("School S on S.project_id = P.project_id")
		->where("P.project_id = " . $projectId);
		
	$db->setQuery($query);
	
	//error_log("ProjectSetup project select query created: " . $query->dump());
	
	$project = $db->loadObject();
	
	// $errMsg = print_r ( $this->project, true );
	// error_log ( "New project: " . $errMsg );
	
	
	$query = $db->getQuery(true)
		->select("O.option_id, O.option_name from Options O")
		->innerJoin("ProjectOptions PO on PO.option_id = O.option_id and O.struc = " . $db->quote("projectdisplay"))
		->where("PO.project_id = " . $projectId);
		
	$db->setQuery($query);
	
	//error_log("ProjectSetup projectdisplay select query created: " . $query->dump());
	
	$project->displayOptions = $db->loadAssocList("option_id", "option_name");
	
	
	$query = $db->getQuery(true)
		->select("O.option_id, O.option_name from Options O")
		->innerJoin("ProjectOptions PO on PO.option_id = O.option_id and O.struc = " . $db->quote("projectfilter"))
		->where("PO.project_id = " . $projectId);
		
	$db->setQuery($query);
	
	//error_log("ProjectSetup filter select query created: " . $query->dump());
	
	$project->speciesLists = $db->loadAssocList("option_id", "option_name");
	
	
	$userDb = $options['userdb'];
	$prefix = $options['userdbprefix'];
	
	$query = $db->getQuery(true)
		->select("PUM.person_id, U.username, U.name, U.email from ProjectUserMap PUM")
		->innerJoin($userDb . "." . $prefix ."users U on PUM.person_id = U.id")
		->where("PUM.role_id = 1")
		->where("PUM.project_id = " . $projectId)
		->order("U.name");
	
	$db->setQuery($query);
	
	//error_log("ProjectSetup project admin query created: " . $query->dump());
	
	$project->projectAdmins = $db->loadObjectList("person_id");
	
	return $project;
		
}


function getAllProjectAdmins () {
	
	$joomlaDb = JFactory::getDbo();
	                    
    $query = $joomlaDb->getQuery(true);
	$query->select("U.id, U.name, U.username, U.email from #__users U")
		->innerJoin("#__usergroups UG on UG.title = " . $joomlaDb->quote("Project Admin") )
		->innerJoin("#__user_usergroup_map UM on UM.user_id = U.id and UM.group_id = UG.id");
		
	$joomlaDb->setQuery($query);
	
	//error_log("getAllProjectAdmins select query created: " . $query->dump());
	
	$users = $joomlaDb->loadObjectList("id");
    
	return $users;
	
}


function printSchoolForm ( $schoolId = null ) {
	
	$projects = getAllProjects();
						
	$currSchool = null;
	if ( $schoolId ) {
		$currSchool = getSchool ( $schoolId );
		
		print '<input type="hidden" name="id" value="'.$schoolId.'"/>';
		
	}
	
	print '<div id="schoolForm1" class="schoolForm">';
	
	
	print '<p>';
	print '<label for="schoolName">School name:</label>';
	if ( $currSchool ) {
		print '  <input type="text" id="schoolName" name="schoolName" value="'.$currSchool->name.'" required>';
	}
	else {
		print '  <input type="text" id="schoolName" name="schoolName"  required>';
	}
	print '</p>';
	
	
	print '<p>';
	print '<label for="image">School image file (including path eg images/projects/BES/schools/MyImage.png):</label>';
	if ( $currSchool ) {
		print '  <input class="input-xxlarge" type="text" id="image" name="image" value="'.$currSchool->image.'">';
	}
	else {
		print '  <input class="input-xxlarge" type="text" id="image" name="image" value="images/projects/BES/schools/MyImage.png">';
	}
	print '</p>';
	
	
	print '<p>';
	print '<label for="projectId">Does the school have a MammalWeb project? (optional):</label>';
	print '<select id="projectId" name="projectId">';
	if ( $currSchool && $currSchool->project_id != null && $currSchool->project_id > 0) {
		
		print '<option value="0">No project</option>';
		foreach ( $projects as $id=>$project ) {
			if ( $currSchool->project_id == $id ) {
				print '<option value="'.$id.'" selected>'.$project->project_prettyname.'</option>';
			}
			else {
				print '<option value="'.$id.'">'.$project->project_prettyname.'</option>';
			}
		}
	}
	else {
		print '<option value="0" selected>No project</option>';
		foreach ( $projects as $id=>$project ) {
			print '<option value="'.$id.'">'.$project->project_prettyname.'</option>';
		}
	}
	print '</select><br>';
	print '</p>';
	
	
	
	
	
	print '</div>';
	
	
}


function getAllSchools () {
	
	$db = \JDatabaseDriver::getInstance(dbOptions());


	$query = $db->getQuery(true)
		->select("S.* from School S")
		->order("name");
		
	$db->setQuery($query);
	
	//error_log("getAllProjects select query created: " . $query->dump());
	
	$schools = $db->loadObjectList("school_id");
	
	return $schools;
		
}



function getSchool ( $schoolId ) {
	
	$db = \JDatabaseDriver::getInstance(dbOptions());


	$query = $db->getQuery(true)
		->select("*, P.project_prettyname as project from School S")
		->leftJoin("Project P on P.project_id = S.project_id")
		->where("school_id = " . $schoolId);
		
	$db->setQuery($query);
	
	$school = $db->loadObject();
	
	return $school;
		
}


function getSchoolUsers ( $usergroup ) {
	
	$joomlaDb = JFactory::getDbo();
	                    
    $query = $joomlaDb->getQuery(true);
	$query->select("U.id, U.name, U.username, U.email from #__users U")
		->innerJoin("#__usergroups UG on UG.title = " . $joomlaDb->quote($usergroup) )
		->innerJoin("#__user_usergroup_map UM on UM.user_id = U.id and UM.group_id = UG.id");
		
	$joomlaDb->setQuery($query);
	
	//error_log("getSchoolUsers select query created: " . $query->dump());
	
	$users = $joomlaDb->loadObjectList("id");
    
	return $users;
}



function printSchoolUserForm ( $schoolId, $usergroup ) {
	
	$users = getSchoolUsers($usergroup);
	
	print '<input type="hidden" name="schoolId" value="'.$schoolId.'"/>';

	$selectId = "add" . str_replace(' ', '', $usergroup);
						
	print '<p>';
	print '<label for="'.$selectId.'">Add a '.$usergroup.' to this school:</label>';
	print '<select id="'.$selectId.'" name="userId" class="input-xxlarge">';
	foreach ( $users as $id=>$user ) {
		print '<option value="'.$id.'">'.$user->name.' ('.$user->email.')</option>';
	}	
	print '</select><br>';
	print '</p>';
	
}



function getOptions ( $struc ) {
	
	$db = \JDatabaseDriver::getInstance(dbOptions());

	$query = $db->getQuery(true)
		->select("option_id, option_name from Options")
		->where("struc = " . $db->quote($struc) )
		->order("option_name");
		
	$db->setQuery($query);
	
	//error_log("getAllProjects select query created: " . $query->dump());
	
	$options = $db->loadAssocList("option_id", "option_name");
	
	return $options;
		
}

function getPriorities() {
	
	return getOptions ( 'priority' );

}

function getSpeciesLists () {
	
	return getOptions ( 'projectfilter' );
		
}

function getProjectDisplayOptions () {
	
	return getOptions ( 'projectdisplay' );
		
}

function getAccessLevels () {
	
	return array("Public (public spotting and trapping)", 
				"Hybrid (public spotting, restricted trappping)", 
				"Restricted (restricted spotting and trapping)", 
				"Private (restricted and not displayed on website)" );
		
}

function getUserDetails ( $userId ) {
	
	$user = JFactory::getUser($userId);
	
	$userDetails = new StdClass();
	$userDetails->username = $user->username;
	$userDetails->name = $user->name;
	$userDetails->email = $user->email;
	
	return $userDetails;

}


function createReportFile ( $type, $headings, $rows ) {
	
	$reportRoot = JPATH_SITE."/biodivimages/reports";
	$filePath = $reportRoot."/admin/";
	
	$t=time();
	$dateStr = date("Ymd_His",$t);
	$filename = $type . '_' . $dateStr . ".csv";
	
	$tmpCsvFile = $filePath . "/tmp_" . $filename;
	$newCsvFile = $filePath . "/" . $filename;
	
	// Has the report already been created?
	if ( !file_exists($newCsvFile) ) {
		
		// Creates a new csv file and store it in directory
		// Rename once finished writing to file
		if (!file_exists($filePath)) {
			mkdir($filePath, 0755, true);
		}
		
		$tmpCsv = fopen ( $tmpCsvFile, 'w');
		
		// First put the headings
		if ( $headings ) {
			fputcsv($tmpCsv, $headings);
		}
		
		// Then each row
		foreach ( $rows as $row ) {
			fputcsv($tmpCsv, $row);
		}
		
		fclose($tmpCsv);
		
		rename ( $tmpCsvFile, $newCsvFile );

	}
	
	$url = JURI::root()."/biodivimages/reports/admin/".$filename;
	return $url;
}


function createReportFileTxt ( $type, $rows ) {
	
	$reportRoot = JPATH_SITE."/biodivimages/reports";
	$filePath = $reportRoot."/admin/";
	
	$t=time();
	$dateStr = date("Ymd_His",$t);
	$filename = $type . '_' . $dateStr . ".txt";
	
	$tmpTxtFile = $filePath . "/tmp_" . $filename;
	$newTxtFile = $filePath . "/" . $filename;
	
	// Has the report already been created?
	if ( !file_exists($newTxtFile) ) {
		
		// Creates a new csv file and store it in directory
		// Rename once finished writing to file
		if (!file_exists($filePath)) {
			mkdir($filePath, 0755, true);
		}
		
		$tmpTxt = fopen ( $tmpTxtFile, 'w');
		
		// Put each row
		foreach ( $rows as $row ) {
			fputs($tmpTxt, $row . "\r\n");
		}
		
		fclose($tmpTxt);
		
		rename ( $tmpTxtFile, $newTxtFile );

	}
	
	$url = JURI::root()."/biodivimages/reports/admin/".$filename;
	return $url;
}

function getAllArticlesForTranslation ( $page, $length, $searchStr = null ) {
	
	$db = JFactory::getDBO();
	
	$query = $db->getQuery(true)
		->select($db->quoteName(array('C.id', 'C.title', 'C.alias', 'CA.title', 'C.modified'), array('article_id', 'title', 'alias', 'category', 'modified')))
		->from($db->quoteName('#__content', 'C'))
		->innerJoin($db->quoteName('#__categories', 'CA'))
		->where("C.catid = CA.id")
		->where("C.language = " . $db->quote('en-GB')  )
		->order("C.modified DESC");
		
	if ( $searchStr ) {
		$query->where('C.title like "%'.$searchStr.'%"');
	}

	$db->setQuery($query);
	$db->execute();
	
	$totalRows = $db->getNumRows();
		
	$start = ($page-1)*$length;
		
	$db->setQuery($query, $start, $length);
	
	//error_log("getAllArticlesForTranslation select query created: " . $query->dump());
	
	$articles = $db->loadObjectList("article_id");
		
	return (object)array("total"=>$totalRows, "articles"=>$articles);
	
}


function getArticle ( $articleId ) {
	
	$jarticle = JTable::getInstance("content");

	$jarticle->load($articleId); 
	
	return $jarticle;
	
}



function getSupportedLanguages () {
	
	$db = JDatabase::getInstance(dbOptions());
	
	$query = $db->getQuery(true);
	$query->select("*")
		->from("Language")
		->where("tag != " . $db->quote('en-GB') );
	$db->setQuery($query);
	$langs = $db->loadObjectList("language_id");
	
	return $langs;
}


function getSupportedLanguage ( $tag ) {
	
	$db = JDatabase::getInstance(dbOptions());
	
	$query = $db->getQuery(true);
	$query->select("*")
		->from("Language")
		->where("tag = " . $db->quote($tag) );
	$db->setQuery($query);
	$lang = $db->loadObject();
	
	return $lang;
}


function getSpeciesTranslationLists () {
	
	$db = JDatabase::getInstance(dbOptions());
	
	$query = $db->getQuery(true)
			->select("*")
			->from("SpeciesTranslationLists");
	$db->setQuery($query);
	
	return $db->loadObjectList("id");
}


function getOptionsHeadings () {
	
	$languages = getSupportedLanguages();
	
	$headings = array("Key", "en-GB", "Context"); 
			
	foreach ($languages as $id=>$lang) {
		$headings[] = $lang->transifex_code;
	}
	
	return $headings;
}


function getNonSpeciesOptionsForTranslation () {
	
	$languages = getSupportedLanguages();
	
	$db = JDatabase::getInstance(dbOptions());
		
	$query = $db->getQuery(true);
	
	$selectStr = "O.option_id, O.option_name, O.struc, O.article_id";
	
	foreach ($languages as $id=>$lang) {
		$selectStr .= ", ".strtoupper($lang->transifex_code).".value as ".strtolower($lang->transifex_code);
	}
		
	$query->select($selectStr)
		->from("Options O");
		
	foreach ($languages as $id=>$lang) {
		$tableName = strtoupper($lang->transifex_code);
		$query->leftJoin("OptionData ".$tableName." on O.option_id = ".$tableName.".option_id and ".$tableName.".data_type = " . $db->quote($lang->tag));
	}
				
	$query->where("struc not in ( 'mammal', 'bird', 'invertebrate', 'beshelp', 'camera', 'kiosk', 'kiosktutorial', 'logo', 'projectdisplay' )")
		->order("struc, option_name");

	$db->setQuery($query);
	
	//error_log("generateTranslateSpeciesData select query created " . $query->dump());
	
	$options = $db->loadObjectList("option_id");
	
	return $options;
}


function getAllAssociations ( $articleId ) {
	
	$associations = null;
	
	if ($articleId)
	{
		$associations = JLanguageAssociations::getAssociations('com_content', '#__content', 'com_content.item', $articleId);
		
	}
	
	return $associations;
}


function getAssociations ( $articleId, $lang ) {
	
	$assoc = null;
	
	if ($articleId)
	{
		$associations = JLanguageAssociations::getAssociations('com_content', '#__content', 'com_content.item', $articleId);
		
		if ( $associations ) {
			if ( array_key_exists($lang, $associations) ) {
				$assoc = $associations[$lang];
			}
		}
	}
	
	return $assoc;
}


function getAssociatedArticle ( $articleId, $lang ) {
	
	$assocId = null;

	if ($articleId != null)
	{
		$associations = JLanguageAssociations::getAssociations('com_content', '#__content', 'com_content.item', $articleId);
		
		if ( $associations ) {
			
			if ( array_key_exists($lang, $associations) ) {
				//$assocId = $associations[$lang]->id;
				$id = explode ( ':', $associations[$lang]->id );
				$assocId = $id[0];
			}
		}
	}
	
	return $assocId;
}


function getAssociatedCategory ( $catId, $lang ) {
	
	$assocId = null;
	
	if ($catId)
	{
		try {
			$associations = JLanguageAssociations::getAssociations('com_content', '#__categories', 'com_categories.item', $catId, 'id', 'alias', 'id');
		}
		catch ( Exception $e ) {
			
			error_log ( "Exception caught: " . $e->getMessage() );
		
		}
		
		
		if ( $associations ) {
			//$assocId = $associations[$lang]->id;
			if ( array_key_exists($lang, $associations) ) {
				$id = explode ( ':', $associations[$lang]->id );
				$assocId = $id[0];
			}
			else {
				error_log ("No associated category for cat " . $catId . ", language " . $lang );
			}
		}
	}
	
	return $assocId;
}


function isJSON ( $string ) {
	
   return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
   
}


function getJSONDecodedArray ( $string ) {
	
	$decoded = null;
	if ( is_string ( $string ) ) {
		$newArray = json_decode($string, true);
		if ( is_array($newArray) && (json_last_error() == JSON_ERROR_NONE) ) {
			$decoded = $newArray;
		}
	}
	
	return $decoded;
   
}


function checkHtml ( $htmlStr ) {
	
	$warning = null;
	
	// Just a basic check
	if ( strpos ( strtolower($htmlStr), "script") !== FALSE ) {
		$warning = "Text may contain a script";
	}
	
	return $warning;
}


function updateArticle ( $articleId, $lang, $title, $html ) {
	
	//error_log ( "updateArticle called" );
	
	$response = null;
	$assocId = null;
	$assoc = null;
	
	$allAssociations = getAllAssociations ( $articleId );
	
	$assocsForKey = array();
	foreach ( $allAssociations as $assocLang=>$assocObj ) {
		$id = explode ( ':', $assocObj->id );
		$assocsForKey[$assocLang] = $id[0];
	}
	
	if ( $allAssociations ) {
		if ( array_key_exists($lang, $allAssociations) ) {
			$assoc = $allAssociations[$lang];
		}
	}
	
	$errMsg = print_r ( $allAssociations, true );
	//error_log ( "Got allAssociations: " . $errMsg);
	
	$errMsg = print_r ( $assocsForKey, true );
	//error_log ( "Created assocsForKey: " . $errMsg);
	
	if ( $assoc ) {
		
		$id = explode ( ':', $assoc->id );
		$assocId = $id[0];
	}
	
	if ( !$assocId ) {
		
		error_log ( "No associated article" );
		
		$table = JTable::getInstance('Content', 'JTable', array());

		$article = getArticle ( $articleId );
		
		// $errMsg = print_r ( $article, true );
		// error_log ( "Article: " . $errMsg );
		
		//$title = $article->title . " " . $lang; 
		$alias = $article->alias . "-" . strtolower($lang); 
	
		$catId = getAssociatedCategory ( $article->catid, $lang );
		
		
		//error_log ( "Got associated category: " . $catId );
		
		$data = array(
			'catid' => $catId,
			'title' => $title,
			//'alias' =>  $alias,
			'language' => $lang,
			'introtext' => $html,
			'state' => 1,
			'images' => $article->images,
			'urls' => $article->urls,
			'attribs' => $article->attribs
		);

		//error_log ( "About to bind, html = " . $html );
		
		try {
			// Bind data
			if (!$table->bind($data))
			{
				throw new Exception("Failed to bind article data. Error: " . $table->getError());
			}

			//error_log ( "About to check" );
			
			// Check the data.
			if (!$table->check())
			{
				throw new Exception("Failed to check article data. Error: " . $table->getError());
			}

			//error_log ( "About to store" );
			
			// Store the data.
			if (!$table->store())
			{
				//error_log ( "Store unsuccessful, error = " . $table->getError() );
				
				throw new Exception("Failed to store article data. Error: " . $table->getError());
			}
			
			$response = "Article text created successfully for new associated article " . $articleId . ", language " . $lang;
			
			$assocTable = JTable::getInstance('Associations', 'JTable', array());
			$associationsContext = 'com_content.item';
			
			//error_log ( "About to handle associations" );
			
			// There is no association for this language but is there for teh original article id
			$db = JFactory::getDBO();
			$query = $db->getQuery(true)
				->select($db->qn('key'))
				->from($db->qn('#__associations'))
				->where($db->qn('context') . ' = ' . $db->quote($associationsContext))
				->where($db->qn('id') . ' = ' . (int) $articleId);
			$db->setQuery($query);
			
			//error_log("Associations old_key query created: " . $query->dump());
			
			$old_key = $db->loadResult();
			
			if ( $old_key ) {

				// Deleting old associations for the associated items
				$query = $db->getQuery(true)
					->delete($db->qn('#__associations'))
					->where($db->qn('context') . ' = ' . $db->quote($associationsContext));

				if ($assocsForKey)
				{
					$query->where('(' . $db->qn('id') . ' IN (' . implode(',', $assocsForKey) . ') OR '
						. $db->qn('key') . ' = ' . $db->q($old_key) . ')');
				}
				else
				{
					$query->where($db->qn('key') . ' = ' . $db->q($old_key));
				}

				$db->setQuery($query);
				
				//error_log("Associations delete query created: " . $query->dump());
				
				$db->execute();
			}
			
			//error_log ( "Table language = " . $table->language );
			//error_log ( "Table id = " . $table->id );

			// Adding original and self to the association
			
			$assocsForKey['en-GB'] = (int) $articleId;
			if ($table->language !== '*')
			{
				$assocsForKey[$table->language] = (int) $table->id;
			}

			if (count($assocsForKey) > 1)
			{
				$errMsg = print_r ( $assocsForKey, true );
				//error_log ( "Associatons: " . $errMsg );
				
				// Adding new association for these items
				$key   = md5(json_encode($assocsForKey));
				
				//error_log ( "New key = " . $key );
				
				$query = $db->getQuery(true)
					->insert('#__associations');

				foreach ($assocsForKey as $id)
				{
					$query->values(((int) $id) . ',' . $db->quote($associationsContext) . ',' . $db->quote($key));
				}

				$db->setQuery($query);
				
				//error_log("Associations delete query created: " . $query->dump());
				
				$db->execute();
			}
			
		} catch ( Exception $e ) {
			
			$response = "Article text failed to create for new associated article " . $articleId . ", language " . $lang . ". Reason: " . $e->getMessage();
			
		}
		
	}
	else {
		
		$db = JFactory::getDBO();
		
		$articleFields = new StdClass();
		$articleFields->id = $assocId;
		$articleFields->title = $title;
		$articleFields->introtext = $html;
		$success = null;
		
		//error_log ( "Updating existing article, html = " . $html );
		$errMsg = print_r ( $articleFields, true );
		//error_log ("Update fields: " . $errMsg );
		
		try {
			$success = $db->updateObject('#__content', $articleFields, "id");
		}
		catch ( Exception $e ) {
			$response = "#__content update failed for article " . $articleId . ", language " . $lang . ", exception caught: " . $e;
			error_log ( $response );
			
		}
		if(!$success){
			$response = "Article text update failed for article " . $articleId . ", language " . $lang;
			error_log ( $response );
		}
		else {
			$response = "Article text updated successfully for article " . $articleId . ", language " . $lang;
		}
		
	}
	return $response;
}


function deletePhotos ( $uploadedFile ) {

	$success = false;
	$num_deleted = 0;
	$deleted_status = 20; 

	// For each photo on the list
	$photos=file($uploadedFile);
	foreach($photos as $line)
	{
		// Trim any quotes
		$photoId = str_replace(['"', "'"], '', trim($line));

		if (filter_var($photoId, FILTER_VALIDATE_INT) !== false) {

			// Get photo details
			$details = codes_getDetails ( $photoId, "photo" );
	
			if ( $details ) {
				// Get the S3 bucket key
				$photoKey = s3Key ( $details );
	
				$db = \JDatabaseDriver::getInstance(dbOptions());
	
				// Set the status to DELETED in the Photo table
				$query = $db->getQuery(true)
                			->update($db->qn('Photo'))
					->set($db->qn('status') . ' = ' . $deleted_status)
					->where($db->qn('photo_id') . ' = ' . $photoId );
	
                		$db->setQuery($query);
	
                		$db->execute();
	
				// Remove the image from the S3 bucket
				$success = delete_from_s3 ( $photoKey );

				if ( !$success ) {
					error_log ( "deletePhotos: failed to delete photo_id " . $photoId );
				}
				else {
					$num_deleted += 1;
				}
			}
			else {
				error_log ( "Found row with non photo_id: " . $photoId );
			}
		}
		else {
			 error_log ( "Found row with non integer photo_id, could be header: " . $line );
		}

	}

	return $num_deleted;
}


function  moveDeletionsFileToS3($uploadPath) {

	move_deletions_file_to_s3 ( $uploadPath );
}


// function newOrUpdateArticle ( $data ) {
	
	// $response = null;
	
	// // Mimic the code in AdminModel
	// $dispatcher = \JEventDispatcher::getInstance();
	// //$table      = $this->getTable();
	// $table      = JTable::getInstance('Content', 'JTable', array());
	// //$context    = $this->option . '.' . $this->name;
	// $context    = 'com_content.item';
	// $app        = \JFactory::getApplication();

	// if (!empty($data['tags']) && $data['tags'][0] != '')
	// {
		// $table->newTags = $data['tags'];
	// }

	// $key = $table->getKeyName();
	// $pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
	// $isNew = true;

	// // Include the plugins for the save events.
	// //\JPluginHelper::importPlugin($this->events_map['save']);

	// // Allow an exception to be thrown.
	// try
	// {
		// // Load the row if saving an existing record.
		// if ($pk > 0)
		// {
			// $table->load($pk);
			// $isNew = false;
		// }

		// // Bind the data.
		// if (!$table->bind($data))
		// {
			// //$this->setError($table->getError());
			
			// //return false;
			
			// throw new Exception("Failed to bind article data. Error: " . $table->getError());

		// }

		// // Prepare the row for saving
		// //$this->prepareTable($table);

		// // Check the data.
		// if (!$table->check())
		// {
			// // $this->setError($table->getError());

			// // return false;
			
			// throw new Exception("Failed to check article data. Error: " . $table->getError());
		// }

		// // Trigger the before save event.
		// //$result = $dispatcher->trigger($this->event_before_save, array($context, $table, $isNew, $data));

		// // if (in_array(false, $result, true))
		// // {
			// // $this->setError($table->getError());

			// // return false;
			
		// // }

		// // Store the data.
		// if (!$table->store())
		// {
			// // $this->setError($table->getError());

			// // return false;
			
			// throw new Exception("Failed to store article data. Error: " . $table->getError());
		// }

		// // Clean the cache.
		// //$this->cleanCache();

		// // Trigger the after save event.
		// //$dispatcher->trigger($this->event_after_save, array($context, $table, $isNew, $data));
	// }
	// catch (\Exception $e)
	// {
		// // $this->setError($e->getMessage());

		// // return false;
		
		// $response = "Article text failed to create for new associated article " . $articleId . ", language " . $lang . ". Reason: " . $e->getMessage();
	// }

	// if (isset($table->$key))
	// {
		// $this->setState($this->getName() . '.id', $table->$key);
	// }

	// $this->setState($this->getName() . '.new', $isNew);

	// if ($this->associationsContext && \JLanguageAssociations::isEnabled() && !empty($data['associations']))
	// {
		// $associations = $data['associations'];

		// // Unset any invalid associations
		// $associations = ArrayHelper::toInteger($associations);

		// // Unset any invalid associations
		// foreach ($associations as $tag => $id)
		// {
			// if (!$id)
			// {
				// unset($associations[$tag]);
			// }
		// }

		// // Show a warning if the item isn't assigned to a language but we have associations.
		// if ($associations && $table->language === '*')
		// {
			// $app->enqueueMessage(
				// \JText::_(strtoupper($this->option) . '_ERROR_ALL_LANGUAGE_ASSOCIATED'),
				// 'warning'
			// );
		// }

		// // Get associationskey for edited item
		// $db    = $this->getDbo();
		// $query = $db->getQuery(true)
			// ->select($db->qn('key'))
			// ->from($db->qn('#__associations'))
			// ->where($db->qn('context') . ' = ' . $db->quote($this->associationsContext))
			// ->where($db->qn('id') . ' = ' . (int) $table->$key);
		// $db->setQuery($query);
		// $old_key = $db->loadResult();

		// // Deleting old associations for the associated items
		// $query = $db->getQuery(true)
			// ->delete($db->qn('#__associations'))
			// ->where($db->qn('context') . ' = ' . $db->quote($this->associationsContext));

		// if ($associations)
		// {
			// $query->where('(' . $db->qn('id') . ' IN (' . implode(',', $associations) . ') OR '
				// . $db->qn('key') . ' = ' . $db->q($old_key) . ')');
		// }
		// else
		// {
			// $query->where($db->qn('key') . ' = ' . $db->q($old_key));
		// }

		// $db->setQuery($query);
		// $db->execute();

		// // Adding self to the association
		// if ($table->language !== '*')
		// {
			// $associations[$table->language] = (int) $table->$key;
		// }

		// if (count($associations) > 1)
		// {
			// $errMsg = print_r ( $associations, true );
			// error_log ( "Associatons: " . $errMsg );
			
			// // Adding new association for these items
			// $key   = md5(json_encode($associations));
			// $query = $db->getQuery(true)
				// ->insert('#__associations');

			// foreach ($associations as $id)
			// {
				// $query->values(((int) $id) . ',' . $db->quote($this->associationsContext) . ',' . $db->quote($key));
			// }

			// $db->setQuery($query);
			// $db->execute();
		// }
	// }
// }


//function updateArticleTitle ( $articleId, $lang, $title ) {
	
	// Check html and flag as error if not
	// if ( str) {
	// }
	
	// $db = JFactory::getDBO();
	
	// $query = $db->getQuery(true)
		// ->select($db->quoteName(array('C.id', 'C.title', 'C.alias', 'CA.title', 'C.modified'), array('article_id', 'title', 'alias', 'category', 'modified')))
		// ->from($db->quoteName('#__content', 'C'))
		// ->innerJoin($db->quoteName('#__categories', 'CA'))
		// ->where("C.catid = CA.id")
		// ->where("C.language = " . $db->quote('en-GB') )
		// ->order("C.modified DESC");

	// $db->setQuery($query);
	
	// //error_log("getUsername select query created: " . $query->dump());
	
	// $articles = $db->loadObjectList("article_id");
		
	
	// return $articles;
//}





// Get an instance of the controller prefixed by Biodiv
$controller = JControllerLegacy::getInstance('BioDiv');

// Perform the Request task
$controller->execute(JFactory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();
