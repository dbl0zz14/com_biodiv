<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

class Project {
	
	var $name;
	var $id;
	var $description;
	var $level;
	var $priority;
	var $imageFile;
	
	// The parent project of this one
	var $parentProject;
	
	// An array of the children of this project.
	var $childProjects;
	
	
	function __construct($name, $id, $description, $level, $priority, $imageFile)
	{
		$this->name = $name;
		$this->id = $id;
		$this->description = $description;
		$this->level = $level;
		$this->priority = $priority;
		$this->imageFile = $imageFile;
		
		$childProjects = [];
	}
	
	function addChild ( $child ) {
		$childProjects[] = $child;
	}

	
	
}

?>