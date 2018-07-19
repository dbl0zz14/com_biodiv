<?php
// find codes from names or names from codes for various types of entity
// define the lookups for the various types
// also define any other tables in which the codes are used, used
// when deleting or re-coding an organisation

// function to setup or return parameters used by codes functions
// change from version 2 to take individually named parameters
// table parameter is a list of pairs (tablename, DB)
// debug if set will show most queries
function codes_parameters($name, $val = null){
	static $parameters;
	if(!$parameters){
		$parameters = array('table' => array(),
							'debug' => false,
							'usecache' => false);
	}
	if($val !== null){
		$parameters[$name] = $val;
	}
	if (isset($parameters[$name])) {
		return $parameters[$name];
	}
	return null;
}

function codes_parameters_addTable($table,$DB){
	$tableList = codes_parameters('table');
	array_push($tableList, array($table,$DB));
	if(codes_parameters('debug')){
		html_warning("Codes: added table $DB $table");
	}
	codes_parameters('table', $tableList);
}


function codes_warning($msg){
	//db_warning($msg);
}

function codes_eval($expr){
	ob_start();
	eval("?>$expr");
	$retval = ob_get_contents();
	ob_end_clean();
	return $retval;
}

function codes_getMeta($type, $cachemeta = 1){
	static $meta;
	if(!$meta or !$cachemeta){
		$tables = codes_parameters("table");
		foreach($tables as $table){
			list($tablename, $DBname) = $table;
			if(codes_parameters("debug")){
				html_warning("Codes: looking in $tablename,$DBname");
			}
			$tablename = codes_eval($tablename);
			$DBname = codes_eval($DBname);
			if(!$DBname or !$tablename){
				continue;
			}

			$query = "SELECT * FROM $tablename";
			$db = db();
			$metaRes = $db->query($query);
			while ($metaLine = $metaRes->fetch_assoc()) {
				$meta[$metaLine['structure']] = $metaLine;
				if(codes_parameters("debug")){
					html_warning("Codes: Added structure");
					print_r($metaLine);
				}

				if(codes_parameters('usecache')){
					cache_dependencies($metaLine['structure'],
					                   "list",
					                   array(array($metaLine['table'],$metaLine['database'])));
				}
			}
			if(codes_parameters('debug')){
//				codes_warning($query." ".$DBName);
			}
		}
		if(codes_parameters('debug')){
			print_r($meta);
		}
	}

	$meta[$type]['table'] = codes_eval($meta[$type]['table']);

	if(isset($meta[$type]['restriction']) && $restriction = $meta[$type]['restriction']){
		$meta[$type]['restriction'] = codes_eval($restriction);
	}
	else {
		$meta[$type]['restriction'] = '1';
	}

	if($database = $meta[$type]['database']){
		$meta[$type]['database'] = codes_eval($database);
	}

	if(isset($meta[$type]['orderby'])){
           $orderby = $meta[$type]['orderby'];
           $meta[$type]['orderby'] = codes_eval($orderby);
	}

	return $meta[$type];
}

function codes_getCode($thisname, $type, $options = array()){
	$typeMeta = codes_getMeta($type);
	$database = $typeMeta["database"];
	$table = $typeMeta["table"];
	$code = $typeMeta["code"];
	$name = $typeMeta["name"];
	if (isset($options['url_name'])) {
		$name = $typeMeta['url_name'];
	}
	$restriction = $typeMeta["restriction"];
	if($table){
		$query = "SELECT $code AS 'code' FROM $table where $name LIKE '$thisname'";
		if($restriction){
			$query .= " AND ".$restriction;
		}
		$db = db();
		$qr = $db->query($query);
		/*
		if ($qr->num_rows > 0) {
			if (isset($qr[0]['code'])) {
				return $qr[0]['code'];
			}
		}
		*/
		if ($qrline = $qr->fetch_assoc()) {
			return $qrline['code'];
		}
	}
}

function codes_getFriendlyName($thiscode, $type) {
	$typeMeta = codes_getMeta($type);
	if(isset($typeMeta['friendlyname']) and $friendlyfield = $typeMeta['friendlyname']){
		$details = codes_getDetails($thiscode, $type);
		$friendlyname = trim($details[$friendlyfield]);
	}
	if(!(isset($friendlyname) and $friendlyname)){
		$friendlyname = codes_getName($thiscode, $type);
	}
	return $friendlyname;
}


