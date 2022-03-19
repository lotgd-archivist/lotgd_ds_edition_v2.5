<?php
define('WEATHER_FOGGY',1);
define('WEATHER_HOT',2);
define('WEATHER_COLD',3);
define('WEATHER_RAINY',4);
define('WEATHER_WARM',5);
define('WEATHER_COLDCLEAR',6);
define('WEATHER_WINDY',7);
define('WEATHER_TSTORM',8);
define('WEATHER_SNOWRAIN',9);
define('WEATHER_SNOW',10);
define('WEATHER_STORM',11);
define('WEATHER_HEAVY_RAIN',12);
define('WEATHER_FROSTY',13);
define('WEATHER_HAIL',14);
define('WEATHER_BOREALIS',15);
define('WEATHER_FLAMES',16);
define('WEATHER_ECLIPSE',17);
define('WEATHER_CLOUDLESS',18);
define('WEATHER_CLOUDY',19);
define('WEATHER_MUGGINESS',20);
define('WEATHER_CLOUDY_LIGHT',21);


$weather = array(
					WEATHER_FOGGY => array('name'=>'Neblig','months'=>array(1=>1, 2=>1, 3=>2, 4=>2, 5=>1, 6=>1, 7=>1, 8=>1, 9=>3, 10=>4, 11=>5, 12=>3)),
					WEATHER_HOT => array('name'=>'Heiß und sonnig','name_night'=>'Drückend schwüle Nacht','months'=>array(1=>0, 2=>0, 3=>0, 4=>0, 5=>1, 6=>2, 7=>3, 8=>4, 9=>3, 10=>0, 11=>0, 12=>0)),
					WEATHER_MUGGINESS => array('name'=>'Schwül-heiß','name_night'=>'Schwül-warme Nacht','months'=>array(1=>0, 2=>0, 3=>0, 4=>0, 5=>1, 6=>2, 7=>3, 8=>3, 9=>3, 10=>0, 11=>0, 12=>0)),
					WEATHER_COLD => array('name'=>'Wechselhaft und kühl, mit sonnigen Abschnitten','name_night'=>'Wechselhaft und kühl, mit sternenklaren Abschnitten','months'=>array(1=>1, 2=>2, 3=>2, 4=>3, 5=>1, 6=>1, 7=>1, 8=>1, 9=>2, 10=>3, 11=>4, 12=>1)),
					WEATHER_RAINY => array('name'=>'Regnerisch','months'=>array(1=>2, 2=>2, 3=>2, 4=>4, 5=>2, 6=>2, 7=>2, 8=>2, 9=>2, 10=>3, 11=>4, 12=>2)),
					WEATHER_CLOUDY => array('name'=>'Stark bewölkt','name_night'=>'Dunkle Nacht','months'=>array(1=>3, 2=>3, 3=>3, 4=>2, 5=>2, 6=>1, 7=>1, 8=>1, 9=>2, 10=>3, 11=>3, 12=>3)),
					WEATHER_CLOUDY_LIGHT => array('name'=>'Schwach bewölkt','name_night'=>'Nacht mit vereinzelten Sternen','months'=>array(1=>3, 2=>3, 3=>3, 4=>2, 5=>2, 6=>2, 7=>2, 8=>2, 9=>3, 10=>3, 11=>3, 12=>3)),
					WEATHER_WARM => array('name'=>'Warm und sonnig','name_night'=>'Lauwarme Nacht','months'=>array(1=>0, 2=>0, 3=>1, 4=>2, 5=>3, 6=>3, 7=>4, 8=>4, 9=>3, 10=>1, 11=>0, 12=>0)),
					WEATHER_CLOUDLESS => array('name'=>'Wolkenloser Himmel und Sonnenschein','name_night'=>'Wolkenloser Himmel mit glitzernden Sternen','months'=>array(1=>0, 2=>0, 3=>1, 4=>2, 5=>3, 6=>3, 7=>4, 8=>4, 9=>3, 10=>1, 11=>0, 12=>0)),
					WEATHER_COLDCLEAR => array('name'=>'Kalt bei klarem Himmel','months'=>array(1=>4, 2=>4, 3=>3, 4=>1, 5=>0, 6=>0, 7=>0, 8=>0, 9=>3, 10=>4, 11=>4, 12=>4)),
					WEATHER_WINDY => array('name'=>'Starker Wind mit vereinzelten Regenschauern','months'=>array(1=>2, 2=>2, 3=>3, 4=>3, 5=>1, 6=>2, 7=>1, 8=>1, 9=>3, 10=>4, 11=>4, 12=>3)),
					WEATHER_TSTORM => array('name'=>'Gewittersturm','months'=>array(1=>0, 2=>0, 3=>1, 4=>2, 5=>2, 6=>2, 7=>2, 8=>2, 9=>2, 10=>1, 11=>0, 12=>0)),
					WEATHER_SNOW => array('name'=>'Schneefälle','months'=>array(1=>4, 2=>4, 3=>2, 4=>1, 5=>0, 6=>0, 7=>0, 8=>0, 9=>0, 10=>1, 11=>2, 12=>4)),
					WEATHER_STORM => array('name'=>'Orkanartige Sturmböen','months'=>array(1=>2, 2=>2, 3=>2, 4=>2, 5=>1, 6=>0, 7=>0, 8=>2, 9=>3, 10=>3, 11=>3, 12=>2)),
					WEATHER_HEAVY_RAIN => array('name'=>'Sintflutartige Regenfälle','months'=>array(1=>1, 2=>1, 3=>2, 4=>2, 5=>2, 6=>1, 7=>0, 8=>0, 9=>1, 10=>2, 11=>2, 12=>2)),
					WEATHER_FROSTY => array('name'=>'Frostig mit schmerzhaft beißendem Wind','months'=>array(1=>4, 2=>4, 3=>3, 4=>1, 5=>0, 6=>0, 7=>0, 8=>0, 9=>0, 10=>3, 11=>4, 12=>4)),
					WEATHER_HAIL => array('name'=>'Starke Hagelschauer','months'=>array(1=>1, 2=>1, 3=>2, 4=>2, 5=>2, 6=>2, 7=>2, 8=>2, 9=>2, 10=>1, 11=>1, 12=>1)),
					WEATHER_BOREALIS => array('name'=>'Klarer Himmel mit seltsamem Wetterleuchten','months'=>array(1=>1, 2=>1, 3=>1, 4=>1, 5=>0, 6=>1, 7=>0, 8=>1, 9=>1, 10=>1, 11=>1, 12=>2)),
					WEATHER_FLAMES => array('name'=>'Blutroter Himmel mit leichtem Flammenregen','months'=>array(1=>0, 2=>0, 3=>0, 4=>0, 5=>0, 6=>0, 7=>0, 8=>0, 9=>0, 10=>0, 11=>0, 12=>0)),
					WEATHER_ECLIPSE => array('name'=>'Sonnenfinsternis','name_night'=>'Besonders dunkle Nacht','months'=>array(1=>0, 2=>0, 3=>0, 4=>1, 5=>0, 6=>1, 7=>0, 8=>1, 9=>0, 10=>0, 11=>1, 12=>0)),
					WEATHER_SNOWRAIN => array('name'=>'Schneeregen','months'=>array(1=>4, 2=>4, 3=>2, 4=>1, 5=>0, 6=>0, 7=>0, 8=>0, 9=>0, 10=>1, 11=>2, 12=>4))
	);

