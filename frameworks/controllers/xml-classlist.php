<?php
  header('Content-type: application/xml');
  $class_uuid = $_PORTAL['action'];
  // get the first column of the first row
  $class_id = portal_get_class_id($class_uuid);
  $students = portal_get_class_students($class_id);
//  print "<!-- \n";
//  print_r($students);
//  print " -->\n";
?>
<otrunk id='<?php print "$class_uuid"; ?>'>
  <imports>
    <import class='org.concord.framework.otrunk.wrapper.OTObjectSet' />
    <import class='org.concord.otrunk.user.OTUserObject' />
  </imports>
  <objects>
    <OTObjectSet>
      <objects>
      <?php
          foreach ($students as $student) {
              print "<OTUserObject name='" . $student['member_first_name'] . " " . $student['member_last_name'] . "'>\n";
	      print "<userDataMap>\n";
              print "<entry key='portal_member_id'><string>" . $student['member_id'] . "</string></entry>\n";
              print "<entry key='diy_member_id'><string>" . $student['diy_member_id'] . "</string></entry>\n";
              print "<entry key='sds_member_id'><string>" . $student['sds_member_id'] . "</string></entry>\n";
              print "<entry key='cc_member_id'><string>" . $student['cc_member_id'] . "</string></entry>\n";
              print "</userDataMap>\n";
              print "</OTUserObject>\n";
          }
      ?>
      </objects>
    </OTObjectSet>
  </objects>
</otrunk>
<?php
  exit;
?>
