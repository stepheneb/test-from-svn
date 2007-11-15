<?php

switch($_PORTAL['activity']) {

	case 'edit':	
		$id_param = $_PORTAL['action'];
		$page_title = 'Edit a class';
		$class_info = portal_get_class_info($id_param);
	break;
	
	case 'copy':	
		$id_param = $_PORTAL['action'];
		$page_title = 'Copy a class';
		$class_info = portal_get_class_info($id_param);
		$class_info['class_name'] = $class_info['class_name'] . ' Copy';
		@$class_info['class_word'] = '';
	break;
	
	case 'add':
		$id_param = 'new';
		$page_title = 'Add a class';
		$class_info = array();
		$class_info['activities'] = array();
		$class_info['diy_activities'] = array();
	break;

}

if ($_SESSION['portal']['member_type'] != 'superuser' && $_SESSION['portal']['member_type'] != 'admin' && $_SESSION['portal']['member_type'] != 'teacher') {

	mystery_redirect('/');
	exit;

}

// FIXME - Add a check here to see if this is the class teacher if the role is a teacher

if (isset($_PORTAL['params']['process'])) {

	$data = array();

	$data['class_name'] = $_REQUEST['class_name'];
	$data['class_school'] = $_SESSION['portal']['member_school'];
	$data['class_teacher'] = $_SESSION['portal']['member_id'];

	//mystery_print_r($_REQUEST, $_PORTAL, $data); exit;
	
	// check the class word
	
	$class_word_in_use = 'no';
	
	$class_using_word = portal_check_class_word($_REQUEST['class_word']);
	
	if ($class_using_word != $id_param && $class_using_word != false) {
		$class_word_in_use = 'yes';
	}
	
	if ($_REQUEST['class_word'] != '' && $class_word_in_use == 'no') {
	
		if ($_PORTAL['activity'] == 'add' || $_PORTAL['activity'] == 'copy') {
	
			$data['creation_date'] = date('Y-m-d H:i:s');
	
			$class_id = mystery_insert_query('portal_classes', $data, 'class_id', 'portal_dbh');
			$class_info['activities'] = array();
			$class_info['diy_activities'] = array();
	
		} else {
		
			$class_id = $id_param;
	
			$status = mystery_update_query('portal_classes', $data, 'class_id', $class_id, 'portal_dbh');
		
		}
		
		// update class word with the actual class word
		
		portal_set_class_word($class_id, $_REQUEST['class_word']);
		
		// add the activities here
		
		$new_activities = $_REQUEST['activities'];
		
		$old_activities = @$class_info['activities'];
		
		$status = portal_subscribe_class_to_activities($class_id, $old_activities, $new_activities);

		$new_activities = $_REQUEST['diy_activities'];
		
		$old_activities = @$class_info['diy_activities'];
		
		$status = portal_subscribe_class_to_diy_activities($class_id, $old_activities, $new_activities);

		
		echo '
		<h2>Class ' . ucfirst($_PORTAL['activity']) . ' Successful</h2>
		
		<p>The class ' . $_PORTAL['activity'] . ' was successful.</p>
		
		<p>Students can sign up for this class using the following sign-up word:</p>
		
		<p><span class="important-highlight-word">' . $_REQUEST['class_word'] . '</span></p>
		
		<h3>Next Steps</h3>
		
		<ul>
		
		<li><a href="/">Return to my home page</a></li>
		
		<li><a href="/class/add/">Add a new class</a></li>

		<li><a href="/class/edit/' . $class_id . '/">Edit this class</a></li>

		<li><a href="/class/copy/' . $class_id . '/">Make a copy of this class (and its activity selections)</a></li>

		<li><a href="/class/preview/' . $class_id . '/">Preview this class as your students will see it</a></li>

		</ul>
		
		';
		
		// mystery_redirect('/');
		// exit;
	
	} else {
	
		$errors = array('Another class is already using that <strong>Sign Up Word</strong>.  Please choose another.');
		
		echo portal_generate_error_page($errors);
	
	}
	
} else {

	$class_info = portal_web_output_filter($class_info);
	
	// generate the activity grid
	
	$activity_grid = portal_generate_activity_grid($class_info['activities'], $class_info['diy_activities'], 'setup');

	$total_activity_count = count($class_info['activities']) + count($class_info['diy_activities']);

	$section_counts = array();
	
	/*
	
	$all_activities = array_merge($class_info['activities'], $class_info['diy_activities']);
	
	foreach ($all_activities as $activity) {
	
		$section_counts[] += 1;
	
	}
	
	*/

	// generate the form

	echo '
	<form action="/class/' . $_PORTAL['activity'] . '/' . $id_param . '/process/" method="post">
	
	<h1>' . $page_title . '</h1>
	
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
	
	<tr>
	
	<td valign="top">
	<p><label for="class-name">Class Name</label> <input type="text" name="class_name" id="class-name" value="' . @$class_info['class_name'] . '" size="35"> <span class="form-field-info"><strong>Example:</strong> Honors Physics, Period 3</span></p>
	</td>
	
	<td valign="top">
	<p><label for="class-word">Sign-up Word</label> <input type="text" name="class_word" id="class-word" value="' . @$class_info['class_word'] . '" size="35"> <span class="form-field-info">This word is used by your students to sign up <br>in this class. It must be unique in <br>the system. Examples: pickle, plasma</span></p>
	</td>
	
	</tr>
	</table>
	
	<p><label for="submit">&nbsp;</label> <input type="submit" id="submit" value="Save this Class"></p>
	
	<div class="clear-both">&nbsp;</div>
		
	<h2>Activities <span class="heading-description">(Total Selected: <span id="total-selected">' . $total_activity_count . '</span>)</span></h2>

	' . $activity_grid . '

	</form>
	
	<script type="text/javascript">
	
		var totalActivities = ' . $total_activity_count . ';
		
		function updateTotalActivities(obj) {
		
			if (obj.checked == 1) {
				totalActivities++;
			} else {
				totalActivities--;
			}
		
			updateTotalActivitiesDisplay();
		
		}
		
		function updateSectionActivities(sectionid, obj) {
		
			if (obj.checked == 1) {
				sectionActivities[sectionid]++;
			} else {
				sectionActivities[sectionid]--;
			}
		
			updateSectionActivitiesDisplay(sectionid);
		
		}
		
		function updateTotalActivitiesDisplay() {

			var s = document.getElementById("total-selected");
			s.innerHTML = totalActivities.toString();

		}
		
		function updateSectionActivitiesDisplay(sectionid) {
			
			//console.log(sectionid);
			
			var s = document.getElementById(sectionid + "-count");
			
			//console.log(s);
			
			var cd = "";
			
			//console.log(sectionActivities[sectionid]);
			
			if (sectionActivities[sectionid] > 0) {
				cd = " (" + sectionActivities[sectionid].toString() + ")";
			}
			
			s.innerHTML = cd;
		
		}
	
		function select_activity_checkboxes(unitid, sectionid, checkbox_control) {
		
			var unit = document.getElementById(unitid);
			
			var inputs = unit.getElementsByTagName("INPUT");
			
			for (var i = 0; i < inputs.length; i++) {
			
				if (inputs[i].type == "checkbox") {
				
					if (inputs[i].checked != checkbox_control.checked) {
					
						inputs[i].checked = checkbox_control.checked;
						
						updateTotalActivities(inputs[i]);
		
						updateSectionActivities(sectionid, inputs[i]);
					
					}
				
				}
			
			}
		
		
		}

		function initialize_activity_counts() {
			// reset counts to 0
			totalActivities = 0;
			for (var section in sectionActivities) {
				sectionActivities[section] = 0;
			}

			var inputs = document.getElementsByTagName("INPUT");
			for (var i = 0; i < inputs.length; i++) {
				if (inputs[i].type == "checkbox") {
					if (inputs[i].parentNode.nodeName != "LABEL") {
						if (inputs[i].checked) {
							var section = inputs[i].parentNode.parentNode.parentNode.parentNode.id;
							totalActivities++;
							sectionActivities[section]++;
						}
					}
				}
			}

			updateTotalActivitiesDisplay();
			for (var s in sectionActivities) {
				updateSectionActivitiesDisplay(s);
			}
		}

		$(document).ready(
			function() { 
			 initialize_activity_counts();	
			}
		);
		
	</script>
	';

}

?>
