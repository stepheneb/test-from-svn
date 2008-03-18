<?php

$page_title = 'Help';

if (@$_SESSION['is_logged_in'] != 'yes') {

	echo '
	<div style="color: #444444;">
		<h2>New to the site?</h2>
	
		<p>Click on the <strong>HOME</strong> link at the left then decide whether to sign up a as teacher or a student.</p>

		<h2>Been to the site before?</h2>
		
		<p>Click on the <strong>HOME</strong> link at the left and enter your email address and password.</p>

		<h2>I don\'t know my password</h2>
		
		<p>If you are student, please ask your teacher to give you your password by checking your class roster.</p>
		
		<p>If you are a teacher, Click on the <strong>HOME</strong> link at the left and click the <strong>"I don\'t know my password"</strong> link under
		the boxes used to sign in.</p>
		
		<h2>I don\'t want to sign up for anything</h2>

		<p>If you see a ' . $portal_config['activities_navigation_word'] . ' link at the left, you can try the activites without signing up.</p>

	</div>
	';

} elseif ($_SESSION['portal']['member_type'] == 'student') {
	
	echo '
	<div style="color: #444444;">

	
	<h2>Sign In</h2>
	
	<p>If you’re a student and your teacher asked you to visit this website, she/he may have given you a username and password. </p>
	
	<p>Simply type your username into the <strong>Username</strong> field, and your password into the <strong>Password</strong> field. Then click the “Sign In” button. </p>
	
	<p><strong>OR</strong></p>
	
	<p>If your teacher did not give you a username and password, but instead gave the whole class a special “Sign-up Word,” you’ll need to create an account first.</p>
	
	<p>To create a new account, click the link to <strong>Sign up as a Student</strong>.</p>
	
	<ol>
	
	<li>1. Enter your first name, last name, and a password (create a password between 4 and 40 characters long).</li>
	
	<li>2. Enter the Sign-up Word your teacher gave you.</li>
	
	<li>3. Click “Continue.”</li>
	
	</ol>
	
	<h2>Running activities</h2>
	
	<p>After you log in, you should see a list of activities that your teacher has assigned for your class. </p>
	
	<p>You can read a short description of the activity by clicking the ' . portal_icon('info') . ' icon to get info. Or you can run the activity by clicking the ' . portal_icon('run') . ' green arrow.</p>
	
	<p><strong>Note:</strong> when you run an activity, a Java window will pop up. </p>
	
	<p>Choose to <strong>open</strong> the file.</p>
	
	<p>Depending on the connection speed, it may take anywhere from a few seconds to a couple minutes for the activity to open. <strong>Be patient!</strong></p>
	
	<h2>Saving data</h2>
	
	<p>Your data is saved when you close an activity. You do not need to click any special button!  When you’re finished with the activity, go to the File menu, and select “Exit” or click the red X button in the upper left of the activity. </p>
	 
	
	
	<h2>Sign out?</h2>
	
	<p>When you’re signed in to the Portal, you’ll notice on the left below your name that there is an option to “Sign out.”  </p>
	
	<p>Make sure to sign out after you’re done.</p>
	
	<h2>Changing your information</h2>
	
	<p>You can change your name, password, or probe interface (by default the Vernier Go!Link is selected). </p>
	 

	</div>
	';

} else {

	echo '
	
	<div style="color: #444444;">
	
	<h2><a name="top"></a>Help Contents</h2>
	
	<ul>
		<li><a href="#getstarted">Getting Started</a></li>
		<li><a href="#students">Portal for Students</a></li>
		<li><a href="#extras">Additional Information</a></li>
	</ul>
	
	<hr>
	
	<h2><a name="getstarted"></a>Four steps to Getting Started</h2>
	
	<h3>1. Sign up</h3>
	
	<p>To make full use of the Portal, you need to create an account. Or, if you just want to try the activities, click the link to View our unit previews.</p>
	
	<p>To create a new account, click the link to Sign up as a teacher.</p>
	
	<ol>
	
	<li>Enter your name, email address, and password.</li>
	
	<li>Choose your school from the list.  If your school is not listed, select “Other” and add the required information about your school (name, address, and so on).</li>
	
	<li>Click “Continue.”</li>
	
	</ol>
	
	<h3>2. Add class</h3>
	
	<p>You’ll need to complete two fields when you add a class:</p>
	
	<ul>
	<li>Class name: Create a name for your class (e.g., Science Period 2 or Science Grade 5).</li>
	
	<li>Sign-up word: Your students will use this word to sign up in this class. All students in your class will use the same word, which allows them to be directly registered into your class. Students use this word only if they will register themselves. Note: the class word must be unique in the system (e.g., pickle, plasma). </li>
	</ul>
	
	<p>Click the button titled “Save this Class.”</p>
	
	<h3>3. Select activities</h3>
	
	<p>It’s time to select various activities associated with your class.  You may choose from any of the available activities.</p>
	
	<p>When your students log in, they will now see only the units you selected.  </p>
	
	<p>After you’ve selected one or more units for your students, click the button titled “Save this Class.”</p>
	
	<p>You can preview what your students will see by going back to the home page, selecting the class that you’re interested in, and clicking this icon:  ' . portal_icon('list') . '</p>
	
	<h3>4. Add students</h3>
	
	<p>Now that you’ve created a class and selected the activities for your students, you need to add students to your class.  Return to your home page.</p>
	
	<p>Click the Add a new student link, and enter the following information for the first student on your list.</p>
	
	<ul>
	<li>First name</li>
	<li>Last name</li>
	<li>Password – must be at least 4 characters long.</li>
	<li>Class – select the class from the list</li>
	<li>Interface – choose between Vernier Go!link and Vernier LabPro</li>
	</ul>
	
	<p>After entering one student, you can: </p>
	
	<ul>
	<li>Add another new student</li>
	<li>View the roster for this student\'s class</li>
	<li>Edit this student</li>
	<li>Return to your home page</li>
	</ul>
	
	<p>Continue to add students one at a time until you’ve completed your class roster.  </p>
	
	<p>You can then view the class roster to confirm you’ve entered all your students.  </p>
	
	<p><strong>Tip: print the class roster page, which shows all student passwords.  You can tell each student his/her username and password.  </strong></p>
	
	
	
	<p><em><a href="#top">Return to Contents</a></em></p>
	
	<hr>
	
	
	
	
	
	<h2><a name="students"></a>Portal for your students</h2>
	
	<p>Have your students access the Portal by going to this website.</p>
	
	<p>Because you added students, they do not need to sign up as first-time users. They can immediately log in with their username and passwords (use the class roster to give each student his/her information). Note: usernames and passwords are not case-sensitive.</p>
	
	<h3>Running activities</h3>
	
	<p>When students run an activity from the Portal, a Java window will pop up.  </p>
	
	<p>Students should choose to open the file.</p>
	
	<p>Depending on the connection speed, it may take anywhere from a few seconds to a couple minutes for the activity to open.</p>
	
	<h3>Saving data</h3>
	
	<p>Student data is saved when a student closes an activity.  They do not need to click any special button!  </p>
	
	<h3>Reviewing student work</h3>
	
	<p>When you sign in as a teacher, you have access to viewing student progress for the whole class or for an individual student. 
	You can see individual students’ responses by clicking this icon:   ' . portal_icon('work') . '</p>
	
	
	
	
	<p><em><a href="#top">Return to Contents</a></em></p>
	
	<hr>
	
	
	
	
	
	
	<h2><a name="extras"></a>A few extras</h2>
	
	<h3>Features and Icons</h3>
	
	<p>The Portal allows you to do several different things – from adding classes and students to viewing reports.  </p>
	
	<p>These different features are available by clicking various icons. To learn about these icons, expand the icon legend in the upper-right corner of the Portal home page by clicking the plus (+) symbol.  Close it by clicking the minus (-) symbol.  Or hold your mouse over an icon to read the short description.</p>
	
	<h3>Sign-outs</h3>
	
	<p>When you’re signed in to the Portal, you’ll notice on the left below your name that there is an option to “Sign out.”  This is important if you’ve signed in on a student’s computer, for instance, but don’t want that student to have your access.  In that case, be sure to sign out.</p>
	
	<p><strong>Sign out after your work session.</strong></p>
	
	<h3>Changing your information</h3>
	
	<p>You can change your name, email address, or password.</p>
	
	<p>You can also change your probe interface (by default the Vernier Go!Link is selected).  </p>
	
	<p><em><a href="#top">Return to Contents</a></em></p>
	
	<hr>
	
	<p>If you need additional assistance or have a question that has not been answered here, please contact <a href="mailto:webmaster@concord.org">webmaster@concord.org</a></p>
	
	</div>
	
	';
	
}	
	
?>