<?php

/********************************************
* Tetra                                     *
* Copyright (c) 2004 Matt "dxprog" Hackmann *
* More copyright info can be found in       *
* license.txt                               *
********************************************/

/********************************************
* Tetra Wiki Module                         *
* Version 1.0                               *
********************************************/

define (URI_WIKI_PAGE, 2);

$s_Class = "Wiki";
class Wiki {

	/* Module settings */
	var $module_name = "Wiki";
	var $module_ver = 1.0;
	var $module_title = true;
	
	/* Admin only control */
	var $admin_only = true;
	
	/**************************************************
	* Function: TetraHandler                          *
	* Description: Handles requests from Tetra core   *
	**************************************************/
	function TetraHandler ($i_Request, $s_Parameters)
	{
	
		
	
	}

	/**************************************************
	* Function: HandleRequest                         *
	* Description: Handles page requests              *
	**************************************************/
	function HandleRequest ($s_Page)
	{
	
		/* Figure out what to do */
		switch ($s_Page) {
		case "edit":
			break;
		case "create":
			break;
		default:
			$this->DisplayPage (P::Get (URI_WIKI_PAGE));
		}
	
	}
	
	/**************************************************
	* Function: DisplayPage                           *
	* Description: Displays a quick page              *
	**************************************************/
	function DisplayPage ($s_Page)
	{
	
		/* Get the user array */
		global $a_User;
	
		/* Lump the page name into a template variable */
		Tpl::Assign ("wiki_page", $s_Page);
	
		/* If no page was passed display the default page */
		if (!$s_Page) {
			$s_Query = "SELECT * FROM wiki WHERE wiki_default='1'";
			$a_Data = DB::db_Array (DB::db_Query ($s_Query));
			$s_Page = $a_Temp["wiki_page"];
		}
		else {
			/* Get the page */
			$s_Query = "SELECT * FROM wiki WHERE LCASE(wiki_name)='".strtolower ($s_Page)."'";
			$a_Data = DB::db_Array (DB::db_Query ($s_Query));
		}
		
		/* If there's no data go to the edit page (if the user can) */
		if (!$a_Data) {
			if (($a_User["user_rank"] == 3 && $this->admin_only) || !$this->admin_only) {
				$this->EditForm ($s_Page);
				return false;
			}
			else
				Tpl::Display ("wiki_no_page.tpl");
		}
		
		/* Convert any tags in the body */
		Tpl::Assign ("wiki_body", $this->ConvertTags ($a_Data["wiki_body"]));
		Tpl::Assign ("wiki_created", make_date ($a_Data["wiki_creation"]));
		Tpl::Assign ("wiki_modified", make_date ($a_Data["wiki_modified"]));
		Tpl::Display ("wiki_page.tpl");
	
	}

	/**************************************************
	* Function: ConvertTags                           *
	* Description: Converts MBCode tags to HTML       *
	**************************************************/	
	function ConvertTags ($s_Text)
	{
	
		/* Stick in HTML entities */
		$s_Text = htmlentities ($s_Text);
		
		/* The preg expressions */
		$a_Match = array ("/\[b\](.*?)\[\/b\]/is",
						  "/\[i\](.*?)\[\/i\]/is",
						  "/\[u\](.*?)\[\/u\]/is",
						  "/\[color=(.*?)\](.*?)\[\/color\]/is",
						  "/\[link=http:\/\/(.*?)\](.*?)\[\/link\]/is",
						  "/\[link=(.*?)\](.*?)\[\/link]/is",
						  "/\[img=http:\/\/(.*?)\]/is",
						  "/\[size=(\d{1,2})\](.*?)\[\/size\]/is");
		
		/* The replacement strings */
		$a_Replace = array ("<b>\$1</b>",
							"<i>\$1</i>",
							"<u>\$1</u>",
							"<span style=\"color: \$1\">\$2</span>",
							"<a href=\"http://\$1\">\$2</a>",
							"<a href=\"./wiki/\$1\">\$2</a>",
							"<img src=\"http://\$1\" alt=\"Picture\" />",
							"<span style=\"font-size: \$1px;\">\$2</span>");
		
		/* Run through the string until we've nailed every tag */
		$i_Size = strlen ($s_Text);
		while ($i_Size != $i_LastSize) {
			$i_LastSize = $i_Size;
			$s_Text = preg_replace ($a_Match, $a_Replace, $s_Text);
			$i_Size = strlen ($s_Text);
		}
		
		/* Return the completed text */
		return nl2br ($s_Text);
	
	}
	
	function EditForm ()
	{
		Tpl::Display ("wiki_edit_form.tpl");
	}
	
}

?>