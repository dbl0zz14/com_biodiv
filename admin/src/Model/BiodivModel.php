<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_categories
 *
 * @copyright   (C) 2008 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MammalWeb\Component\Biodiv\Administrator\Model;


use Joomla\CMS\MVC\Model\AdminModel;

use MammalWeb\Component\Biodiv\Administrator\Library\Biodiv;


\defined('_JEXEC') or die;

/**
 * Categories Component Category Model
 *
 * @since  1.6
 */
class BiodivModel extends AdminModel
{
	
	// Required definition of abstract function
	public function getForm($data = [], $loadData = true) {
		
		return null;
	}
	
	
	public function printAdminMenu($currentPage) {
		
		return Biodiv::printAdminMenu($currentPage);
	}


	public function printProjectForm ( $projectId = null ) {
		
		return Biodiv::printProjectForm ( $projectId );
		

	}

	public function getAllProjects () {
		
		return Biodiv::getAllProjects ();
			
	}


	public function getProject ( $projectId ) {
		
		return Biodiv::getProject ( $projectId );
			
	}


	public function getAllProjectAdmins () {
		
		return Biodiv::getAllProjectAdmins ();
		
	}


	public function printSchoolForm ( $schoolId = null ) {
		
		return Biodiv::printSchoolForm ( $schoolId );
		
		
	}


	public function getAllSchools () {
		
		return Biodiv::getAllSchools ();
			
	}

	public function getSchool ( $schoolId ) {
		
		return Biodiv::getSchool ( $schoolId );
			
	}
	
	public function addSchoolUser ( $role, $personId, $schoolId ) {
		
		return Biodiv::addSchoolUser ( $role, $personId, $schoolId );
	}
	
	
	public static function getSchoolUserGroups () {
	
		return Biodiv::getSchoolUserGroups ();
	}


	// public function getSchoolUsers ( $usergroup ) {
		
		// return Biodiv::getSchoolUsers ( $usergroup );
	// }


	public function printSchoolUserForm ( $schoolId, $usergroup ) {
		
		return Biodiv::printSchoolUserForm ( $schoolId, $usergroup );
		
	}
	
	
	



	// public function getOptions ( $struc ) {
		
		// return Biodiv::getOptions ( $struc );
			
	// }

	// public function getPriorities() {
		
		// return Biodiv::getPriorities();

	// }

	// public function getSpeciesLists () {
		
		// return Biodiv::getSpeciesLists ();
			
	// }

	// public function getProjectDisplayOptions () {
		
		// return Biodiv::getProjectDisplayOptions ();
			
	// }

	public function getAccessLevels () {
		
		return Biodiv::getAccessLevels ();
			
	}

	// private function getRegions () {
		
		// return Biodiv::getRegions ();
			
	// }

	// public function getUserDetails ( $userId ) {
		
		// return Biodiv::getUserDetails ( $userId );

	// }

	public function createReportFile ( $type, $headings, $rows ) {
		
		return Biodiv::createReportFile ( $type, $headings, $rows );
	}

	public function createReportFileTxt ( $type, $rows ) {
		
		return Biodiv::createReportFileTxt ( $type, $rows );
	}

	public function getAllArticlesForTranslation ( $page, $length, $searchStr = null ) {
		
		return Biodiv::getAllArticlesForTranslation ( $page, $length, $searchStr);
		
	}

	// public function getArticle ( $articleId ) {
		
		// return Biodiv::getArticle ( $articleId );
		
	// }

	public function getSupportedLanguages () {
		
		return Biodiv::getSupportedLanguages ();
	}


	// public function getSupportedLanguage ( $tag ) {
		
		// return Biodiv::getSupportedLanguage ( $tag );
	// }


	public function getSpeciesTranslationLists () {
		
		return Biodiv::getSpeciesTranslationLists ();
	}
	
	
	public function sendTranslations ( $articles ) {
		
		return Biodiv::sendTranslations ( $articles );
	}
	
	
	public function getTranslations ( $articles, $language ) {
		
		return Biodiv::getTranslations ( $articles, $language );
	}


	// public function getOptionsHeadings () {
		
		// return Biodiv::getOptionsHeadings ();
	// }


	// public function getNonSpeciesOptionsForTranslation () {
		
		// return Biodiv::getNonSpeciesOptionsForTranslation ();
	// }


	// public function getAllAssociations ( $articleId ) {
		
		// return Biodiv::getAllAssociations ( $articleId );
	// }


	// public function getAssociations ( $articleId, $lang ) {
		
		// return Biodiv::getAssociations ( $articleId, $lang );
	// }


	// public function getAssociatedArticle ( $articleId, $lang ) {
		
		// return Biodiv::getAssociatedArticle ( $articleId, $lang );
	// }


	// public function getAssociatedCategory ( $catId, $lang ) {
		
		// return Biodiv::getAssociatedCategory ( $catId, $lang );
	// }


	// public function isJSON ( $string ) {
		
	   // return Biodiv::isJSON ( $string );
	   
	// }


	// public function getJSONDecodedArray ( $string ) {
		
		// return Biodiv::getJSONDecodedArray ( $string );
	   
	// }


	// public function checkHtml ( $htmlStr ) {
		
		// return Biodiv::checkHtml ( $htmlStr );
	// }


	public function updateArticle ( $articleId, $lang, $title, $html ) {
		
		return Biodiv::updateArticle ( $articleId, $lang, $title, $html );
	}
	
	
	public function generateSpeciesOptionsFile ( $listTranslationId ) {
		
		return Biodiv::generateSpeciesOptionsFile ( $listTranslationId );
	}
	
	
	public function generateNonSpeciesOptionsFile () {
		
		return Biodiv::generateNonSpeciesOptionsFile ();
	}


	// public function deletePhotos ( $uploadedFile ) {

		// return Biodiv::deletePhotos ( $uploadedFile );
	// }


	// public function  moveDeletionsFileToS3($uploadPath) {

		// return Biodiv::moveDeletionsFileToS3($uploadPath);
	// }
	
	
	public function generateNonSpeciesDeepl ( $language ) {
		
		return Biodiv::generateNonSpeciesDeepl ( $language );
	}
	
	
	public function generateIniDeepl ( $language ) {
		
		return Biodiv::generateIniDeepl ( $language );
	}
   
}
