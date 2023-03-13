<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

if ( $this->school ) {
	Biodiv\SchoolCommunity::printSchoolAdminSchool ( $this->schoolUser );
}
else if ( $this->teacher ) {
	Biodiv\SchoolCommunity::printSchoolAccountTeachers ( $this->schoolUser );
}
else if ( $this->classGrid ) {
	Biodiv\SchoolCommunity::printSchoolAccountClasses ( $this->schoolUser );
}
else if ( $this->student ) {
	Biodiv\SchoolCommunity::printSchoolAccountStudents ( $this->schoolUser );
}

?>