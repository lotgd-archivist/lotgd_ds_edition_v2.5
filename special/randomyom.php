<?php
/**
Kleines sinnloses Waldevent:
-Eine blinde Taube liefert eine Nachricht an irgendeinen zuf�lligen Spieler aus,
 der gerade eingeloggt ist
-Man bekommt auch noch ein paar Erfahrungspunkte
by Maris (Maraxxus@gmx.de)
**/

$session['user']['specialinc']='randomyom.php';
if ($_GET['op']=='')
{
	output('`n`5Als du durch das Dickicht schleichst, entdeckst du pl�tzlich eine freie Fl�che, auf der viele K�rner verstreut sind.`nDir fallen auch einige `^Brieftauben`5 auf, die emsig damit besch�ftigt sind, diese K�rner aufzupicken.`nEbenso erblickst du eine scheinbar blinde Taube am Rand, die schon recht ausgem�rgelt ist und immer wieder versucht eines der K�rner zu erhaschen.`nDoch jedesmal kommt ihr eine andere Taube zuvor und schnappt ihr das Futter vor dem Schnabel weg.`n`nIrgendwie empfindest du Mitleid f�r das arme Tier.`nWas willst du tun?`n');

	addnav('Die blinde Taube f�ttern','forest.php?op=feed');
	addnav('Alle Tauben verjagen','forest.php?op=scare');
}
else
{
	if ($_GET['op']=='feed')
	{
		$link = 'forest.php?op=write';
		addnav('',$link);
		output('`5Du scheuchst ein paar der Tauben mit der Hand auf Seite und sammelst einige K�rner auf, um sie der blinden Taube zu geben.`nDankbar und ausgehungert st�rzt sie sich auf das Futter.`nSie ist dir daf�r so dankbar, dass sie eine Nachricht f�r dich �bermitteln wird:`n`n');
		output("<form action='".$link."' method='POST'>
				Dein Brief: <input type='text' name='message' size='100' maxlength='500'>`n`n
				<input type='submit' class='button' value='Abschicken!'></form>",true);
	}
	elseif ($_GET['op']=='write')
	{
		$message = $_POST['message'];
		if (strlen($message) < 5)
		{
			$link = 'forest.php?op=write';
			addnav('',$link);
			output('`&Du kannst keine Nachricht mit weniger als 5 Zeichen verschicken!`n`n');
			output("<form action='".$link."' method='POST'>
					Dein Brief: <input type='text' name='message' size='100' maxlength='500'>`n`n
					<input type='submit' class='button' value='Abschicken!'></form>",true);
		}
		else
		{
			$sql = 'SELECT acctid FROM accounts WHERE '.user_get_online().' ORDER BY RAND() LIMIT 1';
			$result = db_query($sql) or die(db_error(LINK));
			$amount = db_num_rows($result);

			if ($amount>0)
			{
				$row=db_fetch_assoc($result);
				systemmail($row['acctid'],'`^Blinde Brieftaube!`0','`&Eine blinde Brieftaube krallt sich nach einer wackeligen Landung auf deine Schulter.`nSie hat folgende Nachricht bei sich, die '.$session['user']['name'].' `& geschrieben haben muss:`n`n`5'.$message);
			}
			$gain=round($session['user']['experience']*0.01);
			output('`5Als du die Nachricht geschrieben hast, erhebt sich die blinde Taube in die L�fte und fliegt davon.`n
        		Du fragst dich wer deinen Brief erhalten wird, und ob dieser Vogel es �berhaupt fertig bringt irgendwem irgendetwas auszuliefern...
        		'.($gain > 0 ? '`nDennoch hat dich diese Tat ein wenig kl�ger gemacht.`n`^Du erh�ltst '.$gain.' Punkte Erfahrung!`n' : '')
        		);
        		$session['user']['experience']+=$gain;
        		$session['user']['specialinc']='';
		}
	}
	elseif ($_GET['op']=='scare')
	{
		output('`5So wie du es schon in deiner Kindheit geliebt hast, rennst du mit lautem Geschrei und wild rudernden Armen �ber den Platz.`nDie Brieftauben flattern aufgeschreckt hoch und fliegen in alle Richtungen weg.`nDu f�hlst dich... irgendwie beobachtet...');
		addnews($session['user']['name'].'`# wurde dabei gesehen wie '.($session['user']['sex']?'sie':'er').' Brieftauben verjagt hat. Man munkelt nun, '.($session['user']['sex']?'sie':'er').' sei daf�r verantwortlich, dass manche Nachrichten nicht ankommen!');
		$session['user']['specialinc']='';
	}
}
?>
