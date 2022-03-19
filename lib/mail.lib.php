<?php

/**
 * Kategoriebezeichnungen für Anfragen
 */
$ARR_PETITION_KATS = array(0=>"Keine","Fehler","Fragen","Fragen zu Bans","Wünsche","Unterhaltsames","Diskussionen","Hilferuf");


/**
 * Fügt Systemmail-Antwort auf Anfrage in Datenbank ein
 *
 * @param string Betreff
 * @param string Mailinhalt
 * @param int ID der Anfrage
 * @param int AcctID des Absenders
 * @param int Mail gelesen? (optional, Standard 0)
 * @param int AcctID des Empfängers (optional, Standard 0)
 * @param int ID der Mail (optional, Standard 0)
 * @author Chaosmaker
 */
function petitionmail($subject,$body,$petition,$from,$seen=0,$to=0,$messageid=0)
{
	$subject = safeescape($subject);
	$subject=str_replace("\n",'',$subject);
	$subject=str_replace("`n",'',$subject);
	$body = safeescape($body);

	$sql = 'INSERT INTO petitionmail (petitionid,messageid,msgfrom,msgto,subject,body,sent,seen) VALUES ('.(int)$petition.','.(int)$messageid.','.(int)$from.','.(int)$to.',"'.$subject.'","'.$body.'",now(),"'.$seen.'")';
	db_query($sql);
	$sql = 'UPDATE petitions SET lastact=NOW() WHERE petitionid="'.(int)$petition.'"';
	db_query($sql);
}

/**
 * Fügt Systemmail in Datenbank ein (= Versenden einer InGame-Mail an Spieler)
 * Erledigt dabei auch Weiterleiten an EMail etc.
 *
 * @param int AcctID des Empfängers
 * @param string Betreff
 * @param string Mailinhalt
 * @param int AcctID des Absenders (optional, Standard 0, wenn 0: System)
 * @param bool Benachrichtigungs-EMail an Empfänger
 */
function systemmail($to,$subject,$body,$from=0,$noemail=false)
{
	global $session;
	
	$subject = safeescape($subject);
	$subject=str_replace("\n",'',$subject);
	$subject=str_replace('`n','',$subject);
	$body = safeescape($body);
	//echo $subject."<br>".$body;
	$sql = 'SELECT prefs,emailaddress FROM accounts WHERE acctid="'.$to.'"';
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	db_free_result($result);
	$prefs = unserialize($row['prefs']);

	if ($prefs['dirtyemail']==false)
	{
		$subject=soap($subject);
		$body=soap($body);
	}
	
	// Stats
	if($from > 0) {
		
		user_set_stats( array('mailsent'=>'mailsent+1'), $from );
		user_set_stats( array('mailreceived'=>'mailreceived+1'), $to );
		
	}
	// END Stats

	$sql = 'INSERT INTO mail (msgfrom,msgto,subject,body,sent,ip) VALUES ('.(int)$from.','.(int)$to.',"'.$subject.'","'.$body.'",now(),"'.$_SERVER['REMOTE_ADDR'].'")';
	db_query($sql,false);
	
    // Auf verdächtigen Inhalt prüfen und ggf sichern
    if($from > 0) {
    	// Cache
    	
    	$mixed_words = cache_get('suspicious_words');
    	
    	if(false === $mixed_words) {
    		$sql = 'SELECT name FROM suspicious_words';
			$result = db_query($sql);
			$mixed_words = db_create_list($result);
			cache_set('suspicious_words',$mixed_words);
    	}
		
    	if(sizeof($mixed_words) > 0) {
			$str_check = strtolower($body);
			
		    foreach ($mixed_words as $w)
		    {
			      if (substr_count($str_check, $w['name']) > 0)
			      {
			           $sql = 'INSERT INTO suspicious_mail (msgfrom,msgto,subject,body,sent,ip) VALUES ('.(int)$from.','.(int)$to.',"'.$subject.'","'.$body.'",now(),"'.$_SERVER['REMOTE_ADDR'].'")';
			           db_query($sql,false);
			           break;
			      }
			}
    	}
    }
    // Ende Prüfung auf verdächtigen Inhalt
	
	$email=false;
	if(getsetting('emailonmail',0)) {
		if ($prefs['emailonmail'] && $from>0)
		{
			$email=true;
		}
		elseif($prefs['emailonmail'] && $from==0 && $prefs['systemmail'])
		{
			$email=true;
		}
		if (!is_email($row['emailaddress'])) 
		{
			$email=false;
		}
	}
	if ($email && !$noemail)
	{
		$sql = 'SELECT name,login FROM accounts WHERE acctid='.$from;
		$result = db_query($sql);
		$row1=db_fetch_assoc($result);
		db_free_result($result);
		if ($row1['name']!='') {
			$fromline = "\nVon: ".preg_replace('/[`]./','',$row1['name'])."\n";
		}
		// We've inserted it into the database, so.. strip out any formatting
		// codes from the actual email we send out... they make things
		// unreadable
		$body = preg_replace('/[`]n/', "\n", $body);
		$body = preg_replace('/[`]./', '', $body);
		mail($row['emailaddress'],'Neue Spiel-Nachricht',
		"Du hast eine neue Nachricht in ".getsetting('townname','Atrahor')." ( http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME'])." ) empfangen.\n".
		$fromline.""
		.'Betreff: '.preg_replace("'[`].'",'',stripslashes($subject))."\n"
		."Nachricht: \n".stripslashes($body)."\n"
		."\nDu kannst diese Meldungen in deinen Einstellungen abschalten.",
		'From: '.getsetting('gameadminemail','postmaster@localhost')
		);
	}
}

/**
 * Checkt EMail-Adresse auf Gültigkeit
 *
 * @param string EMail-Adresse
 * @return bool TRUE, wenn korrekte Adresse, sonst FALSE
 */
function is_email($email)
{
	return preg_match('/[[:alnum:]_.-]+[@][[:alnum:]_.-]{2,}.[[:alnum:]_.-]{2,}/',$email);
}

?>
