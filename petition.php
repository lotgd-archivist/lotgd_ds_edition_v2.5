<?php
require_once('common.php');

$str_filename = basename(__FILE__);

if($_GET['op'] == 'help' || $_GET['op']=='faq') {
    
    $str_default = 'faq_start';
    
    popup_header(getsetting('townname','Atrahor').' - Hilfe / FAQ');
    
    $str_page = (empty($_GET['page']) ? $str_default : $_GET['page']);
    
    $str_txt = get_extended_text($str_page,'rules_faq',false,false);
    
    output('<script language="javascript">window.resizeTo(600,550);</script>',true);
    
    if (false !== $str_txt) {
    	
        output($str_txt,true);
        
    }
    else {
        
        output('`$Seite konnte nicht gefunden werden!');
                
    }
        
}
else if($_GET['op']=="rules_short")
{
	popup_header("Kurzfassung der Regeln");
	output('
	<a href="petition.php?op=faq">Inhaltsverzeichnis</a>`n`n
	'.get_extended_text('rules_short'),true);

}
else
{
	popup_header("Anfrage für Hilfe");
	
	output('<script language="javascript">window.resizeTo(600,550);</script>
		`c`b`&Anfrage an die Administration`&`b`c`n`n');
	
	if (count($_POST)>0){
		
		if(empty($_POST['description'])) {
			output('`$Das Nichts ist zweifelsohne eine erhabene Tatsache, nur wird die Administration mit einer leeren Anfrage 
					wohl weniger als nichts anfangen können.`n`0');
		}
		elseif( (empty($_POST['email']) || !is_email(stripslashes($_POST['email']))) && !$session['user']['loggedin']) {
			output('`$Wie willst du denn eine Antwort auf diese Anfrage erhalten, wenn du keine gültige EMail-Adresse angibst?`n`0');
		}
		else {
		
			$p = $session['user']['password'];
			unset($session['user']['password']);
					
			if(!$session['user']['loggedin']) {
				$sql = 'SELECT login,acctid,uniqueid,lastip FROM accounts WHERE lastip = "'.addslashes($session['lastip']).'" OR uniqueid = "'.addslashes($session['uniqueid']).'" ORDER BY login, acctid';
				$res = db_query($sql);
				
				$sec_info = '';
				
				while($r = db_fetch_assoc($res) ) {
					
					$sec_info .= '`n'.$r['login'].' (AcctID '.$r['acctid'].', IP '.$r['lastip'].', ID '.$r['uniqueid'].')';
					
				}
			}
			
			$sql = "INSERT INTO petitions (author,date,body,pageinfo,lastact,IP,ID,connected,kat) VALUES (".(int)$session['user']['acctid'].",now(),\"".addslashes(output_array($_POST))."\",\"".addslashes(output_array($session,"Session:"))."\",NOW(),\"".$session['lastip']."\",'".$session['uniqueid']."','".addslashes($sec_info)."',".(int)$_POST['kat'].")";
			db_query($sql);
					
			$session['user']['password']=$p;
					
			output("Deine Anfrage wurde an die Admins gesendet. Bitte hab etwas Geduld, die meisten Admins 
			haben Jobs und Verpflichtungen ausserhalb dieses Spiels. Antworten und Reaktionen können eine Weile dauern.");
			popup_footer();		
		}
	}

	$str_kat_enum = 'enum';
	
	foreach($ARR_PETITION_KATS as $id => $kat) {
		
		$str_kat_enum .= ','.$id.','.$kat;
		
	}
	
	$arr_data = array('charname'=>$session['user']['login'],
						'email'=>$session['user']['emailaddress'],
						'description'=>stripslashes($_POST['description'])
						);
	$arr_form = array('charname'=>'Name deines Characters:',
						'email'=>'Deine E-Mail Adresse:',
						'kat'=>'Art der Anfrage:,'.$str_kat_enum,
						'description'=>'Beschreibe dein Problem:`n,textarea,35,8');
	
	output(get_extended_text('anfrage_beschreibung','*',false,false).'
			<form action="petition.php?op=submit" method="POST">',true);
						
	showform($arr_form,$arr_data,false,'Absenden!');
	
	output('</form>',true);
	
}
popup_footer();
?>
