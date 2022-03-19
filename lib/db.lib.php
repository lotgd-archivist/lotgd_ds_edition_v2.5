<?php

// 21072004

//do some cleanup here to make sure magic_quotes_gpc is ON, and magic_quotes_runtime is OFF, and error reporting is all but notice.
error_reporting (E_ALL ^ E_NOTICE);
if (!get_magic_quotes_gpc()){
	set_magic_quotes($_GET);
	set_magic_quotes($_POST);
	set_magic_quotes($_SESSION);
	set_magic_quotes($_COOKIE);
	set_magic_quotes($HTTP_GET_VARS);
	set_magic_quotes($HTTP_POST_VARS);
	set_magic_quotes($HTTP_COOKIE_VARS);
	ini_set('magic_quotes_gpc',1);
}
set_magic_quotes_runtime(0);

function set_magic_quotes(&$vars)
{
	//eval('\$vars_val =& \$GLOBALS[$vars]$suffix;');
	if (is_array($vars))
	{
		foreach ($vars as $key => $val)
		//reset($vars);
		//while (list($key,$val) = each($vars))
		{
			set_magic_quotes($vars[$key]);
		}
	}
	else
	{
		$vars = addslashes($vars);
	}
}

define('DBTYPE','mysql');

$dbqueriesthishit=0;
$dbtimethishit = 0;
// Wie lang soll die Liste der letzten SQL-Queries in der Session sein?
// 0 um Funktion ganz auszuschalten
$dbquerylog = 50;

// logquery: 
/**
 * Führt angegebenen Query in Datenbank aus.
 *
 * @param string $sql Query
 * @param bool $logquery Soll query aus Debuggründen temporär in session mitgeloggt werden?
 * @return DB-Result
 */
function db_query($sql, $logquery=true)
{
	global $session,$dbqueriesthishit,$dbtimethishit,$dbquerylog;

	$dbqueriesthishit++;
	$dbtimethishit -= getmicrotime();
	$fname = DBTYPE.'_query';
	$r = $fname($sql);

	if(db_errno(LINK)) {
		$str_msg = '<i>Adresse: '.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'</i>
					<pre>'.HTMLEntities($sql).'</pre>
					<b>'.db_errno(LINK).':</b> '.db_error(LINK);

		// Nur Systemlogeintrag vornehmen, wenn feststeht, dass nicht Systemlog den Fehler hervorruft
		if(!strpos($sql,'syslog')) {
			systemlog('`&DB-Fehler: `^'.$str_msg, 0, $session['user']['acctid']);
		}

		echo('<div style="font-family:Helvetica; color:darkblue;">
			<h2 align="center" style="color:green;">Don\'t Panic!</h2>
			Soeben ist durch eine äußerst unwahrscheinliche Dimensionslücke weit draußen in den unerforschten
			Einöden eines total aus der Mode gekommenen Ausläufers des westlichen Spiralarms der Galaxis
			ein Datenbankfehler im Innenleben dieses Servers aufgetreten.<br>
			Bitte kopiere den untenstehenden Fehlertext und teile ihn der Administration per Anfrage mit!
			Du solltest auch beschreiben, was du unmittelbar davor getan / angeklickt hast.<br>
			Danke für dein Verständnis!<br>
			Hier kommt die Meldung:
			<p>'.$str_msg.'</p>
			Um weiterspielen zu können, sollte ein Klick auf den Zurück-Button deines Browsers ausreichen. Falls nicht,
			schließe das Browserfenster und rufe die Adresse neu auf. Schreibe dann von der Startseite aus eine Anfrage.</div>'
			);
			exit;
	}

	$dbtimethishit += getmicrotime();

	if($logquery && $dbquerylog > 0) {
		if(is_array($session['debug_querylog']) && sizeof($session['debug_querylog']) >= $dbquerylog) {
			array_shift($session['debug_querylog']);
		}
		$session['debug_querylog'][] = $sql;
	}

	return $r;
}

/**
 * Gibt zuletzt eingefügte AutoIncrement-ID zurück
 *
 * @param ressource Datenbank-Ressource 
 * @return int ID
 */
