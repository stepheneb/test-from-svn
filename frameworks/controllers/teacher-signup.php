<?php

if ($_PORTAL['action'] == 'process') {

	$member_id = portal_process_teacher_registration($_REQUEST);
	
	if ($member_id == 0) {
	
		echo portal_generate_error_page($_PORTAL['errors']);
	
	} else {
	
		echo '
		<h1>Sign-up Complete</h1>
		
		<p>Your sign-up was successful.</p>

		<p>Please print this page  or write down the following information.</p>
		
		<p><strong>Username:</strong> ' . $_REQUEST['username'] . '</p>

		<p><strong>Password:</strong> ' . $_REQUEST['password'] . '</p>
		
		<p>If you would like to be an administrator of this school, please contact <a href="mailto:webmaster@concord.org">webmaster@concord.org</a>.  Administrators can
		manage information about all classes and students at a school.</p>
		
		<form action="/signin/" method="get">
			<p><input type="submit" value="Return to Sign-in Page"></p>
		</form>
		';
	
	}

} else {

	$state_list = portal_generate_db_form_list('school_state', '', 'mystery4.mystery_states', 'state_abbr', 'state_name', 'list', '', '', array(), array('<option value=""></option>'), 35);

	echo '
	<form action="/signup/teacher/process/" method="post">
	
	<h1>Teacher Registration</h1>
	
	<p><label for="first-name">First Name</label> <input type="text" name="first_name" id="first-name" value="" size="35"></p>

	<p><label for="last-name">Last Name</label> <input type="text" name="last_name" id="last-name" value="" size="35"></p>
	
	<p><label for="email">Email</label> <input type="text" name="email" id="email" value="" size="35"></p>

	<p><label for="password">Password</label> <input type="text" name="password" id="password" value="" size="35"> <span class="form-field-info"><strong>Warning:</strong> this field will display your password</span></p>
	
	<p><label for="school-id">School</label> <select name="school_id" id="school-id"><option value="">Please select your school…</option>' . portal_generate_school_option_list() . '<optgroup label="Other"><option value="other">Please specify…</option></optgroup></select> <span class="form-field-info">if you can not find your school, please select <br>"Other" and enter the information below.</span></p>

	<div id="school-fields">
	
		<p><label for="school-name">School Name</label> <input type="text" name="school_name" id="school-name" value="" size="35"></p>

		<p><label for="school-address-1">Address 1</label> <input type="text" name="school_address_1" id="school-address-1" value="" size="35"></p>

		<p><label for="school-address-2">Address 2</label> <input type="text" name="school_address_2" id="school-address-2" value="" size="35"></p>

		<p><label for="school-city">City</label> <input type="text" name="school_city" id="school-city" value="" size="35"></p>

		<p><label for="school-state">State</label> ' . $state_list . '</p>

		<p><label for="school-zip">Zip</label> <input type="text" name="school_zip" id="school-zip" value="" size="35"></p>
				
	</div>

	<p><label for="submit">&nbsp;</label> <input type="submit" id="submit" value="Continue"></p>
	
	<div class="clear-both">&nbsp;</div>
	
	</form>
	
	<script type="text/javascript">
	
		function toggle_school_fields() {
		
			var school_id = get_select_box_value("school-id");
			
			var fields = document.getElementById("school-fields");

			if (school_id == "other") {
				fields.style.display = "block";
			} else {
				fields.style.display = "none";
			}
		
		}
		
		addLoadEvent(

			function() {

				document.getElementById("school-id").onchange = function() {
					toggle_school_fields();
				}

				toggle_school_fields();

			}
		
		);
	
	</script>
	
	';

}

?>
