<?php
require_once "common.php";

//define('ITEMS_TABLE','items_old');

page_header('Upgrade der Dragonslayer-Edition');


$sql = 'SELECT tpl_id FROM items_tpl';

$res = db_query($sql);

for($i=1;$i<=db_num_rows($res);$i++) {
	
	$tpl = db_fetch_assoc($res);
	
	db_query('UPDATE items_tpl SET tid = '.$i.' WHERE tpl_id="'.$tpl['tpl_id'].'"');
	
	db_query('UPDATE items SET tid = '.$i.' WHERE tpl_id="'.$tpl['tpl_id'].'"');
	
}



// Transfer der gel�schten Felder in accounts -> account_extra_info

/*$sql = 'SELECT acctid,birthday,referer,refererawarded,namecheck,namecheckday,avatar FROM accounts ORDER BY acctid ASC';
$res = db_query($sql);

while($a = db_fetch_assoc($res)) {
	
	$sql = 'UPDATE account_extra_info SET
				birthday="'.addslashes($a['birthday']).'",
				avatar="'.addslashes($a['avatar']).'",
				referer="'.$a['referer'].'",
				refererawarded="'.$a['refererawarded'].'",
				namecheck="'.$a['namecheck'].'",
				namecheckday="'.$a['namecheckday'].'"
			WHERE acctid = '.$a['acctid'];
			
	db_query($sql);
	
	if(db_affected_rows()) {
		
		output('`n`^accounts -> aei: User Nr. '.$a['acctid'].' erfolgreich �bertragen!');
		
	}
	else {
	
		output('`n`$accounts -> aei: `bUser Nr. '.$a['acctid'].' NICHT erfolgreich �bertragen!`b');
		
	}
	
}
// END Transfer a -> aei

// Transfer der gel�schten Felder in aei -> accounts
$sql = 'SELECT acctid,expedition FROM account_extra_info WHERE expedition > 0 ORDER BY acctid ASC';
$res = db_query($sql);

while($a = db_fetch_assoc($res)) {
	
	$sql = 'UPDATE accounts SET
				expedition="'.$a['expedition'].'"
			WHERE acctid = '.$a['acctid'];
			
	db_query($sql);
	
	if(db_affected_rows()) {
		
		output('`n`^aei -> accounts: User Nr. '.$a['acctid'].' erfolgreich �bertragen!');
		
	}
	else {
	
		output('`n`$`baei -> accounts: User Nr. '.$a['acctid'].' NICHT erfolgreich �bertragen!`b');
		
	}
	
}
// END Transfer a -> aei

// �berfl�ssige Privatraumeinladungen l�schen
$ids = '';
$arr_bes = array();

$res = db_query(' SELECT * FROM items WHERE name="Besitzurkunde f�r Privatraum" AND owner>0 ');

while($b = db_fetch_assoc($res)) {
	
	// in diesem Haus (value1) f�r diesen Besitzer (owner)
	$arr_bes[$b['value1']][$b['owner']] = true;
	
}

$res = db_query(' SELECT * FROM items WHERE name="Zugang zu Privatraum" ');
while($e = db_fetch_assoc($res)) {
	
	if(!$arr_bes[$e['value1']][$e['value2']]) {
		$ids .= ','.$e['id'];
	}
	
}

db_query('DELETE FROM items WHERE id IN ( -1 '.$ids.') AND name="Zugang zu Privatraum"');

// END �berfl�ssige Privatraumeinladungen l�schen



// UNIKATE
$nr = item_count('  name LIKE "%Unikat - %" ');

item_set(' name LIKE "%Unikat - %" ', array('tpl_id'=>'unikat') );
$count = db_affected_rows();
output('`n`n'.$count.' von '.$nr.' Unikaten schabloniert.');
// END UNIKATE

// Waffen
$nr = item_count('  class="Waffe" ');

item_set(' class="Waffe" ', array('tpl_id'=>'waffedummy') );
$count = db_affected_rows();
output('`n`n'.$count.' von '.$nr.' Waffen schabloniert.');
// END Waffen

// R�stungen
$nr = item_count('  class="R�stung" ');

item_set(' class="R�stung" ', array('tpl_id'=>'rstdummy') );
$count = db_affected_rows();
output('`n`n'.$count.' von '.$nr.' R�stungen schabloniert.');
// END R�stungen


// Troph�en
$nr = item_count('  class="Troph�e" ');

item_set(' class="Troph�e" ', array('tpl_id'=>'trph') );
$count = db_affected_rows();
output('`n`n'.$count.' von '.$nr.' Troph�en schabloniert.');
// END Troph�en

// Blumenbeete
$nr = item_count('  class="Beet" ');

item_set(' class="Beet" ', array('tpl_id'=>'beet') );
$count = db_affected_rows();
output('`n`n'.$count.' von '.$nr.' Blumenbeeten schabloniert.');
// END Blumenbeete

// Orden
$nr = item_count('  name="`2Bestpreis`0" OR name="`4Verwundetenmedaille`0" OR name="`tBronzenes Ehrenkreuz`0"
					OR name="�tBronzenes Ehrenkreuz`0"
					OR name="`&Silbernes Ehrenkreuz`0" OR name="�^Goldenes Ehrenkreuz`0"
					OR name="`vTapferkeitsmedaille`0" OR name="`#Verdienstorden der B�rgerwehr`0"
					OR name="`1Verdienstorden der B�rgerwehr`0" ');

item_set(' name="`2Bestpreis`0" OR name="`4Verwundetenmedaille`0" OR name="`tBronzenes Ehrenkreuz`0"
					OR name="�tBronzenes Ehrenkreuz`0"
					OR name="`&Silbernes Ehrenkreuz`0" OR name="�^Goldenes Ehrenkreuz`0"
					OR name="`vTapferkeitsmedaille`0" OR name="`#Verdienstorden der B�rgerwehr`0"
					OR name="`1Verdienstorden der B�rgerwehr`0" ', array('tpl_id'=>'medal') );
$count = db_affected_rows();
output('`n`n'.$count.' von '.$nr.' Medaillen schabloniert.');
// END Blumenbeete

// Elfenkunst
$nr = item_count('  class="Schmuck" AND name="Elfenkunst" ');

item_set(' class="Schmuck" AND name="Elfenkunst" ', array('tpl_id'=>'elfknst') );
$count = db_affected_rows();
output('`n`n'.$count.' von '.$nr.' Elfenkunst schabloniert.');
// END Elfenkunst


// Alle Tpl abrufen
$res = item_tpl_list_get(' 1 ');

while($i = db_fetch_assoc($res)) {
	
	item_set(' name="'.addslashes($i['tpl_name']).'" ', array('tpl_id'=>$i['tpl_id']) );
	$count = db_affected_rows();
	output('`n`^'.$count.' '.$i['tpl_name'].'`^ Items auf '.$i['tpl_id'].'`^ schabloniert.');
	
}
// END Tpl setzen

// Nicht zugeordnete M�bel
$nr = item_count('  class="M�bel" AND tpl_id="" ');

item_set(' class="M�bel" AND tpl_id="" ', array('tpl_id'=>'mbl') );
$count = db_affected_rows();
output('`n`n'.$count.' von '.$nr.' nicht zugeordneten M�beln schabloniert.');
// END Elfenkunst

// Goldenes Ei
item_add($session['user']['acctid'],'goldenegg');
if(db_affected_rows()) {
	output('`n`^Goldenes Ei hinzugef�gt!');
	savesetting('hasegg',stripslashes($session['user']['acctid']));
}
else {
	output('`n`4`bGoldenes Ei NICHT hinzugef�gt!`b');
}

// END Goldenes Ei
*/

addnav('Zur�ck','village.php');

page_footer();
?>