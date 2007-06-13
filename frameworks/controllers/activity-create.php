<?php

$page_title = 'Activity Creation';

echo '
<h2>Activity Creation</h2>

<p>Activities used in the ITSI project are created using a system called <strong><a href="/diy/home/" target="_blank">Do-It-Yourself</a></strong>.</p>

<h3>Make your own activity…</h3>

<p><a href="/diy/new/" target="_blank">' . portal_icon('add') . '</a> <a href="/diy/new/" target="_blank">Create a new activity</a> in the <strong>Do-It-Yourself</strong> site</p>

<h3>…or Copy an Existing Activity</h3>

<p>Click on the copy icon ' . portal_icon('copy') . ' next to an activity name to make your own customized version of it.</p>

';

echo portal_generate_activity_grid(array(), array(), 'setup');

?>
