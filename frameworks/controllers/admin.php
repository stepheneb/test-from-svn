<?php

$page_title = 'Administration Area';

$school_id = $_SESSION['portal']['member_school'];

$school_info = portal_get_school_info($school_id);

$options = array();

$options['type'] = 'compact';

echo '
<h2>Our School <a class="heading-link" href="/school/edit/' . $school_id . '/">' . portal_icon('setup') . ' Change this information</a></h2>

<p>
<strong>' . $school_info['school_name'] . '</strong><br>
' . $school_info['school_address_1'] . ' ' . $school_info['school_address_2'] . '<br>
' . $school_info['school_city'] . ', ' . $school_info['school_state'] . ' ' . $school_info['school_zip'] . '
</p>

<br>


<h2>Our Teachers <a class="heading-link" href="/teacher/add/">' . portal_icon('add') . ' Add a new teacher</a></h2>

' . portal_generate_teacher_list($school_id, 'compact') . '

<br>

<h2>Our Classes <a class="heading-link" href="/class/add/">' . portal_icon('add') . ' Add a new class</a></h2>

' . portal_generate_class_list($school_id) . '

<br>

<h2>Our Students <a class="heading-link" href="/student/add/">' . portal_icon('add') . ' Add a new student</a></h2>

' . portal_generate_student_list($school_id, '', $options) . '

';

?>
