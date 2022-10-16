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
include_once "BiodivHelper.php";

define('BIODIV_ADMIN_ROOT', JURI::base() . '?option=' . BIODIV_COMPONENT);
$document = JFactory::getDocument();
$document->addScriptDeclaration('
var BioDiv = {};
BioDiv.root = "'. BIODIV_ADMIN_ROOT . '";');

JHtml::_('jquery.framework');


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



// Get an instance of the controller prefixed by Biodiv
$controller = JControllerLegacy::getInstance('BioDiv');

// Perform the Request task
$controller->execute(JFactory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();