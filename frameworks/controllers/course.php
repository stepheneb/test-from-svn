<?php

// This controller handles the course related functionality (ratings, browsing, etc);

function portal_prepare_activity_list($activities) {

	// this function fixes a set of activities here
	// for the DIY activities, it will add in the schools and the ratings.
	// as well as update the author name
	
	$fixed = array();
	
	$activity_count = count($activities);
	
	for ($i = 0; $i < $activity_count; $i++) {
	
		if (!isset($activities[$i]['activity_author'])) {
			$activities[$i]['activity_author'] = $activities[$i]['first_name'] . ' ' . $activities[$i]['last_name'];
		}
		
		$display = array();

		if (@$activities[$i]['level_name'] != 'DIY') {
 
			if (@$activities[$i]['subject_name'] != '') {
				$display[] = $activities[$i]['subject_name'];
			}
	
			if (@$activities[$i]['unit_name'] != '') {
				$display[] = $activities[$i]['unit_name'];
			}
		
		}
		
		$display[] = $activities[$i]['activity_name'];
		
		$activities[$i]['activity_name'] = implode(': ', $display);
		
		if (isset($activities[$i]['project_name'])) {
			$activities[$i]['activity_school'] = 'Concord Consortium';
			$include_in_list = 'yes';
		} else {
			$activities[$i]['activity_school'] = portal_lookup_member_school($activities[$i]['author']);
			
			if (portal_lookup_member_course_status($activities[$i]['author']) == 1) {
				$include_in_list = 'yes';
			} else {
				$include_in_list = 'no';
			}
		}
		
		$activities[$i]['activity_rating'] = portal_lookup_activity_rating($activities[$i]['diy_identifier']);
		

		if ($include_in_list == 'yes') {
			$fixed[] = $activities[$i];
		}
	
	}
	
	return $fixed;

}

function portal_convert_number_to_stars($number) {

	$round = round($number);
	
	$stars = '';
	
	for ($i = 1; $i <= $round; $i++) {
		$stars .= '<img src="/images/yellow-star.gif">';
	}

	if ($stars == '') {
		$stars = '&nbsp;';
	}

	return $stars;

}

function portal_lookup_member_school($member_username) {

	static $lookup = array();
	
	if (count($lookup) == 0) {
	
		$query = 'SELECT member_username, school_name from portal_members AS pm LEFT JOIN portal_schools AS ps ON pm.member_school=ps.school_id';
		$params = array();

		$results = mystery_select_query($query, $params, 'portal_dbh');
		
		$lookup = mystery_convert_results_to_lookup_array($results, 'member_username', 'school_name');
	
	}
	
	if (!isset($lookup[$member_username])) {
		$lookup[$member_username] = 'Unknown School';
	}
	
	return $lookup[$member_username];

}

function portal_lookup_member_course_status($member_username) {

	static $lookup = array();
	
	if (count($lookup) == 0) {
	
		$query = 'SELECT member_username, taking_course from portal_members';
		$params = array();

		$results = mystery_select_query($query, $params, 'portal_dbh');
		
		$lookup = mystery_convert_results_to_lookup_array($results, 'member_username', 'taking_course');
	
	}
	
	if (!isset($lookup[$member_username])) {
		$lookup[$member_username] = 0;
	}
	
	return $lookup[$member_username];

}

function portal_get_activity_info_from_diy_id($diy_id) {

	// this function returns the information about an activity from its DIY ID
	
	$activity_info = array();
	
	// first look for it in the activities table.
	
	$conditions = array();
	$params = array();
	
	$conditions[] = 'diy_identifier = ?';
	$params[] = $diy_id;
	
	$results = portal_get_activities($conditions, $params);
		
	if (count($results) > 0) {

		$activity_info = $results[0];

	} else {
	
		$conditions2 = array();
		$params2 = array();
		
		$conditions2[] = 'ida.id = ?';
		$params2[] = $diy_id;
	
		$results2 = portal_get_diy_activities_from_db($conditions2, $params2);
		
		if (count($results2) > 0) {
		
			$activity_info = $results2[0];
		}
	
	}

	if (count($activity_info) > 0) {
	
		$ai = portal_prepare_activity_list(array($activity_info));
		$activity_info = $ai[0];
	
	}

	return $activity_info;

}

function portal_lookup_activity_rating($diy_id) {

	static $lookup = array();
	
	if (count($lookup) == 0) {
	
		$query = 'SELECT  comment_diy_identifier, avg(comment_rating) AS activity_rating FROM portal_comments_ratings WHERE comment_rating > 0 GROUP BY comment_diy_identifier';
		$params = array();

		$results = mystery_select_query($query, $params, 'portal_dbh');
		
		$lookup = mystery_convert_results_to_lookup_array($results, 'comment_diy_identifier', 'activity_rating');
	
	}
	
	if (!isset($lookup[$diy_id])) {
		$lookup[$diy_id] = '<em>Not Rated</em>';
	}
	
	return $lookup[$diy_id];

}

