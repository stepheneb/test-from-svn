<?php
/**************************************************************

Sassy! 2.0

Developed by Paul Burney
Web: paulburney.com
AIM: PWBurney
E-mail: support@paulburney.com

***************************************************************

sassy.php

This file should be included into all of your php files.  It will
provide an HTML wrapper for your content or webapp.

***************************************************************

CONFIGURATION

This file contains all of the configuration for the site. 

**************************************************************/

// initialize the configuration array - do not change
$sassy_config = array();

// included files (templates need %%PAGE_CONTENT%% where the page contents go)
$sassy_config['standard_template'] = $portal_config['site_template'];
$sassy_config['navigation_file'] = 'configuration/navigation.' . $portal_config['default_language'] . '.xml';

// general info
$sassy_config['site_title'] = 'ITSI Portal'; // this is prepended to the page title
$sassy_config['admin_name'] = 'Webmaster'; // this is displayed in the contact link at the bottom
$sassy_config['admin_email'] = 'webmaster@concord.org'; // the email address for the site maintainer
$sassy_config['company_name'] = 'The Concord Consortium'; // used in the copyright notice

// default page titles, descriptions and keywords
$sassy_config['page_description'] = '';
$sassy_config['page_keywords'] = '';
$sassy_config['page_title'] = '';

// regular expression used to remove directory indicies from links
$sassy_config['directory_index_re'] = '~(index|default).(html|htm|php)~';

// regular expression used to remove directory indicies from links
$sassy_config['excluded_file_re'] = '~\.(htm)$~';

// error reporting level
$sassy_config['reporting_level'] = E_ALL;

// initialization functions
$sassy_config['init_functions'] = array();

// additional include files
$sassy_config['additional_includes'] = array();

//$sassy_config['additional_includes'][] = 'globallab.php';

// other settings
$sassy_config['breadcrumb_file_name'] = '.DIR_NAME';
$sassy_config['breadcrumb_root_label'] = 'Home';

//$sassy_config['pad_content_threshold'] = 1500;
//$sassy_config['pad_content_lines'] = 30;


// configuration of navigation

// you can use the actual navigation arrays here if you wish instead of the XML doc



/***************************************************************

GENERAL PURPOSE FUNCTIONS

These are the common Sassy! functions

***************************************************************/


// This function sends email with nice headers
function sassy_send_email($to,$from,$subject,$body,$message_id='',$cc=array(),$bcc=array()) {
 
	// This function sends an email with the appropriate headers
	// We can mess with the $eol for different email servers/platforms if necessary
	
	global $sassy_config;
	
	if ($message_id = '') { $message_id = '<sassy-' . md5(uniqid('')) . '@' . $_SERVER['HTTP_HOST'] . '>'; }
	
	$disclaimer = '';
	
	$strip_to = preg_replace('~.*?<(.*?)>~', '\1', $to);
	$strip_from = preg_replace('~.*?<(.*?)>~', '\1', $from);
	
	// this is the proper $eol for all email
	$eol = "\r\n";
	
	$headers = array();
	
	$headers[] = "Return-Path: <" . $strip_from . ">";
	$headers[] = "Bounce-To: " . $strip_from;
	$headers[] = "From: " . $from;
	$headers[] = "Reply-To: " . $from;
	$headers[] = 'Content-type: text/plain; charset="utf-8"';
	
	if (count($cc) > 0) {
		$headers[] = "Cc: " . implode(', ', $cc);
	}

	if (count($bcc) > 0) {
		$headers[] = "Bcc: " . implode(', ', $bcc);
	}
	
	$headers[] = "Message-Id: " . $message_id;
	$headers[] = "X-Mailer: PHP/" . phpversion();
	$headers[] = "X-Mail-Origin: " . @$_SERVER['HTTP_HOST'] . " web to email system";
	$headers[] = "X-Sender-Network-Address: " . @$_SERVER['REMOTE_ADDR'];
	$headers[] = "X-Disclaimer: This is an automated message from a system at " . @$_SERVER['HTTP_HOST'] . ".";
	$headers[] = "X-Apparent-Source-Page: http://" . @$_SERVER['HTTP_HOST'] . @$_SERVER['REQUEST_URI'];
	$headers[] = "X-Site-Maintainer: " . $sassy_config['admin_email'];
	
	$header_string = implode($eol, $headers);
	
	$cmd_param = ' -f' . $strip_from;
	
	$body = preg_replace("~\r\n|\r|\n~", $eol, $body);
	$body = $disclaimer . $body;
	
	if (!mail($to, $subject, $body, $header_string, $cmd_param)) { 
		trigger_error('Could not send e-mail', E_USER_WARNING);
		return 0; 
	} else { 
		return 1; 
	}

}


