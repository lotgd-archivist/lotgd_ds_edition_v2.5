<?php
/*************************************************************
HUNTER'S LODGE for LoGD 0.9.7 ext (GER)
by weasel and anpera

mod by tcb - Spezielle Möbelstücke
Auslagerung der Schlüssel by Maris

*************************************************************/

require_once 'common.php';
require_once(LIB_PATH.'house.lib.php');

define('DP_KOSTEN_SPECIAL_ITEM',350);
define('DP_KOSTEN_LONG_BIO',300);
define('DP_MAX_SPECIAL_ITEMS',30);
define('DP_KOSTEN_HISTORY',75);
define('GEMS_KOSTEN_HISTORY',5);

addcommentary();
page_header('Jägerhütte');

addnav('Zurück');
addnav('D?zum Dorf','village.php');
if(isset($_GET['op'])) addnav('h?zur Jägerhütte','lodge.php');

if ($_GET['op']!='points')
{
	addnav('Punkte','lodge.php?op=points');
}
if(!isset($_GET['op']))
{
	addnav('Empfehlungen','referral.php');
}
$config = unserialize($session['user']['donationconfig']);
$pointsavailable=$session['user']['donation']-$session['user']['donationspent'];

if ($_GET['op']=='')
{
	output('`b`cDie Jägerhütte`c`b');

	output('`0Du folgst einem schmalen Pfad, der hinter den Ställen entlang führt. Am Ende dieses Pfades steht die Jägerhütte. Ein Türsteher stoppt dich und möchte deine Mitgliedskarte sehen. `n`n ');

	if ($session['user']['donation']>=5)
	{
		output('Nach dem Zeigen deiner Mitgliedskarte sagt er, "`7Sehr schön, willkommen in der J. C. Petersen Jägerhütte.  Du hast noch `$`b'.$pointsavailable.'`b`7 Punkte zur Verfügung,`0" und lässt dich rein.
				`n`n
				Du betrittst einen Raum, der durch einen grossen Kamin am anderen Ende beherrscht wird. Die holzgetäfelten Wände werden mit Waffen, Schilden und angebrachten Jagdtrophäen einschliesslich den Köpfen von einigen Drachen bedeckt, die im flackernden Licht des Kamines zu leben scheinen.
				`n`n
				Viele hohe Stühle füllen den Raum.  In dem Stuhl der am nächsten beim Feuer ist, sitzt J. C. Petersen und liest
				"Alchemie Heute."
				`n`n
				Während du dich näherst, hebt ein grosser Jagdhund, der zu seinen Füssen liegt, den Kopf und überlegt ob er dich kennt.
				Als er dich als vertrauenswürdig einstuft legt er sich wieder hin und schläft weiter. `b`4Solltest Du allerdings auch nur auf die Idee kommen,
				die Anwesenden mit Protzereien oder Gejammer über die Anzahl deiner Punkte zu langweilen, wird er deine Mitgliedskarte genüsslich zwischen seinen rasiermesserscharfen Zähnen zerfetzen. Mindestens...`4`b
				`n`n
				In der Nähe ein schroffes Jägergerede:`n');
		viewcommentary('hunterlodge','Hinzufügen',25);

		addnav('Punkte einsetzen');
		addnav('Charmepunkte abfragen (20 Punkte)','lodge.php?op=charm');

		addnav('10 Nächte in der Kneipe (30 Punkte)','lodge.php?op=innstays');
		addnav('2 Edelsteine (50 Punkte)','lodge.php?op=gems');
		addnav('Extra Waldkämpfe für 30 Tage (100 Punkte)','lodge.php?op=forestfights');
		addnav('H?Heilerin Golinda für 30 Tage (100 Punkte)','lodge.php?op=golinda');

		addnav('B?Zur Burg reiten (100 Punkte)','lodge.php?op=reiten1');

		addnav('r?Präparierset (200 Punkte)','lodge.php?op=trophy');
		addnav('v?PvP-Immunität (300 Punkte)','lodge.php?op=immun');

		addnav('Charakter');
		addnav('Shortcuts kaufen (100 Punkte)','lodge.php?op=shortcut1');
		addnav('Längere Bio ('.DP_KOSTEN_LONG_BIO.' Punkte)','lodge.php?op=bio');
		addnav('Farbiger Name ('.($config['namechange'] ? '25':'300').' Punkte)','lodge.php?op=namechange');
		addnav('Aufzeichnungen ('.DP_KOSTEN_HISTORY.' Punkte)','lodge.php?op=history');

		if ($session['user']['donation']>=2000)
		{
			addnav('Sonderbonus');
			addnav('Titel ändern (50 Punkte)','lodge.php?op=title');
		}

		addnav('Heimwerkerbedarf');
		if ($session['user']['house']>0 && $session['user']['housekey']==$session['user']['house'])
		{

			addnav('u?Hausschlüssel','lodge.php?op=keys1');
			addnav('i?Privatgemächer','lodge.php?op=private_keys1');

		}
		addnav('M?Einzigartiges Möbelstück ('.DP_KOSTEN_SPECIAL_ITEM.' Punkte)','lodge.php?op=item');
		addnav('Giftphiole erwerben (20 Punkte)','lodge.php?op=poison');

	}
	else
	{
		output('Du ziehst die Karte deines Lieblingsgasthauses heraus, wo 9 von 10 Slots mit dem kleinen Profil von Cedrik abgestempelt sind.
				`n`n
				Der Türsteher schaut flüchtig auf deine Karte, rät dir nicht soviel zu trinken und weist dir den Weg zurück.');

	}
}
else if ($_GET['op']=='points')
{
	output('`&`c`bPunkte:`b`n`n`c
		Legend of the Green Dragon bietet dir die Möglichkeit, spezielle "Donationpoints" zu sammeln, mit denen du Sonderfunktionen freischalten kannst.`n
		Diese Punkte gibt es für besondere (geheime) Leistungen und für sogenannte "Referrals" (Empfehlungen). Erst wenn du mindestens 5 Donationpoints besitzt, kommst du in die Jagdhütte.`n`n
		Klicke im Eingangsbereich der Jägerhütte auf "Empfehlungen", wenn du wissen willst, wie du auf diesem Weg an Donationpoints kommst.');


	output('`nWenn du den ursprünglichern Erfinder von LoGD oder die Entwicklung auf diesem Server belohnen willst, kannst du pro gespendetem US-$ ebenfalls 100 Punkte kassieren.
			Schicke dazu irgendeinen Beweis deiner Spende, z.B. einen Screenshot der PayPal-Bestätigung, an '.getsetting('gameadminemail','').'.
			Für eine Spende an den Erfinder (Eric Stevens a.k.a. MightyE) benutze den PayPal-Link, der auf jeder Seite oben rechts zu finden ist.');
	output('`n`n
			`bDas kannst du mit diesen Punkten anstellen:`b`n
			- Umsonst in der Kneipe wohnen (10 Nächte für 30 Punkte).`n
			- Edelsteine kaufen (2 Stück für 50 Punkte)`n
			- Zusätzliche Waldkämpfe kaufen (100 Punkte für 30 Tage lang 1 extra Kampf; maximal 5 mehr pro Tag)!`n
			- "Zur Burg reiten" im Wald freischalten (100 Punkte),`n
			- Zusätzliche Shortcuts erwerben (100 Punkte),`n
			- Ein Präparierset kaufen (200 Punkte),`n
			- PvP-Immunität kaufen (300 Punkte für permanente Immunität),`n
			- Die Zeichenbegrenzung deiner Bio von 255 auf '.getsetting('longbiomaxlength',4096).' erhöhen!,`n
			- Einen farbigen Namen machen (300 Punkte). Umfärben kostet nur noch 25 Punkte. `n
			- Anzeige der Charmepunkte (20 Punkte)`n
			- Tödliches Gift erwerben (20 Punkte)`n
			- Eintrag in deine Aufzeichnungen ('.DP_KOSTEN_HISTORY.' Punkte)`n
			- Ersatzschlüssel (10) und zusätzliche Schlüssel (100 Punkte + 10 Edelsteine) für dein Haus kaufen.`n
			- Besondere, von dir gestaltete Möbel ('.DP_KOSTEN_SPECIAL_ITEM.' Punkte) für dein Haus kaufen.`n
			- Ab 2000 gesammelten Punkten (ob ausgegeben oder nicht) kannst du dir für 50 Punkte einen eigenen Titel aussuchen.`n
			`n`n`7Du hast noch `$`b'.$pointsavailable.'`b`7 Punkte von insgesamt `4'.$session['user']['donation'].' `7gesammelten Punkten übrig.
			');
}
else if ($_GET['op']=='golinda')
{
	output('30 Tage Zugang zu Golinda der Heilerin kosten 100 Punkte. Golinda heilt zum halben Preis.');
	if ($pointsavailable<100)
	{
		output('`n`n`$Du hast nicht genug Punkte!`0');
	}
	else
	{
		addnav('Betätige Zugang zu Golinda');
		addnav('JA','lodge.php?op=golindaconfirm');
	}
}
else if ($_GET['op']=='golindaconfirm')
{
	if ($pointsavailable >= 100)
	{
		$config['healer'] += 30;
		output('J. C. Peterson gibt dir eine Karte und sagt "Mit dieser Karte kannst du an 30 verschiedenen Tagen bei Golinda vorstellig werden."');
		$session['user']['donationspent']+=100;
		debuglog('Gab 100DP für Golinda');
	}
}
else if ($_GET['op']=='reiten1')
{
	if ($config['castle'])
	{
		output('Du hast diese Option bereits gekauft. Um zur Burg zu kommen, brauchst du ansonsten nur ein `bPferd`b. Ein `iPferd`i ist ein Tier der Kategorie "Pferde" in Mericks Stall.');
	}
	else
	{
		output('Hiermit schaffst du dir die Möglichkeit, mit einem Reittier im Wald auch zur Burg reiten zu können. Du kannst nur auf Pferden reiten, also die Tiere in Merick\'s Stall, die in der Kategorie "Pferde" stehen.');
		if ($pointsavailable<100)
		{
			output('`n`n`$Du hast nicht genug Punkte!`0');
		}
		else
		{
			addnav('Betätige Freischaltung');
			addnav('JA','lodge.php?op=reiten2');
		}
	}
}
else if ($_GET['op']=='reiten2')
{
	if ($pointsavailable >= 100)
	{
		$config['castle'] = 100;
		output('J. C. Peterson gibt dir eine Karte und sagt "Mit dieser Karte findest du den Weg zur Burg, wenn du ein Pferd hast."');
		$session['user']['donationspent']+=100;
		debuglog('Gab 100DP für Orkburg');
	}
}
else if ($_GET['op']=='shortcut1')
{
	$sqlex = 'SELECT shortcuts FROM account_extra_info WHERE acctid='.$session['user']['acctid'];
	$resex = db_query($sqlex) or die(db_error(LINK));
	$rowex = db_fetch_assoc($resex);

	if ($rowex['shortcuts']>=9)
	{
		output('Du hast bereits 10 Shortcuts.`nMehr kannst du nicht erwerben!');
	}
	else
	{
		output('Hiermit kannst du dir einen weiteren Shortcut erwerben.`n
Shortcuts belegst du in deinen Einstellungen mit kurzen Texten (Namen, häufig verwendete Begriffe etc.) und kannst sie im RPG mit den Kürzeln %x0 - %x9 aufrufen, wodurch sie durch den von dir voreingestellten Text ersetzt werden.`nSie dürfen farbig sein, aber keine anderen Shortcuts enthalten.`n`n
Du hast bereits `^'.($rowex['shortcuts']+1).'`& von `^10 möglichen`& Shortcuts.`n`0');
		if ($pointsavailable<100)
		{
			output('`n`n`$Du hast nicht genug Punkte!`0');
		}
		else
		{
			addnav('Betätige Freischaltung');
			addnav('JA','lodge.php?op=shortcut2');
		}
	}
}
else if ($_GET['op']=='shortcut2')
{
	if ($pointsavailable >= 100)
	{
		$sql = 'UPDATE account_extra_info SET shortcuts=shortcuts+1 WHERE acctid='.$session['user']['acctid'];
		db_query($sql) or die(db_error(LINK));
		output('J. C. Peterson gewährt dir einen weiteren Shortcut und gibt dir die Möglichkeit dich eleganter auszudrücken.');
		$session['user']['donationspent']+=100;
		debuglog('Gab 100DP für Shortcuts');
		$config['shortcuts']+=1;
	}
}
else if ($_GET['op']=='forestfights')
{
	if (!is_array($config['forestfights']))
	{
		$config['forestfights']=array();
	}
	output('1 Extra Waldkampf pro Tag für 30 Tage kostet 100 Punkte. Du bekommst einen extra Waldkampf an jedem Tag, an dem du spielst.`n');
	if ($pointsavailable<100)
	{
		output('`n`n`$Du hast nicht genug Punkte!`0');
	}
	else
	{
		addnav('Bestätige Extra Waldkämpfe');
		addnav('JA','lodge.php?op=fightbuy');
	}
	reset($config['forestfights']);
	foreach($config['forestfights'] as $key=>$val)
	//while (list($key,$val)=each($config['forestfights']))
	{
		output("Du hast noch {$val['left']} Tage, an denen zu einen zusätzlichen Waldkampf für deine am {$val['bought']} bekommst.`n");
	}
}
else if ($_GET['op']=='fightbuy')
{
	if (count($config['forestfights'])>=5)
	{
		output('Du Kannst maximal 5 extra Waldkämpfe haben pro Tag.`n');
	}
	else
	{
		if ($pointsavailable>0)
		{
			array_push($config['forestfights'],array('left'=>30,'bought'=>date('M d')));
			output('Du wirst in den nächsten 30 Tagen, an denen du spielst, einen extra Waldkampf haben.');
			$session['user']['donationspent']+=100;
			debuglog('Gab 100DP für extra Waldkampf');
		}
		else
		{
			output('Extra Waldkämpfe zu kaufen kostet 100 Punkte, aber du hast nicht so viele.');
		}
	}
}
else if ($_GET['op']=='innstays')
{
	output('10 freie Übernachtungen in der Kneipe kosten 30 Punkte. Bist du dir sicher, dass du das willst?');
	if ($pointsavailable<30)
	{
		output('`n`n`$Du hast nicht genug Punkte!`0');
	}
	else
	{
		addnav('Bestätige 10 freie Übernachtungen');
		addnav('JA','lodge.php?op=innconfirm');
	}
}
else if ($_GET['op']=='innconfirm')
{
	if ($pointsavailable>=30)
	{
		output('J. C. Petersen gibt dir eine Karte und sagt "Coupon: Gut für 10 Übernachtungen in der Schenke Zum Eberkopf"');
		$config['innstays']+=10;
		$session['user']['donationspent']+=30;
		debuglog('Gab 30DP für Schlafen in Kneipe');
	}
}
else if ($_GET['op']=='charm')
{
	output('Du fragst J. C. Petersen, ob er dein Aussehen beurteilen kann. Er mustert dich kurz und verspricht dir dann, dass er dir für die Kleinigkeit von 20 Punkten eine ehrliche Antwort geben wird.');
	if ($pointsavailable<20)
	{
		output('`n`n`$Du hast nicht genug Punkte!`0');
	}
	else
	{
		addnav('Bestätige Charmepunkt-Anzeige');
		addnav('JA','lodge.php?op=charmconfirm');
	}
}
else if ($_GET['op']=='charmconfirm')
{
	if ($pointsavailable>=20)
	{
		if ($session['user']['charm']<=0)
		{
			output('J. C. Petersen schaut dich angewidert an und sagt "Du bist hässlich wie die Nacht, ich kann einfach nichts Schönes an dir finden."');
		}
		else if ($session['user']['charm']==1)
		{
			output('J. C. Petersen schaut dich kurz an und sagt "Du bist genauso häßlich wie jeder gemeine Bürger, mehr als `^1 Punkt`0 wird dir kein Preisrichter geben."');
		}
		else
		{
			output('J. C. Petersen mustert dich noch einmal ganz genau und sagt "Du bist `^'.$session['user']['charm'].'`0mal so schön wie der gemeine Bürger."');
		}
		$session['user']['donationspent']+=20;
		debuglog('Gab 20DP für Charmepunktanzeige');
	}
}
else if ($_GET['op']=='poison')
{
	output('Du fragst J. C. Petersen frei heraus, ob er dir nicht etwas seines tödlichen und verbotenen Giftes aushändigen kann kann. Sofort packt er dich am Kragen und hält dir den Mund zu, dann zieht er dich in eine Ecke und gibt dir zu verstehen, dass dich eine Phiole das 20 Punkte kosten wird und 3 Ladungen enthält. Weiterhin macht er dir klar, dass dir sein Jagdhund dorthin beissen wird, wo es besonders weh tut, solltest du noch einmal auf die Idee kommen dieses Thema laut anzusprechen.');
	if ($pointsavailable<20)
	{
		output('`n`n`$Du hast nicht genug Punkte!`0');
	}
	else
	{
		addnav('Bestätige Erwerb von Gift');
		addnav('JA','lodge.php?op=poisonconfirm');
	}
}
else if ($_GET['op']=='poisonconfirm')
{
	if ($pointsavailable>=20)
	{
		output('Petersen öffnet ein kleines Wandschränkchen und holt eine winzige Phiole mit grünem Inhalt heraus.`nDieses Gift reicht für 3 Ladungen, schau dir einfach eine Truhenfalle deiner Wahl im Haus an und fülle sie damit auf!`n');

		item_add($session['user']['acctid'],'gftph');


		$session['user']['donationspent']+=20;
		debuglog('Gab 20DP für Truhengift');
	}
}
else if ($_GET['op']=='gems')
{
	output('2 Edelsteine für 50 Punkte. Bist du dir sicher, dass du das willst?');
	if ($pointsavailable<50)
	{
		output('`n`n`$Du hast nicht genug Punkte!`0');
	}
	else
	{
		addnav('Bestätige 2 Edelsteine');
		addnav('JA','lodge.php?op=gemsconfirm');
	}
}
else if ($_GET['op']=='gemsconfirm')
{
	if ($pointsavailable>=50)
	{
		output('J. C. Petersen gibt dir 2 Edelsteine und sagt "Damit, mein Freund, wird dein Leben leichter werden"');
		$session['user']['gems']+=2;
		$session['user']['donationspent']+=50;
		debuglog('Gab 50DP für Edelsteine');
	}
}
else if ($_GET['op'] == 'title')
{

	$arr_tmp = user_get_aei('ctitle');
	$str_ctitle = $arr_tmp['ctitle'];
	unset($arr_tmp);

	output('`c`bTitel ändern`b`c`n`n');

	$int_cost = 50;

	if($_GET['finished'])
	{
		output('`n`n`c`@`b');

		if(!empty($str_ctitle))
		{
			output('Gratulation, du besitzt hiermit den eigenen Titel '.$str_ctitle.'`@!`n');
		}
		else
		{
			output('Du setzt deinen Titel zurück auf `&'.$session['user']['title'].'`@!`n');
		}


		output('Zusammen ergibt das '.$session['user']['name'].'`@!`b`c`0`n`n');

		$session['user']['donationspent'] += $int_cost;
		debuglog('Gab '.$int_cost.'DP für eigenen Titel');

		page_footer();
		exit;
	}

	output('Den Titel zu ändern kostet '.$int_cost.' Punkte.');

	if($pointsavailable < $int_cost)
	{
		output('`nLeider verfügst du über zu wenig Punkte, um dir das leisten zu können!');
		page_footer();
		exit;
	}

	output('`n`n`0Wie soll dein eigener Titel aussehen? (Lasse das Feld leer, um deinen normalen Titel '.$session['user']['title'].'`0 wiederherzustellen)`n`n');

	$str_newtitle = stripslashes($_POST['newtitle']);

	if(isset($_POST['newtitle']))
	{

		$str_msg = '';

		$str_newtitle = str_replace('`0','',$str_newtitle);
		// Alle anderen Tags als erlaubte Farbcodes rausschmeißen
		$str_newtitle = preg_replace('/[`][^'.regex_appoencode(1,false).']/','',$str_newtitle);

		output('Du wählst: `b'.$str_newtitle.'`b`n`n');

		// Auf was wollen wir alles kontrollieren (Standard reicht hier nicht aus)?
		$int_options = USER_NAME_BADWORD | USER_NAME_BLACKLIST | USER_NAME_EXCLUSIVE_TITLE | USER_NAME_NOCHANGE | USER_NAME_OFFICIALTITLE;

		$str_result = user_retitle(0,false,$str_newtitle,true,$int_options);

		if(true !== $str_result)
		{

			switch($str_result)
			{

				case 'ctitle_blacklist':
					$str_msg .= 'Diesen Titel darfst du leider nicht wählen, da er von den Göttern verboten wurde.`n';
					break;

				case 'ctitle_tooshort':
					$str_msg .= 'Dieser Titel ist zu kurz (Mindestens '.getsetting('titleminlen',3).' Zeichen).`n';
					break;

				case 'ctitle_toolong':
					$str_msg .= 'Dieser Titel ist zu lang (Maximal '.getsetting('titlemaxlen',25).' Zeichen).`n';
					break;

				case 'ctitle_badword':
					$str_msg .= 'Dieser Titel enthält verbotene oder anstößige Wörter.`n';
					break;

				case 'ctitle_officialtitle':
				case 'ctitle_exclusive':
					$str_msg .= 'Diesen Titel darfst du nicht nehmen.`n';
					break;

				case 'ctitle_changeforbidden':
					$str_msg .= 'Deinen aktuellen Titel darfst du leider nicht auf diese Weise ändern.`n';
					break;
					
				case 'ctitle_toomuchcolors':
					$str_msg .= 'Dein gewählter Titel enthält zu viele Farbcodes. Maximal erlaubt sind '.getsetting('title_maxcolors',7).'.`n';
					break;

				default:
					$str_msg .= '';
					break;

			}

			output($str_msg);

		}
		else
		{
			user_set_name(0);
			redirect('lodge.php?op=title&finished=1');
		}

	}
	else
	{
		$str_newtitle = (!empty($str_ctitle) ? $str_ctitle : '');
	}

	$str_lnk = 'lodge.php?op=title';
	addnav('',$str_lnk);

	$arr_form = array('newtitle'=>'Dein neuer Titel mit oder ohne Farbcodes:');
	$arr_data = array('newtitle'=>$str_newtitle);

	output('`&Vorschau: ');
	rawoutput(js_preview('newtitle'));

	output('`n<form action="'.$str_lnk.'" method="POST">',true);

	showform($arr_form,$arr_data,false,'Diesen Titel übernehmen!');

	output('</form>',true);

}
else if ($_GET['op']=='namechange')
{
	output('`c`bNamensfarbe ändern`b`c`n`n');

	$arr_tmp = user_get_aei('cname');
	$str_cname = $arr_tmp['cname'];
	unset($arr_tmp);

	if ($config['namechange']==1)
	{
		$int_cost = 25;
		output('Da du schon vorher viele Punkte für die Farbänderung gegeben hast kostet es dich diesmal nur 25 Punkte.');
	}
	else
	{
		$int_cost = 300;
		output('Da es deine erste Farbänderung ist kostet es dich 300 Punkte . Beim nächsten Wechsel fallen nur 25 Punkte Kosten an.');
	}

	if($_GET['finished'])
	{
		output('`n`n`c`@`bGratulation, '.(!empty($str_cname) ? 'du wählst dir den farbigen Namen '.$str_cname : 'du setzt deinen Namen farblich zurück').'`2!`b`c`0`n`n');

		$session['user']['donationspent'] += $int_cost;
		debuglog('Gab '.$int_cost.'DP für farbigen Namen');

		$config['namechange']=1;

		$session['user']['donationconfig'] = serialize($config);

		page_footer();
		exit;
	}

	if($pointsavailable < $int_cost)
	{
		output('`nLeider verfügst du über zu wenig Punkte, um dir das leisten zu können!');
		page_footer();
		exit;
	}

	output('`n`nDein geänderter Name muss der selbe Name sein wie vor der Farbänderung, nur dass er jetzt die Farbcodes enthalten darf.`n`n');

	if(!empty($str_cname))
	{
		output('Dein farbiger Name bisher ist: ');
		$output.=$str_cname;
		output(', und so wird er aussehen: '.$str_cname);
	}
	else

	{
		output('Bisher besitzt du keinen farbigen Namen!');
	}

	output('`n`n`0Wie soll dein farbiger Name in Zukunft aussehen?`n`n');

	$str_newname = stripslashes($_POST['newname']);

	if(!empty($str_newname))
	{

		$str_msg = '';

		$str_newname = str_replace('`0','',$str_newname);
		// Alle anderen Tags als erlaubte Farbcodes rausschmeißen
		$str_newname = preg_replace('/[`][^'.regex_appoencode(1,false).']/','',$str_newname);

		output('Du wählst: `b'.$str_newname.'`b`n`n');

		// Validieren und gegebenenfalls ändern
		$str_comp1 = strip_appoencode(trim($session['user']['login']),3);
		$str_comp2 = strip_appoencode(trim($str_newname),3);
		// Wenn wir Änderung der Groß-/Kleinschreibung erlauben..:
		if(getsetting('name_casechange',1))
		{

			// Namen in reiner Großschreibung verhindern
			if(!getsetting('allletter_up_allow',1))
			{
				if(ctype_upper($str_comp2))
				{
					$str_result = 'all_up';
				}
			}
			// 1. Buchstabe immer groß
			if(getsetting('firstletter_up',1))
			{
				if(ctype_lower(substr($str_comp2,0,1)))
				{
					$str_result = 'first_down';
				}
			}

			$str_comp1 = strtolower($str_comp1);
			$str_comp2 = strtolower($str_comp2);
		}

		if($str_comp1 != $str_comp2)
		{
			$str_result = 'nochange';
		}

		if(empty($str_result))
		{
			$str_result = user_rename(0,$str_newname,true);
		}

		if(true !== $str_result)

		{

			switch($str_result)
			{

				case 'cname_toomuchcolors':
					$str_msg .= 'Du hast zu viele Farben in deinem Namen benutzt. Du kannst maximal '.getsetting('name_maxcolors',10).' Farbcodes benutzen.`n';
					break;

				case 'nochange':
					$str_msg .= 'Dein neuer Name muss genauso bleiben wie dein alter Name. Du kannst
					'.(getsetting('name_casechange',1) ? 'die Groß-/Kleinschreibung ändern, ' : '').'
					 Farbcodes entfernen oder hinzufügen, aber ansonsten muss alles gleichbleiben.`n';
					break;

				case 'all_up':
					$str_msg .= 'Namen, die nur in Großschreibung gehalten sind, sind verboten. Bitte ändere dies.`n';
					break;

				case 'first_down':
					$str_msg .= 'Namen, deren erster Buchstabe in Kleinschreibung gehalten ist, sind verboten. Bitte ändere dies und verwende als erstes Zeichen einen Großbuchstaben.`n';
					break;

				default:
					$str_msg .= 'Irgendwas stimmt nicht mit deinem Login. Schreib bitte eine Anfrage!`n';
					break;

			}

			output('`$'.$str_msg.'`0`n');

		}
		else
		{
			user_set_name(0);
			redirect('lodge.php?op=namechange&finished=1');

		}

	}
	else
	{
		$str_newname = (!empty($str_cname) ? $str_cname : $session['user']['login']);
	}

	$str_lnk = 'lodge.php?op=namechange';
	addnav('',$str_lnk);

	$arr_form = array('newname'=>'Dein neuer Name mit Farbcodes:');
	$arr_data = array('newname'=>$str_newname);

	output('`&Vorschau: ');
	rawoutput(js_preview('newname'));

	output('`n<form action="'.$str_lnk.'" method="POST">',true);

	showform($arr_form,$arr_data,false,'Diese Färbung übernehmen!');

	output('</form>',true);

}
else if ($_GET['op']=='immun')
{

	// HOT Items
	$bool_hot = (bool)item_count(' hot_item>0 AND owner='.$session['user']['acctid'].' AND deposit1=0 ',true);

	if ($session['user']['pvpflag']==PVP_IMMU)
	{
		output('J. C. Petersen nickt dir zu und gibt dir zu verstehen, dass du noch immer unter seinem Schutz stehst.');
		if($bool_hot) {
			output('`nJedoch trägst du da etwas bei dir, dass diesen Schutz beeinträchtigen könnte..');
		}
	}
	else if (($session['user']['pvpflag']=='1986-10-06 00:42:00') && ($session['user']['marks']<31))
	{
		output('J. C. Petersen zeigt dir einen Vogel und macht dir sehr schnell klar, dass er vorerst nichts mehr für dich tun kann. Er kann niemanden schützen, der selbst mordend durchs Land zieht.');
	}
	else
	{
		output('Du fragst J. C. Petersen, ob er deinen Aufenthaltsort vor herumstreifenden Dieben und Mördern verbergen kann.');
		output(' Er nickt und verspricht dir, dass dir für die Kleinigkeit von 300 Punkten niemand mehr ein Haar krümmen wird. Er wird auch mit Dag Durnick reden. Allerdings kann er für nichts mehr garantieren, wenn du selbst einen Mord begehst!`n`n');
		if($bool_hot) {
			output('`nAußerdem trägst du da etwas bei dir, dass diesen Schutz beeinträchtigen könnte..`n`n');
		}
		output('300 Punkte für permanente PvP Immunität ausgeben?`n(Die Immunität verfällt, sobald du selbst PvP machst, oder ein Kopfgeld auf jemanden aussetzt und kann dann `bnicht`b mehr so schnell erneuert werden!)');
		addnav('Immunität bestätigen?');
		addnav('JA','lodge.php?op=immunconfirm');
	}
}
else if ($_GET['op']=='immunconfirm')
{
	if ($pointsavailable>=300)
	{
		output('J. C. Petersen nutzt seinen Einfluss, um dich für PvP-Spieler unangreifbar zu machen. Es kann auch kein (weiteres) Kopfgeld auf dich ausgesetzt werden.`nDenke daran, dass du nur so lange geschützt bist, bis du selbst jemanden angreifst, oder jemanden auf Dag\'s ');
		output(' Kopfgeldliste setzt. Tust du das, kann selbst Petersen dir in Zukunft nicht mehr helfen.');
		$session['user']['pvpflag']=PVP_IMMU;
		$session['user']['donationspent']+=300;
		debuglog('Gab 300DP für PvP-Immu');
	}
	else
	{
		output('Du hast nicht genug Punkte!');
	}
}
else if ($_GET['op']=='keys1')
{
	$sql = 'SELECT k.*,a.acctid FROM keylist k
			LEFT JOIN accounts a ON a.acctid=k.owner
			WHERE value1='.$session['user']['house'].' ORDER BY id ASC';
	$result = db_query($sql) or die(db_error(LINK));

	$lost = array();

	while ($k = db_fetch_assoc($result))
	{
		if ($k['owner'] == 0 || $k['acctid'] == 0)
		{
			$lost[] = $k;
		}
	}

	if (sizeof($lost))
	{
		output("`b`c`&Verlorene Schlüssel:`c`b<table cellpadding=2 align='center'><tr><td>`bNr.`b</td><td>`bAktion`b</td></tr>",true);
		for ($i=0; $i<sizeof($lost); $i++)
		{
			$row = $lost[$i];
			$bgcolor=($i%2==1?"trlight":"trdark");
			output("<tr class='$bgcolor'><td>".$session['user']['house']."</td><td><a href='lodge.php?op=keys2&id=$row[id]'>Ersetzen (10 Punkte)</a></td></tr>",true);
			addnav('','lodge.php?op=keys2&id='.$row['id']);
		}
		output("</table>",true);
	}
	else
	{

		$sql = 'SELECT status FROM houses WHERE owner='.$session['user']['acctid'];
		$res = db_query($sql) or die(db_error(LINK));

		$house = db_fetch_assoc($res);
		if (($house['status']<30) || ($house['status']>=40))
		{

			output('Der Schlüsselsatz für dein Haus ist komplett. Willst du einen zusätzlichen Schlüssel für 100 Punkte und 10 Edelsteine kaufen?');
			addnav('Zusätzlicher Schlüssel (100 Punkte + 10 Edelsteine)','lodge.php?op=keys2&id=new');
		}
		else
		{
			output('Du hast alle Schlüssel und vergrößern kannst du dein '.get_house_state($house['status'],false).' auch nicht! Was willst du also hier?');
		}
	}
}
else if ($_GET['op']=='keys2')
{
	if ($_GET['id']=='new')
	{
		output('`b100`b ');
	}
	else
	{
		output('`b10`b ');
	}
	output('Punkte für diesen Schlüssel ausgeben?');
	addnav('Schlüsselkauf bestätigen?');
	addnav('JA','lodge.php?op=keys3&id='.$_GET['id']);
}
else if ($_GET['op']=='keys3')
{
	if ($_GET['id']=='new')
	{
		if ($pointsavailable<100)
		{
			output('Du hast nicht genug Punkte übrig.');
		}
		else if ($session['user']['gems']<10)
		{
			output('Du hast nicht genug Edelsteine dabei.');
		}
		else
		{
			$sql = 'SELECT * FROM keylist WHERE value1='.$session['user']['house'].' ORDER BY id ASC';
			$result = db_query($sql) or die(db_error(LINK));
			$nummer=db_num_rows($result)+1;
			db_free_result($result);
			$sql='INSERT INTO keylist (owner,value1,value2,gold,gems,description) VALUES ('.$session['user']['acctid'].','.$session['user']['house'].','.$nummer.',0,0,"Schlüssel für Haus Nummer '.$session['user']['house'].'")';
			db_query($sql) or die(db_error(LINK));
			$session['user']['donationspent']+=100;
			$session['user']['gems']-=10;
			debuglog('Gab 100DP+10ES für Hausschlüssel');
			output("Du hast jetzt `b$nummer`b Schlüssel für dein Haus! Überlege gut, an wen du sie vergibst.");
		}
	}
	else
	{
		if ($pointsavailable<10)
		{
			output("Du hast nicht genug Punkte übrig.");
		}
		else
		{
			$nummer=$_GET['id'];
			$sql="UPDATE keylist SET owner=".$session['user']['acctid'].",hvalue=0,chestlock=0,gold=0,gems=0 WHERE id=$nummer";
			db_query($sql);
			$session['user']['donationspent']+=10;
			debuglog('Gab 10DP für Ersatzschlüssel');
			output("Der Schlüssel wurde ersetzt.");
		}
	}
}
else if ($_GET['op']=="private_keys1")
{

	$sql = 'SELECT status FROM houses WHERE houseid='.$session['user']['house'];
	$res = db_query($sql);
	$house = db_fetch_assoc($res);

	if ($house['status'] < 10)
	{
		output('Du musst dein Haus erst ausbauen, um Platz für Privatgemächer zu schaffen!');
	}
	else
	{
		output('`b40`b Punkte und `b10`b Edelsteine für ein zusätzliches Privatgemach für die Bewohner deines Hauses ausgeben?');
		addnav('Privatgemachkauf bestätigen?');
		addnav('JA','lodge.php?op=private_keys2');
	}
}
else if ($_GET['op']=='private_keys2')
{

	if ($pointsavailable<40)
	{
		output('Du hast nicht genug Punkte übrig.');
	}
	else
	{
		if ($session['user']['gems']<10)
		{
			output('Du hast nicht genug Edelsteine dabei.');
		}
		else
		{

			$nummer=item_count(' tpl_id="privb" AND value1='.$session['user']['house'] ) + 1;

			$item['tpl_description'] = 'Besitzurkunde für ein Privatgemach in Haus Nr. '.$session['user']['house'];
			$item['tpl_value1'] = $session['user']['house'];

			item_add($session['user']['acctid'],'privb',$item);

			$session['user']['donationspent']+=40;
			$session['user']['gems']-=10;
			debuglog('Gab 40DP+10ES für Privatgemach');
			output('Du hast jetzt `b'.$nummer.'`b Privatgemächer für dein Haus! Überlege gut, an wen du sie vergibst.');
		}
	}
}
else if ($_GET['op'] == 'item')
{

	$res = item_list_get(" tpl_id='unikat' AND owner=".$session['user']['acctid'],'',false);
	$anzahl = db_num_rows($res);

	output('Hier hast Du die Möglichkeit, Dir für '.DP_KOSTEN_SPECIAL_ITEM.' Punkte ein einzigartiges, nach Deinen Wünschen gestaltetes Möbelstück fertigen zu lassen.`n');
	output('Außerdem bietet Petersen dir auch an, dieses Möbelstück an andere Einwohner '.getsetting('townname','Atrahor').'s zu versenden.`n');
	if ($anzahl < DP_MAX_SPECIAL_ITEMS && $pointsavailable >= DP_KOSTEN_SPECIAL_ITEM)
	{
		output('`n`nPetersen benötigt nun die folgenden Informationen von Dir:`n`n<form method="POST" action="lodge.php?op=item_confirm">Vorschau: `^'.js_preview('name').'`n`&Name des Möbelstücks: <input type="text" name="name" size="40" maxlength="90" value="'.$name.'">`n`nVorschau: '.js_preview('desc').'`nBeschreibung: <input type="text" name="desc" size="60" maxlength="200" value="'.$desc.'">`n`n<input type="submit" name="ok" value="Kaufen">`n</form>',true);
		addnav('','lodge.php?op=item_confirm');
	}
	else
	{
		output('`n`nLeider ist Petersen nicht bereit, Dir noch weitere Möbelstücke fertigen zu lassen!`n');
	}
	output('`n<hr>Bisher wurden für Dich '.$anzahl.' besondere(s) Möbel hergestellt:`n');
	while ($item = db_fetch_assoc($res))
	{
		output('`i`n'.$item['name'].'`i');
	}
	output ('`n`n');

}
else if ($_GET['op'] == 'item_confirm')
{
	addnav('Besonderes Möbelstück');

	// warum auch immer da mehrfach escaped wird..
	$name = '`^Unikat - '.trim(stripslashes($_POST['name']));
	$desc = trim(stripslashes($_POST['desc']));
	output('Wirklich `b'.DP_KOSTEN_SPECIAL_ITEM.'`b Punkte für dieses einzigartige Möbelstück ausgeben? Es wird ungefähr so aussehen:`n`n');
	output($name.' `&('.$desc.'`&)');

	output('`nWillst du es selbst verwenden oder an jemanden verschenken?`n`n');

	output('<form method="POST" action="lodge.php?op=item_ok">`n<input type="hidden" name="name" value="',true);
	rawoutput(htmlentities($name));
	output('"><input type="hidden" name="desc" value="',true);
	rawoutput(htmlentities($desc));
	output('"><input type="submit" name="ok_selbst" value="Selbst verwenden!"> <input type="submit" name="ok_geschenk" value="Verschenken">`n</form>',true);
	addnav('','lodge.php?op=item_ok');

//	addnav('Nein, zurück!','lodge.php');
}
else if ($_GET['op'] == 'item_ok')
{
	$name = stripslashes($_POST['name']);
	$desc = stripslashes($_POST['desc']);

	if ($_GET['act'] == 'search' && strlen($_POST['search']) > 2)
	{

		output($name.' `&('.$desc.'`&)`n`n');

		$search = str_create_search_string($_POST['search']);

		$sql = 'SELECT name,acctid FROM accounts WHERE name LIKE "'.$search.'" AND acctid!='.$session['user']['acctid'];
		$res = db_query($sql);

		$link = 'lodge.php?op=item_ok';

		output('<form action="'.$link.'" method="POST">',true);

		output('<input type="hidden" name="name" value="',true);
		rawoutput(htmlentities($name));
		output('"><input type="hidden" name="desc" value="',true);
		rawoutput(htmlentities($desc));
		output('">',true);

		output(' <select name="acctid">',true);

		while ($p = db_fetch_assoc($res) )
		{
			output('<option value="'.$p['acctid'].'">'.preg_replace("'[`].'","",$p['name']).'</option>',true);
		}

		output('</select>`n`n',true);

		output('<input type="submit" class="button" value="Auswählen!"></form>',true);
		addnav('',$link);
	}
	else if ($_POST['ok_geschenk'])
	{
		output($name.' `&('.$desc.'`&)`n`n');

		$link = 'lodge.php?op=item_ok&act=search';

		output('An wen willst du das Unikat versenden?`n`n');

		output('<form action="'.$link.'" method="POST">',true);

		output('<input type="hidden" name="name" value="',true);
		rawoutput(htmlentities($name));
		output('"><input type="hidden" name="desc" value="',true);
		rawoutput(htmlentities($desc));
		output('">',true);

		output('Name: <input type="text" name="search"> ',true);
		output('<input type="submit" class="button" value="Suchen!"></form>',true);
		addnav('',$link);

	}
	// END Geschenk
	else
	{
		$acctid = (int)$_POST['acctid'];

		$session['user']['donationspent'] += DP_KOSTEN_SPECIAL_ITEM;
		debuglog('Gab '.DP_KOSTEN_SPECIAL_ITEM.' DP für Specialitem '.$name);

		$item['tpl_name'] = html_entity_decode($name);
		$item['tpl_description'] = html_entity_decode($desc);
		$item['tpl_gold'] = 0;
		$item['tpl_gems'] = 10;

		item_add(($acctid ? $acctid : $session['user']['acctid']) , 'unikat' , $item );

		output('Petersen protokolliert gewissenhaft diesen Wunsch und meint dann:`n');
		if (!$acctid)
		{
			output('`7"Dein besonderes Möbelstück steht nun für dich bereit. Viel Spaß damit..."');
			addnav('Besonderes Möbelstück');
		}
		else
		{
			systemmail($acctid,'`2Ein Geschenk!',$session['user']['name'].'`2 hat dir ein Unikat namens '.$name.'`2 zum Geschenk gemacht. Du kannst es mit dir rumtragen, es anbeten oder einfach in ein Haus oder Privatgemach stellen! Ist das nicht nett?`n(Kleiner Tipp: Du findest es in deinem Inventar.)');
			output('`7"Dein besonderes Möbelstück wurde an die gewünschte Person geliefert. Hoffentlich gefällt es..."');
		}
		output('`0, woraufhin er sich wieder seinem Buch zuwendet.');
	}
}
else if ($_GET['op']=='bio')
{
	$resextra = db_query('SELECT has_long_bio FROM account_extra_info WHERE acctid='.$session['user']['acctid']);
	$rowextra = db_fetch_assoc($resextra);

	if ($rowextra['has_long_bio']==1)
	{
		output('Du hast diese Option bereits gekauft und hast in deiner Bio Platz für '.getsetting('longbiomaxlength',4096).' Zeichen.');
	}
	else
	{
		output('Hiermit schaffst du den ersten Schritt aus deiner Unbedeutenheit heraus. Die anderen Kämpfer werden viel mehr über dich erfahren können, wenn du diese Option freischaltest.');
		if ($pointsavailable<('DP_KOSTEN_LONG_BIO'))
		{
			output('`n`n`$Du hast nicht genug Punkte!`0');
		}
		else
		{
			addnav('Betätige Freischaltung');
			addnav('JA','lodge.php?op=bio2');
		}
	}
}
else if ($_GET['op']=='bio2')
{
	if ($pointsavailable >= DP_KOSTEN_LONG_BIO)
	{
		$sql = "UPDATE account_extra_info SET has_long_bio=1 WHERE acctid = ".$session['user']['acctid'];
		db_query($sql) or die(sql_error($sql));
		output("J. C. Peterson erfüllt dir deinen Wunsch und macht dich zu einem bedeutenderem Bürger.`nDeine Bio fasst nun ".getsetting('longbiomaxlength',4096)." Zeichen.");
		$session['user']['donationspent']+= DP_KOSTEN_LONG_BIO;
		debuglog('Gab '.DP_KOSTEN_LONG_BIO.' DP für eine lange bio ');
	}
}
else if ($_GET['op']=='trophy')
{
	$resextra = db_query('SELECT trophyhunter FROM account_extra_info WHERE acctid='.$session['user']['acctid']);
	$rowextra = db_fetch_assoc($resextra);

	if ($rowextra['trophyhunter']==1)
	{
		output('Du hast doch bereits dein eigenes von J. C. Petersen signiertes Präparierset.`nOder weißt du etwa nicht was du damit anstellen sollst ?`n');
	}
	else
	{
		output('J. C. Petersen zeigt dir die vielen Jagdtrophäen in seiner Hütte, die er selbst herstestellt hat. Nun bietet er dir sein persönliches Präparierset für läppiche 200 Punkte an.');
		if ($pointsavailable<200)

		{
			output('`n`n`$Du hast nicht genug Punkte!`0');
		}
		else
		{
			addnav('Betätige Freischaltung');
			addnav('JA','lodge.php?op=trophy2');
		}
	}
}
else if ($_GET['op']=='trophy2')
{
	if ($pointsavailable >= 200)
	{
		$sql = 'UPDATE account_extra_info SET trophyhunter=1 WHERE acctid = '.$session['user']['acctid'];
		db_query($sql) or die(sql_error($sql));
		output('Gratulation! Du besitzt nun dein eigenes Präparierset und bist somit im Stand deine eigenen Trophäen herzustellen.');
		$session['user']['donationspent']+=200;
		debuglog('Gab 200DP für Präparierset');
	}
}
else if ($_GET['op'] == 'history')
{

	$int_max_length = 100;

	// Aktuelles Spieldatum
	$str_current_date = getsetting('gamedate','0000-00-00');
	// .. als Array
	$arr_current_date = explode('-',$str_current_date);
	// Max. anwählbares Jahr
	$int_max_year = (int)$arr_current_date[0];
	// Max. anwählbarer Monat
	$int_max_month = (int)$arr_current_date[1];
	// Max. anwählbarer Tag
	$int_max_day = (int)$arr_current_date[2];

	if($_GET['act'] == 'save') {

		// Invalide Spieldaten verhindern
		$int_year = min((int)$_REQUEST['year'],$int_max_year);
		$int_month = (int)$_REQUEST['month'];
		$int_day = (int)$_REQUEST['day'];
		if($int_year == $int_max_year)
		{
			$int_month = min($int_month,$int_max_month);
			if($int_month == $int_max_month)
			{
				$int_day = min($int_day,$int_max_day);
			}
		}

		// this piece of code was taken from chaosmakers gamedate-mod
		$str_gamedate = sprintf('%04d-%02d-%02d',$int_year,$int_month,$int_day);

		$str_msg = stripslashes(urldecode($_REQUEST['msg']));
		$str_msg = substr($str_msg,0,$int_max_length);

		$str_msg_save = '`^Besonderes Ereignis:`0 '.$str_msg;

		if($_GET['ok'])
		{
			$session['user']['donationspent'] += DP_KOSTEN_HISTORY;
			$session['user']['gems'] -= GEMS_KOSTEN_HISTORY;
			debuglog('Gab '.DP_KOSTEN_HISTORY.'DP+'.GEMS_KOSTEN_HISTORY.'ES für spezielle Aufzeichnung');
			addhistory($str_msg_save,1,0,$str_gamedate);
			redirect('lodge.php?op=history&act=done');
		}
		else
		{
			output('Deine spezielle Aufzeichnung würde folgendermaßen aussehen:`n`n
					`@'.getgamedate($str_gamedate).' : `2'.$str_msg_save.'`n`n`0
					Entspricht dies deinen Wünschen?`n`n');

			$str_lnk = 'lodge.php?op=history&act=save&ok=1&day='.$int_day.'&month='.$int_month.'&year='.$int_year.'&msg='.urlencode($str_msg);
			output(create_lnk('Ja, für '.DP_KOSTEN_HISTORY.' DP + '.GEMS_KOSTEN_HISTORY.' Edelsteine eintragen!',$str_lnk));

		}
	}
	elseif ($_GET['act'] == 'done')
	{

		output('`@Petersen nimmt deinen Wunsch entgegen und reicht ihn weiter in das Hinterzimmer der Hütte. Bereits nach kurzer Zeit
				kannst du das Ergebnis betrachten:`n`n');
		show_history(1,$session['user']['acctid']);
		page_footer();
		exit;

	}
	else
	{
		output('`&Petersen hat hervorragende Verbindungen zu den Geschichtsschreibern des Landes. Deshalb kann er dir gegen
			`b'.DP_KOSTEN_HISTORY.'`b Donationpoints und `b'.GEMS_KOSTEN_HISTORY.'`b Edelsteine zu einem Eintrag deiner Wahl
			in deinen Aufzeichnungen verhelfen. Hierbei kannst du entweder selbst ein (natürlich gültiges) Datum angeben oder das
			des heutigen Tages verwenden. An den Text deiner Aufzeichnung wird vorne die Bemerkung "Besonderes Ereignis" angefügt.`n
			Achtung: Diese Option dient der Ausgestaltung eures Rollenspiel-Charakters! Unsinnige Spaß-Einträge werden ohne Entschädigung entfernt!`n');

		$int_day = $int_max_day;
		$int_month = $int_max_month;
		$int_year = $int_max_year;

	}

	$str_lnk = 'lodge.php?op=history&act=save';

	if($pointsavailable < DP_KOSTEN_HISTORY || $session['user']['gems'] < GEMS_KOSTEN_HISTORY) {
		output('`$Doch leider, leider kannst du dir das gar nicht leisten.. Schade.');
	}
	else
	{
		addnav('',$str_lnk);
		output('<form method="POST" action="'.$str_lnk.'">');
		$arr_form = array	(
		'msg'=>'Nachricht:,text,'.$int_max_length,
		'msg_pr'=>'Vorschau:,preview,msg',
		'day'=>'Tag,enum_order,1,31',
		'month'=>'Monat,enum_order,1,12',
		'year'=>'Jahr,enum_order,1,'.$int_max_year

		);
		showform($arr_form,array('msg'=>$str_msg,'year'=>$int_year,'month'=>$int_month,'day'=>$int_day),false,'Vorschau!');
		output('</form>');
	}
}

$session['user']['donationconfig'] = serialize($config);

page_footer();
?>
