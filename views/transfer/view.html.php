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
* HTML View class for the MammalWeb Component
*
*/
class BioDivViewTransfer extends JViewLegacy
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
			$query->select("photo_id, person_id, site_id, dirname, filename")
				->from("Photo")
				->where("s3_status = 0");

			//$db->setQuery($query);
			$max_num = 500;
			$db->setQuery($query, 0, $max_num); // LIMIT $max_num
   
			$this->photos = $db->loadAssocList("photo_id");
		}
		else {
			$this->photos = null;
		}
		
		parent::display($tpl);
	}
}



?>