function sassy_redirect($location) {

	// This is a universal redirect function that will attempt to make a redirect work
	// with browsers that don't understand 301/302 response headers (rare)
	
	if (!preg_match('~^http://~', $location)) {
		$location = 'http://' . $_SERVER['HTTP_HOST'] . $location;
	}
	
	if (!headers_sent()) {

		@header("Location: $location");
	
	} else {
	
		echo '
		<html>
		<head>
			<title>Loading...</title>
			<meta http-equiv="refresh" content="0; URL=' , $location , '">
		</head>
	
		<body bgcolor="#ffffff" onload="location.href=\'' , $location , '\'">
			<p><a href="' , $location , '">Follow this link to continue</a></p>
		</body>
	
		</html>
		';
	
	}

	exit;

}


function sassy_print_r() {

	// This function is a nicer version of the standard print_r function.  It supports
	// multiple arguements and displays preformatted in html with a distinct background
	// if you pass a parameter beginning with a #, it will be used as the new background
	// color other than the default royal blue (#000099)

	$numargs = func_num_args();
	$arg_list = func_get_args();

	$bg = '#000099';

	ob_start();
	for ($i = 0; $i < $numargs; $i++) {
		if (@$arg_list[$i][0] == '#') {
			$bg = $arg_list[$i];
		} else {
			print_r($arg_list[$i]);
		}
	}
	$output = ob_get_contents();
	ob_end_clean();
	
	echo '<pre style="margin: 2em; padding: 2em; border: 2 dotted; background-color: ' . $bg . '; color: #ffffff;">' , htmlspecialchars($output) , '</pre>';


}


// this function sends headers to the browser to make the page, hopefully, un-cacheable
function sassy_expire_page() {

	header("Expires: Wed, 9 Aug 1995 11:20:00 GMT");    // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache"); // HTTP/1.0

}


// this function makes http/ftp/mailto URL's in a given piece of text active
function sassy_links_alive($text) {

	$text = preg_replace('~(http://|https://|ftp://|mailto:)([^\s]+)~', '<a href="\1\2">\1\2</a>', $text);
	return $text;
}

// this function generates a breadcrumb trail
function sassy_generate_breadcrumbs($glue = ' -&gt; ', $before = '', $after = '') {

	/* Universal Breadcrumbs
	
	Just put a file named .DIR_NAME in each directory and include this file
	into the desired page.  A breadcrumb trail will be displayed. */
	
	global $page_title, $sassy_config, $_SASSY;
	
	// initialize variables
	$dir_index = 'no';
	$description = '';
	$title = '';
	$top_index = 'no';
	$trail_parts = array();
	
	// get environment variables
	$this_request = rawurldecode($_SASSY['request']);
		
	// 
	if (preg_match('~^/$~', $this_request)) { $top_index = 'yes'; }
	if (preg_match('~/$~', $this_request)) { $dir_index = 'yes'; }
	
	if ($top_index != 'yes') { // don't show bread crumbs at very top page of site
		
		$dirs = explode('/', $this_request);
		
		$l = (count($dirs));
		
		for ($i = 1; $i < $l; $i++) {
				
			$my_url = '/';
		
			for ($j = 1; $j < $i; $j++) {
				$my_url .= $dirs[$j] . '/';
			}
			
			$this_dirname = '';
			
			$fp = @fopen($_SERVER['DOCUMENT_ROOT'] . $my_url . $sassy_config['breadcrumb_file_name'], 'r');
			if ($fp) {
				$this_dirname = fread($fp,1000);
				fclose($fp);
			}
		
			
			if ($this_dirname == '') {
			
				// couldn't get the file, so just use the directory name
				$this_dir_part = ucwords(strtolower(preg_replace('~[_-]~', ' ', $dirs[$i - 1])));
				
				if ($this_dir_part == 'Faq') { $this_dir_part = 'FAQ'; }

				// if that directory doesn't exist, we must be at the root
				if ($this_dir_part == '') {$this_dir_part = $sassy_config['breadcrumb_root_label'];}

				$title = $this_dir_part;
				$description = $this_dir_part;

			} else {

				list($title,$description) = explode("\t", $this_dirname);

			}
			
			if ($i == ($l - 1)) {
			
				if ($dir_index == 'yes') {
				
					$trail_parts[] = '<strong>' . $page_title . '</strong>';
				
				} else {
				
					$trail_parts[] = '<a href="' . $my_url . '" title="' . $description . '">' . $title . '</a>';
					$trail_parts[] = '<strong>' . $page_title . '</strong>';
				
				}
			
			} else {
			
				$trail_parts[] = '<a href="' . $my_url . '" title="' . $description . '">' . $title . '</a>';
			
			}
						
		}
						
	}

	if (count($trail_parts) > 0) {

		return $before . implode($glue,$trail_parts) . $after;

	} else {

		return '';

	}

}

