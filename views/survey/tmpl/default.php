<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

// DO WE WANT A TITLE HERE _ IF SO TAKE FROM SRVEY TABLE....  print "<h1>" . $this->translations['heading']['translation_text'] . "</h1>";


// Survey initial modal - only include if required
if ( $this->haveConsent ) {
	
	print '<form action = "' . BIODIV_ROOT . '" method = "GET">';
	
	print '<div class="panel-group">';
	
	foreach ( $this->sections as $section ) {
		
		// Section heading
		print '<div class="panel panel-warning">';
		print '<div class="panel-heading">';
		print  $section['section_text'];
		print '</div>';
		print '<div class="panel-body">';
		
		
		$sectionId = $section['section_id'];
		foreach ( $this->questions[$sectionId] as $question ) {
			
				
			// TEXT
			if ( $question['response_type'] == 1 ) {
			
				$sqStr = "sq[".$question['sq_id']."]";
				
				print '          <div class="question>';
				print '          <label for="'.$sqStr.'">'.$question['text'].'</label>';
				//print '          <input type="text" class="form-control" name="sq['.$question['sq_id'].']">';
				//print '          <textarea class="form-control" id="'.$sqStr.'" name="'.$sqStr.'"  rows="5" cols="100" maxlength="500"></textarea>';
				print '          <textarea class="form-control" id="'.$sqStr.'" name="'.$sqStr.'"  maxlength="500"></textarea>';
				print '          </div>';

			}
			// OPTION
			else if ( $question['response_type'] == 2 ) {
			
				print '    <div class="question">';
				print '    <div><label>'.$question['text'].'</label></div>';
				print '    <div class="survey-buttons" data-toggle="buttons">';
				
				$reponseOptions = $question['options'];
				
				foreach ( $reponseOptions as $responseId=>$responseText ) {
					print '    <label class="btn btn-default">';
					print '        <input type="radio" name="sq['.$question['sq_id'].']" value="'.$responseId.'" autocomplete="off">' . $responseText;
					print '    </label>';
				}
				
				print '    </div>';
				print '     </div>'; // question
			}
			// SCALE10
			else if ( $question['response_type'] == 3 ) {
			
				print '    <div class="question">';
				print '    <div><label>'.$question['text'].'</label></div>';
				print '    <div class="survey-buttons likert-buttons" data-toggle="buttons">';
				for ( $i=0; $i < 11; $i++ ) {
					print '        <label class="btn btn-default col-1">';
					print '            <input type="radio" name="sq['.$question['sq_id'].']" value="'.$i.'" autocomplete="off"> '.$i;
					print '        </label>';
				}
				
				print '    </div>';
				print '     </div>'; // question
			}
			// NUMBER
			else if ( $question['response_type'] == 4 ) {
				
				$sqStr = "sq[".$question['sq_id']."]";
				
				print '          <div class="question>';
				print '          <label for="'.$sqStr.'">'.$question['text'].'</label>';
				print '          <input type="number" id="'.$sqStr.'" class="form-control" name="'.$sqStr.'" >';
				print '          </div>';

			}
			

			
		}
		
		print '</div>'; // Panel body
		print '</div>'; // panel
	
		
	}
	
	
	print '      </div>'; // panel-group
	//print '       <button  class="btn btn-outline-primary btn-block btn-lg" > '.$this->translations['parti_info']['translation_text'].' <i class="fa fa-angle-down fa-lg" aria-hidden="true"></i></button>';
	
	//print '          <form action = "' . BIODIV_ROOT . '" method = "GET">';
	print '            <div class="col-md-2 col-xs-6">';
	print '              <input type="hidden" name="option" value="'.BIODIV_COMPONENT.'"/>';
	print '              <input type="hidden" name="task" value="add_response"/>';
    print '              <input type="hidden" name="survey" value="'.$this->surveyId.'"/>';
    print '              <button  class="btn btn-warning" type="submit">'.$this->translations['contribute']['translation_text'].'</button>';
	print '          </form>';
	print '          </div';
	print '          <div class="col-md-2 col-xs-6">';
	print '          <form action = "' . BIODIV_ROOT . '" method = "GET">';
	print '              <input type="hidden" name="option" value="'.BIODIV_COMPONENT.'"/>';
	print '              <input type="hidden" name="view" value="status"/>';
    print '              <button id="no_survey" class="btn btn-warning" type="submit"  data-survey-id="'.$this->surveyId.'">'.$this->translations['no_survey']['translation_text'].'</button>';
	print '          </form>';
	print '          </div';
	
	
}
else {
	error_log ( "No consent" );
	print "<h3>Sorry you have not consented to take this survey</h3>";
	
	print '          <form action = "' . BIODIV_ROOT . '" method = "GET">';
	print '              <input type="hidden" name="option" value="'.BIODIV_COMPONENT.'"/>';
	print '              <input type="hidden" name="view" value="status"/>';
    print '              <button class="btn btn-warning" type="submit">'.$this->translations['identify']['translation_text'].'</button>';
	print '          </form>';
	
}

JHTML::script("com_biodiv/survey.js", true, true);

?>


