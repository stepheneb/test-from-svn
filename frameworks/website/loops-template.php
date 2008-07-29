<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
        "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<title>LOOPS: <?php echo $sassy_config['site_title']; ?> - <?php echo $page_title; ?></title>
	<meta name="keywords" content="<?php echo $page_keywords; ?>">
	<meta name="description" content="<?php echo $page_description; ?>">

	<script type="text/javascript" src="/scripts/utilities.js"></script> 
	<script type="text/javascript" src="/scripts/yahoo-dom-event.js"></script> 
	<script type="text/javascript" src="/scripts/datasource-beta-min.js"></script> 
	<script type="text/javascript" src="/scripts/datatable-beta-min.js"></script> 

	<script type="text/javascript" src="/scripts/jquery-1.2.min.js"></script> 
	<script type="text/javascript" src="/scripts/jquery.tablesorter.pack.js"></script> 

	<link rel="stylesheet" type="text/css" href="/css/loops.css">

	<script type="text/javascript" src="/scripts/portal.js"></script>



</head>
<body>


<div id="container">

	<div id="logo">
		<img src="http://loops.concord.org/images/loops-logo-tagline.gif" alt="LOOPS">
	</div>
	
	<div id="navigation">
	
	<ul>
		<li><span><a href="http://loops.concord.org/">Home</strong></a></li>
		<li><span><a href="http://loops.concord.org/about/" title="Details about the CAPA project">About</a></span></li>
		<li><span><strong>Portal</strong></span></li>
	</ul>
	
	</div>

	<?php 
		$options = array('ignore-home' => 'no'); 
		echo portal_generate_user_navigation($options);
	?>

	<?php echo str_replace('<br>', ' ', str_replace('<br><br>', ' | ', portal_generate_user_info_box())); ?>

	
	<div id="content">
	
		<div id="content-content">
		
			%%PAGE_CONTENT%%
			
			<div class="clear-floats"></div>
			
		</div>
		
		
	</div>
	
	<div id="copyright">
		
		<p><img src="/images/cc_logo_gray_text.gif" alt="CC Logo"></p>
	
		<p>© <?php echo date('Y'); ?> The Concord Consortium, Inc.  All Rights Reserved.</p>
		
		<p class="updated-info">Last updated: <?php echo date('m/d/Y', getlastmod()); ?></p>
	
	</div>

</div>


</body>
</html>
