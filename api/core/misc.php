<?php

/********************************************
* Tetra                                     *
* Copyright (c) 2004 Matt "dxprog" Hackmann *
* More copyright info can be found in       *
* license.txt                               *
********************************************/

/********************************************
* Miscellaneous functions that don't belong *
* anywhere else :-P                         *
********************************************/

/* Makes a date using the users preferences */
function make_date ($n_Time)
{

	global $a_User;
	
	return @gmdate ($a_User["user_tf"]." \G\M\T", $n_Time);

}

/* Taken from the PHP manual */
function GetTime(){ 
   list($usec, $sec) = explode(" ",microtime()); 
   return ((float)$usec + (float)$sec); 
} 


?>