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
	print '<a type="button" href="'.$this->translations['dash_page']['translation_text'].'" class="list-group-item btn btn-block" >'.$this->translations['login']['translation_text'].'</a>';
	
}

else {
	
	if ( $this->suggest ) {
		print '<h4>'.$this->translations['could_try']['translation_text'].'</h4>';
	}
	
	if ( $this->onOneLine ) {
		print '<div class="row">';
	}
	
	foreach ( $this->badgeGroupData as $groupId=>$data ) {
		
		$colorClassArray = getOptionData ( $groupId, "colorclass" );
		if ( count($colorClassArray) > 0 ) {
			$colorClass = $colorClassArray[0];
		}
		error_log ( "color class = " . $colorClass );
		
		if ( !$this->onOneLine ) {
			print '<div class="row badgeGroup" >';
		}
		
			
		$badges = json_decode ( $data );

		$errMsg = print_r ( $badges, true );
		error_log ( "badges:");
		error_log ( $errMsg );
		
		if ( !$this->onOneLine ) {
			print '<div class="h4 '.$colorClass.'_text">'.$this->badgeGroups[$groupId].'</div>';
		}
		
		foreach ( $badges as $badge ) {
			
			foreach ( $badge->tasks as $task ) {
				
				// Check whether only display complete tasks
				if ( !$this->completeOnly or ( $this->completeOnly and $task->status >= Biodiv\Badge::COMPLETE ) ) {
				
					$collectClass = "";
					if ( $task->status == Biodiv\Badge::COMPLETE ) {
						$collectClass = "collect_task";
					}
					
					
					print '<div class="col-md-2 badge_col">';
					
					// bootstrap panel version
					/*
					if ( $task->status != Biodiv\Badge::COLLECTED ) {
						
						print '<div id="task_card_'.$task->task_id.'" class="panel task_front '.$collectClass.'">';
								
								
						print '<div class="panel_heading">';
						
						print '<div class="row">';
						
						print '<div class="col-md-12 text-center '.$colorClass.'_text"><h5><strong>';
						print $task->task_name;
						print '</strong></h5></div>'; // col-12
						print '<div class="col-md-12 h5 text-center '.$colorClass.'_text">';
						print $task->points . ' ' . $this->translations['points']['translation_text'];
						print '</div>'; // col-12
						
						print '</div>'; // row
						
						print '</div>'; // task_heading
						
							
						print '<div class="panel_body text-center">';
						
						print '<div><img class="img-responsive badge_img_lg" src="'.$badge->badge_image.'" alt = "badge image" /></div>';
						print '<div class="task_descrip '.$colorClass.'_text"><h5>'.$task->description.'</h5></div>';
						
						print '<div class="text-center">';
						
						if ( $task->status == Biodiv\Badge::COMPLETE ) {
							print '<h5>'.$this->translations['collect']['translation_text'].'</h5>';
						}
						else {
							print '<div id="task_more_'.$task->task_id.'" class="btn btn-primary task_btn '.$colorClass.'_bg" data-toggle="modal" data-target="#task_modal">'.
										$this->translations['more']['translation_text'].'</div>';
							
							if ( $task->counted_by == "USER" and $task->status == Biodiv\Badge::UNLOCKED ) {
								print '<div id="task_done_'.$task->task_id.'" class="btn btn-primary task_btn upload_task '.$colorClass.'_bg">'.$this->translations['done']['translation_text'].'</div>';
							}
						}
						
						print '</div>'; // text-center
						
						print '</div>'; // task_body
						
						
						print '<div class="panel_footer">';

						print '<h5 class="text-center text-warning '.$colorClass.'_text"><i class="fa '.$this->statusIcons[$task->status].' fa-lg"></i></h5>';
						
						print '</div>'; // task_footer
						
						
						print '</div>'; //task_card
					
					}
					
					
					// Back of task card
					// Shown initially if collected
					if ( $task->status == Biodiv\Badge::COLLECTED ) {
						print '<div id="task_detail_'.$task->task_id.'" class="panel">';
					}
					else {
						print '<div id="task_detail_'.$task->task_id.'" class="panel task_back" style="display:none">';
					}
							
							
					print '<div class="row panel_heading">';
					
					print '<div class="col-md-12 text-center '.$colorClass.'_text"><h5><strong>';
					print $task->task_name;
					print '</strong></h5></div>'; // col-12
					print '<div class="col-md-12 h5 text-center '.$colorClass.'_text">';
					print $task->points . ' ' . $this->translations['points']['translation_text'];
					print '</div>'; // col-12
						
					print '</div>'; // task_heading row
					
						
					print '<div class="panel_body">';
					
					print '<div><img class="img-responsive task_img" src="'.$task->image.'" alt = "task image" /></div>';
					print '<div class="task_descrip '.$colorClass.'_text text-center"><h5>'.$task->heading.'</strong></h5></div>';
					print '<div class="text-center "><img class="img-responsive badge_img_sm" src="'.$badge->badge_image.'" alt = "badge image" /></div>';
					
					print '</div>'; // task_body
					
					
					print '<div class="panel_footer">';

					print '<h5 class="text-center text-warning '.$colorClass.'_text"><i class="fa '.$this->statusIcons[$task->status].' fa-lg"></i></h5>';
					
					print '</div>'; // task_footer
					
					
					print '</div>'; //task_card
					*/
					
					// If task has been collected we display the back
					if ( $task->status != Biodiv\Badge::COLLECTED ) {
						
						print '<div id="task_card_'.$task->task_id.'" class="task_panel task_front '.$collectClass.'">';
								
								
						print '<div class="task_heading">';
						
						print '<div class="row">';
						
						print '<div class="col-md-12 text-center '.$colorClass.'_text"><h5><strong>';
						print $task->task_name;
						print '</strong></h5></div>'; // col-12
						print '<div class="col-md-12 text-center '.$colorClass.'_text"><small>';
						print $task->points . ' ' . $this->translations['points']['translation_text'];
						print '</small></div>'; // col-12
						
						print '</div>'; // row
						
						print '</div>'; // task_heading
						
							
						print '<div class="task_body text-center">';
						
						print '<div class="row">';
						print '<div class="col-md-8 col-md-offset-2">';
						print '<div class="badge_img_lg"><img class="img-responsive" src="'.$badge->badge_image.'" alt = "badge image" /></div>';
						print '</div>';
						print '</div>'; // row
						print '<div class="task_descrip '.$colorClass.'_text"><h5>'.$task->description.'</h5></div>';
						
						print '<div class="text-center">';
						
						if ( $task->status == Biodiv\Badge::COMPLETE ) {
							print '<h5>'.$this->translations['collect']['translation_text'].'</h5>';
						}
						else {
							print '<div id="task_more_'.$task->task_id.'" class="btn btn-sm task_btn '.$colorClass.'_bg" data-toggle="modal" data-target="#task_modal">'.
										$this->translations['more']['translation_text'].'</div>';
							
							if ( $task->counted_by == "USER" and $task->status == Biodiv\Badge::UNLOCKED ) {
								print '<div id="task_done_'.$task->task_id.'" class="btn btn-sm task_btn upload_task '.$colorClass.'_bg">'.$this->translations['done']['translation_text'].'</div>';
							}
						}
						
						print '</div>'; // text-center
						
						print '</div>'; // task_body
						
						
						print '<div class="task_footer">';

						print '<h5 class="text-center text-warning '.$colorClass.'_text"><i class="fa '.$this->statusIcons[$task->status].' fa-lg"></i></h5>';
						
						print '</div>'; // task_footer
						
						
						print '</div>'; //task_card
					
					}
					
					
					// Back of task card
					// Shown initially if collected
					if ( $task->status == Biodiv\Badge::COLLECTED ) {
						print '<div id="task_detail_'.$task->task_id.'" class="task_panel">';
					}
					else {
						print '<div id="task_detail_'.$task->task_id.'" class="task_panel task_back" style="display:none">';
					}
							
							
					print '<div class="row task_heading">';
					
					print '<div class="col-md-12 text-center '.$colorClass.'_text"><h5><strong>';
					print $task->task_name;
					print '</strong></h5></div>'; // col-12
					print '<div class="col-md-12 text-center '.$colorClass.'_text"><small>';
					print $task->points . ' ' . $this->translations['points']['translation_text'];
					print '</small></div>'; // col-12
						
					print '</div>'; // task_heading row
					
						
					print '<div class="task_body">';
					
					print '<div><img class="img-responsive task_img" src="'.$task->image.'" alt = "task image" /></div>';
					print '<div class="task_descrip '.$colorClass.'_text text-center"><h5>'.$task->heading.'</strong></h5></div>';
					print '<div class="text-center"><img class="img-responsive badge_img_sm" src="'.$badge->badge_image.'" alt = "badge image" /></div>';
					
					print '</div>'; // task_body
					
					
					print '<div class="task_footer">';

					print '<h5 class="text-center text-warning '.$colorClass.'_text"><i class="fa '.$this->statusIcons[$task->status].' fa-lg"></i></h5>';
					
					print '</div>'; // task_footer
					
					
					print '</div>'; //task_card
					
						
				
					print '</div>'; // col-2
				}
			
			//print '<div id="badge_tasks_'.$badge->badge_id.'" class="badge_tasks"></div>'; 
			}
		}
		
		error_log ( "Closing badge group col" );
		
		if ( !$this->onOneLine ) {
			print '</div>'; // badgeGroup row
		}
		
	}
	
	if ( $this->onOneLine ) {
		print '</div>'; // row
	}
	
}



?>