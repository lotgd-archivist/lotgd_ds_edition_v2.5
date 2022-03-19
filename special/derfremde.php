<?php
// Der Fremde, Version 0.99
//
// Ist es ein Gott? Ein Dämon?
// Oder doch nur Einbildung ...
//
// Erdacht und umgesetzt von Oliver Wellinghoff.
// E-Mail: wellinghoff@gmx.de
// Erstmals erschienen auf: http://www.green-dragon.info
//
//  - 29.06.04 -
//  - Version vom 04.11.2004 -
//  Jetzt kompatibel mit "kleineswesen.php"
// modded by talion auf ctitle backup
//
//Folgenden Abschnitt in newday.php einfügen:
/*
//Der Fremde: Bonus und Malus
if ($session['user']['ctitle']=='`$Ramius´ '.($session[user][sex]?'Sklavin':'Sklave').''){
if ($session[user][reputation]<0){
            output('`$`nDein Herr, Ramius, ist begeistert von deinen Greueltaten und gewährt Dir seine `bbesondere`b Gnade!`n`$Seine Gnade ist heute besonders ausgeprägt - und du erhältst 2 zusätzliche Waldkämpfe!`n');
            $session[user][turns]+=2;
            $session[user][hitpoints]*=1.15;
            $session[bufflist][Ramius1] = array('name'=>'`$Ramius\' `bbesondere`b Gnade','rounds'=>200,'wearoff'=>'`$Ramius hat Dir für heute genug geholfen.','atkmod'=>1.15,'roundmsg'=>'`$Eine Stimme in deinem Kopf befiehlt: `i`bZerstöre!`b Bring Leid über die Lebenden!`i','activate'=>'offense');
}else
    switch(e_rand(1,10)){
            case 1:
            case 2:
            case 3:
            case 4:
            case 5:
            output('`$`nAls dein Herr, Ramius, heute morgen von deinem guten Ruf erfuhr, überlegte er, ob er dich motivieren oder tadeln sollte ... und entschied sich fürs Motivieren.`n'`$Seine Gnade ist heute mit Dir - und du erhältst 2 zusätzliche Waldkämpfe!`n');
            $session[user][turns]+=2;
            $session[user][hitpoints]*=1.1;
            $session[bufflist][Ramius2] = array("name"=>"`\$Ramius' Gnade","rounds"=>150,"wearoff"=>"`\$Ramius hat Dir für heute genug geholfen.","atkmod"=>1.1,"roundmsg"=>"`\$Eine Stimme in deinem Kopf befiehlt: `i`bZerstöre!`b Bring Leid über die Lebenden!`i","activate"=>"offense");
            break;
            case 6:
            case 7:
            case 8:
            case 9:
            case 10:
            output('`$`nAls dein Herr, Ramius, heute morgen von deinem guten Ruf erfuhr, überlegte er, ob er dich motivieren oder tadeln sollte ... und entschied sich fürs Tadeln.`n`$Sein Zorn ist heute mit Dir - und du verlierst 2 Waldkämpfe!`n');
            $session[user][turns]-=2;
            $session[user][hitpoints]*=0.9;
            $session[bufflist][Ramius3] = array("name"=>"`\$Ramius' Zorn","rounds"=>200,"wearoff"=>"`\$Ramius' Zorn ist vorüber - für heute.","defmod"=>0.9,"roundmsg"=>"`\$Ramius ist zornig auf dich!","activate"=>"offense");
            break;
}}
*/

if (!isset($session)) exit();

$session[user][specialinc] = "derfremde.php";

$sql = 'SELECT ctitle,cname FROM account_extra_info WHERE acctid='.$session['user']['acctid'];
$res = db_query($sql);
$row_extra = db_fetch_assoc($res);

