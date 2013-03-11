<?php

/********************************************
* Tetra                                     *
* Copyright (c) 2004 Matt "dxprog" Hackmann *
* More copyright info can be found in       *
* license.txt                               *
********************************************/

/********************************************
* Tetra MessageBoard News                   *
* Version 1.0                               *
********************************************/

/* Use this to tell the messageboard module that this file was successfully included */
define ("MB_NEWS", 1);

class News {

	/*************************************************
	* Function: TetraHandler                         *
	* Description: Handles Tetra requests            *
	*************************************************/
	function TetraHandler ($i_Request, $s_Parameters)
	{
		
		/* See what Tetra wants */
		switch ($i_Request) {
		case T_REQUEST_TITLE:
			return "News";
			break;
		}
	
	}

	/*************************************************
	* Function: HandleRequest                        *
	* Description: Delegates page jobs upon request  *
	*************************************************/
	function HandleRequest ($s_Page)
	{
	
		/* Since this module only does one thing we'll leave all the code in here :-P */

		/* Get the user array */
		global $a_User;

		/* Get the number of news */
		$s_Query = "SELECT count(t.topic_id) AS total FROM mb_topics AS t INNER JOIN mb_forums AS f ON f.forum_id=t.topic_parent WHERE f.forum_news=1";
		$a_Temp = DB::db_Array (DB::db_Query ($s_Query));
		
		/* Figure up the number of pages */
		$i_NumPages = ceil ($a_Temp["total"] / $a_User["user_mb_mpp"]);
		
		/* If a page was specified (and isn't over the total amount of pages) use it and set up the offset
		   otherwise just go to page 1 */
		if (is_numeric ($_GET["page"]) && $_GET["page"] <= $i_NumPages) {
			$i_Page = $_GET["page"];
			$i_Offset = ($i_Page - 1) * $a_User["user_mb_mpp"];
		}
		else {
			$i_Page = 1;
			$i_Offset = 0;
		}
		
		/* Get the topics that fall under forums that are used for news */
		$s_Query = "SELECT t.topic_id FROM mb_topics AS t INNER JOIN mb_forums AS f ON f.forum_id=t.topic_parent INNER JOIN mb_messages AS m ON m.message_id=t.topic_firstpost WHERE f.forum_news=1 ORDER BY m.message_date DESC LIMIT $i_Offset, ".$a_User["user_mb_mpp"];
		$h_Result = DB::db_Query ($s_Query);
		
		/* Display the news header */
		Tpl::Header ("News");
		Tpl::Assign ("page", array ("current_page"=>$i_Page, "num_pages"=>$i_NumPages));
		Tpl::Display ("mb_news_head.tpl");
		
		/* If there were results display them */
		if ($h_Result["num_rows"] != 0) {
		
			/* Loop through each news item */
			for ($i = 0; $i < $h_Result["num_rows"]; $i++) {
			
				/* Get the info */
				$a_Data = DB::db_Array ($h_Result);

				/* Get the message */
				$a_Data = MB::GetTopic ($a_Data["topic_id"], false);
				$a_Message = MB::GetMessage ($a_Data["topic_firstpost"]);
				
				/* Convert smileys and MB code */
				$a_Message["message_body"] = nl2br (MBCode::ConvertMBCode (MBCode::ConvertSmileys ($a_Message["message_body"])));
				
				/* Display the news item */
				Tpl::Assign ("news", $a_Data);
				Tpl::Assign ("news", $a_Message);
				Tpl::Display ("mb_news_article.tpl");
			
			}
		
		}
		
		/* Display the footer */
		Tpl::Display ("mb_news_foot.tpl");
		Tpl::Footer ();
	
	}

}

?>