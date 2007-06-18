<?php

/* Overrides File

This file gives the developers an easy way to override settings for their
development environment, such as database connections, file paths, etc. */

if ($_SERVER['SERVER_ADDR'] != '63.138.119.231') {

	// Determine the developer

	if (trim(@`hostname`) == 'tikal.local') {
	
		/* Paul Burney Development setup*/
		
		$portal_config['cookie_domain'] = '.concord.local';
		$portal_config['diy_server'] = 'itsidiy.concord.local';

		$portal_config['mystery_database_connection'] = 'mysql://portal_admin:W?rm%*(3@localhost/mystery4';
		$portal_config['portal_database_connection'] = 'mysql://portal_admin:W?rm%*(3@localhost/ccportal';
		$portal_config['sunflower_database_connection'] = 'mysql://portal_admin:W?rm%*(3@localhost/sunflower';
		$portal_config['rails_database_connection'] = 'mysql://diy:diy@localhost/diy_development';

	
	}
	
}

?>
