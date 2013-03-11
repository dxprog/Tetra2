<?php

/*****************************************
* Somethin's Fishy                       *
* Copyright (C) 2004 Matt Hackmann       *
* Some Rights Reserved.                  *
*****************************************/

class Fishy {

	/* Module info */
	var $module_name = "Somethin's Fishy";
	var $module_nav_items = true;
	var $module_title = true;

	/********************************************
	* Function: TetraHandler                    *
	* Description: Handles Tetra requests       *
	********************************************/
	function TetraHandler ($i_Request, $s_Paramters)
	{
	
		/* Figure out what's wanted */
		switch ($i_Request) {
		case T_REQUEST_TITLE:
			return "Somthin's Fishy";
		case T_REQUEST_NAV_ITEMS:
			return array ("Somethin's Fishy"=>"./index.php?main=fishy&action=newgame");
		}
	
	}


	/********************************************
	* Function: HandleRequest                   *
	* Description: Handles page requests        *
	********************************************/
	function HandleRequest ($s_Options)
	{
	
		/* Figure up what we need to do here */
		switch ($s_Options) {
		case "play":
			/* Run the game "loop" */
			$this->Play ();
			break;
		case "newgame":
			$this->NewGame ();
			break;
		default:
			/* By default show the high scores/start new game screen */
			Tpl::Header ("Somthin's Fishy");
			$this->DisplayHighScores ();
			Tpl::Footer ();
		}
	
	}

	/********************************************
	* Function: DisplayHighScores               *
	* Description: Displays the high scores     *
	********************************************/
	function DisplayHighScores ()
	{
	
	}

	/********************************************
	* Function: NewGame                         *
	* Description: Starts a new game            *
	********************************************/
	function NewGame ()
	{
	
		/* Get the user array */
		global $a_User;
		
		/* Check to see if a game cache file already exists */
		if (Cache::Cached ("sf_".$a_User["user_name"], true)) {
			/* Display the you wanna continue screen */
			Tpl::Display ("fishy_continue.tpl");
			return false;
		}
		
		/* Create the new number and cache it */
		$s_Fish = rand (1, 6).rand (1, 6).rand (1, 6).rand (1, 6);
		Cache::Write ("sf_".$a_User["user_name"], $s_Fish);
		
		/* Start the game "loop" */
		$this->Play ();
	
	}

