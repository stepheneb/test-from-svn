<?php

$page_title = 'Not Found';

echo '
<p>The portal you were looking for was not found. Please select from one of the following:</p>

<ul>
';

foreach ($portal_config['project_settings'] as $key => $value) {

	$url = str_replace('www', $key, $_SERVER['HTTP_HOST']);

	// lookup the project information
	//$project_info = portal_lookup_project_info($key);
	//if (count($project_info) > 0) {}

	if ($key != 'www') {

		echo '
		<li><a href="http://' . $url . '/">' . $value['display_name'] . '</a></li>
		';
		
	}

}

echo '
</ul>
';

?>
