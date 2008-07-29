<?php

$page_title = 'FAQ - Frequently Asked Questions';


echo '
<div style="color: #444444;">

<p><strong>When I run an activity for the first time, it takes a long time to start. Why?</strong></p>

<p>The activity is run on your computer, so it needs to download several files before it can start up. These files contain the Java code to run all the interactive models in the activity. There are several large models used in our software so the initial download is large. Our software uses Java Web Start handle the downloading of these files in order to make this download as efficient as possible.</p>

<p><strong>What is Java Web Start?</strong></p>

<p>Java Web Start is a program created by Sun Microsystems, the creators of Java. It is included in all standard installations of Java. Java Web Start is used to download and launch Java applications from web pages. Java Web Start downloads the files needed to run Java application in a compressed format. It caches these files, so later start up times are fast. When a file is updated on the web site, Java Web Start only downloads the changes.</p>

<p><strong>What exactly gets downloaded to my computer?</strong></p>

<p>A Java application is downloaded to your computer. After this application is downloaded, it is automatically launched. This application then displays the content for the particular activity you clicked on. The application\'s files are saved in the Java Web Start cache. This application is not installed in Program Files (Windows), or in Applications (Mac). The Java application will not interfere with your other applications.</p>

<p><strong>Do I have to register in the portal to run activities?</strong></p>

<p>That depends. Some projects allow you to run activities without registering.  Look for a link called "Activities" or "Units" in the navigation bar to see if that is the case with this project.</p>

<p><strong>Is student work saved?</strong></p>

<p>Yes! Student work is automatically saved when a student quits an activity. She/he does not need to click a special "save" button. Teachers can see student work through the portal by setting up a class and having students associated with that class.</p>
';

if ($_PORTAL['project'] == 'itsi') {

	echo '
	<p><strong>What are the probe kit activities listed in the portal?</strong></p>
	
	<p>The Concord Consortium developed over 50 activities for use with probes that can be built with simple, inexpensive materials. We put together kits of these materials, which can be used to measure 14 different parameters. For more information on the kits and where to purchase the contents, see <a href="http://probesight.concord.org/probekit/" target="_blank">http://probesight.concord.org/probekit/</a>.</p>
	
	<p><strong>Can I customize an activity?</strong></p>
	
	<p>Yes! When you are logged into the portal, you can preview an activity, which opens the activity in a web browser in the ITSI DIY (Do It Yourself) site. By each activity title, you should see links to run the activity, test the activity (run it without saving data), and copy the activity. Copy the activity, then make any changes you want, and save.</p>
	';

}

echo '
</div>
';

?>