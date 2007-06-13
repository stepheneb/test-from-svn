<?php
/**************************************************************

    __  ___              __                        __ __
   /  |/  /__  __ _____ / /_ ___   _____ __  __   / // /
  / /|_/ // / / // ___// __// _ \ / ___// / / /  / // /_
 / /  / // /_/ /(__  )/ /_ /  __// /   / /_/ /  /__  __/
/_/  /_/ \__, //____/ \__/ \___//_/    \__, /     /_/
        /____/                        /____/

Mystery 4.0.0

Developed by Paul Burney
Web: paulburney.com
AIM: PWBurney
E-mail: support@paulburney.com

***************************************************************

functions/debug.php

This file contains functions that can be used when debugging
applications built using the Mystery framework.

**************************************************************/


function mystery_debug_query($connection = 'dbh') {

	// this function displays the last paramaterized query that was processed
	
	global $_MYSTERY;
	
	mystery_print_r('#009900', $_MYSTERY[$connection]->last_query);

}


?>
