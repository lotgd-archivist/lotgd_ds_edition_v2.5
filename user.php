<?php
/**
* user.php: Zentrales Werkzeug für Superuser, um Spieleraccounts zu bearbeiten und zu verwalten
* @author Standardrelease by MightyE / Anpera, überarbeitet by talion <t@ssilo.de>
* @version DS-E V/2
*/

require_once "common.php";
require_once(LIB_PATH.'dg_funcs.lib.php');
require_once(LIB_PATH.'profession.lib.php');

su_check(SU_RIGHT_EDITORUSER,true);

function editnav () {
	global $row;
	
	addnav('Kontrolle');
	addnav('Verbannen','su_bans.php?op=edit_ban&ids[]='.$row['acctid']);
	addnav("Letzten Treffer anzeigen","user.php?op=lasthit&userid={$_GET['userid']}",false,true);
	if(su_check(SU_RIGHT_DEBUGLOG)) {
		addnav("Debug-Log anzeigen","su_logs.php?op=search&type=debuglog&account_id={$_GET['userid']}");
	}
	if(su_check(SU_RIGHT_MAILBOX)) {
		addnav("Mailbox zeigen","su_mails.php?op=search&to_id={$_GET['userid']}");
	}
	if(su_check(SU_RIGHT_COMMENT)) {
		addnav("Kommentare","su_comment.php?op=user&uid={$_GET['userid']}",false,true);
	}
	if(su_check(SU_RIGHT_EDITORITEMS)) {
		addnav("Inventar","itemsu.php?what=items&acctid={$_GET['userid']}");
	}
	if ($row['house'] && su_check(SU_RIGHT_EDITORHOUSES) ){
		addnav("Zum Hausmeister","suhouses.php?op=drin&id=".$row['house']);
	}
	addnav('Knappeneditor','user.php?op=disciple&userid='.$_GET['userid']);	
	addnav('Aktualisieren','user.php?op=edit&userid='.$_GET['userid']);
	if ($_GET['returnpetition']!=""){
		addnav("Zurück zur Anfrage","su_petitions.php?op=view&id={$_GET['returnpetition']}");
	}
		
}

page_header("User Editor");
output("<form action='user.php?op=search' method='POST'>Suche in allen Feldern: <input name='q' id='q'><input type='submit' class='button'></form>",true);
output("<script language='JavaScript'>document.getElementById('q').focus();</script>",true);
addnav("","user.php?op=search");
addnav("G?Zurück zur Grotte","superuser.php");


addnav('W?Zurück zum Weltlichen',$session['su_return']);
if (su_check(SU_RIGHT_EDITORUSER))
{
	
	addnav('Mechanik');
	addnav("Account-Tabellen abgleichen","user.php?op=extratable");
	//addnav("bestdragonage kopieren","user.php?op=copydata");
	addnav("Überflüssige Tabellen löschen","user.php?op=delextra");
	//addnav("Benutzereditor","user.php");
	//if($session['user']['acctid'] == 2310) {addnav('Forum','user.php?op=forum_all');}
	addnav('Seiten');
	$sql = "SELECT count(acctid) AS count FROM accounts";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$page=0;
	while ($row[count]>0){
		$page++;
		addnav("$page Seite $page","user.php?page=".($page-1)."&sort=$_GET[sort]");
		$row[count]-=100;
	}
}

$str_op = ($_REQUEST['op'] ? $_REQUEST['op'] : '');

