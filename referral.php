<?php
/**
 * referral.php: Anwerbungen
 * @author LOGD-Core
 * @version DS-E V/2
*/

require_once('common.php');

if ($session['user']['loggedin'])
{
	page_header('Empfehlungen');
	addnav('Zurück');
	addnav('D?zum Dorf','village.php');
	addnav('J?zur Jägerhütte','lodge.php');
	output('Du bekommst automatisch '.getsetting('refererdp',50).' Punkte für jeden geworbenen Charakter, der folgenden Status erreicht:`n`n
	       Level `b'.getsetting('refererminlvl',5).'`b`n
	       '.(getsetting('referermindk',0) > 0 ? 'Drachenkills `b'.getsetting('referermindk',0).'`b`n' : '').'`n
			`n(`4Achtung: Eigene Accounts anzuwerben ist verboten und wird mit Verbannung bestraft!`0)
			`n`n
			Woher weiss die Seite, dass du eine Person geworben hast?`n
		  Kleinigkeit! Wenn du Freunden von dieser Seite erzählst, gib ihnen einfach folgenden Link:`n`n`q
		  '.getsetting('serverurl','http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['REQUEST_URI'])).'referral.php?r='. rawurlencode($session['user']['login']).'`n`n
			`0Dadurch wird die Seite wissen, dass du derjenige warst, der ihn hergeschickt hat. Wenn er dann zum ersten Mal oben angeführten Status erreicht, bekommst du deine Punkte!');

	$sql = 'SELECT name,level,refererawarded,dragonkills FROM accounts
			LEFT JOIN account_extra_info USING(acctid)
			WHERE referer='.$session['user']['acctid'].' ORDER BY dragonkills,level';
  	$result = db_query($sql);
  	output('`n`nAccounts, die du geworben hast:`n
  			<table border="0" cellpadding="3" cellspacing="0">
  				<tr class="trhead">
  					<td>Name</td><td>Level</td><td>DKs</td><td>Ausgezahlt?</td>
  				</tr>',true);

  	$int_count = db_num_rows($result);

  	if ($int_count==0)
  	{
		output('<tr><td colspan="3" align="center">`iKeine!</td></tr>',true);
	}

	for ($i=0;$i<$int_count;$i++)
	{
		$row = db_fetch_assoc($result);
		output('<tr class="'.($i%2?'trlight':'trdark').'"><td>',true);
		output($row['name']);
		output('</td><td>'.$row['level'].'</td><td>'.$row['dragonkills'].'</td><td>'.($row['refererawarded']?'`@Ja!`0':'`$Nein!`0').'</td></tr>',true);
	}

	output('</table>',true);
	page_footer();
}
else
{
	page_header('Willkommen in '.getsetting('townname','Atrahor'));

	output('`@
	Legend of the Green Dragon ist ein Remake des klassischen BBS Spiels `$Legend of the Red Dragon`@. Es ist ein Multiplayer Browserspiel, das heisst,
	es muss keinerlei Programm heruntergeladen oder installiert werden.`n
	Komm rein und nehme an einem Abenteuer teil, das eines der ersten Multiplayer Rollenspiele der Welt darstellte!`n`n
	Hier schlüpfst du in die Rolle eines Kriegers in einer Fantasy-Welt, in der eine Legende von einem riesigen grünen Drachen die
	Bewohner in Angst und Schrecken versetzt. Nunja, zumindest die meisten. Oder wenigstens ein paar.`n`n
	');
	output("`2<li>Kämpfe gegen unzählige böse Kreaturen, die das Dorf bedrohen
	<li>Setze unterschiedliche Waffen ein und kaufe dir bessere Rüstungen
	<li>Erforsche das Dorf und den Wald und unterhalte dich mit anderen Kriegern
	<li>Besiege andere Spieler im Zweikampf - oder heirate sie
	<li>Finde und vernichte den grünen Drachen, um im Ansehen zu steigen
	<li>Und vieles mehr`n`n",true);
	if (!empty($_GET['r']))
	{
		$str_login = stripslashes(rawurldecode($_GET['r']));
		$sql = 'SELECT login,acctid FROM accounts WHERE login="'.addslashes($str_login).'"';
		$arr_user = db_fetch_assoc(db_query($sql));
		if(!empty($arr_user))
		{
			output('`@Du wurdest von `b'.$arr_user['login'].'`b hierher eingeladen, damit ihr gemeinsam gegen das Böse kämpfen könnt.');
		}
	}
	output('`@ Melde dich jetzt kostenlos an und werde Teil dieser Welt.');
	addnav('Navigation');
	addnav('Charakter erstellen','create_rules.php'.(!empty($arr_user) ? '?r='.$arr_user['acctid'] : ''));
	addnav('F.A.Q.','petition.php?op=faq',false,true);
	addnav('Zum Login','index.php');
	page_footer();
}
?>