<?php

require_once("common.php");

page_header('Inventar');

// Bei Eintritt in Beutel: Letzte besuchte Seite speichern
if($_GET['r']) {
	set_restorepage_history($g_ret_page);
	redirect('invent.php');
}

$str_ret = get_restorepage_history();

output('`&`c`bGesammelter Besitz von '.$session['user']['name'].'`&:`c`b`n`n');

if($session['user']['weapon'] != 'Fists' ||  $session['user']['armor'] != 'T-Shirt') {
	
	addnav('Ausrüstung');
	
	if($session['user']['weapon'] != 'Fists') { addnav( $session['user']['weapon'].'`0 ablegen!' , 'invhandler.php?op=abl&what=weapon&ret='.urlencode(calcreturnpath()) ); }
	if($session['user']['armor'] != 'T-Shirt') { addnav( $session['user']['armor'].'`0 ablegen!' , 'invhandler.php?op=abl&what=armor&ret='.urlencode(calcreturnpath()) ); }
	
}

$int_depo = (int)$_REQUEST['depo'];
$str_depo_where = ' ';
if($int_depo == 1) {	// Beutel
	$str_depo_where .= 'AND (deposit1=0 OR deposit1='.ITEM_LOC_EQUIPPED.')';
}
elseif($int_depo == 2) {
	$str_depo_where .= 'AND (deposit1>0 AND deposit1!='.ITEM_LOC_EQUIPPED.')';
}

addnav('Besitz');
addnav(($int_depo == 0 ? '`^' : '').'..alles','invent.php?r='.urlencode($ret).'&depo=0');
addnav(($int_depo == 1 ? '`^' : '').'..im Beutel','invent.php?r='.urlencode($ret).'&depo=1');
addnav(($int_depo == 2 ? '`^' : '').'..in Häusern','invent.php?r='.urlencode($ret).'&depo=2');

item_show_invent( ' showinvent=1 AND owner='.$session['user']['acctid'].$str_depo_where, false, 0, 1, 1, '`iDein Beutel ist leer!`i' );

addnav('Sonstiges');

if(!empty($str_ret)) {
	addnav('Zurück',$str_ret);
}
else {
	addnav('Zu den News','news.php');
}


page_footer();
?>
