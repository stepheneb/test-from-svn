<?php

// These are global settings that can live everywhere

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

// WWW Project

$portal_config['project_settings']['www']['display_name'] = 'Portal Directory';
$portal_config['project_settings']['www']['site_template'] = 'website/template.php';


// ITSI Project

$portal_config['project_settings']['itsi']['display_name'] = 'ITSI Project Portal';
$portal_config['project_settings']['itsi']['site_template'] = 'website/itsi-template.php';
$portal_config['project_settings']['itsi']['available_actions']         = array('create', 'copy', 'edit', 'info', 'preview', 'report', 'run', 'try');
$portal_config['project_settings']['itsi']['teacher_available_actions'] = array('guide','create', 'copy', 'edit', 'info', 'preview', 'report', 'run', 'try');
$portal_config['project_settings']['itsi']['use_diy_activities'] = 'yes';
$portal_config['project_settings']['itsi']['use_accommodations'] = 'no';
$portal_config['project_settings']['itsi']['accommodations_name'] = 'Accommodations';
$portal_config['project_settings']['itsi']['show_activities_link_to_students'] = 'yes';
$portal_config['project_settings']['itsi']['activities_navigation_word'] = 'Activities';

$portal_config['project_settings']['itsi']['diy_server'] = 'itsidiy.concord.org';
$portal_config['project_settings']['itsi']['diy_server_path'] = '';
$portal_config['project_settings']['itsi']['diy_database'] = 'production_itsi_prod';
$portal_config['project_settings']['itsi']['diy_table_prefix'] = 'itsidiy_';
$portal_config['project_settings']['itsi']['diy_activities_name'] = 'activities';
$portal_config['project_settings']['itsi']['diy_runnable_type_name'] = 'Activity';
$portal_config['project_settings']['itsi']['diy_manager_user'] = 'ccadmin';
$portal_config['project_settings']['itsi']['diy_manager_password'] = 'h0ru$';
$portal_config['project_settings']['itsi']['diy_session_name'] = '_ITSI_Do_It_Yourself_8jzY4dj6BrlteA8b_o_Rdg____session_id';
$portal_config['project_settings']['itsi']['diy_use_uuid'] = 'no';
$portal_config['project_settings']['itsi']['diy_param_order'] = 'interface/member';
$portal_config['project_settings']['itsi']['diy_reporting_parameter'] = '';

$portal_config['project_settings']['itsi']['extra_navigation_items'] = array();


// UDL Project

$portal_config['project_settings']['udl']['display_name'] = 'UDL Project Portal';
$portal_config['project_settings']['udl']['site_template'] = 'website/udl-template.php';
$portal_config['project_settings']['udl']['available_actions'] = array('info', 'report', 'run', 'try');
$portal_config['project_settings']['udl']['teacher_available_actions'] = array('guide','info', 'report', 'try');
$portal_config['project_settings']['udl']['use_diy_activities'] = 'no';
$portal_config['project_settings']['udl']['use_accommodations'] = 'yes';
$portal_config['project_settings']['udl']['accommodations_name'] = 'UDL Settings';
$portal_config['project_settings']['udl']['show_activities_link_to_students'] = 'no';
$portal_config['project_settings']['udl']['activities_navigation_word'] = 'Units';

$portal_config['project_settings']['udl']['diy_server'] = 'udl.diy.concord.org';
$portal_config['project_settings']['udl']['diy_server_path'] = '';
$portal_config['project_settings']['udl']['diy_database'] = 'production_udl_prod';
$portal_config['project_settings']['udl']['diy_table_prefix'] = 'udldiy_';
$portal_config['project_settings']['udl']['diy_activities_name'] = 'external_otrunk_activities';
$portal_config['project_settings']['udl']['diy_runnable_type_name'] = 'ExternalOtrunkActivity';
$portal_config['project_settings']['udl']['diy_manager_user'] = 'ccadmin';
$portal_config['project_settings']['udl']['diy_manager_password'] = 'h0ru$';
$portal_config['project_settings']['udl']['diy_session_name'] = '_Universal_Design_in_Science_Education_8jzY4dj6BrlteA8b_o_Rdg____session_id';
$portal_config['project_settings']['udl']['diy_use_uuid'] = 'no';
$portal_config['project_settings']['udl']['diy_param_order'] = 'interface/member';
$portal_config['project_settings']['udl']['diy_reporting_parameter'] = '?reporting=reporting';

