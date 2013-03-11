<?php

/***************************************************
* Tetra                                            *
* Copyright (c) 2004-2005 Matt "dxprog" Hackmann   *
* More copyright info can be found in              *
* license.txt                                      *
***************************************************/

/********************************************
* Tetra MessageBoard                        *
* Version 2.0                               *
********************************************/

/* Include the MB code module and the news module */
@include ("./api/modules/mbcode.php");
@include ("./api/modules/news.php");

/* Register the news class */
if (MB_NEWS)
	$a_Modules["news"] = new News ();

class MB {


	/******************************************************************************
	*                               MODULE SETTINGS                               *
	******************************************************************************/	
	var $module_name = "Message Board";
	var $module_nav_items = true;
	var $module_settings = true;
	var $module_user_nav_items = true;
	var $module_sidebars = true;
	var $module_title = true;
	var $profile_template = true;

	/******************************************************************************
	*                             REQUIRED FUNCTIONS                              *
	******************************************************************************/

	/*************************************************
	* Function: TetraHandler                         *
	* Description: Handles Tetra requests            *
	*************************************************/
	function TetraHandler ($i_Request, $s_Parameters)
	{
	
		/* Get the user array */
		global $a_User;
	
		/* See what needs to be done */
		switch ($i_Request) {
		case T_REQUEST_TITLE:
			/* See what page we're dealing with so we can give an accurate title back */
			switch ($s_Parameters) {
			case "view_forum":
				$a_Forum = $this->GetForum ($_GET["id"]);
				return "MessageBoard - Viewing Forum \"".$a_Forum["forum_name"]."\"";
			case "topic_form":
			case "create_topic":
				return "Create Topic";
			case "view_thread":
				$a_Topic = $this->GetTopic ($_GET["id"]);
				return "MessageBoard - Viewing Thread \"".$a_Topic["topic_title"]."\"";
			default:
				return "MessageBoard - Forum Overview";
			}
			
		case T_REQUEST_SETTINGS_FORM:
			/* Display the settings form */
			Tpl::Assign ("signature", explode ("\n", $a_User["user_mb_signature"]));
			Tpl::Display ("mb_settings_form.tpl");
			break;
		case T_REQUEST_SETTINGS_UPDATE:
			
			/* Get the signature and title set up */
			$a_User["user_mb_signature"] = addslashes ($_POST["signature1"]."\n".$_POST["signature2"]);
			$a_User["user_mb_title"] = addslashes ($_POST["title"]);
			
			/* See if the user uploaded a new avatar */
			if ($_FILES["avatar"]["error"] == 0) {
				/* Create the avatar */
				$a_User["user_mb_avatar"] = $this->CreateAvatar ($_FILES["avatar"]["tmp_name"], substr ($_FILES["avatar"]["name"], strlen ($_FILES["avatar"]["name"]) - 3, 3));
			}
			
			break;
		case T_REQUEST_NAV_ITEMS:
			return array ("MessageBoard"=>"./index.php?main=mb");
		case T_REQUEST_PROFILE:
			
			/* Get the last post by the user */
			$s_Query = "SELECT m.message_id, t.topic_title, t.topic_id, m.message_date FROM mb_messages AS m INNER JOIN mb_topics AS t ON t.topic_id=m.message_parent WHERE message_poster='".$s_Parameters["user_id"]."' ORDER BY message_id DESC";
			$h_Result = DB::db_Query ($s_Query);
			
			/* If there are no posts set a flag for that */
			if (!$h_Result["num_rows"])
				Tpl::Assign ("no_post", 1);
			else {
				/* Get the post info */
				$a_Data = DB::db_Array ($h_Result);
				
				/* Get the page the post is on */
				$a_Data["post_page"] = $this->GetPageFromMessage ($_Data["message_id"]);
				
				/* Export to the template */
				Tpl::Assign ("post", $a_Data);
				
			}
			
			/* Fix up the signature */
			$s_Parameters["user_mb_signature"] = nl2br ($s_Parameters["user_mb_signature"]);
			
			/* Display the template */
			Tpl::Assign ("u_info", $s_Parameters);
			Tpl::Display ("users_mb_profile.tpl");
			break;
			
		case T_REQUEST_NAV_USER:
			
			/* Create a date stamp for one week ago */
			$i_LastWeek = mktime (0,0,0, date ("m"), date ("d") - 7, date ("Y"));
			
			/* Get a list of flagged topics */
			$s_Query = "SELECT read_topic FROM mb_read WHERE read_flagged='1' AND read_user='".$a_User["user_id"]."' LIMIT ".$a_User["user_mb_tpp"];
			$h_Result = DB::db_Query ($s_Query);
			
			/* Run through each and check their read status */
			for ($i = 0; $i < $h_Result["num_rows"]; $i++) {
				
				/* Get the info and check it's read status. The second we hit one that _isn't_ read break. */
				$a_Data = DB::db_Array ($h_Result);
				if (!$this->TopicRead ($a_Data["read_topic"])) {
					$s_Flagged = " (new)";
					$i = $h_Result["num_rows"];
				}
				
			}
			
			/* Now check the private threads */
			$s_Query = "SELECT topic_id FROM mb_topics WHERE (topic_from='".$a_User["user_id"]."' OR topic_to='".$a_User["user_id"]."') AND topic_parent='52'";
			$h_Result = DB::db_Query ($s_Query);
			
			for ($i = 0; $i < $h_Result["num_rows"]; $i++) {
			
				/* Do the same as above. Second we hit something unread, jump ship. */
				$a_Data = DB::db_Array ($h_Result);
				if (!$this->TopicRead ($a_Data["topic_id"])) {
					$s_Private = " (new)";
					$i = $h_Result["num_rows"];
				}
			
			}
			
			/* Get that stuff into the array */
			$a_Return = array ("Private Threads$s_Private"=>"mb&action=view_forum&id=52", "Flagged Threads$s_Flagged"=>"mb&action=view_forum&id=51");
			
			/* If the user is an admin show the create section and forum links */
			if ($a_User["user_rank"] == 3) {
				$a_Return["Create Section"] = "mb&action=admin&admin=section_form";
				$a_Return["Create Forum"] = "mb&action=admin&admin=forum_form";
			}
			
			/* Return the array */
			return $a_Return;
		case T_REQUEST_SIDEBARS:
		
			return array ("Recent Posts"=>"mb|recent_posts", "Recent Docs"=>"mb|recent_docs", "Search"=>"mb|search_form");
		
		}
		
	
	}

	/*************************************************
	* Function: HandleRequest                        *
	* Description: Handles page requests             *
	*************************************************/
	function HandleRequest ($s_Page)
	{
	
		/* Get the user array */
		global $a_User;
		
		/* If we're dealing with an admin page make sure the user is an admin */
		if ($_GET["action"] == "admin" && $a_User["user_rank"] < 2) {
			Err::Raise ("You are not authorized to view this page!", E_TETRA_USER, "mb", "Username: ".$a_User["user_name"]);
			return false;
		}
		
		/* Figure out what page we're dealing with */
		switch ($s_Page) {
		case "admin":
			
			/* Figure out what admin page we're dealing with */
			switch ($_GET["admin"]) {
			case "section_form":
				Tpl::Display ("mb_admin_section.tpl");
				break;
			case "createsection":
				$this->CreateSection ();
				break;
			case "forum_form":
				$this->CreateForumForm ();
				break;
			case "createforum":
				$this->CreateForum ();
				break;
			}
			break;
		case "view_forum":
			$this->ViewForum ();
			break;
		case "topic_form":
			$this->CreateTopicForm ();
			break;
		case "create_topic":
			$this->CreateTopic ();
			break;
		case "view_thread":
			$this->ViewThread ();
			break;
		case "post_form":
			$this->PostForm ();
			break;
		case "post":
			$this->PreparePost ();
			break;
		case "edit_form":
			$this->PrepareEdit ();
			break;
		case "edit_post":
			$this->EditPost ();
			break;
		case "settings_form":
			$this->SettingsForm ();
			break;
		case "recent_docs":
			$this->Recent ("docs");
			break;
		case "recent_posts":
			$this->Recent ();
			break;
		case "search_form":
			Tpl::Display ("mb_search_form.tpl");
			break;
		case "search":
			$this->Search ();
			break;
		default:
			$this->ForumOverview ();
			break;
		}
	
	}

