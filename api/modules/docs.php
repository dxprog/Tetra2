<?php

/***************************************************
* Tetra                                            *
* Copyright (c) 2004-2005 Matt "dxprog" Hackmann   *
* More copyright info can be found in              *
* license.txt                                      *
***************************************************/

/********************************************
* Universal Document Module                 *
* Version 1.0                               *
********************************************/

/* URL constants */
define (URI_DOCS_ADMIN, 3);
define (URI_DOCS_ID, 3);
define (URI_DOCS_HEADERS, 4);
define (URI_DOCS_PICSIZE, 4);

/* Document types */
global $a_DocTypes, $a_DocExtensions, $a_Banned;
$a_DocTypes = array (0=>"unknown", 1=>"Image", 2=>"Compressed", 3=>"Web Page", 4=>"Movie", 5=>"Audio File");
$a_DocExtensions = array ("jpg"=>1, "jpeg"=>1, "gif"=>1, "png"=>1, "bmp"=>1, "zip"=>2, "tar"=>2, "gz"=>2, "tgz"=>2, "bz"=>2, "rar"=>2, "7z"=>2, "htm"=>3, "html"=>4, "mov"=>4, "wmv"=>4, "avi"=>4, "mpeg"=>4, "mpg"=>4, "wma"=>5, "wav"=>5, "au"=>5, "mp3"=>5);

/* Banned extensions */
$a_DocBanned = array ("exe", "php", "php3", "php4", "php5", "cgi", "elf", "out", "bin");

$s_Class = "Docs";
class Docs {

	/* Module settings */
	var $module_name = "Documents";
	var $module_user_nav_items = true;
	var $module_nav_items = true;
	var $module_sidebars = true;
	
	/*************************************************
	* Function: TetraHandler                         *
	* Description: Handles requests from the fish    *
	*************************************************/
	function TetraHandler ($i_Request, $s_Parameters)
	{

		global $a_User;
	
		/* See what needs to be done */
		switch ($i_Request) {
		case T_REQUEST_SIDEBARS:
			return array ("Sections"=>"docs|sections", "Recent Documents"=>"docs|recent");
		case T_REQUEST_NAV_ITEMS:
			return array ("Documents"=>"/docs");
		case T_REQUEST_NAV_USER:
			$a_Return["Add Document"] = "docs/add_form";
			$a_Return["My Documents"] = "docs/my_docs";
			if ($a_User["user_rank"] == 3) {
				$a_Return["Add Section"] = "docs/admin/form_section";
			}
			return $a_Return;
		}
	
	}

	/*************************************************
	* Function: HandleRequest                        *
	* Description: Handles incoming page requests    *
	*************************************************/
	function HandleRequest ($s_Page)
	{
	
		/* Get the user array */
		global $a_User;
	
		/* See what needs to be done.. as usual */
		switch ($s_Page) {
		case "admin":
			/* Make sure the user has adequate permissions before doing anything else */
			if ($a_User["user_rank"] < 3) {
				Err::Raise ("Only an admin can perform this function!", E_TETRA_USER, "docs");
				return false;
			}

			/* See what needs to be done */
			switch (P::Get (URI_DOCS_ADMIN)) {
			case "form_section":
				$this->SectionForm ();
				break;
			case "form_type":
				Tpl::Display ("docs_admin_type.tpl");
				break;
			case "add_section":
				$this->AddSection ();
				break;
			case "add_type":
				$this->AddType ();
				break;
			}
			break;
			
		case "add_form":
			$this->AddForm ();
			break;
		case "add_doc":
			$this->Add ();
			break;
		case "sections":
			$this->SectionsBar ();
			break;
		case "thumb":
			if (!P::Get (URI_DOCS_PICSIZE))
				$this->Thumb (64);
			else
				$this->Thumb (P::Get (URI_DOCS_PICSIZE));
			break;
		case "view":
			$this->View ();
			break;
		case "recent":
			$this->RecentDocs ();
			break;
		case "vote":
			$this->Vote ();
			break;
		case "my_docs":
			$this->DocList (1138);
			break;
		default:
			/* If the action is a number display the specific section */
			if (is_numeric ($s_Page))
				$this->DocList ($s_Page);
			else
				$this->DocList ();
			break;
		}
	
	}

