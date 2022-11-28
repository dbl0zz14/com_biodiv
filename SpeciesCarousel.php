<?php

// No direct access to this file
defined('_JEXEC') or die;

class SpeciesCarousel {
	
	private $filters;
	private $allSpecies;
	private $classifyInputs;
	
	function __construct()
	{
		$this->filters = array();
		
		$this->allSpecies = allSpecies();
		
		
		$this->allSpeciesTran = codes_getList ( "speciestran" );
		
		
		$this->classifyInputs = getClassifyInputs();
	}
	
	function setFilters ( $filters ) {
		$this->filters = $filters;
	}
	
	public static function getAllSpecies () {
		
		error_log ("Static getAllSpecies called");
		
		$db = JDatabase::getInstance(dbOptions());
	
		$speciesArray = null;
		
		// Check the language tag and work differently if English
		$lang = langTag();
		if ( $lang == 'en-GB' ) {
			
			error_log ( "Language is English so no join to OptionData" );
			$query = $db->getQuery(true);
			
			$query->select("O.option_id as id, O.option_name as name")
				->from("Options O")
				->where( "O.struc in ( 'mammal', 'bird', 'invertebrate', 'notinlist' )" );
				
			$db->setQuery($query);
			
			$speciesArray = $db->loadRowList();
			
		}
		else {
			
			$query = $db->getQuery(true);
			
			$query->select("OD.option_id as id, OD.value as name")
				->from("OptionData OD")
				->innerJoin("Options O on O.option_id = OD.option_id and O.struc in ( 'mammal','bird','invertebrate','notinlist' )")
				->where("OD.data_type = " . $db->quote($lang) );
				
			$db->setQuery($query);
			
			$speciesArray = $db->loadRowList();
			
		}
		$err_msg = print_r ( $speciesArray, true );
		error_log ( $err_msg );
		
		return $speciesArray;
	}
	
	function generateSpeciesCarousel () {
		//print "Species carousel...";
		//print_r( $filterIds );
		
		// Search box
		print "<div class='input-group'>";
		print "	<span class='input-group-addon'><i class='glyphicon glyphicon-search'></i></span>";
		print "	<input id='search_species' type='text' class='form-control' name='speciesfilter' placeholder='Search..'>";
		print "</div>";
		
		print "<ul id = 'species-nav' class='nav nav-tabs nav-fill'>";
		$first = true;

		foreach ( $this->filters as $filterId=>$filter ) {
			if ( $first == true ) {
				print "  <li class='nav-link active btn-secondary species-tab'><a data-toggle='tab' href='#filter_${filterId}'>".$filter["label"]."</a></li>";
				$first = false;
			} else {
				print "  <li class='nav-link btn-secondary species-tab'><a data-toggle='tab' href='#filter_${filterId}'>".$filter["label"]."</a></li>";
			}
		}
		print "</ul>";

		print "<div class='tab-content no-padding'>";

		$extra = "active";
		foreach ( $this->filters as $filterId=>$filter ) {
			print "  <div id='filter_${filterId}' class='tab-pane fade in $extra'>";
			print "<div id='carousel-species-${filterId}' class='carousel slide' data-ride='carousel' data-interval='false' data-wrap='false'>";
			//printSpeciesList ( $this->species, true );
			printSpeciesListSearch ( $filterId, $filter['species'], false );
			print "</div> <!-- /carousel-species carousel--> \n";
			print "  </div>";
			$extra = "";
		}
		
		print "</div>";
	}
	
	function generateClassifyModal() {
		
		print "<div class='modal fade' id='classify_modal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>";
		print "	<div class='modal-dialog modal-xl'>";
		print "    <div class='modal-content'>";
		print "      <div class='modal-header'>";
 		print "   <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>".JText::_("COM_BIODIV_CLASSIFY_CLOSE")."</span></button>";
		print "    <h4 class='modal-title' id='myModalLabel'>".JText::_("COM_BIODIV_CLASSIFY_CLASS_ANI")." </h4>";
		print "      </div>";
		print "      <div class='modal-body'>";
		print "        <form id='classify-form' role='form'>";
		print "		  <div id='classify-species'>";


		$err_msg = print_r ( $this->allSpecies, true );
		error_log ( "generateClassifyModal allSpecies = " . $err_msg );
		
		foreach ($this->allSpecies as $stuff) {
			list($species_id, $species_name) = $stuff;
			print "<h2 id='species_header_${species_id}' class='species_header'>" . $species_name."</h2>\n";
		}
		
		error_log ( "Done allSpecies" );

		print "<input type='hidden' name='species' id='species_value'/>\n";
		//print "<input id='currPhotoId' type='hidden' name='photo_id' value='".$this->photo_id."'/>\n";
   
		print "  </div>";
  
		print "  <div class='container-fluid'>";
  
		print "<div class='col-md-9'>\n";
		  
		print "<div id='species_helplet'></div>";

		print "</div>"; // col9

		print "<div class='col-md-3'>\n";
		
		foreach($this->classifyInputs as $formInput){
			print "<div class='row'>\n";
			print "<div class='col-md-12'>\n";
			print "<div class='form-group species_classify'>\n";
			print $formInput;
			print "</div>\n";
			print "</div>\n";
			print "</div> <!-- /.row -->\n";
		}

		print "<hr/>";
		print "<button type='button' class='btn btn-default' data-dismiss='modal'>".JText::_("COM_BIODIV_CLASSIFY_CLOSE")."</button>";
		print "<button type='button' class='btn btn-success' id='classify-save' data-dismiss='modal'>".JText::_("COM_BIODIV_CLASSIFY_SAVE")."</button>";

		print "</div> <!--col3 -->";

 		print "</div> <!-- /.container-fluid -->";
		print "        </form>";
		print "      </div>";

		print "    </div>";
		print "  </div>";


		print "</div>";
	}
}



?>
<?php
//JHTML::stylesheet("com_biodiv/mediacarousel.css", array(), true);
//JHTML::script("com_biodiv/mediacarousel.js", true, true);

?>
