<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;


if ( !$this->personId ) {
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_SCHOOLADMIN_LOGIN").'</div>';
}
else if ( !$this->schoolUser ) {
	print '<h2>'.JText::_("COM_BIODIV_SCHOOLADMIN_NOT_SCH_USER").'</h2>';
}
else {
	
	if ( $this->help ) {
		Biodiv\Help::printSchoolAdminHelp( $this->schoolUser );
	}
	
	print '<div class="row">';
	print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	Biodiv\SchoolCommunity::generateNav($this->schoolUser, null, "teacherzone");
	
	print '</div>'; // col-12
	print '</div>'; // row
	
	// --------------------- Info button
	
	if ( $this->helpOption > 0 ) {
		print '<div id="helpButton_schooladmin" class="btn btn-default helpButton h4" data-toggle="modal" data-target="#helpModal">';
		print '<i class="fa fa-info"></i>';
		print '</div>'; // helpButton
	}
	
	// --------------------- Main content
	
	//print '<div class="row menuGridRow">';
	//print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
	print '<div id="displayArea">';
	
	if ( !$this->checklist ) {
		print '<a href="'.$this->educatorPage.'" class="btn btn-success homeBtn" >';
		print '<i class="fa fa-arrow-left"></i> ' . JText::_("COM_BIODIV_STUDENTPROGRESS_EDUCATOR_ZONE");
		print '</a>';
	}
	
	//$adminPanelClass = "";
	if ( $this->checklist ) {
		
		//$adminPanelClass = "hidden";
		
		print '<h2>';
		print '<div class="row">';
		print '<div class="col-md-12 col-sm-12 col-xs-12">';
		print '<span class="greenHeading">'.JText::_("COM_BIODIV_SCHOOLADMIN_CHECKLIST").'</span>';
		print '</div>'; // col-10
		print '</div>'; // row
		print '</h2>';
		
		$hideClass = "";
		if ( $this->setup ) {
			$hideClass = "hidden";
		}
		print '<div id="checklist" class="schoolAdminSection '.$hideClass.'" >';
		
		if ( $this->allDone ) {
			
			print '<h3 id="setupDone" class="vSpaced">'.JText::_("COM_BIODIV_SCHOOLADMIN_ALL_DONE").'</h3>';
			print '<h3 id="setupNotDone" class="vSpaced hidden">'.JText::_("COM_BIODIV_SCHOOLADMIN_CAN_CONTINUE").'</h3>';
			
		}
		else {

			print '<h3 id="setupDone" class="vSpaced hidden">'.JText::_("COM_BIODIV_SCHOOLADMIN_ALL_DONE").'</h3>';
			print '<h3 id="setupNotDone" class="vSpaced">'.JText::_("COM_BIODIV_SCHOOLADMIN_CAN_CONTINUE").'</h3>';
			
		}
		print '<a href="bes-school-dashboard"><button class="btn btn-lg btn-info vSpaced">'.JText::_("COM_BIODIV_SCHOOLADMIN_CONTINUE").'</button></a>';
		
		// -------------------------------- Checklist School -------------------------------------
		
		print '<div class="panel panel-default checklistPanel" >';
		print '<div class="panel-body">';
		print '<div class="checklistGrid">';
		print '<div class="checklistNumber h3">';
		print '1';
		print '</div>'; // checklistNumber
		print '<div class="checklistText h3">';
		print JText::_("COM_BIODIV_SCHOOLADMIN_CHECKLIST_SCHOOL");
		print '</div>'; // checklistText
		print '<div class="checklistDone h3">';
		if ( $this->schoolSetupDone ) {
			print '<i class="fa fa-check-square-o fa-lg"></i>';
		}
		else {
			print '<div id="schoolSetupDone"><i class="fa fa-square-o fa-lg"></i></div>';
		}
		print '</div>'; // checklistDone
		print '<div class="checklistEdit h3">';
		if ( $this->schoolSetupDone ) {
			print '<button id="schoolSetupButton" class="btn btn-lg btn-info schoolOnly">'.JText::_("COM_BIODIV_SCHOOLADMIN_EDIT").'</button>';
		}
		else {
			print '<button id="schoolSetupButton" class="btn btn-lg btn-primary schoolOnly toSetUp">'.JText::_("COM_BIODIV_SCHOOLADMIN_SET_UP").'</button>';
		}
		print '</div>'; // checklistDone
		print '</div>'; // checklistGrid
		print '</div>'; // panel-body
		print '</div>'; // panel
		
		// ------------------------- Checklist Teachers ----------------------------------
		
		print '<div class="panel panel-default checklistPanel" >';
		print '<div class="panel-body">';
		print '<div class="checklistGrid">';
		print '<div class="checklistNumber h3">';
		print '2';
		print '</div>'; // checklistNumber
		print '<div class="checklistText h3">';
		print JText::_("COM_BIODIV_SCHOOLADMIN_CHECKLIST_TEACHERS");
		print '</div>'; // checklistText
		print '<div class="checklistDone h3">';
		if ( $this->teacherSetupDone ) {
			print '<i class="fa fa-check-square-o fa-lg"></i>';
		}
		else {
			print '<div id="teacherSetupDone"><i class="fa fa-square-o fa-lg"></i></div>';
		}
		print '</div>'; // checklistDone
		print '<div class="checklistEdit h3">';
		if ( $this->teacherSetupDone ) {
			print '<button id="teacherSetupButton" class="btn btn-lg btn-info teachersOnly">'.JText::_("COM_BIODIV_SCHOOLADMIN_EDIT").'</button>';
		}
		else if ( $this->schoolSetupDone ) {
			print '<button id="teacherSetupButton" class="btn btn-lg btn-primary teachersOnly toSetUp">'.JText::_("COM_BIODIV_SCHOOLADMIN_SET_UP").'</button>';
		}
		else {
			print '<button id="teacherSetupButton" class="btn btn-lg btn-info teachersOnly toSetUp">'.JText::_("COM_BIODIV_SCHOOLADMIN_SET_UP").'</button>';
		}
		print '</div>'; // checklistDone
		print '</div>'; // checklistGrid
		print '</div>'; // panel-body
		print '</div>'; // panel
		
		// --------------------------------- Checklist Classes ----------------------
		
		print '<div class="panel panel-default checklistPanel" >';
		print '<div class="panel-body">';
		print '<div class="checklistGrid">';
		print '<div class="checklistNumber h3">';
		print '3';
		print '</div>'; // checklistNumber
		print '<div class="checklistText h3">';
		print JText::_("COM_BIODIV_SCHOOLADMIN_CHECKLIST_CLASSES");
		print '</div>'; // checklistText
		print '<div class="checklistDone h3">';
		if ( $this->classSetupDone ) {
			print '<i class="fa fa-check-square-o fa-lg"></i>';
		}
		else {
			print '<div id="classSetupDone"><i class="fa fa-square-o fa-lg"></i></div>';
		}
		print '</div>'; // checklistDone
		print '<div class="checklistEdit h3">';
		if ( $this->classSetupDone ) {
			print '<button id="classSetupButton" class="btn btn-lg btn-info classesOnly">'.JText::_("COM_BIODIV_SCHOOLADMIN_EDIT").'</button>';
		}
		else if ( $this->teacherSetupDone ) {
			print '<button id="classSetupButton" class="btn btn-lg btn-primary classesOnly toSetUp">'.JText::_("COM_BIODIV_SCHOOLADMIN_SET_UP").'</button>';
		}
		else {
			print '<button id="classSetupButton" class="btn btn-lg btn-info classesOnly toSetUp">'.JText::_("COM_BIODIV_SCHOOLADMIN_SET_UP").'</button>';
		}
		
		print '</div>'; // checklistDone
		print '</div>'; // checklistGrid
		print '</div>'; // panel-body
		print '</div>'; // panel
		
		// -------------------------------- Checklist Students -------------------------------------
		
		print '<div class="panel panel-default checklistPanel" >';
		print '<div class="panel-body">';
		print '<div class="checklistGrid">';
		print '<div class="checklistNumber h3">';
		print '4';
		print '</div>'; // checklistNumber
		print '<div class="checklistText h3">';
		print JText::_("COM_BIODIV_SCHOOLADMIN_CHECKLIST_STUDENTS");
		print '</div>'; // checklistText
		print '<div class="checklistDone h3">';
		if ( $this->studentSetupDone ) {
			print '<i class="fa fa-check-square-o fa-lg"></i>';
		}
		else {
			print '<div id="studentSetupDone"><i class="fa fa-square-o fa-lg"></i></div>';
		}
		print '</div>'; // checklistDone
		print '<div class="checklistEdit h3">';
		if ( $this->studentSetupDone ) {
			print '<button id="studentSetupButton" class="btn btn-lg btn-info studentsOnly">'.JText::_("COM_BIODIV_SCHOOLADMIN_EDIT").'</button>';
		}
		else if ( $this->classSetupDone ) {
			print '<button id="studentSetupButton" class="btn btn-lg btn-primary studentsOnly toSetUp">'.JText::_("COM_BIODIV_SCHOOLADMIN_SET_UP").'</button>';
		}
		else {
			print '<button id="studentSetupButton" class="btn btn-lg btn-info studentsOnly toSetUp">'.JText::_("COM_BIODIV_SCHOOLADMIN_SET_UP").'</button>';
		}
		
		print '</div>'; // checklistDone
		print '</div>'; // checklistGrid
		print '</div>'; // panel-body
		print '</div>'; // panel
		
		
		print '</div>'; //schoolDetailsPanel
	}
	else {
		print '<h2>';
		print '<div class="row">';
		print '<div class="col-md-12 col-sm-12 col-xs-12">';
		print '<span class="greenHeading">'.JText::_("COM_BIODIV_SCHOOLADMIN_HEADING").'</span>';
		print '</div>'; // col-10
		print '</div>'; // row
		print '</h2>';  
	}
	
	// ----------------------------------------- School admin
	
	$hideClass = "";
	if ( $this->checklist && !$this->schoolSetup ) {
		$hideClass = "hidden";
	}
	print '<div id="schoolDetailsPanel" class="'.$hideClass.' schoolAdminSection">';
	print '<div class="panel panel-default schoolAdminPanel" >';
	print '<div class="panel-body">';
	if ( $this->checklist ) {
		print '<div class="btn btn-success doLater" role="button"><i class="fa fa-arrow-left"></i> '.JText::_("COM_BIODIV_SCHOOLADMIN_BACK").'</div>';
	}
	print '<h3 class="heavy vSpaced">'.JText::_("COM_BIODIV_SCHOOLADMIN_DETAILS").'</h3>';
	print '<div class="row">';
	print '<div class="col-md-12">';
	print '<h4>'.JText::_("COM_BIODIV_SCHOOLADMIN_SCHOOL_EXPLAIN").'</h4>';
	if ( $this->checklist ) {
		print '<h4>'.JText::_("COM_BIODIV_SCHOOLADMIN_TAP_SCHOOL_COMPLETE").'</h4>';
		print '<div class="btn btn-info btn-lg vSpaced schoolComplete" role="button" data-toggle="modal">'.JText::_("COM_BIODIV_SCHOOLADMIN_SCHOOL_COMPLETE").'</div>';
	}
	print '</div>'; // col-12
	print '<div id="schoolList" class="col-md-12">';
	Biodiv\SchoolCommunity::printSchoolAdminSchool ( $this->schoolUser );
	print '</div>';
	print '</div>'; // col-12
	print '</div>'; // row
	print '</div>'; // panel-body
	print '</div>'; // panel
	print '</div>'; //schoolDetailsPanel
	
	// -------------------------------- Teacher accounts
	
	$hideClass = "";
	if ( $this->checklist && !$this->teacherSetup ) {
		$hideClass = "hidden";
	}
	print '<div id="teacherAccountsPanel" class="'.$hideClass.' schoolAdminSection">';
	print '<div class="panel panel-default schoolAdminPanel" >';
	print '<div class="panel-body">';
	if ( $this->checklist ) {
		print '<div class="btn btn-success doLater" role="button"><i class="fa fa-arrow-left"></i> '.JText::_("COM_BIODIV_SCHOOLADMIN_BACK").'</div>';
	}
	print '<h3 class="heavy vSpaced">'.JText::_("COM_BIODIV_SCHOOLADMIN_TEACHER_ACCOUNTS").'</h3>';
	print '<div class="row">';
	print '<div class="col-xs-12">';
	print '<h4>'.JText::_("COM_BIODIV_SCHOOLADMIN_TEACHER_EXPLAIN").'</h4>';
	if ( $this->checklist ) {
		print '<h4>'.JText::_("COM_BIODIV_SCHOOLADMIN_TAP_SCHOOL_COMPLETE").'</h4>';
	}
	print '</div>'; // col-12
	print '<div class="col-xs-12">';
	print '<div id="addTeacher" class="btn btn-primary btn-lg vSpaced addTeacher" role="button" data-toggle="modal" data-target="#addSchoolUserModal">'.JText::_("COM_BIODIV_SCHOOLADMIN_ADD_TEACHER").'</div>';
	if ( $this->checklist ) {
		print '<div class="btn btn-info btn-lg vSpaced hSpaced teacherComplete" role="button" data-toggle="modal">'.JText::_("COM_BIODIV_SCHOOLADMIN_TEACHER_COMPLETE").'</div>';
	}
	else {
		print '<div id="hideTeachers" class="btn btn-info btn-lg vSpaced hSpaced" role="button">'.JText::_("COM_BIODIV_SCHOOLADMIN_HIDE_TEACHERS").'</div>';
		print '<div id="showTeachers" class="btn btn-info btn-lg vSpaced hSpaced hidden" role="button">'.JText::_("COM_BIODIV_SCHOOLADMIN_SHOW_TEACHERS").'</div>';
	}
	print '</div>'; // col-12
	print '<div id="teacherList" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 h4">';
	
	Biodiv\SchoolCommunity::printSchoolAccountTeachers ( $this->schoolUser );
	
	print '</div>'; // col-12
	print '</div>'; // row
	print '</div>'; // panel-body
	print '</div>'; // panel
	print '</div>'; // teacherAccountsPanel
	
	// ----------------------------- Class accounts
	
	$hideClass = "";
	if ( $this->checklist && !$this->classSetup ) {
		$hideClass = "hidden";
	}
	print '<div id="classAccountsPanel" class="'.$hideClass.' schoolAdminSection">';
	print '<div class="panel panel-default schoolAdminPanel" >';
	print '<div class="panel-body">';
	if ( $this->checklist ) {
		print '<div class="btn btn-success doLater" role="button"><i class="fa fa-arrow-left"></i> '.JText::_("COM_BIODIV_SCHOOLADMIN_BACK").'</div>';
	}
	print '<h3 class="heavy vSpaced">'.JText::_("COM_BIODIV_SCHOOLADMIN_CLASS_ACCOUNTS").'</h3>';
	print '<div class="row">';
	print '<div class="col-xs-12">';
	print '<h4>'.JText::_("COM_BIODIV_SCHOOLADMIN_CLASS_EXPLAIN").'</h4>';
	if ( $this->checklist ) {
		print '<h4>'.JText::_("COM_BIODIV_SCHOOLADMIN_TAP_CLASS_COMPLETE").'</h4>';
	}
	print '</div>'; // col-12
	print '<div class="col-xs-12">';
	print '<div id="addClass" class="btn btn-primary btn-lg vSpaced addClass" role="button" data-toggle="modal" data-target="#addClassModal">'.JText::_("COM_BIODIV_SCHOOLADMIN_ADD_CLASS").'</div>';
	print '<div id="resetClasses" class="btn btn-info btn-lg vSpaced hSpaced" role="button" data-toggle="modal" data-target="#resetClassesModal">'.JText::_("COM_BIODIV_SCHOOLADMIN_RESET").'</div>';
	if ( $this->checklist ) {
		print '<div class="btn btn-info btn-lg vSpaced classComplete" role="button" data-toggle="modal">'.JText::_("COM_BIODIV_SCHOOLADMIN_CLASS_COMPLETE").'</div>';
	}
	else {
		print '<div id="hideClasses" class="btn btn-info btn-lg vSpaced" role="button">'.JText::_("COM_BIODIV_SCHOOLADMIN_HIDE_CLASSES").'</div>';
		print '<div id="showClasses" class="btn btn-info btn-lg vSpaced hidden" role="button">'.JText::_("COM_BIODIV_SCHOOLADMIN_SHOW_CLASSES").'</div>';
	}
	print '</div>'; // col-12
	print '<div id="classList" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 h4">';
	
	Biodiv\SchoolCommunity::printSchoolAccountClasses ( $this->schoolUser );
	
	print '</div>'; // col-12
	print '</div>'; // row
	print '</div>'; // panel-body
	print '</div>'; // panel
	print '</div>'; // classAccountsPanel
	
	// ------------------------------- Student accounts
	
	$hideClass = "";
	if ( $this->checklist && !$this->studentSetup ) {
		$hideClass = "hidden";
	}
	print '<div id="studentAccountsPanel" class="'.$hideClass.' schoolAdminSection">';
	print '<div class="panel panel-default schoolAdminPanel" >';
	print '<div class="panel-body">';
	print '<div class="row">';
	print '<div class="col-xs-12">';
	if ( $this->checklist ) {
		print '<div class="btn btn-success doLater" role="button"><i class="fa fa-arrow-left"></i> '.JText::_("COM_BIODIV_SCHOOLADMIN_BACK").'</div>';
	}
	print '<h3 class="heavy vSpaced">'.JText::_("COM_BIODIV_SCHOOLADMIN_STUDENT_ACCOUNTS").'</h3>';
	print '</div>';
	print '<div class="col-xs-12">';
	print '<h4>'.JText::_("COM_BIODIV_SCHOOLADMIN_STUDENT_EXPLAIN").'</h4>';
	if ( $this->checklist ) {
		print '<h4>'.JText::_("COM_BIODIV_SCHOOLADMIN_TAP_STUDENT_COMPLETE").'</h4>';
	}
	print '</div>'; // col-12
	print '<div class="col-xs-12">';
	print '<div id="addStudent" class="btn btn-primary btn-lg vSpaced addStudent" role="button" data-toggle="modal" data-target="#addSchoolUserModal">'.JText::_("COM_BIODIV_SCHOOLADMIN_ADD_STUDENT").'</div>';
	print '<div id="batch" class="btn btn-info btn-lg vSpaced hSpaced batchAddStudents" role="button" data-toggle="modal" data-target="#batchStudentsModal">'.JText::_("COM_BIODIV_SCHOOLADMIN_BATCH_ADD").'</div>';
	if ( $this->checklist ) {
		print '<div class="btn btn-info btn-lg vSpaced studentComplete" role="button" data-toggle="modal">'.JText::_("COM_BIODIV_SCHOOLADMIN_STUDENT_COMPLETE").'</div>';
	}
	else {
		print '<div id="hideStudents" class="btn btn-info btn-lg vSpaced " role="button">'.JText::_("COM_BIODIV_SCHOOLADMIN_HIDE_STUDENTS").'</div>';
		print '<div id="showStudents" class="btn btn-info btn-lg vSpaced hidden" role="button">'.JText::_("COM_BIODIV_SCHOOLADMIN_SHOW_STUDENTS").'</div>';
	}
	print '</div>'; // col-12
	print '<div id="studentList" class="col-lg-11 col-md-11 col-sm-11 col-xs-12 h4">';
	
	Biodiv\SchoolCommunity::printSchoolAccountStudents ( $this->schoolUser );
	
	print '</div>'; // col-10
	
	print '</div>'; // row
	print '</div>'; // panel-body
	print '</div>'; // panel
	print '</div>'; // studentAccountsPanel
	
	
	print '</div>'; // displayArea
	



	print '<div id="helpModal" class="modal fade" role="dialog">';
	print '  <div class="modal-dialog"  >';

	print '    <!-- Modal content-->';
	print '    <div class="modal-content">';
	print '      <div class="modal-header text-right">';
	print '        <div type="button" role="button" class="closeButton h3" data-dismiss="modal">&times;</div>';
	print '      </div>';
	print '     <div class="modal-body">';
	print '	    <div id="helpArticle" ></div>';
	print '      </div>';
	print '	  <div class="modal-footer">';
	print '        <button type="button" class="btn btn-info" data-dismiss="modal">'.JText::_("COM_BIODIV_SCHOOLADMIN_CLOSE").'</button>';
	print '      </div>';
			  
	print '    </div>';

	print '  </div>';
	print '</div>';



	// -------------------------------------- add school user 

	print '<div id="addSchoolUserModal" class="modal fade" role="dialog">';
	print '  <div class="modal-dialog"  >';
	print '    <!-- Modal content-->';
	print '    <div class="modal-content">';
	print '    <form id="addSchoolUserForm">';
	print '      <div class="modal-header">';
	print '        <button type="button" class="close" data-dismiss="modal">&times;</button>';
	print '        <h4 class="modal-title">'.JText::_("COM_BIODIV_SCHOOLADMIN_ADD_USER").'</h4>';
	print '      </div>';
	print '     <div class="modal-body">';
	print '<input id="suRoleId" type="hidden" name="suRoleId" value="0"/>';

	print '<div class="vSpaced">';
	print '<label for="suName"> '.JText::_("COM_BIODIV_SCHOOLADMIN_NAME").'</label>';
	print '<input type="text" id="suName"  name="suName">';
	print '</div>';

	print '<div class="vSpaced hiddenTeacher">';
	print '<label for="suClassId"> '.JText::_("COM_BIODIV_SCHOOLADMIN_CLASS").'</label>';
	print '<select id = "suClassId" name = "suClassId" class = "form-control">';
	print '<option value="0">'.JText::_("COM_BIODIV_SCHOOLADMIN_NO_CLASS").'</option>';			
	foreach( $this->classes as $nextClass ){
		print '<option value="'.$nextClass->class_id.'">'.$nextClass->name.'</option>';
	}
	print '</select>';
	print '</div>';

	print '<div class="vSpaced">';
	print '<label for="suUsername"> '.JText::_("COM_BIODIV_SCHOOLADMIN_USERNAME").'</label>';
	print '<input type="text" id="suUsername"  name="suUsername">';
	print '</div>';

	print '<div class="vSpaced">';
	print '<label for="suEmail"> '.JText::_("COM_BIODIV_SCHOOLADMIN_EMAIL").'</label>';
	print '<p class = "hiddenTeacher">'.JText::_("COM_BIODIV_SCHOOLADMIN_EMAIL_BLANK").'</p>';
	print '<input type="email" id="suEmail"  name="suEmail">';
	print '</div>';

	print '<div class="vSpaced">';
	print '<label for="suEmail2"> '.JText::_("COM_BIODIV_SCHOOLADMIN_EMAIL2").'</label>';
	print '<input type="email" id="suEmail2"  name="suEmail2">';
	print '</div>';

	print '<div class="vSpaced">';
	print '<label for="suPassword"> '.JText::_("COM_BIODIV_SCHOOLADMIN_PASSWORD").'</label>';
	print '<input type="password" id="suPassword"  name="suPassword">';
	print '</div>';

	print '<div class="vSpaced">';
	print '<label for="suPassword2"> '.JText::_("COM_BIODIV_SCHOOLADMIN_PASSWORD2").'</label>';
	print '<input type="password" id="suPassword2"  name="suPassword2">';
	print '</div>';

	print '<div id="addUserFailMessage" class="vSpaced"></div>';

	print '</div>';

	print '	  <div class="modal-footer">';
	print '        <button type="submit" class="btn btn-primary">'.JText::_("COM_BIODIV_SCHOOLADMIN_SAVE").'</button>';
	print '        <button type="button" class="btn btn-info" data-dismiss="modal">'.JText::_("COM_BIODIV_SCHOOLADMIN_CANCEL").'</button>';
	print '      </div>';
	print '</form>';	  	  
	print '    </div>'; // modalContent
	print '  </div>';
	print '</div>';



	// -------------------------------------- batch create users 

	if ( $this->schoolUser->role_id == Biodiv\SchoolCommunity::TEACHER_ROLE ) {
		print '<div id="batchStudentsModal" class="modal fade" role="dialog">';
		print '  <div class="modal-dialog"  >';
		print '    <!-- Modal content-->';
		print '    <div class="modal-content">';
		print '    <form id="batchStudents">';
		print '      <div class="modal-header">';
		print '        <button type="button" class="close" data-dismiss="modal">&times;</button>';
		print '        <h4 class="modal-title">'.JText::_("COM_BIODIV_SCHOOLADMIN_BATCH_USERS").'</h4>';
		print '      </div>';
		print '     <div class="modal-body">';

		echo JHtml::_( 'form.token' );

		$schoolNameArray = explode(' ', $this->schoolUser->school);
		$schoolInitials = "";
		foreach ( $schoolNameArray as $word ) {
			$schoolInitials .= $word[0];
		}
		error_log ( "schoolInitials = " . $schoolInitials );

		print '<input id="tandCsChecked" type="hidden" name="tandCsChecked" value="1"/>';
		
		print '<input id="fileStem" type="hidden" name="fileStem" value="batchstudents"/>';

		print '<input id="emailDomain" type="hidden" name="emailDomain" value="'.$schoolInitials.$this->schoolUser->school_id.$this->domainDefault.'"/>';
		
		print '<input id="userGroup" type="hidden" name="userGroup" value="'.$this->studentGroup.'"/>';
		
		print '<input id="project" type="hidden" name="project" value="'.$this->schoolUser->project_id.'"/>';
		
		print '<input id="addToSchool" type="hidden" name="addToSchool" value="1"/>';
		
		print '<input id="school" type="hidden" name="school" value="'.$this->schoolUser->school_id.'"/>';

		print '<div class="vSpaced ">';
		print '<label for="batchClassId"> '.JText::_("COM_BIODIV_SCHOOLADMIN_CLASS").'</label>';
		print '<select id = "batchClassId" name = "batchClassId" class = "form-control">';
		print '<option value="0">'.JText::_("COM_BIODIV_SCHOOLADMIN_NO_CLASS").'</option>';			
		foreach( $this->classes as $nextClass ){
			print '<option value="'.$nextClass->class_id.'">'.$nextClass->name.'</option>';
		}
		print '</select>';
		print '</div>';

		print '<div class="vSpaced">';
		print '<label for="userStem">'.JText::_("COM_BIODIV_SCHOOLADMIN_USER_STEM").'</label>';
		print '  <input type="text" id="userStem" name="userStem">';
		print '</div>';

		print '<div class="vSpaced">';
		print '<label for="passwordStem">'.JText::_("COM_BIODIV_SCHOOLADMIN_PWD_STEM").'</label>';
		print '  <input type="text" id="passwordStem" name="passwordStem">';
		print '</div>';

		print '<div class="vSpaced">';
		print '<label for="numUsers">'.JText::_("COM_BIODIV_SCHOOLADMIN_NUM_USERS").'</label>';
		print '  <input type="number" id="numUsers" name="numUsers" min="1" max="30">';
		print '</div>';

		print '<div id="newUsersMsg" class="vSpaced"></div>';

		print '<div id="newUsers" class="vSpaced"></div>';

		print '</div>';

		print '	  <div class="modal-footer">';
		print '        <button id="newUsersSubmit" type="submit" class="btn btn-primary">'.JText::_("COM_BIODIV_SCHOOLADMIN_SAVE").'</button>';
		print '        <button type="button" class="btn btn-info reloadPage" data-dismiss="modal">'.JText::_("COM_BIODIV_SCHOOLADMIN_CLOSE").'</button>';
		print '      </div>';
		print '</form>';	  	  
		print '    </div>'; // modalContent
		print '  </div>';
		print '</div>';
	}



	print '<div id="addClassModal" class="modal fade" role="dialog">';
	print '  <div class="modal-dialog"  >';
	print '    <!-- Modal content-->';
	print '    <div class="modal-content">';
	print '    <form id="addClassForm" >';
	print '      <div class="modal-header">';
	print '        <button type="button" class="close" data-dismiss="modal">&times;</button>';
	print '        <h4 class="modal-title">'.JText::_("COM_BIODIV_SCHOOLADMIN_ADD_CLASS").'</h4>';
	print '      </div>';
	print '     <div class="modal-body">';
	print '<input id="roleId" type="hidden" name="roleId" value="0"/>';
	print '<div class="vSpaced">';
	print '<label for="className"> '.JText::_("COM_BIODIV_SCHOOLADMIN_NAME").'</label>';
	print '<input type="text" id="className"  name="className">';
	print '</div>';
	print '<div class="vSpaced">';
	print '<label for="classAvatar">'.JText::_("COM_BIODIV_SCHOOLADMIN_AVATAR").'</label>';
	print '<select name="classAvatar" id="classAvatar">';
	foreach ($this->avatars as $avatar) {
		print ' <option value="'.$avatar->avatar_id.'">'.$avatar->name.'</option>';
	}
	print '</select>';
	print '</div>';
	print '<div id="addClassFailMessage" class="vSpaced"></div>';
	print '</div>';
	print '	  <div class="modal-footer">';
	print '        <button type="submit" class="btn btn-primary">'.JText::_("COM_BIODIV_SCHOOLADMIN_SAVE").'</button>';
	print '        <button type="button" class="btn btn-info" data-dismiss="modal">'.JText::_("COM_BIODIV_SCHOOLADMIN_CANCEL").'</button>';
	print '      </div>';
	print '</form>';	  	  
	print '    </div>'; // modalContent
	print '  </div>';
	print '</div>';



	print '<div id="resetClassesModal" class="modal fade" role="dialog">';
	print '  <div class="modal-dialog"  >';
	print '    <!-- Modal content-->';
	print '    <div class="modal-content">';
	print '    <form id="resetClassesForm" >';
	print '      <div class="modal-header">';
	print '        <button type="button" class="close" data-dismiss="modal">&times;</button>';
	print '        <h4 class="modal-title">'.JText::_("COM_BIODIV_SCHOOLADMIN_RESET").'</h4>';
	print '      </div>';
	print '     <div class="modal-body">';

	print '<div class="vSpaced">';
	print '<h3>'.JText::_("COM_BIODIV_SCHOOLADMIN_SURE_RESET").'</h3>';
	print '        <button type="submit" class="btn btn-primary btn-lg vSpaced">'.JText::_("COM_BIODIV_SCHOOLADMIN_RESET_CONFIRM").'</button>';
	print '</div>';
	print '</div>';
	print '	  <div class="modal-footer">';

	print '        <button type="button" class="btn btn-info" data-dismiss="modal">'.JText::_("COM_BIODIV_SCHOOLADMIN_CANCEL").'</button>';
	print '      </div>';
	print '</form>';	  	  
	print '    </div>'; // modalContent
	print '  </div>';
	print '</div>';



	print '<div id="editSchoolModal" class="modal fade" role="dialog">';
	print '  <div class="modal-dialog"  >';
	print '    <!-- Modal content-->';
	print '    <div class="modal-content">';
	print '      <div class="modal-header">';
	print '        <button type="button" class="close" data-dismiss="modal">&times;</button>';
	print '        <h4 class="modal-title">'.JText::_("COM_BIODIV_SCHOOLADMIN_EDIT_SCHOOL").' <span id="titleSchoolName"></span></h4>';
	print '      </div>';
	print '     <div class="modal-body">';
	print '<form id="editSchoolForm" action="'. BIODIV_ROOT . '&task=edit_school" method="post">';
	print '<input id="schoolId" type="hidden" name="schoolId" value="'.$this->schoolUser->school_id.'"/>';
	print '<div class="h3 vSpaced">'.JText::_("COM_BIODIV_SCHOOLADMIN_EDIT_SCHOOL_NAME").'</div>';
	print '<div class="form-group">';
	print '<label for="schoolName"> '.JText::_("COM_BIODIV_SCHOOLADMIN_NAME").'</label>';
	print '<input type="text" id="schoolName"  name="schoolName" class="form-control" value="'.$this->schoolUser->school.'">';
	print '</div>';
	print '<div id="editSchoolFailMessage" class="vSpaced"></div>';
	print '<button type="submit" class="btn btn-info btn-lg " >'.JText::_("COM_BIODIV_SCHOOLADMIN_UPDATE_SCHOOL").'</button>';
	print '</form>';	  	  
	print '<hr/>';

	print '<div class="h3 vSpaced">'.JText::_("COM_BIODIV_SCHOOLADMIN_ADD_LOGO").'</div>';

	print '<button id="uploadSchoolLogo" href="#uploadFilesSection" type="button" class="btn btn-primary btn-lg vSpaced" data-toggle="collapse">'.JText::_("COM_BIODIV_SCHOOLADMIN_UPLOAD_LOGO").'</button>';
		
	print '<div id="uploadFilesSection" class="collapse" >';
	print '<div class="h4 vSpaced">'.JText::_("COM_BIODIV_SCHOOLADMIN_UPLOAD_HERE").'</div>';
	print '<div id="logoErrorMessage" class="vSpaced"></div>';
	print '<div id="uploadFiles vSpaced">';
	print '<button id="resourceuploader" >'.JText::_("COM_BIODIV_SCHOOLADMIN_UPLOAD_HERE").'</button>';
	print '<div id="fileuploadspinner"  style="display:none"><i class="fa fa-spinner fa-spin fa-4x"></i></div>';
	print '</div>';
	print '</div>';
		
	print '      </div>';
	print '	  <div class="modal-footer">';
	print '        <button type="button" class="btn btn-info" data-dismiss="modal">'.JText::_("COM_BIODIV_SCHOOLADMIN_CANCEL").'</button>';
	print '      </div>';
	print '    </div>'; // modalContent
	print '  </div>';
	print '</div>';



	print '<div id="editTeacherModal" class="modal fade" role="dialog">';
	print '  <div class="modal-dialog"  >';
	print '    <!-- Modal content-->';
	print '    <div class="modal-content">';
	print '    <form id="editTeacherForm" action="'. BIODIV_ROOT . '&task=edit_teacher" method="post">';
	print '      <div class="modal-header">';
	print '        <button type="button" class="close" data-dismiss="modal">&times;</button>';
	print '        <h4 class="modal-title">'.JText::_("COM_BIODIV_SCHOOLADMIN_EDIT_TEACHER").' <span id="teacherUsername"></span></h4>';
	print '      </div>';
	print '     <div class="modal-body">';
	print '<input id="teacherId" type="hidden" name="teacherId" value="0"/>';
	print '<div class="vSpaced">';
	print '<label for="teacherName"> '.JText::_("COM_BIODIV_SCHOOLADMIN_NAME").'</label>';
	print '<input type="text" id="teacherName"  name="teacherName">';
	print '</div>';
	// print '<div class="vSpaced">';
	// print '<label for="password"> '.JText::_("COM_BIODIV_SCHOOLADMIN_PASSWORD").'</label>';
	// print '<input type="password" id="password"  name="password">';
	// print '</div>';
	print '<div class="vSpaced">';
	print '<div><label for="teacherActive"> '.JText::_("COM_BIODIV_SCHOOLADMIN_INCLUDE_POINTS").'</label></div>';
	print '<input type="checkbox" id="teacherActive" name="teacherActive" value="1">';
	print '</div>';
	print '      </div>';
	print '	  <div class="modal-footer">';
	print '        <button type="submit" class="btn btn-primary">'.JText::_("COM_BIODIV_SCHOOLADMIN_SAVE").'</button>';
	print '        <button type="button" class="btn btn-info" data-dismiss="modal">'.JText::_("COM_BIODIV_SCHOOLADMIN_CANCEL").'</button>';
	print '      </div>';
	print '</form>';	  	  
	print '    </div>'; // modalContent
	print '  </div>';
	print '</div>';




	print '<div id="editClassModal" class="modal fade" role="dialog">';
	print '  <div class="modal-dialog"  >';
	print '    <!-- Modal content-->';
	print '    <div class="modal-content">';
	print '    <form id="editClassForm" action="'. BIODIV_ROOT . '&task=edit_class" method="post">';
	print '      <div class="modal-header">';
	print '        <button type="button" class="close" data-dismiss="modal">&times;</button>';
	print '        <h4 class="modal-title">'.JText::_("COM_BIODIV_SCHOOLADMIN_EDIT_CLASS").' <span id="titleClassName"></span></h4>';
	print '      </div>';
	print '     <div class="modal-body">';
	print '<input id="classId" type="hidden" name="classId" value="0"/>';
	print '<div class="vSpaced">';
	print '<label for="editClassName"> '.JText::_("COM_BIODIV_SCHOOLADMIN_NAME").'</label>';
	print '<input type="text" id="editClassName"  name="editClassName">';
	print '</div>';
	print '<div class="vSpaced">';
	print '<label for="editClassAvatar">'.JText::_("COM_BIODIV_SCHOOLADMIN_AVATAR").'</label>';
	print '<select name="editClassAvatar" id="editClassAvatar">';
	foreach ($this->avatars as $avatar) {
		print ' <option value="'.$avatar->avatar_id.'">'.$avatar->name.'</option>';
	}
	print '</select>';
	print '</div>';
	print '<div class="vSpaced">';
	print '<div><label for="classActive"> '.JText::_("COM_BIODIV_SCHOOLADMIN_INCLUDE_POINTS").'</label></div>';
	print '<input type="checkbox" id="classActive" name="classActive" value="1">';
	print '</div>';
	print '      </div>';
	print '	  <div class="modal-footer">';
	print '        <button type="submit" class="btn btn-primary">'.JText::_("COM_BIODIV_SCHOOLADMIN_SAVE").'</button>';
	print '        <button type="button" class="btn btn-info" data-dismiss="modal">'.JText::_("COM_BIODIV_SCHOOLADMIN_CANCEL").'</button>';
	print '      </div>';
	print '</form>';	  	  
	print '    </div>'; // modalContent
	print '  </div>';
	print '</div>';




	print '<div id="editStudentModal" class="modal fade" role="dialog">';
	print '  <div class="modal-dialog"  >';
	print '    <!-- Modal content-->';
	print '    <div class="modal-content">';
	print '    <form id="editStudentForm" action="'. BIODIV_ROOT . '&task=edit_student" method="post">';
	print '      <div class="modal-header">';
	print '        <button type="button" class="close" data-dismiss="modal">&times;</button>';
	print '        <h4 class="modal-title">'.JText::_("COM_BIODIV_SCHOOLADMIN_EDIT_STUDENT").' <span id="studentUsername"></span></h4>';
	print '      </div>';
	print '     <div class="modal-body">';
	print '<input id="studentId" type="hidden" name="studentId" value="0"/>';

	print '<div class="vSpaced">';
	print '<label for="studentName"> '.JText::_("COM_BIODIV_SCHOOLADMIN_NAME").'</label>';
	print '<input type="text" id="studentName"  name="studentName">';
	print '</div>';

	print '<div class="vSpaced">';
	print '<label for="studentClass"> '.JText::_("COM_BIODIV_SCHOOLADMIN_CLASS").'</label>';
	print '<select id = "studentClass" name = "studentClass" class = "form-control">';
	print '<option value="0">'.JText::_("COM_BIODIV_SCHOOLADMIN_NO_CLASS").'</option>';					
	foreach( $this->classes as $nextClass ){
		print '<option value="'.$nextClass->class_id.'">'.$nextClass->name.'</option>';
	}
	print '</select>';
	print '</div>';

	print '<div class="vSpaced">';
	print '<label for="password"> '.JText::_("COM_BIODIV_SCHOOLADMIN_PASSWORD").'</label>';
	print '<input type="password" id="password"  name="password">';
	print '</div>';

	print '<div class="vSpaced">';
	print '<label for="password2"> '.JText::_("COM_BIODIV_SCHOOLADMIN_CONFIRM_PWD").'</label>';
	print '<input type="password" id="password2"  name="password2">';
	print '</div>';

	print '<div class="vSpaced">';
	print '<div><label for="studentActive"> '.JText::_("COM_BIODIV_SCHOOLADMIN_INCLUDE_POINTS").'</label></div>';
	print '<input type="checkbox" id="studentActive" name="studentActive" value="1">';
	print '</div>';

	print '   </div>';
	print '	  <div class="modal-footer">';
	print '        <button type="submit" class="btn btn-primary">'.JText::_("COM_BIODIV_SCHOOLADMIN_SAVE").'</button>';
	print '        <button type="button" class="btn btn-info" data-dismiss="modal">'.JText::_("COM_BIODIV_SCHOOLADMIN_CANCEL").'</button>';
	print '      </div>';
	print '</form>';	  	  
	print '    </div>'; // modalContent
	print '  </div>';
	print '</div>';

}


JHTML::script("com_biodiv/commonbiodiv.js", true, true);
JHTML::script("com_biodiv/commondashboard.js", true, true);
JHTML::script("com_biodiv/schooladmin.js", true, true);
JHTML::script("jquery-upload-file/jquery.uploadfile.min.js", false, true);



?>





