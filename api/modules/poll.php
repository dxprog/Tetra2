<?php

/********************************************
* Tetra                                     *
* Copyright (c) 2004 Matt "dxprog" Hackmann *
* More copyright info can be found in       *
* license.txt                               *
********************************************/

/********************************************
* Tetra Poll                                *
* Version 0.1                               *
********************************************/

$class_name = "Poll";

class Poll {

	/* Module info */
	var $module_ver = 1.0;
	var $module_name = "Poll";
	var $module_sidebars = true;

	/*******************************************
	* Function:   HandleRequest                *
	* Parameters: $s_Options - says what needs *
	*                          to be done      *
	* Description: Main module handler         *
	*******************************************/
	function TetraHandler ($i_Request, $s_Parameters)
	{
		switch ($i_Request) {
		case T_REQUEST_SIDEBARS:	
				return array ("Poll"=>"poll|default");
		}
	}
	
	function HandleRequest ($s_Options)
	{
	
		/* Get the user array and templating object */
		global $a_User, $Templating;

		/* See if any options were passed */
		switch ($s_Options) {
		case "vote":
			$this->Vote ();
			break;
		case "poll_form":
			if ($a_User["user_rank"] == 3)
				Tpl::Display ("poll_form.tpl");
			break;
		case "create_poll":
			$this->CreatePoll ();
			break;
		default:
			$this->ShowResults ();
			break;
		}
	}

	/*******************************************
	* Function:   ShowResults                  *
	* Description: Displays the selected poll  *
	*******************************************/
	function ShowResults ($i_ID = 0)
	{
	
		/* Get the user array */
		global $a_User;
	
		/* If no poll was selected use the newest */
		if (!$i_ID) {
			$s_Query = "SELECT poll_id FROM polls ORDER BY poll_id DESC LIMIT 1";
			$a_Temp = DB::db_Array (DB::db_Query ($s_Query));
			$i_ID = $a_Temp["poll_id"];
		}
		
		/* If the user hasn't voted show the vote form */
		if (!$a_User["user_poll_voted"] && $_COOKIE["t_poll_voted"] != $i_ID) {
			$this->VoteForm ();
			return false;
		}
		
		/* Get the poll info */
		$s_Query = "SELECT * FROM polls WHERE poll_id='$i_ID'";
		$a_Data = DB::db_Array (DB::db_Query ($s_Query));
		
		/* Get the amount of votes */
		$s_Query = "SELECT sum(vote_count) AS total FROM poll_votes WHERE vote_parent='$i_ID'";
		$a_Temp = DB::db_Array (DB::db_Query ($s_Query));
		Tpl::Assign ("total_votes", $a_Temp["total"]);

		/* Export that and display the header */
		Tpl::Assign ("poll", $a_Data);
		Tpl::Display ("poll_result_head.tpl");
		
		/* Get the poll items */
		$s_Query = "SELECT * FROM poll_votes WHERE vote_parent='$i_ID'";
		$h_Result = DB::db_Query ($s_Query);
		
		/* Display the poll items */
		for ($i = 0; $i < $h_Result["num_rows"]; $i++) {
		
			/* Get the info and display the item */
			$a_Data = DB::db_Array ($h_Result);
			Tpl::Assign ("poll_item", $a_Data);
			Tpl::Display ("poll_result_item.tpl");
		
		}
		
		/* Display the footer */
		Tpl::Display ("poll_result_foot.tpl");
	
	}
	
	/*******************************************
	* Function:   VoteForm                     *
	* Description: Displays the vote form poll *
	*******************************************/
	function VoteForm ()
	{
	
		/* Get the user array */
		global $a_User;
		
		/* Get the info for the latest poll */
		$s_Query = "SELECT * FROM polls ORDER BY poll_id DESC LIMIT 1";
		$a_Temp = DB::db_Array (DB::db_Query ($s_Query));
		$i_ID = $a_Temp["poll_id"];
		
		/* Get all the poll items */
		$s_Query = "SELECT * FROM poll_votes WHERE vote_parent='$i_ID'";
		$h_Result = DB::db_Query ($s_Query);
		
		/* Display the header */
		Tpl::Assign ("poll", $a_Temp);
		Tpl::Display ("poll_vote_head.tpl");
		
		/* Run through the items and display them */
		for ($i = 0; $i < $h_Result["num_rows"]; $i++) {
		
			/* Get the data and display the item */
			$a_Data = DB::db_Array ($h_Result);
			Tpl::Assign ("poll_item", $a_Data);
			Tpl::Display ("poll_vote_item.tpl");
		
		}
	
		/* Display the footer */
		Tpl::Display ("poll_vote_foot.tpl");
	
	}

