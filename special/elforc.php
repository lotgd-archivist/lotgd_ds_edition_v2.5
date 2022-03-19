<?php

// Help one crature and find the golden treasure
//
// idea by:  Jaromir Sosa (ICQ: 271-583-788)
// coding by: Joshua Schmidtke [alias Mikay Kun]
// 
// build: 2006-08-08
// version: 1.0

if (!isset($session)) exit();

$session['user']['specialinc']="elforc.php";

output("`n");

switch($_GET[op])
	{
	case "chest":
		output("`tJe n�her du der Truhe kommst, desto st�rker wird das verlangen sie zu �ffnen. Nun verstehst du warum sie darum gek�mpft haben. Du kniest dich nieder und versuchst das Schloss zu knacken. Nach einiger Zeit hast du die Truhe auch erfolgreich ge�ffnet. In dieser befindet sich...`n`n");
		
		switch(rand(0,17))
			{
			case 0:case 1:
				output("`t...ein kleines Objekt. Es ist ein Ring mit einem gr�nen Stein in der Fassung. Dieser leuchtet immer leicht auf und strahlt eine unbekannte W�rme ab. Du nimmst ihn einfach und denkst, dass dieser Ring schon Gold in deine Taschen bringt.`n`n`#Du findest den `@Katzenring.");
				
				$item['tpl_name']="Katzenring";
				$item['tpl_description']="Ein Ring mit einem gr�nen Stein in der Fassung. Dieser leuchtet immer leicht auf und strahlt eine unbekannte W�rme ab.";
				$item['tpl_gold']=1500;
				$item['tpl_gems']=1;
				
				item_add($session['user']['acctid'], "katznring", $item);
				break;
				
			case 2:case 3:case 4:
				$gold=rand($session['user']['level']*500, ($session['user']['level']*500)+1500);
				
				output("`t...ein wahrer Goldsegen! Du nimmst dir so viel Gold wie du tragen kannst.`n`n`#Die Summe deines Goldsegens betr�gt `^".$gold."`# Goldst�cke");
				
				$session['user']['gold']+=$gold;
				break;
				
			case 5:case 6:case 7:
				output("`t...einen Eimer mit 2 Edelsteinen. Das ist ein Wunder! Fr�hlich gehst du mit deinen neuen Freunden in den Wald zur�ck.");
				
				$session['user']['gems']+=2;
				break;
				
			case 8:case 9:case 10:case 11:
				output("`t...ein `bGiftpfeil`b. Dieser hat sich schon tief in deine Hand verhakt. Da hilft nur noch der Heiler.`n`n`#Du hast nur noch 1 Lebenspunkt.");
				
				$session['user']['hitpoints']=1;
				break;
				
			case 12:
				output("`t...ein Schwert. Es leuchtet hell gelb und du sp�rst eine starke Aura, welche von diesem Schwert ausgeht.");
				
				$item['tpl_name']="`^G`q�`Qtterklin`qg`^e";
				$item['tpl_description']="Die Waffe der G�tter, also passt sie auch zu dir.";
				$item['tpl_gold']=rand(3000,5000);
				$item['tpl_gems']=rand(2,4);
				$item['tpl_value1']=20;
				
				item_add($session['user']['acctid'], "waffedummy", $item);
				break;
				
			default:
				output("`t...nichts. Sie ist vollkommen leer. Das muss doch ein schlechter Scherz sein! Diese Truhe hat nichts! Und daf�r diese ganze Arbeit. Du gehst zur�ck in den Wald und �rgerst dich die ganze Zeit.");
				break;
			}
			
		$session['user']['specialinc']="";
		
		addnav("In den Wald","forest.php");
		break;
		
	case "orc":
		output("`tDu hast dich entschieden dem Orc zu helfen. Deshalb schleichst du dich von hinten an die Elfe heran und erschl�gst ihn
mit deinem ".$session['user']['weapon'].". Als du dich nach dem Orc umschaust ist er pl�tzlich weg. Aber die Hauptsache ist, dass die 
goldene Truhe noch da ist.`n`n`#Dies hat dich 1 Waldkampf gekostet.");
		
		$session['user']['turns']--;
		$session['user']['reputation']-=3;
		
		addnav("Weiter","forest.php?op=chest");
		break;
		
	case "elfe":
		output("`tDu hast dich entschieden der Elfe zu helfen. Deshalb schleichst du dich von hinten an den Orc heran und erschl�gst ihn
mit deinem ".$session['user']['weapon'].". Als du dich nach der Elfe umschaust ist sie pl�tzlich weg. Aber die Hauptsache ist, dass die 
goldene Truhe noch da ist.`n`n`#Dies hat dich 1 Waldkampf gekostet.");
		
		$session['user']['turns']--;
		$session['user']['reputation']+=3;
		
		addnav("Weiter","forest.php?op=chest");
		break;
	
	case "wait":
		output("`tWarten. So kann wenigstens nichts passieren denkst du darum setzt du dich hinter einen Baum und wartest. Der Kampf sieht schrecklich aus und du denkst der Kampf w�rde nie Enden. Doch auf einmal Sacken beide zusammen, t�dlich verletzt ligen sie auf der Lichtung, dass ist deine Chance in die Truhe zu gucken. W�hrend du dich der Truhe n�herst spuckt die Elfe Blut und bringt noch einen letzten Satz heraus: `q\"Sei verflucht du Narr, wir h�tte ihn besiegen k�nnen.\"`t`n`n`#Die Wartezeit hat dich 5 Waldk�mpfe gekostet und dein unehrenhaftes Verhalten 2 Charmpunkte.");
		
		$session['user']['turns']-=5;
		$session['user']['charm']-=2;
		
		addnav("Weiter","forest.php?op=chest");
		break;
		
	case "help":
		output("`tDu m�chtest keine Partei ergreifen und gehst zwischen die beiden K�mpfenden. Noch bevor du etwas sagen kannst,
hast du auch schon die M�glichkeit Ramius pers�nlich auf die Nerven zugehen. Dein guter Wille sollte aber nicht umsonst sein. Daher erh�lst du 25 Gefallen bei Ramius.");
		
		$session['user']['alive']=false;
		$session['user']['hitpoints']=0;
		
		$session['user']['deathpower']+=25;
		
		addnav("Zu den Schatten","village.php");
		
		$session['user']['specialinc']="";
		break;
		
	case "exit":
		output("`tDu gehst zur�ck in den Wald und denkst dir nichts dabei. Sollen die beiden sich doch umbringen. Dein Leben setzt du deswegen nicht aufs Spiel.");
		
		$session['user']['specialinc']="";
		break;
		
	default:
		output("`tW�hrend du durch den Wald l�ufst kommst du an den Anfang einer Waldlichtung. Auf dieser siehst du eine `^Elfe`t und ein `@Orc`t gegeneinander k�mpfen. Nach einiger Zeit f�llt dir auch auf weshalb sie gegeneinander k�mpfen. Etwas im Hintergrund steht eine `^`bgoldene`b`t Truhe. Was wohl in der Truhe ist? Man k�nnte ja versuchen an die Truhe zu kommen.`n`n`#Was m�chtest du machen?");
		
		
		addnav("Warten","forest.php?op=wait");
		addnav("Elfe helfen","forest.php?op=elfe");
		addnav("Orc helfen","forest.php?op=orc");
		addnav("Dazwischen gehen","forest.php?op=help");
		addnav("Zur�ck");
		addnav("In den Wald","forest.php?op=exit");
		break;
	}

?>