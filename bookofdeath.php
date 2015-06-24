<?php

/*
	*****************************
	*****   BOOK OF DEATH   *****
	*****    Version 0.1    *****
	*****************************
	
	Developer: Danny Moules
	Website: http://www.rushyo.com/lotgd/
	
	Required Modules:
		o None
		
	Additional Modules for Full Function: 
		o Alignment Core by Sichae
		
	Module Hooks:
		o forest event
	
	Licensed under Attribution-Non-Commercial-Share Alike 2.0 UK: England & Wales
	http://creativecommons.org/licenses/by-nc-sa/2.0/uk/
	
	Attribution Text (In all derived scripts):
		//Derived from Book of Death developed by Danny Moules (Rushyo)
	
	Attribution Text (In Module Author field):
		Original by Danny Moules (Rushyo)
*/

//Load Alignment Core
if(is_module_active("alignment"))
{
    require_once("./modules/alignment/func.php");
}

function bookofdeath_getmoduleinfo()
{
	$intAlignment = 0;
	if(is_module_active("alignment"))
	{
		$intAlignment = 1;
	}
	$arrInfo = array(
		"name" => "Book of Death",
		"version" => "0.1",
		"author" => "Danny Moules (Rushyo)",
		"category" => "Forest Specials",
		"description" => "The `\$BOOK OF DEATH`0. `iDoes exactly what it says on the tome`i.",
		"download" => "http://www.rushyo.com/lotgd/",
        "settings" => array("Book of Death - Settings,title",
                            "lostturns" => "Amount of turns lost from reading a chapter (Default is 2),int|2",
							"goodbonus" => "Magnitude of helpful bonuses (Default is 3),int|3",
							"deathodds" => "Chance of instant death in percent (Default is 50%),range,0,100,5|50",
							"burnable" => "Enable 'Burn the book' which always murders people,bool|$intAlignment",
							"NOTE: Without Alignment Core enabled the above is plain cruel - otherwise it has a purpose,note",
							
							"`n`b`i`&The following settings apply only when the Alignment Core module is active`&`i`b`n`n,note",
							"evilness" => "Evil alignment shift triggered by reading the book (Default is 3),int|3",
							"goodness" => "Good alignment shift triggered by trying to burn the book (Default is 3),int|3")
	);
	return $arrInfo;
}

function bookofdeath_install(){
	module_addeventhook("forest", "return 100;");
	module_addeventhook("travel", "return 100;");
	return true;
}

function bookofdeath_uninstall(){
	return true;
}

function bookofdeath_dohook($strHookName, $args)
{
	return $args;
}