$portal_config['project_settings']['udl']['extra_navigation_items'] = array();
$portal_config['project_settings']['udl']['extra_navigation_items'][] = array(
	'label' => 'Teacher Resources',
	'value' => 'http://udl.concord.org/share/teacher-guides/',
	'deny' => 'student'
);



// CAPA Project

$portal_config['project_settings']['capa']['display_name'] = 'CAPA Project Portal';
$portal_config['project_settings']['capa']['site_template'] = 'website/capa-template.php';
$portal_config['project_settings']['capa']['available_actions'] = array('info', 'report', 'run', 'try');
$portal_config['project_settings']['capa']['teacher_available_actions'] = array('guide','info', 'report', 'run', 'try');
$portal_config['project_settings']['capa']['use_diy_activities'] = 'no';
$portal_config['project_settings']['capa']['show_activities_link_to_students'] = 'no';
$portal_config['project_settings']['capa']['show_front_page_image'] = 'no';
$portal_config['project_settings']['capa']['show_probe_interface'] = 'no';
$portal_config['project_settings']['capa']['show_probe_model_info'] = 'no';
$portal_config['project_settings']['capa']['activities_navigation_word'] = 'Assessments';

$portal_config['project_settings']['capa']['diy_server'] = 'capa.diy.concord.org';
$portal_config['project_settings']['capa']['diy_server_path'] = '';
$portal_config['project_settings']['capa']['diy_database'] = 'production_capa_prod';
$portal_config['project_settings']['capa']['diy_table_prefix'] = 'capa_';
$portal_config['project_settings']['capa']['diy_activities_name'] = 'external_otrunk_activities';
$portal_config['project_settings']['capa']['diy_runnable_type_name'] = 'ExternalOtrunkActivity';
$portal_config['project_settings']['capa']['diy_manager_user'] = 'ccadmin';
$portal_config['project_settings']['capa']['diy_manager_password'] = 'h0ru$';
$portal_config['project_settings']['capa']['diy_session_name'] = '_CAPA_8jzY4dj6BrlteA8b_o_Rdg____session_id';
$portal_config['project_settings']['capa']['diy_use_uuid'] = 'no';
$portal_config['project_settings']['capa']['diy_param_order'] = 'interface/member';
$portal_config['project_settings']['capa']['diy_reporting_parameter'] = '?reporting=report_mode%26otrunk.root.localid%3Dscript_report_object';

$portal_config['project_settings']['capa']['extra_navigation_items'] = array();

// Loops Project

$portal_config['project_settings']['loops']['display_name'] = 'LOOPS Project Portal';
$portal_config['project_settings']['loops']['site_template'] = 'website/loops-template.php';
$portal_config['project_settings']['loops']['available_actions'] = array('create', 'copy', 'edit', 'info', 'preview', 'report', 'run', 'try');
$portal_config['project_settings']['loops']['teacher_available_actions'] = array('guide','create', 'copy', 'edit', 'info', 'preview', 'report', 'run', 'try');
$portal_config['project_settings']['loops']['use_diy_activities'] = 'yes';
$portal_config['project_settings']['loops']['show_activities_link_to_students'] = 'no';
$portal_config['project_settings']['loops']['show_front_page_image'] = 'yes';
$portal_config['project_settings']['loops']['activities_navigation_word'] = 'Activities';

$portal_config['project_settings']['loops']['diy_server'] = 'loops.diy.concord.org';
$portal_config['project_settings']['loops']['diy_server_path'] = '';
$portal_config['project_settings']['loops']['diy_database'] = 'production_loops_prod';
$portal_config['project_settings']['loops']['diy_table_prefix'] = 'loopsdiy_';
$portal_config['project_settings']['loops']['diy_activities_name'] = 'external_otrunk_activities';
$portal_config['project_settings']['loops']['diy_runnable_type_name'] = 'ExternalOtrunkActivity';
$portal_config['project_settings']['loops']['diy_manager_user'] = 'ccadmin';
$portal_config['project_settings']['loops']['diy_manager_password'] = 'h0ru$';
$portal_config['project_settings']['loops']['diy_session_name'] = '_LOOPS_Do_It_Yourself_8jzY4dj6BrlteA8b_o_Rdg____session_id';

$portal_config['project_settings']['loops']['diy_use_uuid'] = 'no';
$portal_config['project_settings']['loops']['diy_param_order'] = 'interface/member';
$portal_config['project_settings']['loops']['diy_reporting_parameter'] = '?reporting=report_mode';

$portal_config['project_settings']['loops']['extra_navigation_items'] = array();

// RI-ITEST Project

