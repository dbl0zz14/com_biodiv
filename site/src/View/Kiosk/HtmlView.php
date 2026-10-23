<?php

namespace MammalWeb\Component\Biodiv\Site\View\Kiosk;

 
// No direct access to this file
defined('_JEXEC') or die;
 
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
// use Joomla\CMS\Router\Route;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
// use Joomla\CMS\Uri\Uri;
// use Joomla\CMS\Session\Session;


 /**
 * @package     Joomla.Site
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2024 MammalWeb Limited. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

/**
 * View for the kiosk landing page 
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
		
		$menuItem = $app->getMenu()->getActive();
		
		if ($menuItem) {
            $this->params = $menuItem->getParams();
        } else {
            // Provide empty fallback params if hitting the view directly without a menu item
            $this->params = new \Joomla\Registry\Registry();
        }
		
		$this->projectId = (int) $this->params->get('project_id', 1);
        $app->setUserState('com_biodiv.project_id', $this->projectId);

		//$this->projectId =
			//(int)$app->getUserStateFromRequest('com_biodiv.project_id', 'project_id', 0);

		if ( !$this->projectId ) die ("no project id given" );
		
		$kioskModel = $this->getModel();

		$this->project = $kioskModel->getProjectDetails($this->projectId);

		// Take the first kiosk option for the project 
		$kioskRows = $kioskModel->getSingleProjectOptions ( $this->projectId, 'kiosk'  );
		$this->kiosk = $kioskRows[0]['option_name'];
		
		$this->user_key = 
			$app->getUserStateFromRequest('com_biodiv.user_key', 'user_key', 0);

		if ( !$this->user_key ) {
			$input = $app->getInput();
			$this->user_key = $input->getString("user_key");
			$app->setUserState('com_biodiv.user_key', $this->user_key);
		}
		
		$this->isSchoolUser = $kioskModel->isSchoolUser();
		
		$this->logoPath = null;
		if ( $this->isSchoolUser ) {
			
			$schoolSettings = $kioskModel->getSetting ( "school_icons" );
			
			$settingsObj = json_decode ( $schoolSettings );
			
			$this->logoPath = $settingsObj->logo;
		}
		
		// Display the view
		parent::display($tpl);
    }
}



?>