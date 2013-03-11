<?php

/********************************************
* Tetra                                     *
* Copyright (c) 2004 Matt "dxprog" Hackmann *
* More copyright info can be found in       *
* license.txt                               *
********************************************/

/********************************************
* Templating engine for Tetra               *
* Version 2.0                               *
********************************************/

/****************************
*          Globals          *
****************************/

/* Directory where the templates are kept */
$s_TemplateDir = "./templates";

/* The theme directory */
$s_ThemeDir = ".";

/* Compile directory */
$s_CompileDir = "./cache";

/* Stylesheet URL */
$s_Style = "";

/****************************
* Templating Class          *
****************************/
class Tpl {

	/* Module info */
	var $module_name = "Tetra Templating";
	var $module_ver = 2.0;

	/***************************************************
	* Function:    Display                             *
	* Parameters:  $s_FileName - Template file to      *
	*              include                             *
	* Description: Compiles and/or displays the        *
	*              template                            *
	***************************************************/
	function Display ($s_FileName)
	{
	
		/* Get the settings */
		global $a_User;

		/* Attach the proper directories to the filename */
		$s_File = $s_FileName;
		$s_FileName = "./templates/".$a_User["user_theme"]."/".$s_FileName;
		
		/* Make sure the template file exists */
		if (!file_exists ($s_FileName)) {
			/* Check to see if there's a genric template */
			if (!file_exists ("./templates/generic/".$s_File)) {
				Err::Raise ("Template doesn't exist!", E_TETRA_TEMPLATE, "Templating", $s_File);
				return false;
			}
			else {
				$s_FileName = "./templates/generic/".$s_File;
			}
		}
			
		/* Check to see if a compiled version of this file already exists or of the template is newer */
		if (file_exists ($s_CompileDir."/".md5($s_FileName).".php") && filemtime ($s_CompileDir."/".md5($s_FileName).".php") > filemtime ($s_FileName))
			$s_File = $s_CompileDir."/".md5($s_FileName).".php";
		else
			/* Otherwise, compile the file */
			$s_File = Tpl::Compile ($s_FileName);	
		
		/* Include the compiled template */
		include ($s_File);
	
	}

	/***************************************************
	* Function:    Header                              *
	* Parameters:  $s_Title - Title for the content    *
	*                         header                   *
	* Description: Displays the content header         *
	***************************************************/
	function Header ($s_Title = "")
	{
	
		/* Display the content header */
		Tpl::Assign ("content_title", $s_Title);
		Tpl::Display ("content_head.tpl");
	
	}

	/***************************************************
	* Function:    Footer                              *
	* Description: Displays the content footer         *
	***************************************************/
	function Footer ()
	{
	
		/* Display the content footer */
		Tpl::Display ("content_foot.tpl");
	
	}

	/***************************************************
	* Function:    Compile                             *
	* Parameters:  $s_FileName - Template file to      *
	*              compile                             *
	* Description: Compiles the template               *
	***************************************************/
	function Compile ($s_FileName)
	{

		/* Get the compile directory */
		global $s_CompileDir;
		
		/* Open the file */
		$h_File = fopen ($s_FileName, "r");

		/* Read the contents of the file */
		$s_File = fread ($h_File, filesize ($s_FileName));

		/* Replace the PHP tags and add new lines to the beginning and ends of the file so that it is clean when all
		   put together */
		$s_File = "\n<!-- $s_FileName -->\n".Tpl::RemovePHP ($s_File)."\n";

		/* Make the templating object and variable array global to this template */
		$s_File = "\n<?php global \$a_Variables; ?>\n".$s_File;

		/* Process for-next loops */
		$s_File = Tpl::ProcessLoops ($s_File, $s_FileName);

		/* Process if statements */
		$s_File = Tpl::ProcessIfs ($s_File, $s_FileName);
		
		/* Do variable replacement */
		$s_File = Tpl::ProcessVariables ($s_File, $s_FileName);
		
		/* Write the info to a temp file */
		$h_Temp = fopen ($s_CompileDir."/".md5 ($s_FileName).".php", "w");
		fwrite ($h_Temp, $s_File);
		fclose ($h_Temp);
		
		/* Return the file name */
		return ($s_CompileDir."/".md5($s_FileName).".php");

	}