	/******************************************************************************
	*                             GENERAL FUNCTIONS                               *
	******************************************************************************/
	function ForumOverview ()
	{
	
		/* Get the user array */
		global $a_User;
		
		/* Get the list of sections that the current user can view */
		$s_Query = "SELECT * FROM mb_forums WHERE forum_parent='0'";
		$h_Result = DB::db_Query ($s_Query);
		
		/* Display the stats bar */
		$this->DisplayStats ();
		
		/* Display the headers */
		Tpl::Display ("mb_main_head.tpl");
		
		/* If we got results, display 'em */
		if ($h_Result["num_rows"] > 0) {
			
			/* Loop through the returned forums and display them */
			for ($i = 0; $i < $h_Result["num_rows"]; $i++) {
				
				/* Get the section info */
				$a_Data = DB::db_Array ($h_Result);

				/* Display the section header */
				Tpl::Assign ("forum", $a_Data);
				Tpl::Display ("mb_main_section.tpl");
				
				/* Get the forums in this section */
				$s_Query = "SELECT * FROM mb_forums WHERE forum_parent='".$a_Data["forum_id"]."' AND forum_viewperms <= '".$a_User["user_rank"]."'";
				$h_Forums = DB::db_Query ($s_Query);
				
				/* Display each forum */
				for ($j = 0; $j < $h_Forums["num_rows"]; $j++) {
					
					/* Get the forum info */
					$a_Data = DB::db_Array ($h_Forums);
					
					/* Get the number of topics in this forum */
					Tpl::Assign ("num_topics", DB::db_Count ("mb_topics", array("topic_parent"=>$a_Data["forum_id"])));
					
					/* Get get the ID of the topic with the latest post*/
					$s_Query = "SELECT t.topic_id FROM mb_topics AS t INNER JOIN mb_messages AS m ON m.message_id=t.topic_lastpost WHERE t.topic_parent='".$a_Data["forum_id"]."' ORDER BY m.message_date DESC LIMIT 1";
					$a_Temp = DB::db_Array (DB::db_Query ($s_Query));
					
					/* Get the topic information and also info last post */
					$a_Topic = $this->GetTopic ($a_Temp["topic_id"]);
					$a_Post = $this->GetBasicMessage ($a_Topic["topic_lastpost"]);
					Tpl::Assign ("n_topic", $a_Topic);
					Tpl::Assign ("n_topic", $a_Post);
					
					/* Display the template */
					Tpl::Assign ("forum", $a_Data);
					Tpl::Display ("mb_main_forum.tpl");
					
				}
				
			}
		}
		else			
			Tpl::Display ("mb_main_no_forums.tpl");
		
		/* Show the footer */
		Tpl::Display ("mb_main_foot.tpl");
	
	}
	
	/**************************************************
	* Function: ViewForum                             *
	* Description: Displays the topics in a forum     *
	**************************************************/	
	function ViewForum ()
	{
	
		/* Get the user array */
		global $a_User;
		
		/* Validate the forum ID */
		if (!$_GET["id"] || !is_numeric ($_GET["id"])) {
			Err::Raise ("An invalid forum ID was passed!", E_TETRA_GENERAL, "mb", "User: ".$a_User["user_name"]."\nID: ".$_GET["id"]);
			return false;
		}
		
		/* Get the forum info if we're not viewing flagged or private threads */
		if ($_GET["id"] < 51) {
			$a_Forum = $this->GetForum ($_GET["id"]);

			/* Check the user permissions */
			if ($a_User["user_rank"] < $a_Forum["forum_viewperms"]) {
				Err::Raise ("You cannot view this forum!", E_TETRA_USER, "mb", "Forum: ".$a_Forum["forum_name"]."\nUser: ".$a_User["user_name"]);
				return false;
			}

			/* If no results were return either the user doesn't have permission to view the forum or the forum doesn't exist */
			if (!$a_Forum) {
				Err::Raise ("The forum requested doesn't exist!", E_TETRA_GENERAL, "mb");
				return false;
			}
		}
		else {
			
			/* Don't allow guest beyond this point */
			if ($a_User["user_id"] == 1) {
				Err::Raise ("You cannot view these forums.", E_TETRA_USER, "mb");
				return false;
			}
		
			/* Fabricate some forum info */
			if ($_GET["id"] == 51)
				$a_Forum["forum_name"] = "Flagged Threads";
			else
				$a_Forum["forum_name"] = "Private Threads";
		}

		/* Get the number of topics in this forum */
		if ($_GET["id"] < 51)
			$i_NumTopics = DB::db_Count ("mb_topics", array ("topic_parent"=>$_GET["id"]));
		elseif ($_GET["id"] == 51)
			$i_NumTopics = DB::db_Count ("mb_read", array ("read_user"=>$a_User["user_id"], "read_flagged"=>"1"));
		elseif ($_GET["id"] == 52) {
			$s_Query = "SELECT count(*) AS value FROM mb_topics WHERE topic_from='".$a_User["user_id"]."' OR topic_to='".$a_User["user_id"]."' AND topic_parent='52'";
			$a_Temp = DB::db_Array (DB::db_Query ($s_Query));
			$i_NumTopics = $a_Temp["value"];
		}
					
		
		/* Figure up the number of pages */
		$i_NumPages = ceil ($i_NumTopics / $a_User["user_mb_tpp"]);
		
		/* If a different page was requested rework the db offset */
		if (is_numeric ($_GET["page"]) && $_GET["page"] <= $i_NumPages) {
			$i_Page = $_GET["page"];
			$i_Offset = ($_GET["page"] - 1) * $a_User["user_mb_tpp"];
		}
		else {
			$i_Page = 1;
			$i_Offset = 0;
		}
		
		/* Display the stats and forum view header */
		$this->DisplayStats ();
		Tpl::Assign ("forum", $a_Forum);
		Tpl::Assign ("page", array ("current_page"=>$i_Page, "num_pages"=>$i_NumPages));
		Tpl::Header ("Viewing Forum: ".$a_Forum["forum_name"]);
		Tpl::Display ("mb_forum_head.tpl");
		
		/* Form the query depending on what kind of board we're viewing */
		if ($_GET["id"] < 51)
			$s_Query = "SELECT t.topic_id FROM mb_topics AS t INNER JOIN mb_messages AS m ON m.message_id=t.topic_lastpost WHERE topic_parent='".$a_Forum["forum_id"]."' ORDER BY t.topic_sticky DESC, m.message_date DESC LIMIT $i_Offset, ".$a_User["user_mb_tpp"];
		elseif ($_GET["id"] == 51)
			$s_Query = "SELECT t.topic_id FROM mb_read AS r INNER JOIN mb_topics AS t ON t.topic_id=r.read_topic WHERE r.read_user='".$a_User["user_id"]."' AND r.read_flagged=1 ORDER BY t.topic_lastpost";
		elseif ($_GET["id"] == 52)
			$s_Query = "SELECT topic_id FROM mb_topics WHERE topic_to='".$a_User["user_id"]."' OR topic_from='".$a_User["user_id"]."' AND topic_parent='52'";

		/* Execute the query */
		$h_Result = DB::db_Query ($s_Query);
		
		/* If topics were returned display them */
		if ($h_Result["num_rows"] > 0) {
			
			/* Display each thread */
			for ($i = 0; $i < $h_Result["num_rows"]; $i++) {
			
				/* Get the topic info */
				$a_Data = DB::db_Array ($h_Result);
				$a_Data = $this->GetTopic ($a_Data["topic_id"]);

				/* Get the first and last messages */
				Tpl::Assign ("f_post", $this->GetBasicMessage ($a_Data["topic_firstpost"]));
				Tpl::Assign ("l_post", $this->GetBasicMessage ($a_Data["topic_lastpost"]));

				/* Display the topic */
				Tpl::Assign ("topic", $a_Data);
				Tpl::Display ("mb_forum_item.tpl");
				
			}
			
		}
		else
			Tpl::Display ("mb_forum_none.tpl");
			
		/* Display the footers */
		Tpl::Display ("mb_forum_foot.tpl");
		Tpl::Footer ();
	
	}

