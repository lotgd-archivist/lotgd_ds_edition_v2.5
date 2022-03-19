<?
//////////////////
// Der Angelsee //
//////////////////

// 04.09.06: Neugeschrieben und extrem korrigiert by Maris (Maraxxus@gmx.de)

// Basiert auf der Idee von:
/*********************************************
Lots of Code from: lonnyl69 - Big thanks for the help.
By: Kevin Hatfield - Arune v1.0
06-19-04 - Public Release
Written for Fishing Add-On - Poseidon Pool
Translation and simple modifications by deZent deZent@onetimepad.de
*********************************************/

require_once "common.php";
checkday();
addcommentary();
music_set('waldsee');
page_header("Der magische See");

$sql = "SELECT worms,minnows,boatcoupons,fishturn FROM account_extra_info WHERE acctid=".$session['user']['acctid'];
$result = db_query($sql) or die(db_error(LINK));
$rowf = db_fetch_assoc($result);
$minnows=$rowf['minnows'];
$worms=$rowf['worms'];
$boatcoupons=$rowf['boatcoupons'];
$fishturn=$rowf['fishturn'];

/*******************
Minnows
*******************/

function check1(){
    global $session;
    global $minnows;
    global $worms;
    global $boatcoupons;
    global $fishturn;
    if($fishturn<0)
    {
        output("... oder lieber doch nicht. F�r heute hast du vom Angeln definitiv genug!");
        return;
    }
    elseif($minnows<0)
    {
        output("Kurze Zeit sp�ter siehst du ein, dass sich die Fische nicht mit einem blanken Haken zufrieden geben.");
        return;
    }
    elseif($_GET['su_action'])
    {
	   $int_decide = $_GET['su_action'];
    }
    else
    {
        $int_decide = e_rand(1,50);
    }

switch ($int_decide){

    // Boot (0)
    case 1:
    case 2:
    output("Ein Boot zieht an die vor�ber - wie romantisch!`nAber leider werden dadurch auch die Fische aufgescheucht und du f�ngst nichts.");
    break;

    // Goldfund (+)
    case 3:
    case 4:
    $a=e_rand(2,75);
    output("`@Du f�ngst einen kleinen Beutel... `n`nDarin findest du `^$a`^ Gold!");
    $session['user']['gold']+= $a;
    break;

    // Verletzung (-)
    case 5:
    $b=e_rand(10,100);
    output("Beim Auswerfen verf�ngt sich der Angelhaken in deinem Ohr!!!! `n`nDu verlierst `^$b`^ Lebenspunkte.");
    $session['user']['hitpoints'] -= $b;
    if ($session['user']['hitpoints']<=0)
    {
        $session['user']['hitpoints']=1;
        output("`n`n`\$ Ramius akzeptiert deinen j�mmerlichen Anglertod nicht!`n Er gibt dir einen Lebenspunkt, da er sein Schattenreich nicht mit unf�higen Weicheiern f�llen m�chte!");
        output("`n`n`!So ein gef�hrlicher See!`!`n`4Du entscheidest dich heute lieber nicht mehr zu angeln...");
        $fishturn=0;
    }

    break;

    // Nix wars (0)
    case 6:
    output("Mit all deinem K�nnen hast du nichts gefangen!");
    break;

    // Stiefel (0)
    case 7:
    case 8:
    output("`@Du bist dir sicher, dass du einen schweren Fisch am Haken hast!!!`n`n`@.........`n`@Leider war es doch nur ein alter, vergammelter Stiefel");
    break;

    // Verletzung (-)
    case 9:
    output("`2Dein Haken verf�ngt sich in deiner Hand!! `nDu verlierst 12 Lebenspunkte.");
    $session['user']['hitpoints']-=12;
    if ($session['user']['hitpoints']<=0)
    {
        $session['user']['hitpoints']=1;
        output("`\$ Ramius akzeptiert deinen j�mmerlichen Anglertod nicht!`n Er gibt dir einen Lebenspunkt, da er sein Schattenreich nicht mit unf�higen Weicheiern f�llen m�chte!");
    }
    break;

    // Eingeschlafen (0)
    case 10:
    output("Leider bist du beim Fischen eingeschlafen und hast nicht mitbekommen ob etwas angebissen hat!");
    break;

    // 1 Edelstein (+)
    case 11:
    output("Gerade als du deine Leine einholst siehst du im feuchten Gras etwas schimmern.....`n`n`^`bDu findest einen Edelstein !!! `^`b");
    $session['user']['gems']+=1;
    break;

    // 3 W�rmer (+)
    case 12:
    case 13:
    output("`@Du f�ngst etwas... `n`n`!Ein alter Schinken h�ngt an deinem Angelhaken...`n`n`&`bDarin findest du 3 W�rmer!`b");
    addnav("W�rmer behalten?");
    addnav("In den Beutel!","fish.php?op=wormsplus&wp=3");
    break;

    // Silberkreuz (++)
    case 14:
    output("`!Du f�ngst ein seltsames Silberkreuz! `n`n`7Als du es vom Haken nimmst beginnt es leicht zu leuchten`nEin pulsierendes Leuchten erhellt das Ufer!!!");
    if(strchr($session['user']['weapon'],"gl�hend"))
    {
        output("`n`n`b`4Deine Waffe gl�ht bereits...`b");
    }
    else
    {
        output(" Du f�hlst dich st�rker und auch etwas z�her!`nDeine Verteidigung erh�ht sich um `#einen`7 Punkt.`nDeine Waffe wird um `#einen`7 Punkt st�rker.`nDeine Lebenspunkte erh�hen sich permanent um `#einen`7 Punkt.");
        debuglog("Weapon - Glowing enhancement from pool");
        $session['user']['maxhitpoints']+=1;
        $session['user']['defence']+=1;
        $newweapon = "gl�hend - ".$session['user']['weapon'];
        $atk = $session['user']['weapondmg']+2;
        item_set_weapon($newweapon, $atk, -1, 0, 0, 1);
    }
    break;

    // schwere Verletzung (-)
    case 15:
    output("`4Der Wind erfasst deine Angelschnur und wickelt sie um deinen Hals... Der Haken verf�ngt sich in deinem Mund!`n`n`3In Panik ziehst du an deiner Angel!`n`7Dabei ziehst du die Schlinge noch fester zu und f�llst auf den Boden!`n`\$ Ramius akzeptiert deinen j�mmerlichen Anglertod nicht!`n Er gibt dir einen Lebenspunkt, da er sein Schattenreich nicht mit unf�higen Weicheiern f�llen m�chte!");
    $fishturn=0;
    $session['user']['hitpoints']=1;
    break;

    // Fliege weg (0)
    case 16:
    case 17:
    output("`3Deine Fliege ist dir vom Haken geh�pft und freut sich ihres Lebens... `n`n `\$ Seit wann k�nnen Fliegen springen?!?!");
    break;

    // Nix wars (0)
    case 18:
    output("Du hast nichts gefangen!");
    break;

    // Charmeverlust (-)
    case 19:
    case 20:
    output("`7Du rutschst aus und f�llst ins Wasser !`nDa du nicht gut schwimmen kannst, kannst du dich gerade noch an Land retten.`n`^Durch diese peinliche Vorstellung verlierst du 2 Charmpunkte!");
    $session['user']['charm']-=2;
    break;

    // Charmegewinn (+)
    case 21:
    output("`3Du hast Mitleid mit der Fliege und schenkst ihr die Freiheit!`3 `nDabei f�hlst du dich sehr gut und erh�ltst `#einen`V Charmepunkt.");
    $session['user']['charm']+=1;
    break;

    // Volle LP (+)
    case 22:
    case 23:
    output("Du f�ngst einen enormen Barsch!`n`n`7Da du eh Hunger hast isst du ihn noch am See.");
    $session['user']['hitpoints']=$session['user']['maxhitpoints'];
    break;

    // Alle K�der weg (-)
    case 24:
    output("`@Du sp�rst einen Ruck an der Angel!`n`n`6Du ziehst mit einem Ruck...stolperst zur�ck und st�sst deinen Beutel mit K�dern ins Wasser.`n`4Du verlierst alle deine K�der!");
    $minnows=0;
    $worms=0;
    break;

    // Verletzung (-)
    case 25:
    case 26:
    output("`@Du sp�rst einen Ruck an der Angel!`n`nDu springst zur�ck und zerrst mit all deiner Kraft an der Rute!`n`7ZUVIEL f�r deine Rute! Sie bricht und schl�gt dir ins Gesicht!`n`4AUTSCH! Direkt ins Auge.... das hat weh getan!");
    $session['user']['hitpoints']-=75;
    if ($session['user']['hitpoints']<=0)
    {
        $session['user']['hitpoints']=1;
        output("`n`n`\$Ramius akzeptiert deinen j�mmerlichen Anglertod nicht!`n Er gibt dir einen Lebenspunkt, da er sein Schattenreich nicht mit unf�higen Weicheiern f�llen m�chte!");
    }
    break;

    // Fluch (--)
    case 27:
    $earn=e_rand(150,600);
    output("`2Du ziehst eine verfaulte Wasserleiche an Land! `2`n`n`7...`nNach kurzem �berlegen untersuchst du ihren Goldbeutel,`n`^und findest ".$earn." Gold!`n`n`2 Die Seejungfrau des Sees findet deine Aktion jedoch nicht sehr nett und verflucht dich!`n`^ Du verlierst `4einen`^ Punkt Angriff und `4einen`^ Punkt Verteidigung.`nAu�erdem darfst du heute nicht mehr angeln!");
    $fishturn=0;
    $session['user']['gold']+=$earn;
    $session['user']['attack']-=1;
    if ($session['user']['attack']<=0)
    {
        $session['user']['attack']=1;
    }
    $session['user']['defence']-=1;
    if ($session['user']['defence']<=0)
    {
        $session['user']['defence']=1;
    }
    break;

    // Erfahrung (+)
    case 28:
    case 29:
    case 30:
    output("Du f�ngst leider nichts!`n`n Eine Erfahrung mehr in deinem Leben..`n `\$ Du lernst, dass man nicht immer gewinnen kann und bekommst 100 Erfahrungspunkte.");
    $session['user']['experience']+=100;
    break;

    // 5 W�rmer
    case 31:
    output("`2Beim auswerfen der Leine siehst du eine Box mit W�rmern neben dir im Geb�sch!`2`n`n`^Du findest 5 W�rmer!");
    addnav("W�rmer behalten?");
    addnav("In den Beutel!","fish.php?op=wormsplus&wp=5");
    break;

    // 2 Edelsteine (+)
    case 32:
    output("Du f�ngst einen kleinen Lederbeutel!`n`n`^Darin findest du 2 Edelsteine!");
    $session['user']['gems']+=2;
    break;

    // Nix (0)
    case 33:
    case 34:
    output("`2Du siehst eine kleine Welle, die sich sehr schnell auf deinen K�der zubewegt!`n`n`\$ZU`2 schnell f�r deinen Geschmack!`n");
    output("Sicherheitshalber ziehst du deine Angel schnell wieder ein!");
    break;

    // Kleine Verletzung (-)
    case 35:
    case 36:
    output("Ein kleiner Goldfisch springt ans Ufer und bei�t dir in den Zeh!`n AUTSCH!");
    $session['user']['hitpoints']-=5;
    if ($session['user']['hitpoints']<=0)
    {
        $session['user']['hitpoints']=1;
        output("`\$ Ramius akzeptiert deinen j�mmerlichen Anglertod nicht!`n Er gibt dir einen Lebenspunkt, da er sein Schattenreich nicht mit unf�higen Weicheiern f�llen m�chte! ");
    }
    break;

    // At-Bonus (+)
    case 37:
    output("Du triffst genau ins Zentrum des Sees!`n`n Ein Blitz durchf�hrt deinen K�rper`nDie G�tter meinen es heute gut mit dir!`^Du f�hlst dich st�rker! Dein Angriff steigt um `#einen`V Punkt.");
    $session['user']['attack']++;
    break;

    // At-Abzug (-)
    case 38:
    output("Du f�ngst pr�chtigen Fisch, der in allen Farben des Regenbogens leuchtet!`nPech! Dieser Fisch war wohl einem Gott heilig, der dich nun f�r deinen Frevel straft!`nEin Blitz durchzuckt deinen K�rper und du f�hlst dich schw�cher!`n`^Du verlierst einen Angriffspunkt.");
    $session['user']['attack']--;
    if ($session['user']['attack']<=0)
    {
        $session['user']['attack']=1;
    }
    break;

    // Gold weg (-)
    case 39:
    output("`4Du stolperst �ber einen Stein und f�llst ins Wasser! `0!`n`nNat�rlich landest du an der seichtesten Stelle des Sees und knallst mit dem Kopf auf einen Stein`nAls du wieder aufwachst stellst du fest, dass dir jemand dein ganzes Gold gestohlen hat!");
    $session['user']['hitpoints']=1;
    $fishturn=0;
    $session['user']['gold']=0;
    break;

    // Nix (0)
    case 40:
    output("Du hast nichts gefangen!`n`n");
    break;

    // Wasserschrein (0)
    case 41:
    redirect ("watershrine.php");
    break;

    // Forelle (+)
    case 42:
    case 43:
    case 44:
    case 45:
    output("Du hast eine Forelle gefangen!`nDie wird bestimmt sehr gut schmecken. Bis zur Zubereitung packst du sie erstmal in dein Inventar.");
    $itemnew = item_get_tpl('tpl_id="fsh_frl"');
    if( is_array($itemnew) )
    {
        item_add( $session['user']['acctid'], 0, $itemnew);
    }
    break;

    // Nix (0)
    default:
    output("Mit all deinem K�nnen hast du nichts gefangen!");
    break;
}
db_query("UPDATE account_extra_info SET fishturn=$fishturn,minnows=$minnows,worms=$worms,boatcoupons=$boatcoupons WHERE acctid = ".$session['user']['acctid']);
}

