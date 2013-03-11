<?php

/********************************************
* Tetra                                     *
* Copyright (c) 2004 Matt "dxprog" Hackmann *
* More copyright info can be found in       *
* license.txt                               *
********************************************/

/********************************************
* Caching module for Tetra                  *
* Version 1.0                               *
********************************************/

	
/************************************
*             Globals               *
************************************/

/* Location of cache files */
$s_CacheDir = "./cache";

/* MD5 cache files */
$b_Md5Cache = true;


/************************************
* Cache Class                       *
************************************/
class Cache extends Tetra {

	/* Cache version */
	var $module_ver = 1.0;
	
	/******************************************
	* Function:    Cached                     *
	* Parameters:  $s_Name - Cache name       *
	* Description: Checks to see if $s_Name   *
	*              is cached                  *
	******************************************/
	function Cached ($s_Name)
	{
	
		/* Get some globals */
		global $b_Caching, $b_Md5Cache, $s_CacheDir;
	
		/* If caching is disabled return false */
		if (!$b_Caching)
			return false;
		
		/* If MD5ing is enabled, MD5 $s_Name */
		if ($b_Md5Cache)
			$s_Name = md5 ($s_Name);
		
		/* We determine whether or not a cached version of $s_Name exists by
		   checking for its cache file */
		if (file_exists ($s_CacheDir."/$s_Name"))
			/* If the file exists the page has been cached so return true */
			return true;
		else
			/* Otherwise return false */
			return false;
	
	}
	
	/******************************************
	* Function:   WriteCache                  *
	* Parameters: $s_Name - Cache name        *
	*             $s_Value - Value to be      *
	*                        written          *
	* Description: Writes $s_Value to $s_Name *
	******************************************/
	function Write ($s_Name, $s_Value)
	{
	
		/* Globals we need */
		global $b_Caching, $b_Md5Cache, $s_CacheDir;
		
		/* If caching is disabled, return false */
		if (!$b_Caching)
			return false;
		
		/* If MD5 is turned on, MD5 $s_Name */
		if ($b_Md5Cache)
			$s_Name = md5 ($s_Name);
		
		/* Set $s_ToWrite (the value that will be writtin) to $s_Value */
		$s_ToWrite = $s_Value;
		
		/* Check to see if the value is an array */
		if (is_array ($s_Value)) {
			
			/* Blank out the "to write" variable */
			$s_ToWrite = "";
			
			/* Loop through the values preparing a string to be cached */
			foreach ($s_Value as $s_Key => $s_Val)
				/* Add the key and value to the string */
				$s_ToWrite .= "$s_Key=>$s_Val|c|";

		}

		/* Replace new lines in the value */
		$s_ToWrite = str_replace ("\r\n", "|n|", $s_ToWrite);
		$s_ToWrite = str_replace ("\n", "|n|", $s_ToWrite);
		$s_ToWrite = str_replace ("\r", "|n|", $s_ToWrite);

		/* If the data is an array, make a note of it */
		if (is_array ($s_Value))
			$s_ToWrite = "!array!\r".$s_ToWrite;
		
		/* Open up the cache file */
		if (!($h_F = fopen ($s_CacheDir."/$s_Name", "wb"))) {
			/* If there was an error, display an error message and exit */
			echo ("<br /><b>Cache error:</b> Error opening cache file \"<i>".$this->cache_location."/$s_Name</i>\"");
			/* Return false */
			return false;
		}
		
		/* Write the value */
		fwrite ($h_F, $s_ToWrite);
		
		/* Close the file */
		fclose ($h_F);
		
		/* Return true */
		return true;
	
	}
	
	/******************************************
	* Function:   Read                        *
	* Parameters: $s_Name - Cache name        *
	* Description: Reads data from $s_Name    *
	******************************************/
	function Read ($s_Name, $b_Quiet = false) {
	
		/* Get our globals */
		global $b_Caching, $b_Md5Cache, $s_CacheDir;
		
		/* If caching is disabled return false */
		if (!$b_Caching)
			return false;
			
		/* If MD5 is enabled, MD5 $s_Name */
		if ($b_Md5Cache)
			$s_Name = md5 ($s_Name);
		
		/* Make sure the file exists */
		if (!file_exists ($s_CacheDir."/$s_Name") && !$b_Quiet) {
			/* Display an error message */
			echo ("<br /><b>Cache error:</b>File doesn't exist \"<i>".$s_CacheDir."/$s_Name</i>\"");
			/* The file doesn't exist so return false */
			return false;
		}
		
		/* Open the cache file */
		if (!($h_F = fopen ($s_CacheDir."/$s_Name", "rb"))) {
			/* There was an error opening the file, so display an error
			   message and quit */
			echo ("<br /><b>Cache error:</b>Couldn't open file \"<i>".$s_CacheDir."/$s_Name</i>\"");
			return false;
		}
		
		/* Read the cache data */
		$s_Data = @fread ($h_F, filesize ($s_CacheDir."/$s_Name"));
		
		/* Close the file */
		fclose ($h_F);
		
		/* Update the return variable */
		$s_Ret = $s_Data;
		
		/* See if the data is an array */
		if (substr ($s_Ret, 0, 8) == "!array!\r") {
			
			/* Take the "$array$\r" out */
			$s_Data = substr ($s_Data, 8, strlen ($s_Data));
			
			/* Split the data up by \r (the array delimeter) */
			$s_Data = explode ("|c|", $s_Data);
			
			/* Blank out the return variable */
			$s_Ret = "";
			
			/* Loop through the data and recontruct the array */
			for ($i = 0; $i < sizeof ($s_Data); $i++) {
			
				/* Break the data up into key and value */
				$s_Temp = explode ("=>", $s_Data[$i]);
				
				/* Add the data to the stuff to be returned */
				$s_Ret[$s_Temp[0]] = str_replace ("|n|", "\n", $s_Temp[1]);
			}
		}
		
		/* Return the data */
		return $s_Ret;
			
	
	}

	/******************************************
	* Function:   DeleteCache                 *
	* Parameters: $s_Name - Cache name        *
	* Description: Deletes a cache file       *
	******************************************/
	function Delete ($s_Name)
	{
	
		/* Just some stuff */
		global $b_Caching, $b_Md5Cache, $s_CacheDir;
	
		/* See if we're using md5'd names */
		if ($b_Md5Cache)
			$s_Name = md5 ($s_Name);
		
		/* Delete the cache file */
		@unlink ($s_CacheDir."/$s_Name");
	
	}

}

?>