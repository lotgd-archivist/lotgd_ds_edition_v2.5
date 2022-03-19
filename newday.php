<?php



// 24072004

require_once('common.php');
require_once(LIB_PATH.'disciples.lib.php');

// Specialties neu laden
cache_release_local('specialties');
// Tier neu laden
cache_release_local('playermount');

require_once(LIB_PATH.'dg_funcs.lib.php');


/***************

**  SETTINGS **

***************/

$turnsperday = getsetting("turns",10);

$maxinterest = ((float)getsetting("maxinterest",10)/100) + 1; //1.1;

$mininterest = ((float)getsetting("mininterest",1)/100) + 1; //1.1;

//$mininterest = 1.01;

$dailypvpfights = getsetting("pvpday",3);

//Fishing

$fturn = 0;

if ($session['user']['dragonkills']>1) $fturn = 3;

if ($session['user']['dragonkills']>3) $fturn = 4;

if ($session['user']['dragonkills']>5) $fturn = 5;

$changes_new = array();

if ($_GET['resurrection']=="true") {

	$resline = "&resurrection=true";

}
else if ($_GET['resurrection']=="egg") {

	$resline = "&resurrection=egg";

}
//RUNEN MOD
elseif( $_GET['resurrection']=='rune' ){
	$resline = '&resurrection=rune';
}
//RUNEN END
else {	// Nicht wiedererweckt

	$resline = "";

	$changes_new = array( 'symp_given'=>0, 'doc_visited'=>0, 'poollook'=>0, 'treepick'=>0,
	'fishturn'=>$fturn, 'rouletterounds'=>5, 'seenbard'=>0, 'usedouthouse'=>0,
	'lottery'=>0, 'guildfights'=>0, 'itemsin'=>0, 'itemsout'=>0, 'games_played'=>0 );

}

$changes = array ( 'cage_action'=>0, 'dollturns'=>5, 'spittoday'=>0, 'gotfreeale'=>0, 'hadnewday'=>0, 'xchangedtoday'=>0, 'dpower'=>0,
'abused'=>0, 'boughtroomtoday'=>0
, 'goldin'=>0, 'gemsin'=>0, 'goldout'=>0, 'gemsout'=>0, 'seenacademy'=>0, 'witch'=>0 );

$changes = array_merge($changes,$changes_new);

user_set_aei($changes);


// $resline = $_GET['resurrection']=="true" ? "&resurrection=true" : "" ;

/******************

** End Settings **

******************/

if (count($session['user']['dragonpoints']) < $session['user']['dragonkills']&&$_GET['dk']!=""){

	array_push($session['user']['dragonpoints'],$_GET[dk]);

	switch($_GET['dk']){

		case "hp":

			$session['user']['maxhitpoints']+=5;

			break;

		case "at":

			$session['user']['attack']++;

			break;

		case "de":

			$session['user']['defence']++;

			break;

	}

	// Nein, der Drachenhort zählt nicht als Haus

	$session['user']['restatlocation']=0;

}

if (count($session['user']['dragonpoints'])<$session['user']['dragonkills'] && $_GET['dk']!="ignore"){

	page_header("Drachenpunkte");

	addnav("Max Lebenspunkte +5","newday.php?dk=hp$resline");

	addnav("Waldkämpfe +1","newday.php?dk=ff$resline");

	addnav("Angriff + 1","newday.php?dk=at$resline");

	addnav("Verteidigung + 1","newday.php?dk=de$resline");

	//addnav("Ignore (Dragon Points are bugged atm)","newday.php?dk=ignore$resline");

	output("`@Du hast noch `^".($session['user']['dragonkills']-count($session['user']['dragonpoints']))."`@  Drachenpunkte übrig. Wie willst du sie einsetzen?`n`n");

	output("Du bekommst 1 Drachenpunkt pro getötetem Drachen. Die Änderungen der Eigenschaften durch Drachenpunkte sind permanent.");

}
else if (empty($session['user']['race'])){

	page_header("Ein wenig über deine Vorgeschichte");
	if (!empty($_GET['setrace'])){

		$str_race = urldecode(stripslashes($_GET['setrace']));

		$session['user']['race'] = $str_race;
		$arr_race = race_get($session['user']['race'],true);

		// Beschreibung
		output($arr_race['chosen_msg']);

		// Permanenz-Boni
		race_set_boni(true,false,$session['user']);

		addnav("Weiter","newday.php?continue=1$resline");
		if ($session['user']['dragonkills']==0 && $session['user']['level']==1){
			addnews("`#{$session['user'][name]} `#hat unsere Welt betreten. Willkommen!");
			addhistory('Ankunft in '.getsetting('townname','Atrahor'));
		}
	}
	else{

		// Rassenliste abrufen

		$sql = 'SELECT long_desc,id,name,colname FROM races WHERE active=1 AND mindk <= '.$session['user']['dragonkills'].' AND superuser <= '.$session['user']['superuser'].' ORDER BY name ASC';
		$res = db_query($sql);

		$str_out = '`b`c`&Welcher Rasse gehörst du an?`b`c`n`n
					<table border="0">';
		$str_racelnk = basename(__FILE__).'?setrace=';

		while($r = db_fetch_assoc($res)) {

			$str_out .= '<tr class="trdark"><td colspan="2">&nbsp;</td></tr>
						<tr class="trlight"><td valign="top">'.
			create_lnk('&raquo;`b'.$r['colname'].'`b',$str_racelnk.urlencode($r['id'])).': </td>'.
			'<td>'.$r['long_desc'].'</td></tr>';
			addnav($r['name'],$str_racelnk.urlencode($r['id']));

		}
		$str_out .= '</table>';
		output($str_out,true);


	}
}	// END Rasse nicht gesetzt
else if ((int)$session['user']['specialty']==0)
{
	if (!isset($_GET['setspecialty']))
	{
		page_header("Ein wenig über deine Vorgeschichte");
		output("Du erinnerst dich, dass du als Kind:`n`n");
		$sql = "SELECT * FROM specialty WHERE active='1' ORDER BY category,specid";
		$result = db_query($sql);
		$i=0;
		while($row = db_fetch_assoc($result))
		{
			$file = $row['filename'];
			if(file_exists("module/".$file.".php"))
			{
				require_once("module/".$file.".php");
				$f1 = $file."_info";
				$f2 = $file."_run";
				$f1();
				if ($category!=$row['category'])
				{
					addnav($row['category']);
					$category = $row['category'];
				}
				$f2('link',$row['specid']);
				output_array($info);
			}
		}
	}
	else
	{
		addnav("Weiter","newday.php?continue=1$resline");
		$sql = "SELECT * FROM specialty WHERE specid='".$_GET['setspecialty']."'";
		$row = db_fetch_assoc(db_query($sql));
		page_header($row['specname']);
		switch($_GET['setspecialty'])
		{
			case $row['specid']:
				$file = $row['filename'];
				if(file_exists("module/".$file.".php"))
				{
					require_once ("module/".$file.".php");
					$f1 = $file."_info";
					$f2 = $file."_run('backgroundstory');";
					$f1();
					eval($f2);
				}
				break;
		}
		$session['user']['specialty']=(int)$_GET['setspecialty'];
	}
}	// END specialty nicht gesetzt

