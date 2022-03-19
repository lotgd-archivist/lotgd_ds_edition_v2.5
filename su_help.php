<?php
/**
 * su_help.php: Art FAQ für Grottenolme
 * @author talion
 * @version DS-E V/2
*/

define('SUHELP_CAT','suhelp');
$str_filename = basename(__FILE__);
require_once('common.php');

if(!$session['user']['loggedin']) {
	echo('Nicht eingeloggt');
	exit;
}

// Normale User rausschmeißen
su_lvl_check(1,true,true);

popup_header('"Beeing a GOD" for Dummies ; )');

$str_out = '<script language="javascript">window.resizeTo(750,550);</script>';

// Welchen Hilfebereich wollen wir haben?
$str_cat = (empty($_REQUEST['cat']) ? '' : stripslashes($_REQUEST['cat']));

// Stichwort
$str_search = (empty($_POST['search']) ? '*' : stripslashes($_POST['search']));

// Liste der Subkats
$str_sql = 'SELECT DISTINCT subcategory AS category FROM extended_text WHERE category="'.SUHELP_CAT.'" AND subcategory <> "" ORDER BY subcategory ASC';
$res = db_query($str_sql);

$str_lnk = $str_filename;

$str_out .= '`&`c<form method="POST" action="'.$str_lnk.'"><p>Wähle eine Kategorie <select id="cat" name="cat" onchange="this.form.submit();">
					<option value=""> - Bitte wählen - </option>
					<option value="*" '.($str_cat == '*' ? 'selected="selected"':'').'>Alle</option>';
while ($arr_result = db_fetch_assoc($res))
{
	$str_out .= '<option value="'.$arr_result['category'].'" '.(($arr_result['category']==$str_cat)?'selected':'').'>'.$arr_result['category'].'</option>';
}

$str_out .= '</select>&nbsp;</p><p>
			 und / oder Suche nach Stichwort: <input type="text" maxlength="50" size="20" name="search" value="'.($str_search != '*' ? $str_search : '').'" /></p>
			 &nbsp;<input type="submit" value="Go!">
		</form>`c<hr />`n';

// Gewählte Texte abrufen
$arr_txt = get_extended_text('*',SUHELP_CAT,true,false,$str_cat,$str_search);

$int_counter = 1;

if(!is_array($arr_txt)) {
	$str_out .= '`bBitte eine Auswahl treffen.`b`n`n';
	$str_out .= '`bKeine Texte gefunden!`b';
}
else {
	$str_out .= '`c`Q`bDie Enzyklopäde der Götter weiß folgendes zu diesem Thema:`t`n`n`b
					<table>';	
	
	if(isset($arr_txt['id'])) {
		$arr_lst[] = $arr_txt;
	}
	else {
		$arr_lst = $arr_txt;
	}
	
	foreach ($arr_lst as $t) {
		$str_out .= '<tr><td valign="top">`Q`b'.$int_counter.'.`b</td><td>`t'.$t['text'].'</td></tr>
					<tr><td colspan="2">&nbsp;</td></tr>';
		$int_counter++;
	}
	$str_out .= '</table></div>';
	
}

$str_out .= '`n`c';

output($str_out,true);

popup_footer();
?>