$portal_config['project_settings']['ri-itest']['display_name'] = 'RI-ITEST Project Portal';
$portal_config['project_settings']['ri-itest']['site_template'] = 'website/ri-itest-template.php';
$portal_config['project_settings']['ri-itest']['available_actions'] = array('info', 'run', 'try');
$portal_config['project_settings']['ri-itest']['teacher_available_actions'] = array('guide','info', 'try');
$portal_config['project_settings']['ri-itest']['use_diy_activities'] = 'no';
$portal_config['project_settings']['ri-itest']['show_activities_link_to_students'] = 'no';
$portal_config['project_settings']['ri-itest']['show_front_page_image'] = 'yes';
$portal_config['project_settings']['ri-itest']['show_probe_interface'] = 'no';
$portal_config['project_settings']['ri-itest']['show_probe_model_info'] = 'no';
$portal_config['project_settings']['ri-itest']['activities_navigation_word'] = 'Activities';

$portal_config['project_settings']['ri-itest']['diy_server'] = 'ri-itest.diy.concord.org';
$portal_config['project_settings']['ri-itest']['diy_server_path'] = '';
$portal_config['project_settings']['ri-itest']['diy_database'] = 'production_ri_itest_prod';
$portal_config['project_settings']['ri-itest']['diy_table_prefix'] = 'riitestdiy_';
$portal_config['project_settings']['ri-itest']['diy_activities_name'] = 'external_otrunk_activities';
$portal_config['project_settings']['ri-itest']['diy_runnable_type_name'] = 'ExternalOtrunkActivity';
$portal_config['project_settings']['ri-itest']['diy_manager_user'] = 'ccadmin';
$portal_config['project_settings']['ri-itest']['diy_manager_password'] = 'h0ru$';
$portal_config['project_settings']['ri-itest']['diy_session_name'] = '_RI_ITEST_DIY_8jzY4dj6BrlteA8b_o_Rdg____session_id';
$portal_config['project_settings']['ri-itest']['diy_use_uuid'] = 'no';
$portal_config['project_settings']['ri-itest']['diy_param_order'] = 'interface/member';
$portal_config['project_settings']['ri-itest']['diy_reporting_parameter'] = '?reporting=report_mode';

$portal_config['project_settings']['ri-itest']['extra_navigation_items'] = array();

// PAEMST Project

$portal_config['project_settings']['paemst']['display_name'] = 'Presidential Award Portal';
$portal_config['project_settings']['paemst']['site_template'] = 'website/paemst-template.php';
$portal_config['project_settings']['paemst']['available_actions'] = array('create', 'copy', 'edit', 'info', 'preview', 'report', 'run', 'try');
$portal_config['project_settings']['paemst']['teacher_available_actions'] = array('guide','create', 'copy', 'edit', 'info', 'preview', 'report', 'run', 'try');
$portal_config['project_settings']['paemst']['use_diy_activities'] = 'yes';
$portal_config['project_settings']['paemst']['show_activities_link_to_students'] = 'yes';
$portal_config['project_settings']['paemst']['activities_navigation_word'] = 'Activities';

$portal_config['project_settings']['paemst']['diy_server'] = 'paemst.diy.concord.org';
$portal_config['project_settings']['paemst']['diy_server_path'] = '';
$portal_config['project_settings']['paemst']['diy_database'] = 'production_paemst_prod';
$portal_config['project_settings']['paemst']['diy_table_prefix'] = 'pamsdiy_';
$portal_config['project_settings']['paemst']['diy_activities_name'] = 'activities';
$portal_config['project_settings']['paemst']['diy_runnable_type_name'] = 'Activity';
$portal_config['project_settings']['paemst']['diy_manager_user'] = 'ccadmin';
$portal_config['project_settings']['paemst']['diy_manager_password'] = 'h0ru$';
$portal_config['project_settings']['paemst']['diy_session_name'] = '_PAEMST_Activity_Authoring_8jzY4dj6BrlteA8b_o_Rdg____session_id';
$portal_config['project_settings']['paemst']['diy_use_uuid'] = 'no';
$portal_config['project_settings']['paemst']['diy_param_order'] = 'interface/member';
$portal_config['project_settings']['paemst']['diy_reporting_parameter'] = '';

$portal_config['project_settings']['paemst']['extra_navigation_items'] = array();


// Evolution Project