switch($str_op) {
		
	case 'search':
		
		$sql = "SELECT acctid FROM accounts WHERE ";
		$where="
		login LIKE '%{$_POST['q']}%' OR 
		acctid LIKE '%{$_POST['q']}%' OR 
		name LIKE '%{$_POST['q']}%' OR 
		emailaddress LIKE '%{$_POST['q']}%' OR 
		lastip LIKE '%{$_POST['q']}%' OR 
		uniqueid LIKE '%{$_POST['q']}%' OR 
		gentimecount LIKE '%{$_POST['q']}%' OR 
		level LIKE '%{$_POST['q']}%'";
		$result = db_query($sql.$where);
		if (db_num_rows($result)<=0){
			output("`\$Keine Ergebnisse gefunden`0");

			$where="";
		}elseif (db_num_rows($result)>100){
			output("`\$Zu viele Ergebnisse gefunden. Bitte Suche einengen.`0");

			$where="";
		}elseif (db_num_rows($result)==1){
			//$row = db_fetch_assoc($result);
			//redirect("user.php?op=edit&userid=$row[acctid]");

			$_GET['page']=0;
		}else{
			$_GET['page']=0;
		}	
		
	break;	// END search
		
	case 'lasthit':
		
		$output="";
		$sql = "SELECT output FROM accounts WHERE acctid='{$_GET['userid']}'";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		echo str_replace("<iframe src=","<iframe Xsrc=",$row['output']);
		exit();
	
	break; // END lasthit
		
	case 'logout_all':
		
		if($_GET['act'] == 'ok') {
	
			$sql = "UPDATE accounts SET loggedin=0 WHERE superuser=0 AND loggedin=1";
			db_query($sql);
			output(db_affected_rows().' Spieler erfolgreich ausgeloggt!');
			
		}
		else {
			
			$sql = "SELECT COUNT(*) AS a FROM accounts WHERE loggedin=1 AND superuser=0";
			$count = mysql_fetch_row(db_query($sql));
			
			output($count[0].' Spieler wirklich ausloggen?`n`n'.create_lnk('Ab ins Körbchen!','user.php?op=logout_all&act=ok'),true);
			
		}
		
	break;	// END logout all
		
	case 'edit':
		
		$result = db_query("SELECT * FROM accounts WHERE acctid='$_GET[userid]'") or die(db_error(LINK));
		$row = db_fetch_assoc($result) or die(db_error(LINK));
		
		$result2 = db_query("SELECT * FROM account_extra_info WHERE acctid='$_GET[userid]'") or die(db_error(LINK));
		$row2 = db_fetch_assoc($result2) or die(db_error(LINK));
		
		// FORMULAR-ARRAY erstellen

		if(su_check(SU_RIGHT_RIGHTS)) {		
			$arr_grps = user_get_sugroups();
			

			
			$sugroups = ',0,Keine';
			if(is_array($arr_grps)) {
				foreach($arr_grps as $lvl=>$grp) {
					
					$sugroups .= ','.$lvl.','.$grp[0].'/'.$grp[1];
					
				}
			}
			
			$ugrp = array();
			
			// Wenn dieser User einer Gruppe angehört
			if(isset($arr_grps[$row['superuser']])) {
				$ugrp = $arr_grps[$row['superuser']];
				$ugrp_rights = $ugrp[2];
				
			}
									
			$surights = array('Superuser-Rechte,title');
			foreach($ARR_SURIGHTS as $r=>$v) {
				
				// Titel
				if(is_string($v)) {
					$surights[] = $v.',title,1';
				}
				else {
					$surights['surights['.$r.']'] = $v['desc'].($ugrp[0] ? '`nGruppe: '.($ugrp_rights[$r] ? '`@Ja`0' : '`$Nein`0') : '').',enum,-1,Gruppeneinstellung,0,Nein,1,Ja';					
				}
						
			}
			
		}
		
		$mounts=",0,Keins";
		$sql = "SELECT mountid,mountname,mountcategory FROM mounts ORDER BY mountcategory";
		$result = db_query($sql);
		while ($m = db_fetch_assoc($result)){
			$mounts.=",{$m['mountid']},{$m['mountcategory']}: ".strip_appoencode($m['mountname'],3);
		}
		
		$specialties=",0,Keins";
		$sql = "SELECT specname,category,specid FROM specialty ORDER BY category, specname";
		$result = db_query($sql);
		while ($m = db_fetch_assoc($result)){
			$specialties.=",{$m['specid']},{$m['category']}: ".strip_appoencode($m['specname'],3);
		}
		
		$professions = ',0,Keiner';
		
		foreach($profs as $k=>$p) {
			
			$professions .= ','.$k.','.$p[0].'/'.$p[1];
			
		}
		
		$guildfuncs = '';
		
		foreach($dg_funcs as $k=>$f) {
			
			$guildfuncs .= ','.$k.','.$f[0].'/'.$f[1];
			
		}
		
		$races=",,Unbekannt";
		$sql = "SELECT name,id FROM races WHERE active=1 ORDER BY name ASC";
		$result = db_query($sql);
		while ($m = db_fetch_assoc($result)){
			$races.=",{$m['id']},{$m['name']}";
		}
						
		$userinfo = array(
			"Accountdaten & Namen,title",
			"acctid"=>"User ID,viewonly|?Die Accountid, unter der der Account in der DB gespeichert ist.",
			"name"=>"Voller Name,viewonly|?Zum Ändern des Gesamtnamens bitte die einzelnen Bestandteile (Login, Farbname, Titel, eigener Titel) editieren.",
			"login"=>"Login|?Loginname des Accounts.",
			"title"=>"Regulärer Titel",
			"ctitle"=>"Eigener Titel",
			"ctitle_backup"=>"Eigener Titel - Backup",
			"cname"=>"Eigener (farbiger) Name",
			"csign"=>"Besonderes Signum vor dem Namen (max. 3 Zeichen)",
			"namecheckday"=>"Namensprüfungsalter",
			"namecheck"=>"Name geprüft von (acctid); 16777215=ok",
			"newpassword"=>"Neues Passwort",
			"emailaddress"=>"Email-Adresse",
			"banoverride"=>"Verbannungen übergehen,bool",
			"superuser"=>"Superuser,".(su_check(SU_RIGHT_RIGHTS) ? "enum".$sugroups : "viewonly"),
			"incommunity"=>"Community ID (0=nicht eingetragen),int",
			"chatallowed"=>"Chat erlaubt,enum,0,Nein,1,Ja,2,Bann",
			
			"Charakterdaten,title",
			"sex"=>"Geschlecht,enum,0,Männlich,1,Weiblich",
			"race"=>"Rasse,enum".$races,
			"specialty"=>"Spezialgebiet,enum".$specialties,
			"avatar"=>"Avatar:",
			"bio"=>"Bio",
			"long_bio"=>"Verlängerte Bio,textarea,30,15",
			"birthday"=>"Geburtsdatum (Format: YYYY-MM-DD)",
			"profession"=>"Beruf,enum".$professions,
			"marriedto"=>"Partner-ID (4294967295 = Violet/Seth),int",
			"charisma"=>"Flirts (4294967295 = verheiratet mit Partner),int",
									
			"guildid"=>"GildenID,int",
			"guildrank"=>"Gildenrang (1-".count($dg_default_ranks)."),int",
			"guildfunc"=>"Funktion in der Gilde,enum".$guildfuncs,
			
			"Werte,title",
			"dragonkills"=>"Drachenkills,int",
			"level"=>"Level,int",
			"experience"=>"Erfahrung,int",
			"hitpoints"=>"Lebenspunkte (aktuell),int",
			"maxhitpoints"=>"Maximale Lebenspunkte,int",
			"alive"=>"Lebendig,bool",
			"deathpower"=>"Gefallen bei Ramius,int",
			"gravefights"=>"Grabkämpfe übrig,int",
			"soulpoints"=>"Seelenpunkte (HP im Tod),int",
			"turns"=>"Runden übrig,int",
			"castleturns"=>"Schlossrunden übrig,int",
			"fishturn"=>"Angelrunden,int",
			"playerfights"=>"Spielerkämpfe übrig,int",
			"attack"=>"Angriffswert (inkl. Waffenschaden),int",
			"defence"=>"Verteidigung (inkl. Rüstung),int",
			"spirits"=>"Stimmung (nur Anzeige),enum,".RP_RESURRECTION.",RP-Wiedererweckung,-2,Sehr schlecht,-1,Schlecht,0,Normal,1,Gut,2,Sehr gut",
			"resurrections"=>"Auferstehungen,int",
			"daysinjail"=>"Verbrachte Tage im Kerker,int",
			"reputation"=>"Ansehen (-50 - +50),int",
			"imprisoned"=>"Haftstrafe in Tagen,int",
			"charm"=>"Charme,int",
			"battlepoints"=>"Arenapunkte,int",
			"age"=>"Tage seit Level 1,int",				
			"dragonage"=>"Alter beim letzten Drachenkill,int",
			"marks"=>"Male,int",
			"sentence"=>"Zu x Tagen Haft verurteilt,int",
						
			"Ausstattung & Besitz,title",
			"gems"=>"Edelsteine,int",
			"gold"=>"Bargold,int",
			"goldinbank"=>"Gold auf der Bank,int",
			"minnows"=>"Fliegen im Beutel,int",
			"worms"=>"Würmer im Beutel,int",
			"weapon"=>"Name der Waffe",
			"weapondmg"=>"Waffenschaden,int",
			"weaponvalue"=>"Kaufwert der Waffe,int",
			"armor"=>"Name der Rüstung",
			"armordef"=>"Verteidigungswert,int",
			"armorvalue"=>"Kaufwert der Rüstung,int",
			"house"=>"Haus-ID,int",
			"housekey"=>"Hausschlüssel?,int",
			"hashorse"=>"Tier,enum$mounts",
			"xmountname"=>"Name des Tieres",
			
			"Aktueller Spieltag / Übrige Aktionen,title",
			"seenlover"=>"Geflirtet,bool",
			"seendragon"=>"Drachen heute gesucht,bool",
			"seenmaster"=>"Meister befragt,bool",
			"fedmount"=>"Tier gefüttert,bool",
			"seenbard"=>"Barden gehört,bool",
			"usedouthouse"=>"Plumpsklo besucht,bool",
			"treepick"=>"Baum des Lebens besucht,bool",
			"boughtroomtoday"=>"Zimmer für heute bezahlt,bool",
			"cage_action"=>"Käfigkämpfe heute angezettelt,int",
			"gotfreeale"=>"Frei-Ale (MSB: getrunken - LSB: spendiert),int",
			"goldin"=>"Goldeingang heute,int",
			"goldout"=>"Goldausgang heute,int",
			"gemsin"=>"Gemeingang heute,int",
			"gemsout"=>"Gemausgang heute,int",
			"guildtransferred_gold"=>"Gildentransfer (gold),int",
			"guildtransferred_gems"=>"Gildentransfer (gems),int",
			"guildfights"=>"Gildenkämpfe heute,int",
			"temple_servant"=>"Tempeldienertage(x20=heute geleistet),int",
			"drunkenness"=>"Betrunken (0-100),int",
			"pvpflag"=>"Letzter PvP-Kampf (".PVP_IMMU." = Immu an)",
			"balance_forest"=>"Waldbalance|?-10 / +20, > 0 verstärkt Werte der Waldmonster, < 0 verringert sie.",
			"balance_dragon"=>"Drachenbalance|?-10 / +20, > 0 verstärkt Werte des Drachen, < 0 verringert sie.",
						
			"Freischaltungen / DP,title",
			"rename_weapons"=>"Darf Waffen umbenennen,bool",
			"has_long_bio"=>"Verlängerte Bio gekauft (1=ja 0=nein),int",
			"hasxmount"=>"Tier getauft (1=ja 0=nein),int",
			"trophyhunter"=>"Präparierset gekauft (1=ja 0=nein),int",			
						
			"Spezielle Ruhmeshalleneinträge,title",
			"bestdragonage"=>"Jüngstes Alter bei einem Drachenkill,int",
			"beerspent"=>"Anzahl spendierter Ales,int",
			"disciples_spoiled"=>"Anzahl verheizter Knappen,int",
			"timesbeaten"=>"Verpügelt worden,int",
			"runaway"=>"Aus dem Kampf geflohen,int",
															
			"Weitere Infos,title",
			"laston"=>"Zuletzt Online,viewonly",
			"lasthit"=>"Letzter neuer Tag,viewonly",
			"lastmotd"=>"Datum der letzten MOTD,viewonly",
			"lastip"=>"Letzte IP,viewonly",
			"uniqueid"=>"Unique ID,viewonly",
			"gentime"=>"Summe der Seitenerzeugungszeiten,viewonly",
			"gentimecount"=>"Seitentreffer,viewonly",
			"allowednavs"=>"Zulässige Navigation,viewonly",
			"dragonpoints"=>"Eingesetzte Drachenpunkte,viewonly",
			"bufflist"=>"Spruchliste,viewonly",
			"prefs"=>"Einstellungen,viewonly",
			"donationconfig"=>"Spendenkäufe,viewonly"
			
			);
			
		$extrainfo = array(
		);
		
		// END Formular-Array
		
		// Speichern
		if($_GET['act'] == 'save') {
			$sql1 = "UPDATE accounts SET ";
			$sql2 = "UPDATE account_extra_info SET ";
			
			// Rassenänderung: Boni zurücksetzen
			if($row['race'] != $_POST['race']) {
				$arr_change = $_POST;
				// Bisherige Rasse!
				$str_newrace = $_POST['race'];
				$arr_change['race'] = $row['race'];
				// Alte Boni abnehmen
				race_set_boni(true,true,$arr_change);
				// Neue Boni verteilen
				$arr_change['race'] = $str_newrace;
				race_set_boni(true,false,$arr_change);
				$_POST = $arr_change;
			}
					
			// Bei Namensänderung ein bißchen aufpassen
			// cname und Login müssen bis auf Farbcodes identisch sein
			if(strip_appoencode($_POST['cname'],3) != $_POST['login']) {
			    
			    // Ansonsten Farbname weg
			    $_POST['cname'] = '';
			    
			}
			
			// Wenn Login geändert: Forum nicht vergessen
			if($row2['incommunity'] && $_POST['login'] != $row['login']) {
				require_once(LIB_PATH.'communityinterface.lib.php');

				ci_rename($row2['incommunity'], stripslashes($_POST['login']));
			}
			
			// Jetzt noch Gesamtnamen korrekt setzen
			// Muss vor saveuser kommen, da beim Sessionuser noch Änderungen vorgenommen werden!
			if(substr($_POST['name'],0,34) != 'Neuling mit unzulässigem Namen Nr.') {
				$_POST['name'] = user_set_name($_GET['userid'],false,$_POST);
			}
					
			reset($_POST);
			
			// Sonderrechte speichern
			if(su_check(SU_RIGHT_RIGHTS)) {
				foreach($_POST['surights'] as $key=>$r) {
					if($r == -1) {
						unset($_POST['surights'][$key]);
					}
				}
				
				if(sizeof($_POST['surights']) > 0) {
					$_POST['surights'] = addslashes(serialize($_POST['surights']));
				}
				else {
					$_POST['surights'] = '';
				}
				// Zu Formular-Schablone hinzufügen
				$userinfo['surights'] = true;
			}
			
			foreach($_POST as $key=>$val){
				if (isset($row[$key])){
					if ($key=="newpassword" ){
						if ($val>"") {
							$sql1.="password = MD5(\"$val\"),";
						}
					}
					else{
						$sql1.="$key = \"$val\",";
					}
				}
				elseif (isset($row2[$key])){
					$sql2.="$key = \"$val\",";
				}
	
			}
			$sql1=substr($sql1,0,strlen($sql1)-1);
			$sql2=substr($sql2,0,strlen($sql2)-1);
			$sql1.=" WHERE acctid=\"$_GET[userid]\"";
			$sql2.=" WHERE acctid=\"$_GET[userid]\"";
							
			systemlog("Useredit - Editierte User ",$session['user']['acctid'],$_GET['userid']);
			
			//we must manually redirect so that our changes go in to effect *after* our user save.
			addnav("","su_petitions.php?op=view&id={$_GET['returnpetition']}");
			addnav("","user.php");
									
			saveuser();
			
			db_query($sql1) or die(db_error(LINK));
			db_query($sql2) or die(db_error(LINK));
										
			if ($_GET['returnpetition']!=""){
				header("Location: su_petitions.php?op=view&id={$_GET['returnpetition']}");
			}
			else{
				header("Location: user.php");
			}
		
			exit();
		}
		// END Speichern
		
		$userinfo = array_merge($userinfo,$extrainfo,$surights);
						
		debuglog("`&Benutzer ".$row['name']."`& im Usereditor geöffnet.");
						
		$row['surights'] = unserialize(stripslashes($row['surights']));
	
				
		foreach($ARR_SURIGHTS as $r=>$v) {
			
			if(isset($row['surights'][$r])) {
				$row['surights['.$r.']'] = $row['surights'][$r];	
				unset($row['surights'][$r]);
			}
			else {
				$row['surights['.$r.']'] = -1;	
			}
			
		}
		
		$row2['long_bio'] = preg_replace('/\r\n|\r|\n/', '', $row2['long_bio']); // Zeilenumbrüche raus
		
		$row = array_merge($row,$row2);
				
		output("<form action='user.php?op=special&userid=$_GET[userid]".($_GET['returnpetition']!=""?"&returnpetition={$_GET['returnpetition']}":"")."' method='POST'>",true);
		addnav("","user.php?op=special&userid=$_GET[userid]".($_GET['returnpetition']!=""?"&returnpetition={$_GET['returnpetition']}":"")."");
		output("<hr>`n`c".$row[name]."`c`n<input type='submit' class='button' name='newday' value='Neuen Tag gewähren'>",true);
		output("<input type='submit' class='button' name='fixnavs' value='Defekte Navs reparieren'>",true);
		if(!empty($row['emailvalidation'])) {
			output("<input type='submit' class='button' name='clearvalidation' value='E-Mail als gültig markieren'>",true);
		}
		output("<input type='submit' class='button' name='reset_values' value='ATK+DEF Reset (!)'>",true);
		output("<input type='submit' class='button' name='reset_dragonpoints' value='Drachenpunkte Reset (!)'>",true);
		output("</form>",true);
			
		output("<form action='user.php?op=edit&act=save&userid=$_GET[userid]".($_GET['returnpetition']!=""?"&returnpetition={$_GET['returnpetition']}":"")."' method='POST'>",true);
		addnav("","user.php?op=edit&act=save&userid=$_GET[userid]".($_GET['returnpetition']!=""?"&returnpetition={$_GET['returnpetition']}":"")."");
		addnav("","user.php?op=edit&userid=$_GET[userid]".($_GET['returnpetition']!=""?"&returnpetition={$_GET['returnpetition']}":"")."");
		
		editnav();
		
		if( $row['incommunity'] == 0 ){
			addnav("ins Forum übertragen", "user.php?op=forum&userid={$_GET['userid']}&name=".urlencode($row['login'])."&pass=".urlencode($row['password'])."&mail=".urlencode($row['emailaddress']));
		}
		
		addnav("Usereditor");
		addnav("Specials Editor","user_special.php?op=edit&userid={$_GET['userid']}");

		output("<input type='submit' class='button' value='Speichern'>",true);
		showform($userinfo,$row);
		output("</form>",true);
		if($_GET['userid'] != $session['user']['acctid']) {
			output("<iframe src='user.php?op=lasthit&userid={$_GET['userid']}' width='100%' height='400'>Dein Browser muss iframes unterstützen, um die letzte Seite des Users anzeigen zu können. Benutze den Link im Menü stattdessen.</iframe>",true);
		}
		addnav("","user.php?op=lasthit&userid={$_GET['userid']}");
		
	break;	// END edit
		
	case 'special':
		
		if ($_POST[newday]!=""){
			$sql = "UPDATE accounts SET lasthit='".date("Y-m-d H:i:s",strtotime(date("r")."-".(86500/getsetting("daysperday",4))." seconds"))."' WHERE acctid='$_GET[userid]'";
		}elseif($_POST[fixnavs]!=""){
			$sql = "UPDATE accounts SET allowednavs='',output='',restorepage='' WHERE acctid=$_GET[userid]";
		}elseif($_POST[clearvalidation]!=""){
			$sql = "UPDATE accounts SET emailvalidation='' WHERE acctid='$_GET[userid]'";
		}
		elseif ($_POST['reset_values']) {
						
			$sql = 'SELECT dragonpoints,weapondmg,armordef,level,race FROM accounts WHERE acctid='.$_GET['userid'];
			$arr_tmp = db_fetch_assoc(db_query($sql));
									
			$arr_dp = unserialize($arr_tmp['dragonpoints']);
									
			$arr_tmp['attack'] = $arr_tmp['weapondmg'] + $arr_tmp['level'];
			$arr_tmp['defence'] = $arr_tmp['armordef'] + $arr_tmp['level'];
			
			if(is_array($arr_dp)) {
				foreach($arr_dp as $key=>$val)
			    {
			    	if ($val=="atk" || $val == 'at')
			        {
			        	$arr_tmp['attack']++;
			        }
			        if ($val=="def" || $val == 'de')
			        {
			            $arr_tmp['defence']++;
			        }
			    }
			}
			
			if(!empty($arr_tmp['race'])) {
				$arr_race = race_get($arr_tmp['race'],true);
				race_set_boni(true,false,$arr_tmp);
			}
			
			debuglog('setzte ATK (='.$arr_tmp['attack'].') + DEF (='.$arr_tmp['defence'].') zurück für',$_GET['userid']);
			
			$sql = 'UPDATE accounts SET attack='.$arr_tmp['attack'].',defence='.$arr_tmp['defence'].' WHERE acctid='.$_GET['userid'];	
		}
		elseif ($_POST['reset_dragonpoints']) {
			
			$sql = 'SELECT dragonpoints,attack,defence,maxhitpoints,level,weapondmg,armordef FROM accounts WHERE acctid='.$_GET['userid'];
			$arr_tmp = db_fetch_assoc(db_query($sql));
									
			$arr_dp = unserialize($arr_tmp['dragonpoints']);
			
			if(is_array($arr_dp)) {
				foreach ($arr_dp as $key=>$val) {
					
					if($val == 'atk' || $val == 'at') {
						$arr_tmp['attack']--;
					}
					if($val == 'def' || $val == 'de') {
						$arr_tmp['defence']--;
					}
					if($val == 'hp') {
						$arr_tmp['maxhitpoints'] -= 5;
					}
					
				}
			}
			
			$arr_tmp['attack'] = max($arr_tmp['attack'],$arr_tmp['level']+$arr_tmp['weapondmg']);
			$arr_tmp['defence'] = max($arr_tmp['defence'],$arr_tmp['level']+$arr_tmp['armordef']);
			$arr_tmp['maxhitpoints'] = max($arr_tmp['maxhitpoints'],5*$arr_tmp['level']);
			
			debuglog('setzte Drachenpunkte zurück, ATK(='.$arr_tmp['attack'].'), DEF (='.$arr_tmp['defence'].'), HP (='.$arr_tmp['maxhitpoints'].') für',$_GET['userid']);
			
			$arr_tmp['dragonpoints'] = array();
			
			// User kurz ausloggen..
			$sql = 'UPDATE accounts SET loggedin=0,dragonpoints="'.serialize($arr_tmp['dragonpoints']).'",attack='.$arr_tmp['attack'].',defence='.$arr_tmp['defence'].',maxhitpoints='.$arr_tmp['maxhitpoints'].',lasthit="0000-00-00 00:00:00" WHERE acctid='.$_GET['userid'];	
						
		}
		
						
		if (empty($_GET['returnpetition'])) {
			$str_lnk = 'user.php?op=edit&userid='.$_GET['userid'];
		}
		else{
			$str_lnk = 'su_petitions.php?op=view&id='.$_GET['returnpetition'];
		}
		// Von Hand weiterleiten
		addnav('',$str_lnk);
		saveuser();
		
		db_query($sql);
				
		header("Location:".$str_lnk);
		exit();
		
	break;	// END special
	
	// Knappeneditor
	case 'disciple':
		
		$int_uid = (int)$_GET['userid'];
		$int_did = (int)$_POST['id'];
		
		addnav('Zurück zum Useredit','user.php?op=edit&userid='.$int_uid);
		
		if(!empty($int_did)) {
		
			// Feststellen, ob unser Knappe der stärkste im Lande ist
			$bool_bestone = false;
			$sql = 'SELECT level, id FROM disciples WHERE level > '.(int)$_POST['level'].' AND state > 0 ORDER BY level DESC LIMIT 1';
			$res = db_query($sql);
			
			// Gibt keinen stärkeren
			if(!db_num_rows($res)) {
				$bool_bestone = true;
				
				// Alle anderen zurücksetzen
				db_query('UPDATE disciples SET best_one = 0');
			}
			else {
				// Stärkeren zum besten Knappen erheben
				$arr_best = db_fetch_assoc($res);
				
				db_query('UPDATE disciples SET best_one = 1 WHERE id='.$arr_best['id']);
			}
					
			$sql = ($int_did == -1 ? 'INSERT INTO ' : 'UPDATE ');
			$sql .= ' disciples 
					SET name="'.$_POST['name'].'",state='.$_POST['state'].',oldstate='.$_POST['oldstate'].',level='.$_POST['level'].',best_one='.($bool_bestone ? 1 : 0).',master='.$int_uid;
			$sql .= ($int_did > -1 ? ' WHERE id='.$int_did : '');
			db_query($sql);
			
			if(db_affected_rows()) {
				output('`@`b`cKnappe erfolgreich editiert!`c`b`0`n`n');
			}
			else {
				output('`$`b`cKnappe NICHT editiert!`c`b`0`n`n');
			}
		}
		
		$sql = 'SELECT * FROM disciples WHERE master='.$int_uid;
		$res = db_query($sql);
		
		if(db_num_rows($res) == 0) {
			$arr_data = array('id'=>-1);
		}
		else {		
			$arr_data = db_fetch_assoc($res);
		}
		
		$str_state_enum = ',0,tot/inaktiv';	
		for($i=1;$i<=20;$i++) {
			$str_state_enum .= ','.$i.','.get_disciple_stat($i);	
		}
			
		$arr_form = array(
							'id'=>',hidden',
							'name'=>'Name des Knappen:',
							'state'=>'Aktueller Status des Knappen:,enum'.$str_state_enum,
							'oldstate'=>'Status-Backup:,enum'.$str_state_enum,
							'level'=>'Level des Knappen:,enum_order,0,100'
							);
							
		$str_lnk = 'user.php?op=disciple&userid='.$int_uid;
		addnav('',$str_lnk);
		output('<form method="POST" action="'.$str_lnk.'">',true);							
							
		showform($arr_form,$arr_data,false,'Speichern');
		
		output('</form>',true);
		
		
	break;
				
	case 'forum':
		
		$aUser = array();
		$aUser[ 0 ] = array(	'id'	=> $_GET['userid'], 
								'name'	=> urldecode($_GET['name']),
								'pass'	=> urldecode($_GET['pass']),
								'mail'	=> urldecode($_GET['mail'])
							); 
		include_once(LIB_PATH."communityinterface.lib.php");
		$ret = ci_importusers($aUser);
		if( !empty($ret) ){
			redirect("user.php?op=edit2&userid=".$_GET['userid']."&msg=ok");
		}
		else{
			redirect("user.php?op=edit2&userid=".$_GET['userid']."&msg=fail");
		}
		
	break;	// END forum
	
	case 'forum_all':
		
		$sql = 'SELECT accounts.acctid AS id,login AS name,password AS pass,emailaddress AS mail FROM accounts LEFT JOIN account_extra_info USING(acctid) WHERE incommunity > 0';
		$res = db_query($sql);
		
		while($a = db_fetch_assoc($res)) {
			$aUser[] = $a;
		}
				
		include_once(LIB_PATH."communityinterface.lib.php");
		$ret = ci_importusers($aUser);
		if( !empty($ret) ){
			redirect("user.php?op=edit2&userid=".$_GET['userid']."&msg=ok");
		}
		else{
			redirect("user.php?op=edit2&userid=".$_GET['userid']."&msg=fail");
		}
		
	break;	// END forum
		
	case 'logoff':
		
		$id = $_GET['userid'];
		$sql = "UPDATE accounts set loggedin = 0, lasthit = 0 WHERE acctid = $id";
		
		addnav("User Info bearbeiten","user.php?op=edit&userid=$id");
		
		$result = db_query($sql);
		db_query($sql) or die(sql_error($sql));
		output("Der User wurde ausgelogged!");
		
	break;	// END logoff
		
	default:	// Standardanzeige
		
		
		
	break;	// END default

}	// END Main-Switch (op)