function codes_getName($thiscode, $type){
	static $names;
	if(!isset($names[$type][$thiscode])){
		$typeMeta = codes_getMeta($type);
		$code = $typeMeta["code"];
		$name = $typeMeta["name"];

		$table = $typeMeta["table"];
		
		$database = $typeMeta["database"];
		if(!$table or !$database){
			codes_warning("No meta data for structure $type");
		}

		$restriction = $typeMeta["restriction"];
		
		// if the name field starts with a % then it describes a function
		// not a field
		if(substr($name,0,1) == "%"){
			$nameFunction = substr($name,1);
			return ($names[$type][$thiscode] = $nameFunction($thiscode));
		}
		if(strpos($name,",")===false){
			$details = codes_getDetails($thiscode, $type);
			return ($names[$type][$thiscode] = $details[$name]);
		}

		// the name may be split over more than one field
		// this is OK as long as the field names are separated by commas
		if($table){
			$query = "SELECT $name FROM $table where $code='$thiscode'";
			if($restriction){
				$query .= " AND ".$restriction;
			}

			$db = db();
			$qr = $db->query($query);
			if ($qr->num_rows > 0) {
				$nameLine = $qr[0];
				$name = implode(" ",array_reverse($nameLine));
				$names[$type][$thiscode] = $name;
			}
			else{
				codes_warning("No such code for $type: $thiscode");
			}
		}
		else{
			codes_warning("No such entity type: $type");
		}
	}
	return $names[$type][$thiscode];
}


function codes_getTitle($type){
	$typeMeta=codes_getMeta($type);
	return $typeMeta["title"];
}

function codes_getCodeField($type){
	$typeMeta=codes_getMeta($type);
	return $typeMeta["code"];
}

function codes_getNameField($type){
	$typeMeta=codes_getMeta($type);
	return $typeMeta["name"];
}

function codes_getTable($type){
	$typeMeta=codes_getMeta($type);
	return $typeMeta["table"];
}

function codes_getDatabase($type){
	$typeMeta=codes_getMeta($type);
	return $typeMeta["database"];
}

function codes_getDetails($thiscode, $type, $features=null){
	static $details;
	// N.B. there are two types of cache: static variable cache (used here)
	// and DB cache as provided by the cache library
	$cachemeta=1;

	if($features['clearcache']){
		//html_warning("Clearing cache for $type");
		unset($details[$type]);
		$cachemeta=0;
	}

	if(!isset($details[$type][$thiscode])){
		unset($details[$type]);
		$typeMeta = codes_getMeta($type, $cachemeta);
		$database = $typeMeta["database"];
		$table = $typeMeta["table"];
		$code = $typeMeta["code"];
		$name = $typeMeta["name"];
		$restriction = $typeMeta["restriction"];
		$cachelimit = (isset($typeMeta["cachelimit"]) ? $typeMeta['cachelimit'] : 1);

		// if an alias name is used for code then we need to fix it
		$codebits = explode(".", $code);
		$codefieldbase = array_pop($codebits);
		if(count($codebits)>0){
			$alias = array_pop($codebits).".";
		}
		else{
			$alias = "";
		}

		if($table){
			$query = "SELECT $alias* FROM $table WHERE 1";
			if($restriction){
				$query .= " AND ".$restriction;
			}
			//			if($cachelimit == 1){
			if(true){
				$query .= " AND $code='$thiscode'";
			}
			elseif($cachelimit>1){
				// do nothing: load the whole table into the cache
				//	$query.=" AND $code>='$thiscode' ORDER BY $code LIMIT $cachelimit";
			}

			if(codes_parameters('debug')){
				html_warning($query);
			}

			$db = db();
			$qr = $db->query($query);

			$considerMultiple = array();
			while($resLine = $qr->fetch_assoc()) {
				$linecode = $resLine[$codefieldbase];
				$details[$type][$linecode] = $resLine;
				$considerMultiple[] = $linecode;
			}

			if(isset($typeMeta["multiple"]) && $typeMeta["multiple"]){
				$query = "SELECT * FROM Multiple WHERE ID IN('".implode("','",$considerMultiple)."') AND struc='$type' ORDER BY ID, Attr, Sequence";
				if(codes_parameters('debug')){
					html_warning($query);
				}

				$res = $db->queryArray($query);
				foreach ($res as $row) {
					$details[$type][$row['ID']][$row['Attr']][$row['Sequence']]=$row['Val'];
					$details[$type][$row['ID']]["MULTI_".$row['Sequence']."_".$row['Attr']]=$row['Val'];
				}
			}
		}
		else{
			codes_warning("No such entity type: $type");
		}
	}
        if(isset($details[$type][$thiscode])){
           return $details[$type][$thiscode];
        }
        return null;
}

