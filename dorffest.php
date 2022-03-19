<?php
/*
Das Dorffest by Dragonslayer

Fixes & Addons by Maris (Maraxxus@gmx.de)
20.6.2006 Tippfehler (Forum 28.3.06) behoben
*/

require_once 'common.php';
page_header('Das Dorffest');

// F�r Feuerschrein brauchen wir Postl�nge
if(isset($_POST['insertcommentary']['party_fireplace'])) {
	$int_fireshrine_post_len = strlen(preg_replace('/\(.*\)/','',$_POST['insertcommentary']['party_fireplace']));
}

addcommentary();
checkday();
output('`c`b`&Das Dorffest`0`b`c`n`n');
if (!isset($session))
{
	exit();
}

//Each time we reload we get a bit less stuffed
if($_SESSION['bbq_hunger'] != '' && $_SESSION['bbq_hunger'] > 1)
{
	$_SESSION['bbq_hunger']--;
	if($_SESSION['bbq_hunger']<0)
	{
		$_SESSION['bbq_hunger']=0;
	}
}
//Each time we reload we gain a bit of our condition back
if($_SESSION['dance_condition'] != '' && $_SESSION['dance_condition'] > 1)
{
	$_SESSION['dance_condition']--;
	if($_SESSION['dance_condition']<0)
	{
		$_SESSION['dance_condition']=0;
	}
}

//User get drunk, if they get too drunk, they die!
//Der S�ufertot
//Elfen m�ssen aufpassen
if ($session['user']['drunkenness']>99)
{
	if ($session['user']['race']=='elf')
	{
		page_header('Du hast soviel gesoffen');
		output('Du hast zuviel gesoffen und bist an einer Alkoholvergiftung gestorben.`n`n ');
		output('Du verlierst 5% deiner Erfahrungspunkte und die H�lfte deines Goldes!`n`n');
		output('Du kannst morgen wieder spielen.');
		killplayer(50,5,0,'');
		addnews($session['user']['name'].' hat '.($session['user']['sex']?'ihren':'seinen').' zarten Elfenk�rper auf dem Dorffest mit zuviel Ale zugrunde gerichtet.');
		addnav('T�gliche News','news.php');
		page_footer();
		break;
	}
	//Zwerge vertragen mehr
	else if ($session['user']['race']== 'zwg')
	{
		switch(e_rand(1,10))
		{
			case 1:
			case 2:
			case 3:
			case 4:
			case 5:
				output('Du hast zwar zuviel gesoffen, aber da ein Zwerg einiges vertragen kann, hast du es gerade noch �berlebt.`n');
				output('Du verlierst den Gro�teil deiner Lebenspunkte!');
				$session['user']['hitpoints']=1;
				$session['user']['drunkenness']=90;
				addnews($session['user']['name'].' entging nur knapp den Folgen einer Alkoholvergiftung, weil '.($session['user']['sex']?'sie eine Zwergin':'er ein Zwerg').' ist.');
				addnav('Torkel weiter.','dorffest.php');
				break;
			case 6:
			case 7:
			case 8:
			case 9:
			case 10:
				page_header('Du hast soviel gesoffen');
				output('Du hast zuviel gesoffen und bist an einer Alkoholvergiftung gestorben.`n`n ');
				output('Du verlierst 5% deiner Erfahrungspunkte und die H�lfte deines Goldes!`n`n');
				output('Du kannst morgen wieder spielen.');
				killplayer(50,5,0,'');
				addnews($session['user']['name'].' starb an einer �berdosis Ale auf dem Dorffest');
				addnav('T�gliche News','news.php');
				page_footer();
				break;
		}
	}
	//Alle anderen bekommen ne Chance
	switch(e_rand(1,10))
	{
		case 1:
		case 2:
		case 3:
			output('Du hast zwar zuviel gesoffen, es aber gerade noch �berlebt.`n');
			output('Du verlierst den Gro�teil deiner Lebenspunkte!');
			$session['user']['hitpoints']=1;
			$session['user']['drunkenness']=90;
			addnews($session['user']['name'].' entging nur knapp den Folgen einer Alkoholvergiftung.');
			addnav('Torkel weiter.','dorffest.php');
			break;
		case 4:
		case 5:
		case 6:
		case 7:
		case 8:
		case 9:
		case 10:
			page_header('Du hast soviel gesoffen');
			output('Du hast zuviel gesoffen und bist an einer Alkoholvergiftung gestorben.`n`n ');
			output('Du verlierst 5% deiner Erfahrungspunkte und die H�lfte deines Goldes!`n`n');
			output('Du kannst morgen wieder spielen.');
			killplayer(50,5,0,'');
			addnews($session['user']['name'].' starb an einer �berdosis Ale auf dem Dorffest. ');
			addnav('T�gliche News','news.php');
			page_footer();
			break;
	}
}


