<?
//Stalltier Editor
//übersetzt von Fossla für atrahor.de am 15.7.06
require_once "common.php";
su_check(SU_RIGHT_EDITORMOUNTS,true);

page_header("Stalltier Editor");
addnav('Zurück');
addnav("G?Zur Grotte","superuser.php");
addnav('W?Zum Weltlichen',$session['su_return']);
addnav('Aktionen');
addnav('Tier hinzufügen','mounts.php?op=add');


output("`c`b`&Stalltiereditor`0`b`c");


if ($_GET['op']=="del"){
	$sql = "UPDATE mounts SET mountactive=0 WHERE mountid='{$_GET['id']}'";
	db_query($sql);
	$_GET['op']="";
	cache_release('playermount');
}
if ($_GET['op']=="undel"){
	$sql = "UPDATE mounts SET mountactive=1 WHERE mountid='{$_GET['id']}'";
	db_query($sql);
	$_GET['op']="";
	cache_release('playermount');
}

if ($_GET['op']==""){
	$sql = "SELECT * FROM mounts ORDER BY mountcategory, mountcostgems, mountcostgold";
	output("<table>",true);
	output("<tr><td>Option</td><td>Name</td><td>Preis</td><td>&nbsp;</td></tr>",true);
	$result = db_query($sql);
	$cat = "";
	for ($i=0;$i<db_num_rows($result);$i++){
		$row = db_fetch_assoc($result);
		if ($cat!=$row['mountcategory']){
			output("<tr><td colspan='4'>Kategorie: {$row['mountcategory']}</td></tr>",true);
			$cat = $row['mountcategory'];
		}
		output("<tr>",true);
		output("<td>[ <a href='mounts.php?op=edit&id={$row['mountid']}'>Bearbeiten</a> |",true);
		addnav("","mounts.php?op=edit&id={$row['mountid']}");
		if ($row['mountactive']) {
			output(" <a href='mounts.php?op=del&id={$row['mountid']}'>Deaktivieren</a> ]</td>",true);
			addnav("","mounts.php?op=del&id={$row['mountid']}");
		}else{
			output(" <a href='mounts.php?op=undel&id={$row['mountid']}'>Aktivieren</a> ]</td>",true);
			addnav("","mounts.php?op=undel&id={$row['mountid']}");
		}
		output("<td>{$row['mountname']}</td>",true);
		output("<td>{$row['mountcostgems']} Edelsteine, {$row['mountcostgold']} Gold</td>",true);
		//output("<td>{$row['mountbuff']}</td>",true);
		output("<td>Waldkämpfe: {$row['mountforestfights']}, Taverne: {$row['tavern']}</td>",true);
		output("</tr>",true);
	}
	output("</table>",true);
}elseif ($_GET['op']=="add"){
	output("Stalltier hinzufügen:`n");
	addnav("Zurück zum Editor","mounts.php");
	mountform(array());
}elseif ($_GET['op']=="edit"){
	addnav("Zurück zum Editor","mounts.php");
	$sql = "SELECT * FROM mounts WHERE mountid='{$_GET['id']}'";
	$result = db_query($sql);
	if (db_num_rows($result)<=0){
		output("`iDieses Stalltier wurde nicht gefunden.`i");
	}else{
		output("Stalltier Editor:`n");
		$row = db_fetch_assoc($result);
		$row['mountbuff']=unserialize($row['mountbuff']);
		mountform($row);
	}
}elseif ($_GET['op']=="save"){
	$buff = array();
	reset($_POST['mount']['mountbuff']);
	$_POST['mount']['mountbuff']['activate']=join(",",$_POST['mount']['mountbuff']['activate']);
	while (list($key,$val)=each($_POST['mount']['mountbuff'])){
		if ($val>""){
			$buff[$key]=stripslashes($val);
		}
	}
	//$buff['activate']=join(",",$buff['activate']);
	$_POST['mount']['mountbuff']=$buff;
	reset($_POST['mount']);
	$keys='';
	$vals='';
	$sql='';
	$i=0;
	while (list($key,$val)=each($_POST['mount'])){
		if (is_array($val)) $val = addslashes(serialize($val));
		if ($_GET['id']>""){
			$sql.=($i>0?",":"")."$key='$val'";
		}else{
			$keys.=($i>0?",":"")."$key";
			$vals.=($i>0?",":"")."'$val'";
		}
		$i++;
	}
	if ($_GET['id']>""){
		$sql="UPDATE mounts SET $sql WHERE mountid='{$_GET['id']}'";
	}else{
		$sql="INSERT INTO mounts ($keys) VALUES ($vals)";
	}
	db_query($sql);
	if (db_affected_rows()>0){
		output("Das Tier wurde gespeichert!");
		cache_release('playermount');
	}else{
		output("Tier `bnicht`b gespeichert: $sql");
	}
	addnav("Zurück zum Editor","mounts.php");
}

