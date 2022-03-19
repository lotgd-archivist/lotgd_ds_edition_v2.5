<?php
/**
 * su_usergroups.php: Tool zum Einstellen der Superuser-Berechtigungen
 * @author talion
 * @version DS-E V/2
*/

require_once('common.php');
page_header('Superusereditor');

// Gruppen aus Settings laden
$arr_grps = user_get_sugroups(0,false);
if(!is_array($arr_grps)) {
	$arr_grps = array();
}
ksort($arr_grps);

addnav('Zurück');
addnav("G?Zur Grotte","superuser.php");
addnav('W?Zum Weltlichen',$session['su_return']);
addnav('Aktionen');

output("`c`b`&Superusereditor`0`b`c");

if($session['message'] != '') {
	output('`n`b'.$session['message'].'`b`n`n');
	$session['message'] = '';
}


/*
	if(!is_array($mix_rights_in)) {
		$arr_rights = explode(';',$mix_rights_in);
		
		return ($arr_rights);
	}
*/

// MAIN SWITCH
$op = ($_REQUEST['op'] ? $_REQUEST['op'] : '');

switch($op) {
			
	case 'editgroup':
						
		addnav("E?Edit beenden","su_usergroups.php");
				
		$id = (int)$_REQUEST['id'];
		
		$surights = array('Superuser-Rechte,title');
		foreach($ARR_SURIGHTS as $r=>$v) {
			
			// Titel
			if(is_string($v)) {
				$surights[] = $v.',title,1';
			}
			else {
				$surights['surights['.$r.']'] = $v['desc'].'<p>&nbsp;</p>,checkbox,1';
			}
									
		}
		
		if($id > 0) {
			
			$arr_editgrp = user_get_sugroups($id);
			$arr_editgrp = array('name_sing'=>$arr_editgrp[0],
								'name_plur'=>$arr_editgrp[1],
								'surights'=>$arr_editgrp[2],
								'lst_show'=>$arr_editgrp[3]);
									
			$arr_editgrp_rights = $arr_editgrp['surights'];
			
			foreach($arr_editgrp_rights as $r=>$v) {
				$arr_editgrp['surights['.$r.']'] = $v;
			}
						
		}
						
		$form = array('Allgemeines,title',
						'name_sing'=>'Name Singular',
						'name_plur'=>'Name Plural',
						'lst_show'=>'In "Wer ist online?"-Liste auf Startseite gesondert aufführen?,bool'
						);
		
		$form = array_merge($form,$surights);
		
		$link = "su_usergroups.php?op=savegroup";
						
		$out .=	"<form method=\"POST\" action=\"".$link."\">";
		addnav("",$link);
		
		if($_GET['copy']) {
			$arr_editgrp['name_sing'] = 'Kopie '.$arr_editgrp['name_sing'];
			$id = 0;
		}
		else {
			$out .=	"<input type=\"hidden\" value=\"".$id."\" name=\"id\">";				
			if($id > 0) {
				addnav('Kopie anlegen','su_usergroups.php?op=editgroup&id='.$id.'&copy=1');
			}
		}
		
		output($out,true);				
		showform($form,$arr_editgrp);
							
	break;
	
	
	// Gruppe löschen
	case 'delgroup':
		
		$id = (int)$_GET['id'];
		
		$sql = 'SELECT login FROM accounts WHERE superuser='.$id.' ORDER BY acctid';
		$res = db_query($sql);
		
		if(db_num_rows($res)) {
			
			output('`$Folgende Superuser-Accounts befinden sich noch in dieser Gruppe:`n`n');
			while($a = db_fetch_assoc($res)) {
				output('`&'.$a['login'].'`n');
			}
			output('`n`$Bitte zuerst diese Accounts einer anderen Gruppe zuordnen!');
			
		}
		else {
			
			unset($arr_grps[$id]);
		
			savesetting( 'sugroups', addslashes(serialize($arr_grps)) );
			
			$session['message'] = '`@Erfolgreich gelöscht!';
							
			redirect('su_usergroups.php');	
			
		}
						
	break;
	
	// Speichern	
	case 'savegroup':
		
		$id = (int)$_REQUEST['id'];
						
		// Übersetzung der Formulardaten in numerische Array-Schlüssel
		$arr_savegrp = array(0=>$_POST['name_sing'],
								1=>$_POST['name_plur'],				
								2=>$_POST['surights'],
								3=>$_POST['lst_show']);
		
		if($id > 0) {
			systemlog('Superuser-Gruppe '.$arr_grps[$id][0].' geändert.',$session['user']['acctid']);
		
			$arr_grps[$id] = $arr_savegrp;
		}
		else {
			ksort($arr_grps);
			end($arr_grps);
			$int_lastkey = (int)key($arr_grps);
			$arr_grps[$int_lastkey+1] = $arr_savegrp;
		}
		
		savesetting( 'sugroups', addslashes(serialize($arr_grps)) );
					
		$session['message'] = '`@Erfolgreich gespeichert!`0';
		
		redirect('su_usergroups.php');
						
	break;
	
	// User mit SU-Rechten ermitteln	
	case 'check_su_user':
		
		$out = '';
		
		if($_GET['act'] == 'reset') {
			$int_acctid = (int)$_GET['id'];
			
			if($int_acctid) {
				
				if($int_acctid == $session['user']['acctid']) {
					$out .= '`$DAS willst du nicht wirklich ; )`0`n`n';					
				}
				else {
					$sql = 'UPDATE accounts SET surights = "", superuser = 0 WHERE acctid='.$int_acctid;			
					db_query($sql);
					systemlog('`7SU-Rechte zurückgesetzt.',$session['user']['acctid'],$int_acctid);
					$out .= '`@AcctID '.$int_acctid.' wurde der Bürde seiner SU-Rechte enthoben!`0`n`n';
				}
				
			}
		}
		
		addnav('Zurück','su_usergroups.php');
		
		$sql = 'SELECT name,acctid,superuser,surights FROM accounts WHERE surights>"" OR superuser>0 ORDER BY acctid ASC';
		$res = db_query($sql);

		$arr_rights = array();
			
		$out .= '`&`bZeige User mit Superuser-Rechten:`b`n`n';
		
		while($a = db_fetch_assoc($res)) {
			$str_grpname = '`i'.(isset($arr_grps[ $a['superuser'] ]) ? $arr_grps[ $a['superuser'] ][0] : 'Keine Gruppe').'`i';
			$out .= '<hr>`n`&`b'.$a['acctid'].', '.$a['name'].'`&:`b ('.$str_grpname.')`n
					[ '
					.create_lnk('Alle Rechte abnehmen!','su_usergroups.php?op=check_su_user&act=reset&id='.$a['acctid'],true,false,'Wirklich Rechte abnehmen?')
					.' ] [ '
					.create_lnk('In Usereditor laden!','user.php?op=edit&userid='.$a['acctid'])
					.' ]`n`&';
			
			if($a['superuser']) {
				$out .= '`@Darf Grotte betreten.`0`n`n';
			}
					
			$arr_rights = array();
			$arr_urights = unserialize( stripslashes($a['surights']) );
					
			if(isset($arr_grps[ $a['superuser'] ])) {
				$arr_usergroup = $arr_grps[ $a['superuser'] ];
																
				// Einzelrechte überschreiben Gruppenrechte		
				$arr_rights = $arr_usergroup[2];
				if(is_array($arr_urights)) {		
					foreach($arr_urights as $key=>$r) {
						$arr_rights[$key] = ($r == 1 ? 2 : 0);
					}
				}
			}
			else {
				$arr_rights = $arr_urights;
			}
			
			// Ausgabe
			foreach ($arr_rights as $key => $val) {
				if($val) {
					$out .= '`^'.$ARR_SURIGHTS[$key]['desc'].'`& => `@Ja '.($val == 2 ? '(Sonderrecht)' : '').'`0`n';
				}
			}
									
		}	
		
		output($out,true);
		
			
	break;
	
	// Standardansicht, Auswahl
	default:
		
		$out = '`c<table cellspacing="2" cellpadding="2"><tr class="trhead">
					<td>`bID`b</td>		
					<td>`bName`b</td>
					<td>`bAktionen`b</td>
				</tr>';
		
		addnav('Neue Gruppe','su_usergroups.php?op=editgroup&id=0');
		addnav('User mit Rechten','su_usergroups.php?op=check_su_user');
		
		foreach($arr_grps as $id => $g) {
			
			$style = ($style == 'trlight' ? 'trdark' : 'trlight');
			$editlink = create_lnk('Edit','su_usergroups.php?op=editgroup&id='.$id);
			$dellink = create_lnk('Del','su_usergroups.php?op=delgroup&id='.$id);
						
			$out .= '<tr class="'.$style.'">
						<td>'.$id.'</td>
						<td>'.$g[0].' / '.$g[1].'`&</td>
						<td>
							[ '.$editlink.' ] 
							[ `$'.$dellink.'`& ]
						</td>
					</tr>';
			
		}	
		
		$out .= '</table>`c';
		
		output($out,true);
		
	break;
	
}

page_footer();
?>