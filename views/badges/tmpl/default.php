<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

error_log ( "Badges template called" );

/*
$errMsg = print_r ( $this->data, true );
error_log ( "Badges data:");
error_log ( $errMsg );
*/

if ( !$this->personId ) {
	
	// Please log in button
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_BADGES_LOGIN").'</div>';
	
}

else {
	
	if ( $this->suggest ) {
		print '<h4>'.JText::_("COM_BIODIV_BADGES_COULD_TRY").'</h4>';
	}
	
	
	if ( $this->displayBadges ) {
		
		if ( $this->numToCollect == 0 ) {
			print '<div class="row"><div class="col-md-6 h3" style="margin-top:20px;margin-bottom:20px">'.
				JText::_("COM_BIODIV_BADGES_ALL_COLLECTED").'</div><div class="col-md-6 text-right">'.
				'<a href="'.JText::_("COM_BIODIV_BADGES_WILD_SPACE_LINK").'" class="btn btn-primary" >'.JText::_("COM_BIODIV_BADGES_WILD_SPACE").'</button>'.'</a>'.'</div></div>';
		}
		else {
			print '<button class="btn btn-primary collectBadges" style="margin-top:20px;margin-bottom:20px">'.
				JText::_("COM_BIODIV_COLLECT_BADGES").'</button> '.'<a href="'.JText::_("COM_BIODIV_BADGES_WILD_SPACE_LINK").
				'" class="btn btn-primary" >'.
				JText::_("COM_BIODIV_BADGES_WILD_SPACE").'</button>'.'</a>';
		}
		
		
		//print '<div class="row">';
		
		$badgeCount = 0;
		
		foreach ( $this->badgeGroupData as $groupId=>$data ) {
			
			$badges = json_decode ( $data );
					
			foreach ( $badges as $badge ) {

				foreach ( $badge->tasks as $task ) {
					
					if ( $task->status == Biodiv\Badge::COMPLETE ) {
						if ( $badgeCount%6 == 0 ) {
							print '<div class="row">';
						}						
						print '<div class="col-md-2 col-sm-3 col-xs-6">';
						print '<img class="img-responsive" src="'.$badge->unlocked_image.'" alt = "unlocked badge image" />';
						print '<div class="h5 text-center">'.$badge->badge_name.'</div>';
						print '</div>'; // col-2
						$badgeCount += 1;
					}
					else if ( $task->status == Biodiv\Badge::COLLECTED ) {
						if ( $badgeCount%6 == 0 ) {
							print '<div class="row">';
						}	
						print '<div class="col-md-2 col-sm-3 col-xs-6">';
						print '<img class="img-responsive" src="'.$badge->badge_image.'" alt = "badge image" />';
						print '<div class="h5 text-center">'.$badge->badge_name.'</div>';
						print '</div>'; // col-2
						$badgeCount += 1;
					}
					
					if ( $badgeCount%6 == 0 ) {
						print '</div>'; // row
					}
					
				}
			}
		}
		
		if ( $badgeCount%6 != 0 ) {
			print '</div>'; // row
		}

	}
	
	if ( !$this->displayBadges ) {
		
		if ( $this->collect ) {
			
			print '<button class="btn btn-primary collectedBadges" style="margin-top:20px;margin-bottom:20px">'.JText::_("COM_BIODIV_BADGES_SHOW_COLLECTED").'</button>';
			print '<h3>'.JText::_("COM_BIODIV_BADGES_TAP_COLLECT").'</h3>';
			
		}
	
		if ( $this->onOneLine ) {
			print '<div class="row badgeGroup">';
		}
		
		$taskCount = 0;
		
		foreach ( $this->badgeGroupData as $groupId=>$data ) {
			
			$colorClassArray = getOptionData ( $groupId, "colorclass" );
			if ( count($colorClassArray) > 0 ) {
				$colorClass = $colorClassArray[0];
			}
			
			$badges = json_decode ( $data );
			//$badges = $data;
			
			$totalNumBadges = count((array)$badges);
			
			
			if ( $this->singleBadgeGroup and $totalNumBadges == 0  ) {
				print JText::_("COM_BIODIV_BADGES_NO_ACTIVITIES");
			}
			
			
			
			
			//error_log ( "Total num badges = " . $totalNumBadges );
			
			if ( !$this->onOneLine  ) {
				if ( $totalNumBadges > 4 ) {
					print '<div class="row badgeGroup" >';
				}
				else {
					print '<div class="row">';
				}
			}
			
				
			
			foreach ( $badges as $badge ) {
				
				foreach ( $badge->tasks as $task ) {
					
					if ( $this->completeOnly and $task->status < Biodiv\Badge::COMPLETE ) {
						continue;
					}
					if ( $this->unlockedOnly and $task->status != Biodiv\Badge::UNLOCKED ) {
						continue;
					}
					if ( $this->collect and $task->status != Biodiv\Badge::COMPLETE ) {
						continue;
					}
					
					$taskCount += 1;
					$collectClass = "";
					$highlightClass = "";
					if ( $task->status == Biodiv\Badge::COMPLETE ) {
						$collectClass = "collectTask";
					}
					
					$highlightClass = $colorClass.'_border';
						
					// $lockedClass = "";
					// if ( $task->status == Biodiv\Badge::LOCKED ) {
						// $lockedClass = "lockedTask";
					// }
					
					// if ( $this->collect ) {
						// print '<div class="col-md-6">';
					// }
					// else {
						print '<div class="col-md-3">';
					//}
					
					
					// If task has been collected we display the back
					if ( $task->status != Biodiv\Badge::COLLECTED ) {
						 
						print '<div id="task_card_'.$task->task_id.'" class="panel taskPanel '.$collectClass.' ' .$highlightClass.'">';
						
						print '<div class="panel-heading taskHeading">';
						
						print '<div class="row small-gutter">';
						
						print '<div class="col-md-3 col-sm-3 col-xs-3 h4 text-left">';
						print '<span class="'.$colorClass.'_text"><img src="'.$task->icon.'" class="img-responsive badgeGroupIcon" alt="badge group icon"/></span>';
						print '</div>';
						print '<div class="col-md-6 col-sm-6 col-xs-6 text-center"><strong>';
						print $task->points . ' ' . JText::_("COM_BIODIV_BADGES_POINTS");
						print '</strong></div>'; // col-6
						print '<div class="col-md-2 col-md-offset-1 col-sm-2 col-sm-offset-1 col-xs-2 col-xs-offset-1 h4 text-left">';
						print '<img src="'.$task->module_icon.'" class="img-responsive badgeGroupIcon" alt="module icon"/>';
						print '</div>';
						
						print '<div class="col-md-12 col-sm-12 col-xs-12 h5 text-center taskText"><strong>';
						print $badge->badge_name;
						print '</strong></div>'; // col-12
					
						print '</div>'; // row
							
						print '</div>'; // panel heading
						
						print '<div class="panel-body taskBody">';
						
						if ( $task->status == Biodiv\Badge::LOCKED ) {
							print '<div class="taskImage lockedTask">';
							print '<div class="row ">';
							print '<div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2 col-xs-8 col-xs-offset-2">';
							print '<img class="img-responsive" src="'.$badge->locked_image.'" alt = "locked badge image" />';
							print '</div>'; // col-8
							print '</div>'; // row
							print '</div>'; // taskImg
						}
						else if ( $task->status == Biodiv\Badge::PENDING ) {
							print '<div class="taskImage lockedTask">';
							print '<div class="row ">';
							print '<div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2 col-xs-8 col-xs-offset-2">';
							print '<img class="img-responsive" src="'.$badge->unlocked_image.'" alt = "unlocked badge image" />';
							print '</div>'; // col-8
							print '</div>'; // row
							print '</div>'; // taskImg
						}
						else {
							print '<div class="taskImage">';
							print '<div class="row ">';
							print '<div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2 col-xs-8 col-xs-offset-2">';
							print '<img class="img-responsive" src="'.$badge->unlocked_image.'" alt = "unlocked badge image" />';
							print '</div>'; // col-8
							print '</div>'; // row
							print '</div>'; // taskImg
						}
					
						
						$isLinkedTask = false;
						if ( Biodiv\SchoolCommunity::isStudent() and $task->linked_task ) {
							$isLinkedTask = true;
						}
						if ( $isLinkedTask ) {
							print '<div class="taskDescription h5 text-center taskText">'.JText::_("COM_BIODIV_BADGES_TEACHER_TASK").'</div>';
						}
						else if ( $task->status == Biodiv\Badge::PENDING ) {
							print '<div class="taskDescription h5 text-center taskText">'.JText::_("COM_BIODIV_BADGES_PENDING").'</div>';
						}
						else {
							print '<div class="taskDescription h5 text-center taskText">'.$task->description.'</div>';
						}
						
						
						print '<div class="text-center taskButtons">';
								
						if ( $task->status == Biodiv\Badge::COMPLETE ) {
							print '<div class="collectMessage">'.JText::_("COM_BIODIV_BADGES_COLLECT").'</div>';
						}
						else {
							print '<div id="task_more_'.$task->task_id.'" class="btn btn-primary btn-sm task_btn" data-toggle="modal" data-target="#task_modal">'.
										JText::_("COM_BIODIV_BADGES_MORE").'</div>';
										
							if ( !$this->viewOnly and !$isLinkedTask and $task->counted_by == "USER" and $task->status == Biodiv\Badge::UNLOCKED ) {
								print '<div id="task_done_'.$task->task_id.'" class="btn btn-default btn-sm task_btn upload_task">'.JText::_("COM_BIODIV_BADGES_DONE").'</div>';
							}
						}
						
						print '</div>'; // text-center
										
						print '</div>'; // panel-body
						
						print '</div>'; // panel
					
					}
					
					if ( $task->status == Biodiv\Badge::COLLECTED ) {
						print '<div id="task_detail_'.$task->task_id.'" class="panel taskPanel '.$highlightClass.'" >';
					}
					else {
						print '<div id="task_detail_'.$task->task_id.'" class="panel taskPanel turnTask '.$highlightClass.'" style="display:none; position:absolute; top:0; opacity:0;">';
					}
						
					print '<div class="panel-heading taskHeading">';
					
					print '<div class="row small-gutter">';
						
					print '<div class="col-md-3 col-sm-3 col-xs-3 h4 text-left">';
					print '<span class="'.$colorClass.'_text"><img src="'.$task->icon.'" class="img-responsive badgeGroupIcon" alt="badge group icon"/></span>';
					print '</div>';
					print '<div class="col-md-6 col-sm-6 col-xs-6 text-center"><strong>';
					print $task->points . ' ' . JText::_("COM_BIODIV_BADGES_POINTS");
					print '</strong></div>'; // col-6
					print '<div class="col-md-2 col-md-offset-1 col-sm-2 col-sm-offset-1 col-xs-2 col-xs-offset-1 h4 text-left">';
					print '<img src="'.$task->module_icon.'" class="img-responsive badgeGroupIcon" alt="module icon"/>';
					print '</div>';
					
					
					print '<div class="col-md-12 col-sm-10 col-xs-10 h5 text-center taskText"><strong>';
					print $badge->badge_name;
					print '</strong></div>'; // col-12
					
					
					print '</div>'; // row
						
					print '</div>'; // panel heading
					
					print '<div class="panel-body taskBody">';
					
					print '<div class="taskImage">';
					print '<div class="row ">';
					print '<div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2 col-xs-6 col-xs-offset-3">';
					print '<img class="img-responsive" src="'.$badge->badge_image.'" alt = "blank badge image" />';
					print '</div>'; // col-8
					print '</div>'; // row
					print '</div>'; // taskImg
					
					print '<div class="taskDescription h5 text-center taskText">'.$task->description.'</div>';
					
					print '<div class="text-center taskButtons">';
					if ( $task->species_unlocked ) {
						print '<div id="view_species_'.$task->task_id.'" class="btn btn-primary btn-sm species_btn "  data-toggle="modal" data-target="#species_modal">'.JText::_("COM_BIODIV_BADGES_VIEW_SPECIES").'</div>';
					}
					else {
						print '<div id="unlock_species_'.$task->task_id.'" class="btn btn-primary btn-sm species_btn unlock_species"  data-toggle="modal" data-target="#species_modal">'.JText::_("COM_BIODIV_BADGES_UNLOCK_SPECIES").'</div>';
						print '<div id="view_species_'.$task->task_id.'" class="btn btn-primary btn-sm species_btn "  data-toggle="modal" data-target="#species_modal" style="display:none">'.JText::_("COM_BIODIV_BADGES_VIEW_SPECIES").'</div>';
					}
					print '</div>';
					
					print '</div>'; // panel-body
					
					print '</div>'; // panel
					
					
					
					
					print '</div>';  // col-3
					
				
				}
			}
			
			//print '</section>';  // section
			
			//print '</div>'; // col-12
			
			//error_log ( "Closing badge group col" );
			
			if ( !$this->onOneLine ) {
				print '</div>'; // badgeGroup row
			}
			
		}
		
		if ( $this->completeOnly and $taskCount == 0  ) {
			print JText::_("COM_BIODIV_BADGES_NO_COMPLETE_MOD");
		}
		else if ( $this->unlockedOnly and $taskCount == 0  ) {
			print JText::_("COM_BIODIV_BADGES_NO_UNLOCKED");
		}
		else if ( $this->suggest and $taskCount == 0  ) {
			print JText::_("COM_BIODIV_BADGES_NO_SUGGEST");
		}
	
		if ( $this->onOneLine ) {
			print '</div>'; // row
		}
	}
	
	
}

print '<div id="task_modal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog"  >';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header text-right">';
print '        <div type="button" role="button" class="closeButton h3" data-dismiss="modal">&times;</div>';
//print '        <h4 class="modal-title"> </h4>';
print '      </div>';
print '     <div class="modal-body">';
print '	    <div id="task_article" ></div>';
print '      </div>';
print '	  <div class="modal-footer">';
print '        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>';
print '      </div>';
	  	  
print '    </div>';

print '  </div>';
print '</div>';



print '<div id="species_modal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog"  >';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header text-right">';
print '        <div type="button" role="button" class="closeButton h3" data-dismiss="modal">&times;</div>';
//print '        <h4 class="modal-title"> </h4>';
print '      </div>';
print '     <div class="modal-body">';
print '	    <div id="species_article" ></div>';
print '      </div>';
print '	  <div class="modal-footer">';
print '        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>';
print '      </div>';
	  	  
print '    </div>';

print '  </div>';
print '</div>';





?>

