<?php
/**
 * settings.lib.php: Funktionen zur allgemeinen Verwaltung der Spieleinstellungen und textuellen Gestaltung
 * @author LOGD-Core / Drachenserver-Team
 * @version DS-E V/2
*/

/**
 * Speichert Spieleinstellung in Datenbank
 *
 * @param string ID der Einstellung
 * @param mixed Einstellungswert
 * @return bool TRUE, falls Einstellung übernommen; FALSE, wenn nicht bzw. keine Änderung
 */
function savesetting($settingname,$value)
{
	global $settings;
	//Skip overhead of calling function
	if(!is_array($settings))
	{
		loadsettings();
	}
	if ($value != '')
	{
		if (!isset($settings[$settingname]))
		{
			$sql = 'INSERT INTO settings (setting,value) VALUES ("'.addslashes($settingname).'","'.addslashes($value).'")';
		}
		else
		{
			$sql = 'UPDATE settings SET value="'.addslashes($value).'" WHERE setting="'.addslashes($settingname).'"';
		}

		db_query($sql) or die(db_error(LINK));
		$settings[$settingname]=$value;
		if (db_affected_rows()>0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	return false;
}

/**
 * Lädt Inhalt der Spieleinstellungs-Tabelle aus Datenbank
 *
 */
function loadsettings()
{
	global $settings;
	//as this seems to be a common complaint, examine the execution path of this function,
	//it will only load the settings once per page hit, in subsequent calls to this function,
	//$settings will be an array, thus this function will do nothing.
	if (!is_array($settings))
	{
		$settings=array();
		$sql = 'SELECT * FROM settings';
		$result = db_query($sql) or die(db_error(LINK));

		while($row = db_fetch_assoc($result))
		{
			$settings[$row['setting']] = $row['value'];
		}
		db_free_result($result);
	}
}

/**
 * Ermittelt einzelne Spieleinstellung, lädt diese aus DB falls noch nicht geschehen.
 * Wenn Einstellung nicht vorhanden, wird sie neu angelegt
 *
 * @param string ID der Einstellung
 * @param string Standardwert, falls Einstellung noch nicht vorhanden
 * @return mixed Inhalt der Einstellung
 */
function getsetting($settingname,$default)
{
	global $settings;
	//Skip overhead of calling function
	if(!is_array($settings))
	{
		loadsettings();
	}

	if (!isset($settings[$settingname]))
	{
		savesetting($settingname,$default);
		return $default;
	}
	else
	{
		if (trim($settings[$settingname])=='')
		{
			$settings[$settingname]=$default;
		}
		return $settings[$settingname];
	}
}

/**
 * Get an extended string from the database
 *
 * @param string $str_text_id contains the id of the text
 * @param string $str_category contains the optional category, set to "*" to search in any category
 * @param bool $bool_get_as_array Set true to receive an array, else receive just the text
 * @param bool $bool_show_sulnk Set true to show an editlink for superuser
 * @param string $str_subcategory contains the optional subcategory, set to "*" to search in any subcategory
 * @param string $str_tag contains an optional tag that describes the text you're looking for
 * @return mixed - array or string (false if an error ocurred)
 */
function get_extended_text($str_text_id = false,$str_category = '*',$bool_get_as_array = false,$bool_show_sulnk = true,$str_subcategory='*',$str_tag='*')
{
	global $session;
	if($str_text_id == false || empty($str_text_id))
	{
		return '';
	}
	if($str_category == false || is_null($str_category) || empty($str_category))
	{
		$str_category = 'standard';
	}

	//Sanitize
	$str_text_id = addslashes(stripslashes($str_text_id));
	$str_category = addslashes(stripslashes($str_category));
	$str_subcategory = addslashes(stripslashes($str_subcategory));
	$str_tag = addslashes(stripslashes($str_tag));

	$str_sql_get_text = 'SELECT id,text,category,author,subcategory,tags FROM extended_text WHERE 1';
	if($str_text_id != '*')
	{
		$str_sql_get_text.= ' AND id="'.$str_text_id.'"';
	}
	if($str_category != '*')
	{
		$str_sql_get_text.= ' AND category = "'.$str_category.'"';
	}
	if($str_subcategory != '*')
	{
		$str_sql_get_text.= ' AND subcategory = "'.$str_subcategory.'"';
	}
	if($str_tag != '*')
	{
		$str_sql_get_text.= ' AND tags LIKE "%'.$str_tag.'%"';
	}

	$str_sql_get_text.= ' ORDER BY id ASC ';

	$db_result = db_query($str_sql_get_text);
	$int_count = db_num_rows($db_result);
	if($int_count==0)
	{
		return '';
	}

	$arr_return_text = array();
	$arr_one = array();

	while($arr_piece = db_fetch_assoc($db_result)) {

		$arr_piece['text'] = stripslashes($arr_piece['text']);

		//Replacing all PHP Sourcecode
		$str_temp_text = $arr_piece['text'];
		preg_match_all('/{{(.*)}}/sU',$str_temp_text,$arr_matches,PREG_SET_ORDER);
		foreach($arr_matches as $arr_match)
		{
			$arr_match[1] = eval($arr_match[1]);
			$str_temp_text = str_replace($arr_match[0],$arr_match[1],$str_temp_text);
		}

		$arr_piece['text'] = $str_temp_text;

		if(su_check(SU_RIGHT_EDITOREXTTXT) && $bool_get_as_array == false && $bool_show_sulnk)
		{
			$str_link = 'su_extended_text.php?op=edit&id='.$arr_piece['id'];
			addnav('',$str_link);
			$str_html_edit = '[ <span class="colWhiteBlack "><a href="'.$str_link.'">Ändern</a></span> ]<br clear=all />';
			$str_temp_text = $str_html_edit.$str_temp_text;
		}

		$arr_return_text[] = $arr_piece;
		$arr_one = $arr_piece;
	}

	if($bool_get_as_array == true)
	{
		if($int_count == 1) {
			return($arr_one);
		}

		return $arr_return_text;
	}
	else
	{
		return $str_temp_text;
	}
}

/**
 * Write an extended text to the database
 *
 * @param string $str_text_id contains the id of the text
 * @param string $str_text contains the text
 * @param string $str_category contains the optional category
 * @param string $str_author contains the author of the text
 * @param string $str_subcategory contains the optional subcategory
 * @param string $str_tags contains the optional tags
 * @return bool
 */
function set_extended_text($str_text_id = false,$str_text = false, $str_category = 'standard', $str_author = '', $str_subcategory = '', $str_tags = '')
{
	if($str_text_id == false || empty($str_text_id) || $str_text == false || empty($str_text))
	{
		return false;
	}
	if($str_category == false || is_null($str_category) || empty($str_category))
	{
		$str_category = 'standard';
	}

	//Sanitize
	$str_text_id = addslashes(stripslashes($str_text_id));
	$str_text = addslashes(stripslashes($str_text));
	$str_category = addslashes(stripslashes($str_category));
	$str_subcategory = addslashes(stripslashes($str_subcategory));
	$str_author = addslashes(stripslashes($str_author));
	$str_tags = addslashes(stripslashes($str_tags));

	$result = get_extended_text($str_text_id,'*');
	$str_sql_get_text = '';
	if($result != '')
	{
		$str_sql_get_text = 'UPDATE extended_text SET text="'.$str_text.'",category="'.$str_category.'",subcategory="'.$str_subcategory.'",tags="'.$str_tags.'",author="'.$str_author.'" WHERE id="'.$str_text_id.'"';
	}
	else
	{
		$str_sql_get_text = 'INSERT INTO extended_text (id,text,category,subcategory,tags,author) VALUES("'.$str_text_id.'","'.$str_text.'","'.$str_category.'","'.$str_subcategory.'","'.$str_tags.'","'.$str_author.'")';
	}
	$db_result = db_query($str_sql_get_text);
	return ($db_result==false)?false:true;
}

/**
 * Bereinigt Datenbank, entfernt überflüssige / veraltete Inhalte (s. setnewday)
 * Wird zweimal täglich erledigt, das sollte reichen
 *
 * @author LOGD-Core, modded by talion
*/
function cleanup () {

	$int_exp_content = (int)getsetting('expirecontent',180);

	if ($int_exp_content > 0)
	{
		$exp_offset = date('Y-m-d H:i:s',time() - $int_exp_content*86400);
		$sql = 'DELETE FROM commentary WHERE postdate<\''.$exp_offset.'\'';
		db_query($sql);
		$sql = 'DELETE FROM news WHERE newsdate<\''.$exp_offset.'\'';
		db_query($sql);

		// Debuglogs erst nach zweifacher Tagesanzahl löschen
		$sql = 'DELETE FROM debuglog WHERE date <\''.date( 'Y-m-d H:i:s' ,time()-( $int_exp_content*2*86400) ).'\'';
		db_query($sql);

		// Systemlogs erst nach vierfacher Tagesanzahl löschen
		$sql = 'DELETE FROM syslog WHERE date <\''.date( 'Y-m-d H:i:s' ,time()-( $int_exp_content*4*86400) ).'\'';
		db_query($sql);

		// Faillogs erst nach vierfacher Tagesanzahl löschen
		$sql = 'DELETE FROM faillog WHERE date <\''.date( 'Y-m-d H:i:s' ,time()-( $int_exp_content*4*86400) ).'\'';
		db_query($sql);
	}

	$sql = 'DELETE FROM boards WHERE expire < "'.date('Y-m-d H:i:s').'"';
	db_query($sql);

	$sql = 'DELETE FROM mail WHERE sent<\''.date('Y-m-d H:i:s',strtotime(date('r').'-'.getsetting('oldmail',14).'days')).'\'';
	db_query($sql);

	$sql = 'DELETE FROM bans WHERE (banexpire!="0000-00-00" AND banexpire<"'.date('Y-m-d').'")';
	db_query($sql);

	// Herrenlose Items löschen
	$res = item_list_get(' owner=0 AND newday_del>0 ');
	$ids = '-1';
	while($i = db_fetch_assoc($res)) {
		$ids .= ','.$i['id'];
	}
	item_delete(' id IN ('.$ids.') ');
	// END Herrenlose Items löschen

	$old = getsetting('expireoldacct',45)-5;
	$new = getsetting('expirenewacct',10);
	$trash = getsetting('expiretrashacct',1);

	// Abgelaufene Accounts: Warnungen verschicken
	$sql = 'SELECT acctid,emailaddress,login FROM accounts WHERE 1=0 '
			.($old>0?"OR (laston < \"".date('Y-m-d H:i:s', time()-($old*86400) )."\")\n":'')
			." AND (emailaddress!='' AND activated!=".USER_ACTIVATED_SENTNOTICE." AND activated!=".USER_ACTIVATED_MUTE.")";
	$result = db_query($sql);

	while($row = db_fetch_assoc($result))
	{
		if( is_email($row['emailaddress']) ) {
			mail($row['emailaddress'], getsetting('townname','Atrahor').' - Account verfällt',
			'Dein Charakter "'.$row['login'].'" in '.getsetting('townname','Atrahor').' ( http://'.$_SERVER['SERVER_NAME'].' ) verfällt demnächst und wird gelöscht. Wenn du den Charakter retten willst, solltest du dich baldmöglichst damit einloggen!',
			'From: '.getsetting('gameadminemail','postmaster@localhost.com')
			);

			$sql = 'UPDATE accounts SET activated='.USER_ACTIVATED_SENTNOTICE.' WHERE acctid=\''.$row['acctid'].'\'';
			db_query($sql);

			systemlog('`^Account ID '.$row['acctid'].', Login '.$row['login'].' wegen Inaktivität angemailt!`0',0,$row['acctid']);
		}

	}

	// Inaktive Accounts löschen
	$old+=5;
	$sql = 'SELECT acctid,login FROM accounts WHERE superuser=0 AND (1=0 '
	.($old>0?"OR (laston < \"".date('Y-m-d H:i:s',strtotime(date('r').'-'.$old.' days'))."\")\n":"")
	.($new>0?"OR (laston < \"".date('Y-m-d H:i:s',strtotime(date('r').'-'.$new.' days'))."\" AND level=1 AND dragonkills=0)\n":'')
	.($trash>0?"OR (laston < \"".date('Y-m-d H:i:s',strtotime(date('r').'-'.($trash+1).' days'))."\" AND level=1 AND experience < 10 AND dragonkills=0)\n":'')
	.')';

	$res = db_query($sql);

	while($a = db_fetch_assoc($res)) {

		if( user_delete($a['acctid']) ) {

			systemlog('`$Account ID '.$a['acctid'].', Login '.$a['login'].' wegen Inaktivität gelöscht!`0');

		}
	}

	savesetting('lastdboptimize',date('Y-m-d H:i:s'));
	$result = db_query('SHOW TABLES');
	$count = db_num_rows($result);
	$arr_table_list = array();
	for ($i=0;$i<$count;$i++)
	{
		list($key,$val)=each(db_fetch_assoc($result));
		$arr_table_list[] = '`'.$val.'`';
	}

	db_query('OPTIMIZE TABLE '.implode(',',$arr_table_list));

}

/**
 * Ruft Inhalt aus Session-Cache ab, wenn dieser valide und up-to-date
 *
 * @param string Interne ID des Caches
 * @return mixed FALSE, wenn Inhalt nicht mehr gültig, sonst Inhalt
 * @author talion
 */
function cache_get ($str_id) {

	global $session;

	if(isset($session['cache'][$str_id])) {

		if($session['cache_'.$str_id] == (int)getsetting('cache_'.$str_id,'1')) {
			return ($session['cache'][$str_id]);
		}

	}

	return (false);

}

/**
 * Speichert Inhalt in Session-Cache ab, markiert diesen als up-to-date
 *
 * @param string Interne ID des Caches
 * @param mixed Inhalte
 * @author talion
 */
function cache_set ($str_id, $mixed_content) {

	global $session;

	$session['cache'][$str_id] = $mixed_content;
	$session['cache_'.$str_id] = (int)getsetting('cache_'.$str_id,'1');

}

/**
 * Gibt Cache global frei und markiert diesen
 *
 * @param string Interne ID des Caches, leer um alle Caches freizugeben
 * @author talion
 */
function cache_release ($str_id='') {

	if(!empty($str_id)) {
		savesetting('cache_'.$str_id,time());
	}
	else {
		db_query('UPDATE settings SET value="'.time().'" WHERE setting LIKE "cache_%"');
	}

}

/**
 * Gibt Cache lokal frei
 *
 * @param string Interne ID des Caches
 * @author talion
 */
function cache_release_local ($str_id) {

	global $session;

	unset($session['cache'][$str_id]);
	unset($session['cache_'.$str_id]);

}

?>