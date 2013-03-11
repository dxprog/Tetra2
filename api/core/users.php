<?php

/***************************************************
* Tetra                                            *
* Copyright (c) 2004-2005 Matt "dxprog" Hackmann   *
* More copyright info can be found in              *
* license.txt                                      *
***************************************************/

/********************************************
* Tetra user module                         *
* Version 2.0                               *
********************************************/

/* The user class */
class Users {

	/* Module info */
	var $module_ver = 2.0;
	var $module_name = "User";
	var $module_user_nav_items = true;
	var $module_settings = true;

	/* Ranks */
	var $a_Ranks = array ("Guest", "Member", "Moderator", "Admin");

	/*****************************************
	* Function: TetraHandler                 *
	* Description: Gives data to Tetra       *
	*****************************************/
	function TetraHandler ($i_Request, $s_Parameters)
	{

		global $a_User;
	
		/* See what needs to be done */
		switch ($i_Request) {
		case T_REQUEST_NAV_USER:
			$a_Ret = array ("Edit Profile"=>"users&action=edit_profile", "Edit Layout"=>"users&action=layout", "Logout"=>"&preprocess=users&action=logout");
			if ($a_User["user_rank"] == 3)
				$a_Ret["Admin Panel"] = "admin";
			return $a_Ret;
		}
	
	}
	
	/*****************************************
	* Function:   HandleRequest              *
	* Parameters: $s_Options - says what     *
	*             needs to be done           *
	* Description: Main request handler      *
	*****************************************/
	function HandleRequest ($s_Options)
	{

		/* Get the templating, session, and caching objects */
		global $a_User;

		/* Get the sessions array and the moderator activated thing */
		global $_SESS, $mod_activated;

		/* Figure out what we're doing */
		switch ($s_Options) {
		case "admin":
			$this->Admin ();
			break;
		case "stats":
			/* Stats bar */
			$this->Stats ();
			break;
		case "login":
			/* Log the user in */
			$this->Login ();
			break;
		case "login_form":
			/* Display the login form */
			Tpl::Assign ("last_page", $_GET["last_page"]);
			Tpl::Display ("users_login.tpl");
			break;
		case "profile":
			/* Display info about a user */
			$this->DisplayProfile ();
			break;
		case "logout":
			/* Log the user out */
			$this->Logout ();
			break;
		case "edit_profile":
			$this->EditProfile ();
			break;
		case "update_settings":
			$this->UpdateProfile ();
			break;
		case "register":
			$this->Register ();
			break;
		case "activate":
			$this->Activate ();
			break;
		case "adduser":
			$this->AddUser ();
			break;
		case "stats":
			$this->Stats ();
			break;
		case "password":
			$this->LostPass ();
			break;
		case "layout":
			if ($a_User["user_id"] > 0)
				Layout::EditLayout ();
			break;
		default:
			/* User list */
			$this->Userlist ($_GET["let"]);
			break;
		}
	}

	/*****************************************
	* Function:   Login                      *
	* Parameters: none                       *
	* Description: Logs the user in          *
	*****************************************/
	function Login()
	{

		/* Get the session variable and user array */
		global $_SESS, $a_User;

		/* Make sure no fields were left blank */
		if (!$_POST["user"] || !$_POST["pass"]) {
			/* Display an error */
			Err::Message ("Tetra Error", "Please fill out all fields");
			/* Exit */
			return false;
		}

		/* MD5 the password */
		$_POST["pass"] = md5 ($_POST["pass"]);

		/* To see if the user's login is valid we get all entries from the
		   db where the user name and password match, activated is true
		   and banned is false. If no results are returned one of those
		   don't match, hence invalid login */
		$s_Query = "SELECT * FROM users WHERE user_name='$_POST[user]' AND user_pass='$_POST[pass]' AND user_banned=0 AND user_activated=1";

		/* Get the results */
		$h_Result = DB::db_Query ($s_Query);

		/* See if we got any results */
		if ($h_Result["num_rows"] == 0) {
			/* Display an error message */
			Err::Raise ("Couldn't log you in. The problem could be one of the following: <br>\n<ul>\n<li>Your username\\password was typed incorrectly</li>\n<li>Your user account has not been activated</li>\n<li>You have been banned</li></ul>", E_TETRA_USER, "users");
			
			/* Exit */
			return false;
		}

		/* Get the user's info */
		$a_User = DB::db_Array ($h_Result);

		/* Log the user in */
		$_SESS["sess_uid"] = $a_User["user_id"];

		/* Update the session */
		Session::Update ($_SESS["sess_key"]);

		/* Clear action */
		$_GET["action"] = "";

	}

