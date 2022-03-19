<?php
/**
 * nav.lib.php: Navigationsfunktionen
 * @author LOGD-Core / Drachenserver-Team
 * @version DS-E V/2
*/

// Einstellungsarrays für Navs
$allowanonymous=array('index.php'=>true,'login.php'=>true,'create.php'=>true,'create_rules.php'=>true,'about.php'=>true,'list.php'=>true,'petition.php'=>true,'connector.php'=>true,'logdnet.php'=>true,'referral.php'=>true,'news.php'=>true,'motd.php'=>true,'topwebvote.php'=>true,'source.php'=>true);
$allownonnav = array('badnav.php'=>true,'motd.php'=>true,'petition.php'=>true,'mail.php'=>true,'topwebvote.php'=>true,'chat.php'=>true,'source.php'=>true,'watchsu.php'=>true,'comment_funcs.php'=>true,'prefs_new.php'=>true,'music.php'=>true,'su_comment.php'=>true,'su_help.php'=>true,'multi.php'=>true);
$nokeeprestore=array('newday.php'=>1,'badnav.php'=>1,'motd.php'=>1,'mail.php'=>1,'petition.php'=>1,'chat.php'=>1,'comment_funcs.php'=>1,'prefs_new.php'=>1,'music.php'=>true,'su_comment.php'=>true,'su_help.php'=>true,'multi.php'=>true);

$accesskeys=array();
$quickkeys=array();
/**
 * Fügt Navipunkt zu Liste erlaubter Navs + evtl. Navigation dazu
 *
 * @param string Text für Menüpunkt; Leer, um keinen Menüpunkt zu erzeugen
 * @param string Link, FALSE um Überschrift einzubauen
 * @param bool HTML erlauben?
 * @param bool Als Popup öffnen?
 * @param bool In neuem Fenster öffnen?
 * @param bool Hotkey aktivieren?
 * @return Link mit c-Info
 */
function addnav($text,$link=false,$priv=false,$pop=false,$newwin=false,$hotkey=true)
{
	global $nav,$session,$accesskeys,$REQUEST_URI,$quickkeys;
	if ($link===false)
	{
		$nav.=templatereplace('navhead',array('title'=>appoencode($text,$priv)));
	}
	elseif (empty($link))
	{
		$nav.=templatereplace('navhelp',array('text'=>appoencode($text,$priv)));
	}
	else
	{
		if (!empty($text))
		{
			$extra='';
			if ($newwin===false)
			{
				if (strpos($link,'?'))
				{
					$extra='&c='.$session['counter'];
				}
				else
				{
					$extra='?c='.$session['counter'];
				}
			}

			if ($newwin===false)
			{
				$extra.='-'.date('His');
			}
			//$link = str_replace(" ","%20",$link);
			//hotkey for the link.
			if($hotkey) {
				$key='';
				if (substr($text,1,1)=='?')
				{
					// check to see if a key was specified up front.
					if ($accesskeys[strtolower(substr($text, 0, 1))]==1)
					{
						// output ("key ".substr($text,0,1)." already taken`n");
						$text = substr($text,2);
					}
					else
					{
						$key = substr($text,0,1);
						$text = substr($text,2);
						//output("key set to $key`n");
						$found=false;
						$int_strlen = strlen($text);
						for ($i=0;$i<$int_strlen; $i++)
						{
							$char = substr($text,$i,1);
							if ($ignoreuntil == $char)
							{
								$ignoreuntil='';
							}
							else
							{
								if ($ignoreuntil<>'')
								{
									if ($char=='<') $ignoreuntil='>';
									if ($char=='&') $ignoreuntil=';';
									if ($char=='`') $ignoreuntil=substr($text,$i+1,1);
								}
								else
								{
									if ($char==$key)
									{
										$found=true;
										break;
									}
								}
							}
						}
						if ($found==false)
						{
							if (strpos($text, '__') !== false)
							{
								$text=str_replace('__', '('.$key.') ', $text);
							}
							else
							{
								$text='('.strtoupper($key).') '.$text;
							}
							$i=strpos($text, $key);
							// output("Not found`n");
						}
					}
				}
				if (empty($key))
				{
					$int_strlen = strlen($text);
					for ($i=0;$i<$int_strlen; $i++)
					{
						$char = substr($text,$i,1);
						if ($ignoreuntil == $char)
						{
							$ignoreuntil='';
						}
						else
						{
							if (($accesskeys[strtolower($char)]==1) || (strpos('abcdefghijklmnopqrstuvwxyz0123456789', strtolower($char)) === false) || $ignoreuntil<>'')
							{
								if ($char=='<') $ignoreuntil='>';
								if ($char=='&') $ignoreuntil=';';
								if ($char=='`') $ignoreuntil=substr($text,$i+1,1);
							}
							else
							{
								break;
							}
						}
					}
				}
				if ($i<strlen($text))
				{
					$key=substr($text,$i,1);
					$accesskeys[strtolower($key)]=1;
					$keyrep=' accesskey="'.$key.'" ';
				}
				else
				{
					$key='';
					$keyrep='';
				}
				//output("Key is $key for $text`n");

				if ($key!='')
				{
					$text=substr($text,0,strpos($text,$key)).'`H'.$key.'`H'.substr($text,strpos($text,$key)+1);
					if ($pop)
					{
						$quickkeys[$key]=popup($link.$extra);
					}
					else
					{
						$quickkeys[$key]="window.location='$link$extra'";
					}
				}
			}
			$nav.=templatereplace('navitem',array(
			"text"=>appoencode($text,$priv),
			"link"=>HTMLEntities($link.$extra),
			"accesskey"=>$keyrep,
			"popup"=>($pop==true ? "target='_blank' onClick=\"".popup($link.$extra)."; return false;\"" : ($newwin==true?"target='_blank'":""))
			));
			//$nav.="<a href=\"".HTMLEntities($link.$extra)."\" $keyrep class='nav'>".appoencode($text,$priv)."<br></a>";
		}
		$session['allowednavs'][$link.$extra]=true;
		$session['allowednavs'][str_replace(' ', '%20', $link).$extra]=true;
		$session['allowednavs'][str_replace(' ', '+', $link).$extra]=true;

		return($link.$extra);
	}
}

