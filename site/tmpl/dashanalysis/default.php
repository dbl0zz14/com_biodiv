<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;


if ( !$this->personId ) {
	print '<a type="button" href="'.Text::_("COM_BIODIV_DASHANALYSIS_DASH_PAGE").'" class="list-group-item btn btn-block" >'.Text::_("COM_BIODIV_DASHANALYSIS_LOGIN").'</a>';
}
else {
	
	print '<div class="card ps-3 pe-3">';
	print '<div class="card-body">';
			
	print '<h3 class="card-title">'.Text::_("COM_BIODIV_DASHANALYSIS_HEADING").'</h3>';
	
	$this->analysisModel->generateAnalysis($this->analysisType);
	
	print '</div>'; // card-body
	print '</div>'; // card
	

}


?>






