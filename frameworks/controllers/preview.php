<?php

$page_title = '' . $portal_config['activities_navigation_word'] . ' Preview';

$activity_grid = portal_generate_activity_grid(array(), array(), 'preview');

echo '
<h2>' . $portal_config['activities_navigation_word'] . '</h2>

	' . $activity_grid . '

';

?>