/**
 * Setzt die Liste der erlaubten Navs zurück.
 *
 */
function clearnav()
{
	$session['allowednavs']=array();
}

/**
 * Leitet Spieler auf angegebene Seite weiter, nimmt Speicherung der Userdaten vor.
 *
 * @param string Link, auf den weitergeleitet wird
 * @param string Grund für Weiterleitung (optional)
 */
function redirect($location,$reason=false)
{
	global $session,$REQUEST_URI;

	if ($location!='badnav.php')
	{
		$session[allowednavs]=array();
		addnav('',$location);
	}
	if (strpos($location,'badnav.php')===false) $session['output']="<a href=\"".HTMLEntities($location)."\">Hier klicken</a>";
	$session['debug'].="Redirected to $location from $REQUEST_URI.  $reason\n";
	saveuser();
	header("Location: $location");
	echo $location;
	echo $session['debug'];
	exit();
}

/**
 * Zeigt Waldnavigation an.
 *
 * @param bool Beschreibungskopf für den Wald zeigen (optional, Standard false)
 */
function forest($noshowmessage=false)
{
	global $session,$playermount;
	$conf = unserialize($session['user']['donationconfig']);
	if ($conf['healer'] || $session['user']['acctid']==getsetting('hasegg',0) || ($session['user']['marks']>=31))
	{
		addnav('H?Golindas Hütte','healer.php');
	}
	else
	{
		addnav('H?Hütte des Heilers','healer.php');
	}
	addnav('Kampf');
	addnav('B?Etwas zum Bekämpfen suchen','forest.php?op=search');
	if ($session['user']['level']>1)
	{
		addnav('e?Herumziehen','forest.php?op=search&type=slum');
	}
	addnav('N?Nervenkitzel suchen','forest.php?op=search&type=thrill');

	if($session['user']['dragonkills'] >= 50 && $session['user']['level'] <= 14) {
		addnav('l?Hölle suchen','forest.php?op=search&type=extreme');
	}

	addnav('Sonderbare Orte');
	addnav('W?Die Waldlichtung','waldlichtung.php');
	addnav('k?Der dunkle Pfad','thepath.php');

	$admin = su_check(SU_RIGHT_COMMENT);

	// Rassenräume
	$arr_race = race_get($session['user']['race']);

	// Wenn Rassenraum im Wald
	if($arr_race['raceroom'] == 1) {
		addnav($arr_race['raceroom_nav'],'racesspecial.php?race='.$arr_race['id']);
	}

	// Wenn Spieler alle Rassenräume betreten kann
	if($arr_race['raceroom_all'] || su_check(SU_RIGHT_COMMENT)) {

		$sql = 'SELECT id,raceroom_nav,raceroom FROM races WHERE raceroom=1 AND id != "'.$session['user']['race'].'"';
		$res = db_query($sql);

		while($r = db_fetch_assoc($res)) {
			addnav($r['raceroom_nav'],'racesspecial.php?race='.$r['id'],false,false,false,false);
		}

	}

	//if ($session[user][hashorse]>=2) addnav('D?Dark Horse Tavern",'forest.php?op=darkhorse");
	if ($playermount['tavern']>0) addnav('a?Nimm '.$playermount['mountname'].' zur Dark Horse Taverne','forest.php?op=darkhorse');
	if ($playermount['tavern']>0 && $conf['castle']) addnav('r?Nimm '.$playermount['mountname'].' zur Burg','forest.php?op=castle');
	if ($conf['goldmine']>0) addnav('o?Goldmine ('.$conf[goldmine].'x)','paths.php?ziel=goldmine&pass=conf');

	addnav('','forest.php');
	if ($session['user']['level']>=15  && $session['user']['seendragon']==0){
		addnav('G?`@Den Grünen Drachen suchen','forest.php?op=dragon');
	}
	addnav('Sonstiges');

	//Knappe bringt Gold zur Bank
	$arr_disc = $session['bufflist']['decbuff'];
	if (isset($arr_disc) && $arr_disc['state'] == 15 && $session['user']['gold']>0)
	{
	    addnav('s?'.$arr_disc['realname'].' `&zur Bank schicken','forest.php?op=senddisciple');
	}
	//
    addnav('D?Zurück zum Dorf','village.php');
	addnav('M?Zurück zum Marktplatz','market.php');
	addnav('P?Plumpsklo','outhouse.php');
	if ($session['user']['turns']<=1 )
	{
		addnav('x?Hexenhaus','hexe.php');
	}
	if ($noshowmessage!=true){
        if ($session['user']['prefs']['noimg']==1) output('`c`7`bDer Wald`b`0`c`n');
		output('
		Der Wald, Heimat von bösartigen Kreaturen und üblen Übeltätern aller Art.`n`n
		Die dichten Blätter des Waldes erlauben an den meisten Stellen nur wenige Meter Sicht.
		Die Wege würden dir verborgen bleiben, hättest du nicht ein so gut geschultes Auge. Du bewegst dich so leise wie
		eine milde Brise über den dicken Humus, der den Boden bedeckt. Dabei versuchst du es zu vermeiden
		auf dünne Zweige oder irgendwelche der ausgebleichten Knochenstücke zu treten, welche den Waldboden spicken.
		Du verbirgst deine Gegenwart vor den abscheulichen Monstern, die den Wald durchwandern.');

        if ($session['user']['turns']<=1)
		{
			output(' In der Nähe siehst du wieder den Rauch aus dem Kamin eines windschiefen Hexenhäuschens aufsteigen, von dem du schwören könntest, es war eben noch nicht da. ');
		}
	}

	// Imagemap by Maris
	if ($session['user']['prefs']['noimg']==0)
	{
			 $gen_output='`c`7`bDer Wald`b`0`c`n<div><map name="Der Wald">';
			 if ($session['user']['level']>1)
			 {
			 $gen_output.='<area shape="rect" coords="30,150,100,40" href="forest.php?op=search&type=slum" title="Herumziehen">';
			 addnav('','forest.php?op=search&type=slum');
			 }
			 $gen_output.='<area shape="rect" coords="170,160,260,50" href="forest.php?op=search" title="Etwas zum Bekämpfen suchen">
			 <area shape="rect" coords="310,190,380,60" href="forest.php?op=search&type=thrill" title="Nervenkitzel">';
			 addnav('','forest.php?op=search');
			 addnav('','forest.php?op=search&type=thrill');
			 if($session['user']['dragonkills'] >= 50 && $session['user']['level'] <= 14)
			 {
			 $gen_output.='<area shape="rect" coords="470,190,520,100" href="forest.php?op=search&type=extreme" title="Hölle suchen">';
			 addnav('','forest.php?op=search&type=extreme');
			 }
			 $gen_output.='</map></div>`n<p><center><img border="0" src="images/forest.jpg" usemap="#Der Wald"></center></p>`n';
			 headoutput($gen_output,true);
	}
	// Ende Imagemap

	//Changed to adapt the walspecialeditor needs
	if (su_check(SU_RIGHT_FORESTSPECIAL))
	{
		output('`n`nSUPERUSER Specials:`n');
		$query_result = db_query('SELECT filename FROM waldspecial ORDER BY filename ASC') or die(db_error(LINK));
		$count = db_num_rows($query_result);
		for ($i=0;$i<$count;$i++)
		{
			$row = db_fetch_assoc($query_result);
			output('<a href="forest.php?specialinc='.$row['filename'].'">'.$row['filename'].'</a>`n', true);
			addnav('','forest.php?specialinc='.$row['filename']);
		}
	}
}

/**
* @author talion
* @desc Unterstützt Erstellung seitenübergreifender Inhalte durch Bereitstellung einer
*		Seiten-Navi (Verwendet dazu GET['page'])
* @param string Basisadresse (mitsamt aller Params bis auf page, ohne Bindezeichen! [? oder &])
* @param mixed Wenn String: Count-SQL (Für COUNT Alias c verwenden!), wenn int: Gesamtanzahl der Tupel
* @param int Ergebnisse pro Seite (Optional, Standard 50)
* @param string Überschrift für Seitenliste in Navi, Leer für inaktiv (Optional, Standard 'Seiten')
* @param string Seitenbezeichnung in Navi (Optional, Standard 'Seite')
* @param bool Von-Bis hinter Navipunkt anzeigen? (Optional, Standard TRUE)
* @return array Max. Seitenzahl (maxpage), Gesamtzahl (count), aktuelle Seite (page),
				LIMIT-String für den Daten-Query ohne LIMIT-Keyword (limit), Ergebnis von (from), Ergebnis bis (to)
*/
function page_nav ($str_baselnk, $count, $int_rpp=50, $str_caption='Seiten', $str_site='Seite', $bool_range=true) {

	// Navi-Link ermitteln
	$str_baselnk = preg_replace('/([?&]page=[0-9]*)/','',$str_baselnk);
	$str_last_sign = substr($str_baselnk,strlen($str_baselnk)-1);
	if($str_last_sign != '&' && $str_last_sign != '?') {
		$str_baselnk .= (strpos($str_baselnk,'?') ? '&' : '?');
	}
	$str_baselnk .= 'page=';

	// Gesamtanzahl ermitteln
	$int_count = 0;
	if(is_string($count)) {
		$arr_count = db_fetch_assoc(db_query($count));
		$arr_data['count'] = $arr_count['c'];
	}
	else {
		$arr_data['count'] = (int)$count;
	}

	//if(!$arr_data['count']) {return(false);}

	// Aktuelle Seite ermitteln
	$arr_data['page'] = (int)$_REQUEST['page'];
	$arr_data['page'] = ($arr_data['page'] == 0 ? 1 : $arr_data['page']);

	// Max. Seite ermitteln
	$arr_data['maxpage'] = ceil($arr_data['count'] / $int_rpp);

	// LIMIT-String erstellen
	$arr_data['from'] = ($arr_data['page'] - 1) * $int_rpp;
	$arr_data['to'] = min($arr_data['page'] * $int_rpp,$arr_data['count']);
	$arr_data['limit'] = $arr_data['from'].','.$int_rpp;

	if($arr_data['maxpage']) {

		// Übermaß an Navis vermeiden
		if($arr_data['maxpage'] > 60) {
			addnav('Seitengruppen');

			for($i=1; $i<=$arr_data['maxpage']; $i+=60) {

				$int_page_to = min($i + 59, $arr_data['maxpage']);

				$int_from = ($i-1) * $int_rpp + 1;
				$int_to = min($int_page_to * $int_rpp, $arr_data['count']);

				addnav( $str_site.' '.$i.' - '.$int_page_to.' '.($bool_range ? ' ('.$int_from.' - '.$int_to.')' : ''), $str_baselnk.$i);

			}
			$int_page_from = floor( ($arr_data['page']-1) / 60) * 60 + 1;
			$int_page_to = min($arr_data['maxpage'], ceil($arr_data['page'] / 60) * 60 );
		}
		else {
			$int_page_to = $arr_data['maxpage'];
			$int_page_from = 1;
		}

		if(!empty($str_caption)) {addnav($str_caption);}

		// Seitennavi erstellen
		for($i=$int_page_from; $i<=$int_page_to; $i++) {

			$int_from = ($i-1) * $int_rpp + 1;
			$int_to = min($i * $int_rpp, $arr_data['count']);

			addnav( ($i == $arr_data['page'] ? '`^': '').$str_site.' '.$i.($bool_range ? ' ('.$int_from.' - '.$int_to.')' : ''), $str_baselnk.$i);

		}
	}

	return($arr_data);

}

/**
 * Speichert Link auf Seite, um Zurücklink o.ä. anzubieten.
 *
 * @param string Link (wird mit calcreturnpath behandelt)
 */
function set_restorepage_history ($str_val) {
	
	global $session;
			
	$session['user']['prefs']['restore_history'] = calcreturnpath($str_val);
	
}

/**
 * Gibt gespeicherten Zurücklink zurück
 *
 * @return string Link
 */
function get_restorepage_history () {
	
	global $session;
	
	if(isset($session['user']['prefs']['restore_history'])) {
		return ($session['user']['prefs']['restore_history']);
	}
	else {
		return ('');
	}
	
}

?>