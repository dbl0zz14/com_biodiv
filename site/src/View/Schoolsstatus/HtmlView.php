<?php
namespace MammalWeb\Component\Biodiv\Site\View\SchoolsStatus;

 
// No direct access to this file
defined('_JEXEC') or die;


use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;


/**
 * @package     Joomla.Site
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2024 MammalWeb Limited. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

/**
 * View for the spotter status page
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
			
		$this->spotterModel = $this->getModel();
		
		$personId = $this->spotterModel->userID();

		if ( !$personId ) {
			
			$currentUri = Uri::getInstance();
			
			$loginParam = $app->input->getString('login', 0);
			
			//$defaultLoginPage = 'index.php?option=com_users&view=login';
			$defaultLoginPage = Text::_("COM_BIODIV_MWSSTATUS_LOGIN_PAGE");
			if ( $loginParam ) {
				
				// assume login page has specific routing?
				$url = Route::_($loginParam);
				
			}
			else {
				$url = Route::_($defaultLoginPage.'&return='.base64_encode($currentUri));
			}
			
			//$message = Text::_("COM_BIODIV_MWSSTATUS_LOGIN_MSG");
			$app->redirect($url);

		}
		
		// Set the photo to zero on load and the classify option back to default 0
		$app->setUserState('com_biodiv.photo_id', 0);
		$app->setUserState('com_biodiv.classify_only_project', 0);
		$app->setUserState('com_biodiv.classify_project', 0);
		$app->setUserState('com_biodiv.classify_self', 0);
		$app->setUserState('com_biodiv.project_id', 0);
		$app->setUserState('com_biodiv.animal_ids', 0);


		$this->status = $this->spotterModel->getSpotterStatistics();

		$this->projects = $this->spotterModel->getSchoolSpottingProjects();
	
		$mySchoolProject = $this->spotterModel->getMySchoolProject();
		
		$this->mySchoolProjectId = null;
		$this->mySchoolProjectName = null;
			
		if ( $mySchoolProject and array_key_exists( 'proj_id', $mySchoolProject ) ) {
			
			$this->mySchoolProjectId = $mySchoolProject['proj_id'];
			$this->mySchoolProjectName = $mySchoolProject['proj_prettyname'];
			
		}
	
		$this->mylikes = $this->spotterModel->getLikes(1);
		
		$this->isCameraWebsite =  $this->spotterModel->getSetting("camera") === "yes";
		
		$this->featuredProject = $this->spotterModel->getFeaturedProject();
		
		// Display the view
		parent::display($tpl);
	}
}



?>