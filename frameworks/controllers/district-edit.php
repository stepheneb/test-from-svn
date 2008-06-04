<?php

if ($_SESSION['portal']['member_type'] != 'superuser' && $_SESSION['portal']['member_type'] != 'admin') {

	mystery_redirect('/');
	exit;

}

$district_id = $_PORTAL['action'];

$page_title = 'District Edit';

if (isset($_PORTAL['params']['process'])) {

	$data = array();
	
	$data['district_name'] = $_REQUEST['district_name'];
	$data['district_address_1'] = $_REQUEST['district_address_1'];
	$data['district_address_2'] = $_REQUEST['district_address_2'];
	$data['district_city'] = $_REQUEST['district_city'];
	$data['district_state'] = $_REQUEST['district_state'];
	$data['district_zip'] = $_REQUEST['district_zip'];
	$data['district_country'] = $_REQUEST['district_country'];

	$status = mystery_update_query('portal_districts', $data, 'district_id', $district_id, 'portal_dbh');
	
	if ($status == 0) {
	
		$errors = array('Could not update district information');
		echo portal_generate_error_page($errors);
	
	} else {
	
		// redirect back to the admin page
		
		mystery_redirect('/admin/');
		exit;
	
	}
	
} else {

	$district_info = portal_get_district_info($district_id);
	
	$state_list = portal_generate_db_form_list('district_state', @$district_info['district_state'], 'mystery4.mystery_states', 'state_abbr', 'state_name', 'list', '', '', array(), array('<option value=""></option>'), 35);
	
	$country_list = portal_generate_db_form_list('district_country', @$district_info['district_country'], 'mystery4.mystery_countries', 'country_name', 'country_name', 'list', '', '', array(), array('<option value=""></option>'), 50);

	$district_info = portal_web_output_filter($district_info);

	echo '
	<form action="/district/edit/' . $district_id . '/process/" method="post">
	
	<h1>Edit district</h1>
	
	<p><label for="district-name">district Name</label> <input type="text" name="district_name" id="district-name" value="' . @$district_info['district_name'] . '" size="35"></p>

	<p><label for="district-address-1">Address 1</label> <input type="text" name="district_address_1" id="district-address-1" value="' . @$district_info['district_address_1'] . '" size="35"></p>

	<p><label for="district-address-2">Address 2</label> <input type="text" name="district_address_2" id="district-address-2" value="' . @$district_info['district_address_2'] . '" size="35"></p>

	<p><label for="district-city">City</label> <input type="text" name="district_city" id="district-city" value="' . @$district_info['district_city'] . '" size="35"></p>

	<p><label for="district-state">State</label> ' . $state_list . '</p>

	<p><label for="district-zip">Zip</label> <input type="text" name="district_zip" id="district-zip" value="' . @$district_info['district_zip'] . '" size="35"></p>

	<p><label for="district-country">Country</label> ' . $country_list . '</p>

	<p><label for="submit">&nbsp;</label> <input type="submit" id="submit" value="Save"></p>
	
	<div class="clear-both">&nbsp;</div>
	
	</form>
	';

}

?>