function bookofdeath_runevent($type, $strFrom)
{
	global $session;
	
	$session["user"]["specialinc"] = "module:bookofdeath";

	$strOp = httpget("op");
	if($strOp == "" || $strOp == "search")
	{
		output("As you explore you come across a small concrete structure hidden behind foliage.`n");
		output("Intrigued, you step inside the building and, navigating the darkness, come across a dark book.`n");
		output("The book, bound in skin (the skin of what, or who, remains a mystery), has a fluidic red scrawl on the front:`n`n");
		output("`\$BOOK OF DEATH`0");
		addnav("Read from the book", $strFrom."op=read");
		if(get_module_setting("burnable") == true)
		{
			addnav("Burn the book", $strFrom."op=burn");
		}
		addnav("Flee the structure", $strFrom."op=flee");
	}
	elseif($strOp == "burn")
	{
		$session['user']['specialinc'] = "";
		output("Approaching slowly, with a match clutched tightly in your fingers,");
		output("you attempt to set fire to the `\$BOOK OF DEATH`0.`n`n");
		$intDeathType = e_rand(1, 3);
		if($intDeathType == 1)
		{
			output("As you near the book, you find the match slipping out of your fingers.");
			output("The match flies through the air and lights your clothing, leaving you");
			output("flailing and screaming about the room.");
			output("Your best efforts to deal with the fire are for naught, as it appears to have become supernatural.");
			output("Upon the last beat of your heart you notice the book, still sat there unharmed, seemingly gloating at your corpse.");
		}
		elseif($intDeathType == 2)
		{
			output("You carefully set fire to the entire chamber. As you set out to leave");
			output("the door slams behind you and the only light you can see is that brought");
			output("about by the flames you have cast in the room.");
			output("You watch as they flicker around the `\$BOOK OF DEATH`0, never touching");
			output("or indeed marking it, when you yourself are engulfed in flame.");
		}
		elseif($intDeathType == 3)
		{
			output("The `\$BOOK OF DEATH`0 is set on fire! Laughing triumphantly, you fail");
			output("to notice that a gigantic demon has risen up behind you.");
			output("You turn around, ready your %s... and immediately get squashed.", $session["user"]["weapon"]);
			output("As the fire dies down your ghostly form watches as the book instantly");
			output("reappears, good as new, on its pedestal.");
		}
		output("`n`n`^You are dead`0... and your gold has been melted down in the fire.`n");
		
		//Requires Alignment Core
		if(is_module_active("alignment"))
		{
			output("At least you gave it a shot.");
			$intGoodness = get_module_setting("goodness");
			align($intGoodness);
		}
		
		$session['user']['alive'] = false;
		$session['user']['hitpoints'] = 0;
		$session['user']['gold'] = 0;
		
		addnav("Daily News", "news.php");
		addnews("%s died in a mysterious fiery incident.", $session["user"]["name"]);
	}
	elseif($strOp == "read")
	{
		$session['user']['specialinc'] = "";
		
		output("Brushing some of the dust from the tome you slowly open the pages...`n`n");
		$intDeathOdds = get_module_setting("deathodds");
		$intDeathRoll = e_rand(1, 100);
		if($intDeathRoll <= $intDeathOdds)
		{
			output("`iThe book slams shut`i.`n`n");
			output("A translucent hand larger than your body reaches out from the book, taking one foul swipe through the air.`n");
			output("As the hand passes through you, cold rushes throughout your body.`n");
			output("Whilst darkness clouds your vision, you ponder about how you really ought to have noticed the warning signs.`n`n");
			output("`^You are dead.`0 What did you expect from opening an book calling itself the `\$BOOK OF DEATH`0?`n");
			output("Luckily for you, at least the `\$Hand of Death`0 doesn't have any interest in your gold.");
			$session["user"]["alive"] = false;
			$session["user"]["hitpoints"] = 0;
			addnav("Daily News", "news.php");
			addnews("%s was struck down by the `\$Hand of Death`0 for being overly nosey.", $session['user']['name']);
			
		}
		else
		{
			$intRand = e_rand(1, 6);
			if($intRand == 1)
			{
				output("As you open the book, the letters slide off the page.`n`n");
				output("Your body becomes paralysed as the book shuts itself and a manic laughter fills the room.`n");
				output("Racing backwards out of the door involuntarily, you watch the book disappear into the distance.`n");
				output("Something inside tells you things could have been worse.`n`n");
				output("When you finally regain the use of your legs, you have been dumped somewhere unfamiliar.`n");
				output("It may be a while before you return to the book's chambers.");
			}
			if($intRand >= 2)
			{
				$intLostTurns = get_module_setting("lostturns");
				$intGoodBonus = get_module_setting("goodbonus");
			
				if($intRand == 2)
				{
					$strChapter = "Controlling Mortals";
					$session["user"]["charm"] += $intGoodBonus;
					$strGain = "$intGoodBonus Charm";
				}
				if($intRand == 3)
				{
					$strChapter = "Death And Its Forms";
					$intExp = round($session["user"]["experience"] * (0.1 * $intGoodBonus), 0);
					$session["user"]["experience"] += $intExp;
					$strGain = "$intExp Experience";
				}
				if($intRand == 4)
				{
					$strChapter = "Necromancy For Dummies";
					
					if (getsetting("enablecompanions", true))
					{
						$intHP = round($session["user"]["level"] * $intGoodBonus, 0);
						$intAttack = (round($session["user"]["level"] / 5, 0) * $intGoodBonus) + 1;
						$intDefense = (round($session["user"]["level"] / 7, 0) * $intGoodBonus) + 1;
					
						apply_companion('bod_conjured_skeleton',
						array(
							"name" => "`6Conjured Skeleton",
							"hitpoints" => $intHP,
							"maxhitpoints" => $intHP,
							"attack" => $intAttack,
							"defense" => $intDefense,
							"dyingtext" => "`\$Your conjured skeleton breaks into pieces at your feet.`n",
							"abilities" => array("fight" => true),
							"ignorelimit" => true //Just in case...
						), true);
						$strGain = "a skeleton you conjured all by yourself";
					}
					else
					{
						$intMinions = round($session['user']['level'] / 3, 0) + 1;
						$intDamage = (round($session['user']['level'] / 5, 0) * $intGoodBonus) + 1;
						apply_buff('bod_conjured_skeleton_2',
						array(
							"startmsg" => "`\Using your newly acquired necromancy knowledge you conjure up a skeleton horde.",
							"name" => "`6Conjured Skeleton Horde",
							"rounds" => 5,
							"wearoff" => "`\$Your conjured skeleton horde breaks into pieces at your feet.",
							"minioncount" => $intMinions,
							"maxbadguydamage" => $intDamage,
							"effectmsg" => "`)A conjured skeleton hits {badguy}`) for `^{damage}`) damage.",
							"effectnodmgmsg" => "`)An conjured skeleton flails at {badguy}`) but `\$MISSES`)!",
							"schema" => "module-bookofdeath"
						));
						$strGain = "a small horde of frail skeletons";
					}
				}
				if($intRand == 5)
				{
					$strChapter = "Dark Elixirs";
					$session["user"]["maxhitpoints"] += 1;
					$strGain = "a point of health, `bpermanently`b";
				}
				if($intRand == 6)
				{
					$strChapter = "Hidden Treasures";
					$session["user"]["gems"] += $intGoodBonus;
					$strGain = "$intGoodBonus Gems";
				}
				
				output("Opening the book to the `3%s`0 chapter you tense, waiting for the book to strike at you.`n`n", $strChapter);
				output("Minutes pass before you muster up the courage to continue reading...");
				output("yet you find yourself entranced by the knowledge the book has to offer.`n`n");
				output("Utterly engrossed you lose track of time, waving `^%s forest fights`0 goodbye.`n", $intLostTurns);
				output("You have learnt much from your reading and gain `%%s`0.", $strGain);
				$session["user"]["turns"] -= $intLostTurns;
				
				//Requires Alignment Core
				if(is_module_active("alignment"))
				{
					output("`n`nWhen you finally step away from the book you feel a slight pang of darkness inside yourself, apropo of what you have learned.");
					$intEvilness = get_module_setting("evilness");
					align(0 - $intEvilness);
				}
				
			}
		}
	}
	else
	{
		$session["user"]["specialinc"] = "";
		output("You remove yourself from the chamber unharmed. Perhaps it was for the best.");
	}
}
?>
