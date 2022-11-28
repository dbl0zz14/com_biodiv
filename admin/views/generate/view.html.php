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
* HTML View class for the HelloWorld Component
*
* @since 0.0.1
*/
class BioDivViewGenerate extends JViewLegacy
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

		$input = JFactory::getApplication()->input;
			
		$this->species = $input->getInt('species', 0);
		$this->options = $input->getInt('opt', 0);
		
		$languages = getSupportedLanguages();
		
		if ( $this->species == 1 ) {
			
			$this->title = "Species file for translation";
			
			$options = dbOptions();
			$db = JDatabaseDriver::getInstance($options);
			
			$options = dbOptions();
			
			$db = JDatabase::getInstance(dbOptions());
			
			$query = $db->getQuery(true);
			
			$selectStr = "O.option_id, O.option_name, O.struc, O.article_id";
			
			foreach ($languages as $id=>$lang) {
				$selectStr .= ", ".strtoupper($lang->transifex_code).".value as ".strtolower($lang->transifex_code);
			}
				
			$query->select($selectStr)
				->from("Options O");
				
			foreach ($languages as $id=>$lang) {
				$tableName = strtoupper($lang->transifex_code);
				$query->leftJoin("OptionData ".$tableName." on O.option_id = ".$tableName.".option_id and ".$tableName.".data_type = " . $db->quote($lang->tag));
			}
				
			$query->where("struc in ( 'mammal', 'bird', 'invertebrate' )")
				->order("struc, option_name");

			$db->setQuery($query);
			
			//error_log("generateTranslateOptions select query created " . $query->dump());
			
			$this->speciesToTranslate = $db->loadObjectList("option_id");
			
			$headings = array("Key", "en-GB", "Context"); 
			
			foreach ($languages as $id=>$lang) {
				$headings[] = $lang->transifex_code;
			}
			
			$rows = array();
			
			foreach ( $this->speciesToTranslate as $optionId=>$species ) {
				
				$jarticle = JTable::getInstance("content");

				$jarticle->load($species->article_id); 
			
				$introtext = $jarticle->introtext;
				
				$dom = new DomDocument();
				@ $dom->loadHTML(mb_convert_encoding($introtext, 'HTML-ENTITIES', 'UTF-8'));
				
				$scientificName = "Scientific name not available";
								
				$ps = $dom->getElementsByTagName('p');
				foreach( $ps as $p ) {
					
					$text = $p->textContent;
					
					$sciName = "Scientific name: ";
					
					if ( stripos ( $text, $sciName ) !== false ) {
						
						$scientificName = substr( $text, strlen($sciName) );
					}
				}
				
				$speciesArray = array($optionId, 
									$species->option_name, 
									$scientificName . ' (' . $species->struc . ')' );
									
				foreach ($languages as $id=>$lang) {
					$prop = strtolower($lang->transifex_code);
					$speciesArray[] = $species->$prop;
				}
									
				array_push($rows, $speciesArray);
			}
			
			$this->reportURL = createReportFile ( "SpeciesForTranslation", $headings, $rows );
			
		}
		else if ( $this->options == 1 ) {
			
			$this->title = "Non-species Options file for translation";
			
			$db = JDatabase::getInstance(dbOptions());
		
			$query = $db->getQuery(true);
			
			$selectStr = "O.option_id, O.option_name, O.struc, O.article_id";
			
			foreach ($languages as $id=>$lang) {
				$selectStr .= ", ".strtoupper($lang->transifex_code).".value as ".strtolower($lang->transifex_code);
			}
				
			$query->select($selectStr)
				->from("Options O");
				
			foreach ($languages as $id=>$lang) {
				$tableName = strtoupper($lang->transifex_code);
				$query->leftJoin("OptionData ".$tableName." on O.option_id = ".$tableName.".option_id and ".$tableName.".data_type = " . $db->quote($lang->tag));
			}
						
			$query->where("struc not in ( 'mammal', 'bird', 'invertebrate', 'beshelp', 'camera', 'kiosk', 'kiosktutorial', 'logo', 'projectdisplay' )")
				->order("struc, option_name");

			$db->setQuery($query);
			
			error_log("generateTranslateSpeciesData select query created " . $query->dump());
			
			$this->options = $db->loadObjectList("option_id");
			
			$headings = array("Key", "en-GB", "Context"); 
			
			foreach ($languages as $id=>$lang) {
				$headings[] = $lang->transifex_code;
			}
			
			$rows = array();
			
			foreach ( $this->options as $optionId=>$option ) {
				
				$optionsArray = array($optionId, 
									str_replace(array("\r\n", "\n\r", "\r", "\n"), ' ', str_replace(',', '-', $option->option_name)),
									$option->struc );
									
									
				foreach ($languages as $id=>$lang) {
					$col = strtolower($lang->transifex_code);
					$prop = str_replace(array("\r\n", "\n\r", "\r", "\n"), ' ', str_replace(',', '-', $option->$col));
					$optionsArray[] = $prop;
				}
									
				array_push($rows, $optionsArray);
			}
			
			$this->reportURL = createReportFile ( "OptionsForTranslation", $headings, $rows );
			
		}
		
		
		// Display the view
		parent::display($tpl);
    }
}



?>
