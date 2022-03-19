<?php
// Gestr�pp... hier findet man auch die seltsamsten... Dinge
//
// by Maris (Maraxxus@gmx.de)

if (!isset($session)) exit();
$session[user][specialinc]="kudzu.php";
if ($_GET['op']=="")
{

	output("`2Auf deinem Weg durch den Wald entdeckst du auf einmal neben dir einen gro�en Strauch, der dir seltsam bekannt vorkommt.`nK�nnte dies nicht etwa einer der seltenen Macadamia-Str�ucher sein, an dem die begehrten N�sse wachsen?`nUnsicher und neugierig wirfst du einen n�heren Blick auf den Strauch und erkennst, dass er offensichtlich keine N�sse tr�gt. Warscheinlich hat ihn schon ein anderer abgeerntet, allerdings ist die Chance gro�, dass derjenige nur die N�sse gepfl�ckt hat, die er sehen konnte.`nPl�tzlich beginnt es in dem Strauch leicht zu rascheln.`nWas tust du?`n");
	addnav("Tief hineingreifen","forest.php?op=take");
	addnav("Weitergehen","forest.php?op=leave");

}
elseif ($_GET['op']=="take")
{
	output("`2Du beugst dich vor und streckst deine Hand tief in den Strauch.`n`n");

	$chance = e_rand(1,5);
	switch ($chance)
	{
		// N�sse
		case 1:
			output("`2Es gelingt dir eine kleine handvoll Macadamia-N�sse zu pfl�cken!`n");
			$res = item_tpl_list_get( 'tpl_name="Macadamia-N�sse" LIMIT 1' );
			if( db_num_rows($res) )
			{
				$itemnew = db_fetch_assoc($res);
				item_add( $session[user][acctid], 0, $itemnew);
			}
			addnav("Weitergehen","forest.php?op=leave");
			break;
			// Kratzer
		case 2:
			output("`2Dabei st�sst du gegen etwas weiches, pelziges. Und bevor du dich versiehst hat es dich auch schon kr�ftig in die Hand gebissen!`nDas tat weh, du solltest schnell zum Heiler bevor es sich entz�ndet!`n");
			$session['user']['hitpoints']-=$session['user']['level']*9;
			if ($session['user']['hitpoints']<1)
			{
				$session['user']['hitpoints']=1;
			}
			addnav("Weitergehen","forest.php?op=leave");
			break;
			// Nichts
		case 3:
			output("`2Wie schade! Dieser Strauch wurde restlos gepl�ndert. Vielleicht hast du n�chstes Mal mehr Gl�ck!`n");
			addnav("Weitergehen","forest.php?op=leave");
			break;
			// Spielerfalle ;)
		case 4:
		case 5:
			$victim=getsetting("kudzu","0");
			if ($victim=="0")
			{
				$amount=0;
			}
			else
			{
				$sql = "SELECT name,sex FROM accounts WHERE acctid=".$victim;
				$result = db_query($sql) or die(db_error(LINK));
				$amount = db_num_rows($result);
			}
			if ($victim=="0" || $amount<1)
			{
				output("`2Oh je! Irgendetwas packt dich an der Hand und zieht dich in die Str�ucher!`nNoch bevor deine F��e im lockeren Waldboden Halt finden k�nnen verlierst du das Gleichgewicht und f�llst vorn�ber ins Dickicht!`n`n`4Du bist nun im Geb�sch gefangen und kannst dich kein St�ck r�hren!`nDornen zerkratzen dein Gesicht und deine Haut!`nDu wirst hier wohl warten m�ssen, bis dir jemand zu Hilfe kommt.`n`n`2Aber da die G�tter es gut mit dir meinen gew�hren sie dir eine Reise in die Zukunft!`nDu kannst also weiterspielen.`nVergiss aber nicht, dass du eigentlich immer noch in den Str�uchern liegst und auf Hilfe wartest!`n");
				addnews($session['user']['name'].'`2 st�rzte im Wald in ein Geb�sch und hofft nun auf Rettung.');
				savesetting("kudzu",$session[user][acctid]);
				addnav("Wenigsten das...","forest.php?op=leave");
			}
			else
			{
				output("`2Dabei bekommst du etwas zu fassen, was sich wie eine Hand anf�hlt! Sofort packt diese fest zu. Voller Schrecken verkrampfst du dich und du rei�t deinen Arm zur�ck, wodurch du fest an der fremden Hand ziehst.`nDu bist dabei einen noch lebenden K�rper aus dem Geb�sch zu ziehen, es handelt sich dabei um ");
				if ($victim==$session['user']['acctid'])
				{
					output("`@dich selbst??`n`n`2Vollkommen verst�rt l�sst du wieder los und schw�rst dir k�nftig nicht mehr soviel Ale zu trinken.`n");
					addnav("Weitergehen","forest.php?op=leave");
				}
				else
				{
					$row = db_fetch_assoc($result);
					output("`@".$row['name']."`2. Wie ".($row['sex']?'sie':'er')." in diese missliche Lage geraten ist bleibt dir ein R�tsel, aber ohne deine Hilfe wird ".($row['sex']?'sie':'er')." es nicht allein dort heraus schaffen.`n".$row['name']."`2 muss schon eine ganze Weile in diesem Gestr�pp ausgeharrt haben und schaut dich mit hoffnungsvollen Augen an.`nWas willst du tun?");
					addnav(($row['sex']?'Sie':'Ihn')." herausziehen","forest.php?op=rescue&who=".$victim);
					addnav(($row['sex']?'Sie':'Ihn')." noch tiefer reinstossen","forest.php?op=push&who=".$victim);
				}

			}
			break;
	}
	// Spieler retten
}
elseif ($_GET['op']=="rescue")
{
	$who=$_GET['who'];
	$victim=getsetting("kudzu","0");
	$sql = "SELECT name,sex,acctid FROM accounts WHERE acctid=".$who;
	$result = db_query($sql) or die(db_error(LINK));
	$amount = db_num_rows($result);
	if ($amount>0 && $who==$victim)
	{
		$row = db_fetch_assoc($result);
		output("`2Du ziehst nach Leibeskr�ften und schaffst es ".$row['name']."`2 aus dem Geb�sch zu retten!`nDaf�r wird ".($row['sex']?'sie':'er')." sich sicherlich noch sehr dankbar erweisen!`n`nF�r die noble Tat erh�lst du einen Charmepunkt!`n");
		$session['user']['charm']++;
		systemmail($row['acctid'],"`@Du wurdest gerettet!`0","`2{$session['user']['name']}`2 hat dich hilflos in den Str�uchern im Wald liegend entdeckt und dich herausgezogen! Du solltest dich daf�r bedanken!");
		savesetting("kudzu","0");
		addnews($session['user']['name'].'`2 hat '.$row['name'].'`2 im Wald aus einem Geb�sch gezogen.');
	}
	else
	{
		output("`2Du ziehst nach Leibeskr�ften, aber scheinbar warst du einer Sinnest�uschung unterlegen.`nDa ist gar niemand in dem Geb�sch!`n");
	}
	addnav("Weitergehen","forest.php?op=leave");
	// Spieler hineinschubsen
}
elseif ($_GET['op']=="push")
{
	$who=$_GET['who'];
	$victim=getsetting("kudzu","0");
	$sql = "SELECT name,sex,acctid FROM accounts WHERE acctid=".$who;
	$result = db_query($sql) or die(db_error(LINK));
	$amount = db_num_rows($result);
	if ($amount>0 && $who==$victim)
	{
		$row = db_fetch_assoc($result);
		output("`2Du grinst ".$row['name']."`2 verschlagen an und st�sst ".($row['sex']?'sie':'ihn')." mit Schwung zur�ck in die Str�ucher!`nSo ein".($row['sex']?'e':'en')." rettest du doch nicht, wo kommen wir denn dahin?`n".($row['sex']?'Ihr':'Sein')." fluchen und schimpfen kannst du noch eine ganze Weile aus dem Geb�sch h�ren, w�hrend du sich in deinem Gesicht ein zufriedenes L�cheln abzeichnet.`n");
		systemmail($row['acctid'],"`2Gemeinheit!`0","`2{$session['user']['name']}`2 hat dich hilflos in den Str�uchern im Wald liegend entdeckt, aber sich geweigert dir zu helfen und dich stattdessen nur noch tiefer hineingestossen!");
	}
	else
	{
		output("`2Du legst schon dein fieses Sonntagsgrinsen auf, aber scheinbar warst du einer Sinnest�uschung unterlegen.`nDa ist gar niemand in dem Geb�sch!`n");
	}
	addnav("Weitergehen","forest.php?op=leave");
	// Weitergehen
}
elseif ($_GET['op']=="leave")
{
	output("`2Du wendest dich von diesem Strauch ab und gehst weiter deines Weges.`n`n ");
	$session['user']['specialinc']="";
}
?>