$portal_config['project_settings']['er']['display_name'] = 'Evolution Readiness Project Portal';
$portal_config['project_settings']['er']['site_template'] = 'website/er-template.php';
$portal_config['project_settings']['er']['available_actions'] = array('info', 'report', 'run', 'try');
$portal_config['project_settings']['er']['teacher_available_actions'] = array('guide','info', 'report', 'try');
$portal_config['project_settings']['er']['use_diy_activities'] = 'no';
$portal_config['project_settings']['er']['use_accommodations'] = 'yes';
$portal_config['project_settings']['er']['accommodations_name'] = 'Evolution Settings';
$portal_config['project_settings']['er']['show_activities_link_to_students'] = 'no';
$portal_config['project_settings']['er']['activities_navigation_word'] = 'Activities';

$portal_config['project_settings']['er']['diy_server'] = 'er.diy.concord.org';
$portal_config['project_settings']['er']['diy_server_path'] = '';
$portal_config['project_settings']['er']['diy_database'] = 'production_er_prod';
$portal_config['project_settings']['er']['diy_table_prefix'] = '';
$portal_config['project_settings']['er']['diy_activities_name'] = 'external_otrunk_activities';
$portal_config['project_settings']['er']['diy_runnable_type_name'] = 'ExternalOtrunkActivity';
$portal_config['project_settings']['er']['diy_manager_user'] = 'ccadmin';
$portal_config['project_settings']['er']['diy_manager_password'] = 'h0ru$';
$portal_config['project_settings']['er']['diy_session_name'] = '_Evolution_Readiness_8jzY4dj6BrlteA8b_o_Rdg____session_id';
$portal_config['project_settings']['er']['diy_use_uuid'] = 'no';
$portal_config['project_settings']['er']['diy_param_order'] = 'interface/member';
$portal_config['project_settings']['er']['diy_reporting_parameter'] = '';

$portal_config['project_settings']['er']['extra_navigation_items'] = array();
/*
$portal_config['project_settings']['er']['extra_navigation_items'][] = array(
	'label' => 'Teacher Resources',
	'value' => 'http://er.concord.org/share/teacher-guides/',
	'deny' => 'student'
);
*/

// Geniquest Project

$portal_config['project_settings']['geniquest']['display_name'] = 'Geniquest Project Portal';
$portal_config['project_settings']['geniquest']['site_template'] = 'website/geniquest-template.php';
$portal_config['project_settings']['geniquest']['available_actions'] = array('info', 'report', 'run', 'try');
$portal_config['project_settings']['geniquest']['teacher_available_actions'] = array('guide','info', 'report', 'try');
$portal_config['project_settings']['geniquest']['use_diy_activities'] = 'no';
$portal_config['project_settings']['geniquest']['use_accommodations'] = 'yes';
$portal_config['project_settings']['geniquest']['accommodations_name'] = 'Geniquest Settings';
$portal_config['project_settings']['geniquest']['show_activities_link_to_students'] = 'no';
$portal_config['project_settings']['geniquest']['activities_navigation_word'] = 'Activities';

$portal_config['project_settings']['geniquest']['diy_server'] = 'geniquest.diy.concord.org';
$portal_config['project_settings']['geniquest']['diy_server_path'] = '';
$portal_config['project_settings']['geniquest']['diy_database'] = 'production_geniquest_prod';
$portal_config['project_settings']['geniquest']['diy_table_prefix'] = '';
$portal_config['project_settings']['geniquest']['diy_activities_name'] = 'external_otrunk_activities';
$portal_config['project_settings']['geniquest']['diy_runnable_type_name'] = 'ExternalOtrunkActivity';
$portal_config['project_settings']['geniquest']['diy_manager_user'] = 'ccadmin';
$portal_config['project_settings']['geniquest']['diy_manager_password'] = 'h0ru$';
$portal_config['project_settings']['geniquest']['diy_session_name'] = '_Geniquest_DIY_8jzY4dj6BrlteA8b_o_Rdg____session_id';
$portal_config['project_settings']['geniquest']['diy_use_uuid'] = 'no';
$portal_config['project_settings']['geniquest']['diy_param_order'] = 'interface/member';
$portal_config['project_settings']['geniquest']['diy_reporting_parameter'] = '';

$portal_config['project_settings']['geniquest']['extra_navigation_items'] = array();
/*
$portal_config['project_settings']['geniquest']['extra_navigation_items'][] = array(
	'label' => 'Teacher Resources',
	'value' => 'http://er.concord.org/share/teacher-guides/',
	'deny' => 'student'
);
*/




/****************************************
        General Configuration
****************************************/

$portal_config['default_language'] = 'en';

$portal_config['default_project'] = 'udl'; 

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
$portal_config['icons']['guide'] = '/images/icons/apple.png';

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
$portal_config['ie-icons']['guide'] = '/images/icons/gifs/apple.png';


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
