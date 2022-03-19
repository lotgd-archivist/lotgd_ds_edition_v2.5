<?
// idea of gargamel @ www.rabenthal.de
/* changes by Salator:
Bankraub bei Sieg f�r Spieler mit mehr als 10DK
Hilfe rufen (f�r Neulinge) wieder aktiviert,
Erfolg bei (DK+1)*2 Spielern gleicher Rasse die sich in den letzten (900) Sekunden in die Felder ausgeloggt haben
*/
if (!isset($session))
{
    exit();
}
$session['user']['specialinc']='moocher.php';
switch ($_GET['op'])
{
case "":
    output("`nDu folgst dem Waldweg und bist besonders wachsam, weil du in einem
d�steren Abschnitt des Waldes gelandet bist. Dann passiert es:`n
Hinter einer Wegbiegung wirst du pl�tzlich von Dieben umringt!`n`n
`%Die Typen sehen wirklich furchterregend aus und fordern mit gezogenen Waffen
dein Gold.`0`nBleibt dir eine andere Wahl?`0");
    //abschluss intro
    addnav("Gold herausgeben","forest.php?op=give");
    addnav("K�mpfen","forest.php?op=fight");
    if ($session['user']['dragonkills']<6)
    {
        addnav("Hilfe rufen","forest.php?op=help");
    }
    break;
    
case "give":
    // Gold geben
    $gold = $session['user']['gold'];
    if ($session['user']['gold'] > 0 )
    {
        output("`nAngesichts der �bermacht der Diebe entschlie�t du dich, dein Gold
herauszugeben.`nDich schmerzt der `QVerlust von $gold Gold`0, aber noch schlimmer
w�re der Verlust deines Lebens gewesen.`0");
        $session['user']['gold']=0;
    }
    else
    {
        // aber nix dabei
        output("`nDu erkl�rst dem Anf�hrer, dass du zahlen willst.`nAls du ihm jedoch
deine leere Geldb�rse hinh�ltst, findet er das gar nicht komisch.`n`QEr gibt seiner
wilden Truppe ein Zeichen.... `0Die ganze Meute pr�gelt nun auf dich ein und sie
lassen erst von dir ab, als du schon tot scheinst.`n`n
`9Du bist aber gerade noch mit dem Leben davon gekommen und verlierst einen
permanenten Lebenspunkt.`0");
        $session['user']['maxhitpoints']-=1;
        $session['user']['hitpoints']=1;
    }
    $session['user']['specialinc'] = "";
    break;
    
    case"fight":   // k�mpfen
    output("`n`%Du entschlie�t dich zu k�mpfen und ziehst blitzschnell deine Waffe.`n`n`0");
    $hp = $session['user']['hitpoints'] * 2;
    $dam = e_rand(1,$hp);
            
    if ($session['user']['hitpoints'] > $dam )
    {
        //sieg
        output("In einem un�bersichtlichen Get�mmel wirst du hart getroffen, aber du
f�hrst deine Waffe auch erfolgreich. Nach einer ganzen Weile steht fest:`n`n
`@Du hast gewonnen!`n`n
Schwer gezeichnet feierst du deinen letzten Sieg des heutigen Tages. Wenigstens
hast du einiges an Erfahrung gewonnen.`0");
        $session['user']['hitpoints']-= $dam;
        $session['user']['turns']=0;
        $session['user']['experience']+= round($session['user']['experience']*0.08);
        if ($session['user']['dragonkills'] >10 && e_rand(1,3) != 0)
        {
            output('`n`n`%Beim Durchsuchen des Bandenf�hrers findest du einen Zettel. Es scheint ein Plan f�r einen Raub zu sein. Du bemerktst die Notiz `&'.getsetting('townname','Atrahor').' Bank.`n`n`@Du k�nntest die �berlebenden R�uber zwingen, etwas dazu zu sagen.');
            addnav('R?zum Reden zwingen','forest.php?op=ask');
            addnav('Zur�ck in den Wald','forest.php?op=leave');
        }
        else
        {
            $session['user']['specialinc'] = "";
        }
    }
    else
    {
        //niederlage
        output("Die Entscheidung, gegen die �bermacht der Diebe zu k�mpfen, war sicher
nicht deine beste! `QDu hast einfach keine Chance!`n`n
`6Nach einem kurzen, heftigen Kampf verabschiedest du dich vom Leben.`n`n
F�r deinen Mut wird dich jedoch Ramius belohnen.`0");
        $session['user']['alive']=false;
        $session['user']['gold']=0;
        $session['user']['hitpoints']=0;
        $session['user']['gravefights']+=2;
        $session['user']['specialinc'] = "";
        addnews("`^".$session['user']['name']."`# hatte keine Chance im Kampf gegen die Diebesbande. Auf Wiedersehen!");
        addnav("T�gliche News","news.php");
    }
    break;
    
    case"ask":   // ask for bankrobbery
    $moochers = e_rand(1,4);
    //Anzahl �berlebende der R�uberbande; Erfolgs-Chance
    output('`@Als du dir das Gemetzel betrachtest stellst du fest da� '.$moochers.' R�uber �berlebt haben, welche du zu dem geplanten Raub befragen kannst. 
    		Also h�ltst du einem die Klinge an den Hals.`n
    		Schon bald beginnt der R�uber um sein Leben zu wimmern und gibt dir die Informationen, die du haben willst...`nDu kannst nun ');
    output('`n- '.create_lnk('einen gemeinsamen Raub planen und die Beute teilen','forest.php?op=rob&anz='.$moochers), true);
    output('`n- '.create_lnk('die Sache alleine durchziehen','forest.php?op=rob'), true);
        
    if ($session['user']['reputation']>10)
    {
        output('`n`@- '.create_lnk('die Bande in den Kerker bringen','forest.php?op=inform'), true);
    }
    else
    {
        output('`n`@- '.create_lnk('schnell von hier verschwinden','forest.php?op=leave'), true);
    }
    break;
    
    case"rob":   // bankrobbery
    $session['user']['specialinc']='';
    if ($session['user']['profession'] == PROF_TEMPLE_SERVANT 
    	|| $session['user']['profession'] == PROF_JUDGE 
    	|| $session['user']['profession'] == PROF_JUDGE_HEAD
    	|| $session['user']['profession'] == PROF_GUARD
    	|| $session['user']['profession'] == PROF_GUARD_HEAD)
    {
    	$str_prof = $profs[$session['user']['profession']][$session['user']['sex']];
        output('`6Gerade noch rechtzeitig f�llt dir ein, dass du ja '.$str_prof.' bist. Also vergisst du die Sache schnell wieder.');
    }
    elseif ($session['user']['login'] == strip_appoencode(getsetting('fuerst',''),3)) {
    	output('`6Gerade noch rechtzeitig f�llt dir ein, dass du ja den F�rstentitel tr�gst und eine solche Aktion deinem Amt.. eher abtr�glich w�re. Also vergisst du die Sache schnell wieder.');
    }
    else
    {
        $moochers = $_GET['anz'];
        output("`6Vermummt ".($moochers?"und mit ".$moochers." R�ubern im Gefolge ":"")."st�rmst du in die Bank!`n");
        output("`6Die Gelegenheit scheint g�nstig. Du schreist `$`n`c`bDas ist ein Bank�berfall!`b`c`n`n");
        $moochers++;
        //Spieler z�hlt mit dazu
        switch (e_rand($moochers,6))
        {
        case 1 :
            //Alleingang kann �bel werden
            output("`6Tja, leider bist du heute nicht der Erste der auf diese Idee gekommen ist. Du rennst der Stadtwache direkt in die Arme. An Ort und Stelle wirt dir in einem Schnellverfahren der Proze� gemacht. Noch ewig wirst du den Richterspruch in deinen Gedanken h�ren:");
            output("`n`qIm Namen der Gesetze verdonnern wir die Kr�tze wegen wiederholten `QEierdiebstahls`q zu 500 Jahren schweren Kerkers mit einem Fasttag t�glich!");
            output("`n`n<a href='prison.php'>K�ss die Hand Herr Kerkermeister, ich bin wieder da.</a>");
            addnews("`b`@".$session['user']['name']."`b`& lief in der Bank der Stadtwache direkt in die Arme.");
            systemmail($session['user']['acctid'],"`$Du wurdest verurteilt!`0","Das in der Bank an Ort und Stelle einberufene Gericht hat dich zu 1 Tag Kerker verurteilt.`nDiese Strafe wird zu eventuell anderen Strafen hinzugerechnet, jedoch kann deine Haft dadurch nicht l�nger als ".getsetting('maxsentence',5)." Tage werden.");
            if ($session['user']['imprisoned'] < getsetting('maxsentence',5))
            {
                $session['user']['imprisoned']++;
            }
            addnav("In den Kerker!","prison.php");
            
            break;
            
        case 2 :
            output("`6Eine alte Frau dreht sich um und sagt `QJunger Mann, stelln Sie sich gef�lligst hinten an!`6`nDu hast Respekt vor dem Kr�ckstock der alten Dame und stellst dich wie gehei�en ans Ende der Schlange.`n`@Seit einer halben Stund� stehst du ewig in der Reih�, du `4verlierst einen Waldkampf`@ durch die bl�de Warterei.");
            $session['user']['turns']--;
            break;
            
        case 3 :
            output("`6Der Kassierer schaut dich an und meint `3Das kann ja jeder sagen.`6`nDu sagst `4Das kann eben nicht jeder sagen.`6`nDer Kassierer sagt `3Das ist ein Bank�berfall. Seht Ihr, das kann jeder sagen.`6`nEinige Anwesende sagen nun ebenfalls `qDas ist ein Bank�berfall.`6`n");
            output("`nDie Sache wird dir peinlich und du verl��t die Bank.");
            if (e_rand(1,2)==1 && $session['user']['reputation']>0)
            {
                output("`n`&Jemand hat dich trotz der Maskierung erkannt, dein Ansehen sinkt.");
                $session['user']['reputation']--;
            }
            break;
            
        case 4 :
            output("`6Der Kassierer sagt `@Nein! Was f�llt Euch ein?`n`qNa gut,`6 sagst du,`q dann zahl� ich halt was ein.`n`n");
            addnav('E?was Einzahlen','bank.php?op=deposit');
            break;
            
        case 5 :
            output("`6Du st�rzt auf den Schalter zu, h�ltst dem Kassier dein ".$session['user']['weapon']."`6 an den Hals und br�llst ihn an `\$Geld oder Leber!`&`n");
            output("`6 Der Kassierer �berreicht dir eine Schweineleber, frisch aus der Dark Horse Taverne, mit den Worten `@Ihr habt.. Leber gesagt?`n`6Wenigstens hast du jetzt was zu essen.");
            if ($session['user']['hitpoints'] < $session['user']['maxhitpoints'])
            {
                $session['user']['hitpoints']++;
            }
            break;
            
        case 6 :
            $row = db_fetch_assoc(db_query("SELECT name,sex FROM accounts WHERE loggedin=0 ORDER BY rand(".e_rand().") LIMIT 1"));
            output("`6Du greifst dir `^".$row['name']."`6 als Geisel und h�ltst ".($row['sex']?"ihr":"ihm")." dein ".$session['user']['weapon']."`6 an den Hals, w�hrend du den Kassierer anbr�llst `\$Geld oder Leben!`&`n");
            $foundgold = e_rand(200,600) * $session['user']['level'];
            output("`n`6Du erbeutest `Q".$foundgold." Gold");
            if ($moochers >1)
            {
                $foundgold=round($foundgold/$moochers);
                output("`6, wovon nach Aufteilung der Beute noch `Q".$foundgold." Gold`6 f�r dich bleiben,");
            }
            $session['user']['gold'] += $foundgold;
            switch (e_rand(1,3))
            {
            case 1 :
                //Raub mit sp�terer Verurteilung
            case 2 :
                output("`n`6Wenig sp�ter h�ngt dein Steckbrief �berall im Dorf. Es wird nicht lange dauern bis man dich fasst. Dein Ansehen leidet unter dieser Aktion.");
                $session['user']['reputation']-=2;
                addnews("`b`@".$session['user']['name']."`b`& erbeutete`^ ".$foundgold."`& Gold bei einem Bank�berfall.");
                addcrimes("`b`@".$session['user']['name']."`b`& erbeutete`^ ".$foundgold."`& Gold bei einem Bank�berfall.");
                break;
                
            case 3 :
                //Raub ohne Folgen
                output("`6 und entkommst unerkannt.");
                $meldung = '`$Gerade wurde die Bank von '.getsetting('townname','Atrahor').' ausgeraubt! Zeugen gesucht!';
                $sql = "INSERT INTO news(newstext,newsdate,accountid) VALUES ('".addslashes($meldung)."',NOW(),0)";
                db_query($sql) or die(db_error($link));
                
            default:
                output('`n`&Justitia meint es gut mit dir, im Zweifel f�r den Angeklagten.');
            }
            
            break;
            
        default : //Fehlerfall
            output('`&Irgendetwas stimmt hier nicht. Du z�hlst nocheinmal alle R�uber durch, mit dir sind es '.$moochers.'. Da hat sich doch ein Spion dazugeschummelt? Es ist besser, du l��t die Bank sein.');
        }
    }
    break;
    
    case"inform":   // petzen
    output('`@Als ehrenhafter B�rger �bergibst du die Schurken der Stadtwache. Du erh�ltst `^1000 Gold und 3 Edelsteine`@ als Belohnung und bist der `6Held des Tages`@!');
    $session['user']['gold']+=1000;
    $session['user']['gems']+=3;
    $session['user']['reputation']=50;
    addnews('`^'.$session['user']['name'].'`# hat eine Diebesbande gefasst und ist der Held des Tages!');
    $session['user']['specialinc']='';
    break;
    
case "leave":
    // win fight and exit
    output('`@Die Sache kommt dir merkw�rdig vor und du beschlie�t, schnell zu verschwinden.');
    $session['user']['specialinc']="";
    break;
    
    case"help":   // hilfe
    //    $needed = 6;  // im wald ben�tigte helfer
    $needed = ($session['user']['dragonkills']+1)*2;
    output("Du rufst um Hilfe. Ganz laut.`n
Die Diebensbande ist erstaunt, greift dich aber nicht an. Offensichtlich wollen sie
sich einwenig an deiner Angst weiden. Und sie sind sicher, dass dir eh niemand hilft.`n`n`0");
    
    //    $sql = "SELECT name,level,title,location FROM accounts WHERE locked=0 AND location=5 AND loggedin=1 AND laston>'".date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT",900)." seconds"))."' ORDER BY level DESC";
    $sql = "SELECT name,level,alive,race,location FROM accounts WHERE locked=0 AND location=0 AND alive=1 AND race='".$session['user']['race']."' AND loggedin=0 AND laston>'".date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT",900)." seconds"))."' ORDER BY level DESC";
    $result = db_query($sql) or die(sql_error($sql));
    $max = db_num_rows($result);
    $anz = db_num_rows($result);
    
    if ($anz >= $needed )
    {
        // genug helfer da
        output("`6Aber die Diebe haben nicht mit deinen Freunden aus dem Dorf gerechnet!
Die folgenden Bewohner von ".getsetting('townname','Atrahor')." sind n�mlich gerade in der N�he und eilen dir
zu Hilfe:`n`0");
        for ($i=0; $i<$needed; $i++)
        {
            //$max zu $needed ge�ndert
            $row = db_fetch_assoc($result);
            if ($row['name'] != $session['user']['name'] )
            {
                output("$row[tile] $row[name]`n`0");
            }
        }
        output("`n`2Gemeinsam besiegt ihr die Diebesbande. `0Du bedankst Dich bei allen
Helfern und versprichst, in der Taverne eine Runde zu schmeissen.`n
Du verlierst zwar einen Waldkampf, ziehst aber trotzdem gl�cklich weiter.`0");
        $session['user']['turns']-= 1;
    }
    else if ($anz == 0 )
    {
        output("`3Leider behalten die Diebe recht, denn im Moment sind keine anderen
Bewohner in der N�he. Sie schauen noch einen Moment zu, wie du verzweifelt auf
Hilfe wartest und greifen dich dann an.`n`n
`QNach einem kurzen, heftigen Kampf verabschiedest du dich vom Leben");
        $session['user']['alive']=false;
        $session['user']['gold']=0;
        if($session['user']['gems'] > 50) {
        	$int_losegems = min(round($session['user']['gems'] * 0.8),100);
        	if($int_losegems > 0) {
        		output(', sowie '.$int_losegems.' deiner Edelsteine. Schade drum..');
        		$session['user']['gems'] -= $int_losegems;
        		debuglog('Verlor '.$int_losegems.' Gems bei den Dieben im Wald.');
        	}
        }
        
        $session['user']['hitpoints']=0;
        addnews("`^".$session['user']['name']."`# hatte keine Chance im Kampf gegen die Diebesbande. Auf Wiedersehen!");
                        
        addnav("T�gliche News","news.php");
    }
    else
    {
        // nicht genug bewohner
        if ($anz == 1 )
        {
            output("`#Zwar werden Deine Hilferufe geh�rt. Aber der einzige Bewohner, der
zur Zeit auch noch in der N�he ist, erreicht dich nicht rechtzeitig.`n`0");
        }
        else
        {
            output("`#Deine Hilferufe werden zwar von $max Bewohnern geh�rt, die auch
gerade in der N�he sind, aber ungl�cklicherweise k�nnen die den Ort des
�berfalls nicht rechtzeitig erreichen. Du bleibst auf dich allein gestellt.`n`0");
        }
        output("Damit behalten die Diebe leider recht. Sie schauen noch einen Moment
zu, wie du verzweifelt auf Hilfe wartest und greifen dich dann an.`n`n
`QNach einem kurzen, heftigen Kampf verabschiedest du dich vom Leben");
        $session['user']['alive']=false;
        $session['user']['gold']=0;
        if($session['user']['gems'] > 50) {
        	$int_losegems = min(round($session['user']['gems'] * 0.8),100);
        	if($int_losegems > 0) {
        		output(', sowie '.$int_losegems.' deiner Edelsteine. Schade drum..');
        		$session['user']['gems'] -= $int_losegems;
        		debuglog('Verlor '.$int_losegems.' Gems bei den Dieben im Wald.');
        	}
        }
        $session['user']['hitpoints']=0;
        addnews("`^".$session['user']['name']."`# hatte keine Chance im Kampf gegen die Diebesbande. Auf Wiedersehen!");
        addnav("T�gliche News","news.php");
    }
    $session['user']['specialinc']="";
    break;
    
    default: //Fehlerfall
    output('`n`&Ein gewaltiger Blitz trifft dich. Du verlierst die Besinnung und tr�umst davon, Waldmonster zu erschlagen.');
    $session['user']['turns']++;
    $session['user']['specialinc']="";
    break;
}
?>