	/*****************************************
	* Function:   DisplayProfile             *
	* Parameters: none                       *
	* Description: Displays the profile of a *
	*              user                      *
	*****************************************/
	function DisplayProfile ()
	{

		/* Get the modules and user arrays */
		global $a_Modules, $a_User;
	
		/* Validate the ID */
		if (!is_numeric ($_GET["id"]) || !$_GET["id"]) {
			Err::Raise ("Invalid user ID!", E_TETRA_FORM, "users");
			return false;
		}
		
		/* Get the user's profile */
		$s_Query = "SELECT * FROM users WHERE user_id='".$_GET["id"]."'";
		$h_Result = DB::db_Query ($s_Query);
		
		/* If nothing was returned we obviously got a bogus. */
		if (!$h_Result["num_rows"]) {
			Err::Raise ("Invalid user ID!", E_TETRA_FORM, "users");
			return false;
		}
		
		/* Get the data */
		$a_Data = DB::db_Array ($h_Result);
		
		/* Clear the password and e-mail if need be so wannabe template hackers can't have their way with things.
           However, admins have access to all e-mail addresses. */
		$a_Data["user_password"] = "";
		$a_Data["user_ip"] = "";
		$a_Data["user_actid"] = "";
		if ($a_Data["user_showemail"] = 0 && $a_User["user_rank"] != 3)
			$a_Data["user_email"] = "N/A";
		
		/* Format any dates there needs to be formatted */
		$a_Data["user_bdate"] = @date ("m-d-Y", $a_Data["user_bdate"]);
		$a_Data["user_joined"] = @date ("m-d-Y", $a_Data["user_joined"]);
		$a_Data["user_lastlogin"] = make_date ($a_Data["user_lastlogin"]);
		$a_Data["user_http"] = htmlspecialchars ($a_Data["user_http"]);
		
		/* Export everything to the template and display it */
		Tpl::Assign ("u_info", $a_Data);
		Tpl::Display ("users_profile_head.tpl");
		
		/* Run through each module and see if they have a user profile template */
		foreach ($a_Modules as $s_Key=>$s_Value) {
			
			/* If there is a template, display it */
			if ($s_Value->profile_template)
				$s_Value->TetraHandler (T_REQUEST_PROFILE, $a_Data);
			
		}
		
		/* Display the footer */
		Tpl::Display ("users_profile_foot.tpl");
		
		/* If the user is an admin, display the admin stuffs */
		if ($a_User["user_rank"] == 3)
			Tpl::Display ("users_admin.tpl");

	}

	/*****************************************
	* Function:   EditProfile                *
	* Parameters: none                       *
	* Description: Prepares the edit profile *
	*              form                      *
	*****************************************/
	function EditProfile ()
	{

		/* Get the user and modules arrays */
		global $a_User, $a_Modules;
		
		/* Make sure the user isn't guest */
		if ($a_User["user_id"] == 1) {
			Err::Raise ("You must be logged in to edit your profile.", E_TETRA_USER, "user");
			return false;
		}
		
		/* Set up the birthday fields */
		Tpl::Assign ("b_month", intval (date ("m", $a_User["user_bdate"])));
		Tpl::Assign ("b_day", intval (date ("d", $a_User["user_bdate"])));
		Tpl::Assign ("b_year", intval (date ("Y", $a_User["user_bdate"])));
		
		/* Scan for installed themes */
		
		/* Show the user settings template */
		Tpl::Header ("Edit Profile");
		Tpl::Display ("users_edit_profile_head.tpl");
		
		/* Loop through each module and if it is configured for displaying a settings form have it display it */
		foreach ($a_Modules as $s_Key=>$s_Value) {
		
			/* Check to see if the module has a settings form */
			if ($s_Value->module_settings)
				/* Tell it to display its form */
				$s_Value->TetraHandler (T_REQUEST_SETTINGS_FORM, 0);
		
		}
		
		/* Show the footer (and close the form tag) */
		Tpl::Display ("users_edit_profile_foot.tpl");
		Tpl::Footer ();

	}