	/**************************************************
	* Function: CreateTopicForm                       *
	* Description: Sets up the new topic form         *
	**************************************************/
	function CreateTopicForm ()
	{
	
		/* Get the user array */
		global $a_User;
		
		/* Validate the forum ID */
		if (!$_GET["id"] || !is_numeric ($_GET["id"])) {
			Err::Raise ("The forum ID passed is invalid!", E_TETRA_FORM, "mb", "ID: ".$_GET["id"]."\nUser: ".$a_User["user_name"]);
			return false;
		}
		
		/* Get the forum info */
		$a_Forum = $this->GetForum ($_GET["id"]);
		
		/* Check to see if the user has permission to create topics in this forum */
		if ($a_User["user_rank"] < $a_Forum["forum_topicperms"] || ($a_User["user_id"] == 1 && $_GET["id"] > 50)) {
			Err::Raise ("You cannot create topics in this forum!", E_TETRA_USER, "mb", "Forum: ".$a_Forum["forum_name"]."\nUser: ".$a_User["user_name"]);
			return false;
		}
		
		/* Show the form */
		Tpl::Header ("Create New Topic");
		Tpl::Assign ("to", $_GET["to"]);
		Tpl::Assign ("id", $_GET["id"]);
		Tpl::Display ("mb_post_topic.tpl");
		Tpl::Footer ();
		
	
	}

	/**************************************************
	* Function: CreateTopic                           *
	* Description: Creates a topic                    *
	**************************************************/
	function CreateTopic ()
	{
	
		/* Get the user array */
		global $a_User;
		
		/* Make sure the forum ID is valid */
		if (!$_GET["id"] || !is_numeric ($_GET["id"])) {
			Err::Raise ("The forum ID passed is invalid!", E_TETRA_FORM, "mb", "ID: ".$_GET["id"]."\nUser: ".$a_User["user_name"]);
			return false;
		}
		
		/* Get the forum info */
		$a_Forum = $this->GetForum ($_GET["id"]);
		
		/* If this is a section, stop now */
		if (!$a_Forum["forum_parent"]) {
			Err::Raise ("Invalid forum ID!", E_TETRA_FORM, "mb");
			return false;
		}
		
		/* Make sure the user has permission to create topics in this forum */
		if ($a_User["user_rank"] < $a_Forum["forum_topicperms"] || ($a_User["user_id"] == 1 && $_GET["id"] > 50)) {
			Err::Raise ("You cannot create topics in this forum!", E_TETRA_USER, "mb", "Forum: ".$a_Forum["forum_name"]."\nUser: ".$a_User["user_name"]);
			return false;
		}
		
		/* Make sure all the fields were filled */
		if (!$_POST["body"] || !$_POST["title"]) {
			Err::Raise ("Please go back and make sure that the title and body of your message are filled.", E_TETRA_FORM, "mb");
			return false;
		}
		
		/* Escape the title and remove HTML elements */
		$_POST["title"] = addslashes (htmlentities ($_POST["title"]));
		
		/* Check for sticky */
		if ($_POST["sticky"] == "on")
			$b_Sticky = 1;
		else
			$b_Sticky = 0;
		
		/* Check the flag thread thingy */
		if ($_POST["flag"] == "on")
			$b_Flag = 1;
		else
			$b_Flag = 0;
		
		/* Create the topic */
		$s_Query = "INSERT INTO mb_topics (topic_title, topic_sticky, topic_parent, topic_to, topic_from) VALUES ";
		$s_Query .= "('".$_POST["title"]."', '$b_Sticky', '".$_GET["id"]."', '".$_GET["to"]."', '".$a_User["user_id"]."')";
		if (($i_Topic = DB::db_Query ($s_Query))) {
			/* Post the message */
			$i_Message = $this->Post ($_POST["body"], $i_Topic, "", $b_Flag);
			
			/* Now set the first post field */
			$s_Query = "UPDATE mb_topics SET topic_firstpost='$i_Message' WHERE topic_id='$i_Topic'";
			DB::db_Query ($s_Query);
			
			/* Update the read status */
			$this->SetRead ($i_Topic, $i_Message, $b_Flag);
			
		}
		else {
			Err::Message ("Couldn't create topic!", "There was an error creating the topic. Please go back and try again. If you still get an error contact a moderator.");
			return false;
		}
	
	}
	