	/*************************************************
	* Function: SectionsBar                          *
	* Description: Displays a the list of sections   *
	*************************************************/	
	function SectionsBar ()
	{
	
		/* Get the base sections */
		$s_Query = "SELECT * FROM doc_sections WHERE section_parent='0'";
		$h_Result = DB::db_Query ($s_Query);
		
		/* Display the header */
		Tpl::Display ("docs_section_head.tpl");
		
		/* Loop through the sections */
		for ($i = 0; $i < $h_Result["num_rows"]; $i++) {
		
			/* Get the data */
			$a_Data = DB::db_Array ($h_Result);
			
			/* Display the section */
			Tpl::Assign ("section", $a_Data);
			Tpl::Assign ("sub_section", 0);
			Tpl::Display ("docs_section_item.tpl");
			
			/* Get any subsections */
			$s_Query = "SELECT * FROM doc_sections WHERE section_parent='".$a_Data["section_id"]."'";
			$h_Temp = DB::db_Query ($s_Query);
			for ($j = 0; $j < $h_Temp["num_rows"]; $j++) {
			
				/* Get the info and display it */
				$a_Temp = DB::db_Array ($h_Temp);
				Tpl::Assign ("seb_section", 1);
				Tpl::Assign ("section", $a_Temp);
				Tpl::Display ("docs_section_item.tpl");
			
			}
		
		}
		
		/* Display the footer */
		Tpl::Display ("docs_section_foot.tpl");
	
	}
	
	/*************************************************
	* Function: AddSection                           *
	* Description: Yeah, that's what it does         *
	*************************************************/
	function AddSection ()
	{
	
		/* If we're in preprocess add the section */
		global $b_Preprocess, $b_Success;
		if ($b_Preprocess) {
		
			/* Make sure a section name was provided */
			if (!$_POST["section"]) {
				Err::Raise ("Please enter a section name!", E_TETRA_FORM, "docs");
				return false;
			}
			
			/* Create the query and go */
			$s_Query = "INSERT INTO doc_sections (section_name, section_description, section_parent) VALUES ('".addslashes ($_POST["section"])."', '".addslashes ($_POST["description"])."', '".$_POST["parent"]."')";
			if (!DB::db_Query ($s_Query)) {
				Err::Raise ("There was an error adding the section!", E_TETRA_CRITICAL, "docs");
				return false;
			}
			else
				$b_Success = true;
		}
		else {
			
			/* If we were successful earlier say so */
			if ($b_Success) {
				Err::Message ("Section Added", "The document section was added successfully!");
				return false;
			}
		}
	
	}

	/*************************************************
	* Function: AddForm                              *
	* Description: Pulls together data for the add   *
	*              form and then displays it         *
	*************************************************/
	function AddForm ()
	{
		
		/* Get the user array */
		global $a_User;
		
		/* Make sure this ain't some "guest" */
		if ($a_User["user_id"] == 1) {
			Err::Raise ("Only registered users can perform that function!", E_TETRA_USER, "docs");
			return false;
		}
		
		/* Get the document sections */
		$s_Sections = "";
		$s_Query = "SELECT * FROM doc_sections";
		$h_Result = DB::db_Query ($s_Query);
		for ($i = 0; $i < $h_Result["num_rows"]; $i++) {
			$a_Data = DB::db_Array ($h_Result);
			$s_Sections .= "<option value=\"".$a_Data["section_id"]."\">".$a_Data["section_name"]."</option>\n";
		}
		
		/* Get the document types */
		$s_Types = "";
		$s_Query = "SELECT * FROM doc_types";
		$h_Result = DB::db_Query ($s_Query);
		for ($i = 0; $i < $h_Result["num_rows"]; $i++) {
			$a_Data = DB::db_Array ($h_Result);
			$s_Types .= "<option value=\"".$a_Data["type_id"]."\">".$a_Data["type_description"]."</option>\n";
		}
		
		/* Display the form */
		Tpl::Assign ("doc_sections", $s_Sections);
		Tpl::Assign ("doc_types", $s_Types);
		Tpl::Display ("docs_add_form.tpl");
		
	}
	
