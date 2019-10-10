<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

class Location {
	
	private $lat;
	private $lon;
	private $areaLat;
	private $areaLon;
	
	
	function __construct($lat, $lon)
	{
		$this->lat = $lat;
		$this->lon = $lon;
		$this->areaLat = round($lat, 1);
		$this->areaLon = round($lon, 1);
	}
	
	function getLat () {
		return $this->lat;
	}
	
	function getLon () {
		return $this->lon;
	}
	
	function getAreaLat () {
		return $this->areaLat;
	}
	
	function getAreaLon () {
		return $this->areaLon;
	}
	
	
}

?>