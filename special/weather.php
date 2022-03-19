<?
// idea of gargamel @ www.rabenthal.de
// Anregungen für die neuen Wetter von Gloin

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
            output("\"Könnte besser sein\" denkst du dir und gehst weiter.`0");
            break;
        }
    case WEATHER_WARM :
        {
            output("Du bist hier ganz in der Nähe von einem kleinen Waldsee. Und so
wundert es nicht, dass bei diesem Wetter eine wahre Mückenplage herrscht.`n`0");
            $case = e_rand(1,2);
            switch ($case )
            {
            case 1:
                output("Du musst die Plagegeister ständig wegscheuchen, was dich etwas
Aufmerksamkeit im nächsten Kampf kostet. `n`^Deine Verteidigung wird schwächer.`n`0");
                $session['bufflist']['muecken'] = array("name"=>"`4Mücken",
                "rounds"=>10,
                "wearoff"=>"Die Mücken haben sich verzogen.",
                "defmod"=>0.92,
                "atkmod"=>1,
                "roundmsg"=>"Die Mücken behindern Dich.",
                "activate"=>"defense");
                break;
                
            case 2:
                output("Bei dem ständigen Geschwirre kannst du dich kaum auf den nächsten
Kampf konzentrieren. `n`^Deine Angriffsfähigkeit ist daher eingeschränkt.`0");
                $session['bufflist']['muecken'] = array("name"=>"`4Mücken",
                "rounds"=>10,
                "wearoff"=>"Die Mücken haben sich verzogen.",
                "defmod"=>1,
                "atkmod"=>0.92,
                "roundmsg"=>"Die Mücken behindern Dich.",
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
Deinen Fähigkeiten tut dies jedoch gut und `^du steigst eine Stufe auf.`0");
                increment_specialty();
            }
            else
            {
                output("Als nun ein weiterer Schauer niedergeht, ziehst du dir erstmal
schnell deinen Regenschutz über.`n
`^Leider behindert er dich etwas beim Kämpfen...`0");
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
                output("Das kommt dir mit deinen Diebesfähigkeiten natürlich entgegen.
`^Du erhältst einen zusätzlichen Waldkampf!`0");
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
                output("Du sammelst etwas Reisig im Unterholz und wärmst dich erstmal
an einem kleinen Feuerchen.`n
`^Die Pause kostet dich einen Waldkampf.`0");
                $session['user']['turns']--;
            }
            break;
        }
    case WEATHER_HOT :
        {
            output("Im Dorf hast du es sogar als schwül empfunden und geniesst daher
die Zeit im schattigen, kühlen Wald.`n
`^Du bekommst einen Waldkampf.`0");
            $session['user']['turns']++;
            break;
        }
    case WEATHER_WINDY :
        {
            output("Die großen alten Bäume hier biegen sich unter der Wucht einzelner
Windböen. Ein großer Ast kann dem Wind nicht mehr standhalten und kracht zu
Boden.`0");
            $case = e_rand(1,2);
            switch ($case )
            {
            case 1:
                output("Du hast mehr Glück als Verstand! Der mächtige Ast schlägt nur
wenige Schritte von dir entfernt auf. Dir ist nichts passiert.`n
`^Etwas eingeschüchtert gehst du weiter.`0");
                break;
                
            case 2:
                output("Zum Glück schlägt der Ast neben dir ein, aber ein paar kleinere
Äste treffen dich doch. `^Du büsst Lebenspunkte ein!`0");
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
Genau richtig, um die magischen Kräfte aufzuladen.`n
`^Du kannst Deine Fähigkeiten wieder einsetzen.`0");
                //-> fähigkeiten aktivieren
                restore_specialty();
            }
            else
            {
                output("Gerade im Wald ist das nicht ungefährlich!`n`n
Um dich vor Blitzschlag zu schützen stellst du dich in einer Höhle
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
Prompt rutscht du aus und fällst auf die Nase.");
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
`^Du verlierst einen Waldkampf während du dein Tier wieder ausgräbst.`0");
                $session['user']['turns']--;
            }
            else
            {
                output("Als du weitergehen willst merkst du, daß du bis zur Hüfte eingeschneit bist.");
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
            
            output("Und es kommt wie es kommen muß, ein starker Windstoß reißt dir ".$session['user']['armor']."`0 vom Leib. ");
            if ($session['user']['specialtyuses'][$skillpoints[$int_specid]]>0)
            {
                output("Geistesgegenwärtig besinnst du dich auf deine Fähigkeiten in ".$skillnames[$int_specid]."`0 und zauberst deine Rüstung zurück.
`n`^Du verlierst eine Anwendung in ".$skillnames[$int_specid]."`^.`0");
                $session['user']['specialtyuses'][$skillpoints[$int_specid]]--;
            }
            else
            {
                output("Es dauert eine ganze Weile bis du deine Rüstung wiedergefunden hast. Wie peinlich!
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
            output("Du ziehst dir erstmal schnell deinen Regenschutz über.`n
`^Leider behindert er dich etwas beim Kämpfen. Wer geht bei so einem Mistwetter auch in den Wald???`0");
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
            output("Zähneklappernd ziehst du weiter. Jetzt, wo man dich darauf aufmerksam gemacht hat, kommt es dir noch viel kälter vor.
`n`^Du kannst kaum deine Waffe ruhig halten.`0");
            $session['bufflist']['zittern'] = array("name"=>"`4zitternde Hände",
            "rounds"=>25,
            "wearoff"=>"Du hast dich warm gekämpft.",
            "atkmod"=>0.9,
            "roundmsg"=>"Vor Kälte kannst du deine Waffe nicht ruhig führen.",
            "activate"=>"offense");
            break;
        }
    case WEATHER_HAIL :
        {
            output("Meinst Du wirklich, ".$session['user']['armor']." ist da die richtige Kleidung?
`n`0`^Prompt trifft dich ein taubeneigroßes Hagelkorn und du verlierst ein paar Lebenspunkte.`0");
            $session['user']['hitpoints']=round($session['user']['hitpoints']*0.95);
            break;
        }
    case WEATHER_FLAMES :
    case WEATHER_BOREALIS :
        {
            output("Ein faszinierender Anblick. Du beschließt, heute etwas länger draußen zu bleiben.`n
`^Du bekommst einen Waldkampf.`0");
            $session['user']['turns']++;
            break;
        }
    case WEATHER_ECLIPSE :
        {
            output("Du fühlst, daß heute ein ganz besonderer Tag ist.`n");
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
                output("Da macht das Kämpfen doch gleich doppelt Spaß.`n`^Du erhältst 2 Waldkämpfe`0");
                $session['user']['turns']+=2;
            }
            break;
        }
       
    case WEATHER_CLOUDY_LIGHT :
        {
            if ($session['user']['race']=='vmp' || $session['user']['race']=='wwf' || $session['user']['race']=='dkl')
            {
                output("Genau das richtige Wetter für dich als Schattenwesen.`n`^Du erhältst 2 Waldkämpfe`0");
                $session['user']['turns']+=2;
            }
            else
            {
                output("Aber du wirst schon deine Gründe haben, jetzt durch den Wald zu laufen.`0");
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
    output('Du befindest dich in unerforschtem Gebiet. Die Götter allein wissen wie Du hier hingekommen bist.');
}
?>