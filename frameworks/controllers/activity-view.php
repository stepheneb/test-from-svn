<?php

/*


/portal/view/:id
POST or GET: ?login=userlogin&password=userpassword
Response: HTTP 302 moved response forwarding the user to the JNLP for the activity in VIEW mode


*/

$diy_id = $_PORTAL['action'];

$path = '/portal/view/:' . $diy_id . '?login=' . $_SESSION['user_username'] . '&password=' . $_SESSION['portal']['member_pw'];

echo $path;

$host = $portal_config['diy_server'];

$fp = fsockopen($host, 80);
fputs($fp, "GET " . $path . " HTTP/1.0\r\n");
fputs($fp, "Host: " . $host . "\r\n");
fputs($fp, "Connection: close\r\n");

echo $fp;

$response = '';

while (!feof($fp)) {
	$response .= fgets($fp, 128);
}

fclose($fp);

list($http_headers,$http_content) = explode("\r\n\r\n", $response);

// rather than properly parse the xml, we'll just do a quick regular expression
// in the future, we could probably use MiniXML - http://minixml.psychogenic.com/overview.html

mystery_print_r($response);

/*preg_match('~>([0-9]+)</id>~', $http_content, $matches);

$diy_member_id = $matches[1];

return $diy_member_id;
*/


?>
