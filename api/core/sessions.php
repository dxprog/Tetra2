<?php

/********************************************
* Tetra                                     *
* Copyright (c) 2004 Matt "dxprog" Hackmann *
* More copyright info can be found in       *
* license.txt                               *
********************************************/

/********************************************
* Sessions handler for Tetra                *
* Version 2.0                               *
********************************************/

/* All the session data is stored in this array */
$_SESS = "";

/* Life of the session (in seconds) */
$i_Expires = 86400;

class Session extends Tetra {

	/* Module version */
	var $module_ver = 2.0;

	/*************************************
	* Function:    Initialize            *
	* Parameters:  none                  *
	* Description: Initializes a session *
	*************************************/
	function Initialize ()
	{
	
		/* Get the session life */
		global $i_Expires, $a_User;
		
		/* Get rid of old sessions */
		$s_Query = "DELETE FROM sessions WHERE sess_expires < ".time ();
		DB::db_Query ($s_Query);

		/* Check to see if a previous session exists */
		if (!$_COOKIE["sess_key"]) {
		
			/* Generate a session key by md5ing the current time and a random number */
			$s_Key = md5 (time () + (rand () % 999999));

			/* Write the key to a cookie */
			setcookie ("sess_key", $s_Key);

			/* Read the cookie data */
			Session::Read ($s_Key);
			
			/* Return the key */
			return $s_Key;
			
		}
		else {
			
			/* Read the cache data */
			Session::Read ($_COOKIE["sess_key"]);
			
			/* Update the session expiration */
			if ($a_User["user_id"] > 1) {
				Session::Update ($_COOKIE["sess_key"]);
				setcookie ("sess_key", $_COOKIE["sess_key"], (time () + $i_Expires));
			}
			
			/* Since the session already exists, just return the key 
			   from the cookie */
			return $_COOKIE["sess_key"];
		}
	
	}
	
	
	/*************************************
	* Function:    Read                  *
	* Parameters:  none                  *
	* Description: Reads session data    *
	*************************************/
	function Read($s_Key)
	{
	
		/* Globals we need */
		global $_SESS, $a_User;
		
		/* See if there is session info stored in the database */
		$s_Query = "SELECT * FROM sessions WHERE sess_key='$s_Key'";

		/* Execute the query */
		$h_Result = DB::db_Query ($s_Query);

		/* Is there a db entry for this session? */
		if ($h_Result["num_rows"] == 0) {
			
			/* If there weren't any sessions, create one */
			$s_Query = "INSERT INTO sessions VALUES ('$s_Key', 1, ".(time () + $i_Expires).")";
			DB::db_Query ($s_Query);
			
			/* Put the info in the session array */
			$_SESS = array ("sess_key"=>$s_Key, "sess_uid"=>1, "sess_expires"=>(time () + $i_Expires));
			
		}
		else {
		
			/* Get session info from database */
			$_SESS = DB::db_Array ($h_Result);
		
		}
			
		/* Get the info from the database */
		$s_Query = "SELECT * FROM users WHERE user_id=".$_SESS["sess_uid"];
		$h_Result = DB::db_Query ($s_Query);
		
		/* Store the info in the user array */
		$a_User = DB::db_Array ($h_Result);
		$a_User["user_ip"] = gethostbyaddr ($_SERVER["REMOTE_ADDR"]);
		
		/* If the user isn't guest, update their last login time and IP */
		if ($a_User["user_id"] > 1) {
			$s_Query = "UPDATE users SET user_lastlogin=".time ().", user_ip='".gethostbyaddr ($_SERVER["REMOTE_ADDR"])."' WHERE user_id=$a_User[user_id]";
			DB::db_Query ($s_Query);			
		}

	}
	
	/*************************************
	* Function:    Update                *
	* Parameters:  none                  *
	* Description: Update session data   *
	*************************************/
	function Update ($s_Key)
	{
	
		/* Globals */
		global $_SESS, $i_Expires;
		
		/* Prepare a query */
		$s_Query = "UPDATE sessions SET sess_uid=".$_SESS["sess_uid"].", sess_expires=".(time () + $i_Expires)." WHERE sess_key='$s_Key'";
		
		/* Execute the query */
		DB::db_Query ($s_Query);
		
		/* Read the new data */
		Session::Read ($s_Key);
	
	}

}

?>