function set_weather ($weather_id=0) {

	global $weather;

	if(!$weather_id) {	// Ermitteln

		$month = (int)date('m',strtotime( getsetting('gamedate','0000-00-00') ) );
		$list = array();

		foreach($weather as $id => $w) {
			if($w['months'][$month] > 0) {
				for($i=0;$i < $w['months'][$month];$i++) {
					$list[] = $id;
				}
			}

		}

		$weather_id = $list[ e_rand(0,sizeof($list)-1) ];

	}

	savesetting('weather',$weather_id);

	return($weather[$weather_id]);

}

function get_weather () {

	global $weather;

	$time = gametime();
    $hour = (int)date('H',$time);



	$arr_w = $weather[getsetting('weather',0)];

	// Nacht
	if( ($hour > 20 || $hour < 6) && isset($arr_w['name_night']) ) {
		$arr_w['name'] = $arr_w['name_night'];
	}

	return($arr_w);

}

function get_max_hp ()
{
	global $session;

	reset($session['user']['dragonpoints']);
    $dkhp=0;
    while(list($key,$val)=each($session['user']['dragonpoints']))
	{
    	if ($val=='hp') $dkhp++;
	}
    $maxhp=getsetting('limithp',0)*$session['user'][dragonkills]+12*$session['user'][level]+5*$dkhp;
	return($maxhp);
}

