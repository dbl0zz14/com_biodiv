<?php 

if ( !$this->personId ) {
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_ADDSCHOOLUSER_LOGIN").'</div>';
}
else {
	
	//if ( array_key_exists ( "errors", $this->addUserResult ) ) {
	if ( array_key_exists ( "errors", $this->addUserResult ) && (count($this->addUserResult["errors"]) > 0) ) {
		error_log ( "Found errors" );
		foreach ( $this->addUserResult["errors"] as $message ) {
			print '<p>'.$message["error"].'</p>';
		}
	}
	if ( $this->printTeachers ) {
		
		error_log ("Printing teachers");
		
		Biodiv\SchoolCommunity::printSchoolAccountTeachers ( $this->schoolUser );
				
	}
	else if ( $this->printStudents ) {
		
		error_log ("Printing students");
		
		Biodiv\SchoolCommunity::printSchoolAccountStudents ( $this->schoolUser );			
	}
	
}
 
?>