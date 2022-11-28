<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

error_log ( "SchoolTasks template called" );


if ( !$this->personId ) {
	
	// Please log in button
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_SCHOOLTASK_LOGIN").'</div>';
	
}
else if ( !$this->isTeacher ) {
	
	// Must be teacher message
	print JText::_("COM_BIODIV_SCHOOLTASK_MUST_BE_TEACHER");
	
}

else {
	
	print '<div class="row">';
	print '<form id="schoolTaskForm">';
	
	$schoolId = $this->schoolRoles[0]['school_id'];
	$schoolRoleId = $this->schoolRoles[0]['role_id'];
	
	$resourceTypeId = codes_getCode ( "Task", "resource" );
	
	print "<input type='hidden' name='school' value='" . $schoolId . "'/>";
	print "<input type='hidden' name='resourceType' value='" . $resourceTypeId . "'/>";
	
	print '<div class="col-md-6">';
	
	print '<h3>'.JText::_("COM_BIODIV_SCHOOLTASK_SELECT_TASK").'</h3>';
	
	print '  <div class="input-group">';
	print '    <span class="input-group-addon" style="background-color:#FFFFFF; border-bottom-left-radius:25px; border-top-left-radius:25px;"><span class="glyphicon glyphicon-search"></span></span>';
	print '    <input type="search" class="form-control" id="searchTasks" placeholder="Search for a task..." style="border-left: 0px;border-bottom-right-radius:25px; border-top-right-radius:25px;">';
	print '  </div>'; // input-group
	
	
	print '<div id="taskSelect" class="list-group btn-group-vertical btn-block" role="group" aria-label="Task Buttons" name="taskSelect">';
	
	foreach($this->allTasks as $task){
		
		$moduleId = $task->module_id;
		$moduleImg = $task->icon;
		$imgClass = "statusModuleIcon" . $task->module_name;
		$groupImg = $task->badge_icon;
		
		print '<button id="schoolTask_'.$task->task_id.'" type="button" class="list-group-item btn btn-block schoolTask " style="white-space: normal;" data-module=".$moduleId.">';
		print '<div class="row">';
		print '<div class="col-md-3 col-sm-3 col-xs-3">';
		print '<div class="row">';
		print '<div class="col-md-6 col-sm-6 col-xs-6">';
		print '<img src="'.$moduleImg.'" class="'.$imgClass.'">';
		print '</div>'; // col-6
		print '<div class="col-md-6 col-sm-6 col-xs-6">';
		print '<img src="'.$groupImg.'" class="img-responsive schoolTaskGroupIcon">';
		print '</div>'; // col-6
		print '</div>'; // row
		print '</div>'; // col-3
		print '<div class="col-md-9 col-sm-9 col-xs-9 text-left">';
		print '<h5> '.$task->module_name. ' ' . $task->badge_group. ' ' . JText::_("COM_BIODIV_SCHOOLTASK_BADGE") . '. ' .$task->badge_name. ' - ' . $task->name . 
					'</h5><p><small>'.$task->description.'</small></p>';
		print '</div>'; // col-9
		print '</div>'; // row	
		print '</button>';
	}

	print '</div>'; // list-group
	
	print '</div>'; // col-6
	
	
	
	print '<div class="col-md-3">';
	
	print '<div id="chooseStudents" >';
	
	print '<h3>'.JText::_("COM_BIODIV_SCHOOLTASK_SELECT_STUDENTS").'</h3>';
	
	print '<div class="input-group">';
	print '<div class="checkbox">';
	print '<label><input id="selectAllStudents" type="checkbox" value="" aria-label="checkbox to select all students">'. JText::_("COM_BIODIV_SCHOOLTASK_SELECT_ALL") .'</label>';
	print '</div>';
	print '</div>';
	
	print '<div id="studentSelect" name="studentSelect">';
	
	foreach ( $this->myStudents as $student ) {
		print '<div class="checkbox">';
		print '<label><input type="checkbox" class="studentCheckbox" name="studentCheckBox[]" value="'.$student->person_id.
					'" aria-label="checkbox to select '.$student->username.'">'. $student->username .'</label>';
		print '</div>';
	}
	
	print '</div>'; // studentSelect
	
	print '</div>'; // chooseStudents
	print '</div>'; // col-3
	
	
	
	print '<div class="col-md-3 text-left">'; 
	
	print '<div id="uploadButton"">';
	
	print '<button type="submit" id="uploadSchoolTask" class="btn btn-primary btn-lg dash_btn ">'.JText::_("COM_BIODIV_SCHOOLTASK_UPLOAD").'</button>';
		
	print '</div>'; // uploadButton
	
	print '<div id="schoolTaskMsg"></div>';
	
	print '</div>'; // col-3
	
	print '</form>';
	
	print '</div>'; // row
	
	//print '</div>'; // #displayArea
	
}



?>