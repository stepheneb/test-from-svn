<?php

$page_title = 'Links';
/*
function portal_generate_link_list($parent_id = 0) {

	// select the links for this level, provide a way back up a level,
	// and link any additional links below to it.

	$query = 'SELECT * FROM portal_links WHERE link_parent = ? AND project_id = ? AND link_enabled = ? ORDER BY link_order';
	
	$params = array(0, $GLOBALS['_PORTAL']['project_info']['project_id'], 'Yes');
	
	$results = mystery_select_query($query, $params, 'portal_dbh');
	
	$links = array();
	
	for ($i = 0; $i < count($results); $i++) {
	
		$url = $results[$i]['link_href'];
		
		if ($url == '') {
			$url = '/links/' . $results[$i]['link_id'] . '/';
		}
	
		$links[] = '<li><a href="' . $url . '" title="' . $results[$i]['link_title'] . '">' . $results[$i]['link_nav_title'] . '</a></li>';
	
	}
		
	return $links;
}


function portal_generate_link_navigation() {

	// select the names of the top level items and generate the linked list
	

}
*/

function portal_generate_child_links($parent_link) {

	$links = portal_get_child_links($parent_link);
	
	$list = '';
	
	if (count($links) > 0) {
	
		$list .= '
		<ul class="child-link-list">
		';
		
		for ($i = 0; $i < count($links); $i++) {
		
			$url = $links[$i]['link_href'];
			
			if ($url == '') {
				$url = '/links/' . $links[$i]['link_id'] . '/';
			}
		
			$list .= '
			<li style="' . $links[$i]['link_style'] . '">
			<a href="' . $url . '">' . $links[$i]['link_title'] . '</a>
			<div>' . $links[$i]['link_description'] . '</div>
			</li>
			';
		
		}
		
		$list .= '
		</ul>
		';
		
	}
	
	return $list;

}

function portal_get_link_info($link_id) {

	$query = 'SELECT * FROM portal_links WHERE link_id = ?';
	
	$params = array($link_id);
	
	$results = mystery_select_query($query, $params, 'portal_dbh');
	
	if (count($results) > 0) {
	
		return $results[0];
	
	} else {
		
		return $results;
	
	}
	
}

$parent_link = $_PORTAL['activity'];

$link_info = portal_get_link_info($parent_link);

if (count($link_info) > 0) {

	echo '
	
	<h1>' . $link_info['link_title'] . '</h1>
	
	<div style="margin: 1em 0;">' . nl2br($link_info['link_description']) . '</div>
	
	' . portal_generate_child_links($parent_link) . '
	
	';

} else {

	echo '<p>Invalid Link</p>';

}


?>
