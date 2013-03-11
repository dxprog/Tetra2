<?php
/********************************************
* Tetra                                     *
* Copyright (c) 2004 Matt "dxprog" Hackmann *
* More copyright info can be found in       *
* license.txt                               *
********************************************/

/***************************
*        Constants         *
***************************/
define ("T_COLUMN", 1);
define ("T_MODULE", 2);
define ("T_TEMPLATE", 3);

/********************************************
* Tetra Layout Manager                      *
* Version 0.8                               *
********************************************/
class Layout {
	
	/******************************************
	* Function:    ParseLayout                *
	* Parameters:  none                       *
	* Description: Parses the layout          *
	******************************************/
	function ParseLayout ()
	{
	
		/* Get the user array */
		global $a_User;
		
		/* Get the columns from the layout table */
		$s_Query = "SELECT layout_id, layout_data FROM layout WHERE layout_type='".T_COLUMN."' AND layout_user='".$a_User["user_id"]."' ORDER BY layout_position ASC";
		$h_Result = DB::db_Query ($s_Query);
		
		/* Loop through each column and get it's children */
		for ($i = 0; $i < $h_Result["num_rows"]; $i++) {			
			
			/* Get the column info */
			$a_Data = DB::db_Array ($h_Result);

			/* Add the column to the array */
			$a_Theme[sizeof ($a_Theme)][0] = T_COLUMN;
			$a_Theme[sizeof ($a_Theme) - 1][1] = $a_Data["layout_data"];
			$a_Theme[sizeof ($a_Theme) - 1][2] = $a_Data["layout_id"];
			
			/* Get the children of this module */
			$s_Query = "SELECT * FROM layout WHERE layout_parent='".$a_Data["layout_id"]."' ORDER BY layout_position ASC";
			$h_Temp = DB::db_Query ($s_Query);
			
			/* Loop through those and stuff them into the array */
			for ($j = 0; $j < $h_Temp["num_rows"]; $j++) {
				
				/* Get the info */
				$a_Temp = DB::db_Array ($h_Temp);
				
				/* Add it to the array */
				$a_Theme[sizeof ($a_Theme)][0] = $a_Temp["layout_type"];
				$a_Theme[sizeof ($a_Theme) - 1][1] = $a_Temp["layout_data"];
				$a_Theme[sizeof ($a_Theme) - 1][2] = $a_Temp["layout_id"];
				
			}
			
		}
		
		/* Return the array */
		return $a_Theme;
	
	}
	
	/******************************************
	* Function:    ShowLayout                 *
	* Description: Displays the layout in     *
	*              edit form                  *
	******************************************/
	function ShowLayout ()
	{
	
		/* Get the user array */
		global $a_User, $a_Modules;
		
		/* Get the user's layout */
		$a_Layout = Layout::ParseLayout ();
		
		/* Display the header */
		Tpl::Display ("layout_head.tpl");
		
		/* Okay, this is an oddity. Due to the complexity of this particular setup this is the only time you're
		   going to find HTML inside a Tetra module (with the exception of error reporting). We'll write the 
		   opening table */
		echo ("<table style=\"width: 532px; height: 400px; border: 1px solid #000000; background-color: #FFFFFF\">\n\t<tr>\n\t\t<td>");
		
		/* If there is no layout just show the main box */
		if (!$a_Layout)
			echo ("\n\t\t</td>\n\t</tr>\n\t<tr>\n\t<td align=\"center\" style=\"border: 1px solid #000000;\">\n\t\tMain\n\t</td>\n</tr>");
		else {
		
			/* Loop through the items and display them */
			for ($i = 0; $i < sizeof ($a_Layout); $i++) {
				
				/* See if this object is selected */
				if ($_GET["id"] == $a_Layout[$i][2]) {
					$i_SelID = $a_Layout[$i][2];
					$i_SelType = $a_Layout[$i][0];
					$s_BgColor = "EEEEEE";
				}
				else
					$s_BgColor = "FFFFFF";
				
				/* Decide what to do */
				switch ($a_Layout[$i][0]) {
				case T_COLUMN:
					/* Start a new column */
					echo ("\n\t\t</td>\n\t\t<td style=\"background-color: #$s_BgColor; padding: 2px; border: 1px solid #000000;\" align=\"center\" valign=\"top\" style=\"width: ".$a_Layout[$i][1].";\"><a href=\"./index.php?main=users&amp;action=layout&amp;id=".$a_Layout[$i][2]."\">Select Column</a><br>");
					break;
				case T_MODULE:
					/* Seperate the module from the sidebar name */
					echo ("\n\t\t\t<table style=\"width: 100%; height: 100px; border: 1xp solid #000000; background-color: #$s_BgColor;\">\n\t\t\t\t<tr>\n\t\t\t\t\t<td align=\"center\">\n\t\t\t\t\t\t<a href=\"./index.php?main=users&amp;action=layout&amp;id=".$a_Layout[$i][2]."\">".$a_Layout[$i][1]."</a>\n\t\t\t\t\t</td>\t\t\t\t</tr>\t\t\t</table>");
					break;
				}
				
			}
		
		}
		
		/* Display the footer */
		Tpl::Assign ("sel_id", $i_SelID);
		Tpl::Assign ("sel_type", $i_SelType);
		echo ("\n\t\t</td>\n\t</tr>\n</table>");
		Tpl::Display ("layout_foot.tpl");
	
	}
	