	/*****************************************
	* Function:   UpdateProfile              *
	* Parameters: none                       *
	* Description: Updates a user's profile  *
	*****************************************/
	function UpdateProfile ()
	{

		/* Get the user and modules array */
		global $a_User, $a_Modules;
		
		/* Verify the password */
		if (md5 ($_POST["pass"]) != $a_User["user_pass"]) {
			Err::Raise ("Your password is incorrect!", E_TETRA_USER, "users");
			$_GET["action"] = "edit_profile";
			return false;
		}
		
		/* Now one thing that's done different here than in previous versions of Tetra is that the user
		   array itself is modified. These changes are then created into the UPDATE SQL statement. This
		   saves a lot of pain and trouble writing out the whole thing and allows updates done in other
		   modules to be much, much easier. */
		
		/* If the password was changed, make sure they match then update */
		if ($_POST["new_pass1"] && $_POST["new_pass1"] == $_POST["new_pass2"])
			$a_User["user_pass"] = md5 ($_POST["new_pass1"]);
		elseif ($_POST["new_pass1"] && $_POST["new_pass1"] != $_POST["new_pass2"]) {
			Err::Raise ("Could not change your password because they didn't match.", E_TETRA_USER, "users");
			$_GET["action"] = "edit_profile";
			return false;
		}
		
		/* If the user changed their e-mail we're going to need to generate a new activation ID and send
		   out a new e-mail */
		if ($_POST["email"] != $a_User["user_email"]) {
		
			/* Set the action for the normal display to logout the user out */
			$_GET["action"] = "logout";
		
			/* Generate a user activation ID */
			$s_ActID = md5 ((rand () % 888888) + 111111);
			$i_Start = (rand () % 5);
			$i_Length = (rand () % 20) + 5;
			$a_User["user_actid"] = substr ($s_ActID, $i_Start, $i_Length);
			
			/* Set the e-mail and deactivate the user's account */
			$a_User["user_email"] = addslashes ($_POST["email"]);
			$a_User["user_activated"] = 0;
			
			/* Send the user an e-mail with the activation ID */
			@mail ($_POST["email"], $a_HostInfo["SiteName"]." Activation", "Click the link below to activate your account and make all features of our site available to you:\r\n".$a_HostInfo["HostURL"]."./index.php?preprocess=users&action=activate&user=$i_ID&actid=$s_ActID\r\n--\r\n".$a_HostInfo["Signature"], "From: ".$a_HostInfo["EMail"]);
			
			/* Kill the user's session */
			$this->Logout ();
		
		}
		
		/* Update the "make e-mail visible" setting */
		if ($_POST["show"] == "on")
			$a_User["user_showemail"] = 1;
		else
			$a_User["user_showemail"] = 0;
			
		/* Update the various IM names, website address, real name, and birth date */
		$a_User["user_msn"] = addslashes ($_POST["msn"]);
		$a_User["user_aim"] = addslashes ($_POST["aim"]);
		$a_User["user_icq"] = addslashes ($_POST["icq"]);
		$a_User["user_irc"] = addslashes ($_POST["irc"]);
		$a_User["user_http"] = addslashes ($_POST["http"]);
		$a_User["user_rname"] = addslashes ($_POST["rname"]);
		$a_User["user_bdate"] = mktime (0,0,0, $_POST["b_month"], $_POST["b_day"], $_POST["b_year"]);
		$a_User["user_from"] = addslashes ($_POST["from"]);
		$a_User["user_tf"] = addslashes ($_POST["date"]);
		
		/* Now go through the various modules that have settings to update and let them do their stuff */
		foreach ($a_Modules as $s_Key=>$s_Value) {
			
			/* If this module has user settings let it do it's stuff */
			if ($s_Value->module_settings)
				$s_Value->TetraHandler (T_REQUEST_SETTINGS_UPDATE, 0);
			
		}
		
		/* Now we make the query */
		$s_Query = "UPDATE users SET ";
		foreach ($a_User as $s_Key=>$s_Val) {
		
			/* Make sure we only get the keys that have keys, i.e. not array index numbers */
			if (!is_numeric ($s_Key))
				$s_Query .= $s_Key."='".$s_Val."', ";
		
		}
		
		/* Finish the query and execute it */
		$s_Query = substr ($s_Query, 0, strlen ($s_Query) - 2)." WHERE user_id='".$a_User["user_id"]."'";
		DB::db_Query ($s_Query);
		
	}

