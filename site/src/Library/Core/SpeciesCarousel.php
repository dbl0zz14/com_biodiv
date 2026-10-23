<?php

namespace MammalWeb\Component\Biodiv\Site\Library\Core;


use Joomla\CMS\HTML\HTMLHelper;


// No direct access to this file
defined('_JEXEC') or die;


use Joomla\CMS\Language\Text;


class SpeciesCarousel {
	
	private $filters;
	private $allSpecies;
	private $allSpeciesTran;
	private $classifyInputs;
	
	function __construct()
	{
		$this->filters = array();
		
		$this->allSpecies = Biodiv::allSpecies();
		
		
		$this->allSpeciesTran = codes_getList ( "speciestran" );
		
		
		$this->classifyInputs = Biodiv::getClassifyInputs();
		//$this->classifyNothingInputs = Biodiv::getClassifyNothingInputs();

	}
	
	function setFilters ( $filters ) {
		$this->filters = $filters;
	}
	
	public static function getAllSpecies () {
		
		$db = db();
	
		$speciesArray = null;
		
		// Check the language tag and work differently if English
		$lang = langTag();
		if ( $lang == 'en-GB' ) {
			
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
		
		return $speciesArray;
	}
	
	function generateSpeciesCarousel () {
		
		// Search box
		// print "<div class='input-group'>";
		// print "	<span class='input-group-addon'><i class='glyphicon glyphicon-search'></i></span>";
		// print "	<input id='search_species' type='text' class='form-control' name='speciesfilter' placeholder='Search..'>";
		// print "</div>";
		
		// print "<ul id = 'species-nav' class='nav nav-tabs' role='tablist'>";
		// $first = true;

		// foreach ( $this->filters as $filterId=>$filter ) {
			// if ( $first == true ) {
				// print "  <li class='nav-item species-tab' role='presentation'><button data-bs-toggle='tab' class='nav-link active' data-bs-target='#filter_{$filterId}' type='button' role='tab'>".$filter["label"]."</button></li>";
				// $first = false;
			// } else {
				// print "  <li class='nav-item species-tab' role='presentation'><button data-bs-toggle='tab' class='nav-link active' data-bs-target='#filter_{$filterId}' type='button' role='tab'>".$filter["label"]."</button></li>";
			// }
		// }
		// print "</ul>";

		// print "<div class='tab-content no-padding'>";

		// $extra = "show active";
		// foreach ( $this->filters as $filterId=>$filter ) {
			// print "  <div id='filter_{$filterId}' class='tab-pane fade $extra' role='tab-panel'>";
			// print "<div id='carousel-species-{$filterId}' class='carousel slide' data-ride='carousel' data-interval='false' data-wrap='false'>";
			// //printSpeciesList ( $this->species, true );
			// Biodiv::printSpeciesListSearch ( $filterId, $filter['species'], false );
			// print "</div> <!-- /carousel-species carousel--> \n";
			// print "  </div>";
			// $extra = "";
		// }
		
		// print "</div>";
		print '<div class="container">';
		print '<div class="row d-flex align-items-stretch">';
		print '<div class="input-group">';
		print '    <span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>';
		print '    <input id="search_species" type="text" class="form-control" name="speciesfilter" placeholder="Search..">';
		print '</div>';
		print '</div>';

		print '<div class="row d-flex align-items-stretch">';
		print "<ul id = 'species-nav' class='nav nav-tabs' role='tablist'>";
		$first = true;

		foreach ( $this->filters as $filterId=>$filter ) {
			if ( $first == true ) {
				print "  <li class='nav-item species-tab' role='presentation'><button data-bs-toggle='tab' class='nav-link active' data-bs-target='#filter_{$filterId}' type='button' role='tab'>".$filter["label"]."</button></li>";
				$first = false;
			} else {
				print "  <li class='nav-item species-tab' role='presentation'><button data-bs-toggle='tab' class='nav-link' data-bs-target='#filter_{$filterId}' type='button' role='tab'>".$filter["label"]."</button></li>";
			}
		}
		print "</ul>";
		print '</div>';

		print "<div class='tab-content no-padding'>";

		$extra = "show active";
		foreach ( $this->filters as $filterId=>$filter ) {
			print "  <div id='filter_{$filterId}' class='tab-pane fade $extra' role='tab-panel'>";
			print "<div id='carousel-species-{$filterId}' class='carousel slide' data-ride='carousel' data-interval='false' data-wrap='false'>";
			
			Biodiv::printSpeciesListSearch ( $filterId, $filter['species'], false );

			print "</div> <!-- /carousel-species carousel--> \n";
			print "  </div>";
			
			$extra = "";
		}

		print "</div>";
		print "</div>";

		
		
		
	}
	
	function generateClassifyModal( $photoId = null ) {
		
		
		print '<div class="modal fade" id="classify_modal" tabindex="-1" aria-labelledby="classifyModalLabel" >';
		print '<div class="modal-dialog modal-dialog-scrollable modal-xl">';
		print '<div class="modal-content">';
		print '<div class="modal-header">';
		print '<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">'.Text::_("COM_BIODIV_CLASSIFY_CLOSE").'</span></button>';
		print '<h4 class="modal-title" id="classifyModalLabel">'.Text::_("COM_BIODIV_CLASSIFY_CLASS_ANI").'</h4>';
		print '</div>';
		print '<div class="modal-body">';
		print '<form id="classify-form" role="form">';
		print HTMLHelper::_('form.token');
		print '<div id="classify-species">';

		foreach ($this->allSpecies as $stuff) {
			list($species_id, $species_name) = $stuff;
			print "<h2 id='species_header_{$species_id}' class='species_header'>" . $species_name."</h2>\n";
		}

		print "<input type='hidden' name='species' id='species_value'/>\n";
		
		if ( $photoId ) {
			print "<input id='currPhotoId' type='hidden' name='photo_id' value='".$photoId."'/>\n";
		}		  

		print '</div>';
		  
		print '<div class="container-fluid">';
		print '<div class="row">';
		print "<div class='col-md-9'>\n";
		  
		print "<div id='species_helplet' class='m-4'></div>";

		print "</div>"; // col9



		print "<div class='col-md-3'>\n";

		  
		foreach($this->classifyInputs as $formInput){
		  print "<div class='row'>\n";
		  print "<div class='col-md-12'>\n";
		  print "<div class='mt-2 mb-1 form-group species_classify'>\n";
		  print $formInput;
		  print "</div>\n";
		  print "</div>\n";
		  print "</div> <!-- /.row -->\n";
		}
		
		print '<hr/>';
		print '<div class="d-flex justify-content-end">';
		print '<button type="button" class="btn btn-outline-secondary m-1 mb-3" data-dismiss="modal">'.Text::_("COM_BIODIV_CLASSIFY_CLOSE").'</button>';
		print '<button type="button" class="btn btn-primary m-1 mb-3 me-4" id="classify-save">'.Text::_("COM_BIODIV_CLASSIFY_SAVE").'</button>';
		print '</div>';
		print '</form>';

		
		print '</div>'; // col-3

		print '</div>'; // row

		
		print ' </div> <!-- /.container-fluid -->';
		
		// print '<hr/>';
		// print '<div class="d-flex justify-content-end">';
		// print '<button type="button" class="btn btn-outline-secondary m-1 mb-3" data-dismiss="modal">'.Text::_("COM_BIODIV_CLASSIFY_CLOSE").'</button>';
		// print '<button type="button" class="btn btn-primary m-1 mb-3 me-4" id="classify-save">'.Text::_("COM_BIODIV_CLASSIFY_SAVE").'</button>';
		// print '</div>';
		// print '</form>';


		print '     </div>'; // modal-body
		// print '       <div class="modal-footer">';

		// print '<button type="button" class="btn btn-default" data-dismiss="modal">'.Text::_("COM_BIODIV_CLASSIFY_CLOSE").'</button>';
		// print '        <button type="button" class="btn btn-success" id="classify-save">'.Text::_("COM_BIODIV_CLASSIFY_SAVE").'</button>';

		// print '       </div>'; // modal-footer
		//print '       </form>';


		print '    </div>'; // modal-content
		print '  </div>'; // modal-dialog


		print '</div>'; // modal





		
		
		
		
		
		// print "<div class='modal fade' id='classify_modal' tabindex='-1' aria-labelledby='classifyModalLabel' aria-hidden='true'>";
		// print "	<div class='modal-dialog modal-dialog-scrollable modal-xl'>";
		// print "    <div class='modal-content'>";
		// print "      <div class='modal-header'>";
 		// print "   <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>".Text::_("COM_BIODIV_CLASSIFY_CLOSE")."</span></button>";
		// print "    <h4 class='modal-title' id='myModalLabel'>".Text::_("COM_BIODIV_CLASSIFY_CLASS_ANI")." </h4>";
		// print "      </div>";
		// print "      <div class='modal-body'>";
		// print "        <form id='classify-form' role='form'>";
		// print "		  <div id='classify-species'>";


		// foreach ($this->allSpecies as $stuff) {
			// list($species_id, $species_name) = $stuff;
			// print "<h2 id='species_header_{$species_id}' class='species_header'>" . $species_name."</h2>\n";
		// }
		
		// print "<input type='hidden' name='species' id='species_value'/>\n";
		// //print "<input id='currPhotoId' type='hidden' name='photo_id' value='".$this->photo_id."'/>\n";
   
		// print "  </div>";
  
		// print "  <div class='container-fluid'>";
  
		// print "<div class='col-md-9'>\n";
		  
		// print "<div id='species_helplet'></div>";

		// print "</div>"; // col9

		// print "<div class='col-md-3'>\n";
		
		// foreach($this->classifyInputs as $formInput){
			// print "<div class='row'>\n";
			// print "<div class='col-md-12'>\n";
			// print "<div class='form-group species_classify'>\n";
			// print $formInput;
			// print "</div>\n";
			// print "</div>\n";
			// print "</div> <!-- /.row -->\n";
		// }

		// print "<hr/>";
		// print "<button type='button' class='btn btn-default' data-dismiss='modal'>".Text::_("COM_BIODIV_CLASSIFY_CLOSE")."</button>";
		// print "<button type='button' class='btn btn-success' id='classify-save' data-dismiss='modal'>".Text::_("COM_BIODIV_CLASSIFY_SAVE")."</button>";

		// print "</div> <!--col3 -->";

 		// print "</div> <!-- /.container-fluid -->";
		// print "        </form>";
		// print "      </div>";

		// print "    </div>";
		// print "  </div>";


		// print "</div>";
	}
	
	
	function generateClassifyNothingModal( $photoId = null ) {
		
		
		print '<div class="modal fade" id="classify_nothing_modal" tabindex="-1" aria-labelledby="classifyNothingModalLabel" >';
		print '<div class="modal-dialog modal-dialog-scrollable modal-xl">';
		print '<div class="modal-content">';
		print '<div class="modal-header">';
		print '<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">'.Text::_("COM_BIODIV_CLASSIFY_CLOSE").'</span></button>';
		print '<h4 class="modal-title" id="classifyNothingModalLabel">'.Text::_("COM_BIODIV_CLASSIFY_CLASS_NOTHING").'</h4>';
		print '</div>';
		print '<div class="modal-body">';
		print '<form id="classify-nothing-form" role="form">';
		print HTMLHelper::_('form.token');
		print '<div id="classify-nothing">';
		
		$nothingId = codes_getCode("Nothing",'noanimal');

		print "<input type='hidden' name='species' id='nothing_species_value' value = '".$nothingId."'/>\n";
		
		if ( $photoId ) {
			print "<input id='currPhotoId' type='hidden' name='photo_id' value='".$photoId."'/>\n";
		}		  

		print '</div>';
		  
		print '<div class="container-fluid">';
		print '<div class="row">';
		print "<div class='col-md-9'>\n";
		  
		print "<div id='nothing_helplet' class='m-4'></div>";

		print "</div>"; // col9



		print "<div class='col-md-3'>\n";

		 
		$struc = 'nothing';
		
        $struc_key = $struc.'_id';
		$help = Text::_("COM_BIODIV_CLASSIFY_NOTHING_LABEL");
		$select_name = 'notes';
        //print '          <h5>'.$help[$struc_key].'</h5>';
		print '          <h5>'.$help.'</h5>';
        // print '          <select id = "classify_notes_nothing" name = "'.$select_name.'" class = "form-control required" >';
        $isFirst = true;
		foreach ( codes_getList($struc."tran") as $thing ) {
			list($code, $name) = $thing;
			$value = $code . ' ' . $name;
			if ( $isFirst ) {
				$isFirst = false;
				//print "<option value='$value' selected>$name</option>";
				print '<div class="species-radio"><input name="notes" type="radio" value="'.$value.
					'" id="nothing_'.$code.'" checked="checked"><label for="nothing_'.$code.'">'.
					$name.'</label></div>';
			}
			else {
				//print "<option value='$value'>$name</option>";
				print '<div class="species-radio"><input name="notes" type="radio" value="'.$value.
					'" id="nothing_'.$code.'" ><label for="nothing_'.$code.'">'.
					$name.'</label></div>';
			}
        }
        // print '          </select>';
		
		
		print '<hr/>';
		print '<div class="d-flex justify-content-end">';
		print '<button type="button" class="btn btn-outline-secondary m-1 mb-3" data-dismiss="modal">'.Text::_("COM_BIODIV_CLASSIFY_CLOSE").'</button>';
		print '<button type="button" class="btn btn-primary m-1 mb-3 me-4" id="classify-save-nothing">'.Text::_("COM_BIODIV_CLASSIFY_SAVE").'</button>';
		print '</div>';
		print '</form>';
		
		print '</div>'; // col-3

		print '</div>'; // row

		
		print ' </div> <!-- /.container-fluid -->';
		
		print '     </div>'; // modal-body
		print '    </div>'; // modal-content
		print '  </div>'; // modal-dialog


		print '</div>'; // modal



	}
	
}


?>
