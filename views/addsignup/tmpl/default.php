<?php 

if ( !$this->personId ) {
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_ADDSIGNUP_LOGIN").'</div>';
}
else {
	
	if ( $this->addSignupResult ) {
		print $this->addSignupResult;
	}
	else {
		print "Result is null"
	}
	
}
 
?>