	/**************************************************
	* Function: ViewThread                            *
	* Description: Displays the messages in a topic   *
	**************************************************/	
	function ViewThread ($i_ID = 0, $s_Order = "ASC", $b_Headers = true)
	{
	
		/* Get the user array */
		global $a_User;
		
		/* If an ID wasn't passed to the function use what's in GET instead */
		if (!$i_ID)
			$i_ID = $_GET["id"];
		
		/* Validate the topic ID */
		if (!$i_ID || !is_numeric ($i_ID)) {
			Err::Raise ("The topic ID passed is invalid!", E_TETRA_FORM, "mb", "ID: ".$i_ID."\nUser: ".$a_User["user_name"]);
			return false;
		}

		/* If we're supposed to be going to the first unread post figure that stuff up */
		if ($a_User["user_id"] > 1 && $_GET["new_post"]) {
			/* Get the ID of the last read message */
			$s_Query = "SELECT read_message FROM mb_read WHERE read_user='".$a_User["user_id"]."' AND read_topic='".$_GET["id"]."'";
			$a_Temp = DB::db_Array (DB::db_Query ($s_Query));

			/* Now get the ID of the message next in line */
			$s_Query = "SELECT message_id FROM mb_messages WHERE message_id > '".$a_Temp["read_message"]."' AND message_parent='".$_GET["id"]."' LIMIT 1";
			$a_Temp = DB::db_Array (DB::db_Query ($s_Query));

			/* Get the page number and redirect to the proper place */
			$i_Page = $this->GetPageFromMessage ($a_Temp["message_id"]);
			header ("Location: ./index.php?main=mb&action=view_thread&id=".$_GET["id"]."&page=$i_Page#".$a_Temp["message_id"]);
			return true;
		}
		
		/* Get the topic and forum data */
		$a_Topic = $this->GetTopic ($i_ID);
		$a_Forum = $this->GetForum ($a_Topic["topic_parent"]);
		
		/* If this is a private topic fix the forum data */
		if ($a_Topic["topic_parent"] == 52) {
			$a_Forum["forum_name"] = "Private Threads";
			$a_Forum["forum_id"] = 52;
		}
		
		/* If this is a pricate topic make sure the user is in on the fun */
		if ($a_Topic["topic_to"] != 0 && $a_Topic["topic_to"] != $a_User["user_id"] && $a_Topic["topic_from"] != $a_User["user_id"] && $a_User["user_rank"] < 3) {
			Err::Raise ("You cannot view other people's private topics!", E_TETRA_USER, "mb");
			return false;
		}
		
		/* Figure up the number of pages */
		$i_NumPages = ceil ($a_Topic["topic_numposts"] / $a_User["user_mb_mpp"]);
		
		/* If a page number was passed and isn't greater than the number of pages get things set up accordingly
		   otherwise just go to the last page */
		if (is_numeric ($_GET["page"]) && $_GET["page"] <= $i_NumPages) {
			$i_Page = $_GET["page"];
			$i_Offset = ($i_Page - 1) * $a_User["user_mb_mpp"];
		}
		else {
			$i_Page = $i_NumPages;
			$i_Offset = ($i_NumPages - 1) * $a_User["user_mb_mpp"];
		}
		
		/* Check to see if the user has permission to view the topic */
		if ($a_User["user_rank"] < $a_Forum["forum_viewperms"]) {
			Err::Raise ("You do not have permission to view topics in this forum!", E_TETRA_USER, "mb", "Topic: ".$a_Topic["topic_title"]."\nUser: ".$a_User["user_name"]);
			return false;
		}
		
		/* Display the headers */
		Tpl::Header ("Viewing Thread: ".$a_Topic["topic_title"]);
		Tpl::Assign ("page", array ("current_page"=>$i_Page, "num_pages"=>$i_NumPages));
		Tpl::Assign ("topic", $a_Topic);
		Tpl::Assign ("forum", $a_Forum);
		if ($b_Headers)
			Tpl::Display ("mb_thread_head.tpl");
		
		/* Get the messages */
		$s_Query = "SELECT message_id FROM mb_messages WHERE message_parent='".$a_Topic["topic_id"]."' ORDER BY message_id $s_Order LIMIT $i_Offset, ".$a_User["user_mb_mpp"];
		$h_Result = DB::db_Query ($s_Query);
		
		/* Loop through the messages and display them */
		for ($i = 0; $i < $h_Result["num_rows"]; $i++) {
			
			/* Get the message data */
			$a_Data = DB::db_Array ($h_Result);
			$a_Data = $this->GetMessage ($a_Data["message_id"]);
			
			/* Check to see if the user can edit this post. A few factors determine this:
				If the user is an admin, yes.
				If this is the original poster and it's been less than thirty minutes since the original post. */
			if ($a_User["user_rank"] > 1)
				Tpl::Assign ("can_edit", 1);
			elseif ($a_User["user_id"] == $a_Data["message_poster"] && (time () - $a_Data["message_time"]) < 1800)
				Tpl::Assign ("can_edit", 1);
			else
				Tpl::Assign ("can_edit", 0);
			
			/* If the user has a signature attach it to the message */
			if ($a_Data["user_mb_signature"])
				$a_Data["message_body"] .= "<hr>".$a_Data["user_mb_signature"];
			
			/* Convert the smileys and mbcode */
			$a_Data["message_body"] = nl2br (MBCode::ConvertMBCode ($a_Data["message_body"]));
			$a_Data["message_body"] = MBCode::ConvertSmileys ($a_Data["message_body"]);
			
			/* Display the message */
			Tpl::Assign ("post", $a_Data);
			Tpl::Display ("mb_thread_message.tpl");
			
		}
		
		/* If the user isn't guest update the topic read stuff */
		if ($a_User["user_id"] > 1) {
			/* Get the last message read by this user */
			$s_Query = "SELECT read_message FROM mb_read WHERE read_user='".$a_User["user_id"]."' AND read_topic='".$a_Data["topic_id"]."'";
			$a_Temp = DB::db_Array (DB::db_Query ($s_Query));
			
			/* If the last message on this page is newer update the read message */
			if ($a_Data["message_id"] > $a_Temp["read_message"])
				$this->SetRead ($i_ID, $a_Data["message_id"]);

		}
		
		/* Show the footers */
		if ($b_Headers)
			Tpl::Display ("mb_thread_foot.tpl");
		Tpl::Footer ();
		
	}
	
	/**************************************************
	* Function: PostForm                              *
	* Description: Sets up the post form              *
	**************************************************/
	function PostForm ()
	{
	
		/* Get the user array */
		global $a_User;
		
		/* Validate the topic ID */
		if (!$_GET["id"] || !is_numeric ($_GET["id"])) {
			Err::Raise ("The topic ID passed is invalid!", E_TETRA_FORM, "mb", "ID: ".$_GET["id"]."\nUser: ".$a_User["user_name"]);
			return false;
		}
		
		/* Get the topic and forum info */
		$a_Topic = $this->GetTopic ($_GET["id"]);
		Tpl::Assign ("flagged", $this->TopicFlagged ($_GET["id"]));
		$a_Forum = $this->GetForum ($a_Topic["topic_parent"]);
		
		/* Make sure the topic isn't locked */
		if ($a_Topic["topic_locked"] != 0) {
			Err::Raise ("This topic has been locked. You cannot reply to it.", E_TETRA_CRITICAL, "mb");
			return false;
		}
		
		/* Check to make sure the user has permission to post here */
		if ($a_User["user_rank"] < $a_Forum["forum_postperms"]) {
			Err::Raise ("You cannot post to topics in this forum!", E_TETRA_USER, "Topic: ".$a_Topic["topic_title"]."\nUser: ".$a_User["user_name"]);
			return false;
		}
		
		/* If this is a private topic make sure the user is in on the fun */
		if ($a_Topic["topic_to"] != 0 && $a_Topic["topic_to"] != $a_User["user_id"] && $a_Topic["topic_from"] != $a_User["user_id"] && $a_User["user_rank"] < 3) {
			Err::Raise ("You cannot post to other people's private topics!", E_TETRA_USER, "mb");
			return false;
		}
		
		/* If a message ID was passed get it's info to use for the quote */
		if (is_numeric ($_GET["message"])) {
			$a_Message = $this->GetMessage ($_GET["message"]);
			Tpl::Assign ("quote", "[quote=".$a_Message["user_name"]."]".$a_Message["message_body"]."[/quote]");
		}
		
		/* Display the post form */
		Tpl::Header ("Reply to ".$a_Topic["topic_title"]);
		Tpl::Assign ("id", $_GET["id"]);
		Tpl::Display ("mb_post_form.tpl");
		Tpl::Footer ();
	
	}

	/**************************************************
	* Function: PreparePost                           *
	* Description: Sets things up before adding the   *
	*              message to the database            *
	**************************************************/
	function PreparePost ()
	{
	
		/* Get the user array */
		global $a_User;
		
		/* Validate the topic ID */
		if (!$_GET["id"] || !is_numeric ($_GET["id"])) {
			Err::Raise ("The topic ID is invalid. Cannot post.", E_TETRA_FORM, "mb");
			return false;
		}
		
		/* Check the flag checkbox */
		if ($_POST["flag"] == "on")
			$b_Flag = 1;
		else
			$b_Flag = 0;
		
		/* If the user is an admin and lock was set, lock the topic */
		if ($a_User["user_rank"] > 1 && $_POST["lock"] == "on") {
			$s_Query = "UPDATE mb_topics SET topic_locked='1' WHERE topic_id='".$_GET["id"]."'";
			DB::db_Query ($s_Query);
		}
		
		/* See if there was an attachment */
		if ($_POST["attach"]) {
			
			/* Set the file's path and name */
			$s_File = "./docs/".$a_User["user_name"]."_".$_FILES["attachment"]["name"];
			
			/* Make sure the file is under the limit */
			if ($_FILES["attachment"]["size"] > 102400)
				Err::Raise ("Your file is too large! All file uploads are restricted to 100K. Your message will still be posted.", E_TETRA_GENERAL, "mb");
			else {			
				/* Move the uploaded file to the docs folder */
				if (move_uploaded_file ($_FILES["attachment"]["tmp_name"], $s_File))
					$s_Attach = $s_File;
				else {
					Err::Raise ("Your file did not upload successfully. Your message will still be posted.", E_TETRA_GENERAL, "mb");
				}
			}
		}
		
		/* Post the message */
		$i_Message = $this->Post ($_POST["body"], $_GET["id"], $s_Attach, $b_Flag);
		
		/* Update the read status of this topic if the message ID came back non-zero */
		if ($i_Message)
			$this->SetRead ($_GET["id"], $i_Message, $b_Flag);
	
	}