	/*******************************************
	* Function:   Vote                         *
	* Description: Registers a user's vote     *
	*******************************************/
	function Vote ()
	{
	
		/* Get the user array */
		global $a_User;
		
		/* Get ID for the latest poll */
		$s_Query = "SELECT poll_id FROM polls ORDER BY poll_id DESC LIMIT 1";
		$a_Temp = DB::db_Array (DB::db_Query ($s_Query));
		$i_ID = $a_Temp["poll_id"];
		
		/* See if the user has already voted */
		if ($a_User["user_poll_voted"] || $_COOKIE["t_poll_voted"] == $i_ID) {
			Err::Raise ("You have already voted on this poll.", E_TETRA_USER, "poll");
			return false;
		}
		
		/* Make sure the item selected is a valid vote item */
		if (!DB::db_Count ("poll_votes", array ("vote_parent"=>$i_ID, "vote_id"=>$_POST["poll"]))) {
			Err::Raise ("That is not a valid item!", E_TETRA_USER, "poll", "User: ".$a_User["user_name"]);
			return false;
		}
	
		/* Place the vote */
		$s_Query = "UPDATE poll_votes SET vote_count=vote_count+1 WHERE vote_id='".$_POST["poll"]."'";
		DB::db_Query ($s_Query);
		$a_User["user_poll_voted"] = 1;
		
		/* If this is a registered user update their voted status */
		if ($a_User["user_id"] > 1) {
			$s_Query = "UPDATE users SET user_poll_voted='1' WHERE user_id='".$a_User["user_id"]."'";
			DB::db_Query ($s_Query);
		}
		else {
			/* Set a cookie saying that the user has voted for this particular poll. Expiration is set at a year. 
			   Also set the user property so things don't get screwed up later on. */
			setcookie ("t_poll_voted", $i_ID, 31536000);
		}
		
		/* Clear the action command so it won't try to vote twice */
		$_GET["action"] = "";
	
	}
	
	/*******************************************
	* Function:   CreatePoll                   *
	* Description: Read the function title :-P *
	*******************************************/
	function CreatePoll ()
	{
	
		/* Get ye olde array */
		global $a_User;
		
		/* Make sure the user is an admin */
		if ($a_User["user_rank"] != 3) {
			Err::Raise ("Only administrators can perform this function!", E_TETRA_USER, "poll");
			return false;
		}
		
		/* See how many poll items were filled */
		for ($i = 1; $i < 11; $i++) {
			if ($_POST["item$i"])
				$i_Items++;
		}
		
		/* Make sure we have a poll caption and at least to poll items */
		if (!$_POST["title"] || $i_Items < 2) {
			Err::Raise ("You must supply a poll caption and have at least two poll options.", E_TETRA_FORM, "poll");
			return false;
		}
		
		/* Put in the main poll entry */
		$s_Query = "INSERT INTO polls (poll_question, poll_date) VALUES ('".addslashes ($_POST["title"])."', '".time ()."')";
		$i_ID = DB::db_Query ($s_Query);
		
		/* Now insert each item */
		for ($i = 1; $i < 11; $i++) {
		
			/* Make sure the caption isn't blank before continuing */
			if ($_POST["item$i"]) {
			
				/* Insert the item */
				$s_Query = "INSERT INTO poll_votes (vote_caption, vote_parent) VALUES ('".addslashes ($_POST["item$i"])."', '$i_ID')";
				DB::db_Query ($s_Query);
			
			}
		
		}
		
		/* Update the voted status of all the users */
		$s_Query = "UPDATE users SET user_poll_voted=0";
		DB::db_Query ($s_Query);
	
		/* Show the vote form */
		Err::Message ("Poll Created", "Your poll was successfully created.");
		$this->VoteForm ();
	
	}
	
}

?>