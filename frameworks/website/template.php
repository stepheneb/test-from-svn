<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
        "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<title><?php echo $sassy_config['site_title']; ?> - <?php echo $page_title; ?></title>

	<link rel="stylesheet" type="text/css" href="/scripts/reset-fonts-grids.css"> 
	<link type="text/css" rel="stylesheet" href="http://yui.yahooapis.com/2.2.2/build/datatable/assets/datatable.css"> 

	<script type="text/javascript" src="/scripts/utilities.js"></script> 
	<script type="text/javascript" src="/scripts/yahoo-dom-event.js"></script> 
	<script type="text/javascript" src="http://yui.yahooapis.com/2.2.2/build/datasource/datasource-beta-min.js"></script> 
	<script type="text/javascript" src="http://yui.yahooapis.com/2.2.2/build/datatable/datatable-beta-min.js"></script> 

	<link rel="stylesheet" type="text/css" href="/css/main.css">

	<script type="text/javascript" src="/scripts/portal.js"></script>

	<!--[if lt IE 7]>
	<link rel="stylesheet" href="/css/ie.css" type="text/css">
	<![endif]-->

</head>
<body>

<div id="main-container">
<div id="main-content">

	<div id="page-container">
	<div id="page-content">
	
	<h1>ITSI Portal — <?php echo $page_title; ?></h1>
	
	%%PAGE_CONTENT%%
		
	</div>
	</div>
	
	<div id="sidebar-container">
	<div id="sidebar-content">
	
		<div><a href="http://itsi.concord.org/"><img src="/images/itsi-logo-150.gif" alt="ITSI"></a></div>
		
		<?php echo portal_generate_user_info_box(); ?>

		<?php echo portal_generate_user_navigation(); ?>

		<?php // echo sassy_generate_navbar('main', "\n","<div id=\"navigation\">\n<ul>","</ul>\n</div>",'','<li><span class="%%CLASS%%">%%LINK%%</span></li>'); ?>
		
	</div>
	</div>
	
	<div id="copyright-container">
	<div id="copyright-content">
	
		<div><a href="http://itsi.concord.org/"><img src="/images/cc_logo_gray_text.gif" alt="The Concord Consortium"></a><br>
		<br>© 2007 All Rights Reserved</div>

	</div>
	</div>

</div>
</div>


</body>
</html>
