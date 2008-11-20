<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
        "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<title>Geniquest <?php echo $sassy_config['site_title']; ?> - <?php echo $page_title; ?></title>

	<link rel="stylesheet" type="text/css" href="/scripts/reset-fonts-grids.css"> 

	<script type="text/javascript" src="/scripts/utilities.js"></script> 
	<script type="text/javascript" src="/scripts/yahoo-dom-event.js"></script> 
	<script type="text/javascript" src="/scripts/datasource-beta-min.js"></script> 
	<script type="text/javascript" src="/scripts/datatable-beta-min.js"></script> 

	<script type="text/javascript" src="/scripts/jquery-1.2.min.js"></script> 
	<script type="text/javascript" src="/scripts/jquery.tablesorter.pack.js"></script> 

	<link rel="stylesheet" type="text/css" href="/css/geniquest.css">

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
	
	<h1>Geniquest Portal - <?php echo $page_title; ?></h1>
	
	%%PAGE_CONTENT%%
		
	</div>
	</div>
	
	<div id="sidebar-container">
	<div id="sidebar-content">
	
		<div><!-- <a href="http://geniquest.concord.org/"> --><img src="/images/geniquest-logo.png" alt="Geniquest"></a></div>
		
		<?php echo portal_generate_user_info_box(); ?>

		<?php echo portal_generate_user_navigation(); ?>

		<?php // echo sassy_generate_navbar('main', "\n","<div id=\"navigation\">\n<ul>","</ul>\n</div>",'','<li><span class="%%CLASS%%">%%LINK%%</span></li>'); ?>
		
	</div>
	</div>
	
	<div id="copyright-container">
	<div id="copyright-content">
	
		<div><a href="http://www.concord.org/"><img src="/images/cc_logo_gray_text.gif" alt="The Concord Consortium"></a><br>
		<br>© 2008 All Rights Reserved</div>

	</div>
	</div>

</div>
</div>


</body>
</html>
