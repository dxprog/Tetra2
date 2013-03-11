<?php

/********************************************
* Tetra                                     *
* Copyright (c) 2004 Matt "dxprog" Hackmann *
* More copyright info can be found in       *
* license.txt                               *
********************************************/

/*************************************
* db_mysql.php                       *
* ---------------------------------- *
* Functions for MySQL db support     *
*************************************/

/* DB module name */
$db_name = "mysql";
$db_description = "MySQL 4.x";

/* Connection the the MySQL server */
$mysql_conn = "";

/*************************************
* db_Setup()                         *
* ---------------------------------- *
* Shows the setup form (for initial  *
* Tetra setup)                       *
*************************************/
class DB {
	function db_Setup ()
	{

		/* This is the only time you'll see HTML inside a Tetra module. The reason is because
		   the templating object hasn't been included and this is the only way to get it to the 
		   browser */
		echo ("<form action=\"./index.php?step=4\" method=\"post\">\n<table  style=\"border: 1px solid #000000; background-color: #FFFFFF; font-family: Sans-serif; font-size: 12px;\">\n");
		echo ("<tr><td style=\"background-color: #EEEEEE; font-family: Sans-serif; font-size: 12px; font-weight: bold; color: #000000;\" colspan=\"2\" align=\"center\"><b>Database Information</b></td></tr>\n");
		echo ("<tr><td align=\"center\" colspan=\"2\"><b>MySQL Configuration</b></td></tr>\n");
		echo ("<tr><td>Host name:</td><td><input name=\"mysql_host\"></td></tr>\n");
		echo ("<tr><td>User name:</td><td><input name=\"mysql_user\"></td></tr>\n");
		echo ("<tr><td>Password:</td><td><input name=\"mysql_pass\" type=\"password\"></td></tr>\n");
		echo ("<tr><td>Database:</td><td><input name=\"mysql_db\"></td></tr>\n");
		echo ("<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"Next >>\"></td></tr>\n");
		echo ("</table></form>\n");

	}

	/*************************************
	* db_Connect()                       *
	* ---------------------------------- *
	* Creates a connection to the MySQL  *
	* server.                            *
	*************************************/
	function db_Connect()
	{
		// Get the required info for db connection
		global $mysql_host, $mysql_user, $mysql_pass, $mysql_conn, $mysql_db;

		if (func_num_args () == 0)
			$db = $mysql_db;
		else
			$db = func_get_arg (0);

		// Connect to the mysql server
		$mysql_conn = @mysql_pconnect ($mysql_host, $mysql_user, $mysql_pass);

		// If the link was established, connect to the database
		if ($mysql_conn)
			@mysql_select_db ($db, $mysql_conn);
		else {
			// If there was an error, display it and quit
			Err::Raise ("Couldn't connect to MySQL server!", E_TETRA_DATABASE, "DB", DB::db_Error ());
			exit ();
		}

		// Make sure that the connection to the database was successful
		if (DB::db_Error()) {
			Err::Raise ("Couldn't connect to MySQL database!", E_TETRA_DATABASE, "DB", DB::db_Error ());
			exit ();
		}

	}

	/*************************************
	* db_Error()                         *
	* ---------------------------------- *
	* Returns error string for last      *
	* operation.                         *
	*************************************/
	function db_Error()
	{
		// Return the error string
		return mysql_error ();
	}

	/*************************************
	* db_Query()                         *
	* ---------------------------------- *
	* Executes the SQL query passed      *
	*************************************/
	function db_Query ($query)
	{

		global $mysql_conn, $MYSQL_FAIL, $MYSQL_SUCCESS;

		// If a blank query string is passed, return fail
		if ($query == "")
			return $MYSQL_FAIL;

		// Exectue the query and get the auto_increment id (we'll get 0 if it isn't an INSERT statement) */
		$result = mysql_query ($query);
		$i_ID = mysql_insert_id ();

		// See if there were any errors
		if (DB::db_Error ()) {
			Err::Raise ("There was an error executing an SQL statement!", E_TETRA_DATABASE, "DB", DB::db_Error ()."\r\n            ".$query);
			// Return that the query failed
			return 1;
		}

		// If the SQL statement passed was a SELECT statement, return the resource ID and
		// number of rows
		if (substr (strtolower ($query), 0, 6) == "select") {
			// Get the number of rows returned
			$num_rows = mysql_num_rows ($result);

			// Put everthing in an array and return it
			$ret = array ("result"=>$result, "num_rows"=>$num_rows);
			return $ret;
		}

		// Return the ID
		return $i_ID;

	}

	/*************************************
	* db_Array()                         *
	* ---------------------------------- *
	* Returns an array of fields from    *
	* the MySQL resource provided        *
	*************************************/
	function db_Array ($result)
	{

		global $MYSQL_FAIL;

		// If the resource provided is blank, display an error and return fail
		if ($result["result"] == "") {
			Err::Raise ("An invalid database resource was passed!", E_TETRA_DATABASE, "DB");
			return 1;
		}

		$ret = @mysql_fetch_array ($result["result"]);

		// If there was an error retrieving the array, display the error message and return fail
		if (DB::db_Error ()) {
			Err::Raise ("Error retrieving information from database!", E_TETRA_DATABASE, "DB", DB::db_Error ());
			return 1;
		}
		
		// If the array isn't blank strip the slashes out of each item
		if (sizeof ($ret) > 1) {
			foreach ($ret as $s_Key=>$s_Val)
				$ret[$s_Key] = stripslashes ($s_Val);
		}

		// Everything was successful, return the array
		return $ret;

	}


	/*************************************
	* db_Close()                         *
	* ---------------------------------- *
	* Dummy function. Only exists so     *
	* there isn't an error when calling  *
	* this function.                     *
	*************************************/
	function db_Close() {}

	/*************************************
	* db_Count()                         *
	* ---------------------------------- *
	* Returns the number of records in a *
	* table. An array of fields can also *
	* be passed for a specific search    *
	*************************************/
	function db_Count ($s_Table, $a_Options = false)
	{

		/* Prep the base query */
		$s_Query = "SELECT count(*) AS value FROM $s_Table";

		/* See if any options where passed */
		if ($a_Options) {
			/* Add "WHERE" onto the query */
			$s_Query .= " WHERE";

			/* Loop through them and add to the base query */
			foreach ($a_Options as $s_Key=>$s_Value) {
				$s_Query .= " $s_Key='$s_Value' AND";
			}

			$s_Query .= " 1";

		}

		/* Run the query and retrieve the results */
		$a_Row = DB::db_Array (DB::db_Query ($s_Query));

		/* Return the value */
		return $a_Row["value"];

	}

	/*************************************
	* db_Dump()                          *
	* ---------------------------------- *
	* Executes multiple queries seperated*
	* by semi-colons                     *
	*************************************/
	function db_Dump ($s_Dump)
	{

		/* Boil the array down to a string */
		$s_Dump = implode ("\n", $s_Dump);

		/* Now split it up by the command line delimeter (;) */
		$a_Dump = explode (";", trim ($s_Dump));

		/* Loop through the array and execute the commands */
		for ($i = 0; $i < sizeof ($a_Dump); $i++) {
			if (DB::db_Query ($a_Dump[$i])) {
				die ("There was an error dumping the queries!");
			}
		}

	}
}
?>