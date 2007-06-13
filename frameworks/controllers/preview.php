<?php

$page_title = 'Units Preview';

$activity_grid = portal_generate_activity_grid(array(), array(), 'preview');

echo '
<h2>Activities</h2>

	' . $activity_grid . '

';

?>