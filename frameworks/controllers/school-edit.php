<?php

if ($_SESSION['portal']['member_type'] != 'superuser' && $_SESSION['portal']['member_type'] != 'admin') {

	mystery_redirect('/');
	exit;

}

$page_title = 'School Edit';

if ($_PORTAL['action'] == 'process') {

	$data = array();
	
	$data['school_name'] = $_REQUEST['school_name'];
	$data['school_address_1'] = $_REQUEST['school_address_1'];
	$data['school_address_2'] = $_REQUEST['school_address_2'];
	$data['school_city'] = $_REQUEST['school_city'];
	$data['school_state'] = $_REQUEST['school_state'];
	$data['school_zip'] = $_REQUEST['school_zip'];
	$data['school_country'] = $_REQUEST['school_country'];

	$status = mystery_update_query('portal_schools', $data, 'school_id', $_SESSION['portal']['member_school'], 'portal_dbh');
	
	if ($status == 0) {
	
		$errors = array('Could not update school information');
		echo portal_generate_error_page($errors);
	
	} else {
	
		// redirect back to the admin page
		
		mystery_redirect('/admin/');
		exit;
	
	}
	
} else {

	$school_info = portal_get_school_info($_SESSION['portal']['member_school']);
	
	$state_list = portal_generate_db_form_list('school_state', @$school_info['school_state'], 'mystery4.mystery_states', 'state_abbr', 'state_name', 'list', '', '', array(), array('<option value=""></option>'), 35);
	
	$country_list = portal_generate_db_form_list('school_country', $school_info['district_school'], 'mystery4.mystery_countries', 'country_name', 'country_name', 'list', '', '', array(), array('<option value=""></option>'), 50);

	$school_info = portal_web_output_filter($school_info);

	echo '
	<form action="/school/edit/process/" method="post">
	
	<h1>Edit School</h1>
	
	<p><label for="school-name">School Name</label> <input type="text" name="school_name" id="school-name" value="' . @$school_info['school_name'] . '" size="35"></p>

	<p><label for="school-address-1">Address 1</label> <input type="text" name="school_address_1" id="school-address-1" value="' . @$school_info['school_address_1'] . '" size="35"></p>

	<p><label for="school-address-2">Address 2</label> <input type="text" name="school_address_2" id="school-address-2" value="' . @$school_info['school_address_2'] . '" size="35"></p>

	<p><label for="school-city">City</label> <input type="text" name="school_city" id="school-city" value="' . @$school_info['school_city'] . '" size="35"></p>

	<p><label for="school-state">State</label> ' . $state_list . '</p>

	<p><label for="school-zip">Zip</label> <input type="text" name="school_zip" id="school-zip" value="' . @$school_info['school_zip'] . '" size="35"></p>

	<p><label for="school-country">Country</label> ' . $country_list . '</p>

	<p><label for="submit">&nbsp;</label> <input type="submit" id="submit" value="Save"></p>
	
	<div class="clear-both">&nbsp;</div>
	
	</form>
	';

}

?>
