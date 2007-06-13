<?php

/*
 * foundation.php - This file is the foundation for all programming frameworks.
 * 
 * Each site page request includes this file first.  This file then includes the
 * necessary resources.
 * 
 */

// The following allows us to easily include content in the frameworks directory

$framework_location = dirname(__FILE__);

$current_include_path = ini_get('include_path');
    
$pathdivider = ":";

$re1 = '~' . $framework_location . $pathdivider . '~'; //eph
$re2 = '~' . $pathdivider . $framework_location . '~'; //eph

if (!preg_match($re1, $current_include_path) && !preg_match($re2, $current_include_path) && $current_include_path != $framework_location) {
	
	// only add the new path if it isnt alreday in the path
	
	$new_include_path = 
		$framework_location . $pathdivider .
		$current_include_path;
	
	ini_set('include_path', $new_include_path); 

}

// Include required resources

// main configuration options should be first
include_once 'configuration/configuration.php';

// include any developer overrides
include_once 'overrides.php';

// include support for accessing the database
include_once 'database/mystery_library.php';

// include Portal custom code library
include_once 'portal_library.php';

// include custom language setup
include_once 'localization.php';

// include the main URL procesessing controller functions
include_once 'controller.php';

// include support for XML in PHP 4
//include_once 'minixml/minixml.inc.php';

// the website auto-template must be added last
include_once 'website/sassy.php';

?>
