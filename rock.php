<?php
// MOD tcb, 17.5.05: Bettelstein -> Tempel

require_once("common.php");
// This idea is Imusade's from lotgd.net
if ($session['user']['dragonkills']>0 || su_check(SU_RIGHT_COMMENT)) addcommentary();

checkday();
if ($_GET[op]=="egg"){
	page_header("Das goldene Ei");
	output("`^Du untersuchst das Ei und entdeckst winzige Inschriften:`n`n");
	viewcommentary("goldenegg","Botschaft hinterlassen:",10,"schreibt");
	addnav("Zurück zum Club","rock.php");
}else if($_GET[op]=="egg2"){
	page_header("Das goldene Ei");
	$preis=$session[user][level]*60;
	output("`3Du fragst ein paar Leute hier, ob sie wissen, wo sich der Besitzer des legendären goldenen Eis aufhält. Einige lachen dich aus, weil du nach einer Legende suchst, ");
	output("schütteln nur den Kopf. Du willst gerade ".($session[user][sex]?"einen jungen Mann":"eine junge Dame")." ansprechen, als dich eine nervös wirkende Echse zur Seite zieht: ");
	output("\"`#Psssst! Ich weissss wen ihr ssssucht und wo ssssich diesssser Jemand aufhält. Aber wenn ich euch dassss ssssagen ssssoll, müsssst ihr mir einen Gefallen tun. Ich habe ");
	output("Sssschulden in Höhe von `^$preis`# Gold. Helft mir, diesssse losssszzzzuwerden und ich ssssag euch, wassss ich weissss. Anssssonssssten habt ihr mich nie gessssehen.`3\"");
	addnav("G?Zahle `^$preis`0 Gold","rock.php?op=egg3");
	addnav("Zurück zum Club","rock.php");
}else if($_GET[op]=="egg3"){
	page_header("Das goldene Ei");
	$preis=$session[user][level]*60;
	if ($session[user][gold]<$preis){
		output("`3\"`#Von dem bisssschen Gold kann ich meine Sssschulden nicht bezzzzahlen. Vergissss essss!`3\"");
	}else{
		$sql="SELECT acctid,name,location,loggedin,laston,alive,housekey,activated,restatlocation FROM accounts WHERE acctid=".getsetting("hasegg",0);
		$result = db_query($sql) or die(db_error(LINK));
		$row = db_fetch_assoc($result);
		$loggedin=user_get_online(0,$row);
		if ($row[location]==USER_LOC_FIELDS) $loc=($loggedin?"Online":"in den Feldern");
		if ($row[location]==USER_LOC_INN) $loc="in einem Zzzzimmer in der Kneipe";
		// part from houses.php
		if ($row[location]==USER_LOC_HOUSE){
			$loc="im Haussss Nummer ".($row['restatlocation'])."";
		}
		// end houses
		$row[name]=str_replace("s","ssss",$row[name]);
		$row[name]=str_replace("z","zzzz",$row[name]);
		output("`3Hissssa nimmt deine `^$preis`3 Gold, schaut sich nervös um und flüstert dir zu: \"`#$row[name]`# isssst $loc ".($row[alive]?"und lebt.":", isssst aber tot!")." Und jetzzzzt lassss mich bitte in Ruhe. Achja: Diesssse Information hasssst du nicht von mir!`3\"");
		$session[user][gold]-=$preis;
	}
	addnav("Zurück zum Club","rock.php");
}else if($_GET[op]=="idols"){
	page_header("Idole");
	output("`3Du mischst dich unter die Leute und beginnst kleine, belanglose Plaudereien.`nWie zufällig näherst du das Gesprächsthema einem ganz bestimmten Begriff an: `^Idol`3.`nNach einer ganzen Reihe von ernüchternden wie auch spöttischen Antworten stehst du plötzlich vor einem Zwerg, der genüsslich sein Ale aus einem 2-liter Krug schlürft.`nDu machst es kurz und kommst direkt zur Sache, doch der kräftige Mann scheint dich gar nicht wahrzunehmen.`nAls du deinen Redefluss für einen Moment unterbrichst, um Luft zu holen, deutet er dir mit der Hand die Zahl `^1`3, bevor er sich weiter seinem Ale widmet.");
	addnav("G?Zeige 1 Goldstück","rock.php?op=idols2");
	addnav("E?Zeige 1 Edelstein","rock.php?op=idols3");
	addnav("Zucke mit den Schultern","rock.php");
}else if($_GET[op]=="idols2"){
    page_header("Idole");
  if ($session['user']['gold']<1)
  {
    output("`4Du kannst dir ja noch nichtmal das leisten!`n`3Beschämt über deine Armut lässt du den Zwergen stehen und mischst dich wieder unter die Leute.`n");
    addnav("Zurück","rock.php");
  }
  else
  {
    $session['user']['gold']--;
    output("`3Mit stolzem Grinsen ziehst du ein funkelnagelneues, strahlendes und auf Hochglanz poliertes Goldstück aus deiner Tasche und hälst es dem Zwerg unter die Nase, als wären es die Kronjuwelen der südlichen Reiche.`nRecht unbeeindruckt nippt dieser jedoch weiter an seinem Ale und als du ihm gerade deinen Schatz noch darbietungsvoller hinhalten willst, rempelt dich jemand von hinten an und das Goldstück fällt dir aus der Hand!`nDas wirst du wohl nicht wieder sehen.`n");
    addnav("Mist!","rock.php?op=idols");
  }
}else if($_GET[op]=="idols3"){
    page_header("Idole");
  if ($session['user']['gems']<1)
  {
    output("`4Du kannst dir ja noch nichtmal das leisten!`n`3Beschämt über deine Armut lässt du den Zwergen stehen und mischst dich wieder unter die Leute.`n");
    addnav("Zurück","rock.php");
  }
  else
  {
    $price=$session['user']['level']*70;
    $session['user']['gems']--;
    output('`3Der Zwerg schnappt sich den Edelstein und lässt ihn in seine Tasche fallen, dann blickt er dich von oben bis unten an.`n`n"`@Wie ein Schatzjäger seht Ihr mir aber nicht aus.`3", urteilt er knapp und fährt fort ohne dir die Möglichkeit einer Rechtfertigung zu lassen,"`@Aber gut gut... Ihr wollt also etwas über die Idole wissen. Tja, wo fange ich an? Ja, genau! Es gibt 5 Stück davon, warum weiß keiner so recht. Manche munkeln es würde mit den Elementen zu tun haben, aber davon sind mir auch nur 3 bekannt:`nFeuer, Erz und Gold. Naja, vielleicht noch Silber, aber das zählt nicht so wirklich.`nWo war ich stehen geblieben? Ah ja, es gibt davon 5, das `^Idol des Waldläufers`@, das `!Idol des Genies`@, das `4Idol des Kriegers`@, das `2Idol des Anglers`@ und das `&Idol des Totenbeschwörers`@... Allen sagt man nach, dass sie magische Kräfte haben sollen und ihrem Träger übernatürliche Stärke verleihen.`nUnd ich glaube sogar zu wissen, wo sich diese befinden... könnten.`nAber oh je! Mein Krug ist leer und meine Stimme wird rauh! Ich kann Euch so unmöglich etwas erzählen... wäret Ihr so gut meine Rechnung zu zahlen und mir ein weiteres Ale zu holen?"`3`n`nDu stellst fest, dass sich die Rechnung des guten Zwerges auf mitlerweile `^'.$price.'`3 Goldmünzen beläuft, und weniger wird es sicher nicht je länger du wartest.`nWillst du zahlen und deine Frage stellen?');
   addnav("`^Idol des Waldläufers`&?","rock.php?op=idols4&what=1&price=$price");
   addnav("`!Idol des Genies`&?","rock.php?op=idols4&what=2&price=$price");
   addnav("`4Idol des Kriegers`&?","rock.php?op=idols4&what=3&price=$price");
   addnav("`2Idol des Anglers`&?","rock.php?op=idols4&what=4&price=$price");
   addnav("`&Idol des Totenbeschwörers?","rock.php?op=idols4&what=5&price=$price");
   addnav("Alles Humbug!","rock.php");
  }
}else if($_GET[op]=="idols4"){
    page_header("Idole");
    $price=$_GET['price'];
    if ($session['user']['gold']<$price)
    {
      output("`3Beschämt musst du dem Zwerg beichten, dass du nicht in der Lage bist seine Rechnung zu bezahlen und dich still und leise davon machen.`n");
      addnav("Zurück","rock.php");
    }
    else
    {
      $session['user']['gold']-=$price;
      $what=$_GET['what'];
      switch ($what)
      {
        case 1:
        $id="idolrnds";
        $name="`^Idol des Waldläufers";
        break;
        case 2:
        $id="idolgnie";
        $name="`!Idol des Genies";
        break;
        case 3:
        $id="idolkmpf";
        $name="`4Idol des Kriegers";
        break;
        case 4:
        $id="idolfish";
        $name="`2Idol des Anglers";
        break;
        case 5:
        $id="idoldead";
        $name="`&Idol des Totenbeschwörers";
        break;
      }
      
		$sql="SELECT acctid,accounts.name,location,loggedin,laston,alive,housekey,activated,restatlocation FROM accounts LEFT JOIN items it ON acctid=it.owner WHERE it.tpl_id='$id'";
        $result = db_query($sql) or die(db_error(LINK));
        $price=round($price*1.5);
        output('`3Der Zwerg nimmt dankend ein neues Ale entgegen und beobachtet mit Freude wie du seine Zeche zahlst.`nDann raunt er dir zu:`n');
        if (db_num_rows($result)>0)
        {
		$row = db_fetch_assoc($result);
		$loggedin=user_get_online(0,$row);
		if ($row[location]==USER_LOC_FIELDS) $loc=($loggedin?"online":"in den Feldern");
		if ($row[location]==USER_LOC_INN) $loc="in einem Zimmer in der Kneipe";
		if ($row[location]==USER_LOC_HOUSE){
			$loc="im Haus Nummer ".($row['restatlocation'])."";
		}
        output('"`@Jemand namens '.$row[name].'`@ soll derzeit das '.$name.' `@mit sich herum schleppen, befindet sich '.$loc.'`@ und '.($row[alive]?" erfreut sich bester Gesundheit.":" ist mausetot!").'`n');
        }
        else
        {
        output('"`@Tief im Wald, so munkelt man, gibt es ein Grab, das Grab eines alten Recken, den letztendlich auch sein Schicksal ereilt hat und den das Idol nicht vor dem Tod bewahren konnte.`nIn diesem Grab werdet Ihr finden was Ihr such, wenn Euch nicht ein Anderer zuvor kommt!`n');
        }
        output('Wenn Ihr mehr wissen wollt... Nur zu... Fragt! Mein Krug ist schon wieder leer und da gibt es auch noch eine alte Rechnung zu zahlen, ich glaube sie beträgt um die `^'.$price.'`@ Goldmünzen.`3"');
        addnav("`^Idol des Waldläufers`&?","rock.php?op=idols4&what=1&price=$price");
        addnav("`!Idol des Genies`&?","rock.php?op=idols4&what=2&price=$price");
        addnav("`4Idol des Kriegers`&?","rock.php?op=idols4&what=3&price=$price");
        addnav("`2Idol des Anglers`&?","rock.php?op=idols4&what=4&price=$price");
        addnav("`&Idol des Totenbeschwörers?","rock.php?op=idols4&what=5&price=$price");
	    addnav("Zurück zum Club","rock.php");
    }
} elseif($_GET['op'] == 'map') {
	
	page_header('Die Schatzkarten');
	
	$preis = $session['user']['level'] * 60;
		
	if($_GET['act'] == 'ok') {
		
		if ($session['user']['gold']<$preis){
			output("`3\"`#Von dem bisssschen Gold kann ich meine Sssschulden nicht bezzzzahlen. Vergissss essss!`3\"");
		}
		else{
			$sql = 'SELECT a.name,a.loggedin,a.location,a.laston,a.acctid,a.activated,a.restatlocation FROM items i
					LEFT JOIN accounts a ON a.acctid=i.owner WHERE i.tpl_id="mapt" AND i.owner!='.$session['user']['acctid'].' GROUP BY i.owner ORDER BY RAND() LIMIT 4';
			$res = db_query($sql);			
			
			output("`3Hissssa nimmt deine `^$preis`3 Gold, schaut sich nervös um und flüstert dir zu: `n");
			
			if(db_num_rows($res) == 0) {
				output('NIEMAND hat eine Ssssschatzzzkarte!');
			}
			else {
			
				while($p = db_fetch_assoc($res)) {
					
					$loggedin=user_get_online(0,$p);
					if ($p[location]==USER_LOC_FIELDS) $loc=($loggedin?"Online":"in den Feldern");
					if ($p[location]==USER_LOC_INN) $loc="in einem Zzzzimmer in der Kneipe";
					// part from houses.php
					if ($p[location]==USER_LOC_HOUSE){
						$loc="im Haussss Nummer ".($p['restatlocation'])."";
					}
					// end houses
					$p[name]=str_replace("s","ssss",$p[name]);
					$p[name]=str_replace("z","zzzz",$p[name]);
					
					output('`n`3'.$p['name'].'`3 besssitzt mindestens einen Teil und issst '.$loc.'.');
												
				}			
				
				output('`n`nDassss weißßt du aber nicht von mir, und nun verssssschwinde.');
				
			}	// END if vorhanden
			
			$session[user][gold]-=$preis;
			
		}	// END if gold
		
	}	// END if ok
	else {
	
		output("`3Du fragst ein paar Leute hier, ob sie wissen, wer bisher Schatzkarten besitzt. Da zieht dich eine nervös wirkende Echse zur Seite: ");
		output("\"`#Psssst! Ich weissss was ihr ssssucht und wo sssssich einiges davon befindet. Aber wenn ich euch dassss ssssagen ssssoll, müsssst ihr mir einen Gefallen tun. Ich habe ");
		output("Sssschulden in Höhe von `^$preis`# Gold. Helft mir, diesssse losssszzzzuwerden und ich ssssag euch, wassss ich weissss. Anssssonssssten habt ihr mich nie gessssehen.`3\"");
		addnav("G?Zahle `^$preis`0 Gold","rock.php?op=map&act=ok");
	}
	
}
else{
	if ($session['user']['dragonkills']>0 || su_check(SU_RIGHT_COMMENT)){
		page_header("Club der Veteranen");
	
		output("`b`c`2Der Club der Veteranen`0`c`b");
	
		output("`n`n`4Irgendetwas in dir zwingt dich, den merkwürdig aussehenden Felsen zu untersuchen. Irgendeine dunkle Magie, gefangen in uraltem Grauen.");
		output("`n`nAls du am Felsen ankommst, fängt eine alte Narbe an deinem Arm an zu pochen. Das Pochen ist mit einem rätselhaften Licht synchron, ");
		output("das jetzt von dem Felsen zu kommen scheint. Gebannt starrst du auf den schimmernden Felsen, der eine Sinnestäuschung von dir abschüttelt. Du erkennst, daß das mehr ");
		output("als ein Felsbrocken ist. Tatsächlich ist es ein Eingang, über dessen Schwelle du andere wie dich siehst, die auch die selbe Narbe wie du tragen. Sie ");
		output("erinnert dich irgendwie an den Kopf einer dieser riesigen Schlangen aus Legenden. Du hast den Club der Veteranen entdeckt und betrittst dieses unterirdische Gewölbe.");
		output("`n`n");
		if ($session[user][acctid]==getsetting("hasegg",0)){
			output("Da du dich hier zurückziehen kannst, könntest du das `^goldene Ei`4 mal näher untersuchen.`n`nDie Veteranen unterhalten sich:`n");
			addnav("Ei untersuchen","rock.php?op=egg");
		}else if (getsetting("hasegg",0)>0){
			output("Wenn dir hier niemand sagen kann, wo sich der Besitzer des goldenen Eis aufhält, dann wird es dir niemand sagen können.");
			addnav("Nach dem goldenen Ei fragen","rock.php?op=egg2");
		}
             if (getsetting("idols_acttivated",1)>0){
                output("Ebenso ist es wahrscheinlich, dass du hier etwas über die Idole erfahren kannst.`n");
     			addnav("Nach den Idolen fragen","rock.php?op=idols");
             }
		if (!$session['user']['prefs']['nosounds']) {
			switch(e_rand(1,9)){
			case 1:
			output("<embed src=\"media/alf.mid\" width=10 height=10 autostart=true loop=false hidden=true volume=100>",true);	
			break;
			case 2:
			output("<embed src=\"media/babyonemoretime.mid\" width=10 height=10 autostart=true loop=false hidden=true volume=100>",true);	
			break;
			case 3:
			output("<embed src=\"media/entertainer2.mid\" width=10 height=10 autostart=true loop=false hidden=true volume=100>",true);	
			break;
			case 4:
			output("<embed src=\"media/escape.mid\" width=10 height=10 autostart=true loop=false hidden=true volume=100>",true);	
			break;
			case 5:
			output("<embed src=\"media/goldeneye.mid\" width=10 height=10 autostart=true loop=false hidden=true volume=100>",true);	
			break;
			case 6:
			output("<embed src=\"media/layla.mid\" width=10 height=10 autostart=true loop=false hidden=true volume=100>",true);	
			break;
			case 7:
			output("<embed src=\"media/mybonnie.mid\" width=10 height=10 autostart=true loop=false hidden=true volume=100>",true);	
			break;
			case 8:
			output("<embed src=\"media/sandman.mid\" width=10 height=10 autostart=true loop=false hidden=true volume=100>",true);	
			break;
			case 9:
			output("<embed src=\"media/locomo.mid\" width=10 height=10 autostart=true loop=false hidden=true volume=100>",true);	
			break;
			}
		}
		
		output('`nUnd ganz bestimmt findest du hier auch Informationen über die Besitzer der Schatzkartenteile.`n`nDie Veteranen unterhalten sich:`n');
		addnav('Nach Schatzkarten fragen','rock.php?op=map');
			
		viewcommentary("veterans","Angeben:",30,"prahlt");
		addnav("Schrein des Ramius","shrine.php");
		addnav("Schrein der Erneuerung","rebirth.php");
		//if( $session['user']['superuser'] > 0 ){
			addnav('Runenmeister','runemaster.php?op=master');
		//}
	}else{
		page_header("Seltsamer Felsen");
		output("Du näherst dich dem seltsam aussehenden Felsen. Nachdem du ihn eine ganze Weile angestarrt hast, bleibt es auch weiterhin nur ein seltsam aussehender Felsen.`n`n");
		output("Gelangweilt gehst du zum Dorfplatz zurück.");
	}
}
// Jetzt im Tempel..
// addnav("Der Bettelstein","beggar.php");
addnav("Zurück zum Dorf","village.php");

page_footer();
?>