	/*************************************************
	* Function: Add                                  *
	* Description: Pulls together data for the add   *
	*              form and then displays it         *
	*************************************************/
	function Add ()
	{
	
		/* Get the user array */
		global $a_User, $a_DocBanned;
		
		/* Make sure the user isn't a guest */
		if ($a_User["user_id"] == 1) {
			Err::Raise ("Only registered users can perform that function!", E_TETRA_USER, "docs");
			return false;
		}
		
		/* Make sure the important fields were filled out */
		if (!$_POST["title"] || !$_POST["description"]) {
			Err::Raise ("You must provide a title and a description.", E_TETRA_FORM, "docs");
			return false;
		}
		
		/* If a file wasn't uploaded check some stuff against the location */
		if (!$_FILES["file"]["name"]) {
			/* Make sure the location doesn't give us a 404 */
			if (!url_validate ($_POST["location"])) {
				Err::Raise ("The link you provided does not exist.", E_TETRA_CRITICAL, "docs");
				return false;
			}
			
			/* If we're here set the location of the document */
			$s_Location = $_POST["location"];
			
		}
		else {
			
			/* First, check to see if the file was uploaded successfully */
			if (!is_uploaded_file ($_FILES["file"]["tmp_name"])) {
				Err::Raise ("Your file was not uploaded successfully.", E_TETRA_CRITICAL, "docs");
				return false;
			}
			
			/* Now make sure the file is under the 100K limit */
			if ($_FILES["file"]["size"] > 1048576) {
				Err::Raise ("Your file is too large. File uploads are restricted to 100K", E_TETRA_FORM, "docs");
				return false;
			}
			
			/* Set the name of the file */
			$s_Location = "/docs/".$a_User["user_name"]."_".$_FILES["file"]["name"];
			
			/* Check to make sure this type of file isn't banned */
			$s_Ext = $this->GetExtension ($s_Location);
			for ($i = 0; $i < sizeof ($a_DocBanned); $i++) {
				if (strtolower ($s_Ext) == $a_DocBanned[$i]) {
					Err::Raise ("This type of document is not allowed to be uploaded!", E_TETRA_CRITICAL, "docs", "Doc name: ".$_FILES["file"]["name"]);
					return false;
				}
			}
			
			/* Move the file */
			if (!@move_uploaded_file ($_FILES["file"]["tmp_name"], ".$s_Location")) {
				Err::Raise ("There was an error uploading your file!", E_TETRA_CRITICAL, "docs");
				return false;
			}
			
		}
		
		/* Add "comments" onto the end of the title for the topic */
		$s_Title = $_POST["title"];
		$_POST["title"] .= " Comments";
		
		/* Create a topic for this document */
		$_POST["body"] = $_POST["description"];
		if (!($i_Topic = MB::CreateTopic (54, true))) {
			Err::Raise ("Couldn't create a topic for your document!", E_TETRA_CRITICAL, "docs");
			return false;
		}
		
		/* Everything seems to check out, add the doc */
		$s_Query = "INSERT INTO docs (doc_thread, doc_title, doc_description, doc_date, doc_poster, doc_section, doc_type, doc_url) VALUES ";
		$s_Query .= "('$i_Topic', '".addslashes ($s_Title)."', '".addslashes ($_POST["description"])."', '".time ()."', '".$a_User["user_id"]."', '".$_POST["section"]."', '".$_POST["type"]."', '$s_Location')";
		if (!($i_Doc = DB::db_Query ($s_Query))) {
			Err::Raise ("There was an error adding your document to the database!", E_TETRA_CRITICAL, "docs");
			return false;
		}
		else
			Err::Message ("Success!", "Your document was successfully posted!");
			
	
	}

