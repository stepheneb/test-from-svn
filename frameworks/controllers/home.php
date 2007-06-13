<?php

$page_title = 'Home';

$school_id = $_SESSION['portal']['member_school'];

$options = array();
$options['type'] = 'compact';

$class_list = portal_generate_class_list($school_id, $_SESSION['portal']['member_id'], $options);

$student_list = portal_generate_student_list($school_id, $_SESSION['portal']['member_id'], $options);

echo '
<h2>My Classes <a class="heading-link" href="/class/add/">' . portal_icon('add') . ' Add a new class</a></h2>

' . $class_list . '

<br>

<h2>My Students <a class="heading-link" href="/student/add/">' . portal_icon('add') . ' Add a new student</a></h2>

' . $student_list . '

<br>

';

include 'controllers/member-info.php';

?>