function db_insert_id($link=false)
{
	global $dbtimethishit;
	$dbtimethishit -= getmicrotime();
	$fname = DBTYPE.'_insert_id';
	if ($link===false)
	{
		$r = $fname();
	}
	else
	{
		$r = $fname($link);
	}
	$dbtimethishit += getmicrotime();
	return $r;
}

/**
 * Gibt zuletzt vorgefallenen Fehler zurück.
 *
 * @param ressource Datenbank-Ressource 
 * @return string Fehler
 */
function db_error($link)
{
	$fname = DBTYPE.'_error';
	$r = $fname($link);

	return $r;
}

/**
 * Gibt zuletzt vorgefallene Fehler-# zurück.
 *
 * @param ressource Datenbank-Ressource 
 * @return int Fehlernummer
 */
function db_errno($link)
{
	$fname = DBTYPE.'_errno';
	$r = $fname($link);

	return $r;
}

/**
 * Extrahiert Tupel aus DB-Result in Form eines assoz. Arrays
 *
 * @param result DB-Result
 * @return array Assoz. Ergebnisarray
 */
function db_fetch_assoc($result)
{
	global $dbtimethishit;
	$dbtimethishit -= getmicrotime();
	$fname = DBTYPE.'_fetch_assoc';
	$r = $fname($result);
	$dbtimethishit += getmicrotime();
	return $r;
}

/**
 * Gibt Anzahl d. Tupel in DB-Result zurück
 *
 * @param result DB-Result
 * @return int Anzahl der Tupel
 */
function db_num_rows($result)
{
	global $dbtimethishit;
	$dbtimethishit -= getmicrotime();
	$fname = DBTYPE.'_num_rows';
	$r = $fname($result);
	$dbtimethishit += getmicrotime();
	return $r;
}

/**
 * Gibt Anzahl d. von letztem Query betroffenen Tupel zurück
 *
 * @param ressource DB-Ressource
 * @return int Anzahl der Tupel
 */
function db_affected_rows($link=false)
{
	global $dbtimethishit;
	$dbtimethishit -= getmicrotime();
	$fname = DBTYPE.'_affected_rows';
	if ($link===false)
	{
		$r = $fname();
	}
	else
	{
		$r = $fname($link);
	}
	$dbtimethishit += getmicrotime();
	return $r;
}

/**
 * Stellt permanente Verbindung zur DB her
 *
 * @param string $host DB-Host
 * @param string $user Username
 * @param string $pass Passwort
 * @return unknown
 */
function db_pconnect($host,$user,$pass)
{
	global $dbtimethishit;
	$dbtimethishit -= getmicrotime();
	$fname = DBTYPE.'_connect';
	$r = $fname($host,$user,$pass);
	$dbtimethishit += getmicrotime();
	return $r;
}

/**
 * Wählt Datenbank aus
 *
 * @param string Datenbankname
 * @return unknown
 */
function db_select_db($dbname)
{
	global $dbtimethishit;
	$dbtimethishit -= getmicrotime();
	$fname = DBTYPE.'_select_db';
	$r = $fname($dbname);
	$dbtimethishit += getmicrotime();
	return $r;
}

/**
 * Gibt von DB-Result belegten Speicher wieder frei
 *
 * @param result DB-Result
 * @return unknown
 */
function db_free_result($result)
{
	global $dbtimethishit;
	$dbtimethishit -= getmicrotime();
	$fname = DBTYPE.'_free_result';
	$r = $fname($result);
	$dbtimethishit += getmicrotime();
	return $r;
}