//Standard Text
if ($_GET['op']=='')
{
	output('`2 Du trittst auf die gro�e, bunt geschm�ckte Festwiese, die auf allen Seiten von B�umen ges�umt wird.
	In den hohen Baumkronen h�ufen sich bunte Lampions und in den Schatten der B�ume knutschen P�rchen. Du betrachtest viele B�rger, die tanzen, essen,
	ausgelassen schwatzen, oder einfach nur feiern!`n
	`n`@Es ist B�rgerparty!`@`n
	`n`2 Geradezu spielt Seth mit einer kleinen Band ein paar stimmige Lieder. Es wird ausgelassen getanzt und es liesse sich
	bestimmt die eine oder andere nette Bekanntschaft schliessen, stellst du mit fachkundlichem Blick fest.
	M�chtest du dich dazu gesellen?`n
	Andererseits weht auch der Duft des Grills heran, wo sich bestimmt die eine oder andere Leckerei finden liesse.`n
	Das Lagerfeuer sieht hingegen auch sehr einladend aus, man kann dort bestimmt gut das eine oder andere bereden und ein
	leckeres Ale trinken. Irgendwie klasse, dass Dragonslayer alle alkoholischen Getr�nke auf Kosten der Steuern �bernimmt!');
	if($session['user']['marriedto']=='4294967295')
	{
		$str_mate = ($session['user']['sex']==0)?'Seth':'Violet';
		output("`2 Schade, dass $str_mate so viel zu tun hat, du h�ttest nichts gegen ein T�nzchen einzuwenden. Naja, vielleicht sp�ter");
	}
	output('`n`n');
	viewcommentary('party_main','Auf der Wiese feiern',30,'erz�hlt');
	addnav('Dorffest');
	addnav('T?Zum Tanze...', 'dorffest.php?op=dance');
	addnav('L?Zum Lagerfeuer','dorffest.php?op=fire');
	addnav('G?Zum Grill','dorffest.php?op=grill');
	addnav('St�nde');
	addnav('Schnappers Losstand','dorffest.php?op=profitgame');

	if($session['user']['marriedto']!=0 && $session['user']['marriedto'] != 4294967295)
	{
		addnav('F�r Verliebte');
		addnav('Lauschiges Pl�tzchen suchen','dorffest.php?op=flirt');
	}

	addnav('Zum Dorf');
	addnav('Z?Zur�ck zum Dorf','village.php');
}

