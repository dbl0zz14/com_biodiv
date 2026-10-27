<?php

namespace MammalWeb\Component\Biodiv\Site\View\DashLikes;

 
// No direct access to this file
defined('_JEXEC') or die;
 
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Language\Text;
 
/**
 * @package     Joomla.Site
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2024 MammalWeb Limited. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

/**
 * View for project data, used in charts
 */
class RawView extends BaseHtmlView {
  
	/**
	*
	* @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	*
	* @return  void
	*/
	public function display($tpl = null) 
	{

		$this->reportModel = $this->getModel();

		$this->personId = $this->reportModel->userID();
				
		if ( $this->personId ) {
			
			$app = Factory::getApplication();
			
			$input = $app->input;
			
			$this->siteId = $input->get('site', 0, 'INT');
			
			$this->speciesId = $input->get('species', 0, 'INT');
			
			$this->year = $input->get('year', 0, 'INT');
			
			$this->likedByOthers = $input->get('other', 0, 'INT');
			
			$this->allMy = $input->get('all', 0, 'INT');
			
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
			$this->sortSelect = array("site"=>Text::_("COM_BIODIV_DASHLIKES_SORT_SITE"),
									"liked"=>Text::_("COM_BIODIV_DASHLIKES_SORT_LIKED"),
									"taken"=>Text::_("COM_BIODIV_DASHLIKES_SORT_TAKEN")
									);
			
			$this->siteSelect = $this->reportModel->getLikesSiteSelect( $this->allMy,
															$this->likedByOthers );
															
			$this->siteSelect = array(Text::_("COM_BIODIV_DASHLIKES_ALL")) + $this->siteSelect;
			
			
			$this->yearSelect = $this->reportModel->getLikesYearSelect( $this->allMy,
															$this->likedByOthers );
			$this->yearSelect = array(Text::_("COM_BIODIV_DASHLIKES_ALL")) + $this->yearSelect;
			
			
			// Filter by species
			
			$this->speciesSelect = $this->reportModel->getLikesSpeciesSelect ( $this->allMy,
															$this->likedByOthers );
			$this->speciesSelect = array(Text::_("COM_BIODIV_DASHLIKES_ALL")) + $this->speciesSelect;
			
					
			$this->likesArray = $this->reportModel->getLikes (  $this->allMy,
															$this->likedByOthers,
															$this->speciesId,
															$this->siteId,
															$this->year,
															$this->sortBy,
															$start,
															$this->numPerPage );
															
			$this->numLikes = count($this->likesArray);	
			
			$this->totalLikes = $this->reportModel->getTotalLikes ( $this->allMy,
															$this->likedByOthers,
															$this->speciesId,
															$this->siteId,
															$this->year );

		}

		// Display the view
		parent::display($tpl);
	}
}



?>