function get_exp_required ($lvl,$dks,$autochallenge=false)
{
	$exparray=array(1=>100,400,1002,1912,3140,4707,6641,8985,11795,15143,19121,23840,29437,36071,43930,55000);
	foreach ($exparray as $key => $val)
	{
		$exparray[$key]= round($val + ($dks * min(0.18+$lvl*0.015,0.3) ) * $lvl * 100,0);
	}
	if($autochallenge)
	{
		$lvl++;
	}
	return($exparray[$lvl]);
}

/**
* @author talion
* @desc Steigert das Level des Spielers. Erledigt alle damit verbundenen Änderungen.
* @return int Neuen Level.
*/
function increment_level () {

	global $session;

	if (!$session['user']['prefs']['nosounds']) {
		output("<embed src=\"media/cheer.wav\" width=10 height=10 autostart=true loop=false hidden=true volume=100>",true);
	}
	$session['user']['level']++;
	$session['user']['maxhitpoints']+=10;
	$session['user']['soulpoints']+=5;
	$session['user']['attack']++;
	$session['user']['defence']++;
	$session['user']['seenmaster']=0;
	$session['user']['reputation']+=3;

	if($session['user']['balance_forest'] > 0) {	// Derzeit schwer
		// Balance nur erleichtern, wenn Spieler nicht ohnehin sehr schnell
		if( ($session['user']['age'] / $session['user']['level']) > 1) {
			// halbieren
			$session['user']['balance_forest'] = round($session['user']['balance_forest']*0.5);
		}
	}
	else {	// derzeit leicht
		// auf jeden Fall halbieren
		$session['user']['balance_forest'] = round($session['user']['balance_forest']*0.5);
	}

	output("`n`#Du steigst auf zu Level `^".$session['user']['level']."`#!`n");
	output("Deine maximalen Lebenspunkte sind jetzt `^".$session['user']['maxhitpoints']."`#!`n");
	output("Du bekommst einen Angriffspunkt dazu!`n");
	output("Du bekommst einen Verteidigungspunkt dazu!`n");
	if ($session['user']['level']<15){
		output("Du hast jetzt einen neuen Meister.`n");
	}else{
		output("Keiner im Land ist mächtiger als du!`n");
	}

	$rowe = user_get_aei('referer,refererawarded');

	if ($rowe['referer']>0 && $session['user']['level']>=getsetting('refererminlvl',5) && $session['user']['dragonkills']>=getsetting('referermindk','0') && $rowe['refererawarded']<1){
		$dp = getsetting('refererdp',50);

		$sql = "UPDATE accounts SET donation=donation+$dp WHERE acctid=".$rowe['referer'];
		db_query($sql);

		user_set_aei(array('refererawarded'=>1));

		systemmail($rowe['referer'],"`%Eine deiner Anwerbungen hat's geschafft!`0","`%{$session['user']['name']}`# ist auf Level `^{$session['user']['level']}`# aufgestiegen und du hast deine `^".$dp."`# Punkte bekommen!");
	}
	if ($session['user']['level']==10){
		$session['user']['donation']+=1;
	}
	increment_specialty();

	return($session['user']['level']);

}

