<?php

namespace MammalWeb\Component\Biodiv\Site\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;

use MammalWeb\Component\Biodiv\Site\Library\Core\Analysis;

/**
 * @package     Joomla.Site
 * @subpackage  com_biodiv
 *
 * @copyright   Copyright (C) 2024 MammalWeb Ltd. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

/**
 * Biodiv Analysis Model
 * @since 4.0.0
 */
class AnalysisModel extends BiodivModel {

    /**
     * Constructor
     *
     * @param   array                $config   An array of configuration options (name, state, dbo, table_path, ignore_request).
     * @param   MVCFactoryInterface  $factory  The factory.
     *
     * @since   3.0
     * @throws  \Exception
     */
    public function __construct ( $config = [], MVCFactoryInterface $factory = null ) {
		
        parent::__construct($config);	
    }
	
	
	public static function generateAnalysis ( $analysisType ) {
		
		return Analysis::generateAnalysis ( $analysisType );
	}
	
	
	public function runAnalysis() {
		
		return Analysis::runAnalysis();
	}
	
}