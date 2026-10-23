<?php


use Joomla\CMS\Language\Text;
use Joomla\CMS\Date\Date;
use Joomla\CMS\HTML\HTMLHelper;
use MammalWeb\Component\Biodiv\Site\Library\Core\Biodiv;

 
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

if ( $this->siteId == 0 ) {
	
	print '<h3>No site id</h3>';
}
else {

	$action = $this->root . "&view=uploadm";

	Biodiv::clearMessages();
	// Need to know whether this is reload following upload and if so include the alert...
	// if ( $this->previousUploadId ) {
		
		// if ( $this->previousNumFailed == 0 ) {
			// $this->trapperModel->showAlert ( 'success', 
					// Text::_("COM_BIODIV_UPLOAD_LAST_UP") . " - " . 
					// Text::_("COM_BIODIV_UPLOAD_NUM_UPLOADED") . " ". $this->previousNumUploaded );
		// }
		// else {
			// $this->trapperModel->showAlert ( 'error', 
					// Text::_("COM_BIODIV_UPLOAD_LAST_UP") . " - " . 
					// Text::_("COM_BIODIV_UPLOAD_NUM_FAILS") . " ". $this->previousNumFailed . ". " .
					// Text::_("COM_BIODIV_UPLOAD_NUM_UPLOADED") . " ". $this->previousNumUploaded);
		// }
	// }
	

	print "<h1>" . Text::_("COM_BIODIV_UPLOAD_UPLOAD_IM") . " " . $this->siteName . " " . Text::_("COM_BIODIV_UPLOAD_ID") . " " . $this->siteId . "</h1>";


	print "<div class='container'>";
	print "<div class='row'>";
	print "<div class='col-6'>";

	print "<h2>". Text::_("COM_BIODIV_UPLOAD_START_NEW")."</h2>";
	
	// <form action="<?php echo Route::_('index.php?option=com_sample_form2'); >"
		// method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

		// <?php echo $this->form->renderField('message');  >

		// <?php echo $this->form->renderField('email');  >

		// <?php echo $this->form->renderField('telephone');  >

		// <button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('myform.submit')">Submit</button>

		// <input type="hidden" name="task" />
		// <?php echo HtmlHelper::_('form.token'); >
	// </form>

	// Failed Joomla form version
	//print "<form id='uploadForm' role='form' action='".$this->root . "&task=add_upload"."'";
	//print " method='post' class='form-validate' >";
	
	print "<form id='uploadFilesForm' role='form' action='".$this->root . "&task=add_upload"."'";
	print " method='post' onsubmit='return validateUploadForm()'>";

	print "<input type='hidden' name='site_id' value='".$this->siteId."'/>";

	if ( $this->badge ) {
		print "<input type='hidden' name='badge' value='".$this->badge."'/>";
	}
	if ( $this->classId ) {
		print "<input type='hidden' name='class_id' value='".$this->classId."'/>";
	}
	//print '<div class="mb-3">';
	
	//print 'Fields here';
	
	// print $this->uploadForm->renderField('timezone');
	// print $this->uploadForm->renderField('dst');
	// print $this->uploadForm->renderField('deployment_date');
	// print $this->uploadForm->renderField('collection_date');
	
	//print '</div>';

	// // Deployment date and times only relevant for camera deployments, as opposed to recordings on phones etc.
	// // However timezone is relevant for all media types
	print "    <div class='form-group'>";
		 
	$timezone_text = Text::_("COM_BIODIV_UPLOAD_TIMEZONE");
	$dst_text = Text::_("COM_BIODIV_UPLOAD_DST");
	$dst_yes = Text::_("COM_BIODIV_UPLOAD_DST_YES");
	$dst_no = Text::_("COM_BIODIV_UPLOAD_DST_NO");
	$timezone_abbreviations = DateTimeZone::listAbbreviations();
	$timezone_ids = DateTimeZone::listIdentifiers();

	print  "<label for='timezone' style='display:block; margin-top: 20px;'>$timezone_text</label>";

	print  "<select id='timezone' name ='timezone' class='form-control'/>\n";
	print  "<option value='' disabled selected hidden>".Text::_("COM_BIODIV_UPLOAD_PLS_SELECT")."</option>";

	// Make UTC the first option: (should take the value from previous upload....)
	$utc = array_pop($timezone_ids);
	print "<option id='UTC'>$utc</option>\n"; // - UTC+00:00</option>\n";
	foreach ($timezone_ids as $zone_id) {
		print "<option id='$zone_id'>" . $zone_id . "</option>\n";
	}
	print "</select>";

	print  "<label for='dst' style='display:block; margin-top: 20px;'>$dst_text</label>\n";

	if ( $this->previousIsDst ) {
		print  "<div><input type='radio' id='dst_yes' name ='dst' value='1' checked> " . $dst_yes . "</div>";

		print  "<div><input type='radio' id='dst_no' name ='dst' value='0'> " . $dst_no . "</div>";
	}
	else {
		print  "<div><input type='radio' id='dst_yes' name ='dst' value='1'> " . $dst_yes . "</div>";

		print  "<div><input type='radio' id='dst_no' name ='dst' value='0' checked> " . $dst_no . "</div>";
	}

	print "    </div> <!-- /.form-group -->";

	if ( $this->isCamera ) {

		print "<div class='row' >";

		$now = new Date();
		$dForm = 'Y-m-d';
		$tForm = 'H:i';
		$hForm = 'H';
		$mForm = 'i';
		$dtForm = 'Y-m-dTh:i';
		
		$minDeploymentDate = '';
		$minDeploymentTime = '';
		$minDeploymentDateTime = '';
		$maxDeploymentDate = $now->format($dForm);
		$maxDeploymentTime = $now->format($tForm);
		$maxDeploymentDateTime = $maxDeploymentDate . 'T' . $maxDeploymentTime;
		
		$minCollectionDate = '';
		$minCollectionTime = '';
		$minCollectionDateTime = '';
		$maxCollectionDate = $now->format($dForm);
		$maxCollectionTime = $now->format($tForm);
		$maxCollectionDateTime = $maxCollectionDate . 'T' . $maxCollectionTime;
		
		if ( $this->previousCollectionDate ) {
			
			$minDeploymentDate = $this->previousCollectionDate->format($dForm);
			$minDeploymentTime = $this->previousCollectionDate->format($tForm);
			$minDeploymentDateTime = $minDeploymentDate . 'T' . $minDeploymentTime;
			
			$minCollectionDate = $this->previousCollectionDate->format($dForm);
			$minCollectionTime = $this->previousCollectionDate->format($tForm);
			$minCollectionDateTime = $minCollectionDate . 'T' . $minCollectionTime;
			
		}
		

		$field = "Deployment";
		$lfield = strtolower($field);
			  
		$date_text = Text::_("COM_BIODIV_UPLOAD_DEP_DATE");
		$time_text = Text::_("COM_BIODIV_UPLOAD_DEP_TIME");
		print "<div class='col-md-6' style='margin-top:20px;'>";
		print "   <div class='form-group'>";
		
		print "     <label for='{$lfield}_date' style='display:block;'>$date_text</label>\n";
		print "     <input type='datetime-local' class='form-control' name='{$lfield}_date' id='{$lfield}_date' value='".$minDeploymentDateTime.
						"' max='".$maxDeploymentDateTime."'/>";
		print "   </div>";
		print "</div> <!-- /.col -->";

		// print "<div class='col-md-8' style='margin-top:20px;'>";
		// print "  <div class='form-group'>";
		
		// print "     <label for='{$lfield}_time' style='display:block;'>$time_text</label>\n";
		// print "     <input type='time' class='form-control' name='{$lfield}_time' id='{$lfield}_time'  value='".$minDeploymentTime."'/>";	
		
		// print "  </div> <!-- /.form-group -->";
		// print "</div> <!-- /.col -->";
		
		
		
		$field = "Collection";
		$lfield = strtolower($field);
			  
		$date_text = Text::_("COM_BIODIV_UPLOAD_COLL_DATE");
		$time_text = Text::_("COM_BIODIV_UPLOAD_COLL_TIME");
		print "<div class='col-md-6' style='margin-top:20px;'>";
		print "   <div class='form-group'>";
		
		print "     <label for='{$lfield}_date' style='display:block;'>$date_text</label>\n";
		print "     <input type='datetime-local' class='form-control' name='{$lfield}_date' id='{$lfield}_date' value='".$maxCollectionDateTime.
						"' max='".$maxCollectionDateTime."'/>";
		print "   </div>";
		print "</div> <!-- /.col -->";

		// print "<div class='col-md-8' style='margin-top:20px;'>";
		// print "  <div class='form-group'>";
		
		// print "     <label for='{$lfield}_time' style='display:block;'>$time_text</label>\n";
		// print "     <input type='time' class='form-control' name='{$lfield}_time' id='{$lfield}_time'  value='".$maxCollectionTime."'/>";	
		
		// print "  </div> <!-- /.form-group -->";
		// print "</div> <!-- /.col -->";
		

		print "</div> <!-- .row -->";

	}	

	print " <div style='display: block; margin-top: 20px;'>";

	print '<h5 id="uploadDateErrorMsg" style="display:none">'.Text::_("COM_BIODIV_UPLOAD_DATE_ERROR").'</h5>';

	print "	<button type='submit' id='add_upload' class='btn btn-primary btn-lg'>".$this->trapperModel->biodivLabelIcons('upload', Text::_("COM_BIODIV_UPLOAD_UPLOAD"))."</button>";
	print " <button type='button' class='btn btn-outline-secondary btn-lg mw_help' data-dismiss='modal'>".$this->trapperModel->biodivLabelIcons('help', Text::_("COM_BIODIV_UPLOAD_HELP"))."</button>";
		
	print "  </div>";

	print HtmlHelper::_('form.token');	

	print "</form>";



	print "</div>"; // class='col-md-6'

	print "<div class='col-6'>";



	if ($this->previousUploadId){
		
		print "<h2>" . Text::_("COM_BIODIV_UPLOAD_LAST_UP") . "</h2>\n";
		print "<strong><p  style='margin-top:30px;'>" . Text::_("COM_BIODIV_UPLOAD_LAST_AT") . " ". $this->previousUploadDate . "</p>";
		if($this->isCamera && $this->previousCollectionDate){
			print "<p>" . Text::_("COM_BIODIV_UPLOAD_WITH_DEPL") . " ". $this->previousDeploymentDate . "</p>";
			print "<p>" . Text::_("COM_BIODIV_UPLOAD_WITH_COLL") . " ". $this->previousCollectionDate . "</p>";
			print "<p>" . Text::_("COM_BIODIV_UPLOAD_NUM_UPLOADED") . " ". $this->previousNumUploaded . "</p>";
			print "<p>" . Text::_("COM_BIODIV_UPLOAD_NUM_LARGE") . " ". $this->previousNumLarge . "</p>";
			
			if ( $this->previousNumOriginal > 0 ) {
				print "<p>" . Text::_("COM_BIODIV_UPLOAD_NUM_ORIGINAL") . " ". $this->previousNumOriginal . "</p>";
			}
			print "<p>" . Text::_("COM_BIODIV_UPLOAD_NUM_FAILS") . " ". $this->previousNumFailed . "</p>";

			$action_more = $this->root . "&task=upload_more";
			print "<p  style='margin-top:20px;'><a class='btn btn-outline-secondary btn-lg' role='button' href='$action_more&upload_id=". $this->previousUploadId. "'>" . $this->trapperModel->biodivLabelIcons('upload', Text::_("COM_BIODIV_UPLOAD_UP_MORE")) . "</a></p>";
		}
		print "</strong>";

		print "  <div style='margin-top:20px;'>";
		print "  <a class='btn btn-outline-secondary btn-lg' role='button' href='" . BIODIV_ROOT."&view=uploaderrors&upload_id=". $this->previousUploadId."'>".$this->trapperModel->biodivLabelIcons('list', Text::_("COM_BIODIV_UPLOAD_LIST_ERRORS"))."</a>";
		print "  </div>";
	
	}


	print "  <div style='margin-top:20px;'>";
	print "  <a class='btn btn-outline-secondary btn-lg' role='button' href='" . BIODIV_ROOT."&view=uploaded&site_id=". $this->siteId."'>".$this->trapperModel->biodivLabelIcons('list', Text::_("COM_BIODIV_UPLOAD_LIST_UP"))."</a>";
	print "  </div>";


	print "</div>"; // class col-md-6
	print "</div>"; // row
	print "</div>"; // container


	print "<div id='help_modal' class='modal fade' role='dialog'>";
	print "  <div class='modal-dialog modal-sm'>";

	print "    <!-- Modal content-->";
	print "    <div class='modal-content'>";
	print "      <div class='modal-header'>";
	print "        <button type='button' class='close' data-dismiss='modal'>&times;</button>";
	print "        <h4 class='modal-title'>".Text::_("COM_BIODIV_UPLOAD_HELP_TITLE")." </h4>";
	print "      </div>";
	print "      <div class='modal-body'>";
			if($this->title ){
				print "<div class='well'>\n";
				if ( $this->introtext ) {
					print "<div class='help-article'>".$this->introtext."</div>"; 
				}
				print "</div>\n";
			}
	print "      </div>";
	print "      <div class='modal-footer'>";
	print "        <button type='button' class='btn btn-danger classify-modal-button' data-dismiss='modal'>".Text::_("COM_BIODIV_UPLOAD_CLOSE")."</button>";
	print "      </div>";
	print "    </div>";

	print "  </div>";
	print "</div>";
	
	$this->document->getWebAssetManager()->usePreset('com_biodiv.upload');
	
	HTMLHelper::_('bootstrap.modal', '.selector', []);
	
}


// JHTML::script("com_biodiv/upload.js", true, true);
// JHTML::script("bootstrap-datepicker-master/bootstrap-datepicker.js", true, true);



?>