/**
* @author talion
* @desc Holt Flirtstatus zwischen zwei Spielern aus Datenbank.
* @param int AccountID des einen Partners
* @param int AccountID des zweiten Partners
* @return mixed SQL-Result (bzw. false wenns schiefgeht)
*/
function flirt_get ($int_acctid1,$int_acctid2) {

	$int_acctid1 = (int)$int_acctid1;
	$int_acctid2 = (int)$int_acctid2;

	$str_ids = $int_acctid1.','.$int_acctid2;
	$str_where = 'acctid1 IN ('.$str_ids.')'. ( $int_acctid2 > 0 ? ' AND acctid2 IN ('.$str_ids.') AND acctid1!=acctid2' : '');

	$sql = 'SELECT * FROM flirts WHERE
				'.$str_where;
	$res = db_query($sql);

	if(db_error(LINK) || !db_num_rows($res)) {
		return(false);
	}

	return( $res );

}

/**
* @author talion
* @desc Verändert Flirtstatus zwischen zwei Spielern bzw. entfernt Beziehung.
*		FlirtID o. AccountIDs müssen gegeben sein!
* @param int FlirtID
* @param int AccountID des einen Partners
* @param int AccountID des zweiten Partners
* @param int Beziehungsstatus: -1 um Eintrag zu entfernen, 0 für unverändert
* @param int Flirtcount
* @return mixed FALSE wenn's schiefgeht, TRUE wenn's entfernt wurde, sonst FlirtID
*/
function flirt_set ($int_flirtid,$int_acctid1,$int_acctid2,$int_state,$int_count) {

	$int_flirtid = (int)$int_flirtid;
	$int_acctid1 = (int)$int_acctid1;
	$int_acctid2 = (int)$int_acctid2;
	$int_state = (int)$int_state;
	$int_count = (int)$int_count;

	if($int_flirtid) {
		$str_where = ' flirtid='.$int_flirtid;
	}
	else {

		if($int_acctid1 == 0 && $int_acctid2 == 0) {
			return(false);
		}
		$str_ids = $int_acctid1.','.$int_acctid2;
		$str_where = 'acctid1 IN ('.$str_ids.')'. ( $int_acctid2 > 0 ? ' AND acctid2 IN ('.$str_ids.') AND acctid1!=acctid2' : '');

	}

	// Entfernen
	if($int_state == -1) {
		$sql = 'DELETE FROM flirts WHERE '.$str_where;
		db_query($sql);
		if(!db_affected_rows()) {
			return(false);
		}
		return(true);
	}

	// Schauen, ob schon ein solcher Eintrag existiert
	// (Wenn keine Flirtid gegeben, sonst ist es logisch)
	if($int_flirtid == 0) {
		$arr_flirt = flirt_get($int_acctid1,$int_acctid2);
	}
	else {
		$arr_flirt = array('flirtid'=>$int_flirtid);
	}

	if($arr_flirt['flirtid'] > 0) {

		// Keine Änderung nötig?
		if($arr_flirt['flirtstate'] == $int_state) {
			return($arr_flirt['flirtid']);
		}

		$sql = 'UPDATE ';
	}
	else {
		$sql = 'INSERT INTO ';
	}
	$sql .= ' flirts SET flirtid=flirtid,'.($int_state > 0 ? 'flirtstate='.$int_state.'flirtcount='.$int_count : '');
	if($arr_flirt['flirtid'] > 0) {
		$sql .= ' WHERE flirtid='.$arr_flirt['flirtid'];
	}
	else {
		$sql .= ',acctid1='.$int_acctid1. ($int_acctid2 > 0 ? ',acctid2='.$int_acctid2 : '');
	}

	db_query($sql);

	if(db_error(LINK)) {
		return(false);
	}

	$int_result = ($arr_flirt['flirtid'] > 0 ? $arr_flirt['flirtid'] : db_insert_id());

	return( $int_result );

}

