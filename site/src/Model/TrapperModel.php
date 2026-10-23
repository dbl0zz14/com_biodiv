<?php

namespace MammalWeb\Component\Biodiv\Site\Model;

\defined('_JEXEC') or die;

// // Needed to ensure pick up the correct component files
// set_include_path(JPATH_COMPONENT . get_include_path());

// error_log ( 'JPATH_COMPONENT is ' . JPATH_COMPONENT );

// set_include_path('' . JPATH_COMPONENT . ';' . get_include_path());
// include_once "local.php";

use Joomla\CMS\Factory;
//use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;

use MammalWeb\Component\Biodiv\Site\Library\Core\Biodiv;
use MammalWeb\Component\Biodiv\Site\Library\Core\SiteHelper;
use MammalWeb\Component\Biodiv\Site\Library\Core\Statistics;
use MammalWeb\Component\Biodiv\Site\Library\School\SchoolCommunity;


/**
 * @package     Joomla.Site
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2024 MammalWeb Ltd. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

/**
 * Biodiv Trapper Model
 * @since 4.0.0
 */
class TrapperModel extends BiodivModel {

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

		$this->isCamera = $this->getSetting("camera") == "yes";
		
		if ( Biodiv::userID() ) {
			$this->siteHelper = new SiteHelper($this->isCamera);
		}
		else {
			$this->siteHelper = null;
		}
    }
	
	
	public function getUploadDetails ( $siteId ) {
		
		return Biodiv::getUploadDetails ( $siteId );
	}
	
	
	public function getUploadCounts ( $uploadId ) {
		
		return Biodiv::getUploadCounts ( $uploadId );
	}
	
	
	public function getUploadErrors ( $uploadId ) {
		
		return Biodiv::getUploadErrors ( $uploadId );
	}
	
	
	public function getUploadFailsForDownload ( $uploadId ) {
		
		return Biodiv::getUploadFailsForDownload ( $uploadId );
	}
	
	
	public function getPrevPhotos ( $uploadId ) {
		
		return Biodiv::getPrevPhotos ( $uploadId );
	}
	
	
	public function getSitePhotos ( $siteId ) {
		
		return Biodiv::getSitePhotos ( $siteId );
	}
	
	
	public function getPhotosToSplit ( $siteId ) {
		
		return Biodiv::getPhotosToSplit ( $siteId );
	}
	
	
	public function getPhotosToResize ( $siteId ) {
		
		return Biodiv::getPhotosToResize ( $siteId );
	}
	
	
	public function getLatestUploads ( $siteId ) {
		
		return Biodiv::getLatestUploads ( $siteId );
	}
	
	
	public function getFieldsArray () {
		
		if ( !$this->siteHelper && Biodiv::userID() ) {
			$this->siteHelper = new SiteHelper($this->isCamera);
		}
		if ( $this->siteHelper ) {
			return $this->siteHelper->getFieldsArray();
		}
		return null;
		
	}
	
	
	public function getHelpArray () {
		
		if ( !$this->siteHelper && Biodiv::userID() ) {
			$this->siteHelper = new SiteHelper($this->isCamera);
		}
		if ( $this->siteHelper ) {
			return $this->siteHelper->getHelpArray();
		}
		return null;
		
	}
	
	
	public function getSites () {
		
		if ( !$this->siteHelper && Biodiv::userID() ) {
			$this->siteHelper = new SiteHelper($this->isCamera);
		}
		if ( $this->siteHelper ) {
			return $this->siteHelper->getSites();
		}
		return null;
		
	}
	
	
	public function getSiteData () {
		
		if ( !$this->siteHelper && Biodiv::userID() ) {
			$this->siteHelper = new SiteHelper($this->isCamera);
		}
		if ( $this->siteHelper ) {
			return $this->siteHelper->getSiteData();
		}
		return null;
		
	}
	
	
	public function getSitePhotoCount () {
		
		if ( !$this->siteHelper && Biodiv::userID() ) {
			$this->siteHelper = new SiteHelper($this->isCamera);
		}
		if ( $this->siteHelper ) {
			return $this->siteHelper->getSitePhotoCount();
		}
		return null;
	}
	
	
	public function getUserProjects () {
		
		if ( !$this->siteHelper && Biodiv::userID() ) {
			$this->siteHelper = new SiteHelper($this->isCamera);
		}
		if ( $this->siteHelper ) {
			return $this->siteHelper->getUserProjects();
		}
		return null;
		
	}
	
	
	public function getProjectSiteData () {
		
		if ( !$this->siteHelper && Biodiv::userID() ) {
			$this->siteHelper = new SiteHelper($this->isCamera);
		}
		if ( $this->siteHelper ) {
			return $this->siteHelper->getProjectSiteData();
		}
		return null;
	}
	
	
	public function getProjectSiteDataJSON () {
		
		if ( !$this->siteHelper && Biodiv::userID() ) {
			$this->siteHelper = new SiteHelper($this->isCamera);
		}
		if ( $this->siteHelper ) {
			return $this->siteHelper->getProjectSiteDataJSON();
		}
		return null;
		
	}
	
	
	public function getProjects () {
		
		if ( !$this->siteHelper && Biodiv::userID() ) {
			$this->siteHelper = new SiteHelper($this->isCamera);
		}
		if ( $this->siteHelper ) {
			return $this->siteHelper->getProjects();
		}
		return null;
		
	}
	
	public function generateSiteCreationModal( $withUpload = false , $defaultProjectId = 1, $badgeId = null, $classId = null ) {
	
		if ( !$this->siteHelper && Biodiv::userID() ) {
			$this->siteHelper = new SiteHelper($this->isCamera);
		}
		if ( $this->siteHelper ) {
			return $this->siteHelper->generateSiteCreationModal( $withUpload, $defaultProjectId, $badgeId, $classId );
		}
		return null;
		
	}
	
	public function generateSiteEditModals( $defaultProjectId = 1 ) {
	
		if ( !$this->siteHelper && Biodiv::userID() ) {
			$this->siteHelper = new SiteHelper($this->isCamera);
		}
		if ( $this->siteHelper ) {
			return $this->siteHelper->generateSiteEditModals( $defaultProjectId );
		}
		return null;
		
	}
	
	public function getBesProjectId () {
		
		$schoolUser = SchoolCommunity::getSchoolUser();
		
		$besProjectId = null;
		if ( $schoolUser ) {
			$besProjectId = $schoolUser->project_id;
		}
		
		return $besProjectId;
	}
	
	public function getSiteDataStrucs ( $projectIds ) {
		
		return Biodiv::getSiteDataStrucs ( $projectIds );
	}
	
	public function getUnsequencedUploads () {
		
		return Biodiv::getUnsequencedUploads ();
	}
	
	public function sequencePhotos ( $uploadId, $sequenceThreshold = 10 ) {
		
		return Biodiv::sequencePhotos ( $uploadId, $sequenceThreshold );
	}
	
	public static function getSiteSelect () {
		
		return Biodiv::getSiteSelect();
	}
	
	public static function getNumAnimals () {
		
		return Biodiv::getNumAnimals();
	}
	
	public function getTrapperStatistics () {
		
		return Statistics::getTrapperStatistics ();
	}
	
	public function getPhotosToTransfer () {
		
		return Biodiv::getPhotosToTransfer ();
	}
	
	public function getOriginalFilesToTransfer () {
		
		return Biodiv::getOriginalFilesToTransfer ();
	}
	
	public function getLargeFilesToTransfer () {
		
		return Biodiv::getLargeFilesToTransfer ();
	}
	
	public function getFilesToConvert () {
		
		return Biodiv::getFilesToConvert ();
	}
	
	public function writeSplitFile ( $ofId, $newFile, $delay = 0 ) {
		
		return Biodiv::writeSplitFile ( $ofId, $newFile, $delay );
	}
	
	public function setOriginalFileStatus ($tsId, $success) {
		
		return Biodiv::setOriginalFileStatus ($tsId, $success);
	}
	
	// public static function addUpload ( $this->isCamera, $siteId, $badgeId = null, $classId = null ) {
	
		// return Biodiv::addUpload ( $this->isCamera, $siteId, $badgeId, $classId );
	// }
	
}