<?php

/********************************************
* Tetra                                     *
* Copyright (c) 2004 Matt "dxprog" Hackmann *
* More copyright info can be found in       *
* license.txt                               *
********************************************/

/********************************************
* Tetra Error Module                        *
* Version 0.1                               *
********************************************/

/* Define the error constants */
define ("E_TETRA_CRITICAL", 1);
define ("E_TETRA_TEMPLATE", 2);
define ("E_TETRA_DATABASE", 3);
define ("E_TETRA_USER", 4);
define ("E_TETRA_FORM", 5);
define ("E_TETRA_GENERAL", 6);

/* An array of the error type descriptions */
$a_ErrorType = array (E_TETRA_CRITICAL=>"Critical Error", 
					  E_TETRA_TEMPLATE=>"Template Error",
					  E_TETRA_DATABASE=>"Database Error", 
					  E_TETRA_USER=>"User Error",
					  E_TETRA_FORM=>"Form Error",
					  E_TETRA_GENERAL=>"General Error");

class Err {

	/**********************************************
	* Function: Raise                             *
	* Description: Displays the error message and *
	*              saves it to the log            *
	**********************************************/
	function Raise ($s_ErrorMsg, $i_ErrorNum, $s_Module, $s_Extra = "")
	{
	
		/* Get the user array, templating object, theme flag and error type array */
		global $a_User, $b_Theme, $a_ErrorType, $b_Err, $b_Preprocess, $s_ErrMsg, $i_ErrNum, $s_ErrModule;
		
		/* If we're still in preprocess phase save the info for when the theme comes into play */
		if ($b_Preprocess) {
			/* Raise the error flag */
			$b_Err = true;
			
			/* Save the info */
			$s_ErrMsg = $s_ErrorMsg;
			$i_ErrNum = $i_ErrorNum;
			$s_ErrModule = $s_Module;
			return false;
		}
		
		/* Get the date */
		$s_Date = date ("n-j-y g:iA");
		
		/* Pull together the error string for the log */
		$s_Error =  "\r\n====================================================================";
		$s_Error .= "\r\nDate: ".$s_Date;
		$s_Error .= "\r\nError type: ".$a_ErrorType[$i_ErrorNum];
		$s_Error .= "\r\n--------------------------------------------------------------------";
		$s_Error .= "\r\nError message: ".$s_ErrorMsg;
		$s_Error .= "\r\nExtra info: ".$s_Extra;
		$s_Error .= "\r\nModule: ".$s_Module;
		$s_Error .= "\r\nAction: ".$_GET["action"];
		$s_Error .= "\r\nUser: ".$a_User["user_name"];
		$s_Error .= "\r\n====================================================================";
		
		/* Write the error to the log */
		$h_File = fopen ("./config/errorlog.txt", "a");
		fwrite ($h_File, $s_Error);
		fclose ($h_File);
		
		/* Display a message for the user */
		if ($b_Theme && $i_ErrorNum != E_TETRA_TEMPLATE) {
			Tpl::Assign ("title", "Tetra Core Error");
			Tpl::Assign ("message", "Tetra experienced a problem while generating this page. The adminsitrators have been notified.<br><br><b>Error message</b><br><hr><b>".$a_ErrorType[$i_ErrorNum]."</b>: ".$s_ErrorMsg);
			Tpl::Display ("message.tpl");
		}
		else {
			$s_Out = "\n<table width=\"50%\" style=\"border: 1px solid #000000; background-color: #FFFFFF; font-family: Sans-serif; font-size: 12px;\">\n";
			$s_Out .= "<tr><td style=\"background-color: #EEEEEE; font-family: Sans-serif; font-size: 12px; font-weight: bold; color: #000000;\" align=\"center\">Tetra Core Error</td></tr>\n";
			$s_Out .= "<tr><td>Tetra experienced a problem while generating this page. The adminsitrators have been notified.<br><br></td></tr>\n";
			$s_Out .= "<tr><td><b>".$a_ErrorType[$i_ErrorNum]."</b>: ".$s_ErrorMsg."</td></tr>\n";
			$s_Out .= "</table><br>\n";
			echo ($s_Out);
		}
	
	}
	
	/* Display a message using the message template */
	function Message ($title, $message)
	{
		Tpl::Assign ("title", $title);
		Tpl::Assign ("message", $message);
		Tpl::Display ("message.tpl");
	}

}