/************************
Worms
************************/

function check2(){
    global $session;
    global $minnows;
    global $worms;
    global $boatcoupons;
    global $fishturn;
    global $fishdelete;
    if($fishturn<0)
    {
        output("... oder lieber doch nicht. F�r heute hast du vom Angeln definitiv genug!");
        return;
    }
    elseif($worms<0)
    {
        output("Du wirfst deinen unsichtbaren Wurm aus und f�ngst einen unsichtbaren, prachtvollen Fisch! Tja, sch�n w�rs...");
        return;
    }
    elseif($_GET['su_action'])
    {
	   $int_decide = $_GET['su_action'];
    }
    else
    {
        $int_decide = e_rand(1,50);
    }

switch ($int_decide)
{
    // Nichts (0)
    case 1:
    case 2:
    output("Du hast, wenn man es genauer betrachtet, NICHTS gefangen!");
    break;

    // Bauernkinder �rgern (0)
    case 3:
    case 4:
    case 5:
    $sql = "SELECT name FROM accounts WHERE dragonkills=0 AND ".user_get_online()." ORDER BY RAND() LIMIT 1";
        $result = db_query($sql) or die(db_error(LINK));
        $amount = db_num_rows($result);

        if ($amount>0)
        {
            $row=db_fetch_assoc($result);
            $name=$row['name'];
        }
        else
        {
            $name="jemand";
        }
    output("`7Du holst weit mit der Angel aus, sehr weit, sehr sehr weit...`n`n\"`4AUTSCH!!!`7\" h�rst du `&".$name." `7 lauthals rufen.`nSchnell l�sst du die Angel fallen und suchst dir einen anderen Platz.");
    break;

    // 3 Edelsteine (+)
    case 6:
    output("Du f�ngst einen schweren Lederbeutel...`nDarin findest du `n`^3 Edelsteine!");
    $session['user']['gems']+=3;
    break;

    // Charmebonus (+)
    case 7:
    output("Du f�ngst einen enormen Fisch!`nViele Fischer werden auf dich neidisch sein.`n`^Du bekommst 1 Charmpunkt!");
    $session['user']['charm']+=1;
    break;

    // Nix... (0)
    case 8:
    case 9:
    output("Deine Angelschnur ist gerissen!`nDu verlierst deinen K�der.");
    break;

    // 15 Fliegen (+)
    case 10:
    case 11:
    case 12:
    output("Als du deinen Haken einholst siehst du, dass du einen B�schel Seegras gefangen hast.`nDer B�schel stinkt so sehr, dass sofort `^15 Fliegen dran h�ngen.");
    addnav("Fliegen behalten?");
    addnav("In den Beutel!","fish.php?op=minnowsplus&mp=15");
    break;

    // Nix (0)
    case 13:
    case 14:
    output("Auch nach einer Stunde hast du noch nichts gefangen!");
    break;

    // Wieder nix (0)
    case 15:
    case 16:
    output("Du siehst jemanden hinter dem Geb�sch und rufst ihm laut `iHALLO!`i zu. `n In diesem Moment f�llt dir ein wie dumm das von dir war.... `n`n Nat�rlich wei�t du, dass f�r die n�chste Stunde alle Fische verscheucht hast!");
    break;

    // Immer noch nix (0)
    case 17:
    output("Du hast nichts gefangen!");
    break;

    // Nichts (0)
    case 18:
    output("Du hast den K�dern neben den See geworfen... Eine Stunde sp�ter bist du dir endlich sicher, dass man an Land keine Fische fangen kann...");
    break;

    // Nix (0)
    case 20:
    output("Du hast nichts gefangen!");
    break;

    // Verteidigungs-Bonus (+)
    case 21:
    output("Als du deine Leine einholst siehst du etwas Gl�hendes am Haken h�ngen`nEin schwacher Energiesto� trifft deinen K�rper`n`n`^Deine Verteidigung steigt um `#2`^ Punkte!");
    $session['user']['defence']+=2;
    break;

    // Kristall (++)
    case 22:
    output("`!Du f�ngst einen Kristall! `n`n`7Als du den Kristall in deiner Hand h�ltst..`nbeginnt das schwarze Wasser blau zu leuchten!!!`n`n");
    if(strchr($session['user']['weapon'],"geh�rtet"))
    {
        output("`b`4Deine Waffe ist bereits geh�rtet!`b");
    }
    else
    {
        output("Deine Waffe wird schwerer und irgendwie f�hlt sie sich m�chtiger an.`nDie St�rke deiner Waffe erh�ht sich um `#5`V Punkte!`n`7Deine Verteidigung steigt um `#3`V Punkte.`n`7Du erh�ltst 10 permanente Lebenspunkte dazu!");
        debuglog("Weapon - Crystalized enhancement from pool");
        $session['user']['maxhitpoints']+=10;
        $session['user']['defence']+=3;
        $newweapon = "geh�rtet - ".$session['user']['weapon'];
        $atk = $session['user']['weapondmg']+5;
        item_set_weapon($newweapon, $atk, -1, 0, 0, 1);
        $fishturn=0;
        addnews("`@".$session['user']['name']."`@ hat heute beim Angeln einen gro�en Fang gemacht!");
    }
    break;

    // Gro�er Fisch (0)
    case 23:
    case 24:
    case 25:
    output("Du f�ngst einen gigantischen Fisch!!`nZappelnd ziehst du ihn ans Ufer!`nAls du ihn mit all deinen Kr�ften an Land gezogen hast und feststellst, dass er nicht zur�ck ins Wasser will, sondern sich schnappend in deine Richtung bewegt, ziehst du schnell deine Waffe.!`nUnsicher stellst du dich dem Fisch..`n");
    if ($session['user']['attack']<35)
    {
        output("`4Gerade als du zustechen willst, packt dich der Fisch unerwartet am Fu� und zieht dich ins Wasser.`n`n Du wehrst dich mit all deiner Kraft, doch das pechschwarze Wasser raubt dir bereits den Blick zur Sonne. `n Der Fisch zieht dich immer weiter in die Tiefen des Sees...");
        $session['user']['experience']-=500;
        $session['user']['hitpoints']-=250;
        if ($session['user']['hitpoints']<=0)
        {
          $fishturn=0;
          addnav("Ramius wartet...");
          addnav("Na dann...","shades.php");
        }
    }
    else
    {
    $waffe1=$session['user']['weapon'];
    output("`!Der Fisch packt dich am Fu�, du nutzt deine Chance und erlegst ihn gekonnt mit deine(m) $waffe1 !`n`!Leider ist der Fisch zu schwer um ihn an Land zu ziehen - dennoch erh�ltst du 1000 Erfahrungspunkte.");
    $session['user']['experience']+=1000;
    }
    break;

    // Gold weg (-)
    case 26:
    case 27:
    output("Du bist beim Fischen eingeschlafen.... `nAls du wieder aufwachst stellst du fest, dass dein ganzes Gold verschwunden ist!");
    $session['user']['gold']=0;
    break;

    // 2 Edelsteine weg (-)
    case 28:
    output("Etwas zerrt an deiner Angel. Du verlierst das Gleichgewicht und stolperst mit einem Ruck nach vorn,`n");
    If ($session['user']['gems']>1)
    {
        output("wodurch sich dein Edelsteinbeutel �ffnet und du `42 Edelsteine verlierst!");
        $session['user']['gems']-=2;
    }
    else
    {
        output("kannst dich aber gerade noch so halten.");
    }
    break;

    // Charme-Bonus (+)
    case 29:
    case 30:
    output("Weit entfernt siehst du den Umriss einer Gestalt durch den dichten Nebel schimmern... Es k�nnte ".($session['user']['sex']?"ein Klabautermann ":"eine Seejungfrau ")."sein... `n`nEs ist ".($session['user']['sex']?"ein Klabautermann ":"eine Seejungfrau ")."!! `^ Du bekommst einen Charmpunkt!");
    $session['user']['charm']+=1;
    break;

    // Def-Abzug (-)
    case 31:
    output("Als du deine Leine einholst siehst du etwas bedrohlich Gl�hendes am Haken h�ngen`nEin schmerzhafter Energiesto� trifft deinen K�rper`n`n`^Deine Verteidigung sinkt um `4einen`^ Punkt!");
    $session['user']['defence']-=1;
    if ($session['user']['defence']<=0)
    {
        $session['user']['defence']=1;
    }
    break;

    // Anglertod (-)
    case 32:
    case 33:
    output("`4Der Wind erfasst deine Angelschnur und wickelt sie um deinen Hals... Der Haken verf�ngt sich in deinem Mund!`n`n");
    output("`3In Panik ziehst Du an deiner Angel!`n");
    output("`7Dabei ziehst Du die Schlinge noch fester zu und f�llst auf den Boden!");
    $session['user']['hitpoints']=0;
    $fishturn=0;
    addnav("�tsch, erwischt...");
    addnav("Na toll!","shades.php");
    break;

    // Gold weg (-)
    case 34:
    output("`0Du hast einen Beutel `^Gold`0 gefangen!`nGanz auf all das Gold fixiert z�hlst du die M�nzen!`n`4BOOM! `0Du wurdest von etwas stumpfen getroffen...Und gehst zu Boden!`n`n`iWieder einer auf den alten Goldbeuteltrick reingefallen`i h�rst du gerade noch als bei dir das Licht ausgeht!`nDein ganzes Gold ist nat�rlich auch weg...`nF�r heute wars das mit dem angeln!");
    $session['user']['hitpoints']=1;
    $fishturn=0;
    $session['user']['gold']=0;
    break;

    // Fluch der G�tter (--)
    case 35:
    output("Du denkst dir, dass es schon sehr unw�rdig f�r einen Krieger ist, in aller Ruhe zu angeln w�hrend der Drache sein Unwesen treibt.`nDiese Ansicht teilen auch die G�tter.`n`n`4Sie verfluchen dich! Dein Angriff und deine Verteidigung sinken um jeweils 2 Punkte!");
    addnews("`@".$session['user']['name']."`@ bekam heute beim Angeln von den G�ttern eine Lektion erteilt.");
    $session['user']['defence']-=2;
    if ($session['user']['defence']<=0)
    {
        $session['user']['defence']=1;
    }
    $session['user']['attack']-=2;
    if ($session['user']['attack']<=0)
    {
        $session['user']['attack']=1;
    }
    $fishturn=0;
    break;

    // Mei�el (++)
    case 36:
    output("`!Du hast einen Mei�el am Haken!`n`n`7Als du �ber die vielf�ltigen Einsatzgebiete eines Mei�els nachdenkst ber�hrst du versehntlich deine R�stung.`nWow..irgendwie passt deine R�stung jetzt viel besser als zuvor. Sie wirkt auch irgendwie stabiler!`n");
    if(strchr($session['user']['armor'],"verst�rkt"))
    {
        output("`n`b`4Leider war deine R�stung auch zuvor schon ver�ndert und du stellst fest, dass du dir das Ganze nur eingebildet hast!`b");
    }
    else
    {
        output(" Deine R�stung wurde verbessert! Vor lauter Freude wirfst du den Mei�el wieder in den See`nDeine R�stung wird um `#3`& Punkte st�rker!`n`nMit der neuen R�stung siehst du viel besser aus!`n`^Du bekommst 1 Charmpunkt!");
        debuglog("Armor - Chisel enhancement from pool");
        $newarmor = "verst�rkt ".$session['user']['armor'];
        $session['user']['charm']+=1;
        item_set_armor($newarmor, $session['user']['armordef']+3, -1, 0, 0, 1);
    }
    break;

    // Nix (0)
    case 37:
    output("Du hast nichts gefangen!");
    break;

    // Wasserschreib (0)
    case 38:
    redirect ("watershrine.php");
    break;

    // Joke (0)
    case 39:
    case 40:
    output("Du triffst ziemlich in die Mitte des Sees!`nEtwas schweres h�ngt an deinem Angelhaken... du ziehst... mit aller Kraft...`n`n`4PLOPP!`nDu hast den St�psel gezogen!`nDer komplette See, einschliesslich dir wird in den Abfluss gesogen!`nDeine Existenz ist ausgel�scht!`n");
    $fishdelete=1;
    addnav("Weiter","fish_delete.php");
    break;

    // Lachs (+)
    case 41:
    case 42:
    case 43:
    case 44:
    output("Du f�ngt einen pr�chtigen Lachs!`nIn deinem Inventar wird er gut aufgehoben sein.");
    $itemnew = item_get_tpl('tpl_id="fsh_lax"');
    if( is_array($itemnew) )
    {
        item_add( $session['user']['acctid'], 0, $itemnew);
    }
    break;

    // Topf (0)
    case 45:
    case 46:
    case 47:
    output("Du hast etwas schweres an der Angel!`nAch, leider nur ein verbeulter Topf... du nimmst ihn dennoch mit.");
    $itemnew = item_get_tpl('tpl_id="fsh_topf"');
    if( is_array($itemnew) )
    {
        item_add( $session['user']['acctid'], 0, $itemnew);
    }
    break;

    // Nix (0)
    default:
    output("Mit all deinem K�nnen hast du nichts gefangen!");
    break;
}
db_query("UPDATE account_extra_info SET fishturn=$fishturn,minnows=$minnows,worms=$worms,boatcoupons=$boatcoupons WHERE acctid = ".$session['user']['acctid']);
}

