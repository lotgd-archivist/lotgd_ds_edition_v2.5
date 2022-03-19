<?
/**
 * nerwen.php: A village addition by Spider
 * inspiration from the original fortune teller
 * author unkown
 * Nerwen will tell your fortune, and depending
 * on how much money you offer her, you have a
 * chance at a "better" fortune.
 * 
 * @author Spider, translation and modification by Salator and talion
 * @version DS-E V/2
*/

require_once 'common.php';
page_header('Nerwen\'s Zelt');
output('`c`bNerwen\'s Zelt`b`c');

if ($_GET['op']=='')
{
    output('Nerwen Lossëhelin schaut auf als du eintrittst. Elfen sind bekannt für ihre Schönheit, doch auch nach elfischen Maßstäben ist Nerwen hübsch. Ihre tiefblauen Augen blicken geradewegs in deine und du hast das Gefühl, sie könnte dein Innerstes sehen.`n`n
`3"Ah, lieber '.$session['user']['name'].'`3, wir haben uns ja lange nicht gesehen. Gut schaust Du aus! Doch sag, hast du etwas von meinem Bruder Golradir gehört? Die Bauarbeiten an seinem Haus sind fast fertig und seine kleine Zuflucht wird öffnen, sobald er zurück ist." `0Nerwen hält einen Moment inne, noch immer in deine Augen blickend. `3"Nein, ich sehe du weißt auch nichts über ihn. Aber ich fühle seine baldige Rückkehr."`n`n"Nun gut, genug Worte über meinen verirrten Bruder. Ich nehme an, Du bist zu mir gekommen, um etwas mehr über Dich zu erfahren? Ich kann Dir ein Wenig aus Deiner Zukunft enthüllen wenn du mir ein Wenig von Deinem Gold gibst."');
    addnav('Z?Frage nach deiner Zukunft','nerwen.php?op=future');
}
else if ($_GET['op']=='future')
{
    output('Begierig, etwas über deine Zukunft zu erfahren, fragst du Nerwen wieviel "ein Wenig" ist.`n`n`3"Mein Kind, ein Wenig ist so viel wie Du mir geben willst. Die Frage ist, wie freigiebig bist Du heute?"`0`n`n');
    output('Wieviel Gold willst du Nerwen geben?`n');
    output("<form action='nerwen.php?op=future2' method='POST'><input id='input' name='amount' width=5 accesskey='h'> <input type='submit' class='button' value='weggeben'></form>",true);
    output("<script language='javascript'>document.getElementById('input').focus();</script>",true);
    addnav('','nerwen.php?op=future2');
}
else if ($_GET['op']=='future2')
{
    $offer=abs((int)$_POST['amount']);
    if ($offer==0)
    {
        output('Nerwen schaut dich streng an.`n`n`3"Du willst mich zum Narren halten, oder vielleicht denkst Du, mit mir kann man Scherze machen. Verschwinde aus meinem Zelt und komm wieder, wenn du Manieren gelernt hast!"');
    }
    else if ($offer<100)
    {
        output('Nerwen schaut auf den kläglichen Goldhaufen, den du ihr anbietest und schüttelt den Kopf.`n`n`3"Tut mir leid mein Kind, aber das ist einfach nicht genug. Dies ist meine Art, den Lebensunterhalt zu verdienen, und für so wenig kann ich das nicht machen."');
    }
    else if ($offer>$session['user']['gold'])
    {
        output('Nerwen schaut dich streng an.`n`n`3"Ich denke, Du brauchst etwas Nachhilfe in der Zahlenlehre, bevor du dich mit der Zukunft beschäftigst. Wie kannst du mir etwas geben was Du nicht hast?"');
        addnav('nochmal versuchen','nerwen.php?op=future');
    }
    else
    {
        $max=min(ceil($offer/100),15);
        $session['user']['gold']-=$offer;
        output('Nerwen nimmt dein Gold, lächelt und schaut dir tief in die Augen.`n`n');
        $fortune = e_rand(1,$max);
        debuglog('gab '.$offer.' an Nerwen, bekam Ereignis '.$fortune);
        switch ($fortune)
        {
        case 1:
            output('`3"Heute sieht es gar nicht gut aus für Dich, tut mir schrecklich leid."');
            $session['user']['hitpoints']=1;
            $session['user']['gold']-=100;
            $session['user']['charm']-=1;
            $session['user']['gems']-=1;
            if ($session['user']['gold'] < 0)
            {
                $session['user']['gold'] = 0;
            }
            if ($session['user']['gems'] < 0)
            {
                $session['user']['gems'] = 0;
            }
            break;
        case 2:
            output('`3"Mein Kind, Du wirst heute zeitig schlafen gehen."');
            $session['user']['turns']-=2;
            if ($session['user']['turns'] < 0)
            {
                $session['user']['turns'] = 0;
            }
            break;
        case 3:
        case 11:
            output('`3"Dein Tag wird sich in Liebesdingen großartig entwickeln."');
            $session['user']['charm']++;
            break;
        case 4:
            output('`3"Ich fürchte, Du wirst heute etwas verlieren. Ich kann aber nicht sehen, was es ist."');
            $session['user']['goldinbank']-=500;
            if ($session['user']['goldinbank']<0)
            {
                debuglog('Kredit durch 500 Bank-Gold Abzug beim Wahrsager');
            }
            break;
        case 5:
            $sql='SELECT houseid, gems FROM houses WHERE owner='.$session['user']['acctid'];
            $result=db_query($sql) or die(db_error(LINK));
            $row=db_fetch_assoc($result);
            if ($row['gems']>0)
            {
                $row['gems']--;
                $sql = 'UPDATE houses SET gems='.$row['gems'].' WHERE houseid='.$row['houseid'];
                db_query($sql);
                                
                insertcommentary(1,'/msg Eine Elster landet am offenen Fenster, fliegt zur Schatztruhe und schnappt sich einen Edelstein.','house-'.$row['houseid']);
                
                output('`3"Ich fürchte, Dein Haus wird heute bestohlen."');
            }
            else
            {
                output('`3"Ich fürchte, man wird dich heute auf hinterhältige Weise umbringen."');
            }
            break;
        case 7:
            output('`3"Du wirst im Laufe des Tages eine freudige Überraschung erleben."');
            $session['user']['goldinbank']+=1000;
            break;
        case 8:
        case 20:
            output('`3"Frische Kraft durchströmt Dich, Dein Tag wird lang und produktiv."');
            $session['user']['turns']+=2;
            break;
        case 9:
            $sql='SELECT houseid, gems FROM houses WHERE owner='.$session['user']['acctid'];
            $result=db_query($sql) or die(db_error(LINK));
            $row=db_fetch_assoc($result);
            if ($row['houseid'])
            {
                $row['gems']++;
                $sql = 'UPDATE houses SET gems='.$row['gems'].' WHERE houseid='.$row['houseid'];
                db_query($sql);
                
                insertcommentary(1,'/msg Ein Edelstein fällt vom Himmel und kullert direkt vor die Schatztruhe.','house-'.$row['houseid']);
                
                output('`3"Du wirst zuhause eine freudige Überraschung erleben."');
            }
            else
            {
                output('`3"Ich sehe, Du wirst heute etwas wertvolles finden"');
                item_add($session['user']['acctid'],'glasfigur');
            }
            break;
        case 10:
            output('`3"Ich sehe heute neue Kräfte in Dir erwachen."');
            $session['user']['hitpoints']+=200;
            break;
        case 12:
            output('`3"Ich sehe, Du fühlst Dich heute nicht so gut. Hoffentlich geht es Dir bald besser."');
            $session['user']['hitpoints']*=.5;
            if ($session['user']['hitpoints'] < 0)
            {
                $session['user']['hitpoints'] = 1;
            }
            break;
        case 13:
            output('`3"Ich empfehle Dir, Dich heute von der Schenke fernzuhalten. In Deinem Zustand bist Du dort nicht sehr willkommen."');
            $session['user']['drunkenness']=80;
            break;
        case 14:
            output('`3"Du bist heute gesegnet, genieße es solange es anhält."');
            $session['user']['hitpoints']+=50;
            break;
        case 15:
            output('`3"Dein Tag sieht sehr vielversprechend aus. Nun geh\' und mach\' das Beste daraus."');
            $session['user']['hitpoints']+=10;
            $session['user']['gold']+=100;
            $session['user']['charm']+=1;
            $session['user']['gems']+=1;
            break;
            default:
            output('`3"Ich fürchte, Deine Zukunft ist heute etwas vernebelt, mehr kann ich nicht sehen."');
        }
    }
}
addnav('M?Zurück zum Markt','market.php');

page_footer();
?>