if ($row_extra['ctitle']=="`\$Ramius ".($session[user][sex]?"Sklavin":"Sklave").""){

switch($_GET[op]){

case "":
output('`@Nach langer Zeit findest du zu dem Ort zurück, an dem du damals deine Seele an `$Ramius`@ verkauft hast.
 Auf einem Baumstumpf im Sonnenschein sitzt eine Gestalt, die sich in einen schwarzen Umhang hüllt.
 Als du nähertrittst, erhebt sie das Wort: `#"Mein Name ist `b`i`@May`2ann`i`b`#, und ich bin wie Du eine Sklavin des Ramius..."
 `@Sie seufzt. `#"Aber Du wandelst noch unter den Lebenden, ihm gehört nur Deine Seele.
 Meine Seele jedoch vermachte ich ihm zusammen mit meinem Körper..."
 `n`@Die verhüllte Gestalt erhebt sich, lüftet ihre Kapuze und zum Vorschein kommt eine wunderschöne Elfe.
 `#"Nun, ich kann Dich von seinem Griff befreien und dir deine Seele zurückgeben. Aber dazu brauche ich fünf Edelsteine.
 Ohne sie ist es auch mir nicht möglich, seinen Fluch zu brechen."');
if ($session[user][gems]>=5){
output('`@Sie lächelt dich an, als sie deinen geöffneten Beutel erblickt. `#"Wie ich sehe, hast Du einige dabei.
 `n`nMöchtest Du, dass ich `$Ramius\'`# Fluch breche?"
 `n`n`@<a href="forest.php?op=befreienja">Ja, bitte ...</a>
 `n`n`@<a href="forest.php?op=befreiennein">Nein, danke!</a>', true);
    addnav("","forest.php?op=befreienja");
    addnav("","forest.php?op=befreiennein");
    addnav("Ja, bitte ...","forest.php?op=befreienja");
    addnav("Nein, danke!","forest.php?op=befreiennein");
}else{
output("`@`n`nSie seufzt, als sie deinen geöffneten Beutel erblickt. `#'Wie ich sehe, hast Du nicht genügend Edelsteine dabei ... Komm später wieder ...' `n`n`@Mit diesen Worten verschwindet sie zwischen den Bäumen.");
$session[user][specialinc]="";
break;
}

case "befreiennein":
if ($_GET[op]=="befreiennein"){
output("`@Sie seufzt. `#'Wie ich sehe, hat er Dich fest im Griff ...' `n`n`@Mit diesen Worten verschwindet sie zwischen den Bäumen.");
$session[user][specialinc]="";
break;
}

case "befreienja":
if ($_GET[op]=="befreienja"){
output ("`@Ohne ein weiteres Wort zu verlieren tritt `b`i`@May`2ann`i`b`@ an dich heran und nimmt die Edelsteine entgegen. `#'Schließe nun die Augen.'`@");
output ("`@du tust, wie dir geheißen und tauchst ein in eine Welle blaugleißenden Lichtes ... schwimmst hindurch und siehst eine Siedlung in der Ferne, durchleuchtet von Blau und Weiß ...");
output ("`#'Das ist Chadyll'`@, sagt `b`i`@May`2ann`i`b`@, `#'meine Heimat, zu der ich nie mehr zurückkehren darf ...'`@, aber es ist, als wäre `b`i`@May`2ann`i`b`@ ganz weit von dir entfernt ... ganz ... weit ...");
output ("`n`nAls du wieder zu dir kommst, liegst du unter einem Baum ins Moos gebettet. Es bleibt nur eine Erinnerung, ein letztes Wort: `#'Wir vergessen nun ...'`n`n`@Wer hat das gesagt? Was hat es zu bedeuten ...?");
output ("`n`n`^Du wurdest von `\$Ramius'`^ Fluch befreit und bekommst deinen regulären Titel zurück! Solltest du vor der Versklavung einen selbstgewählten Titel gehabt haben, so wirst du ihn neu erstellen müssen.`n`n Oder hast du etwa wirklich gedacht, so glimpflich davon kommen zu könne`\$hehehehehehahahahahahahihihahaha ...!'");

//Kompatibilität mit "kleineswesen.php":
  $oldname = $session[user][name];

	$regname = ($row_extra['cname'] ? $row_extra['cname'] : $session['user']['login']);

  $session[user][name] = $session[user][title].' '.$regname;
  
  $sql = 'UPDATE account_extra_info SET ctitle = "" WHERE acctid='.$session['user']['acctid'];
  db_query($sql);
  
  $session[user][gems]-=5;

//Kompatibilität mit "kleineswesen.php":
//  $sql = db_query("SELECT verkleinert FROM kleineswesen");
  //$result = db_fetch_assoc($sql) or die(db_error(LINK));
  $result = false;
if ($oldname == "".$result[verkleinert]."")
{
  db_query("UPDATE kleineswesen SET verkleinert = '".$session[user][name]."'");
  addnav("Tägliche News","news.php");
  addnav("Zurück zum Wald.","forest.php");
  addnews("`@`b".$regname."`b `@begegnete `b`i`@May`2ann`i`b`@ und wurde mit ihrer Hilfe von ".($session[user][sex]?"ihrem":"seinem")." Dasein als ".($session[user][sex]?"Sklavin":"Sklave")." des `\$Ramius`@ befreit!");
  $session[user][specialinc]="";
  break;
}else
  addnav("Tägliche News","news.php");
  addnav("Zurück zum Wald.","forest.php");
  addnews("`@`b".$regname."`b `@begegnete `b`i`@May`2ann`i`b`@ und wurde mit ihrer Hilfe von ".($session[user][sex]?"ihrem":"seinem")." Dasein als ".($session[user][sex]?"Sklavin":"Sklave")." des `\$Ramius`@ befreit!");
  $session[user][specialinc]="";
  break;
}
}
}else

