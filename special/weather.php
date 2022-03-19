<?
// idea of gargamel @ www.rabenthal.de
// Anregungen f�r die neuen Wetter von Gloin

if (!isset($session))
{
    exit();
}

if ($_GET['op']=="")
{
    $w = get_weather();
    output("`nSag mal, ".$session['user']['name'].", hast Du eigentlich heute schon zum
Himmel geschaut? Das Wetter ist \"`^".$w['name']."`0\"!!`n`0");
    
    switch ($settings['weather'])
    {
    case WEATHER_COLD :
        {
            output("\"K�nnte besser sein\" denkst du dir und gehst weiter.`0");
            break;
        }
    case WEATHER_WARM :
        {
            output("Du bist hier ganz in der N�he von einem kleinen Waldsee. Und so
wundert es nicht, dass bei diesem Wetter eine wahre M�ckenplage herrscht.`n`0");
            $case = e_rand(1,2);
            switch ($case )
            {
            case 1:
                output("Du musst die Plagegeister st�ndig wegscheuchen, was dich etwas
Aufmerksamkeit im n�chsten Kampf kostet. `n`^Deine Verteidigung wird schw�cher.`n`0");
                $session['bufflist']['muecken'] = array("name"=>"`4M�cken",
                "rounds"=>10,
                "wearoff"=>"Die M�cken haben sich verzogen.",
                "defmod"=>0.92,
                "atkmod"=>1,
                "roundmsg"=>"Die M�cken behindern Dich.",
                "activate"=>"defense");
                break;
                
            case 2:
                output("Bei dem st�ndigen Geschwirre kannst du dich kaum auf den n�chsten
Kampf konzentrieren. `n`^Deine Angriffsf�higkeit ist daher eingeschr�nkt.`0");
                $session['bufflist']['muecken'] = array("name"=>"`4M�cken",
                "rounds"=>10,
                "wearoff"=>"Die M�cken haben sich verzogen.",
                "defmod"=>1,
                "atkmod"=>0.92,
                "roundmsg"=>"Die M�cken behindern Dich.",
                "activate"=>"offense");
                break;
            }
            break;
        }
    case WEATHER_RAINY :
        {
            if ($session['user']['specialty'] == 1 )
            {
                output("Als du nun bei dem miesen Wetter durch den Wald stapfst, wird
deine Stimmung nochmal schlechter.`n
Deinen F�higkeiten tut dies jedoch gut und `^du steigst eine Stufe auf.`0");
                increment_specialty();
            }
            else
            {
                output("Als nun ein weiterer Schauer niedergeht, ziehst du dir erstmal
schnell deinen Regenschutz �ber.`n
`^Leider behindert er dich etwas beim K�mpfen...`0");
                $session['bufflist']['regenjacke'] = array("name"=>"`4Regenschutz",
                "rounds"=>25,
                "wearoff"=>"Gut! Der Regenschauer ist vorbei.",
                "defmod"=>0.96,
                "atkmod"=>0.92,
                "roundmsg"=>"Der Regenschutz behindert dich.",
                "activate"=>"defense");
            }
            break;
        }
    case WEATHER_FOGGY :
        {
            if ($session['user']['specialty'] == 3 )
            {
                output("Das kommt dir mit deinen Diebesf�higkeiten nat�rlich entgegen.
`^Du erh�ltst einen zus�tzlichen Waldkampf!`0");
                $session['user']['turns']++;
            }
            else
            {
                output("Da ist es noch schwieriger, sich im Wald zurechtzufinden. Und
prompt nimmst du einen falschen Abzweig vom Waldweg.`n
`^Du verlierst einen Waldkampf.`0");
                $session['user']['turns']--;
            }
            break;
        }
    case WEATHER_COLDCLEAR :
        {
            output("Meinst Du wirklich, ".$session['user']['armor']." ist da die richtige
Kleidung?`n`0");
            $case = e_rand(1,2);
            switch ($case )
            {
            case 1:
                output("`^Du handelst Dir einen Schnupfen ein und verlierst ein paar
Lebenspunkte.`0");
                $session['user']['hitpoints']=round($session['user']['hitpoints']*0.95);
                break;
                
            case 2:
                output("Du sammelst etwas Reisig im Unterholz und w�rmst dich erstmal
an einem kleinen Feuerchen.`n
`^Die Pause kostet dich einen Waldkampf.`0");
                $session['user']['turns']--;
            }
            break;
        }
    case WEATHER_HOT :
        {
            output("Im Dorf hast du es sogar als schw�l empfunden und geniesst daher
die Zeit im schattigen, k�hlen Wald.`n
`^Du bekommst einen Waldkampf.`0");
            $session['user']['turns']++;
            break;
        }
    case WEATHER_WINDY :
        {
            output("Die gro�en alten B�ume hier biegen sich unter der Wucht einzelner
Windb�en. Ein gro�er Ast kann dem Wind nicht mehr standhalten und kracht zu
Boden.`0");
            $case = e_rand(1,2);
            switch ($case )
            {
            case 1:
                output("Du hast mehr Gl�ck als Verstand! Der m�chtige Ast schl�gt nur
wenige Schritte von dir entfernt auf. Dir ist nichts passiert.`n
`^Etwas eingesch�chtert gehst du weiter.`0");
                break;
                
            case 2:
                output("Zum Gl�ck schl�gt der Ast neben dir ein, aber ein paar kleinere
�ste treffen dich doch. `^Du b�sst Lebenspunkte ein!`0");
                $hp = e_rand(1,$session['user']['hitpoints']);
                $session['user']['hitpoints']=$hp;
                break;
            }
            break;
        }
    case WEATHER_TSTORM :
        {
            if ($session['user']['specialty'] == 2 )
            {
                output("Um dich herum zucken die Blitze durch den verdunkelten Himmel.
Genau richtig, um die magischen Kr�fte aufzuladen.`n
`^Du kannst Deine F�higkeiten wieder einsetzen.`0");
                //-> f�higkeiten aktivieren
                restore_specialty();
            }
            else
            {
                output("Gerade im Wald ist das nicht ungef�hrlich!`n`n
Um dich vor Blitzschlag zu sch�tzen stellst du dich in einer H�hle
unter.`n
`^Du verlierst einen Waldkampf.`0");
                $session['user']['turns']--;
            }
            break;
        }
        //neue Wetter:
    case WEATHER_SNOWRAIN :
        {
            output("Du schaust also zum Himmel, was in diesem Moment keine gute Idee war.
Prompt rutscht du aus und f�llst auf die Nase.");
            if ($session['user']['hitpoints']>20 )
            {
                output("`n`^Du verlierst 5 Lebenspunkte.`0");
                $session['user']['hitpoints']-=5;
            }
            break;
        }
    case WEATHER_SNOW :
        {
            output("Du schaust also zum Himmel - vielleicht einen Moment zu lange. ");
            if ($session['user']['hashorse']>0 )
            {
                output("Als du dich umsiehst ist dein ".$playermount['mountname']." verschwunden.
Und wo kommt der Schneeberg neben dir her?`n
`^Du verlierst einen Waldkampf w�hrend du dein Tier wieder ausgr�bst.`0");
                $session['user']['turns']--;
            }
            else
            {
                output("Als du weitergehen willst merkst du, da� du bis zur H�fte eingeschneit bist.");
            }
            break;
        }
    case WEATHER_STORM :
        {
            //Code aus specialty.lib.php modifiziert, geht Anwendung wegnehmen vielleicht auch einfacher?
            $int_specid = $session['user']['specialty'];
            $sql = 'SELECT * FROM specialty WHERE specid='.$int_specid;
            $row = db_fetch_assoc(db_query($sql));
            
            $skillnames = array($row['specid']=>$row['specname']);
            $skills = array($row['specid']=>$row['usename']);
            $skillpoints = array($row['specid']=>$row['usename']."uses");
            
            output("Und es kommt wie es kommen mu�, ein starker Windsto� rei�t dir ".$session['user']['armor']."`0 vom Leib. ");
            if ($session['user']['specialtyuses'][$skillpoints[$int_specid]]>0)
            {
                output("Geistesgegenw�rtig besinnst du dich auf deine F�higkeiten in ".$skillnames[$int_specid]."`0 und zauberst deine R�stung zur�ck.
`n`^Du verlierst eine Anwendung in ".$skillnames[$int_specid]."`^.`0");
                $session['user']['specialtyuses'][$skillpoints[$int_specid]]--;
            }
            else
            {
                output("Es dauert eine ganze Weile bis du deine R�stung wiedergefunden hast. Wie peinlich!
`n`^Du verlierst einen Charmepunkt.`0");
                if ($session['user']['charm']>0)
                {
                    $session['user']['charm']--;
                }
            }
            break;
        }
    case WEATHER_HEAVY_RAIN :
        {
            output("Du ziehst dir erstmal schnell deinen Regenschutz �ber.`n
`^Leider behindert er dich etwas beim K�mpfen. Wer geht bei so einem Mistwetter auch in den Wald???`0");
            $dk = $session['user']['dragonkills']+1;
            if ($dk > 40)
            {
                $dk = 40;
            }
            $rounds = round(sqrt($dk)*$session['user']['level'])+20;
            if ($session['user']['race'] == 'ecs')
            {
                $rounds = int($rounds/2);
            }
            $session['bufflist']['regenjacke'] = array("name"=>"`4Regenschutz",
            "rounds"=>$rounds,
            "wearoff"=>"Gut! Der heftige Regen ist vorbei.",
            "defmod"=>0.96,
            "atkmod"=>0.92,
            "roundmsg"=>"Der Regenschutz behindert dich.",
            "activate"=>"defense");
            break;
        }
    case WEATHER_FROSTY :
        {
            output("Z�hneklappernd ziehst du weiter. Jetzt, wo man dich darauf aufmerksam gemacht hat, kommt es dir noch viel k�lter vor.
`n`^Du kannst kaum deine Waffe ruhig halten.`0");
            $session['bufflist']['zittern'] = array("name"=>"`4zitternde H�nde",
            "rounds"=>25,
            "wearoff"=>"Du hast dich warm gek�mpft.",
            "atkmod"=>0.9,
            "roundmsg"=>"Vor K�lte kannst du deine Waffe nicht ruhig f�hren.",
            "activate"=>"offense");
            break;
        }
    case WEATHER_HAIL :
        {
            output("Meinst Du wirklich, ".$session['user']['armor']." ist da die richtige Kleidung?
`n`0`^Prompt trifft dich ein taubeneigro�es Hagelkorn und du verlierst ein paar Lebenspunkte.`0");
            $session['user']['hitpoints']=round($session['user']['hitpoints']*0.95);
            break;
        }
    case WEATHER_FLAMES :
    case WEATHER_BOREALIS :
        {
            output("Ein faszinierender Anblick. Du beschlie�t, heute etwas l�nger drau�en zu bleiben.`n
`^Du bekommst einen Waldkampf.`0");
            $session['user']['turns']++;
            break;
        }
    case WEATHER_ECLIPSE :
        {
            output("Du f�hlst, da� heute ein ganz besonderer Tag ist.`n");
            increment_specialty();
            output("`0Vielleicht solltest du ja in den Tempel oder zur Waldlichtung gehen um mit den anderen Dorfbewohnern zu meditieren.
Bestimmt sind auch Priester/Hexen anwesend um eine Zeremonie abzuhalten.
`n`^Was du jetzt tust ist allein deine Entscheidung.`0");
            break;
        }
    case WEATHER_CLOUDLESS :
        {
            if ($session['user']['race']=='vmp' || $session['user']['race']=='wwf' || $session['user']['race']=='dkl')
            {
                output("Als Schattenwesen findest du das jedoch nicht so toll und beeilst dich, wieder in deine Behausung zu kommen.");
            }
            else
            {
                output("Da macht das K�mpfen doch gleich doppelt Spa�.`n`^Du erh�ltst 2 Waldk�mpfe`0");
                $session['user']['turns']+=2;
            }
            break;
        }
       
    case WEATHER_CLOUDY_LIGHT :
        {
            if ($session['user']['race']=='vmp' || $session['user']['race']=='wwf' || $session['user']['race']=='dkl')
            {
                output("Genau das richtige Wetter f�r dich als Schattenwesen.`n`^Du erh�ltst 2 Waldk�mpfe`0");
                $session['user']['turns']+=2;
            }
            else
            {
                output("Aber du wirst schon deine Gr�nde haben, jetzt durch den Wald zu laufen.`0");
            }
            break;
        }
        default: {
            output("Du denkst dir, schon ganz andere Sachen erlebt zu haben, und ziehst weiter.");
        }
    }
}
else
{
    //es kann kein HTTP_GET_VARS[op] geben
    output('Du befindest dich in unerforschtem Gebiet. Die G�tter allein wissen wie Du hier hingekommen bist.');
}
?>