// Losestand von Schnapper
elseif ($_GET['op']=='profitgame')
{
	addnav('Losstand');

	if ($_GET['uac']=='buy') // Kaufen eines Loses
	{
		if ($session['user']['gold']>=100)
		{
			switch(rand(0,11))
			{
				case 0:
				case 1:
				case 2:
					$los='`wblaues';
					$losmsg='...es ist ein Gewinn! Hurra! Schnell zeigst du Schnapper das Los und er �bergibt dir etwas. Es ist eine M�ckenfalle.';

					$item['tpl_name']='M�ckenfalle';
					$item['tpl_description']='Ein Glas mit einer gelblichen Substanz innen drin. Es zieht M�cken magisch an.';
					$item['tpl_gold']=25;
					$item['tpl_gems']=0;

					item_add($session['user']['acctid'], 'mueckfalle', $item);
					break;

				case 3:
				case 4:
				case 5:
					$los='`@gr�nes';
					$losmsg='...es ist ein Gewinn! Hurra! Schnell zeigst du Schnapper das Los und er �bergibt dir etwas. Es ist ein St�ck Katzengold.';

					$item['tpl_name']='Katzengold';
					$item['tpl_description']='Wertloses Gold. Schade!';
					$item['tpl_gold']=75;
					$item['tpl_gems']=0;

					item_add($session['user']['acctid'], 'katzengold', $item);
					break;

				case 11:
					$los='`^goldenes';
					$goldpresse=item_get('name="Goldpresse" AND owner='.$session['user']['acctid']);

					if (getsetting('ci_goldpresse',0) AND $goldpresse==false)
					{
						$losmsg='...es ist ein Gewinn! Hurra! Schnell zeigst du Schnapper das Los und er �bergibt dir die ber�chtigte `b`qGoldpresse`b`t. Was du genau damit machen kannst ist dir noch nicht bekannt, aber es wird wohl was ganz besonderes sein.';

						$item['tpl_name']='Goldpresse';
						$item['tpl_description']='Auf einem Schildchen an der Maschine steht: "1 Gold einwerfen und Hebel ziehen. Danach Aush�ngeschild entnehmen."';
						$item['tpl_gold']=0;
						$item['tpl_gems']=0;

						item_add($session['user']['acctid'], 'unikat', $item);
					}

					else
					{
						$losmsg="...es ist ein Gewinn! Hurra! Schnell zeigst du Schnapper das Los und er �bergibt dir ein S�ckchen Gold. Beim genauen hinschauen bemerkst du, dass es gesunde Getreidetaler sind. Da du ein wenig hungrig bist nimmst du dir gleich alle und verdr�ckst sie. Sie sind voller Energie und du f�hlst dich bereit den Wald nochmal einen Besuch abzustatten um etwas die Menge aufzumischen.`n`n`#Du erh�ltst 3 Waldk�mpfe.";

						$session['user']['turns']+=3;
					}
					break;

				default:
					$los='`$rotes';
					$losmsg='...es ist eine Niete! So ein Mist. Nunja, vielleicht wird es beim n�chsten mal besser.';
					break;
			}

			output('`q"Danke f�r dein Gold und hier, nimm dir ein Los!"`t, sagt Schnapper gerade zu �berfreundlich. Aber ohne dies auch zu kommentieren greifst du in den Eimer. Schnell ziehst du ein Los herraus.`n`nEs ist ein '.$los.'`t Los. Mal schauen was es bringt. Langsam �ffnest du es und...`n`n'.$losmsg);

			$session['user']['gold']-=100;

			addnav('Nochmal ziehen','dorffest.php?op=profitgame&uac=buy');
			addnav('Stand verlassen','dorffest.php');
		}

		else
		{
			output('`tDu m�chtest dein Gl�ck versuchen und hast bereits ein Los in der Hand. Doch Schnapper fragt erst, ob du auch Gold dabei hast, welches die Kosten deckt. Deinem Gesichtsausdruck zu folge entreisst dir Schnapper das Los und packt es zur�ck. `q"Komm zur�ck wenn du Gold hast!"`t. Mit diesen Worten verscheucht dich Schnapper.');
			addnav('Stand verlassen','dorffest.php');
		}
	}

	else // Standardtext
	{
		output('`tDu entdeckst Schnapper etwas am Rande stehen. Laut schreiend versucht er seine Ware unter die Leute zu bringen. Doch diese Idee ist mal was anderes.`n`n`q"KAUFT LOSE! JEDES LOS IST EIN GEWINN!".`t`n`nDen Rest fl�stert er nur leise, aber es heisst wohl: Zumindestens f�r mich. Damit ist es mal wieder klar. Der Satz "Kauft Lose! Jedes Los ein Gewinn, zumindestens f�r mich!" l�sst dich vorsichtig sein w�hrend du den Stand ansteuerst. Du denkst: Ein Versuch ist es Wert. Doch so ein Schlitzohr wie Schnapper ist alles zu zutrauen. Selbst die Preise sind ein Wunder f�r sich: `^100 Goldst�cke `tdas Los. Eine nette Summe!`n`n`#Was m�chtest du tun?');

		addnav('1 Los kaufen','dorffest.php?op=profitgame&uac=buy');
		addnav('Stand verlassen','dorffest.php');
	}
}

