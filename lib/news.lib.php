<?php

/**
 * Fügt Eintrag zu den Spielnews hinzu
 *
 * @param string $news Nachricht
 * @param int $int_acctid AccountID; optional, Standard SpielerID
 */
function addnews($news, $int_acctid = 0)
{
	global $session;
	
	$int_acctid = (!$int_acctid ? $session['user']['acctid'] : $int_acctid);
	
	$sql = 'INSERT INTO news(newstext,newsdate,accountid) VALUES ("'.addslashes($news).'",NOW(),'.$int_acctid.')';
	db_query($sql);

}

/**
 * Fügt Eintrag zu den Straftaten hinzu
 *
 * @param string $crimes Straftat-Text
 */
function addcrimes($crimes)
{
	global $session;
	$sql = 'INSERT INTO crimes(newstext,newsdate,accountid) VALUES ("'.addslashes($crimes).'",NOW(),'.$session['user']['acctid'].')';
	db_query($sql);
}

/**
 * Fügt Eintrag zu den aktuellen Fällen im Gericht hinzu
 *
 * @param string $case Straftat-Text
 * @param int $suspect AccountID des Verdächtigen
 */
function addtocases($case,$suspect)
{
	global $session;
	$sql = 'INSERT INTO cases(newstext,accountid,judgeid,court) VALUES ("'.addslashes($case).'",'.$_GET[suspect].','.$session['user']['acctid'].',0)';
	db_query($sql);
}

/**
 * Fügt Eintrag zu den Expeditionsnews hinzu
 *
 * @param string $news Nachricht
 */
function addnews_ddl($news)
{
	global $session;
	$sql = 'INSERT INTO ddlnews(newstext,newsdate,accountid) VALUES ("'.addslashes($news).'",NOW(),'.$session['user']['acctid'].')';
	db_query($sql);
}
?>
