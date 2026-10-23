<?php

namespace MammalWeb\Component\Biodiv\Administrator\View\BatchUsers;

 
// No direct access to this file
defined('_JEXEC') or die;
 
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

use MammalWeb\Component\Biodiv\Site\Library\Core\Users;

/**
 * View for creating batch users, used in admin batch users (biodivs)
 */
class RawView extends BaseHtmlView 
{
	/**
	 * Display the BatchUsers view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		//error_log ("BatchUsers view calling batchCreateUsers");
		
		$this->newUsers = Users::batchCreateUsers();
			
		// Display the template
		parent::display($tpl);
	}
	
	
}