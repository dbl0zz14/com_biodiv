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
* HTML View class for the Projects page 
*
* @since 0.0.1
*/
class BioDivViewChooseModule extends JViewLegacy
{
  /**
   *
   * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
   *
   * @return  voidz
   */
  
  public function display($tpl = null) 
  {
    $this->personId = (int)userID();
    
    $app = JFactory::getApplication();
	
	if ( $this->personId ) {
		
		$app = JFactory::getApplication();
		$input = $app->input;
			
		$this->student = $input->getInt('student', 0);
	
		$this->teacher = $input->getInt('teacher', 0);
	
		$this->modules = Biodiv\Module::getModules( false );
	}
	
	// Display the view
    parent::display($tpl);
  }
}



?>

