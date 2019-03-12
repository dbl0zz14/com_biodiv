<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

print "<h1>MammalWeb: Testing</h1>";

$t0 = time();
$msp = mySpottingProjects();
$t1 = time();
$tdiff = $t1 - $t0;
print("<br>Time taken for mySpottingProjects: " . $tdiff . " seconds<br>Projects are: <br>");
print_r($msp);

$t0 = time();
$po = getProjectOptions(null, "priority", false);
$t1 = time();
$tdiff = $t1 - $t0;
print("<br>Time taken for getProjectOptions: " . $tdiff . " seconds<br>Project options are: <br>");
print_r($po);

$priority_array = array_column ( $po, "option_name", "project_id" );

$all_priorities = array("Single", "Multiple", "Single to multiple", "Site time ordered");
  
  // which priorities do we have projects for?
  $distinct_priorities = array_intersect ( $all_priorities, $priority_array );
  
  //print "<br>nextSequence, got distinct_priorities: <br>" ;
  //print_r ( $distinct_priorities );
  
  //error_log ( "distinct priorities: " . implode ( ',', $distinct_priorities ) );
  
  // Determine the order of priority types to try.  Take a weighted choice from each priority type that this user has 
  // access to.  
  $all_weightings = getPriorityWeightings ();
  //print "<br>nextSequence, got all_weightings: <br>" ;
  //print_r ( $all_weightings );


  $reqd_weightings = array_intersect_key ( $all_weightings, array_flip($priority_array ));
  //print "<br>nextSequence, got reqd_weightings: <br>" ;
  //print_r ( $reqd_weightings );

  $total_weighting = array_sum($reqd_weightings);
  
  $ordered_priorities = array();
  
  $num_iterations = count($reqd_weightings);

  for ( $i=0; $i< $num_iterations; $i++ ) {
	//print "<br>nextSequence, i = " . $i . ", now got reqd_weightings: <br>" ;
    //print_r ( $reqd_weightings );
	//print "<br>nextSequence, i = " . $i . ", total_weighting =  " . $total_weighting . "<br>" ;
    

	if ( count($reqd_weightings) == 1 ) {
		//print "<br>just one reqd weighting left <br>" ;

		$ordered_priorities[] = array_keys($reqd_weightings)[0];
		break;
	}
	// Choose a random integer between 0 and $total_weightings.
    $choice = rand ( 1, $total_weighting );

    //print "<br>nextSequence, choice:" . $choice . " <br>" ;

    // check through the accumulated weightings to see where the choice lies..
    $count = 0;
    foreach ( $reqd_weightings as $priority=>$weighting ) {
	  $count += $weighting;
	  if ( $choice <= $count ) {
	    $ordered_priorities[] = $priority;
		$total_weighting -= $weighting;
		//print "<br>About to unset " . $priority . " <br>" ;
        unset($reqd_weightings[$priority]);
		//print "<br>nextSequence, after unset reqd_weightings: <br>" ;
        //print_r ( $reqd_weightings );
	
	    break;
	  }
    }
  }
  //print "<br>nextSequence, got ordered_priorities: <br>" ;
  //print_r ( $ordered_priorities );

  foreach ( $ordered_priorities as $current_priority ) {
	  if ( true ) {
		  switch ($current_priority) {
			  case "Multiple":
				$project_ids = array_keys ( $priority_array, "Multiple" );
				$t0 = time();
	            $photo_id = chooseMultiple ( $project_ids, false );
				$t1 = time();
				$tdiff = $t1 - $t0;
				print("<br>Time taken for chooseMultiple: " . $tdiff . " seconds<br>Used project ids: <br");
				print_r($project_ids);
				print("Photo id is: " . $photo_id . "<br>");
				break;
			  case "Single":
				$project_ids = array_keys ( $priority_array, "Single" );
	            $t0 = time();
				$photo_id = chooseSingle ( $project_ids, false );
				$t1 = time();
				$tdiff = $t1 - $t0;
				print("<br>Time taken for chooseSingle: " . $tdiff . " seconds<br>Used project ids: <br");
				print_r($project_ids);
				print("Photo id is: " . $photo_id . "<br>");
				break;
			  case "Single to multiple":
				$project_ids = array_keys ( $priority_array, "Single to multiple" );
	            $t0 = time();
				$photo_id = chooseSingle ( $project_ids, false );
				if ( !$photo_id ) {
					$photo_id = chooseMultiple ( $project_ids, false );
				}
				$t1 = time();
				$tdiff = $t1 - $t0;
				print("<br>Time taken for choose Single to multiple: " . $tdiff . " seconds<br>Used project ids: <br");
				print_r($project_ids);
				print("Photo id is: " . $photo_id . "<br>");
				break;
			  case "Site time ordered":
				$t0 = time();
				$project_ids = array_keys ( $priority_array, "Site time ordered" );
	            $photo_id = chooseSiteTimeOrdered ( $project_ids, 0, false );
				$t1 = time();
				$tdiff = $t1 - $t0;
				print("<br>Time taken for chooseSiteTimeOrdered: " . $tdiff . " seconds<br>Used project ids: <br");
				print_r($project_ids);
				print("Photo id is: " . $photo_id . "<br>");
				break;
			  default:
			    break;
	  
		  }
	  }
  }
  


?>