function sassy_get_single_nav_label($links, $request) {

	// given a typical sassy link array, this function determines what the appropriate label is, if any
	
	$n = count(@$links['urls']);
	
	if ($n == 0) {
		return '';
	}
	
	//cc_print_r($links, $request);
	
	$root_label = '';
	
	for ($i = 0; $i < $n; $i++) {
	
		if ($links['urls'][$i] == '/') {
		
			$root_label = $links['labels'][$i]; 

		} elseif (preg_match('~/$~', $links['urls'][$i]) && $i == 0) {

			$root_label = $links['labels'][$i]; 

		} elseif (preg_match('~^' . $links['urls'][$i] . '~i', $request)) {
		
			// cc_print_r($links['urls'][$i], ' +=+=+ ', $request);

			return $links['labels'][$i];

		}
	
	}

	return $root_label;

}


// this function looks through the nav arrays to determine what the current main nav and sub nav labels are

function sassy_get_nav_labels() {

	// this is the rewritten version of the function.
	// This page doesn't work: http://chroma.concord.org/courses/why_experts.html
	// But this one does: http://chroma.concord.org/publications/newsletter/   

	// PB: Boy this function is ugly with all those associative arrays... Please fix me.
	
	// it has something to do with those root labels... what are they and why are they so important...

	global $sassy_config, $_SASSY;

	static $nav_labels_set = 'no';
	
	if ($nav_labels_set == 'yes') { return; }
	
	// determine the nav labels

	$_SASSY['this_main_nav_label'] = sassy_get_single_nav_label(@$sassy_config['main_nav_links'], $_SASSY['request']);

	$_SASSY['this_sub_nav_label'] = sassy_get_single_nav_label(@$sassy_config['sub_nav_links'][$_SASSY['this_main_nav_label']], $_SASSY['request']);

	$_SASSY['this_add_nav_label'] = sassy_get_single_nav_label(@$sassy_config['add_nav_links'][$_SASSY['this_main_nav_label']][$_SASSY['this_sub_nav_label']], $_SASSY['request']);

	$_SASSY['this_extra_nav_label'] = sassy_get_single_nav_label(@$sassy_config['extra_nav_links'][$_SASSY['this_main_nav_label']][$_SASSY['this_sub_nav_label']][$_SASSY['this_add_nav_label']], $_SASSY['request']);
		
	// finally, set this variable so we don't need to perform this check again
	
	$nav_labels_set = 'yes';
	
	//cc_print_r($sassy_config['add_nav_links'][$_SASSY['this_main_nav_label']][$_SASSY['this_sub_nav_label']]['urls']);
	
	// cc_print_r($_SASSY);

}

// this function takes a give link and bolds it if it is the current location and 
// makes an href if not.
function sassy_highlight_link($url, $label, $desc, $a_class = '', $sa_class = '', $s_class = '') {

	global $_SASSY;
	
	$a_class = '';
	$sa_class = 'nav-selected';
	$s_class = 'nav-selected';
	
	// initialize other variables
	$this_class = '';
	
	// check the url to see how it matches
	if (preg_match('~^' . $url . '$~', $_SASSY['request']) || $url == '') {
		
		// this is the exact page, so don't make a link to itself; use strong class
		if ($s_class != '') { $this_class = ' class="' . $s_class . '"'; }
		return '<strong' . $this_class . '>' . htmlspecialchars($label) . '</strong>';
	
	} elseif ($label == $_SASSY['this_main_nav_label'] || $label == $_SASSY['this_sub_nav_label'] || $label == $_SASSY['this_add_nav_label'] || $label == $_SASSY['this_extra_nav_label']) {
	
		// this is a page under the currently selected menu section; use link selected class
		if ($sa_class != '') { $this_class = ' class="' . $sa_class . '"'; }
		return '<a href="' . $url . '" title="' . htmlspecialchars($desc) . '"' . $this_class . '>' . htmlspecialchars($label) . '</a>';
		
	} else {
	
		// this is a page apart from the selected menu section; use regular class
		if ($a_class != '') { $this_class = ' class="' . $a_class . '"'; } 
		return '<a href="' . $url . '" title="' . htmlspecialchars($desc) . '"' . $this_class . '>' . htmlspecialchars($label) . '</a>';	
	
	}

}

