<?php

//23052006
/*
calevents.php - datumsabh�ngige Waldereignisse - von Salator (salator@gmx.de)
macht Gebrauch von den Malen (Erweiterung Die Auserw�hlten)
Hexenhaus-Event fragt Knappen ab

Itemschablonen anlegen:
INSERT INTO `items_tpl` VALUES ('rohei', 'H�hnerei', 3, 'Ein rohes Ei von durchschnittlicher Gr��e', 15, 0, 0, 0, 0, 0, 4, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 3, 0, 0, 0, 1, 0, 0, 0, 0, 1, '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, '');
INSERT INTO `items_tpl` VALUES ('nessie', '`9N`3e`#ss`3i`9e`0', 7, 'Das sagenumwobene Ungeheuer aus dem Waldsee, besiegt nach einem harten Kampf.', 15000, 20, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 3, 0, 0, 0, 0, 0, 0, 0, 0, 1, '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, '');
*/

$arr_race = race_get($session['user']['race']);

if ($_GET['op']=="")
{
    $indate = getsetting('gamedate','0005-01-01');
    $date = explode('-',$indate);
    $tag = $date[2];
    $monat = $date[1];
    // output ('Monat '.$monat.' Tag '.$tag.' stop');
    switch ($monat)
    {
    case 1:
        //Januar
        {
            if ($tag==1)
            {
                output('`n`tDu hast z�nftig den Jahreswechsel gefeiert und heute geht es dir entsprechend schlecht. Also legst du eine kleine Pause ein. Dadurch verlierst du `41 Waldkampf.');
                $session['user']['turns']--;
            }
            else
            {
                output('`n`&Es hat �ber Nacht geschneit und der ganze Wald ist wei�. `tEin paar Kinder tollen herum und ');
                if (e_rand(1,2)==1)
                {
                    output('spielen im Schnee. Da kommt auch schon ein `&Schneeball`t in deine Richtung geflogen und trifft dich hart.`nDu `4verlierst`t ein paar Lebenspunkte.');
                    $session['user']['hitpoints'] = ceil($session['user']['hitpoints']*.9);
                }
                else
                {
                    output('bauen einen `&Schneemann`t. Ein kleiner Bauernjunge fragt dich, ob du mithilfst.');
                    if ($session['user']['reputation']>0)
                    {
                        output('`nDiesen Wunsch kannst du ihm nicht abschlagen.`n`nDu verlierst `4einen Waldkampf`t, hast aber Kinderherzen gl�cklich gemacht und bekommst `^einen Charmepunkt.');
                        $session['user']['turns']--;
                        $session['user']['charm']++;
                    }
                    else
                    {
                        output('`n`6"Lass doch die bl�den G�ren"`t denkst du dir und st��t den Kleinen in den Schnee. Da kommt auch schon ein `&Schneeballhagel`t in deine Richtung geflogen.`nDu `4verlierst`t Lebenspunkte.');
                        $session['user']['hitpoints'] = ceil($session['user']['hitpoints']*.7);
                    }
                    if($session['user']['speciality']==3) //Kleptomanie
                    {
                        output('`n`tAls der Schneemann fertig ist ziert ein Stock anstatt einer M�hre sein Gesicht. Denn die M�hre hast du klammheimlich eingesteckt. Als du au�er Sichtweite bist g�nnst du dir eine kleine St�rkung.');
                        $session['user']['hitpoints']++;
                    }
                }
            }
            break;
        }
    case 2:
        //Februar
        {
            output('`n`tDu kommst an einem kleinen Bach vorbei. Zumindest hast du das, was du vor dir siehst als kleinen Bach in Erinnerung. Die Schneeschmelze hat jedoch einen rei�enden Strom daraus gemacht.`n');
            if($session['user']['marks'] & 8 || $session['user']['marks'] >31) //Wassermal
            {
                output('Da du aber das `9Mal des Wassers`t tr�gst st�rt dich das nicht weiter und du setzt deinen Weg fort.');
            }
            else
            {
                output('Du mu�t einen Umweg nehmen und verlierst dadurch `4einen Waldkampf.');
                $session['user']['turns']--;
            }
            break;
        }
    case 3:
        //M�rz
        {
            output('`n`tAuf einer kleinen Lichtung siehst du die ersten Fr�hlingsblumen sprie�en. Du pfl�ckst einen Strau� ');
            if($session['user']['marriedto']>0 && $session['user']['charisma']==4294967295) //verheiratet
            {
                if ($session['user']['marriedto']==4294967295)
                {
                    $row['name']=($session['user']['sex']?'`8Seth':'`5Violet');
                }
                else
                {
                    $row = db_fetch_assoc(db_query("SELECT name FROM accounts WHERE acctid=".$session['user']['marriedto'].""));
                }
                output('und stellst ihn auf den Tisch von eurem gemeinsamen Heim.`n`q'.$row['name'].'`t freut sich dar�ber sehr und du bekommst `@2 Charmepunkte.');
                $session['user']['charm']+=2;
            }
            else if($session['user']['charisma']>1 ) //ab 2 Flirts
            {
                output('f�r '.($session['user']['sex']?'`qdeinen Liebsten`t, der':'`qdeine Liebste`t, die').' sich sehr dar�ber freut. Du bekommst `@1 Charmepunkt.');
                $session['user']['charm']++;
            }
            else //einsam
            {
                output('f�r '.($session['user']['sex']?'`8Seth`t, der':'`5Violet`t, die').' sich sehr dar�ber freut.');
            }
            break;
        }
    case 4:
        //April
        {
            output('`n`tUm diese Zeit ist es kurioser Brauch, Eier zu verstecken. Also h�ltst du deine Augen offen... und hast Gl�ck.`nDu findest ein ');
            if ($session['user']['dragonkills']>1)
            {
                $chance=1;
            }
            else
            {
                $chance=2;
            }
            switch (e_rand($chance,9))
            {
            case 1:
                {
                    output('`9blaues Ei`t.`nIrgendwie schmeckt dieses Ei nach Fisch, was dich daran erinnert mal wieder angeln zu gehen.`n`nDu bekommst `@1 Angelrunde.');
                    //        $session['user']['fishturn']++;
                    $sql = "UPDATE account_extra_info SET fishturn=fishturn+1 WHERE acctid = ".$session['user']['acctid'];
                    db_query($sql) or die(sql_error($sql));
                    addnav("Petri heil!","fish.php");
                    break;
                }
            case 2:
                {
                    output('`2gr�nes Ei`t.`n`nDiese kleine St�rkung kommt dir gerade recht und du `@erh�ltst 1 Waldkampf.');
                    $session['user']['turns']++;
                    break;
                }
            case 3:
                {
                    output('`#t�rkises Ei`t. Das sieht lecker aus!`n`4Autsch!`t Du hast auf etwas hartes gebissen');
                    if ($session['user']['hitpoints']>10)
                    {
                        $session['user']['hitpoints']-=5;
                        output(' und hast jetzt `4Zahnschmerzen.');
                    }
                    output('`n`n`tDu schaust nach worauf du gebissen hast und hast `#1 Edelstein`t in der Hand, welchen du sofort einsteckst.');
                    $session['user']['gems']++;
                    break;
                }
            case 4:
                {
                    output('`$rotes Ei`t. Nachdem du es gegessen hast mu�t du feststellen, da� es wohl nicht mehr so ganz frisch war und sich dein Magen umdreht. `n`nDu `$verlierst viele Lebenspunkte');
                    $session['user']['hitpoints']*=.5;
                    break;
                }
            case 5:
                {
                    output('`5violettes Ei`t, das dich daran erinnert, mal wieder bei `5Violet`t vorbeizuschauen.');
                    break;
                }
            case 6:
                {
                    output('`^goldenes Ei.');
                    if (getsetting("hasegg",0)==0)
                    {
                        output('`n`tDu greifst dir das Ei. Sofort sp�rst du, dass dir dieses Ei einige T�ren �ffnen wird und magische F�higkeiten hat, die sogar den Tod besiegen k�nnen. Aber dir ist auch klar, dass dieses Ei den Neid vieler anderer Krieger auf sich ziehen wird.');
                        addnews('`^'.$session['user']['name'].'`t hat auf der Suche nach Eiern ein `^goldenes`t Exemplar gefunden!');
                        savesetting("hasegg",stripslashes($session['user']['acctid']));
                        item_set(' tpl_id="goldenegg"', array('owner'=>$session['user']['acctid']) );
                    }
                    else
                    {
                        output('`n`n`tDas kann nur eine Einbildung sein, denn `b');
                        $sql = "SELECT acctid,name FROM accounts WHERE acctid = '".getsetting("hasegg",0)."'";
                        $result = db_query($sql) or die(db_error(LINK));
                        $row = db_fetch_assoc($result);
                        if ($session['user']['acctid'] == $row['acctid'])
                        {
                            output('du selbst');
                        }
                        else
                        {
                            output($row['name']);
                        }
                        output('`t`b besitzt dieses Unikat.');
                    }
                    break;
                }
            case 7:
                {
                    output('`7k`qu`tn`gt`9e`$r`@b`8u`$n`%t`!e`Gs `0Ei`t und �berlegst ob du es essen oder damit jonglieren sollst. `n`nDu entscheidest dich f�r Joglieren und bekommst `^5 Anwendungen in Gaukelei.');
                    $session['user']['specialtyuses']['juggleryuses']+=5;
                    break;
                }
            case 8:
                {
                    output('`8rohes H�hnerei`t.');
                    if ($session['user']['hashorse']>0 && $session['bufflist']['mount']['rounds'] > 0)
                    {
                        output('`n`tDu willst es gerade ausschl�rfen als dein `b'.$playermount['mountname'].'`b`t es dir vor der Nase wegschnappt. Naja immerhin gibt das deinem '.$playermount['mountname'].'`t Kraft f�r weitere 15 Runden.');
                        $session['bufflist']['mount']['rounds'] += 15;
                    }
                    else
                    {
                        output('`n`tOb man das Ei vielleicht ausbr�ten lassen kann? Vielleicht wird ja ein Kampfhahn daraus. Du steckst das Ei ein und ziehst weiter.');
                        //alt     $sql="INSERT INTO items(name,class,owner,value1,gold,gems,description) VALUES ('H�hnerei','Beute',".$session[user][acctid].",0,15,0,'Ein rohes Ei von durchschnittlicher Gr��e')";
                        //        db_query($sql);
                        item_add($session['user']['acctid'],'rohei');
                    }
                    break;
                }
                default:
                {
                    output('`Tverfaultes Ei`t. Das l�sst du besser liegen.');
                }
            }
            break;
        }
    case 5:
        //Mai
        {
            if($tag==1) //internationaler Kampf- und Feiertag der Werkt�tigen (das mu� sein *g*)
            {
                output('`n`tDu findest eine `$rote Nelke`t, die du dir ins Knopfloch steckst. Mit dieser Nelke hast du Anrecht auf eine Bratwurst f�r 0,5 Gold und Zutritt zur Dark Horse Taverne.`n');
                addnav('Dark Horse Taverne','forest.php?specialinc=darkhorse.php');
            }
            output('`n`tDer Duft von Gegrilltem steigt dir in die Nase. Du folgst dem Duft und entdeckst ein paar Waldbewohner, die um ein Feuer sitzen. Sie laden dich ein, mit ihnen zu essen. Das l��t du dir nat�rlich nicht zweimal sagen.`n`nDu f�hlst dich `@gest�rkt.');
            $session['user']['hitpoints']*=1.1;
            break;
        }
    case 6:
        //Juni
        {
            output('`n`tDu kommst an eine kleine Waldlichtung und siehst dort ein Rehkitz im Gras sitzen.`n');
            if ($session['user']['reputation']>=0)
            {
                output(' Eine Weile erfreust du dich an diesem Anblick und ziehst dann gl�cklich weiter.`nDeine `@Verteidigung steigt.');
                $session['bufflist']['freude'] = array("name"=>"`9Freude","rounds"=>25,"wearoff"=>"`&Du vergisst das Rehkitz.","defmod"=>1.1,"roundmsg"=>"`9Das Rehkitz l��t dich an deine Sicherheit denken.","activate"=>"offense");
            }
            else
            {
                output(' Dein Ansehen im Dorf ist aber ohnehin schon ruiniert, also macht es dir nichts aus, das junge Leben mit deinem '.$session['user']['weapon'].'`t zu beenden. Schlie�lich braucht ein Krieger ja auch was zu essen.');
                $session['user']['hitpoints']*=1.05;
            }
            // uncomment if you have a picture
            //    output('`n`n`c<img src="images/rehkitz.jpg" alt="">`c');
            break;
        }
    case 7:
        //Juli
        {
            $w = get_weather();
            output('`n`tDu kommst an einen kleinen Waldsee. Ein richtig idyllisches Fleckchen. Du schaust zum Himmel und bemerkst, das Wetter ist "`^'.$w['name'].'`t"!!`n');
            switch ($settings['weather'])
            {
            case WEATHER_WARM:
            case WEATHER_CLOUDLESS:
            case WEATHER_HOT:
                {
                    output('`gWunderbar,`t denkst du dir, `gdas ist ja richtiges Badewetter!`n`tAlso springst du ins Wasser und ');
                    $session['user']['clean']=0;
                    //hat das �berhaupt Wirkung in Atrahor?
                    if ($session['user']['hitpoints']<$session['user']['maxhitpoints'])
                    {
                        output('f�hlst dich `@erfrischt.');
                        $session['user']['hitpoints']=$session['user']['maxhitpoints'];
                    }
                    else
                    {
                        output('schwimmst eine Runde. Deine `@Erfahrung steigt.');
                        $session['user']['experience']*=1.05;
                    }
                    break;
                }
            case WEATHER_FOGGY:
            case WEATHER_BOREALIS:
            case WEATHER_FLAMES:
            case WEATHER_WINDY:
                {
                    output('`@Naja,`t denkst du dir, `@Badewetter ist das nicht, aber zum Angeln reichts allemal.`n');
                    $sql = "SELECT worms,minnows,fishturn FROM account_extra_info WHERE acctid=".$session['user']['acctid']."";
                    $result = db_query($sql) or die(db_error(LINK));
                    $row = db_fetch_assoc($result);
                    //        output('`3Angelrunden: '.$row['fishturn'].', W�rmer: '.$row['worms'].', Fliegen: '$row['minnows']);
                    if ($row['fishturn']>0 &&($row['worms']>0||$row['minnows']>0))
                    {
                        switch (e_rand(1,5))
                        {
                        case 1:
                            {
                                output('`n`n`t Wenig sp�ter hast du `9N`3e`#ss`3i`9e`& das Seeungeheuer`t am Haken h�ngen.');
                                $session['user']['specialinc']="calevents.php";
                                addnav('K�mpfen!','forest.php?op=nessie');
                                addnav('wegrennen','forest.php?op=leave');
                                $delnav=1;
                                break;
                            }
                        case 2:
                        case 3:
                            {
                                output('`n`n`t Wenig sp�ter f�ngst du ');
                                if ($row['minnows']>0)
                                {
                                    output('eine `3Forelle`t, die');
                                }
                                else
                                {
                                    output('einen `3Barsch`t, den');
                                }
                                output(' du gleich grillst. Guten Appetit!');
                                $session['user']['hitpoints']*=1.1;
                                break;
                            }
                        case 4:
                            {
                                output('`n`n`t Wenig sp�ter hast du einen `3Buckelwal`t am Haken h�ngen.');
                                switch ($session['user']['race'])
                                {
                                case 'trl':
                                case 'dmn':
                                case 'avt':
                                    {
                                        output('`nAls '.$arr_race['name'].' schaffst du es ohne gr��ere Schwierigkeiten, den dicken Brocken an Land zu ziehen. Du hast jetzt so viel zu essen, dass dir das `@2 permanente Lebenspunkte`t einbringt.');
                                        $session['user']['maxhitpoints']+=2;
                                        break;
                                    }
                                case 'elf':
                                case 'men':
                                case 'dkl':
                                case 'wwf':
                                case 'ork':
                                    {
                                        output('`nAls '.$arr_race['name'].' hast du einige Schwierigkeiten, den dicken Brocken an Land zu ziehen.');
                                        if ($session['user']['hashorse']>0)
                                        {
                                            output(' Dein '.$playermount['mountname'].' hilft dir.');
                                        }
                                        output('`nEndlich ist es geschafft. Du `$verlierst 1 Waldkampf`t, hast aber so viel zu essen da� dir das `@1 permanenten Lebenspunkt`t einbringt.');
                                        $session['user']['turns']--;
                                        $session['user']['maxhitpoints']++;
                                        break;
                                    }
                                    default:
                                    {
                                        output('`nSo sehr du dich auch anstrengst, den Wal bekommst du nicht aus dem Wasser.');
                                        break;
                                    }
                                }
                                break;
                            }
                            default:
                            {
                                output('Du angelst einen alten Stiefel. Der hilft dir nicht wirklich was.');
                            }
                        }
                    }
                    else
                    {
                        output('Leider hast du deine Angel nicht dabei. So bleibt dir nichts weiter �brig als wieder in den Wald zu gehen.');
                    }
                    break;
                }
                default:
                {
                    output('Du beschlie�t, bei besserem Wetter an diesen Platz zur�ckzukehren.');
                }
            }
            break;
        }
    case 8:
        //August
        {
            output('`n`tDu schlenderst nichtsahnend durch den Wald als du mit deinem '.$session['user']['weapon'].'`t ein `^We`Tsp`^en`Tne`^st `tstreifst. Die Wespen fallen �ber dich her.');
            if (e_rand(1,2)==1)
            {
                output('`nSo schnell du kannst rennst du weg, doch die Wespen sind schneller. Du `4verlierst viele Lebenspunkte.');
                $session['user']['hitpoints'] = ceil($session['user']['hitpoints']*.5);
            }
            else
            {
                output('`nTodesmutig stellst du dich den Biestern und kannst sie vertreiben. Du zerschl�gst das Wespennest und findest darin `#einen Edelstein.');
                $session['user']['gems']++;
            }
            break;
        }
    case 9:
        //September
        {
            output('`n`tAuf deiner Suche nach Waldmonstern kommst du an einem `^Kornfeld`t vorbei, welches gerade abgeerntet wird. Du opferst etwas deiner Zeit f�r einen kleinen Zusatzverdienst und hilfst bei der Ernte.');
            if ($session['user']['turns'] > 2)
            {
                $session['user']['turns']-=2;
            }
            else
            {
                $session['user']['turns']=0;
            }
            $session['user']['gold']+= min($session['user']['level']*200,1000);
            break;
        }
    case 10:
        //Oktober
        {
            if($tag==31) //Halloween
            {
                switch ($session['user']['race'])
                {
                case 'vmp':
                    {
                        output('`n`tDu kommst an eine Stelle an der die Menschen Halloween feiern. Ein gefundenes Fressen f�r dich als Vampir. Du schleichst dich an ein ahnungsloses Opfer und saugst es aus. Deine `@Lebenspunkte`t erh�hen sich und du hast Kraft f�r `@einen weiteren Waldkampf`t.');
                        $session['user']['hitpoints']=$session['user']['maxhitpoints']*2;
                        $session['user']['turns']++;
                        break;
                    }
                case 'wwf':
                    {
                        output('`n`tDu kommst an eine Stelle an der die Menschen Halloween feiern. Ein gefundenes Fressen f�r dich als Werwolf. Du schleichst dich an ein ahnungsloses Opfer und bei�t zu. Deine `@Lebenspunkte`t erh�hen sich und du hast Kraft f�r `@einen weiteren Waldkampf`t.');
                        $session['user']['hitpoints']=$session['user']['maxhitpoints']*2;
                        $session['user']['turns']++;
                        break;
                    }
                case 'dmn':
                    {
                        output('`n`tDu kommst an eine Stelle an der die Menschen Halloween feiern. Als D�mon zeigst du wer der wahre Herrscher ist und ergreifst Besitz von einem K�rper. Deine `@Lebenspunkte`t erh�hen sich und du hast Kraft f�r `@einen weiteren Waldkampf`t.');
                        $session['user']['hitpoints']=$session['user']['maxhitpoints']*2;
                        $session['user']['turns']++;
                        break;
                    }
                case 'men':
                    {
                        output('�n�tHeute ist Halloween, die Nacht der Toten. Du zelebrierst mit anderen Menschen ein Ritual der Totenbeschw�rung. Diese Gelegenheit nutzt ein `4Untoter`t und bem�chtigt sich deines K�rpers. Du bist etwas geschw�cht und `4verlierst einen Waldkampf`t, bekommst aber `@5 Anwendungen in Dunklen K�nsten`t.');
                        $session['user']['specialtyuses']['darkartuses']+=5;
                        $session['user']['turns']--;
                        break;
                    }
                    default:
                    {
                        output('`n`tEs ist Halloween. Als '.$arr_race['name'].' interessiert dich das aber nicht sonderlich. Du kannst aber eine Totenbeschw�rung versuchen');
                        $session['user']['specialinc']="calevents.php";
                        addnav('Totenbeschw�rung','forest.php?op=shades');
                        addnav('Zur�ck in den Wald','forest.php?op=leave');
                        $delnav=1;
                    }
                }
            }
            else
            {
                output('`n`tDu kommst an einem abgeernteten Feld vorbei und siehst in der Mitte eine Vogelscheuche stehen.`nAls Du gerade weitergehen willst vernimmst Du ');
                if (e_rand(1,2)==1)
                {
                    output('eine leichte Bewegung und bleibst einen kurzen Moment verwundert stehen. Neugierig gehst Du auf die Vogelscheuche zu. In dem Moment als Du die Hand nach ihr austreckst merkst du da� die Vogelscheuche lebt - `4und dich angreift!');
                    $session['user']['specialinc']="calevents.php";
                    addnav('K�mpfen!','forest.php?op=scarecrowfight');
                    addnav('wegrennen','forest.php?op=leave');
                    $delnav=1;
                }
                else
                {
                    output('ein leichtes Glitzern und bleibst einen kurzen Moment verwundert stehen. Neugierig gehst Du auf die Vogelscheuche zu und stellst erfreut fest, dass eine Elster fleissig Edelsteine gesammelt und diese auf dem Hut der Vogelscheuche gelagert hat. Schnell steckst Du die `#2 Edelsteine`t ein und ziehst weiter.');
                    $session['user']['gems']+=2;
                }
            }
            break;
        }
    case 11:
        //November
        {
            output('`n`tDu ziehst durch den Wald und �rgerst dich �ber das graue Novemberwetter. Da triffst du eine'.($session['user']['sex']?'n jungen Elf, der':'junge Elfin, die').' dich anspricht: `gHallo! Du mu�t '.$session['user']['name'].'`g sein.');
            output('`n`7Ja, '.($session['user']['sex']?'die':'der').' bin ich. `tantwortest du verdutzt.`n`gOh, dich wollte ich schon lange mal zum Tee einladen. Magst du nicht mitkommen? Ich wohne gleich da dr�ben.');
            output('`n`tErfreut nimmst du die Einladung an.`n`nDer Elfen-Tee hatte heilende Wirkung, deine Lebenspunkte wurden `^vollst�ndig aufgef�llt`t und du versp�rst Kraft f�r einen `^zus�tzlichen Waldkampf`t.');
            $session['user']['hitpoints']=$session['user']['maxhitpoints'];
            $session['user']['turns']++;
            break;
        }
    case 12:
        //Dezember
        {
            switch ($tag)
            {
            case 6:
                //Nikolaus
                {
                    $findgold = e_rand(20,200);
                    output('`n`tDu findest einen sauber geputzten Stiefel, der mit Waln�ssen gef�llt ist. Neugierig knackst du eine Nu� und findest darin nicht etwa einen e�baren Kern, sondern `^ein Goldst�ck`t. Sofort machst du dich daran, auch die anderen N�sse zu knacken und findest insgesamt `^'.$findgold.' Goldst�cke.');
                    $session['user']['gold']+=$findgold;
                    break;
                }
            case 9:
                //damit es nicht zu eint�nig wird
            case 10:
            case 11:
            case 12:
            case 13:
            case 14:
            case 15:
                {
                    output('`n`tDu verirrst dich total. In dieser Gegend des Waldes bist du noch nie gewesen. Da entdeckst du ein Hexenhaus welches vollkommen aus Pfefferkuchen gebaut ist. Was wirst du jetzt tun?');
                    addnav('Pfefferkuchen naschen','forest.php?op=gingerbread');
                    addnav('w?einfach weitergehen','forest.php?op=leave');
                    $session['user']['specialinc']="calevents.php";
                    $delnav=1;
                    break;
                }
            case 24:
                //Weihnachten
            case 25:
            case 26:
                {
                    output('`n`tDu triffst einen seltsamen Mann mit `$rotem Mantel `tund `&wei�em Bart`t. Diholter dipolter wer stapft durch den Tann?`nDer wunderliche Alte schenkt dir `#2 Edelsteine.');
                    $session['user']['gems']+=2;
                    break;
                }
            case 27:
                //Weihnachten ist vorbei
            case 28:
            case 29:
            case 30:
                {
                    output('`n`tDas Reh springt hoch, das Reh springt weit. Warum auch nicht, es hat ja Zeit.');
                    break;
                }
            case 31:
                //Silvester
                {
                    output('`n`tSag mal, '.$session['user']['name'].'`t, heute ist Silvester. W�re das nicht eine passende Gelegenheit, etwas mit Feuermagie zu spielen?');
                    break;
                }
                default:
                {
                    output('`n`tSag mal, '.$session['user']['name'].'`t, hast du eigentlich schon `QWeihnachtsgeschenke`t gekauft? Wenn nicht solltest du mal in den Geschenkeladen gucken.');
                }
            }
            break;
        }
        default: //if you have more than 12 months, add some events
        {
            output('`n`tDu findest einen Kalender, wei�t aber nicht was du damit anfangen sollst. Dir f�llt nur auf da� es einen '.$monat.'. Monat gibt.');
        }
    }
    if (!$delnav)
    {
        addnav('Zur�ck in den Wald','forest.php');
    }
}
if ($_GET['op']=="shades")
{
    output('`5Deine Totenbeschw�rung hatte Erfolg, du kannst mit den Toten sprechen:`n`n');
    viewcommentary("shade","Sprich zu den Toten",25,"spricht");
    $session['user']['specialinc']="calevents.php";
    addnav('Zur�ck in den Wald','forest.php?op=leave');
}
if ($_GET['op']=="gingerbread")
{
    switch ($_GET['what'])
    {
    case "breakout":
        {
            output('`n`tDu schaffst es irgendwie, ein Loch in die Wand zu essen. Doch als du dich hindurchzw�ngen willst, bemerkst du, dass du viel zu dick bist und verendest j�mmerlich.');
            addnav('Hallo Ramius!','shades.php');
            $session['user']['hitpoints']=0;
            $session['user']['alive']=0;
            addnews('`b`t'.$session['user']['name'].'`b`3 verstarb unter ungekl�rten Umst�nden in einem Lebkuchenhaus...');
            break;
        }
    case "finger":
        {
            output('`n`tDie Hexe betastet deinen Finger und sagt `6"Ja, jetzt bist du fett genug. Ab in den Backofen mit dir!"`n`tAls die Hexe noch einmal nach dem Feuer sehen will nutzt du die Gelegenheit und st��t sie hinein. `n`@Puh, das ist ja grad nochmal gut gegangen!');
            addnav('Zur�ck in den Wald','forest.php');
            $session['user']['specialinc']="";
            $session['user']['hitpoints']*=1.2;
            addnews('`b`t'.$session['user']['name'].'`b`3 wurde von einer Hexe gemastet.');
            break;
        }
    case "knave":
        {
            output('`n`tDu rufst deinen Knappen, welcher auch sofort herbeieilt. Das war un�berlegt, denn die Hexe hat das bemerkt und dein Knappe ist jetzt ebenfalls eingesperrt. Immerhin hast du jetzt Gesellschaft...');
            addnav('Ausbrechen','forest.php?op=gingerbread&what=breakout');
            addnav('Finger hinhalten','forest.php?op=gingerbread&what=finger');
            addnav('St�ckchen hinhalten','forest.php?op=gingerbread&what=twig');
            $session['user']['specialinc']="calevents.php";
            break;
        }
    case "twig":
        {
            $sql = "SELECT name,state,level FROM disciples WHERE state>0 AND master=".$session['user']['acctid']."";
            $result = db_query($sql) or die(db_error(LINK));
            output('`n`tDu h�ltst der Hexe ein d�rres St�ckchen hin. Die Hexe betastet das St�ckchen und sagt `6"Jaja, du mu�t noch fetter werden."`n`n`tDu bleibst eine weitere Runde eingesperrt.');
            addnav('Ausbrechen','forest.php?op=gingerbread&what=breakout');
            addnav('Finger hinhalten','forest.php?op=gingerbread&what=finger');
            addnav('St�ckchen hinhalten','forest.php?op=gingerbread&what=twig');
            if (db_num_rows($result)>0)
            {
                addnav('Knappen rufen','forest.php?op=gingerbread&what=knave');
            }
            $session['user']['turns']--;
            $session['user']['specialinc']="calevents.php";
            break;
        }
        default:
        {
            output('`n`tDu brichst einen Pfefferkuchen ab und willst gerade hineinbei�en, als du eine Stimme h�rst: `6Knusper, knusper, kn�uschen, wer knuspert an meinem H�uschen?`n`tEhe du dich versiehst steht eine alte Hexe vor dir. `6"Ah, '.$session['user']['name'].'`6, du scheinst hungrig zu sein. Na dann komm mal herein."`n');
            output('`tDu folgst der Hexe in eine kleine Kammer. Ein riesiger Berg Pfefferkuchen ist aufgetischt und du machst dich dar�ber her. Als du zum Platzen satt bist stellst du jedoch fest da� du gefangen bist. Die Hexe erkl�rt dir da� du erst wieder raus kommst wenn du fett genug bist - um im Backofen zu landen.`n');
            output('Gerade kommt die Hexe wieder um zu f�hlen ob du schon fett genug bist. Was willst du jetzt tun?');
            addnav('Ausbrechen','forest.php?op=gingerbread&what=breakout');
            addnav('Finger hinhalten','forest.php?op=gingerbread&what=finger');
            addnav('St�ckchen hinhalten','forest.php?op=gingerbread&what=twig');
            $session['user']['specialinc']="calevents.php";
        }
    }
}
if ($_GET['op']=="scarecrowfight")
{
    $session['user']['specialinc']="calevents.php";
    $badguy = array("creaturename"=>"`^Vogelscheuche`0","creaturelevel"=>$session['user']['level'],"creatureweapon"=>"Stroh-Arme","creatureattack"=>$session['user']['attack'],"creaturedefense"=>$session['user']['defence'],"creaturehealth"=>$session['user']['maxhitpoints'], "diddamage"=>0);
    $session['user']['badguy']=createstring($badguy);
    $fight=true;
}
if ($_GET['op']=="nessie")
{
    $session['user']['specialinc']="calevents.php";
    //hier noch sinnvolle Werte eintragen
    $badguy = array("creaturename"=>"`^Nessie`0","creaturelevel"=>0,"creatureweapon"=>"riesiges Maul","creatureattack"=>$session['user']['attack']*.9,"creaturedefense"=>$session['user']['defence']*1.1,"creaturehealth"=>$session['user']['maxhitpoints']+1000, "diddamage"=>0);
    $session['user']['badguy']=createstring($badguy);
    $fight=true;
}
if ($_GET['op'] == "fight")
{
    $session['user']['specialinc']="calevents.php";
    $fight=true;
}
if ($_GET['op'] == "run")
{
    $session['user']['specialinc']="";
    $session['bufflist'] = unserialize($session['user']['buffbackup']);
    output('`&Du glaubst, keine Chance zu haben und rennst so schnell du kannst davon. Aber man wird im Dorf �ber deine Feigheit reden.`n');
    //  $session['user']['reputation']--;
    addnav('Zur�ck in den Wald','forest.php');
}
if ($fight)
{
    $session['user']['specialinc']="calevents.php";
    if (count($session['bufflist'])>0 && is_array($session['bufflist']) || $_GET['skill']!="")
    {
        $_GET['skill']="";
        if ($_GET['skill']=="")
        {
            $session['user']['buffbackup']=serialize($session['bufflist']);
        }
        $session['bufflist']=array();
        output("`&Die au�ergew�hnlichen Umst�nde hindern dich daran, deine besonderen F�higkeiten einzusetzen!`0");
    }
    include "battle.php";
    if ($victory)
    {
        $session['user']['specialinc']="";
        addnav('Zur�ck in den Wald','forest.php');
        switch ($badguy['creaturename'])
        {
        case "`^Vogelscheuche`0":
            {
                output('`n`&Du hast die Vogelscheuche besiegt und dir `5h�heres Ansehen`& verdient!`n');
                $session['user']['reputation']++;
                addnews('`b`^'.$session['user']['name'].'`b`g hat die `^mordende Vogelscheuche`g besiegt!');
                output("`n`tAls du die Vogelscheuche durchsuchst findest du `#3 Edelsteine!`t Die hat wohl eine Elster hier versteckt.");
                $session['user']['gems']+=3;
                break;
            }
        case "`^Nessie`0":
            {
                output("`n`&Du hast `^Nessie`& besiegt!`n`tSo eine seltene Troph�he, die mu�t du einfach mitnehmen.`n`3Ein wenig eng wird es ja jetzt in deinem Beutel, vielleicht solltest du daf�r die halbe Kuh hier lassen.");
                addnews('`b`^'.$session['user']['name'].'`b`# hat ein `^Seeungeheuer`# gefangen!');
                //alt   $sql="INSERT INTO items(name,class,owner,value1,gold,gems,description) VALUES ('`9N`3e`#ss`3i`9e`0','M�bel',".$session[user][acctid].",0,15000,20,'Das sagenumwobene Ungeheuer aus dem Waldsee, besiegt nach einem harten Kampf.')";
                //      db_query($sql);
                item_add($session['user']['acctid'],'nessie');
                break;
            }
            default:
            {
                output('`^Oops, du hast irgendwas besiegt was nicht da ist! Ein Fall f�r Onkel Admin.');
                debuglog($session['user']['name'].'`0 hat '.$badguy['creaturename'].' besiegt, diese Kreatur gibt es nicht. calevents.php');
            }
        }
    }
    else if ($defeat)
    {
        output('`n`&Kurz vor dem endg�ltigen Todessto� l��t '.$badguy['creaturename'].'`& von dir ab. Du hast nur noch 1 Lebenspunkt und verlierst 2 Waldk�mpfe, aber du hast Gl�ck, noch am Leben zu sein!');
        $session['user']['hitpoints']=1;
        $session['user']['turns']-=2;
        $session['user']['specialinc']="";
        addnav('Zur�ck in den Wald','forest.php');
    }
    else
    {
        fightnav(false,true);
    }
}
if ($_GET['op']=="leave")
{
    $session['user']['specialinc']="";
    redirect("forest.php");
    
}
output("`n`n");
page_footer();
?>