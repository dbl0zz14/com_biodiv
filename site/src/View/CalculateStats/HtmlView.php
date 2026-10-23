<?php

namespace MammalWeb\Component\Biodiv\Site\View\CalculateStats;

 
// No direct access to this file
defined('_JEXEC') or die;
 

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
 
/**
 * @package     Joomla.Site
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2024 MammalWeb Limited. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

/**
 * View for calculate stats processes
 */
 
class HtmlView extends BaseHtmlView {

	/**
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */

	public function display($tpl = null) 
	{
		$app = Factory::getApplication();
		$input = $app->input;
	
		$this->calcAll = $input->getInt("calc_all", 0);
		$this->projectId = $input->getInt("project_id", 0);
		$this->allProjects = $input->getInt("all_projects", 0);
		$this->calcMonths = $input->getInt("calc_months", 0);
		$this->calcDate = $input->getInt("calc_date", 0);
		
		// Entry in Statistics for all projects
		$this->calcTotals = $input->getInt("calc_totals", 0);
		
		
		$this->calcLeagueTable = $input->getInt("calc_leaguetable", 0);
		
		// Calculate monthly upload and classify statistics by user and project
		$this->calcLeagueMonth = $input->getInt("calc_leaguemonth", 0);
		
		$this->calcAnimals = $input->getInt("calc_animals", 0);
		
		// Calculate animals by site and by year - populates Features, FeatureSites and SiteAnimals tables
		$this->calcSiteAnimals = $input->getInt("site_animals", 0);
		
		// Calculate uploads and classifications by site - populates SiteStatistics table
		$this->calcSiteStats = $input->getInt("site_stats", 0);
		
		// As site_stats but populates/overwrites historical dates - use with care.
		$this->calcSiteHistory = $input->getInt("site_hist", 0);
		
		// Calculate user expertise based on gold standard
		$this->calcExpertise = $input->getInt("calc_expertise", 0);
		
		// Calculate monthly quiz statistics by user
		$this->calcTests = $input->getInt("calc_tests", 0);

		parent::display($tpl);
	}
	
}



?>