// new generate_navbar class
// this function generates a main navigation bar for the web site
function sassy_generate_navbar($type = 'main', $glue = ' | ', $before = '', $after = '', $styled = 'yes', $line_template = '') {

	global $_SASSY, $sassy_config;

	// link string generation
	$link_string_parts = array();
	$links = array();
	
	// determine what the nav labels are
	sassy_get_nav_labels();
	
	switch ($type) {
	
		case 'main':
		
			$links = $sassy_config['main_nav_links'];
			$sub = '';
			
		break;
		
		case 'sub':
		
			if (isset($sassy_config['sub_nav_links'][$_SASSY['this_main_nav_label']])) {
				$links = $sassy_config['sub_nav_links'][$_SASSY['this_main_nav_label']];
			}
			$sub = 'sub';
		
		break;
	
		case 'add':
		
			if (isset($sassy_config['add_nav_links'][$_SASSY['this_main_nav_label']][$_SASSY['this_sub_nav_label']])) {
				$links = $sassy_config['add_nav_links'][$_SASSY['this_main_nav_label']][$_SASSY['this_sub_nav_label']];
			}
			$sub = 'add';
		
		break;
	
		case 'extra':
			
			if (isset($sassy_config['extra_nav_links'][$_SASSY['this_main_nav_label']][$_SASSY['this_sub_nav_label']][$_SASSY['this_add_nav_label']])) {
				$links = $sassy_config['extra_nav_links'][$_SASSY['this_main_nav_label']][$_SASSY['this_sub_nav_label']][$_SASSY['this_add_nav_label']];
			}
			$sub = 'extra';
		
		break;
	
	}
	
	// loop through the items in the links array and process
	if (isset($links['labels'])) {
	
		for ($i = 0; $i < count($links['labels']); $i++) {
			
			$this_link = '';
			
			// Watch out for unescaped tildes...
			$links['urls'][$i] = str_replace('~','%7e',$links['urls'][$i]);
			
			// generate the link string parts
			
			$a_class = $sub . 'menu';
			$sa_class = 'selected' . $sub . 'menu';
			$s_class = 'selected' . $sub . 'menu';

			if ($styled != 'yes') {
				$a_class = '';
				$sa_class = '';
				$s_class = '';
			}
			
			$this_link = sassy_highlight_link($links['urls'][$i],$links['labels'][$i],$links['comments'][$i],$a_class,$sa_class,$s_class);

			if ($line_template != '') {
				$this_link = preg_replace('~%%LINK%%~', $this_link, $line_template);
				if (@$links['classes'][$i] != '') {
					$this_link = preg_replace('~%%CLASS%%~', $links['classes'][$i],$this_link);
				}
			}
			
			$link_string_parts[] = $this_link;
			
		}
	
	}
	
	if (count($link_string_parts) > 0) {
	
		return $before . implode($glue, $link_string_parts) . $after;
	
	} else {
	
		return '';
	
	}
	
}


// this function genterates a multilevel complete nav bar, highlighting 
// the current location.  By default it does this using nested unordered 
// lists but you can modify the call to produce a nested table, for 
// example.  Key: Before/After are placed around the whole block, 
// whereas Start/End are placed around each item.
function sassy_generate_combo_nav($before = '', $main_before = '<ul type="square">', $main_start = '<li>', $sub_before = '<ul type="disc">', $sub_start = '<li>', $add_before = '<ul type="circle">', $add_start = '<li>', $add_end = '</li>', $add_after = '</ul>', $sub_end = '</li>', $sub_after = '</ul>', $main_end = '</li>', $main_after = '</ul>', $after = '') {
	
	global $sassy_config, $_SASSY;
	
	// determine what the nav labels are
	sassy_get_nav_labels();

	$this_map = '';
	
	$this_map .= $main_before;
	
	for ($i = 0; $i < count($sassy_config['main_nav_links']['labels']); $i++) {
	
		$current_main_nav_label = $sassy_config['main_nav_links']['labels'][$i];
		
		$this_map .= $main_start . highlight_link($sassy_config['main_nav_links']['urls'][$i],$sassy_config['main_nav_links']['labels'][$i],$sassy_config['main_nav_links']['comments'][$i]);
	
		if (($current_main_nav_label == $_SASSY['this_main_nav_label']) && isset($sassy_config['sub_nav_links'][$current_main_nav_label])) {
			
			$this_map .= $sub_before;
	
			for ($j = 0; $j < count($sassy_config['sub_nav_links'][$_SASSY['this_main_nav_label']]['labels']); $j++) {
			
				$current_sub_nav_label = $sassy_config['sub_nav_links'][$_SASSY['this_main_nav_label']]['labels'][$j];
			
				$this_map .= $sub_start . highlight_link($sassy_config['sub_nav_links'][$_SASSY['this_main_nav_label']]['urls'][$j],$sassy_config['sub_nav_links'][$_SASSY['this_main_nav_label']]['labels'][$j],$sassy_config['sub_nav_links'][$_SASSY['this_main_nav_label']]['comments'][$j]);
				
				if (($current_sub_nav_label == $_SASSY['this_sub_nav_label']) && isset($sassy_config['add_nav_links'][$_SASSY['this_main_nav_label']][$_SASSY['this_sub_nav_label']])) {
				
					$this_map .= $add_before;
					
					for ($k = 0; $k < count($sassy_config['add_nav_links'][$_SASSY['this_main_nav_label']][$_SASSY['this_sub_nav_label']]['labels']); $k++) {
					
						$this_map .= $add_start . highlight_link($sassy_config['add_nav_links'][$_SASSY['this_main_nav_label']][$_SASSY['this_sub_nav_label']]['urls'][$k],$sassy_config['add_nav_links'][$_SASSY['this_main_nav_label']][$_SASSY['this_sub_nav_label']]['labels'][$k],$sassy_config['add_nav_links'][$_SASSY['this_main_nav_label']][$_SASSY['this_sub_nav_label']]['comments'][$k]) . $add_end;
					
					}
					
					$this_map .= $add_after;
				
				}
				
				$this_map .= $sub_end;
			
			}
		
			$this_map .= $sub_after;
		}
	
		$this_map .= $main_end;
		
	}
	
	$this_map .= $main_after;
	
	if ($this_map != '') {

		return $before . $this_map . $after;
	
	} else {
	
		return '';
	
	}

}

