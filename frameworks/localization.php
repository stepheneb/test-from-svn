<?php

if (isset($_GET['lang'])) {

	$expire_seconds = 365*24*3600;
	
	mystery_cookie('lang', $_GET['lang'], $expire_seconds);

	$_COOKIE['lang'] = $_GET['lang'];
	
}

if (isset($_COOKIE['lang'])) {

	$portal_config['default_language'] = $_COOKIE['lang'];

}

// FIXME... add options here to parse the language file based on the default/selected language


?>