<?php
// Gestrüpp... hier findet man auch die seltsamsten... Dinge
//
// by Maris (Maraxxus@gmx.de)

if (!isset($session)) exit();
$session[user][specialinc]="kudzu.php";
if ($_GET['op']=="")
{

	output("`2Auf deinem Weg durch den Wald entdeckst du auf einmal neben dir einen großen Strauch, der dir seltsam bekannt vorkommt.`nKönnte dies nicht etwa einer der seltenen Macadamia-Sträucher sein, an dem die begehrten Nüsse wachsen?`nUnsicher und neugierig wirfst du einen näheren Blick auf den Strauch und erkennst, dass er offensichtlich keine Nüsse trägt. Warscheinlich hat ihn schon ein anderer abgeerntet, allerdings ist die Chance groß, dass derjenige nur die Nüsse gepflückt hat, die er sehen konnte.`nPlötzlich beginnt es in dem Strauch leicht zu rascheln.`nWas tust du?`n");
	addnav("Tief hineingreifen","forest.php?op=take");
	addnav("Weitergehen","forest.php?op=leave");

}
elseif ($_GET['op']=="take")
{
	output("`2Du beugst dich vor und streckst deine Hand tief in den Strauch.`n`n");

	$chance = e_rand(1,5);
	switch ($chance)
	{
		// Nüsse
		case 1:
			output("`2Es gelingt dir eine kleine handvoll Macadamia-Nüsse zu pflücken!`n");
			$res = item_tpl_list_get( 'tpl_name="Macadamia-Nüsse" LIMIT 1' );
			if( db_num_rows($res) )
			{
				$itemnew = db_fetch_assoc($res);
				item_add( $session[user][acctid], 0, $itemnew);
			}
			addnav("Weitergehen","forest.php?op=leave");
			break;
			// Kratzer
		case 2:
			output("`2Dabei stösst du gegen etwas weiches, pelziges. Und bevor du dich versiehst hat es dich auch schon kräftig in die Hand gebissen!`nDas tat weh, du solltest schnell zum Heiler bevor es sich entzündet!`n");
			$session['user']['hitpoints']-=$session['user']['level']*9;
			if ($session['user']['hitpoints']<1)
			{
				$session['user']['hitpoints']=1;
			}
			addnav("Weitergehen","forest.php?op=leave");
			break;
			// Nichts
		case 3:
			output("`2Wie schade! Dieser Strauch wurde restlos geplündert. Vielleicht hast du nächstes Mal mehr Glück!`n");
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
				output("`2Oh je! Irgendetwas packt dich an der Hand und zieht dich in die Sträucher!`nNoch bevor deine Füße im lockeren Waldboden Halt finden können verlierst du das Gleichgewicht und fällst vornüber ins Dickicht!`n`n`4Du bist nun im Gebüsch gefangen und kannst dich kein Stück rühren!`nDornen zerkratzen dein Gesicht und deine Haut!`nDu wirst hier wohl warten müssen, bis dir jemand zu Hilfe kommt.`n`n`2Aber da die Götter es gut mit dir meinen gewähren sie dir eine Reise in die Zukunft!`nDu kannst also weiterspielen.`nVergiss aber nicht, dass du eigentlich immer noch in den Sträuchern liegst und auf Hilfe wartest!`n");
				addnews($session['user']['name'].'`2 stürzte im Wald in ein Gebüsch und hofft nun auf Rettung.');
				savesetting("kudzu",$session[user][acctid]);
				addnav("Wenigsten das...","forest.php?op=leave");
			}
			else
			{
				output("`2Dabei bekommst du etwas zu fassen, was sich wie eine Hand anfühlt! Sofort packt diese fest zu. Voller Schrecken verkrampfst du dich und du reißt deinen Arm zurück, wodurch du fest an der fremden Hand ziehst.`nDu bist dabei einen noch lebenden Körper aus dem Gebüsch zu ziehen, es handelt sich dabei um ");
				if ($victim==$session['user']['acctid'])
				{
					output("`@dich selbst??`n`n`2Vollkommen verstört lässt du wieder los und schwörst dir künftig nicht mehr soviel Ale zu trinken.`n");
					addnav("Weitergehen","forest.php?op=leave");
				}
				else
				{
					$row = db_fetch_assoc($result);
					output("`@".$row['name']."`2. Wie ".($row['sex']?'sie':'er')." in diese missliche Lage geraten ist bleibt dir ein Rätsel, aber ohne deine Hilfe wird ".($row['sex']?'sie':'er')." es nicht allein dort heraus schaffen.`n".$row['name']."`2 muss schon eine ganze Weile in diesem Gestrüpp ausgeharrt haben und schaut dich mit hoffnungsvollen Augen an.`nWas willst du tun?");
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
		output("`2Du ziehst nach Leibeskräften und schaffst es ".$row['name']."`2 aus dem Gebüsch zu retten!`nDafür wird ".($row['sex']?'sie':'er')." sich sicherlich noch sehr dankbar erweisen!`n`nFür die noble Tat erhälst du einen Charmepunkt!`n");
		$session['user']['charm']++;
		systemmail($row['acctid'],"`@Du wurdest gerettet!`0","`2{$session['user']['name']}`2 hat dich hilflos in den Sträuchern im Wald liegend entdeckt und dich herausgezogen! Du solltest dich dafür bedanken!");
		savesetting("kudzu","0");
		addnews($session['user']['name'].'`2 hat '.$row['name'].'`2 im Wald aus einem Gebüsch gezogen.');
	}
	else
	{
		output("`2Du ziehst nach Leibeskräften, aber scheinbar warst du einer Sinnestäuschung unterlegen.`nDa ist gar niemand in dem Gebüsch!`n");
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
		output("`2Du grinst ".$row['name']."`2 verschlagen an und stösst ".($row['sex']?'sie':'ihn')." mit Schwung zurück in die Sträucher!`nSo ein".($row['sex']?'e':'en')." rettest du doch nicht, wo kommen wir denn dahin?`n".($row['sex']?'Ihr':'Sein')." fluchen und schimpfen kannst du noch eine ganze Weile aus dem Gebüsch hören, während du sich in deinem Gesicht ein zufriedenes Lächeln abzeichnet.`n");
		systemmail($row['acctid'],"`2Gemeinheit!`0","`2{$session['user']['name']}`2 hat dich hilflos in den Sträuchern im Wald liegend entdeckt, aber sich geweigert dir zu helfen und dich stattdessen nur noch tiefer hineingestossen!");
	}
	else
	{
		output("`2Du legst schon dein fieses Sonntagsgrinsen auf, aber scheinbar warst du einer Sinnestäuschung unterlegen.`nDa ist gar niemand in dem Gebüsch!`n");
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
