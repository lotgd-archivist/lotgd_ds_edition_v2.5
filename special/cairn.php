<?php
// Das Grab
// Hier lassen sich die Idole finden.
// Sollte sich ein Idol bereits im Besitz eines Spielers befinden, so ist das Grab leer.
// Pl�nderer werden u.U. bestraft
//
// by Maris (Maraxxus@gmx.de)

if (!isset($session))
{
	exit();
}
$session['user']['specialinc']='cairn.php';

if ($_GET['op']=='')
{
	output('`7Du bemerkst abseits des Weges eines manngro�en, mit Moos bewachsenen Stein.`nAls du einen weiteren fl�chtigen Blick darauf wirfst, entdeckst du kleine Gravuren auf dem Stein.`nNeugierig trittst du n�her, um dir das ganze etwas genauer anzuschauen.`nDann erkennst du, dass es sich hierbei um ein altes, halb �berwuchertes Grab handelt.`n');

	//Typ bestimmen
	$typ=e_rand(1,5);
	switch($typ)
	{
		case 1:
			$name='`^Waldl�ufers';
			break;
		case 2:
			$name='`!Genies';
			break;
		case 3:
			$name='`4Kriegers';
			break;
		case 4:
			$name='`2Anglers';
			break;
		case 5:
			$name='`&Totenbeschw�rers';
			break;
	}

	output('Die sterblichen �berreste eines '.$name.' `7scheinen hier zur Ruhe gelegt worden zu sein, wie du es nach genauerer Pr�fung der Gravuren und Grabbeigaben vermuten kannst.');
	output('`n`nWas gedenkst du zu tun?`n`n');
	addnav('Das Grab �ffnen','forest.php?op=open&typ='.$typ);
	if ($session['user']['turns'] > 2)
	{
		addnav('Beten (3 Waldk�mpfe)','forest.php?op=pray&typ='.$typ);
	}
	addnav('Weitergehen','forest.php?op=leave');

}
elseif ($_GET['op']=='pray')
{
	$typ=$_GET['typ'];
	$session['user']['turns']-=3;
	output('`7Du kniest vor dem Grab nieder und widmest der Seele des Toten eine Reihe von Gebeten, die du von irgendwoher kennst. Dabei merkst du gar nicht wie die Zeit vergeht.`n`n');

	$chance = e_rand(1,2);
	switch ($chance)
	{
		// Umsonst - Ansehen zum Trost
		case 1:
			output('`7Du erhebst dich nachdem du fertig mit beten bist und ziehst weiter deines Weges.`nEin zuf�lliger Wanderer, der dich dabei beobachtet hat, erz�hlt voller Verwunderung wilde Geschichten �ber deine Fr�mmigkeit im Dorf.`nDadurch steigt dein Ansehen bei den B�rgern um 10 Punkte!`n');
			$session['user']['reputation']+=10;
			$session['user']['specialinc']='';
			addnews($session['user']['name'].'`7 wurde beim frommen Gebet an einem Grab im Wald beobachtet.');
			break;
			// Chance auf ein Idol
		case 2:
			output('`7Nachdem du deine Gebete zuende gef�hrt hast f�llt pl�tzlich ein heller Lichtstrahl direkt vom Himmel auf den Stein!`nAlsbald beginnt die Erde unter deinen F��en leicht wackeln und sich zu bewegen, so als w�rde sich etwas seinen Weg zu dir nach oben graben.`nWas nun?');
			addnav('In der Erde graben','forest.php?op=search&typ='.$typ);
			addnav('Abwarten','forest.php?op=wait&typ='.$typ);
			addnav('Schnell verschwinden','forest.php?op=leave');
			break;
	}
	// Weglaufen
}
elseif ($_GET['op']=='leave')
{
	output('`7Dir ist das alles hier langsam etwas zu unheimlich geworden, und so siehst du zu, dass du schnell davon kommst.`n`n ');
	$session['user']['specialinc']='';
	// Pl�nderer werden evtl. bestraft
}
elseif ($_GET['op']=='open')
{
	$typ=$_GET['typ'];
	$session['user']['turns']--;
	output('`7Du streckst deine H�nde in den lockeren Waldboden und gr�bst nach Reicht�mern.`n');
	switch (e_rand(1,3)) {
		case 1:
		case 2:
			output('`7Doch das, was du herausziehst, ist sicherlich etwas anderes, als das, was du zu finden erwartet hast.`n');
			output('Ein furchterregender Gruftschrecken steigt aus dem Grab heraus um dich f�r seine Sch�ndung zu bestrafen!`n`n');
			$badguy = array(
			'creaturename'=>'`7Gruftschrecken`0',
			'creaturelevel'=>$session['user']['level']+2,
			'creatureweapon'=>'Eiskalte Ber�hrung',
			'creatureattack'=>$session['user']['attack']+2,
			'creaturedefense'=>$session['user']['defence']+2,
			'creaturehealth'=>round($session['user']['maxhitpoints']*1.25,0),
			'diddamage'=>0);
			$session['user']['badguy']=createstring($badguy);
			$_GET['op']='fight';
			break;
		case 3:
			output('`7Kurz darauf beginnt der Boden leicht zu wackeln, so als w�rde sich etwas seinen Weg zu dir nach oben graben!`n');
			addnav('Weitergraben','forest.php?op=search&typ='.$typ);
			addnav('Abwarten','forest.php?op=wait&typ='.$typ);
			addnav('Schnell verschwinden','forest.php?op=leave');
			break;
	}
	// Abwarten
}
elseif ($_GET['op']=='wait')
{
	output('`7Du wartest ein Weile, doch es geschieht weiter nichts.`nAuch das wackeln und beben legt sich wieder.`n');
	$typ=$_GET['typ'];
	addnav('Weitergraben','forest.php?op=open&typ='.$typ);
	addnav('Lieber verschwinden','forest.php?op=leave');
}
elseif ($_GET['op']=='search')
{
	$typ=$_GET['typ'];
	switch($typ)
	{
		case 1:
			$name='`^Idol des Waldl�ufers';
			$id='idolrnds';
			break;
		case 2:
			$name='`!Idol des Genies';
			$id='idolgnie';
			break;
		case 3:
			$name='`4Idol des Kriegers';
			$id='idolkmpf';
			break;
		case 4:
			$name='`2Idol des Anglers';
			$id='idolfish';
			break;
		case 5:
			$name='`&Idol des Totenbeschw�rers';
			$id='idoldead';
			break;
	}

	$sql = "SELECT ac.name AS name FROM items it LEFT JOIN accounts ac ON it.owner=ac.acctid WHERE it.tpl_id='$id'";
	$result = db_query($sql);
	$amount=db_num_rows($result);

	if ($amount>0)
	{
		$rown = db_fetch_assoc($result);
		output('`7Es scheint, als sei alles vergebens gewesen.`nDieses Grab wurde wohl bereits gepl�ndert. Da scheint jemand schneller gewesen zu sein als du!`n`nDer Wind fl�stert dir einen Namen zu: '.$rown['name']);
	}
	else
	{
		output('`7Du bekommst etwas festes zu fassen und ziehst es aus dem Boden heraus.`n`nDU HAST SOEBEN DAS '.$name.'`7 ERLANGT!');
		$res = item_tpl_list_get( "tpl_id='$id' LIMIT 1" );
		if( db_num_rows($res) )
		{
			$itemnew = db_fetch_assoc($res);
			item_add( $session['user']['acctid'], 0, $itemnew);
			addnews($session['user']['name'].'`7 hat heute das '.$name.'`7 gefunden!');
		}
	}
	$session['user']['specialinc']='';
}
if ($_GET['op']=='fight')
{
	$battle=true;
}
if ($battle)
{
	include('battle.php');
	if ($victory)
	{
		$badguy=array();
		$session['user']['badguy']='';
		output('`n`7Du hast den Gruftschrecken bezwungen und kannst dich nun nach herzenslust der Pl�nderung des Grabes hingeben.`n`n');
		$exp_gain=($session['user']['level']+1)*15;
		output("`7Du bekommst $exp_gain Erfahrungspunkte und findest `@2 Edelsteine und 2500 Gold`7.`n`n");
		$session['user']['experience']+=$exp_gain;
		$session['user']['gems']+=2;
		$session['user']['gold']+=2500;
		$session['user']['specialinc']='';
	}
	elseif ($defeat)
	{
		$badguy=array();
		$session['user']['badguy']='';
		output('`n`7Der Gruftschrecken saugt dir das Leben aus und bannt dich zur Strafe in das Totenreich.`n');
		output('`n`7Au�erdem nimmt er dir `4einen permanenten Lebenspunkt`7!`n');
		$session['user']['maxhitpoints']--;
		addnews('`7Die Grabsch�nderkarriere von '.$session['user']['name'].'`7 nahm heute ein j�hes Ende.');
		addnav('Na dann','shades.php');
	}
	else
	{
		fightnav(true,false);
	}
}
?>
