<?php

namespace MammalWeb\Component\Biodiv\Site\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;

use MammalWeb\Component\Biodiv\Site\Library\Core\Biodiv;
use MammalWeb\Component\Biodiv\Site\Library\Core\Project;

/**
 * @package     Joomla.Site
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2024 MammalWeb Ltd. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

/**
 * Biodiv Project Model
 * @since 4.0.0
 */
class ProjectModel extends BiodivModel {

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
	
	
	public function listedProjects(){
		
		return Project::listedProjects();
		
	}
	
	
	public function listedSchoolProjects(){
		
		return Project::listedSchoolProjects();
		
	}
	
	
	public function projectImageURL( $projectId ) {
		
		return Project::projectImageURL( $projectId );
	}
	
	
	public function projectProgress ( $projectId ) {
		
		return Project::projectProgress ( $projectId );
	}
    
	
	public function getSubProjectsById($projectId, $includePrivate = false){
		
		return Project::getSubProjectsById ( $projectId, $includePrivate );
	}
	
	
	public function getProjectTree ( $projectId ) {
		
		return Project::getProjectTree ( $projectId );
	}
	
	
	public function getChildProjectsById($projectId, $includePrivate = false){
	
		return Project::getChildProjectsById($projectId, $includePrivate);
	}
	
	
	public function projectData ( $projectId, $numMonths, $intervalInMonths = 1, $endDate = null ) {
	
		return Project::projectData ( $projectId, $numMonths, $intervalInMonths, $endDate );
	}


	public function projectAnimals ( $projectId, $numSpecies = null, $includeDontknow = false, 
									$includeHuman = false, $includeNothing = false ) {
	
		return Project::projectAnimals ( $projectId, $numSpecies, $includeDontknow, 
										$includeHuman, $includeNothing );
	}

	
	public function retire ( $projectId ) {
		
		return Project::retire ( $projectId );
	}

	
	public function unretire ( $projectId ) {
		
		return Project::unretire ( $projectId );
	}
	
	
	
}