	/***************************************************
	* Function:    ProcessIfs                          *
	* Parameters:  $s_String - The template data to    *
	*                          compile                 *
	*              $s_FileName - Name of the template  *
	*                            file                  *
	* Description: Converts template if statements to  *
	*              PHP if statements                   *
	***************************************************/
	function ProcessIfs ($s_String, $s_FileName)
	{

		/* Get the array of variables */
		global $a_Variables;

		/* Get the numer of <if>s and </if>s */
		$n_NumIfs = substr_count ($s_String, "<if:");
		$n_NumEndIfs = substr_count ($s_String, "<end:if>");

		/* If there are no if statements, jump out */
		if ($n_NumIfs == 0)
			return $s_String;

		/* If there were more <if>s than </if>s, display an error message */
		if ($n_NumIfs > $n_NumEndIfs)
			die ("<b>Templating Error ($s_FileName):</b> &lt;if&gt; without &lt;/if&gt;");

		/* Check for the opposite */
		if ($n_NumIfs < $n_NumEndIfs)
			die ("<b>Templating Error ($s_FileName):</b> &lt;/if&gt; without &lt;if&gt;");

		/* Loop through each if statement and replace it with a proper PHP if statement */
		while (($n_If = strpos ($s_String, "<if:", $n_EndIf)) !== false) {

			/* Get the position of the next > (end of the if statement) */
			$n_EndIf = strpos ($s_String, ">", $n_If);

			/* Get the guts of the if statement */
			$s_If = substr ($s_String, $n_If + 4, ($n_EndIf - ($n_If + 4)));

			/* Replace =, ands, ors, gt (>) and lt (<) with proper PHP syntax */
			$s_If = str_replace (" = ", " == ", $s_If);
			$s_If = str_replace (" and ", " && ", $s_If);
			$s_If = str_replace (" or ", " || ", $s_If);
			$s_If = str_replace (" gt ", " > ", $s_If);
			$s_If = str_replace (" lt ", " < ", $s_If);

			/* Break the if string up for variable replacement */
			$a_If = explode (" ", $s_If);

			/* We'll use this to keep track of the position in the array */
			$i = 0;

			/* Loop through each item and check for a variable (anything that's not a number or operator) */
			foreach ($a_If as $s_IfItem) {

				/* If this isn't a number or an operator then run a variable replacement on it */
				if ($s_IfItem != "||" && $s_IfItem != "&&" && $s_IfItem != "!=" && $s_IfItem != "==" && !is_numeric($s_IfItem) && $s_IfItem != "<" && $s_IfItem != ">") {

					/* If the value is not numeric and isn't already in quotes, it's a variable. Treat it as such */
					if (!is_numeric ($s_IfItem) && substr ($s_IfItem, 0, 1) != "\"" && substr ($s_IfItem, strlen ($s_IfItem) - 1, 1) != "\"")
						$a_If[$i] = "\$a_Variables[\"".$s_IfItem."\"]";

					/* Change the value if this is a variable */
					if (Tpl::VariableSet ($s_IfItem))
						$a_If[$i] = "\$a_Variables[\"$s_IfItem\"]"; // $a_Variables[$s_IfItem];			

				}

				/* Increment the array index pointer */
				$i++;

			}

			/* Add PHP tags and if statement */
			$s_If = "<?php if (".implode (" ", $a_If).") { ?>";

			/* Replace the template if statement with the PHP one */
			$s_String = substr_replace ($s_String, $s_If, $n_If, ($n_EndIf - $n_If) + 1);

		}

		/* Replace the end:ifs with the closing PHP tags. Elso replace <else> with the proper PHP equiv */
		$s_String = str_replace ("<end:if>", "<?php } ?>", $s_String);
		$s_String = str_replace ("<else>", "<?php } else { ?>", $s_String);

		/* Return the result */
		return $s_String;

	}

