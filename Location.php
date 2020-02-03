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
		//nb this gives south west corner of the .2 x .1 rectangle the point lies in.
		$this->south = floor($lat*10)/10;
		$this->west = floor($lon*5)/5;
		$this->north = $this->south + 0.1;
		$this->east = $this->west + 0.2;
		
		// Correct for lat/lon falling outside range
		if ( $this->south < -90 ) $this->south += 90;
		if ( $this->west < -180 ) $this->west += 360;
		if ( $this->north > 90 ) $this->north -= 90;
		if ( $this->east > 180 ) $this->east -= 360;
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
	
	function getSouth () {
		return $this->south;
	}
	
	function getWest () {
		return $this->west;
	}
	function getNorth () {
		return $this->north;
	}
	
	function getEast () {
		return $this->east;
	}
	
	
	
}

?>