<?php
/**
* su_board.php: Nachrichtenbretter kontrollieren
* @author talion <t@ssilo.de>
* @version DS-E V/2
*/

$str_filename = basename(__FILE__);
require_once('common.php');

// Sektionen abrufen
$str_section_select = '';

$sql = 'SELECT section FROM boards GROUP BY section ORDER BY section ASC';
$res = db_query($sql);
while($s = db_fetch_assoc($res)) {
	$str_section_select .= ','.$s['section'].','.$s['section'];
}
// END Sektionen abrufen

// Suchmaske
$arr_form = array(
					'account_id'		=>'Autor-ID ODER -Login,int',
					'section'			=>'Sektion,enum,,Alle'.$str_section_select,
					'message'			=>'Stichwortsuche in Nachrichten',
					'results_per_page'	=>'Ergebnisse pro Seite,enum,5,5,10,10,25,25,50,50,75,75,100,100'
					);
$arr_data = array(
					'account_id'		=> $_REQUEST['account_id'],
					'section'			=> $_REQUEST['section'],
					'message'			=> $_REQUEST['message'],
					'results_per_page'	=> (empty($_REQUEST['results_per_page']) ? 50 : (int)$_REQUEST['results_per_page'])
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

page_header('Schwarze-Brett-Kontrolle');

output('`c`b`&Schwarze-Brett-Kontrolle`&`b`c`n`n');

// Grundnavi erstellen
addnav('Zurück');

addnav('G?Zur Grotte','superuser.php');
addnav('W?Zum Weltlichen',$session['su_return']);

addnav('Aktionen');
addnav('Suche',$str_filename);
addnav('Neuer Eintrag',$str_filename.'?op=new');
// END Grundnavi erstellen
								


// Evtl. Fehler / Erfolgsmeldungen anzeigen
if($session['message'] != '') {
	output('`n`b'.$session['message'].'`b`n`n');
	$session['message'] = '';
}
// END Evtl. Fehler / Erfolgsmeldungen anzeigen

function show_board_search () {
	
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
										.($arr_data['account_id'] > 0 	? ' AND b.author = '.$arr_data['account_id'] : '')
										.(!empty($arr_data['section'])	? ' AND b.section = "'.$arr_data['section'].'"' : '')
										.(!empty($arr_data['message'])	? ' AND b.message LIKE "%'.$arr_data['message'].'%"' : '');
		
		$str_count_sql = '	SELECT 		COUNT( * ) AS c
							FROM		boards b'
							.$str_where;
		
		$str_data_sql = '	SELECT 		b.*,a.name
							FROM		boards b
							LEFT JOIN 	accounts a ON a.acctid = b.author '
							.$str_where.' ORDER BY postdate DESC';
							
		
		$arr_res = page_nav($str_baselnk,$str_count_sql,$arr_data['results_per_page']);
		
		$str_data_sql .= ' LIMIT '.$arr_res['limit'];
		
		$str_out .= show_board_search();			
				
		$str_out .= '`n`c<table cellpadding="2" cellspacing="2">
						';
		
		$str_tr_class = 'trlight';
		
		$res = db_query($str_data_sql);
		
		if(db_num_rows($res) == 0) {
			
			$str_out .= '`iKeine Ergebnisse gefunden!`i';
			
		}
															
		// Ergebnisse zeigen
		while($l = db_fetch_assoc($res)) {
									
			$str_out .= '<tr class="trlight">
							<td align="left">`bAcctID '.$l['author'].':`b '.(!empty($l['name']) ? $l['name'].'`0' : 'Gelöscht / System').'</td>
							<td>'.date('d.m.Y H:i:s',strtotime($l['postdate'])).' - '.date('d.m.Y H:i:s',strtotime($l['expire'])).'</td>
							<td align="right">'.$l['section'].'</td>
						</tr>
						<tr>
							<td colspan="3">`n';
																	
			$str_out .= closetags($l['message'],'`c`i`b');
								
			$str_out .= '	</td>
						</tr><tr><td colspan="3">[ '.create_lnk('`$Löschen`0',$str_filename.'?op=del&id='.$l['id'].'&ret='.urlencode($str_baselnk.$page),true,false,'Wirklich löschen?').' ]
							</td>
						</tr>
						<tr><td>&nbsp;</td></tr>';
		
		}
		
		$str_out .= '</table>`c';
		// END Ergebnisse zeigen
		
		output($str_out, true);
		
	break;
	// END Suchergebnisse
			
	case 'new':
		
		if($_GET['act'] == 'save') {
			
			if(empty($_POST['author'])) {
				$arr_entry['author'] = $session['user']['acctid']; 	
			}
			else {
				if((int)$_POST['author'] == 0) {
					$arr_tmp = db_fetch_assoc(db_query('SELECT acctid FROM accounts WHERE login="'.addslashes(stripslashes($_POST['author'])).'"'));
					$arr_entry['author'] = $arr_tmp['acctid']; 	
				}
				else {
					$arr_entry['author'] = $_POST['author']; 	
				}
			}
			
			$arr_entry['message'] = $_POST['message'];
			$arr_entry['expire'] = (empty($_POST['expire']) ? date('Y-m-d H:i:s',time()+86400) : $_POST['expire']);
			$arr_entry['postdate'] = array('sql'=>true,'value'=>'NOW()');
			$arr_entry['section'] = (empty($_POST['section_new']) ? $_POST['section'] : $_POST['section_new']);
			
			db_insert('boards',$arr_entry);
			
			redirect($str_filename);
		}
		
		$arr_new_form = array(	'author'=>'AccountID / Login als Autor,int',
								'section'=>'Sektion (Aus Liste wählen..),enum'.$str_section_select,
								'section_new'=>'Sektion (Oder neu eingeben!),text,10',
								'expire'=>'Auslaufdatum (Format: YYYY-MM-DD HH:MM:SS),text,19',
								'message'=>'Nachricht');

		$str_lnk = $str_filename.'?op=new&act=save';
		addnav('',$str_lnk);								
		output('<form method="POST" action="'.$str_lnk.'">',true);									
		showform($arr_new_form,array('author'=>$session['user']['login']));
		output('</form>',true);
			
	break;
	
	case 'del':
		
		$int_id = (int)$_GET['id'];
		
		$sql = 'DELETE FROM boards WHERE id='.$int_id;
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
