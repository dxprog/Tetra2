<?php

/***************************************************
* Tetra                                            *
* Copyright (c) 2004-2005 Matt "dxprog" Hackmann   *
* More copyright info can be found in              *
* license.txt                                      *
***************************************************/

/********************************************
* Tetra Core                                *
* Version 1.0                               *
********************************************/

/* Tetra class */
class Tetra {

	/* Tetra version */
	var $tetra_ver = 0.7;

	/**********************************************
	* Function: GeneratePage                      *
	* Description: Generates the page content     *
	**********************************************/
	function GeneratePage ()
	{
	
		/* Get the configuration and core variables */
		global $s_DbName, $b_Caching, $a_Modules, $a_User, $a_HostInfo, $start_time, $s_ThemeDir, $s_DefaultPage, $b_Theme, $b_Preprocess;
		
		/* Error handling stuff */
		global $b_Err, $s_ErrMsg, $i_ErrNum, $s_ErrModule;
		
		/* Get the current time (this is used to calculate page generation time */
		$i_Start = GetTime ();
		
		/* Begin preprocess phase */
		$b_Preprocess = true;
		
		/* Connect to the database */
		DB::db_Connect ();

		/* Initialize the session */
		$s_Key = Session::Initialize ();
		
		/* If no particular page was requested, goto home by default */
		if (!$_GET["main"])
			$_GET["main"] = "news";

		/* Get the installed modules */
		$s_Query = "SELECT * FROM modules WHERE module_parent > 0 ORDER BY module_parent ASC";
		$h_Result = DB::db_Query ($s_Query);
		
		/* Go through the modules */
		for ($i = 0; $i < $h_Result["num_rows"]; $i++) {
			
			/* Get the module data */
			$a_Data = DB::db_Array ($h_Result);
			
			/* Include the module API and instantiate the class */
			include ("./api/modules/$a_Data[module_api]");
			$a_Modules[strtolower ($a_Data["module_class"])] = new $a_Data["module_class"];
		}

		/* Make $main available to the templates */
		Tpl::Assign ("main", $_GET["main"]);

		/* Run any pretheme processes (generally user related stuff) */
		if ($_GET["preprocess"]) {

			/* Make sure the module exists */
			if (!$a_Modules[$_GET["preprocess"]])
				/* Set main to the preprocess module so that the proper error message will be displayed */
				$_GET["main"] = $_GET["preprocess"];
			else
				/* Run the request handler */
				$a_Modules[$_GET["preprocess"]]->HandleRequest ($_GET["action"]);

		}

		/* Update the template variables */
		Tpl::Assign ("user", $a_User);
		Tpl::Assign ("site", $a_HostInfo);
		Tpl::Assign ("page", $_GET["main"]);
		Tpl::Assign ("style", "./templates/".$a_User["user_theme"]."/styles/".$a_User["user_style"]);
		
		/* Generate the page title */
		if (!isset ($a_Modules[$_GET["main"]]))
			Tpl::Assign ("page_title", $a_HostInfo["SiteName"]);
		else { 
			if ($a_Modules[$_GET["main"]]->module_title)
				Tpl::Assign ("page_title", $a_HostInfo["SiteName"].": ".$a_Modules[$_GET["main"]]->TetraHandler (T_REQUEST_TITLE, $_GET["action"]));
		}

		/* End preprocess phase */
		$b_Preprocess = false;
		
		/* Turn on output buffering */
		ob_start ();

		/* See how much header info needs to be sent */
		switch ($_GET["headers"]) {
		case "none":
			/* Only display the module's output. */
			if (!$a_Modules[$_GET["main"]])
				Err::Raise ("The page \"".$_GET["main"]."\" could not be found!", E_TETRA_CRITICAL, "Tetra Core");
			else
				$a_Modules[$_GET["main"]]->HandleRequest ($_GET["action"]);
				
			break;
		case "basic":
			/* Only display basic headers and skip layout parsing */
			Tpl::Display ("basic_head.tpl");
			if (!$a_Modules[$_GET["main"]])
				Err::Raise ("The page \"".$_GET["main"]."\" could not be found!", E_TETRA_CRITICAL, "Tetra Core");
			else
				$a_Modules[$_GET["main"]]->HandleRequest ($_GET["action"]);
			Tpl::Display ("basic_foot.tpl");
			break;
		default:
			/* Display everything in the layout file */
			
			/* Display the page header */
			Tpl::Display ("page_head.tpl");
			
			/* Display the nav bar */
			$a_Modules["nav"]->HandleRequest ("nav");
			
			/* Parse the layout file */
			$a_Theme = Layout::ParseLayout ();			
			
			/* Set the themed flag to true */
			$b_Theme = true;
			
			/* If there is now theme data automatically show what's in main */
			if (!$a_Theme) { 
				$a_Theme[0][0] = T_MODULE;
				$a_Theme[0][1] = "main";
			}
			
			/* Loop through the layout file */
			for ($i = 0; $i < sizeof ($a_Theme); $i++) {
				
				/* Display what's needed for this element */
				switch ($a_Theme[$i][0]) {
				case T_TEMPLATE:
					
					/* Open up a new box for the template */
					Tpl::Display ("content_basic_head.tpl");
					
					/* Display the template */
					Tpl::Display ($a_Theme[$i][1].".tpl");

					/* If the user is an admin show the layout box */
					if ($a_User["user_rank"] == 3) {
						Tpl::Assign ("block_id", $a_Theme[$i][2]);
						Tpl::Display ("admin_layout.tpl");
					}

					/* Close up the box */
					Tpl::Display ("content_basic_foot.tpl");

					break;
				
				case T_MODULE:

					/* If the content type was "main" use $_GET["main"] for a module reference */
					if ($a_Theme[$i][1] == "main") {
						/* If there was a preprocess error, display that first */
						if ($b_Err)
							Err::Raise ($s_ErrMsg, $i_ErrNum, $s_ErrModule);
							
						/* Make sure the module exists, first */
						if (!isset ($a_Modules[$_GET["main"]]))
							Err::Raise ("The page \"".$_GET["main"]."\" could not be found!", E_TETRA_CRITICAL, "Tetra Core");
						else
							$a_Modules[$_GET["main"]]->HandleRequest ($_GET["action"]);							
					}
					/* Otherwise use the module name from the layout file */
					else {
						/* Split the module name from the parameters */
						$a_Temp = explode ("|", $a_Theme[$i][1]);
						
						/* Make sure the module exists, first */
						if (!isset ($a_Modules[$a_Temp[0]]))
							Err::Raise ("The page \"".$a_Temp[1]."\" could not be found!", E_TETRA_CRITICAL, "Tetra Core");
						else
							$a_Modules[$a_Temp[0]]->HandleRequest ($a_Temp[1]);
					}

					break;
				case T_COLUMN:
					/* Display the column template */
					Tpl::Assign ("col_width", $a_Theme[$i][1]);
					Tpl::Assign ("col_id", $a_Theme[$i][2]);
					Tpl::Display ("page_divider.tpl");
					break;
				}
				
			}

			/* Display the page footer */
			Tpl::Display ("page_foot.tpl");

		}

		/* Print out the page generation time and copyright info */
		$i_GenTime = GetTime () - $i_Start;
		echo ("<!-- GENERATION TIME: $i_GenTime SECONDS -->");
		echo (TETRA_COPYRIGHT);
		
		/* Release the dam (send the data in the buffer to the browser)! */
		ob_end_flush ();
		
	}
	
	/**********************************************
	* Function: GetModule                         *
	* Description: Returns a handle to a module   *
	**********************************************/
	function GetModule ($s_Name)
	{
	
		/* Get the module array */
		global $a_Modules;
		
		/* Convert the string to lower case */
		$s_Name = strtolower ($s_Name);
	
		/* Make sure the module exists */
		if (!$a_Modules[$s_Name])
			return false;
		else
			return $a_Modules[$s_Name];
	
	}
}