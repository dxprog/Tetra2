<?php

/********************************************
* Tetra                                     *
* Copyright (c) 2004 Matt "dxprog" Hackmann *
* More copyright info can be found in       *
* license.txt                               *
********************************************/

$start_time = microtime();
$start_time = explode (" ", $start_time);
$start_time = $start_time[1] + $start_time[0];

/* Include the Tetra and error class modules */
require ("./api/core/tetra.php");
require ("./api/core/error.php");

/* Include core config files */
@include ("./config/core.php");

/* If Tetra hasn't been setup yet, include the setup module and go from there */
if ($b_Setup == false) {
	/* Include the setup API */
	include ("./setup.php");
	
	/* Create the setup class */
	$setup = new Setup ();
	
	/* Write the HTML header */
	echo ("<html>\n<head><title>Tetra Setup</title>\n</head>\n<body bgcolor=\"#EEEEEE\">\n");
	
	/* Setup a nice looking table */
	echo ("<center style=\"font-family: Sans-serif; font-size: 16px; font-weight: bold; color: #000000;\">Tetra Setup\n");
	
	/* Show the setup screen */
	$setup->HandleRequest($_GET["step"]);
	
	/* Write the HTML footer */
	echo ("</center></body></html>\n");

	/* If this was the final step, delete the setup file */
	if ($_GET["step"] == "done")
		unlink ("./setup.php");
		
}
else {

	/* Include core API files */
	require ("./api/core/constants.php");
	require ("./api/core/layout.php");
	require ("./api/core/cache.php");
	require ("./api/core/sessions.php");
	require ("./api/core/templating.php");
	require ("./api/core/misc.php");
	require ("./api/core/users.php");
	require ("./api/core/admin.php");
	require ("./api/core/nav.php");

	/* Include the database APIs */
	require ("./api/databases/db_$s_DbName.php");

	/* Super globals (or whatever :-P) */
	$a_User = "";
	$a_Modules = "";
	$b_Theme = false;

	/* Setup the user, admin and navigation classes */
	$a_Modules["users"] = new Users ();
	$a_Modules["admin"] = new Admin ();
	$a_Modules["nav"] = new Nav ();

	/* Rank */
	$ranks = array ("Member", "Teacher", "Moderator", "Admin");

	/* Create the Tetra class */
	Tetra::GeneratePage ();

}

?>