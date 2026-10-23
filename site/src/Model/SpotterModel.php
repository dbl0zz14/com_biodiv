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
use MammalWeb\Component\Biodiv\Site\Library\Core\Statistics;
use MammalWeb\Component\Biodiv\Site\Library\Core\Training;
use MammalWeb\Component\Biodiv\Site\Library\Core\SpeciesCarousel;

/**
 * @package     Joomla.Site
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2024 MammalWeb Ltd. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

/**
 * Biodiv Spotter Model
 * @since 4.0.0
 */
class SpotterModel extends BiodivModel {

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
	
	
	public function getSpotterStatistics () {
		
		return Statistics::getSpotterStatistics ();
	}
	
	
	public function getSpotterBriefStatistics () {
		
		return Statistics::getSpotterBriefStatistics ();
	}
	
	
	public function getLikes ( $maxNum ) {
		
		return Biodiv::getLikes ( $maxNum );
		
	}
	
	public function getAnimal ( $animalId ) {
		
		return Biodiv::getAnimal ( $animalId );
	}
	
	
	public function getSingleProjectOptions ( $projectId, $optionType ) {
		
		return Project::getSingleProjectOptions ( $projectId, $optionType );
		
	}
	
	
	public function getFeaturedProject () {
		
		return Project::getFeaturedProject ();
		
	}
	
	
	public function getSequenceDetails($photoId) {
	  
		return Biodiv::getSequenceDetails ( $photoId );
		
	}
	
	
	public function getExistingClassifications ( $animalIds ) {
		
		return Biodiv::getExistingClassifications ( $animalIds );
	
	}
	
	
	public function getProjectFilters ( $projectId, $photoId = null ) {
	
		return Biodiv::getProjectFilters ( $projectId, $photoId );
		
	}
	
	
	public function getSpecies ( $filterId, $onePage ) {
	
		return Biodiv::getSpecies ( $filterId, $onePage );

	}
	
	
	public function photoSequenceStart ( $lastPhotoId ) {
	
		return Biodiv::photoSequenceStart ( $lastPhotoId );

	}
	
	
	public function getClassifyInputs () {
		
		return Biodiv::getClassifyInputs ();

	}
	
	
	public function getPrevPhotos ( $uploadId ) {
		
		return Biodiv::getPrevPhotos ( $uploadId );
	}
	
	
	public function getClassificationButton ( $id, $animalArray ) {
		
		return Biodiv::getClassificationButton ( $id, $animalArray );
	}
	
	
	public function getTrainingTopics () {
		
		return Training::getTrainingTopics ();
	}
	
	
	public function getTrainingTopicIds () {
		
		return Training::getTrainingTopicIds ();
	}
	
	
	public function getTopicFilters ( $topicId ) {
		
		return Training::getTopicFilters ( $topicId );
	}
	
	
	public function getSpeciesCarousel () {
		
		return new SpeciesCarousel();
	}
	
	
	public function getTraining () {
		
		return new Training ();
	}
	
	
	
}