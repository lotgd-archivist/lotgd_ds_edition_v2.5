<?
/**
 * su_retitle.php: 
 * @author LOGD-Core / Drachenserver-Team
 * @version DS-E V/2
*/

$str_filename = basename(__FILE__);
require_once('common.php');
su_check(SU_RIGHT_RETITLE,true);

page_header('Retitler');
addnav('Zurück');
addnav('G?Zurück zur Grotte','superuser.php');
addnav('W?Zurück zum Weltlichen',$session['su_return']);

addnav('Aktionen');
addnav('Namen korrigieren',$str_filename.'?op=rebuild');

output('`c`&`bRetitler`b`c`n`n');

$int_accs_p = 200;

if ($_GET['op']=="rebuild"){
	
	$int_step = (isset($_GET['step']) ? (int)$_GET['step'] : 0);

	$sql = "SELECT name,login,title,dragonkills,a.acctid,sex,ae.ctitle,ae.cname,ae.csign
    		FROM  accounts a 
    		LEFT JOIN account_extra_info ae USING(acctid)
    		ORDER BY acctid ASC 
    		LIMIT ".($int_step*$int_accs_p).','.$int_accs_p;
    $result = db_query($sql);
	
    if(db_num_rows($result) == 0) {
    	
    	systemlog('hat mit Retitler Namen repariert',$session['user']['acctid']);
    	
    	output('`qOperation abgeschlossen:`n`n');
    	output($session['msg']);
    	    	
    }
    else {
    	$int_first_id = 0;
    	$int_last_id = 0;
    	
	    while($arr_acc = db_fetch_assoc($result)) {

	    	if(!$int_first_id) {
	    		$int_first_id = $arr_acc['acctid'];
	    	}
	    	 	
	    	if(!user_check_title_nochange( strip_appoencode(strtolower($arr_acc['title']),3) )) {
	    		$arr_acc['title'] = $titles[$arr_acc['dragonkills']][$arr_acc['sex']];
	    		if(empty($arr_acc['title'])) {
	    			$arr_title = end($titles);
	    			$arr_acc['title'] = $arr_title[$arr_acc['sex']];
	    		}
	    	}
	    	
	    	$str_oname = $arr_acc['name'];
	    	
	    	if(substr($str_oname,0,34) != 'Neuling mit unzulässigem Namen Nr.') {
	    		$str_newname = user_set_name($arr_acc['acctid'],false,$arr_acc);
	    	}
	    	else {
	    		$str_newname = $str_oname;
	    	}
			
	    	$str_sql = 'UPDATE accounts SET name="'.addslashes($str_newname).'", title="'.addslashes($arr_acc['title']).'" WHERE acctid = '.$arr_acc['acctid'];
	    	db_query($str_sql);
	    	    	
	        $session['msg'] .= "`@Ändere `^$str_oname`@ auf `^$str_newname`@.`n";
	        
	        $int_last_id = $arr_acc['acctid'];
	
	    }
	   	    
	    $str_lnk = $str_filename.'?op=rebuild&step='.($int_step+1);
	    $str_jslink = addnav('',$str_lnk);
	    
	    output('`qFühre Schritt Nr. '.($int_step+1).' durch: Ändere Accounts ID '.($int_first_id).' bis '.($int_last_id).'. 
	    		<script language="javascript">window.setTimeout("window.location=\''.$str_jslink.'\'",2000);</script>',true);
    }
}
else{
    output('`qMit diesem Werkzeug lassen sich die Namen aller Spieler auf den korrekten Stand (Titel, Name etc.) zurücksetzen.`n
    		Manuelle Änderungen (Über den Usereditor) werden dabei zum größten Teil verlorengehen!');
   	$session['msg'] = '';
}

page_footer();
?>
