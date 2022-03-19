<?php
/**
* su_races.php: Rasseneditor
* @author talion <t@ssilo.de>
* @version DS-E V/2
*/

$str_filename = basename(__FILE__);
require_once('common.php');

su_check(SU_RIGHT_EDITORRACES,true);

page_header('Rasseneditor');

output('`c`b`&Rasseneditor`&`b`c`n`n');

// Grundnavi erstellen
addnav('Zurück');

addnav('G?Zur Grotte','superuser.php');
addnav('W?Zum Weltlichen',$session['su_return']);

addnav('Aktionen');
addnav('Liste',$str_filename);
addnav('Neuer Eintrag',$str_filename.'?op=edit');
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
	
	// Suchergebisse
	case 'search':	
													
		$str_data_sql = '	SELECT 		r.*
							FROM		races r
							ORDER BY name ASC';
						
		$str_out .= '`n`c<table cellpadding="4" cellspacing="5">
							<tr class="trhead">
								<td>`bID`b</td>
								<td>`bName`b</td>
								<td>`bMin. DK`b</td>
								<td>`bSuperuser?`b</td>
								<td>`bAktiv?`b</td>
							</tr>
						';
		
		$str_tr_class = 'trlight';
		
		$res = db_query($str_data_sql);
		
		if(db_num_rows($res) == 0) {
			
			$str_out .= '`iKeine Rassen vorhanden!`i';
			
		}
															
		// Ergebnisse zeigen
		while($r = db_fetch_assoc($res)) {
									
			$str_out .= '<tr class="'.$str_tr_class.'">
							<td>&nbsp;'.$r['id'].'&nbsp;</td>
							<td>&nbsp;'.$r['name'].'&nbsp;</td>
							<td>&nbsp;'.$r['mindk'].'&nbsp;</td>
							<td>&nbsp;'.($r['superuser'] ? 'Ja':'Nein').'&nbsp;</td>
							<td>&nbsp;'.($r['active'] ? '`@Ja`0':'`$Nein`0').'&nbsp;</td>
						</tr>
						<tr class="'.$str_tr_class.'">
							<td align="right" colspan="5">
								[ '.create_lnk('Edit',$str_filename.'?op=edit&id='.urlencode($r['id'])).' ]
								[ '.create_lnk('Löschen',$str_filename.'?op=del&id='.urlencode($r['id']),true,false,'Wirklich löschen?').' ]
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
		
		$arr_form = 	array(	'active'=>'Aktiv,bool',
								'id'=>'Feste ID der Rasse,text,3',
								'name'=>'Name der Rasse ohne Formatierungen,text,40',
								'colname'=>'Name der Rasse mit Formatierungen (optional),text,60',
								'colname_pr'=>'Vorschau:,preview,colname',
								'name_plur'=>'Name Plural der Rasse ohne Formatierungen,text,44',
								'colname_plur'=>'Name Plural der Rasse mit Formatierungen (optional),text,60',
								'colname_plur_pr'=>'Vorschau:,preview,colname_plur',
								'chosen_msg'=>'Nachricht nachdem Rasse gewählt wurde,text,1000',
								'newday_msg'=>'Nachricht am neuen Tag (optional),text,255',
								'long_desc'=>'Ausführl. Beschreibung der Rasse,textarea,40,20',
								'raceroom'=>'Rassenraum?,enum,0,Keinen,1,Wald,2,Wohnviertel',
								'raceroom_name'=>'Name des Rassenraums,text,80',
								'raceroom_nav'=>'Navitext für den Rassenraum,text,80',
								'raceroom_desc'=>'Beschreibung für Rassenraum (falls vorhanden),textarea,40,20',
								'raceroom_all'=>'Darf Rassenräume aller Rassen betreten?,bool',
								'superuser'=>'Nur für Superuser wählbar,bool',
								'mindk'=>'Wählbar ab DK ..,int',
								'Boni (Intervall -10 ; +10),title',
								'boni[attack]'=>'Angriff +/-,int',
								'boni[defence]'=>'Verteidigung +/-,int',
								'boni[maxhitpoints]'=>'Max. LP +/-,int',
								'boni[turns]'=>'Waldkämpfe +/-,int',
								'boni[castleturns]'=>'Schlossrunden +/-,int');
		
		$str_id = stripslashes($_GET['id']);
				
		if($_GET['act'] == 'save') {
														
			$arr_boni = $_POST['boni'];
			unset($_POST['boni']);
			foreach ($arr_boni as $key=>$b) {
										
				$b = (int)$b;
				$b = min($b,10);
				$b = max($b,-10);
				if($b == 0) {
					unset($arr_boni[$key]);
				}
			}
			$arr_specboni = $_POST['specboni'];
			unset($_POST['specboni']);
			foreach ($arr_specboni as $key=>$b) {
										
				$b = (int)$b;
				$b = min($b,10);
				$b = max($b,-10);
				if($b == 0) {
					unset($arr_specboni[$key]);
				}
			}
						
			$sql = (!empty($str_id) ? 'UPDATE ' : 'INSERT INTO ');
		
			$sql .= ' races SET ';
			
			foreach($arr_form as $key => $v) {
				if(isset($_POST[$key])) {
					$sql .= $key.' = "'.addslashes(stripslashes($_POST[$key])).'",';
				}
			}
			$sql .= ' boni="'.addslashes(serialize($arr_boni)).'",specboni="'.addslashes(serialize($arr_specboni)).'"';
												
			$sql .= ($str_id ? ' WHERE id="'.addslashes($str_id).'"' : '');
			
			db_query($sql);
					
			$session['message'] = '`@Erfolgreich gespeichert!';

			cache_release('playerrace');
					
			redirect($str_filename);
		}
		
		$arr_data = array();
		
		if(!empty($str_id)) {
			$arr_data = db_fetch_assoc(db_query('SELECT * FROM races WHERE id="'.addslashes($str_id).'"'));
			$arr_boni = unserialize($arr_data['boni']);
			$arr_specboni = unserialize($arr_data['specboni']);
			if(is_array($arr_specboni) && sizeof($arr_specboni) > 0) {
				foreach ($arr_specboni as $key=>$b) {
					$arr_data['specboni['.$key.']'] = $b;
				}
			}
			if(is_array($arr_boni) && sizeof($arr_boni) > 0) {
				foreach ($arr_boni as $key=>$b) {
					$arr_data['boni['.$key.']'] = $b;
				}
			}
		}
		
		if(isset($session['form_data'])) {
			$arr_data = array_merge($arr_data,$session['form_data']);
			unset($session['form_data']);
		}
										
		// Fähigkeiten anhängen
		$sql = 'SELECT specname,usename FROM specialty ORDER BY specname ASC';
		$res = db_query($sql);
		
		while($s = db_fetch_assoc($res)) {
			
			$arr_form['specboni['.$s['usename'].']'] = 'Spezialanwendungen in '.$s['specname'].',int';
			
		}
		// END Fähigkeiten
		
		$str_lnk = $str_filename.'?op=edit&act=save&id='.$str_id;
		addnav('',$str_lnk);								
		output('<form method="POST" action="'.$str_lnk.'">',true);									
		showform($arr_form,$arr_data);
		output('</form>',true);
			
	break;
	
	case 'del':
		
		$str_id = stripslashes($_GET['id']);
		
		$sql = 'DELETE FROM races WHERE id="'.addslashes($str_id).'"';
		db_query($sql);
		
		$sql = 'UPDATE accounts SET race="" WHERE race="'.addslashes($str_id).'"';
		db_query($sql);
		
		if(db_affected_rows(LINK)) {
			$session['message'] = '`@Rasse ID '.$str_id.' erfolgreich gelöscht!`0';
			cache_release('playerrace');
		}
		else {
			$session['message'] = '`$Fehler bei Löschen der Rasse ID '.$str_id.'!`0';
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
