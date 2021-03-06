<?php
/**
 * su_configuration.php: Verwaltung der Spieleinstellungen
 * @author LOGD-Core / Drachenserver-Team
 * @version DS-E V/2
*/

require_once 'common.php';

su_check(SU_RIGHT_GAMEOPTIONS,true);

loadsettings();

output('`c`&`bSpieleinstellungen`b`c`n`n');

/**
 * Settings have to be saved
 */
if ($_GET['op']=='save')
{
    if ($_POST['blockdupeemail']==1) {$_POST['requirevalidemail']=1;}
    if ($_POST['requirevalidemail']==1) {$_POST['requireemail']=1;}

    output('?bernehme ?nderungen...`n`n');

    $str_log = '';

    $arr_back = unserialize(urldecode(stripslashes($_POST['settings_back'])));
    unset($_POST['settings_back']);

    reset($_POST);
	foreach ($_POST as $key=>$val)
	{

		// Wenn jemand versucht, zu schummeln ; )
		if($key == 'sugroups') {
			su_check(SU_RIGHT_RIGHTS,true);
			continue;
		}

		$val = stripslashes($val);
		if($arr_back[$key] != $val) {

			// F?rst ?ndern
			if($key == 'townname') {
				$str_oldtitle = 'F?rst von '.addslashes($settings[$key]);
				$str_newtitle = '`&F?rst von '.addslashes($val);
				$sql = 'SELECT acctid FROM account_extra_info WHERE ctitle LIKE "%'.$str_oldtitle.'%"';
				$arr_acc = db_fetch_assoc(db_query($sql));

				if(!empty($arr_acc['acctid'])) {
					$sql = 'UPDATE account_extra_info SET ctitle = "'.$str_newtitle.'" WHERE acctid='.$arr_acc['acctid'];
					db_query($sql);

					user_set_name($arr_acc['acctid']);
				}
			}

	        savesetting($key,$val);
	        $str_log .= $key.' => "'.$val.'"; ';
		}
    }

    if(!empty($str_log)) {
    	systemlog('`3Ver?nderte Spieleinstellungen:`n'.$str_log,$session['user']['acctid']);
    	output($str_log);
    }
    else {
    	output('Keine Ver?nderungen vorgenommen, nichts gespeichert!');
    }
    addnav('Zur?ck zu den Spieleinstellungen',basename(__FILE__));
}



page_header('Spieleinstellungen');
addnav('G?Zur?ck zur Grotte','superuser.php');
addnav('W?Zur?ck zum Weltlichen',$session['su_return']);
addnav('',$REQUEST_URI);


$time = (strtotime(date('1981-m-d H:i:s',strtotime(date('r').'-'.getsetting('gameoffsetseconds',0).' seconds'))))*getsetting('daysperday',4) % strtotime('1981-01-01 00:00:00');
$time = gametime();


$tomorrow = mktime(0,0,0,date('m',$time),date('d',$time)+1,date('Y',$time));
$today = mktime(0,0,0,date('m',$time),date('d',$time),date('Y',$time));
$dayduration = ($tomorrow-$today) / getsetting('daysperday',4);
$secstotomorrow = $tomorrow-$time;
$secssofartoday = $time - $today;
$realsecstotomorrow = round($secstotomorrow / getsetting('daysperday',4),0);
$realsecssofartoday = round($secssofartoday / getsetting('daysperday',4),0);
$enum='enum';

for ($i=0;$i<=86400;$i+=900)
{
    $enum.=",$i,".((int)($i/60/60)).":".($i/60 %60);
}

$weather_enum = 'radio';
foreach($weather as $id=>$w)
{
	$w['name'] = str_replace(',','',$w['name']);
	$weather_enum.=','.$id.','.$w['name'].'.';
}

