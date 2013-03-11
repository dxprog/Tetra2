<?php

/*****************************************
* TetraXML                               *
* Copyright (C) 2005 Matt Hackmann       *
* All Rights Reserved.                   *
*****************************************/

/* The XML item class */
class XMLNode {

	/* Properties */
	var $s_Name = "";
	var $s_Value = "";
	var $t_Attribute = "";

}

/* The XML object */
$t_XMLRet = "";

/* The XML handler class */
class XML {

	/**********************************************
	* Function: ParseXML                          *
	* Description: Parses an XML file and returns *
	*              an XmlItem pbject with all the *
	*              XML data.                      *
	**********************************************/
	function Parse ($s_File)
	{
	
		/* Global the XML object */
		global $t_XMLRet;
		
		/* Blank out the XML return object */
		$t_XMLRet = "";
		
		/* Make sure the file exists before we go any further */
		if (!file_exists ($s_File)) {
			Err::Raise ("The XML document \"$s_File\" doesn't exist!", E_CRITICAL_ERROR, "XML");
			return false;
		}
		
		/* Load the XML file */
		$a_XML = file ($s_File);
		
		/* Set up the parser and callback functions */
		$t_Parser = xml_parser_create ();
		xml_set_object ($t_Parser, &$this);
		xml_set_element_handler ($t_Parser, "XML_TagOpen", "XML_TagClose");
		xml_set_character_data_handler ($t_Parser, "XML_CData");
		
		/* Parse the data */
		for ($i = 0; $i < sizeof ($a_XML); $i++) {
			if (!xml_parse ($t_Parser, $a_XML[$i])) {
				Err::Raise ("There was an error parsing XML document \"$s_File\".", E_CRITICAL_ERROR, "XML", "Line number: ".xml_get_current_line_number ($t_Parser)."\nError message:".xml_error_string (xml_get_error_code ($t_Parser)));
				return false;
			}
		}
		
		/* Close the parser and return the data */
		xml_parser_free ($t_Parser);
		return $t_XMLRet;
	
	}
	
	/***************************************************
	* Function: XML_TagOpen                            *
	* Description: Organizes the incoming XML data     *
	*              into XMLNode classes                *
	***************************************************/
	function XML_TagOpen ($t_Parser, $s_Tag, $a_Attributes)
	{
		
		/* Global the XML object */
		global $t_XMLRet;
		
		/* Stuff this into the next available slot */
		$t_XMLRet[sizeof ($t_XMLRet) + 1] = new XMLNode ();
		$t_XMLRet[sizeof ($t_XMLRet)]->s_Name = $s_Tag;
		
		/* If there are any attributes add those too */
		if (sizeof ($a_Attributes) > 0) {
		
			/* Loop through each value and add it to the attributes property of this node */
			foreach ($a_Attributes as $s_Key=>$s_Value) {
			
				$t_XMLRet[sizeof ($t_XMLRet)]->t_Attributes[$s_Key] = new XMLNode ();
				$t_XMLRet[sizeof ($t_XMLRet)]->t_Attributes[$s_Key] = $s_Value;
			
			}
		
		}
		
	}
	
	/***************************************************
	* Function: XML_CData                              *
	* Description: Gets the data from the tag and adds *
	*              it into our node class              *
	***************************************************/
	function XML_CData ($t_Parser, $s_Data)
	{
	
		/* Global the XML object */
		global $t_XMLRet;
		
		/* Stuff the data into the object */
		$t_XMLRet[sizeof ($t_XMLRet)]->s_Value = $s_Data;
	
	}
	
	function XML_TagClose () {}

}

?>