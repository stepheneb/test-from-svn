<?php

$page_title = 'Usage Stats';

if (@$_REQUEST['hours'] == '') {
	$_REQUEST['hours'] = 2;
}

echo '<p>This page shows usage stats for the previous ' . $_REQUEST['hours'] . ' hours</p>';

$query = 'SELECT CONCAT(member_first_name, " ", member_last_name, " (", pal.member_id, ")") AS member, member_type, request_uri, date_format(access_time, "%Y-%m-%d %H:%i:%s") AS formatted_time FROM portal_access_log AS pal LEFT JOIN portal_members AS pm ON pal.member_id=pm.member_id WHERE request_uri NOT LIKE ? AND access_time >= DATE_SUB(NOW(), INTERVAL ? hour) ORDER BY access_time DESC';

$params = array('/usage/%',$_REQUEST['hours']);

$results = mystery_select_query($query, $params, 'portal_dbh');

$display = '
<form action="" method="post">
	<p>View the past <input type="text" size="3" name="hours" value="' . $_REQUEST['hours'] . '"> hours <input type="submit" value="Go!"></p>
</form>

<div id="activity-table-container">
<table border="0" cellspacing="0" cellpadding="0" id="activity-table">
<thead>
	<tr>
		<th>Member</th>
		<th>Type</th>
		<th>Request</th>
		<th>Date</th>
	</tr>
</thead>
<tbody>
';

for ($i = 0; $i < count($results); $i++) {

	$display .= '
	<tr>
		<td>' . $results[$i]['member'] . '</td>
		<td>' . $results[$i]['member_type'] . '</td>
		<td>' . $results[$i]['request_uri'] . '</td>
		<td>' . $results[$i]['formatted_time'] . '</td>
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
		{key:"member", text:"Member", sortable: true},
		{key:"type", text:"Type", sortable: true},
		{key:"request", text:"Request", sortable: true},
		{key:"date", text:"Date", sortable: true},
	];
	
	var activityColumnSet = new YAHOO.widget.ColumnSet(activityColumnHeaders);


	/* Parse the markup for data */ 
	var myDataTable = new YAHOO.widget.DataTable("activity-table-container",activityColumnSet); 
</script> 

';

echo $display;

?>