<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;


if ( !$this->personId ) {
	
	// Please log in button
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_COMMUNITYPOSTS_LOGIN").'</div>';
}
else if ( !$this->schoolUser ) {
	
	print '<div type="button" class="list-group-item btn btn-block" >'.JText::_("COM_BIODIV_COMMUNITYPOSTS_NOT_SCH_USER").'</div>';
}

else {
	
	if ( $this->newBadge ) {
		print '<div id="newBadge" data-newbadgeid="'.$this->badge.'"></div>';
	}
	else {
		
		print '<div id="newBadge" ></div>';
	}
	
	print '<div class="row">';
	
	foreach ( $this->posts as $post ) {
		
		print '<div id="postCol_'.$post->set_id.'" class="col-md-4 col-sm-6 col-xs-12 postCol postCol_'.$post->school_id.'">';
		
		$post = new Biodiv\Post ($this->schoolUser, 
								$post->person_id,
								$post->set_id,
								$post->text,
								$post->school_id,
								$post->name,
								$post->image,
								$post->num_likes,
								$post->my_likes,
								$post->files,
								$post->tstamp								
								);
								
		$post->printPost();
		
		print '</div>';
		
	}
	
	print '</div>'; // row
	
	
	if ( $this->numPages > 1 ) {
		
		$firstPageButton = intval(($this->page - 1)/6) * 6 + 1;
		$lastPageButton = min($this->numPages, $firstPageButton + 5);
		
		print '<div class="row">';
		print '<div class="col-md-10 col-md-offset-1">';
		print '<ul class="pagination pagination-lg">';
		if ( $firstPageButton > 1 ) {
			$i = $firstPageButton - 1;
			print '<li><a id="page_'.$i.'" class="newPage" data-school="'.$this->school.'"><i class="fa fa-backward"></i></a></li>';
		}
		for ( $i=$firstPageButton; $i <= $lastPageButton; $i++ ) {
			$activeClass = "";
			if ( $this->page == $i ){
				$activeClass = "active";
			}
			print '<li class="'.$activeClass.'"><a id="page_'.$i.'" class="newPage" data-school="'.$this->school.'">'.JText::_("COM_BIODIV_COMMUNITYPOSTS_PAGE").' '.$i.'</a></li>';
			
		}
		if ( $this->numPages > $lastPageButton ) {
			$i = $lastPageButton + 1;
			print '<li><a id="page_'.$i.'" class="newPage" data-school="'.$this->school.'"><i class="fa fa-forward"></i></a></li>';
		}
		print '</ul>';
		print '</div>';
		print '</div>';
	}
}



?>