	/*************************************************
	* Function: SectionForm                          *
	* Description: Displays the create section form  *
	*************************************************/
	function SectionForm ()
	{
	
		/* Get the other base sections to create a list of possible parents */
		$s_Query = "SELECT * FROM doc_sections WHERE section_parent='0'";
		$h_Result = DB::db_Query ($s_Query);
		
		/* Start off the options with "No Parent" */
		$s_Sections = "<option value=\"0\">No Parent</option>";
		
		/* Run through anything that was returned from the database and toss it into the mix */
		for ($i = 0; $i < $h_Result["num_rows"]; $i++) {
			$a_Data = DB::db_Array ($h_Result);
			$s_Sections .= "<option value=\"".$a_Data["section_id"]."\">".$a_Data["section_name"]."</option>";
		}
		
		/* Display the form */
		Tpl::Assign ("sections", $s_Sections);
		Tpl::Display ("docs_admin_section.tpl");
	
	}
	
	/*************************************************
	* Function: DocList                              *
	* Description: Displays the list of documents    *
	*************************************************/
	function DocList ($i_Section = 0)
	{
	
		/* Get the list of doc types and their extension mapping */
		global $a_DocTypes, $a_DocExtensions, $a_User;
	
		/* If a section ID was provided get the name */
		if ($i_Section != 1138 && $i_Section) {
			
			/* Get the section name */
			$s_Query = "SELECT section_name FROM doc_sections WHERE section_id='$i_Section'";
			$a_Data = DB::db_Array (DB::db_Query ($s_Query));
			
			/* If the name is blank this must be a faulty ID */
			if (!$a_Data["section_name"]) {
				Err::Raise ("Invalid section ID!", E_TETRA_FORM, "docs");
				return false;
			}
			
			/* Set a template variable with the name */
			Tpl::Assign ("section", $a_Data["section_name"]);
			
		}
		/* If the section is 1138, list the user's documents */
		elseif ($i_Section == 1138) {
			Tpl::Assign ("section", "My Documents");
		}
	
		/* Get the documents */
		if ($i_Section && $i_Section != 1138)
			$s_Query = "SELECT d.*, u.user_name, t.type_description FROM docs AS d INNER JOIN users AS u ON u.user_id=d.doc_poster INNER JOIN doc_types AS t ON t.type_id=d.doc_type WHERE d.doc_section='$i_Section'";
		elseif ($i_Section == 1138)
			$s_Query = "SELECT d.*, u.user_name, t.type_description FROM docs AS d INNER JOIN users AS u ON u.user_id=d.doc_poster INNER JOIN doc_types AS t ON t.type_id=d.doc_type WHERE d.doc_poster='".$a_User["user_id"]."'";
		else
			$s_Query = "SELECT d.*, u.user_name, t.type_description FROM docs AS d INNER JOIN users AS u ON u.user_id=d.doc_poster INNER JOIN doc_types AS t ON t.type_id=d.doc_type";
		$h_Result = DB::db_Query ($s_Query);
		
		/* Display the header */
		Tpl::Display ("docs_list_head.tpl");
		
		/* Display the stuff */
		for ($i = 0; $i < $h_Result["num_rows"]; $i++) {
			
			/* Get the data */
			$a_Data = DB::db_Array ($h_Result);
			
			/* Get the extension of the doc */
			$s_Ext = $this->GetExtension ($a_Data["doc_url"]);
			
			/* If we got an extension back get it's type */
			if ($s_Ext) 
				$i_DocType = $a_DocExtensions [$s_Ext];
			else
				$i_DocType = 0;
			
			/* If this is an image let the template know that we'll be generating a thumbnail */
			if ($i_DocType == 1)
				Tpl::Assign ("thumbnail", 1);
			else 
				Tpl::Assign ("thumbnail", 0);
				
			/* Assign the type name */
			Tpl::Assign ("doc_type", $a_DocTypes[$i_DocType]);
			
			/* Display the document */
			Tpl::Assign ("doc", $a_Data);
			Tpl::Assign ("ext", $s_Ext);
			Tpl::Display ("docs_list_item.tpl");
			
		}
	
		/* Display the foot */
		Tpl::Display ("docs_list_foot.tpl");
	
	}