switch($_GET[op]){

case "":
    output("`@Die letzte Stunde verlief sehr beschwerlich; scharfer Wind war aufgekommen und du fragst dich, wie das überhaupt sein kann, bei dem dichten Baumstand. In diesem Teil des Waldes ist es so dunkel, dass man kaum zwanzig Fuß weit sehen kann. Und jetzt hat es auch noch angefangen zu regnen ... Du bist völlig durchnässt. Hoffentlich holst du dir keinen Schnupfen, das wäre das letzte, was--
Jemand steht hinter dir, du spürst es ganz genau!`n");
    output("`@Vorsichtig, auf dein/e/en `b`2".$session[user][weapon]."`b`@ vertrauend drehst du dich um, eine Eiseskälte im Nacken, und bereit, dich sofort auf den Fremden zu stürzen. Doch als du dich umgedreht hast, kannst du tief durchatmen. Da ist niemand.`n");
    output("Mit einem Lächeln auf den Wangen drehst du dich zurück in deine Reiserichtung - und starrst erstarrt in die endlose Dunkelheit unter der Kapuze eines Mannes ... Wesens ..., das dir, kaum eine Schwertlänge entfernt, gegenübersteht; still, stumm, in eine tiefschwarze Robe gehüllt, die den Boden kaum berührt - es ist, als würde der Fremde schweben. Langsam erhebt er seinen rechten, ausgestreckten Arm. Du kannst seine Hand nicht erkennen - aber unter dem langen, weiten Ärmel siehst du etwas rotglühend hervorglitzern ... `n`nWas wirst du tun?");
    output("`n`n`@<a href='forest.php?op=wegrennen'>Wegrennen!</a>", true);
    output("`@`n`n<a href='forest.php?op=hand'>Ebenfalls die Hand ausstrecken.</a>", true);
    output("`@`n`n<a href='forest.php?op=respekt'>Ich verlange den mir gebührenden Respekt von diesem Landstreicher!</a>", true);
    output("`n`n`@<a href='forest.php?op=demut'>Auf die Knie! Das muss ein Gott sein!</a>", true);
    output("`n`n`@<a href='forest.php?op=angriff'>Angreifen! Das muss ein Dämon sein!</a>", true);
    output("`n`n`@<a href='forest.php?op=ignorieren'>Ignorieren! Das kann nur Einbildung sein!</a>", true);
    addnav("","forest.php?op=wegrennen");
    addnav("","forest.php?op=hand");
    addnav("","forest.php?op=respekt");
    addnav("","forest.php?op=demut");
    addnav("","forest.php?op=angriff");
    addnav("","forest.php?op=ignorieren");
    addnav("Wegrennen.","forest.php?op=wegrennen");
    addnav("Hand ausstrecken.","forest.php?op=hand");
    addnav("Respekt verlangen.","forest.php?op=respekt");
    addnav("Auf die Knie.","forest.php?op=demut");
    addnav("Angreifen.","forest.php?op=angriff");
    addnav("Ignorieren.","forest.php?op=ignorieren");

case "wegrennen":
if ($_GET[op]=="wegrennen"){
      output("`@Wie sagte bereits deine Großmutter? `#'Wenn Du nicht weißt, was es ist, dann lass es auf dem Teller!'`n`@ Du rennst so schnell Du kannst, ohne dich umzudrehen - und merkst mit jedem Schritt, wie die Eiseskälte näher kommt. Links, rechts, vor dir! Der Fremde ist überall!`n Vom Laufen erschöpft - so erklärst du es später zumindest; Angst kann ja kaum der Grund gewesen sein ... -, fällst du in Ohnmacht.");
    output("`@Was auch immer es war, es hat dich allein durch seinen Anblick besiegt. Soviel steht fest.");
if ($session ['user']['dragonkills']<=4){
output("`@`n`nAber für `b".($session[user][sex]?"eine schwächliche":"einen schwächlichen")." ".$session[user][title]."`b`@ hast du Dich angemessen verhalten.");
}
if ($session ['user']['dragonkills']>=5 && $session ['user']['dragonkills']<=8){
output("`@`n`nWar eine solche Vorstellung für `b".($session[user][sex]?"eine abenteuerhungrige":"einen abenteuerhungrigen")." ".$session[user][title]."`b`@ wirklich nötig?");
$session[user][reputation]-=3;
}
if ($session ['user']['dragonkills']>=9 && $session ['user']['dragonkills']<=13){
output("`@`n`n`bFür ".($session[user][sex]?"eine erfahrene":"einen erfahrenen")." ".$session[user][title]."`b`@ war das `beine äußerst schwache Vorstellung`b`@!");
$session[user][reputation]-=7;
addnav("Tägliche News","news.php");
addnav("Zurück zum Wald.","forest.php");
addnews("`\$`b".$session[user][name]."`b`\$ verstrickte sich in Lügengeschichten über ".($session[user][sex]?"ihre":"seine")." Feigheit!");
    $sql = "INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'village',".$session[user][acctid].",'/me `\$hört einige kleine Bauernjungen lachen und fragt sich, ob das mit ".($session[user][sex]?"ihrer":"seiner")." Feigheit zu tun haben könnte ...')";
    db_query($sql) or die(db_error(LINK));
}
if ($session ['user']['dragonkills']>=14){
output("`@`n`n`bFür ".($session[user][sex]?"eine gestandene":"einen gestandenen")." ".$session[user][title]."`b`@ war dieses Verhalten `babsolut erniedrigend und ehrlos`b`@!");
$session[user][reputation]-=15;
addnav("Tägliche News","news.php");
addnav("Zurück zum Wald.","forest.php");
addnews("`\$`b".$session[user][name]."`b`\$ verstrickte sich in Lügengeschichten über ".($session[user][sex]?"ihre":"seine")." Feigheit, was ".($session[user][sex]?"ihrem":"seinem")." Ansehen im Dorf sehr schadet!");
    $sql = "INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'village',".$session[user][acctid].",'/me `\$wird von allen Anwesenden wegen ".($session[user][sex]?"ihrer":"seiner")." Feigheit ausgelacht, als ".($session[user][sex]?"sie":"er")." den Dorfplatz betritt.')";
    db_query($sql) or die(db_error(LINK));
}
    $turns = (e_rand(0,2));
if ($turns==0){
    $session[user][turns]-=$turns;
    $session[user][specialinc]="";
    break;
}else
    output("`n`n`@Als du aus deiner Ohnmacht erwachst, hast du `^".$turns."`@ Waldkämpfe verschlafen!");
    $session[user][turns]-=$turns;
    $session[user][specialinc]="";
    break;
}

case "hand":
if ($_GET[op]=="hand"){
       output("`@Dein Herz rast und deine Finger zittern, als du deinen Arm ausstreckst und sich deine Hand dem Glitzern unter dem Ärmel des Fremden nähert. Mit jedem weiteren Zentimeter wird es immer kälter ...");
      output("`n`n`@<a href='forest.php?op=handweiter'>Weiter.</a>", true);
    addnav("","forest.php?op=handweiter");
    addnav("Weiter.","forest.php?op=handweiter");
}

case "handweiter":
if ($_GET[op]=="handweiter"){
switch(e_rand(1,10)){
            case 1:
            case 2:
            case 3:
            case 4:
            case 5:
            output("`@Als du das Glitzern fast erreicht hast, schließt du die Augen. Es fühlt sich kalt an ... und hart. Du bleibst noch eine Weile so stehen und wagst es nicht, die Augen wieder zu öffnen. Schon bald hat der Gegenstand in deiner Hand deine Körperwärme angenommen. Du öffnest die Augen und siehst: `^einen Edelstein`@!");
            output("`@`nVon dem Fremden ist nichts mehr zu sehen und der Regen hat sich gelegt.");
            $session[user][gems]+=1;
            $session[user][specialinc]="";
            break;
            case 6:
            case 7:
            output("`@Gebannt starrst du auf das rote Glitzern - wie ist es wunderschön ... wie ist es ... kalt ... wie ist es- Völlig unvorbereitet schnellt aus dem Ärmel des Fremden eine glühende Sichel hervor und drückt sich in deine offene Handfläche. Der Schmerz ist kurz und intensiv. dir schwinden die Sinne ...");
            output("`@`nAls du wieder aufwachst, fühlst du dich ausgelaugt und schwach. Der Regen hat aufgehört und der Fremde ist nirgends zu erblicken.");
if ($session[user][maxhitpoints]>$session[user][level]*10){
             output("`@`n`nDu verlierst `$1`@ permanenten Lebenspunkt!");
            $session[user][maxhitpoints]-=1;
            $session[user][hitpoints]-=1;
}
            output("`@`n`nDu verlierst `^1`@ Waldkampf!");
            $session[user][turns]-=1;
            $session[user][specialinc]="";
            break;
            case 8:
            case 9:
            case 10:
            output("`@Gebannt starrst du auf das rote Glitzern - wie ist es wunderschön ... wie ist es ... kalt ... wie ist es- Völlig unvorbereitet schnellt aus dem Ärmel des Fremden eine Hand hervor, zart und ebenmäßig wie die einer jungen Frau. Das Glitzern entpuppt sich als Fingerring.");
            output("`#`n'Du solltest nicht hier sein, `b".$session[user][name]."`b'`@, hörst du eine sanfte Stimme sagen. In demselben Moment erkennst du unter der Kapuze die Züge einer jungen, bildhübschen Elfe. `#'Und auch ich nicht.' `@Sie seufzt. `#'Mein Name ist `b`i`@May`2ann`i`b`@ - `b`i`@May`2ann`i`b`@, die Vergessene, die Vergebliche, die Vergangene ... Einst zog ich das Reich der Schatten dem der Lebenden vor - um den Preis meines Glücks, um den Preis der Liebe, um den Preis meines geliebten Clouds ... Nimm Dich vor`$ Ramius`# in Acht, hüte Dich vor seinen falschen Versprechungen! Hier, nimm einen Teil meiner einstigen, weltlichen Schönheit - und werde mit jemandem glücklich! So, wie ich niemals mehr glücklich werden darf ...'`n`@Mit diesen Worten verschwindet sie in die Dunkelheit.");
            output("`@`n`nDu erhältst `^2`@ Charmepunkte!");
            output("`@`n`nDu verlierst `$1`@ Waldkampf!");
            $session[user][charm]+=2;
            $session[user][turns]-=1;
            $session[user][specialinc]="";
            break;
}}

case "respekt":
if ($_GET[op]=="respekt"){
  output("`@Du nimmst deine gewohnte Pose ein, die du jeden Tag vor dem Spiegel übst, und stellst dich nach einem kurzen Räuspern mit diesen Worten vor: `#'Sei Er gegrüßt, Lumpenträger!");
if ($session ['user']['dragonkills']==0){
output("`bIch bin ".($session[user][sex]?"die":"der")." überaus mutige ".$session[user][name]."`b!");
}
if ($session ['user']['dragonkills']>=1 && $session ['user']['dragonkills']<=4) {
output("`bIch bin ".($session[user][sex]?"die":"der")." überaus mutige und starke ".$session[user][name]."`b!");
}
if ($session ['user']['dragonkills']>=5 && $session ['user']['dragonkills']<=8){
output("`bIch bin ".($session[user][sex]?"die":"der")." überaus reiche und unglaublich mutige ".$session[user][name]."`b!");
}
if ($session ['user']['dragonkills']>=9 && $session ['user']['dragonkills']<=13){
output("`bIch bin ".($session[user][sex]?"die":"der")." allseits bekannte und überaus erfahrene ".$session[user][name]."`b!");
}
if ($session ['user']['dragonkills']>=14 && $session ['user']['dragonkills']<=17){
output("`bIch bin ".($session[user][sex]?"die":"der")." überaus kriegserfahrene und hochdekorierte ".$session[user][name]."`b!");
}
if ($session ['user']['dragonkills']>=18 && $session ['user']['dragonkills']<=22){
output("`bIch bin ".($session[user][sex]?"die":"der")." überaus einflussreiche und unglaublich wohlhabende ".$session[user][name]."`b!");
}
if ($session ['user']['dragonkills']>=23 && $session ['user']['dragonkills']<=27){
output("`bIch bin ".($session[user][sex]?"die":"der")." über alle Maßen fähige und weitbekannte ".$session[user][name]."`b!");
}
if ($session ['user']['dragonkills']>=28 && $session ['user']['dragonkills']<=34){
output("`bIch bin ".($session[user][sex]?"die":"der")." unaufhaltsame und weltberühmte ".$session[user][name]."`b!");
}
if ($session ['user']['dragonkills']>=35 && $session ['user']['dragonkills']<=38){
output("`bIch bin ".($session[user][sex]?"die":"der")." königliche und ehrfurchtgebietende, den Göttern nahestehende ".$session[user][name]."!`b");
}
if ($session ['user']['dragonkills']>=39 && $session ['user']['dragonkills']<=45){
output("`bIch bin ".($session[user][sex]?"die":"der")." strahlende und unglaublich mächtige ".$session[user][name]."`b!");
}
if ($session ['user']['dragonkills']>=46 && $session ['user']['dragonkills']<=49){
output("`bIch bin ".($session[user][sex]?"die":"der")." den Göttern am nächsten kommende ".$session[user][name]."`b!");
}
if ($session ['user']['dragonkills']==50){
output("`bIch bin ".($session[user][sex]?"die":"der")." gottgleiche und allesvermögende ".$session[user][name]."`b!");
}
    output("`#Sage `bEr`b mir nun, wer `bEr`b ist, dass `bEr`b es wagt, `bmich`b so zu erschrecken!'`@");
    output("`nFür einen Moment wird es still im Wald. Es regnet noch immer, aber selbst das Plätschern ist verstummt. Der Fremde nimmt seinen Arm zurück und rührt sich nicht ...");
    output("`n`n`@<a href='forest.php?op=respektweiter'>Weiter.</a>", true);
    addnav("","forest.php?op=respektweiter");
    addnav("Weiter.","forest.php?op=respektweiter");
    break;
}

case "respektweiter":
if ($_GET[op]=="respektweiter"){
switch(e_rand(1,10)){
            case 1:
            case 2:
            case 3:
			output("`@Schließlich antwortet der Fremde mit einer tiefen, gravitätischen Stimme: `\$'Damit bist Du heute schon ".($session[user][sex]?"die":"der")." zweite, ".($session[user][sex]?"der ihre":"dem seine")." beschränkten Fähigkeiten zu Kopf gestiegen sind. - ".$session[user][name].", ich gebe dir etwas Überirdisches mit auf den Weg: Überirdische Schmerzen!'");
            output("`\$`n`nDu bist tot!");
            output("`n`n`@Du verlierst `\$".$session[user][experience]*0.08." `@Erfahrungspunkte!");
            output("`n`nDu verlierst all dein Gold!");
            output("`n`n`@Du kannst morgen weiterspielen.");
            $session[user][alive]=false;
            $session[user][hitpoints]=0;
            $session[user][gold]=0;
            $session[user][experience]=$session[user][experience]*0.92;
            addnav("Tägliche News","news.php");
            addnews("`\$Ramius`4 gewährte `b".$session[user][name]."`b`4 Einblicke in die facettenreiche Welt unendlicher Schmerzen.");
            $sql = "INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'shade',".$session[user][acctid].",'/me `\$hängt kopfüber in einem Dornenstrauch, wo ".($session[user][sex]?"sie":"er")." von einem Peindämon genüsslich ausgelöffelt wird.')";
            db_query($sql) or die(db_error(LINK));
            $session[user][specialinc]="";
            break;
			case 4:
            case 5:
            case 6:
            output("`@Schließlich antwortet der Fremde mit einer tiefen, gravitätischen Stimme: `\$'Wie gut, dass Du Dich von selbst vorgestellt hast. - So weiß ich wenigstens schon mal, wie ich Dich für den Rest der Ewigkeit rufen werde: `b".$session[user][name].", die kleine, dumme, völlig durchgedrehte und überhebliche Bauerngöre`b!'");
            output("`$`n`nDu bist tot!");
            output("`n`n`@Du verlierst `$".$session[user][experience]*0.07." `@Erfahrungspunkte!");
            output("`n`nDu verlierst all dein Gold!");
            output("`n`n`@Du kannst morgen weiterspielen.");
            $session[user][alive]=false;
            $session[user][hitpoints]=0;
            $session[user][gold]=0;
            $session[user][experience]=$session[user][experience]*0.93;
            addnav("Tägliche News","news.php");
            addnews("`4Aus dem Totenreich berichtet man, dass`$ Ramius `4`b".$session[user][name]."`b `$'Du kleine, dumme, völlig durchgedrehte und überhebliche Bauerngöre!' `4nachrief.");
            $sql = "INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'shade',".$session[user][acctid].",'/me `\$wird von Ramius als ´kleine, dumme, völlig durchgedrehte und überhebliche Bauerngöre´ beschimpft!')";
            db_query($sql) or die(db_error(LINK));
            $session[user][specialinc]="";
            break;
            case 7:
            case 8:
            output("`@Schließlich antwortet der Fremde mit einer tiefen, gravitätischen Stimme:`$ 'Deine Überheblichkeit wird viel Verderben über die anderen Lebenden bringen. Deshalb lasse ich Dich ziehen. Aber nicht, ohne Dich zuvor `bnoch`b verderbenbringender gemacht zu haben!'");
            output("`@Unter der Berührung des Fremden sackst du zusammen. Als du wieder aufwachst, hat der Regen aufgehört.");
            output("`@`n`nDu erhältst `^1`@ Angriffspunkt!");
            output("`@`n`nDu verlierst `$1`@ Waldkampf!");
            $session[user][turns]-=1;
            $session[user][attack]++;
            $session[user][specialinc]="";
            break;
            case 9:
            case 10:
            output("`@Schließlich antwortet der Fremde mit einer tiefen, gravitätischen Stimme: `$'Deine Überheblichkeit wird viel Verderben über die anderen Lebenden bringen. Deshalb lasse ich Dich ziehen. Aber nicht, ohne Dich zuvor noch verderbenbringender gemacht zu haben!'");
            output("`@Unter der Berührung des Fremden sackst du zusammen. Als du wieder aufwachst, hat der Regen aufgehört.");
            output("`@`n`nDu verlierst die meisten deiner Lebenspunkte!");
            output("`@`n`nDu erhältst `^2`@ permanente Lebenspunkte!");
            output("`@`n`nDu verlierst `$1`@ Waldkampf!");
            $session[user][maxhitpoints]+=2;
            $session[user][hitpoints]=1;
            $session[user][turns]-=1;
            $session[user][specialinc]="";
            break;
}}

case "demut":
if ($_GET[op]=="demut"){
output("`@Voll Ehrfurcht lässt du dich zu Boden sinken, hinab in den nassen Matsch.`n `#'Ich bin unwürdig!', `@rufst du. `#'Ich bin glanzlos im Lichte deiner Erscheinung, oh ");
  if ($session[user][race]=='trl'){
output("`#`bCrogh-Uuuhl, Beleber der Sümpfe, Herr der Trolle - Gott der Götter!`b'");
}
  if ($session[user][race]=='elf'){
output("`#`bChara, Herrin der Wälder, Licht durch die Baumkronen - Göttin der Götter!`b'");
}
  if ($session[user][race]=='men'){
output("`#`beinäugiger Odin, Herr der Asen und der Menschen - Gott der Götter!`b'");
}
  if ($session[user][race]=='zwg'){
output("`#`bYkronos, Hüter von Ygh'gor - der Wahrheit -, Herr der Zwerge - Gott der Götter!`b'");
}
  if ($session[user][race]=='ecs'){
output("`#`bSssslassarrr, Hüterin der Plateuebenen von Chrizzak, Herrin der Echsen - Göttin der Götter!`b'");
}
    output("`@`n`nZitternd wartest du auf eine Reaktion.");
    output("`n`n`@<a href='forest.php?op=demutweiter'>Weiter.</a>", true);
    addnav("","forest.php?op=demutweiter");
    addnav("Weiter.","forest.php?op=demutweiter");
    break;
}

case "demutweiter":
if ($_GET[op]=="demutweiter"){
switch(e_rand(1,10)){
            case 1:
            case 2:
            output("`@`#'Erhebe Dich, Sterblicher!'`@ hörst du eine sanfte Stimme sagen. Du tust, wie dir geheißen und erblickst unter der Kapuze das Antlitz einer jungen, bildhübschen Elfe. `#'Ich bin kein Gott und auch keine Göttin. Wisse, dass ich `b`i`@May`2ann`i`b`@ bin, die Verblendete und ewige Gefangene des `\$Ramius`#. Verschwinde von hier, schnell! Er ist hier, in mir - und ich kann ihn nur für kurze Zeit zurückhalten. - Nimm das, auf dass es Dich auf Deinen Abenteuern beschütze.'");
            output("`n`@Du greifst nach dem Fingerring, den sie dir hinhält, verbeugst dich und rennst davon.`n Schon bald hat der Regen aufgehört und du kannst verschnaufen. Sie hat dir einen Schutzring der Lichtelfen gegeben!");
            output("`n`n`@Du erhältst `^1`@ Punkte Verteidigung!");
            output("`n`nDu verlierst einen Waldkampf!");
            $session[user][turns]-=1;
            $session[user][defence]++;
            $session[user][specialinc]="";
            break;
            case 3:
            case 4:
            case 5:
            case 6:
            output("`@Schließlich antwortet der Fremde mit einer tiefen, gravitätischen Stimme: `$'Das ist ja geradezu `berbärmlich`b! Erst dieser arrogante Schwächling von eben - und nun so etwas! Verschwinde! Für Dich ist noch der Tod zu schade!'");
            output("`n`@Du rutscht ein paar Mal aus, als du im regennassen Schlamm aufstehen willst, und rennst so schnell du kannst davon. Wer auch immer der Fremde war, er hatte gerade ziemlich schlechte Laune ...");
            output("`n`n`@Du verlierst einen Waldkampf!");
            $session[user][turns]-=1;
            $session[user][specialinc]="";
            break;
            case 7:
            case 8:
            case 9:
            case 10:
            output("`@Schließlich antwortet der Fremde mit einer tiefen, gravitätischen Stimme: `$'So ist es recht! Nieder in den Schlamm mit Dir, erbärmlicher Sterblicher! Ich sehe, Du hast bei Deinen Aufenthalten in meinem Reich viel gelernt, nur die korrekte Anpreisung meiner Herrlichkeit müssen wir noch üben. Erinnere mich beim nächsten Mal daran, dass Du ein paar Gefallen gut hast ...'");
            output("`@Während du zitternd daliegst, löst sich der Fremde in der Dunkelheit auf.");
            $gefallen1 = e_rand(40,80);
            $session[user][deathpower]+=$gefallen1;
            output("`n`nDu erhältst `^".$gefallen1."`@ Gefallen von`$ Ramius`@!");
            output("`n`nDu verlierst einen Waldkampf!");
            $session[user][turns]-=1;
            $session[user][specialinc]="";
            break;
}}

case "angriff":
if ($_GET[op]=="angriff"){
  output("`@Geistesgegenwärtig springst du mit einem Satz zurück und bringst dein/e/en `b".$session[user][weapon]."`b in Bereitschaft. `n`#'Kreatur der Niederhöllen'`@, rufst du,`# 'Dein letztes Stündlein hat geschlagen!'");
  output("`n`n`@<a href='forest.php?op=angriffweiter1'>Weiter.</a>", true);
    addnav("","forest.php?op=angriffweiter1");
    addnav("Weiter.","forest.php?op=angriffweiter1");
    break;
}

case "angriffweiter1":
if ($_GET[op]=="angriffweiter1"){
switch(e_rand(1,10)){
            case 1:
            case 2:
            case 3:
            case 4:
            output("`@`#'Warte, Fremder!'`@ - Die Gestalt lüftet ihre Kapuze und zum Vorschein kommt eine bildhübsche Elfe. Sie wirkt traurig. `#'Hat der Tod mich etwa dermaßen verändert, dass man mich für einen Dämonen halten kann?! Ach ... lass gut sein ...'`n`n `@Die Fremde verschwindet in der Dunkelheit. Wer sie wohl war?");
            $session[user][specialinc]="";
            break;
            case 5:
            case 6:
            case 7:
            case 8:
            case 9:
            case 10:
            output("`@Du willst gerade entschlossen vorstürmen, als dich plötzlich ein kalter Griff im Nacken festhält und einen Fingerbreit anhebt. Unter der Kapuze dröhnt eine dunkle Stimme hervor: `n`$'Glaubst Du `bwirklich`b, dass `bDu`b es mit mir aufnehmen kannst, Sterblicher?'");
            output("`n`n`@<a href='forest.php?op=angriffweiter2'>Ja, Bestie!</a>", true);
            addnav("","forest.php?op=angriffweiter2");
            addnav("Ja.","forest.php?op=angriffweiter2");
            output("`n`n`@<a href='forest.php?op=angriffweiter3'>Also, eigentlich ...</a>", true);
            addnav("","forest.php?op=angriffweiter3");
            addnav("Nein.","forest.php?op=angriffweiter3");
            break;
}}

case "angriffweiter2":
if ($_GET[op]=="angriffweiter2"){
switch(e_rand(1,10)){
            case 1:
            case 2:
            case 3:
            case 4:
            case 5:
            output("`$'Ha! Ist es Leichtsinn oder ist es Mut? In jedem Fall wäre es eine große Dummheit! Du kannst dich glücklich schätzen, dass mir gerade nicht danach ist, Dich ganz mitzunehmen ...'`@`n Die eisige Hand in deinem Nacken schleudert dich weitab in die Büsche. Als du wieder aufwachst, hat der Regen aufgehört und der Fremde ist verschwunden.");
if ($session[user][maxhitpoints]>$session[user][level]*10){
             output("`@`n`nDu verlierst `$1`@ permanenten Lebenspunkt!");
            $session[user][maxhitpoints]-=1;
            $session[user][hitpoints]-=1;
}
            $session[user][hitpoints]=1;
            output("`n`n`@Du verlierst fast alle deine Lebenspunkte!");
            output("`n`n`@Du verlierst `^1`@ Waldkampf!");
            $session[user][turns]-=1;
            $session[user][specialinc]="";
            break;
            case 6:
            case 7:
            output("`@`$'Dann zeig, was Du kannst!'`n`@Das lässt du dir nicht zweimal sagen. Sobald sich der Griff gelockert hat, stürmst du mit einem wilden, furchterregenden Schrei nach vorne, holst aus und - schlägst durch den Fremden hindurch!");
            output("`@`nVon deinem eigenen Schwung umgerissen, fällst du zu Boden. Als du wieder aufschaust, stellst du mit Schrecken fest, dass der Fremde sich über dich gebeugt hat. Das letzte, was du spürst, ist ein seltsames Stechen an der Stirn ... Dein Tod muss grauenvoll gewesen sein.");
            output("`$`n`nDu bist tot!");
if ($session[user][maxhitpoints]>$session[user][level]*10){
               $hpverlust = e_rand(1,3);
             output("`@`n`nDu verlierst `$".$hpverlust."`@ permanente(n) Lebenspunkt(e)!");
            $session[user][maxhitpoints]-=$hpverlust;
            $session[user][hitpoints]-=$hpverlust;
}
            output("`n`n`@Du verlierst `$".$session[user][experience]*0.10."`@ Erfahrungspunkte und all dein Gold!");
            output("`n`n`@Du kannst morgen weiterspielen.");
            $session[user][alive]=false;
            $session[user][hitpoints]=0;
            $session[user][gold]=0;
            $session[user][experience]=$session[user][experience]*0.90;
            addnav("Tägliche News","news.php");
            addnews("`$ Ramius`& `4hat `b".$session[user][name]."´s`b Seele durch einen Strohhalm eingesogen ...");
            $session[user][specialinc]="";
            break;
            case 8:
            case 9:
            case 10:
            output("`@`$'Ha! Ist es Leichtsinn oder ist es Mut? In jedem Fall wäre es eine große Dummheit! Aber ich mag Deine Geradlinigkeit - eine seltene Tugend unter Euch Sterblichen. Dafür sollst Du belohnt werden! Aber zuvor begleitest Du mich noch in mein Schattenreich ...'");
            output("`$`n`nDu bist tot und Ramius verwehrt es dir, noch heute zu den Lebenden zurückzukehren!");
            output("`n`n`@Du verlierst `$".$session[user][experience]*0.15."`@ Erfahrungspunkte und all dein Gold!");
            output("`n`n$Ramius gewährt Dir `^1`@ Punkt Verteidigung!");
            output("`n`n$Ramius gewährt Dir `^1`@ Punkt Angriff!");
            output("`n`n`@Du kannst morgen weiterspielen.");
            $session[user][defence]++;
            $session[user][attack]++;
            $session[user][alive]=false;
            $session[user][hitpoints]=0;
            $session[user][gold]=0;
            $session[user][experience]=$session[user][experience]*0.85;
            $session[user][gravefights]=0;
if ($session[user][deathpower]>=100){
            $session[user][deathpower]=99;
}
            addnav("Tägliche News","news.php");
            addnews("`4`b".$session[user][name]."`b hat`$ Ramius`4 tief beeindruckt und darf einen Tag lang sein Mausoleum bewachen!");
            $sql = "INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'shade',".$session[user][acctid].",'/me `\$hat eine große Sichel dabei und postiert sich als Wache vor dem Mausoleum!')";
            db_query($sql) or die(db_error(LINK));
            $session[user][specialinc]="";
            break;
}}

case "angriffweiter3":
if ($_GET[op]=="angriffweiter3"){
               output("`@`$'Dann nieder mit Dir in den Dreck, Du erbärmlicher, ehrloser Feigling!'`@ Du tust, wie dir geheißen und wartest zitternd darauf, dass der Regen aufhört. Es vergehen Stunden in ehrloser Schande ... Dann erst wagst du es wieder aufzuschauen.`n`n Der Fremde ist nirgends zu entdecken.");
            $turns2 = e_rand(2,5);
            output("`n`n`^Du verlierst ".$turns2." Waldkämpfe!");
            $session[user][turns]-=$turns2;
            $session[user][reputation]-=3;
            $session[user][specialinc]="";
            break;
}

case "ignorieren":
if ($_GET[op]=="ignorieren"){
   output("`@Du konzentrierst dich voll und ganz auf deinen gesunden Verstand und ...");
   output("`n`n`@<a href='forest.php?op=ignorierenweiter'>Weiter.</a>", true);
   addnav("","forest.php?op=ignorierenweiter");
   addnav("Weiter.","forest.php?op=ignorierenweiter");
   break;
}

case "ignorierenweiter":
if ($_GET[op]=="ignorierenweiter"){
switch(e_rand(1,10)){
            case 1:
            case 2:
            output("`@... tatsächlich! Der Fremde war nur eine Einbildung. Du kannst weiterziehen.");
            $session[user][specialinc]="";
            break;
            case 3:
            output("`@... wirst immer unsicherer. Der Fremde schwebt vor dir, als wäre es das Normalste der Welt.`n Unter seiner Kapuze dringt schließlich eine dunkle Stimme hervor: `$'Du hast großen Mut bewiesen, mir nicht zu weichen, ".$session[user][name]."! Nimm diesen Beutel als Belohnung.'`@");
            output("Der Fremde lässt einen kleinen Beutel fallen, den du sofort aufhebst. Als du dich wieder aufgerichtet hast, fallen gerade die letzten Regentropfen von den Bäumen herab. Der Fremde ist verschwunden.");
            $gold = e_rand(500,1500);
            output("`@`n`nDu erhältst `^".$gold * $session['user']['level']."`@ Goldstücke!");
            output("`n`nDu verlierst `$1`@ Waldkampf!");
            $session[user][turns]-=1;
            addnav("Tägliche News","news.php");
            addnav("Zurück zum Wald.","forest.php");
            addnews("`4`b".$session[user][name]."`b`4 wurde für ".($session[user][sex]?"ihre":"seine")." außergewöhnliche Willensstärke von`$ Ramius`4 mit `^".$gold * $session['user']['level']."`4 Goldstücken belohnt!");
            $session['user']['gold'] += $gold * $session['user']['level'];
            $session[user][specialinc]="";
            break;
            case 4:
            case 5:
            output("`@... wirst immer unsicherer. Der Fremde schwebt vor dir, als wäre es das normalste der Welt. `nUnter seiner Kapuze dringt schließlich eine dunkle Stimme hervor: `$'Du wagst es, mir nicht zu weichen! Mir? Ramius, dem Gebieter der Toten und Schrecken der Lebenden?! Eine bodenlose Frechheit ist das!'");
            output("`@`nJetzt geht alles ganz schnell. Der Fremde prescht nach vorne und fährt in deinen Körper ein - dir schwinden die Sinne. Als du wieder aufwachst findest du dich auf dem Dorfplatz wieder - nackt! Aber immerhin unverletzt.");
            output("`n`n`@Du verlierst all dein Gold!");
            output("`n`nDu verlierst `$2`@ Waldkämpfe!");
            $session[user][turns]-=2;
			$session['user']['gold']=0;
            addnav("Tägliche News","news.php");
            addnav("Erwache auf dem Dorfplatz.","village.php");
            addnews("`@Heute herrschte großes Gelächter auf dem Dorfplatz, als `b".$session[user][name]."`b`@ nackt und bewusslos neben der Kneipe aufgefunden wurde!");
            $sql = "INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'village',".$session[user][acctid].",'/me `\@wird bewusstlos und splitterfasernackt neben der Kneipe aufgefunden!')";
            db_query($sql) or die(db_error(LINK));
            $session[user][reputation]-=2;
            $session[user][specialinc]="";
            break;
            case 6:
            case 7:
            case 8:
            case 9:
            case 10:
    output("`@... wirst immer unsicherer. Der Fremde schwebt vor dir, als wäre es das normalste der Welt. `nUnter seiner Kapuze dringt schließlich eine dunkle Stimme hervor: `$'Du hast großen Mut bewiesen, mir nicht zu weichen! Wisse, dass ich Ramius bin, der Gebieter über das Reich der Schatten. Als Belohnung für deine unglaubliche Willenskraft gewähre ich Dir `beinen`b Wunsch.`n`n Was soll ich für Dich tun?'");
    output("`n`n<a href='forest.php?op=sklave'>Ich möchte Deine unvergleichliche Macht aus nächster Nähe spüren!`n Meister, mache mich zu ".($session[user][sex]?"Deiner Sklavin":"Deinem Sklaven")."!</a>", true);
    output("`@`n`n<a href='forest.php?op=gefallen'>Gewähre mir Gefallen im Schattenreich!</a>", true);
    output("`n`n`@<a href='forest.php?op=opferung'>Nimm mein Leben zum Zeichen meiner Hochachtung!</a>", true);
    output("`n`n`@<a href='forest.php?op=wunschlos'>Ich habe keine Wünsche.</a>", true);
    addnav("","forest.php?op=sklave");
    addnav("","forest.php?op=gefallen");
    addnav("","forest.php?op=wunschlos");
    addnav("","forest.php?op=opferung");
    addnav("Sklave werden.","forest.php?op=sklave");
    addnav("Gefallen gewähren.","forest.php?op=gefallen");
    addnav("Leben verschenken.","forest.php?op=opferung");
    addnav("Wunschlos.","forest.php?op=wunschlos");
        break;
}}

case "sklave":
if ($_GET[op]=="sklave"){
  output("`$'So sei es!'`n`n'Nun wirst Du bis ans Ende aller Tage ".($session[user][sex]?"meine Sklavin":"mein Sklave")." sein! `n`nDeine Seele ist mein ... hahaha! `n`nZiehe nun aus und `bzerstöre! Bringe Unheil über die Lebenden! `n`nSofort!`b'");

//Kompatibilität mit "kleineswesen.php":
$oldname = $session[user][name];

$regname = ($row_extra['cname'] ? $row_extra['cname'] : $session['user']['login']);

  $session[user]['name'] = "`\$Ramius ".($session[user][sex]?"Sklavin":"Sklave")." ".$regname."";

  
  $sql = 'UPDATE account_extra_info SET ctitle = "`\$Ramius '.($session[user][sex]?"Sklavin":"Sklave").'" WHERE acctid='.$session['user']['acctid'];
  db_query($sql);

//Kompatibilität mit "kleineswesen.php":
//  $sql = db_query("SELECT verkleinert FROM kleineswesen");
//  $result = db_fetch_assoc($sql) or die(db_error(LINK));
// if ($oldname == "".$result[verkleinert].""){
//  db_query("UPDATE kleineswesen SET verkleinert = '".$session[user][name]."'");
//  addnav("Tägliche News","news.php");
//  addnav("Zurück zum Wald.","forest.php");
//  addnews("`4`b".$regname."`b begegnete `\$Ramius`4 und machte sich freiwillig zu ".($session[user][sex]?"seiner Sklavin":"seinem Sklaven")."!");
//  $session[user][specialinc]="";
//  break;
// }else
  addnav("Tägliche News","news.php");
  addnav("Zurück zum Wald.","forest.php");
  addnews("`4`b".$regname."`b begegnete `\$Ramius`4 und machte sich freiwillig zu ".($session[user][sex]?"seiner Sklavin":"seinem Sklaven")."!");
  $session[user][specialinc]="";
  break;
}

//Noch nicht implementiert
//
// case "niederstrecken":
//if ($_GET[op]=="niederstrecken"){
//    $session[user][specialinc]="";
//}

//Noch nicht implementiert
//
// case "erwecken":
//if ($_GET[op]=="erwecken"){
//    $session[user][specialinc]="";
//}

case "gefallen":
if ($_GET[op]=="gefallen"){
  $gefallen= e_rand(10,150);
  output("`$ 'So sei es!'");
  output("`$`n`nRamius gewährt Dir `^".$gefallen."`\$ Gefallen!");
  $session[user][deathpower]+=$gefallen;
  $session[user][specialinc]="";
  break;
}

case "opferung":
if ($_GET[op]=="opferung"){
  output("`$ 'So sei es!'");
  output("`$`n`nDu bist tot!");
  output("`n`n`\$Du kannst morgen weiterspielen.");
  $session[user][alive]=false;
  $session[user][hitpoints]=0;
  $session[user][gold]=0;
  addnav("Tägliche News","news.php");
  addnews("`\$ Aus unerfindlichen Gründen hat `b".$session[user][name]."`b`\$ ".($session[user][sex]?"ihr":"sein")." Leben an Ramius verschenkt!");
  $sql = "INSERT INTO commentary (postdate,section,author,comment) VALUES (now(),'shade',".$session[user][acctid].",'/me `\$kehrt heute aus freien Stücken in das Schattenreich ein - ".($session[user][sex]?"ihr":"sein")." Leben ein Geschenk an Ramius!')";
  db_query($sql) or die(db_error(LINK));
  $session[user][specialinc]="";
  break;
}

case "wunschlos":
if ($_GET[op]=="wunschlos"){
  output("`$ 'Bemerkenswert! `bÄußerst`b bemerkenswert ...'");
  $session[user][reputation]+=10;
  addnews("`@Von`$ Ramius`@ vor die Wahl gestellt erwies sich `b".$session[user][name]."`b `@als wunschlos glücklich ...");
  $session[user][specialinc]="";
  break;
}
}
?>