// this function makes a page into its printable HTML version
function sassy_make_printable($text) {
	
	// be very careful with tildes
	$text = str_replace('~', '%7e', $text);
	
	// get rid of targets
	$text = preg_replace('~ (target|title)="[^"]+"~is','', $text);
		
	// temporarily replace strong, i, em, b, code, u, etc to make sure we can zap all links
	$text = preg_replace('~<(/?)(strong|i|em|b|code|u)>~is','[$1$2]', $text);

	// fix any href spacing issues
	$text = preg_replace('~href=[\s]*~','href=',$text);
	
	//echo '<pre>' , htmlspecialchars($text) , '</pre>'; exit;

	// fix local absolute links
	$text = preg_replace('~href="/([^"]*)"~is','href="http://' . $_SERVER['HTTP_HOST'] . '/$1"',$text);
	
	// fix relative links
	$this_request_directory = substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['REQUEST_URI'], '/') + 1);
	$text = preg_replace('~href="([^h][^t][^t][^p][^:][^/][^/])([^"]+)"~is','href="http://' . $_SERVER['HTTP_HOST'] . $this_request_directory . '$1$2"',$text);
	
	// get a list of all the links
	preg_match_all('~href="([^"]+)~is',$text,$matches0);
	
	// get rid of the anchor links on the page
	$matches = array();
	for ($i = 0; $i < count($matches0[1]); $i++) {
		if (!preg_match('~(^#|' . $_SERVER['SCRIPT_NAME'] . ')~', $matches0[1][$i])) {
			$matches[1][] = $matches0[1][$i];
		}
	}
	
	// for each url, replace it in the page with a number and underline the text
	for ($i = 0; $i < count(@$matches[1]); $i++) {	
		$text = preg_replace('~<a\s+href="' . $matches[1][$i] . '">([^<]+?)</a>~is','<u>$1</u><sup>' . ($i + 1) . '</sup>',$text);
	}
			
	// put back any html tags we replaced
	$text = preg_replace('~\[(/?)(strong|i|em|b|code|u)\]~is','<$1$2>', $text);

	// if there were any links, append them to page body
	if (count(@$matches[1]) > 0) {
	
		$link_list = '<br clear="all"><p><strong>Links</strong></p><ol><li>' . implode('</li><li>', $matches[1]) . '</li></ol>';

		$text = preg_replace('~</body~is', $link_list . '</body', $text);

	}

	return $text;

}


// text to html function

function sassy_text2html($text) {

    // fix line breaks
    $text = str_replace("\r\n","\n",$text); // windows to unix
    $text = str_replace("\r","\n",$text); // mac to unix
    $text = preg_replace("~^\n+~",'',$text); // trim beginning linebreaks
    $text = preg_replace("~\n+$~",'',$text); // trim ending linebreaks
    
    // convert to html entities
    $text = htmlspecialchars($text);
    
    // add paragraphs
    $text = "\n<p></p>\n<p>" . str_replace("\n","</p>\n\n<p>", $text) . "</p>\n<p></p>\n";
    
    // add horizontal rules    
    $text = preg_replace('~<p>[\s]*?[-=_]{5,}[\s]*?</p>~', '<hr>',$text);
    
    // add unordered lists
    $text = preg_replace('~<p>[\s]*?(\*|\.|\-)-?[\s]*?([^\s].*?)</p>~', '<li>\2</li>',$text);
    $text = preg_replace('~</p>[\s]*?<li>~', "</p>\n\n<ul>\n<li>",$text);
    $text = preg_replace('~</li>[\s]*?<p>~', "</li>\n</ul>\n\n<p>",$text);
    
    // add ordered lists
    $text = preg_replace('~<p>[\s]*?[\d#]+(\.|\))[\s]*?([^\s].*?)</p>~', '<li>\2</li>',$text);
    $text = preg_replace('~</(p|ul)>[\s]*?<li>~', "</\\1>\n\n<ol>\n<li>",$text);
    $text = preg_replace('~</li>[\s]*?<(p|ul)>~', "</li>\n</ol>\n\n<\\1>",$text);
        
    // add blockquotes
    $text = preg_replace('~<p>[\t ]+?([^\s].*?)</p>~', '<blockquote>\1</blockquote>',$text);
    $text = preg_replace('~</blockquote>[\s]*?<blockquote>~', "\n\n",$text);
    
    // remove empty tags
    $text = str_replace("\n<p></p>\n", '', $text);
    
    // add styles
    //$text = preg_replace('~_([^ _][^_]*?)_~','<em>\1</em>',$text); // set italics
    $text = preg_replace('~\*([^ _][^_]*?)\*~','<strong>\1</strong>',$text); // set bolds
    
    // optimize any white space
    $text = preg_replace('~ +~',' ', $text);
    $text = preg_replace("~\t+~","\t", $text);
    $text = preg_replace("~\n+~","\n", $text);
    
    // remove adjacant lists
    $text = preg_replace("~</ol>\n<ol>~", '',$text);
    $text = preg_replace("~</ul>\n<ul>~", '',$text);
        
    // highlight any links (watch out encroaching tags and for punctuation)
    $text = preg_replace('~(http://|https://|ftp://|mailto:)([^\s<]+[^\.!?&,\)\(\<\-\s])~', '<a href="\1\2">\1\2</a>', $text);
    
    // done - return
    return $text;

}

