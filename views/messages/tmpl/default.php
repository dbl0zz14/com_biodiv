<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
* 
*/
 
// No direct access to this file
defined('_JEXEC') or die;

if ( !$this->personId ) {
	print '<div type="button" class="list-group-item btn btn-block reloadPage" >'.JText::_("COM_BIODIV_MESSAGES_LOGIN").'</div>';
}
else {
	
	print '<div class="row">';
	if ( $this->schoolUser->role_id != Biodiv\SchoolCommunity::STUDENT_ROLE ) {
		print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
		Biodiv\SchoolCommunity::generateNav("messages");
		
		print '</div>';
		
		print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
	
	}
	else {
		
		print '<div class="col-md-12 col-sm-12 col-xs-12">'; 
		
		Biodiv\SchoolCommunity::generateBackAndLogout();
	}
	
	// --------------------- Main content
	
	print '<div id="displayArea">';
	
	print '<h2>';
	print '<div class="row">';
	print '<div class="col-md-10 col-sm-10 col-xs-10">';
	print '<span class="greenHeading">'.JText::_("COM_BIODIV_MESSAGES_HEADING").'</span> <small class="hidden-xs">'.JText::_("COM_BIODIV_MESSAGES_SUBHEADING").'</small>';
	print '</div>'; // col-10
	print '<div class="col-md-2 col-sm-2 col-xs-2 text-right">';
	if ( $this->helpOption > 0 ) {
		print '<div id="helpButton_'.$this->helpOption.'" class="btn btn-default helpButton h4" data-toggle="modal" data-target="#helpModal">';
		print '<i class="fa fa-info"></i>';
		print '</div>'; // helpButton
	}
	print '</div>'; // col-2
	print '</div>'; // row
	print '</h2>';  
	
	print '<div class="row filterRow">';

	print '<div class="col-md-6 col-sm-8 col-xs-8">';
	
	print '<div class="btn-group" role="group" aria-label="message filters">';
  

	print '<div class="btn btn-info inboxTab active ">';
	print JText::_("COM_BIODIV_MESSAGES_INBOX");
	print '</div>';
	
	print '<div class="btn btn-info sentMessagesTab  ">';
	print JText::_("COM_BIODIV_MESSAGES_SENT_MESSAGES");
	print '</div>';
	
	print '</div>'; // btn-group
	
	print '</div>'; // col-6

	
	
	print '<div class="col-md-6 col-sm-4 col-xs-4 text-right">';
	print '<button class="btn btn-primary" data-toggle="modal" data-target="#messageModal">';
	print JText::_("COM_BIODIV_MESSAGES_NEW_MESSAGE");
	print '</button>';
	print '</div>';
	
	print '</div>'; // row
	
	print '<div id="messageArea" class="fullPageHeight"></div>';
	
	print '</div>'; // displayArea
	
	print '</div>'; // col-10 or 12
	
	print '</div>'; // row
	

	// ------------------------------- send message modal	
	print '<div id="messageModal" class="modal fade" role="dialog">';
	print '  <div class="modal-dialog"  >';

	print '    <!-- Modal content-->';
	print '    <div class="modal-content">';
	print '      <div class="modal-header">';
	print '        <button type="button" class="close" data-dismiss="modal">&times;</button>';
	print '        <h4 class="modal-title">'.JText::_("COM_BIODIV_MESSAGES_NEW_MESSAGE").'</h4>';
	print '      </div>';

	print '	    <form id="newMessageForm" >';

	print '     <div class="modal-body">';

	print '       <input type="hidden" name="sender" value="' . $this->personId . '"/>';

	print '<h5><label for="recipientSelect">'.JText::_("COM_BIODIV_MESSAGES_TO").'</label></h5>';
	print '<select id="recipientSelect" name="recipientSelect" class="form-control">';
	print '<option value="" disabled selected hidden>'.JText::_("COM_BIODIV_MESSAGES_SEL_PERSON").'...</option>';
	foreach ( $this->recipients as $id=>$person ) {
		if ( $id == $this->personId ) continue;
		print '<option value="'.$id.'" >'.$person->name.'</option>';
	}
	print '</select>';

	print '<h5><label for="messageText">'.JText::_("COM_BIODIV_MESSAGES_MESSAGE").'</label></h5>';
	print '<textarea id="messageText" name="messageText" rows="10" cols="50"></textarea>'; 

	print '<h5><div id="messageMsg"></div></h5>';
	print '     </div>';
	print '	  <div class="modal-footer">';
	print '        <button id="sendMessageBtn" type="submit" class="btn btn-primary" >'.JText::_("COM_BIODIV_MESSAGES_SEND").'</button>';
	print '        <button type="button" class="btn btn-default" data-dismiss="modal">'.JText::_("COM_BIODIV_MESSAGES_CLOSE").'</button>';
	print '      </div>';
	print '     </form>';
			  
	print '    </div>';

	print '  </div>';
	print '</div>';



	// ----------------------------- reply modal
	print '<div id="replyModal" class="modal fade" role="dialog">';
	print '  <div class="modal-dialog"  >';

	print '    <!-- Modal content-->';
	print '    <div class="modal-content">';

	print '      <div class="modal-header">';
	print '        <button type="button" class="close" data-dismiss="modal">&times;</button>';
	print '        <h4 class="modal-title">'.JText::_("COM_BIODIV_MESSAGES_NEW_MESSAGE").'</h4>';
	print '      </div>';

	print '	    <form id="replyMessageForm" >';

	print '     <div class="modal-body">';
	print '       <input type="hidden" name="sender" value="' . $this->personId . '"/>';
	print '       <input type="hidden" name="replyTo" value=""/>';

	print '       <input id="replyRecipient" type="hidden" name="replyRecipient" value=""/>';


	//print '<div class="row">';
	//print '<div class="col-md-12">';

	print '<h5><label for="replyText">'.JText::_("COM_BIODIV_MESSAGES_MESSAGE").'</label></h5>';
	print '<textarea id="replyText" name="replyText" rows="10" cols="50"></textarea>'; 

	//print '</div>'; // col-12
	//print '</div>'; // row
			

	print '<h5><div id="replyMessageMsg"></div></h5>';
	print '     </div>';


	print '	  <div class="modal-footer">';
	print '        <button id="replyMessageBtn" type="submit" class="btn btn-primary" >'.JText::_("COM_BIODIV_MESSAGES_SEND").'</button>';
	print '        <button type="button" class="btn btn-default" data-dismiss="modal">'.JText::_("COM_BIODIV_MESSAGES_CLOSE").'</button>';
	print '      </div>';


	print '     </form>';

			  
	print '    </div>';

	print '  </div>';
	print '</div>';
	
	
	// ----------------------------- report modal
	print '<div id="reportModal" class="modal fade" role="dialog">';
	print '  <div class="modal-dialog"  >';

	print '    <!-- Modal content-->';
	print '    <div class="modal-content">';

	print '      <div class="modal-header">';
	print '        <button type="button" class="close" data-dismiss="modal">&times;</button>';
	print '        <h4 class="modal-title">'.JText::_("COM_BIODIV_MESSAGES_REPORT_MESSAGE").'</h4>';
	print '      </div>';

	print '	    <form id="reportMessageForm" >';

	print '     <div class="modal-body">';
	print '       <input type="hidden" name="sender" value="' . $this->personId . '"/>';
	print '       <input type="hidden" name="reportMessage" value="1"/>';
	print '       <input type="hidden" name="reportedMsgId" value=""/>';

	
	print '<h5><label for="reportText">'.JText::_("COM_BIODIV_MESSAGES_REASON").'</label></h5>';
	print '<textarea id="reportText" name="reportText" rows="10" cols="50"></textarea>'; 

			

	print '<h5><div id="reportMessageMsg"></div></h5>';
	print '     </div>';


	print '	  <div class="modal-footer">';
	print '        <button id="reportMessageBtn" type="submit" class="btn btn-primary" >'.JText::_("COM_BIODIV_MESSAGES_SEND_REPORT").'</button>';
	print '        <button type="button" class="btn btn-default" data-dismiss="modal">'.JText::_("COM_BIODIV_MESSAGES_CLOSE").'</button>';
	print '      </div>';


	print '     </form>';

			  
	print '    </div>';

	print '  </div>';
	print '</div>';


print '<div id="helpModal" class="modal fade" role="dialog">';
print '  <div class="modal-dialog"  >';

print '    <!-- Modal content-->';
print '    <div class="modal-content">';
print '      <div class="modal-header text-right">';
print '        <div type="button" role="button" class="closeButton h3" data-dismiss="modal">&times;</div>';
print '      </div>';
print '     <div class="modal-body">';
print '	    <div id="helpArticle" ></div>';
print '      </div>';
print '	  <div class="modal-footer">';
print '        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>';
print '      </div>';
	  	  
print '    </div>';

print '  </div>';
print '</div>';



}

JHTML::script("com_biodiv/commonbiodiv.js", true, true);
JHTML::script("com_biodiv/commondashboard.js", true, true);
JHTML::script("com_biodiv/messages.js", true, true);



?>