	/**************************************************
	* Function: PrepareEdit                           *
	* Description: Prpares a post for editing         *
	**************************************************/
	function PrepareEdit ()
	{
	
		/* Get the user array */
		global $a_User;
		
		/* Validate the message ID */
		if (!$_GET["id"] || !is_numeric ($_GET["id"])) {
			Err::Raise ("An invalid message ID was passed!", E_TETRA_FORM, "mb");
			return false;
		}
		
		/* Get the message, topic and the first message in the topic */
		$a_Message = $this->GetMessage ($_GET["id"]);
		$a_Topic = $this->GetTopic ($a_Message["message_parent"]);
		$a_First = $this->GetBasicMessage ($a_Topic["topic_firstpost"]);

		/* Make sure the user has permission to edit it */
		if ($a_User["user_id"] != $a_Message["message_poster"] && $a_User["user_rank"] < 2) {
			Err::Raise ("You do not have permission to edit this post!", E_TETRA_USER, "mb");
			return false;
		}
		
		/* Check to see if the topic is locked */
		if ($a_Topic["topic_locked"] != 0) {
			Err::Raise ("You can not edit posts in a locked topic.", E_TETRA_GENERAL, "mb");
			return false;
		}
		
		/* If the user isn't an admin check to see if we're still within the time limit */
		if ($a_User["user_rank"] < 1 && (time () - $a_Message["message_time"]) > 1800) {
			Err::Raise ("You can no longer edit this post. If you must make a change ask a moderator or admin to edit it for you.", E_TETRA_USER, "mb");
			return false;
		}
		
		/* If the user is also the creator of the topic (or an admin), show the edit title stuff */
		if ($a_User["user_id"] == $a_First["user_id"] || $a_User["user_rank"] > 1) {
			Tpl::Assign ("title_edit", 1);
			Tpl::Assign ("title", $a_Topic["topic_title"]);
		}
		
		/* Display the template */
		Tpl::Header ("Edit Post");
		Tpl::Assign ("id", $_GET["id"]);
		Tpl::Assign ("sticky", $a_Topic["topic_sticky"]);
		Tpl::Assign ("flagged", $this->TopicFlagged ($a_Topic["topic_id"]));
		Tpl::Assign ("message", $a_Message["message_body"]);
		Tpl::Display ("mb_post_edit.tpl");
		Tpl::Footer ();
		
		/* Display the topic review */
		$this->ViewThread ($a_Topic["topic_id"], "DESC");
	
	}
	
	/**************************************************
	* Function: EditPost                              *
	* Description: Edits a post                       *
	**************************************************/
	function EditPost ()
	{
	
		/* Get the user array */
		global $a_User;
		
		/* Validate the message ID */
		if (!$_GET["id"] || !is_numeric ($_GET["id"])) {
			Err::Raise ("An invalid message ID was passed!", E_TETRA_FORM, "mb");
			return false;
		}
		
		/* Get the message, topic and the first message in the topic */
		$a_Message = $this->GetBasicMessage ($_GET["id"]);
		$a_Topic = $this->GetTopic ($a_Message["message_parent"]);
		$a_First = $this->GetBasicMessage ($a_Topic["topic_firstpost"]);
		
		/* Make sure the user has permission to edit it */
		if ($a_User["user_id"] != $a_Message["user_id"] && $a_User["user_rank"] < 2) {
			Err::Raise ("You do not have permission to edit this post!", E_TETRA_USER, "mb");
			return false;
		}

		/* Check to see if the topic is locked */
		if ($a_Topic["topic_locked"] != 0) {
			Err::Raise ("You can not edit posts in a locked topic.", E_TETRA_GENERAL, "mb");
			return false;
		}
		
		/* If the user isn't an admin check to see if we're still within the time limit */
		if ($a_User["user_rank"] < 1 && (time () - $a_Message["message_time"]) > 1800) {
			Err::Raise ("You can no longer edit this post. If you must make a change ask a moderator or admin to edit it for you.", E_TETRA_USER, "mb");
			return false;
		}
		
		/* If the user is also the creator of the topic (or an admin), update the topic title */
		if ($a_User["user_id"] == $a_First["message_poster"] || $a_User["user_rank"] > 1) {

			/* If the user is an admin check the sticky stuff */
			if ($a_User["user_rank"] > 1) {
				if ($_POST["sticky"] == "on")
					$s_Sticky = ", topic_sticky=1";
				else
					$s_Sticky = ", topic_sticky=0";
			}
			
			/* Check to see if the topic was locked */
			if ($_POST["lock"] == "on")
				$i_Lock = 1;
			else
				$i_Lock = 0;
			
			/* Update the topic */
			$s_Query = "UPDATE mb_topics SET topic_title='".addslashes ($_POST["title"])."', topic_sticky='$s_Sticky', topic_locked='$i_Lock' WHERE topic_id='".$a_Topic["topic_id"]."'";
			if (DB::db_Query ($s_Query)) {
				Err::Raise ("Couldn't edit the topic title! Go back and try again. If you continue to have problems, contact an administrator.", E_TETRA_CRITICAL, "mb");
				return false;
			}

		}
	
		/* Update the message */
		$s_Query = "UPDATE mb_messages SET message_body='".addslashes ($_POST["body"])."', message_edits=message_edits+1 WHERE message_id='".$a_Message["message_id"]."'";
		if (DB::db_Query ($s_Query)) {
			Err::Raise ("Couldn't update your message. Please go back and try again. If you continue to have problems contact an administrator.", E_TETRA_CRITICAL, "mb");
			return false;
		}
		
		/* Update the flagged status */
		if ($_POST["flag"] == "on")
			$b_Flagged = 1;
		else
			$b_Flagged = 0;
		
		$s_Query = "UPDATE mb_read SET read_flagged='$b_Flagged' WHERE read_user='".$a_User["user_id"]."' AND read_topic='".$a_Topic["topic_id"]."'";
		DB::db_Query ($s_Query);
		
		/* Display the user's post */
		$this->ViewThread ($a_Topic["topic_id"]);
	
	}
	
