<?php

$page_title = 'About';

echo '

<img src="' . @$_PORTAL['project_info']['project_logo'] . '" align="right" border="0">

<h2>' . @$_PORTAL['project_info']['project_title'] . '</h2>

<p>' . portal_web_output_filter(@$_PORTAL['project_info']['project_description']) . '</p>

<p>For more information, please <a href="' . @$_PORTAL['project_info']['project_url'] . '">visit the project website</a>.</p>

';

?>