else	// Normaler Newday
{

	$row_extra = user_get_aei();

	// Stats
	if($session['user']['turns'] > 0 && $session['user']['alive']) {
		user_set_stats( array('turns_not_used'=>'turns_not_used+'.$session['user']['turns']) );
	}
	// END Stats

	page_header("Es ist ein neuer Tag!");

	output("`c<font size='+1'>`b`#Es ist ein neuer Tag!`0`b</font>`c",true);

	if (!$session['user']['prefs']['nosounds']) {
		output("<embed src=\"media/newday.wav\" width=10 height=10 autostart=true loop=false hidden=true volume=100>",true);
	}

	if ($session['user']['alive']!=true)
	{
		$session['user']['resurrections']++;
		output("`@Du bist wiedererweckt worden! Dies ist der Tag deiner ".ordinal($session['user']['resurrections'])." Wiederauferstehung.`0`n");

		$session['user']['alive']=true;
	}
	$session['user']['age']++;

	output("Du öffnest deine Augen und stellst fest, dass dir ein neuer Tag geschenkt wurde. Dies ist dein `^".ordinal($session['user']['age'])."`0 Tag in diesem Land. ");
	output("Du fühlst dich frisch und bereit für die Welt!`n");
	output("`2Runden für den heutigen Tag: `^$turnsperday`n");


	// BANK
	$interestrate = e_rand($mininterest*100,$maxinterest*100)/(float)100;

	if ($session['user'][goldinbank]<0 && abs($session['user'][goldinbank])<(int)getsetting("maxinbank",10000))
	{
		output("`2Heutiger Zinssatz: `^".(($interestrate-1)*100)."% `n");
		output("`2Zinsen für Schulden: `^".-(int)($session['user']['goldinbank']*($interestrate-1))."`2 Gold.`n");
	}
	else if ($session['user'][goldinbank]<0 && abs($session['user'][goldinbank])>=(int)getsetting("maxinbank",10000))
	{
		output("`4Die Bank erlässt dir deine Zinsen, da du schon hoch genug verschuldet bist.`n");
		$interestrate=1;
	}
	else if ($session['user'][goldinbank]>=0 && $session['user'][goldinbank]>=(int)getsetting("maxinbank",10000) && $session['user']['turns']<=getsetting("fightsforinterest",4))
	{
		$interestrate=1;
		output("`4Die Bank kann dir heute keinen Zinsen zahlen. Sie würde früher oder später an dir pleite gehen.`n");
	}
	else if ($session['user'][goldinbank]>=0 && $session['user'][goldinbank]<(int)getsetting("maxinbank",10000) && $session['user']['turns']<=getsetting("fightsforinterest",4))
	{
		output("`2Heutiger Zinssatz: `^".(($interestrate-1)*100)."% `n");
		output("`2Durch Zinsen verdientes Gold: `^".(int)($session['user']['goldinbank']*($interestrate-1))."`n");
	}
	else
	{
		$interestrate=1;
		output("`2Dein heutiger Zinssatz beträgt `^0% (Die Bank gibt nur den Leuten Zinsen, die dafür arbeiten)`n");
	}
	// END Bank

	// Schläger

	if ($row_extra['beatenup']!=1) {
		output("`2Deine Gesundheit wurde wiederhergestellt auf `^".$session['user']['maxhitpoints']."`n");
		$session['user']['hitpoints'] = $session['user'][maxhitpoints];
	}
	else {
		output("`4Die Prügel der letzten Nacht haben Spuren hinterlassen. Du regenerierst nicht.`n");
		user_set_aei( array('beatenup'=>0) );
	}


	if ($row_extra['beatenup']>0) {
		$beaten=$row_extra['beatenup']-1;

		if ($row_extra['beatenup']>2) {
			output("`6Die \"Familie\" wird dich noch für ".($beaten-1)." Tage ihren Freund nennen!`n");
		}
		else {
			output("`6Von heute an bist du kein Freund der \"Familie\" mehr!`n");
			$beaten=0;
		}

		user_set_aei( array('beatenup'=>$beaten) );

	}

	// END Schläger

	// Verheiratet
	if ($session['user']['marriedto']==4294967295 || $session['user']['charisma']==4294967295)
	{
		output("`n`%Du bist verheiratet, es gibt also keinen Grund mehr, das perfekte Image aufrecht zu halten. Du lässt dich heute ein bisschen gehen.`n Du verlierst einen Charmepunkt.`n");
		$session['user']['charm']--;

	}
	// END Verheiratet

	// Buffs
	$tempbuf = unserialize($session['user']['bufflist']);
	$session['user']['bufflist']='';
	$session['bufflist']=array();
	if(is_array($tempbuff))
	{
		foreach($tempbuff as $key => $val)
		{
			if ($val['survivenewday']==1){
				$session['bufflist'][$key]=$val;
				output("{$val['newdaymessage']}`n");
			}
		}
	}
	// END Buffs

	$dkff=0;
	if(is_array($session['user']['dragonpoints']))
	{
		foreach($session['user']['dragonpoints'] as $key => $val)
		{
			if ($val=='ff'){
				$dkff++;
			}
		}
		if ($dkff>0) {

			output("`n`2Du erhöhst deine Waldkämpfe um `^$dkff`2 durch verteilte Drachenpunkte!");
		}

		// Vieh
		if ($session['user']['hashorse']){

			// Mount neu laden
			getmount($session['user']['hashorse'],true);

			$session['bufflist']['mount']=unserialize($playermount['mountbuff']);

			if ($row_extra['hasxmount']==1) {
				$session['bufflist']['mount']['name']=$row_extra['xmountname']." `&({$session['bufflist']['mount']['name']}`&)";
			}

			$session['bufflist']['mount']['rounds']+=$row_extra['mountextrarounds'];

		}
		// END Vieh

		// Wiederauferstehungen
		if ($_GET['resurrection']=="true"){
			addnews("`&{$session['user']['name']}`& wurde von `\$Ramius`& wiedererweckt.");
			$spirits=-6;
			if ($session['user']['marks']>=CHOSEN_FULL) {
				$session['user']['deathpower']-=80;
			}
			else {
				$session['user']['deathpower']-=100;
			}
			$session['user']['restorepage']="village.php?c=1";
		}
		elseif ($_GET['resurrection']=="egg"){
			addnews("`&{$session['user']['name']}`& hat das `^goldene Ei`& benutzt und entkam so dem Schattenreich.");
			$spirits=-6;
			//$session['user']['deathpower']-=100;
			$session['user']['restorepage']="village.php?c=1";
			savesetting("hasegg",stripslashes(0));

			item_set(' tpl_id="goldenegg" ',array('owner'=>0));

		}
		elseif ($_GET['resurrection']=='rune'){
			addnews('`q'.$session['user']['name'].'`q hat die Magie der `#Eiwaz-Rune`q benutzt um aus dem Schattenreich zu entkommen.');
			$spirits=-6;
			//$session['user']['deathpower']-=100;
			$session['user']['restorepage']="village.php?c=1";
			item_delete('tpl_id="r_eiwaz" AND owner='.$session['user']['acctid'],1);

		}
		else
		{
			// END Wiederauferstehungen
			// LAUNE
			$r1 = e_rand(-1,1);
			$r2 = e_rand(-1,1);
			$spirits = $r1+$r2;

			// Schelme sind nie schlecht gelaunt
			if ((int)$session['user']['race']==13)
			{
				$spirits+=2;
				if ($spirits>2) { $spirits=2; }
			}
		}

		$sp = array((-6)=>"Auferstanden",(-2)=>"Sehr schlecht",(-1)=>"Schlecht","0"=>"Normal",1=>"Gut",2=>"Sehr gut");

		output("`n`2Dein Geist und deine Stimmung ist heute `^".$sp[$spirits]."`2!`n");
		if (abs($spirits)>0){
			output("`2Deswegen `^");
			if($spirits>0){
				output("bekommst du zusätzlich ");
			}
			else{
				output("verlierst du ");
			}
			output(abs($spirits)." Runden`2 für heute.`n");
		}

		// END Laune


		// Allg. Wertesetzen
		$session['user']['laston'] = date("Y-m-d H:i:s");
		$bgold = $session['user']['goldinbank'];
		$session['user']['goldinbank']*=$interestrate;
		$nbgold = $session['user']['goldinbank'] - $bgold;

		$session['user']['turns']=$turnsperday+$spirits+$dkff;

		$session['user']['drunkenness']=0;
		$session['user']['bounties']=0;

		$session['user']['castleturns']=getsetting("castle_turns",1);
		if ($session['user'][maxhitpoints]<6) $session['user'][maxhitpoints]=6;
		$session['user']['spirits'] = $spirits;
		$session['user']['playerfights'] = $dailypvpfights;

		$session['user']['seendragon'] = 0;
		$session['user']['seenmaster']=0;
		$session['user']['mazeturn']=0;
		$session['user']['seenlover'] = 0;
		$session['user']['fedmount'] = 0;
		if ($_GET['resurrection']!="true" && $_GET['resurrection']!="egg" && $_GET['resurrection']!='rune'){
			$session['user']['soulpoints']=50 + 5 * $session['user']['level'];
			$session['user']['gravefights']=getsetting("gravefightsperday",10);
			$session['user']['reputation']+=5;
		}

		$session['user']['recentcomments']=$session['user']['lasthit'];
		$session['user']['lasthit'] = date("Y-m-d H:i:s");
		if ($session['user']['drunkenness']>66){
			output("`&Wegen deines schrecklichen Katers wird dir 1 Runde für heute abgezogen.");
			$session['user']['turns']--;
		}


		// NEWDAY SEMAPHORE
		// following by talisman & JT
		//Set global newdaysemaphore

		$lastnewdaysemaphore = convertgametime(strtotime(getsetting("newdaysemaphore","0000-00-00 00:00:00")));
		$gametoday = date("Ymd",gametime());

		if ($gametoday!=date("Ymd",$lastnewdaysemaphore)){
			$sql = "LOCK TABLES settings WRITE";
			db_query($sql);

			// Talion: We have to get it right from the database to ensure that it is the newest version
			$arr_tmp = db_fetch_assoc(db_query('SELECT value FROM settings WHERE setting="newdaysemaphore"'));
			$lastnewdaysemaphore = convertgametime(strtotime(stripslashes($arr_tmp['value'])));

			if ($gametoday!=date("Ymd",$lastnewdaysemaphore)){
				//we need to run the hook, update the setting, and unlock.
				savesetting("newdaysemaphore",date("Y-m-d H:i:s"));
				$sql = "UNLOCK TABLES";
				db_query($sql);

				require_once "setnewday.php";

			}
			else{
				//someone else beat us to it, unlock.
				$sql = "UNLOCK TABLES";
				db_query($sql);
				output("Somebody beat us to it");
			}
		}

		$w = get_weather();
		output("`nEin Blick zum Himmel verrät dir das derzeitige Wetter: `6".$w['name']."`@.`n");
		// Wettereffekt nicht bei Wiedererweckung oder Haft
		if (($_GET['resurrection']=="") && ($session['user']['imprisoned']==0)) {
			$sql = "SELECT * FROM specialty WHERE specid='".$session['user']['specialty']."'";
			$row = Db_Fetch_Assoc(Db_Query($sql));
			require_once "module/".$row['filename'].".php";
			$f = $row['filename']."_run";
			$f("weather");
		}
		//End global newdaysemaphore code and weather mod.

		if ($session['user']['hashorse']){
			output(str_replace("{weapon}",$session['user']['weapon'],"`n`&{$playermount['newday']}`n`0"));
			if ($playermount['mountforestfights']>0){
				output("`n`&Weil du ein(e/n) {$playermount['mountname']} besitzt, bekommst du `^".((int)$playermount['mountforestfights'])."`& Runden zusätzlich.`n`0");
				$session['user']['turns']+=(int)$playermount['mountforestfights'];
			}
			else
			{
				$session['user']['turns']+=(int)$playermount['mountforestfights'];
			}
		}
		else{
			output("`n`&Du schnallst dein(e/n) `%".$session['user']['weapon']."`& auf den Rücken und ziehst los ins Abenteuer.`0");
		}

		//knappe
		$arr_disc = get_disciple($session['user']['acctid']);

		if (is_array($arr_disc) && $arr_disc['state'] > 0) {
			output("`&Dein Knappe ".$arr_disc['name']." erwartet dich schon voller Spannung auf die Abenteuer dieses Tages.");
			$session['bufflist']['decbuff'] = $arr_disc['buff'];
		}
		// END knappe


		// BONI durch DP

		// WK durch DP
		$config = unserialize($session['user']['donationconfig']);

		if (!is_array($config['forestfights'])) {
			$config['forestfights']=array();
		}

		reset($config['forestfights']);
		foreach($config['forestfights'] as $key=>$val){
			$config['forestfights'][$key]['left']--;
			output("`@Du bekommst eine Extrarunde für die Punkte auf `^{$val['bought']}`@.");
			$session['user']['turns']++;
			if ($val['left']>1){
				output(" Du hast `^".($val['left']-1)."`@ Tage von diesem Kauf übrig.`n");
			}
			else{
				unset($config['forestfights'][$key]);
				output(" Dieser Kauf ist damit abgelaufen.`n");
			}
		}
		// Golinda
		if ($config['healer'] > 0) {
			$config['healer']--;
			if ($config['healer'] > 0) {
				output("`n`@Golinda ist bereit, dich noch {$config['healer']} weitere Tage zu behandeln.");
			}
			else {
				output("`n`@Golinda wird dich nicht länger behandeln.");
				unset($config['healer']);
			}
		}
		// Goldmine
		if ($config['goldmineday']>0) {
			$config['goldmineday']=0;
		}

		$session['user']['donationconfig']=serialize($config);
		// END BONI durch DP

		// Specialties
		$sb = getsetting("specialtybonus",1);
		output("`n`n`2Für dein Spezialgebiet erhältst du zusätzlich $sb Anwendung(en) für heute.`n");

		restore_specialty();
		// END specialties

		// RASSEN
		$arr_race = race_get($session['user']['race'],true);

		if(!empty($arr_race['newday_msg'])) {
			output('`n'.$arr_race['newday_msg']);
		}

		// Boni
		race_set_boni(false,false,$session['user']);

		// END RASSEN

		// Heimsuchung
		if ($row_extra['hauntedby']>""){
			output("`n`n`)Du wurdest von {$row_extra['hauntedby']}`) heimgesucht und verlierst eine Runde!");
			$session['user']['turns']--;
			user_set_aei( array('hauntedby'=>'') );
		}

		//Stadtwache
		if ($session['user']['profession']==3) {
			$session['user']['profession']=4;
			output("`n`&Mit dem heutigen Tag endet dein Dienst bei der Stadtwache.`n");
		}
		if ($session['user']['profession']==1) {
			output("`n`&Als `@Mitglied der`& Stadtwache hast du 2 Spielerkämpfe mehr!`n");
			$session['user']['playerfights']+=2;
		}
		if ($session['user']['profession']==2) {
			output("`n`&Als `4Hauptmann`& der Stadtwache hast du 3 Spielerkämpfe mehr!`n");
			$session['user']['playerfights']+=3;
		}

		// Priester
		if ($session['user']['profession']==11 || $session['user']['profession']==12) {
			output("`n`&Als `b`7Priester`b`& erhältst du 1 Anwendung in mystischen Kräften zusätzlich!`n");
			$session['user']['specialtyuses']['magicuses']++;
		}

		// Tempeldiener
		if ($session['user']['profession']==PROF_TEMPLE_SERVANT && $_GET['resurrection'] == '') {

			$servant_days = ($row_extra['temple_servant'] >= 20 ? $row_extra['temple_servant']*0.05 : $row_extra['temple_servant']);
			$servant_days++;

			$days_left = 8 - $servant_days;

			if($days_left <= 0) {
				output("`n`@Dein Dienst als `b`7Tempeldiener`b`@ neigt sich dem Ende zu!");
				addnews($session['user']['name'].'`8s Zeit als Tempeldiener ist Vergangenheit.');
				$session['user']['profession'] = 0;
				$servant_days = 5;	// Tage vor neuerlichem Dienst
			}
			else {
				output("`n`6Als `b`7Tempeldiener`b`6 musst du noch ".$days_left." Tage arbeiten!`n");
			}

		}
		else if ($session['user']['profession']!=PROF_TEMPLE_SERVANT && $row_extra['temple_servant'] > 0 && $_GET['resurrection'] == '') {
			$servant_days = $row_extra['temple_servant']-1;
		}

		if(isset($servant_days)) {
			user_set_aei( array('temple_servant'=>$servant_days) );
		}

		//Kerker-Addon
		if ($session['user']['imprisoned']>0) {

			$session['user']['daysinjail']++;

			if ($session['user']['imprisoned']==1) {
				output ("`n`^Deine Haftstrafe ist beendet und du kannst den Kerker verlassen.`&`n");
				$session['user']['imprisoned']=0;
				$session['user']['steuertage']=7;
				$session['user']['location']=0;

			}
			else {
				$session['user']['imprisoned']-=1;
				output ("`n`^Du bist im Kerker gefangen und musst noch ".($session['user']['imprisoned'])." Tage absitzen, bevor du frei gelassen wirst!`&`n");
			}
		}
		if ($session['user']['imprisoned']<0) {
			output ("`n`^Du wurdest von einem MOD oder einem ADMIN auf unbestimmte Zeit eingekerkert. Kläre mit diesem, wann du wieder frei gelassen wirst!`&`n");
		}
		// END Kerker-Addon

		// Male
		if (($session['user']['marks']==CHOSEN_BLOODGOD) && ($session['user']['level']>2) && (e_rand(1,60)==15))
		{
			$session['user']['marks']+=1;
			systemmail($session['user']['acctid'],"`\$Von : Blutgott!`0","`&Sterblicher!`nWisse dass ich, der Blutgott, deiner überdrüssig geworden bin! Ich fordere dich auf die Feste der Auserwählten aufzusuchen und dich im Kampf gegen meinen Champion als würdig zu zeigen!`nDu hast 3 Tage Zeit. Solltest du dieser Herausforderung nicht nachkommen, so betrachte unseren Pakt als nichtig!");
		}
		else if (($session['user']['marks']>=CHOSEN_BLOODCHAMP) && ($session['user']['marks']<CHOSEN_BLOODCHAMP_END))
		{
			$session['user']['marks']+=1;
			systemmail($session['user']['acctid'],"`\$Von : Blutgott!`0","`&Sterblicher!`nMein Champion wartet auf dich!`nBeweise Mut und fordere ihn heraus! Du hast noch ".(36-$session['user']['marks'])." Tage Zeit!");
		}
		else if ($session['user']['marks']>=CHOSEN_BLOODCHAMP_END)
		{
			$session['user']['marks']=CHOSEN_FULL;
			systemmail($session['user']['acctid'],"`\$Von : Blutgott!`0","`&Sterblicher!`nDeine Feigheit ist mir zuwider! Betrachte unseren Pakt als nichtig!");
		}
		// END Male

		//DK-Verweigerung

		$cowardlevel = getsetting("cowardlevel",10);

		if (($session['user']['level']>=$cowardlevel) && ($session['user'][dragonkills]>=2) && ($session['user']['marks']<31)) {

			if ($session['user']['age']>=getsetting('maxpvpage',50)){
				output("`n`@Du bist nun schon eine ganze Weile hier und hast immer noch nicht den Drachen herausgefordert.");
				output("`n`@Die Leute fangen schon an über dich zu reden. Du solltest dich beeilen!`n");
				$session['user']['reputation']-=5;
			}

			if ($session['user']['age']>=getsetting('cowardage',60)) {

				if ($session[user][title]!="Feigling")
				{
					$newtitle = 'Feigling';

					$regname = ($row_extra['cname'] ? $row_extra['cname'] : $session['user']['login']);

					$session['user']['name'] = $newtitle.' '.$regname;
					$session['user']['title'] = $newtitle;

					output("`n`@Von nun an bist du bekannt als {$session['user']['name']}`0!`n`n");
					addnews("`@".$regname."`@ ist aufgrund ".($session[user][sex]?"ihrer":"seiner")." Feigheit vor dem Drachen von nun an bekannt als ".$session['user']['name']."!`n");

				}
				else {
					if(e_rand(1,5) == 1) {
						$sql = "INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'village',".$session[user][acctid].",'/me `\@eilt hastig über den Dorfplatz,   verfolgt von einer Horde Kinder, die ".($session[user][sex]?"sie":"ihn")." johlend mit Steinchen bewerfen!')";
						db_query($sql) or die(db_error(LINK));
						addnews("`@".$session['user']['name']."`@ ist wieder in der Stadt! Haltet Eier und faules Obst bereit!");
					}
				}

				output("`@Deine Feigheit vor dem Drachen spricht sich herum! Du verlierst Ansehen!`n");
				$session['user']['reputation']-=10;
			}
		}

		// Wiedergewinn von Erfahrung für Drückeberger
		$recoveryage=getsetting("recoveryage",75);
		$recoveryexp=getsetting("recoveryexp",500);
		$exp=0;

		if ($session['user']['age']>=$recoveryage)
		{
			$exp+=($recoveryexp*$session['user']['dragonkills']);
			if ($session['user']['age']>=($recoveryage*2))
			{
				$exp+=($recoveryexp*$session['user']['dragonkills']);
			}
			$exp+=$recoveryexp;
			if ($session['user']['age']>=($recoveryage*3))
			{
				$exp+=($recoveryexp*$session['user']['dragonkills']);
			}
			output("`^`nDu bist nun schon derart lange im Dorf, dass Teile deiner Erinnerung zurückkehren, die du in deinem letzten Kampf gegen den Drachen verloren hast.`nDeine Erfahrung steigt um $exp Punkte.`n");
			$session['user']['experience']+=$exp;
		}

		// END DK-Ausweichler

		//Steuern zahlen
		output("`n");
		if (($session['user']['marks']<CHOSEN_FULL) && ($session['user']['imprisoned']==0) && ($session['user']['level'] >= 5) && (getsetting("taxrate",750)>0) ) {

			if ($session['user'][steuertage]==3) {
				output("`^`cIn zwei Tagen musst Du Steuern zahlen gehen!`c`n`n");
			}
			if ($session['user'][steuertage]==2) {
				output("`^`cMorgen musst Du Steuern zahlen gehen!`c`n`n");
			}
			if ($session['user'][steuertage]==1) {

				if($session['user']['dragonkills'] > 0) {
					$taxprison=getsetting("taxprison",1);
					output("`^`cHeute ist Zahltag - du musst heute Steuern zahlen!");

					if ($taxprison>0)
					{
						output(" Tust du es nicht, wanderst du in den Kerker!`n");
						$mailtext="`&Vergiss nicht heute deine Steuern zu zahlen! Tust du es nicht, wirst du in den Kerker gesperrt und dein Bankkonto wird gepfändet!`n`nHochachtungsvoll`nDein Steuereintreiber";
					}
					else
					{
						$mailtext="`&Vergiss nicht heute deine Steuern zu zahlen! Tust du es nicht, wird dein Bankkonto gepfändet!`n`nHochachtungsvoll`nDein Steuereintreiber";
					}

					systemmail($session['user']['acctid'],"`\$Heute ist Zahltag!`0",$mailtext);
				}
				else {
					output("`^`cHeute wäre Zahltag - du müsstest heute Steuern zahlen. Als Neuankömmling bist du jedoch noch davon befreit!`n");
				}

			}
			if ($session['user'][steuertage]<1)
			{
				if($session['user']['dragonkills'] > 0) {
					$taxrate=2*getsetting("taxrate",750);
					$taxprison=getsetting("taxprison",1);

					if ($taxprison>0)
					{
						$mailtext="`&Du wusstest, dass du Steuern zahlen musst und hast dich dennoch geweigert. Du hieltest es nicht einmal für nötig im Dorfamt zu erscheinen und zu erklären warum du das Gold nicht hast.`nDafür haben sie dich jetzt geholt. Mitten in der Nacht wurdest du festgenommen und in den Kerker geworfen. Von deinem Bankkonto wurden {$taxrate} Gold gepfändet. Lass dir das fürs nächste Mal eine Lehre sein!";
						output("`^Da du Deine Steuern nicht gezahlt hast kommst du in den Kerker! Außerdem wurden {$taxrate} Gold von der Bank gepfändet!`n`n");
						$session['user']['imprisoned']=$taxprison;
						$session['user']['restatlocation']=0;
						debuglog('hinterzog Steuern und landete im Kerker.');
						addnews("`2{$session['user']['name']}`2 hat die Steuern nicht gezahlt und büßt dafür im Kerker!");
					}
					else
					{
						$mailtext="`&Du wusstest, dass du Steuern zahlen musst und hast dich dennoch geweigert. Du hieltest es nicht einmal für nötig im Dorfamt zu erscheinen und zu erklären warum du das Gold nicht hast.`nDafür wurde jetzt das Doppelte des hinterzogenen Betrags von deinem Konto gepfändet, nämlich {$taxrate} Gold. Sei froh, dass so etwas derzeit nicht mit Kerker bestraft wird und lass es dir fürs nächste Mal eine Lehre sein!";
						output("`^Da du Deine Steuern nicht gezahlt hast wurden {$taxrate} Gold von der Bank gepfändet!`n`n");
						debuglog('hinterzog Steuern.');
					}
					systemmail($session['user']['acctid'],"`\$Steuerhinterziehung!`0",$mailtext);
					savesetting ("amtskasse" ,getsetting ("amtskasse",0)+ $taxrate);
					if (getsetting("amtskasse","0")>getsetting("maxbudget","2000000"))
					{
						savesetting("amtskasse",getsetting("maxbudget","2000000"));
					}
					$session['user'][goldinbank]-=$taxrate;
					$session['user'][steuertage]=7;
				}
				else {
					$session['user'][steuertage]=7;
				}
			}

			$session['user'][steuertage]--;

		}
		// END Steuern zahlen

		$rp = $session['user']['restorepage'];
		$x = max(strrpos("&",$rp),strrpos("?",$rp));
		if ($x>0) $rp = substr($rp,0,$x);

		// korrekt ausgeloggt ?
		if ($session['user']['imprisoned']!=0) {
			addnav("Weiter","prison.php");
		}
		else if (substr($rp,0,10)=="badnav.php") {
			addnav("Weiter","news.php");
		}
		else
		{
			addnav("Weiter",preg_replace("'[?&][c][=].+'","",$rp));
		}


		// Ehre & Ansehen
		$session['user']['reputation'] = min($maximumrep,$session['user']['reputation']);

		if ($session['user']['reputation']<=-50){
			$session['user']['reputation']=-50;
			output("`n`8Da du aufgrund deiner Ehrenlosigkeit häufig Steine in den Weg gelegt bekommst, kannst du heute 1 Runden weniger kämpfen. Außerdem sind deine Feinde vor dir gewarnt.`nDu solltest dringend etwas für deine Ehre tun!");
			$session['user']['turns']--;
			$session['user']['playerfights']--;
		}
		else if ($session['user']['reputation']<=-30){
			output("`n`8Deine Ehrenlosigkeit hat sich herumgesprochen! Deine Feinde sind vor dir gewarnt, weshalb dir heute 1 Spielerkampf weniger gelingen wird.`nDu solltest dringend etwas für deine Ehre tun!");
			$session['user']['playerfights']--;
		}
		else if ($session['user']['reputation']<-10){
			output("`n`8Da du aufgrund deiner Ehrenlosigkeit häufig Steine in den Weg gelegt bekommst, kannst du heute 1 Runde weniger kämpfen.");
			$session['user']['turns']--;
		}
		else if ($session['user']['reputation']>=30){
			if ($session['user']['reputation']>50) {
				$session['user']['reputation']=50;
			}
			output("`n`9Da du aufgrund deiner großen Ehrenhaftigkeit das Volk auf deiner Seite hast, kannst du heute 1 Runde und 1 Spielerkampf mehr kämpfen.");
			$session['user']['turns']++;
			$session['user']['playerfights']++;
		}
		else if ($session['user']['reputation']>10){
			output("`n`9Da du aufgrund deiner großen Ehrenhaftigkeit das Volk auf deiner Seite hast, kannst du heute 1 Runde mehr kämpfen.");
			$session['user']['turns']++;
		}
		// END Ehre

		// Newday-Hooks der Items
		// (Werden auch zur Kombo-Überprüfung eingesetzt)
		$arr_playeritems = array();

		$res = item_list_get( ' owner='.$session['user']['acctid'] );

		while($i = db_fetch_assoc($res)) {

			// Wenn angelegt oder im Inventar: Zur Kombo-Überprüfung einsetzen
			if($i['deposit1'] == 0 || $i['deposit1'] == ITEM_LOC_EQUIPPED) {
				$arr_playeritems[$i['tpl_id']] = $i['id'];
				if(!empty($i['newday_hook'])) {

					item_load_hook($i['newday_hook'],'newday',$i);

				}
			}
			else {

				if(!empty($i['newday_furniture_hook'])) {

					item_load_hook($i['newday_furniture_hook'],'newday',$i);

				}

			}

			// HOT Items:
			if($i['hot_item'] && $i['deposit1'] == 0 && $session['user']['pvpflag'] == PVP_IMMU) {
				output('`n`c`4'.$i['name'].'`4 setzt deine PvP-Immunität außer Kraft, solange du es bei dir trägst!`c`n');
			}

		}
		// END newday hooks

		// Item-Kombos
		$sql = 'SELECT * FROM items_combos WHERE type='.ITEM_COMBO_NEWDAY;
		$res = db_query($sql);

		while($c = db_fetch_assoc($res)) {

			$bool_ok = true;

			if($c['id1'] && !$arr_playeritems[$c['id1']]) {
				$bool_ok = false;
			}
			if($c['id2'] && !$arr_playeritems[$c['id2']]) {
				$bool_ok = false;
			}
			if($c['id3'] && !$arr_playeritems[$c['id3']]) {
				$bool_ok = false;
			}

			if($bool_ok) {

				if(!empty($c['hook'])) {
					item_load_hook($c['hook'],'newday',$c);
				}
				if(!$item_hook_info['hookstop']) {

					$str_buffs = ','.$c['buff'];

					item_set_buffs(ITEM_BUFF_NEWDAY | ITEM_BUFF_FIGHT,$str_buffs);

					output('`n`n`^'.$c['combo_name'].'`^ zeigt einen Effekt!`0');
				}

			}

		}
		// END Item-Kombos

		// GILDEN-UPDATE
		if($session['user']['guildfunc'] == DG_FUNC_CANCELLED) {	// Gildenhopping verhindern
			$session['user']['guildrank']--;
			if($session['user']['guildrank'] <= 0) {
				$session['user']['guildfunc'] = 0;
				$session['user']['guildrank'] = 0;
				output('`n`n`8`cDu darfst nun wieder einer Gilde beitreten!');
			}
			else {
				output('`n`n`8`cDu musst noch '.$session['user']['guildrank'].' Tage warten, ehe du wieder einer Gilde beitreten darfst!');
			}
		}

		if($session['user']['guildid']) {

			if($session['user']['guildfunc'] != DG_FUNC_APPLICANT) {
				dg_player_update($session['user']['guildid']);
				dg_player_boni($session['user']['guildid']);
			}

		}

		// END GILDEN

		//Der Fremde: Bonus und Malus
		if ($row_extra['ctitle']=="`\$Ramius ".($session['user'][sex]?"Sklavin":"Sklave")."")
		{
			output ("`n");

			if ($session['user'][reputation]<0)
			{
				output("`\$`nDein Herr, Ramius, ist begeistert von Deinen Greueltaten und gewährt Dir seine `bbesondere`b Gnade!`n");
				output("`\$Seine Gnade ist heute besonders ausgeprägt - und Du erhältst 2 zusätzliche Waldkämpfe!`n");
				$session['user'][turns]+=2;
				$session['user'][hitpoints]*=1.15;
				$session[bufflist][Ramius1] = array("name"=>"`\$Ramius' `bbesondere`b Gnade","rounds"=>200,"wearoff"=>"`\$Ramius hat Dir für heute genug geholfen.","atkmod"=>1.5,"roundmsg"=>"`\$Eine Stimme in Deinem Kopf befiehlt: `i`bZerstöre!`b Bring Leid über die Lebenden!`i","activate"=>"offense");
			}
			else
			{
				switch(e_rand(1,10))
				{
					case 1:
					case 2:
					case 3:
					case 4:
					case 5:
						output("`\$`nAls Dein Herr, Ramius, heute morgen von Deinem guten Ruf erfuhr, überlegte er, ob er Dich motivieren oder tadeln sollte ... und entschied sich fürs Motivieren.`n");
						output("`\$Seine Gnade ist heute mit Dir - und Du erhältst 2 zusätzliche Waldkämpfe!`n");
						$session['user'][turns]+=2;
						$session['user'][hitpoints]*=1.1;
						$session[bufflist][Ramius2] = array("name"=>"`\$Ramius' Gnade","rounds"=>150,"wearoff"=>"`\$Ramius hat Dir für heute genug geholfen.","atkmod"=>1.1,"roundmsg"=>"`\$Eine Stimme in Deinem Kopf befiehlt: `i`bZerstöre!`b Bring Leid über die Lebenden!`i","activate"=>"offense");
						break;
					case 6:
					case 7:
					case 8:
					case 9:
					case 10:
						output("`\$`nAls Dein Herr, Ramius, heute morgen von Deinem guten Ruf erfuhr, überlegte er, ob er Dich motivieren oder tadeln sollte ... und entschied sich fürs Tadeln.`n");
						output("`\$Sein Zorn ist heute mit Dir - und Du verlierst 2 Waldkämpfe!`n");
						$session['user'][turns]-=2;
						$session['user'][hitpoints]*=0.9;
						$session[bufflist][Ramius3] = array("name"=>"`\$Ramius' Zorn","rounds"=>200,"wearoff"=>"`\$Ramius' Zorn ist vorüber - für heute.","defmod"=>0.9,"roundmsg"=>"`\$Ramius ist zornig auf Dich!","activate"=>"offense");
						break;
				}
			}
		}
		// END Der Fremde

	}	// END normaler Newday
}




page_footer();

?>