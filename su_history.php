<?php
/**
* su_history.php: Aufzeichnungen kontrollieren
* @author talion <t@ssilo.de>
* @version DS-E V/2
*/

$str_filename = basename(__FILE__);
require_once('common.php');

// Suchmaske
$arr_form = array(
					'account_id'		=>'Account-ID / -Login und/oder..,int',
					'guild_id'			=>'..Gilden-ID oder..,int',
					'world'				=>'..Globale Aufzeichnungen,bool',
					'msg'				=>'Stichwortsuche in Nachrichten',
					'results_per_page'	=>'Ergebnisse pro Seite,enum,5,5,10,10,25,25,50,50,75,75,100,100'
					);
$arr_data = array(
					'account_id'		=> $_REQUEST['account_id'],
					'guild_id'			=> $_REQUEST['guild_id'],
					'world'				=> $_REQUEST['world'],
					'msg'				=> $_REQUEST['msg'],
					'results_per_page'	=> (empty($_REQUEST['results_per_page']) ? 100 : (int)$_REQUEST['results_per_page'])
					);
					
if( (int)$arr_data['account_id'] == 0 && !empty($arr_data['account_id']) ) {
	$arr_tmp = db_fetch_assoc(db_query('SELECT acctid FROM accounts WHERE login="'.$arr_data['account_id'].'" LIMIT 1'));
	if($arr_tmp['acctid'] > 0) {
		$arr_data['account_id'] = $arr_tmp['acctid'];
	}
	else {
		$arr_data['account_id'] = 0;
	}	
}
// END Suchmaske						

page_header('Aufzeichnungen - Kontrolle');

output('`c`b`&Aufzeichnungen - Kontrolle`&`b`c`n`n');

// Grundnavi erstellen
addnav('Zurück');

addnav('G?Zur Grotte','superuser.php');
addnav('W?Zum Weltlichen',$session['su_return']);

addnav('Aktionen');
addnav('Suche',$str_filename);
addnav('Neuer Eintrag',$str_filename.'?op=edit');
// END Grundnavi erstellen
								


// Evtl. Fehler / Erfolgsmeldungen anzeigen
if($session['message'] != '') {
	output('`n`b'.$session['message'].'`b`n`n');
	$session['message'] = '';
}
// END Evtl. Fehler / Erfolgsmeldungen anzeigen

function show_history_search () {
	
	global $str_filename,$arr_form,$arr_data,$str_type;
	
	$str_out = '';
	
	$str_lnk = $str_filename.'?op=search';
		
	addnav('',$str_lnk);
				
	$str_out .= '<form method="POST" action="'.$str_lnk.'">';
				
	// Suchmaske zeigen
	$str_out .= generateform($arr_form,$arr_data,false,'Suchen!');
			
	$str_out .= '</form><hr />';
	
	return($str_out);
	
}

// MAIN SWITCH
$str_op = ($_REQUEST['op'] ? $_REQUEST['op'] : '');

