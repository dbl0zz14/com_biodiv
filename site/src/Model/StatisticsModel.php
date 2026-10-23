<?php

namespace MammalWeb\Component\Biodiv\Site\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;

use MammalWeb\Component\Biodiv\Site\Library\Core\Statistics;

/**
 * @package     Joomla.Site
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2024 MammalWeb Ltd. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

/**
 * Biodiv Statistics Model
 * @since 4.0.0
 */
class StatisticsModel extends BiodivModel {

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
	
	
	public function calculateStats($projectId, $calcDate) {
		
		return Statistics::calculateStats($projectId, $calcDate);
	}
	
	
	public function calculateLeagueTable () {
		
		return Statistics::calculateLeagueTable ();
	}
	
	
	public function calculateLeagueTableByMonth ( $projectId = null, $endDate = null ) {
		
		return Statistics::calculateLeagueTableByMonth ( $projectId, $endDate );
	}
	
	
	public function calculateLeagueTableByMonthHistory ( $projectId = null, $numMonths = null) {
	
		return Statistics::calculateLeagueTableByMonthHistory ( $projectId, $numMonths );
	}
	
	
	public function calculateStatsTotals ( $endDate = null ) {
		
		return Statistics::calculateStatsTotals ( $endDate );
	}
	
	
	public function calculateSiteStats ( $endDate = null ) {
		
		return Statistics::calculateSiteStats ( $endDate );
	}
	
	
	public function calculateSiteStatsHistory ( $numMonths = null) {
		
		return Statistics::calculateSiteStatsHistory ( $numMonths );
	}
	
	
	public function calculateAnimalStatistics () {
		
		return Statistics::calculateAnimalStatistics ();
	}
	
	
	public function calculateSiteAnimalStatistics () {
		
		return Statistics::calculateSiteAnimalStatistics ();
	}
	
	
	public function calculateUserTestStatistics ( $end_date = null ) {
		
		return Statistics::calculateUserTestStatistics ( $end_date );
	}
	
	
	public function calculateUserTestStatisticsHistory ( $num_months = null ) {
		
		return Statistics::calculateUserTestStatisticsHistory ( $num_months );
	}
	
	
	public function discoverData ( $latStart, $latEnd, $lonStart, $lonEnd, $numMonths = 12 ) {
		
		return Statistics::discoverData ( $latStart, $latEnd, $lonStart, $lonEnd, $numMonths );
	}
	
	
	public function discoverAnimals ( $latStart, $latEnd, $lonStart, $lonEnd, $numSpecies = null, $includeDontknow = false, $includeHuman = false, $includeNothing = false ) {
		
		return Statistics::discoverAnimals ( $latStart, $latEnd, $lonStart, $lonEnd, $numSpecies, $includeDontknow, $includeHuman, $includeNothing );
	}
	
	
	public function discoverSites () {
		
		return Statistics::discoverSites ();
	}
	
	
	public function discoverSpecies ( $species_id, $year = null ) {
		
		return Statistics::discoverSpecies ( $species_id, $year );
	}
	
	
	public function discoverUserAnimals ( $siteId = null, $top=true, $numSpecies = 10, $includeDontknow = false, $includeHuman = false, $includeNothing = false  ) {
		
		return Statistics::discoverUserAnimals ( $siteId, $top, $numSpecies, $includeDontknow, $includeHuman, $includeNothing  );
	}
	
	
	public function discoverUserSeqAnimals ( $siteId = null, $top=true, $numSpecies = 10, $includeDontknow = false, $includeHuman = false, $includeNothing = false  ) {
		
		return Statistics::discoverUserSeqAnimals ( $siteId, $top, $numSpecies, $includeDontknow, $includeHuman, $includeNothing  );
	}
	
	
	public function discoverUserUploads ( $siteId = null, $numMonths = 12  ) {
		
		return Statistics::discoverUserUploads ( $siteId, $numMonths );
	}


	
}