	/******************************************
	* Function:    EditLayout                 *
	* Description: Figures out where we are   *
	*              in the editing stage       *
	******************************************/
	function EditLayout ()
	{
	
		/* Figure up what's going on */
		switch ($_GET["layout"]) {
		case "create_column":
			Tpl::Display ("layout_create_column.tpl");
			break;
		case "add_col":
			Layout::AddColumn ();
			break;
		case "move":
			Layout::Move ();
			break;
		case "move_col":
			Layout::MoveColumn ();
			break;
		case "add_form":
			Layout::AddForm ();
			break;
		case "add":
			Layout::Add ();
			break;
		case "delete":
			Layout::Delete ();
			break;
		default:
			Layout::ShowLayout ();
			break;
		}
	
		$_GET["layout"] = "";
	
	}

	/******************************************
	* Function:    AddColumn                  *
	* Description: Creates a new column       *
	******************************************/
	function AddColumn ()
	{
	
		/* Get the user array */
		global $a_User;
		
		/* Get the layout array */
		$a_Layout = Layout::ParseLayout ();
		
		/* Get the position of the column farthest to the right */
		$s_Query = "SELECT layout_position FROM layout WHERE layout_type='1' AND layout_user='".$a_User["user_id"]."' ORDER BY layout_position DESC LIMIT 1";
		$a_Temp = DB::db_Array (DB::db_Query ($s_Query));
		
		/* Create the column */
		$i_Col = Layout::AddItem ($a_Temp["layout_position"] + 1, T_COLUMN, $_POST["width"]);

		/* If there's nothing in the layout add main */
		if (!$a_Layout)
			Layout::AddItem (1, T_MODULE, "main", $i_Col);
	
	}
	
	/******************************************
	* Function:    AddItem                    *
	* Description: Adds a layout item         *
	******************************************/
	function AddItem ($i_Position, $i_Type, $s_Data, $i_Parent = "")
	{
	
		/* Get the user array */
		global $a_User;
		
		/* Build and run the query */
		$s_Query = "INSERT INTO layout (layout_position, layout_type, layout_data, layout_parent, layout_user) VALUES ('$i_Position', '$i_Type', '$s_Data', '$i_Parent', '".$a_User["user_id"]."')";
		return DB::db_Query ($s_Query);
	
	}
	
	/******************************************
	* Function:    GetItem                    *
	* Description: Gets a layout item         *
	******************************************/
	function GetItem ($i_ID)
	{
	
		/* This is so simple it needs no comment :-P */
		$s_Query = "SELECT * FROM layout WHERE layout_id='$i_ID'";
		return DB::db_Array (DB::db_Query ($s_Query));
	
	}
	
