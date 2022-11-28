<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;


if ( !$this->personId ) {
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_CHOOSEMODULE_LOGIN").'</div>';
}
else {
	
	print '<div class="vSpaced">';
	
	print '<div class="chooseModuleGrid">';
	
	$browseClass = 'browseBadges';
	$textStem = 'find_';
	if ( $this->student ) {
		$browseClass = 'allStudentBadges';
		$textStem = 'student_';
	}
	else if ( $this->teacher ) {
		$browseClass = 'allTeacherTasks';
		$textStem = 'teacher_';
	}
	
	
	foreach ( $this->modules as $moduleId=>$module ) {
		
		$moduleName = $module->name;
		$moduleNameLc = strtolower($moduleName);
		
		$badgeUrl = $module->badge_url;
		if ( $this->student ) {
			$badgeUrl = $module->student_badge_url;
		}
		else if ( $this->teacher ) {
			$badgeUrl = $module->teacher_badge_url;
		}
		
		if ( $module->active ) {
			print '<div id="chooseModule_'.$module->module_id.'" class="find'.$moduleName.'Activity '.$browseClass.'">';
			print '<a href="'.$badgeUrl.'">';
			print '<div class="panel panel-default actionPanel '.$module->class_stem.'Color">';
			print '<div class="panel-body">';
			$trStr = strtoupper ( $textStem.$moduleNameLc );
			print '<div class="h3 panelHeading">'.JText::_("COM_BIODIV_CHOOSEMODULE_".$trStr).'</div>';
			print '<div class="text-center"><img src="'.$module->image.'"  class="'.$module->class_stem.'Img img-responsive" alt="Find '.$moduleNameLc.' icon" /></div>';
			print '</div>'; // panel-body
			print '</div>'; // panel
			print '</a>';
			print '</div>';
		}
		else {
			print '<div id="chooseModule_'.$module->module_id.'" class="find'.$moduleName.'Activity">';
			print '<div class="panel panel-default actionPanel '.$module->class_stem.'Color">';
			print '<div class="panel-body">';
			$trStr = strtoupper ( $textStem.$moduleNameLc );
			print '<div class="h3 panelHeading">'.JText::_("COM_BIODIV_CHOOSEMODULE_SOON").": ".JText::_("COM_BIODIV_CHOOSEMODULE_".$trStr).'</div>';
			print '<div class="text-center"><img src="'.$module->image.'"  class="'.$module->class_stem.'Img img-responsive" alt="Find '.$moduleNameLc.' icon" /></div>';
			print '</div>'; // panel-body
			print '</div>'; // panel
			print '</div>';
		}
	}
	
	
	
	print '</div>'; // chooseModuleGrid
	
	print '</div>'; // vSpaced
}




?>