else if($_GET['op']=='flirt')
{
	$query_result = mysql_query("Select name, sex from accounts where acctid = '{$session['user']['marriedto']}' LIMIT 1");
	$arr_mate = mysql_fetch_array($query_result);
	output("`2 Als du inmitten der tanzenden Menge {$arr_mate[0]} ersp�hst, macht dein Herz einen Sprung! Voller Freude
	lauft ihr euch entgegen und ergreift euch bei den H�nden.`n
	Ein Blick gen�gt und ihr versteht euch. Bereits nach kurzer Zeit habt ihr die ausgelassene Menge hinter euch gelassen und
	befindet euch ein St�ck tief im friedlichen Wald direkt am Dorfrand. Leicht k�nnt ihr noch den Kl�ngen der Musik lauschen,
	doch euer Interesse gilt eigentlich etwas anderem.`n
	Als der Mond euch in weiche Schatten h�llt, bemerkt ihr, dass ihr v�llig allein auf einer wundersch�nen kleinen Lichtung steht
	- Wie herrlich! `n`n");

	output("Dieser Platz ist NUR f�r euch beide, ihr seid hier v�llig ungest�rt!`n`n");

	addnav("Wege");
	addnav("Z?Zur�ck","dorffest.php");

	//Little disturbance by another couple, but only little chance to take place
	switch(e_rand(0,300))
	{
		case 300:
			$query_result = mysql_query("Select name, marriedto from accounts where marriedto != 4294967295 AND marriedto != 0 order by rand() Limit 1");
			$arr_first_name = mysql_fetch_array($query_result);
			if($arr_first_name == false)
			{
				break;
			}
			$query_result = mysql_query("SELECT name from accounts where acctid = '{$arr_first_name[1]}'");
			$arr_second_name = mysql_fetch_array($query_result);
			output("`$ Pl�tzlich raschelt etwas im Geb�sch! Ihr zuckt erschrocken zusammen und k�nnt erkennen, wie sich
		`@{$arr_first_name[0]} `$ und `@$arr_second_name[0] `$ gemeinsam durch das Geb�sch schleichen`n`n
		`4 Ihr grinst euch gegenseitig an...was die beiden wohl gesucht haben *g*");
			break;
	}

	//Generate a unique commentary ID which only those two can read
	$temp_array = array($session['user']['marriedto'],$session['user']['acctid']);
	sort($temp_array);

	$id_for_party_flirt = implode('',$temp_array);
	viewcommentary($id_for_party_flirt,"Fl�stern",30,"fl�stert");
}

else if ($_GET['op']=='dance')
{
	output('`2Du trittst auf die rappelvolle Tanzfl�che und willst dem anderen Geschlecht mal so richtig zeigen was Sache ist!`n
	Als die Musik aufspielt beginnst du, wie alle anderen auch, mit einem gewagten Tanz.');

	addnav('Tanzen');
	addnav('Imponieren','dorffest.php?op=dancefloor&action=posing');
	addnav('Ruhiger Tanz','dorffest.php?op=dancefloor&action=gossip');


	//Specials for special charakters
	if($session['user']['thievery']>0 || $session['user']['race']=='vmp')
	{
		addnav('Special');
		if($session['user']['thievery']>0)
		{
			addnav('T�nzer bestehlen','dorffest.php?op=special&action=steal');
		}

		if($session['user']['race']=='vmp')
		{
			addnav('Opfer aussaugen','dorffest.php?op=special&action=suck');
		}
	}

	addnav('Wege');
	addnav('Z?Zur�ck','dorffest.php');
}

//Add some special events for special charkters
else if($_GET['op']=='special')
{
	switch($_GET['action'])
	{
		case 'steal':
			if($_SESSION['specialtries']<10)
			{
				//Chance to steal and to fight somebody is rather high
				switch(e_rand(1,5))
				{
					case 1:
						output('Es schaut gerade niemand hin, wie zuf�llig rempelst du jemanden an und
						wie zuf�llig fallen dir einige Goldm�nzen in die Hand');
						$session['user']['gold']+=e_rand(50,200);
						addnav('Wege');
						addnav('Z?Zur�ck','dorffest.php?op=dance');
						break;
					case 5:
						$query_result = mysql_query('SELECT name,level,weapon,attack,defence,hitpoints from accounts order by rand() Limit 1');
						$arr_result_user = mysql_fetch_array($query_result);

						$badguy = array(
						'creaturename'=>$arr_result_user['name']
						,'creaturelevel'=>$arr_result_user['level']
						,'creatureweapon'=>$arr_result_user['weapon']
						,'creatureattack'=>$arr_result_user['attack']
						,'creaturedefense'=>$arr_result_user['defence']
						,'creaturehealth'=>$arr_result_user['hitpoints']
						,'diddamage'=>0);

						$userattack=$session['user']['attack']+e_rand(1,3);
						$userhealth=round($session['user']['hitpoints']);
						$userdefense=$session['user']['defense']+e_rand(1,3);
						$badguy[creaturelevel]=$session['user']['level'];
						$badguy[creatureattack]+=($userattack-4);
						$badguy[creaturehealth]+=$userhealth;
						$badguy[creaturedefense]+=$userdefense;
						$session[user][badguy]=createstring($badguy);
						output('`2Verdammt, dein Opfer hat dich bemerkt!');

						addnav('K�mpfe!!!','dorffest.php?op=fight');
						page_footer();
						break;
					default:
						output('`2 Hm, es schaut gerade zuf�llig jemand in deine Richtung, du l�sst es wohl lieber bleiben.');
				}
			}
			addnav('Wege');
			if($_SESSION['specialtries']<10)
			{
				$_SESSION['specialtries']++;
				addnav('N?Nochmal versuchen','dorffest.php?op=special&action='.$_GET['action']);
			}
			else
			{
				output('`n`2 Du denkst dir, dass es besser w�re es erstmal bleiben zu lassen, sonst sch�pft noch jemand Verdacht.');
			}
			addnav('Z?Zur�ck','dorffest.php?op=dance');
			break;
		case 'suck':
			//chance to suck and fight somebody is rather high
			if($_SESSION['specialtries']<10)
			{
				switch(e_rand(1,5))
				{
					case 1:
						output('Es schaut gerade niemand hin, wie zuf�llig rempelst du jemanden an und
						wie zuf�llig bei�t du deinem Opfer unauff�llig in den Hals... Du hast halt �bung darin!`n...oder sie sind halt schon etwas angetrunken!');
						$session['user']['hitpoints']+=e_rand(25,75);
						addnav('Wege');
						addnav('Z?Zur�ck','dorffest.php?op=dance');
						break;
					case 5:
						$query_result = mysql_query('SELECT name,level,weapon,attack,defence,hitpoints from accounts order by rand() Limit 1');
						$arr_result_user = mysql_fetch_array($query_result);

						$badguy = array(
						'creaturename'=>$arr_result_user['name']
						,'creaturelevel'=>$arr_result_user['level']
						,'creatureweapon'=>$arr_result_user['weapon']
						,'creatureattack'=>$arr_result_user['attack']
						,'creaturedefense'=>$arr_result_user['defence']
						,'creaturehealth'=>$arr_result_user['hitpoints']
						,'diddamage'=>0);

						$userattack=$session['user']['attack']+e_rand(1,3);
						$userhealth=round($session['user']['hitpoints']);
						$userdefense=$session['user']['defense']+e_rand(1,3);
						$badguy[creaturelevel]=$session['user']['level'];
						$badguy[creatureattack]+=($userattack-4);
						$badguy[creaturehealth]+=$userhealth;
						$badguy[creaturedefense]+=$userdefense;
						$session[user][badguy]=createstring($badguy);

						output('`2Verdammt, dein Opfer hat dich bemerkt!');
						addnav('K�mpfe!!!','dorffest.php?op=fight');
						page_footer();
						break;
					default:
						output('`2 Hm, es schaut gerade zuf�llig jemand in deine Richtung, du l�sst es wohl lieber bleiben.');
				}
			}
			addnav('Wege');
			if($_SESSION['specialtries']<10)
			{
				$_SESSION['specialtries']++;
				addnav('N?Nochmal versuchen','dorffest.php?op=special&action='.$_GET['action']);
			}
			else
			{
				output('`n`2 Du denkst dir, dass es besser w�re es erstmal bleiben zu lassen, sonst sch�pft noch jemand Verdacht.');
			}
			addnav('Z?Zur�ck','dorffest.php?op=dance');
			break;
		default:
	}
}

else if($_GET['op']=='dancefloor')
{
	switch($_GET['action'])
	{
		case 'gossip':
			output('`2 Du tanzt ruhig und gelassen mit einigen Bekannten und unterh�ltst dich nett.`n`n');
			viewcommentary('party_dancefloor','Beim tanzen unterhalten',30,'sagt');
			break;
		default:
			if($_SESSION['dance_condition']>70)
			{
				output('`2 Deine F��e tun weh - Du kannst bestimmt nicht so schnell wieder tanzen... Erstmal eine kleine Pause am Feuer?
				Aber auf jeden Fall was ruhiges! Mit der Zeit wirst du dich schon erholen.');
				break;
			}
			switch(e_rand(1,20))
			{
				case 1:
					output('`2Bei einem gewagten Man�ver verdrehst du dir das Knie und PLAUTZ liegst du auf der Nase... Naja, das k�nnen wir aber besser!`n
					Zum Gl�ck hat es niemand gesehen, so dass du ohne Peinlichkeiten aufstehen und weiter machen kannst.');
					$_SESSION['dance_condition']+=20;
					break;
				case 5:
					output('`2 Du drehst eine Pirouette - gekonnt, gekonnt.');
					$_SESSION['dance_condition']+=5;
					break;
				case 6:
					output('`2 Eine Soloeinlage w�re jetzt nicht schlecht, denkst du dir... Schade nur, dass es hier so eng ist.');
					$_SESSION['dance_condition']+=5;
					break;
				case 15:
					output('Ungeschickt rutscht du aus und stolperst von der Tanzfl�che. Dort f�llt dir ein kleines Goldst�ck auf, das wohl jemand verloren hat.
					Dem gibst du wohl besser schnell ein neues zu Hause!');
					$session['user']['gold']++;
					$_SESSION['dance_condition']+=5;
					break;
				case 20:
					output('`2 Du tanzt heute abend einfach g�ttlich und viele Blicke fliegen dir zu!
					Du f�hlst dich berauscht und bist bei einigen Beobachtern sicher in der Achtung gestiegen!`n
					`@Du erh�ltst einen Charmpunkt`n`n
					Schnell merkst du aber, dass so etwas doch arg auf die Kondition geht...Du bist v�llig au�er Puste');
					$session['user']['charm']++;
					$_SESSION['dance_condition']+=300;
					break;
				default:
					output('`2Du tanzt eine Weile vor dich hin und f�hlst dich dabei einfach gro�artig.');
					$_SESSION['dance_condition']+=5;
			}
	}
	addnav('Tanzen');
	addnav('Imponieren','dorffest.php?op=dancefloor&action=posing');
	addnav('Ruhiger Tanz','dorffest.php?op=dancefloor&action=gossip');
	addnav('Wege');
	addnav('Z?Zur�ck','dorffest.php');
}

//The fireplace
else if ($_GET['op']=='fire')
{
	switch($_GET['action'])
	{
		case 'gossip':
			output('`2 Du setzt dich an das Lagerfeuer zu ein paar alten oder neuen Bekannten und beginnst eine angeregte Diskussion.`n`n');
			viewcommentary('party_fireplace','Am Lagerfeuer erz�hlen',30,'sagt');
			if (su_check(SU_RIGHT_DEBUG)) { addnav('Feuerschrein','fireshrine.php');}
			if ($session['user']['drunkenness']>60 && $bool_comment_written)
			{
				$int_max_chance = ceil(1300 / ($int_fireshrine_post_len + 1));
				$int_max_chance = max($int_max_chance,4);
				
				if(e_rand(1,$int_max_chance)==1) {
					redirect('fireshrine.php'); 
				}
			}
			
			addnav('Etwas zu trinken holen','dorffest.php?op=fire');
			
			break;
		default:
			output('`2Hach ja, am Feuer kann man sich immer das eine oder andere erz�hlen und auch das eine oder andere trinken.
			Ja, besonders trinken... Denn getrunken wird hier reichlich,
			schlie�lich ruft gerade wieder einmal jemand `@FREIIIIBIIIIIER`2 als du ankommst.`n
			`@"Endlich mal ne vern�nftige Verwendung f�r die Steuergelder!"`2 denkst du dir!`n');
			output('`2 Schon kommt Cedrik mit einem riesigen Tablett auf dich zu und ehe du dich versiehst, hast du wieder etwas zu trinken in der Hand!');

			addnav('Lagerfeuer');
			addnav('Ans Lagerfeuer setzen','dorffest.php?op=fire&action=gossip');

			addnav('Getr�nke');
			addnav('Ale','dorffest.php?op=get_drink&action=ale');
			addnav('Met','dorffest.php?op=get_drink&action=met');
			addnav('Orkenwein','dorffest.php?op=get_drink&action=wine');
			addnav('Gr�ner Drachenschnaps','dorffest.php?op=get_drink&action=goodstuff');
			addnav('MILCH! (20 Gold)','dorffest.php?op=get_drink&action=milk');
	}

	addnav('Wege');
	addnav('Z?Zur�ck','dorffest.php');
}
else if($_GET['op']=='get_drink')
{
	switch($_GET['action'])
	{

		case 'ale':
			output('`2 Hmmm, k�stlich!');
			$session['user']['drunkenness']+=10;
			break;
		case 'met':
			output('`2 Sch�n s��, genau wie du es magst');
			$session['user']['drunkenness']+=10;
			break;
		case 'wine':
			output('`2Hm, edler Wein, ganz ausgezeichneter Jahrgang und fantastisches Bouquet. Er schmeichelt deinem Gaumen!');
			$session['user']['drunkenness']+=15;
			break;
		case 'goodstuff':
			output('`2 HUIUIUIUIUI, halt dich lieber am Boden fest!!! Man ist der scharf!`n
			Du f�hlst dich, als ob du Feuer speien k�nntest... Naja, wenigstens wei�t du jetzt warum das geniale Ges�ff
			`@Gr�ner Drachenschnaps`2 heisst. Man, geht der in den Kopf!');
			$session['user']['drunkenness']+=30;
			break;
		case 'milk':
			if($session['user']['gold']<20)
			{
				output('`2 So sehr du jetzt auch vielleicht eine Milch brauchst, du kannst sie dir nicht leisten!');
			}
			else
			{
				output('`2 So dumm es auch vielleicht aussieht Milch zu trinken, durch die Milch f�hlst du dich besser und etwas klarer!');
				$session['user']['gold'] -=20;
				$session['user']['drunkenness']-=10;
				if ($session['user']['hitpoints']>$session['user']['maxhitpoints'])
				{ 
					$session['user']['hitpoints']=$session['user']['maxhitpoints']; 
				}
			}
			break;
	}
	addnav('Wege');
	addnav('Z?Zur�ck','dorffest.php?op=fire');
}
//The grill
else if ($_GET['op']=='grill')
{
	if($_SESSION['bbq_hunger']>50)
	{
		output('`2Also wenn du jetzt noch etwas essen m�sstest, dann wird dir sicher spei�bel.
		Lassen wir das erstmal sch�n wieder sacken.');
	}
	else
	{
		output('`2Hmmm, der Duft von gebratenem Fleisch und Knollengem�se liegt in der Luft und der warme
		flackernde Schein des offenen Feuers tut sein �briges - dir l�uft das Wasser im Munde zusammen.`n
		Da die Schlange vor dir nicht allzu lang erscheint, stellst du dich an, zuversichtlich dass du
		einige Leckereien bekommen wirst. Als du endlich an der Reihe bist, wirfst du einen Blick auf den Grill. ');
		output('`@"Tjo", `2meint der Grillmeister, `@"siehst ja wie es hier zugeht, wie bei der Raubtierf�tterung.
		Tut mir leid, wenn wir nicht immer alles da haben, ich muss erst frisch nachlegen, das dauert halt ne Weile!" ');
		output('`2Kein Problem, denkst du dir, nehm ich halt was gerade da ist.');

		addnav('Grillgut');
		switch (e_rand(0,3))
		{
			case 1:
				addnav('Grillwurst (5 Gold)','dorffest.php?op=buy_bbq&action=sausage');
				addnav('Nackensteak (15 Gold)','dorffest.php?op=buy_bbq&action=steak');
				addnav('Grillhaxe (50 Gold)','dorffest.php?op=buy_bbq&action=bigpork');
				break;
			case 2:
				addnav('Grillwurst (5 Gold)','dorffest.php?op=buy_bbq&action=sausage');
				addnav('Nackensteak (15 Gold)','dorffest.php?op=buy_bbq&action=steak');
				addnav('Maiskolben (10 Gold)','dorffest.php?op=buy_bbq&action=corncrob');
				break;
			case 3:
				addnav('Kartoffel (5 Gold)','dorffest.php?op=buy_bbq&action=potato');
				addnav('Maiskolben (10 Gold)','dorffest.php?op=buy_bbq&action=corncrob');
				addnav('T-Bone Steak (75 Gold)','dorffest.php?op=buy_bbq&action=tbone');
				break;
			default:
				addnav('Grillwurst (5 Gold)','dorffest.php?op=buy_bbq&action=sausage');
				addnav('Nackensteak (15 Gold)','dorffest.php?op=buy_bbq&action=steak');
				addnav('Grillhaxe (50 Gold)','dorffest.php?op=buy_bbq&action=bigpork');
		}
	}
	addnav('Wege');
	addnav('Z?Zur�ck','dorffest.php');
}
else if($_GET['op'] == 'buy_bbq')
{
	//Let the user pay and decrease the hunger
	switch($_GET['action'])
	{
		case 'sausage':
			$session['user']['gold'] -= 5;
			$_SESSION['bbq_hunger']+=5;
			break;
		case 'steak':
			$session['user']['gold'] -= 15;
			$_SESSION['bbq_hunger']+=15;
			break;
		case 'bigpork':
			$session['user']['gold'] -= 50;
			$_SESSION['bbq_hunger']+=50;
			break;
		case 'corncrob':
			$session['user']['gold'] -= 10;
			$_SESSION['bbq_hunger']+=5;
			break;
		case 'potato':
			$session['user']['gold'] -= 5;
			$_SESSION['bbq_hunger']+=5;
			break;
		case 'tbone':
			$session['user']['gold'] -= 75;
			$_SESSION['bbq_hunger']+=20;
			break;
	}

	//Not enough money available
	if($session['user']['gold']<1)
	{
		output('`@"Na ja, wollen wir mal nicht so sein, das bekommst Du heute auch mal f�r etwas weniger Geld!"`n');
		$session['user']['gold']=0;

	}
	output('`2 Du bezahlst und bei�t herzhaft hinein!');

	//Special for the food
	switch(e_rand(1,10))
	{
		case 1:
			output('`nEs ist ein bisschen kalt, aber sonst sehr lecker.');
			break;
		case 2:
			output('`nHm, sehr lecker, ein Labsaal f�r deinen Magen.');
			$session['user']['hitpoints']++;
			break;
		case 3:
			output('`nVerdammt ist das heiss, du verbrennst dir ein wenig die Zunge! Aba schonscht scher lecka.');
			$session['user']['hitpoints']--;
			break;
		case 4:
			output('`n`$BU�RKS!!!`2 Da muss wohl Schnapper eins von seinen W�rsten untergeschummelt haben... Da vergeht einem ja alles!');
			$session['user']['hitpoints']-=20;
			$_SESSION['bbq_hunger']+=300;
			break;
		case 10:
			output('`n Du willst gerade in dein leckeres Essen hineinbei�en. Das Wasser l�uft dir im Munde zusammen. Du schliesst die Augen, �ffnest den Mund und&nbsp;-`n
			`$WAS ZUM?!?`n`n
			`@Du wirst versehentlich angesto�en und das leckere, �beraus saftige, Wasser im Mund zusammenlaufen
			lassende St�ckchen Gl�ck f�llt dir aus der Hand und direkt in den Dreck,
			wo sich schon einige geifernde Hunde dar�ber her machen.`nMist!');
			$_SESSION['bbq_hunger']-=20;
			break;
		default:
			output("`nDu l�sst dir das Essen munden. Und dann auch noch so preiswert... So ein Dorffest ist schon etwas feines.");
	}

	//The user does not have to die here, if the hitpoints get below one, increase them
	if($session['user']['hitpoints']<1)
	{
		$session['user']['hitpoints']=1;
	}
	addnav('Wege');
	addnav('Z?Zur�ck','dorffest.php');
}



if ($_GET['op']=='fight' or $_GET['op']=='run')
{
	$battle=true;
	$fight=true;
	if ($battle == true)
	{
		include_once ('battle.php');

		if ($victory == true)
		{
			output('`b`4Du hast `^'.$badguy['creaturename'].'`4 besiegt.`b`n');
			$badguy=array();
			$session['user']['badguy']='';
			$session['user']['specialinc']='';
			$gold=e_rand(100,500);
			$experience=$session['user']['level']*e_rand(37,99);
			output('`#Du erh�ltst `6'.$gold.' `#Gold!`n');
			$session['user']['gold']+=$gold;
			output('`#Du erh�ltst `6'.$experience.' `#Erfahrung!`n');
			$session['user']['experience']+=$experience;
			addnav('Weiter','dorffest.php?op=dance');
		}
		else if ($defeat == true)
		{
			output('`4Als du auf dem Boden aufschl�gst, dreht sich  `^'.$badguy['creaturename'].'`4 um und tanzt weiter.');
			$badguy=array();
			$session['user']['badguy']='';
			$session['user']['hitpoints']=0;
			$session['user']['alive']=0;
			$session['user']['specialinc']='';
			addnav('Weiter','shades.php');
		}
		else
		{
			if ($fight)
			{
				fightnav(true,false);
				if ($badguy['creaturehealth'] > 0)
				{
					$hp=$badguy['creaturehealth'];
				}
			}
		}
	}
	else
	{
		redirect('dorffest.php?op=dance');
	}
}

page_footer();
?>
