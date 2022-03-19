<?
/**
 * racesspecial.php: Räume, die nur Spielern bestimmter Rassen zugänglich sind
 *		 				Gedenkstein hinzugefügt von Fossla (maikesonja@gmx.de)
 * @author maris <maraxxus@gmx.de>, modded by talion: Neues Rassensystem
 * @version DS-E V/2
*/

require_once "common.php";
checkday();
addcommentary();

function showcasulties($victim){
		
	$arr_casualties = array();
	$arr_casualties = unserialize(stripslashes(getsetting('race_casualties','')));
			
	if(!sizeof($arr_casualties) || !sizeof($arr_casualties[$victim])) {
		output('Der Tod ist zynisch, humorvoll und notorisch unpünktlich.');
		return;
	}

	// Rassenliste
	$res = db_query('SELECT colname_plur,id FROM races WHERE active=1');
	$arr_races = db_create_list($res,'id');
				
	$arr_race_cas = $arr_casualties[$victim];
	
	$int_total = 0;
	
	foreach ($arr_race_cas as $str_id => $int_number) {
		
		$int_total += $int_number;
		
		$str_out .= '`$'.$int_number.'`& wurden von '.$arr_races[$str_id]['colname_plur'].'`& ';
		
		switch(e_rand(1,3)) {
			case 1:$str_out .= 'niedergestreckt';break;
			case 2:$str_out .= 'getötet';break;
			case 3:$str_out .= 'gemeuchelt';break;
		}
		
		$str_out .= '!`n';
	}
	$str_out .= '`nWir trauern um alle `$'.$int_total.'`& unserer getöteten Schwestern und Brüder!';
	
	output($str_out);
	return;

}

$str_raceid = $_GET['race'];

if(empty($str_raceid)) {
	redirect('village.php');
}


	
$arr_race = race_get($str_raceid,true);

if($_GET['op'] == 'show_list') {

	page_header("Die Rassenliste");
			
	output ('`&`c`bEine Liste am Rande dieses Ortes zeigt Dir auf magische Weise alle '.$arr_race['name_plur'].' in '.getsetting('townname','Atrahor').':`b`c`n`n');
	
	user_show_list(50,' race="'.$arr_race['id'].'"','dragonkills DESC, name ASC');
		
	addnav('Zurück','racesspecial.php?race='.$str_raceid);
	
}

elseif($_GET['op'] == 'pvp_deads') {

	page_header('Der Gedenkstein');
	
	output ('`&`c`bAuf einem dunklen Stein steht geschrieben:`b`c`n`n');
	
	showcasulties($str_raceid);
	
	addnav('Zurück','racesspecial.php?race=' . $str_raceid);

}

else {
	
	page_header(strip_appoencode($arr_race['raceroom_name'],3));
	
	output('`c`b`&'.$arr_race['raceroom_name'].'`b`c`n`n'.$arr_race['raceroom_desc'].'`&`n`n',true);
	
	addcommentary(false);
	
	$str_section = 'raceroom_'.$arr_race['id'];
	
	viewcommentary($str_section,'Sagen:',25);
	
	addnav('Zur Rassenliste','racesspecial.php?op=show_list&race='.$str_raceid);
		addnav('Zum Gedenkstein','racesspecial.php?op=pvp_deads&race='.$str_raceid);
	
	addnav('Zurück');
	if($arr_race['raceroom'] == 1) {
		addnav('Zum Wald','forest.php');
	}
	else {
		addnav('Zum Wohnviertel','houses.php');
	}
	
}

page_footer();
?>