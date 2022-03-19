<?php

// 15082004

require_once 'common.php';
page_header('�ber Legend of the Green Dragon (Dragonslayer Edition)');

checkday();

if ($_GET['op']=='')
{

	/* NOTICE
	* NOTICE This section may not be modified, please modify the Server Specific section above.
	* NOTICE
	*/
	output(get_extended_text('about_lotgd'),true);

	addnav('Spieleinstellungen','about.php?op=setup');
	addnav('Modifikationen','about.php?op=modifications');
	addnav('GNU GPL','about.php?op=gpl');
	addnav('Impressum','about.php?op=impressum');
}
elseif($_GET['op']=='setup')
{
	//$time = (strtotime(date('1981-m-d H:i:s',strtotime(date('r').'-'.getsetting('gameoffsetseconds',0).' seconds'))))*getsetting('daysperday',4) % strtotime('1981-01-01 00:00:00');
	$time = gametime();

	// by Moonraven
	$tomorrow = mktime(0,0,0,date('m',$time),date('d',$time)+1,date('Y',$time));
	$today = mktime(0,0,0,date('m',$time),date('d',$time),date('Y',$time));
	$dayduration = ($tomorrow-$today) / getsetting('daysperday',4);
	$secstotomorrow = $tomorrow-$time;
	$secssofartoday = $time - $today;
	$realsecstotomorrow = round($secstotomorrow / getsetting('daysperday',4),0);
	$realsecssofartoday = round($secssofartoday / getsetting('daysperday',4),0);

	addnav('�ber LoGD','about.php');
	addnav('Modifikationen','about.php?op=modifications');
	$setup = array(
	'Spieleinstellungen,title',
	'pvp'=>'Spieler gegen Spieler erlaubt,viewonly',
	'pvpday'=>'Erlaubte Anzahl Spielerk�mpfe pro Tag,viewonly',
	'pvpimmunity'=>'Tage die Spieler sicher vor PvP sind,viewonly',
	'pvpminexp'=>'N�tige Erfahrungspunkte bevor ein Spieler im PvP angreifbar wird,viewonly',
	'soap'=>'Spielerbeitr�ge "bereinigen" (Wortfilter),viewonly',
	'newplayerstartgold'=>'Startmenge an Gold f�r neue Charaktere,viewonly',
	'avatare'=>'Avatare erlaubt?,viewonly',
	'maxonline'=>'Maximal gleichzeitig online (0 f�r unbegrenzt),viewonly',

	'Neue Tage,title',
	'fightsforinterest'=>'Um Zinsen zu bekommen muss ein Spieler weniger Waldk�mpfe haben als,viewonly',
	'maxinterest'=>'Maximaler Zinssatz (%),viewonly',
	'mininterest'=>'Minimaler Zinssatz (%),viewonly',
	'daysperday'=>'Spieltage pro Kalendertag,viewonly',
	'specialtybonus'=>'Extras des Spezialgebiets t�glich einsetzen,viewonly',

	'Handelseinstellungen,title',
	'borrowperlevel'=>'Maximum das ein Spieler pro Level leihen kann,viewonly',
	'transferperlevel'=>'Maximum das ein Spieler pro Level des Empf�ngers �berweisen kann,viewonly',
	'mintransferlev'=>'Mindestlevel f�r �berweisungen,viewonly',
	'transferreceive'=>'�berweisungen die ein Spieler pro Tag empfangen darf,viewonly',
	'maxtransferout'=>'Absolutes Maximum das ein Spieler pro Tag und Level �berweisen darf,viewonly',

	'Kopfgeld,title',
	'bountymin'=>'Mindestbetrag pro Level der Zielperson,viewonly',
	'bountymax'=>'Maximalbetrag pro Level der Zielperson,viewonly',
	'bountylevel'=>'Mindestlevel um Opfer sein zu k�nnen,viewonly',
	'bountyfee'=>'Geb�hr f�r Dag Durnick in Prozent,viewonly',
	'maxbounties'=>'Anzahl an Kopfgeldern die ein Spieler pro Tag aussetzen darf,viewonly',

	'Wald,title',
	'turns'=>'Waldk�mpfe (Z�ge) pro Tag,viewonly',
	'dropmingold'=>'Waldbewohner lassen wenigstens 1/4 des m�glichen Golds fallen,viewonly',
	'lowslumlevel'=>'Mindestlevel zum Herumstreifen,viewonly',

	'Mail Einstellungen,title',
	'mailsizelimit'=>'Maximale Nachrichtengr��e,viewonly',
	'inboxlimit'=>'Maximale Anzahl an Nachrichten in der Inbox,viewonly',
	'oldmail'=>'Alte Nachrichten werden automatisch gel�scht nach Tagen,viewonly',

	'Inhaltsverfallsdatum (0 f�r keines),title',
	'expirecontent'=>'Tage die Kommentare und Neuigkeiten aufgehoben werden,viewonly',
	'expiretrashacct'=>'Accounts die sich nie eingeloggt haben werden nach x Tagen gel�scht. x =,viewonly',
	'expirenewacct'=>'Level 1 Charaktere ohne Drachenkill werden nach x Tagen gel�scht. x =,viewonly',
	'expireoldacct'=>'Alle anderen Accounts werden nach x Tagen Inaktivit�t gel�scht. x =,viewonly',
	'LOGINTIMEOUT'=>'Sekunden Inaktivit�t bis zum automatischen Logout,viewonly',

	'N�tzliche Infos,title',
	'Tagesl�nge: '.round(($dayduration/60/60),0).' Stunden,viewonly',
	'aktuelle Serveruhrzeit: '.date('Y-m-d h:i:s a').',viewonly',
	'Letzter neuer Tag: '.date('h:i:s a',strtotime(date('r').'-'.$realsecssofartoday.' seconds')).',viewonly',
	'aktuelle Spielzeit: '.getgametime().',viewonly',
	'N�chster neuer Tag: '.date('h:i:s a',strtotime(date('r').'+'.$realsecstotomorrow.' seconds')).' ('.date('H\\h i\\m s\\s',strtotime('1980-01-01 00:00:00 + '.$realsecstotomorrow.' seconds')).'),viewonly',
	'weather'=>'Heutiges Wetter:,viewonly'
	);

	output("`@<h3>Einstellungen f�r diesen Server</h3>`2LoGD 0.9.7+jt komplett auf deutsch, mit Sound und vielen Extras!`n`n",true);
	showform($setup,$settings,true);

}
elseif($_GET['op']=='modifications')
{
	addnav('�ber LoGD','about.php');
	addnav('Spieleinstellungen','about.php?op=setup');

	output(get_extended_text('game_modifications'),true);
}
elseif($_GET['op']=='gpl')
{
	output(get_extended_text('GPL'));
	addnav('�ber LoGD','about.php');
}
elseif($_GET['op']=='impressum')
{
	output(get_extended_text('impressum'),true);
	addnav('�ber LoGD','about.php');
}
if ($session['user']['loggedin'])
{
	addnav('Zur�ck zu den News','news.php');
}
else
{
	addnav('Login','index.php');
}
page_footer();
?>