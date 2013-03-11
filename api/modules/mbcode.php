<?php

/********************************************
* Tetra                                     *
* Copyright (c) 2004 Matt "dxprog" Hackmann *
* More copyright info can be found in       *
* license.txt                               *
********************************************/

/********************************************
* Tetra MB CodeModule                       *
* Also has censor and smiley converter      *
* Version 0.4                               *
********************************************/

class MBCode {

	/* Module info */
	var $module_ver = 1.0;
	var $module_name = "Tetra MB MBCode Module";
	
	/**********************************************
	* Function: Censor                            *
	* Parameters: $s_String - String to censor    *
	* Description: Censors profanity from the     *
	*              string passed                  *
	**********************************************/
	function Censor ($s_String)
	{
		

	
	}
	
	/**********************************************
	* Function: ConvertMBCode                     *
	* Parameters: $s_String - String to convert   *
	* Description: Converts MB code tags to HTML  *
	**********************************************/
	function ConvertMBCode ($s_String)
	{
	
		/* Array of strings to find */
		$s_Preg = array ("/\[i\](.*?)\[\/i\]/is", 
		                 "/\[b\](.*?)\[\/b\]/is", 
		                 "/\[u\](.*?)\[\/u\]/is",
		                 "/\[code\](.*?)\[\/code\]/is", 
		                 "/\[quote\](.*?)\[\/quote\]/is",
		          	 	 "/\[link=http:\/\/(.*?)\](.*?)\[\/link\]/is",
		                 "/\[quote=(.*?)\](.*?)\[\/quote\]/is",
		                 "/\[color=#(.*?)\](.*?)\[\/color\]/is");
		
		/* Array of replacement strings */
		$s_Replace = array ("<i>\$1</i>", 
		                    "<b>\$1</b>", 
		                    "<u>\$1</u>",
		                    "<br><pre class=\"code\">\$1</pre>", 
		                    "<br><table class=\"Quote\"><tr><td>\$1</td></tr></table>",
		                    "<a href=\"http://\$1\" target=\"_blank\" class=\"Link\">\$2</a>",
		                    "<br><table class=\"Quote\" width=\"100%\"><tr><td><b>\$1 wrote:</b><br>\$2</td></tr></table>",
		                    "<span style=\"color: #\$1;\">\$2</span>");

		/* Do the conversion */
		$s_Temp = preg_replace ($s_Preg, $s_Replace, $s_String);
		
		/* If the string was changed, do another conversion to catch nested tags */
		if ($s_Temp != $s_String)
			$s_Temp = MBCode::ConvertMBCode ($s_Temp);
			
		/* Return the completed product */
		return $s_Temp;
	
	}

	/**********************************************
	* Function: ConvertSmileys                    *
	* Parameters: $s_Text - String to convert     *
	* Description: Converts smielys to images     *
	**********************************************/
	function ConvertSmileys($s_Text)
	{

		/* Get the templating object */
		global $Templating;
		
		/* Replace smileys with the IMG tags */
		$s_Text = str_replace (":-)", "<img src=\"./templates/generic/styles/images/smileys/smile.png\" alt=\":-)\">", $s_Text);
		$s_Text = str_replace (":-@", "<img src=\"./templates/generic/styles/images/smileys/mad.png\" alt=\":-@\">", $s_Text);
		$s_Text = str_replace (":-(", "<img src=\"./templates/generic/styles/images/smileys/sad.png\" alt=\":-(\">", $s_Text);
		$s_Text = str_replace (":-|", "<img src=\"./templates/generic/styles/images/smileys/neutral.png\" alt=\":-|\">", $s_Text);
		$s_Text = str_replace (":-D", "<img src=\"./templates/generic/styles/images/smileys/grin.png\" alt=\":-D\">", $s_Text);
		$s_Text = str_replace (";-)", "<img src=\"./templates/generic/styles/images/smileys/wink.png\" alt=\";-)\">", $s_Text);
		$s_Text = str_replace ("8-)", "<img src=\"./templates/generic/styles/images/smileys/cool.png\" alt=\"8-)\">", $s_Text);
		$s_Text = str_replace (":-P", "<img src=\"./templates/generic/styles/images/smileys/razz.png\" alt=\":-P\">", $s_Text);
		$s_Text = str_replace (":-/", "<img src=\"./templates/generic/styles/images/smileys/vague.png\" alt=\":-/\">", $s_Text);
		$s_Text = str_replace (":-O", "<img src=\"./templates/generic/styles/images/smileys/surprised.png\" alt=\":-O\">", $s_Text);
		$s_Text = str_replace ("&lt;&gt;&lt;", "<img src=\"./templates/generic/styles/images/smileys/fish.png\" alt=\"Fish!\">", $s_Text);
		
		/* Return the string */
		return $s_Text;

	}

}