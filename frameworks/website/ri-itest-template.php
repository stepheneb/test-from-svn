<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
        "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<title>RI-ITEST: <?php echo $sassy_config['site_title']; ?> - <?php echo $page_title; ?></title>
	<meta name="keywords" content="<?php echo $page_keywords; ?>">
	<meta name="description" content="<?php echo $page_description; ?>">

	<script type="text/javascript" src="/scripts/utilities.js"></script> 
	<script type="text/javascript" src="/scripts/yahoo-dom-event.js"></script> 
	<script type="text/javascript" src="/scripts/datasource-beta-min.js"></script> 
	<script type="text/javascript" src="/scripts/datatable-beta-min.js"></script> 

	<script type="text/javascript" src="/scripts/jquery-1.2.min.js"></script> 
	<script type="text/javascript" src="/scripts/jquery.tablesorter.pack.js"></script> 

	<link rel="stylesheet" type="text/css" href="/css/ri-itest.css">

	<script type="text/javascript" src="/scripts/portal.js"></script>

	<!--[if lt IE 7]>
	<link rel="stylesheet" href="/css/ri-itest-ie.css" type="text/css">
	<![endif]-->


</head>
<body>

<div id="container">

	<div id="content-container">
	
		<div id="overlay"><a href="#" title="Image: Biological Molecule 1CRN"></a></div>

		<div id="navigation">

			<ul>
				<li><span><a href="http://ri-itest.concord.org/" title="RI-ITEST Home">Home</a></span></li>
				<li><span><a href="http://ri-itest.concord.org/about/" title="Details about the RI-ITEST project">About</a></span></li>
				<li><span><a href="http://ri-itest.concord.org/pubs/" title="Read or view our publications">Publications</a></span></li>
				<li><span><a href="http://mw2.concord.org/tmp.jnlp?address=http://mw2.concord.org/myhome.jsp?client=mw" title="Run our Activities using Molecular Workbench">Activities</a></span></li>
				<li><span><a href="http://moodle.concord.org/cc/course/view.php?id=15" title="The RI-ITEST Course">Course</a></span></li>
				<li><strong class="nav-selected"><a href="/" title="Portal Home">Portal</a></strong></li>
			</ul>

		</div>
		
		<div id="main-content">
				
		<?php 
			$options = array('ignore-home' => 'yes'); 
			echo portal_generate_user_navigation($options);
		?>

		<?php echo str_replace('<br>', ' ', str_replace('<br><br>', ' | ', portal_generate_user_info_box())); ?>

		%%PAGE_CONTENT%%
		
		<div style="clear: right;"></div>
		
		</div>
		
		<div id="copyright">

			<p>© Copyright <?php echo date('Y'); ?>, The Concord Consortium, Inc.</p>
	
			<p class="updated-info">Last updated: <?php echo date('m/d/Y', getlastmod()); ?></p>
			
			<p class="legal">These materials are based upon work supported by the National 
			Science Foundation under grant number ESI-0737649.</p>

			<p class="legal">Any opinions, findings, and conclusions
			or recommendations expressed in this material are those of
			the author(s) and do not necessarily reflect the views
			of the National Science Foundation.</p>

		</div>
		
	</div>

</div>

</body>
</html>
