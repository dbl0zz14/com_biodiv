<?php

namespace MammalWeb\Component\Biodiv\Site\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;


/**
 * @package     Joomla.Site
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2024 MammalWeb Ltd. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

/**
 * Biodiv Upload Model
 * @since 4.0.0
 */
class UploadModel extends FormModel {

    /**
     * Constructor
     *
     * @param   array                $config   An array of configuration options (name, state, dbo, table_path, ignore_request).
     * @param   MVCFactoryInterface  $factory  The factory.
     *
     * @since   3.0
     * @throws  \Exception
     */
    // public function __construct ( $config = [], MVCFactoryInterface $factory = null ) {
		
        // parent::__construct($config);	

		// $this->isCamera = $this->getSetting("camera") == "yes";
		
		// if ( Biodiv::userID() ) {
			// $this->siteHelper = new SiteHelper($this->isCamera);
		// }
		// else {
			// $this->siteHelper = null;
		// }
    // }
	
	
	//public function getUploadForm () {
	public function getForm($data = [], $loadData = true) {
		
		$loadData = false;
		$uploadForm = $this->loadForm(
			'com_biodiv.upload',  		// just a unique name to identify the form
			'upload_form',				// the filename of the XML form definition
										// Joomla will look in the site/forms folder for this file
			array(
				'control' => 'jform',	// the name of the array for the POST parameters
				'load_data' => $loadData // if set to true, then there will be a callback to 
										 // loadFormData to supply the data
			)
		);
		
		return $uploadForm;
	}
	
	
}