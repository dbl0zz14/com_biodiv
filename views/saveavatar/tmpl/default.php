<?php 


if ( !$this->personId ) {
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_SAVEAVATAR_LOGIN").'</div>';
}
else if($this->avatar ){
	
	print '<div class="row">';
	print '<div class="col-md-2 col-md-offset-5 text-center">';
	
	//print "avatar here";
	print '<h3 class="text-center">'.JText::_("COM_BIODIV_SAVEAVATAR_YOUR_AVATAR").'</h3>';
	print '<img src="'.$this->avatar->image.'" class="img-responsive" alt="'.$this->avatar->name.' avatar" />';
	print '<h3 class="text-center">'.$this->avatar->name.'</h3>';
	
	print '</div>'; // col-2 5
	print '</div>'; // row
	
 }
 
?>