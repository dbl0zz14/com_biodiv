<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;
?>

<?php
	$project = $this->project;

	print '<h2 itemprop="name">';
	print $project["project_prettyname"];
	print '</h2>';
	print '<p class="spacer-1em"></p>';
	print "<div class='row'>";
	
	print "<div class='col-md-8 project-col' >";
	if ( in_array('Logo', $this->displayOptions ) ){
		$url = projectImageURL($project["project_id"]);
		//print "image url is " . $url;
		print '<img class="project-col-logo" alt = "" src="'.$url.'" />';
		print '<p class="spacer-1em"></p>';		
	}
	if ( in_array('Photo', $this->displayOptions ) ){
		$url = projectImageURL($project["project_id"]);
		//print "image url is " . $url;
		//print '<img class="project-col-image" alt = "" src="'.$url.'" />';
		print '<img alt = "" src="'.$url.'" />';
		print '<p class="spacer-1em"></p>';		
	}
	if ( in_array('Description', $this->displayOptions) ) {
		print '<p>'.$project["project_description"].'</p>';
		print '<p class="spacer-1em"></p>';
	}
	// Print how to join the project for restricted projects.
	if ( $this->access_level == 1 ) {
		print '<p>'.JText::_("COM_BIODIV_PROJECT_HYBRID").'</p>';
		print '<p class="spacer-1em"></p>';
	}
	if ( $this->access_level > 1 ) {
		print '<p>'.JText::_("COM_BIODIV_PROJECT_RESTRIC").'</p>';
		print '<p class="spacer-1em"></p>';
	}
	
	
	print "<form action = '" . BIODIV_ROOT . "' method = 'GET'>";
	print "<div class='input-group'>";
    print "<input type='hidden' name='view' value='".$this->classifyView."'/>";
    print "<input type='hidden' name='option' value='" . BIODIV_COMPONENT . "'/>";
    print "<input type='hidden' name='classify_only_project' value='1'/>";
	print "<input type='hidden' name='project_id' value='". $project["project_id"] ."'/>";
	
	print "<span class='input-group-btn'>";
    print "  <button  class='btn btn-success btn-lg' type='submit'><i class='fa fa-search'></i> ".JText::_("COM_BIODIV_PROJECT_CLASS_PROJ")."</button>";
	print "</span>";
	
	print "</div>";
	print "</form>";
	print '<p class="spacer-2em"></p>';
	
	
	print "</div>";
	print "<div class='col-md-4 project-col' >";
	//print '<p class="spacer-2em"></p>';
	if ( in_array('ProgressChartShort', $this->displayOptions ) ) {
		print "<canvas id='progressChartShort' class='progress-chart' data-project-id='".$project["project_id"]."' height='190px'></canvas>";
		print '<p class="spacer-3em"></p>';
	}
	if ( in_array('ProgressChartMedium', $this->displayOptions ) ) {
		print "<canvas id='progressChartMedium' class='progress-chart' data-project-id='".$project["project_id"]."' height='190px'></canvas>";
		print '<p class="spacer-3em"></p>';
	}
	if ( in_array('ProgressChartLong', $this->displayOptions ) ) {
		print "<canvas id='progressChartLong' class='progress-chart' data-project-id='".$project["project_id"]."' height='190px'></canvas>";
		print '<p class="spacer-3em"></p>';
	}
	if ( in_array('AnimalsChart', $this->displayOptions ) ) {
		print "<canvas id='animalsChart' class='animals-doughnut' data-project-id='".$project["project_id"]."' height='230px'></canvas>";
		print '<p class="spacer-3em"></p>';
	}
	if ( in_array('AnimalsBarChart', $this->displayOptions ) ) {
		//print "<div class='animals-bar-container'>";
		print "<canvas id='animalsBarChart' class='animals-bar' data-project-id='".$project["project_id"]."' height='320px' ></canvas>";
		//print "</div>";
		print '<p class="spacer-3em"></p>';
	}
	print "</div>";
	print "</div>";
	
	
	if($this->title || $this->introtext){
		//print "<h2>" . $this->title . "</h2>\n";
		//print '<p class="spacer-3em"></p>';
		print $this->introtext; 
		
		if ( $this->fulltext ) {
			print $this->fulltext;
		}
		
		print '<p class="spacer-1em"></p>';
	}
	
	if ( in_array('SubProjectLarge', $this->displayOptions ) ) {
		
	
		if ( count($this->subProjects) > 0 ) {
		print '<p>'.JText::_("COM_BIODIV_PROJECT_SUB_PROJ").'</p>';
		print '<p class="spacer-1em"></p>';
		}
		
		$project_num = 0;
		foreach ( $this->subProjects as $proj_id=>$proj_prettyname ) {
			if ( $project_num%4 == 0 ) print '<div class="row">';
			print "<div class='col-md-3 project-col' >";
			$url = projectImageURL($proj_id);
				
			print '<form action = "';
			print BIODIV_ROOT;
			print '" method = "GET">';
			print "<input type='hidden' name='view' value='projecthome'/>";
			print "<input type='hidden' name='option' value='";
			print BIODIV_COMPONENT;
			print "'/>";
			print "<input type='hidden' name='project_id' value='".$proj_id."'/>";
			print "<button class='image-btn project-btn' type='submit' data-tooltip='".JText::_("COM_BIODIV_PROJECT_TOOLTIP")."'><div class='crop-width'><img class='project-col-image cover scale2' alt = 'project image' src='".$url."' /></div></button>";
			print "</form>";
			
			print '<div class="subproject-title">';
			print '<h3 itemprop="name">';
			print $proj_prettyname;
			print '</h3>';
			print '</div>';
			
			print '<p class="spacer-3em"></p>';
			print '</div>';
			$project_num += 1;
			if ( $project_num%4 == 0 ) {
				print '</div>';
				
			}			
		}
	}
	


?>



<?php
//JHTML::stylesheet("com_biodiv/com_biodiv.css", true, true);
JHTML::stylesheet("com_biodiv/com_biodiv.css", array(), true);
//JHTML::script("com_biodiv/bootbox.js", true, true);
JHTML::script("https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js", true, true);
JHTML::script("com_biodiv/project.js", true, true);
?>



