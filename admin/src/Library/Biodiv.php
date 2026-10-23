<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_biodiv
 *
 */

namespace MammalWeb\Component\Biodiv\Administrator\Library;

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseFactory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Associations;
use Joomla\Utilities\ArrayHelper;
use Joomla\Database\ParameterType;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

use MammalWeb\Component\Biodiv\Site\Library\Core\BiodivHelper;
use MammalWeb\Component\Biodiv\Site\Library\Core\Report;
use MammalWeb\Component\Biodiv\Site\Library\Wrapper\AWS;
use MammalWeb\Component\Biodiv\Site\Library\Wrapper\Transifex;
use MammalWeb\Component\Biodiv\Site\Library\Core\Users;

require JPATH_COMPONENT_SITE . '/vendor/autoload.php';
use Deepl\Translator;

// // Needed to ensure pick up the correct component files
// set_include_path('' . JPATH_COMPONENT_SITE . ';' . JPATH_COMPONENT_SITE . '/src/Library;' . get_include_path());

// include_once "local.php";
// include_once "codes.php";
// include_once "biodiv.php";

require_once JPATH_SITE . '/components/com_biodiv/src/Library/local.php';
require_once JPATH_SITE . '/components/com_biodiv/src/Library/codes.php';
require_once JPATH_SITE . '/components/com_biodiv/src/Library/biodiv.php';




// include_once "BiodivHelper.php";
// include_once "BiodivReport.php";
// include_once "BiodivAWS.php";
// include_once "BiodivTransifex.php";
// include_once "Biodiv/Users.php";

// //require_once 'libraries/vendor/guzzlehttp/guzzle/src/Client.php';
// require 'libraries/vendor/autoload.php';