	/*****************************************
	* Function:   Logout                     *
	* Parameters: none                       *
	* Description: Logs the user out         *
	*****************************************/
	function Logout ()
	{

		/* Get the session array and object and the cache object */
		global $_SESS;

		/* Update the last login thing */
		$s_Query = "UPDATE users SET user_lastlogin=".time ()." WHERE user_id=$_SESS[sess_uid]";
		DB::db_Query ($s_Query);

		/* Log the user out */
		$_SESS["sess_uid"] = 1;

		/* Update the session */
		Session::Update ($_SESS["sess_key"], $Cache);
	}

	/*****************************************
	* Function:   Register                   *
	* Parameters: none                       *
	* Description: Register a user           *
	*****************************************/
	function Register ()
	{

		/* Get the user array */
		global $a_User;
		
		/* Make sure the user isn't already logged in */
		if ($a_User["user_id"] != 1) {
			Err::Raise ("You cannot register another user name!", E_TETRA_USER, "users", "User: ".$a_User["user_name"]);
			return false;
		}
		
		/* Display the user registration form */
		Tpl::Assign ("username", $_POST["username"]);
		Tpl::Assign ("email", $_POST["email"]);
		Tpl::Display ("users_register.tpl");

	}

	/*****************************************
	* Function:   AddUser                    *
	* Parameters: none                       *
	* Description: Adds a user's credenitals *
	*              into the database         *
	*****************************************/	
	function AddUser ()
	{
	
		/* Get the user and site info array */
		global $a_User, $a_HostInfo;

		/* A list of invalid username characters */
		$a_InvalidChars = array ("<", ">", "/", "\\". "&", "\"", "@", " ", "\'", "|");
		
		/* Make sure there's no user logged in */
		if ($a_User["user_id"] != 1) {
			Err::Raise ("You cannot register another user name!", E_TETRA_USER, "user", "User: ".$a_User["user_name"]);
			return false;
		}
		
		/* Make sure all the fields were provided */
		if (!$_POST["username"] || !$_POST["pass1"] || !$_POST["pass2"] || !$_POST["email"]) {
			Err::Raise ("Please fill out all fields.", E_TETRA_FORM, "users");
			$this->Register ();
			return false;
		}

		/* Make sure the passwords match */
		if ($_POST["pass1"] != $_POST["pass2"]) {
			Err::Raise ("The passwords do not match.", E_TETRA_FORM, "users");
			$this->Register ();
			return false;
		}
		
		/* Make sure this e-amil isn't in the db */
		if (DB::db_Count ("users", array ("user_email"=>$_POST["email"])) > 0) {
			Err::Raise ("Our records show that you have already registered. You cannot register more than one user name. The administrators have been notified.", E_TETRA_USER, "users", "E-Mail: ".$_POST["email"]);
			return false;
		}
		
		/* Check to see if there is anybody with this username */
		if (DB::db_Count ("users", array ("user_name"=>$_POST["username"])) > 0) {
			Err::Raise ("The username \"".$_POST["username"]."\" is already taken.", E_TETRA_FORM, "user");
			$this->Register ();
			return false;
		}
		
		/* Check to see if there is a banned user with this IP */
		if (DB::db_Count ("users", array ("user_banned"=>1, "user_ip"=>gethostbyaddr (getenv ("REMOTE_ADDR")))) > 0) {
			Err::Raise ("Our records show that you have been banned previously. You are not allowed to reregister. The administrators have been notified.", E_TETRA_USER, "users");
			return false;
		}
		
		/* Run a check to make sure there aren't any illegal characters in the username */
		for ($i = 0; $i < sizeof ($a_InvalidChars); $i++) {
		
			/* Check to see if this character is in the user's name */
			if (substr_count ($_POST["username"], $a_InvalidChars) > 0) {
				Err::Raise ("You have an illegal character in your user name. The following characters are not allowed: (space) &lt;&gt;/ \\ \" \' & @<br>Please remove these characters and try again.", E_TETRA_USER, "users");
				$this->Register ();
				return false;
			}
		
		}
		
		/* Generate a user activation ID */
		$s_ActID = md5 ((rand () % 888888) + 111111);
		$i_Start = (rand () % 5);
		$i_Length = (rand () % 20) + 5;
		$s_ActID = substr ($s_ActID, $i_Start, $i_Length);
		
		/* We're done with the checks, now lump everything into the database */
		$s_Query = "INSERT INTO users (user_name, user_pass, user_email, user_actid, user_joined, user_theme, user_style, user_rank, user_activated) VALUES ('".$_POST["username"]."', '".md5 ($_POST["pass1"])."', '".$_POST["email"]."', '$s_ActID', '".time ()."', '".$a_User["user_theme"]."', '".$a_User["user_style"]."', '1', '0')";
		$i_ID = DB::db_Query ($s_Query);
		
		/* Copy the default layout to this user */
		Layout::CopyLayout (1, $i_ID);
		
		/* E-Mail the user the link to the activation page */
		@mail ($_POST["email"], $a_HostInfo["SiteName"]." Activation", "Click the link below to activate your account and make all features of our site available to you:\r\n".$a_HostInfo["HostURL"]."./index.php?preprocess=users&action=activate&user=$i_ID&actid=$s_ActID\r\n--\r\n".$a_HostInfo["Signature"], "From: ".$a_HostInfo["EMail"]);
		
		/* Display success */
		Err::Message ("Registration Complete", "Congratulations! Registration for your ".$a_HostInfo["SiteName"]." user account is complete. You should recieve an e-mail with a link to activate your account within a few moments.");
	
	}
	