	/**************************************************
	* Function: RecentPosts                           *
	* Description: Lists the five topics with the     *
	*              most recent posts                  *
	**************************************************/
	function Recent ($s_Type = "")
	{

		/* Get the user array */
		global $a_User;
	
		/* See what we're supposed to be displaying */
		switch ($s_Type) {
		case "docs":
			/* Get the five newest attachments */
			$s_Query = "SELECT m.message_poster AS user_id, m.message_id, t.topic_title, t.topic_id, u.user_name FROM mb_messages AS m INNER JOIN mb_topics AS t ON t.topic_id=m.message_parent INNER JOIN users AS u ON u.user_id=m.message_poster WHERE m.message_attachment != '' ORDER BY m.message_id DESC LIMIT 5";
			$s_Type = "Documents";
			break;
		default:
			/* Get the topics with the newest posts */
			$s_Query = "SELECT t.topic_id FROM mb_topics AS t INNER JOIN mb_forums AS f ON f.forum_id=t.topic_parent INNER JOIN mb_messages AS m ON m.message_id=t.topic_lastpost WHERE t.topic_parent < '50' AND f.forum_viewperms <= '".$a_User["user_rank"]."' ORDER BY m.message_date DESC LIMIT 5";
			$s_Type = "Posts";
		}
	
		/* Display the header */
		Tpl::Assign ("type", $s_Type);
		Tpl::Display ("mb_recent_head.tpl");
		
		/* Run the query */
		$h_Result = DB::db_Query ($s_Query);
		
		/* Display them */
		for ($i = 0; $i < $h_Result["num_rows"]; $i++) {
		
			/* Get the data and display it */
			$a_Data = DB::db_Array ($h_Result);
			$a_Data = $this->GetTopic ($a_Data["topic_id"]);
			$a_Data["page"] = $this->GetPageFromMessage ($a_Data["topic_lastpost"]);
			Tpl::Assign ("topic", $a_Data);
			Tpl::Assign ("post", $this->GetBasicMessage ($a_Data["topic_lastpost"]));
			Tpl::Display ("mb_recent_item.tpl");
		
		}
		
		/* Display the footer */
		Tpl::Display ("mb_recent_foot.tpl");
		
	
	}
	
	/**************************************************
	* Function: Search                                *
	* Description: Searchs the message board          *
	**************************************************/
	function Search ()
	{
	
		/* Get the user array */
		global $a_User;
		
		/* Verify the query */
		if (!$_GET["q"]) {
			Err::Raise ("Please enter a search query.", E_TETRA_FORM, "mb");
			return false;
		}
		
		/* If a page number was provided figure up the DB offsets */
		if ($_GET["page"]) {
			$i_Page = $_GET["page"];
			$i_Offset = ($i_Page - 1) * $a_User["user_mb_tpp"];
		}
		else {
			$i_Page = 1;
			$i_Offset = 0;
		}
		
		/* Build the query and run */
		$s_Query = "SELECT message_parent, message_id, MATCH(message_body) AGAINST ('".$_GET["q"]."' IN BOOLEAN MODE) AS score FROM mb_messages WHERE MATCH (message_body) AGAINST ('".$_GET["q"]."' IN BOOLEAN MODE) ORDER BY score DESC LIMIT $i_Offset, ".$a_User["user_mb_tpp"];
		$h_Result = DB::db_Query ($s_Query);
		
		/* Figure up page info */
		$a_Page["num_pages"] = ceil ($a_Temp["total"] / $a_User["user_mb_tpp"]);
		$a_Page["current_page"] = $i_Page;
		Tpl::Assign ("page", $a_Page);
		Tpl::Assign ("query", $_GET["q"]);
		
		/* Display the header */
		Tpl::Assign ("total", $h_Result["num_rows"]);
		Tpl::Display ("mb_search_head.tpl");
		
		/* Loop through each item and display it */
		if (!$h_Result["num_rows"])
			Tpl::Display ("mb_search_none.tpl");
		else {
			for ($i = 0; $i < $h_Result["num_rows"]; $i++) {
				
				/* Get the data */
				$a_Data = DB::db_Array ($h_Result);
				
				/* Get the topic and message stuffs */
				Tpl::Assign ("topic", $this->GetTopic ($a_Data["message_parent"]));
				$a_Message = $this->GetMessage ($a_Data["message_id"]);
				
				/* Check to see if the message is long */
				if (strlen ($a_Message["message_body"]) > 128)
					/* If it's longer than 128 characters cut it off at the first space after the 128 mark */
					$a_Message["message_body"] = substr ($a_Message["message_body"], 0, strpos ($a_Message["message_body"], " ", 128))."...";
				
				/* Get the page the message is on */
				$a_Message["message_page"] = $this->GetPageFromMessage ($a_Message["message_id"]);
				
				/* Display the template */
				Tpl::Assign ("post", $a_Message);
				Tpl::Display ("mb_search_item.tpl");
				
			}
		}
		
		/* Display the header */
		Tpl::Display ("mb_search_foot.tpl");
		
	}
	
	/******************************************************************************
	*                               ADMIN FUNCTIONS                               *
	******************************************************************************/

	/**************************************************
	* Function: CreateSection                         *
	* Description: Creates a section                  *
	**************************************************/
	function CreateSection ()
	{
	
		/* Make sure that a name was provided */
		if (!$_POST["name"]) {
			Err::Raise ("Please enter a name!");
			Tpl::Display ("mb_admin_section.tpl");
			return false;
		}
		
		/* Add the section */
		$s_Query = "INSERT INTO mb_forums (forum_name) VALUES ('".addslashes ($_POST["name"])."')";
		if (DB::db_Query ($s_Query))
			Err::Message ("Section Created", "The section was successfully created. You can now add forums in it.");
	
	}
	
	/**************************************************
	* Function: CreateForumForm                       *
	* Description: Gets info for and displays the     *
	*              create forum form                  *
	**************************************************/
	function CreateForumForm ()
	{
	
		/* Get a list of the forum sections */
		$s_Query = "SELECT forum_id, forum_name FROM mb_forums WHERE forum_parent='0'";
		$h_Result = DB::db_Query ($s_Query);
		
		/* If there are no sections and we're not creating a section let the user know */
		if ($h_Result["num_rows"] == 0 && !$_GET["section"]) {
			Err::Message ("Cannot create forum", "You do not have any sections to add this forum to. Please create a section first.");
			return false;
		}
		
		/* Create a list of the section options */
		for ($i = 0; $i < $h_Result["num_rows"]; $i++) {
			
			/* Get the info */
			$a_Data = DB::db_Array ($h_Result);
			
			/* Add it to the list */
			$s_Forums .= "\n<option value=\"".$a_Data["forum_id"]."\">".$a_Data["forum_name"]."</option>\n";
			
		}
		
		/* Display the template */
		if ($_GET["section"])
			Tpl::Assign ("sectionform", 1);
		Tpl::Assign ("sections", $s_Forums);
		if ($_GET["section"])
			Tpl::Header ("Create Section");
		else
			Tpl::Header ("Create Forum");
		Tpl::Display ("mb_admin_forum.tpl");
		Tpl::Footer ();
	
	}

	/**************************************************
	* Function: CreateForum                           *
	* Description: Creates a forum                    *
	**************************************************/
	function CreateForum ()
	{
	
		/* Make sure that at least a name was provided */
		if (!$_POST["f_name"]) {
			Err::Raise ("Please go back and provide a name", E_TETRA_FORM, "mb");
			return false;
		}
		
		/* Escape the forum name and description */
		$s_Name = addslashes ($_POST["f_name"]);
		$s_Desc = addslashes ($_POST["f_desc"]);
		
		/* Set up the permissions and news stuff */
		if ($_POST["f_news"] == "on")
			$b_News = 1;
		else
			$b_News = 0;
		
		/* Create the forum */
		$s_Query = "INSERT INTO mb_forums (forum_name, forum_description, forum_viewperms, forum_postperms, forum_topicperms, forum_news, forum_parent) VALUES ";
		$s_Query .= "('$s_Name', '$s_Desc', '".$_POST["f_view"]."', '".$_POST["f_post"]."', '".$_POST["f_topic"]."', '$b_News', '".$_POST["f_section"]."')";
		if (!DB::db_Query ($s_Query)) {
			Err::Message ("Forum created!", "The forum was created successfully!");
		}
	
	}

	
	
