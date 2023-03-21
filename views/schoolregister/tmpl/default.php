<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*
*/
 
// No direct access to this file
defined('_JEXEC') or die;

Biodiv\SchoolCommunity::generateNonUserHeader ( $this->helpOption );

print '<div class="row">';
print '<div class="col-md-12 col-sm-12 col-xs-12">'; 

print '<h2>'.JText::_("COM_BIODIV_SCHOOLREGISTER_WELCOME").'</h2>';

print '</div>';

print '</div>'; // row

print '<div id="displayArea">';
print '<div class="row">';
print '<div class="col-md-12 col-sm-12 col-xs-12">'; 


print '<h3>'.JText::_("COM_BIODIV_SCHOOLREGISTER_EXPLAIN").'</h3>';
print '<h4 class="vSpaced">'.JText::_("COM_BIODIV_SCHOOLREGISTER_NEXT").'</h4>';

print '<h4 class="vSpaced text-danger"><strong>'.JText::_("COM_BIODIV_SCHOOLREGISTER_NOTE").'</strong></h4>';

print '</div>';

print '<div class="col-md-8 col-sm-10 col-xs-12">';

print '<a href="bes-login"><button class="btn btn-info btn-lg">'.JText::_("COM_BIODIV_SCHOOLREGISTER_GOT_APPROVAL").'</button></a>';

print '<form id="signupForm">';

// --------------------------------- MammalWeb account details

print '<h3>'.JText::_("COM_BIODIV_SCHOOLREGISTER_YOUR_DETAILS").'</h3>';

print '<div class="form-group">';
print '<label for="name" class="h4">'.JText::_("COM_BIODIV_SCHOOLREGISTER_NAME").'</label>';
print '<input type="text" id="name" name="name" value = "" class="form-control schoolSetupInput">';
print '</div>';

print '<div class="form-group">';
print '<label for="username" class="h4">'.JText::_("COM_BIODIV_SCHOOLREGISTER_USERNAME").'</label>';
print '<input type="text" id="username" name="username" value = "" class="form-control schoolSetupInput">';
print '</div>';

print '<div class="form-group">';
print '<label for="email" class="h4"> '.JText::_("COM_BIODIV_SCHOOLREGISTER_EMAIL").'</label>';
print '<input type="email" id="email"  name="email" class="form-control schoolSetupInput">';
print '</div>';

print '<div class="form-group">';
print '<label for="email2" class="h4"> '.JText::_("COM_BIODIV_SCHOOLREGISTER_EMAIL2").'</label>';
print '<input type="email" id="email2"  name="email2" class="form-control schoolSetupInput">';
print '</div>';

print '<div class="form-group">';
print '<label for="password" class="h4"> '.JText::_("COM_BIODIV_SCHOOLREGISTER_PASSWORD").'</label>';
print '<input type="password" id="password"  name="password" class="form-control schoolSetupInput">';
print '</div>';

print '<div class="form-group">';
print '<label for="password2" class="h4"> '.JText::_("COM_BIODIV_SCHOOLREGISTER_PASSWORD2").'</label>';
print '<input type="password" id="password2"  name="password2" class="form-control schoolSetupInput">';
print '</div>';


print '<h3>'.JText::_("COM_BIODIV_SCHOOLREGISTER_SCHOOL_DETAILS").'</h3>';

print '<div class="form-group">';
print '<label for="schoolName" class="h4">'.JText::_("COM_BIODIV_SCHOOLREGISTER_SCHOOL_NAME").'</label>';
print '<input type="text" id="schoolName" name="schoolName" value = "" class="form-control schoolSetupInput">';
print '</div>';
	
print '<div class="form-group">';
print '<label for="postcode" class="h4">'.JText::_("COM_BIODIV_SCHOOLREGISTER_POSTCODE").'</label>';
print '<input type="text" id="postcode" name="postcode" value = "" class="form-control schoolSetupInput">';
print '</div>';
	
print '<div class="form-group">';
print '<label for="website" class="h4">'.JText::_("COM_BIODIV_SCHOOLREGISTER_WEBSITE").'</label>';
print '<input type="url" id="website" name="website" value = "" class="form-control schoolSetupInput">';
print '</div>';

print '<div class="form-group">';
print '<label for="wherehear" class="h4">'.JText::_("COM_BIODIV_SCHOOLREGISTER_WHEREHEAR").'</label>';
print '<input type="text" id="wherehear" name="wherehear" value = "" class="form-control schoolSetupInput">';
print '</div>';

if ( $this->recaptchaRequired ) {
	print '<div class="g-recaptcha" data-sitekey="'.$this->recaptchaSiteKey.'"></div>';
}

print '<h4>'.JText::_("COM_BIODIV_SCHOOLREGISTER_PLEASE_AGREE");//.JText::_("COM_BIODIV_SCHOOLREGISTER_TERMS");
print ' <a class="btn btn-default" href="'.JText::_("COM_BIODIV_SCHOOLREGISTER_POLICY_LINK").'" target="_blank" rel="noopener noreferrer" >'.JText::_("COM_BIODIV_SCHOOLREGISTER_POLICY_LINK_TEXT").'</a></h4>';
print '<h3 id="termsMessage" class="text-danger hidden">'.JText::_("COM_BIODIV_SCHOOLREGISTER_TERMS_FAIL").'</h3>';
print '<div class="checkbox">';
print '<label class="h4">';
print '<input type="checkbox" id="terms" name="terms" value="1">'.JText::_("COM_BIODIV_SCHOOLREGISTER_TERMS");
print '</label>';
print '</div>';

print '<div></div>';
print '<div class="vSpaced">';
print '<button type="submit" class="btn btn-primary btn-lg spaced">'.JText::_("COM_BIODIV_SCHOOLREGISTER_SIGN_UP").'</button>';
print '</div>';

print '</form>';

print '<div id="signUpFailMessage"></div>';

print '</div>'; // col-12

print '</div>'; // row
	
print '</div>'; // displayArea



print JHtml::_('form.token');

JHTML::script("com_biodiv/schoolregister.js", true, true);
JHTML::script("https://www.google.com/recaptcha/api.js", true, true);




?>





