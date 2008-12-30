<?php
   $script_start = microtime(true);

   $page_title = "Statistics";
   echo '<div class="statistics">';
   // if the current user isn't an admin, redirect to the home page
   echo '<ul>';
   $portal_teachers = portal_get_all_teachers($_PORTAL['project_info']['project_id']);
   // print_r($portal_teachers);
   // for each teacher in the current portal
   foreach ($portal_teachers as $teacher) {
     echo '<li>' . $teacher['member_first_name'] . " " . $teacher['member_last_name'] . " (" . $teacher['member_id'] . ")" . "</li>";
     $class_ids = portal_get_teacher_classes($teacher['member_id']);
     // for each class that the teacher has
     echo '<ul>';
     foreach ($class_ids as $class_id) {
       $class_activities = portal_get_class_diy_activities($class_id);
       if (count($class_activities) == 0) {
         continue;
       }
       $registered_students = portal_get_class_students($class_id);
       if (count($registered_students) == 0) {
         continue;
       }
       $class = portal_get_class_info($class_id);
       // count the number of students registered
       echo '<li>' . $class['class_name'] . ': ' . count($registered_students) . ' students</li>';
       // count the number of students that have a learner session
       $activity_usage = array();
       foreach ($registered_students as $stu) {
         $used = portal_get_diy_activity_usage_from_db($stu['member_id']);
         foreach ($used as $act_id) {
           if (array_key_exists($act_id,$activity_usage)) {
             $activity_usage[$act_id]++;
           } else {
             $activity_usage[$act_id] = 1;
           }
         }
       }
       // list the activities for the class
       echo '<ul>';
       foreach ($class_activities as $activity) {
         echo '<li>' . $activity['activity_name'] . ' (' . (array_key_exists($activity['activity_id'], $activity_usage) ? $activity_usage[$activity['activity_id']] : "0") . ' active students)' . '</li>';
       }
       echo '</ul>';
     }
     echo '</ul>';

   }
   echo '</ul>';
   echo '</div>';

   $script_end = microtime(true);

   $elapsed_time = round($script_end - $script_start, 5);

   echo '<div style="">Rendered in ' . $elapsed_time . ' seconds</div>';
?>