/*******************
Boat
*******************/

function check4(){
    global $session;
    global $minnows;
    global $worms;
    global $boatcoupons;
    global $fishturn;
    if($fishturn<0)
    {
        output("... oder lieber doch nicht. F�r heute hast du vom Angeln definitiv genug!");
        return;
    }
    elseif($boatcoupons<0)
    {
        output("Leider jedoch musst du vorher bezahlen um mit einem Boot rausfahren zu k�nnen!");
        return;
    }
    elseif($_GET['su_action'])
    {
	   $int_decide = $_GET['su_action'];
    }
    else
    {
        $int_decide = e_rand(1,50);
    }

switch ($int_decide){

    // Nix (0)
    case 1:
    case 2:
    case 3:
    output("Voller Vorfreude springst du ins Boot und greifst die Ruder.`nLeider machst du dabei so einen L�rm, dass alle Fische gewarnt sind und du nichts f�ngst!");
    break;

    // Wieder nix (0)
    case 4:
    case 5:
    case 6:
    output("Was f�r ein d�mlicher Knoten!`nDu brauchst eine halbe Ewigkeit um dein Boot vom Steg zu l�sen und als du es endlich geschafft hast, da ist deine Zeit auch schon um.`nDieser Coupon war vollkommen verschwendet!");
    break;

    // 2 Edelsteine + Ansehen (+)
    case 7:
    case 8:
    output("`^Mann �ber Board!!`n`7In direkter N�he zu dir st�rzt ein anderer Angler aus seinem Boot und droht zu ertrinken!`nDu rettest ihm das Leben und wirst von ihm mit `^2 Edelsteinen`7 belohnt.`nZus�tzlich steigt dein Ansehen im Dorf um `^5 Punkte`7!");
    $session['user']['gems']+=2;
    $session['user']['reputation']+=5;
    break;

    // Gestank (-)
    case 9:
    case 10:
    output("Du ruderst auf den See hinaus...`nPl�tzlich stellst du fest, dass dein Boot ein Leck hat und Langsam volll�uft!`nMit aller Kraft versuchst du das Ufer zu erreichen, doch es ist bereits zu sp�t - du gehst im hohen Schilf unter.`nZwar ist das Wasser dort nicht tief und du kannst dich auch an Land retten, allerdings bist du �ber und �ber mit Algen behangen.`n`4Du stinkst grauenhaft!");
    $res = item_tpl_list_get( 'tpl_id="fldgestank" LIMIT 1' );
    if( db_num_rows($res) )
    {
        $itemnew = db_fetch_assoc($res);
        item_add( $session['user']['acctid'], 0, $itemnew);
    }
    $session[bufflist]['H�llengestank'] = array(
        "name"=>"H�llengestank",
        "rounds"=>10,
        "wearoff"=>"`QDas Blut deines Gegners �berdeckt den H�llengestank.`0",
        "roundmsg"=>"`QDer verfluchte H�llengestank an dir macht deinen Gegner besonders aggressiv`0",
        "badguyatkmod"=>1.08,
        "activate"=>"offense");
    break;

    // sterben (-)
    case 11:
    output("Du ruderst weit auf den See hinaus und wirfst die Angel aus.`n`4Dabei verlierst du das Gleichgewicht und f�llst ins Wasser.`nIrgendetwas hat da unten bereits auf dich gewartet und zieht dich in die Tiefe!`nDu bist tot.");
    $session['user']['hitpoints']=0;
    $fishturn=0;
    addnav("Stirb!");
    addnav("Ok...","shades.php");
    break;

    // Schatz (+)
    case 13:
    $earn=e_rand(1000,5000);
    output("Als du ein gutes St�ck hinausgerudert bist wirfst du die Angel aus.`nIrgendetwas scheint an deinem Haken festzuh�ngen!`n`nDu ziehst mit aller Kraft und kannst eine kleine Truhe bergen.`nSie enth�lt `^".$earn." Goldm�nzen!!!");
    $session['user']['gold']+=$earn;
    break;

    // Rundenbonus (+)
    case 14:
    case 15:
    case 16:
    case 17:
    output("Du ruderst auf den See hinaus und entdeckst mehrere abgelegene Stellen, die von Fischen nur so zu wimmeln scheinen.`nVorsichtig legst du wieder an Land an, um sie nicht zu vertreiben.`nMit diesem Wissen kannst du heute weitere drei mal angeln!");
    $fishturn+=3;
    break;

    // Rundenabzug (-)
    case 18:
    case 19:
    case 20:
    output("Nachdem du ein gutes St�ck hinaus gerudert bist und deine Angel ausgeworfen hast beisst pl�tzlich etwas an.`nEs muss ein gewaltiger Fisch sein - denn er zieht dich an der Angel mitsamt dem Boot quer durch den See!`nDie anderen Angler winken dir zun�chst am�siert zu, doch als sie merken, dass du ihnen mit deinem Geschrei gerade alle Fische vertrieben hast ballen sie zornig die F�uste.`nAls du irgendwann wieder am sicheren Ufer angelangt bist entscheidest du, dass es besser w�re dich besser heute hier nicht mehr sehen zu lassen.");
    $fishturn=0;
    break;

    // Nix (0)
    case 21:
    case 22:
    output("Irgendwie bist du wohl zu schwer, oder das Boot zu alt. Es sinkt noch am Ufer.`nDu kannst dich gerade noch rausretten ohne nass zu werden.");
    break;

    // Zu Ramius (0)
    case 23:
    output("In der Mitte des Sees wird dein Boot pl�tzlich von einem Strudel erfasst und in die Tiefe gerissen.`nDu hast einen Direktzugang zu Ramius Reich gefunden!`nDa der Gott der Toten zur Zeit gar nichts mit dir anfangen kann, gew�hrt er dir 100 Gefallen, damit du so schnell wie m�glich wieder aus dem Totenreich verschwindest ohne ihn gro� zu bel�stigen.");
    $session['user']['deathpower']+=100;
    $session['user']['hitpoints']=0;
    $fishturn=0;
    addnav("Ramius besuchen");
    addnav("Tach auch!","shades.php");
    break;

    // W�rmer oder Fliegen (+)
    case 25:
    case 26:
    case 27:
    output("Als du in deinem Boot auf den See hinaus ruderst, siehst du pl�tzlich die Ausr�stung eines Anglers an die vorbei treiben.`nDie Rute ist wohl hin�ber, allerdings kannst du zwei K�derbeutel ausmachen.`nIn einem sind Fliegen, im andren W�rmer. Einen dieser Beutel k�nntest du zu dir ins Boot ziehen.");
    addnav("W�rmer einstecken!","fish.php?op=wormsplus&wp=7");
    addnav("Fliegen einstecken!","fish.php?op=minnowsplus&mp=10");
    break;

    // Charmeverlust (-)
    case 28:
    case 29:
    output("Du ruderst wie ein Weltmeister, kommst aber nicht vom Fleck.`nBis dir irgendwann jemand mitteilt, dass dein Boot immer noch am Steg befestigt ist.`nPeinlich, peinlich! Und weil das auch jeder mitbekommen hat `4verlierst du einen Charmepunkt.");
    $session['user']['charm']--;
    break;

    // Ungeheuer (0)
    case 30:
    output("Gerade als du sch�n weit rausgerudert bist schlingen sich Tentakel um dein Boot und zerren daran`nEin Seeungeheuer hat es auf dich abgesehen!!!`n");
    if ($session['user']['attack']<50)
    {
        output("`4Du haust wie wahnsinnig mit deiner Waffe auf die Tentakel ein, aber es hilft dir nichts, du wirst mitsamt dem Boot in einem Happs verschlungen...");
        $session['user']['experience']*=0.95;
        $session['user']['hitpoints']=0;
        $fishturn=0;
        addnav("So long, Fishburger");
        addnav("Sterben","shades.php");
    }
    else
    {
    output("`!Mit Heldenmut und cleverem Einsatz gelingt es dir alle Tentakel mit deiner Waffe zu treffen.`nDie Bestie ist davon so �berrascht, dass sie von dir abl�sst - du erh�ltst 2500 Erfahrungspunkte.");
    $session['user']['experience']+=2500;
    }
    break;

    // Forelle (+)
    case 31:
    case 32:
    case 33:
    case 34:
    output("Du hast eine Forelle gefangen!`nDie wird bestimmt sehr gut schmecken. Bis zur Zubereitung packst du sie erstmal in dein Inventar.");
    $res = item_tpl_list_get( 'tpl_name="Forelle" LIMIT 1' );
    if( db_num_rows($res) )
    {
        $itemnew = db_fetch_assoc($res);
        item_add( $session['user']['acctid'], 0, $itemnew);
    }
    break;

    // Lachs (+)
    case 35:
    case 36:
    case 37:
    case 38:
    output("Du f�ngt einen pr�chtigen Lachs!`nIn deinem Inventar wird er gut aufgehoben sein.");
    $res = item_tpl_list_get( 'tpl_name="Lachs" LIMIT 1' );
    if( db_num_rows($res) )
    {
        $itemnew = db_fetch_assoc($res);
        item_add( $session['user']['acctid'], 0, $itemnew);
    }
    break;

    // Goldverlust (-)
    case 39:
    case 40:
    output("Du ruderst auf den See hinaus. Auf einmal wird dein Boot von einer kr�ftigen Windb�e gepackt und durchgesch�ttelt.`nAls sich alles wieder beruhigt hat stellst du fest, dass sich dein Goldbeutel gel�st hat und ins Wasser gefallen ist.`nDu siehst ihn gerade noch untergehen!");
    $session['user']['gold']=0;
    break;

    default:
    output("Diese Bootsfahrt war zwar sch�n, aber v�llig umsonst - du hast nichts gefangen!");
    break;
}
db_query("UPDATE account_extra_info SET fishturn=$fishturn,minnows=$minnows,worms=$worms,boatcoupons=$boatcoupons WHERE acctid = ".$session['user']['acctid']);
}

