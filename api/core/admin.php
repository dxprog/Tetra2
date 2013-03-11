<?php

/********************************************
* Tetra                                     *
* Copyright (c) 2004 Matt "dxprog" Hackmann *
* More copyright info can be found in       *
* license.txt                               *
********************************************/

/********************************************
* Tetra Module/Theme Installation Module    *
* Version 0.8                               *
********************************************/

class Admin {

	/* Module info */
	var $module_name = "Tetra Module/Theme Installation Module";
	var $module_ver = 0.1;

	/******************************************************
	* Function: HandleRequest                             *
	* Description: Handles page requests                  *
	******************************************************/
	function HandleRequest ($s_Options)
	{
	
		/* Get the user array */
		global $a_User;
		
		/* Make sure this user is an admin */
		if ($a_User["user_rank"] < 3) {
			Err::Raise ("Only admins can view this page!", E_TETRA_USER, "Admin", "Page accessed by ".$a_User["user_name"]);
			return false;
		}
		
		/* Now that the user has been verified, continue on to see what needs to be done */
		switch ($s_Options) {
		case "module_form":
			Tpl::Header ("Install Module");
			Tpl::Display ("admin_module_install.tpl");
			Tpl::Footer ();
			break;
		case "install":
			$this->InstallModule ();
			break;
		case "logo":
			Tpl::Header ("Upload Logo");
			Tpl::Display ("admin_logo.tpl");
			Tpl::Footer ();
			break;
		case "upload_logo":
			$this->Logo ();
			break;
		case "nav_form":
			$this->NavForm ();
			break;
		case "update_nav":
			$this->UpdateNav ();
			break;
		default:
			Tpl::Header ("Administrative Tools");
			Tpl::Display ("admin_nav.tpl");
			Tpl::Footer ();
			break;
		}
	
	}
	
	/******************************************************
	* Function: InstallModule                             *
	* Description: Installs a Tetra module                *
	******************************************************/
	function InstallModule ()
	{
		
		/* Open the file */
		$h_Zip = zip_open ($_FILES["package"]["tmp_name"]);
		
		/* Make sure it opened okay before continuing */
		if (!$h_Zip) {
			/* Delete the file */
			unlink ("./docs/".$_FILES["package"]["name"]);
			
			/* Display the error message and quit */
			Err::Raise ("The file loaded could not be opened. The file may be corrupt.", E_TETRA_CRITICAL, "Admin");
			return false;
		}
		
		/* Run through the zip file and uncompress everything */
		while ($h_ZipFile = zip_read ($h_Zip)) {
		
			/* Check to see if the file entry can be opened */
			if (!zip_entry_open ($h_Zip, $h_ZipFile, "rb")) {
				Err::Raise ("The file \"".zip_entry_name ($h_ZipFile)."\" could not be opened. The zip file may be corrupt!", E_TETRA_CRITICAL, "Admin");
				zip_close ($h_Zip);
			}
			
			/* Add this file to an array. We'll use this to keep track of what was created so it can be cleaned up
			   later */
			$a_Files[] = zip_entry_name ($h_ZipFile);
			
			/* Now save the contents */
			$h_File = fopen ("./docs/".zip_entry_name ($h_ZipFile), "wb");
			fwrite ($h_File, zip_entry_read ($h_ZipFile, zip_entry_filesize ($h_ZipFile)));
			fclose ($h_File);
			
			/* Close the zip entry */
			zip_entry_close ($h_ZipFile);
		
		}
		
		/* Now that we're done with the zip file, close and delete it */
		zip_close ($h_Zip);
		unlink ($_FILES["package"]["tmp_name"]);
		
		/* Load up the contents of the config file */
		if (!($a_File = file ("./docs/module.conf"))) {
			/* There was no config file so this can't be a Tetra package */
			Err::Raise ("This is not a Tetra package!", E_TETRA_CRITICAL, "Admin");
			
			/* Delete all the files that were unzipped */
			for ($i = 0; $i < sizeof ($a_Files); $i++);
				@unlink ("./docs/".$a_Files[$i]);
				
			/* Quit */
			return false;
		}
		
		/* Parse the config file */
		for ($i = 0; $i < sizeof ($a_File); $i++) {
		
			/* Trim the leading and ending spaces */
			$a_File[$i] = trim ($a_File[$i]);
		
			/* Check to see if this is a section header and if it is set the phase state */
			if ($a_File[$i] == "[Module]") {
				$s_Phase = "module";
				continue;
			}
			elseif ($a_File[$i] == "[Files]") {
				$s_Phase = "files";
				continue;
			}
			elseif ($a_File[$i] == "[Database]") {
				$s_Phase = "db";
				continue;
			}

			/* Seperate the caption from the data */
			$a_Temp = explode ("=", $a_File[$i]);
			
			/* Decide how to interpret the data depending on what phase we're in */
			switch ($s_Phase) {
			case "module":
				
				/* Set the various module infos */
				if ($a_Temp[0] == "Name")
					$s_ModuleName = $a_Temp[1];
				elseif ($a_Temp[0] == "API")
					$s_ModuleAPI = $a_Temp[1];
				elseif ($a_Temp[0] == "Class")
					$s_ModuleClass = $a_Temp[1];
				
				break;
			
			case "files":
				
				
				/* Make sure the file exists before attempting to move it */
				if (!file_exists ("./docs/".$a_Temp[0])) {
					Err::Raise ("There was an error moving file \"".$a_Temp[0]."\"", E_TETRA_CRITICAL, "Admin");
					
					/* Delete all the files that were unzipped */
					for ($i = 0; $i < sizeof ($a_Files); $i++)
						@unlink ("./docs/".$a_Files[$i]);
					
					return false;
				}
				
				/* Delete any existing file with this name */
				@unlink ($a_Temp[1]."/".$a_Temp[0]);
				
				/* Move the file to its new location */
				rename ("./docs/".$a_Temp[0], $a_Temp[1]."/".$a_Temp[0]);
				break;
			
			case "db":
			
				/* If this is a DB dump, dump it */
				if ($a_Temp[0] == "Dump") {
					/* Make sure the file exists */
					if (!file_exists ("./docs/".$a_Temp[1])) {
						Err::Raise ("The database file \"".$a_Temp[1]."\" does not exists!", E_TETRA_CRITICAL, "Admin");
						
						/* Delete all the files that were unzipped */
						for ($i = 0; $i < sizeof ($a_Files); $i++)
							@unlink ("./docs/".$a_Files[$i]);
						
						return false;
					}
					
					/* Dump the file */
					DB::db_Dump (file ("./docs/".$a_Temp[1]));
				}
				break;
			}
		
		}
		
		/* Create the database entry for the module */
		$s_Query = "INSERT INTO modules (module_name, module_parent, module_api, module_class) VALUES ('$s_ModuleName', '1', '$s_ModuleAPI', '$s_ModuleClass')";
		DB::db_Query ($s_Query);

		/* Delete all the files that were unzipped */
		for ($i = 0; $i < sizeof ($a_Files); $i++)
			@unlink ("./docs/".$a_Files[$i]);
		
		/* Let the user know everything went okay */
		Err::Message ("Module \"$s_ModuleName\" installed!", "The module \"$s_ModuleName\" has been installed successfuly!");
		
	}

