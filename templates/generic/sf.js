/***********************************
* Somethin's Fishy                 *
* Copyright (C) 2004 Matt Hackmann *
* All Rights Reserved.             *
***********************************/

/* Browser sniffer */
var b_IE = document.all ? 1 : 0;
var b_NS = document.layers ? 1 : 0;
var b_Moz = document.getElementByID ? 1 : 0;

/* Global variables */
var i_CurrentFish = 1;

/* Error handler */
window.onerror = HandleError;

function GetObject (s_Object)
{

	/* Return the object for this browser */
	if (b_IE)
		return document.all[s_Object].style;
	else if (b_NS)
		return document.layers[s_Object];
	else
		return eval ("document." + s_Object + ".style");

}

function ChangeFish (i_Fish)
{

	/* Change the value on the form */
	var t_Fish = eval ("document.sfform.Fish" + i_CurrentFish);
	t_Fish.value = i_Fish;
	
	/* Change the image */
	var t_Fish = eval ("document.FishImg" + i_CurrentFish);
	t_Fish.src = "./templates/generic/styles/images/fish" + i_Fish + ".png";

}

function ChangeCurrentFish (i_Fish)
{

	/* Clear the background of the old selection */
	t_Box = GetObject ("FishImg" + i_CurrentFish);
	t_Box.backgroundColor = "";

	/* Set the current selected fish */
	i_CurrentFish = i_Fish;

	/* Set the background and border style */
	t_Box = GetObject ("FishImg" + i_Fish);
	t_Box.backgroundColor = "#245DD8";

}

function HandleError (errorMsg, url, line)
{

	window.alert ("There was an error parsing the script!\nURL: " + url + "\nLine: " + line + "\nMessage: " + errorMsg);

}