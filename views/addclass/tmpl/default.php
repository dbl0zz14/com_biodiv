<?php 

if ( !$this->personId ) {
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_ADDCLASS_LOGIN").'</div>';
}
else {
	
	if ( array_key_exists ( "errors", $this->addClassResult ) && (count($this->addClassResult["errors"]) > 0) ) {
		foreach ( $this->addClassResult["errors"] as $message ) {
			print '<p>'.$message["error"].'</p>';
		}
	}
		
	Biodiv\SchoolCommunity::printSchoolAccountClasses ( $this->schoolUser );
				
	
}
 
?>