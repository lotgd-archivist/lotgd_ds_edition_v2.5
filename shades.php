<?php
/**
 * shades.php: Die Unterwelt - Hauport für die Toten. 
 * @author LOGD-Core
 * @version DS-E V/2
*/

require_once('common.php');

page_header('Land der Schatten');
addcommentary();
checkday();

music_set('shades');

if ($session['user']['alive']) {
	redirect('village.php');
}

/**
 * @ TODO: Talion - sinnvollere Lösung finden, z.B. generelles Flag für Buffs, das die Nutzung im Totenreich erlaubt.
 */
if(isset($session['bufflist']['decbuff']) && $session['bufflist']['decbuff']['state'] == 20 && $session['bufflist']['decbuff']['rounds'] > 0) {	// untot
	$buffsave=$session['bufflist']['decbuff'];
	$session['bufflist']=array();
	$session['bufflist']['decbuff']=$buffsave;		
}
else {
	$session['bufflist']=array();
}

output('`$Du wandelst jetzt unter den Toten, du bist nur noch ein Schatten. Überall um dich herum sind die Seelen der in alten Schlachten und bei  
gelegentlichen Unfällen gefallenen Kämpfer. Jede trägt Anzeichen der Niedertracht, durch welche sie ihr Ende gefunden haben.`n`n
Im Dorf dürfte es jetzt etwa `^'.getgametime().'`$ sein, aber hier herrscht die Ewigkeit und Zeit gibt es mehr als genug.`n`n
Die verlorenen Seelen flüstern ihre Qualen und plagen deinen Geist mit ihrer Verzweiflung.`n');

viewcommentary('shade','Verzweifeln',25,'jammert');
addnav('Das Totenreich');
addnav('Der Friedhof','graveyard.php');

//RUNEN MOD
//wenn man eine eiwazrune hat, kommt man wieder nach oben
if( item_count('tpl_id="r_eiwaz" AND owner='.$session['user']['acctid']) > 0 ){
	addnav('Runenkraft');
	addnav('Benutze eine Eiwaz-Rune','newday.php?resurrection=rune');	
}
//RUNEN END


if ($session['user']['acctid']==getsetting('hasegg',0)){ 
    addnav('Das goldene Ei');
	addnav('Benutze das goldene Ei','newday.php?resurrection=egg');
}

addnav('Sonstiges');
addnav('`^Drachenbücherei`0','library.php');
addnav('In Diskussionsräume geistern','ooc.php?op=ooc');
addnav('Einwohnerliste','list.php');
addnav('In Ruhmeshalle spuken','hof.php');
addnav('Zurück');
addnav('Neuigkeiten','news.php');

if ($session['user']['superuser'] > 0)
{
	addnav('Back to Life','superuser.php?op=iwilldie');
}
  

page_footer();
?>