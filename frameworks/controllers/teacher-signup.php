<?php

// PB: This needs to be a multi-step process so people can look for a district or school first

// Note... there's a security issue with the automatic "admin"ing of people who create schools in that some student might create a school
// that is later used by their real school and then he could get access to everything.  Unlikely, but possible and so noted.  Maybe a solution
// would involve showing who the administrator is at each school in the list, but that gets really ugly and can still be forged.

if (@$_PORTAL['action'] == '') {
	$_PORTAL['action'] = 'search';
}

switch ($_PORTAL['action']) {

	// STEP 1 - Search for the School

	case 'search':
	
		// ask user for city, state or country so we can try to find a school
		
		$state_list = portal_generate_db_form_list('state', '', 'mystery4.mystery_states', 'state_abbr', 'state_name', 'list', '', '', array(), array('<option value=""></option>'), 50);

		$country_list = portal_generate_db_form_list('country', '', 'mystery4.mystery_countries', 'country_name', 'country_name', 'list', '', '', array(), array('<option value=""></option>'), 50);

		echo '
		<h1>Teacher Registration — Step 1 — Find your school</h1>
		
		<table width="100%" class="registration-form">
			<tr>
				<td width="50%" valign="top">
					<form action="/signup/teacher/search-results/" method="post">
						<h2>United States / Canada</h2>
						
						<p>Please enter your city and state below and click on the <strong>Continue</strong> button.</p>
						
						<p><strong>City/Town:</strong><br><input type="text" name="city" value=""></p>

						<p><strong>State/Province:</strong><br>' . $state_list . '</p>
						
						<p><input type="hidden" name="country" value="United States"><input type="submit" value="Continue to Step 2"></p>
					</form>
				</td>
				<td width="50%" valign="top">
					<form action="/signup/teacher/search-results/" method="post">
						<h2>International</h2>
						
						<p>Please select your country below and click on the <strong>Continue</strong> button.</p>
						
						<p><strong>Country:</strong><br>' . $country_list . '</p>
						
						<p><input type="submit" value="Continue to Step 2"></p>
					</form>
				</td>
			</tr>
		</table>
		
		';
	
	break;
	


	// STEP 2 - Display Search Results and let user create new District if applicable

	case 'search-results':
	
		// show the user a list of results in that city/state/country
		// with an option to add a new district/school
	
		$country_param = $_REQUEST['country'];
		$state_param = @$_REQUEST['state'];
		$city_param = @$_REQUEST['city'];
		
		if ($state_param == '') {
			$state_param = '%';
		}
		
		if ($city_param == '') {
			$city_param = '%';
		}
		
		$query = 'SELECT * FROM portal_schools AS ps LEFT JOIN portal_districts AS pd ON ps.school_district=pd.district_id WHERE school_country = ? AND school_state = ? AND school_city = ? ORDER BY district_name, school_name';
		$params = array($country_param, $state_param, $city_param);
		
		$results = mystery_select_query($query, $params, 'portal_dbh');
		
		$school_results = '';
		
		$result_count = count($results);
		
		if ($result_count > 0) {

			$school_results .= '
			<ul>
			';

			for ($i = 0; $i < $result_count; $i++) {
				$district_display = $results[$i]['district_name'];
				if ($district_display == '') {
					$district_display = 'N/A';
				}
				$school_results .= '<li><a href="/signup/teacher/info/?school_id=' . $results[$i]['school_id'] . '">' . $results[$i]['school_name'] . '</a><br><small>(' . $district_display . ')</small></li>';
			}
			
			$school_results .= '
			</ul>
			';
			
		} else {
		
			$school_results = '<p><em>No matching schools</em></p>';
		
		}
	
		$query = 'SELECT * FROM portal_districts WHERE district_country = ? AND district_state = ? AND district_city = ?';
		$params = array($country_param, $state_param, $city_param);
		
		$results = mystery_select_query($query, $params, 'portal_dbh');
		
		$district_results = '';
		
		$result_count = count($results);
		
		if ($result_count > 0) {

			$district_results .= '
			<ul>
			';

			for ($i = 0; $i < $result_count; $i++) {
				$district_results .= '<li><a href="/signup/teacher/add-school/?district_id=' . $results[$i]['district_id'] . '">' . $results[$i]['district_name'] . '</a></li>';
			}
			
			$district_results .= '
			</ul>
			';
			
		} else {

			$district_results = '<p><em>No matching districts</em></p>';
		
		}
	
		$state_list = portal_generate_db_form_list('district_state', @$_REQUEST['state'], 'mystery4.mystery_states', 'state_abbr', 'state_name', 'list', '', '', array(), array('<option value=""></option>'), 50);

		$country_list = portal_generate_db_form_list('district_country', @$_REQUEST['country'], 'mystery4.mystery_countries', 'country_name', 'country_name', 'list', '', '', array(), array('<option value=""></option>'), 50);

		echo '
		<h1>Teacher Registration — Step 2 — Choose</h1>
		
		<p>Click on one of the below links to join a school or district, or add your district on the right.</p>
		
		<table width="100%" class="registration-form">

		<tr>

			<td width="33%" valign="top">

				<h2>Matching Schools</h2>

				' . $school_results . '
			
			</td>
	
			<td width="33%" valign="top">

				<h2>Matching Districts</h2>

				' . $district_results . '
			
			</td>
	
			<td width="33%" valign="top">

				<h2>Add a New District</h2>

				<form action="/signup/teacher/add-school/" method="post">
	
					<p><strong>District Name </strong><br><input type="text" name="district_name" id="district-name" value="" size="35"></p>
			
					<p><strong>Address 1</strong><br> <input type="text" name="district_address_1" id="district-address-1" value="" size="35"></p>
			
					<p><strong>Address 2</strong><br> <input type="text" name="district_address_2" id="district-address-2" value="" size="35"></p>
			
					<p><strong>City</strong><br> <input type="text" name="district_city" id="district-city" value="' . @$_REQUEST['city'] . '" size="35"></p>
			
					<p><strong>State</strong><br> ' . $state_list . '</p>
			
					<p><strong>Zip</strong><br><input type="text" name="district_zip" id="district-zip" value="" size="35"></p>
				
					<p><strong>Country</strong><br> ' . $country_list . '</p>

					<p><input type="submit" value="Continue to Step 3"></p>
				</form>
			
			</td>

		</tr>

		</table>
		';
	
	break;



	// STEP 3 - Add a school for the user
	
	case 'add-school':
	
		if (!isset($_REQUEST['district_id'])) {
		
			// process the district information
			
			$data = array();
			
			$data['district_name'] = $_REQUEST['district_name'];
			$data['district_address_1'] = $_REQUEST['district_address_1'];
			$data['district_address_2'] = $_REQUEST['district_address_2'];
			$data['district_city'] = $_REQUEST['district_city'];
			$data['district_state'] = $_REQUEST['district_state'];
			$data['district_zip'] = $_REQUEST['district_zip'];
			$data['district_country'] = $_REQUEST['district_country'];
			
			$_REQUEST['district_id'] = mystery_insert_query('portal_districts', $data, 'district_id', 'portal_dbh');
		
		}
		
		$district_info = portal_get_district_info($_REQUEST['district_id']);
	
		$state_list = portal_generate_db_form_list('school_state', $district_info['district_state'], 'mystery4.mystery_states', 'state_abbr', 'state_name', 'list', '', '', array(), array('<option value=""></option>'), 50);

		$country_list = portal_generate_db_form_list('school_country', $district_info['district_country'], 'mystery4.mystery_countries', 'country_name', 'country_name', 'list', '', '', array(), array('<option value=""></option>'), 50);
	
		echo '
		<form action="/signup/teacher/info/" method="post">
		
		<h1>Teacher Registration — Step 3 — Your School Info</h1>
	
		<p><strong>School Name</strong> <br><input type="text" name="school_name" id="school-name" value="" size="35"></p>

		<p><strong>Address 1</strong> <br><input type="text" name="school_address_1" id="school-address-1" value="" size="35"></p>

		<p><strong>Address 2</strong> <br><input type="text" name="school_address_2" id="school-address-2" value="" size="35"></p>

		<p><strong>City</strong> <br><input type="text" name="school_city" id="school-city" value="' . $district_info['district_city'] . '" size="35"></p>

		<p><strong>State</strong> <br>' . $state_list . '</p>

		<p><strong>Zip</strong> <br><input type="text" name="school_zip" id="school-zip" value="' . $district_info['district_zip'] . '" size="35"></p>
				
		<p><strong>Country</strong><br> ' . $country_list . '</p>

		<p><input type="hidden" name="district_id" value="' . $_REQUEST['district_id'] . '"><input type="submit" value="Continue to Step 4"></p>
		
		</form>
		';

	break;
	
	
	
	// STEP 4 - Ad a member for the teacher
	
	case 'info':

		if (!isset($_REQUEST['school_id'])) {
			
			$data = array();
			
			$data['school_name'] = $_REQUEST['school_name'];
			$data['school_district'] = $_REQUEST['district_id'];
			$data['school_address_1'] = $_REQUEST['school_address_1'];
			$data['school_address_2'] = $_REQUEST['school_address_2'];
			$data['school_city'] = $_REQUEST['school_city'];
			$data['school_state'] = $_REQUEST['school_state'];
			$data['school_zip'] = $_REQUEST['school_zip'];
			$data['school_country'] = $_REQUEST['school_country'];
			
			$_REQUEST['school_id'] = mystery_insert_query('portal_schools', $data, 'school_id', 'portal_dbh');
			
			$_SESSION['school_created'] = 'yes';
		
		}
		
		// show the teacher info form
	
		echo '
		<form action="/signup/teacher/process/" method="post">
		
		<h1>Teacher Registration — Step 4 — Your Info</h1>
		
		<p><strong>First Name</strong> <br><input type="text" name="first_name" id="first-name" value="" size="35"></p>
	
		<p><strong>Last Name</strong> <br><input type="text" name="last_name" id="last-name" value="" size="35"></p>
		
		<p><strong>Email</strong> <br><input type="text" name="email" id="email" value="" size="35"></p>
	
		<p><strong>Password</strong> <br><input type="text" name="password" id="password" value="" size="35"> <span class="form-field-info"><strong>Warning:</strong> this field will display your password<br><strong>Note:</strong> your password must be between 4 and 40 characters long</span></p>
		
		<p><strong>How did you hear about this service?</strong><br> <input type="text" name="source" value="" size="35"></p>
		
		<p><input type="hidden" name="school_id" value="' . $_REQUEST['school_id'] . '"><input type="submit" id="submit" value="Complete your Registration"></p>
				
		</form>
		';
	
	break;
		
	case 'process':
	
		// add the teacher information
		
		if (@$_SESSION['school_created'] == 'yes') {
		
			// make this member an admin of the school

			$type = 'admin';

			$admin_message = '
			<p>Note: You are the administrator of this school.  Administrators can
			manage information about all classes and students at a school.</p>
			';
		
		} else {
		
			$type = 'teacher';

			$admin_message = '
			<p>If you would like to be an administrator of this school, please 
			contact <a href="mailto:webmaster@concord.org">webmaster@concord.org</a>.  Administrators can
			manage information about all classes and students at a school.</p>
			';
		
		}
		
		$member_id = portal_process_teacher_registration($_REQUEST, $type);
		
		if ($member_id == 0) {
		
			echo portal_generate_error_page($_PORTAL['errors']);
		
		} else {
		
			echo '
			<h1>Registration Complete</h1>
			
			<p>Your registration was successful.</p>
	
			<p>Please print this page  or write down the following information.</p>
			
			<p><strong>Username:</strong> ' . $_REQUEST['username'] . '</p>
	
			<p><strong>Password:</strong> ' . $_REQUEST['password'] . '</p>
			
			' . $admin_message . '
			
			<form action="/signin/" method="get">
				<p><input type="submit" value="Return to Sign-in Page"></p>
			</form>
			';
		
		}
	
	break;

}

/*

<script type="text/javascript">

	function toggle_school_fields() {
		if ($("#school-id").val() == "other") {
			$("#school-fields").show();
		} else {
			$("#school-fields").hide();
		}
	}

	function toggle_district_fields() {
		if ($("#school-district").val() == "other") {
			// show the other text box and change the names of the fields
			$("#school-district-text").show();
			$("#school-district").attr("name", "school_district_list");
			$("#school-district-text").attr("name", "school_district");
		} else {
			$("#school-district-text").hide();
			$("#school-district-text").attr("name", "school_district_text");
			$("#school-district").attr("name", "school_district");
		}
	}
	
	$(document).ready(

		function() {

			$("#school-id").change(toggle_school_fields);
			$("#school-district").change(toggle_district_fields);

			toggle_school_fields();
			toggle_district_fields();

		}
	
	);

</script>
		
*/

?>
