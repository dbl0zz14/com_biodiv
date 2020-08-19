<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*/
 
// No direct access to this file
defined('_JEXEC') or die;
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
* HTML View class for the HelloWorld Component
*
* @since 0.0.1
*/
class BioDivViewCalculatestats extends JViewLegacy
{
        /**
         *
         * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
         *
         * @return  void
         */

        public function display($tpl = null) 
        {
			$this->calcAll = JRequest::getInt("calc_all");
			$this->projectId = JRequest::getInt("project_id");
			$this->calcMonths = JRequest::getInt("calc_months");
			$this->calcDate = JRequest::getInt("calc_date");
			$this->calcTotals = JRequest::getInt("calc_totals");
			$this->calcLeagueTable = JRequest::getInt("calc_leaguetable");
			$this->calcAnimals = JRequest::getInt("calc_animals");
			
			// Calculate animals by site and by year - populates Features, FeatureSites and SiteAnimals tables
			$this->calcSiteAnimals = JRequest::getInt("site_animals");
			
			// Calculate uploads and classifications by site - populates SiteStatistics table
			$this->calcSiteStats = JRequest::getInt("site_stats");
			
			// As site_stats but populates/overwrites historical dates - use with care.
			$this->calcSiteHistory = JRequest::getInt("site_hist");
			
			// Calculate user expertise based on gold standard
			$this->calcExpertise = JRequest::getInt("calc_expertise");
		
			parent::display($tpl);
        }
}



?>