	/*****************************************
	* Function:   Activate                   *
	* Parameters: none                       *
	* Description: Activates a user account  *
	*****************************************/
	function Activate ()
	{

		/* Get the user and session array */
		global $a_User, $_SESS;
		
		/* Make sure the user isn't logged in */
		if ($a_User["user_id"] > 1) {
			Err::Raise ("You cannot register another user name!", E_TETRA_USER, "user", "User: ".$a_User["user_name"]);
			return false;
		}
		
		/* Make sure all the fields were filled */
		if (!is_numeric ($_GET["user"]) || !$_GET["actid"]) {
			Err::Raise ("Invalid parameters were passed. Cannot activate your account.", E_TETRA_FORM, "users");
			return false;
		}
		
		/* Make sure the activation ID is correct */
		if (DB::db_Count ("users", array ("user_actid"=>$_GET["actid"], "user_id"=>$_GET["user"], "user_activated"=>0)) == 0) {
			Err::Raise ("That is the wrong user/activation ID or your account has already been activated. If you typed in the URL check for any typos.", E_TETRA_FORM, "users");
			return false;
		}
		
		/* Now that everything is verified activate the account */
		$s_Query = "UPDATE users SET user_activated='1', user_rank='1' WHERE user_id='".$_GET["user"]."'";
		DB::db_Query ($s_Query);
		
		/* Get the user's credentials and login */
		$s_Query = "SELECT * FROM users WHERE user_id='".$_GET["user"]."'";
		$a_Data = DB::db_Array (DB::db_Query ($s_Query));

		/* Log the user in */
		$_SESS["sess_uid"] = $_GET["user"];

		/* Update the session */
		Session::Update ($_SESS["sess_key"]);

	}

	/*****************************************
	* Function:   Stats                      *
	* Parameters: none                       *
	* Description: Displays stats on user    *
	*              user visits, who's online *
	*              etc.                      *
	*****************************************/
	function Stats ()
	{

		/* Get the number of guests */
		$i_NumGuests = DB::db_Count ("sessions", array ("sess_uid"=>"1"));
	
		/* Grab the list of sessions and usernames */
		$s_Query = "SELECT DISTINCT u.user_id, u.user_name, u.user_lastlogin FROM sessions AS s INNER JOIN users AS u ON u.user_id=s.sess_uid WHERE s.sess_uid > '1' AND u.user_lastlogin > '".(time () - 3600)."'";
		$h_Result = DB::db_Query ($s_Query);
		
		/* Show the header */
		Tpl::Assign ("num_guests", $i_NumGuests);
		Tpl::Display ("users_stats_head.tpl");
		
		/* If there are no users, say so */
		if (!$h_Result["num_rows"]) {
			Tpl::Assign ("no_users", 1);
			Tpl::Display ("users_stats_item.tpl");
		}
		else {
			/* Loop through all the users online and display them */
			for ($i = 0; $i < $h_Result["num_rows"]; $i++) {
				
				/* Get the data and display it */
				$a_Data = DB::db_Array ($h_Result);
				Tpl::Assign ("users", $a_Data);
				Tpl::Display ("users_stats_item.tpl");
				
			}
		}
		
		/* Display the footer */
		Tpl::Display ("users_stats_foot.tpl");

	}

	/*****************************************
	* Function:   Userlist                   *
	* Parameters: $s_Let - Letter to define  *
	*                      what users to     *
	*                      display           *
	* Description: Displays a list of users  *
	*****************************************/
	function Userlist ($s_Let)
	{

		

	}