// Konstantendefs für Verhalten der flirt-Logik
define('FLIRT_AFFIANCE',1);			// Verlobung auslösen?
define('FLIRT_WEDDING',2);			// Heirat auslösen?
define('FLIRT_ENGAGED_DIVORCE',2);	// Bei Fremdflirt in verlobtem Status:

/**
* @author talion
* @desc Erledigt Flirtlogik: Heirat etc.
* @param int AccountID des Ziels
* @param int Verhalten,
* @return mixed FALSE wenn's schiefgeht, TRUE wenn's entfernt wurde, sonst FlirtID
*/


/**
* @author talion
* @desc Lädt Rasse aus DB in Session, falls nötig (sprich: noch nicht in Session existent).
* @param string ID der Rasse. Wenn == Spielerrasse, wird Session verwendet und Spielerrasse gesetzt.
* @param bool Wenn true, wird Array in Session auf jeden Fall überschrieben (Optional, Standard false)
* @return array Array mit Rasse
*/
function race_get ($str_race,$bool_forcereload=false) {

	global $session;

	$str_race = stripslashes($str_race);

	if(empty($str_race)) { return(array()); }

	$bool_playerrace = ($str_race == $session['user']['race'] ? true : false);
	$arr_race = array();
	$mixed_cache = cache_get('playerrace');

	if(false === $mixed_cache || !$bool_playerrace || $bool_forcereload) {
		$sql = 'SELECT * FROM races WHERE id="'.addslashes($str_race).'"';
		$result = db_query($sql);

		if (db_num_rows($result)>0){
			$arr_race = db_fetch_assoc($result);
			$arr_race['boni'] = unserialize($arr_race['boni']);
			$arr_race['specboni'] = unserialize($arr_race['specboni']);
		}

		if($bool_playerrace) {

			$mixed_cache = $arr_race;
			cache_set('playerrace',$mixed_cache);

		}
		else {
			return($arr_race);
		}

	}

	// Hier steht fest, dass wir Playerrace haben
	return($mixed_cache);

}

/**
* @author talion
* @desc Setzt Boni aktueller Rasse auf Spieler bzw. entfernt diese
* @param bool Wenn true, werden permanente Boni gesetzt, sonst temp. (Newday)
* @param bool Wenn true, werden die Boni abgenommen
* @param array Array mit Userdaten, auf die Boni angewendet werden.
*/
function race_set_boni ($bool_perm,$bool_off,&$arr_data) {

	if(sizeof($arr_data) == 0) {
		return;
	}

	if(empty($arr_data['race'])) {
		return;
	}

	$arr_race = race_get($arr_data['race']);

	$int_sign = $bool_off ? -1 : 1;

	if($bool_perm) {

		$arr_data['attack'] = max($arr_data['attack']+($arr_race['boni']['attack']*$int_sign),0);
		$arr_data['defence'] = max($arr_data['defence']+($arr_race['boni']['defence']*$int_sign),0);
		$arr_data['maxhitpoints'] = max($arr_data['maxhitpoints']+($arr_race['boni']['maxhitpoints']*$int_sign),9);

	}
	else {

		$arr_data['turns'] = max($arr_data['turns']+($arr_race['boni']['turns']*$int_sign),1);
		$arr_data['castleturns'] = max($arr_data['castleturns']+($arr_race['boni']['castleturns']*$int_sign),0);

		if(is_array($arr_race['specboni']) && sizeof($arr_race['specboni']) > 0) {

			foreach ($arr_race['specboni'] as $k=>$v) {
				$arr_data['specialtyuses'][$k.'uses'] = max($arr_data['specialtyuses'][$k.'uses']+($v*$int_sign),0);

			}
		}



	}

}

?>