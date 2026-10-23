<?php

namespace MammalWeb\Component\Biodiv\Site\Model;

\defined('_JEXEC') or die;

// // Needed to ensure pick up the correct component files
// set_include_path(JPATH_COMPONENT . get_include_path());

// error_log ( 'JPATH_COMPONENT is ' . JPATH_COMPONENT );

// set_include_path('' . JPATH_COMPONENT . ';' . get_include_path());
// include_once "local.php";

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;

use MammalWeb\Component\Biodiv\Site\Library\Core\Biodiv;
use MammalWeb\Component\Biodiv\Site\Library\Core\Project;
use MammalWeb\Component\Biodiv\Site\Library\Core\KioskSpecies;
use MammalWeb\Component\Biodiv\Site\Library\Core\Training;
use MammalWeb\Component\Biodiv\Site\Library\School\SchoolCommunity;


/**
 * @package     Joomla.Site
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2024 MammalWeb Ltd. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

/**
 * Biodiv Kiosk Model
 * @since 4.0.0
 */
class KioskModel extends BiodivModel {

    /**
     * Constructor
     *
     * @param   array                $config   An array of configuration options (name, state, dbo, table_path, ignore_request).
     * @param   MVCFactoryInterface  $factory  The factory.
     *
     * @since   3.0
     * @throws  \Exception
     */
    public function __construct ( $config = [], MVCFactoryInterface $factory = null ) {
		
        parent::__construct($config);	

    }
	
	public function getProjectDetails ( $projectId ) {
		
		return Project::projectDetails ( $projectId );
	}
	
	public function isSchoolUser () {
		
		return SchoolCommunity::isSchoolUser();
	}
	
	public function getLogos ( $projectId ) {
		
		return Project::getLogos ( $projectId );
	}
	
	public function getIntroCategory ( $projectId ) {
		
		return Project::getKioskIntroCategory ( $projectId );
	}
	
	public function getCategoryContent ( $categoryId ) {
		
		return Project::getCategoryContent ( $categoryId );
	}
	
	public function getKioskSpecies ( $projectId ) {
		
		return new KioskSpecies($projectId);
	}
	
	public function getSeqsJson ( $projectId ) {
		
		return Biodiv::getSeqsJson($projectId);
	}
	
	public function getSecondaryProject ( $projectId ) {
		
		return Biodiv::getSecondaryProject ( $projectId );
	}
	
	public function getBeginnerQuiz ( $projectId ) {
		
		return Training::getBeginnerQuiz ( $projectId );
	}		
			
	public function getTopic ( $projectId, $level ) {
		
		return Training::getTopic ( $projectId, $level );
	}
	
	public function getMapSettings ( $projectId ) {
		
		return Project::getMapSettings ( $projectId );
	}
	
	public function getKioskAboutArticles ( $projectId ) {
		
		return Biodiv::getKioskAboutArticles ( $projectId );
	}
	
	public function getKioskAllAnimals ( $allAnimalsArray ) {
		
		return Biodiv::getKioskAllAnimals ( $allAnimalsArray );
	}
	
	public function writeTestResults ( $topicId, $sequencesJson, $answersJson, $scorePercent ) {
		
		return Training::writeTestResults ( $topicId, $sequencesJson, $answersJson, $scorePercent );
	}
	
}