	/******************************************************************************
	*                             IN-HOUSE FUNCTIONS                              *
	******************************************************************************/
	
	/**************************************************
	* Function: Post                                  *
	* Description: Posts a message                    *
	**************************************************/
	function Post ($s_Message, $i_Topic, $s_Attachment = "", $b_Flag = 0)
	{
	
		/* Get the user array */
		global $a_User;
		
		/* Get the topic and forum info */
		$a_Topic = $this->GetTopic ($i_Topic);
		$a_Forum = $this->GetForum ($a_Topic["topic_parent"]);
	
		/* Get the time of the last post by the user on this topic */
		$s_Query = "SELECT message_date FROM mb_messages WHERE message_poster='".$a_User["user_id"]."' AND message_parent='".$a_Topic["topic_id"]."' ORDER BY message_id DESC LIMIT 1";
		$a_Temp = DB::db_Array (DB::db_Query ($s_Query));
		
		/* If it's only been a minute since the last post keep from posting */
		if ((time () - $a_Temp["message_date"]) < 60) {
			Err::Raise ("Flood control has prevented this message from being posted. If you want to make changes to your last message please edit it. Otherwise wait a full minute before trying to post again.", E_TETRA_GENERAL, "mb");
			return false;
		}
	
		/* Check to see if the user has permission to post to this forum */
		if ($a_User["user_rank"] < $a_Forum["forum_postperms"]) {
			Err::Raise ("You do not have permission to post to this forum!", E_TETRA_USER, "mb", "Topic: ".$a_Topic["topic_name"]."\nUser: ".$a_User["user_name"]);
			return false;
		}
		
		/* If the user is a guest make sure that they provided a nick name */
		if ($a_User["user_id"] == 1 && !$_POST["guest_name"]) {
			Err::Raise ("You must enter a nickname!", E_TETRA_FORM, "mb");
			return false;
		}
		
		/* If the topic was locked make sure the user has permissions to do so */
		if ($_POST["locked"] == "on" && $a_User["user_rank"] < 2) {
			Err::Raise ("You cannot lock this topic. Your message will still post, but the admins have been notified of your activity.", E_TETRA_USER, "users", "Username: ".$a_User["user_name"]);
		}
		
		/* Escape the message and remove HTML */
		$s_Message = addslashes (htmlentities ($s_Message));
		
		/* Stuff it into the database */
		$s_Query = "INSERT INTO mb_messages (message_guest, message_parent, message_body, message_poster, message_date, message_attachment) VALUES ";
		$s_Query .= "('".$_POST["guest_name"]."', '$i_Topic', '$s_Message', '".$a_User["user_id"]."', '".gmdate ("U")."', '$s_Attachment')";
		if (($i_Message = DB::db_Query ($s_Query))) {
			
			/* Now we need to update the last post field in the topic entry. Also update the number of posts */
			$s_Query = "UPDATE mb_topics SET topic_numposts=topic_numposts+1, topic_lastpost='$i_Message' WHERE topic_id='$i_Topic'";
			DB::db_Query ($s_Query);
			
			/* Display success */
			Tpl::Header ("Message Posted");
			Tpl::Assign ("forum", $a_Forum);
			Tpl::Assign ("topic", $a_Topic);
			Tpl::Display ("mb_post_success.tpl");
			Tpl::Footer ();
			
			/* Return the ID */
			return $i_Message;
			
		}
		else {
			echo ($s_Query);
			Err::Message ("Couldn't post message", "There was an error posting your message. Please go back and try again. If you continue to have problems contant a moderator.");
			return false;
		}
	
	}
	
	/**************************************************
	* Function: GetForum                              *
	* Description: Returns forum info from the ID     *
	**************************************************/
	function GetForum ($i_ID)
	{
	
		/* Get the forum info and return it */
		$s_Query = "SELECT * FROM mb_forums WHERE forum_id='$i_ID'";
		return DB::db_Array (DB::db_Query ($s_Query));
	
	}

	/**************************************************
	* Function: GetMessage                            *
	* Description: Returns message stuff from the ID  *
	**************************************************/
	function GetMessage ($i_ID)
	{
		
		/* Get the user array */
		global $a_User;
		
		/* Get the message and the user name of it's poster */
		$s_Query = "SELECT m.*, u.user_name, u.user_rank, u.user_mb_avatar, u.user_mb_signature, u.user_mb_title FROM mb_messages AS m INNER JOIN users AS u ON u.user_id=m.message_poster WHERE m.message_id='$i_ID'";
		$a_Temp = DB::db_Array (DB::db_Query ($s_Query));
		$a_Temp["message_time"] = $a_Temp["message_date"];
		$a_Temp["message_date"] = make_date ($a_Temp["message_date"]);
		$a_Temp["message_body"] = $a_Temp["message_body"];
		
		/* If the person who posted this was a guest replace the user_name with their made up one */
		if ($a_Temp["message_poster"] == 1)
			$a_Temp["user_name"] = $a_Temp["message_guest"];
		
		/* Slap the rank title on */
		switch ($a_Temp["user_rank"]) {
		case 0:
			$a_Temp["user_rank_title"] = "Guest";
			break;
		case 1:
			$a_Temp["user_rank_title"] = "Member";
			break;
		case 2:
			$a_Temp["user_rank_title"] = "<font color=\"#008000\">Moderator</font>";
			break;
		case 3:
			$a_Temp["user_rank_title"] = "<font color=\"#0080FF\">Admin</font>";
			break;
		}
		
		return $a_Temp;
	
	}

	/**************************************************
	* Function: GetBasicMessage                       *
	* Description: Returns basic elements of a post   *
	**************************************************/
	function GetBasicMessage ($i_ID)
	{
		
		/* Get the message and the user name of it's poster */
		$s_Query = "SELECT m.message_parent, m.message_id, m.message_date, m.message_poster AS user_id, u.user_name FROM mb_messages AS m INNER JOIN users AS u ON u.user_id=m.message_poster WHERE m.message_id='$i_ID' LIMIT 1";
		$a_Temp = DB::db_Array (DB::db_Query ($s_Query));
		$a_Temp["message_time"] = $a_Temp["message_date"];
		$a_Temp["message_date"] = make_date ($a_Temp["message_date"]);
		
		return $a_Temp;
	
	}

	/**************************************************
	* Function: GetTopic                              *
	* Description: Returns topic data                 *
	**************************************************/
	function GetTopic ($i_ID, $b_GetRead = true)
	{
	
		/* Get the topic */
		$s_Query = "SELECT * FROM mb_topics WHERE topic_id='$i_ID'";
		$a_Temp = DB::db_Array (DB::db_Query ($s_Query));
		if ($b_GetRead)
			$a_Temp["topic_read"] = $this->TopicRead ($i_ID);
		return $a_Temp;
	
	}
	
	/**************************************************
	* Function: DisplayStats                          *
	* Description: Displays message board statistics  *
	**************************************************/
	function DisplayStats ()
	{
	
		/* Get the total amount of posts */
		$s_Query = "SELECT count(*) AS total FROM mb_messages";
		$a_Posts = DB::db_Array (DB::db_Query ($s_Query));
	
	}

