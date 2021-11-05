<?php
/**
* @package Joomla.Administrator
* @subpackage com_biodiv
*/
 
// No direct access to this file
defined('_JEXEC') or die;
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
* Kiosk Classify top level
*
* @since 0.0.1
*/
class BioDivViewKioskIntro extends JViewLegacy
{
    /**
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */

    public function display($tpl = null) 
    {
		// Assign data to the view
		//($person_id = (int)userID()) or die("No person_id");
		$app = JFactory::getApplication();

		$this->projectId =
		(int)$app->getUserStateFromRequest('com_biodiv.project_id', 'project_id', 0);

		if ( !$this->projectId ) die ("no project id given" );

		$this->project = projectDetails($this->projectId);

		$this->user_key = 
		$app->getUserStateFromRequest('com_biodiv.user_key', 'user_key', 0);

		if ( !$this->user_key ) {
			$this->user_key = JRequest::getString("user_key");
			$app->setUserState('com_biodiv.user_key', $this->user_key);
		}

		error_log("Kiosk View: user_key = " . $this->user_key);

		// Get the text snippets - enables multilingual
		$this->translations = getTranslations("kioskclassify");

		// get the url for the project image
		$this->projectImageUrl = projectImageURL($this->projectId);
		
		$db = JDatabase::getInstance(dbOptions());
		
		$query = $db->getQuery(true)
				->select("OD.value")
				->from("OptionData OD")
				->innerJoin("Options O on O.option_id = OD.option_id and O.struc = 'kiosk'")
				->innerjoin("ProjectOptions PO on PO.option_id = OD.option_id and OD.data_type = 'introcategory'")
				->where("PO.project_id = " . $this->projectId);
		$db->setQuery($query); 
		
		$this->categoryId = $db->loadResult();
		
		error_log ( "KioskIntro got category id = " . $this->categoryId );
		
		
		$dbo = JFactory::getDbo();
		$query1 = $dbo->getQuery(true)
			->select('*')
			->from('#__content')
			->where('catid = ' . $this->categoryId )
			->where('state = 1')
			->order('ordering');
		
		error_log("content query1 created: " . $query1->dump() );
		
		$dbo->setQuery($query1);
		
		$res = $dbo->loadObjectList();
		
		error_log ( "KioskIntro got content, num rows = " . count($res)  );
		
		$isFirst = true;
		print '<div class="col-md-12 spaced_row">';
		foreach ($res as $r) {
			/* echo '<h3>'.$r->title.'</h3>'; */
			if ( $isFirst ) {
				print '<div class="col-md-12">';
				print $r->introtext;
				print '</div>';
				$isFirst = false;
			}
			else {
				print '<div class="col-md-4">';
				print $r->introtext;
				print '</div>';
			}
			
			
			//print  "<p>" . $r->id . "</p>";
			//$intro = $r->introtext;
			//echo "<a href=index.php?option=com_content&view=article&id='$r->id'>$intro</a>";
			//echo '<hr>';
		} 
		
		print '</div>'; // col-12
		
		
		error_log ( "KioskIntro got content, about to display" );
		
		// Display the view
		parent::display($tpl);
    }
}



?>