switch($str_op) {
	
	// Suchergebisse
	case 'search':	
				
		$str_baselnk = $str_filename . '?op=search&';
		foreach($arr_data as $key => $val) {
			$str_baselnk .= $key.'='.urlencode($val).'&';			
		}
			
		$str_where = 	'	WHERE 		1 '
										.($arr_data['account_id'] > 0 	? ' AND h.acctid = '.$arr_data['account_id'] : '')
										.($arr_data['guild_id'] > 0 	? ' AND h.guildid = '.$arr_data['guild_id'] : '')
										.($arr_data['world'] > 0 		? ' AND h.guildid = 0 AND h.acctid = 0' : '')
										.(!empty($arr_data['msg'])	? ' AND h.msg LIKE "%'.$arr_data['msg'].'%"' : '');
		
		$str_count_sql = '	SELECT 		COUNT( * ) AS c
							FROM		history h'
							.$str_where;
		
		$str_data_sql = '	SELECT 		h.*,a.name AS aname,g.name AS gname
							FROM		history h
							LEFT JOIN 	accounts a ON a.acctid = h.acctid
							LEFT JOIN 	dg_guilds g ON g.guildid = h.guildid '
							.$str_where;
							
		
		$arr_res = page_nav($str_baselnk,$str_count_sql,$arr_data['results_per_page']);
		
		$str_data_sql .= ' LIMIT '.$arr_res['limit'];
		
		$str_out .= show_history_search();			
				
		$str_out .= '`n`c<table cellpadding="4" cellspacing="4">
							<tr class="trhead">
								<td>`bReal-/Spieldatum`b</td>
								<td>`bSpieler`b</td>
								<td>`bGilde`b</td>
							</tr>
						';
		
		$str_tr_class = 'trlight';
		
		$res = db_query($str_data_sql);
		
		if(db_num_rows($res) == 0) {
			
			$str_out .= '`iKeine Ergebnisse gefunden!`i';
			
		}
															
		// Ergebnisse zeigen
		while($l = db_fetch_assoc($res)) {

			$str_tr_class = ($str_tr_class == 'trlight' ? 'trdark' : 'trlight');

			$l['aname'] = empty($l['aname']) ? ' - ' : $l['aname'];
			$l['gname'] = empty($l['gname']) ? ' - ' : $l['gname'];
					
			$str_out .= '<tr class="'.$str_tr_class.'">
							<td>'.date('d.m.Y H:i:s',strtotime($l['date'])).' / '.getgamedate($l['gamedate']).'</td>
							<td>'.$l['aname'].'</td>
							<td>'.$l['gname'].'</td>
						</tr>';
			
			$str_out .= '<tr class="'.$str_tr_class.'">
							<td colspan="2">'.$l['msg'].'</td>
							<td align="right">
								[ '.create_lnk('Edit',$str_filename.'?op=edit&id='.$l['id'].'&ret='.urlencode($str_baselnk.$page)).' ] 
								[ '.create_lnk('`$Löschen`0',$str_filename.'?op=del&id='.$l['id'].'&ret='.urlencode($str_baselnk.$page),true,false,'Wirklich löschen?').' ]
							</td>
						</tr>
						';
		
		}
		
		$str_out .= '</table>`c';
		// END Ergebnisse zeigen
		
		output($str_out, true);
		
	break;
	// END Suchergebnisse
			
	case 'edit':
		
		$int_id = (int)$_GET['id'];
		
		if($_GET['act'] == 'save') {
			
			if(empty($_POST['acctid'])) {
				$arr_entry['acctid'] = $session['user']['acctid']; 	
			}
			else {
				if((int)$_POST['acctid'] == 0) {
					$arr_tmp = db_fetch_assoc(db_query('SELECT acctid FROM accounts WHERE login="'.addslashes(stripslashes($_POST['acctid'])).'"'));
					$arr_entry['acctid'] = $arr_tmp['acctid']; 	
				}
				else {
					$arr_entry['acctid'] = $_POST['acctid']; 	
				}
			}
			
			$arr_entry['guildid'] = (int)$_POST['guildid'];
			$arr_entry['msg'] = $_POST['msg'];
			$arr_entry['gamedate'] = (empty($_POST['gamedate']) ? getsetting('gamedate','') : $_POST['gamedate']);
			$arr_entry['date'] = array('sql'=>true,'value'=>'NOW()');

			if(!$int_id) {
				db_insert('history',$arr_entry);
			}
			else {
				$sql = 'UPDATE history 
						SET gamedate="'.$arr_entry['gamedate'].'",msg="'.addslashes(stripslashes($arr_entry['msg'])).'",guildid='.$arr_entry['guildid'].',acctid='.$arr_entry['acctid'].' 
						WHERE id='.$int_id;
				db_query($sql);
			}
			
			redirect($str_filename);
		}
		
		$arr_new_form = array(	'acctid'=>'AccountID / Login,int',
								'guildid'=>'GildenID,int',
								'gamedate'=>'Spieldatum (Format: YYYY-MM-DD)',
								'msg'=>'Nachricht');
		
		if($int_id) {
			$arr_data = db_fetch_assoc(db_query('SELECT * FROM history WHERE id='.$int_id));
		}
		else {
			$arr_data = array();
		}
								
		$str_lnk = $str_filename.'?op=edit&act=save&id='.$int_id;
		addnav('',$str_lnk);								
		output('`&Um Aufzeichnung als Weltnachricht einzutragen, AccountID und GildenID leer lassen.
				<form method="POST" action="'.$str_lnk.'">',true);									
		showform($arr_new_form,$arr_data);
		output('</form>',true);
		
		if(!empty($_GET['ret'])) {
			addnav('Abbruch',urldecode($_GET['ret']));
		}
			
	break;
	
	case 'del':
		
		$int_id = (int)$_GET['id'];
		
		$sql = 'DELETE FROM history WHERE id='.$int_id;
		db_query($sql);
		
		if(db_affected_rows(LINK)) {
			$session['message'] = '`@Eintrag '.$int_id.' erfolgreich gelöscht!`0';
		}
		else {
			$session['message'] = '`$Fehler bei Löschen des Eintrags '.$int_id.'!`0';
		}
		
		redirect( urldecode($_GET['ret']) );
		
	break;
				
	// Hm..		
	default:
		redirect($str_filename. '?op=search');

	break;
}


page_footer();
?>
