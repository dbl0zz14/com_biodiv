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
		
			parent::display($tpl);
        }
}



?>
