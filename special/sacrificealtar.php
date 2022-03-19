<?php
/* ******************* 
Altar of Sacrifice 
Written by TheDragonReborn 
    Based on Forest.php

Translation by Lendara Mondkind (Lisandra)

******************* */  

$specialbat="sacrificealtar.php"; 
$allowflee=true; 
$allowspecial=true; 
if ($_GET[op]==""){ 
    output("`@Als du durch den Wald wanderst, entdeckst du plötzlich einen Steinaltar. 
     Er wurde aus Basaltstein unter einen riesigen Baum gebaut. Du gehst näher zu ihm hin und du siehst 
     eingetrocknete Blutflecken von Jahrhunderten der Opferungen. Das ist eindeutig ein besonderer Ort und 
     Du kannst eine göttliche Präsenz spüren. `n
     Du solltest den Göttern vielleicht etwas opfern, um sie nicht zu beleidigen.
     `n`nWas wirst du tun?"); 
    addnav("Was opfern?"); 
    addnav("Dich selbst","forest.php?op=Sacrifice&type=Yourself"); 
//    addnav("Ein starkes Monster","forest.php?op=Sacrifice&type=Creature&Difficulty=Strong"); 
//    addnav("Ein mittleres Monster","forest.php?op=Sacrifice&type=Creature&Difficulty=Moderate"); 
//    if ($session[user][level] != 1) addnav("Ein schwaches Monster","forest.php?op=Sacrifice&type=Creature&Difficulty=Weak"); 
    addnav("Blumen","forest.php?op=Sacrifice&type=Flowers"); 
    if ($session[user][gems]>0) addnav("Edelstein","forest.php?op=Sacrifice&type=Edelstein"); 
    addnav("`nAltar verlassen","forest.php?op=Leave"); 
    $session[user][specialinc]=$specialbat; 
}elseif ($_GET[op]=="Sacrifice"){ 
        if ($_GET[type]=="Yourself"){ 
        output("`@Du legst deine Sachen ab und legst dich auf den Altar. Als du dein/e/n ".$session[user][weapon]." erhebst,
         denkst du an die Liebe. Dann, ohne weitere Verzögerung, nimmst du dir mit ".$session[user][weapon]." das Leben. 
         Als sich die Dunkelheit deiner bemächtigt, "); 
        switch(e_rand(1,15)){ 
        case 1: 
        case 2: 
        case 3: 
            output("denkst du, dass du genug getan hast, um die Götter zu besänftigen, damit diese die Welt 
             zu einem besseren Ort machen...`n`nLeider wirst DU nicht dabei sein, um es zu sehen.
             `n`n`^Du bist tot!`n
             Du verlierst all dein Gold!`n
             Du verlierst 5% deiner Erfahrung.`n
             Du kannst morgen wieder weiterspielen."); 
            killplayer(100,5,0,'');
            addnav("Tägliche News","news.php"); 
            if (strtolower(substr($session[user][name],-1))=="s") addnews($session[user][name]."' Körper wurde auf einem Altar in den Wäldern gefunden."); 
            else addnews($session[user][name]."'s Körper wurde auf einem Altar in den Wäldern gefunden.");  
            break; 
        case 4: 
        case 5: 
            output("siehst du wie der Himmel rot wird aufgrund des Zorns der Götter. Sie sind nicht so leichtgläubig wie du gedacht hast. Sie
             wissen warum du das getan hast. Niemand, der sich selbst respektiert, würde einer Selbstopferung zustimmen, wenn er
             nicht denken würde, dass er etwas dadurch erhält. Ein gewaltiger Blitz kommt vom Himmel herab und
             trifft deinen toten Körper. Dabei nimmt der Blitz einige deiner Angriffs- und Verteidigungsfähigkeiten mit. Nun,
             das ist es was du dafür erhältst, dass du die Götter betrügen wolltest.
             `n`n`^Du bist gestorben!`n
             Du verlierst all dein Gold!`n
             Du verlierst 10% deiner Erfahrung!`n
             Du verlierst 1 Punkt in Angriff und Verteidigung!`n
             Du kannst morgen wieder weiterspielen."); 
            killplayer(100,5,0,'');
            if ($session[user][attack] >= 2)$session[user][attack]--; 
            if ($session[user][defence] >= 2)$session[user][defence]--; 
            addnav("Tägliche News","news.php"); 
            if (strtolower(substr($session[user][name],-1))=="s") addnews($session[user][name]."'s Überbleibsel wurden verkohlt auf einem Altar gefunden."); 
            else addnews($session[user][name]."'s Überbleibsel wurden verkohlt auf einem Altar gefunden."); 
            break; 
        case 6: 
        case 7: 
        case 8: 
        case 9: 
            output("siehst du ein strahlendes Leuchten. Es formt sich langsam zur Gestalt eines gutmütigen alten Mannes.`n`n
             \"`#".($session[user][sex]?"Meine geliebte Tochter":"Mein geliebter Sohn").",\"`@ sagt er, \"`#Du hast mir die höchste
             Opferung erbracht und dafür werde ich Dich belohnen.`@\"`n`n
             Er erhebt seine Hand und fährt sie an der gesamten Länge deines Körpers entlang. Er hält sie ganz knapp vor der Berührung
             mit dir. Du fühlst wie eine warme Energie durch dich wandert und alles fängt an klarer zu werden. Du stehst auf
             und erkennst, dass die Wunde von deine/r/m ".$session['user']['weapon']." komplett geheilt ist. Du schaust dich nach dem
             alten Mann um, doch er ist verschwunden.`n`n
             Du nimmst deine Sachen wieder auf und machst du bereit weiterzugehen. Als du an einer Wasserpfütze vorbei gehst, siehst du zufällig
             in sie und siehst dein Spiegelbild. Du siehst wesentlich ".($session['user']['sex']?'schöner':'angenehmer')); 
            output(" aus als je zuvor. Es muss ein Geschenk der Götter sein.`n`n`^Du erhältst 2 Charmepunkte!"); 
            $session[user][charm]+=2; 
            break; 
        case 10: 
        case 11: 
        case 12: 
        case 13: 
            output("siehst du ein strahlendes Leuchten. Es formt sich langsam zur Gestalt eines gutmütigen alten Mannes.`n`n
             \"`#".($session[user][sex]?"Meine geliebte Tochter":"Mein geliebter Sohn").",\"`@ sagt er, \"`#Du hast mir die höchste
             Opferung erbracht und dafür werde ich Dich belohnen.`@\"`n`n
             Er erhebt seine Hand und fährt sie an der gesamten Länge deines Körpers entlang. Er hält sie ganz knapp vor der Berührung
             mit dir. Du fühlst wie eine warme Energie durch dich wandert und alles fängt an klarer zu werden. Du stehst auf
             und erkennst, dass die Wunde von deine/r/m ".$session[user][weapon]."  komplett geheilt ist. Du schaust dich nach dem
             alten Mann um, doch er war verschwunden.`n`n
             Als du den Altar verlässt, fällt dir auf, dass du mehr Lebenspunkte als zuvor hast."); 
//            $reward=$session[user][maxhitpoints] * 0.05;  
            $reward=1;  
            output("`n`n`^Deine maximalen Lebenspunkte sind `bpermanent`b um $reward Punkte gestiegen!"); 
            $session[user][maxhitpoints]+=$reward; 
            break; 
        case 14: 
        case 15: 
            output("siehst du ein strahlendes Leuchten. Es formt sich langsam zur Gestalt eines gutmütigen alten Mannes.`n`n
             \"`#".($session[user][sex]?"Meine geliebte Tochter":"Mein geliebter Sohn").",\"`@ sagt er, \"`#Du hast mir die höchste
             Opferung erbracht und dafür werde ich Dich belohnen.`@\"`n`n
             Er erhebt seine Hand und fährt sie an der gesamten Länge deines Körpers entlang. Er hält sie ganz knapp vor der Berührung
             mit dir. Du fühlst wie eine warme Energie durch dich wandert und alles fängt an klarer zu werden. Du stehst auf
             und erkennst, dass die Wunde von deine/r/m ".$session[user][weapon]."  komplett geheilt ist. Du schaust dich nach dem
             alten Mann um, doch er war verschwunden.`n`n
             Als du den Altar verlässt, fällt dir auf, dass deine Muskeln größer geworden sind.
             `n`n`^Du erhältst +1 Angriff und +1 Verteidigung!"); 
            $session[user][attack]++; 
            $session[user][defence]++; 
            break;                                                 
        } 
     
    }elseif ($_GET[type]=="Creature"){ 
    output("Du entscheidest dich eine unglückselige Kreatur an die Götter zu opfern. Darum gehst du in den Wald und schaust dich nach einem passenden Geschenk um.`n"); 
$session[user][turns]--; 
              $battle=true; 
            if (e_rand(0,2)==1){ 
                $plev = (e_rand(1,5)==1?1:0); 
                $nlev = (e_rand(1,3)==1?1:0); 
            }else{ 
              $plev=0; 
                $nlev=0; 
            } 
             
            if ($Difficulty=="Weak"){  
              $nlev++; 
                output("`\$Du gehst in ein Gebiet des Waldes, von dem du weisst, dass sich dort eher leichtere Gegner aufhalten.`0`n"); 
            } 
             
            if ($Difficulty=="Strong"){ 
              $plev++; 
                output("`\$Du gehst in ein Gebiet des Waldes, welches Kreaturen aus deinen Alpträumen enthält, in der Hoffnung, dass du ein verletztes findest.`0`n"); 
            } 
            $targetlevel = ($session['user']['level'] + $plev - $nlev ); 
            if ($targetlevel<1) $targetlevel=1; 
            $sql = "SELECT * FROM creatures WHERE creaturelevel = $targetlevel ORDER BY rand(".e_rand().") LIMIT 1"; 
            $result = db_query($sql) or die(db_error(LINK)); 
            $badguy = db_fetch_assoc($result); 
            $expflux = round($badguy['creatureexp']/10,0); 
            $expflux = e_rand(-$expflux,$expflux); 
            $badguy['creatureexp']+=$expflux; 

            //make badguys get harder as you advance in dragon kills. 
            //output("`#Debug: badguy gets `%$dk`# dk points, `%+$atkflux`# attack, `%+$defflux`# defense, +`%$hpflux`# hitpoints.`n"); 
            $badguy['playerstarthp']=$session['user']['hitpoints']; 
            $dk = 0; 

            while(list($key, $val)=each($session[user][dragonpoints])) { 
                if ($val=="at" || $val=="de") $dk++; 
            } 
             
            $dk += (int)(($session['user']['maxhitpoints']- 
                ($session['user']['level']*10))/5); 
            if (!$beta) $dk = round($dk * 0.25, 0); 
            else $dk = round($dk,0); 

            $atkflux = e_rand(0, $dk); 
            if ($beta) $atkflux = min($atkflux, round($dk/4)); 
            $defflux = e_rand(0, ($dk-$atkflux)); 
            if ($beta) $defflux = min($defflux, round($dk/4)); 
            $hpflux = ($dk - ($atkflux+$defflux)) * 5; 
            $badguy['creatureattack']+=$atkflux; 
            $badguy['creaturedefense']+=$defflux; 
            $badguy['creaturehealth']+=$hpflux; 
            if ($beta) { 
                $badguy['creaturedefense']*=0.66; 
                $badguy['creaturegold']*=(1+(.05*$dk)); 
                if ($session['user']['race']=='vmp') $badguy['creaturegold']*=1.1; 
            } else { 
                if ($session['user']['race']=='vmp') $badguy['creaturegold']*=1.2; 
            } 
            $badguy['diddamage']=0; 
            $session['user']['badguy']=createstring($badguy); 
            if ($beta) { 
                if ($session['user']['superuser']>=0){ 
                    output("Debug: $dk dragon points.`n"); 
                    output("Debug: +$atkflux attack.`n"); 
                    output("Debug: +$defflux defense.`n"); 
                    output("Debug: +$hpflux health.`n"); 
                        $session[user][specialinc]="sacrificealter.php"; 
                }  
            } 
    }elseif ($_GET[type]=="Edelstein"){ 
    switch(e_rand(1,2)){ 
        case 1: 
        output("`#Du legst einen deiner hart verdienten Edelsteine auf den Altar und wartest ab was passiert.
        Aber es passiert nichts, gar nichts. Du bist natürlich schlau und versuchst ein paar Tricks wie
        im Busch verstecken, eine Art Regentanz, zu Edelstein und Altar sprechen, beten und Purzelbäume schlagen,
        aber trotz deiner Bemühungen... es passiert nichts.`nAlso beschließt du den Edelstein wieder mitzunehmen
        und stattdessen ein paar Monster zu töten.
        `nDu verlierst einen Waldkampf wegen deiner versuchten Tricks."); 
        $session[user][turns]--; 
//        addnav("`nZum Altar zurückkehren","forest.php?op="); 
        break; 
        case 2: 
        output("`#Du legst einen deiner hart verdienten Edelsteine auf den Altar und als du ihn aus den Fingern lässt
         ist der Edelstein verschwunden!`n
         Du wartest ob etwas passiert, aber es passiert nichts. Du wirst wütend wegen deiner Dummheit und erhältst einen Waldkampf!"); 
//        addnav("`nZum Altar zurückkehren","forest.php?op="); 
        $session[user][turns]++; 
        $session[user][gems]--; 
        $session[user][donation]+=1; 
        break; 
        } 
    }elseif ($_GET[type]=="Flowers"){ 
        if (!$_GET[flower]){ 
            $session[user][turns]--; 
            output("`@Du suchst im Wald nach wilden Blumen, bis du auf eine Wiese mit verschiedenen Blumen gelangst.
             Dort sind`$ Rosen`@, `&Gänseblümchen`@, und `^Löwenzahn`@.`n Welche möchtest du opfern?"); 
            addnav("R?Opfere Rosen","forest.php?op=Sacrifice&type=Flowers&flower=Roses"); 
            addnav("G?Opfere Gänseblümchen","forest.php?op=Sacrifice&type=Flowers&flower=Daisies"); 
            addnav("L?Opfere Löwenzahn","forest.php?op=Sacrifice&type=Flowers&flower=Dandelions"); 
            addnav("`nZum Altar zurückkehren","forest.php?op="); 
            $session[user][specialinc]=$specialbat; 
        }else{ 
            if ($_GET[flower]=="Roses"){ 
                output("`@Du legst die Rosen als Opfergabe auf den Altar. Du senkst deinen Kopf um ein Gebet an die Götter zu richten,
                du bittest sie deine Opfergabe anzunehmen. Als du deinen Kopf wieder anhebst um auf den Altar zu schauen, "); 
                switch(e_rand(1,7)){ 
                    case 1: 
                        output("siehst du einen `^wütenden Hasen`@! Du dachtest doch nicht dass Götter, die einen
                         blutverschmierten Altar haben, wirklich eine Opfergabe bestehend aus Blumen akzeptieren würden?
                         Wirklich, wer würde so etwas denken? Jetzt wirst du deinen Tod finden, welcher dich
                         mit großen und scharfen Zähnen erwartet!
                         `n`n`^Du wurdest getötet von einem `\$wütenden Hasen`^!`n
                         Du verlierst all dein Gold!`n
                         Du verlierst 10% deiner Erfahrung!
                         Du kannst morgen wieder weiterspielen."); 
                        killplayer(100,10,0,'');
                        $session[user][donation]+=1; 
                        addnav("Tägliche News","news.php"); 
                        if (strtolower(substr($session[user][name],-1))=="s") addnews($session[user][name]."'s Körper wurde gefunden... angeknabbert von Hasen!"); 
                        else addnews($session[user][name]."'s Körper wurde gefunden... angeknabbert von Hasen!"); 
                        break; 
                    case 2: 
                    case 3: 
                    case 4: 
                        output("siehst du eine wunderschöne Frau vor dir stehen.`n`n
                         \"`#".($session[user][sex]?"Meine geliebte Tochter":"Mein geliebter Sohn").",`@\" sagt sie, \"
                         `# ich danke Dir für das Geschenk der Rosen. Ich weiss, dass Du ein hartes Leben hinter Dir hast,
                         also erhältst du ein Geschenk von mir.`@\"`n`n
                         Sie legt ihre Hand auf deinen Kopf und du fühlst ein warmes Gefühl durch deinen Körper gleiten.
                         Als sie ihre Hand von deinem Kopf nimmt, sagt sie dir, dass du in die Wasserpfütze beim Altar schauen sollst.
                         Du gehst zur Wasserpfütze und schaust hinein. Du bemerkst, dass du ein wenig ".($session[user][sex]?'schöner':'angenehmer'));  
                        output(" aussiehst als zuvor. Du gehst zum Altar zurück und bemerkst, dass die Göttin verschwunden ist. Wie war wohl ihr Name?"); 
                        output("`n`n`^Du erhältst 1 Charmepunkt!"); 
                        $session[user][charm]++; 
                        break; 
                    case 5: 
                    case 6: 
                    case 7: 
                        output("siehst du eine wunderschöne Frau vor dir stehen.`n`n
                         \"`#".($session[user][sex]?"Meine geliebte Tochter":"Mein geliebter Sohn").",`@\" sagt sie, \"
                         `# ich danke Dir für das Geschenk der Rosen. Ich weiss, dass Du ein hartes Leben hinter Dir hast,
                         also erhältst du ein Geschenk von uns.`@\"`n`n
                         Sie sagt dir, dass du in die Wasserpfütze beim Altar schauen sollst. Du gehst zur Wasserpfütze
                         und schaust hinein. Du siehst etwas funkelndes im Wasser! Du schaust zurück zum Altar und bemerkst,
                         dass die Göttin verschwunden ist. Wie war wohl ihr Name?
                         `n`n`^Du hast `%ZWEI`^ Edelsteine gefunden!"); 
                        $session[user][gems]+=2; 
                        break; 
                 
                } 
            } 
            elseif ($_GET[flower]=="Daisies"){ 
                output("`@Du legst die Gänseblümchen als Opfergabe auf den Altar. Du senkst deinen Kopf zum Gebet an die Götter und bittest sie
                 die Opfergabe anzunehmen. Als du deinen Kopf hebst und zum Altar schaust "); 
                switch(e_rand(1,12)){ 
                    case 1: 
                        output("siehst du wie sich die Gänseblümchen in eine `^Riesen Venus Fliegenfalle`@, verwandeln,
                         mit dem Unterschied, dass diese keine Fliegen fängt. Bevor du fliehen kannst, oder deine Waffe
                         in die Hand nimmst, hat dich die Pflanze bereits mit ihrem Maul verschlungen. Du bist nun dabei,
                         in den nächsten 100 Jahren langsam verdaut zu werden. Denk über deine Fehler nach, genug Zeit dafür hast du nun...
                         `n`n`^Du wurdest gefressen von einer `\$Riesen Venus Fliegenfalle`^!`n
                         Du verlierst all dein Gold!`n
                         Du verlierst 10% deiner Erfahrung!
                         Du kannst morgen wieder weiterspielen."); 
                        killplayer(100,10,0,'');
                        addnav("Tägliche News","news.php"); 
                        $session['user']['donation']+=1; 
                        if (strtolower(substr($session[user][name],-1))=="s") addnews($session[user][name]."'s Waffen wurden bei einer Riesenpflanze gefunden, aber mehr konnte nicht herausgefunden werden."); 
                        else addnews($session[user][name]."'s Waffen wurden bei einer Riesenpflanze gefunden, aber mehr konnte nicht herausgefunden werden."); 
                        break; 
                    case 2: 
                    case 3: 
                    case 4: 
                    case 5: 
                    case 6: 
                        output("siehst Du ein junges Mädchen, welches auf dem Altar sitzt und die Gänseblümchen in der Händen hält.`n`n
                         `#Er liebt mich, er liebt mich nicht. Er liebt mich, er liebt mich nicht,`@\" sagt sie während sie die
                         Blumenblätter abrupft. Du starrst sie bewundernd an, bis sie das letzte Blumenblatt rupft.`n`n"); 
                         
                        if (e_rand(0,1)==0){ 
                            output("\"`#Er liebt mich nicht. Was?!`@\" schreit sie laut und fängt an zu weinen. Sie hüpft vom Altar und rennt
                             dicht an dir vorbei in den Wald. Du fühlst dich weniger charmant.`n`n
                             `^Du verlierst 1 Charmepunkt!"); 
                            $session[user][charm]--; 
                        }else{ 
                            output("\"`#Er liebt mich. Juchu! Er liebt mich, er liebt mich!`@\" sagt sie und hüpft auf und ab.
                             Sie springt vom Altar und rennt dicht an dir vorbei in den Wald. Du fühlst dich nach der Freude
                             des Mädchens charmanter.`n`n
                             `^Du erhältst 1 Charmepunkt!"); 
                            $session[user][charm]++; 
                        } 
                        break; 
                    case 7: 
                    case 8: 
                    case 9: 
                    case 10: 
                    case 11: 
                    case 12: 
                        $reward=e_rand($session[user][experience]*0.025+10, $session[user][experience]*0.1+10); 
                        output("siehst du eine wunderschöne Frau in deiner Nähe.`n`n
                         \"`#".($session[user][sex]?"Meine Tochter":"Mein Sohn").",`@\" sagt sie, \"`#Ich danke Dir für das Geschenk. Ich
                         weiß Du hattest ein hartes Leben bisher, darum erhältst Du ein Geschenk von uns.`@\"`n`n
                         Sie gibt dir etwas, das wie ein leckerer Brotlaib aussieht und motiviert dich es zu essen.
                         Da du nicht unhöflich sein willst nimmst du das Brot in den Mund und ißt es.
                         Auf einmal fühlst du dich so als ob sich mehr Wissen in deinem Gedächtnis breitgemacht hat.
                         Du schliesst kurz deine Augen und als du sie wieder öffnest ist die Göttin verschwunden. Wie war wohl ihr Name?"); 
                        output("`n`n`^Du erhältst $reward Erfahrungspunkte!"); 
                        output(""); 
                        $session[user][experience]+=$reward; 
                        break; 
                    } 
                 
                }elseif ($_GET[flower]=="Dandelions"){ 
                output("`@Du legst den Löwenzahn auf den Opferaltar. Du senkst den Kopf zum Gebet an die Götter, mit der Hoffnung, "); 
                output("dass sie dein Geschenk akzeptieren. Als du deinen Kopf wieder anhebst schaust du auf den Altar und "); 
                switch(e_rand(1,5)){ 
                    case 1: 
                        output("siehst eine Göttin, die mißbilligend auf dein Geschenk schaut. Plötzlich dreht sie sich zu dir
                         und ihre Wut bricht aus. Sie geht voller Zorn auf dich zu!
                         `n`n\"`#A `iUnkraut`i!! Du schenkst `iUnkraut`i an die mächtigen Götter! Wurm! Du verdienst es nicht einmal
                         zu leben!`@\" sagt sie und schleudert dann Feuerbälle auf dich.`n`n
                         Der erste durchwandert dich einfach, verwandelt deinen Oberkörper in Asche und deine Arme, Beine und dein Kopf
                         sterben langsam ab. Als dein Kopf auf den Boden fällt und rollt, tritt die Göttin diesen mit ihrem Fuß,
                         nimmt diesen dann auf und schaut in deine Augen. `n`n
                         \"`#Nun, ".$session[user][name].", ich denke Du hast Deine Lektion gelernt. Störe die Götter nie wieder
                         mit solchen Kleinigkeiten.`@\"
                         `n`nAls dein Geist in die Schatten abtaucht, denkst du noch \"`&Sie irren sich, ich denke nicht, 
                         dass es der Gedanke ist der zählt...`@\"`n`n
                         `^Du bist tot!`n
                         Du verlierst all dein Gold!`n
                         Diese Lektion hat dir mehr Erfahrung eingebracht als du verlieren könntest."); 
                        killplayer(100,0,0,''); 
                        addnav("Tägliche News","news.php"); 
                        if (strtolower(substr($session[user][name],-1))=="s") addnews($session[user][name]."'s Kopf wurde gefunden... auf einem Speer in der Nähe eines Altars für die Götter."); 
                        else addnews($session[user][name]."'s Kopf wurde gefunden... auf einem Speer in der Nähe eines Altars für die Götter"); 
                        break; 
                    case 2: 
                    case 3: 
                    case 4: 
                    case 5: 
                        output("Dein Geschenk geht in Flammen auf. Feuer umgibt den Löwenzahn. Als die Flammen alles in Asche
                         verwandelt haben gehst du zu ihnen hin und entsorgst die Asche. `n"); 
                        switch(e_rand(1,3)){ 
                            case 1: 
                                output("`iDu findest dort nichts!`i Die Götter müssen dein Geschenk abgelehnt haben. Deine Hände sind
                                 ganz klebrig von dem ganzen Löwenzahn. Naja, es war ja nur Unkraut..."); 
                                break; 
                            case 2: 
                            case 3: 
                                output("`iDu findest einen Edelstein!!`i Die Götter müssen dein Geschenk angenommen haben. Deine Hände sind
                                 ganz klebrig von dem ganzen Löwenzahn, aber der Edelstein war es wert!"); 
                                output("`n`n`^ Du findest `%EINEN`^ Edelstein!"); 
                                $session[user][gems] +=1; 
                                break; 
                        } 
                } 
            } 
        } 
    }         
}elseif ($_GET[op]=="Leave"){ 
  output("`#Das ist ein heiliger Ort für Götter und Priester. Am besten machst du dich schnellstens wieder auf den Weg, bevor die Götter zornig werden, "); 
  output("weil du an ihrem heiligen Altar verweilst."); 
}elseif ($_GET[op]=="Won"){ 
    if ($_GET[Difficulty]=="Strong")$dif="Strong"; 
    if ($_GET[Difficulty]=="Moderate")$dif="Moderate"; 
    if ($_GET[Difficulty]=="Weak")$dif="Weak"; 
    output("`@Du trägst deinen Geschenk, `^".$badguyname."`@, zurück zum Altar. Du legst den toten Leichnahm auf den "); 
    output("Altar und führst das Blutritual durch. Als du dieses beendet hast "); 
    switch(e_rand(1,15)){ 
        case 1: 
            output("`i erwacht `^".$badguyname."`@ zu neuem Leben!`i Mit dem Unterschied das es nun Fangarme und Krallen besitzt und es sieht sehr hungrig aus. Dein Pech ist, du hast es bereits "); 
            output("getötet, weil du nichts töten kannst das bereits tot ist. Du hättest wissen müssen das die Götter "); 
            output("solche Opfer nicht annehmen. Das war `imenschliches`i Blut auf dem Altar.`n`nDie Götter wollen Blut und "); 
            output("sie bekommen es nun von dir, ob dir das nun gefällt oder nicht."); 
            output("`n`n`^Du bist tot!`n"); 
            output("Die Götter scheinen auch glänzendes gelbes Metall zu lieben, denn sie nahmen dir all dein Gold!`n"); 
            output("Du verlierst 5% deiner Erfahrung.`n"); 
            output("Du kannst morgen wieder weiterspielen."); 
            $session[user][alive]=false; 
            $session[user][hitpoints]=0; 
            $session[user][experience]*=0.95; 
            $session[user][gold] = 0; 
            addnav("Tägliche News","news.php"); 
            if (strtolower(substr($session[user][name],-1))=="s") addnews($session[user][name]."s Überreste waren nicht sehr schön als sie gefunden wurden..."); 
            else addnews($session[user][name]."'s Überreste waren nicht sehr schön als sie gefunden wurden..."); 
        break; 
        case 2: 
        case 3: 
        case 4: 
        case 5: 
            if ($dif=="Weak"){ 
                $reward = 1;  
                $rewardnum="EINEN`^ Edelstein"; 
            }     
            if ($dif=="Moderate"){ 
                $reward = 2;  
                $rewardnum="ZWEI`^ Edelsteine"; 
            } 
            if ($dif=="Strong"){ 
                $reward = 3;  
                $rewardnum="DREI`^ Edelsteine"; 
            } 
            output("sprichst du ein Gebet für den Geist des toten `^".$badguyname."`@ aus. Du drehst dich um umd wäscht deine Hände in "); 
            output("einer kleinen Pfütze beim Altar. Als du fertig bist, stehst du wieder auf und drehst dich wieder zum Altar. `i`^".$badguyname."`@ ist "); 
            output("verschwunden!`i An dessen Stelle ist nun ein Beutel. Du gehst hin und schaust in den Beutel hinein. Im Beutel findest du $reward Edelsteine! Die Götter "); 
            output("haben dein Opfer wohl akzeptiert und dich für deine Mühen entlohnt."); 
            output("`n`n`^Du findest `%".$rewardnum."!`n"); 
            $session[user][gems] +=$reward; 
            break; 
        case 6: 
        case 7: 
        case 8: 
            if ($dif=="Weak"){ 
                $reward = e_rand(10, 100); 
                $bag="small bag"; 
            } else if ($dif=="Strong"){ 
                $reward = e_rand(175, 300); 
                $bag="large bag"; 
            } else {
                $reward = e_rand(75, 200); 
                $bag="bag"; 
            } 
            output("sprichst du ein Gebet für den Geist des toten `^".$badguyname."`@ aus. Du drehst dich um umd wäscht deine Hände in "); 
            output("einer kleinen Pfütze beim Altar. Als du fertig bist, stehst du wieder auf und drehst dich wieder zum Altar. `i`^".$badguyname."`@ ist "); 
            output("verschwunden!`i An dessen Stelle ist nun ein Beutel. Du gehst hin und schaust in den Beutel hinein. Im Beutel findest du ".$reward." Gold! Die Götter "); 
            output("haben dein Opfer wohl akzeptiert und dich für deine Mühen entlohnt."); 
            output("`n`n`^Du findest $reward Gold!`n"); 
            $session[user][gold] += $reward; 
            break; 
        case 9: 
        case 10: 
        case 11: 
        case 12: 
            if ($dif=="Weak")$reward = 2; 
            if ($dif=="Moderate")$reward = 3; 
            if ($dif=="Strong")$reward = 4; 
            output("legst du deine Hand auf den toten Körper um zu beten, aber als deine Hand das Fleisch des "); 
            output("toten ".$badguyname." berührt, fühlst du dich von Energie durchflossen. Deine Schwäche wurde ausgesaugt und "); 
            output("Deine Müdigkeit besänftigt. Die Götter haben dir genug Stärke gegeben für weitere $reward Waldkämpfe!"); 
            output("`n`n`^Du erhältst weitere $reward Waldkämpfe!!"); 
            $session[user][turns]+=$reward; 
            break; 
        case 13: 
        case 14: 
            if ($dif=="Weak")$charmloss = 3; 
            if ($dif=="Moderate")$charmloss = 2; 
            if ($dif=="Strong")$charmloss = 1;                 
            output("fängt der Leichnahm an größer zu werden, als ob er mit Luft gefüllt wird! Er wird immer noch größer. Du bist zu überrascht um dich zu bewegen. "); 
            output("Letztlich explodiert `^".$badguyname."`@ und beschmutzt dich mit Blut und Überresten. Das Opfer muss wohl nicht genug gewesen sein "); 
            output("und du wurdest dafür bestraft.");  
            output("`n`n`^Du verlierst ".$charmloss." Charmepunkte!"); 
            $session[user][charm]-=$charmloss; 
            $session['user']['donation']+=$charmloss; 
            break; 
        case 15: 
            output("`\$färbt sich der Himmel rot. `@Du fürchtest dich davor das du die Götter verärgert hast und drehst dich um um den Ort zu verlassen. Gerade als du den Ort `n`n"); 
            output("verlassen willst, fällt ein Blitz vom Himmel und trifft dich. Du wirst zurückgeschleudert und "); 
            output("als du den Boden triffst, bist du bereits tot ".$session[user][weapon].". Es ist nicht gut den Göttern"); 
            output("zu wenig Respekt zu zollen und du fandest das auf dem harten Weg heraus.");  
            output("`n`n`^Du bist tot!`n"); 
            output("Du verlierst all dein Gold!`n"); 
            output("Du verlierst 10% deiner Erfahrung!`n"); 
            $session['user']['donation']+=1; 
            output("Du kannst morgen wieder weiterspielen."); 
            $session[user][alive]=false; 
            $session[user][hitpoints]=0; 
            $session[user][experience]*=0.9; 
            $session[user][gold] = 0; 
            addnav("Tägliche News","news.php"); 
            addnews("Der verkohlte Körper von ".$session[user][name]." wurde irgendwo im Wald gefunden."); 
            break;                                                 
    } 
} 
if ($_GET[op]=="run"){ 
     
    if (e_rand()%3 == 0){ 
        output ("`c`b`&Du bist erfolgreich vor deinem Feind geflohen!`0`b`c`n"); 
        $_GET[op]=""; 
        output("Du fliehst feige vor deiner Beute und hast dabei vergessen wo sich der Altar befindet. Du wirst möglicherweise nie mehr etwas opfern. "); 
        output("Denk immer daran, es ist alleine deine Schuld."); 
    }else{ 
        output("`c`b`\$Du konntest vor deinem Feind nicht fliehen!`0`b`c"); 
    } 
} 
         
if ($_GET[op]=="fight" || $_GET[op]=="run"){ 
    $battle=true; 
} 
if ($battle){ 
  include("battle.php"); 
    if ($victory){ 
    if (getsetting("dropmingold",0)){  
            $badguy[creaturegold]=e_rand($badguy[creaturegold]/4,3*$badguy[creaturegold]/4); 
        }else{ 
            $badguy[creaturegold]=e_rand(0,$badguy[creaturegold]); 
        } 
        $expbonus = round( 
            ($badguy[creatureexp] * 
                (1 + .25 * 
                    ($badguy[creaturelevel]-$session[user][level]) 
                ) 
            ) - $badguy[creatureexp],0 
        ); 
        output("`b`&$badguy[creaturelose]`0`b`n");  
        output("`b`\$Du hast $badguy[creaturename] getötet!`0`b`n"); 
        output("`#Du erhältst `^$badguy[creaturegold]`# Gold!`n");  
        if ($badguy['creaturegold']) {                                 
            //debuglog("erhielt {$badguy['creaturegold']} Gold für das Töten eines Monsters."); 
        } 
        if (e_rand(1,25) == 1) { 
          output("`&Du findest einen Edelstein!`n`#"); 
          $session['user']['gems']++; 
          //debuglog("fand einen Edelstein beim Monster."); 
        } 
        if ($expbonus>0){ 
          output("`#***Weil der Kampf schwieriger war, erhälst du zusätzliche `^$expbonus`# Erfahrungspunkte! `n($badguy[creatureexp] + ".abs($expbonus)." = ".($badguy[creatureexp]+$expbonus).") "); 
          $dif="Strong"; 
        }else if ($expbonus<0){ 
          output("`#***Weil der Kampf so leicht war, werden dir `^".abs($expbonus)."`# Erfahrungspunkte abgezogen! `n($badguy[creatureexp] - ".abs($expbonus)." = ".($badguy[creatureexp]+$expbonus).") "); 
          $dif="Weak"; 
        } 
        output("Du erhältst insgesamt `^".($badguy[creatureexp]+$expbonus)."`# Erfahrungspunkte!`n`0"); 
        $session[user][gold]+=$badguy[creaturegold]; 
        $session[user][experience]+=($badguy[creatureexp]+$expbonus); 
        $creaturelevel = $badguy[creaturelevel]; 
        $_GET[op]=""; 
        //if ($session[user][hitpoints] == $session[user][maxhitpoints]){ 
        if ($badguy['diddamage']!=1){ 
            if ($session[user][level]>=getsetting("lowslumlevel",4) || $session[user][level]<=$creaturelevel){ 
                output("`b`c`&~~ Flawless Fight! ~~`\$`n`bDu erhälst einen Extra-Waldkampf!`c`0`n"); 
                $session[user][turns]++; 
            }else{ 
                output("`b`c`&~~ Unglaublicher Kampf! ~~`b`\$`nEin schwierigerer Kampf hätte dir einen Extra-Waldkampf eingebracht.`c`n`0"); 
            } 
        } 
        $dontdisplayforestmessage=true; 
        addhistory(($badguy['playerstarthp']-$session['user']['hitpoints'])/max($session['user']['maxhitpoints'],$badguy['playerstarthp'])); 
        $badguyname=$badguy['creaturename']; 
        $badguy=array(); 
//    Add victory possiblilities below: 
        addnav("Zum Altar zurückkehren","forest.php?op=Won&Difficulty=$dif&badguyname=$badguyname"); 
            $session[user][specialinc]=$specialbat; 
//    End of Victory Possibilities,         
    }elseif ($defeat){ 
        addnav("Tägliche News","news.php"); 
        $sql = "SELECT taunt FROM taunts ORDER BY rand(".e_rand().") LIMIT 1"; 
        $result = db_query($sql) or die(db_error(LINK)); 
        $taunt = db_fetch_assoc($result); 
        $taunt = str_replace("%s",($session[user][sex]?"her":"him"),$taunt[taunt]); 
        $taunt = str_replace("%o",($session[user][sex]?"she":"he"),$taunt); 
        $taunt = str_replace("%p",($session[user][sex]?"her":"his"),$taunt); 
        $taunt = str_replace("%x",($session[user][weapon]),$taunt); 
        $taunt = str_replace("%X",$badguy[creatureweapon],$taunt); 
        $taunt = str_replace("%W",$badguy[creaturename],$taunt); 
        $taunt = str_replace("%w",$session[user][name],$taunt); 
        addhistory(1); 
        addnews("`%".$session[user][name]."`5 wurde im Wald getötet von $badguy[creaturename]`n$taunt"); 
        $session[user][alive]=false; 
        //debuglog("verloren {$session['user']['gold']} Gold als sie im Wald getötet wurden"); 
        $session[user][gold]=0; 
        $session[user][hitpoints]=0; 
        $session[user][experience]=round($session[user][experience]*.9,0); 
        $session[user][badguy]=""; 
        output("`b`&Du wurdest getötet von `%$badguy[creaturename]`&!!!`n"); 
        output("`4Du hast all dein Gold verloren!`n"); 
        output("`410% deiner Erfahrung ging verloren!`n"); 
        output("Du kannst morgen wieder weiterspielen."); 
             
        page_footer(); 
    }else{ 
      $session[user][specialinc]=$specialbat;    //    Sets the specialinc field to either "" or "somespecialfile.php"
	fightnav(true,true);
    } 
} 

?>
