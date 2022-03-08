<?php 


if ( !$this->personId ) {
	print '<a type="button" href="'.JURI::root().'/'.$this->translations['page']['translation_text'].'" class="list-group-item btn btn-block" >'.$this->translations['login']['translation_text'].'</a>';
}
else if($this->avatar ){
	
	print '<div class="row">';
	print '<div class="col-md-2 col-md-offset-5 text-center">';
	
	//print "avatar here";
	print '<h3 class="text-center">'.$this->translations['your_avatar']['translation_text'].'</h3>';
	print '<img src="'.$this->avatar->image.'" class="img-responsive" alt="'.$this->avatar->name.' avatar" />';
	print '<h3 class="text-center">'.$this->avatar->name.'</h3>';
	
	print '</div>'; // col-2 5
	print '</div>'; // row
	
 }
 
?>