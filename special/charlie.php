<?php
//charlie.php - eine Schrecksekunde f�r unsere B-Tasten-Dr�cker
//Autor: Salator (salator@gmx.de)
//Date: 18.7.06

$servername = '`r'.getsetting('server_name','Charly').'`0';

if ($_GET['op']=='')
{
	output('`@Du hast den Gegner '.$servername.'`@ entdeckt, der sich mit seiner Waffe `%riesige Datenbank`@ auf dich st�rzt!`n`n');
	output('`2Level: `6'.($session['user']['level']+1).'`n
		`2`bBeginn der Runde:`b`n'
	.$servername.'`2\'s Lebenspunkte: `6'.$session['user']['maxhitpoints'].'`n
		`2DEINE Lebenspunkte: `6'.$session['user']['hitpoints'].'`n`n
		`$`bDein K�nnen erlaubt dir den ersten Angriff!`b`n`n');
	addnav('K�mpfen','forest.php?op=fight');
	addnav('Wegrennen','forest.php?op=leave');
	addnav('AutoFight');
	addnav('5 Runden k�mpfen','forest.php?op=fight&count=5');
	addnav('Bis zum bitteren Ende','forest.php?op=fight&count=100');
	addnav('besondere F�higkeiten');
	$session['user']['specialinc'] = 'charlie.php';
}
if ($_GET['op']=='fight')
{
	$session['user']['specialinc'] = '';
	headoutput('`b`c`$Niederlage!`c`b`0`n`n`&Du wurdest von `%'.$servername.'`& niedergemetzelt!!!`n
		`4Dein ganzes Gold wurde dir abgenommen!`n
		`410% deiner Erfahrung hast du verloren!`n
		`&Durch deine d�mmliche Entscheidung `%'.$servername.'`& anzugreifen, hast du den Spielserver f�r `$etwa 2 Realtage `&lahmgelegt.
		`n`n`$`b`cBitte logge dich jetzt aus.`c`b`n<hr>');
	output('`@Du hast den Gegner `^'.$servername.'`@ entdeckt, der sich mit seiner Waffe `%riesige Datenbank`@ auf dich st�rzt!`n`n');
	output('`2Level: `6'.($session[user][level]+1).'`n
		`2`bBeginn der Runde:`b`n'
	.$servername.'`2\'s Lebenspunkte: `6'.$session['user']['maxhitpoints'].'`n
		`2DEINE Lebenspunkte: `6'.$session['user']['hitpoints'].'`n`n');
	if($_GET['count'])
	{
		output('`4Du triffst `^'.$servername.'`4 mit `^'.ceil($session['user']['hitpoints']/4).'`4 Schadenspunkten!`n
		`^'.$servername.'`4 trifft dich mit `$'.ceil($session['user']['maxhitpoints']/4).'`4 Schadenspunkten!`n`n');
		output('`2N�chste Runde:`n
		`4Du triffst `^'.$servername.'`4 mit `^'.ceil($session['user']['hitpoints']/5).'`4 Schadenspunkten!`n
		`^'.$servername.'`4 trifft dich mit `$'.ceil($session['user']['maxhitpoints']/3).'`4 Schadenspunkten!`n`n');
		output('`2N�chste Runde:`n
		`4Du triffst `^'.$servername.'`4 mit `^'.ceil($session['user']['hitpoints']/6).'`4 Schadenspunkten!`n
		`^'.$servername.'`4 trifft dich mit `$'.ceil($session['user']['maxhitpoints']/2).'`4 Schadenspunkten!`n');
	}
	else
	{
		output('`4Du versuchst `^'.$servername.'`4 zu treffen aber der `$ABWEHRSCHLAG`4 trifft dich mit `^'.ceil($session['user']['hitpoints']/2).'`4 Schadenspunkten!`n
		`^'.$servername.'`4 trifft dich mit `$'.ceil($session['user']['maxhitpoints']/2).'`4 Schadenspunkten!`n`n');
	}

	addnews('`lBeim Versuch, ein mysteri�ses, sehr verf�hrerisches aber vor allem unheimlich starkes Wesen namens '.$servername.'`l zu t�ten,
  			widerfuhr `&'.$session['user']['login'].'`l ein seltsames Schicksal..');

	addnav('Logout','login.php?op=logout');
}
if ($_GET['op']=='leave')
{
	$session['user']['specialinc'] = '';
	output('`2Du h�ltst es f�r besser, '.$servername.' nicht zu attackieren. Eine weise Entscheidung.
		`n'.$servername.' dankt es dir mit `#einem Edelstein`2.');
	$session['user']['gems']++;
}
?>