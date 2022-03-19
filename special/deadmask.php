<?php

// Mask of death
//
// idea and coding by: Joshua Schmidtke [alias Mikay Kun]
// 
// build: 2006-08-08
// version: 1.0

if (!isset($session)) exit();

$session[user][specialinc]="deadmask.php";

$sql = 'SELECT ctitle,cname FROM account_extra_info WHERE acctid='.$session['user']['acctid'];
$res = db_query($sql);
$row_extra = db_fetch_assoc($res);

if ($row_extra['ctitle']=="`^Sandgeist")
	{
	switch($_GET[ac])
		{
		case "get":
			addnav("Schnell weg!","forest.php");
			
			$newtitle="`tSandgeist";
			
			$session[user][specialinc]="";
			$session[user][age]--;
						
			// Neuen Titel setzen, falls man nicht gerade der F�rst ist (TODO)
			//if() {
				user_retitle($session['user']['acctid'],false,$newtitle,true,-1);
				// Name aktualisieren
				user_set_name($session['user']['acctid']);
				addnews("`@Eine weitere Kreatur ist aufgetaucht bekannt als `tSandgeist`@!");
			
				output("`n`^Du hebst die Maske zu deinem Gesicht und setzt sie auf. Als die Maske dein Gesicht ber�hrt, brennt es in dir und du f�hlst wie sich dein K�rper ver�ndert. Die Maske ist ein Teil von dir und dein Aussehen hat sich dementsprechend ge�ndert. Du bist nun einer der `t`bSandgeister`b`^.`n`nErst jetzt bemerkst du das der Tempel in seiner alten Pracht erstrahlt. Ein Tempeldiener hat dich bemerkt und schreit: \"`qHalt im Namen `4M`tika`4y K`tu`4n`q's. Du bist hier nicht erw�nscht du verdammter Sandgeist!`^\". Schnell l�ufst du aus dem Tempel in den Wald. Nachdem du den Tempel verlassen hast bemerkst du, dass dir ein neuer Tag bevorsteht, was unverkennbar die Macht der Maske erkl�rt. Sie hat dich in der Zeit zur�ck geschickt.`n`nDu m�chtest gerade deines Weges gehen als ein komisches Gef�hl im R�cken dich schaudern l�sst. Hinter dir erscheint eine gro�e schwarze Kreatur, welche als `bDahakra`b bekannt ist. Er ist der W�chter des schwarzen Sandes. Dir bleibt nur die Flucht oder der Tod! Deine Entscheidung ist nicht schwer und du nimmst die Beine in die Hand.`n`n`n`#Du erh�ltst ".$session['user']['turns']." Waldk�mpfe.");
			//}
				
			$dkff=0;
			
			while(list($key,$val)=each($session['user']['dragonpoints']))
				{
				if ($val=="ff")
					{ $dkff++; }
				}
				
			$turnsperday=getsetting("turns",10);
			
			$session['user']['turns']=$turnsperday+$dkff;
			$session[user][hitpoints]=$session[user][maxhitpoints];
			
			$session[bufflist][dahakra] = array(
			"name"=>"`4Dahakra-Flucht",
			"rounds"=>150,
			"wearoff"=>"`^Der Dahakra verfolgt dich nicht l�nger.",
			"defmod"=>0.8,
			"atkmod"=>0.8,
			"roundmsg"=>"`4Du l�sst dich im Kampf ablenken, um nach dem Dahakra ausschau zu halten.",
			"activate"=>"roundstart");
						
			break;
			
		case "exit":
			$session[user][specialinc]="";
			output("Du gehst zur�ck in den Wald.");
			addnav("Weiter","forest.php");
			break;
		
		default:
			output("`n`^Als du durch den Wald wanderst bemerkst du einen zugewachsenen Eingang zu einem Tempel. Viele Steine sind aus der Wand gefallen und liegen verstreut herum. Du betrittst das Gew�lbe des Tempels, welcher fr�her wohl oft von J�ngern besucht wurde um zu einem Gott zu beten. Auf einigen W�nden sind uralte Schriftzeichen, welche unverkennbar den Gott `bCirdan`b loben.`nDu gehst weiter durch das Gew�lbe und bemerkst eine kleine Statue, welche noch gut erhalten ist. Als du n�her an diese gelangst bemerkst du das es eine Statue von einem Menschen ist. Diese Figur hat eine matt goldene Maske auf. Als du diese n�her betrachtest bemerkst du, dass die Maske locker auf der Figur sitzt. Ohne zu z�gern nimmst du die Maske und beschaust sie dir.`nIm inneren der Maske sind wieder die Schriftzeichen, welche schon zuvor an der Wand wahren. Nur diesesmal steht dort etwas anderes. Du kannst es nicht verstehen, aber du merkst das die Maske wohl eine gro�e Macht in sich tr�gt. Du bedenkst die Maske aufzusetzen, aber so ganz sicher bist du dir da nicht.`n`nWas m�chtest du tun?");
		
			addnav("Maske aufsetzen","forest.php?ac=get");
			addnav("Gew�lbe verlassen","forest.php?ac=exit");
			break;
		}
	}