	/******************************************
	* Function:    Move                       *
	* Description: Moves a content box        *
	******************************************/
	function Move ()
	{
	
		/* Get the user array */
		global $a_User;
		
		/* See which way we're moving */
		switch ($_GET["dir"]) {
		case "left":
			/* Get info on this box and its column */
			$a_Box = Layout::GetItem ($_GET["id"]);
			$a_Col = Layout::GetItem ($a_Box["layout_parent"]);

			/* If this is already the leftmost column don't do anything */
			if ($a_Col["layout_position"] == 1)
				return false;
			
			/* Get the ID of the column to the left of the current one */
			$s_Query = "SELECT layout_id FROM layout WHERE layout_position < '".$a_Col["layout_position"]."' AND layout_user='".$a_User["user_id"]."' AND layout_type='".T_COLUMN."' ORDER BY layout_position DESC LIMIT 1";
			$a_New = DB::db_Array (DB::db_Query ($s_Query));
			
			/* Bump any boxes in the new column that are in the way */
			$s_Query = "UPDATE layout SET layout_position = layout_position + 1 WHERE layout_user='".$a_User["user_id"]."' AND layout_position >= '".$a_Box["layout_position"]."' AND layout_parent='".$a_New["layout_id"]."' AND layout_type > '".T_COLUMN."'";
			DB::db_Query ($s_Query);
			
			/* Move any boxes below the box in the column up */
			$s_Query = "UPDATE layout SET layout_position = layout_position - 1 WHERE layout_user='".$a_User["user_id"]."' AND layout_position > '".$a_Box["layout_position"]."' AND layout_parent='".$a_Col["layout_id"]."' AND layout_type > '".T_COLUMN."'";
			DB::db_Query ($s_Query);
			
			/* Move the box over */
			$s_Query = "UPDATE layout SET layout_parent='".$a_New["layout_id"]."' WHERE layout_id='".$_GET["id"]."' AND layout_user='".$a_User["user_id"]."'";
			DB::db_Query ($s_Query);
			break;
		case "right":
			/* Get info on this box and its column */
			$a_Box = Layout::GetItem ($_GET["id"]);
			$a_Col = Layout::GetItem ($a_Box["layout_parent"]);

			/* Get the ID of the rightmost column */
			$s_Query = "SELECT layout_position FROM layout WHERE layout_user='".$a_User["user_id"]."' AND layout_type='".T_COLUMN."' ORDER BY layout_position DESC LIMIT 1";
			$a_Temp = DB::db_Array (DB::db_Query ($s_Query));
			
			/* If this is already the leftmost column don't do anything */
			if ($a_Col["layout_position"] == $a_Temp["layout_position"])
				return false;
			
			/* Get the ID of the column to the left of the current one */
			$s_Query = "SELECT layout_id FROM layout WHERE layout_position > '".$a_Col["layout_position"]."' AND layout_user='".$a_User["user_id"]."' AND layout_type='".T_COLUMN."' LIMIT 1";
			$a_New = DB::db_Array (DB::db_Query ($s_Query));
			
			/* Bump any boxes in the new column that are in the way */
			$s_Query = "UPDATE layout SET layout_position = layout_position + 1 WHERE layout_user='".$a_User["user_id"]."' AND layout_position >= '".$a_Box["layout_position"]."' AND layout_parent='".$a_New["layout_id"]."' AND layout_type > '".T_COLUMN."'";
			DB::db_Query ($s_Query);
			
			/* Move any boxes below the box in the column up */
			$s_Query = "UPDATE layout SET layout_position = layout_position - 1 WHERE layout_user='".$a_User["user_id"]."' AND layout_position > '".$a_Box["layout_position"]."' AND layout_parent='".$a_Col["layout_id"]."' AND layout_type > '".T_COLUMN."'";
			DB::db_Query ($s_Query);
			
			/* Move the box over */
			$s_Query = "UPDATE layout SET layout_parent='".$a_New["layout_id"]."' WHERE layout_id='".$_GET["id"]."' AND layout_user='".$a_User["user_id"]."'";
			DB::db_Query ($s_Query);
			break;
		case "up":
			
			/* Get the info on this item */
			$a_Box = Layout::GetItem ($_GET["id"]);
			
			/* If this is already in the topmost position don't bother with anything */
			if ($a_Box["layout_position"] == 1)
				return false;
			
			/* Move the box above down */
			$s_Query = "UPDATE layout SET layout_position = layout_position + 1 WHERE layout_user='".$a_User["user_id"]."' AND layout_parent='".$a_Box["layout_parent"]."' AND layout_position='".($a_Box["layout_position"] - 1)."'";
			DB::db_Query ($s_Query);
			
			/* Bump the selected box up */
			$s_Query = "UPDATE layout SET layout_position = layout_position - 1 WHERE layout_id='".$a_Box["layout_id"]."'";
			DB::db_Query ($s_Query);
			break;
		
		case "down":
			
			/* Get the info on this item */
			$a_Box = Layout::GetItem ($_GET["id"]);
			
			/* Get the position of the lowermost box */
			$s_Query = "SELECT layout_position FROM layout WHERE layout_parent='".$a_Box["layout_parent"]."' ORDER BY layout_position DESC LIMIT 1";
			$a_Temp = DB::db_Array (DB::db_Query ($s_Query));
			
			/* If this is already in the topmost position don't bother with anything */
			if ($a_Box["layout_position"] == $a_Temp["layout_position"])
				return false;
			
			/* Move the box below up */
			$s_Query = "UPDATE layout SET layout_position = layout_position - 1 WHERE layout_user='".$a_User["user_id"]."' AND layout_parent='".$a_Box["layout_parent"]."' AND layout_position='".($a_Box["layout_position"] + 1)."'";
			DB::db_Query ($s_Query);
			
			/* Bump the selected box down */
			$s_Query = "UPDATE layout SET layout_position = layout_position + 1 WHERE layout_id='".$a_Box["layout_id"]."'";
			DB::db_Query ($s_Query);
			break;
			
		}
		
	}