	/*****************************************
	* Function:   LostPass                   *
	*****************************************/
	function LostPass ()
	{

		/* Get the user array */
		global $a_User, $a_HostInfo;
		
		/* If the user is logged in raise an error */
		if ($a_User["user_id"] > 1) {
			Err::Raise ("Please logout before performing this operation.", E_TETRA_USER, "users");
			return false;			
		}
		
		/* Figure out what stage we're at */
		switch ($_GET["stage"]) {
		case "1":
			/* Validate the username */
			$s_Query = "SELECT user_id, user_actid FROM users WHERE user_name='".$_POST["user"]."'";
			$a_Data = DB::db_Array (DB::db_Query ($s_Query));
			
			/* If no ID was returned it's a bogus username */
			if (!$a_Data["user_id"]) {
				Err::Raise ("That is not a registered username.", E_TETRA_GENERAL, "users");
				return false;
			}
			
			/* Send an e-mail to the user */
			@email ("Password request", "Somebody has requested that your password be changed. If it was not you please contact an administrator immediately and include the following in your message: IP: ".$a_User["user_ip"].".\nIf you were the one who requested the change please follow the link below to change your password.\n\t".$a_HostInfo["HostURL"]."/index.php?main=users&action=password&stage=2&actid=".$a_Data["user_actid"]."&uid=".$a_Data["user_id"]."\n\n".$a_HostInfo["Signature"]);
			break;
		case "2":
			/* Verify the activation and user IDs */
			if (!DB::db_Count ("users", array ("user_actid"=>$_GET["actid"], "user_id"=>$_GET["uid"]))) {
				Err::Raise ("The activation ID does not match the user id. You cannot change this user's password!", E_TETRA_USER, "users");
				return false;
			}
			
			/* Now that everything is verified show the password form */
			Tpl::Assign ("u_info", array ("user_actid"=>$_GET["actid"], "user_id"=>$_GET["uid"]));
			Tpl::Display ("users_change_pass.tpl");
			break;
		case "3":
			/* Verify the activation and user IDs */
			if (!DB::db_Count ("users", array ("user_actid"=>$_GET["actid"], "user_id"=>$_GET["uid"]))) {
				Err::Raise ("The activation ID does not match the user id. You cannot change this user's password!", E_TETRA_USER, "users");
				return false;
			}
			
			/* Make sure that a password was entered */
			if (!$_POST["pass"]) {
				Err::Raise ("Please enter a password!", E_TETRA_FORM, "users");
				return false;
			}
			
			/* Now that that's done change the password */
			$s_Query = "UPDATE users SET user_pass=MD5('".$_POST["pass"]."') WHERE user_id='".$_GET["uid"]."'";
			DB::db_Query ($s_Query);
			
			/* Let the user have a sigh of relief */
			Err::Message ("Password Changed", "Your password has been successfully changed. You can now log in.");
			break;
		default:
			/* Display the form */
			Tpl::Display ("mb_pass_form.tpl");
			break;
		}
	}

	/*****************************************
	* Function:   Admin                      *
	* Parameters: none                       *
	* Description: Admin tools               *
	*****************************************/
	function Admin ()
	{

		/* Get the user array */
		global $a_User;
		
		/* Make sure the user is an admin before continuing */
		if ($a_User["user_rank"] < 3) {
			Err::Raise ("You are not allowed to tamper with user settings. The admins have been notified,", E_TETRA_USER, "users", "Username: ".$a_User["user_name"]);
			return false;
		}
		
		/* Make sure the id is valid */
		if (!$_GET["id"] || !is_numeric ($_GET["id"])) {
			Err::Raise ("Invalid user ID!", E_TETRA_FORM, "users");
			return false;
		}
		
		/* See what we're doing */
		switch ($_GET["admin"]) {
		case "ban":
			/* Update the users banned status */
			$s_Query = "UPDATE users SET user_banned='".$_GET["ban"]."' WHERE user_id='".$_GET["id"]."'";
			DB::db_Query ($s_Query);
			
			/* Delete any sessions he has going */
			$s_Query = "DELETE FROM sessions WHERE sess_uid='".$_GET["id"]."'";
			DB::db_Query ($s_Query);
			break;
		case "rank":
			/* Update the user's rank */
			$s_Query = "UPDATE users SET user_rank='".$_GET["rank"]."' WHERE user_id='".$_GET["id"]."'";
			DB::db_Query ($s_Query);
			break;
		}
		
	}
	
}

?>