function sassy_generate_site_map () {

	global $sassy_config;
	
	$this_map = '';
	
	// change the last zero to something to rebalance the columns
	
	$half = ceil(count($sassy_config['main_nav_links']['labels']) / 2) - 0;
	
	$this_map .= '<table summary="this table contains two columns containing most of the main links in this site"><tr><td valign="top">';
	
	for ($i = 0; $i < count($sassy_config['main_nav_links']['labels']); $i++) {
	
		$this_main_nav_label = $sassy_config['main_nav_links']['labels'][$i];
		
		$this_map .= '
		<h2><a href="' . $sassy_config['main_nav_links']['urls'][$i] . '" title="' . $sassy_config['main_nav_links']['comments'][$i] . '">' . $sassy_config['main_nav_links']['labels'][$i] . '</a></h2>
		';

		if (isset($sassy_config['sub_nav_links'][$this_main_nav_label])) {
			
			$this_map .= '
			<ul type="disc">
			';

			for ($j = 0; $j < count($sassy_config['sub_nav_links'][$this_main_nav_label]['labels']); $j++) {
			
				$this_sub_nav_label = $sassy_config['sub_nav_links'][$this_main_nav_label]['labels'][$j];
			
				$this_map .= '
				<li><a href="' . $sassy_config['sub_nav_links'][$this_main_nav_label]['urls'][$j] . '" title="' . $sassy_config['sub_nav_links'][$this_main_nav_label]['comments'][$j] . '">' . $sassy_config['sub_nav_links'][$this_main_nav_label]['labels'][$j] . '</a>';
				
				if (isset($sassy_config['add_nav_links'][$this_main_nav_label][$this_sub_nav_label])) {
				
					$this_map .= '
					<ul type="circle">
					';
					
					for ($k = 0; $k < count($sassy_config['add_nav_links'][$this_main_nav_label][$this_sub_nav_label]['labels']); $k++) {
					
						$this_map .= '
						<li><a href="' . $sassy_config['add_nav_links'][$this_main_nav_label][$this_sub_nav_label]['urls'][$k] . '" title="' . $sassy_config['add_nav_links'][$this_main_nav_label][$this_sub_nav_label]['comments'][$k] . '">' . $sassy_config['add_nav_links'][$this_main_nav_label][$this_sub_nav_label]['labels'][$k] . '</a>
						';
					
					}
					
					$this_map .= '
					</ul>
					';
				
				}
				
				$this_map .= '</li>	
				';
			
			}
		
			$this_map .= '
			</ul>
			';
		}
		
		if ($i == $half) {
		
			$this_map .= '</td><td valign="top">';
		
		}
		
	}
	
	$this_map .= '</td></tr></table>';
	
	return $this_map;
	
}

function sassy_get_html_meta_information($text) {

	// this function takes some text and returns an associative array of any meta tags
	
	global $sassy_config;
	
	$meta = array();
	
	// First, get the title if available
	
	if (preg_match('~<title>(.*?)</title>~is',$text,$title_matches)) {
		$meta['title'] = $title_matches[1];
	}

	// Now look for other meta tags
	
	preg_match_all('~<meta(.*?)>~is',$text,$meta_matches);

	// Go through each and determine the name/content and add to array
	
	for ($i = 0; $i < count($meta_matches[1]); $i++) {
	
		// Split the meta tag contents based on the whitespace and quotes
				
		if (preg_match('~name="(.*?)"~is',$meta_matches[1][$i],$name_matches)) {
			$this_name = $name_matches[1];
		}
		
		if (preg_match('~content="(.*?)"~is',$meta_matches[1][$i],$content_matches)) {
			$this_content = $content_matches[1];
		}
		
		$meta[@$this_name] = $this_content;
	
	}
	
	if (@$meta['title'] == '') { $meta['title'] = 'Untitled'; }
	if (@$meta['description'] == '') { $meta['description'] = $meta['title'] . ' - ' . $sassy_config['page_description']; }
	if (@$meta['keywords'] == '') { $meta['keywords'] = $sassy_config['page_keywords'] . ', ' . str_replace(' ',', ', $meta['description']); }
	
	return $meta;

}

