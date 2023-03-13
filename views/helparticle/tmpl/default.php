<?php 

if ( !$this->personId ) {
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_HELPARTICLE_LOGIN").'</div>';
}
else {
	
	if ( $this->type == "intro" ) {
		print '<h2>'.JText::_("COM_BIODIV_HELPARTICLE_INTRO_TITLE").'</h2>';
	}
	else if ( $this->type == "faqs" ) {
		print '<h2>'.JText::_("COM_BIODIV_HELPARTICLE_FAQS_TITLE").'</h2>';
		print '<h4 class="vSpaced">'.JText::_("COM_BIODIV_HELPARTICLE_FAQS_LINE").'</h3>';
	}
	else if ( $this->type == "contact" ) {
		print '<h2>'.JText::_("COM_BIODIV_HELPARTICLE_CONTACT_TITLE").'</h2>';
	}
	else if (  $this->type == "badges" ) {
		print '<h2>'.$this->title.'</h2>';
		print '<h4 class="vSpaced">'.$this->line.'</h4>';
		print '<a href="'.$this->showLink.'" class="btn btn-primary btn-lg vSpaced">'.JText::_("COM_BIODIV_HELPARTICLE_SHOW").'</a>';
	}
	else if (  $this->type == "resourcehub" ) {
		print '<h2>'.$this->title.'</h2>';
		print '<h4 class="vSpaced">'.$this->line.'</h4>';
		print '<a href="'.$this->showLink.'" class="btn btn-primary btn-lg vSpaced">'.JText::_("COM_BIODIV_HELPARTICLE_SHOW").'</a>';
	}
	else {
		print '<h2>'.$this->title.'</h2>';
	}


	if ( $this->introtext ) {
		print "<div>".$this->introtext."</div>"; 
	}
	
}
 
?>