if (isset($_GET['page'])){
	$order = "acctid";
	if ($_GET[sort]!="") $order = "$_GET[sort]";
	$offset=(int)$_GET['page']*100;
	$sql = "SELECT acctid,login,name,level,laston,gentimecount,lastip,uniqueid,emailaddress,activated FROM accounts ".($where>""?"WHERE $where ":"")."ORDER BY \"$order\" LIMIT $offset,100";
	$result = db_query($sql) or die(db_error(LINK));
	output("<table>",true);
	output("<tr>
	<td>Ops</td>
	<td><a href='user.php?sort=login'>Login</a></td>
	<td><a href='user.php?sort=name'>Name</a></td>
	<td><a href='user.php?sort=acctid'>ID</a></td>
	<td><a href='user.php?sort=level'>Lev</a></td>
	<td><a href='user.php?sort=laston'>Zuletzt da</a></td>
	<td><a href='user.php?sort=gentimecount'>Treffer</a></td>
	<td><a href='user.php?sort=lastip'>IP</a></td>
	<td><a href='user.php?sort=uniqueid'>ID</a></td>
	<td><a href='user.php?sort=emailaddress'>E-Mail</a></td>
	</tr>",true);
	addnav("","user.php?sort=login");
	addnav("","user.php?sort=name");
	addnav("","user.php?sort=acctid");
	addnav("","user.php?sort=level");
	addnav("","user.php?sort=laston");
	addnav("","user.php?sort=gentimecount");
	addnav("","user.php?sort=lastip");
	addnav("","user.php?sort=uniqueid");
	$rn=0;
	for ($i=0;$i<db_num_rows($result);$i++){
		$row=db_fetch_assoc($result);
		$loggedin=user_get_online(0,$row,true);
		$laston=round((strtotime(date("r"))-strtotime($row[laston])) / 86400,0)." Tage";
		if (substr($laston,0,2)=="1 ") $laston="1 Tag";
		if (date("Y-m-d",strtotime($row[laston])) == date("Y-m-d")) $laston="Heute";
		if (date("Y-m-d",strtotime($row[laston])) == date("Y-m-d",strtotime(date("r")."-1 day"))) $laston="Gestern";
		if ($loggedin) $laston="Jetzt";
		$row[laston]=$laston;
		if ($row[$order]!=$oorder) $rn++;
		$oorder = $row[$order];
		output("<tr class='".($rn%2?"trlight":"trdark")."'>",true);
		
		output("<td>",true);
		
		//ADDED LOG OFF HERE
		output("[<a href='user.php?op=edit&userid=$row[acctid]'>Edit</a>|"
				.create_lnk('Del','su_delete.php?ids[]='.$row['acctid'].'&ret='.urlencode(calcreturnpath()) ).'|'.
				create_lnk('Ban','su_bans.php?op=edit_ban&ids[]='.$row['acctid'].'&ret='.urlencode(calcreturnpath()) ).'|'.
				create_lnk('Logs','su_logs.php?op=search&type=debuglog&account_id='.$row['acctid']).'|'.
				create_lnk('Disku','su_userdiscu.php?op=new&id='.$row['acctid']).']'.
				"<a href='user.php?op=logoff&userid=$row[acctid]'>Log Off</a>|",true);
		addnav("","user.php?op=edit&userid=$row[acctid]");
		addnav("","user.php?op=setupban&userid=$row[acctid]");
		//ADDED LOG OFF HERE
		addnav("","user.php?op=logoff&userid=$row[acctid]");
		
		output("</td><td>",true);
		output($row['login']);
		output("</td><td>",true);
		output($row['name']);
		output("</td><td>",true);
		output($row['acctid']);
		output("</td><td>",true);
		output($row['level']);
		output("</td><td>",true);
		output($row['laston']);
		output("</td><td>",true);
		output($row['gentimecount']);
		output("</td><td>",true);
		output($row['lastip']);
		output("</td><td>",true);
		output($row['uniqueid']);
		output("</td><td>",true);
		output($row['emailaddress']);
		output("</td>",true);
		$gentimecount+=$row['gentimecount'];
		$gentime+=$row['gentime'];

		output("</tr>",true);
	}
	output("</table>",true);
	output("Treffer gesamt: $gentimecount`n");
	output("CPU-Zeit gesamt: ".round($gentime,3)."s`n");
	output("Durchschnittszeit für Seitenerzeugung: ".round($gentime/max($gentimecount,1),4)."s`n");
}

page_footer();
?>