function mountform($mount){
	global $output;
	output("<form action='mounts.php?op=save&id={$mount['mountid']}' method='POST'>",true);
	addnav("","mounts.php?op=save&id={$mount['mountid']}");
	$output.="<table>";
	$output.="<tr><td>Tier-Name:</td><td><input name='mount[mountname]' value=\"".htmlentities($mount['mountname'])."\"></td></tr>";
	$output.="<tr><td>Beschreibung für Mericks Ställe:</td><td><input name='mount[mountdesc]' value=\"".htmlentities($mount['mountdesc'])."\"></td></tr>";
	$output.="<tr><td>Tier-Kategorie:</td><td><input name='mount[mountcategory]' value=\"".htmlentities($mount['mountcategory'])."\"></td></tr>";
	$output.="<tr><td>Kosten an Edelsteinen:</td><td><input name='mount[mountcostgems]' value=\"".htmlentities((int)$mount['mountcostgems'])."\"></td></tr>";
	$output.="<tr><td>Kosten an Gold:</td><td><input name='mount[mountcostgold]' value=\"".htmlentities((int)$mount['mountcostgold'])."\"></td></tr>";
	$output.="<tr><td>Zusätzliche Waldkämpfe pro Tag:</td><td><input name='mount[mountforestfights]' value=\"".htmlentities((int)$mount['mountforestfights'])."\" size='5'></td></tr>";
	$output.="<tr><td>Findet DarkHorse Taverne (1 = ja):</td><td><input name='mount[tavern]' value=\"".htmlentities((int)$mount['tavern'])."\" size='1'></td></tr>";
	$output.="<tr><td>Nachricht am neuen Tag:</td><td><input name='mount[newday]' value=\"".htmlentities($mount['newday'])."\" size='40'></td></tr>";
	$output.="<tr><td>Nachricht bei vollkommener Erholung:</td><td><input name='mount[recharge]' value=\"".htmlentities($mount['recharge'])."\" size='40'></td></tr>";
	$output.="<tr><td>Nachricht bei teilweiser Erholung:</td><td><input name='mount[partrecharge]' value=\"".htmlentities($mount['partrecharge'])."\" size='40'></td></tr>";
	$output.="<tr><td>Wahrscheinlichkeit in die Mine zu kommen (in %):</td><td><input name='mount[mine_canenter]' value=\"".htmlentities((int)$mount['mine_canenter'])."\"></td></tr>";
	$output.="<tr><td>Wahrscheinlichkeit in der Mine zu sterben (in %):</td><td><input name='mount[mine_candie]' value=\"".htmlentities((int)$mount['mine_candie'])."\"></td></tr>";
	$output.="<tr><td>Wahrscheinlichkeit den Spieler in der Mine zu retten (in %):</td><td><input name='mount[mine_cansave]' value=\"".htmlentities((int)$mount['mine_cansave'])."\"></td></tr>";
	$output.="<tr><td>Nachricht zum anbinden des Tieres vor der Mine:</td><td><input name='mount[mine_tethermsg]' value=\"".htmlentities($mount['mine_tethermsg'])."\" size='40'></td></tr>";
	$output.="<tr><td>Nachricht bei Tod in der Mine:</td><td><input name='mount[mine_deathmsg]' value=\"".htmlentities($mount['mine_deathmsg'])."\" size='40'></td></tr>";
	$output.="<tr><td>Nachricht wenn der Spieler gerettet wurde:</td><td><input name='mount[mine_savemsg]' value=\"".htmlentities($mount['mine_savemsg'])."\" size='40'></td></tr>";
	$output.="<tr><td>Mindest DK-Grenze:</td><td><input name='mount[mindk]' value=\"".htmlentities($mount['mindk'])."\" size='40'></td></tr>";
	$output.="<tr><td>Faktor für Kosten bei Tiertrainer (Wert hoch der schon erhaltenen Runden):</td><td><input name='mount[trainingcost]' value=\"".htmlentities($mount['trainingcost'])."\" size='40'></td></tr>";
	$output.="<tr><td valign='top'>Tier Fähigkeiten:</td><td>";
	$output.="<b>Nachrichten:</b><Br/>";
	$output.="Aktionsname: <input name='mount[mountbuff][name]' value=\"".htmlentities($mount['mountbuff']['name'])."\"><Br/>";
	//output("Start Nachricht: <input name='mount[mountbuff][startmsg]' value=\"".htmlentities($mount['mountbuff']['startmsg'])."\">`n",true);
	$output.="Nachricht jede Runde: <input name='mount[mountbuff][roundmsg]' value=\"".htmlentities($mount['mountbuff']['roundmsg'])."\"><Br/>";
	$output.="Nachricht wenn die Runden des Tieres aufgebraucht sind: <input name='mount[mountbuff][wearoff]' value=\"".htmlentities($mount['mountbuff']['wearoff'])."\"><Br/>";
	$output.="Effekt Nachricht: <input name='mount[mountbuff][effectmsg]' value=\"".htmlentities($mount['mountbuff']['effectmsg'])."\"><Br/>";
	$output.="Effekt Nachricht bei vollkommener Gesundheit des Spielers: <input name='mount[mountbuff][effectnodmgmsg]' value=\"".htmlentities($mount['mountbuff']['effectnodmgmsg'])."\"><Br/>";
	$output.="Effekt Fehlschlag Nachricht: <input name='mount[mountbuff][effectfailmsg]' value=\"".htmlentities($mount['mountbuff']['effectfailmsg'])."\"><Br/>";
	$output.="<Br/><b>Effekte:</b><Br/>";
	$output.="Runden am neuen Tag: <input name='mount[mountbuff][rounds]' value=\"".htmlentities((int)$mount['mountbuff']['rounds'])."\" size='5'><Br/>";
	$output.="Spieler Angriffs-Multiplikator: <input name='mount[mountbuff][atkmod]' value=\"".htmlentities($mount['mountbuff']['atkmod'])."\" size='5'> (Faktor)<Br/>";
	$output.="Spieler Verteidigungs-Multiplikator: <input name='mount[mountbuff][defmod]' value=\"".htmlentities($mount['mountbuff']['defmod'])."\" size='5'> (Faktor)<Br/>";
	$output.="Regeneration: <input name='mount[mountbuff][regen]' value=\"".htmlentities($mount['mountbuff']['regen'])."\"><Br/>";
	$output.="Günstlings-Zähler (minion count): <input name='mount[mountbuff][minioncount]' value=\"".htmlentities($mount['mountbuff']['minioncount'])."\"><Br/>";
	$output.="Min Schaden am Gegner: <input name='mount[mountbuff][minbadguydamage]' value=\"".htmlentities($mount['mountbuff']['minbadguydamage'])."\" size='5'><Br/>";
	$output.="Max Schaden am Gegner: <input name='mount[mountbuff][maxbadguydamage]' value=\"".htmlentities($mount['mountbuff']['maxbadguydamage'])."\" size='5'><Br/>";
	$output.="Lebenskraft-Multiplikator (Lifetap): <input name='mount[mountbuff][lifetap]' value=\"".htmlentities($mount['mountbuff']['lifetap'])."\" size='5'> (Faktor)<Br/>";
	$output.="Schadensabwehr: <input name='mount[mountbuff][damageshield]' value=\"".htmlentities($mount['mountbuff']['damageshield'])."\" size='5'> (Faktor)<Br/>";
	$output.="Gegner Schaden: <input name='mount[mountbuff][badguydmgmod]' value=\"".htmlentities($mount['mountbuff']['badguydmgmod'])."\" size='5'> (Faktor)<Br/>";
	$output.="Gegner Angriff: <input name='mount[mountbuff][badguyatkmod]' value=\"".htmlentities($mount['mountbuff']['badguyatkmod'])."\" size='5'> (Faktor)<Br/>";
	$output.="Gegner Verteidigung: <input name='mount[mountbuff][badguydefmod]' value=\"".htmlentities($mount['mountbuff']['badguydefmod'])."\" size='5'> (Faktor)<Br/>";
	//$output.=": <input name='mount[mountbuff][]' value=\"".htmlentities($mount['mountbuff'][''])."\">`n",true);
	
	$output.="<Br/><b>Aktivieren:</b><Br/>";
	$output.="<input type='checkbox' name='mount[mountbuff][activate][]' value=\"roundstart\"".(strpos($mount['mountbuff']['activate'],"roundstart")!==false?" checked":"")."> Bei Rundenbeginn<Br/>";
	$output.="<input type='checkbox' name='mount[mountbuff][activate][]' value=\"offense\"".(strpos($mount['mountbuff']['activate'],"offense")!==false?" checked":"")."> Beim Angriff<Br/>";
	$output.="<input type='checkbox' name='mount[mountbuff][activate][]' value=\"defense\"".(strpos($mount['mountbuff']['activate'],"defense")!==false?" checked":"")."> Bei Verteidigung<Br/>";
	$output.="<Br/>";
	$output.="</td></tr>";
	$output.="</table>";
	$output.="<input type='submit' class='button' value='Speichern'></form>";
}

page_footer();
?>