	/***************************************************
	* Function:    ProcessLoops                        *
	* Parameters:  $s_String - The template data to    *
	*                          compile                 *
	*              $s_FileName - Name of the template  *
	*                            file                  *
	* Description: Converts template loops to PHP      *
	*              loops                               *
	***************************************************/
	function ProcessLoops ($s_String, $s_FileName)
	{

		/* Get the variable array */
		global $a_Variables;
		
		$a_ForVars = false;
		
		/* Get a count of how many fors and nexts there are */
		$n_NumFor = substr_count ($s_String, "<for:");
		$n_NumNext = substr_count ($s_String, "<next:for>");

		/* If there are no fors, just jump out and save clock cycles */
		if ($n_NumFor == 0)
			return $s_String;

		/* If the number of nexts and fors are different, display an error and stop execution */
		if ($n_NumFor > $n_NumNext)
			die ("<b>Template Error ($s_FileName):</b> For without next!");
		if ($n_NumFor < $n_NumNext)
			die ("<b>Template Error ($s_FileName):</b> Next without for!");

		/* Loop through each for and convert to PHP syntax */
		while (($n_For = strpos ($s_String, "<for:", $n_EndFor)) !== false) {

			/* Get the position of the end of the statement */
			$n_EndFor = strpos ($s_String, ">", $n_For);

			/* Get the guts of the for statement */
			$s_For = substr ($s_String, $n_For + 5, ($n_EndFor - ($n_For + 5)));

			/* Turn the for statement into an array of parameters */
			$a_For = explode (" ", $s_For);

			/* Make sure this loop variable isn't already taken */
			if (isset ($a_ForVars["$a_For[0]"])) {
				Err::Raise ("Loop variable '$a_For[0]' in use!", E_TETRA_TEMPLATE, "Templating", $s_FileName);
				return false;
			}
			else
				$a_ForVars["$a_For[0]"] = true;

			/* Add the loop variable to the template variables */
			Tpl::Assign ($a_For[0], "NULL");

			/* Check to see if the beginning variable is a template variable */
			if (!is_numeric ($a_For[2])) {
				
				/* Replace the value */
				$a_For[2] = "\$a_Variables[\"$a_For[2]\"]";
			}

			/* Check to see if the end variable is a template variable */
			if (!is_numeric ($a_For[4])) {
				
				/* Replace the value */
				$a_For[4] = "\$a_Variables[\"$a_For[4]\"]";
			}

			/* Setup the beginning of the for statement and loop variable  */
			$s_For = "<?php for (\$".$a_For[0]." = ".$a_For[2]."; \$".$a_For[0]." < ".$a_For[4]." + 1; \$".$a_For[0]."++) { ?>";

			/* Set up the code to alter the template variable of this loop */
			$s_For .= "<?php Tpl::Assign(\"$a_For[0]\", \$".$a_For[0]."); ?>";

			/* Replace the old for statement with the new */
			$s_String = substr_replace ($s_String, $s_For, $n_For, ($n_EndFor - $n_For) + 1);
			
			/* Replace the next:for with the closing PHP tag and a line of code to delete the loop
			   variable so it can be reused */
			$n_Next = strpos ($s_String, "<next:for>", $n_EndFor);
			$s_String = substr_replace ($s_String, "<?php } ?>", $n_Next, 10);

			/* Replace any uses of the loop variable with something that'll reflect the proper value */
			$s_String = str_replace ("<var:$a_For[0]>", "<?php echo (\$$a_For[0]); ?>", $s_String);		

		}
		
		/* Return the compiled code */
		return $s_String;

	}