function codes_describe($type){
	$typeMeta=codes_getMeta($type);
	$database=$typeMeta["database"];
	$table=$typeMeta["table"];
	$code=$typeMeta["code"];
	$name=$typeMeta["name"];
	$db = db();
	$query="SHOW COLUMNS FROM ".$table;
	$descRes = $db->queryArray($query);

	print ("<br/><strong>".$typeMeta["title"]."</strong>");
	print "<ol>\n";
	foreach ($descRes as $descLine) {
		print ("<li>".$descLine['Field']."</li>\n");
	}
	print "</ol>\n";
	if($table){
		$query="SELECT * FROM $table WHERE $code='$thiscode'";
		$qr = $db->queryArray($query);
		if (isset($qr[0])) {
			return $qr[0];
		}
		else {
			return false;
		}
	}
	else{
		codes_warning("No such entity type: $type");
	}
}

// generate a hyperlink for this thing
function codes_getLink($thiscode,$type,$baseURL){
	$link="<a href=\"$baseURL$thiscode\">";
	$link.=codes_getName($thiscode,$type);
	$link.="</a>";
	return $link;
}

// generate a list of options for as an unnumbered list
// with the given given base URL
function codes_getLinks($type, $baseURL,$features=array()){
	$options=codes_getList($type,$features);
	$stuff="<ul>\n";
	foreach($options as $option) {
		list($thisCode,$thisName)=$option;
		$link="<a href=\"$baseURL$thisCode\">";
		$link.=$thisName;
		$link.="</a>";
		$stuff.="<li>$link</li>\n";
	}
	$stuff.="</ul>\n";
	return $stuff;
}

// codes and names as JSON string as used by x-editable
// e.g. [{value: 1, text: "text1"}, {value: 2, text: "text2"}, ...]

function codes_getListJSON($type, $features = array()){
  $items = array();
  foreach(codes_getList($type, $features) as $thing){
    list($code, $name) = $thing;
    $item = new stdClass;
    $item->value = $code;
    $item->text = $name;
    $items[] = $item;
  }
  return addCSlashes(json_encode($items),"'");
}

// all the codes and names as a list of pairs
function codes_getList($type,$features=array()){
	$typeMeta=codes_getMeta($type);
	if(function_exists("getYear")) {
		$features['year']=getYear();
	}

	if(codes_parameters("usecache") and $typeMeta['cachelimit']>0){
		if($list=cache_fetch($type,"list",$features)){
			return $list;
		}
	}
	$database=$typeMeta["database"];
	$table=$typeMeta["table"];
	$code=$typeMeta["code"];
	$name=$typeMeta["name"];
	$restriction=$typeMeta["restriction"];
	$restrictions=array();
	if($restriction){
		$restrictions[]=$restriction;
	}
	if(isset($features['restriction']) && $frestriction=$features['restriction']){
		$restrictions[]=$frestriction;
	}
	$query="SELECT $code AS 'code' FROM $table ";
	if(count($restrictions)){
		$query.=" WHERE ".implode(" AND ",$restrictions);
	}
	if(!isset($features['order']) || !$order=$features['order']){
		if(!isset($typeMeta['orderby']) || !$order=$typeMeta['orderby']) {
			// only set to order by $name if $name is not a function
			if(substr($name,0,1) != "%") {
				$order=$name;
			}
			else {
				$order=$code;
			}
		}
	}
	$query.=" ORDER BY $order";

	$db = db();
	if(!$optionRes = $db->query($query)){
	  print $query;
	  print "Error: ". $db->error;
	}
	$list=array();
	while ($optionLine = $optionRes->fetch_assoc()) {
		$code = $optionLine['code'];
		$name = codes_getName($code, $type);
		if (isset($features['url_name']) && $features['url_name']) {
			if (isset($typeMeta['url_name']) && strlen($typeMeta['url_name']) > 0) {
				$details = codes_getDetails($code, $type);
				$code = $details[$typeMeta['url_name']];
			}
		}
		$list[]=array($code, $name);
	}
	if(codes_parameters("usecache")  and $typeMeta['cachelimit']>0){
		cache_add($type,"list",$features,$list);
	}
	return $list;
}

