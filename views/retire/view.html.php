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
* HTML retire sequences View class for the Biodiv Component
*
* @since 0.0.1
*/
class BioDivViewRetire extends JViewLegacy
{
        /**
         *
         * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
         *
         * @return  void
         */

        public function display($tpl = null) 
        {

		$app = JFactory::getApplication();
		
		$input = $app->input;
			
		$this->projectId = $input->getInt("project_id", 0);
		
		$this->unretire = $input->getInt("unretire", 0);
		
		$this->retired = null;
		$this->unretired = null;
		
		if ( $this->unretire ) {
			$this->unretired = unretire( $this->projectId );
		}
		else {
			$this->retired = retire( $this->projectId );
		}

		parent::display($tpl);
        }
}



?>
