<?php

/****************************************
        C O N F I G U R A T I O N
****************************************/

$portal_config = array(); 


/****************************************
     Project Specific Configuration
****************************************/

$portal_config['project_settings'] = array(); // used for project portal instance settings

// need to which actions are available, etc.
// 
// Valid Available actions are:
// 	create, copy, edit, info, preview, report, run, try
// 
// 


// ITSI Project

$portal_config['project_settings']['itsi']['site_template'] = 'website/itsi-template.php';
$portal_config['project_settings']['itsi']['available_actions'] = array('create', 'copy', 'edit', 'info', 'preview', 'report', 'run', 'try');
$portal_config['project_settings']['itsi']['use_diy_activities'] = 'yes';
$portal_config['project_settings']['itsi']['show_activities_link_to_students'] = 'yes';

$portal_config['project_settings']['itsi']['diy_server'] = 'itsidiy.concord.org';
$portal_config['project_settings']['itsi']['diy_server_path'] = '';
$portal_config['project_settings']['itsi']['diy_table_prefix'] = 'itsidiy_';
$portal_config['project_settings']['itsi']['diy_activities_name'] = 'activities';
$portal_config['project_settings']['itsi']['diy_runnable_type_name'] = 'Activity';
$portal_config['project_settings']['itsi']['diy_manager_user'] = 'username';
$portal_config['project_settings']['itsi']['diy_manager_password'] = 'password';
$portal_config['project_settings']['itsi']['diy_session_name'] = '_ITSI_Do_It_Yourself_Tjc8FDLwSBbwH2OOHxJzTg____session_id';
$portal_config['project_settings']['itsi']['diy_use_uuid'] = 'no';
$portal_config['project_settings']['itsi']['diy_param_order'] = 'interface/member';


// UDL Project

$portal_config['project_settings']['udl']['site_template'] = 'website/udl-template.php';
$portal_config['project_settings']['udl']['available_actions'] = array('info', 'report', 'run', 'try');
$portal_config['project_settings']['udl']['use_diy_activities'] = 'no';
$portal_config['project_settings']['udl']['show_activities_link_to_students'] = 'no';

$portal_config['project_settings']['udl']['diy_server'] = 'rails.dev.concord.org';
$portal_config['project_settings']['udl']['diy_server_path'] = '/udl';
$portal_config['project_settings']['udl']['diy_table_prefix'] = 'udl_';
$portal_config['project_settings']['udl']['diy_activities_name'] = 'external_otrunk_activities';
$portal_config['project_settings']['udl']['diy_runnable_type_name'] = 'ExternalOtrunkActivity';
$portal_config['project_settings']['udl']['diy_manager_user'] = 'username';
$portal_config['project_settings']['udl']['diy_manager_password'] = 'password';
$portal_config['project_settings']['udl']['diy_session_name'] = '_Universal_Design_in_Science_Education_OWCgmRUr8s1GQfrvi2SItw____session_id';
$portal_config['project_settings']['udl']['diy_use_uuid'] = 'yes';
$portal_config['project_settings']['udl']['diy_param_order'] = 'member/interface';



/****************************************
        General Configuration
****************************************/

$portal_config['default_language'] = 'en';

$portal_config['default_project'] = 'itsi'; 

$portal_config['mystery_database_connection'] = 'mysql://user:password@localhost/mystery4';
$portal_config['portal_database_connection'] = 'mysql://user:password@localhost/ccportal';
$portal_config['sunflower_database_connection'] = 'mysql://user:password@localhost/sunflower';
$portal_config['rails_database_connection'] = 'mysql://user:password@localhost/diy_development';

$portal_config['error_log'] = '/web/logs/portal_error_log';
$portal_config['security_log'] = '/web/logs/portal_security_log';

$portal_config['cookie_domain'] = '.concord.org';
$portal_config['session_name'] = 'ccportal_session';


$portal_config['icons']['run'] = '/images/icons/bullet_go.png';
$portal_config['icons']['try'] = '/images/icons/picture_go.png';
$portal_config['icons']['preview'] = '/images/icons/picture.png';
$portal_config['icons']['delete'] = '/images/icons/cross.png';
$portal_config['icons']['list'] = '/images/icons/page_white_get.png';
$portal_config['icons']['copy'] = '/images/icons/page_white_copy.png';
$portal_config['icons']['report'] = '/images/icons/chart_bar.png';
$portal_config['icons']['setup'] = '/images/icons/cog.png';
$portal_config['icons']['work'] = '/images/icons/tick.png';
$portal_config['icons']['info'] = '/images/icons/information.png';
$portal_config['icons']['customized'] = '/images/icons/user.png';
$portal_config['icons']['add'] = '/images/icons/add.png';

$portal_config['ie-icons']['run'] = '/images/icons/gifs/bullet_go.gif';
$portal_config['ie-icons']['try'] = '/images/icons/gifs/picture_go.gif';
$portal_config['ie-icons']['preview'] = '/images/icons/gifs/picture.gif';
$portal_config['ie-icons']['delete'] = '/images/icons/gifs/cross.gif';
$portal_config['ie-icons']['list'] = '/images/icons/gifs/page_white_get.gif';
$portal_config['ie-icons']['copy'] = '/images/icons/gifs/page_white_copy.gif';
$portal_config['ie-icons']['report'] = '/images/icons/gifs/chart_bar.gif';
$portal_config['ie-icons']['setup'] = '/images/icons/gifs/cog.gif';
$portal_config['ie-icons']['work'] = '/images/icons/gifs/tick.gif';
$portal_config['ie-icons']['info'] = '/images/icons/gifs/information.gif';
$portal_config['ie-icons']['customized'] = '/images/icons/gifs/user.gif';
$portal_config['ie-icons']['add'] = '/images/icons/gifs/add.gif';


$portal_config['interfaces']['6'] = 'Vernier Go!Link';
$portal_config['interfaces']['8'] = 'Vernier LabPro';

$portal_config['image_upload_directory'] = '/web/portal.concord.org/images/public';
$portal_config['image_upload_web_path'] = '/images/public';

$portal_config['school_image_width'] = 175;
$portal_config['school_image_height'] = 175;

$portal_config['school_image_thumb_width'] = 50;
$portal_config['school_image_thumb_height'] = 50;

$portal_config['site_image_width'] = 375;
$portal_config['site_image_height'] = 375;

$portal_config['site_image_thumb_width'] = 75;
$portal_config['site_image_thumb_height'] = 75;


?>
