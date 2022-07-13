<?php

namespace Biodiv;


// No direct access to this file
defined('_JEXEC') or die;



class Module {
	
	private $id;
	private $name;
	private $description;
	private $image;
	private $icon;
	
	function __construct( $id )
	{
		
	}
	
	
	public static function getModules ( $activeOnly = true ) {
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$selectStr = "* from Module";
		if ( $activeOnly ) {
			$selectStr .= " where active = 1";
		}
		$query = $db->getQuery(true)
		->select($selectStr)
		->order("seq");
	
		$db->setQuery($query);
	
		$modules = $db->loadObjectList("module_id");
		
		return $modules;
	}
	
	
	public static function getModule ( $moduleId ) {
		
		$db = \JDatabaseDriver::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
		->select("* from Module")
		->where("module_id = " . $moduleId);
	
		$db->setQuery($query);
	
		$module = $db->loadObject();
		
		return $module;
	}
	
}



?>