$document = Factory::getDocument();
$document->addScriptDeclaration('
var BioDiv = {};
BioDiv.root = "'. BIODIV_ADMIN_ROOT . '";');



//JHtml::_('jquery.framework');


// function db(){
  // static $db;
  // $options = dbOptions();
  // if(!$db){
    // $db = new mysqli($options['host'],
		     // $options['user'],
		     // $options['password'],
		     // $options['database']);
  // }
  // return $db;
// }


// $dbOptions = dbOptions();
// codes_parameters_addTable("StructureMeta", $dbOptions['database']);


class Biodiv {

	public static function printAdminMenu($currentPage) {
		
		if ( !$currentPage ) $currentPage = "HOME";
		$btnActiveClass = 'btn btn-primary';
		$btnInactiveClass = 'btn btn-primary';

		

		$toolbar = Toolbar::getInstance('toolbar');

		ToolbarHelper::title('MammalWeb Manager', 'stack');

        // Custom buttons linking to custom views
		
		if ( $currentPage == "PROJECT" ) {
			$btnClass = $btnActiveClass;
		}
		else {
			$btnClass = $btnInactiveClass;
		}
        $toolbar->linkButton('custom')
            ->text('View or edit project')
            ->icon('icon-edit') // Joomla 5 icon class
            ->url('index.php?option=com_biodiv&view=selectproject')
            ->buttonClass($btnClass);
		
        if ( $currentPage == "NEWPROJECT" ) {
			$btnClass = $btnActiveClass;
		}
		else {
			$btnClass = $btnInactiveClass;
		}
        $toolbar->linkButton('custom')
            ->text('New project')
            ->icon('icon-add') // Joomla 5 icon class
            ->url('index.php?option=com_biodiv&view=newproject')
            ->buttonClass($btnClass);
		
        $toolbar->linkButton('custom')
            ->text('Delete photos')
            ->icon('icon-delete') // Joomla 5 icon class
            ->url('index.php?option=com_biodiv&view=deletephotos')
            ->buttonClass('btn btn-primary');
		
        $toolbar->linkButton('custom')
            ->text('View or edit school')
            ->icon('icon-edit') // Joomla 5 icon class
            ->url('index.php?option=com_biodiv&view=selectschool')
            ->buttonClass('btn btn-primary');
		
        $toolbar->linkButton('custom')
            ->text('New school')
            ->icon('icon-add') // Joomla 5 icon class
            ->url('index.php?option=com_biodiv&view=newschool')
            ->buttonClass('btn btn-primary');
		
        $toolbar->linkButton('custom')
            ->text('Batch user creation')
            ->icon('icon-add') // Joomla 5 icon class
            ->url('index.php?option=com_biodiv&view=biodivs')
            ->buttonClass('btn btn-primary');
		
        $toolbar->linkButton('custom')
            ->text('Translations')
            ->icon('icon-language') // Joomla 5 icon class
            ->url('index.php?option=com_biodiv&view=translations')
            ->buttonClass('btn btn-primary');
			
		// if ( $currentPage == "HOME" ) {
			// print '<h2>Home</h2>';
		// }
		// else if ( $currentPage == "PROJECT" ) {
			// print '<h2>View or edit project</h2>';
		// }
		// else if ( $currentPage == "NEWPROJECT" ) {
			// print '<h2>New project</h2>';
		// }
		// else if ( $currentPage == "DELETEPHOTOS" ) {
			// print '<h2>Delete photos</h2>';
		// }
		// else if ( $currentPage == "SCHOOL" ) {
			// print '<h2>View or edit school</h2>';
		// }
		// else if ( $currentPage == "NEWSCHOOL" ) {
			// print '<h2>New school</h2>';
		// }
		// else if ( $currentPage == "USERS" ) {
			// print '<h2>Batch user creation</h2>';
		// }
		// else if ( $currentPage == "TRANSLATIONS" ) {
			// print '<h2>Translations</h2>';
		// }
		// else {
			// print '<h2>Biodiv admin component</h2>';
		// }


		
		// if ( !$currentPage ) $currentPage = "HOME";
		// $activeLi = '<li class="active">';
		
		
		// print '<div id="j-sidebar-container" class="j-sidebar-container j-sidebar-visible">';
			
		// print '<div id="j-toggle-sidebar-wrapper">';
		// print '	<div id="j-toggle-button-wrapper" class="j-toggle-button-wrapper j-toggle-visible">';
		// print '		<div id="j-toggle-sidebar-button" class="j-toggle-sidebar-button hidden-phone hasTooltip" onclick="toggleSidebar(false); return false;" data-original-title="Hide the sidebar">';
		// print '		<span id="j-toggle-sidebar-icon" class="icon-arrow-left-2 j-toggle-visible" aria-hidden="true"></span>';
		// print '</div>';
		// print '	</div>';
		// print '	<div id="sidebar" class="sidebar">';
		// print ' <div class="sidebar-nav">';
		// print '	<ul id="submenu" class="nav nav-list">';
		// if ( $currentPage == "HOME" ) {
			// print $activeLi;
		// }
		// else {
			// print '<li>';
		// }
		// print '<a href="index.php?option=com_biodiv">MammalWeb admin home</a>';
		// print '</li>';
		// if ( $currentPage == "PROJECT" ) {
			// print $activeLi;
		// }
		// else {
			// print '<li>';
		// }
		// print '<a href="index.php?option=com_biodiv&amp;view=selectproject">View or edit project</a>';
		// print '</li>';
		// if ( $currentPage == "NEWPROJECT" ) {
			// print $activeLi;
		// }
		// else {
			// print '<li>';
		// }
		// print '<a href="index.php?option=com_biodiv&amp;view=newproject">New project</a>';
		// print '</li>';
		// if ( $currentPage == "DELETEPHOTOS" ) {
			// print $activeLi;
		// }
		// else {
			// print '<li>';
		// }
		// print '<a href="index.php?option=com_biodiv&amp;view=deletephotos">Delete photos</a>';
		// print '</li>';
		// if ( $currentPage == "SCHOOL" ) {
			// print $activeLi;
		// }
		// else {
			// print '<li>';
		// }
		// print '<a href="index.php?option=com_biodiv&amp;view=selectschool">View or edit school</a>';
		// print '</li>';
		// if ( $currentPage == "NEWSCHOOL" ) {
			// print $activeLi;
		// }
		// else {
			// print '<li>';
		// }
		// print '<a href="index.php?option=com_biodiv&amp;view=newschool">New school</a>';
		// print '</li>';
		// if ( $currentPage == "USERS" ) {
			// print $activeLi;
		// }
		// else {
			// print '<li>';
		// }
		// print '<a href="index.php?option=com_biodiv&amp;view=biodivs">Batch user creation</a>';
		// print '</li>';
		// if ( $currentPage == "TRANSLATIONS" ) {
			// print $activeLi;
		// }
		// else {
			// print '<li>';
		// }
		// print '<a href="index.php?option=com_biodiv&amp;view=translations">Translations</a>';
		// print '</li>';
		// print '</ul>';
		// print '</div>';
		// print '</div>';
		// print '<div id="j-toggle-sidebar"></div>';
		// print '</div>';
		// print '</div>';	

	}


	public static function printProjectForm ( $projectId = null ) {
		
		$accessLevels = self::getAccessLevels();
										
		$projects = self::getAllProjects();
		
		$schools = self::getAllSchools();
			
		$priorities = self::getPriorities();
							
		$fullyClassed = self::getFullyClassed();
							
		$regions = self::getRegions();
							
		$speciesLists = self::getSpeciesLists();
		
		$displayOptions = self::getProjectDisplayOptions();
		
		$projectAdmins = self::getAllProjectAdmins();
		
		$currProject = null;
		if ( $projectId ) {
			$currProject = self::getProject ( $projectId );
			
			print '<input type="hidden" name="id" value="'.$projectId.'"/>';
			
		}
		
		
		print '<div id="projectForm1" class="projectForm">';
		
		print '<h3>Describe the project</h3>';
		print '<div class="form-group">';
		print '<label for="projectName">Short project name:</label>';
		if ( $currProject ) {
			print '  <input class="form-control" type="text" id="projectName" name="projectName" value="'.$currProject->project_name.'">';
		}
		else {
			print '  <input class="form-control" type="text" id="projectName" name="projectName" required>';
		}
		print '</div>';

		print '<div class="form-group">';
		print '<label for="prettyName">Project display name (pretty name):</label>';
		if ( $currProject ) {
			print '  <input class="form-control" type="text" id="prettyName" name="prettyName" value="'.$currProject->project_prettyname.'">';
		}
		else {
			print '  <input class="form-control" type="text" id="prettyName" name="prettyName" required>';
		}
		print '</div>';

		print '<div class="form-group">';
		print '<label for="projectDescription">Project description:</label>';
		if ( $currProject ) {
			print '  <input class="form-control" type="text" class="input-xxlarge" id="projectDescription" name="projectDescription" value="'.$currProject->project_description.'">';
		}
		else {
			print '  <input class="form-control" type="text" class="input-xxlarge" id="projectDescription" name="projectDescription">';
		}
		print '</div>';
		
		print '<div class="form-group">';
		print '<button id="next_projectForm2" type="button" class="btn btn-primary js-stools-btn-clear projectNextBack" title="Next" data-original-title="Next">Next</button>';
		print '</div>';

		print '</div>';
		
		
		print '<div id="projectForm2" class="projectForm" style="display:none">';

		print '<h3>Set a project image and article</h3>';

		print '<div class="form-group">';
		print '<label for="imageDir">Project image folder (usually images/projects):</label>';
		if ( $currProject ) {
			print '  <input class="form-control" type="text" id="imageDir" name="imageDir" value="'.$currProject->dirname.'">';
		}
		else {
			print '  <input class="form-control" type="text" id="imageDir" name="imageDir" value="images/projects">';
		}
		print '</div>';

		print '<div class="form-group">';
		print '<label for="imageFile">Project image filename:</label>';
		if ( $currProject ) {
			print '  <input class="form-control" type="text" id="imageFile" name="imageFile" value="'.$currProject->image_file.'">';
		}
		else {
			print '  <input class="form-control" type="text" id="imageFile" name="imageFile">';
		}
		print '</div>';

		print '<div class="form-group">';
		print '<label for="articleId">Article id (if you have created a Joomla article for the project, input the id here - you can add it later if not):</label>';
		if ( $currProject ) {
			print '  <input class="form-control" type="number" id="articleId" name="articleId"  value="'.$currProject->article_id.'">';
		}
		else {
			print '  <input class="form-control" type="number" id="articleId" name="articleId" value="0">';
		}
		print '</div>';
		
		print '<div class="form-group">';
		print '<button id="back_projectForm1" type="button" class="btn btn-outline-primary projectNextBack" title="Back" data-original-title="Back">Back</button>';
		print '<button id="next_projectForm3" type="button" class="btn btn-primary js-stools-btn-clear projectNextBack" title="Next" data-original-title="Next">Next</button>';
		print '</div>';
		
		print '</div>';
		
		
		print '<div id="projectForm3" class="projectForm" style="display:none">';

		print '<h3>Project hierarchy</h3>';

		print '<div class="form-group">';
		print '<label for="parentProject">Choose a parent project (optional):</label>';
		print '<select class="form-control" id="parentProject" name="parentProject">';
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
		print '</select>';
		print '</div>';
		
		print '<div class="form-group">';
		print '<button id="back_projectForm2" type="button" class="btn btn-outline-primary projectNextBack" title="Back" data-original-title="Back">Back</button>';
		print '<button id="next_projectForm4" type="button" class="btn btn-primary js-stools-btn-clear projectNextBack" title="Next" data-original-title="Next">Next</button>';
		print '</div>';
		
		
		print '</div>';
		
		
		print '<div id="projectForm4" class="projectForm" style="display:none">';
		
		print '<h3>What region does the project belong to? </h3>';

		print '<div class="form-group">';
		print '<label for="region">Choose a region:</label>';
		if ( $currProject ) {
			print '<select class="form-control" class="input-xxlarge" id="region" name="region">';
			foreach ( $regions as $id=>$title ) {
				if ( $id == $currProject->region_id ) {
					print '<option value="'.$id.'" selected>'.$title.'</option>';
				}
				else if ( ($currProject->region_id == NULL) and $title == 'UK' ) {
					print '<option value="'.$id.'" selected>'.$title.'</option>';
				}
				else {
					print '<option value="'.$id.'">'.$title.'</option>';
				}
			}
			print '</select>';
		}
		else {
			print '<select class="form-control" class="input-xxlarge" id="region" name="region">';
			foreach ( $regions as $id=>$title ) {
				if ( $title == 'UK' ) {
					print '<option value="'.$id.'" selected>'.$title.'</option>';
				}
				else {
					print '<option value="'.$id.'">'.$title.'</option>';
				}
			}
			print '</select>';
		}
		print '</div>';
		
		print '<div class="form-group">';
		print '<button id="back_projectForm3" type="button" class="btn btn-outline-primary projectNextBack" title="Back" data-original-title="Back">Back</button>';
		print '<button id="next_projectForm5" type="button" class="btn btn-primary js-stools-btn-clear projectNextBack" title="Next" data-original-title="Next">Next</button>';
		print '</div>';
		
		print '</div>';
		
		
		print '<div id="projectForm5" class="projectForm" style="display:none">';
		
		print '<h3>Who can access the project for spotting and trapping?</h3>';

		print '<div class="form-group">';
		print '<label for="accessLevel">Choose an access level:</label>';
		if ( $currProject ) {
			print '<select class="form-control" class="input-xxlarge" id="accessLevel" name="accessLevel">';
			foreach ( $accessLevels as $id=>$title ) {
				if ( $id == $currProject->access_level ) {
					print '<option value="'.$id.'" selected>'.$title.'</option>';
				}
				else {
					print '<option value="'.$id.'">'.$title.'</option>';
				}
			}
			print '</select>';
		}
		else {
			print '<select class="form-control" class="input-xxlarge" id="accessLevel" name="accessLevel">';
			foreach ( $accessLevels as $id=>$title ) {
				print '<option value="'.$id.'">'.$title.'</option>';
			}
			print '</select>';
		}
		print '</div>';
		
		print '<div class="form-group">';
		print '<button id="back_projectForm4" type="button" class="btn btn-outline-primary projectNextBack" title="Back" data-original-title="Back">Back</button>';
		print '<button id="next_projectForm6" type="button" class="btn btn-primary js-stools-btn-clear projectNextBack" title="Next" data-original-title="Next">Next</button>';
		print '</div>';
		
		print '</div>';
		
		
		print '<div id="projectForm6" class="projectForm" style="display:none">';
		
		print '<h3>How many classifications should there be to consider a sequence fully classified? </h3>';

		print '<div class="form-group">';
		print '<label for="fullyClassed">Choose a number (or never):</label>';
		if ( $currProject ) {
			print '<select class="form-control" class="input-xxlarge" id="fullyClassed" name="fullyClassed">';
			foreach ( $fullyClassed as $id=>$title ) {
				if ( $id == $currProject->fc_id ) {
					print '<option value="'.$id.'" selected>'.$title.'</option>';
				}
				// else if ( ($currProject->fc_id == NULL) and $title == '3' ) {
					// print '<option value="'.$id.'" selected>'.$title.'</option>';
				// }
				else {
					print '<option value="'.$id.'">'.$title.'</option>';
				}
			}
			if ($currProject->fc_id == NULL) {
				print '<option value="0" selected>Never</option>';
			}
			else {
				print '<option value="0" >Never</option>';
			}
			print '</select>';
		}
		else {
			print '<select class="form-control" class="input-xxlarge" id="fullyClassed" name="fullyClassed">';
			foreach ( $fullyClassed as $id=>$title ) {
				if ( $title == '3' ) {
					print '<option value="'.$id.'" selected>'.$title.'</option>';
				}
				else {
					print '<option value="'.$id.'">'.$title.'</option>';
				}
			}
			print '<option value="0" >Never</option>';
			print '</select>';
		}
		print '</div>';
		
		print '<div class="form-group">';
		print '<button id="back_projectForm5" type="button" class="btn btn-outline-primary projectNextBack" title="Back" data-original-title="Back">Back</button>';
		print '<button id="next_projectForm7" type="button" class="btn btn-primary js-stools-btn-clear projectNextBack" title="Next" data-original-title="Next">Next</button>';
		print '</div>';
		
		print '</div>';
		
		
		print '<div id="projectForm7" class="projectForm" style="display:none">';
		
		print '<h3>How should the project be displayed on MammalWeb?</h3>';
		
		print '<div class="form-group">';
		print '<label for="listingLevel">Listing level (determines ordering on Projects page - normally between 1 and 10,000):</label>';
		if ( $currProject ) {
			print '  <input class="form-control" type="number" id="listingLevel" name="listingLevel" min="1" max="10000" value="'.$currProject->listing_level.'">';
		}
		else {
			print '  <input class="form-control" type="number" id="listingLevel" name="listingLevel" min="1" max="10000" value="100">';
		}
		print '</div>';

		print '<div class="form-group">';
		print '<label for="displayOptions">What display options should the project have? These are used to construct the project page.</label>';	
		if ( $currProject ) {
			$currDisplayOptions = $currProject->displayOptions;
			foreach ( $displayOptions as $id=>$title ) {
				
				print '<div>';
				if ( array_key_exists($id, $currDisplayOptions) ) {
					print '<input class="form-check-input" type="checkbox" id="displayOption_'.$id.'" name="displayOptions[]" value="'.$id.'"  style="margin-bottom:7px;" checked> ';
				}
				else {
					print '<input class="form-check-input" type="checkbox" id="displayOption_'.$id.'" name="displayOptions[]" value="'.$id.'"  style="margin-bottom:7px;"> ';
				}
				print '<label class="form-check-label for="displayOption_'.$id.'"  style="display:inline-block;">'.$title.'</label>';
				print '</div>';
				
			}
		}
		else {
			foreach ( $displayOptions as $id=>$title ) {
				
				print '<div>';
				print '<input class="form-check-input" type="checkbox" id="displayOption_'.$id.'" name="displayOptions[]" value="'.$id.'"  style="margin-bottom:7px;"> ';
				print '<label class="form-check-label for="displayOption_'.$id.'"  style="display:inline-block;">'.$title.'</label>';
				print '</div>';
				
			}
		}
		print '</div>';
		
		print '<div class="form-group">';
		print '<button id="back_projectForm6" type="button" class="btn btn-outline-primary projectNextBack" title="Back" data-original-title="Back">Back</button>';
		print '<button id="next_projectForm8" type="button" class="btn btn-primary projectNextBack" title="Next" data-original-title="Next">Next</button>';
		print '</div>';

		print '</div>';
		
		
		print '<div id="projectForm8" class="projectForm" style="display:none">';
		
		print '<h3>How should the project be classified?</h3>';

		print '<div class="form-group">';
		print '<label for="priority">What classifying priority should the project have?</label>';
		print '<select class="form-control" id="priority" name="priority">';
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
		print '</select>';
		print '</div>';


		print '<div class="form-group">';
		print '<label for="speciesLists">What species lists should the project have?  These are used to choose species when classifying.</label>';	
		if ( $currProject ) {
			$currSpeciesLists = $currProject->speciesLists;
			foreach ( $speciesLists as $id=>$title ) {
				
				print '<div>';
				if ( array_key_exists($id, $currSpeciesLists) ) {
					print '<input class="form-check-input" type="checkbox" id="speciesList_'.$id.'" name="speciesLists[]" value="'.$id.'"  style="margin-bottom:7px;" checked> ';
				}
				else {
					print '<input class="form-check-input" type="checkbox" id="speciesList_'.$id.'" name="speciesLists[]" value="'.$id.'"  style="margin-bottom:7px;"> ';
				}
				print '<label class="form-check-label for="speciesList_'.$id.'"  style="display:inline-block;">'.$title.'</label>';
				print '</div>';
				
			}
		}
		else {
			foreach ( $speciesLists as $id=>$title ) {
			
				print '<div>';
				print '<input class="form-check-input" type="checkbox" id="speciesList_'.$id.'" name="speciesLists[]" value="'.$id.'"  style="margin-bottom:7px;"> ';
				print '<label class="form-check-label for="speciesList_'.$id.'"  style="display:inline-block">'.$title.'</label>';
				print '</div>';
				
			}
		}
		
		print '</div>';
		
		print '<div class="form-group">';
		print '<button id="back_projectForm7" type="button" class="btn btn-outline-primary projectNextBack" data-original-title="Back">Back</button>';
		print '<button id="next_projectForm9" type="button" class="btn btn-primary projectNextBack" title="Next" data-original-title="Next">Next</button>';
		print '</div>';

		print '</div>';
		
		
		print '<div id="projectForm9" class="projectForm" style="display:none">';
		
		print '<h3>Set project admin user(s)</h3>';

		print '<div class="form-group">';
		print '<label for="projectAdmins">Choose who should be project administrators for this project (users with Project Admin permissions in Joomla)</label>';	
		if ( $currProject ) {
			$currProjectAdmins = $currProject->projectAdmins;
			foreach ( $projectAdmins as $id=>$user ) {
				
				print '<div>';
				if ( array_key_exists($id, $currProjectAdmins) ) {
					print '<input class="form-check-input" type="checkbox" id="projectAdmin_'.$id.'" name="projectAdmins[]" value="'.$id.'"  style="margin-bottom:7px;" checked> ';
				}
				else {
					print '<input class="form-check-input" type="checkbox" id="projectAdmin_'.$id.'" name="projectAdmins[]" value="'.$id.'"  style="margin-bottom:7px;"> ';
				}
				print '<label class="form-check-label for="projectAdmin_'.$id.'"  style="display:inline-block;">'.$user->username.' ('. $user->name . ', ' . $user->email . ')</label>';
				print '</div>';
				
			}
		}
		else {
			foreach ( $projectAdmins as $id=>$user ) {
			
				print '<div>';
				print '<input class="form-check-input" type="checkbox" id="projectAdmin_'.$id.'" name="projectAdmins[]" value="'.$id.'"  style="margin-bottom:7px;"> ';
				print '<label class="form-check-label for="projectAdmin_'.$id.'"  style="display:inline-block">'.$user->username.' ('. $user->name . ', ' . $user->email . ')</label>';
				print '</div>';
				
			}
		}
		
		print '</div>';
		
		print '<div class="form-group">';
		print '<button id="back_projectForm8" type="button" class="btn btn-outline-primary projectNextBack" data-original-title="Back">Back</button>';
		print '<button id="next_projectForm10" type="button" class="btn btn-primary projectNextBack showProjectSave" title="Next" data-original-title="Next">Next</button>';
		print '</div>';

		print '</div>';
		
		
		
		print '<div id="projectForm10" class="projectForm" style="display:none">';
		
		
		print '<div class="form-group">';
		print '<h3>Is this project a BES Schools project?</h3>';
		
		$existingSchool = false;
		if ( $currProject ) {
			if ( $currProject->school_id ) {
				$existingSchool = true;
				print '<div class="form-check">';
				print '<input class="form-check-input" type="radio" id="schoolProjectNo" name="isSchoolProject" value="0"  style="margin-bottom:7px;"> ';
				print '<label class="form-check-label for="schoolProjectNo"  style="display:inline-block;">No</label>';
				print '</div>';
				print '<div class="form-check">';
				print '<input class="form-check-input" type="radio" id="schoolProjectExists" name="isSchoolProject" value="1"  style="margin-bottom:7px;" checked> ';
				print '<label class="form-check-label for="schoolProjectExists"  style="display:inline-block;">Yes, school exists in database</label>';
				print '</div>';
				print '<div class="form-check">';
				print '<input class="form-check-input" type="radio" id="schoolProjectCreate" name="isSchoolProject" value="2"  style="margin-bottom:7px;"> ';
				print '<label class="form-check-label for="schoolProjectCreate"  style="display:inline-block;">Yes, create new school in database</label>';
				print '</div>';
			}
			else {
				print '<div class="form-check">';
				print '<input class="form-check-input" type="radio" id="schoolProjectNo" name="isSchoolProject" value="0"  style="margin-bottom:7px;" checked> ';
				print '<label class="form-check-label for="schoolProjectNo"  style="display:inline-block;">No</label>';
				print '</div>';
				print '<div class="form-check">';
				print '<input class="form-check-input" type="radio" id="schoolProjectExists" name="isSchoolProject" value="1"  style="margin-bottom:7px;"> ';
				print '<label class="form-check-label for="schoolProjectExists"  style="display:inline-block;">Yes, school exists in database</label>';
				print '</div>';
				print '<div class="form-check">';
				print '<input class="form-check-input" type="radio" id="schoolProjectCreate" name="isSchoolProject" value="2"  style="margin-bottom:7px;"> ';
				print '<label class="form-check-label for="schoolProjectCreate"  style="display:inline-block;">Yes, create new school in database</label>';
				print '</div>';
			}
		}
		else {
			print '<div class="form-check">';
			print '<input class="form-check-input" type="radio" id="schoolProjectNo" name="isSchoolProject" value="0"  style="margin-bottom:7px;" checked> ';
			print '<label class="form-check-label for="schoolProjectNo"  style="display:inline-block;">No</label>';
			print '</div>';
			print '<div class="form-check">';
			print '<input class="form-check-input" type="radio" id="schoolProjectExists" name="isSchoolProject" value="1"  style="margin-bottom:7px;"> ';
			print '<label class="form-check-label for="schoolProjectExists"  style="display:inline-block;">Yes, school exists in database</label>';
			print '</div>';
			print '<div class="form-check">';
			print '<input class="form-check-input" type="radio" id="schoolProjectCreate" name="isSchoolProject" value="2"  style="margin-bottom:7px;"> ';
			print '<label class="form-check-label for="schoolProjectCreate"  style="display:inline-block;">Yes, create new school in database</label>';
			print '</div>';
		}
		
		print '</div>';
		
		print '<div class="form-group">';
		print '<div id="schoolCreate" style="display:none" >';
		
		print '  <label for="newSchoolName">New school name:</label>';
		print '  <input class="form-control" class="input-xlarge" type="text" id="newSchoolName" name="newSchoolName">';
		
		print '</div>';
		print '</div>';
		
		if ( $existingSchool ) {
			print '<div id="schoolExists" >';
		}
		else {
			print '<div id="schoolExists" style="display:none">';
		}
		
		print '<div class="form-group">';
		print '<label for="school">School name:</label>';
		if ( $currProject ) {
			print '<select class="form-control" id="school" name="school">';
			print '<option value="0" selected>No school</option>';
			foreach ( $schools as $id=>$school ) {
				if ( $id == $currProject->school_id ) {
					print '<option value="'.$id.'" selected>'.$school->name.'</option>';
				}
				else {
					print '<option value="'.$id.'">'.$school->name.'</option>';
				}
			}
			print '</select>';
		}
		else {
			print '<select class="form-control" id="school" name="school">';
			foreach ( $schools as $id=>$school ) {
				print '<option value="'.$id.'">'.$school->name.'</option>';
			}
			print '</select>';
		}
		
		print '</div>';
		
		
		print '<div id="projectMsg"></div>';

		
		print '</div>';
		
		print '<div class="form-group">';
		print '<button id="back_projectForm9" type="button" class="btn btn-outline-primary projectNextBack hideProjectSave" data-original-title="Back">Back</button>';
		print '</div>';

		print '</div>';
		

	}


	public static function getAllProjects () {
		
		$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());


		$query = $db->getQuery(true)
			->select("P.* from Project P")
			->order("project_prettyname");
			
		$db->setQuery($query);
		
		//error_log("getAllProjects select query created: " . $query->dump());
		
		$projects = $db->loadObjectList("project_id");
		
		return $projects;
			
	}


	public static function getProject ( $projectId ) {
		
		$options = dbOptions();
		$db = (new DatabaseFactory)->getDriver('mysqli', $options);


		$query = $db->getQuery(true)
			->select("P.*, O.option_id as priority_id, O.option_name as priority, R1.fc_id, R1.fully_classed, R.region_id, R.region, P2.project_prettyname as parent_name, S.school_id, S.name as school_name from Project P")
			->innerJoin("ProjectOptions PO on PO.project_id = P.project_id")
			->innerJoin("Options O on O.option_id = PO.option_id and O.struc = 'priority'")
			->leftJoin("(select PO2.project_id as project_id, O2.option_id as region_id, O2.option_name as region from ProjectOptions PO2 " .
						"inner join Options O2 on O2.option_id = PO2.option_id and O2.struc = 'region') R on R.project_id = P.project_id")
			->leftJoin("(select PO3.project_id as project_id, O3.option_id as fc_id, O3.seq as fully_classed from ProjectOptions PO3 " .
						"inner join Options O3 on O3.option_id = PO3.option_id and O3.struc = 'numtoretire') R1 on R1.project_id = P.project_id")
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


	public static function getAllProjectAdmins () {
		
		$joomlaDb = Factory::getDbo();
							
		$query = $joomlaDb->getQuery(true);
		$query->select("U.id, U.name, U.username, U.email from #__users U")
			->innerJoin("#__usergroups UG on UG.title = " . $joomlaDb->quote("Project Admin") )
			->innerJoin("#__user_usergroup_map UM on UM.user_id = U.id and UM.group_id = UG.id");
			
		$joomlaDb->setQuery($query);
		
		//error_log("getAllProjectAdmins select query created: " . $query->dump());
		
		$users = $joomlaDb->loadObjectList("id");
		
		return $users;
		
	}


	public static function createProject ($projectName,
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
									) {
										
	$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
	
	// Create project
	$fields = new \StdClass();
	$fields->project_name = $projectName;
	$fields->project_prettyname = $prettyName;
	$fields->project_description = $projectDescription;
	$fields->access_level = $accessLevel;
	if ( $parentProject ) {
		$fields->parent_project_id = $parentProject;
	}
	$fields->dirname = $imageDir;
	$fields->image_file = $imageFile;
	$fields->article_id = $articleId;
	$fields->listing_level = $listingLevel;
	
	$success = $db->insertObject("Project", $fields, 'project_id');
	if(!$success){
		error_log ( "Project insert failed" );
	}	
	
	$projectId = $fields->project_id;
	
	if ( $success ) {
		
		// Set classification priority
		$priorityFields = new \StdClass();
		$priorityFields->project_id = $projectId;
		$priorityFields->option_id = $priority;
		
		$success = $db->insertObject("ProjectOptions", $priorityFields);
		if(!$success){
			error_log ( "ProjectOptions insert classification priority failed" );
		}
		
		// Set fully classed
		if ( $fullyClassed ) {
			$fcFields = new \StdClass();
			$fcFields->project_id = $projectId;
			$fcFields->option_id = $fullyClassed;
			
			$success = $db->insertObject("ProjectOptions", $fcFields);
			if(!$success){
				error_log ( "ProjectOptions insert fully classified number failed" );
			}
		}
		
		// Set region
		$regionFields = new \StdClass();
		$regionFields->project_id = $projectId;
		$regionFields->option_id = $region;
		
		$success = $db->insertObject("ProjectOptions", $regionFields);
		if(!$success){
			error_log ( "ProjectOptions insert classification region failed" );
		}
		
		// Set displayOptions
		foreach ( $displayOptions as $displayId ) {
			$displayFields = new \StdClass();
			$displayFields->project_id = $projectId;
			$displayFields->option_id = $displayId;
			
			$success = $db->insertObject("ProjectOptions", $displayFields);
			if(!$success){
				error_log ( "ProjectOptions display option failed" );
			}
		}
		
		// Set displayOptions
		foreach ( $speciesLists as $listId ) {
			$speciesFields = new \StdClass();
			$speciesFields->project_id = $projectId;
			$speciesFields->option_id = $listId;
			
			$success = $db->insertObject("ProjectOptions", $speciesFields);
			if(!$success){
				error_log ( "ProjectOptions species list option failed" );
			}
		}
		
		// Set project admins
		foreach ( $projectAdmins as $projectAdmin ) {
			$adminFields = new \StdClass();
			$adminFields->project_id = $projectId;
			$adminFields->person_id = $projectAdmin;
			$adminFields->role_id = 1;
			
			$success = $db->insertObject("ProjectUserMap", $adminFields);
			if(!$success){
				error_log ( "ProjectUserMap species list option failed" );
			}
		}
		
		if ( $isSchoolProject == 1 ) {
			
			$schoolFields = new \StdClass();
			
			if ( $existingSchoolId > 0 ) {
				$schoolFields->school_id = $existingSchoolId;
				$schoolFields->project_id = $projectId;
			
				$success = $db->updateObject("School", $schoolFields, 'school_id');
				if(!$success){
					error_log ( "School project update failed" );
				}
			}
		}
		else if ( $isSchoolProject == 2 ) {
			
			$schoolFields = new \StdClass();
			
			if ( strlen ( $newSchoolName ) > 0 ) {
				$schoolFields->name = $newSchoolName;
				$schoolFields->project_id = $projectId;
				$schoolFields->image = $imageDir . '/' . $imageFile;
			
				$success = $db->insertObject("School", $schoolFields, 'school_id');
				if(!$success){
					error_log ( "School project insert failed" );
				}
			}
		}
	}
	
	return $projectId;
	
}

	public static function editProject ($projectId,
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
									) {
										
		$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());

		// Create project
		$fields = new \StdClass();
		$fields->project_id = $projectId;
		$fields->project_name = $projectName;
		$fields->project_prettyname = $prettyName;
		$fields->project_description = $projectDescription;
		$fields->access_level = $accessLevel;
		if ( $parentProject ) {
			$fields->parent_project_id = $parentProject;
		}
		$fields->dirname = $imageDir;
		$fields->image_file = $imageFile;
		$fields->article_id = $articleId;
		$fields->listing_level = $listingLevel;
		
		$success = $db->updateObject("Project", $fields, 'project_id');
		if(!$success){
			error_log ( "Project update failed" );
		}	
		
		if ( $success ) {
			
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('ProjectOptions'))
				->where("project_id = " . $projectId );
			$db->setQuery($query);
			$result = $db->execute();
			
			
			// Set classification priority
			$priorityFields = new \StdClass();
			$priorityFields->project_id = $projectId;
			$priorityFields->option_id = $priority;
			
			$success = $db->insertObject("ProjectOptions", $priorityFields);
			if(!$success){
				error_log ( "ProjectOptions insert classification priority failed" );
			}
			
			// Set fully classed
			if ( $fullyClassed ) {
				$fcFields = new \StdClass();
				$fcFields->project_id = $projectId;
				$fcFields->option_id = $fullyClassed;
				
				$success = $db->insertObject("ProjectOptions", $fcFields);
				if(!$success){
					error_log ( "ProjectOptions insert fully classified number failed" );
				}
			}
		
			// Set region
			$regionFields = new \StdClass();
			$regionFields->project_id = $projectId;
			$regionFields->option_id = $region;
			
			$success = $db->insertObject("ProjectOptions", $regionFields);
			if(!$success){
				error_log ( "ProjectOptions insert classification region failed" );
			}
		
			// Set displayOptions
			foreach ( $displayOptions as $displayId ) {
				$displayFields = new \StdClass();
				$displayFields->project_id = $projectId;
				$displayFields->option_id = $displayId;
				
				$success = $db->insertObject("ProjectOptions", $displayFields);
				if(!$success){
					error_log ( "ProjectOptions display option failed" );
				}
			}
			
			// Set displayOptions
			foreach ( $speciesLists as $listId ) {
				$speciesFields = new \StdClass();
				$speciesFields->project_id = $projectId;
				$speciesFields->option_id = $listId;
				
				$success = $db->insertObject("ProjectOptions", $speciesFields);
				if(!$success){
					error_log ( "ProjectOptions species list option failed" );
				}
			}
			
			// Set project admins
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('ProjectUserMap'))
				->where("project_id = " . $projectId )
				->where("role_id = 1");
			$db->setQuery($query);
			$result = $db->execute();
			
			foreach ( $projectAdmins as $projectAdmin ) {
				$adminFields = new \StdClass();
				$adminFields->project_id = $projectId;
				$adminFields->person_id = $projectAdmin;
				$adminFields->role_id = 1;
				
				$success = $db->insertObject("ProjectUserMap", $adminFields);
				if(!$success){
					error_log ( "ProjectUserMap species list option failed" );
				}
			}
			
			if ( $isSchoolProject == 1 ) {
				
				$schoolFields = new \StdClass();
				
				if ( $existingSchoolId > 0 ) {
					$schoolFields->school_id = $existingSchoolId;
					$schoolFields->project_id = $projectId;
					
					$success = $db->updateObject("School", $schoolFields, 'school_id');
					if(!$success){
						error_log ( "School project update failed" );
					}
				}
			}
			else if ( $isSchoolProject == 2 ) {
				
				$schoolFields = new \StdClass();
				
				if ( strlen ( $newSchoolName ) > 0 ) {
					$schoolFields->name = $newSchoolName;
					$schoolFields->project_id = $projectId;
					$schoolFields->image = $imageDir . '/' . $imageFile;
				
					$success = $db->insertObject("School", $schoolFields, 'school_id');
					if(!$success){
						error_log ( "School project insert failed" );
					}
				}
			} 		
		}
	}

	public static function printSchoolForm ( $schoolId = null ) {
		
		$projects = self::getAllProjects();
							
		$currSchool = null;
		if ( $schoolId ) {
			$currSchool = self::getSchool ( $schoolId );
			
			print '<input type="hidden" name="id" value="'.$schoolId.'"/>';
			
		}
		
		print '<div id="schoolForm1" class="schoolForm">';
		
		
		print '<div class="form-group">';
		print '<label for="schoolName">School name:</label>';
		if ( $currSchool ) {
			print '  <input class="form-control" type="text" id="schoolName" name="schoolName" value="'.$currSchool->name.'" required>';
		}
		else {
			print '  <input class="form-control" type="text" id="schoolName" name="schoolName"  required>';
		}
		print '</div>';
		
		
		print '<div class="form-group">';
		print '<label for="image">School image file (including path eg images/projects/BES/schools/MyImage.png):</label>';
		if ( $currSchool ) {
			print '  <input class="form-control" class="input-xxlarge" type="text" id="image" name="image" value="'.$currSchool->image.'">';
		}
		else {
			print '  <input class="form-control" class="input-xxlarge" type="text" id="image" name="image" value="images/projects/BES/schools/MyImage.png">';
		}
		print '</div>';
		
		
		print '<div class="form-group">';
		print '<label for="projectId">Does the school have a MammalWeb project? (optional):</label>';
		print '<select class="form-control" id="projectId" name="projectId">';
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
		print '</select>';
		print '</div>';
		
		
		
		
		
		print '</div>';
		
		
	}
	
	
	public static function createSchool ( $schoolName, $image, $projectId ) {
		
		$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		
		// Create school
		$fields = new \StdClass();
		$fields->name = $schoolName;
		$fields->image = $image;
		$fields->project_id = $projectId;
		
		$success = $db->insertObject("School", $fields, 'school_id');
		if(!$success){
			error_log ( "School insert failed" );
		}	
		
		$schoolId = $fields->school_id;
		
		return $schoolId;
	}


	public static function getAllSchools () {
		
		$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());


		$query = $db->getQuery(true)
			->select("S.* from School S")
			->order("name");
			
		$db->setQuery($query);
		
		//error_log("getAllProjects select query created: " . $query->dump());
		
		$schools = $db->loadObjectList("school_id");
		
		return $schools;
			
	}



	public static function getSchool ( $schoolId ) {
		
		$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());

		$query = $db->getQuery(true)
			->select("*, P.project_prettyname as project from School S")
			->leftJoin("Project P on P.project_id = S.project_id")
			->where("school_id = " . $schoolId);
			
		$db->setQuery($query);
		
		$school = $db->loadObject();
		
		return $school;
			
	}
	
	
	public static function editSchool ( $schoolId, $schoolName, $image, $projectId ) {
		
		$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		
		// Update school
		$fields = new \StdClass();
		$fields->school_id = $schoolId;
		$fields->name = $schoolName;
		$fields->image = $image;
		$fields->project_id = $projectId;
		
		
		$success = $db->updateObject("School", $fields, 'school_id');
		if(!$success){
			error_log ( "School update failed" );
		}	
		
		return $success;
		
	}


	public static function getSchoolUsers ( $usergroup ) {
		
		$joomlaDb = Factory::getDbo();
							
		$query = $joomlaDb->getQuery(true);
		$query->select("U.id, U.name, U.username, U.email from #__users U")
			->innerJoin("#__usergroups UG on UG.title = " . $joomlaDb->quote($usergroup) )
			->innerJoin("#__user_usergroup_map UM on UM.user_id = U.id and UM.group_id = UG.id");
			
		$joomlaDb->setQuery($query);
		
		//error_log("getSchoolUsers select query created: " . $query->dump());
		
		$users = $joomlaDb->loadObjectList("id");
		
		return $users;
	}



	public static function printSchoolUserForm ( $schoolId, $usergroup ) {
		
		$users = self::getSchoolUsers($usergroup);
		
		print '<input type="hidden" name="schoolId" value="'.$schoolId.'"/>';

		$selectId = "add" . str_replace(' ', '', $usergroup);
							
		print '<div class="form-group">';
		print '<label for="'.$selectId.'">Add a '.$usergroup.' to this school:</label>';
		print '<select class="form-control" id="'.$selectId.'" name="userId" class="input-xxlarge">';
		foreach ( $users as $id=>$user ) {
			print '<option value="'.$id.'">'.$user->name.' ('.$user->email.')</option>';
		}	
		print '</select>';
		print '</div>';
		
	}



	private static function getOptions ( $struc ) {
		
		$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());

		$query = $db->getQuery(true)
			->select("option_id, option_name from Options")
			->where("struc = " . $db->quote($struc) )
			->order("option_name");
			
		$db->setQuery($query);
		
		//error_log("getAllProjects select query created: " . $query->dump());
		
		$options = $db->loadAssocList("option_id", "option_name");
		
		return $options;
			
	}

	private static function getPriorities() {
		
		return self::getOptions ( 'priority' );

	}

	private static function getFullyClassed() {
		
		return self::getOptions ( 'numtoretire' );

	}

	private static function getRegions() {
		
		return self::getOptions ( 'region' );

	}

	private static function getSpeciesLists () {
		
		return self::getOptions ( 'projectfilter' );
			
	}

	private static function getProjectDisplayOptions () {
		
		return self::getOptions ( 'projectdisplay' );
			
	}

	public static function getAccessLevels () {
		
		return array("Public (public spotting and trapping)", 
					"Hybrid (public spotting, restricted trappping)", 
					"Restricted (restricted spotting and trapping)", 
					"Private (restricted and not displayed on website)" );
			
	}


	public static function addSchoolUser ( $role, $personId, $schoolId ) {
		
		$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		
		$query = $db->getQuery(true)
			->select("role_id from Role where role_name = " . $db->quote($role) );
			
		$db->setQuery($query);
	
		$roleId = $db->loadResult();
		
		$userDetails = self::getUserDetails($personId);
		
		$success = false;
		
		if ( $roleId > 0 ) {
			
			try {
		
				// Create school user
				$fields = new \StdClass();
				$fields->person_id = $personId;
				$fields->role_id = $roleId;
				$fields->school_id = $schoolId;
				
				$success = $db->insertObject("SchoolUsers", $fields);
			
			}
			catch ( Exception $e ) {
				error_log ( "AddSchoolUser error inserting record " . $e->getMessage() );
				$success = false;
			}
			
		}
		
		if ( !$success ) {
			error_log ( "SchoolUsers insert failed" );
		}
		
		return $success;
	}


	public static function getUserDetails ( $userId ) {
		
		$user = Factory::getUser($userId);
		
		$userDetails = new \StdClass();
		$userDetails->username = $user->username;
		$userDetails->name = $user->name;
		$userDetails->email = $user->email;
		
		return $userDetails;

	}


	public static function getSchoolUserGroups () {
		
		$joomlaDb = Factory::getDbo();
        $joomlaDb->setQuery( 'SELECT id, title' .
                        ' FROM `#__usergroups`' .
						' WHERE title like "School%"' );
                        
        
        $userGroups = $joomlaDb->loadAssocList ("id", "title");
		
		return $userGroups;

	}


	public static function createReportFile ( $type, $headings, $rows ) {
		
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
		
		$url = Uri::root()."/biodivimages/reports/admin/".$filename;
		return $url;
	}


	public static function createReportFileTxt ( $type, $rows ) {
		
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
		
		$url = Uri::root()."/biodivimages/reports/admin/".$filename;
		return $url;
	}

	public static function getAllArticlesForTranslation ( $page, $length, $searchStr = null ) {
		
		$db = Factory::getDBO();
		
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


	public static function getArticle ( $articleId ) {
		
		$jarticle = Table::getInstance("content");

		$jarticle->load($articleId); 
		
		return $jarticle;
		
	}



	public static function getSupportedLanguages () {
		
		return Transifex::getSupportedLanguages();
	}


	// public static function getSupportedLanguage ( $tag ) {
		
		// $db = JDatabase::getInstance(dbOptions());
		
		// $query = $db->getQuery(true);
		// $query->select("*")
			// ->from("Language")
			// ->where("tag = " . $db->quote($tag) );
		// $db->setQuery($query);
		// $lang = $db->loadObject();
		
		// return $lang;
	// }


	public static function getSpeciesTranslationLists () {
		
		$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		
		$query = $db->getQuery(true)
				->select("*")
				->from("SpeciesTranslationLists");
		$db->setQuery($query);
		
		return $db->loadObjectList("id");
	}


	public static function getOptionsHeadings () {
		
		$languages = self::getSupportedLanguages();
		
		$headings = array("Key", "en-GB", "Context"); 
				
		foreach ($languages as $id=>$lang) {
			$headings[] = $lang->transifex_code;
		}
		
		return $headings;
	}


	private static function getNonSpeciesOptionsForTranslation () {
		
		$languages = self::getSupportedLanguages();
		
		$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
			
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


	public static function getAllAssociations ( $articleId ) {
		
		$associations = null;
		
		if ($articleId)
		{
			$associations = Associations::getAssociations('com_content', '#__content', 'com_content.item', $articleId);
			
		}
		
		return $associations;
	}


	// public static function getAssociations ( $articleId, $lang ) {
		
		// $assoc = null;
		
		// if ($articleId)
		// {
			// $associations = JLanguageAssociations::getAssociations('com_content', '#__content', 'com_content.item', $articleId);
			
			// if ( $associations ) {
				// if ( array_key_exists($lang, $associations) ) {
					// $assoc = $associations[$lang];
				// }
			// }
		// }
		
		// return $assoc;
	// }


	// public static function getAssociatedArticle ( $articleId, $lang ) {
		
		// $assocId = null;

		// if ($articleId != null)
		// {
			// $associations = JLanguageAssociations::getAssociations('com_content', '#__content', 'com_content.item', $articleId);
			
			// if ( $associations ) {
				
				// if ( array_key_exists($lang, $associations) ) {
					// //$assocId = $associations[$lang]->id;
					// $id = explode ( ':', $associations[$lang]->id );
					// $assocId = $id[0];
				// }
			// }
		// }
		
		// return $assocId;
	// }


	public static function getAssociatedCategory ( $catId, $lang ) {
		
		$assocId = null;
		
		if ($catId)
		{
			try {
				$associations = Associations::getAssociations('com_content', '#__categories', 'com_categories.item', $catId, 'id', 'alias', 'id');
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


	// public static function isJSON ( $string ) {
		
	   // return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
	   
	// }


	// moved to admin public static function getJSONDecodedArray ( $string ) {
		
		// $decoded = null;
		// if ( is_string ( $string ) ) {
			// $newArray = json_decode($string, true);
			// if ( is_array($newArray) && (json_last_error() == JSON_ERROR_NONE) ) {
				// $decoded = $newArray;
			// }
		// }
		
		// return $decoded;
	   
	// }


	// moved to admin public static function checkHtml ( $htmlStr ) {
		
		// $warning = null;
		
		// // Just a basic check
		// if ( strpos ( strtolower($htmlStr), "script") !== FALSE ) {
			// $warning = "Text may contain a script";
		// }
		
		// return $warning;
	// }


	public static function updateArticle ( $articleId, $lang, $title, $html ) {
		
		//error_log ( "updateArticle called" );
		
		$response = null;
		$assocId = null;
		$assoc = null;
		
		$allAssociations = self::getAllAssociations ( $articleId );
		
		$assocsForKey = array();
		foreach ( $allAssociations as $assocLang=>$assocObj ) {
			$id = explode ( ':', $assocObj->id );
			$assocsForKey[$assocLang] = (int)$id[0];
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
			
			// Note there if there is an article with the associated category we get an error on save.
			
			//error_log ( "No associated article so we will need to create a new one" );
			
			//$table = Table::getInstance('Content', 'JTable', array());

			$article = self::getArticle ( $articleId );
			
			// $errMsg = print_r ( $article, true );
			// error_log ( "Article: " . $errMsg );
			
			//$title = $article->title . " " . $lang; 
			$alias = $article->alias . "-" . strtolower($lang); 
		
			$catId = self::getAssociatedCategory ( $article->catid, $lang );
			
			
			//error_log ( "Got associated category: " . $catId );
			
			$article = array(
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
				// // Bind data
				// if (!$table->bind($data))
				// {
					// throw new \Exception("Failed to bind article data. Error: " . $table->getError());
				// }

				// //error_log ( "About to check" );
				
				// // Check the data.
				// if (!$table->check())
				// {
					// throw new \Exception("Failed to check article data. Error: " . $table->getError());
				// }

				// //error_log ( "About to store" );
				
				// // Store the data.
				// if (!$table->store())
				// {
					// //error_log ( "Store unsuccessful, error = " . $table->getError() );
					
					// throw new \Exception("Failed to store article data. Error: " . $table->getError());
				// }
				
				$app = Factory::getApplication();
				$mvcFactory = $app->bootComponent('com_content')->getMVCFactory();
				$articleModel = $mvcFactory->createModel('Article', 'Administrator', ['ignore_request' => true]);
				$table = $articleModel->getTable();

				if (!$articleModel->save($article)){
					throw new \Exception($articleModel->getError());
				}
				
				$response = "Article text created successfully for new associated article " . $articleId . ", language " . $lang;
				
				$newArticleId = (int) $articleModel->getState($articleModel->getName() . '.id');
				
				// // Create an association (example: link EN article to FR article)
				// $associations = Associations::getAssociations ( 'com_content', '#__content', 'com_content.item', $articleId );
															// // 'com_content', '#__content', 'com_content.item', $item->id
															// // $extension,
															// // $tablename,
															// // $context,
															// // $id,
				// if ( $associations ) {
					// // update with the new article for the language given
				// }
				// else {
					// $associations = [
						// $lang => $newArticleId,
						// 'en-GB' => $articleId, // Existing English article ID
					// ];
				// }

				// Associations::saveAssociations('com_content', 'article', $articleId, $associations);

				// echo "Article and association created successfully.";
	
	
				// Unused $assocTable = Table::getInstance('Associations', 'JTable', array());
				$associationsContext = 'com_content.item';
				
				$db = Factory::getDBO();

				// AdminModel method for associations
				if ($associationsContext && Associations::isEnabled()) {
					
					if ( !empty($allAssociations) ) {
					
						// Unset any invalid associations
						//$associations = ArrayHelper::toInteger($allAssociations);
						// $associations = array();
						// foreach ( $allAssociations as $language=>$details ) {
							// if ( property_exists( $details, 'id' ) ) {
								
								// $fullId = $details->id;
								// $arr = explode ( ":", $fullId );
								// $artclId = $arr[0];
								// $associations[$language] = $artclId;
							// }
						// }
						
						//$associations = $assocsForKey;

						// Unset any invalid associations
						foreach ($assocsForKey as $tag => $id) {
							if (!$id) {
								unset($assocsForKey[$tag]);
							}
						}

						// Get associationskey for edited item
						// $id = (int) $table->$key;
						$query = $db->getQuery(true)
							->select($db->quoteName('key'))
							->from($db->quoteName('#__associations'))
							->where($db->quoteName('context') . ' = :context')
							->where($db->quoteName('id') . ' = :id')
							->bind(':context', $associationsContext)
							->bind(':id', $articleId, ParameterType::INTEGER);
						$db->setQuery($query);
						$oldKey = $db->loadResult();

						if ($assocsForKey || $oldKey !== null) {
							// Deleting old associations for the associated items
							$query = $db->getQuery(true)
								->delete($db->quoteName('#__associations'))
								->where($db->quoteName('context') . ' = :context')
								->bind(':context', $associationsContext);

							$where = [];

							if ($assocsForKey) {
								$where[] = $db->quoteName('id') . ' IN (' . implode(',', $query->bindArray(array_values($assocsForKey))) . ')';
							}

							if ($oldKey !== null) {
								$where[] = $db->quoteName('key') . ' = :oldKey';
								$query->bind(':oldKey', $oldKey);
							}

							$query->extendWhere('AND', $where, 'OR');
							$db->setQuery($query);
							$db->execute ();
						}
					}

					// Adding self to the association
					// if ($table->language !== '*') {
						// $associations[$table->language] = (int) $table->$key;
					// }
					
					// if ( \count($assocsForKey) == 0 ) {
						// $assocsForKey['en-GB'] = (int) $articleId;
					// }
					$assocsForKey['en-GB'] = (int) $articleId;
					$assocsForKey[$lang] = (int) $newArticleId;

					if (\count($assocsForKey) > 1) {
						// Adding new association for these items
						$key   = md5(json_encode($assocsForKey));
						$query = $db->getQuery(true)
							->insert($db->quoteName('#__associations'))
							->columns(
								[
									$db->quoteName('id'),
									$db->quoteName('context'),
									$db->quoteName('key'),
								]
							);

						foreach ($assocsForKey as $lng=>$id) {
							$query->values(
								implode(
									',',
									$query->bindArray(
										[$id, $associationsContext, $key],
										[ParameterType::INTEGER, ParameterType::STRING, ParameterType::STRING]
									)
								)
							);
						}

						$db->setQuery($query);
						
						//error_log ( "Biodiv::updateArticle, add associations query: " . $query->dump() );

						$db->execute();
					}
				}
				
				
				
				// //error_log ( "About to handle associations" );
				
				// // There is no association for this language but is there for teh original article id
				// $db = Factory::getDBO();
				// $query = $db->getQuery(true)
					// ->select($db->qn('key'))
					// ->from($db->qn('#__associations'))
					// ->where($db->qn('context') . ' = ' . $db->quote($associationsContext))
					// ->where($db->qn('id') . ' = ' . (int) $articleId);
				// $db->setQuery($query);
				
				// //error_log("Associations old_key query created: " . $query->dump());
				
				// $old_key = $db->loadResult();
				
				// if ( $old_key ) {

					// // Deleting old associations for the associated items
					// $query = $db->getQuery(true)
						// ->delete($db->qn('#__associations'))
						// ->where($db->qn('context') . ' = ' . $db->quote($associationsContext));

					// if ($assocsForKey)
					// {
						// $query->where('(' . $db->qn('id') . ' IN (' . implode(',', $assocsForKey) . ') OR '
							// . $db->qn('key') . ' = ' . $db->q($old_key) . ')');
					// }
					// else
					// {
						// $query->where($db->qn('key') . ' = ' . $db->q($old_key));
					// }

					// $db->setQuery($query);
					
					// //error_log("Associations delete query created: " . $query->dump());
					
					// $db->execute();
				// }
				
				// //error_log ( "Table language = " . $table->language );
				// //error_log ( "Table id = " . $table->id );

				// // Adding original and self to the association
				
				// $assocsForKey['en-GB'] = (int) $articleId;
				// if ($table->language !== '*')
				// {
					// $assocsForKey[$table->language] = (int) $table->id;
				// }

				// if (count($assocsForKey) > 1)
				// {
					// $errMsg = print_r ( $assocsForKey, true );
					// //error_log ( "Associatons: " . $errMsg );
					
					// // Adding new association for these items
					// $key   = md5(json_encode($assocsForKey));
					
					// //error_log ( "New key = " . $key );
					
					// $query = $db->getQuery(true)
						// ->insert('#__associations');

					// foreach ($assocsForKey as $id)
					// {
						// $query->values(((int) $id) . ',' . $db->quote($associationsContext) . ',' . $db->quote($key));
					// }

					// $db->setQuery($query);
					
					// //error_log("Associations delete query created: " . $query->dump());
					
					// $db->execute();
				// }
				
			} catch ( Exception $e ) {
				
				$response = "Article text failed to create for new associated article " . $articleId . ", language " . $lang . ". Reason: " . $e->getMessage();
				
			}
			
		}
		else {
			
			$db = Factory::getDBO();
			
			$articleFields = new \StdClass();
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
	
	
	public static function generateSpeciesOptionsFile ( $listTranslationId ) {
		
		$languages = self::getSupportedLanguages();
		
		$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		
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
		
		$reportType = "Species";
		if ( $listTranslationId ) {
			
			$speciesTrans = self::getSpeciesTranslationLists();
			$listTran = $speciesTrans[$listTranslationId];
			$listSql = $listTran->list_sql;
			$reportType .= "_" . $listTran->list_name;
			
			$query->innerJoin("SpeciesList S on S.species_id = O.option_id")
				->where($listSql);
			
		}
			
		$query->where("struc in ( 'mammal', 'bird', 'invertebrate' )")
			->order("struc, option_name");

		$db->setQuery($query);
		
		//error_log("generateTranslateOptions select query created " . $query->dump());
		
		$speciesToTranslate = $db->loadObjectList("option_id");
		
		$headings = self::getOptionsHeadings();
		
		$rows = array();
		
		foreach ( $speciesToTranslate as $optionId=>$species ) {
			
			$scientificName = "Scientific name not available";
				
			if ( property_exists($species, "article_id") ) {
				
				$article = self::getArticle( $species->article_id );
						
				$introtext = $article->introtext;
				$title = $article->title;
				
				if ( $introtext ) {
					$dom = new \DomDocument();
					@ $dom->loadHTML(mb_convert_encoding($introtext, 'HTML-ENTITIES', 'UTF-8'));
								
					$ps = $dom->getElementsByTagName('p');
					foreach( $ps as $p ) {
						
						// //$text = (string)$p->textContent;
						// $text = (string)$p->nodeValue;
						
						// if($p->childNodes->length) {
							// foreach($p->childNodes as $i) {
								// $nodeValue = $i->nodeValue;
							// }
						// }
						
						if($p->childNodes->length > 2) {
							$node0 = $p->childNodes[0];
							$node1 = $p->childNodes[1];
							$node2 = $p->childNodes[2];
							if ( strpos ( $node0->nodeValue, 'Scientific Name' ) !== false ) {
								$scientificName = $node2->nodeValue;
								continue;
							}
							// foreach($p->childNodes as $i) {
								// $nodeValue = $i->nodeValue;
							// }
						}
						
						
						
						// $cleanText = strip_tags ($text);
						
						// $sciName = 'Scientific Name:';
						// //$testName = 'Scientific Name:';
						
						// $pos = strpos ( $cleanText, $sciName );
						
						// if ( $pos === false ) {
							// // No scientific name
							// $msg = 'not found';
						// }
						// else {
							// $scientificName = substr( $cleanText, strlen($sciName)+1 );
							// continue;
						// }
						
						
						// //test
						// $mystring = 'abc';
						// $findme   = 'a';
						// $pos = strpos($mystring, $findme);

						// // Note our use of ===.  Simply == would not work as expected
						// // because the position of 'a' was the 0th (first) character.
						// if ($pos === false) {
							// echo "The string '$findme' was not found in the string '$mystring'";
						// } else {
							// echo "The string '$findme' was found in the string '$mystring'";
							// echo " and exists at position $pos";
						// }
					}
				}	
			}
			
			$speciesArray = array($optionId, 
								$species->option_name, 
								$scientificName . ' (' . $species->struc . ')' );
								
			foreach ($languages as $id=>$lang) {
				$prop = strtolower($lang->transifex_code);
				$speciesArray[] = $species->$prop;
			}
								
			array_push($rows, $speciesArray);
		}
		
		$reportURL = self::createReportFile ( $reportType, $headings, $rows );
		
		return $reportURL;
	}
	
	
	public static function generateNonSpeciesOptionsFile () {
		
		$languages = self::getSupportedLanguages();
			
		$options = self::getNonSpeciesOptionsForTranslation ();
		
		$headings = self::getOptionsHeadings();
		
		$rows = array();
		
		foreach ( $options as $optionId=>$option ) {
			
			$optionsArray = array($optionId, 
								str_replace(array("\r\n", "\n\r", "\r", "\n"), ' ', str_replace(',', '-', $option->option_name)),
								$option->struc );
								
								
			foreach ($languages as $id=>$lang) {
				$col = strtolower($lang->transifex_code);
				if ( !$option->$col ) {
					$prop = "";
				}
				else {
					$prop = str_replace(array("\r\n", "\n\r", "\r", "\n"), ' ', str_replace(',', '-', $option->$col));
				}
				$optionsArray[] = $prop;
			}
								
			array_push($rows, $optionsArray);
		}
		
		$reportURL = self::createReportFile ( "OptionsForTranslation", $headings, $rows );
		
		return $reportURL;
	}


	public static function deletePhotos ( $uploadedFile ) {

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
					$photoKey = AWS::s3Key ( $details );
		
					$db = (new DatabaseFactory)->getDriver('mysqli', dbOptions());
		
					// Set the status to DELETED in the Photo table
					$query = $db->getQuery(true)
								->update($db->qn('Photo'))
						->set($db->qn('status') . ' = ' . $deleted_status)
						->where($db->qn('photo_id') . ' = ' . $photoId );
		
					$db->setQuery($query);
		
					$db->execute();
		
					// Remove the image from the S3 bucket
					$success = AWS::delete_from_s3 ( $photoKey );

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
	
	
	public static function sendTranslations ( $articles ) {
		
		$responses = array();
		
		$transifex = new Transifex ();
		
		foreach ( $articles as $articleId ) {
			
			$article = self::getArticle( $articleId );
					
			$articleContent = $article->introtext;
			$title = $article->title;
			
			//articleBase64 = base64_encode ( $articleContent );
			
			$articleExists = $transifex->articleExists( $articleId );
			if ( $articleExists === true ) {
				$response = $transifex->updateArticle ( $articleId, $title, $articleContent );
				if ( $response === false ) {
					$error = $transifex->getLastError();
					$responses[] = "Transifex article " . $articleId . " failed to update: " . $error;
				}
				else {
					$responses[] = "Transifex article " . $articleId . " updated";
				}
			}
			else if ( $articleExists === false ){
				$response = $transifex->newArticle ( $articleId, $title, $articleContent );
				if ( $response === false ) {
					$error = $transifex->getLastError();
					$responses[] = "Transifex article " . $articleId . " failed to create: " . $error;
				}
				else {
					$responses[] = "Transifex article " . $articleId . " created";
				}
			}
			else {
				$error = $transifex->getLastError();
				error_log ("Error checking whether Transifex article exists: " . $error);
				$responses[] = "Error checking whether Transifex article " . $articleId . " exists: " . $error;
			}
		}
		
		return $responses;
	}
	
	
	public static function getTranslations ( $articles, $language ) {
		
		$languages = array();
		if ( $language == "All" ) {
			$allLangs = self::getSupportedLanguages();
			foreach ( $allLangs as $lang ) {
				$languages[] = $lang->tag;
			}
		}
		else {
			$languages[] = $language;
		}
		
		$statusResponses = array();
		
		$transifex = new Transifex ();
		
		foreach ( $languages as $lang ) {
			
			foreach ( $articles as $articleId ) {
				
				$statusResponses[$articleId][$lang] = $transifex->getTranslation ( $articleId, $lang );
			}
		}
				
		return $statusResponses;
		
	}


	public static function  moveDeletionsFileToS3($uploadPath) {

		AWS::move_deletions_file_to_s3 ( $uploadPath );
	}
	
	
	public static function generateNonSpeciesDeepl ( $language ) {
		
		$translateOpts = translateOptions();
		$authKey = $translateOpts['deepl']; 
		$translator = new \DeepL\Translator($authKey);
		
		$reportURL = null;
		$errorMessage = null;
		
		// get the options rows
		$optionsRows = self::getNonSpeciesOptionsForTranslation();
		
		// $optionsStr = print_r ( $this->optionsRows, true );
		// error_log ( 'Got options: ' . $optionsStr );
		
		$optionsCol = array();
		foreach ( $optionsRows as $row ) {
			$optionsCol[] = $row->option_name;
		}
		
		$translatedStrings = null;
		try {
			
			$translatedStrings = $translator->translateText(
				$optionsCol,
				'en',
				$language,
			);
			
			// Now update the rows for the required language
			$numRows = count($optionsRows);
			if ( $numRows == count($translatedStrings) ) {
				
				$errMsg = print_r ( $translatedStrings, true );
				error_log ("Got correct num translations: " . $errMsg);
				
				$languages = self::getSupportedLanguages();
				
				$trLang = null;
				
				// Find the Transifex lang code
				$trCode = "no_lang";
				foreach ( $languages as $lang ) {
					if ( $lang->deepl_code == $language ) {
						$trLang = $lang->transifex_code;
					}
				}
				
				if ( $trLang ) {
					$optionProp = strtolower($trLang);
					
					$i = 0;
					foreach ( $optionsRows as $optionRow ) {
						$translation = $translatedStrings[$i];
						$optionRow->$optionProp = $translation->text;
						$i++;
					}
					
					// $optionsStr = print_r ( $this->optionsRows, true );
					// error_log ( 'Options plus translations: ' . $optionsStr );
					
					
					$headings = self::getOptionsHeadings();
					
					$rows = array();
					foreach ( $optionsRows as $optionId=>$option ) {
					
						$optionsArray = array($optionId, 
											str_replace(array("\r\n", "\n\r", "\r", "\n"), ' ', str_replace(',', '-', $option->option_name)),
											$option->struc );
											
											
						foreach ($languages as $id=>$lang) {
							$col = strtolower($lang->transifex_code);
							if ( !$option->$col ) {
								$prop = "";
							}
							else {
								$prop = str_replace(array("\r\n", "\n\r", "\r", "\n"), ' ', str_replace(',', '-', $option->$col));
							}
							//$prop = str_replace(array("\r\n", "\n\r", "\r", "\n"), ' ', str_replace(',', '-', $option->$col));
							$optionsArray[] = $prop;
						}
											
						array_push($rows, $optionsArray);
					}
					
					$reportURL = self::createReportFile ( "OptionsForTranslation", $headings, $rows );
				}
				else {
					$errorMessage = "Error: " .$language. " language is not supported by MammalWeb";
				}
			}
			else {
				$errorMessage = "Error: Num translated strings not equal to option strings";
			}	
		} catch (Exception $error) {
			
			$errorMessage = 'Error occurred while translating strings: ' . ($error->getMessage() ?? 'unknown error');
			
		}
		
		$result = new \StdClass;
		$result->reportURL = $reportURL;
		$result->errorMessage = $errorMessage;
		
		return $result;
	}


	public static function generateIniDeepl ( $language ) {
		
		$translateOpts = translateOptions();
		$authKey = $translateOpts['deepl']; 
		$translator = new \DeepL\Translator($authKey);
		
		$reportURL = null;
		$errorMessage = null;
		
		// Open the ini file for reading
		$iniFilename = JPATH_SITE . "/language/en-GB/en-GB.com_biodiv.ini";
		
		$t=time();
		$dateStr = date("Ymd_His",$t);
	
		$reportRoot = JPATH_SITE."/biodivimages/reports";
		$filePath = $reportRoot."/admin/";
		$snippetsFilename = $filePath . "snippets" . $dateStr . ".txt";
		
		$iniFile = fopen($iniFilename, "r") or die("Unable to open file!");
		$snippetsFile = fopen($snippetsFilename, "w") or die("Unable to open file!");
		while(!feof($iniFile)) {
			$nextRow = fgets($iniFile);
			$firstQuotePos = strpos ( $nextRow, '"' );
			$lastQuotePos = strrpos ( $nextRow, '"' );
			$snippetLen = $lastQuotePos - $firstQuotePos -1;
			$firstPart = substr ( $nextRow, 0, $firstQuotePos + 1 );
			$toTranslate = substr ( $nextRow, $firstQuotePos + 1, $snippetLen );
			fputs ( $snippetsFile, $toTranslate . "\n" );
		}
		fclose($iniFile);
		fclose($snippetsFile);
		
		$translatedSnippets = $filePath . $language ."translatedSnippets" . $dateStr . ".txt";
		
		try {
			$translator->translateDocument(
				$snippetsFilename,
				$translatedSnippets,
				'en',
				$language,
			);
			
			// Now create new file for download
			$rows = array();
				
			$iniFile = fopen($iniFilename, "r") or die("Unable to open file!");
			$trFile = fopen($translatedSnippets, "r") or die("Unable to open file!");
			while(!feof($iniFile) and !feof($trFile)) {
				$nextRow = fgets($iniFile);
				$firstQuotePos = strpos ( $nextRow, '"' );
				$lastQuotePos = strrpos ( $nextRow, '"' );
				$snippetLen = $lastQuotePos - $firstQuotePos -1;
				$firstPart = substr ( $nextRow, 0, $firstQuotePos + 1 );
				
				$translatedSnippet = fgets ( $trFile );
				
				$translatedRow = $firstPart . trim($translatedSnippet) . '"';
				array_push($rows, $translatedRow);
			}
			fclose($iniFile);
			fclose($trFile);
			
			$newFilename = $language . "com_biodiv";
			$reportURL = self::createReportFileTxt ( $newFilename, $rows );
			
		} catch (\DeepL\DocumentTranslationException $error) {
			// If the error occurs after the document was already uploaded,
			// documentHandle will contain the document ID and key
			$errorMessage = 'Error occurred while translating document: ' . ($error->getMessage() ?? 'unknown error');
			if ($error->documentHandle) {
				$handle = $error->documentHandle;
				echo "Document ID: {$handle->documentId}, document key: {$handle->documentKey}";
			} else {
				echo 'Unknown document handle';
			}
		}
	
		$result = new \StdClass;
		$result->reportURL = $reportURL;
		$result->errorMessage = $errorMessage;
		
		return $result;
	
	}

}





// // Get an instance of the controller prefixed by Biodiv
// $controller = JControllerLegacy::getInstance('BioDiv');

// // Perform the Request task
// $controller->execute(JFactory::getApplication()->input->get('task'));

// // Redirect if set by the controller
// $controller->redirect();
