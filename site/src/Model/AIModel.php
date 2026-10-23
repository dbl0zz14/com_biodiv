<?php

namespace MammalWeb\Component\Biodiv\Site\Model;

\defined('_JEXEC') or die;


use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;

use MammalWeb\Component\Biodiv\Site\Library\Core\AI;
// use MammalWeb\Component\Biodiv\Site\Library\Core\Project;
// use MammalWeb\Component\Biodiv\Site\Library\Core\KioskSpecies;
// use MammalWeb\Component\Biodiv\Site\Library\Core\Training;
// use MammalWeb\Component\Biodiv\Site\Library\School\SchoolCommunity;


/**
 * @package     Joomla.Site
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2024 MammalWeb Ltd. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

/**
 * Biodiv AI Model
 * @since 4.0.0
 */
class AIModel extends BiodivModel {

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
	
	public function getTestPhotoIds ( $testTag ) {
		
		return AI::getTestPhotoIds ( $testTag );
	}
	
	public function getUploadId ( $aiType, $projectId, $excludeSubs, $latest ) {
		
		return AI::getUploadId ( $aiType, $projectId, $excludeSubs, $latest );
	}
	
	public function addToAIQueue($aiType, $photoId, $priority) {
		
		return AI::addToAIQueue($aiType, $photoId, $priority);
	}
	
	public function addUploadToAIQueue($aiType, $uploadId, $priority) {
		
		return AI::addUploadToAIQueue($aiType, $uploadId, $priority);
	}
	
	public function getNextSiteId ( $aiType ) {
		
		return AI::getNextSiteId ( $aiType );
	}
	
	public function getPhotoDetails ( $aiType, $siteId, $number, $lowestPriority ) {
		
		return AI::getPhotoDetails ( $aiType, $siteId, $number, $lowestPriority );
	}
	
	public function setQueueStatusSingle ( $aiType, $photoId, $status, $msg = null ) {
		
		return AI::setQueueStatusSingle ( $aiType, $photoId, $status );
	}
	
	public function setQueueStatusMultiple ( $aiType, $photoIdArray, $status, $msg = null ) {
		
		return AI::setQueueStatusMultiple ( $aiType, $photoIdArray, $status, $msg);
	}
	
	public function setProcessingFlags ( $aiType, $theseIds ) {
		
		return AI::setQueueStatusMultiple ( $aiType, $theseIds, AI::PROCESSING );
	}
	
	public function classify ( $aiType, $siteId, $photos ) {
		
		return AI::classify ( $aiType, $siteId, $photos );
	}
	
	public function process ( $aiType, $processAll ) {
		
		return AI::process ( $aiType, $processAll );
	}
}