<?php

namespace MammalWeb\Component\Biodiv\Site\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;

use MammalWeb\Component\Biodiv\Site\Library\Core\Biodiv;
use MammalWeb\Component\Biodiv\Site\Library\Core\Report;
use MammalWeb\Component\Biodiv\Site\Library\Core\Project;
use MammalWeb\Component\Biodiv\Site\Library\Core\DataExtract;


/**
 * @package     Joomla.Site
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2024 MammalWeb Ltd. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

/**
 * Biodiv Report Model
 * @since 4.0.0
 */
class ReportModel extends BiodivModel {

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
	
	public static function listReports () {
		
		return Report::listReports ();
	}

	public function getNewReport( $reportType, $projectId = null ) {
		
		return new Report( $reportType, $projectId );
	}
	
	public function getReport( $reportId ) {
		
		return Report::createFromId ( $reportId );
	}
	
	public function getReportForDownload ( $reportId ) {
		
		return Report::getReportForDownload ( $reportId );
	}
	
	public function getAllProjectOptInReports ( $projectIds ) {
		
		return Report::getAllProjectOptInReports ( $projectIds );
	}
	
	public function getReportHelpText ( $reportId ) {
		
		return Report::getReportHelpText ( $reportId );
	}
	
	public function getProjectUsers ( $projectId ) {
		
		return Project::getProjectUsers ( $projectId );
	}
	
	// public function getFilterValues ( $type ) {
		
		// return Report::getFilterValues ( $type );
	// }
	
	// public function getSelectedValue ( $type, $filter ) {
		
		// return Report::getSelectedValue ( $type, $filter );
	// }
	
	public function getJsonThreshold () {
		
		return Report::REPORT_FILE_THRESHOLD;
	}
	
	public function getLikesSiteSelect( $uploadedByMe, $likedByOthers ) {
		
		return Report::getLikesSiteSelect( $uploadedByMe, $likedByOthers );
	}
	
	public function getLikesYearSelect( $uploadedByMe, $likedByOthers ) {
		
		return Report::getLikesYearSelect( $uploadedByMe, $likedByOthers );
	}
	
	public function getPublicLikesYearSelect() {
		
		return Report::getPublicLikesYearSelect();
	}
	
	public function getLikesSpeciesSelect( $uploadedByMe, $likedByOthers ) {
		
		return Report::getLikesSpeciesSelect( $uploadedByMe, $likedByOthers );
	}
	
	public function getPublicLikesSpeciesSelect() {
		
		return Report::getPublicLikesSpeciesSelect();
	}
	
	public function getLikes( $uploadedByMe,
								$likedByOthers,
								$speciesId,
								$siteId,
								$year,
								$sortBy,
								$start,
								$numPerPage ) {
		
		return Report::getLikes( $uploadedByMe,
								$likedByOthers,
								$speciesId,
								$siteId,
								$year,
								$sortBy,
								$start,
								$numPerPage );
	}
	
	public function getTotalLikes( $uploadedByMe,
								$likedByOthers,
								$speciesId,
								$siteId,
								$year ) {
		
		return Report::getTotalLikes( $uploadedByMe,
								$likedByOthers,
								$speciesId,
								$siteId,
								$year );
	}
	
	
	public function getPublicLikes ( $speciesId,
										$year,
										$sortBy,
										$start,
										$numPerPage ) {
		
		return Report::getPublicLikes ( $speciesId,
										$year,
										$sortBy,
										$start,
										$numPerPage );									
	}
										
	public function getTotalPublicLikes( $speciesId,
								$year ) {
		
		return Report::getTotalPublicLikes( $speciesId,
								$year );
	}
	
	
	public function generateNbn ( $region, $filename ) {
		
		return DataExtract::generateNbn ( $region, $filename );
	}
	
	
	public function generateCamtrapDp ( $startDate, $endDate, $zipFileTag ) {
		
		return DataExtract::generateCamtrapDp ( $startDate, $endDate, $zipFileTag );
	}
	
	
	public function removeExtractDir ( $dir ) {
		
		return DataExtract::removeExtractDir ( $dir );
	}
	
	

}