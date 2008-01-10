<?php

// This file provides lists of activities available from the diy

$page_title = 'Activity Listing';

echo '<p><em>This page will be used during the workshop to make it easier for everyone to share each other\'s activities.  After the workshop, this page
will be incorporated into the standard activity selection panel so that you can use them with your students.</em></p>';

/*
	$query_conditions[] = 'public = ?';
	$query_params[] = 1;
	

*/


$query = 'SELECT member_username FROM portal_members WHERE member_school = ?';
$params = array($_SESSION['portal']['member_school']);

$results = mystery_select_query($query, $params, 'portal_dbh');

$school_members = mystery_convert_results_to_simple_array($results, 'member_username');

//mystery_print_r($school_members);

$conditions = array();
$params = array();

switch ($_PORTAL['activity']) {

	case 'my':

		// only my activities	
		$conditions[] = 'login = ?';
		$params[] = $_SESSION['portal']['member_username'];

	break;
	
	case 'school':

		// other's in my school activities but not mine
		
		$conditions[] = 'login <> ?';
		$params[] = $_SESSION['portal']['member_username'];

		$conditions[] = 'login IN ("' . implode('","', $school_members) . '")';

	break;
	
	case 'world':

		$conditions[] = 'login NOT IN ("' . implode('","', $school_members) . '")';

	break;

}

$conditions[] = 'ida.id NOT IN ("' . implode('","', portal_get_diy_ids_to_exclude()) . '")';


$activities = portal_get_diy_activities_from_db($conditions, $params);

$display = '
<div id="activity-table-container">
<table border="0" cellspacing="0" cellpadding="0" id="activity-table">
<thead>
	<tr>
		<th>ID Number</th>
		<th>Activity Name</th>
		<th>Author</th>
		<th>Options</th>
	</tr>
</thead>
<tbody>
';

for ($i = 0; $i < count($activities); $i++) {

	$diy_id = $activities[$i]['diy_identifier'];
	
	$copy = '<a href="/diy/copy/' . $diy_id . '/" target="_blank" title="Make your own version of this activity">' . portal_icon('copy') . '</a>';
	
	$edit = '<a href="/diy/edit/' . $diy_id . '/" target="_blank" title="Edit this activity">' . portal_icon('setup') . '</a>';

	$preview = '<a href="/diy/show/' . $diy_id . '/" target="_blank" title="View a quick preview version of this activity">' . portal_icon('preview') . '</a>';
	
	$usage = '<a href="/diy/usage/' . $diy_id . '/" target="_blank" title="View the learner data from this activity">' . portal_icon('report') . '</a>';
	
	$info = '<a href="#" onclick="toggle_block_element(\'activity-description-' . $activities[$i]['activity_id'] . '\'); return false;">' . portal_icon('info') . '</a>';
	
	$run = '<a href="/diy/run/' . $diy_id .  '/" title="Run this activity">' . portal_icon('run') . '</a>';

	if ($_PORTAL['activity'] != 'my') {
		$edit = '';
	}

	$options = '
	&nbsp;&nbsp;
	' . $edit . '
	' . $copy . '
	' . $usage . '
	' . $run . '
	' . $info . '
	' . $preview . '
	&nbsp;&nbsp;
	';

	$display .= '
	<tr>
		<td>' . $diy_id . '</td>
		<td>' . $activities[$i]['activity_name'] . '</td>
		<td>' . $activities[$i]['first_name'] . ' ' . $activities[$i]['last_name'] . '</td>
		<td>' . $options . '
			<div class="activity-description" id="activity-description-' . $activities[$i]['activity_id'] . '">
			' . $activities[$i]['activity_description'] . '
			</div>
		</td>
	</tr>
	';

}

$display .= '
</tbody>
</table>
</div>

<script type="text/javascript"> 
	/* Set up the Column set */
	var activityColumnHeaders = [
		{key:"diyid", text:"ID Number", type:"number", sortable: true},
		{key:"activityname", text:"Activity Name", sortable: true},
		{key:"author", text:"Author", sortable: true},
		{key:"options", text:"Options", sortable: false}
	];
	
	var activityColumnSet = new YAHOO.widget.ColumnSet(activityColumnHeaders);


	/* Parse the markup for data */ 
	var myDataTable = new YAHOO.widget.DataTable("activity-table-container",activityColumnSet); 
</script> 

';

echo $display;

// mystery_print_r($activities);

// portal_get_diy_activities();


?>