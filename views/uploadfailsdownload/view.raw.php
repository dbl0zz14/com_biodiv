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
* HTML View class for the BioDiv Component
*
* @since 0.0.1
*/
class BioDivViewUploadFailsDownload extends JViewLegacy
{
	/**
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */

    public function display($tpl = null) 
    {

	($personId = userID()) or die("Please log in");

	$app = JFactory::getApplication();
	$input = $app->input;

	$this->uploadId =
	(int)$app->getUserStateFromRequest('com_biodiv.upload_id', 'upload_id');

	//$this->fails = $trapperModel->getUploadFailsForDownload ( $this->uploadId );

	$details = codes_getDetails($this->uploadId, 'upload');

	$this->fails = null;

	if ( $details && ($details['person_id'] == $personId) ) {

		$this->fails = new StdClass();

		$this->fails->filename = "Upload" . $this->uploadId . "Site" . $details['site_id'] . "Errors.csv";

		$db = JDatabase::getInstance(dbOptions());
		$query = $db->getQuery(true);
		$query->select("upload_filename")
			->from($db->quoteName("UploadError"))
			->where("upload_id = " .(int)$this->uploadId);
		$db->setQuery($query);
		$this->fails->data = $db->loadColumn();

	}
	else {
		$this->fails = new StdClass();
		$this->fails->data = "Sorry you do not have access to this file";
	}


	// Display the view
	parent::display($tpl);

    }
}



?>
