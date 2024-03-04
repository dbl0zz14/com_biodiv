<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*/
 
// No direct access to this file
defined('_JEXEC') or die;
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
* HTML View class for the MammalWeb Component
*
*/
class BioDivViewRequestAnalysis extends JViewLegacy
{
	
	
	/**
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */

	public function display($tpl = null) 
	{
		$app = JFactory::getApplication();
		
		$input = $app->input;
			
		$this->test = $input->getInt('test', 0);
		$this->type = $input->getString('type', 0);
		
		
		$this->aiDate = $input->getString('date', 0);
		
		$this->aiDateStart = $input->getString('start', 0);
		$this->aiDateEnd = $input->getString('end', 0);
		
		
		if ( !$this->aiDate && !$this->aiDateStart && !$this->aiDateEnd ) {
			
			$this->aiDate = date('Ymd', strtotime("yesterday"));
			error_log ( "Using date " . $this->aiDate );
			
		}
		
		$this->sequences = array();
		
		$db = JDatabase::getInstance(dbOptions());
		$query = $db->getQuery(true);
			
		if ( $this->aiDate ) {
			
			//error_log ( "Using date " . $this->aiDate );
			
			$aiStr = "(SELECT CONCAT('[', GROUP_CONCAT(C2.species_id SEPARATOR ','), ']') FROM Classify C2 where C2.sequence_id = C.sequence_id GROUP By C2.sequence_id)";
			
			$query->select("C.origin, C.model, C.sequence_id, ".$aiStr." as ai_species, CONCAT('[', GROUP_CONCAT(A.species SEPARATOR ','), ']') as human_species")
				->from("Classify C")
				->innerJoin("Photo P on P.photo_id = C.photo_id")
				->innerJoin("Animal A on A.photo_id = P.photo_id")
				->where("C.origin = " . $db->quote('CAI') )
				->where("(DATE(C.timestamp) = " . $db->quote($this->aiDate) . 
						" or DATE(A.timestamp) = " . $db->quote($this->aiDate) . ")" )
				->where("C.sequence_id not in (select RT.sequence_id from AnalysisRThumb RT where RT.ai_type = C.origin and RT.ai_version = C.model)")
				->group("C.sequence_id, A.person_id")
				->order("A.animal_id");
			
			//error_log("RequestAI select query created: " . $query->dump());
		}
		else if ( $this->aiDateStart and $this->aiDateEnd ) {
			
			//error_log ( "Using start date " . $this->aiDateStart . " and end date " . $this->aiDateEnd );
			
			$aiStr = "(SELECT CONCAT('[', GROUP_CONCAT(C2.species_id SEPARATOR ','), ']') FROM Classify C2 where C2.sequence_id = C.sequence_id GROUP By C2.sequence_id)";
			
			$query->select("C.origin, C.model, C.sequence_id, ".$aiStr." as ai_species, CONCAT('[', GROUP_CONCAT(A.species SEPARATOR ','), ']') as human_species")
				->from("Classify C")
				->innerJoin("Photo P on P.photo_id = C.photo_id")
				->innerJoin("Animal A on A.photo_id = P.photo_id")
				->where("C.origin = " . $db->quote('CAI') )
				->where("(DATE(C.timestamp) between " . $db->quote($this->aiDateStart) . " and " . $db->quote($this->aiDateEnd) . " or DATE(A.timestamp) between " . $db->quote($this->aiDateStart) . " and " . $db->quote($this->aiDateEnd) . ")" )
				->where("C.sequence_id not in (select RT.sequence_id from AnalysisRThumb RT where RT.ai_type = C.origin and RT.ai_version = C.model)")
				->group("C.sequence_id, A.person_id")
				->order("A.animal_id");
			
			//error_log("RequestAI select query created: " . $query->dump());
		}
		else {
			error_log ( "RequestAI: Start and end dates not correctly set" );
			
		}
		
		$db->setQuery($query); 
				
		$this->sequences = $db->loadObjectList();
		
		parent::display($tpl);
	}
	
}



?>
