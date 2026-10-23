<?php

namespace MammalWeb\Component\Biodiv\Site\Model;

\defined('_JEXEC') or die;

// Needed to ensure pick up the correct component files
set_include_path(JPATH_COMPONENT . get_include_path());

//include_once "local.php";

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;

use MammalWeb\Component\Biodiv\Site\Library\Core\Biodiv;
use MammalWeb\Component\Biodiv\Site\Library\Core\Project;
use MammalWeb\Component\Biodiv\Site\Library\Core\Survey;
use MammalWeb\Component\Biodiv\Site\Library\Core\Sequence;
use MammalWeb\Component\Biodiv\Site\Library\Core\MediaCarousel;
use MammalWeb\Component\Biodiv\Site\Library\Core\Report;
use MammalWeb\Component\Biodiv\Site\Library\Core\Training;
use MammalWeb\Component\Biodiv\Site\Library\Core\Statistics;
use MammalWeb\Component\Biodiv\Site\Library\Core\Analysis;
use MammalWeb\Component\Biodiv\Site\Library\Wrapper\AWS;
use MammalWeb\Component\Biodiv\Site\Library\School\SchoolCommunity;


/**
 * @package     Joomla.Site
 * @subpackage  com_biodivprojects
 *
 * @copyright   Copyright (C) 2023 MammalWeb Ltd. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

/**
 * Biodiv Biodiv Model common functions
 * @since 4.0.0
 */
class BiodivModel extends BaseDatabaseModel {

    protected $biodivOptions = null;
	
	/**
     * Constructor
     *
     * @param   array                $config   An array of configuration options (name, state, dbo, table_path, ignore_request).
     * @param   MVCFactoryInterface  $factory  The factory.
     *
     * @since   3.0
     * @throws  \Exception
     */
    public function __construct($config = [], MVCFactoryInterface $factory = null)
    {
        parent::__construct($config);

    }
	
	
	public function userID () {
		
		return (int)Biodiv::userID();
	}
	
	
	public function canClassify ( $struc, $fields ) {
		
		return (int)Biodiv::canClassify( $struc, $fields );
	}
       

	public function canEdit ( $id, $struc ) {
		
		return (int)Biodiv::canEdit(  $id, $struc );
	}
       

	public function canRunScripts () {
		
		return (int)Biodiv::canRunScripts();
	}
       

	public function getSetting ( $key ) {
		
		return Biodiv::getSetting ( $key );
	}
	
	
	public function getArticle ( $optionId ) {
	
		return Biodiv::getArticle ( $optionId );
	}
	
	
	public function getArticleById ( $articleId ) {
	
		return Biodiv::getArticleById ( $articleId );
	}
       

	public function getAssociatedArticleId ( $articleId ) {
	
		return Biodiv::getAssociatedArticleId ( $articleId );
	}
       

	public function photoURL ( $photoId ) {
		
		return Biodiv::photoURL ( $photoId );
	}
	
	
	public function projectImageURL ( $projectId ) {
		
		return Project::projectImageURL ( $projectId );
	}
	
	
	public function imageURL ( $optionId ) {
		
		return Biodiv::imageURL ( $optionId );
	}
	
	
	public function s3URL ( $photoDetails ) {
		
		return AWS::s3URL($details);
	}
	
	
	public function isCamera () {
		
		return Biodiv::getSetting("camera") === "yes";
	}
	
	
	public function isVideo ( $photoId ) {
		
		return Biodiv::isVideo ( $photoId );
	}
	
	
	public function isAudio ( $photoId ) {
		
		return Biodiv::isAudio ( $photoId );
	}
	
	
	public function isFavourite ( $photoId ) {
		
		return Biodiv::isFavourite ( $photoId );
	}
	
	
	public function getSiteLocation ( $siteId ) {
		
		return Biodiv::getSiteLocation ( $siteId );
	
	}
	
	
	public function showAlert ( $type, $msg ) {
		
		return Biodiv::showAlert ( $type, $msg );
	}
	
	
	public function getSingleProjectOptions ( $project_id, $option_type ) {
		
		return Project::getSingleProjectOptions ( $project_id, $option_type );
	}
	
	
	public function generateSurveyModal () {
		
		return Survey::generateSurveyModal ();
	}
	
	
	public function biodivLabelIcons($type, $str, $what=""){
	
		return Biodiv::biodiv_label_icons($type, $str, $what);
	}
	
	
	public static function setRunning ( $processName, $isRunning ) {
		
		return Biodiv::setRunning ( $processName, $isRunning );
	}
	
	
	public function getMediaCarousel () {
		
		return new MediaCarousel();
	}
	
	
	public function getSequence ( $sequenceId ) {
		
		return new Sequence ( $sequenceId );
	}
	
	
	public function getTrainingSequence( $sequenceId, $topicId = null ) {
		
		return Training::getTrainingSequence( $sequenceId, $topicId );
	}
	
	
	public function printSpeciesListSearch ( $filterId, $speciesList, $useSeq=false, $dataToggle = true ) {
		
		return Biodiv::printSpeciesListSearch ( $filterId, $speciesList, $useSeq, $dataToggle );
	}
	
	
	public function countLikes ( $photoId ) {
		
		return Biodiv::countLikes ( $photoId );
	}
	
	
	public function getSpottingProjects ( $reduce = false ) {
		
		return Project::mySpottingProjects ( $reduce );
	}
	
	
	public function getTrappingProjects ( $reduce = false ) {
		
		return Project::myTrappingProjects ( $reduce );
	}
	
	
	public function getAdminProjects ( $reduce = false ) {
		
		return Project::myAdminProjects ( $reduce );
	}
	
	
	public function getSchoolSpottingProjects ( $reduce = false ) {
		
		return SchoolCommunity::getSchoolSpottingProjects ( $reduce );
	}
	
	
	public function getMySchoolProject () {
		
		return SchoolCommunity::getMySchoolProject ();
	}
	
	
	public function isSchoolUser () {
		
		return SchoolCommunity::isSchoolUser ();
	}
	
	
	public function nextSequence( $projectId = null){
		
		return Biodiv::nextSequence ( $projectId );

	}
	
	
	public function nextSequenceObject( $projectId = null){
		
		$sequenceDetails = Biodiv::nextSequence ( $projectId );
		
		$sequence = null;
		
		if ( count($sequenceDetails) > 0 ) {
			$sequenceId = $sequenceDetails[0]['sequence_id'];
			$sequence = new Sequence ( $sequenceId );
		}
		
		return $sequence;

	}
	
	
	public function listUserReports () {
		
		return Report::listUserReports ();
	}
	
	
	public function listUserOptInReports () {
		
		return Report::listUserOptInReports ();
	}
	
	
	public function listUserAnalysis () {
		
		return Analysis::listUserAnalysis ();
	}
	
	
	public function getReportHelpText ( $reportList ) {
		
		return Report::getReportHelpText ( $reportList );
	}
	
	
	public static function removeExistingReports ( $personId ) {
		
		return Report::removeExistingReports ( $personId );
	}
	
	
	public static function userExpertise( $personId ) {
		
		return Training::userExpertise( $personId );
	}
	
	
	public function getTrainingSequences( $topicId, $maxNumber=10 ) {
		
		return Training::getTrainingSequences( $topicId, $maxNumber );
	}
	
	
	public function getFeatureSpecies() {
		
		return Statistics::getFeatureSpecies ();
	}
	
	
	public function getTrapperBriefStatistics () {
		
		return Statistics::getTrapperBriefStatistics ();
	}
	
	
	public function getNumSites () {
		
		return Biodiv::getNumSites();
	}
	
	
}