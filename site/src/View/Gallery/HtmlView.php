<?php

namespace MammalWeb\Component\Biodiv\Site\View\Gallery;
 
// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * @package     Joomla.Site
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2024 MammalWeb Limited. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

/**
 * View for the projects list display page
 */

class HtmlView extends BaseHtmlView {

	/**
     * Display the view
     *
     * @param   string  $template  The name of the layout file to parse.
     * @return  void
     */
    
	public function display($tpl = null) 
	{
		
		$app = Factory::getApplication();
			
		$input = $app->input;
		
		$this->reportModel = $this->getModel();
		
		$this->speciesId = $input->get('species', 0, 'INT');
		
		$this->year = $input->get('year', 0, 'INT');
		
		$this->sortBy = $input->get('sort', 'liked', 'string');
		
		$this->page = $input->get('page', 0, 'INT');
		
		$this->numPerPageStr = $input->get('number', 3, 'string');
		
		if ( $this->numPerPageStr == "all" ) {
			$start = 0;
			$this->numPerPage = null;
		}
		else {
			$this->numPerPage = intval($this->numPerPageStr);
			$start = ($this->page) * $this->numPerPage;	
		}			
				
				
		// Set up the select arrays in value=>displayedValue form
		
		// Number per page
		$this->numberSelect = array("3"=>"3", "6"=>"6", "all"=>Text::_("COM_BIODIV_DASHLIKES_ALL"));
		
		// Sort by
		$this->sortSelect = array("liked"=>Text::_("COM_BIODIV_DASHLIKES_SORT_LIKED"),
								"taken"=>Text::_("COM_BIODIV_DASHLIKES_SORT_TAKEN")
								);
		
		$this->yearSelect = $this->reportModel->getPublicLikesYearSelect();
		$this->yearSelect = array(Text::_("COM_BIODIV_DASHLIKES_ALL")) + $this->yearSelect;
		
		
		// Filter by species
		
		$this->speciesSelect = $this->reportModel->getPublicLikesSpeciesSelect ();
		$this->speciesSelect = array(Text::_("COM_BIODIV_DASHLIKES_ALL")) + $this->speciesSelect;
		
				
		// Get any liked sequences - ?add to do for the previous 15 months
		$this->likesArray = $this->reportModel->getPublicLikes( $this->speciesId,
														$this->year,
														$this->sortBy,
														$start,
														$this->numPerPage);
		
		$this->numLikes = count($this->likesArray);	
		
		$this->totalLikes = $this->reportModel->getTotalPublicLikes ( $this->speciesId,
														$this->year );
														
		
		// $this->likesArray = $this->reportModel->getLikes (  $this->allMy,
														// $this->likedByOthers,
														// $this->speciesId,
														// $this->siteId,
														// $this->year,
														// $this->sortBy,
														// $start,
														// $this->numPerPage );
														
		// $this->numLikes = count($this->likesArray);	
		
		// $this->totalLikes = $this->reportModel->getTotalLikes ( $this->allMy,
														// $this->likedByOthers,
														// $this->speciesId,
														// $this->siteId,
														// $this->year );
		
		parent::display($tpl);
	  
	}
	
  
}



?>