	/*************************************************
	* Function: Vote                                 *
	* Description: Votes on a document               *
	*************************************************/
	function Vote ()
	{

		/* Get the user array */
		global $a_User;
	
		/* Validate the doc ID */
		$i_ID = P::Get (URI_DOCS_ID);
		if (!$i_ID || !is_numeric ($i_ID)) {
			Err::Raise ("Invalid document ID!", E_TETRA_FORM, "docs");
			return false;
		}
		
		/* Check to see if the user can vote on the document */
		if ($this->Rated ($i_ID)) {
			Err::Raise ("You cannot vote on this document.", E_TETRA_USER, "docs");
			return false;
		}
		
		/* Make sure the rating is within the range (> 0 && < 11) */
		if ($_POST["Rating"] < 1 || $_POST["Rating"] > 10) {
			Err::Raise ("You cannot vote over 10 or under 1. You have been reported to the administrators!", E_TETRA_USER, "docs", "User name: " & $a_User["user_name"]);
			return false;
		}
		
		/* Insert the rating */
		$s_Query = "INSERT INTO doc_ratings (rate_doc, rate_user, rate_rating) VALUES ('$i_ID', '".$a_User["user_id"]."', '".$_POST["Rating"]."')";
		if (!DB::db_Query ($s_Query)) {
			Err::Raise ("There was an error voting for the document!", E_TETRA_CRITICAL, "docs");
			return false;
		}
		else {
			Err::Message ("Vote Placed!", "Your vote has been registered!");
			$this->View ();
		}
	
	}
	
	/*************************************************
	* Function: View                                 *
	* Description: Displays stats on a document      *
	*************************************************/
	function View ()
	{
	
		/* Get the list of doc types and their extension mapping */
		global $a_DocTypes, $a_DocExtensions, $a_User;
	
		/* Get the ID */
		$i_ID = P::Get (URI_DOCS_ID);
		if (!is_numeric ($i_ID) || !$i_ID) {
			Err::Raise ("Invalid document ID!", E_TETRA_FORM, "docs");
			return false;
		}
		
		/* If we were told not to have headers, clean the cache */
		if (P::Get (URI_DOCS_HEADERS) == "noheaders")
			ob_clean ();
			
		/* Get info on the document */
		$s_Query = "SELECT d.*, s.section_name, t.type_description, u.user_name FROM docs AS d INNER JOIN doc_sections AS s ON s.section_id=d.doc_section INNER JOIN doc_types AS t ON t.type_id=d.doc_type INNER JOIN users AS u ON u.user_id=d.doc_poster WHERE doc_id='$i_ID'";
		$a_Data = DB::db_Array (DB::db_Query ($s_Query));
		
		/* Get the date readable */
		$a_Data["doc_date"] = make_date ($a_Data["doc_date"]);
		
		/* Get the extension of the doc */
		$s_Ext = $this->GetExtension ($a_Data["doc_url"]);
		
		/* If we got an extension back get it's type */
		if ($s_Ext) 
			$i_DocType = $a_DocExtensions [$s_Ext];
		else
			$i_DocType = 0;
		
		/* If this is an image let the template know that we'll be generating a thumbnail */
		if ($i_DocType == 1)
			Tpl::Assign ("thumbnail", 1);
		else 
			Tpl::Assign ("thumbnail", 0);
			
		/* Assign the type name */
		Tpl::Assign ("doc_type", $a_DocTypes[$i_DocType]);
		Tpl::Assign ("ext", $s_Ext);
		
		/* Get the average rating */
		$a_Data["doc_rating"] = $this->AvgRating ($i_ID);
		
		/* See if the user has rated the document */
		$a_Data["user_rated"] = $this->Rated ($i_ID);
		
		/* Lump all that info into a template var and display the template */
		Tpl::Assign ("doc", $a_Data);
		Tpl::Display ("docs_document.tpl");
		
		/* Display the thread for this document */
		MB::ViewThread ($a_Data["doc_thread"]);
		
		/* If we got the noheaders quit executing script */
		if (P::Get (URI_DOCS_HEADERS) == "noheaders")
			exit ();
	
	}
	
