<?php
/**
* buffs.lib.php: Funktionsbibliothek für Methoden, die zur Verwaltung / Bereitstellung der Buff-Funktionalität benötigt werden
* @author Talion / Drachenserver-Team
* @version DS-E V/2
*/

// Konstantendefs für 'activate' und 'elapse'-Wert
define('BUFF_ACT_PAGESTART',1);
define('BUFF_ACT_BATTLESTART',2);
define('BUFF_ACT_ROUNDSTART',4);
define('BUFF_ACT_OFFENSE',8);
define('BUFF_ACT_DEFENSE',16);
define('BUFF_ACT_VICTORY',32);
define('BUFF_ACT_DEFEAT',64);

// Konstantendefs für Buffbesitzer
define('BUFF_OWNER_PLAYER',1);
define('BUFF_OWNER_MINION',2);
define('BUFF_OWNER_BADGUY',4);

/**
*@desc Fügt Buff zu Buff-Liste hinzu
*@author talion
*/
function buff_add ($arr_buff, $int_owner=0, $int_acctid=0, $int_ownerid=0) {
	
	global $session;
	
	if(empty($arr_buff) || !is_array($arr_buff)) {
		return(false);
	}
	
	// Feststellen, ob Buff zu aktuellem Spieler hinzugefügt werden soll
	
	
	switch($int_owner) {
		
		case BUFF_OWNER_PLAYER:
			
			
			
		break;
		
	}
	
	$session['bufflist'][ $arr_buff['name'] ] = $arr_buff;
	buff_unset();
	buff_set();
		
}

function buff_remove ($buff) {
	
	global $session;
	
	if($buff === true) {
		unset($session['bufflist']);
	}
	else {
		unset($session['bufflist'][ $buff ]);
	}
	
}

function buff_backup ($buff) {
	
	global $session;
	
	if($buff === true) {
		$session['user']['buffbackup'] = serialize($session['bufflist']);
	}
	else {
		$buffback = unserialize($session['user']['buffbackup']);
		$buffback[$buff] = $session['bufflist'][$buff];
		$session['user']['buffbackup'] = serialize($buffback);
	}
	
}

// für NICHT-Battle-Buffs
function buff_set () {
	
	global $session;
			
	$buffs_applied = array();
	
	foreach($session['bufflist'] as $b) {

		$buffs_applied['charm'] += $b['plus_charm'];
		$buffs_applied['attack'] += $b['plus_attack'];
		$buffs_applied['defence'] += $b['plus_defence'];
		
	}
	
	$session['buffs_applied'] = $buffs_applied;
	
}

function buff_unset () {

	global $session;
	
	$buffs_applied = $session['buffs_applied'];
	
	unset($session['buffs_applied']);
	
	if(sizeof($buffs_applied) > 0) {
			
		foreach($buffs_applied as $key=>$b) {
			
			$session['user'][$key] = max($session['user'][$key]-$b,0);
							
		}
	}
	
	unset($buffs_applied);
		
}

?>