// generate a list of options for a <select>
// with the given elemented pre-selected
function codes_getOptions($thiscode, $type, $features=array()){
	$width = (isset($features['width']) ? $features['width'] : 50);

	if (isset($features['url_name']) && $features['url_name']) {
		$meta = codes_getMeta($type);
		if (isset($meta['url_name']) && strlen($meta['url_name']) > 0) {
			if (is_array($thiscode)) {
				$newCodes = array();
				foreach ($thiscode as $code) {
					$details = codes_getDetails($code, $type);
					$newCodes[] = $details[$meta['url_name']];
				}
				$thiscode = $newCodes;
			}
			else {
				$details = codes_getDetails($thiscode, $type);
				$thiscode = $details[$meta['url_name']];
			}
		}
	}

	$options = '';
	foreach(codes_getList($type,$features) as $listitem){
		list($lcode,$lname)=$listitem;
		$options .= '<option value="' . $lcode . '"';
		if (($thiscode and $lcode == $thiscode) || is_array($thiscode) && in_array($lcode, $thiscode)) {
			$options.= " selected='selected'";
		}
		$options .= ">";
		$options .= substr($lname,0,$width);
		$options .= "</option>\n";
	}
	return $options;
}

/** generate a list of checkboxes - similar to getOptions */
function codes_getCheckboxes($input_name, $type, $features=array()){
	$width = (isset($features['width']) ? $features['width'] : 50);

	$thiscode=$features['thiscode'];

	foreach(codes_getList($type,$features) as $listitem){
		list($lcode,$lname)=$listitem;

		$options.= "<input name='".$input_name."[]' type='checkbox' value='$lcode'";

		// RD 2008-06-20 - Added ID and label code
		$id = $input_name."[".$lcode."]";

		if($lcode==$thiscode ||
			(is_array($thiscode)
			&& in_array($lcode,$thiscode))
			){
			$options.= " checked='checked'";
		}
		$options.= " id='$id'/> <label for='$id'>".substr($lname,0,$width)."</label><br />\n";
	}
	return $options;
}

function codes_getRadiobuttons($input_name, $type, $features=array()){
	$width = (isset($features['width']) ? $features['width'] : 50);

	$thiscode=$features[$input_name];
	
	$options = "";

	foreach(codes_getList($type,$features) as $listitem){
		list($lcode,$lname)=$listitem;
		$options.= "<input name='$input_name' type='radio' value='$lcode'";
		if($lcode==$thiscode){
			$options.= " checked='checked'";
		}
		$options.= " />".substr($lname,0,$width)."<br />\n";
	}
	return $options;
}

// just return a list of codes, by default in order of code
function codes_allCodes($type, $features=array()){
	$meta=codes_getMeta($type);
	if(!isset($features['order']) || (isset($features['order']) && !$features['order'])) {
		$features['order']=codes_getCodeField($type);
	}
	$list=codes_getList($type,$features);
	$codes=array();
	foreach($list as $item){
		list($code,$name)=$item;
		$codes[]=$code;
	}
	return $codes;
}

function codes_rowRestrictions($struc,$rowrestrictions){
	$restrictions=array();

	if(is_array($rowrestrictions)){
		foreach($rowrestrictions as $restrictstruc => $restrictvals){
			unset($field);


			$vals=$restrictvals;


			if($struc==$restrictstruc) {
				$field=codes_getCodeField($struc);
			}
			else {
				if(!function_exists("restrictionField")) {
					continue;
				}
				list($field,$vals)=restrictionField($struc,$restrictstruc,$restrictvals);
			}

			if(!is_array($vals)) {
				$vals=explode(",",$vals);
			}


			if($field){
				$restrictions[]="$field IN('".implode("','",$vals)."')";
				global $debug;
				if($debug){
					html_warning("restrictions");
					print_r($restrictions);
				}
			}
		}
	}
	if(count($restrictions)){
		$restriction=implode(" AND ",$restrictions);
	}
	else{
		$restriction="1";
	}
	return $restriction;
}

// create a page asking user to choose a structure from a pull-down in the specified variable
function codes_choosePage($struc,$var,$blurb="",$url="") {
	$page=new pageInfo("Choose ".codes_getTitle($struc));
	$page->top();
	if(!$url) {
		$url=$_SERVER['PHP_SELF'];
	}

	print "<form action='$url'>\n";
	print $blurb;

	print "<select name='$var'>";
	print "<option value=''>Choose ".codes_getTitle($struc)."</option>\n";
	print codes_getOptions("","$struc");
	print "</select>\n";
	print "<input type='submit' value='Continue'/>\n";
	print "</form>\n";
	$page->bottom();
}

?>