	/***************************************************
	* Function:    ProcessVariables                    *
	* Parameters:  $s_String - The template data to    *
	*                          compile                 *
	* Description: Replaces template variables with    *
	*              assigned values and also does       *
	*              variable setting                    *
	***************************************************/
	function ProcessVariables ($s_String, $s_FileName)
	{
	
		/* Get the variable array */
		global $a_Variables;
		
		/* Check to see if there are any set statments */
		$i_NumSets = substr_count ($s_String, "<set:");
		
		/* If there aren't any return, skip this part */
		if ($i_NumSets != 0) {
		
			/* Loop through each statement */
			while (($i_Set = strpos ($s_String, "<set:", $i_End)) !== false) {

				/* Get the end of the statement */
				$i_End = strpos ($s_String, ">", $i_Set);

				/* Get the contents of the statement */
				$s_Set = substr ($s_String, $i_Set + 5, ($i_End - ($i_Set + 5)));

				/* Break the pieces up into an array */
				$a_Set = explode (" ", $s_Set);

				/* Make sure the array has five elements. No more, no less. */
				if (sizeof ($a_Set) != 5) {
					Err::Raise ("Invalid number of elements in set statement: $s_Set", E_TETRA_TEMPLATE, "Templating", $s_FileName);
					return false;
				}

				/* Go through each element (except 1 and 3 which are operators) and replace any variables */
				for ($i = 0; $i < sizeof ($a_Set); $i++) {
					/* If this is one of the operators, skip it */
					if ($i != 1 && $i != 3) {

						/* If the item isn't numeric replace it with a template variable */
						if (!is_numeric ($a_Set[$i])) {
							$a_Set[$i] = "\$a_Variables[\"".$a_Set[$i]."\"]";
						}

					}
				}

				/* Make sure the second element is an equals sign */
				if ($a_Set[1] != "=") {
					Err::Raise ("Equals symbol missing: $s_Set", E_TETRA_TEMPLATE, "Templating", $s_FileName);
					return false;
				}

				/* Make sure that the operator is valid (+, -, *, /, mod) */
				if ($a_Set[3] != "+" && $a_Set[3] != "-" && $a_Set[3] != "*" && $a_Set[3] != "/" && $a_Set[3] != "mod" && $a_Set[3] != "%") {
					Err::Raise ("Invalid operator: $s_Set", E_TETRA_TEMPLATE, "Templating", $s_FileName);
					return false;
				}

				/* If the operator is mod replace it with the PHP modulus symbol (%) */
				if ($a_Set[3] == "mod")
					$a_Set[3] = "%";

				/* Stuff a round function in to keep division problems from getting out of hand */
				$a_Set[1] = "= round (";
					
				/* Fix up the final statement and replace the template code */
				$s_String = substr_replace ($s_String, "\n<?php ".implode (" ", $a_Set).", 2); ?>", $i_Set, ($i_End - $i_Set) + 1);

			}
		}

		/* Replace all variables with PHP code */
		$s_Search = "/<var:(.*?)>/is";
		$s_Replace = "<?php echo (\$a_Variables [\"\$1\"]); ?>";
		$s_String = preg_replace ($s_Search, $s_Replace, $s_String);
		
		/* Return the finished string */
		return $s_String;
	
	}

	/***************************************************
	* Function:    VariableSet                         *
	* Parameters:  $s_Name - Variable to be tested     *
	* Description: Checks to see if a template         *
	*              variable has been set               *
	***************************************************/
	function VariableSet ($s_Name)
	{
	
		/* Get the variable array */
		global $a_Variables;
		
		/* Check to see if the variable is set at all, or blank */
		if (!isset ($a_Variables[$s_Name]) || $a_Variables[$s_Name] === false)
			/* The variable isn't set, so return false */
			return false;
		
		/* The variable is set, return true */
		return true;
	
	}
	
	/******************************************
	* Function:    Assign                     *
	* Parameters:  $s_Var - Name of the       *
	*                       template variable *
	*              $s_Value - Value of $s_Var *
	* Description: Adds a variable to the     *
	*              array of template variables*
	******************************************/
	function Assign ($s_Var, $s_Value)
	{
		
		/* Get the variable array */
		global $a_Variables;
		
		/* See if the variable is an array */
		if (is_array ($s_Value)) {
			
			/* Go through the array */
			foreach ($s_Value as $s_Key=>$s_Val) {
			
				/* Fix up a variable name (using BASIC stlye arrays) */
				$s_Name = "$s_Var($s_Key)";

				/* Get rid of any PHP tags */
				$s_Val = str_replace ("<?", "&lt;?", $s_Val);
				$s_Val = str_replace ("?>", "?&gt;", $s_Val);				
				
				/* Put the value into the var array */
				$a_Variables[$s_Name] = $s_Val;
			
			}
		
		}
		else {

			/* Get rid of any PHP tags */
			$s_Value = str_replace ("<?", "&lt;?", $s_Value);
			$s_Value = str_replace ("?>", "?&gt;", $s_Value);

			/* Put the value into the variable array */
			$a_Variables[$s_Var] = $s_Value;
		}

	}
	
	/**************************************************
	* Function: RemovePHP                             *
	* Parameters: $s_String - String to remove tags   *
	*                         from                    *
	* Description: Removes PHP tags from the string   *
	**************************************************/
	function RemovePHP ($s_String)
	{
	
		$s_String = str_replace ("<?", "&lt;?", $s_String);
		return $s_String;
	
	}
}

?>