	/*************************************************
	* Function: RecentDocs                           *
	* Description: Displays a list of recent docs    *
	*************************************************/
	function RecentDocs ()
	{
	
		/* Display the header */
		Tpl::Display ("docs_rec_head.tpl");
		
		/* Get the 5 newest documents */
		$s_Query = "SELECT d.*, u.user_name, s.section_name, t.type_description FROM docs AS d INNER JOIN users AS u ON u.user_id=d.doc_poster INNER JOIN doc_sections AS s ON s.section_id=d.doc_section INNER JOIN doc_types AS t ON t.type_id=d.doc_type ORDER BY doc_id DESC LIMIT 5";
		$h_Result = DB::db_Query ($s_Query);
		
		/* Display the items */
		if ($h_Result["num_rows"] > 0) {
			for ($i = 0; $i < $h_Result["num_rows"]; $i++) {
				
				/* Get the info */
				$a_Data = DB::db_Array ($h_Result);
				
				/* Convert the date */
				$a_Data["doc_date"] = make_date ($a_Data["doc_date"]);
				
				/* Display the document */
				Tpl::Assign ("doc", $a_Data);
				Tpl::Display ("docs_rec_item.tpl");
				
			}
		}
		else
			Tpl::Display ("docs_rec_none.tpl");
		
		/* Show the footer */
		Tpl::Display ("docs_rec_foot.tpl");
	
	}
	
	/****************************************************************************
	*                              INTERNAL FUNCTIONS                           *
	****************************************************************************/
	
	/*************************************************
	* Function: AvgRating                            *
	* Description: Gets the average rating of a doc  *
	*************************************************/	
	function AvgRating ($i_ID)
	{
	
		/* Get all the ratings for this document */
		$s_Query = "SELECT SUM(rate_rating) AS total, COUNT(*) AS num FROM doc_ratings WHERE rate_doc='$i_ID'";
		$a_Data = DB::db_Array (DB::db_Query ($s_Query));
		
		/* If there weren't any results return zip */
		if ($a_Data["num"] == 0)
			return 0;
		else
			/* Return the average */
			return ($a_Data["total"] / $a_Data["num"]);
	
	}

	/*************************************************
	* Function: Rated                                *
	* Description: Checks to see if the user rated a *
	*              document                          *
	*************************************************/
	function Rated ($i_ID)
	{
	
		/* Get the user array */
		global $a_User;
		
		/* If the user is guest return that they voted (guest's can't vote) */
		if ($a_User["user_id"] == 1)
			return 1;
		
		/* See if there's a rating entry by this user for this doc */
		if (DB::db_Count ("doc_ratings", array ("rate_doc"=>$i_ID, "rate_user"=>$a_User["user_id"])))
			return 1;

		/* Make sure this isn't the person who posted the document */
		$s_Query = "SELECT doc_poster FROM docs WHERE doc_id='$i_ID'";
		$a_Temp = DB::db_Array (DB::db_Query ($s_Query));
		
		/* If this is the same person don't allow them to vote */
		if ($a_Temp["doc_poster"] == $a_User["user_id"])
			return 1;
		else
			return 0;
	
	}
	
	/*************************************************
	* Function: GetExtension                         *
	* Description: Gets the extension of the file    *
	*************************************************/
	function GetExtension ($s_File)
	{
		/* Get the last period in the file */
		$i_Period = strrpos ($s_File, ".");
		
		/* If we got false, return nothing */
		if ($i_Period === false)
			return;
		
		/* Get the extension and return it */
		$s_Ext = trim (substr ($s_File, $i_Period + 1, strlen ($s_File) - $i_Period + 1));
		return strtolower ($s_Ext);
		
	}
	
