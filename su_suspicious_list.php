<?php
// Editor für verdächtige Begriffe in Mails
// by Maris (Maraxxus@gmx.de)
// Vorlage : su_blacklist

$str_filename = basename(__FILE__);
require_once('common.php');
su_check(SU_RIGHT_EDITORUSER,true);

page_header('Editor für verdächtige Worte in Brieftauben');

output('`c`b`&Liste bearbeiten`&`b`c`n`n');

// Grundnavi erstellen
addnav('Zurück');
addnav('T?Zum Brieftaubenamt','su_mails.php');
addnav('W?Zum Weltlichen',$session['su_return']);

$str_ret = urldecode($_REQUEST['ret']);
if(!empty($str_ret)) {
	addnav('Zurück', $str_ret);
}
addnav('Aktionen');
addnav('Neuer Eintrag',$str_filename.'?op=edit_bl&ret='.urlencode($str_ret));
addnav('Liste',$str_filename.'?ret='.urlencode($str_ret));
// END Grundnavi erstellen

// Evtl. Fehler / Erfolgsmeldungen anzeigen
if($session['message'] != '') {
	output('`n`b'.$session['message'].'`0`b`n`n');
	$session['message'] = '';
}

// MAIN SWITCH
$str_op = ($_REQUEST['op'] ? $_REQUEST['op'] : '');

switch($str_op) {
    case 'edit_bl':

		$arr_form = array();
		$arr_data = array();

		$int_id = (int)$_REQUEST['id'];

		// Fehlgeschlagener Eintrag
		if(!empty($session['bl_post'])) {
			$arr_data = $session['bl_post'];
			unset($session['bl_post']);
		}
		else if($int_id > 0) {
			$sql = 'SELECT * FROM suspicious_words WHERE id='.$int_id;
			$arr_data = db_fetch_assoc(db_query($sql));

			$int_type = $arr_data['type'];
			unset($arr_data['type']);

		}
		// Keine Daten gegeben
		else {

		}

		$arr_form = array(
							'id'=>'EintragID,hidden',
							'name'=>'Eintrag:',
						);

		$str_lnk = $str_filename.'?op=insert_bl&ret='.urlencode($str_ret);
		addnav('',$str_lnk);

		$str_out = '`c<form method="POST" action="'.$str_lnk.'">';

		$str_out .= generateform($arr_form,$arr_data,false,'Speichern!');

		$str_out .= '</form>`c';

		output($str_out, true);

	break;

	
	// Eintrag einfügen
	case 'insert_bl':
						
		$int_type = 0;
		$int_id = (int)$_REQUEST['id'];
				
		$str_value = strtolower($_POST['name']);
		$str_remarks = $_POST['remarks'];
		
		// Auf doppelte Einträge checken
		if(!$int_id) {
			$sql = 'SELECT id FROM suspicious_words WHERE LOWER(name)="'.$str_value.'"';
			if(db_num_rows(db_query($sql))) {
				$session['message'] = '`$Ein identischer Eintrag besteht bereits!`0';
				$session['bl_post'] = $_POST;
				redirect($str_filename.'?op=edit_bl&ret='.urlencode($str_ret));	
			}
		}
						
		// Eintrag
		if($int_id == 0) {
			$sql = 'INSERT INTO ';
		}
		else {
			$sql = 'UPDATE ';
		}
		$sql .= 'suspicious_words SET name="'.$str_value.'"';
		$sql .= ($int_id ? ' WHERE id='.$int_id : '');
		
		db_query($sql);
		
		$int_id =  (!$int_id ? db_insert_id() : $int_id);
						
		if(db_error(LINK) || db_affected_rows() < 0) {	
			$session['message'] = '`$Fehler bei Bearbeiten des Eintrags ID '.$int_id.': '.db_error(LINK).'`n';
		}	
		else {
			$session['message'] = '`@Eintrag ID '.$int_id.' erfolgreich getätigt!`0'; 
			cache_release('suspicious_words');
		}
		redirect($str_filename.'?ret='.urlencode($str_ret));
		
	break;
	
	// Eintrag löschen
	case 'del_bl':
		$int_id = (int)$_GET['id'];
		
		$sql = 'DELETE FROM suspicious_words WHERE id='.$int_id;
		db_query($sql);
		
		if( db_error(LINK) ) {
			$session['message'] = '`$Fehler bei Löschen des Eintrags ID '.$int_id.':`n';
		}	
		else {
			$session['message'] = '`@Eintrag ID '.$int_id.' erfolgreich gelöscht!`0'; 		
			cache_release('suspicious_words');
		}
		redirect($str_filename.'?ret='.urlencode($str_ret));
		
	break;
		
	// Liste
	default:
		
		$count_sql = 'SELECT COUNT(*) AS c FROM suspicious_words ORDER BY name';
		$arr_res = page_nav($str_filename, $count_sql, 100);
		
		$sql = 'SELECT * FROM suspicious_words ORDER BY name LIMIT '.$arr_res['limit'];
		$res = db_query($sql);
		
		addnav('',$str_filename);

        $str_out .= '`c<table cellspacing="3" cellpadding="3"></tr>';
							
		if(db_num_rows($res) == 0) {
			$str_out .= '<tr><td colspan="5">`iKeine Einträge vorhanden!`i</td></tr>';
		}
		else{
			$str_out .= '<tr><td colspan="5">`i'.$arr_res['count'].' Einträge vorhanden!`i</td></tr>';
			$str_out .= '`b';
		}
		
		$c_first = 0;
		while($b = db_fetch_assoc($res)) {
           
			$str_trclass = ($str_trclass == 'trlight' ? 'trdark' : 'trlight');
			$str_out .= '<tr class="'.$str_trclass.'">';
			$str_out .= '<td>
							
							['.create_lnk('Del',$str_filename.'?op=del_bl&id='.$b['id'].'&ret='.urlencode($str_ret),true,false,'Diesen Listeneintrag wirklich aufheben?').']
						</td>';
			$str_out .= '<td>`b'.$b['id'].'`b</td>';
			$str_out .= '<td>`$`b'.$b['name'].'`b`0</td>';
			$str_out .= '</tr>';
										
		}
				
		$str_out .= '</table>`c';
		
		output($str_out, true);
		
	break;
}


page_footer();