	/******************************************************
	* Function: Logo                                      *
	* Description: Uploads a logo picture                 *
	******************************************************/
	function Logo ()
	{
		
		/* Delete any old logo that may be present */
		@unlink ("./docs/logo.png");
		
		/* Move the uploaded file */
		move_uploaded_file ($_FILES["pic"]["tmp_name"], "./docs/logo.png");
	
	}
	
	/******************************************************
	* Function: NavForm                                   *
	* Description: Displays a list of nav options         *
	******************************************************/
	function NavForm ()
	{
	
		/* Get the modules array */
		global $a_Modules;
		
		/* Display the header */
		Tpl::Display ("admin_nav_head.tpl");
		
		/* Get all the nav items */
		foreach ($a_Modules as $s_Name=>$s_Module) {
		
			/* See if there are any nav items to be had */
			if ($s_Module->module_nav_items) {
			
				/* Get the items */
				$a_Items = $s_Module->TetraHandler (T_REQUEST_NAV_ITEMS, "");
				
				/* Show the section header */
				Tpl::Assign ("module_name", $s_Module->module_name);
				Tpl::Display ("admin_nav_module.tpl");
				
				/* Display them */
				foreach ($a_Items as $s_Caption=>$s_Url) {
				
					Tpl::Assign ("caption", $s_Caption);
					Tpl::Assign ("url", $s_Url);
					Tpl::Display ("admin_nav_mod_item.tpl");
				
				}
			}
		
		}
		
		Tpl::Display ("admin_nav_remove.tpl");
		
		/* Get all the current nav items */
		$s_Query = "SELECT * FROM navigation";
		$h_Result = DB::db_Query ($s_Query);
		
		/* Loop through them and display them */
		for ($i = 0; $i < $h_Result["num_rows"]; $i++) {
		
			/* Get the info and display it */
			$a_Data = DB::db_Array ($h_Result);
			Tpl::Assign ("id", $a_Data["nav_id"]);
			Tpl::Assign ("caption", $a_Data["nav_caption"]);
			Tpl::Display ("admin_nav_rem_item.tpl");
		
		}
		
		/* Show the footer */
		Tpl::Display ("admin_nav_foot.tpl");
	
	}
	
	/******************************************************
	* Function: UpdateNav                                 *
	* Description: Updates the navigation stuff           *
	******************************************************/
	function UpdateNav ()
	{
	
		/* If the custom fields were set add that first */
		if ($_GET["caption"] && $_GET["url"]) {
			$s_Query = "INSERT INTO navigation (nav_caption, nav_href) VALUES ('".addslashes ($_GET["caption"])."', '".$_GET["url"]."')";
			DB::db_Query ($s_Query);
		}
		
		/* Run through module stuff */
		for ($i = 0; $i < $_POST["NumItems"] + 1; $i++) {
		
			/* If this item is checked add it */
			if ($_POST["nav$i"]) {
				$a_Temp = explode ("|", $_POST["nav$i"]);
				$s_Query = "INSERT INTO navigation (nav_caption, nav_href) VALUES ('".addslashes ($a_Temp[0])."', '".$a_Temp[1]."')";
				DB::db_Query ($s_Query);
			}
		
		}
		
		/* Delete any checked delete items */
		for ($i = 0; $i < $_POST["NumRems"] + 1; $i++) {
		
			/* If this item is checked add it */
			if ($_POST["rem$i"]) {
				$s_Query = "DELETE FROM navigation WHERE nav_id='".$_POST["rem$i"]."'";
				DB::db_Query ($s_Query);
			}
		
		}		
	
		$_GET["action"] = "";
	
	}
	
}

?>