	/******************************************
	* Function:    MoveColumn                 *
	* Description: Moves a column             *
	******************************************/
	function MoveColumn ()
	{
	
		/* Get the user array */
		global $a_User;
	
		/* See which way we're going */
		switch ($_GET["dir"]) {
		case "left":
			
			/* Get info on the column */
			$a_Col = Layout::GetItem ($_GET["id"]);
			
			/* If this column is already all the way to the left don't do anything */
			if ($a_Col["layout_position"] == 1)
				return false;
			
			/* Move the column to the left right one */
			$s_Query = "UPDATE layout SET layout_position='".$a_Col["layout_position"]."' WHERE layout_user='".$a_User["user_id"]."' AND layout_position='".($a_Col["layout_position"] - 1)."' AND layout_type='".T_COLUMN."'";
			DB::db_Query ($s_Query);
			
			/* Move the selected column left */
			$s_Query = "UPDATE layout SET layout_position='".($a_Col["layout_position"] - 1)."' WHERE layout_id='".$_GET["id"]."'";
			DB::db_Query ($s_Query);
			break;
		case "right":
			/* Get info on the column */
			$a_Col = Layout::GetItem ($_GET["id"]);
			
			/* Get the position of the last column */
			$s_Query = "SELECT layout_position FROM layout WHERE layout_type='".T_COLUMN."' AND layout_user='".$a_User["user_id"]."' ORDER BY layout_position DESC LIMIT 1";
			$a_Temp = DB::db_Array (DB::db_Query ($s_Query));
			
			/* If this column is already all the way to the right don't do anything */
			if ($a_Col["layout_position"] == $a_Temp["layout_position"])
				return false;
			
			/* Move the column to the right left one */
			$s_Query = "UPDATE layout SET layout_position='".$a_Col["layout_position"]."' WHERE layout_user='".$a_User["user_id"]."' AND layout_position='".($a_Col["layout_position"] + 1)."' AND layout_type='".T_COLUMN."'";
			DB::db_Query ($s_Query);
			
			/* Move the selected column right */
			$s_Query = "UPDATE layout SET layout_position='".($a_Col["layout_position"] + 1)."' WHERE layout_id='".$_GET["id"]."'";
			DB::db_Query ($s_Query);
			break;
		}
	
	}
	
	/******************************************
	* Function:    AddItem                    *
	* Description: Shows a list of content    *
	*              sidebars                   *
	******************************************/
	function AddForm ()
	{
	
		/* Get the module array */
		global $a_Modules;
		
		/* Run through modules and create a list of sidebars */
		foreach ($a_Modules as $s_Key=>$s_Module) {
			/* If this has sidebars loop through them and add to the list */
			if ($s_Module->module_sidebars) {
				$a_Bars = $s_Module->TetraHandler (T_REQUEST_SIDEBARS, "");
				foreach ($a_Bars as $s_Name=>$s_Value)
					$s_Out .= "<option value=\"$s_Value\">$s_Name</option>\n";
			}
		
		}
		
		/* Show the form */
		Tpl::Assign ("id", $_GET["id"]);
		Tpl::Assign ("options", $s_Out);
		Tpl::Display ("layout_add_form.tpl");
	
	}
	