function portal_get_activity_comments($diy_id, $member_id = '') {

	$comments = array();

	$query = 'SELECT *, DATE_FORMAT(pcr.last_update, "%M %e, %Y") AS formatted_date FROM portal_comments_ratings AS pcr LEFT JOIN portal_members AS pm ON pcr.comment_author=pm.member_id WHERE comment_diy_identifier = ?';
	
	$params = array($diy_id);

	if ($member_id != '') {
	
		$query .= ' AND comment_author = ?';
		$params[] = $member_id;
	
	}
	
	$query .= ' ORDER BY pcr.last_update DESC';
	
	$comments = mystery_select_query($query, $params, 'portal_dbh');

	return $comments;

}

function portal_get_member_activity_comments($diy_id, $member_id) {

	$comments = portal_get_activity_comments($diy_id, $member_id);
	
	if (count($comments) > 0) {
		return $comments[0];
	} else {
		return $comments;
	}

}

if ($_SESSION['is_logged_in'] != 'yes' || !$_SESSION['portal']['taking_course']) {
	
	mystery_redirect('/');


}


switch($_PORTAL['activity']) {

	case 'details':
	
		$diy_id = @$_PORTAL['action'];
		
		if ($diy_id == '') {
			mystery_redirect('/course/');
		}
		
		if (isset($_PORTAL['params']['process'])) {
		
			$query = 'DELETE FROM portal_comments_ratings WHERE comment_diy_identifier = ? AND comment_author = ?';
			$params = array($diy_id, $_SESSION['portal']['member_id']);
			
			$status = mystery_delete_query($query, $params, 'portal_dbh');
			
			if (!isset($_REQUEST['comment_delete'])) {
				
				$data = array();
				
				$data['comment_author'] = $_SESSION['portal']['member_id'];
				$data['comment_diy_identifier'] = $diy_id;
				$data['comment_title'] = $_REQUEST['comment_title'];
				$data['comment_body'] = $_REQUEST['comment_body'];
				$data['comment_rating'] = $_REQUEST['comment_rating'];
				$data['creation_date'] = date('Y-m-d H:i:s');
	
				$comment_id = mystery_insert_query('portal_comments_ratings', $data, 'comment_id', 'portal_dbh');
			
				echo '<p style="color: #009900;"><em>Comment saved!</em></p>';
	
			} else {
			
				echo '<p style="color: #009900;"><em>Comment deleted!</em></p>';
				
			}
		
		}
		
		$activity_info = portal_get_activity_info_from_diy_id($diy_id);
		
		$page_title = $activity_info['activity_name'] . ' by ' . $activity_info['activity_author'];
	
		$comments = portal_get_activity_comments($diy_id);
		
		$average_rating = portal_lookup_activity_rating($diy_id);
			
		$my_comments = portal_get_member_activity_comments($diy_id, $_SESSION['portal']['member_id']);
		
		if (count($my_comments) > 0) {
			$add_edit_word = 'Edit';
			$delete_checkbox = '<input type="checkbox" name="comment_delete" id="comment-delete" value="yes"> Delete this comment?';
		} else {
			$add_edit_word = 'Add';
			$delete_checkbox = '';
		}
		
		$comment_section = '';
		
		$comment_count = count($comments);
		
		if ($comment_count > 0) {
			
			for ($i = 0; $i < $comment_count; $i++) {
			
				if ($i % 2 == 0) {
					$bg = '#eeeeee';
				} else {
					$bg = '#cdcdcd';
				}
				
				if ($comments[$i]['comment_rating'] == 0) {
					$comments[$i]['comment_rating'] = '<em>not rated</em>';
				}
			
				$comment_section .= '
				<div class="comment-section" style="margin: 5px 20px 10px 50px; border-bottom: 1px solid #cccccc;">
				<div style="padding: 4px;">
		
				<p><span class="comment-rating">' . portal_convert_number_to_stars($comments[$i]['comment_rating']) . '</span> <strong><span class="comment-title" style="font-size: 120%;">' . $comments[$i]['comment_title'] . '</span></strong>
				<br><em>by <span class="comment-author">' . $comments[$i]['member_first_name'] . ' ' . $comments[$i]['member_last_name'] . '</span> on <span class="comment-date">' . $comments[$i]['formatted_date'] . '</span></em>
				</p>
		
				<p>' . $comments[$i]['comment_body'] . '</p>

				</div>
				</div>
				';
			
			}
		
		} else {
		
			$comment_section = '<p><em>There are currently no comments on this activity.  Please add one below.</em></p>';
		
		}
		
		$rating_box = '';
		
		$rating_box .= '
		<p class="comment-rating-container"><label for="comment-rating">Rating</label> 
		<select name="comment_rating" id="comment-rating">
		';
		
		for ($rating = 0; $rating <= 5; $rating++) {
		
			$selected = '';
			
			if (@$my_comments['comment_rating'] == $rating) {
				$selected = ' selected="selected"';
			}
		
			$rating_box .= '<option value="' . $rating . '"' . $selected . '>' . $rating . '</option>';
		
		}
		
		$rating_box .= '
		</select></p>

		<script type="text/javascript" src="/scripts/jquery.rating.js"></script>

		<script type="text/javascript">
		
			$(document).ready(
				function() {
					$("#comment-rating").selectToRating();
				}
			);
		
		</script>
		';
			
		echo '
		<p>' .  $activity_info['activity_description'] . '</p>
		
		<p><strong>Explore:</strong> <a href="/diy/view/' . $diy_id .  '/" title="Try this activity (as a teacher, do not save data)"> Run this activity</a> ' . portal_icon('run') . '</p>
		
		<p><strong>Average Rating:</strong> ' . portal_convert_number_to_stars($average_rating) . ' (' . $average_rating . ')</p>
		
		<h2>Comments</h2>
		
		' . $comment_section . '
		
		<form action="/course/details/' . $diy_id . '/process/" method="post" id="comment-form">
		
		<h2>' . $add_edit_word . ' My Comment</h2>
		
		<p><label for="comment-title">Summary</label> <input type="text" name="comment_title" id="comment-title" size="60" value="' . @$my_comments['comment_title'] . '"></p>
		
		<p><label for="comment-body">Details</label> <textarea name="comment_body" id="comment-body" rows="4" cols="60" wrap="soft">' . @$my_comments['comment_body'] . '</textarea></p>

		' . $rating_box . '
		
		<p><label for="comment-submit">&nbsp;</label> <input type="submit" id="comment-submit" value="Save Changes"> 
		' . $delete_checkbox . '</p>
		
		</form>
		
		';
			
	break;
	
	default:
	
		$page_title = 'ITSI Online Course';
	
		echo '
		<p>You will use this page in conjunction with the ITSI online course being offered
		by the Concord Consortium.</p>
		
		<p><a href="http://moodle.concord.org/">Go to the Online Course</a></p>
		
		<h2>Course Participants and Activities</h2>
		
		<p>Below is a table containing all of the ITSI Course participants and their activities.  
		Click on the info link to find out more about an activity, or to rate it and comment on it. </p>
		
		';

		$activities = portal_get_all_activities('activity_author, activity_name');
		
		$activities = portal_prepare_activity_list($activities);
		
		$activity_count = count($activities);
		
		if ($activity_count > 0) {
		
			echo '
			<form>
			<p><strong>Filter </strong><input type="text" size="40" id="filter" onkeyup="filterTable(this, \'activity-table-body\');"> viewing <strong id="filtered-count">' . $activity_count . '</strong> of <strong>' . $activity_count . '</strong></p>
			
			<table id="activity-table" class="tablesorter">
			<thead>
			<tr>
				<th>School</th>
				<th>Author</th>
				<th>Activity Name</th>
				<th width="120">Rating</th>
				<th>Details</th>
			</tr>
			</thead>
			<tbody id="activity-table-body">
			';
			
			for ($i = 0; $i < count($activities); $i++) {
			
				echo '
				<tr>
					<td>' . @$activities[$i]['activity_school'] . '</td>
					<td>' . $activities[$i]['activity_author'] . '</td>
					<td>' . $activities[$i]['activity_name'] . '</td>
					<td align="center">' . portal_convert_number_to_stars(@$activities[$i]['activity_rating']) . '</td>
					<td align="center"><a href="/course/details/' . $activities[$i]['diy_identifier'] . '/" title="View details, comments, and ratings">' . portal_icon('run') . '</a></td>
				</tr>
				';
			
			}
			
			echo '
			</tbody>
			</table>
			</form>

			<script type="text/javascript">
			
				// taken from here - http://www.vonloesch.de/node/23

				function filterTable(phrase, _id) {
				
					var words = phrase.value.toLowerCase().split(" ");
					var table = document.getElementById(_id);
					var ele;
					
					var filtered_rows = 0;

					for (var r = 0; r < table.rows.length; r++) {
					
						ele = table.rows[r].innerHTML.replace(/<[^>]+>/g,"");
						var displayStyle = "none";

						for (var i = 0; i < words.length; i++) {

							if (ele.toLowerCase().indexOf(words[i])>=0) {

								displayStyle = "";
								filtered_rows++;

							} else {

								displayStyle = "none";
								break;

							}

						}
						
						table.rows[r].style.display = displayStyle;
						
					}

					document.getElementById("filtered-count").innerHTML = filtered_rows;

				}
				


				$(document).ready(
					function() {
						$("#activity-table").tablesorter({
							sortList: [[0,0],[1,0],[2,0]],
							headers: {
								4: {
									sorter: false
								}
							}
						});
					}
				);
			</script>
			';
		
		} else {
		
			echo '<p><em>No activities available.</em></p>';
		
		}
		
	break;
	
	
}

?>
