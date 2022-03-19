<?php

// A wonderful magic grassyfield with flowers
//
// idea by:  Jaromir Sosa (ICQ: 271-583-788)
// coding by: Joshua Schmidtke [alias Mikay Kun]
// 
// build: 2006-08-25
// version: BETA

if (!isset($session)) exit();

$session['user']['specialinc']="flowerfield.php";

output("`n");

switch($_GET[op])
	{
	case "fire":
		switch($_GET[uop])
			{
			case "run":
				output("`tDu springst über die Flammen und rennst in Richtung Wald. \"Aber das Feuer brennt schön!\", denkst du, während du im Wald verschwindest.`n`n`#Du hast die Hälfte deiner Lebenspunkte verloren.");
				
				if ($session['user']['hitpoits']>1)
					{ $session['user']['hitpoits']=round($session['user']['hitpoits']/2); }
				
				addnav("In den Wald","forest.php");
				$session['user']['specialinc']="";
				break;
				
			case "wait":
				output("`tEine Windbriese facht die Flammen weiter an und du hast keine Chance zu entkommen.`n`n`#Du bist gestorben.");
				
				addnews($session['user']['name']."`$ hat sich angezündet. Nur noch Asche erinnert an diese arme Person.");
				killplayer(0,0,0,"");
				$session['user']['specialinc']="";
				
				addnav("Zu den Schatten","village.php");
				break;
				
			default:
				output("`tSo was ist nichts für dich, aber ein schönes Feuer wäre schön. Und du schnappst dir eine Fackel und läufst durch die Blumenwiese. Alles was hinter dir liegt brennt nun im Hellen Schein.`n`nWas kannst du eigentlich? Du bist im Kreis gelaufen und vom Feuer umzingelt.");
				$session['user']['charm']--;
				break;
			}
		
		addnav("Was nun?");
		addnav("Warten","forest.php?op=fire&uop=wait");
		addnav("Weglaufen","forest.php?op=fire&uop=run");
		break;
		
	case "sleep":
			switch($_GET[uop])
			{	
			case "go":
				output("`tDu kannst es einfach nicht lassen und läufst noch mal durch die Wiese. Dabei stolperts du über irgendetwas. Als du genau hinschaust findest du einen eingegrabenen Edelstein. Zum ausgraben brauchst du allerdings eine kurze Weile.");
				$session['user']['charm']++;
				$session['user']['turns']-=1;
				
				addnav("Zurück");
				break;
				
			default:
				output("`tDu gehst auf die Wiese und läufst durch das Blumenmeer. Die ganzen verschiedenen Düfte bleiben an dir haften und du bekommst 1 Charmepunkt für deinen tollen Geruch.`n`n`#Was willst du nun machen, denn nur rumlaufen ist doch langweilig?");
				$session['user']['charm']++;
				
				addnav("Was nun?");
				addnav("Weiterlaufen","forest.php?op=sleep&uop=go");
				break;
			}
			
		addnav("In den Wald","forest.php?op=exit");
		break;
	
	case "exit":
		output("`tNach nur drei Schritten bist du wieder im Wald. Recht unheimlich das ganze.");
		
		$session['user']['specialinc']="";
		forest();
		break;
		
	default:
		output("`tAuf deinem Streifzug durch den Wald findest du eine wunderbar duftende Blumenwiese. Es ist bereits dunkel und überall stehen Fackeln, welche die Umgebung noch schöner wirken lassen. \"Sehr Idyllisch und Ruhig hier. Dieser Ausblick! Ein wahres Paradies.\", denkst du dir. Besonders fällt eine Art Burggraben auf, welcher die gesamt Wiese umgibt. In der Mitte der Wiese ist ein kleiner Altar aufgebaut. Auf diesem ist ein kleines Symbol abgebildet. Es könnte vielleicht ein Schutzzeichen sein.`n`n`#Was willst du hier machen?");
				
		addnav("Feuer","forest.php?op=fire");
		addnav("Ausruhen","forest.php?op=sleep");
		#addnav("Zum Altar","forest.php?op=magic");
		addnav("Zurück");
		addnav("In den Wald","forest.php?op=exit");
		break;
	}

?>