/**
* @author talion
* @desc Erstellt Listenarray aus SQL-Result, wahlweise mit numerischen oder assoziativen Schlüsseln
* @param result SQL-Result
* @param string Name des Feldes, das als Schlüssel verwendet werden soll (Optional, wenn nicht gesetzt: numerischer Schlüssel)
* @param bool Falls TRUE und nur eine Spalte vorhanden, wird diese ohne Schlüssel direkt eingefügt (Optional, Standard FALSE)
* @return array Array-Liste
*/
function db_create_list($result, $str_key = false, $bool_nokey = false)
{
	global $dbtimethishit;

	$dbtimethishit -= getmicrotime();

	$arr_list = array();
	$mixed_first = false;

	while($row = db_fetch_assoc($result)) {

		if($bool_nokey && sizeof($row) == 1) {
			$mixed_first = reset($row);
		}

		// Mit Schlüssel
		if( false !== $str_key && isset($row[$str_key]) ) {
			if(!empty($mixed_first)) {
				$arr_list[ $row[$str_key] ] = $mixed_first;
			}
			else {
				$arr_list[ $row[$str_key] ] = $row;
			}
		}
		else {
			if(!empty($mixed_first)) {
				$arr_list[] = $mixed_first;
			}
			else {
				$arr_list[] = $row;
			}
		}

	}


	$dbtimethishit += getmicrotime();

	return($arr_list);
}

/**
 * Fügt Eintrag in Datenbank-Tabelle hinzu (INSERT)
 *
 * @param string Tabellenname
 * @param array Datenarray, assoziativ: Spaltenname => Wert
 * 				Um für eine Spalte direkt SQL-Code zu übergeben, Array ('sql'=>true, 'value'=>SQL-Code) als Spalteninhalt setzen
 * @return bool TRUE, wenn erfolgreich, sonst FALSE
 * @author talion
 */
function db_insert ($str_table, $arr_data) {

	$str_sql = 'INSERT INTO '.$str_table.' ';

	$str_fields = '             (';
	$str_values = ' VALUES      ( ';
	$str_data = '';

	foreach ($arr_data as $str_field => $data) {

		if(is_array($data)) {
			// SQL-Code
			if(isset($data['sql']) && $data['sql'] === true) {
				$str_data = $data['value'];
			}
			else {
				$str_data = '"'.addslashes(serialize($data)).'"';
			}
		}
		else if(is_string($data)) {
			$str_data = '"'.addslashes(stripslashes($data)).'"';
		}
		else {
			$str_data = '"'.$data.'"';
		}
		$str_values .= $str_data.',';

		$str_fields .= $str_field.',';

	}

	// Komma weg
	$str_values = substr($str_values,0,strlen($str_values)-1).')';
	$str_fields = substr($str_fields,0,strlen($str_fields)-1).')';

	$str_sql .= $str_fields."\n".$str_values;

	db_query($str_sql);

	if(!db_errno(LINK)) {
		return(true);
	}
	else {
		return(false);
	}

}

/**
 * @desc Nimmt einen String und wandelt ihn in einen MYSQL Suchstring um
 * Zwischen alle Zeichen wird ein Trennzeichen eingefügt
 *
 * @param string $str_input_string enthält den Eingabestring
 * @param string $str_split_char enthält das Zeichen dass zum Trennen verwendet werden soll
 * @param string $str_remove_chars enthält den regulären Ausdruck der Zeichen die aus dem String entfernt werden sollen
 * @return string
 */
function str_create_search_string($str_input_string = '',$str_split_char='%', $str_remove_chars = '#[\s\W\d]#')
{
	//Return an empty string
	if($str_input_string == '')
	{
		return $str_input_string;
	}

	//Remove slashes
	$str_input_string = stripslashes($str_input_string);
	$str_input_string = preg_replace($str_remove_chars,'',$str_input_string);

	//Split the string
    if(function_exists('str_split')) 
	{
        $arr_temp = str_split($str_input_string, 1);
    }
    else 
	{
        $arr_temp = preg_split('#(?<=.)(?=.)#s', $str_input_string);
    }

	//Add split chars
	$str_return = $str_split_char.addslashes(implode($str_split_char,$arr_temp)).$str_split_char;

	//Return the string
	return $str_return;

	/*$search = '%';
	for ($i=0;$i<strlen($str_input_string);$i++)
	{
		$search.=substr($str_input_string,$i,1).'%';
	}
	return addslashes($search);*/
}

/**
 * @desc Gib einen vorformattierten SQL Fehler aus
 *
 * @param string $sql Der SQL fehler
 * @return array
 */
function sql_error($sql)
{
	global $session;
	return output_array($session).'SQL = <pre>$sql</pre>'.db_error(LINK);
}
?>