function sassy_create_directory($directory_path, $mode = 0777) {

	// this function makes any non existant directories in the given path
	
	$old_umask = umask(0);

	if (is_dir($directory_path)) {
		return true;
	}
	
	$parent_directory_path = dirname($directory_path);
	
	if (!sassy_create_directory($parent_directory_path, $mode)) {
		return false;
	} else {
		return mkdir($directory_path, $mode);
	}

	umask($oldumask);

}


function sassy_xml_parseXMLintoarray($xmldata) { 
	// starts the process and returns the final array
	$xmlparser = xml_parser_create();
	xml_parse_into_struct($xmlparser, $xmldata, $arraydat);
	xml_parser_free($xmlparser);
	$semicomplete = sassy_xml_subdivide($arraydat);
	$complete = sassy_xml_correctentries($semicomplete);
	return $complete;
}

function sassy_xml_subdivide($dataarray, $level = 1) {
	foreach ($dataarray as $key => $dat) {
		if ($dat['level'] === $level && $dat['type'] === "open") {
			$toplvltag = $dat['tag'];
		}
		elseif ($dat['level'] === $level && $dat['type'] === "close" && $dat['tag'] === $toplvltag) {
			$newarray[$toplvltag][] = sassy_xml_subdivide($temparray, ($level + 1));


			unset($temparray, $nextlvl);
		}
		elseif ($dat['level'] === $level && $dat['type'] === "complete") {
			$newarray[$dat['tag']] = @$dat['value'];
		}
		elseif ($dat['type'] === "complete" || $dat['type'] === "close" || $dat['type'] === "open") {
			$temparray[] = $dat;
		}
	}
	return $newarray;
}

function sassy_xml_correctentries($dataarray) {

	if (is_array($dataarray)) {
		$keys = array_keys($dataarray);
		if (count($keys) == 1 && is_int($keys[0])) {
			$tmp = $dataarray[0];
			unset($dataarray[0]);
			$dataarray = $tmp;
		}
		$keys2 = array_keys($dataarray);
		foreach($keys2 as $key) {
			$tmp2 = $dataarray[$key];
			unset($dataarray[$key]);
			$dataarray[$key] = sassy_xml_correctentries($tmp2);
			unset($tmp2);
		}
	}
	return $dataarray;
}

function sassy_parse_xml_navigation($file) {

	// this should really be a recursive function instead

	global $sassy_config;
	
	$xml = implode('', file($file, 1));

	$base = sassy_xml_parseXMLintoarray($xml);

	for ($i = 0; $i < count($base['NAVIGATION']['ITEM']); $i++) {
	
		$m_item = $base['NAVIGATION']['ITEM'][$i];

		$sassy_config['main_nav_links']['labels'][] = $m_item['LABEL'];
		$sassy_config['main_nav_links']['comments'][] = $m_item['DESCRIPTION'];
		$sassy_config['main_nav_links']['urls'][] = $m_item['URL'];
		$sassy_config['main_nav_links']['classes'][] = $m_item['CLASS'];
		
		for ($j = 0; $j < @count($m_item['ITEM']); $j++) {
		
			$s_item = $m_item['ITEM'][$j];
			
			$sassy_config['sub_nav_links'][$m_item['LABEL']]['labels'][] = $s_item['LABEL'];
			$sassy_config['sub_nav_links'][$m_item['LABEL']]['comments'][] = $s_item['DESCRIPTION'];
			$sassy_config['sub_nav_links'][$m_item['LABEL']]['urls'][] = $s_item['URL'];
			
			for ($k = 0; $k < @count($s_item['ITEM']); $k++) {
			
				$a_item = $s_item['ITEM'][$k];
				
				$sassy_config['add_nav_links'][$m_item['LABEL']][$s_item['LABEL']]['labels'][] = $a_item['LABEL'];
				$sassy_config['add_nav_links'][$m_item['LABEL']][$s_item['LABEL']]['comments'][] = $a_item['DESCRIPTION'];
				$sassy_config['add_nav_links'][$m_item['LABEL']][$s_item['LABEL']]['urls'][] = $a_item['URL'];
			
				for ($l = 0; $l < @count($a_item['ITEM']); $l++) {
				
					$e_item = $a_item['ITEM'][$l];
					
					$sassy_config['extra_nav_links'][$m_item['LABEL']][$s_item['LABEL']][$a_item['LABEL']]['labels'][] = $e_item['LABEL'];
					$sassy_config['extra_nav_links'][$m_item['LABEL']][$s_item['LABEL']][$a_item['LABEL']]['comments'][] = $e_item['DESCRIPTION'];
					$sassy_config['extra_nav_links'][$m_item['LABEL']][$s_item['LABEL']][$a_item['LABEL']]['urls'][] = $e_item['URL'];
				
				}

			}
			
		}
	
	}

}

