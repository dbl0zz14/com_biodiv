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
* HTML View class for the MammalWeb Component - transfer resource files to S3
*
*/
class BioDivViewTransferResources extends JViewLegacy
{
	/**
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */

	public function display($tpl = null) 
	{
		if ( canRunScripts() ) {
			// Create a list of all photos to be copied to AWS S3 storage - nb could be videos too
			$db = JDatabase::getInstance(dbOptions());
			$query = $db->getQuery(true);
			$query->select("resource_id, person_id, resource_type, set_id, filename, url")
				->from("Resource")
				->where("s3_status = 0");

			$max_num = 500;
			$db->setQuery($query, 0, $max_num); // LIMIT $max_num
   
			$this->resources = $db->loadAssocList("resource_id");
			
			
		}
		else {
			$this->resources = null;
			
		}
		
		parent::display($tpl);
	}
}



?>
