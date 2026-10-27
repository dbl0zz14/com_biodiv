<?php

namespace MammalWeb\Component\Biodiv\Site\View\GalleryItem;
 
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
		
		$spotterModel = $this->getModel();
		
		$this->sequenceId = $input->get('sequence', 0, 'INT');
		$this->photoId = $input->get('photo', 0, 'INT');
		
		if ( !$this->sequenceId ) {
			
			// Get the sequence id of the photo
			$sequenceDetails = $spotterModel->getSequenceDetails($this->photoId);
			if ( count($sequenceDetails) > 0 ) {
				$this->sequenceId = $sequenceDetails[0]['sequence_id'];
			}
		}
		
		$this->sequence = null;
		
		if ( $this->sequenceId ) {
			
			// Ensure this sequence is only from a public project.
			$isPublicLike = $this->spotterModel->isPublicLike( $this->sequenceId );
		
			if ( $isPublicLike ) {
				$this->sequence = $spotterModel->getTrainingSequence($this->sequenceId);
			}
		
			// Create the classes used to generate the carousel html code
			$this->mediaCarousel = $spotterModel->getMediaCarousel();
			
		}
		
		parent::display($tpl);
	  
	}
	
  
}



?>