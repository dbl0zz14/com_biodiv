<?php 

	
	if ( count($this->result["errors"]) == 0 ) {
		print '<h3>'.JText::_("COM_BIODIV_ADDSCHOOLREGISTER_THANKS").'</h3>';
		print '<h4 class="vSpaced">'.JText::_("COM_BIODIV_ADDSCHOOLREGISTER_STEPS").'</h4>';	
		print '<h4 class="vSpaced">'.JText::_("COM_BIODIV_ADDSCHOOLREGISTER_AUTH").'</h4>';	
		print '<h4 class="vSpaced">'.JText::_("COM_BIODIV_ADDSCHOOLREGISTER_SUCCESS").'</h4>';	
	}
	else {
		print '<h4>'.JText::_("COM_BIODIV_ADDSCHOOLREGISTER_FAIL").'</h4>';
		foreach ( $this->result["errors"] as $nextError ) {
			print '<p>'.$nextError["error"].'</p>';
		}
		print '<a href="bes-register"><button class="btn btn-primary btn-lg">'.JText::_("COM_BIODIV_ADDSCHOOLREGISTER_TRY_AGAIN").'</button></a>';
	}
	
	
	
	

 
?>