/************************
Fishing with Golden Egg
************************/

function check3()
{
global $session;
global $minnows;
global $worms;
global $boatcoupons;
global $fishturn;
$chance=(e_rand(1,10));
switch ($chance) {

    case 1:
    output("Gerade als du in Tr�umen um den Reichtum schwelgst, der dich erwartet... `nmacht es `n`n`4BOOM`V `n`nHalb bewusstlos siehst du gerade noch jemanden mit dem `^goldenen Ei`V unter dem Arm davonlaufen.");
    $session['user']['hitpoints']=1;
    break;

    case 2:
    output("Das `^Ei`V geht unter... aber pl�tzlich beginnt das Wasser an der Stelle wild zu blubbern und zu sch�umen. `nEtwas SEHR gro�es n�hert sich aus den Tiefen des Sees!!!! `nVor lauter Schreck l�sst du die Angel fallen, die vom Gewicht des `^Ei`Vs sofort untergeht.");
    break;

    case 3:
    output("Du sp�rst einen Ruck und bevor du dich versehen kannst h�ltst du nur noch eine abgebissene Leine in der Hand!");
    break;

    case 4:
    output("Schon bald beginnt etwas heftig an deinem K�der zu zerren. `nEmsig ziehst du es an Land... `nZwar ist das `^Ei`V fort, jedoch befindet sich stattdessen eine seltsame Waffe an deiner Leine.`n`nDu hast das legend�re Schwert `4Ausweider`V gefunden!!!`n`nIn Angst man k�nne es dir wieder wegnehmen eilst du sofort zum Dorfplatz und vergi�t vor lauter Aufregung deine alte Waffe am Ufer.");
    $item_ausw = item_get_tpl(' tpl_id="ausweider" ');
    $ausw_id = item_add($session['user']['acctid'],0,$item_ausw);
    item_set_weapon($item_ausw['tpl_name'], $item_ausw['tpl_value1'], $item_ausw['tpl_gold'], $ausw_id, 0, 2);
    break;

    case 5:
    output("Du sp�rst einen Ruck an deiner Leine! `nAls du die Angel einholst entdeckst du anstelle des K�ders einen kleinen Eimer`ngef�llt mit `@50`V Edelsteinen!!!");
    $session['user']['gems']+=50;
    break;

    case 6:
    output("Die Fee des Sees nimmt dein Geschenk dankend an.`nAls Zeichen ihrer Wertsch�tzung belohnt sie dich reich.`n`nDu erh�ltst : `n`@10 permanente Lebenspunkte, `n5 Punkte Angriff und 5 Punkte Verteidigung, `n10 Punkte Charme `nund 300 Gefallen bei Ramius!");
    $session['user']['maxhitpoints']+=10;
    $session['user']['attack']+=5;
    $session['user']['defence']+=5;
    $session['user']['charm']+=10;
    $session['user']['deathpower']+=300;
    break;

    case 7:
    output("Du hast etwas sehr Schweres an der Leine! `nEmsig ziehst du es an Land... `nPl�tzlich beginnt deine Leine zu gl�hen und das Gl�hen geht auch auf dich �ber.`nDas `^Ei`V ist zwar fort, doch eine `%Schutzaura`V umgibt dich!`n`n�bergl�cklich rei�t du dir deine alte, schwere R�stung vom Leib und wirfst sie fort. Die wirst du nicht wieder brauchen.");
    $item_ausw = item_get_tpl(' tpl_id="schtzaura" ');
    $ausw_id = item_add($session['user']['acctid'],0,$item_ausw);
    item_set_armor($item_ausw['tpl_name'], $item_ausw['tpl_value1'], $item_ausw['tpl_gold'], $ausw_id);
    break;

    case 8:
    output("Das `^Ei`V geht unter wie ein Stein! `nDie schlaff herabh�ngene Leine gibt dir das ungute Gef�hl, dass diese Aktion nicht besonders clever war.");
    break;

    case 9:
    output("Etwas hat angebissen! `nDoch mit gewaltiger Kraft wird dir deine Angelrute mitsamt `^Ei`V aus den H�nden gerissen... So ein Pech aber auch...");
    break;

    case 10:
    output("Es tut sich absolut gar nichts... `nAls du nach einer ganzen Weile deinen wertvollen K�der wieder an Land ziehen willst merkst du, dass nur noch ein wertloser Stein an deiner Leine h�ngt!");
    break;
}
db_query("UPDATE account_extra_info SET fishturn=$fishturn,minnows=$minnows,worms=$worms,boatcoupons=$boatcoupons WHERE acctid = ".$session['user']['acctid']);
}

