<?php

// 20.6.2006 Tippfehler (Forum 9.4.06) beseitigt

// gardenflirt 1.0 by anpera
// uses 'charisma' entry in database to determine how far a love goes, and 'marriedto' to know who with whom. ;)
// no changes necessary in database
// some changes in newday.php, hof.php, dragon.php, and inn.php required and in user.php optional!
// See http://www.anpera.net/forum/viewforum.php?f=27 for details

// MOD by tcb, 11.5.05: neues Heiratssytem, Details s. tempel.php
// Schaukel-Addon, 26.08.06 by Maris (Maraxxus@gmx.de)

require_once 'common.php';
require_once(LIB_PATH.'profession.lib.php');

$buff = array('name'=>'`!Schutz der Liebe','rounds'=>60,'wearoff'=>'`!Du vermisst deine gro�e Liebe!`0','defmod'=>1.2,'roundmsg'=>'Deine gro�e Liebe l�sst dich an deine Sicherheit denken!','activate'=>'defense');

page_header('Die G�rten');

music_set('garden');

if ($_GET['op']=='swing')
{
	music_set('schaukel');
	addcommentary();
	checkday();
	$str_output .= '`b`c`2Die Gartenschaukel`0`c`b';
	$str_output .= '`n`nIm hinteren Teil des Gartens, nahe einer romantischen Laube, befindet sich an m�chtigen Pf�hlen angebracht eine gro�e Schaukel.`nSie ist wohl stabil genug um auch den kr�ftigsten Troll zu tragen, allerdings bietet sie nur Platz f�r eine einzige Person.`nDu kannst dich hier auf den B�nken niederlassen, es dir in der Laube gem�tlich machen oder gar einen Schaukelgang wagen.`n`n';
	viewcommentary('gardens_swing','Fl�stern',30,'fl�stert');
	addnav('Schaukeln');
	addnav('Auf die Schaukel','gardens.php?op=swing2');
	addnav('Sonstiges');
	addnav('Zur�ck zum Garten','gardens.php');
}
elseif ($_GET['op']=='swing2')
{
	$str_output .= 'Du h�pfst auf die einladend aussehende Schaukel und schaukelst eine Runde.`n`n';
	$chance=e_rand(1,10);
	switch($chance)
	{
		case 1:
		case 2:
		case 3:
		case 4:
		case 5:
			$str_output .= 'Dabei f�hlst du dich wirklich gro�artig und leicht beschwingt!';
			break;
		case 6:
		case 7:
			if ($session['user']['turns']>0)
			{
				$str_output .= 'Dabei wird dir derart schlecht, dass du die n�chste Zeit deinen Bauch halten wirst!`nDu `4verlierst eine Runde!`&';
				$session['user']['turns']--;
			}
			else
			{
				$str_output .= 'Dabei wird dir ein wenig schlecht, und du beschliesst die Sache langsamer angehen zu lassen.';
			}
			break;
		case 8:
		case 9:
			if ($session['user']['turns']>0)
			{
				$str_output .= 'Dabei f�hlst du dich derart beschwingt, dass du neue Kraft f�r eine `@weitere Runde`& sch�pfst!';
				$session['user']['turns']++;
			}
			else
			{
				$str_output .= 'Leider bist du zu m�de, um den n�tigen Schwung zu finden.';
			}
			break;
		case 10:
			if ($session['user']['turns']>0)
			{
				$str_output .= 'Bei dem Versuch dich besonders hoch zu schwingen f�llst du von der Schaukel und landest mit dem Gesicht im Matsch!`nSelbstverst�ndlich ist das einer dieser Momente, in denen wirklich JEDER in deine Richtung schaut.`n`4Du verlierst einen Charmepunkt!`&';
				$session['user']['charm']--;
				$sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'gardens_swing',".$session['user']['acctid'].",'/me landet beim Versuch besonders hoch zu schaukeln mit dem Gesicht im Matsch!')";
				db_query($sql) or die(db_error(LINK));
			}
			else
			{
				$str_output .= 'Bei dem Versuch dich besonders hoch zu schwingen f�llst du fast von der Schaukel, kannst dich aber gerade noch so halten.';
			}
			break;
	}
	addnav('Die Schaukel');
	addnav('Zur�ck','gardens.php?op=swing');
	addnav('Sonstiges');

}
elseif ($_GET['op']=='flirt1')
{
	if ($session['user']['seenlover'])
	{
		$sql = "SELECT name FROM accounts WHERE locked=0 AND acctid=".$session['user']['marriedto'];
		$result = db_query($sql) or die(db_error(LINK));
		$row = db_fetch_assoc($result);
		$partner=$row['name'];
		if ($partner=='')
		{
			$partner = $session['user']['sex']?'`^Seth`0':'`5Violet`0';
		}
		$str_output .= 'Du versuchst, dich in Gedanken auf einen hei�en Flirt vorzubereiten, aber irgendwie bekommst du den Kopf nicht frei. Vielleicht solltest du bis morgen warten.';
		addnav('Zur�ck zum Garten','gardens.php');
	}
	else
	{
		if (isset($_POST['search']) || strlen($_GET['search'])>0)
		{
			if (strlen($_GET['search'])>0)
			{
				$_POST['search']=$_GET['search'];
			}
			$search = str_create_search_string($_POST['search']);
			$search='name LIKE \''.$search.'\' AND ';
		}
		else
		{
			$search='';
		}
		$ppp=25; // Player Per Page to display
		if (!$_GET['limit'])
		{
			$page=0;
		}
		else
		{
			$page=(int)$_GET['limit'];
			addnav('Vorherige Seite','gardens.php?op=flirt1&limit='.($page-1).'&search='.$_POST['search']);
		}
		$limit=($page*$ppp).','.($ppp+1);
		if ($session['user']['marriedto']==4294967295)
		{
			$str_output .= 'Du denkst nochmal �ber deine Ehe mit '.($session['user']['sex']?'`^Seth`0':'`5Violet`0').' nach und �berlegst, ob du '.($session['user']['sex']?'ihn':'sie').' in der Kneipe besuchen sollst, oder f�r wen du diese Ehe aufs Spiel setzen w�rdest.`n';
		}
		if($session['user']['charme']==4294967295)
		{
			$str_output .= 'Du �berlegst dir, dass du dir mal wieder etwas Zeit f�r '.($session['user']['sex']?'deinen Mann':'deine Frau').' nehmen solltest. W�hrend du '.($session['user']['sex']?'ihn':'sie').' im Garten suchst, stellst du aber fest, dass der Rest der '.($session['user']['sex']?'M�nner':'Frauen').' hier auch nicht zu verachten ist.`n';
		}
		$str_output .= 'F�r wen entscheidest du dich?`n`n';
		$str_output .= "<form action='gardens.php?op=flirt1' method='POST'>Nach Name suchen: <input name='search' value='$_POST[search]'><input type='submit' class='button' value='Suchen'></form>";
		addnav('','gardens.php?op=flirt1');
		$sql = "SELECT acctid,name,sex,level,race,login,marriedto,charisma FROM accounts WHERE
		$search
		(locked=0) AND
		(sex <> ".$session['user']['sex'].") AND
		(alive=1) AND
		(acctid <> ".$session['user']['acctid'].") AND
		(laston > '".date("Y-m-d H:i:s",strtotime(date("r")."-346000 sec"))."' OR (charisma=4294967295 AND acctid=".$session['user']['marriedto'].") )
		ORDER BY (acctid=".$session['user']['marriedto'].") DESC, charm DESC LIMIT $limit";
		$result = db_query($sql) or die(db_error(LINK));
		$str_output .= "<table border='0' cellpadding='3' cellspacing='0'><tr><td>";
		$str_output .= ($session['user']['sex']?"<img src=\"images/male.gif\">":"<img src=\"images/female.gif\">")."</td><td><b>Name</b></td><td><b>Level</b></td><td><b>Rasse</b></td><td><b>Status</b><td><b>Ops</b></td></tr>";
		if (db_num_rows($result)>$ppp)
		{
			addnav('N�chste Seite','gardens.php?op=flirt1&limit='.($page+1).'&search='.$_POST['search']);
		}

		// Rassen abrufen
		$arr_races = db_create_list(db_query('SELECT colname,id FROM races'),'id');

		$count = db_num_rows($result);
		for ($i=0;$i<$count;$i++)
		{
			$row = db_fetch_assoc($result);
			$biolink='bio.php?char='.rawurlencode($row['login']).'&ret='.urlencode($_SERVER['REQUEST_URI']);
			addnav('', $biolink);
			if ($session['user']['charisma']<=$row['charisma'])
			{
				$flirtnum=$session['user']['charisma'];
			}
			if ($row['charisma']<$session['user']['charisma'])
			{
				$flirtnum=$row['charisma'];
			}
			$str_output .= '<tr valign="top" class="'.($i%2?'trlight':'trdark').'"><td></td><td>'.$row['name'].'</td><td>'.$row['level'].'</td><td>';
			$str_output .= $arr_races[$row['race']]['colname'];
			$str_output .= '</td><td>';
			if ($session['user']['acctid']==$row['marriedto'] && $session['user']['marriedto']==$row['acctid'])
			{
				if ($session['user']['charisma']==4294967295 && $row['charisma']==4294967295)
				{
					$str_output .= '`@`bDein'.($session['user']['sex']?' Mann':'e Frau').'!`b`n`n`n`0';
				}
				else if ($flirtnum==999)
				{
					$str_output .= '`\$Heiratsantrag!`0';
				}
				else
				{
					$str_output .= '`^'.$flirtnum.' von '.$session['user']['charisma'].' Flirts erwidert!`0';
				}
			}
			else if ($session['user']['acctid']==$row['marriedto'])
			{
				$str_output .= 'Flirtet '.$row['charisma'].' mal mit dir';
			}
			else if ($session['user']['marriedto']==$row['acctid'])
			{
				$str_output .= 'Deine letzten '.$session['user']['charisma'].' Flirts';
			}
			else if ($row['marriedto']==4294967295 || $row['charisma']==4294967295)
			{
				$str_output .= '`iVerheiratet`i';
			}
			else
			{
				$str_output .= '-';
			}
			$str_output .= '</td><td>[ <a href="'.$biolink.'">Bio</a> | <a href="gardens.php?act=flirt&name='.rawurlencode($row['login']).'">Flirten</a> ]</td></tr>';
			addnav('','gardens.php?act=flirt&name='.rawurlencode($row['login']));
		}
		$str_output .= '</table>';
		addnav('Zur�ck zum Garten','gardens.php');
	}
}
else if ($_GET['act']=='flirt')
{
	if ($session['user']['goldinbank']>0)
	{
		//Bitshift ist schneller als Division durch zwei
		$getgold=round($session['user']['goldinbank'] >> 1);
	}
	$sql = 'SELECT acctid,name,experience,charm,charisma,lastip,emailaddress,race,marriedto,uniqueid FROM accounts WHERE login="'.$_GET['name'].'"';
	$result = db_query($sql) or die(db_error(LINK));
	if (db_num_rows($result)>0)
	{
		$row = db_fetch_assoc($result);
		if ($session['user']['charisma']<=$row['charisma'])
		{
			$flirtnum=$session['user']['charisma'];
		}
		if ($row['charisma']<$session['user']['charisma'])
		{
			$flirtnum=$row['charisma'];
		}
		if (ac_check($row))
		{
			$str_output .= '`$`bDas geht doch nicht!!`b Du kannst doch nicht mit deinen eigenen Charakteren oder deiner eigenen Familie flirten!';
			addnav('Zur�ck zum Garten','gardens.php');
		}
		else if ( $session['user']['charisma'] == 999 && $row['acctid'] != $session['user']['marriedto'] )
		{

			$str_output .= 'So gern du auch flirten m�chtest, dein Partner geht dir nicht aus dem Kopf! Verwirrt drehst du wieder um.';

		}
		else if ( $row['charisma'] == 999 && $row['marriedto'] != $session['user']['acctid'] )
		{

			$str_output .= 'Neidisch beobachtest du `6'.$row['name'].'`0 bei '.($row['sex'] ? 'ihren' : 'seinen').' Hochzeitsvorbereitungen. In diese traute Zweisamkeit willst du lieber nicht hereinplatzen...';

		}
		else if (($session['user']['race']=='elf' && $row['race']=='zwg') &&  ($row['marks']<31) && ($session['user']['marks']<31) || ($session['user']['race']=='zwg' && $row['race']=='elf'))
		{
			$str_output .= 'Du wartest im Garten auf `6'.$row['name'].'`0 und beobachtest '.($session['user']['sex']?'ihn':'sie').' eine Weile. Bei n�herer Betrachtung stellst du aber fest, dass Elfen und Zwerge vielleicht doch niemals zusammen passen werden.';
			$str_output .= ' So verl�sst du den Garten.';
		}
		else if ($session['user']['turns']<1)
		{
			$str_output .= 'Als `6'.$row['name'].'`0 endlich im Garten auftaucht, f�hlst du dich pl�tzlich vom vielen K�mpfen so erledigt und geschw�cht, dass du es f�r besser h�ltst, mit dem Flirten bis morgen zu warten.`nDu hast deine Runden f�r heute aufgebraucht. ';
		}
		else if ($session['user']['charm']<=1 && $session['user']['charisma']!=4294967295)
		{
			$str_output .= 'Du n�herst dich `6'.$row['name'].'`0 und mit dem Charme einer Blattlaus sprichst du '.($session['user']['sex']?'ihn':'sie').' an. Schon fast beleidigt dreht sich '.$row['name'].' um und stapft davon.`nDu solltest etwas an deiner Ausstrahlung arbeiten...';
		}
		else if ($row['charm']<=1 && $session['user']['charisma']!=4294967295)
		{
			$str_output .= 'Du n�herst dich `6'.$row['name'].'`0. Je n�her du '.($session['user']['sex']?'ihm':'ihr').' kommst, umso h�sslicher kommt '.($session['user']['sex']?'er':'sie').' dir vor. Am Ende wirkt '.($session['user']['sex']?'er':'sie').' so absto�end auf dich, dass du einfach vorbei zur�ck ins Dorf l�ufst.';
		}
		else if ((abs($row['charm']-$session['user']['charm'])>25 && $session['user']['charisma']!=4294967295) && ($session['user']['marks']<31))
		{
			$str_output .= 'Du n�herst dich `6'.$row['name'].'`0. Ihr beginnt ein Gespr�ch, aber irgendwie redet ihr aneinander vorbei. Ein richtiger Flirt entwickelt sich nicht. Du beschlie�t, es sp�ter nochmal zu versuchen und machst dich auf den Weg zur�ck ins Dorf.';
		}
		else if ($session['user']['drunkenness']>66)
		{
			$str_output .= 'Du entdeckst `6'.$row['name'].'`0 im Schatten unter einer Gruppe B�ume und machst dich sofort daran, '.($session['user']['sex']?'ihn':'sie').' mit deiner Alefahne vollzulallen. Als '.($session['user']['sex']?'er':'sie').' �berhaupt ';
			$str_output .= 'nicht reagiert und immer noch irgendwie auf den Boden zu starren scheint, willst du '.($session['user']['sex']?'seinen':'ihren').' Kopf heben - und greifst voll in das Dornengestr�pp vor dir.`n';
			$str_output .= 'Du hast in deinem Rausch diesen Busch f�r '.$row['name'].' gehalten!! Vielleicht ist es besser, erst etwas auszun�chtern, bevor du es nochmal versuchst.`n`n';
			$str_output .= '`^Dieser Irrtum hat dich einen Waldkampf und einen Charmepunkt gekostet!';
			$session['user']['turns']-=1;
			$session['user']['charm']-=1;
		}
		else if (($session['user']['marriedto']==4294967295 || $session['user']['charisma']==4294967295) && ($row['marriedto']==4294967295 || $row['charisma']==4294967295))
		{ // M�glichkeiten, wenn beide verheiratet
			if ($session['user']['marriedto']==$row['acctid'] && $session['user']['acctid']==$row['marriedto'])
			{
				$str_output .= '`%Du f�hrst '.($session['user']['sex']?'deinen Mann':'deine Frau').' `6'.$row['name'].'`% in den Garten aus und ihr nehmt euch etwas Zeit f�reinander. ';
				$str_output .= '`nDu bekommst einen Charmepunkt.';
				$session['bufflist']['lover']=$buff;
				$session['user']['charm']++;
				$session['user']['seenlover']=1;

				if(e_rand(1,3) == 2)
				{

					systemmail($row['acctid'],'`%Gartenflirt!`0','`&'.$session['user']['name'].'`6 hat mit dir einige wundersch�ne Momente im Garten verlebt und dir von Neuem '.($session['user']['sex'] ? 'ihre' : 'seine').' Liebe versichert.');

				}
			}
			else if ($session['user']['charm']==$row['charm'])
			{
				$str_output .= '`%Du n�herst dich `6'.$row['name'].'`%. Sofort entsteht ein heftiger Flirt und ein angeregtes Gespr�ch. Du verstehst dich einfach blendend mit `6'.$row['name'].'`%!';
				$str_output .= ' Ihr verzieht euch eine Weile an einen etwas abseits gelegenen Ort';
				$str_output .= ' und verbringt ein paar sehr sch�ne Stunden miteinander. Da ihr beide verheiratet seid, versprecht ihr euch gegenseitig, dass niemand jemals davon erfahren wird.';
				$str_output .= '`n`nIhr bekommt beide einen Charmepunkt!';
				$session['user']['charm']+=1;
				$session['user']['seenlover']=1;
				$sql = 'UPDATE accounts SET charm=charm+1 WHERE acctid=\''.$row['acctid'].'\'';
				db_query($sql);
				systemmail($row['acctid'],'`%Gartenflirt!`0','`&'.$session['user']['name'].'`6 hat mit dir ein paar wundersch�ne Stunden im Garten verbracht. Ihr habt beide einen Charmepunkt bekommen und haltet euer Geheimnis vor eurem Ehepartner verborgen.');
			}
			else
			{
				$str_output .= '`%Du n�herst dich `6'.$row['name'].'`% und f�ngst an zu flirten was das Zeug h�lt. `6'.$row['name'].'`% steigt darauf ein, ';
				switch(e_rand(1,4))
				{
					case 1:
					case 2:
						$str_output .= '`% und da ihr beide verheiratet seid, versprecht ihr euch gegenseitig, dass niemand jemals davon erfahren wird.';
						$str_output .= '`n`nIhr VERLIERT beide einen Charmepunkt, da ihr euer schlechtes Gewissen nicht vor eurem Ehepartner verbergen k�nnt!';
						$sql = 'UPDATE accounts SET charm=charm-1 WHERE acctid='.$row['acctid'];
						$session['user']['charm']-=1;
						systemmail($row['acctid'],'`%Gartenflirt!`0','`&'.$session['user']['name'].'`6 hat mit dir ein paar sch�ne Stunden im Garten verbracht. Ihr habt beide einen Charmepunkt VERLOREN, da euer schlechtes Gewissen eurem Ehepartner nicht verborgen blieb.');
						db_query($sql);
						$session['user']['seenlover']=1;
						break;
					case 3:
						$str_output .= '`% und da ihr beide verheiratet seid, versprecht ihr euch gegenseitig, dass niemand jemals davon erfahren wird.';
						$str_output .= '`n`nIhr bekommt beide einen Charmepunkt!';
						$sql = 'UPDATE accounts SET charm=charm+1 WHERE acctid='.$row['acctid'];
						$session['user']['charm']+=1;
						systemmail($row['acctid'],'`%Gartenflirt!`0','`&'.$session['user']['name'].'`6 hat mit dir ein paar sch�ne Stunden im Garten verbracht. Ihr habt beide einen Charmepunkt bekommen und haltet euer Geheimnis vor euren Ehepartnern verborgen.');
						db_query($sql);
						$session['user']['seenlover']=1;
						break;
					case 4:
						$str_output .= ' aber ihr werdet bei eurem Vergn�gen von '.($session['user']['sex']?'deinem Mann':'deiner Frau').' erwischt.`nDie Katastrophe ist komplett.`0`n`n'.($session['user']['sex']?'Dein Mann':'Deine Frau').' verl�sst dich';
						if ($session['user']['charisma']==4294967295)
						{
							$str_output .= ' und bekommt 50% deines Verm�gens von der Bank zugesprochen';
							$sql = 'UPDATE accounts SET marriedto=0,charisma=0,goldinbank=goldinbank+'.$getgold.' WHERE acctid='.$session['user']['marriedto'];
							db_query($sql);
							systemmail($session['user']['marriedto'],'`$Scheidung!`0','`6Du hast `&'.$session['user']['name'].'`6 mit `&'.$row['name'].' im Garten erwischt und verl�sst '.($session['user']['sex']?'sie':'ihn').'.`nDir werden `^'.$getgold.'`6 Gold von deinem ehemaligen Ehepartner zugesprochen.');
							$session['user']['goldinbank']-=$getgold;
						}
						$str_output .= '.`nDu verlierst einen Charmepunkt.';
						$session['user']['marriedto']=$row['acctid'];
						$session['user']['charisma']=1;
						$session['user']['seenlover']=1;
						systemmail($row['acctid'],'`%Gartenflirt!`0','`&'.$session['user']['name'].'`6 hat mit dir geflirtet und wurde dabei von '.($session['user']['sex']?'ihrem Mann':'seiner Frau').' erwischt.');
						$session['user']['charm']-=1;
						addnews('`\$'.$session['user']['name'].'`\$ wurde beim Flirten mit '.$row['name'].' `$im Garten von '.($session['user']['sex']?'ihrem Mann':'seiner Frau').' erwischt und ist jetzt wieder solo.');
						break;
				}
			}
		}
		else if ($session['user']['marriedto']==4294967295 || $session['user']['charisma']==4294967295)
		{ // M�glichkeiten, wenn nur selbst verheiratet
			if ($session['user']['marriedto']==4294967295 && $session['user']['charisma']>=5)
			{
				$str_output .= '`6'.($session['user']['sex']?'Seth':'Violet').' springt aus einem Geb�sch und beschimpft dich aufs Heftigste, als du dich '.$row['name'].' n�hern willst. '.($session['user']['sex']?'Er':'Sie').'  beobachtet deine "Gartenarbeit" schon eine ganze Weile!`0`n`n'.($session['user']['sex']?'Seth':'Violet').' verl�sst dich.`nDu verlierst einen Charmepunkt.';
				$session['user']['marriedto']=$row['acctid'];
				$session['user']['charisma']-=1;
				$session['user']['seenlover']=1;
				$session['user']['charm']-=1;
				addnews('`\$'.$session['user']['name'].'`$ wurde beim Flirten mit '.$row['name'].' im Garten von '.($session['user']['sex']?'Seth':'Violet').' erwischt und ist jetzt wieder solo.');
			}
			else
			{
				if ($session['user']['acctid']==$row['marriedto'])
				{
					$str_output .= '`%Obwohl du verheiratet bist, gehst du auf die Flirtversuche von '.$row['name'].' ein. Ihr versteht euch prima und f�r einen Moment vergisst du '.($session['user']['sex']?'deinen Mann':'deine Frau').'. ';
				}
				else
				{
					$str_output .= '`%Obwohl du verheiratet bist, l�sst du dich auf einen Flirt ein. Ihr versteht euch prima und f�r einen Moment vergisst du '.($session['user']['sex']?'deinen Mann':'deine Frau').'. ';
				}
				switch(e_rand(1,4))
				{
					case 1:
					case 2:
					case 3:
						$str_output .= '`% Aber du wei�t, dass eine Beziehung keine Zukunft hat, solange du verheiratet bist.';
						systemmail($row['acctid'],'`%Gartenflirt!`0','`&'.$session['user']['name'].'`6 hat mit dir ein paar sch�ne Stunden im Garten verbracht.');
						$session['user']['seenlover']=1;
						if ($session['user']['marriedto']==4294967295) $session['user']['charisma']+=1;
						break;
					case 4:
						$str_output .= ' Aber '.($session['user']['sex']?'er':'sie').' ruft sich selbst aufs Heftigste ins Ged�chtnis zur�ck!`nDie Katastrophe ist komplett.`0`n`n'.($session['user']['sex']?'Dein Mann':'Deine Frau').' verl�sst dich ';
						if ($session['user']['charisma']==4294967295)
						{
							$str_output .= ' und bekommt 50% deines Verm�gens von der Bank zugesprochen.`nDu verlierst einen Charmepunkt';
							$sql = 'UPDATE accounts SET marriedto=0,charisma=0,goldinbank=goldinbank+'.$getgold.' WHERE acctid='.$session['user']['marriedto'];
							db_query($sql);
							systemmail($session['user']['marriedto'],'`$Scheidung!`0','`6Du hast `&'.$session['user']['name'].'`6 mit `&'.$row['name'].' im Garten erwischt und verl�sst '.($session['user']['sex']?'sie':'ihn').'.`nDir werden `^'.$getgold.'`6 Gold von deinem ehemaligen Ehepartner zugesprochen.');
							$session['user']['goldinbank']-=$getgold;
						}
						$str_output .= '.';
						$session['user']['marriedto']=$row['acctid'];
						$session['user']['charisma']=1;
						$session['user']['seenlover']=1;
						systemmail($row['acctid'],'`%Gartenflirt!`0','`&'.$session['user']['name'].'`6 hat mit dir geflirtet, wurde dabei aber von '.($session['user']['sex']?'ihrem Mann':'seiner Frau').' erwischt.');
						$session['user']['charm']-=1;
						addnews('`$'.$session['user']['name'].'`$ wurde beim Flirten im Garten von '.($session['user']['sex']?'ihrem Mann':'seiner Frau').' erwischt und ist jetzt wieder solo.');
						break;
				}
			}
		}
		else if ($row['marriedto']==4294967295 || $row['charisma']==4294967295)
		{ // M�glichkeiten, wenn nur Gegen�ber verheiratet
			if ($session['user']['marriedto']==$row['acctid'])
			{
				$session['user']['charisma']+=1;
				$session['user']['seenlover']=1;
				$str_output .= '`%Du flirtest zum `^'.$session['user']['charisma'].'.`% Mal mit '.$row['name'].' `%, wei�t aber, dass der Flirt wohl nicht erwidert wird, da '.$row['name'].'`% (noch) verheiratet ist.';
			}
			else
			{
				$str_output .= '`%Du flirtest mit '.$row['name'].'`% und ihr verbringt einige Zeit gemeinsam im Garten.';
				$session['user']['charisma']=1;
				$session['user']['seenlover']=1;
			}
			systemmail($row['acctid'],'`%Gartenflirt!`0','`&.'.$session['user']['name'].'`6 hat mit dir im Garten geflirtet.');
			$session['user']['marriedto']=$row['acctid'];
		}
		else
		{ // beide unverheiratet
			if ($session['user']['acctid']==$row['marriedto'])
			{
				if ($flirtnum>=5)
				{
					if($session['user']['charisma']!=999)
					{
						$session['user']['charisma']=999;
						$sql = 'UPDATE accounts SET charisma=999,charm=charm+1 WHERE acctid='.$row['acctid'];
						db_query($sql);

						$str_output .= '`&Das heutige Treffen ist etwas Besonderes! Ihr versteht euch intuitiv besonders gut, scheint gar vor Liebe der Welt entr�ckt zu sein..`n';
						$str_output .= 'Nach einem langen Gespr�ch, in dem ihr euch immer wieder eurer Zuneigung versichert, fasst sich '.$row['name'].'`& ein Herz und macht dir einen romantischen `bHeiratsantrag`b!`n`n';
						$str_output .= 'Ihr seid jetzt offiziell verlobt. In n�chster Zeit wird ein Priester auf euch zukommen, um die Details eurer Hochzeit zu besprechen. Alternativ k�nntet nat�rlich auch ihr Kontakt mit den Priestern im Tempel aufnehmen!`n`n';

						$session['user']['seenlover']=1;
						$session[bufflist][lover]=$buff;
						$session['user']['charm']+=1;
						$session['user'][donation]+=1;

						addhistory('Verlobung mit '.$row['name'],1,$session['user']['acctid']);
						addhistory('Verlobung mit '.$session['user']['name'],1,$row['acctid']);

						systemmail($row['acctid'],'`&Verlobung!`0','`& Du und `&'.$session['user']['name'].'`& habt nach zahlreichen gemeinsamen Flirts im Garten beschlossen, bald zu heiraten!`nIn n�chster Zeit wird ein Priester auf euch zukommen, um die Details eurer Hochzeit zu besprechen. Alternativ k�nntet nat�rlich auch ihr Kontakt mit den Priestern im Tempel aufnehmen!');

						$sql = 'SELECT acctid FROM accounts WHERE profession='.PROF_PRIEST_HEAD.' ORDER BY loggedin DESC,rand() LIMIT 1';
						$res = db_query($sql);
						if(db_num_rows($res))
						{
							$p=db_fetch_assoc($res);
							systemmail($p['acctid'],'`&Heirat zu planen!`0','`&'.$row['name'].'`& und `&'.$session['user']['name'].'`& haben sich heute verlobt. Du als Priester solltest dich darum bem�hen, den beiden eine sch�ne Hochzeit zu verschaffen!');
						}
					}
					else
					{
						$str_output .= '`&Voller Vorfreude plant ihr eure Hochzeit. Wenn ihr nicht grade mit dem jeweils anderen besch�ftigt seid..';
					}
					addnav('Zum Tempel','tempel.php');
				}
				else if ($flirtnum>0)
				{
					$session['user']['charisma']+=1;
					$session['user']['seenlover']=1;
					$session['user']['charm']+=1;
					$str_output .= '`%Du flirtest zum `^'.$session['user']['charisma'].'. `%Mal mit deiner Flamme '.$row['name'].' `%.`n';
					$str_output .= 'Ihr habt eure Flirts schon '.$flirtnum.' Mal gegenseitig erwidert. Gelingt euch das insgesamt 5 Mal, verspricht '.$row['name'].' `%dir, dich zu heiraten!';
					$str_output .= '`n`n`^Du erh�ltst einen Charmepunkt.';
					systemmail($row['acctid'],'`%Gartenflirt!`0','`&'.$session['user']['name'].'`6 hat mit dir ein paar sch�ne Stunden im Garten verbracht. Damit habt ihr '.$flirtnum.' gegenseitige Flirts. Ab dem 5. gemeinsamen Flirt k�nnt ihr heiraten!');
				}
				else
				{
					$session['user']['charisma']+=1;
					$session['user']['seenlover']=1;
					$session['user']['charm']+=1;
					$str_output .= '`%Du erwiderst den Flirt von '.$row['name'].' `%und verbringst einige Zeit mit '.($session['user']['sex']?'ihm':'ihr').' im Garten.`n';
					systemmail($row['acctid'],'`%Gartenflirt!`0','`&'.$session['user']['name'].'`6 erwidert deinen Flirt und hat mit dir ein paar sch�ne Stunden im Garten verbracht.');
					$str_output .= '`n`n`^Du erh�ltst einen Charmepunkt.';
				}
				$session['user']['marriedto']=$row['acctid'];
			}
			else if ($session['user']['marriedto']==$row['acctid'])
			{
				$session['user']['charisma']+=1;
				$session['user']['seenlover']=1;
				$str_output .= '`%Du flirtest zum `^'.$session['user']['charisma'].'.`% Mal mit '.$row['name'].' `%und hoffst darauf, dass der Flirt erwidert wird.';
				systemmail($row['acctid'],'`%Gartenflirt!`0','`&'.$session['user']['name'].'`6 hat mit dir schon wieder ein paar sch�ne Stunden im Garten verbracht.`nWillst du nicht mal reagieren?');
			}
			else
			{
				$str_output .= '`%Du flirtest mit '.$row['name'].'`% und ihr verbringt einige Zeit gemeinsam im Garten.';
				systemmail($row['acctid'],'`%Gartenflirt!`0','`&'.$session['user']['name'].'`6 hat mit dir ein paar sch�ne Stunden im Garten verbracht.');
				$session['user']['charisma']=1;
				$session['user']['seenlover']=1;
				$session['user']['marriedto']=$row['acctid'];
			}
		}
	}
	else
	{
		$str_output .= '`$Fehler:`4 Dieser Krieger wurde nicht gefunden. Darf ich fragen, wie du �berhaupt hierher gekommen bist?';
	}
}
else
{
	addcommentary();
	checkday();

	$show_invent = true;

	$str_output .= '`b`c`2Die G�rten`0`c`b';
	if (!$session['user']['prefs']['nosounds'])
	{
		$str_output .= '<embed src="media/vogel.wav" width=10 height=10 autostart=true loop=false hidden=true volume=100>';
	}
	$str_output .= '`n`n`2Du betrittst den Garten und  pl�tzliche Ruhe umf�ngt dich. ';
	$str_output .= 'Die Bl�tter der B�ume rascheln leise in einer sanften B�e und das Gras wiegt sich hin und her, als tanze es zu einer Melodie. ';
	$str_output .= 'Durch den Garten schl�ngelt sich ein silbrig gl�nzender, fr�hlich pl�tschernder Fluss, �ber den sich eine Br�cke aus wei�em Stein spannt. Verborgen hinter einigen B�umen liegt ein kleiner Tempel, der majest�tische Gelassenheit ausstrahlt. ';
	$str_output .= 'Hier und dort in den nat�rlichen Nischen der B�sche siehst du das eine oder andere Paar, das sich leise unterh�lt, sich die ewige Liebe schw�rt oder sich in den Armen liegt. ';
	$str_output .= 'Du sp�rst, dass eine besonnene Stimmung �ber diesem Garten liegt und instinktiv ist dir bewusst, dass hier besondere Regeln herrschen, wenn du hier geduldet werden willst.`n`n ';
	viewcommentary('gardens','Hier fl�stern',30,'fl�stert');

	addnav('Liebesdinge');
	addnav('Flirten','gardens.php?op=flirt1');
	addnav('Tempel','tempel.php');
	addnav('Geschenkeladen','newgiftshop.php');

	addnav('Der Garten');
	addnav('S?Zur Schaukel','gardens.php?op=swing');
    addnav('W?Zur Wolkeninsel','wolkeninsel.php');
	addnav('Blumenbeet','flowers.php');
	addnav('Tiefer in den Garten','treeoflife.php');

	addnav('Zur�ck');

}
addnav('Zur�ck zum Dorf','village.php');
headoutput($str_output,true);
page_footer();
?>