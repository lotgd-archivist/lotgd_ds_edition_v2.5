<?php

//Based on barber.php by: UnderDark
//�bersetzt und f�r Atrahor angepasst von Salator

require_once("common.php");
page_header('Nimbus, der Barbier');

output('`c`b`#Nimbus, der Barbier`b`c`n`n');

//hier kommt eine Begrenzung auf 1 Besuch pro tag rein. das koppeln wir fieserweise an gotfreeale
//if (session besuch true)
//spieglein spieglein...
//output ab 100 CP:(ihr seid der/die sch�nste im ganzen land. aber (bestaussehender) �ber den sieben bergen bei den sieben zwergen ist (1000/100/10)mal sch�ner als ihr)
//unter 100CP: geh beiseite, ich seh nix!
switch($_GET['op']){
    case '':
        output('`^Durch das Fenster siehst du Nimbus, den besten Barbier '.getsetting('townname','Atrahor').'s, welcher seiner Arbeit nachgeht. Du h�rst wie sich seine Kunden ob ihres neuen Aussehens bei ihm bedanken, doch Nimbus zeigt sich davon unbeeindruckt.`n`n
Ein Blick auf die Preisliste verr�t dir:`nHaareschneiden: 5000 Gold`nRasieren: 2000 Gold`nHaare spenden: 50 Charmepunkte, +500 Gold `n`~Regressanspr�che sind ausgeschlossen.');
	  break;
    case 'cut':
        if($session['user']['gold']<0)
           {
                output('`$ES denkt wohl, ich schneide jedem dahergelaufenen Taugenichts die Haare!? Komme ES wieder wenn ES sich meinen Service leisten kann!');
           }
           else
           {
                output('`@ Nimbus nimmt dein Gold und macht sich sogleich �ber dein Haar her, w�hrend er dir die neuesten Ereignisse im Dorf erz�hlt.`n`nAls er wenig sp�ter mit seiner Arbeit fertig ist betrachtest du dich im Spiegel.`n');
                $c = e_rand(1,7);
                $r = e_rand(1,3);
                if ($r == 2) $c*=-1;
                if ($c < 0)
                {
                    $t = abs($c);
                    $what=array('dummy','Gr�bchen','Pickel','Fettbacken','Warzen','Falten','grauen Str�hnen','vampirartigen Z�hne');
                    output('`&Das soll also der neueste Schrei der Mode sein... Vielleicht f�r andere, bei dir jedoch werden die `$'.$what[$t].'`& besonders hervorgehoben. Du `$verlierst '.$t.' Charmepunkte`&. Nimbus entschuldigt sich wortreich, weist dich aber auf einen kleinen Zusatz seiner Preisliste hin.`nDein Gold bekommst du also auch nicht zur�ck...');
                }
                else
                {
                    output('`%Du bist erfreut als du deinen neuen Haarschnitt erblickst. Hervorragende Arbeit!`nDu bekommst `&'.$c.'`% Charmepunkte.');
                }
                $session['user']['gold'] -= 5000;
                $session['user']['charm'] += $c;
        }
        break;
    case 'raze':
//oh oh
        break;
    case 'donate':
        if($session[user][charm] <= 50)
            {
                output('`n`nNimbus untersucht eine Haarstr�hne von dir und sagt `6"Wei�t du, eine tote Ratte liefert besseres Material."');
            }
            else{
			   output('`@Nimbus schneidet dir eine Glatze.`nDu verlierst zwar `$50 Charmpunkte`@, bekommst aber `^500`@ Gold!');
				 $session[user][charm]-=50;
				 $session[user][gold] += 500;
			}
        break;
    default:
        output('`& Nimbus schaut dich an und sch�ttelt den Kopf. Da ist wohl nichts mehr zu machen...');
        break;
}

addnav(Friseursalon);
addnav('Haare schneiden','barber.php?op=cut');
addnav('Rasieren','barber.php?op=raze');
addnav('Haare spenden','barber.php?op=donate');
addnav('Zur�ck');
addnav('M?zum Markt','market.php');

page_footer();
?>
