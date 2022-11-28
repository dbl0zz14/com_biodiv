<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file

defined('_JEXEC') or die('No direct access');

print '      <div class="panel panel-warning">';

print '          <div class="panel-body">';

print $this->debriefArticle;

print '          <form action = "' . BIODIV_ROOT . '" method = "GET">';
print '              <input type="hidden" name="option" value="'.BIODIV_COMPONENT.'"/>';
print '              <input type="hidden" name="view" value="status"/>';
print '              <button class="btn btn-warning" type="submit">'.JText::_("COM_BIODIV_SURVEYDEBRIEF_IDENTIFY").'</button>';
print '          </form>';

print '          </div>';

print '      </div>'; //panel






?>