	/******************************************
	* Function:    Add                        *
	* Description: Adds content to a column   *
	******************************************/
	function Add ()
	{
	
		/* Get the user array */
		global $a_User;
		
		/* Get the position of the last item in the column */
		$s_Query = "SELECT layout_position FROM layout WHERE layout_parent='".$_GET["id"]."' ORDER BY layout_position DESC LIMIT 1";
		$a_Temp = DB::db_Array (DB::db_Query ($s_Query));
		
		/* Add the item */
		$s_Query = "INSERT INTO layout (layout_user, layout_parent, layout_data, layout_position, layout_type) VALUES ('".$a_User["user_id"]."', '".$_GET["id"]."', '".$_POST["content"]."', '".($a_Temp["layout_position"] + 1)."', '".T_MODULE."')";
		DB::db_Query ($s_Query);
	
	}
	
	/******************************************
	* Function:    CopyLayout                 *
	* Description: Copies one user layout to  *
	*              another                    *
	******************************************/
	function CopyLayout ($i_Src, $i_Dest)
	{
	
		/* Get all the columns */
		$s_Query = "SELECT * FROM layout WHERE layout_type='".T_COLUMN."' AND layout_user='$i_Src'";
		$h_Result = DB::db_Query ($s_Query);
		
		for ($i = 0; $i < $h_Result["num_rows"]; $i++) {
		
			/* Get the info */
			$a_Data = DB::db_Array ($h_Result);
			
			/* Stuff this column into dest user's layout. We'll store the IDs into an array (the key being the
			   source user's column ID) so that we can properly map that child items to their new parents */
			$s_Query = "INSERT INTO layout (layout_user, layout_type, layout_data, layout_position) VALUES ('$i_Dest', '".$a_Data["layout_type"]."', '".$a_Data["layout_data"]."', '".$a_Data["layout_position"]."')";
			$a_Cols[$a_Data["layout_id"]] = DB::db_Query ($s_Query);
		
		}
		
		/* Now get all the child items */
		$s_Query = "SELECT * FROM layout WHERE layout_type != '".T_COLUMN."' AND layout_user='$i_Src'";
		$h_Result = DB::db_Query ($s_Query);
		
		for ($i = 0; $i < $h_Result["num_rows"]; $i++) {
		
			/* Get the info */
			$a_Data = DB::db_Array ($h_Result);
			
			/* Save the item with it's new parent ID */
			$s_Query = "INSERT INTO layout (layout_user, layout_parent, layout_type, layout_position, layout_data) VALUES ('$i_Dest', '".$a_Cols[$a_Data["layout_parent"]]."', '".$a_Data["layout_type"]."', '".$a_Data["layout_position"]."', '".$a_Data["layout_data"]."')";
			DB::db_Query ($s_Query);
		
		}
	
	}
	
	/******************************************
	* Function:    Delete                     *
	* Description: Deletes a column/box       *
	******************************************/
	function Delete ()
	{
	
		/* Get the user array */
		global $a_User;
		
		/* Make sure main isn't parented to this */
		if (DB::db_Count ("layout", array ("layout_user"=>$a_User["user_id"], "layout_data"=>"main")) == 1) {
			Err::Raise ("You cannot delete this column because the main content is displayed here. Please move \"main\" before continuing.", E_TETRA_GENERAL, "layout");
			return false;
		}
	
		/* Get item info */
		$a_Data = Layout::GetItem ($_GET["id"]);
		
		/* If this _is_ main, don't let them do it! */
		if ($a_Data["layout_data"] == "main") {
			Err::Raise ("You cannot delete this!", E_TETRA_GENERAL, "layout");
			return false;
		}
	
		/* Now that we know it's safe, delete this and all children */
		$s_Query = "DELETE FROM layout WHERE layout_user='".$a_User["user_id"]."' AND (layout_id='".$_GET["id"]."' OR (layout_parent='".$_GET["id"]."' AND layout_parent > '1'))";
		DB::db_Query ($s_Query);
		
		/* Move anything positionally after this up/left */
		$s_Query = "UPDATE layout SET layout_position = layout_position - 1 WHERE layout_position > '".$a_Data["layout_position"]."' AND layout_user='".$a_User["user_id"]."' AND layout_parent='".$a_Data["layout_parent"]."'";
		DB::db_Query ($s_Query);
	
	}
	
}
?>