	/*************************************************
	* Function: Thumb                                *
	* Description: Creates a thumbnail               *
	*************************************************/
	function Thumb ($i_PicSize)
	{
	
		/* Clear the object buffer */
		ob_clean ();
		
		/* Send out that we're creating a PNG */
		Header ("Content-type: image/png");
		
		/* Get the image location */
		$s_Query = "SELECT doc_url FROM docs WHERE doc_id='".P::Get (URI_DOCS_ID)."' LIMIT 1";
		$a_Data = DB::db_Array (DB::db_Query ($s_Query));
		$s_SrcFile = $a_Data["doc_url"];
		
		/* If the first character is a slash, put a period in front */
		if (substr ($s_SrcFile, 0, 1) == "/")
			$s_SrcFile = ".$s_SrcFile";
		
		/* Create a new image */
		$t_DestImage = ImageCreateTrueColor ($i_PicSize, $i_PicSize);
		
		/* Create our colors */
		$c_White = ImageColorAllocate ($t_DestImage, 255, 255, 255);
		$c_Black = ImageColorAllocate ($t_DestImage, 0, 0, 0);
		
		/* Fill the image with white */
		ImageFill ($t_DestImage, 0, 0, $c_White);
		
		/* Turn on antialiasing for the destination image */
		ImageAntiAlias ($t_DestImage, true);
		
		/* Open the source image */
		switch ($this->GetExtension ($s_SrcFile)) {
		case "jpg":
			$t_SrcImage = @ImageCreateFromJPEG ($s_SrcFile);
			break;
		case "png":
			$t_SrcImage = @ImageCreateFromPNG ($s_SrcFile);
			break;
		case "gif":
			$t_SrcImage = @ImageCreateFromGIF ($s_SrcFile);
			break;
		}
		
		/* If the source image was unable to load, we'll copy it over and allow the browser to resize it. */
		if (!$t_SrcImage) {
			move_uploaded_file ($s_SrcFile, "./docs/".$a_User["user_name"]."_avatar.".$s_Ext);
			return "./docs/".$a_User["user_name"]."_avatar.".$s_Ext;
		}
		
		/* Get the width and height and X and Y of the image */
		$i_Width = ImageSX ($t_SrcImage);
		$i_Height = ImageSY ($t_SrcImage);
		$i_X = 0;
		$i_Y = 0;
		
		/* If the image is larger than the thumbnail size in any respect we'll do an aspect ratio scale */
		if ($i_Width > $i_PicSize || $i_Height > $i_PicSize) {
			
			/* If the image is taller than it is wide scale the width using a height / width ratio */
			if ($i_Height > $i_Width) {
				
				/* Set the height and width */
				$i_Width = $i_PicSize * ($i_Width / $i_Height);
				$i_Height = $i_PicSize;
				
				/* Set the X and Y */
				$i_X = ($i_PicSize - $i_Width) / 2;
				$i_Y = 0;
				
			}
			else {
				
				/* Set the width to maximum and calculate the height from that */
				$i_Height = $i_PicSize * ($i_Height / $i_Width);
				$i_Width = $i_PicSize;
				
				/* Set the X and Y */
				$i_X = 0;
				$i_Y = ($i_PicSize - $i_Height) / 2;
			
			}
			
		}		
		/* If the image is smaller, we'll just center it and the heck with it */
		elseif ($i_Width < $i_PicSize && $i_Height < $i_PicSize) {
		
			/* Center that puppy */
			$i_X = ($i_PicSize - $i_Width) / 2;
			$i_Y = ($i_PicSize - $i_Height) / 2;
		
		}
		
		/* Copy the image into the destination buffer */
		ImageCopyResized($t_DestImage,$t_SrcImage, $i_X, $i_Y, 0, 0,$i_Width,$i_Height, ImageSX($t_SrcImage),ImageSY($t_SrcImage));
		
		/* Output the image */
		ImagePNG ($t_DestImage);
		exit ();
	
	}
	
}