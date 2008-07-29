<?php

$page_title = 'Technical Notes and Requirements';

echo '
<div style="color: #444444;">
';

switch ($_PORTAL['project']) {


	case 'capa':

		echo '
		<h2>System Requirements</h2>
		
		<p>In order to run our software, you must have the following:</p>
		
		<ul>
		
			<li>Microsoft Windows 2000 or later</li>
			<li><a href="http://java.sun.com/javase/downloads" target="_blank">Java Runtime Environment (1.5 or later) with Web Start</a></li>
			<li><a href="http://joule.ni.com/nidu/cds/view/p/id/861/lang/en" target="_blank">LabVIEW 8.5 Runtime Engine</a></li>
			<li><a href="http://www.adobe.com/products/flashplayer/" target="_blank">Adobe Flash Player</a></li>
			<li><a href="http://www.adobe.com/products/acrobat/readstep2.html" target="_blank">Adobe Reader</a></li>
			<li>User privileges to install to <strong>c:\Program Files</strong></li>

		</ul>
		
		<p><a target="_blank" href="http://capa.concord.org/software/">Find out more about these requirements</a></p>
		
		';
	
	break;
	
	default:
	
		echo '
		<h2>Technical Notes</h2>
	
		<h3>Flash Support</h3>
		
		<p><a href="http://jnlp.concord.org/dev/mozswing/mozswing.jnlp">Install Embedded Flash Support</a></p>
		
		<p><strong>Note:</strong> You may need to install <a href="http://www.mozilla.com/firefox/">Firefox</a> and the <a href="http://www.adobe.com/go/getflashplayer">Flash Player</a> if it is not already on your
		system.</p>
		
		<h3>Mac OS X Web Start Fix</h3>
		
		<p>If you are using MacOS 10.4 or later, you will almost certainly need to <a href="http://confluence.concord.org/display/CCTR/How+to+fix+the+WebStart+bug"><strong>fix a Java Web Start bug</strong></a>. You will need to follow the steps on that page once for each computer on which you run our activities, and additionally each time that java is updated.</p>
		';
	
	break;

}

echo '
</div>
';


?>