/************************
Auswertung des Parameters
************************/
$event=$_GET['op'];
switch ($event)
{
    case 'awake':
    output("`@Puh!`nDiese Tagtr�ume werden aber auch immer gemeiner!");
    break;

    case 'wormsplus':
    output("Du steckst die W�rmer in deinen K�derbeutel...");
    $worms+=$_GET['wp'];
    $inventory=$worms+$minnows;
    if ($inventory>100)
    {
      output("`n`4Leider bekommst du nicht alle dort hinein, da der Beutel bereits zu voll ist.");
      $worms-=($inventory-100);
    }
    db_query("UPDATE account_extra_info SET fishturn=$fishturn,minnows=$minnows,worms=$worms,boatcoupons=$boatcoupons WHERE acctid = ".$session['user']['acctid']);
    break;

    case 'minnowsplus':
    output("Du steckst die Fliegen in deinen K�derbeutel...");
    $minnows+=$_GET['mp'];
    $inventory=$worms+$minnows;
    if ($inventory>100)
    {
      output("`n`4Leider bekommst du nicht alle dort hinein, da der Beutel bereits zu voll ist.");
      $minnows-=($inventory-100);
    }
    db_query("UPDATE account_extra_info SET fishturn=$fishturn,minnows=$minnows,worms=$worms,boatcoupons=$boatcoupons WHERE acctid = ".$session['user']['acctid']);
    break;

    // Minnows
    case 'check1':
    if (($fishturn > 0) AND ($minnows>0) AND ($session['user']['hitpoints']>0))
    {
        output("Du wirfst Deine Angel aus...`n`n");
        $minnows--;
        $fishturn--;
        check1();
    }
    elseif($_GET['su_action'])
    {
        check1();
    }
    break;

    // Worms
    case 'check2':
    if (($fishturn > 0) AND ($worms>0) AND ($session['user']['hitpoints']>0))
    {
        output("Du wirfst Deine Angel aus...`n`n");
        $worms--;
        $fishturn--;
        check2();
    }
    elseif($_GET['su_action'])
    {
        check2();
    }
    break;

    // Boat
    case 'check4':
    if (($fishturn > 0) AND ($boatcoupons>0) AND ($session['user']['hitpoints']>0))
    {
        output("Du entwertest einen Coupon und besteigst eins der Ruderboote...`n`n");
        $boatcoupons--;
        $fishturn--;
        check4();
    }
    elseif($_GET['su_action'])
    {
        check4();
    }
    break;

    // Golden Egg
    case 'check3':
    if (($session['user']['acctid']==getsetting("hasegg",0) AND ($fishturn > 0) AND ($session['user']['hitpoints']>0)))
    {
        output("Du wickelst Deine Leine sorgf�ltig um das `^goldene Ei`V und l�sst es vorsichtig zu Wasser...`n`n");
        savesetting("hasegg","0");
        item_set(' tpl_id="goldenegg"', array('owner'=>0) );
        addnews("`@".$session['user']['name']."`@ hat das `^Goldene Ei`@ beim Angeln verloren`V!");
        $fishturn=0;
        check3();
    }
    break;

    default:
    output("`7Du folgst dem Weg um den See...`n");
    output("Wenn du dich umschaust, siehst Du andere Dorfbewohner, die sich am See aufhalten.`n");
    output("Du bist Dir sicher, dass Dir heute der gro�e Wurf gelingt.`n`n");
    break;
}