$setup = array(

	'Servereinstellungen,title',
	'server_name'=>'Der Name des Servers|?Der logische Name des Servers. Wir ehren unsere liebe Dame Charly mit diesem Setting',
    'defaultlanguage'=>'Voreingestellte Sprache (z. Zt nur de),enum,en,English,dk,Danish,de,Deutsch,es,Espanol,fr,French',
    'locale'=>'Einstellung f?r lokal unterschiedl. Darstellungen (Zeit etc.; Keine = Server-Default),text,10|?Der Wert f?r explizit deutsche Darstellung lautet de_DE',
    'forum'=>'Link (URL) zum Forum',
    'gameadminemail'=>'Admin Email',
	'petitionemail'=>'Anfragen Email (Absender)',
	'paypalemail'=>'E-Mail Adresse f?r den PayPal Account des Admins',
	'LOGINTIMEOUT'=>'Sekunden Inaktivit?t bis zum automatischen Logout,int',

    'Spieleinstellungen,title',
	'wartung'=>'Wartungsmodus an,bool|?Um einzelne Accounts f?r den Wartungsmodus freizuschalten, kannst du die Rechtesektion im Usereditor verwenden.',
    'blocknewchar'=>'Neuanmeldungen sperren?,bool',
    'loginbanner'=>'Login Banner (unterhalb der Login-Aufforderung; 255 Zeichen)',
    'impressum'=>'Server betrieben von: (255 Zeichen)',
	'defaultskin'=>'Standardskin ( + .htm(l) )',
	'townname'=>'Name des Dorfes:',
	'teamname'=>'Bezeichnung des Administrationsteams:',
    'soap'=>'Userbeitr?ge s?ubern (filtert Gossensprache und trennt W?rter mit ?ber 45 Zeichen),bool',
    'maxonline'=>'Maximal gleichzeitig online (0 f?r unbegrenzt),int',
    'maxcolors'=>'Maximale # erlaubter Farbwechsel in Userkommentaren,int',
	'longbiomaxlength'=>'Maximale Zeichenanzahl d. longbio,int',
	'emailonmail'=>'Email-Benachrichtigung bei Brieftauben-Eingang,bool',
    'automaster'=>'Meister jagt s?umige Lehrlinge,bool',
    'multimaster'=>'Meister kann mehrmals pro Tag herausgefordert werden?,bool',
    'beta'=>'Beta-Features f?r alle Spieler aktivieren?,bool',
    'limithp'=>'Lebenpunkte maximal Level*12+5*DPinHP+x*DK (0=deaktiviert),int',
    'autofight'=>'Automatische Kampfrunden erm?glichen,bool',
    'witchvisits'=>'Erlaubte Besuche bei der Hexe,int',
    'symp_active'=>'Sympathiepunktesystem / F?rst aktiv,bool',
    'max_symp'=>'Vergebbare Sympathiepunkte pro Monat,int',
    'symp_per_acc'=>'Max. Anzahl an Symp.punkten die auf einen Chara verteilt werden k?nnen,int',
    'dailyspecial'=>'Heutiges besonderes Ereignis',
	'enable_commentemail'=>'User d?rfen Chatmitschnitte an ihre Mail senden,bool',
	'enable_modcall'=>'"Mod rufen"-Button unter Chats anbieten,bool',
	'ci_goldpresse'=>'Goldpresse aktiv,bool',

	'Die Schenke,title',
    'maxales'=>'Maximale Anzahl Ale die bei einer "Runde" spendiert werden kann,int',
    'paidales'=>'Ale das als "Runde" spendiert wurde (Wert-1),int',
    'dragonmind_game'=>'Dragonmind Spiel aktivieren,bool',
    'memory_game'=>'Memory Spiel aktivieren,bool',

    'Expedition,title',
    'DDL_new_order'=>'DDL-Lagenwechsel nach sp?testens x Tagen,int',
    'DDL_balance_malus'=>'DDL-Punkteabzug pro Tag,int',
    'DDL_balance_push'=>'DDL-Punkteschwelle um Lage?nderunge herbeizuf?hren,int',
    'DDL_balance_win'=>'DDL-Punkteschwelle damit Angriff gelingt,int',
    'DDL_balance_lose'=>'DDL-Negativpunkteschwelle zur Niederlage,int',
    'DDL-restart'=>'DDL-Lager nach x Tagen erneuern,int',
    'DDL_comments_req'=>'DDL-Anzahl der Posts in der Ein?de bis neue Gegner erscheinen,int',

    'B?ro des F?rsten,title',
	'fuerst'=>'F?rst,viewonly',
    'taxrate'=>'Derzeitiger Steuersatz,int',
    'mintaxes'=>'Mindeststeuersatz,int',
    'maxtaxes'=>'H?chstm?glicher Steuersatz,int',
    'taxprison'=>'Derzeitige Anzahl Kerkertage f?r Steuerhinterziehung,int',
    'maxprison'=>'H?chststrafe f?r Steuerhinterziehung,int',
    'callvendormax'=>'F?rst kann wie oft in seiner Amtszeit den Wanderh?ndler holen,int',
    'beggarmax'=>'Maximales Fassungsverm?gen des Bettelsteins,int',
    'maxbudget'=>'Maximale Gr??e der Staatskasse,int',
    'maxamtsgems'=>'Maximale Anzahl an Edelsteinen in den Tresoren,int',
    'lurevendor'=>'Kosten um den Wanderh?ndler anzulocken,int',
    'freeorkburg'=>'Kosten um die Orkburg freizulegen,int',

    'Account Erstellung,title',
    'newplayerstartgold'=>'Gold mit dem ein neuer Char startet,int',
    'requireemail'=>'E-Mail Adresse beim Anmelden verlangen,bool',
    'requirevalidemail'=>'E-Mail Adresse best?tigen lassen,bool',
    'blockdupeemail'=>'Nur ein Account pro E-Mail Adresse,bool',
    'spaceinname'=>'Erlaube Leerzeichen in Benutzernamen,bool',
    'specialkeys'=>'Erlaube Sonderzeichen in Benutzernamen,bool',
    'criticalchars'=>'Zeichen die nicht in Namen vorkommen d?rfen (regul?rer Ausdruck /[..Eingegebene Zeichen..]/)!),text,100',
    'allletter_up_allow'=>'Namen nur in Gro?buchstaben erlauben,bool',
    'firstletter_up'=>'Erster Buchstabe immer in Gro?schreibung,bool',
    'name_casechange'=>'?nderung der Gro?-/Kleinschreibung des Namens in J?gerh?tte erlauben,bool',
    'nameminlen'=>'Mindestl?nge f?r Login in Zeichen (Ohne Farbcodes)',
    'namemaxlen'=>'Maximall?nge f?r Login in Zeichen (Ohne Farbcodes)',
    'titleminlen'=>'Mindestl?nge f?r eigenen Titel in Zeichen (Ohne Farbcodes)',
    'titlemaxlen'=>'Maximall?nge f?r eigenen Titel in Zeichen (Mit Farbcodes)',
    'name_maxcolors'=>'Maximalanzahl an Farbcodes im Namen,int',
    'title_maxcolors'=>'Maximalanzahl an Farbcodes in eigenem Titel,int',
    'selfdelete'=>'Erlaube den Spielern ihren Charakter zu l?schen,bool',
    'avatare'=>'Erlaube den Spielern Avatare zu verlinken,bool',
    'refererdp'=>'DP f?r eine Anwerbung,int',
    'refererminlvl'=>'Mindestlvl f?r Anwerbungs-DP,int',
    'referermindk'=>'MindestDK f?r Anwerbungs-DP,int',
    'recoveryage'=>'Tage ab denen ein Spieler t?glich Extra-Erfahrung bekommt,int',
    'recoveryexp'=>'Anzahl der Extra-Erfahrungspunkte (*DKs) pro Tag,int',
    'cowardlevel'=>'Level den ein Spieler haben muss um Feigling zu werden,int',
	'cowardage'=>'Tageanzahl seit DK um Feigling zu werden,int',
	'maxagepvp'=>'Max Tageanzahl seit DK f?r PvP und Ruhmeshalle,int',
	'race_change_allowed'=>'Rassenwechsel in der Schenke erlauben,bool',
	'unaccepted_namechange'=>'Abgelehnte Namen werden ge?ndert zu -unzul?ssiger Name xxx-,bool',

	'Berufe,title',
    'numberofguards'=>'Maximale Zahl an Stadtwachen',
	'numberofpriests'=>'Maximale Zahl an Priestern',
	'numberofwitches'=>'Maximale Zahl an Hexen',
	'numberofjudges'=>'Maximale Zahl an Richtern',
	'guardreq'=>'N?tige DKs um Stadtwache zu werden',
	'judgereq'=>'N?tige DKs um Richter zu werden',
	'priestreq'=>'N?tige DKs um Priester / Hexer zu werden',
	'guard_max_imprison'=>'Max. Anzahl an Stadtwacheneinkerkerungen pro Spieltag und Wache',

	'Dorffest,title',
    'lastparty'=>'Wann war das letzte B?rgerfest',
    'min_party_level'=>'Wieviel Geld muss f?r eine Party vorhanden sein,int',
	'amtskasse'=>'Gold in der Amtskasse,int',
    'party_duration'=>'Wieviele Tage soll das Dorffest dauern (1;2;0.5;...),int',

    'Einstellungen f?r unsere Mods,title',
    'libdp'=>'Max. vergebbare Donationpoints pro angenommenem Buch,int',
	'rebirth_dks'=>'N?tige DKs f?r Erneuerung',
    'wallchangetime'=>'Geschmiere an der Mauer kann erst nach x Sekunden ge?ndert werden,int',
    'maxsentence'=>'H?chststrafe in Tagen',
    'locksentence'=>'Tage im Kerker ab denen es Sicherheitsverwahrung gibt',
	'user_rename'=>'Preis in DP f?r Namens?nderung nach Erneuerung / Wiedergeburt',
	'deathjackpot'=>'Derzeitiger Stand des Tot-o-Lotto Jackpots,int',
	'deathjackpotmax'=>'Maximaler Stand des Tot-o-Lotto Jackpots,int',

    'Neue Tage,title',
    'fightsforinterest'=>'H?chste Anzahl an ?brigen Waldk?mpfen um Zinsen zu bekommen,int',
    'maxinterest'=>'Maximaler Zinssatz (%),int',
    'mininterest'=>'Minimaler Zinssatz (%),int',
    'daysperday'=>'Spieltage pro Kalendertag,int',
    'dispnextday'=>'Zeit zum n?chsten Tag in Vital Info,bool',
    'specialtybonus'=>'Zus?tzliche Eins?tze der Spezialfertigkeit am Tag,int',
    'activategamedate'=>'Spieldatum aktiv,bool',
    'gamedateformat'=>'Datumsformat (zusammengesetzt aus: %Y; %y; %m; %n; %d; %j)',
    'gametimeformat'=>'Zeitformat',

    'Wald,title',
    'turns'=>'Waldk?mpfe pro Tag,int',
    'dropmingold'=>'Waldkreaturen lassen mindestens 1/4 des m?glichen Goldes fallen,bool',
    'lowslumlevel'=>'Mindestlevel bei dem perfekte K?mpfe eine Extrarunde geben,int',
	'forestbal'=>'Prozentsatz der pro perfektem Kampf auf Monsterst?rke aufgeschlagen wird',
	'forestdkbal'=>'Prozentsatz mit dem Drachenpunkteeinfluss auf Monsterst?rke multipliziert wird',
	'foresthpbal'=>'Zahl durch die max. LP geteilt werden ehe sie auf DP-Einfluss addiert werden',

	'Schloss,title',
	'castle_turns_wk'=>'Anzahl an WKs die man f?r eine Schlossrunde erh?lt,int',
	'wk_castle_turns'=>'Anzahl an WKs die eine Schlossrunde kostet,int',
    'castle_turns'=>'Schlossrunden pro Tag ,int',
    'castlegemdesc'=>'Abweichung vom max. Edelsteingewinn / Runde ?ber dem max.,int',
	'castlegolddesc'=>'Abweichung vom max. Goldgewinn / Runde ?ber dem max.,int',

	'Gilden,title',
    'dgguildmax'=>'Max. Anzahl an Gilden,int',
    'dgguildfoundgems'=>'Gems zur Gr?ndung,int',
	'dgguildfoundgold'=>'Gold zur Gr?ndung,int',
	'dgguildfound_k'=>'DKs zur Gr?ndung,int',
	'dgmaxmembers'=>'Max. Mitgliederzahl ohne Ausbauten,int',
	'dgminmembers'=>'Min. Mitgliederzahl,int',
	'dgplayerfights'=>'Max. K?mpfe eines Spielers gegen Gildenwachen pro Spieltag,int',
	'dgimmune'=>'Spieltage Immunit?t f?r eine neu gegr?ndete Gilde,int',
	'dggpgoldcost'=>'Kosten eines GP in Gold,int',
	'dgtaxdays'=>'Alle x Spieltage Steuern,int',
	'dgmaxtaxfails'=>'x mal Steuern nicht zahlen damit Gilde aufgel?st,int',
	'dgtaxgold'=>'Basis-Goldkosten der Steuer,int',
	'dgtaxgems'=>'Basis-Gemkosten der Steuer,int',
	'dgmaxgemstransfer'=>'Max. Edelsteinauszahlung pro Lvl,int',
	'dgmaxgoldtransfer'=>'Max. Goldauszahlung pro Lvl,int',
	'dgmaxgoldin'=>'Max. Goldeinzahlung pro Spieltag,int',
	'dgmaxgemsin'=>'Max. Gemeinzahlung pro Spieltag,int',
	'dgtrsmaxgold'=>'Max Gold in Schatzkammer,int',
	'dgtrsmaxgems'=>'Max Gems in Schatzkammer,int',
	'dgminmembertribute'=>'Mindesttribut der Mitglieder in %,int',
	'dgmindkapply'=>'Mindestanzahl an DKs f?r Mitgliedschaft,int',
	'dgstartgold'=>'Startgold,int',
	'dgstartgems'=>'Startgems,int',
	'dgstartpoints'=>'StartGP,int',
	'dgstartregalia'=>'Startinsignien,int',
	'dgbiomax'=>'Max. Zeichenanzahl der Bio,int',
	'dgminregaliaval'=>'Min. Preis / Insignie in GP,int',
	'dgmaxregaliaval'=>'Max. Preis / Insignie in GP (sinkt pro halbe Insignie die im Durchschnitt mehr verkauft wurde um 1),int',
	'dg_invent_out_price'=>'Faktor mit dem der Wert eines Items beim Auslagern aus Gildeninventar multipliziert wird um die Geb?hr zu ermitteln,int',

    'Kopfgeld,title',
    'bountymin'=>'Mindestbetrag pro Level der Zielperson,int',
    'bountymax'=>'Maximalbetrag pro Level der Zielperson,int',
    'bountylevel'=>'Mindestlevel um Opfer sein zu k?nnen,int',
    'bountyfee'=>'Geb?hr f?r Dag Durnick in Prozent,int',
    'maxbounties'=>'Anzahl an Kopfgeldern die ein Spieler pro Tag aussetzen darf,int',

    'Handelseinstellungen,title',
    'borrowperlevel'=>'Maximalwert den ein Spieler pro Level leihen kann (Bank),int',
    'maxinbank'=>'+/- Maximalbetrag f?r den noch Zinsen bezahlt/verlangt werden,int',
	'bankmaxgemstrf'=>'Max. Anzahl an Gem?berweisungen / Tag,int',
    'allowgoldtransfer'=>'Erlaube ?berweisungen (Gold und Edelsteine),bool',
    'transferperlevel'=>'Maximalwert den ein Spieler pro Level empfangen oder nehmen kann,int',
    'mintransferlev'=>'Mindestlevel f?r ?berweisungen (bei 0 DKs),int',
	'bankgemtrflvl'=>'Minimallvl um Edelstein?berweisungen empfangen zu k?nnen,int',
    'maxtransferout'=>'Menge die ein Spieler an andere ?berweisen kann (Wert x Level),int',
    'innfee'=>'Geb?hr f?r Expressbezahlung in der Kneipe (x oder x%),int',
    'selledgems'=>'Edelsteine die Vessa vorr?tig hat,int',
    'vendor'=>'H?ndler heute in der Stadt?,bool',
    'paidgold'=>'Gold das in Bettlergasse spendiert wurde,int',

	'H?user,title',
	'housemaxgemsout'=>'Max. Anzahl an Edelsteinen / Tag aus Haus entnehmbar,int',
    'newhouses'=>'Bauen neuer H?user m?glich ?,bool',
	'maxhouses'=>'Maximale Anzahl an H?usern ?,int',
	'housegetdks'=>'Min. DKs f?r H?userbau / kauf?,int',
	'housekeylvl'=>'Min. Lvl (bei 0 DKs) f?r Schl?sselvergabe?,int',
	'houseextdks'=>'Min. DKs f?r Hausausbau?,int',
	'houseextsellenabled'=>'Ausgebaute H?user zum Verkauf anbieten?,bool',
	'housegetdks'=>'Min. DKs f?r H?userbau / kauf?,int',
	'housekeylvl'=>'Min. Lvl (bei 0 DKs) f?r Schl?sselvergabe?,int',
	'houseextdks'=>'Min. DKs f?r Hausausbau?,int',
	'houseextsellenabled'=>'Ausgebaute H?user zum Verkauf anbieten?,bool',
	'housetrsshare'=>'Bei Schl?sselabnahme Teil aus Hausschatz an Betroffenen?,bool',
	'housedesclen'=>'Max. L?nge f?r Hausbeschreibung?,int',

    'Brieftauben,title',
    'mailsizelimit'=>'Maximale Anzahl an Zeichen in einer Nachricht,int',
    'inboxlimit'=>'Anzahl an Nachrichten in der Inbox,int',
    'modinboxlimit'=>'Dergleichen f?r MODs,int',
    'oldmail'=>'Alte Nachrichten automatisch l?schen nach x Tagen. x =,int',
    'modoldmail'=>'Dergleichen f?r MODs,int',
    'show_yom_contacts'=>'Zeige das Adressbuch in der YOM an,bool',
    'max_yom_contacts'=>'Maximale Anzahl an YOM Kontakten,int',
    'message2mail_activated'=>'D?rfen YoMs per Mail archiviert werden?,bool',

    'PvP,title',
    'pvp'=>'Spieler gegen Spieler aktivieren,bool',
    'pvpday'=>'Spielerk?mpfe pro Tag,int',
    'pvpimmunity'=>'Tage die neue Spieler vor PvP sicher sind,int',
    'pvpminexp'=>'Mindest-Erfahrungspunkte f?r PvP-Opfer,int',
    'pvpattgain'=>'Prozentsatz der Erfahrung des Opfers den der Angreifer bei Sieg bekommt,int',
    'pvpattlose'=>'Prozentsatz an Erfahrung den der Angreifer bei Niederlage verliert,int',
    'pvpdefgain'=>'Prozentsatz an Erfahrung des Angreifers den der Verteiger bei einem Sieg gewinnt,int',
    'pvpdeflose'=>'Prozentsatz an Erfahrung den der Verteidiger bei Niederlage verliert,int',
    'pvpmindkxploss'=>'DKs Unterschied zwischen T?ter und Opfer bis zu dem noch 0% XP abgezogen werden,int',

    'Inhalte l?schen (0 f?r nie l?schen),title',
	'lastcleanup'=>'Datetime der letzten S?uberung',
	'cleanupinterval'=>'Sekunden zwischen S?uberungen,int',
    'expirecontent'=>'Tage die Kommentare und News aufgehoben werden,int',
    'expiretrashacct'=>'Tage die Accounts gespeichert werden die nie eingeloggt waren,int',
    'expirenewacct'=>'Tage die Level 1 Accounts ohne Drachenkill aufgehoben werden,int',
    'expireoldacct'=>'Tage die alle anderen Accounts aufgehoben werden,int',

    'N?tzliche Informationen,title',
    'weather'=>'Heutiges Wetter:,'.$weather_enum,
    'newplayer'=>'Neuster Spieler',
    'Letzter neuer Tag: '.date('h:i:s a',strtotime(date('r').'-$realsecssofartoday seconds')).',viewonly',
    'N?chster neuer Tag: '.date('h:i:s a',strtotime(date('r').'+$realsecstotomorrow seconds')).',viewonly',
    'Aktuelle Spielzeit: '.getgametime().',viewonly',
    'Tagesl?nge: '.($dayduration/60/60).' Stunden,viewonly',
    'Aktuelle Serveruhrzeit: '.date('Y-m-d h:i:s a').',viewonly',
    'gameoffsetseconds'=>'Offset der Spieltage,$enum',
    'gamedate'=>'aktuelles Spieldatum (Y-m-d)',
	'exsearch_limit'=>'x Suchen f?r die Offlinesuche,int|?Wie oft darf in der Spielerliste gesucht werden wenn man nicht online ist.',
	'exsearch_time'=>'x Minuten Wartezeit bei Offlinesuche,int|?Wie lange muss man warten bevor man wieder die Spielerliste offline durchsuchen darf.',

    'LoGD-Netz Einstellungen (file wrappers m?ssen in der PHP Konfiguration aktiviert sein!!),title',
    'logdnet'=>'Beim LoGD-Netz eintragen?,bool',
    'serverurl'=>'Server URL',
    'serverdesc'=>'Serverbeschreibung (255 Zeichen)',
    'logdnetserver'=>'LoGD-Netz Zentralserver (Default: http://lotgd.net)',

	'Forum + IRC Einstellungen,title',
    'ci_active'=>'Passierschein aktiv,bool',
	'ci_dk'=>'Anzahl der Dk f?r Passierschein?,int',
	'ci_su'=>'Superuser level >=,enum,0,0,1,1,2,2,3,3,4,4,5,5',
    'ci_dk_mail_active'=>'Mail bei Drachenkill?,bool',
	'ci_dk_mail_head'=>'?berschrift der Mail (Betreff)',
    'ci_dk_mail_text'=>'Text der Mail,textarea,30,5',
	'ci_std_pw_active'=>'Standard Passwort,bool',
	'ci_std_pw'=>'Standard Passwort (Klartext)',
	'ci_std_pwc'=>'Standard Passwort (so wie es eingetragen wird)',

    'irc_active'=>'IRC aktiv,bool',
    'irc_su_minlevel'=>'Superuser minLevel|?numerischer Wert wie in security.php definiert',
    'irc_appletserver'=>'Server wo pJirc liegt|?z.B. http://irc.bbox.ch/',
    'irc_server'=>'IRC-Server|?z.B. irc.bbox.ch',
    'irc_server_port'=>'Port (6667)',
    'irc_channel'=>'IRC-Raumname (#atrahor)',
    'irc_cpw'=>'Raum-Passwort|?mu? angegeben werden um den Raum zu betreten wenn Raum auf +k steht',


	'RSS Einstellungen,title',
	'rss_enable_motd_feed'=>'Automatisch RSS feed f?r MOTD erstellen?,bool',
	'rss_item_count'=>'Default Anzahl der RSS Items,int',
	'rss_title'=>'Titel des RSS Feeds',
	'rss_description'=>'Beschreibung des RSS Feeds',
	'rss_link'=>'Wohin linken die RSS Items',
	'rss_image'=>'Link zu einem Bild das den Feed beschreibt',
	'rss_file_abs_path'=>'Absoluter Pfad zum MOTD RSS File|?',
	'rss_file_rel_path'=>'Relativer Pfad zum MOTD RSS File|?relativer Pfad vom Spiel aus gesehen, also bswp'
);



if ($_GET['op']=='')
{

	$output .= '<form action="su_configuration.php?op=save" method="POST">
				<input type="hidden" name="settings_back" value="'.urlencode(serialize($settings)).'">';
    addnav('','su_configuration.php?op=save');

    showform($setup,$settings);

    $output .= '</form>';
}

page_footer();
?>
