<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

error_log ( "ResourceFile template called" );

if ( !$this->personId ) {
	print '<a type="button" href="'.$this->translations['hub_page']['translation_text'].'" class="list-group-item btn btn-block" >'.$this->translations['login']['translation_text'].'</a>';
}
else if ( $this->resourceId ) {
	
	error_log ( "Got resource id" );
	
	if ( $this->mainType == "image" ) {
		error_log ( "Got img file" );
		print '<img src="'.$this->resourceUrl.'" type="'.$this->resourceFiletype.'" width="100%" height="auto" />';
	}
	else if ( $this->mainType == "video" ) {
		error_log ( "Got video file" );
		print '<video src="'.$this->resourceUrl.'" type="'.$this->resourceFiletype.'" oncontextmenu="return false;" disablePictureInPicture controls controlsList="nodownload noplaybackrate" width="100%" height="auto" ></video>';
	}
	else if ( $this->mainType == "audio" ) {
		error_log ( "Got audio file" );
		print '<audio src="'.$this->resourceUrl.'" type="'.$this->resourceFiletype.'" oncontextmenu="return false;" disablePictureInPicture controls controlsList="nodownload noplaybackrate" width="100%" height="auto" ></audio>';
	}
	else {
		error_log ( "Got other file - " . $this->resourceFiletype );
		if ( strpos($this->resourceFiletype, "word") ) {
			print '<h4 class="text-center">'.$this->translations['word_download']['translation_text'].'</h4>';
			print '<iframe src="'.$this->resourceUrl.'#toolbar=0" type="'.$this->resourceFiletype.'" width="100%" height="0px" hidden ></iframe>';
		}
		else {
			print '<iframe src="'.$this->resourceUrl.'#toolbar=0" type="'.$this->resourceFiletype.'" width="100%" height="600px"></iframe>';
		}
	}
	
	/*
	if ( $this->resourceFiletype == "application/pdf" ) {
		error_log ( "Got pdf file" );
		print '<iframe src="'.$this->resourceUrl.'#toolbar=0" type="'.$this->resourceFiletype.'" width="100%" height="600px" ></iframe>';
	}
	else if ( $this->resourceFiletype == "image/jpeg" ) {
		error_log ( "Got img file" );
		print '<img src="'.$this->resourceUrl.'" type="'.$this->resourceFiletype.'" width="100%" height="auto" />';
	}
	else if ( $this->resourceFiletype == "video/mp4" ) {
		error_log ( "Got video file" );
		print '<video src="'.$this->resourceUrl.'" type="'.$this->resourceFiletype.'" oncontextmenu="return false;" disablePictureInPicture controls controlsList="nodownload noplaybackrate" width="100%" height="auto" ></video>';
	}
	else if ( $this->resourceFiletype == "audio/mp4" ) {
		error_log ( "Got audio mp4 file" );
		print '<audio src="'.$this->resourceUrl.'" type="'.$this->resourceFiletype.'" oncontextmenu="return false;" disablePictureInPicture controls controlsList="nodownload noplaybackrate" width="100%" height="auto" ></audio>';
	}
	else if ( $this->resourceFiletype == "audio/mp3" ) {
		error_log ( "Got audio  mp3bfile" );
		print '<audio src="'.$this->resourceUrl.'" type="'.$this->resourceFiletype.'" oncontextmenu="return false;" disablePictureInPicture controls controlsList="nodownload noplaybackrate" width="100%" height="auto" ></audio>';
	}
	*/
}
else {
	print ('<div class="col-md-12" >'.$this->translations['no_file']['translation_text'].'</div>');
}

//print ('Resource File here');


?>