/************
Fischen...
************/

If (!$fishdelete==1)
{
if ($session['user']['hitpoints']>0)
{   if ((($minnows>0) || ($worms>0) || ($boatcoupons>0)) && ($fishturn>0)) addnav("Angeln");
    if (($minnows>0) && ($fishturn>0)) addnav("Fliege auswerfen","fish.php?op=check1");
    if (($worms>0) && ($fishturn>0)) addnav("Wurm auswerfen","fish.php?op=check2");
    if (($boatcoupons>0) && ($fishturn>0)) addnav("Ein Boot nehmen","fish.php?op=check4");
    if (($session['user']['acctid']==getsetting("hasegg",0)) && ($fishturn>0)) addnav("`VDas `^goldene Ei`V als K�der verwenden","fish.php?op=check3");
    if(su_check(SU_RIGHT_DEBUG))
    {
       addnav("MOD-Aktionen");
	   addnav('Gl�hend','fish.php?op=check1&su_action=14');
	   addnav('Geh�rtet','fish.php?op=check2&su_action=22');
	   addnav('Mei�el','fish.php?op=check2&su_action=36');
	   addnav('St�psel','fish.php?op=check2&su_action=39');
	   addnav('Stinken','fish.php?op=check4&su_action=10');
	   addnav('Bauernkinder �rgern','fish.php?op=check2&su_action=5');
	   addnav("Wasserschrein","watershrine.php");
    }
    addnav("Wege");
    addnav("R?Zur�ck zum See","pool.php");
    addnav("B?Angelshop","bait.php");

    output("`n`2-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-`n`n`0");
    viewcommentary("fishing", "Etwas schreiben", 25, "sagt");
}
}
    headoutput("`2Du hast in deinem Beutel:`n`!Fliegen - ".($minnows>0?$minnows:"0")."`n`!W�rmer - ".($worms>0?$worms:"0")."`n`!Bootscoupons - ".($boatcoupons>0?$boatcoupons:"0")."`n`n`@Runden zum fischen - ".($fishturn>0?$fishturn:"0")."`n`n`0");

page_footer();

?>

