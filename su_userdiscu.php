<?php
/**
* su_userdiscu.php: Diskussionen über User, Verwaltung von Uservergehen
* @author talion <t@ssilo.de>
* @version DS-E V/2
*/

$str_filename = basename(__FILE__);
require_once('common.php');

su_lvl_check(1,true,true);

function userdiscu_view ($arr_user) {
	global $session,$str_filename;

	output('`c`b`&Diskussion / Akte zu '.$arr_user['name'].'`& (AcctID '.$arr_user['acctid'].')`b`n');

	if($arr_user['discussion']) {
		output('(Diskussion offen)');
	}
	else {
		output('(Diskussion geschlossen)');
	}
	output('`c`n');

	// Strafen auflisten
	if($arr_user['activated'] == USER_ACTIVATED_MUTE) {
		output(' `^Geknebelt`0 ');
		if(su_check(SU_RIGHT_MUTE)) {
		//	output(' [ '.create_lnk('Entknebeln',$str_filename.'?op=mute&act=off&id='.$arr_user['acctid'],true,false,'Wirklich entknebeln?').' ] ',true);
		}
	}
	else {
		if(su_check(SU_RIGHT_MUTE)) {
		//	output(' [ '.create_lnk('Knebeln',$str_filename.'?op=mute&act=on&id='.$arr_user['acctid'],true,false,'Wirklich knebeln?').' ] ',true);
		}
	}
	if($arr_user['imprisoned'] < 0) {
		output(' `QGekerkert`0 ');
		if(su_check(SU_RIGHT_PRISON)) {
		//	output(' [ '.create_lnk('Befreien',$str_filename.'?op=prison&act=off&id='.$arr_user['acctid'],true,false,'Wirklich befreien?').' ] ',true);
		}
	}
	else {
		if(su_check(SU_RIGHT_PRISON)) {
		//	output(' [ '.create_lnk('Kerkern',$str_filename.'?op=prison&act=on&id='.$arr_user['acctid'],true,false,'Wirklich kerkern?').' ] ',true);
		}
	}
	if(checkban(false,false,false,false,$arr_user['acctid'],false)) {
		output(' `$Gebannt`0 ');
	}
	output('`n');
	// End Strafen auflisten

	$str_lnk = $str_filename.'?op=action&id='.$arr_user['acctid'].'&act=save&ret='.urlencode(calcreturnpath());
	addnav('',$str_lnk);

	output('`cBisherige Anmerkungen zu diesem Benutzer:`n');
	output('<form method="POST" action="'.$str_lnk.'">
				<textarea cols="40" rows="10" class="input" name="record">'.$arr_user['record'].'</textarea>`n
				<input type="submit" value="Speichern">
			</form>`n`n`c',true);

	addnav('Aktionen');

	if($arr_user['discussion'])	{
		$str_section = 'Discuss-'.$arr_user['acctid'];

		addcommentary(false);

		viewcommentary($str_section,"Hinzufügen:",50,"sagt");

		if(su_check(SU_RIGHT_EDITORUSER)) {
			addnav('Diskussion schließen',$str_filename.'?op=action&act=close&id='.$arr_user['acctid'].'&ret='.urlencode(calcreturnpath()));
			addnav('Akte leeren und schließen',$str_filename.'?op=action&act=del&id='.$arr_user['acctid'].'&ret='.urlencode(calcreturnpath()));
		}
	}
	else {
		addnav('Diskussion öffnen',$str_filename.'?op=action&act=open&id='.$arr_user['acctid'].'&ret='.urlencode(calcreturnpath()));
	}

	addnav('Bio','bio.php?id='.$arr_user['acctid'].'&ret=/'.urlencode(calcreturnpath()));
	if(su_check(SU_RIGHT_EDITORUSER)) {
		addnav('Usereditor','user.php?op=edit&userid='.$arr_user['acctid']);
	}

}

page_header('Userdiskussionen');

output('`c`b`&Userdiskussionen`&`b`c`n`n');

// Grundnavi erstellen
addnav('Zurück');

$str_ret = urldecode($_GET['r']);
if(!empty($str_ret)) {
	addnav('Zum Ausgangspunkt',$str_ret);
}

addnav('G?Zur Grotte','superuser.php');
addnav('W?Zum Weltlichen',$session['su_return']);

addnav('Gehe zu');
addnav('Diskussionen',$str_filename);
addnav('Neue Diskussion',$str_filename.'?op=new');
//addnav('Archiv',$str_filename.'?op=old');

// END Grundnavi erstellen


// Evtl. Fehler / Erfolgsmeldungen anzeigen
if($session['message'] != '') {
	output('`n`b'.$session['message'].'`b`n`n');
	$session['message'] = '';
}
// END Evtl. Fehler / Erfolgsmeldungen anzeigen

// MAIN SWITCH
$str_op = ($_REQUEST['op'] ? $_REQUEST['op'] : '');

switch($str_op) {

	// Neue Disku
	case 'new':

		$str_out = '';
		$int_id = (int)$_REQUEST['id'];

		// AccountID gegeben: Speichern!
		if(!empty($int_id)) {

			// Wenn zu diesem Acc bereits eine Akte existiert, dorthin weiterleiten
			if(!db_num_rows(db_query('SELECT acctid FROM account_extra_info WHERE acctid='.$int_id.' AND record<>""'))) {
				$sql = 'UPDATE account_extra_info SET discussion=1 WHERE acctid='.$int_id;
				db_query($sql);
			}

			redirect($str_filename.'?op=recent&id='.$int_id.'&r='.urlencode($_GET['r']));
		}
		else {	// Sonst: Suchformular anzeigen

			$str_lnk = $str_filename.'?op=new&r='.urlencode($_GET['r']);
			addnav('',$str_lnk);
			$str_out .= '`c<form method="POST" action="'.$str_lnk.'">';

			// Name eingegeben?
			if(!empty($_POST['search']) && strlen($_POST['search']) > 2) {


				$str_search = str_create_search_string($_POST['search']);

				$sql = 'SELECT login, acctid FROM accounts WHERE login LIKE "'.$str_search.'" ORDER BY login ASC';
				$res = db_query($sql);

				if(!db_num_rows($res)) {
					$str_out .= '`nEs wurden keine Accounts gefunden, die auf deine Eingabe passen!';
				}
				else {
					$str_out .= '<select name="id">';
					while($a = db_fetch_assoc($res)) {
						$str_out .= '<option value="'.$a['acctid'].'">'.$a['login'].'</option>';
					}
					$str_out .= '</select>';
				}

			}
			else {	// Sonst: Eingabefeld
				$str_out .= 'Name: <input type="text" name="search"> ';
			}

			$str_out .= ' <input type="submit" value="Übernehmen">
						</form>`c';

		}

		output($str_out,true);

	break;

	// Aktuelle Diskussionen
	case 'recent':

		$int_id = (int)$_GET['id'];

		$str_login = (strlen($_POST['login']) > 2 ? stripslashes($_POST['login']) : '');

		$str_search = '';

		if(!empty($str_login))
		{

			$str_search = str_create_search_string($str_login);

		}

		$str_out = '';

		$str_lnk = $str_filename.'?op=recent';

		$arr_res = page_nav($str_lnk,'SELECT COUNT(*) AS c FROM account_extra_info aei
										LEFT JOIN accounts a
										USING (acctid)
										WHERE (aei.discussion OR aei.record <> "")',100);

		$sql = 'SELECT a.name,a.acctid,aei.record,aei.discussion,a.imprisoned,a.activated
				FROM account_extra_info aei
				LEFT JOIN accounts a
				USING (acctid)
				WHERE (aei.discussion OR aei.record <> "")
				'.(!empty($str_search) ? ' AND a.login LIKE "'.$str_search.'"' : '').
				'ORDER BY aei.discussion DESC, name ASC
				LIMIT '.$arr_res['limit'];
		$res = db_query($sql);

		addnav('',$str_lnk);

		$str_out .= '<form method="POST" action="'.$str_lnk.'">
						Suche nach Login: <input type="text" maxlength="60" value="'.$str_login.'" name="login">
						<input type="submit" value="Suchen">
					</form>`n`n';

		if(!db_num_rows($res)) {
			$str_out .= 'Keine Diskussionen / Akten vorhanden!';
		}
		else {
			$str_out .= '`c`bOffene Diskussionen / Akten:`b`c';
		}

		$bool_old = false;

		while ($u = db_fetch_assoc($res)) {

			if(!$u['discussion'] && !$bool_old) {
				$str_out .= '`n`n`c`bGeschlossene Diskussionen / Akten:`b`c`n';
				$bool_old = true;
			}

			$str_out .= '`n`n<hr>`n'.create_lnk('`&'.$u['name'],$str_filename.'?op=recent&id='.$u['acctid']).'`0 ';

			if($u['discussion']) {
				$sql = 'SELECT COUNT(*) AS c,postdate FROM commentary WHERE section="Discuss-'.$u['acctid'].'" GROUP BY section ORDER BY postdate ASC';
				$arr_tmp = db_fetch_assoc(db_query($sql));
				$str_out .= '('.(!$arr_tmp['c'] ? 0 : $arr_tmp['c']).' Kommentare'.(!empty($u['record']) ? ', Akte vorhanden' : '').')';
			}

			if($int_id == $u['acctid']) {

				$str_out .= ' `bGerade geöffnet`b';

				userdiscu_view($u);

			}

		}

		output($str_out,true);

	break;

	// Archiv
	case 'old':

		$int_id = (int)$_GET['id'];

		$str_out = '';

		$sql = 'SELECT a.name,a.acctid,aei.record,aei.discussion
				FROM account_extra_info aei
				LEFT JOIN accounts a
				USING (acctid)
				WHERE aei.discussion = 0 AND aei.record <> ""
				'.(!empty($str_search) ? ' AND a.login LIKE "'.addslashes($str_search).'"' : '').
				'ORDER BY name ASC';
		$res = db_query($sql);

		$int_count = db_num_rows($res);
		if(!$int_count) {
			output('Keine Akten gefunden!`n`n');
		}
		else {
			output($int_count.' Akten gefunden:`n`n');
		}

		$str_lnk = $str_filename.'?op=old';
		addnav('',$str_lnk);

		$str_out .= '<form method="POST" action="'.$str_lnk.'">
						Suche nach Login: <input type="text" maxlength="60" value="'.$str_login.'" name="login">
						<input type="submit" value="Suchen">
					</form>`n`n';

		while ($u = db_fetch_assoc($res)) {

			$str_out .= '`n`n<hr>`n'.create_lnk($u['name'],$str_filename.'?op=old&id='.$u['acctid']).'`0 ';

			if($u['acctid'] == $int_id) {
				$str_out .= ' `bGerade geöffnet`b';

				userdiscu_view($u);
			}
		}

		output($str_out,true);


	break;

	// Diskussionsops
	case 'action':
		$int_id = (int)$_GET['id'];

		// Diskussion öffnen
		if($_GET['act'] == 'open') {
			$sql = 'UPDATE account_extra_info SET discussion=1 WHERE acctid='.$int_id;
			db_query($sql);
		}

		// Diskussion schließen
		if($_GET['act'] == 'close') {
			$sql = 'UPDATE account_extra_info SET discussion=0 WHERE acctid='.$int_id;
			db_query($sql);
		}

		// Akte leeren und Disku schließen
		if($_GET['act'] == 'del') {
			$sql = 'UPDATE account_extra_info SET discussion=0,record="" WHERE acctid='.$int_id;
			db_query($sql);
		}

		// Speichern
		if($_GET['act'] == 'save') {
			$str_record = addslashes(strip_appoencode(stripslashes($_POST['record']),3));
			$str_record = trim($str_record);
			$sql = 'UPDATE account_extra_info SET record = "'.$str_record.'" WHERE acctid='.$int_id;
			db_query($sql);
		}

		if($_GET['act'] == 'prison') {
			$sql = 'UPDATE account_extra_info SET impisoned= WHERE acctid='.$int_id;
			db_query($sql);
		}

		redirect(urldecode($_GET['ret']));

	break;

	// Hm..
	default:
		redirect($str_filename. '?op=recent');

	break;
}


page_footer();
?>
