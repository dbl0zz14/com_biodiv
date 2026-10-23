<?php

namespace MammalWeb\Component\Biodiv\Site\View\Like;
 
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
 * View for like
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
		$spotterModel = $this->getModel();
		
		$personId = $spotterModel->userID();

		if ( !$personId ) die("Please log in");
		
		$app = Factory::getApplication();
		$this->photoId =
		(int)$app->getUserStateFromRequest('com_biodiv.like_photo_id', 'photo_id');

		$this->numLikes = $spotterModel->countLikes ( $this->photoId );

		// Display the view
		parent::display($tpl);
	}
}



?>