<?php 

if ( !$this->personId ) {
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_ADDSCHOOLUSER_LOGIN").'</div>';
}
else {
	
	if ( array_key_exists ( "errors", $this->editUserResult ) && (count($this->editUserResult["errors"]) > 0) ) {
		error_log ( "Found errors" );
		foreach ( $this->editUserResult["errors"] as $message ) {
			print '<p>'.$message["error"].'</p>';
		}
	}
	if ( $this->schoolId ) {
		
		Biodiv\SchoolCommunity::printSchoolAdminSchool ( $this->schoolUser );
				
	}
	else if ( $this->teacherId ) {
		
		Biodiv\SchoolCommunity::printSchoolAccountTeachers ( $this->schoolUser );
				
	}
	else if ( $this->classId ) {
		
		Biodiv\SchoolCommunity::printSchoolAccountClasses ( $this->schoolUser );
				
	}
	else if ( $this->studentId ) {
		
		Biodiv\SchoolCommunity::printSchoolAccountStudents ( $this->schoolUser );			
	}
	
}
 
?>