	/********************************************
	* Function: Play                            *
	* Description: Game handling section        *
	********************************************/
	function Play ()
	{
	
		/* Get the user array */
		global $a_User;
		
		/* Load the cached info */
		$s_Temp = Cache::Read ("sf_".$a_User["user_name"], true);
		
		/* Make sure it's valid */
		if ($s_Temp == "") {
			/* There was no cache file (or it was corrupt) so generate some new numbers and cache them */
			$s_Temp = rand (1, 6).rand (1, 6).rand (1, 6).rand (1, 6);
			Cache::Write ("sf_".$a_User["user_name"], $s_Fish);
		}

		/* Seperate the data */
		$a_Temp = explode ("|", $s_Temp);

		/* This first array element is the random numbers. Shift it off into that variable */
		$s_Fish = array_shift ($a_Temp);

		/* The rest of it is guesses. Set a_Guesses to that info */
		$a_Guesses = $a_Temp;

		/* If the user was posting a guess add it to the array */
		if ($_POST["Fish1"]) {
			/* Make sure we aren't going over the turn limit before continuing */
			if (sizeof ($a_Guesses) < 10)
				$a_Guesses[] = $_POST["Fish1"].$_POST["Fish2"].$_POST["Fish3"].$_POST["Fish4"];
		}

		/* Cache the results */
		Cache::Write ("sf_".$a_User["user_name"], $s_Fish."|".implode ("|", $a_Guesses));
		
		/* Display the header */
		Tpl::Display ("fishy_head.tpl");
		
		/* If there are any guesses to display, display 'em */
		if (sizeof ($a_Guesses) > 0) {

			/* Check to see if the player has won or if he's run out of turns */
			$a_Temp = $this->ProcessGuess ($s_Fish, $a_Guesses[sizeof ($a_Guesses) - 1]);
			if ($a_Temp["rcrp"] == 4 || sizeof ($a_Guesses) >= 10) {
				
				/* If the user lost show the you lose screen */
				if ($a_Temp["rcrp"] != 4) {
					echo ($s_Fish);
					/* Display the lose screen */
					Tpl::Assign ("Fish1", substr ($s_Fish, 0, 1));
					Tpl::Assign ("Fish2", substr ($s_Fish, 1, 1));
					Tpl::Assign ("Fish3", substr ($s_Fish, 2, 1));
					Tpl::Assign ("Fish4", substr ($s_Fish, 3, 1));
					Tpl::Display ("fishy_lose.tpl");
					
					/* Delete the cache file and quit */
					Cache::Delete ("sf_".$a_User["user_name"]);
					return true;
					
				}
				
				/* If we're this far, the user won so figure up the score. The guess scoring goes as follows:
				     RCRP = 25
				     RCWP = 10
				   Then we use the amount of turns it took the user to figure out the code. If the user
				   gets it in one turn (yeah right) they'll get 100% of the guess score. 10 turns would be 10%. */
				for ($i = 0; $i < sizeof ($a_Guesses); $i++) {
					$a_Results = $this->ProcessGuess ($s_Fish, $a_Guesses[$i]);
					$i_Score += ($a_Results["rcrp"] * 25);
					$i_Score += ($a_Results["rcwp"] * 10);
				}
				
				/* Figure up the percentage */
				$i_Percentage = (abs ((sizeof ($a_Guesses) - 10) * 10) + 10) / 100;
				echo ($i_Score."|".$i_Percentage);
				/* Figure up the final score */
				$i_Score *= $i_Percentage;
				
				/* Display the win screen */
				Tpl::Assign ("Fish1", substr ($s_Fish, 0, 1));
				Tpl::Assign ("Fish2", substr ($s_Fish, 1, 1));
				Tpl::Assign ("Fish3", substr ($s_Fish, 2, 1));
				Tpl::Assign ("Fish4", substr ($s_Fish, 3, 1));
				Tpl::Assign ("score", $i_Score * 10);
				Tpl::Display ("fishy_win.tpl");
				
				/* Delete the cache file */
				Cache::Delete ("sf_".$a_User["user_name"]);
				return true;
				
			}

			/* Show the guesses header */
			Tpl::Display ("fishy_guesses_head.tpl");

			/* Loop through the guesses and show the info */
			for ($i = 0; $i < sizeof ($a_Guesses); $i++) {
				
				/* If this guess is blank, don't display it */
				if (!$a_Guesses[$i])
					continue;
				
				/* Seperate the info for the template */
				Tpl::Assign ("Fish1", substr ($a_Guesses[$i], 0, 1));
				Tpl::Assign ("Fish2", substr ($a_Guesses[$i], 1, 1));
				Tpl::Assign ("Fish3", substr ($a_Guesses[$i], 2, 1));
				Tpl::Assign ("Fish4", substr ($a_Guesses[$i], 3, 1));
				
				/* Get the RCRPs and RCWPs and stuff them in a template var */
				$a_Temp = $this->ProcessGuess ($s_Fish, $a_Guesses[$i]);
				Tpl::Assign ("results", $a_Temp);
				
				/* Display the guess */
				Tpl::Display ("fishy_guesses_item.tpl");
				
			}

			/* Show the guesses footer */
			Tpl::Display ("fishy_guesses_foot.tpl");
		
		}
		
		/* Show the fish selection form (also the footer) */
		Tpl::Display ("fishy_foot.tpl");
	
	}

	/********************************************
	* Function: ProcessGuess                    *
	* Description: Figures out how close the    *
	*              user came to the number      *
	********************************************/
	function ProcessGuess ($s_Fish, $s_Guess)
	{
		/* Figure out how many of the fish the user got of the right color and position (RCRP) */
		for ($i = 0; $i < 4; $i++) {
			
			/* If this fish is the same as in the guess, increment the RCRP amount and lock this color so it isn't
			   counted in the right color, wrong position (RCWP) count */
			if (substr ($s_Fish, $i, 1) == substr ($s_Guess, $i, 1)) {
				$i_RCRP++;
				$s_Fish{$i} = "8";
				$s_Guess{$i} = "9";
			}
			
		}
		
		/* Now figure out how many RCWPs there are */
		for ($i = 0; $i < 4; $i++) {
			for ($j = 0; $j < 4; $j++) {
				
				/* Check to see if the fish color is the same as the guess color */
				if (substr ($s_Fish, $i, 1) == substr ($s_Guess, $j, 1)) {
					/* Increment the RCWP amount */
					$i_RCWP++;
					
					/* Lock this fish */
					$s_Fish{$i} = "8";
					$s_Guess{$j} = "9";
					
					/* Break out so we don't incurr anymore counts on this color */
					$j = 4;
				}
				
			}
		}
		
		/* Return the results */
		return array ("rcrp"=>$i_RCRP, "rcwp"=>$i_RCWP);
	
	}

}

?>