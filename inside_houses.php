<?php

// RPG im Haus

require_once("common.php");
require_once(LIB_PATH.'house.lib.php');

addcommentary();

// base values for pricing and chest size:

$goldmax=15000;
$gemmax=50;

$goldcost=30000;
$gemcost=50;

page_header('Im Inneren eines Hauses');

addcommentary();
checkday();
is_new_day();

if ($_GET[id])
{
    $session[housekey]=(int)$_GET[id];
}

if (!$session[housekey])
{
    redirect("houses.php");
}

$sql = "SELECT * FROM houses WHERE houseid=".$session[housekey]." ORDER BY houseid DESC";

$result = db_query($sql) or die(db_error(LINK));

$row = db_fetch_assoc($result);

if ($_GET[act]=="takekey")
{

    if (!$_POST[ziel])
    {

        $sql = "SELECT owner FROM keylist WHERE value1=$row[houseid] ORDER BY id ASC";

        $result = db_query($sql) or die(db_error(LINK));

        output("<form action='inside_houses.php?act=takekey' method='POST'>",true);

        output("`2Wem willst du den Schl�ssel wegnehmen? <select name='ziel'>",true);

        for ($i=0; $i<db_num_rows($result); $i++)
        {

            $item = db_fetch_assoc($result);

            $sql = "SELECT acctid,name,login FROM accounts WHERE acctid=$item[owner] ORDER BY login DESC";

            $result2 = db_query($sql) or die(db_error(LINK));

            $row2 = db_fetch_assoc($result2);

            if ($amt!=$row2[acctid] && $row2[acctid]!=$row[owner])
            {
                output("<option value=\"".rawurlencode($row2['name'])."\">".preg_replace("'[`].'","",$row2['name'])."</option>",true);
            }

            $amt=$row2[acctid];

        }

        output("</select>`n`n",true);

        output("<input type='submit' class='button' value='Schl�ssel abnehmen'></form>",true);

        addnav("","inside_houses.php?act=takekey");

    }
    else
    {

        $sql = "SELECT acctid,name,location, restatlocation, login,gold,gems FROM accounts WHERE name='".addslashes(rawurldecode(stripslashes($_POST['ziel'])))."' AND locked=0";
        $result2 = db_query($sql);
        $row2  = db_fetch_assoc($result2);

        output("`2Du verlangst den Schl�ssel von `&$row2[name]`2 zur�ck.`n");
        
        $ausbau = 0;
        if (($row[status]==5) or($row[status]==11) or($row[status]==21) or($row[status]==31) or($row[status]==41) or($row[status]==51) or($row[status]==61) or($row[status]==71) or($row[status]==81) or($row[status]==91) or($row[status]==101))
        {
            $ausbau=1;
        }
        
        // Anteil aus Schatz auszahlen?
        $int_goldgive = 0;
        $int_gemsgive = 0;
        $str_mail_plus = '';
        $str_comment_plus = '';
        if(getsetting('housetrsshare',1)) {
	        if(!$ausbau) {
	        	$sql = "SELECT COUNT(*) AS c FROM keylist WHERE value1=$row[houseid] AND owner<>$row[owner]";
		    	$count = db_fetch_assoc(db_query($sql));
		    	
		        $int_goldgive=round($row['gold']/($count['c']+1));
		        $int_gemsgive=round($row['gems']/($count['c']+1));
		        $str_mail_plus = "Du bekommst `^$int_goldgive`2 Gold auf die Bank und `#$int_gemsgive`2 Edelsteine aus dem gemeinsamen Schatz ausbezahlt!";
		        $str_comment_plus = addslashes($row2['name']).'`^ bekommt einen Teil aus dem Schatz.';
		        output("$row2[name]`2 bekommt `^$int_goldgive`2 Gold und `#$int_gemsgive`2 Edelsteine aus dem gemeinsamen Schatz.");
		    }
		    else {
		    	$str_mail_plus = "Weil sich das Haus im Ausbau befindet, bekommst du jedoch keinen Teil aus dem Schatz!";
		    }
        }
        	    
        $row2['name'] = addslashes($row2['name']);   
       
        systemmail($row2[acctid],"`@Schl�ssel zur�ckverlangt!`0","`&{$session['user']['name']}
        `2 hat den Schl�ssel zu Haus Nummer `b$row[houseid]`b ($row[housename]`2) zur�ckverlangt. ".
        	$str_mail_plus
        	);
        
        // �berpr�fen ob die Person auch in dem Haus liegt, von dem der Schl�ssel war, sonst nat�rlich im Haus liegen lassen by Azura

        if ($row2['restatlocation']==$row['houseid'])
        {
            $sql = "UPDATE accounts SET goldinbank=goldinbank+$int_goldgive,gems=gems+$int_gemsgive,location=0,restatlocation=0 WHERE acctid=$row2[acctid]";
            db_query($sql);
        }
        else
        {
            $sql = "UPDATE accounts SET goldinbank=goldinbank+$int_goldgive,gems=gems+$int_gemsgive WHERE acctid=$row2[acctid]";
            db_query($sql);
        }
        
        if($int_goldgive > 0 || $int_gemsgive > 0) {
	        $sql = "UPDATE houses SET gold=gold-$int_goldgive,gems=gems-$int_gemsgive WHERE houseid=$row[houseid]";
	        db_query($sql);
        }
        
        $sql = "INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'house-".$row[houseid]."',".$session[user][acctid].",'/me `^nimmt $row2[name]`^ einen Schl�ssel ab. $str_comment_plus')";
        db_query($sql) or die(db_error(LINK));

        $sql = "UPDATE keylist SET owner=$row[owner],hvalue=0,value2=0,gold=0,gems=0,chestlock=0 WHERE owner=$row2[acctid] AND value1=$row[houseid]";
        db_query($sql);

        // An Hausbesitzer zur�ckgeben

        // Einladungen in Privatgem�cher l�schen
        item_delete(' tpl_id="prive" AND value1='.$row['houseid'].' AND value2='.$row2['acctid']);

        // Privatgem�cher zur�cksetzen
        item_set(' tpl_id="privb" AND value1='.$row['houseid'].' AND owner='.$row2['acctid'], array('owner'=>0,'description'=>'') );

        // M�bel f�r Privatgem�cher zur�cksetzen
        item_set(' owner< 1234567 AND deposit1='.$row['houseid'].' AND deposit2='.$row2['acctid'], array('deposit1'=>0,'deposit2'=>0) );
    }

    addnav("H?Zur�ck zum Haus","inside_houses.php");

}
else if ($_GET[act]=="givekey")
{

    if (!$_POST['ziel'])
    {

        output("`2Einen Schl�ssel f�r dieses Haus hat:`n`n");

        $sql = "SELECT *,accounts.name AS besitzer FROM keylist LEFT JOIN accounts ON accounts.acctid=keylist.owner WHERE value1=$row[houseid] AND owner<>".$session[user][acctid]." ORDER BY id ASC";

        $result = db_query($sql) or die(db_error(LINK));

        for ($i=0; $i<db_num_rows($result); $i++)
        {

            $item = db_fetch_assoc($result);

            output("`c`& $item[besitzer]`0`c");

        }

        $sql = "SELECT id FROM keylist WHERE value1=$row[houseid] AND owner=$row[owner] ORDER BY id ASC";

        $result = db_query($sql) or die(db_error(LINK));

        if (db_num_rows($result)>0)
        {

			$session['keygiven_check'] = md5(time());

            output("`n`2Du kannst noch `b".db_num_rows($result)."`b Schl�ssel vergeben.");

            output("<form action='inside_houses.php?act=givekey' method='POST'>",true);

            output("An wen willst du einen Schl�ssel �bergeben? <input name='ziel'>`n", true);

            output("<input type='submit' class='button' value='�bergeben'></form>",true);

            output("`n`nWenn du einen Schl�ssel vergibst, wird der Schatz des Hauses gemeinsam genutzt. Du kannst einem Mitbewohner jederzeit den Schl�ssel wieder wegnehmen. ");
        	if(getsetting('housetrsshare',1)) {
            	output("Er wird dann einen gerechten Anteil aus dem gemeinsamen Schatz bekommen.");
		}
            addnav("","inside_houses.php?act=givekey");

        }
        else
        {

            output("`n`2Du hast keine Schl�ssel mehr �brig. Vielleicht kannst du in der J�gerh�tte noch einen nachmachen lassen?");

        }

    }
    else
    {

        if ($_GET['subfinal']==1)
        {

            $sql = "SELECT acctid,name,login,lastip,emailaddress,dragonkills,level,sex FROM accounts WHERE acctid='".$_POST['ziel']."' AND locked=0";

        }
        else
        {

            $ziel = rawurldecode($_POST['ziel']);

            $name = str_create_search_string($ziel);

            $sql = "SELECT acctid,name,login,lastip,emailaddress,dragonkills,level,sex FROM accounts WHERE name LIKE '".$name."' AND locked=0";

        }

        $result2 = db_query($sql);

		if (db_num_rows($result2) == 0)
        {

            output("`2Es gibt niemanden mit einem solchen Namen. Versuchs nochmal.");

        }
        else if (db_num_rows($result2) > 100)
        {

            output("`2Es gibt �ber 100 Krieger mit einem �hnlichen Namen. Bitte sei etwas genauer.");

        }
        else if (db_num_rows($result2) >= 1 && !$_GET['subfinal'])
        {

            output("<form action='inside_houses.php?act=givekey&subfinal=1' method='POST'>",true);

            output("`2Wen genau meinst du? <select name='ziel'>",true);

            for ($i=0; $i<db_num_rows($result2); $i++)
            {

                $row2 = db_fetch_assoc($result2);

                output("<option value=\"".$row2['acctid']."\">".preg_replace("'[`].'","",$row2['name'])."</option>",true);

            }

            output("</select>`n`n",true);

            output("<input type='submit' class='button' value='Schl�ssel �bergeben'></form>",true);

            addnav("","inside_houses.php?act=givekey&subfinal=1");

            //addnav("","inside_houses.php?act=givekey");
            // why the hell was this in there?

        }
        if ($_GET['subfinal'])
        {

            $row2  = db_fetch_assoc($result2);

            $sql = "SELECT owner FROM keylist WHERE owner=$row2[acctid] AND value1=$row[houseid] ORDER BY id ASC LIMIT 1";

            $result = db_query($sql) or die(db_error(LINK));

            if ($row2[login] == $session[user][login])
            {

                output("`2Du kannst dir nicht selbst einen Schl�ssel geben.");

            }
            else if (db_num_rows($result))
            {

                output("`2$row2[name]`2 hat bereits einen Schl�ssel!");

            }
            else if ($session['user']['lastip'] == $row2['lastip'] || ($session['user']['emailaddress'] == $row2['emailaddress'] && $row2[emailaddress]))
            {

                output("`2Deine Charaktere d�rfen leider nicht miteinander interagieren!");

            }
            else if ($row2['level']<5 && $row2['dragonkills']<1)
            {

                output("`2$row2[name]`2 ist noch nicht lange genug um Dorf, als dass du ".($row2['sex']?"ihr":"ihm")." vertrauen k�nntest. Also beschlie�t du, noch eine Weile zu beobachten.");

            }
            else
            {
            	// Kleine Abfrage, um sicherzustellen dass durch Aktualisieren nicht mehrfach ein Schl�ssel vergeben wird
				if($session['keygiven_check'] != '') {

					unset($session['keygiven_check']);

					$sql = "SELECT id FROM keylist WHERE value1=$row[houseid] AND owner=$row[owner] ORDER BY id ASC LIMIT 1";

					$result = db_query($sql) or die(db_error(LINK));

					$knr = db_fetch_assoc($result);

					$knr=$knr['id'];

					systemmail($row2[acctid],"`@Schl�ssel erhalten!`0","`&{$session['user']['name']}
					`2 hat dir einen Schl�ssel zu Haus Nummer `b$row[houseid]`b($row[housename]`2) gegeben!");

					$row2['name'] = addslashes($row2['name']);

					$sql = "UPDATE keylist SET owner=$row2[acctid],hvalue=0,value2=0,gold=0,gems=0,chestlock=0 WHERE owner=$row[owner] AND value1=$row[houseid] AND id=$knr";

					db_query($sql);

					$sql = "INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'house-".$row[houseid]."',".$session[user][acctid].",'/me `^gibt $row2[name]`^ einen Schl�ssel.')";

					db_query($sql) or die(db_error(LINK));
				}

				output("`2Du �bergibst `&$row2[name]`2 einen Schl�ssel f�r dein Haus. Du kannst den Schl�ssel zum Haus jederzeit wieder wegnehmen");
				if(getsetting('housetrsshare',1)) {
                	output(", aber $row2[name]`2 wird dann einen gerechten Anteil aus dem gemeinsamen Schatz des Hauses bekommen.`n");
				}
				else {
					output('.');
				}

            }

        }

    }

    addnav("H?Zur�ck zum Haus","inside_houses.php");

}
else if ($_GET[act]=="takegold")
{

    if (($row[status]==5) || ($row[status]==11) || ($row[status]==21) || ($row[status]==31) || ($row[status]==41) || ($row[status]==51) || ($row[status]==61) || ($row[status]==71) || ($row[status]==81) || ($row[status]==91) || ($row[status]==101))
    {

        output("Hier wird gearbeitet! Du wirst dich doch wohl nicht an der Baukasse vergreifen...`n");

        addnav("H?Zur�ck zum Haus","inside_houses.php");

    }
    else
    {

		$rowe = user_get_aei('goldin');

        $maxtfer = $session[user][level]*getsetting("transferperlevel",25)*4;

		$maxtfer = max($maxtfer-$rowe['goldin'],0);

        if ($row['owner'] != $session['user']['acctid']) {
		    $sql = "SELECT gold,chestlock FROM keylist WHERE value1=$row[houseid] AND owner=".$session[user][acctid]."";
		    $res = db_query($sql) or die(db_error(LINK));
		    $row2 = db_fetch_assoc($res);
		}
		else {
			$row2['chestlock'] = 0;
		}

        if ($row2['chestlock']!=1)
        {

            if (!isset($_POST['gold']))
            {


                output("`2Es befindet sich `^$row[gold]`2 Gold in der Schatztruhe des Hauses.`nDu darfst heute noch `^$maxtfer`2 Gold mitnehmen.`n(Leerlassen, um Maximum zu entnehmen.)");

                output("`2<form action=\"inside_houses.php?act=takegold\" method='POST'>",true);

                output("`nWieviel Gold mitnehmen? <input type='gold' id='gold' name='gold'>`n`n",true);

                output("<input type='submit' class='button' value='Mitnehmen'>",true);
                output("<script language='javascript'>document.getElementById('gold').focus();</script>",true);
                addnav("","inside_houses.php?act=takegold");

            }
            else
            {

                $amt=abs((int)$_POST[gold]);

				if($amt == 0) {	// Maximum bei leerem Feld

					$amt = min($maxtfer,$row['gold']);

				}

                if ($amt>$row[gold])
                {

                    output("`2So viel Gold ist nicht mehr da.");

                }
                else if ($maxtfer<$amt)
                {

                    output("`2Du darfst maximal `^$maxtfer`2 Gold auf einmal nehmen.");

                }
                else if ($amt<0)
                {

                    output("`2Wenn du etwas in den Schatz legen willst, versuche nicht, etwas negatives herauszunehmen.");

                }
                else if($amt > 0)
                {

                    $row[gold]-=$amt;

                    $session[user][gold]+=$amt;

                    user_set_aei(array('goldin'=>$rowe['goldin']+$amt));

                    $sql = "UPDATE houses SET gold=$row[gold] WHERE houseid=$row[houseid]";

                    db_query($sql) or die(db_error(LINK));

                    output("`2Du hast `^$amt`2 Gold genommen. Insgesamt befindet sich jetzt noch `^$row[gold]`2 Gold im Haus.");

                    $goldspent=$row2[gold];
                    $goldspent-=$amt;

                    if ($row['owner'] != $session['user']['acctid'])
                    {
                        $sql = "UPDATE keylist SET gold=$goldspent WHERE value1=$row[houseid] AND owner=".$session[user][acctid]."";
                        db_query($sql) or die(db_error(LINK));
                    }

                    $sql = 'INSERT INTO commentary
									(postdate,section,author,comment)
								VALUES
									(now(),"house-'.$row['houseid'].'",'.$session['user']['acctid'].',"/me `\$nimmt `^'.$amt.'`\$ Gold.")';

                    db_query($sql) or die(db_error(LINK));

                }

            }
        }
        else
        {
            output("`&Der Hausherr hat ein schweres, doppeltes Sicherheitsschloss an der Truhe angebracht, dass diese vor unerw�nschtem Zugriff sch�tzt.`nDa du keinen Schl�ssel f�r dieses Schloss hast, sieht es wohl so aus als ob der Hausherr nicht will, dass du dich weiterhin an seinen Reicht�mern vergreifst.`nDas tut mir aber leid...");
        }

        addnav("H?Zur�ck zum Haus","inside_houses.php");
    }

}
else if ($_GET[act]=="givegold")
{

	$rowe = user_get_aei('goldout');

    $maxout = $session[user][level]*getsetting("maxtransferout",25);

	$transleft = max($maxout - $rowe['goldout'],0);

	if ($row['owner'] != $session['user']['acctid']) {
	    $sql = "SELECT gold,chestlock FROM keylist WHERE value1=$row[houseid] AND owner=".$session[user][acctid]."";
	    $res = db_query($sql) or die(db_error(LINK));
	    $row2 = db_fetch_assoc($res);
	}
	else {
		$row2['chestlock'] = 0;
	}

    if ($row2[chestlock]!=1)
    {
        if (!isset($_POST['gold']))
        {

            output("`2Es befindet sich `^$row[gold]`2 Gold in der Schatztruhe des Hauses.`n
					Du darfst heute noch `^$transleft`2 Gold deponieren.`n(Feld leerlassen, um Maximum einzuzahlen)");

            output("`2<form action=\"inside_houses.php?act=givegold\" method='POST'>",true);

            output("`nWieviel Gold deponieren? <input type='gold' id='gold' name='gold'>`n`n",true);

            output("<input type='submit' class='button' value='Deponieren'>",true);
            output("<script language='javascript'>document.getElementById('gold').focus();</script>",true);
            addnav("","inside_houses.php?act=givegold");

        }
        else
        {

            // Anwesen, Gasthaus
            if (($row['status']==10) || ($row['status']==12) || ($row['status']==13) ||
            ($row['status']==17) || ($row['status']==18) || ($row['status']==19))
            {
                $goldmax=75000;
            }
            else // Villa
            if (($row['status']==14) || ($row['status']==15) || ($row['status']==16))
            {
                $goldmax=150000;
            }
            else // Versteck, Refugium, Keller
            if (($row['status']==30) || (($row['status']>=32) && ($row['status']<=39)))
            {
                $goldmax=3000;
            }
            else // Ausbau Stufe 1
            if ($row[status]==5)
            {
                $goldmax=300000;
            }
            else // Ausbau Stufe 2
            if (($row[status]==11) || ($row[status]==21) || ($row[status]==31) || ($row[status]==41) || ($row[status]==51) || ($row[status]==61) || ($row[status]==71) || ($row[status]==81) || ($row[status]==91) || ($row[status]==101))
            {
                $goldmax=500000;
            }
            // Alles Andere
            else
            {
                $goldmax=round($goldcost/2);
            }

			$amt=abs((int)$_POST[gold]);

			if($amt == 0) {	// Maximum

				$amt = min($transleft, round($goldmax)-$row['gold']);
				$amt = min($amt,$session['user']['gold']);

			}

            if ($amt>$session[user][gold])
            {
                output("`2So viel Gold hast du nicht dabei.");
            }
            else if ($row[gold]>round($goldmax))
            {
                output("`2Der Schatz ist voll.");
            }
            else if ($amt>(round($goldmax)-$row[gold]))
            {

                output("`2Du gibst alles, aber du bekommst beim besten Willen nicht so viel in den Schatz.");

            }
            else if ($amt<0)
            {

                output("`2Wenn du etwas aus dem Schatz nehmen willst, versuche nicht, etwas negatives hineinzutun.");

            }
            else if ($rowe['goldout']+$amt > $maxout)
            {

                output("`2Du darfst nicht mehr als `^$maxout`2 Gold pro Tag deponieren.");

            }
            else if ($amt > 0)
            {

                $row[gold]+=$amt;

                $session[user][gold]-=$amt;

                user_set_aei(array('goldout'=>$rowe['goldout']+$amt));

                output("`2Du hast `^$amt`2 Gold deponiert. Insgesamt befinden sich jetzt `^$row[gold]`2 Gold im Haus.");

                $goldspent=$row2[gold];
                $goldspent+=$amt;

                $sql = "UPDATE houses SET gold=$row[gold] WHERE houseid=$row[houseid]";
                db_query($sql) or die(db_error(LINK));

                if ($row['owner'] != $session['user']['acctid'])
                {
                    $sql = "UPDATE keylist SET gold=$goldspent WHERE value1=$row[houseid] AND owner=".$session[user][acctid]."";
                    db_query($sql) or die(db_error(LINK));
                }

                $sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'house-".$row[houseid]."',".$session[user][acctid].",'/me `@deponiert `^$amt`@ Gold.')";

                db_query($sql) or die(db_error(LINK));

            }
			else {
				output('`2Irgendwie mag der Schatz nicht so ganz, wie du willst..');
			}

        }
    }
    else
    {
        output("`&Der Hausherr hat ein schweres, doppeltes Sicherheitsschloss an der Truhe angebracht, dass diese vor unerw�nschtem Zugriff sch�tzt.`nDa du keinen Schl�ssel f�r dieses Schloss hast, sieht es wohl so aus als ob der Hausherr nicht will, dass du dich weiterhin an seinen Reicht�mern vergreifst.`nDas tut mir aber leid...");
    }
    addnav("H?Zur�ck zum Haus","inside_houses.php");



}
else if ($_GET[act]=="takegems")
{

    if (($row[status]==5) || ($row[status]==11) || ($row[status]==21) || ($row[status]==31) || ($row[status]==41) || ($row[status]==51) || ($row[status]==61) || ($row[status]==71) || ($row[status]==81) || ($row[status]==91) || ($row[status]==101))
    {

        output("Hier wird gearbeitet! Du wirst dich doch wohl nicht an der Baukasse vergreifen...`n");

        addnav("H?Zur�ck zum Haus","inside_houses.php");

    }
    else
    {

		$rowe = user_get_aei('gemsin');

		$maxtfer = max(getsetting('housemaxgemsout',10) - $rowe['gemsin'],0);

		if($row['owner'] != $session['user']['acctid']) {
	        $sql = "SELECT gems,chestlock FROM keylist WHERE value1=$row[houseid] AND owner=".$session[user][acctid]."";
	        $res = db_query($sql) or die(db_error(LINK));
	        $row2 = db_fetch_assoc($res);
		}
		else {
			$row2['chestlock'] = 0;
		}

        if ($row2['chestlock']!=1)
        {

            if (!$_POST[gems])
            {

                output("`2Es befinden sich `#$row[gems]`2 Edelsteine in der Schatztruhe des Hauses.`nDu darfst heute noch `^$maxtfer`2 Edelsteine mitnehmen.`n");

                output("`2<form action=\"inside_houses.php?act=takegems\" method='POST'>",true);

                output("`nWieviele Edelsteine mitnehmen? <input type='gems' id='gems' name='gems'>`n`n",true);

                output("<input type='submit' class='button' value='Mitnehmen'>",true);
                output("<script language='javascript'>document.getElementById('gems').focus();</script>",true);
                addnav("","inside_houses.php?act=takegems");

            }
            else
            {

                $amt=abs((int)$_POST[gems]);

                if ($amt>$row[gems])
                {

                    output("`2So viele Edelsteine sind nicht mehr da.");

                }
                else if ($amt<0)
                {

                    output("`2Wenn du etwas in den Schatz legen willst, versuche nicht, etwas negatives herauszunehmen.");

                }
				else if ($maxtfer<$amt)
                {

                    output("`2Du darfst maximal `^$maxtfer`2 Edelsteine pro Tag nehmen.");

                }
                else if($amt > 0)
                {

                    $row[gems]-=$amt;

                    $session[user][gems]+=$amt;

					user_set_aei(array('gemsin'=>$rowe['gemsin']+$amt));

                    $sql = "UPDATE houses SET gems=$row[gems] WHERE houseid=$row[houseid]";

                    db_query($sql);

                    output("`2Du hast `#$amt`2 Edelsteine genommen. Insgesamt befinden sich jetzt noch `#$row[gems]`2 Edelsteine im Haus.");

                    $gemsspent=$row2[gems];
                    $gemsspent-=$amt;

                    // Nur aktualisieren, wenn nicht Hauseigent�mer
                    if ($row['owner'] != $session['user']['acctid'])
                    {
                        $sql = "UPDATE keylist SET gems=$gemsspent WHERE value1=$row[houseid] AND owner=".$session[user][acctid]."";
                        db_query($sql) or die(db_error(LINK));
                    }

                    $sql = "INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'house-".$row[houseid]."',".$session[user][acctid].",'/me `\$nimmt `#$amt`\$ Edelsteine.')";

                    db_query($sql) or die(db_error(LINK));

                }

            }
        }
        else
        {
            output("`&Der Hausherr hat ein schweres, doppeltes Sicherheitsschloss an der Truhe angebracht, dass diese vor unerw�nschtem Zugriff sch�tzt.`nDa du keinen Schl�ssel f�r dieses Schloss hast, sieht es wohl so aus als ob der Hausherr nicht will, dass du dich weiterhin an seinen Reicht�mern vergreifst.`nDas tut mir aber leid...");
        }
        addnav("H?Zur�ck zum Haus","inside_houses.php");
    }

}
else if ($_GET[act]=="givegems")
{


    if($row['owner'] != $session['user']['acctid']) {
        $sql = "SELECT gems,chestlock FROM keylist WHERE value1=$row[houseid] AND owner=".$session[user][acctid]."";
        $res = db_query($sql) or die(db_error(LINK));
        $row2 = db_fetch_assoc($res);
	}
	else {
		$row2['chestlock'] = 0;
	}

    if ($row2[chestlock]!=1)
    {

        if (!$_POST[gems])
        {

            output("`2<form action=\"inside_houses.php?act=givegems\" method='POST'>",true);

            output("`nWieviele Edelsteine deponieren? <input type='gems' id='gems' name='gems'>`n`n",true);

            output("<input type='submit' class='button' value='Deponieren'>",true);
            output("<script language='javascript'>document.getElementById('gems').focus();</script>",true);
            addnav("","inside_houses.php?act=givegems");

        }
        else
        {

            $amt=abs((int)$_POST[gems]);

            // Anwesen, Gasthaus
            if (($row['status']==10) || ($row['status']==12) || ($row['status']==13) ||
            ($row['status']==17) || ($row['status']==18) || ($row['status']==19))
            {
                $gemmax=150;
            }
            // Villa
            else if (($row['status']==14) || ($row['status']==15) || ($row['status']==16))
            {
                $gemmax=300;
            }
            // Versteck, Refugium, Keller
            else if (($row['status']==30) || (($row['status']>=32) && ($row['status']<=39)))
            {
                $gemmax=20;
            }
            // Ausbau Stufe 1
            else if ($row[status]==5)
            {
                $gemmax=200;
            }
            // Ausbau Stufe 2
            else if (($row[status]==11) || ($row[status]==21) || ($row[status]==31) || ($row[status]==41) || ($row[status]==51) || ($row[status]==61) || ($row[status]==71) || ($row[status]==81) || ($row[status]==91) || ($row[status]==101))
            {
                $gemmax=500;
            }
            // Alles Andere
            else
            {
                $gemmax=$gemcost;
            }

            if ($amt>$session[user][gems])
            {
                output("`2So viele Edelsteine hast du nicht.");
            }
            else if ($row[gems]>=round($gemmax))
            {
                output("`2Der Schatz ist voll.");
            }
            else if ($amt>(round($gemmax)-$row[gems]))
            {
                output("`2Du gibst alles, aber du bekommst beim besten Willen nicht so viel in den Schatz.");

            }
            else if ($amt<0)
            {

                output("`2Wenn du etwas aus dem Schatz nehmen willst, versuche nicht, etwas negatives hineinzutun.");

            }
            else if($amt > 0)
            {

                $row[gems]+=$amt;

                $session[user][gems]-=$amt;

                $sql = "UPDATE houses SET gems=$row[gems] WHERE houseid=$row[houseid]";

                db_query($sql);

                output("`2Du hast `#$amt`2 Edelsteine deponiert. Insgesamt befinden sich jetzt `#$row[gems]`2 Edelsteine im Haus.");

                $gemsspent=$row2[gems];
                $gemsspent+=$amt;

                // Nur aktualisieren, wenn nicht Hauseigent�mer
                if ($row['owner'] != $session['user']['acctid'])
                {
                    $sql = "UPDATE keylist SET gems=$gemsspent WHERE value1=$row[houseid] AND owner=".$session[user][acctid]."";
                    db_query($sql) or die(db_error(LINK));
                }

                $sql = "INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'house-".$row[houseid]."',".$session[user][acctid].",'/me `@deponiert `#$amt`@ Edelsteine.')";

                db_query($sql) or die(db_error(LINK));

                if ($amt>20)
                {
                    debuglog("Deponiert $amt Edelsteine in einem Haus.");
                }



            }

        }

    }
    else
    {
        output("`&Der Hausherr hat ein schweres, doppeltes Sicherheitsschloss an der Truhe angebracht, dass diese vor unerw�nschtem Zugriff sch�tzt.`nDa du keinen Schl�ssel f�r dieses Schloss hast, sieht es wohl so aus als ob der Hausherr nicht will, dass du dich weiterhin an seinen Reicht�mern vergreifst.`nDas tut mir aber leid...");
    }
    addnav("H?Zur�ck zum Haus","inside_houses.php");

}
else if ($_GET[act]=="upgrade")
{


    if ($session['user']['dragonkills'] < getsetting('houseextdks',10) )
    {

        output('`2Du w�rdest ja dein Haus gerne weiter ausbauen, doch entspricht dein derzeitiger Rang
wohl noch nicht den Anforderungen! Die Stadtverwaltung '.getsetting('townname','Atrahor').'s
r�t dir, erst noch einige Drachen zu t�ten - so ungef�hr '.(getsetting('houseextdks',10)-$session['user']['dragonkills']).' -
und dann noch einmal um einen Ausbau zu ersuchen!');

        addnav('H?Zur�ck zum Haus','inside_houses.php');

        page_footer();
        exit;

    }

    $sql = "SELECT * FROM houses WHERE owner=".$session[user][acctid]." ORDER BY houseid DESC";
    $result = db_query($sql) or die(db_error(LINK));
    $row = db_fetch_assoc($result);


    // Upgrade-Kosten
    if ($row['status']<10)
    {
        $upgold=300000;
        $upgems=200;
    }
    else
    {
        $upgold=500000;
        $upgems=500;
    }

    if (($row['status']==30) || ($row['status']==31))
    {
        $upgold=100000;
        $upgems=100;
    }
    if ($_GET[form]=="start")
    {





        output("`@Du kannst nun beginnen deinen Hausausbau zu finanzieren.`n`n");

        output("`0<form action=\"inside_houses.php?act=upgrade&form=build2\" method='POST'>",true);

        output("`nWieviel Gold anzahlen? <input type='gold' name='gold'>`n",true);

        output("`nWieviele Edelsteine? <input type='gems' name='gems'>`n",true);

        output("<input type='submit' class='button' value='Ausbauen'>",true);

        if ($row['status']==1)
        {
            $newstate=5;
        }
        else
        {
            $newstate=$row['status']+1;
        }

        $sql = "UPDATE houses SET status=$newstate WHERE houseid=$row[houseid]";

        db_query($sql);


        addnav("","inside_houses.php?act=upgrade&form=build2");

        addnav("H?Zur�ck zum Haus","inside_houses.php");

        addnav("W?Zur�ck zum Wohnviertel","houses.php?op=enter");

    }
    // END - gerade erst beginnen
    else if ($_GET[form]=="done")
    {

        $newstate=($_GET[choice]);
        $row['status']=$newstate;

        $sql = "UPDATE houses SET status=$newstate,gold=0,gems=0 WHERE houseid=$row[houseid]";
        db_query($sql) or die(db_error(LINK));

        addnews("`2".$session[user][name]."`3 hat die Arbeiten am Haus `2$row[housename]`3 fertiggestellt.");
        addhistory("`3Hat die Arbeiten am Haus `2$row[housename]`3 fertiggestellt.");

        output("`@Du hast dein Haus ausgebaut. Du wirst nun sicher viel mehr Freude an ihm haben!`n`n");

        // Message und Effekt

        switch ($row['status'])
        {

        case 10 :
            output("`&Dein neues Anwesen wird viel mehr an Reicht�mern aufnehmen k�nnen als dein altes Haus.`n`7Du erh�ltst `@5`7 weitere Schl�ssel!");

            // Schl�ssel z�hlen (wichtig f�r Numererierung)
            $sql = "SELECT * FROM keylist WHERE value1=".$session['user']['house']." ORDER BY id ASC";
            $result = db_query($sql) or die(db_error(LINK));
            $nummer=db_num_rows($result)+1;
            if (db_num_rows($result))
            {
                db_free_result($result);
            }

            // 5 St�ck auff�llen
            for ($i=$nummer; $i<$nummer+5; $i++)
            {
                $sql = "INSERT INTO keylist (owner,value1,value2,gold,gems,description) VALUES (".$session[user][acctid].",$row[houseid],$i,0,0,'Schl�ssel f�r Haus Nummer $row[houseid]')";
                db_query($sql);
            }
            break;

        case 14 :
            output("`&Deine neue Villa wird noch mehr Reicht�mer horten k�nnen als das Anwesen.`n`7Du erh�ltst `@3`7 weitere Schl�ssel!");

            // Schl�ssel z�hlen (wichtig f�r Numererierung)
            $sql = "SELECT * FROM keylist WHERE value1=".$session['user']['house']." ORDER BY id ASC";
            $result = db_query($sql) or die(db_error(LINK));
            $nummer=db_num_rows($result)+1;
            if (db_num_rows($result))
            {
                db_free_result($result);
            }

            // 3 St�ck auff�llen
            for ($i=$nummer; $i<$nummer+3; $i++)
            {
                $sql = "INSERT INTO keylist (owner,value1,value2,gold,gems,description) VALUES (".$session[user][acctid].",$row[houseid],$i,0,0,'Schl�ssel f�r Haus Nummer $row[houseid]')";
                db_query($sql);
            }
            break;

        case 17 :
            output("`&Dein neues Gasthaus wird dir und deinen Mitbewohnern eine willkommene M�glichkeit zur Rast und St�rkung bieten.`n`7Du erh�ltst `@3`7 weitere Schl�ssel!");

            // Schl�ssel z�hlen (wichtig f�r Numererierung)
            $sql = "SELECT * FROM keylist WHERE value1=".$session['user']['house']." ORDER BY id ASC";
            $result = db_query($sql) or die(db_error(LINK));
            $nummer=db_num_rows($result)+1;
            if (db_num_rows($result))
            {
                db_free_result($result);
            }

            // 3 St�ck auff�llen
            for ($i=$nummer; $i<$nummer+3; $i++)
            {
                $sql = "INSERT INTO keylist (owner,value1,value2,gold,gems,description) VALUES (".$session[user][acctid].",$row[houseid],$i,0,0,'Schl�ssel f�r Haus Nummer $row[houseid]')";
                db_query($sql);
            }
            break;

        case 20 :
            output("`&Deine neue Festung wird ein sehr sicherer Ort f�r alle werden, die darin schlafen.`n");
            break;

        case 24 :
            output("`&Dein neuer Turm wird durch seine H�he noch sicherer f�r dich und deine Mitbewohner sein und durch seine N�he zu den Sternen der perfekte Ort f�r magische Praktiken.");
            break;

        case 27 :
            output("`&Deine neue Burg ist der Inbegriff von Sicherheit und Schutz. Es gibt praktisch keinen Ort, an dem du und deine G�ste unbesorgter und besser schlafen k�nnen.");
            break;

        case 30 :
            output("`&Dein neues Versteck wird jenen Unterschlupf bieten, die es sich mit Allem und Jedem verscherzt haben und nirgendwo mehr sicher sind. Der Hausschatz wird nur minimal, die Nacht nicht sehr erholsam sein, du hast nur noch 5 Schl�ssel!`n");

            // Schl�ssel z�hlen
            $sql = "SELECT * FROM keylist WHERE value1=".$session['user']['house']." ORDER BY id DESC";
            $result = db_query($sql) or die(db_error(LINK));

            // Von hinten beginnend alle Schl�ssel bis auf 5 l�schen
            $nummer=(db_num_rows($result)-5);
            for ($i=0; $i<$nummer; $i++)
            {
                $h = db_fetch_assoc($result);
                $sql="DELETE FROM keylist WHERE id=".$h['id'];
                db_query($sql);
            }
            break;

        case 34 :
            output("`&Dein neues Refugium nutzt seinen Keller um die Versteckm�glichkeiten seiner G�ste gr��er und komfortabler zu gestalten. Du und deine Mitbewohner k�nnen dort nun ohne Beeintr�chtigung n�chtigen.`n");
            break;

        case 37 :
            output("`&Dein neues Kellergew�lbe ist so gestaltet, dass es einen Bewohner mehr aufnehmen kann, auch der Komfort wurde ein klein wenig gehoben. Dennoch ist es immer noch nicht die sch�nste Art zu �bernachten.`n`7Du erh�ltst `@zwei`7 weitere Schl�ssel!`n`&");

            // Schl�ssel z�hlen (wichtig f�r Numererierung)
            $sql = "SELECT * FROM keylist WHERE value1=".$session['user']['house']." ORDER BY id ASC";
            $result = db_query($sql) or die(db_error(LINK));
            $nummer=db_num_rows($result)+1;
            if (db_num_rows($result))
            {
                db_free_result($result);
            }

            // 2 St�ck auff�llen
            for ($i=$nummer; $i<$nummer+2; $i++)
            {
                $sql = "INSERT INTO keylist (owner,value1,value2,gold,gems,description) VALUES (".$session[user][acctid].",$row[houseid],$i,0,0,'Schl�ssel f�r Haus Nummer $row[houseid]')";
                db_query($sql);
            }
            break;

        case 40 :
            output("`&Dein neues Gildenhaus wird seinen Bewohnern neue Anwendungen in ihren Spezialf�higkeiten gew�hren, wenn diese aufgebraucht sind.`n");
            break;

        case 43 :
            output("`&Dein neues Zunfthaus wird k�nftig erfahrene Abenteurer anlocken, von deren Erfahrung alle profitieren k�nnen.`n");

        case 47 :
            output("`&Dein neues Handelshaus wird der zentrale Punkt des Handelns werden.`nGesch�ftsm�nner werden von weither kommen.`n");
            break;

        case 50 :
            output("Dein neuer Bauernhof wird beliebig oft die Tiere seiner G�ste versorgen k�nnen.`n");
            break;

        case 54 :
            output("`&Deine neue Tierfarm wird nicht nur die Versorgung der Tiere gew�hrleisten, sondern auch ihre Ausbildung.`n");
            break;

        case 57 :
            output("`&Dein neuer Gutshof wird die Arbeit derer, die ihn bewohnen, reichlich vergolden und bietet somit eine gute Einnahmequelle bei finanziellen Engp�ssen.`n");
            break;

        case 60 :
            output("`&Deine neue Gruft wird den dunklen G�ttern sicherlich gut gefallen.`n");
            break;

        case 64 :
            output("`&Deine neue Krypta erm�glicht es dir und deinen G�sten mit k�rzlich Verstorbenen in Kontakt zu treten und sie bei ihrer Suche nach Wiedererweckung zu unterst�tzen.`n");
            break;

        case 67 :
            output("`&Deine neuen Katakomben bergen d�rstere Geheimnisse und sind mit Blut und Frevel befleckt.`n");
            break;

        case 70 :
            output("`&Dein neuer Kerker wird dir und deinen Mitbewohnern eine hohe Verantwortung �ber die Gefangenen �bertragen.`n");
            break;

        case 74 :
            output("`&Dein neues Gef�ngnis l�sst dich und deine Mitbewohner ein wenig mehr Kontrolle �ber die Haftdauer der Insassen aus�ben, wenn auch zu einem gewissen Preis.`n");
            break;

        case 77 :
            output("`&Dein neues Verlies bietet dir und deinen Mitbewohnern eine weitere grausige M�glichkeit die Gefangenen zu disziplinieren.`n");
            break;

        case 80 :
            output("`&Dein neues Kloster wird seinen Bewohnern und allen G�sten stets Hilfe und Heilung bieten.`n");
            break;

        case 84 :
            output("`&Deine neue Abtei wird den G�ttern sicherlich gut gefallen. Sei dir ihres Segens, bei eintsprechend hoher Opfergabe, gewiss.`n");
            break;

        case 87 :
            output("`&Dein neuer Ritterorden zieht Recken aus dem ganzen Land an, die Helden suchen, um sie auf ihren Abenteuern zu begleiten.`n");
            break;

        case 90 :
            output("`&Dein neues Trainingslager wird dir und deinen Mitbewohnern eine gute Kampfausbildung erm�glichen.`n");
            break;

        case 94 :
            output("`&Deine neue Kaserne wird dir und deinen Mitbewohnern die M�glichkeit geben durch harten und schmerzhaften Drill an Kampfeskraft zu gewinnen.`n");
            break;

        case 97 :
            output("`&Dein neues S�ldnerlager zieht Schurken aller Art, darunter auch begabte Schmiede, an.`nDiese werden f�r geringes Entgelt deine Ausr�stung verbessern.`n");
            break;

        case 100 :
            output("`&Dein neues Bordell wird dir und deinen G�sten sicherlich sehr viel Freude bereiten.`n");
            break;

        case 104 :
            output("`&Dein neuer Rotlichtpalast bietet dir eine weitere M�glichkeit deine Stimmung zu verbessern.`n");
            break;

        case 107 :
            output("`&Die Spelunke hat neben der M�glichkeit des Am�sierens f�r dich auch noch einige schlagkr�ftige Argumente f�r deine Feinde �brig.`n");
            break;
        }

        addnav("H?Zur�ck zum Haus","inside_houses.php");
        addnav("W?Zur�ck zum Wohnviertel","houses.php");
    }
    // end fertig mit bauen

    else if ($_GET[form]=="build2")
    {

        //Gold
        $paidgold=(int)$_POST['gold'];
        $paidgems=(int)$_POST['gems'];

        if ($session[user][gold]<$paidgold || $session[user][gems]<$paidgems)
        {
            output("`@Du hast nicht genug dabei!");
            addnav("H?Zur�ck zum Haus","inside_houses.php");
            addnav("W?Zur�ck zum Wohnviertel","houses.php");
        }
        else if ($session[user][turns]<1)
        {
            output("`@Du bist zu m�de, um heute noch an deinem Haus zu arbeiten!");
            addnav("H?Zur�ck zum Haus","inside_houses.php");
            addnav("W?Zur�ck zum Wohnviertel","houses.php");
        }
        else if ($paidgold<0 || $paidgems<0)
        {
            output("`@Versuch hier besser nicht zu beschummeln.");
            addnav("H?Zur�ck zum Haus","inside_houses.php");
            addnav("W?Zur�ck zum Wohnviertel","houses.php");
        }
        else
        {
            output("`@Du baust f�r `^$paidgold`@ Gold und `#$paidgems`@ Edelsteine an deinem Haus...`n");
            $row[gold]+=$paidgold;
            $session[user][gold]-=$paidgold;
            output("`nDu verlierst einen Waldkampf.");
            $session[user][turns]--;

            if ($row[gold]>$upgold)
            {
                output("`nDu hast die kompletten Goldkosten bezahlt und bekommst das �bersch�ssige Gold zur�ck.");
                $session[user][gold]+=$row[gold]-$upgold;
                $row[gold]=$upgold;
            }

            $sql = "UPDATE houses SET gold=$row[gold] WHERE houseid=$row[houseid]";
            db_query($sql) or die(db_error(LINK));

            //Edelsteine
            $row[gems]+=$paidgems;
            $session[user][gems]-=$paidgems;

            if ($row[gems]>$upgems)
            {
                output("`nDu hast die kompletten Edelsteinkosten bezahlt und bekommst �bersch�ssige Edelsteine zur�ck.");
                $session[user][gems]+=$row[gems]-$upgems;
                $row[gems]=$upgems;
            }

            if (($row[gems]<$upgems) or($row[gold]<$upgold))
            {
                addnav("H?Zur�ck zum Haus","inside_houses.php");
                addnav("W?Zur�ck zum Wohnviertel","houses.php");
            }

            $goldtopay=$upgold-$row[gold];
            $gemstopay=$upgems-$row[gems];

            $done=round(100-((100*$goldtopay/$upgold)+(100*$gemstopay/$upgems))/2);

            output("`nDein Ausbau ist damit zu `$$done%`@ fertig. Du musst noch `^$goldtopay`@ Gold und `#$gemstopay `@Edelsteine bezahlen, bis du fertig bist.");

            $sql = "UPDATE houses SET gems=$row[gems] WHERE houseid=$row[houseid]";
            db_query($sql) or die(db_error(LINK));

            //fertig
            if ($row[gems]>=$upgems && $row[gold]>=$upgold)
            {
                output("`n`n`bGl�ckwunsch!`b Dein Ausbau ist fertig. Was soll aus deinem sch�nen Haus werden?`n`n");

                if ($row['status']>=10)
                {
                    output("`^Du erweiterst dein Haus um die 2. Ausbaustufe.`nDir stehen 2 M�glichkeiten zu Wahl. Egal wof�r du dich entscheidest, dein Haus wird keine seiner M�glichkeiten verlieren und in jedem Fall verbessert werden.`&`n`n");
                }
                switch ($row['status'])
                {

                case 5 :
                    // Beschreibungen 1. Stufe
                    output("`7Ein `%Anwesen`7 w�rde sehr viel mehr an Reicht�mern horten k�nnen als ein gew�hnliches Haus.`n`n");
                    output("`7Eine `QFestung`7 bietet zus�tzlichen Schutz gegen Angriffe.`n`n");
                    output("`7Ein `tVersteck`7 ist kaum ein Ort zum bequemen wohnen. Wer sich hier verkriecht ist von niemandem aufzusp�ren. Daf�r gibt es allerding kaum Lagerm�glichkeiten f�r Gold und Edelsteine.`n`^Ein Versteck kann h�chstens 5 Zimmer haben. Alle Schl�ssel bis auf 5 werden verloren gehen, solange der Ausbau besteht!`n`n");
                    output("`7Ein `5Gildenhaus`7 w�rde die M�glichkeit bieten zus�tzlich Anwendungen im Spezialgebiet zu erhalten, wenn diese aufgebraucht sind.`n`n");
                    output("`7Ein `tBauernhof`7 ist ein Ort an dem sich Tiere besonders wohl f�hlen und neue Kraft sch�pfen k�nnen.`n`n");
                    output("`7Eine `TGruft`7 ist eine dunkle und finstre Unterkunft f�r dunkle und finstre Kreaturen. Hier kann man u.A. dem Blutgott huldigen. `n`n");
                    output("`7Ein `qKerker`7 h�lt �ble Schurken und Verbrecher gefangen und erteilt ihnen ihre gerechte Strafe. `n`n");
                    output("`7Ein `&Kloster`7 ist ein Ort der Heilung und der Fr�mmigkeit. Hier wird selbstlos jeder armen Seele geholfen. `n`n");
                    output("`7Ein `vTrainingslager`7 beherbergt junge wie alte Krieger. Von den Veteranen kann man sehr viel lernen!`n`n");
                    output("Ein `4Bordell`7 ist ein Ort der Freude und der Lust. Nach einem Besuch ist so mancher Krieger erfolgreicher im Kampf. `n`n");

                    addnav("A?Ein Anwesen!","inside_houses.php?act=upgrade&form=done&choice=10");
                    addnav("F?Eine Festung!","inside_houses.php?act=upgrade&form=done&choice=20");
                    addnav("V?Ein Versteck!","inside_houses.php?act=upgrade&form=done&choice=30");
                    addnav("G?Ein Gildenhaus!","inside_houses.php?act=upgrade&form=done&choice=40");
                    addnav("B?Ein Bauernhof!","inside_houses.php?act=upgrade&form=done&choice=50");
                    addnav("r?Eine Gruft!","inside_houses.php?act=upgrade&form=done&choice=60");
                    addnav("K?Ein Kerker!","inside_houses.php?act=upgrade&form=done&choice=70");
                    addnav("l?Ein Kloster!","inside_houses.php?act=upgrade&form=done&choice=80");
                    addnav("T?Ein Trainingslager!","inside_houses.php?act=upgrade&form=done&choice=90");
                    addnav("o?Ein Bordell!","inside_houses.php?act=upgrade&form=done&choice=100");
                    addnav("h?Muss ich mir noch �berlegen","inside_houses.php");
                    break;

                    // 2. Stufe
                case 11 :
                    output("Eine `%Villa`7 w�rde noch viel mehr an Reicht�mern horten k�nnen als ein Anwesen.`n`n");
                    output("Ein `%Gasthaus`7 w�rde etwas mehr an Reicht�mern horten k�nnen und zus�tzlich die M�glichkeit der St�rkung bei einer guten Suppe bieten.`n`n");
                    addnav("V?Eine Villa!","inside_houses.php?act=upgrade&form=done&choice=14");
                    addnav("G?Ein Gasthaus!","inside_houses.php?act=upgrade&form=done&choice=17");
                    addnav("h?Muss ich mir noch �berlegen","inside_houses.php");
                    break;

                case 21 :
                    output("`7Ein `QMagierturm`7 bietet weiteren Schutz gegen Angriffe und erm�glicht ein Ritual zur St�rkung der mystischen Kr�fte.`n`n");
                    output("`7Eine `QBurg`7 bietet extremen Schutz gegen Angriffe.`n`n");
                    addnav("T?Ein Turm!","inside_houses.php?act=upgrade&form=done&choice=24");
                    addnav("B?Eine Burg!","inside_houses.php?act=upgrade&form=done&choice=27");
                    addnav("h?Muss ich mir noch �berlegen","inside_houses.php");
                    break;

                case 31 :
                    output("`7Ein `tRefugium`7 verliert den Nachteil des schlechten Schlafes und bietet weiterhin Unangreifbarkeit.`n`n");
                    output("`7Ein `tKellergew�lbe`7 mindert den Nachteil des schlechten Schlafes und bietet zus�tzlich 2 weitere Schl�ssel.`n`n");
                    addnav("R?Ein Refugium!","inside_houses.php?act=upgrade&form=done&choice=34");
                    addnav("K?Ein Kellergew�lbe!","inside_houses.php?act=upgrade&form=done&choice=37");
                    addnav("h?Muss ich mir noch �berlegen","inside_houses.php");
                    break;

                case 41 :
                    output("`7Ein `5Zunfthaus`7 w�rde eine leichtere M�glichkeit bieten �fter ins Schloss zu k�nnen.`n`n");
                    output("`7Ein `5Handelshaus`7 erm�glicht dir den Kauf und Verkauf von Edelsteinen bei einem H�ndler von weit her.`n`n");
                    addnav("Z?Ein Zunfthaus!","inside_houses.php?act=upgrade&form=done&choice=44");
                    addnav("H?Ein Handelshaus!","inside_houses.php?act=upgrade&form=done&choice=47");
                    addnav("n?Muss ich mir noch �berlegen","inside_houses.php");
                    break;

                case 51 :
                    output("`7Eine `tTierfarm`7 erm�glicht das fachgerechte Training von Tieren.`n`n");
                    output("`7Ein `tGutshof`7 ist ein Ort an dem sich schnell durch Arbeit Gold verdienen l�sst.`n`n");
                    addnav("T?Eine Tierfarm!","inside_houses.php?act=upgrade&form=done&choice=54");
                    addnav("G?Ein Gutshof!","inside_houses.php?act=upgrade&form=done&choice=57");
                    addnav("Muss ich mir noch �berlegen","inside_houses.php");
                    break;

                case 61 :
                    output("`7Eine `TKrypta`7 erm�glicht es bei Ramius ein gutes Wort f�r Verstorbene einzulegen. `n`n");
                    output("`TKatakomben`7 beherbergen einen rituellen Opferschrein mit dem es m�glich ist sich selbst ins Reich der Toten zu bef�rdern. `n`n");
                    addnav("y?Eine Krypta!","inside_houses.php?act=upgrade&form=done&choice=64");
                    addnav("K?Katakomben!","inside_houses.php?act=upgrade&form=done&choice=67");
                    addnav("h?Muss ich mir noch �berlegen","inside_houses.php");
                    break;

                case 71 :
                    output("`7Ein `qGef�ngnis`7 macht es m�glich Insassen zu befreien, allerdings zu einem hohen Preis.`n`n");
                    output("`7Ein `qVerlies`7 erm�glicht es Gefangene in brutalen K�figk�mpfen gegen Bestien antreten zu lassen und mit einer guten Wette noch etwas Gold zu verdienen. `n`n");
                    addnav("G?Ein Gef�ngnis!","inside_houses.php?act=upgrade&form=done&choice=74");
                    addnav("V?Ein Verlies!","inside_houses.php?act=upgrade&form=done&choice=77");
                    addnav("h?Muss ich mir noch �berlegen","inside_houses.php");
                    break;

                case 81 :
                    output("`7Eine `&Abtei`7 ist ein Ort des Segens. Bei ausreichend Spende und Gebet wird dieser Segen jedem gew�hrt. `n`n");
                    output("`7Ein `&Ritterorden`7 erm�glicht es einen jungen Knappen als treuen Wegbegleiter zu erhalten. `n`n");
                    addnav("A?Eine Abtei!","inside_houses.php?act=upgrade&form=done&choice=84");
                    addnav("R?Ein Ritterorden!","inside_houses.php?act=upgrade&form=done&choice=87");
                    addnav("h?Muss ich mir noch �berlegen","inside_houses.php");
                    break;

                case 91 :
                    output("`7In einer `vKaserne`7 lassen sich mit schwei�treibendem Training Angriff und Verteidigung verbessern!`n`n");
                    output("`7Im `vS�ldnerlager`7 werten geschickte Schmiede Waffen und R�stungen auf!`n`n");
                    addnav("K?Eine Kaserne!","inside_houses.php?act=upgrade&form=done&choice=94");
                    addnav("S?Ein S�ldnerlager!","inside_houses.php?act=upgrade&form=done&choice=97");
                    addnav("h?Muss ich mir noch �berlegen","inside_houses.php");
                    break;

                case 101 :
                    output("Im `4Rotlichtpalast`7 lassen sich wilde, stimmungserheiternde Orgien feiern. `n`n");
                    output("Eine `4Spelunke`7 zieht Gauner, Ganoven und Schl�ger an, die sich gern f�r ein kleines Entgeld beauftragen lassen. `n`n");
                    addnav("R?Ein Rotlichtpalast!","inside_houses.php?act=upgrade&form=done&choice=104");
                    addnav("S?Eine Spelunke!","inside_houses.php?act=upgrade&form=done&choice=107");
                    addnav("h?Muss ich mir noch �berlegen","inside_houses.php");
                    break;
                }

            }
            // END Einzahlung ok, Ausbaukosten komplett



        }
        // END - Einzahlung ok

    }
    // end get[form]=build2

    else
    {
        if ($session[user][turns]<1)
        {
            output("`@Du bist zu ersch�pft, um heute noch irgendetwas zu bauen. Warte bis morgen.");
            addnav("H?Zur�ck zum Haus","inside_houses.php");
            addnav("W?Zur�ck zum Wohnviertel","houses.php");
        }

        // im Ausbau ?
        else if (($row[status]==5) || ($row[status]==11) || ($row[status]==21) || ($row[status]==31) ||
        ($row[status]==41) || ($row[status]==51) || ($row[status]==61) || ($row[status]==71) ||
        ($row[status]==81) || ($row[status]==91) || ($row[status]==101))
        {

            if ($row[gems]>=$upgems && $row[gold]>=$upgold)
            {
                // Kosten bezahlt ?
                redirect("inside_houses.php?act=upgrade&form=build2");
            }

            output("`@Du schaust wie weit du mit dem Ausbau bereits bist.`n`n");
            $goldtopay=$upgold-$row[gold];
            $gemstopay=$upgems-$row[gems];

            $done=round(100-((100*$goldtopay/$upgold)+(100*$gemstopay/$upgems))/2);

            output("`nEs ist zu `$$done%`@ fertig. Du musst noch `^$goldtopay`@ Gold und `#$gemstopay `@Edelsteine bezahlen.`nWillst du jetzt weiter bauen?`n`n");

            output("`0<form action=\"inside_houses.php?act=upgrade&form=build2\" method='POST'>",true);
            output("`nWieviel Gold zahlen? <input type='gold' name='gold'>`n",true);
            output("`nWieviele Edelsteine? <input type='gems' name='gems'>`n",true);
            output("<input type='submit' class='button' value='Bauen'>",true);
            addnav("","inside_houses.php?act=upgrade&form=build2");
            addnav("H?Zur�ck zum Haus","inside_houses.php");
            addnav("W?Zur�ck zum Wohnviertel","houses.php");

        }
        // End - Ausbau bereits begonnen

        else
        {

            output("`@Du �berlegst ob du aus deinem schn�den normalen Wohnhaus nicht etwas Gr��eres, Sch�neres machen k�nntest.");

            output(" Ein Ausbau w�rde dich `^$upgold Gold`@ und `#$upgems Edelsteine`@ kosten. Wie beim Hausbau kannst du es ansparen.`n");

            output(" `4W�hrend des Ausbaus kann niemand aus dem Haus Gold oder Edelsteine aus der Truhe nehmen!`@");

            output(" Ein gestartetes Ausbauvorhaben kann nicht abgebrochen werden.`n`nWillst du mit dem Hausausbau beginnen?");

            addnav("b?Ausbau beginnen","inside_houses.php?act=upgrade&form=start");

            addnav("H?Zur�ck zum Haus","inside_houses.php");

            addnav("W?Zur�ck zum Wohnviertel","houses.php");

        }

    }



    //Hausausbau Ende



    // Ausbau entfernen

}
else if ($_GET[act]=="removeupg")
{

    output("`#Die Kosten, die durch das Entfernen des Ausbaus entstehen werden gerade durch den Wert der Baumaterialien gedeck.`n");

    output("`@Du wirst also NICHTS von deinem investierten Gold oder von den Edelsteinen zur�ckerhalten.`n");

    if ($row[status]==10 || $row[status]==14 ||$row[status]==17)
    {
        output("`7Deine vergr��erte Schatzkammer wird entfernt. Alles Gold und alle Edelsteine, die danach zuviel in deiner Truhe sind gehen `4UNWIEDERBRINGLICH VERLOREN!`7`n");
    }

    output("`@Dein Haus wird wieder ein gew�hnliches Wohnhaus sein. Gekaufte Privatgem�cher werden entfernt. Zus�tzliche Schl�ssel der Villa werden entfernt, durch einen Umbau zum Versteck, Refugium oder Kellergew�lbe verloren gegangene R�ume werden wieder hergestellt. Bist du sicher, dass du das willst ??");

    addnav("Ja, weg damit!","inside_houses.php?act=rip",false,false,false,false);

    addnav("NEIN! Zur�ck zum Haus","inside_houses.php");



}
else if ($_GET[act]=="rip")
{



    if ($row[gold]>round($goldcost/2))
    {
        $row[gold]=round($goldcost/2);
    }

    if ($row[gems]>$gemcost)
    {
        $row[gems]=$gemcost;
    }

    $sql = "SELECT status,houseid,owner FROM houses WHERE owner=".$session[user][acctid]."";
    $res = db_query($sql) or die(db_error(LINK));

    if(!db_num_rows($res)) {
    	output('`n`n`$Fehler: Ich konnte kein Haus finden, das dir geh�rt! Schreibe bitte eine Anfrage.`n');
    	page_footer();
    	exit();
    }

    $house = db_fetch_assoc($res);

    // Privatgem�cher zur�cksetzen
    // (au�er Einladungen in Privatraum des Hausherrn)
    item_delete(' (tpl_id="prive" OR tpl_id="privb") AND value1='.$house['houseid'].' AND value2!='.$house['owner'].'' );

    // Anwesen - Schl�ssel l�schen
    if ($house[status]==10)
    {
        $sql = "SELECT id FROM keylist WHERE value1=".$session[user][house]." ORDER BY id DESC LIMIT 0,5";
        $result = db_query($sql) or die(db_error(LINK));

        while ($h=db_fetch_assoc($result))
        {
            $sql="DELETE FROM keylist WHERE id=".$h['id'];
            db_query($sql);
        }
    }

    // Villa, Gasthaus - Schl�ssel l�schen
    else if (($house[status]==14) || ($house[status]==17))
    {
        $sql = "SELECT id FROM keylist WHERE value1=".$session[user][house]." ORDER BY id DESC LIMIT 0,8";
        $result = db_query($sql) or die(db_error(LINK));

        while ($h=db_fetch_assoc($result))
        {
            $sql="DELETE FROM keylist WHERE id=".$h['id'];
            db_query($sql);
        }
    }

    // Versteck, Refugium, Kellergew�lbe - Schl�ssel zur�ck
    else if (($house[status]>29) && ($house[status]<40))
    {
        $sql = "SELECT * FROM keylist WHERE value1=".$session[user][house]." ORDER BY id ASC";
        $result = db_query($sql) or die(db_error(LINK));
        $nummer=db_num_rows($result)+1;
        if (db_num_rows($result))
        {
            db_free_result($result);
        }

        for ($i=$nummer; $i<10; $i++)
        {

            $sql = "INSERT INTO keylist (owner,value1,value2,gold,gems,description) VALUES (".$session[user][acctid].",$row[houseid],$i,0,0,'Schl�ssel f�r Haus Nummer $row[houseid]')";
            db_query($sql);
        }
    }

    $sql = "UPDATE houses SET status=1,gems=$row[gems],gold=$row[gold] WHERE houseid=$row[houseid]";

    db_query($sql);

    redirect("inside_houses.php");

}
else if ($_GET[act]=="rename")
{

    if (!$_POST[housename])
    {

        output("`2Das Haus umbenennen kostet `^1000`2 Gold und `#1`2 Edelstein.`n`n");

        output("`0<form action=\"inside_houses.php?act=rename\" method='POST'>Vorschau: ",true);

        rawoutput(js_preview('housename').'<br><br>');
        rawoutput("Gebe einen neuen Namen f�r dein Haus ein: <input name='housename' maxlength='40' value='$row[housename]'>");

        output("<br><input type='submit' class='button' value='Umbenennen'>",true);

        addnav("","inside_houses.php?act=rename");

    }
    else
    {

        if ($session[user][gold]<1000 || $session[user][gems]<1)
        {

            output("`2Das kannst du nicht bezahlen.");

        }
        else
        {

        	$fixed = strip_appoencode(stripslashes($_POST['housename']),2);
        	$fixed = strip_tags($fixed);
        	output("`2Dein Haus `@$row[housename]`2 hei�t jetzt `@".$fixed."`2.");

            $sql = "UPDATE houses SET housename='".addslashes($fixed)."`0' WHERE houseid=$row[houseid]";

            db_query($sql);

            $session[user][gold]-=1000;

            $session[user][gems]-=1;

        }

    }

    addnav("H?Zur�ck zum Haus","inside_houses.php");

}
else if ($_GET[act]=="desc")
{

	$int_max_length = getsetting('housedesclen',500);

    if (!$_POST['description'])
    {

        output("`2Hier kannst du die Beschreibung f�r dein Haus �ndern.`n`nDie aktuelle Beschreibung lautet:`0$row[description]`0`n");

        output("`0<form action=\"inside_houses.php?act=desc\" method='POST'>",true);

        $arr_form = array(	'desc_pr'=>'Vorschau:,preview,description',
        					'description'=>'`2Gebe eine Beschreibung f�r dein Haus ein:`n,textarea,40,20,'.$int_max_length);

        showform($arr_form,$row,false,'�bernehmen!');

        output("</form>",true);

        addnav("","inside_houses.php?act=desc");

    }
    else
    {

        $fixed = closetags(stripslashes($_POST['description']),'`c`i`b');
        $fixed = strip_tags($fixed,'<img>');
        $fixed = substr($fixed,0,$int_max_length);
        
        output("`2Die Beschreibung wurde ge�ndert.`n`0".$fixed."`2.");
        $sql = "UPDATE houses SET description='".addslashes($fixed)."`0' WHERE houseid=$row[houseid]";

        db_query($sql);

    }

    addnav("H?Zur�ck zum Haus","inside_houses.php");

}
else if ($_GET[act]=="logout")
{


    $sql = "UPDATE account_extra_info SET hadnewday=1 WHERE acctid = ".$session['user']['acctid'];
    db_query($sql) or die(sql_error($sql));

    redirect('login.php?op=logout&loc='.USER_LOC_HOUSE.'&restatloc='.$row['houseid']);


}
else
{

    $show_invent = true;

    output("`2`b`c$row[housename]`c`b");
    output("`2`c( ".get_house_state($row[status],false)."`2)`0`c`n");

    if ($row[description])
    {
    	$row['description'] = strip_tags(closetags($row['description'],'`c`i`b'),'<img>');
        output("`0`c$row[description]`c`n");
    }

    output("`2Du und deine Mitbewohner haben `^$row[gold]`2 Gold und `#$row[gems]`2 Edelsteine im Haus gelagert.`n");

    if (getsetting('activategamedate','0')==1)
    {
        output("Wir schreiben den `^".getgamedate()."`2.`n");
    }

    output("Es ist jetzt `^".getgametime()."`2 Uhr.`n`n");

    viewcommentary("house-".$row[houseid],"Mit Mitbewohnern reden:",30,"sagt",false,true,false,false,true);

    output("`n`n`n<table border='0'><tr><td>`2`bDie Schl�ssel:`b `0</td><td>`2`bExtra Ausstattung`b</td></tr><tr><td valign='top'>",true);

    $sql = 'SELECT 	keylist.*,
					accounts.acctid AS aid,accounts.name AS besitzer, accounts.restatlocation, accounts.laston
			FROM keylist
			LEFT JOIN accounts ON accounts.acctid=keylist.owner
			WHERE value1='.$row['houseid'].' ORDER BY id ASC';

    $result = db_query($sql) or die(db_error(LINK));

	$int_keycount = db_num_rows($result);

	// Nach wie vielen Sekunden werden Inaktive gel�scht?
	// Bei 90% der verstrichenen Zeit eine Warnung anzeigen
	$int_deluser = round((int)getsetting('expireoldacct',50) * 0.85 * 86400);
	// Point of no return
	$int_warningtime = time() - $int_deluser;

    for ($i=1; $i<=$int_keycount; $i++)
    {

        $item = db_fetch_assoc($result);

        if ($item[besitzer]=="")
        {

            output("`n`2".$i.": `4`iVerloren`i`0");

        }
        else
        {

            output("`n`2".$i.": `&$item[besitzer]`0");

            // Warnung f�r Hausbesitzer, falls User vor L�schung steht
            if($row['owner'] == $session['user']['acctid']) {

            	$int_laston = strtotime($item['laston']);
            	if($int_laston <= $int_warningtime) {
            		output(' `$(Verschwindet bald)`0 ');
            	}
            }

// Link auf die Bio einf�gen
        }

        if ($item[aid]==$row[owner])
        {
            output(" (der Eigent�mer) ");
        }

        if ($item['restatlocation'] == $row['houseid'] && $item['owner']>0)
        {
            output(" `ischl�ft hier`i");
        }


    }

    if (db_num_rows(db_query('SELECT acctid FROM accounts WHERE acctid='.$row['owner'].' AND restatlocation='.$row['houseid'].' AND location='.USER_LOC_HOUSE))>0) {
		output("`nDer Eigent�mer schl�ft hier");
	}

    output("</td><td valign='top'>",true);

    // M�bel mit besonderen Funktionen
    $properties = ' owner < 1234567 AND deposit>0 AND deposit1='.$session['housekey'].' AND deposit2=0 ';
    $extra = ' ORDER BY name DESC, id ASC';

    $res = item_list_get($properties , $extra , true , ' name,description,id,furniture_hook ' );

    $count = db_num_rows($res);

    $hooks = '';
    $furniturenav = array();$navcount=0;
    for($i=1; $i<=$count; $i++)
    {

        $item = db_fetch_assoc($res);
        output("`n`c<hr width=90% style='border:1px solid #484848;'>`c`&$item[name]`0 (`i$item[description]`i)");

        if ($item['furniture_hook'] != '' && !$hooks[$item['furniture_hook']])
        {
            $hooks[$item['furniture_hook']] = true;
//nach unten    addnav($item['name'],'furniture.php?item_id='.$item['id']);
            $furniturenav[$navcount][0]=$item['name'];
            $furniturenav[$navcount][1]='furniture.php?item_id='.$item['id'];
            $navcount++;
        }

    }

    output("</td></tr></table>",true);

    addnav("Hausschatz");

    addnav("G?Gold deponieren","inside_houses.php?act=givegold");

    addnav("n?Gold mitnehmen","inside_houses.php?act=takegold");

    addnav(':');

    addnav("E?Edelsteine deponieren","inside_houses.php?act=givegems");

    addnav("t?Edelsteine mitnehmen","inside_houses.php?act=takegems");

    if ($session[user][house]==$session[housekey])
    {

        addnav("Schl�ssel");

        addnav("V?Vergeben","inside_houses.php?act=givekey");

        addnav("n?Zur�cknehmen","inside_houses.php?act=takekey");

    }

    ///////////////////////
    // Privatraumerweiterung (NEU)
    $sql = 'SELECT tpl_id,id,a.name AS playername,i.value2 AS private_owner,i.name FROM items i
LEFT JOIN accounts a ON a.acctid=i.value2
WHERE (i.tpl_id="prive" OR i.tpl_id="privb") AND i.value1='.$session['housekey'].' AND i.owner='.$session['user']['acctid'];
    $res = db_query($sql);

    $own_room = ($session['user']['house']==$session['housekey']) ? true : false;

    if (db_num_rows($res) > 0 || $own_room)
    {
        addnav("Privates");

        while ($p = db_fetch_assoc($res))
        {

            if ($p['tpl_id'] == 'privb')
            {
                // eigener Raum
                $own_room = true;
            }
            else
            {
            	addnav('Privatgemach von '.$p['playername'],'houses_private.php?private='.$p['private_owner'].'&housekey='.$session['housekey'],false,false,false,false);
            }

        }

        if ($own_room)
        {
            addnav('h?Eigenes Gemach','houses_private.php?private='.$session['user']['acctid'].'&housekey='.$session['housekey']);

        }

    }

    if ($session['user']['house']==$session['housekey'] && $row['status'] >= 10)
    {

        addnav('Privatgem�cher vergeben','houses_private.php?op=raum_geben&private='.$session['user']['acctid'],false,false,false,false);
        addnav('Privatgem�cher abnehmen','houses_private.php?op=raum_nehmen&private='.$session['user']['acctid'],false,false,false,false);

    }
    //	}

    // ENDE Privatraumerweiterung
    /////////////////////


    if ($session[user][house]==$session[housekey])
    {

        addnav("Sonstiges");

        if ($row[status]<=5)
        {
            addnav("a?Haus ausbauen","inside_houses.php?act=upgrade");
        }

        if ((($row[status]==10) || ($row[status]==11) || ($row[status]==20) || ($row[status]==21) || ($row[status]==30) || ($row[status]==31) || ($row[status]==40) || ($row[status]==41) || ($row[status]==50) || ($row[status]==51) || ($row[status]==60) || ($row[status]==61) || ($row[status]==70) || ($row[status]==71) || ($row[status]==80) || ($row[status]==81) || ($row[status]==90) || ($row[status]==91) || ($row[status]==100) || ($row[status]==101)))
        {
            addnav("a?Haus weiter ausbauen","inside_houses.php?act=upgrade");
        }

        if ($row[status]>5)
        {
            addnav("Ausbau entfernen","inside_houses.php?act=removeupg",false,false,false,false);
        }

        addnav("Haus umbenennen","inside_houses.php?act=rename",false,false,false,false);

        addnav("Beschreibung �ndern","inside_houses.php?act=desc",false,false,false,false);

    }

    if (($row[status]>=10) && (($row[status]<30) || ($row[status]>=40)))
    {
        addnav("Besonderes");
    }

    if (($row[status]>=40) && ($row[status]<50))
    {
        addnav("r?Mit den Gildenmeistern reden (1000 Gold)","housefeats.php?act=fill");
    }

    if (($row[status]>=20) && ($row[status]<30))
    {
        addnav("K?Ins Kellergew�lbe","housefeats.php?act=cry");
    }

    if ((($row[status]>=50) && ($row[status]<60)) && ($session['user']['hashorse']>0))
    {
        addnav("r?Tier versorgen","housefeats.php?act=feed");
    }

    if (($row[status]>=100) && ($row[status]<110))
    {
        addnav("r?Am�sieren (2000 Gold)","housefeats.php?act=amuse");
    }

    if (($row[status]>=90) && ($row[status]<100))
    {
        addnav("r?Mit den Veteranen trainieren (3000 Gold)","housefeats.php?act=train");
    }

    if (($row[status]>=70) && ($row[status]<80))
    {
        addnav("q?Gefangene qu�len","housefeats.php?act=torture");
    }

    if (($row[status]>=80) && ($row[status]<90))
    {
        addnav("r?Heilung erbitten","housefeats.php?act=healing");
    }

    if ((($row[status]>=60) && ($row[status]<70)) && ($session['user']['level']>1))
    {
        addnav("r?Dem Blutgott opfern","housefeats.php?act=sacrifice");
    }

    if (($row[status]>=17) && ($row[status]<20))
    {
        addnav("K?M�tterchens Kohlsuppe kosten","housefeats.php?act=soup");
    }

    if (($row[status]>=24) && ($row[status]<27))
    {
        addnav("R?Ritual abhalten (1 Edelstein)","housefeats.php?act=ritual");
    }

    if (($row[status]>=44) && ($row[status]<47))
    {
        addnav("A?Zu den Abenteurern","housefeats.php?act=adventure");

        // Knappe
        $sql = "SELECT name,state FROM disciples WHERE master=".$session['user']['acctid']."";
        $result = db_query($sql) or die(db_error(LINK));
        $rowk['state']=0;
        if (db_num_rows($result)>0)
        {
            $rowk = db_fetch_assoc($result);
        }

        if ($rowk['state'] == 0 && db_num_rows($result))
        {
            addnav("K?Verlorenen Knappen suchen","housefeats.php?act=searchdisciple");
        }
    }

    if (($row[status]>=47) && ($row[status]<50))
    {
        addnav("S?Zum Schmuckh�ndler","housefeats.php?act=gems");
        addnav("i?Zum Lieferanten","housefeats.php?act=sendtrophy");
    }

    if (($row[status]>=54) && ($row[status]<57))
    {
        addnav("i?Zum Tiertrainer","housefeats.php?act=trainanimal");
    }

    if (($row[status]>=57) && ($row[status]<60))
    {
        addnav("a?Hart arbeiten","housefeats.php?act=workhard");
    }

    if (($row[status]>=64) && ($row[status]<67))
    {
        addnav("A?Zum Ahnenschrein","housefeats.php?act=givepower");
    }

    if (($row[status]>=67) && ($row[status]<70))
    {
        addnav("O?Zum Opferschrein","housefeats.php?act=suicide");
    }

    if (($row[status]>=74) && ($row[status]<77))
    {
        addnav("O?Zum Oberaufseher","housefeats.php?act=exchange");
    }

    if (($row[status]>=77) && ($row[status]<80))
    {
        addnav("a?Zur Gefangenenarena","housefeats.php?act=arena");
    }

    if (($row[status]>=84) && ($row[status]<87))
    {
        addnav("S?Den Segen erbitten","housefeats.php?act=bless");
    }

    if (($row[status]>=87) && ($row[status]<90))
    {
        addnav("K?Einen Knappen annehmen (20 Edelsteine)","housefeats.php?act=disciple");
    }

    if (($row[status]>=94) && ($row[status]<97))
    {
        addnav("t?Mit den Meistern trainieren (".($session['user']['level']*750)." Gold)","housefeats.php?act=mastertrain");
    }

    if (($row[status]>=97) && ($row[status]<100))
    {
        addnav("s?Zum Lagerschmied","housefeats.php?act=smith");
    }

    if (($row[status]>=104) && ($row[status]<107))
    {
        addnav("O?Orgie (3000 Gold)","housefeats.php?act=orgy");
    }

    if (($row[status]>=107) && ($row[status]<110))
    {

        // "Mafia"-Special
        $sql = "SELECT beatenup FROM account_extra_info WHERE acctid=".$session['user']['acctid']."";
        $result = db_query($sql) or die(db_error(LINK));
        $rowb = db_fetch_assoc($result);

        addnav("F?\"Die Familie\" befragen","housefeats.php?act=checkfriend");

        if ($rowb['beatenup']>1)
        {
            addnav("S?Schl�ger anheuern","housefeats.php?act=beater");
        }
        else
        {
            addnav("b?\"Familie\" beschenken","housefeats.php?act=familygift");
        }
        addnav("O?Zum Orkisch Roulette","housefeats.php?act=roulette");
    }

    if (($row[status]>=60) && ($row[status]<70) && ($session['user']['race']=='vmp'))
    {
    	// Knappe
	    $sql = "SELECT name,state FROM disciples WHERE master=".$session['user']['acctid']."";
	    $result = db_query($sql) or die(db_error(LINK));
	    $rowk['state']=0;
	    if (db_num_rows($result)>0)
	    {
	        $rowk = db_fetch_assoc($result);
	    }
	    if(($rowk['state']>0) && ($rowk['state']<19)) {
        	addnav("b?".$rowk['name']." bei�en","housefeats.php?act=dbite");
	    }
    }
    if (($row[status]>=60) && ($row[status]<70) && ($session['user']['race']=='wwf'))
    {
    	// Knappe
	    $sql = "SELECT name,state FROM disciples WHERE master=".$session['user']['acctid']."";
	    $result = db_query($sql) or die(db_error(LINK));
	    $rowk['state']=0;
	    if (db_num_rows($result)>0)
	    {
	        $rowk = db_fetch_assoc($result);
	    }
	    if(($rowk['state']>0) && ($rowk['state']<19)) {
        	addnav("b?".$rowk['name']." bei�en","housefeats.php?act=dbite2");
	    }
    }

    addnav("Ausgang");
    addnav("L?Einschlafen (Log Out)","inside_houses.php?act=logout");

    addnav("W?Zur�ck zum Wohnviertel","houses.php?op=enter");

    addnav("D?Zur�ck zum Dorf","village.php");
    addnav("M?Zur�ck zum Marktplatz","market.php");
    if($furniturenav)
    {
        addnav("Einrichtung");
        for($i=sizeof($furniturenav)-1;$i>=0;$i--){
          addnav($furniturenav[$i][0],$furniturenav[$i][1]);
        }
    }
}

page_footer();
?>

