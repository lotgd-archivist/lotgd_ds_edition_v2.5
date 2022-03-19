<?php
// Richter-Addon : Erg�nzung zu Dorfamt u. Stadtwache
// Ben�tigt : [profession] (shortint, unsigned) in [accounts]
//             Tabellen [crimes],[cases]

// by Maris (Maraxxus@gmx.de)

require_once "common.php";
require_once(LIB_PATH.'board.lib.php');
require_once(LIB_PATH.'profession.lib.php');

page_header("Der Gerichtshof");

if (!isset($session))
{
    exit();
}

$op = ($_GET['op']) ? $_GET['op'] : "court";

if ($_GET['op']=="newsdelete")
{
    $sql = "DELETE FROM crimes WHERE newsid='$_GET[newsid]'";
    db_query($sql);
    $return = $_GET['return'];
    $return = preg_replace("'[?&]c=[[:digit:]-]*'","",$return);
    $return = substr($return,strrpos($return,"/")+1);
    redirect($return);
}

switch ($op)
{

case 'bewerben':

    output("`&Du holst tief Luft und �ffnest langsam die schwere Eichent�re. Ein betagter Mann mit dichtem Backenbart sitzt hinter einem Tisch aus dunklem Holz und ist gerade in seine Arbeit vertieft. Als die Ger�usche deiner Schritte auf dem Holzboden zu ihm dringen blickt er auf. \"`#Wen haben wir denn hier?`&\" fragt er mit einem sadistischem Grinsen. Nachdem du dich vorgestellt und ihm dein Anliegen mitgeteilt hast kneift er die Augen zusammen.`n`n");
    $maxamount = getsetting("numberofjudges",10);
    $reqdk = getsetting("judgereq",50);

    $sql = "SELECT profession FROM accounts WHERE profession=".PROF_JUDGE_HEAD." OR profession=".PROF_JUDGE;
    $result = db_query($sql) or die(db_error(LINK));
    if ((db_num_rows($result)) < $maxamount)
    {

        if (($session['user']['profession']==PROF_JUDGE_ENT) || ($session['user']['profession']==24))
        {
            output("\"`# ".($session['user']['name'])."! So sehr ich Euren Wunsch nachempfinden kann wieder richten zu d�rfen muss ich Euch jedoch entt�uschen. Ihr hattet Eure Chance! Und nun verlasst mein B�ro!`&\"");
        }
        else
        {
            output("\"`# ".($session['user']['name'])."!`# Ich hoffe Ihr wisst worauf Ihr Euch hier einlasst? Das Amt des Richters ist hart und entbehrungsreich. Und an Euch werden besondere Forderungen gestellt : Ihr m�sst sowohl ruhmreich wie auch von h�chstem Ansehen sein und in Eurem Verhalten ein Vorbild!`&\"`n`n");

            if (($session['user']['dragonkills']) >= $reqdk)
            {
                if ($session['user']['reputation']>=50)
                {
                    output("\"`#Ich sehe, ich sehe... Ihr seid sowohl ruhmreich, wie auch von allerh�chstem Ansehen! Das ist gut, sehr gut. Meinetwegen k�nnt Ihr sofort anfangen. Doch wisset, dass Ihr als Richter nicht nur Rechte, sondern auch Pflichten habt. Es ist Euch strengstens untersagt mit zwielichtigen Gesellen Kontakte zu kn�pfen, auch nicht zur T�uschung! Jedes Eurer Urteile muss gerecht und nachvollziehbar sein! Geschenke anzunehmen ist Euch strengstens untersagt!`n Dem obersten Richter habt Ihr Folge zu leisten! Sollte man Euch bei irgendeinem Versto� oder irgendeiner Unehrenhaftigkeit erwischen, seid Ihr f�r lange Zeit Richter gewesen! Sind wir uns da einige?`nAlso, wollt Ihr noch immer ?`&\"");
                    addnav("Ja, Richter werden","court.php?op=bewerben_ok");
                }
                else
                {
                    output("\"`#Ruhmreich seid mehr als es von N�ten w�re, doch f�rchte ich, dass Euch die Leute nicht trauen w�rden, wenn Ihr pl�tzlich in Richterrobe daher k�met. Tut mal etwas f�r Euer Ansehen und versucht es dann noch einmal!`&\"");
                }
            }
            else
            {
                output("\"`#Ihr seid zwar ruhmreich, doch wie es mir scheint nicht ruhmreich genug. Ihr solltet noch mehr Ruhm im Kampf gegen den Drachen erlangen und es dann noch einmal versuchen!`&\"");
            }
        }
        // Kein entlassener
    }
    // Noch nicht zu viele
    else
    {
        output("\"`#Es tut mir sehr leid, aber das Dorf hat zur Zeit gen�gend Richter. Versucht es doch sp�ter noch einmal!`&\"");
    }

    addnav("Zur�ck","dorfamt.php");

    break;

case 'bewerben_ok':

    output("`&Du �berreichst dem alten Mann dein Bewerbungsschreiben. Dieser verstaut es unter einem hohen Stapel Pergamenten und meint: \"Wir werden auf dich zur�ckkommen!\"");
    $session['user']['profession']=PROF_JUDGE_NEW;
    $sql = "SELECT acctid FROM accounts WHERE profession=".PROF_JUDGE_HEAD." ORDER BY loggedin DESC, RAND() LIMIT 1";
    $res = db_query($sql);
    if (db_num_rows($res))
    {
        $w = db_fetch_assoc($res);
        systemmail($w['acctid'],"`&Neue Bewerbung!`0","`&".$session['user']['name']."`& hat sich als Richter beworben. Du solltest die Bewerbung �berpr�fen und eine Entscheidung treffen.");
    }

    addnav("Zur�ck","dorfamt.php");

    break;

case 'bewerben_abbr':

    $session['user']['profession'] = 0;
    output("Du ziehst deine Bewerbung zur�ck.");
    addnav("Zur�ck","dorfamt.php");

    break;

case 'aufn':

    $pid = (int)$_GET['id'];

    $sql = "SELECT COUNT(*) AS anzahl FROM accounts WHERE (profession=".PROF_JUDGE_HEAD." OR profession=".PROF_JUDGE.")";
    $res = db_query($sql);
    $p = db_fetch_assoc($res);

    if ($p['anzahl'] >= getsetting("numberofjudges",10))
    {
        output("Es gibt bereits ".$p['anzahl']." Richter! Mehr sind zur Zeit nicht m�glich.");
        addnav("Zur�ck","court.php?op=listj");
    }
    else
    {

        $sql = "UPDATE accounts SET profession = ".PROF_JUDGE."
WHERE acctid=".$pid;
        db_query($sql) or die(db_error(LINK));

        $sql = "SELECT name FROM accounts WHERE acctid=".$pid;
        $res = db_query($sql);
        $p = db_fetch_assoc($res);

        systemmail($pid,"Du wurdest aufgenommen!",$session['user']['name']."`& hat deine Bewerbung zum Richter angenommen. Damit bist du vom heutigen Tage an offiziell H�ter f�r Recht und Ordnung!");

        $sql = "INSERT INTO news SET newstext = '".addslashes($p['name'])." `&wurde heute offiziell das ehrenvolle Amt eines Richters zugewiesen!',newsdate=NOW(),accountid=".$pid;
        db_query($sql) or die(db_error(LINK));

        addhistory('`2Aufnahme ins Richteramt',1,$pid);

        addnav("Willkommen!","court.php?op=listj");

        output("Der neue Richter ist jetzt aufgenommen!");
    }

    break;

case 'abl':

    $pid = (int)$_GET['id'];

    $sql = "UPDATE accounts SET profession = 0
WHERE acctid=".$pid;
    db_query($sql) or die(db_error(LINK));

    systemmail($pid,"Deine Bewerbung wurde abgelehnt!",$session['user']['name']."`& hat deine Bewerbung als Richter abgelehnt.");

    addnav("Zur�ck","court.php?op=listj");

    break;

case 'entlassen':

    $pid = (int)$_GET['id'];

    $sql = "UPDATE accounts SET profession = 0
WHERE acctid=".$pid;
    db_query($sql) or die(db_error(LINK));

    $sql = "SELECT name FROM accounts WHERE acctid=".$pid;
    $res = db_query($sql);
    $p = db_fetch_assoc($res);

    systemmail($pid,"Du wurdest entlassen!",$session['user']['name']."`& hat dich als Richter entlassen!");

    $sql = "INSERT INTO news SET newstext = '".addslashes($p['name'])." `&wurde heute vom Amt eines Richters enthoben!',newsdate=NOW(),accountid=".$pid;
    db_query($sql) or die(db_error(LINK));

    addhistory('`$Entlassung aus dem Richteramt',1,$pid);

    addnav("Weiter","court.php?op=listj");

    output("Der Richter wurde entlassen!");

    break;

case 'leave':

    output("`&Mit schlotternden Knien betrittst du das Zimmer, in dem der �ltere Herr mit dem Backenbart wie gewohnt hinter seinem Schreibtisch sitzt. Als du eintrittst und ihm die Hand reichst bittet er dich Platz zu nehmen und schau dich erwartungsvoll an.`nWillst du wirklich dein Richteramt aufgeben?");
    addnav("Ja, austreten!","court.php?op=leave_ok");
    addnav("NEIN. Dabei bleiben","dorfamt.php");

    break;

case 'leave_ok':

    output("`&Du bittest um deine Entlassung und der �ltere Herr erledigt sichtlich schweren Herzens alle Formalit�ten \"`#Wirklich schade, dass Ihr geht! Ich danke Euch vielmals f�r die treuen Dienste, die Ihr dem Dorf geleistet habt und werde Euch nie vergessen! Beachtet, dass Eure Entlassung erst mit Beginn des morgigen Tages wirksam wird. F�r heute seid Ihr jedoch beurlaubt.`&\"");
    addnews("".$session['user']['name']."`@ hat das Richteramt niedergelegt. Die Gaunerwelt atmet auf.");
    $session['user']['profession'] = PROF_JUDGE_ENT;

    addhistory('`2Aufgabe des Richteramts');

    addnav("Zur�ck ins Zivilleben","dorfamt.php");

    break;

case 'court':

    addcommentary();

    output("`b`c`2Der Gerichtshof von ".getsetting('townname','Atrahor')."`b`c");
    output("`&Dieser Teil des Geb�udes ist dem Gerichtswesen zugeteilt. Mehrere T�ren sind links und rechts des breiten Ganges zu erkennen und auf gro�en Holzt�felchen steht geschrieben was sich dahinter verbirgt.`nManche T�ren sind f�r dich verschlossen, andere zug�nglich.");
    addnav("�ffentliches");
    addnav("Verhandlungsraum","court.php?op=thecourt");
    addnav("Liste der Richter","court.php?op=listj");
    addnav("Gerichtsschreiber");
    addnav("Zum Gerichtsschreiber","court.php?op=schreiber");
    if ($session['user']['profession']==PROF_JUDGE || $session['user']['profession']==PROF_JUDGE_HEAD
    || su_check(SU_RIGHT_DEBUG) )
    {

        addnav("Arbeit");
        addnav("Verd�chtige Taten","court.php?op=news");
        addnav("Aktuelle F�lle","court.php?op=cases");
        //addnav("Kopfgeldliste","court.php?op=listh");
        addnav("Schwarzes Brett","court.php?op=board");
        addnav("Diskussionsraum","court.php?op=judgesdisc");
        addnav("Archiv");
        addnav("Urteile","court.php?op=archiv");
        addnav("Handbuch f�r Jungrichter","court.php?op=faq");
    }
    addnav("Sonstiges");
    addnav("Zur�ck","dorfamt.php");
    output("`n`n");
    $bool_showform = ($session['user']['profession']==PROF_JUDGE || $session['user']['profession']==PROF_JUDGE_HEAD || $session['user']['profession']==PROF_JUDGE_NEW || $session['user']['superuser']);
    viewcommentary("court","Sprechen:",30,"spricht",false,$bool_showform);

    break;

case 'board':

    output("`&Du stellst dich vor das gro�e Brett und schaust ob eine neue Mitteilung vorliegt.`n");
    //addcommentary();
    // if (($session['user']['profession']==2) || ($session['user']['superuser']>1))
    //{
        output("`tDu kannst eine Notiz hinterlassen oder entfernen.`n`n");

        if ($_GET['board_action'] == "add")
        {

            board_add('richter');

            redirect("court.php?op=board&ret=$_GET[ret]");

        }
        else
        {

            board_view_form('Hinzuf�gen','');

            board_view('richter',2,'','',true,true,true);
        }

        if ($_GET['ret']==1)
        {
            addnav("Zur�ck","court.php?op=judgesdisc");
        }
        else
        {
            addnav("Zur�ck","court.php");
        }

        break;

    case 'listj':
        $admin = ($session['user']['profession'] == PROF_JUDGE_HEAD || su_check(SU_RIGHT_DEBUG)) ? true : false;

        output("<span style='color: #9900FF'>",true);
        $sql = "SELECT name,acctid,loggedin,dragonkills,login,level,profession,activated,laston FROM accounts WHERE profession=21 OR profession=22 OR profession=23 OR profession=25
ORDER BY profession DESC, level DESC";
        $result = db_query($sql) or die(db_error(LINK));
        output("`&Folgende Helden sind Richter:`n`n");
        output("<table border='0' cellpadding='5' cellspacing='2' bgcolor='#999999'><tr class='trhead'><td>Name</td><td>Level</td><td>Funktion</td><td>",true);
        if ($admin)
        {
            output('Aktionen',true);
        }
        output("</td><td>Status</td></tr>",true);
        $lst=0;
        $dks=0;
        for ($i=0; $i<db_num_rows($result); $i++)
        {
            $row = db_fetch_assoc($result);
            $lst+=1;
            $dks+=$row['dragonkills'];
            output("<tr class='".($lst%2?"trlight":"trdark")."'><td><a href=\"mail.php?op=write&to=".rawurlencode($row['login'])."\" target=\"_blank\" onClick=\"".popup("mail.php?op=write&to=".rawurlencode($row['login'])."").";return false;\"><img src='images/newscroll.GIF' width='16' height='16' alt='Mail schreiben' border='0'></a><a href='bio.php?char=".rawurlencode($row['login'])."&ret=".URLEncode($_SERVER['REQUEST_URI'])."'>$row[name]</a></td><td>$row[level]</td><td>",true);
            addnav("","bio.php?char=".rawurlencode($row['login'])."&ret=".URLEncode($_SERVER['REQUEST_URI']));
            if ($row['profession']==PROF_JUDGE)
            {

                output("`#Richter`&</td><td>",true);
                if ($admin)
                {
                    output('<a href="court.php?op=entlassen&id='.$row['acctid'].'">Entlassen</a>',true);
                    addnav("","court.php?op=entlassen&id=".$row['acctid']);
                }
            }
            if ($row['profession']==PROF_JUDGE_HEAD)
            {
                output("`4Oberster Richter`&</td><td>",true);
            }
            if ($row['profession']==PROF_JUDGE_ENT)
            {
                output("`6Entlassung l�uft`&</td><td>",true);
            }
            if ($row['profession']==PROF_JUDGE_NEW)
            {

                output("`@Bittet um Aufnahme`&</td><td>",true);
                if ($admin)
                {
                    output('<a href="court.php?op=aufn&id='.$row['acctid'].'">Aufnehmen</a>`n',true);
                    addnav("","court.php?op=aufn&id=".$row['acctid']);
                    output('<a href="court.php?op=abl&id='.$row['acctid'].'">Ablehnen</a>',true);
                    addnav("","court.php?op=abl&id=".$row['acctid']);
                }

            }
            output("</td><td>",true);
            if (user_get_online(0,$row))
            {
                output("`@online`&",true);
            }
            else
            {
                output("`4offline`&",true);
            }
            output("</td></tr>",true);
        }
        db_free_result($result);
        output("</table>",true);
        output("</span>",true);
        output("<big>`n`@Gemeinsame Drachenkills der Richter : `^$dks`n`n`&<small>",true);

        if ($_GET['ret']==1)
        {
            addnav("Zur�ck","court.php?op=judgesdisc");
        }
        else
        {
            addnav("Zur�ck","court.php");
        }

        break;

    case 'listh':

        output("<span style='color: #9900FF'>",true);
        output("`&Die Kopfgeldliste:`n`n");

        $sql = "SELECT name,acctid,location,bounty,laston,alive,housekey,loggedin,login,level,activated,restatlocation FROM accounts WHERE bounty>0
ORDER BY bounty DESC";
        $result = db_query($sql) or die(db_error(LINK));

        output("<table border='0' cellpadding='4' cellspacing='1' bgcolor='#999999'><tr class='trhead'><td>Kopfgeld</td><td>Level</td><td>Name</td><td>Ort</td><td>Lebt?</td></tr>",true);
        $lst=0;

        for ($i=0; $i<db_num_rows($result); $i++)
        {
            $row = db_fetch_assoc($result);

            $lst+=1;
            output("<tr class='".($lst%2?"trlight":"trdark")."'><td>".($row['bounty'])."</td><td>".($row['level'])."</td><td><a href='bio.php?char=".rawurlencode($row['login'])."&ret=".URLEncode($_SERVER['REQUEST_URI'])."'>$row[name]</a>",true);
            addnav("","bio.php?char=".rawurlencode($row['login'])."&ret=".URLEncode($_SERVER['REQUEST_URI']));
            output("</td><td>",true);

            if ($row['location'] == USER_LOC_FIELDS)
            {
                output(user_get_online(0,$row)?"`@online":"`3Die Felder",true);
            }

            if ($row['location']==USER_LOC_INN)
            {
                output("`3Zimmer in Kneipe`0",true);
            }
            if ($row['location']==USER_LOC_PRISON)
            {
                output("`3Im Kerker`0",true);
            }
            if ($row['location']==USER_LOC_HOUSE)
            {
                $loc=$row['restatlocation'];
                output("Haus Nr. $loc",true);
            }
            output("</td><td>",true);
            if ($row['alive'])
            {
                output("`@lebt`&",true);
            }
            else
            {
                output("`4tot`&",true);
            }
            output("</td></tr>",true);
        }
        if ($_GET['ret']==1)
        {
            addnav("Zur�ck","court.php?op=judgesdisc");
        }
        else
        {
            addnav("Zur�ck","court.php");
        }

        db_free_result($result);
        output("</table>",true);
        output("</span>",true);

        break;

    case 'news':

        $daydiff = ($_GET['daydiff']) ? $_GET['daydiff'] : 0;
        $min = $daydiff-1;

        $sql = "SELECT newstext,newsdate,newsid,accountid FROM crimes WHERE (DATEDIFF(NOW(),newsdate) <= ".$daydiff." AND DATEDIFF(NOW(),newsdate) > ".$min.")
ORDER BY newsid DESC
LIMIT 0,200";

        /** If you are using mysql < ver 4.1.1 try using the following query :
SELECT newstext,newsdate FROM news WHERE
(newstext LIKE '%freigesprochen%' OR newstext LIKE '%verurteilt%')
AND (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(newsdate)) <= 86400
ORDER BY newsid DESC
LIMIT 0,200 **/

        $res = db_query($sql);

        output("`&Die verd�chtigen Taten von ".(($daydiff==0)?"heute":(($daydiff==1)?"gestern":"vor ".$daydiff." Tagen")).":`n");


        for ($i=0; $i<db_num_rows($res); $i++)
        {
            $row = db_fetch_assoc($res);
            output("`c`2-=-`@=-=`2-=-`@=-=`2-=-`@=-=`2-=-`0`c");
            output("$row[newstext]`n");

            output("[ <a href='court.php?op=inspect&accountid=$row[accountid]&daydiff=".$daydiff."'>Ermitteln</a> ]&nbsp;",true);
            addnav("","court.php?op=inspect&accountid=$row[accountid]&daydiff=".$daydiff);


            output("[ <a href='court.php?op=newsdelete&newsid=$row[newsid]&return=".URLEncode($_SERVER['REQUEST_URI'])."'>L�schen</a> ]&nbsp;",true);
            addnav("","court.php?op=newsdelete&newsid=$row[newsid]&return=".URLEncode($_SERVER['REQUEST_URI']));


        }
        if (db_num_rows($res)==0)
        {
            output("`n`1`b`c Keine offenen F�lle an diesem Tag. `c`b`0");
        }

        addnav("Aktualisieren","court.php?op=news");
        addnav("Heute","court.php?op=news");
        addnav("Gestern","court.php?op=news&daydiff=1");
        addnav("Vor 2 Tagen","court.php?op=news&daydiff=2");
        addnav("Vor 3 Tagen","court.php?op=news&daydiff=3");

        if ($_GET['ret']==1)
        {
            addnav("Zur�ck","court.php?op=judgesdisc");
        }
        else
        {
            addnav("Zur�ck","court.php");
        }

        break;

    case 'inspect':

        $sql = "SELECT newstext,newsdate,newsid FROM crimes WHERE accountid=".$_GET['accountid']."
ORDER BY newsid DESC
LIMIT 0,200";
        $res = db_query($sql);

        output("`&Eine genauere Betrachtung bringt folgendes Ergebnis :`n");

        for ($i=0; $i<db_num_rows($res); $i++)
        {
            $row = db_fetch_assoc($res);
            output("`c`2-=-`@=-=`2-=-`@=-=`2-=-`@=-=`2-=-`0`c");
            output("$row[newstext]`n");

        }
        addnav("Anklage erheben","court.php?op=accuse&ret=$_GET[ret]&suspect=".$_GET['accountid']."&daydiff=".$daydiff);
        addnav("Zur�ck","court.php?op=news&ret=$_GET[ret]");

        break;

    case 'caseinfo':

        $sql = "SELECT * FROM cases WHERE accountid=".$_GET['who']."
		ORDER BY newsid DESC
		LIMIT 0,200";
        $res = db_query($sql);
        output("`&Folgende Tatbest�nde werden verhandelt :`n");
        for ($i=0; $i<db_num_rows($res); $i++)
        {
            $row = db_fetch_assoc($res);
            output("`c`2-=-`@=-=`2-=-`@=-=`2-=-`@=-=`2-=-`0`c");
            output("$row[newstext]`n");
        }
        if ($row['court']==0)
        {
            output("`n`nVerfahren wurde er�ffnet von:`n");
            $sql2 = "SELECT name FROM accounts WHERE acctid=$row[judgeid]";
            $res2 = db_query($sql2);
            $row2 = db_fetch_assoc($res2);
            output($row2['name']);
            output("`n`nEin anderer Richter muss das Urteil verk�nden.");

            if (($session['user']['acctid']!=$row['judgeid']) && ($session['user']['acctid']!=$row['accountid']))
            {
                addnav('Mit Prozess');
                addnav('Prozess planen','court.php?op=preprozess&ret='.$_GET[ret].'&who='.$row['accountid']);
                addnav('Prozess f�hren','court.php?op=prozess&ret='.$_GET[ret].'&who='.$row['accountid']);
                addnav('Prozess �ffentlich f�hren','court.php?op=prozess&public=1&ret='.$_GET[ret].'&who='.$row['accountid']);
                addnav('Aktenlage');
                addnav('Verurteilen','court.php?op=guilty&suspect='.$_GET['who'].'&ret='.$_GET[ret]);
                addnav('Freisprechen','court.php?op=notguilty&suspect='.$_GET['who'].'&ret='.$_GET[ret]);
                addnav("Sonstiges");
            }
        }
        elseif ($row['court']==1)
        {
            $persons=array('judge'=>$session[user][login], 'counsel'=>'niemand', 'attestor'=>'niemand', 'public'=>'nein');
            if($row['persons']) $persons=unserialize($row['persons']);
            $form = array('Prozessvorbereitung,title',
                          'judge'=>'Richter:', 
                          'counsel'=>'Verteidigung:', 
                          'attestor'=>'Zeugen:',
                          'public'=>'�ffentlich?');
            output('<form action="court.php?op=preprozess&who='.$row['accountid'].'" method="POST">');
            addnav('','court.php?op=preprozess&who='.$row['accountid']);
            showform($form,$persons);
            output('</form><hr>');
            addcommentary(false);
            viewcommentary('preprozess'.$row['accountid'],"Planen:",30,"sagt");
            addnav("Prozess f�hren","court.php?op=prozess&ret=$_GET[ret]&who=".$row['accountid']."");
            addnav("Prozess �ffentlich f�hren","court.php?op=prozess&public=1&ret=$_GET[ret]&who=".$row['accountid']);
 
        }
        else
        {
            $persons=array('judge'=>'undefiniert', 'counsel'=>'niemand', 'attestor'=>'niemand');
            if($row['persons']) $persons=unserialize($row['persons']);
            output('`n`n`&Es l�uft ein Prozess zu diesem Fall!`n
             `nVorsitz: '.$persons['judge'].'
             `nVerteidigung: '.$persons['counsel'].'
             `nZeugen: '.$persons['attestor'].'
             `n�ffentlich: '.$persons['public'].'`0');
        }
        if ($_GET['proc']==1)
        {
            addnav('Zur�ck','court.php?op=thecourt2&accountid='.$_GET['who']);
        }
        else
        {
            addnav("Zur�ck","court.php?op=cases&ret=$_GET[ret]");
        }

        break;

    case 'preprozess': //setzt angegebenen Prozess in den Planungszustand
        $persons=array('judge'=>$session[user][login], 'counsel'=>'niemand', 'attestor'=>'niemand', 'public'=>'nein');
        if(isset($_POST['judge']))
        {
            $persons['judge']=$_POST['judge'];
            $persons['counsel']=$_POST['counsel'];
            $persons['attestor']=$_POST['attestor'];
            $persons['public']=$_POST['public'];
        }
        db_query('update cases SET court=1, persons="'.addslashes(serialize($persons)).'" WHERE accountid='.$_GET['who']);
        redirect('court.php?op=caseinfo&ret='.$_GET[ret].'&who='.$_GET['who']);
        addnav("Zur�ck","court.php");
        break;

    case 'accuse':

        $sql = "SELECT newstext,newsdate,newsid FROM crimes WHERE accountid=".$_GET['suspect']."
ORDER BY newsid DESC
LIMIT 0,200";
        $res = db_query($sql);

        output("`&Die Verbrechen wurde soeben zur Anklage gebracht.`n");



        for ($i=0; $i<db_num_rows($res); $i++)
        {
            $row = db_fetch_assoc($res);


            addtocases("$row[newstext]",$_GET['accountid']);
            $sql = "DELETE FROM crimes WHERE newsid='$row[newsid]'";
            db_query($sql);

        }

        redirect('court.php?op=news&daydiff='.$_GET['daydiff']);

        addnav("Zur�ck","court.php?op=news&daydiff=$_GET[daydiff]");

        break;

    case 'cases':

        $sql = "SELECT newsid,accountid,judgeid,court,name FROM cases
				LEFT JOIN accounts ON accountid = acctid
				GROUP BY accountid
				ORDER BY court ASC, newsid DESC
				LIMIT 0,200";
        $res = db_query($sql);
        $int_count = db_num_rows($res);
        output('`&Derzeit wird '.$int_count.' Verbrechern der Prozess gemacht :`n`n');

        if ($int_count==0)
        {
            output("`n`1`b`c Zurzeit werden keine F�lle verhandelt. `c`b`0");
        }

        for ($i=0; $i<$int_count; $i++)
        {
            $row = db_fetch_assoc($res);
            output("<a href='court.php?op=caseinfo&ret=$_GET[ret]&who=$row[accountid]'>".($row['name']?$row['name']:$row[accountid].'`4 (User gel�scht)`0')."</a>".($row['judgeid'] == $session['user']['acctid'] ? ' (Von dir angeklagt)':'').($row['court'] ? ' (Prozess '.($row['court']==1 ? 'in Planung':'l�uft').')':'')."`n",true);
            addnav("","court.php?op=caseinfo&ret=$_GET[ret]&who=$row[accountid]");
        }

    	if ($_GET['ret']==1)
        {
            addnav("Zur�ck","court.php?op=judgesdisc");
        }
        else
        {
            addnav("Zur�ck","court.php");
        }


        break;

    case 'guilty':
        Output("Wie lautet dein Strafma�?`n");

        $suspect=$_GET['suspect'];
        $ret=$_GET['ret'];
        $proc=$_GET['proc'];

        output('<form method="POST" action="court.php?op=guilty2&ret='.$ret.'&suspect='.$suspect.'&proc='.$proc.'">',true);
        output('`n<input type="text" name="count" id="count"><input type="hidden" name="count2"> <input type="submit" value="Tage Haft"></form>',true);
        addnav('','court.php?op=guilty2&ret='.$ret.'&suspect='.$suspect.'&proc='.$proc.'');
        output("<script language='javascript'>document.getElementById('count').focus();</script>",true);

        if ($_GET['proc']!=1)
        {
            addnav("Zur�ck","court.php?op=caseinfo&ret=$_GET[ret]&who=$_GET[suspect]");
        }
        else
        {
            addnav("Zur�ck","court.php?op=thecourt2&ret=$_GET[ret]&accountid=$_GET[suspect]");
        }
        break;

    case 'guilty2':

        $count = $_POST['count'];
        //   $count = abs((int)$_GET[count] + (int)$_POST[count]);
        $maxsentence=getsetting("maxsentence",5);
        if ($count>$maxsentence)
        {
            output("Na, wir wollen es mal nicht �bertreiben. Findest du nicht, dass ".$maxsentence." Tage ausreichend w�ren ?");
        }
        else
        {
            $sql2 = "SELECT name,acctid FROM accounts WHERE acctid=$_GET[suspect]";
            $res2 = db_query($sql2);
            $row2 = db_fetch_assoc($res2);
            $sql3 = "SELECT sentence FROM account_extra_info WHERE acctid=$_GET[suspect]";
            $res3 = db_query($sql3);
            $row3 = db_fetch_assoc($res3);

            $count2=$count+$row3['sentence'];
            if ($count2>$maxsentence)
            {
                $count2=$maxsentence;
            }

            output("`&Alles klar! ".$count." Tage Haft. Die Stadtwachen wurden informiert. ".$row2['name']." `&soll nun f�r ".$count2." `&Tage hinter Gitter!");
            addnews("`#Richter ".$session['user']['name']." hat `@".$row2['name']."`& zu ".$count." `&Tagen Kerker verurteilt!");

            $mailtext="`@{$session['user']['name']}
            `& hat dich f�r deine Vergehen zu ".$count." Tagen Kerker verurteilt!`nDiese Strafe wird zu eventuell anderen Strafen hinzugerechnet, jedoch kann deine Haft dadurch nicht l�nger als ".$maxsentence." Tage werden.`nDeine Vergehen im Einzelnen :`n`n";

            $sql3 = "SELECT newstext FROM cases WHERE accountid=".$row2['acctid']."
ORDER BY newsid DESC
LIMIT 0,200";
            $res3 = db_query($sql3);

            for ($j=0; $j<db_num_rows($res3); $j++)
            {
                $row3 = db_fetch_assoc($res3);
                $mailtext=$mailtext.$row3['newstext']."`n";
            }

            systemmail($row2['acctid'],"`$Du wurdest verurteilt!`0",$mailtext);

            $sql = "DELETE FROM cases WHERE accountid='$_GET[suspect]'";
            db_query($sql);
            $sql = "UPDATE account_extra_info SET sentence=$count2 WHERE acctid='$_GET[suspect]'";
            db_query($sql);

            if ($_GET['proc']==1)
            {
                $roomname="court".$_GET['suspect'];
                                
                insertcommentary(1,'/msg`^Das Hohe Gericht verurteilt '.$row2['name'].'`^ zu '.$count.' Tagen Kerker und beendet den Prozess.',$roomname);

            }
        }

        if ($_GET['proc']==1)
        {

//            item_delete(' tpl_id="vorl" AND value1='.$_GET['suspect']);
            db_query('UPDATE items SET owner = "0" WHERE value1='.$_GET['suspect']);

        }
        if ($_GET['ret']==1)
        {
            addnav("Zur�ck","court.php?op=cases");
        }
        else
        {
            addnav("Zur�ck","court.php?op=cases");
        }
        break;

    case 'notguilty':
        output("Du entscheidest zugunsten des Angeklagten.");

        $sql2 = "SELECT name FROM accounts WHERE acctid=$_GET[suspect]";
        $res2 = db_query($sql2);
        $row2 = db_fetch_assoc($res2);

        addnews("`#Richter ".$session['user']['name']." hat `@".$row2['name']."`& freigesprochen!");

        $sql = "DELETE FROM cases WHERE accountid='$_GET[suspect]'";
        db_query($sql);

        if ($_GET['proc']==1)
        {
            $roomname="court".$_GET['suspect'];
            
            insertcommentary(1,'/msg`@Das Hohe Gericht spricht '.$row2['name'].'`@ in allen Anklagepunkten frei und beendet den Prozess.',$roomname);

        }

        if ($_GET['proc']==1)
        {
//            item_delete(' tpl_id="vorl" AND value1='.$_GET['suspect']);
            db_query('UPDATE items SET owner = "0" WHERE value1='.$_GET['suspect']);
        }

        if ($_GET['ret']==1)
        {
            addnav("Zur�ck","court.php?op=cases");
        }
        else
        {
            addnav("Zur�ck","court.php?op=cases");
        }
        break;

    case 'archiv':

        $daydiff = ($_GET['daydiff']) ? $_GET['daydiff'] : 0;
        $min = $daydiff-1;

        $sql = "SELECT newstext,newsdate FROM news WHERE
(newstext LIKE '%freigesprochen%' OR newstext LIKE '%verurteilt%')
AND (DATEDIFF(NOW(),newsdate) <= ".$daydiff." AND DATEDIFF(NOW(),newsdate) > ".$min.")
ORDER BY newsid DESC
LIMIT 0,200";
        $res = db_query($sql);

        output("`&Urteile von ".(($daydiff==0)?"heute":(($daydiff==1)?"gestern":"vor ".$daydiff." Tagen")).":`n");

        while ($n = db_fetch_assoc($res))
        {

            output('`n`n'.$n['newstext']);

        }

        if (db_num_rows($res)==0)
        {
            output("`n`1`b`c Keine Urteile an diesem Tag. `c`b`0");
        }

        addnav("Aktualisieren","court.php?op=archiv");
        addnav("Heute","court.php?op=archiv");
        addnav("Gestern","court.php?op=archiv&daydiff=1");
        addnav("Vor 2 Tagen","court.php?ret=$_GET[ret]&op=archiv&daydiff=2");
        addnav("Vor 3 Tagen","court.php?ret=$_GET[ret]&op=archiv&daydiff=3");
        addnav("Vor 4 Tagen","court.php?ret=$_GET[ret]&op=archiv&daydiff=4");
        addnav("Vor 5 Tagen","court.php?ret=$_GET[ret]&op=archiv&daydiff=5");
        if ($_GET['ret']==1)
        {
            addnav("Zur�ck","court.php?op=judgesdisc");
        }
        else
        {
            addnav("Zur�ck","court.php");
        }
        break;

    case 'faq':
        $maxsentence=getsetting("maxsentence",5);
        output("Handbuch f�r Jungrichter - wie richte ich richtig`n`n");
        output("1. Verd�chtige Taten : Hier werden alle Missetaten aller Spieler aufgelistet.`n");
        output("2. Ermitteln : Zeigt alle Taten eines bestimmten Spielers. Klicke auf Anklage erheben.`n");
        output("3. Aktuelle F�lle : Spieler, gegen die ermittelt wird, werden hier aufgef�hrt.`n");
        output("4. Ein Klick auf den Namen zeigt die Taten des Verd�chtigen.`n");
        output("5. Verurteilen : Man lege die Haftstrafe (bis ".$maxsentence." Tage) fest und schon werden die Wachen aktiv.`n");
        output("`n`nHinweise : Der Richter, der das Verfahren er�ffnet hat darf nicht das Urteil f�llen!`n");
        output("Richtet stets fair und unbestechlich, sonst droht Rauswurf (oder Schlimmeres).`n");
        output("Sollten w�hrend einer Verhandlung weitere Straftaten geschehen k�nnen sie wie in Punkt 1-2 hinzugef�gt werden.`n");
        output("`n`n`n/Anstatt eines Urteils kann auch ein `@Prozess`& begonnen werden.`n");
        output("`&Aber Vorsicht : Ein Prozess bedeutet `^RPG`& und kostet Zeit, eure Zeit udn die der Angeklagten und Zeugen. Ordnet deswegen nicht wegen allt�glichen Dingen jedesmal einen neuen Prozess an.`nBesser ist es auf Anzeigen von Spielern mit einem Prozess zu reagieren.`n");
        output("`&Die `^H�chststrafe`& f�r einen Spieler betr�gt `4".$maxsentence." Tage`&.");
        if ($_GET['ret']==1)
        {
            addnav("Zur�ck","court.php?op=judgesdisc");
        }
        else
        {
            addnav("Zur�ck","court.php");
        }
        break;

    case 'schreiber':
        output("`&In einem viel zu kleinen Raum sitzt ein karges M�nnlein hinter einem kleinen Tisch, der meterhoch mit Unterlagen zugestellt ist. Irgendwo dazwischen steht eine kleine eiserne Kassette auf dem Tisch, die ein paar Goldm�nzen enth�lt. Der Schreiber schaut dich an als du eintrittst.'");
        addnav("Anzeige erstatten","court.php?op=anzeige&ret=$_GET[ret]");
        if ($_GET['ret']==1)
        {
            addnav("Zur�ck","court.php?op=judgesdisc");
        }
        else
        {
            addnav("Zur�ck","court.php");
        }
        break;

    case 'anzeige':
        output("`&Der Schreiberling schaut dich an. \"`#Na, wer hat Euch denn Schlimmes angetan?`&\" fragt er.`n`n");

        if ($_GET['who']=="")
        {
            addnav("�h.. niemand!","court.php?op=schreiber&ret=$_GET[ret]");
            if ($_GET['subop']!="search")
            {
                output("<form action='court.php?op=anzeige&ret=$_GET[ret]&subop=search' method='POST'><input name='name'><input type='submit' class='button' value='Suchen'></form>",true);
                addnav("","court.php?op=anzeige&ret=$_GET[ret]&subop=search");
            }
            else
            {
                addnav("Neue Suche","court.php?op=anzeige&ret=$_GET[ret]");
                $search = str_create_search_string($_POST['name']);
                $sql = "SELECT name,alive,location,sex,level,reputation,laston,loggedin,login FROM accounts WHERE (locked=0 AND name LIKE '$search') ORDER BY level DESC";
                $result = db_query($sql) or die(db_error(LINK));
                $max = db_num_rows($result);
                if ($max > 50)
                {
                    output("`n`n\"`#Geht es vielleicht ein bisschen genauer ?`&`n");
                    $max = 50;
                }
                output("<table border=0 cellpadding=0><tr><td>Name</td><td>Level</td></tr>",true);
                for ($i=0; $i<$max; $i++)
                {
                    $row = db_fetch_assoc($result);
                    output("<tr><td><a href='court.php?op=anzeige&ret=$_GET[ret]&who=".rawurlencode($row['login'])."'>$row[name]</a></td><td>$row[level]</td></tr>",true);
                    addnav("","court.php?op=anzeige&ret=$_GET[ret]&who=".rawurlencode($row['login']));
                }
                output("</table>",true);
            }
        }
        else
        {

            $sql = "SELECT acctid,login,name FROM accounts WHERE login=\"$_GET[who]\"";
            $result = db_query($sql) or die(db_error(LINK));
            if (db_num_rows($result)>0)
            {
                $row = db_fetch_assoc($result);
                $costs=$session['user']['level']*100;

                output("`&Der Schreiber nickt. \"`&Ja, der Name ".($row['name'])." `& ist mir ein Begriff... Die Geb�hren f�r eine Anzeige liegen f�r Euch bei `^".$costs." Gold.`#\"`&`n`n");
                if ($costs>$session['user']['gold'])
                {
                    output("`&`n`qDu schaust in deinen Beutel und stellst fest da� du nicht genug Gold dabei hast.`n`QUntert�nigst entschuldigst du dich beim Gerichtsdiener und verl�sst das Geb�ude.`n`n");
                    addnav("Tut mir leid!","village.php");
                }
                else
                {
                    output("`n`&Wie lautet deine Anzeige? Bitte beschreibe den Tathergang ausf�hrlich!");
                    output("<form action='court.php?op=anzeige2&ret=$_GET[ret]&who=".rawurlencode($row['login'])."' method='POST'><textarea name='text' id='text' class='input' cols='50' rows='10'></textarea><br><input type='submit' class='button' value='diktieren'></form>",true);
                    output("<script language='JavaScript'>document.getElementById('text').focus();</script>",true);
                    addnav("","court.php?op=anzeige2&ret=$_GET[ret]&who=".rawurlencode($row['login'])."");
                    addnav("Abbrechen","court.php?ret=$_GET[ret]&op=schreiber");
                }
            }
            else
            {
                output("\"`#Ich kenne niemanden mit diesem Namen.`&\"");
            }
        }

        break;

    case 'anzeige2':

        $text = $_POST['text'];

        $sql = "SELECT acctid,login,name FROM accounts WHERE login=\"$_GET[who]\"";
        $result = db_query($sql) or die(db_error(LINK));
        $row = db_fetch_assoc($result);

        output("`&Die Anzeige lautet:`n`n");
        $pretext="`&Anzeige von ".$session['user']['name']." `&gegen ".$row['name']." `&: ";
        $text2=$pretext.$text;
        output($text2);
        output("`n`n`&Zufrieden?");
        addnav("Sehr gut!","court.php?op=anzeige3&ret=$_GET[ret]&who=$row[acctid]&text=".rawurlencode($text)."");
        addnav("Nein, nochmal!","court.php?op=anzeige&ret=$_GET[ret]&who=".rawurlencode($row['login'])."");

        break;

    case 'anzeige3':

        $sql = "SELECT acctid,login,name FROM accounts WHERE acctid=\"$_GET[who]\"";
        $result = db_query($sql) or die(db_error(LINK));
        $row = db_fetch_assoc($result);

        $text = $_GET['text'];
        $pretext="`&Anzeige von ".$session['user']['name']." `&gegen ".$row['name']."`&: ";
        $text=$pretext.$text;
        output("`&Du hast zu Protokoll gegeben:`n");
        output($text);

        $buy=$session['user']['level']*100;
        if ($buy>$session['user']['gold'])
        {
            output("`&`n`nWas glaubst du wo du hier bist? Die M�hlen der Justiz mahlen sicherlich nicht umsonst. Also besorg dir ein wenig Kleingeld bevor du wiederkommst.`nDer Gerichtsdiener bef�rdert dich mit einem Tritt nach draussen.");
            addnav("Autsch!","village.php");
        }
        else
        {
            output('`&`n`n');
            $text=ereg_replace("\ {2,}"," ",$text);
            if(strlen($text)>150)
            {
                output('`&Der Schreiberling sieht dich an: `#"Wenn das so ist brauchst Du nur die halbe Geb�hr bezahlen."`&`n');
                $buy*=.5;
            }
            output("Du bezahlst deine $buy Goldm�nzen und sie versinken leise klirrend in der eisernen Kassette auf des Schreiberlings Tisch.`n");
            $session['user']['gold']-=$buy;

            $sql = "INSERT INTO crimes(newstext,newsdate,accountid) VALUES ('".addslashes($text)."',NOW(),".$row['acctid'].")";
            db_query($sql) or die(db_error($link));

            if ($_GET['ret']==1)
            {
                addnav("Hehe...","court.php?op=judgesdisc");
            }
            else
            {
                addnav("Hehe...","court.php");
            }
        }


        break;

    case 'thecourt':
//Richter bekommen alle Prozesse
        if ($session['user']['profession']==PROF_JUDGE || $session['user']['profession']==PROF_JUDGE_HEAD || su_check(SU_RIGHT_DEBUG) )
        {
            $res= item_list_get(' i.tpl_id="vorl" ',' GROUP BY value1 ORDER BY value1 DESC LIMIT 0,200 ');
        }
        else
        {
          if(item_get(' i.tpl_id="vorl" AND i.owner='.$session['user']['acctid'],false))
//auf eigene Vorladung pr�fen
          {
            $res= item_list_get(' i.tpl_id="vorl" AND (i.owner="'.$session['user']['acctid'].'" OR value2="1") GROUP BY value1 ORDER BY value1 DESC LIMIT 0,200 ');
          }
          else //Abfrage ob public
          {
            $res= item_list_get(' i.tpl_id="vorl" AND value2="1" GROUP BY value1 ORDER BY value1 DESC LIMIT 0,200 ');
          }
        }
        if (db_num_rows($res))
        {
            output("`&Zu welchem Prozess m�chtest du gehen ?`n`n");
			$int_count = db_num_rows($res);
            for ($i=0; $i<$int_count; $i++)
            {
                $row = db_fetch_assoc($res);
                $sql2 = "SELECT name FROM accounts WHERE acctid=$row[value1] ORDER BY name DESC";
                $res2 = db_query($sql2);
                $row2 = db_fetch_assoc($res2);
                output(create_lnk('&raquo; `&'.strip_appoencode($row2['name'],3),"court.php?op=entrymsg&ret=$_GET[ret]&accountid=$row[value1]").'`n',true);
            }

            addnav("Zur�ck","court.php");

        }
        else
        {

            if ($session['user']['profession']==PROF_JUDGE || $session['user']['profession']==PROF_JUDGE_HEAD || su_check(SU_RIGHT_DEBUG) )
            {
                output("`&Derzeit werden hier keine F�lle verhandelt und du bist gewiss nicht gekommen um den Boden zu schrubben...`n`n");
                if ($_GET['ret']==1)
                {
                    addnav("Zur�ck","court.php?op=judgesdisc");
                }
                else
                {
                    addnav("Zur�ck","court.php");
                }
            }
            else
            {
                output("`&Du hast keine Vorladung und die Verhandlungen sind nicht �ffentlich.`nWas willst du also hier ?`n`n");
                addnav("Zur�ck","court.php");
            }
        }
        break;

    case 'thecourt2':
        output("`&Du �ffnest die schwere Eichent�re und betrittst den Gerichtssaal. St�hle und B�nke sind im hinteren Teil des gro�en Raumen ordentlich aufgestellt worden, eine Absperrung trennt diesen Teil von der Richterkanzel. T�ren im hinteren Teil des Raumes f�hren zum Archiv und zum Besprechungsraum. Du stellst fest, dass dieser Raum sehr gepflegt und der Boden gut poliert ist.`n`n");

        $roomname="court".$_GET['accountid'];

        $accountid=substr($roomname,5);

        addcommentary();
//Verhandlungsraum 
        $bool_showform = ($session['user']['profession']==PROF_JUDGE || $session['user']['profession']==PROF_JUDGE_HEAD || $session['user']['profession']==PROF_JUDGE_NEW || item_get(' i.tpl_id="vorl" AND i.owner='.$session['user']['acctid'].' AND value1='.$_GET['accountid'],false) || $session['user']['superuser']);
        viewcommentary($roomname,"Sagen:",30,"sagt",false,$bool_showform);

        //(wer || wer darf?) && Vorladung Besitzer= Angeklagter?
        if (($session['user']['profession']==PROF_JUDGE || $session['user']['profession']==PROF_JUDGE_HEAD || su_check(SU_RIGHT_DEBUG)) && item_get(' i.tpl_id="vorl" AND i.owner= '.$accountid,false) )
        {
            addnav("Zeugen vorladen");
            addnav("Vorladen","court.php?op=witn&ret=$_GET[ret]&accountid=$_GET[accountid]");
            addnav("Anklageschrift");
            addnav("Lesen","court.php?op=caseinfo&ret=$_GET[ret]&who=$_GET[accountid]&proc=1");
            if ($session['user']['acctid']!=$_GET['accountid'])
            {
                addnav("Prozess beenden");
                addnav("Schuldig","court.php?op=guilty&ret=$_GET[ret]&proc=1&suspect=$accountid");
                addnav("Nicht schuldig","court.php?op=notguilty&ret=$_GET[ret]&proc=1&suspect=$accountid");
            }
            addnav("Prozesspause");
            addnav("Saal verlassen","court.php?op=leavemsg&ret=$_GET[ret]&accountid=$_GET[accountid]");
        }
        else
        {
            addnav("Raus hier","court.php?op=leavemsg&ret=$_GET[ret]&accountid=$_GET[accountid]");
        }
        break;

    case 'judgesdisc':
        output("`&Hier im kleinen Hinterzimmer des gro�es Verhandlungsraumes kannst du dich mit den anderen Richtern treffen. Ungest�rt von Plebs und P�bel k�nnt ihr hier wichtige F�lle diskutieren oder einfach nur mal kurz ausspannen.`nEin gro�er runder Tisch in der Mitte des Raumes bietet allen Richtern Platz und sieht sehr gem�tlich aus.`n`n");
        if ($session['user']['profession']==PROF_JUDGE || $session['user']['profession']==PROF_JUDGE_HEAD || su_check(SU_RIGHT_DEBUG) )
        {
            addcommentary();
        }
        viewcommentary("judges","Deine Meinung sagen:",30,"meint");

        addnav("�ffentliches");
        addnav("Verhandlungsraum","court.php?op=thecourt&ret=1");
        addnav("Liste der Richter","court.php?op=listj&ret=1");
        addnav("Gerichtsschreiber");
        addnav("Zum Gerichtsschreiber","court.php?op=schreiber&ret=1");
        addnav("Arbeit");
        addnav("Verd�chtige Taten","court.php?op=news&ret=1");
        addnav("Aktuelle F�lle","court.php?op=cases&ret=1");
        //addnav("Kopfgeldliste","court.php?op=listh&ret=1");
        addnav("Schwarzes Brett","court.php?op=board&ret=1");
        addnav("Archiv");
        addnav("Urteile","court.php?op=archiv&ret=1");
        addnav("Handbuch f�r Jungrichter","court.php?op=faq&ret=1");
        addnav("Sonstiges");
        addnav("Zur�ck","court.php");
        break;

    case 'prozess': //Prozess er�ffnen

        $sql = "SELECT name FROM accounts WHERE acctid=$_GET[who]";
        $res = db_query($sql);
        $row = db_fetch_assoc($res);

        $item['tpl_value1'] = $_GET['who'];
        $item['tpl_value2'] = $_GET['public'];

        $item['tpl_description'] = '`&Du wirst zum Gericht befohlen! Es betrifft das Verfahren gegen `4DICH!`& Solltest du dem nicht nachkommen, droht dir eine harte Strafe.';

        item_add($_GET['who'], 'vorl', $item );

        systemmail($_GET['who'],"`4Vorladung!`2",$item['tpl_description']);

        output($row['name']."`& hat eine Vorladung erhalten und wird sich (hoffentlich) bald im Gerichtssaal einfinden.`n");

        $sql = "UPDATE cases SET court=2 WHERE accountid=$_GET[who]";
        db_query($sql) or die(sql_error($sql));
        $sql = "INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'court".$_GET[who]."',".$session[user][acctid].",'/msg ---Prozess er�ffnet am ".getgamedate()." von Richter ".$session['user']['login']."---')";
        db_query($sql) or die(db_error(LINK));

        if ($_GET['ret']==1)
        {
            addnav("Zur�ck","court.php?op=judgesdisc");
        }
        else
        {
            addnav("Zur�ck","court.php");
        }

        break;

    case 'witn':
        output("`&Wen m�chtest du zu diesem Prozess vorladen?`n`n");

        if ($_GET['who']=="")
        {
            addnav("Niemanden!","court.php?op=thecourt2&accountid=$_GET[accountid]");
            if ($_GET['subop']!="search")
            {
                output("<form action='court.php?op=witn&ret=$_GET[ret]&accountid=$_GET[accountid]&subop=search' method='POST'><input name='name'><input type='submit' class='button' value='Suchen'></form>",true);
                addnav("","court.php?op=witn&ret=$_GET[ret]&accountid=$_GET[accountid]&subop=search");
            }
            else
            {
                addnav("Neue Suche","court.php?op=witn&ret=$_GET[ret]&accountid=$_GET[accountid]");
                $search = str_create_search_string($_POST['name']);
                $sql = "SELECT name,alive,location,sex,level,reputation,laston,loggedin,login FROM accounts WHERE (locked=0 AND name LIKE '$search') ORDER BY IF(login='".addslashes(stripslashes($_POST['name']))."',1,0) DESC, level DESC";
                $result = db_query($sql) or die(db_error(LINK));
                $max = db_num_rows($result);
                if ($max > 50)
                {
                    output("`n`n`&Zu viele Suchergebnisse`&`n");
                    $max = 50;
                }
                output("<table border=0 cellpadding=0><tr><td>Name</td><td>Level</td></tr>",true);
                for ($i=0; $i<$max; $i++)
                {
                    $row = db_fetch_assoc($result);
                    output("<tr><td><a href='court.php?op=witn&ret=$_GET[ret]&accountid=$_GET[accountid]&who=".rawurlencode($row['login'])."'>$row[name]</a></td><td>$row[level]</td></tr>",true);
                    addnav("","court.php?op=witn&ret=$_GET[ret]&accountid=$_GET[accountid]&who=".rawurlencode($row['login']));
                }
                output("</table>",true);
            }
        }
        else
        {

            $sql = "SELECT acctid,login,name FROM accounts WHERE login=\"$_GET[who]\"";
            $result = db_query($sql) or die(db_error(LINK));
            if (db_num_rows($result)>0)
            {
                $row = db_fetch_assoc($result);


                output($row['name']." `& als Zeugen vorladen ?`n`n");

                addnav("Ja","court.php?op=witn2&ret=$_GET[ret]&accountid=$_GET[accountid]&who=$row[acctid]");
                addnav("Nein","court.php?op=thecourt2&ret=$_GET[ret]&accountid=$_GET[accountid]");
            }
            else
            {
                output("\"`#Name wurde nicht gefunden.`&\"");
            }
        }

        break;

    case 'witn2':

        $sql = "SELECT name FROM accounts WHERE acctid=$_GET[accountid]";
        $res = db_query($sql);
        $row = db_fetch_assoc($res);

        $sql2 = "SELECT name FROM accounts WHERE acctid=$_GET[who]";
        $res2 = db_query($sql2);
        $row2 = db_fetch_assoc($res2);

        $item['tpl_value1'] = $_GET['accountid'];
        $item['tpl_description'] = '`&Du wirst zum Gericht befohlen! Es betrifft das Verfahren gegen '.$row['name'].'`&. Solltest du dem nicht nachkommen, droht dir eine harte Strafe.';

        item_add($_GET['who'], 'vorl', $item );

        systemmail($_GET['who'],"`4Vorladung!`2",$item['tpl_description']);

        output($row2['name']."`& hat eine Vorladung erhalten und wird sich (hoffentlich) bald im Gerichtssaal einfinden.`n");

        $roomname="court".$_GET['accountid'];
        
        insertcommentary(1,'/msg `&'.$row2['name'].'`& wird vom Hohen Gericht als Zeuge vorgeladen!',$roomname);

        addnav("Zur�ck","court.php?op=thecourt2&ret=$_GET[ret]&accountid=$_GET[accountid]");
        break;

    case 'entrymsg':
        $roomname="court".$_GET['accountid'];
//        $sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'$roomname',".$session['user']['acctid'].",'/me `&betritt den Gerichtssaal.`V')";
//        db_query($sql) or die(db_error(LINK));
        redirect("court.php?op=thecourt2&ret=$_GET[ret]&accountid=$_GET[accountid]");
        break;

    case 'leavemsg':
        $roomname="court".$_GET['accountid'];
//        $sql="INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'$roomname',".$session['user']['acctid'].",'/me `&verl�sst den Gerichtssaal.`V')";
//        db_query($sql) or die(db_error(LINK));

        if ($_GET['ret']==1)
        {
            redirect("court.php?op=judgesdisc");
        }
        else
        {
            redirect("court.php");
        }
        break;

        default:
        break;

}

page_footer();
?>
