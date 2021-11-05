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
* HTML View class for the Biodiversity Monitoring component
*
* @since 0.0.1
*/
class BioDivViewKioskAjaxAudio extends JViewLegacy
{
	/**
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */

	public function display($tpl = null) 
	{
		$this->person_id = (int)userID();
	  
		$option_id = JRequest::getInt("option_id");
	  
		$article = getArticle ( $option_id );
	  
		$this->title = $article->title;
	  
		$this->introtext = $article->introtext;
	  
		// If there's a local sonogram, try to replace iframe, if present
		$sonoJSON = getOptionData ( $option_id, 'sonogram' );
		$this->sonogram = null;
		$this->audioAttribution = null;
		
		if ( count($sonoJSON) > 0 ) {
			$sonoObj = json_decode ( $sonoJSON[0] );
			
			//$errStr = print_r ( $sonoObj, true );
			//error_log ( "Sonogram Object: " . $errStr ); 
			
			$this->sonogram = $sonoObj->video;
			$this->audioAttribution = $sonoObj->attribution;
		}
		
		parent::display($tpl);
    }
}



?>