<?php

/***************************************************
* Tetra                                            *
* Copyright (c) 2004-2005 Matt "dxprog" Hackmann   *
* More copyright info can be found in              *
* license.txt                                      *
***************************************************/

/********************************************
* Tetra Navigation                          *
* Version 1.0                               *
********************************************/

/* The class */
class Nav {

	var $module_sidebars = true;

	/*********************************************
	* Function: TetraHandler                     *
	* Description: Handles Tetra requests        *
	*********************************************/
	function TetraHandler ($i_Request, $s_Parameters)
	{
	
		/* Since the only thing supported by this module is sidebars just spit out array of the options */
		return array ("User Panel"=>"nav|user");
	
	}
	
	/*********************************************
	* Function: HandleRequest                    *
	* Description: Handles page requests         *
	*********************************************/
	function HandleRequest ($s_Page)
	{
	
		/* See what we're doing */
		switch ($s_Page) {
		case "nav":
			/* Make the nav bar */
			$this->NavBar ();
			break;
		case "user":
			/* Make the login box/user box */
			$this->UserBox ();
			break;
		}
	
	}

	/*********************************************
	* Function: NavBar                           *
	* Description: Generates the navigation bar  *
	*********************************************/
	function NavBar ()
	{
	
		/* Show the nav header */
		Tpl::Display ("nav_head.tpl");
		
		/* Get the nav items from the database */
		$s_Query = "SELECT * FROM navigation ORDER BY nav_id ASC";
		$h_Result = DB::db_Query ($s_Query);
		Tpl::Assign ("num_nav_items", $h_Result["num_rows"]);
		
		/* Loop through each item */
		for ($i = 0; $i < $h_Result["num_rows"]; $i++) {
			
			/* Get the info */
			$a_Data = DB::db_Array ($h_Result);
			
			/* Display the item template */
			Tpl::Assign ("nav_url", htmlspecialchars ($a_Data["nav_href"]));
			Tpl::Assign ("nav_caption", $a_Data["nav_caption"]);
			Tpl::Display ("nav_item.tpl");
			
		}
		
		/* Show the footer */
		Tpl::Display ("nav_foot.tpl");
	
	}

	/*********************************************
	* Function: UserBox                          *
	* Description: Displays the login/user box   *
	*********************************************/
	function UserBox ()
	{
	
		/* Get the user, modules and site info arrays */
		global $a_User, $a_Modules, $a_HostInfo;
		
		/* If the user isn't logged in show the login box */
		if ($a_User["user_id"] == 1) {
			Tpl::Display ("nav_login_form.tpl");
		}
		else {
			
			/* Show the user nav header */
			Tpl::Display ("nav_user_head.tpl");
			
			/* Query each module for user items */
			foreach ($a_Modules as $s_Key=>$s_Thing) {
			
				/* Get the array of user nav items */
				if ($a_Modules[$s_Key]->module_user_nav_items) {
					$a_Temp = $a_Modules[$s_Key]->TetraHandler (T_REQUEST_NAV_USER, "");
				
					/* Make sure the array isn't blank before continuing */
					if (!$a_Temp)
						continue;
					
					/* Display the group header */
					Tpl::Assign ("nav_group", $a_Modules[$s_Key]->module_name." Settings");
					Tpl::Display ("nav_group.tpl");
					
					/* Go through each item and display it */
					foreach ($a_Temp as $s_Key=>$s_Value) {
						
						/* Set up the URL */
						$s_URL = $a_HostInfo["HostURL"]."/index.php?main=".$s_Value;
						
						/* Display the item */
						Tpl::Assign ("nav_url", htmlspecialchars ($s_URL));
						Tpl::Assign ("nav_caption", $s_Key);
						Tpl::Display ("nav_user_item.tpl");
						
					}
				}				
			}
			
			/* Display the footers */
			Tpl::Display ("nav_user_foot.tpl");
			
		}
	
	}

}

?>