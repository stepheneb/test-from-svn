<?php

// now get rid of the session
session_destroy();
mystery_setup_default_session();
session_regenerate_id();

// get rid of any diy session
mystery_cookie($portal_config['diy_session_name'], '');

mystery_redirect('/signin/?signout');

?>