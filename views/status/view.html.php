<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*/
 
// No direct access to this file
defined('_JEXEC') or die;
 
// import Joomla view library
jimport('joomla.application.component.view');
 
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;

/**
* HTML View class for the HelloWorld Component
*
* @since 0.0.1
*/
class BioDivViewStatus extends JViewLegacy
{
  /**
   *
   * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
   *
   * @return  void
   */
  
  public function display($tpl = null) 
  {
    $person_id = (int)userID();
	
    if ( !$person_id ) {
		
		$app = JFactory::getApplication();
		
		$currentUri = Uri::getInstance();
		
		$loginParam = $app->input->getString('login', 0);
		
		$defaultLoginPage = 'index.php?option=com_users&view=login';
		
		if ( $loginParam ) {
			
			// assume login page has specific routing?
			$url = JRoute::_($loginParam);
			
		}
		else {
			$url = JRoute::_($defaultLoginPage.'&return='.base64_encode($currentUri));
		}
		
		$message = JText::_("COM_BIODIV_STATUS_LOGIN_MSG");
		$app->redirect($url, $message);

    }
	
    $this->status = getSpotterStatistics();
	
	// Set the photo to zero on load and the classify option back to default 0
	$app = JFactory::getApplication();
    $app->setUserState('com_biodiv.photo_id', 0);
	$app->setUserState('com_biodiv.classify_only_project', 0);
    $app->setUserState('com_biodiv.classify_project', 0);
    $app->setUserState('com_biodiv.classify_self', 0);
    $app->setUserState('com_biodiv.project_id', 0);
	$app->setUserState('com_biodiv.animal_ids', 0);
    

	// call new biodiv.php function instead of myProjects()
	// Changed back argument to check redirect issue  
	$this->projects = mySpottingProjects( true );
	$this->mylikes = getLikes(1);
	
		
    // Display the view
    parent::display($tpl);
  }
}



?>