	/**************************************************
	* Function: SetRead                               *
	* Description: Sets the read status of a topic    *
	**************************************************/
	function SetRead ($i_Topic, $i_Message, $b_Flagged = 10)
	{
	
		/* Get the user array */
		global $a_User;
		
		/* If the user is guest don't bother doing anything */
		if ($a_User["user_id"] == 1)
			return false;
		
		/* Get the last read message for this topic */
		$s_Query = "SELECT read_message, read_flagged FROM mb_read WHERE read_topic='$i_Topic' AND read_user='".$a_User["user_id"]."'";
		$h_Result = DB::db_Query ($s_Query);
		
		/* If results were passed make sure that the last post read isn't newer */
		if ($h_Result["num_rows"] != 0) {
			
			/* Get the message ID */
			$a_Data = DB::db_Array ($h_Result);
			
			/* If the ID is greater, break */
			if ($a_Data["read_message"] > $i_Message)
				return false;
		}
		
		/* If the flag parameter wasn't set use what was in the previous read entry */
		if ($b_Flagged == 10)
			$b_Flagged = $a_Data["read_flagged"];
		
		/* Delete the old entry (if it exists) */
		$s_Query = "DELETE FROM mb_read WHERE read_topic='$i_Topic' AND read_user='".$a_User["user_id"]."'";
		DB::db_Query ($s_Query);
		
		/* Insert the new entry */
		$s_Query = "INSERT INTO mb_read (read_user, read_topic, read_message, read_flagged) VALUES ('".$a_User["user_id"]."', '$i_Topic', '$i_Message', '".intval ($b_Flagged)."')";
		DB::db_Query ($s_Query);
	
	}

	/**************************************************
	* Function: GetPageFromMessage                    *
	* Description: Returns the page number a message  *
	*              is on                              *
	**************************************************/	
	function GetPageFromMessage ($i_Message)
	{
	
		/* Get the user array */
		global $a_User;
		
		/* Get the message info */
		$a_Message = $this->GetBasicMessage ($i_Message);
		
		/* Get the number of messages before the requested one */
		$s_Query = "SELECT count(*) AS total FROM mb_messages WHERE message_parent='".$a_Message["message_parent"]."' AND message_id < '$i_Message'";
		$a_Temp = DB::db_Array (DB::db_Query ($s_Query));
		
		/* Figure up how many pages this would be and we have our number */
		return ceil (($a_Temp["total"] + 1) / $a_User["user_mb_mpp"]);
	
	}

	/**************************************************
	* Function: TopicFlagged                          *
	* Description: Returns whether or not the topic   *
	*              is flagged                         *
	**************************************************/
	function TopicFlagged ($i_Topic)
	{
	
		/* Get the user array */
		global $a_User;
		
		/* Get the flagged value from the read table */
		$s_Query = "SELECT read_flagged FROM mb_read WHERE read_user='".$a_User["user_id"]."' AND read_topic='".$i_Topic."'";
		$h_Result = DB::db_Query($s_Query);
		
		/* If no results were returned then the user couldn't have flagged it. Heck, they ain't read it :-P */
		if ($h_Result["num_rows"] == 0)
			return false;
		else {
			/* Get the info */
			$a_Data = DB::db_Array ($h_Result);
			
			/* Return the flagged value */
			return (bool) $a_Data["read_flagged"];
		}
	
	}

	/**************************************************
	* Function: TopicRead                             *
	* Description: Returns whether or not the user    *
	*              has read a topic                   *
	**************************************************/
	function TopicRead ($i_ID)
	{
	
		/* Get the user array */
		global $a_User;
		
		/* If the user isn't logged in return read */
		if ($a_User["user_id"] == 1)
			return true;
		
		/* Get the topic info and the ID of the last message viewed */
		$a_Topic = $this->GetTopic ($i_ID, false);
		
		$s_Query = "SELECT read_message FROM mb_read WHERE read_user='".$a_User["user_id"]."' AND read_topic='".$i_ID."'";
		$a_Read = DB::db_Array (DB::db_Query ($s_Query));
		
		/* If the ID of the newest post on the topic is the same as the last post read, return read */
		if ($a_Read["read_message"] == $a_Topic["topic_lastpost"])
			return true;
		
		/* The newest post is newer so get the date. */
		$s_Query = "SELECT message_date FROM mb_messages WHERE message_id='".$a_Topic["topic_lastpost"]."'";
		$a_Post = DB::db_Array (DB::db_Query ($s_Query));
		
		/* If this post is older than a week mark it as read */
		if ($a_Post["message_date"] + 604800 < time ())
			return true;
	
	}

	/********************************************
	* Function: CreateAvatar                    *
	* Description: Creates an avatar of the     *
	*              requested picture            *
	********************************************/	
	function CreateAvatar ($s_SrcFile, $s_Ext)
	{
		
		/* Get the user array */
		global $a_User;
		
		/* Create a new image */
		$t_DestImage = ImageCreateTrueColor (100, 100);
		
		/* Create our colors */
		$c_White = ImageColorAllocate ($t_DestImage, 255, 255, 255);
		$c_Black = ImageColorAllocate ($t_DestImage, 0, 0, 0);
		
		/* Fill the image with white */
		ImageFill ($t_DestImage, 0, 0, $c_White);
		
		/* Turn on antialiasing for the destination image */
		ImageAntiAlias ($t_DestImage, true);
		
		/* Open the source image */
		switch ($s_Ext) {
		case "jpg":
			$t_SrcImage = @ImageCreateFromJPEG ($s_SrcFile);
			break;
		case "png":
			$t_SrcImage = @ImageCreateFromPNG ($s_SrcFile);
			break;
		case "gif":
			$t_SrcImage = @ImageCreateFromGIF ($s_SrcFile);
			break;
		}
		
		/* If the source image was unable to load, we'll copy it over and allow the browser to resize it. */
		if (!$t_SrcImage) {
			move_uploaded_file ($s_SrcFile, "./docs/".$a_User["user_name"]."_avatar.".$s_Ext);
			return "./docs/".$a_User["user_name"]."_avatar.".$s_Ext;
		}
		
		/* Get the width and height and X and Y of the image */
		$i_Width = ImageSX ($t_SrcImage);
		$i_Height = ImageSY ($t_SrcImage);
		$i_X = 0;
		$i_Y = 0;
		
		/* If the image is larger than the thumbnail size in any respect we'll do an aspect ratio scale */
		if ($i_Width > 100 || $i_Height > 100) {
			
			/* If the image is taller than it is wide scale the width using a height / width ratio */
			if ($i_Height > $i_Width) {
				
				/* Set the height and width */
				$i_Width = 100 * ($i_Width / $i_Height);
				$i_Height = 100;
				
				/* Set the X and Y */
				$i_X = (100 - $i_Width) / 2;
				$i_Y = 0;
				
			}
			else {
				
				/* Set the width to maximum and calculate the height from that */
				$i_Height = 100 * ($i_Height / $i_Width);
				$i_Width = 100;
				
				/* Set the X and Y */
				$i_X = 0;
				$i_Y = (100 - $i_Height) / 2;
			
			}
			
		}		
		/* If the image is smaller, we'll just center it and the heck with it */
		elseif ($i_Width < 100 && $i_Height < 100) {
		
			/* Center that puppy */
			$i_X = (100 - $i_Width) / 2;
			$i_Y = (100 - $i_Height) / 2;
		
		}
		
		/* Copy the image into the destination buffer */
		ImageCopyResized($t_DestImage,$t_SrcImage, $i_X, $i_Y, 0, 0,$i_Width,$i_Height, ImageSX($t_SrcImage),ImageSY($t_SrcImage));
		
		/* Draw a box around the edges */
		ImageRectangle ($t_DestImage, 0, 0, 99, 99, $c_Black);
		
		/* Output the image */
		ImagePNG ($t_DestImage, "./docs/".$a_User["user_name"]."_avatar.png");
		return "./docs/".$a_User["user_name"]."_avatar.png";
		
	}
	
}

?>