function sassy_extract_content($text) {

	$text = str_replace('~', '%7e', $text);
	
	$find = '.*?<body[^>]*?' . '>(.*?)</body.*';
	
	$replace = '$1';
	
	// this started failing with very large pages so we'll just fall
	// back to the plain text if the next command kills our text
	
	$new_text = preg_replace('~' . $find . '~is', $replace, $text);
	
	if ($new_text == '') {
		//$new_text = $text;
	}
	
	return $new_text;

}


/**************************************************************

APPLICATION AND PROCESSING

This section sets up things like navigation and include paths.
Then it processes the page replacement.

**************************************************************/

// set up navigation

if (!isset($sassy_config['main_nav_links'])) {
	$sassy_config['main_nav_links'] = array();
}

if (!isset($sassy_config['sub_nav_links'])) {
	$sassy_config['sub_nav_links'] = array();
}

if (!isset($sassy_config['add_nav_links'])) {
	$sassy_config['add_nav_links'] = array();
}

if (!isset($sassy_config['extra_nav_links'])) {
	$sassy_config['extra_nav_links'] = array();
}

sassy_parse_xml_navigation($sassy_config['navigation_file']);



// set up include path

// Determine where this file is
$sassy_location = dirname(__FILE__);
$current_include_path = ini_get('include_path');
$pathdivider = ':';

// only add the this directory to the path if it isn't alreday there
if (!preg_match('~' . PATH_SEPARATOR . '?' . $sassy_location . PATH_SEPARATOR . '?~', $current_include_path)) {
	$new_include_path = $sassy_location . PATH_SEPARATOR . $current_include_path;
	ini_set('include_path', $new_include_path); 
}

// fix the backtrack problem in php 5

if (ini_get('pcre.backtrack_limit')) {
	ini_set('pcre.backtrack_limit', 1000000);
}

// initialize a container variable for current "session" information
$_SASSY = array();

// initialize variables
$_SASSY['this_main_nav_label'] = '';
$_SASSY['this_sub_nav_label'] = '';
$_SASSY['this_add_nav_label'] = '';
$_SASSY['this_extra_nav_label'] = '';

// this special request has no query string, nor index file on it
$_SASSY['request'] = preg_replace($sassy_config['directory_index_re'], '', preg_replace('~\?.*~', '', $_SERVER['REQUEST_URI']));

// setup error reporting
ini_set('error_reporting', $sassy_config['reporting_level']);

// Include any other files, if necessary.
for ($i = 0; $i < count($sassy_config['additional_includes']); $i++) {
	include $sassy_config['additional_includes'][$i];
}

// perform any necessary initialization functions
for ($i = 0; $i < count($sassy_config['init_functions']); $i++) {
	call_user_func($sassy_config['init_functions'][$i]);
}

// we process all files that don't have a .htm suffix and don't have display_headers = 'no' in them.

if (!preg_match($sassy_config['excluded_file_re'], $_SERVER['SCRIPT_FILENAME'])) {

	// a script can change the display headers value to turn off auto headers, default is on
	if (!isset($sassy_display_headers)) { $sassy_display_headers = 'yes'; }

	// capture the contents of the PHP page
	ob_start();
	include $_SERVER['SCRIPT_FILENAME'];
	$this_page = ob_get_contents();
	ob_end_clean();

	// if this is the printable version, convert the link text to bold and parenthize the url
	if (@$_REQUEST['version'] == 'print') {
		$this_template_file = $sassy_config['print_template'];
		$this_page = sassy_make_printable($this_page);
	} else {
		$this_template_file = $sassy_config['standard_template'];
	}

	if ($sassy_display_headers == 'yes') { 
	
		// pre/append the include files if headers should be displayed

		$meta = sassy_get_html_meta_information($this_page);
		
		// a page can override the following variables.  If not set, we get from text.
		
		if (!isset($page_title)) {
			$page_title = $meta['title'];
		}
		
		if (!isset($page_keywords)) {
			$page_keywords = $meta['keywords'];
		}
		
		if (!isset($page_description)) {
			$page_description = $meta['description'];
		}
		
		ob_start();
		include $this_template_file;
		$template_content = ob_get_contents();
		ob_end_clean();
		
		$this_page = sassy_extract_content($this_page);

		/*$page_length = strlen($this_page);
		
		if ($page_length < $sassy_config['pad_content_threshold']) {
			$lines_to_add = ceil((1 - ($page_length / $sassy_config['pad_content_threshold'])) * $sassy_config['pad_content_lines']);
			for ($i = 0; $i < $lines_to_add; $i++) {
				$this_page .= '
				<br><!-- ' . $i . ' -->';
			}
		}*/

		$this_page = preg_replace('~%%PAGE_CONTENT%%~', $this_page, $template_content);
		
		echo $this_page;
		
	} else {

		echo $this_page;

	}
	
	exit;

}

?>