else
	{
	if (isset($session[bufflist][dahakra]))
		{
		$session[user][specialinc]="";
		
		output("`^Du kommst an einem zugewachsenen Tempel vorbei und vor dir steht der `bDahakra`b. Dir bleibt keine Chanche zur Flucht! Du hast dein Leben gelebt. Nicht wie du wolltest, aber du hast gelebt.`n`n`4Du bist ausgel�scht worden.");
		
		$session['user']['alive']=false;
		$session['user']['hitpoints']=0;
			
		addnews($session[user][name]." `t wurde von dem `bDahakra`b ausgel�scht.");
		
		addnav("Weiter","village.php");
		}
		
	else
		{
		switch($_GET[ac])
			{
			case "help":
				$session[user][specialinc]="";
				output("`^Du nimmst ein Messer und wirfst es dem Goblin direkt in den R�cken. Er zerf�llt sofort zu Sand. Du siehst wie du die Maske aufsetzt und aufeinmal bist du in einem Sandsturm verschwunden.`n`nIrgendwoher wusstest du das es kein gutes Ende nehmen kann. Direkt vor dir taucht der `bDahakra`b auf. Durch deine Erfahrung mit diesem Wesen l�ufst du schnell in den Wald.");
			
				$session[bufflist][dahakra]=array(
				"name"=>"`4Dahakra-Flucht",
				"rounds"=>150,
				"wearoff"=>"`^Der Dahakra verfolgt dich nicht l�nger.",
				"defmod"=>0.8,
				"atkmod"=>0.8,
				"roundmsg"=>"`4Du l�sst dich im Kampf ablenken, um nach dem Dahakra ausschau zu halten.",
				"activate"=>"roundstart");
			
				addnav("Weiter","forest.php");
				break;
			
			case "kill":
				$session[user][specialinc]="";
				output("`^Da du dir sicher bist, dass es wohl das beste w�re einfach zu sterben l�sst du dein altes ICH sich von dem netten kleinen Goblin niederstechen.`n`nKurz nachdem sich dein K�rper aufgel�st hat bemerkst du, dass die Maske locker ist. Du nimmst sie einfach ab und packst sie zur�ck auf die Statue. Der n�chste wird sich wohl genauso freuen.`n`nFroh �ber deine neue Freiheit gehst du zur�ck in den Wald. Im Hinterkopf bist du froh dein Schicksal nun �ndern zu k�nnen.`n`n`#Du erh�lst 3 Waldk�mpfe.");
							
				// Eigenen Titel l�schen, falls erlaubt
				user_retitle($session['user']['acctid'],false,'',true,USER_NAME_NOCHANGE);
				// Name aktualisieren
				user_set_name($session['user']['acctid']);
												
				$session['user']['turns']+=3;
				
				$session[bufflist][mydie] = array(
				"name"=>"`4ICH-Verlust",
				"rounds"=>110,
				"wearoff"=>"`^Dein Leben verl�uft nun wieder normal.",
				"defmod"=>0.9,
				"atkmod"=>1.2,
				"roundmsg"=>"`4Dein neues Leben l�sst Wunder wirken.",
				"activate"=>"roundstart");
				
				addnav("Weiter","forest.php");
				break;
			
			default:
				output("`n`^Als du durch den Wald wanderst bemerkst du einen ge�fneten Eingang zu einem Tempel. Viele Steine sind aus der Wand gefallen und liegen verstreut herum. Du betrittst das Gew�lbe des Tempels, welcher fr�her wohl oft von J�ngern besucht wurde um zu einem Gott zu beten. Nun erinnerst du dich! Dies ist der Tempel, wo du die Maske gefunden hast. Vor dir siehst du eine Person die Maske beschauen. Aber wie ist das m�glich? Du bist doch mit ihr verbunden. Aber nein, das vor dir ist keine fremde Person. Das bist DU! Und soeben schleicht sich ein Goblin an dich ran. Was jetzt?");
			
				addnav("Lass mich sterben","forest.php?ac=kill");
				addnav("Ich rette mich","forest.php?ac=help");
				break;
			}
		}
	}

?>