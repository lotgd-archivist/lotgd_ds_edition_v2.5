<?php
/**
* chat.php: ein Java-basierter IRC-Client für lotgd angepasst
* benötigt einen IRC-Server und einen Server der das Applet zur Verfügung stellt
* Dateien: superuser.php (initial link) su_chat.php (management)
* @author Salator <salator@gmx.de>
* @version DS-E V/2
*/

require_once('common.php');

$irc_active=getsetting(irc_active,0);
$su_allowed_minlevel=getsetting(irc_su_minlevel,1);
$irc_appletserver=getsetting(irc_appletserver,'http://irc.lotgd.net/');	
$irc_chatserver=getsetting(irc_server,'irc.lotgd.net');
$irc_server_port=getsetting(irc_server_port,'6667');
$channel=getsetting('irc_channel','LotgD');
$cpw=getsetting('irc_cpw','');
$nickname='Drachentoeter_??';	
$password='';
$quitmessage='Ich muß den Drachen töten!';

if(su_lvl_check($su_allowed_minlevel['irc_su_minlevel'])) popup_header('IRC - Server: '.$irc_chatserver.' Port: '.$irc_server_port.' Channel: '.$channel);
else popup_header('Chat');
$row=user_get_aei(chatallowed);

if($_GET[op]=='chat') //load the applet and chat
{
  output('<script language="javascript" type="text/javascript">window.resizeTo(800,600);</script>');
  if (isset($_POST["nick"]) && $_POST["nick"]!="") {$nickname=$_POST["nick"];}
  if (isset($_POST["pass"])) $password=$_POST["pass"];

  if($_POST["chattype"]=="eIRC") //Dieser Chat wird von bbox.ch nicht unterstützt
  {
/*  ?>
    <applet code="EIRC" archive="EIRC.jar,EIRC-gfx.jar" width="100%" height="100%" codebase="http://werde-legende.de/~webchat/standard/">
      <param name="server" value="irc.bbox.ch" />
        <param name="port" value="6667" />
       <!--param name="mainbg" value="#424242" /-->
        <param name="mainbg" value="#006600" />
        <param name="mainfg" value="#000000" />
        <param name="textbg" value="#000000" />
        <param name="textfg" value="#FFFFFF" />
        <param name="selbg" value="#00007F" />
        <param name="selfg" value="#13220C" />
        <param name="channel" value="#atrahor" />
        <param name="titleExtra" value="Atrahor Chat" />
       <!--<param name="username" value="SGsChat" /> /-->
        <param name="realname" value="WebChat" />
<?php 
 echo '<param name="username" value="',$nickname,'" /> ';
 echo '<param name="nickname" value="',$nickname,'" /> ';
 echo '<param name="password" value="',$passwort,'" /> '; 
?>

       <!--param name="servPassword" value="" /-->
       <!--param name="servEmail" value="" /-->
        <param name="quitmessage" value="Ich muß den Drachen töten!" />
        <param name="login" value="1" />
       <!--param name="spawn_frame" value="1" /-->
       <!--param name="frame_width" value="600" /-->
       <!--param name="frame_height" value="400" /-->
        <param name="language" value="de" /-->
        <param name="country" value="DE" /-->
  <h1>Java - IRC - Client</h1><p>Sorry, aber Du musst Java installiert und unterstützt haben. 
Nähere Informationen und DownloadMöglichkeiten findest Du unter <a href="http://www.IRC-Mania.de/java.php">http://www.IRC-Mania.de/java.php</a> 
<a href="http://www.IRC-Mania.de">IRC-Mania.de</a></p></applet>
  <?php
*/
  }

  else if($_POST["chattype"]=="pJirc") //http://www.pjirc.com
  {
    output('<applet code=IRCApplet.class archive="irc.jar,pixx.jar" width=765 height=450 codebase="'.$irc_appletserver.'">
     <param name="CABINETS" value="irc.cab,securedirc.cab,pixx.cab">
     <param name="pixx:lngextension" value="txt"> 
     <param name="lngextension" value="txt">
     <param name="pixx:language" value="pixx-german"> 
     <param name="language" value="german">
     <param name="nick" value="'.$nickname.'">
     <param name="password" value="'.$password.'">
     <param name="name" value="'.$nickname.'">
     <param name="host" value="'.$irc_chatserver.'">
     <param name="gui" value="pixx">
     <param name="quitmessage" value="'.$quitmessage.'">
     <param name="asl" value="true">
     <param name="useinfo" value="false">
     <param name="command1" value="/join '.$channel.' '.$cpw.'">
     <param name="style:bitmapsmileys" value="true">
     <param name="style:smiley1" value=":) img/sourire.gif">
     <param name="style:smiley2" value=":-) img/sourire.gif">
     <param name="style:smiley3" value=":-D img/content.gif">
     <param name="style:smiley5" value=":-O img/OH-2.gif">
     <param name="style:smiley6" value=":o img/OH-1.gif">
     <param name="style:smiley7" value=":-P img/langue.gif">
     <param name="style:smiley8" value=":p img/langue.gif">
     <param name="style:smiley9" value=";-) img/clin-oeuil.gif">
     <param name="style:smiley10" value=";) img/clin-oeuil.gif">
     <param name="style:smiley11" value=":-( img/triste.gif">
     <param name="style:smiley12" value=":( img/triste.gif">
     <param name="style:smiley13" value=":-| img/OH-3.gif">
     <param name="style:smiley14" value=":| img/OH-3.gif">
     <param name="style:smiley15" value=":\'( img/pleure.gif">
     <param name="style:smiley16" value=":$ img/rouge.gif">
     <param name="style:smiley17" value=":-$ img/rouge.gif">
     <param name="style:smiley18" value="(H) img/cool.gif">
     <param name="style:smiley19" value="(h) img/cool.gif">
     <param name="style:smiley20" value=":-@ img/enerve1.gif">
     <param name="style:smiley21" value=":@ img/enerve2.gif">
     <param name="style:smiley22" value=":-S img/roll-eyes.gif">
     <param name="style:smiley23" value=":s img/roll-eyes.gif">
     <param name="style:backgroundimage" value="false">
     <param name="style:backgroundimage1" value="all all 0 YOUR_LOGO.gif">
     <param name="style:floatingasl" value="true">
     <param name="soundbeep" value="snd/bell2.au">
     <param name="soundquery" value="snd/ding.au">
     <param name="pixx:showabout" value="false">
     <param name="pixx:helppage" value="http://irc.bbox.ch/help.htm">
     <param name="pixx:timestamp" value="true">
     <param name="pixx:highlight" value="true">
     <param name="pixx:highlightnick" value="true">
     <param name="pixx:nickfield" value="false">
     <param name="pixx:styleselector" value="true">
     <param name="pixx:setfontonstyle" value="true">
     <param name="pixx:color0" value="000000">
     <param name="pixx:color2" value="C0C0C0">
     <param name="pixx:color3" value="003366">
     <param name="pixx:color5" value="6b563f">
     <param name="pixx:color6" value="433828">
     <param name="pixx:color9" value="6b563f">
     <param name="pixx:color15" value="433828">
     <br><div align="center"><font color=888888>Diese Applikation benötigt Java-Support - Der Chatraum ist aber auch via IRC-Client erreichbar unter:<br>
     <a href="irc://'.$irc_chatserver.':'.$irc_server_port.'/">Server: '.$irc_chatserver.'</a> * Port: '.$irc_server_port.' * Channel: '.$channel.'<br></font>
     </div>
    </applet>');
/*
colors
0 : Button Highlight / Popup & Close Button Text & Higlight / Scrollbar Highlight (Black)
1 : Button Border & Text : ScrollBar Border & arrow : Popup & Close button Border : User List border & Text & icons (Default Black)
2 : Popup & Close button shadow (Default Dark Grey)
3 : Scrollbar shadow (Default Dark Grey)
4 : Scrollbar de-light (3D Dim colour (Default Light Grey))
5 : foreground : Buttons Face : Scrollbar Face (Default Light Blue)
6 : background : Header : Scrollbar Track : Footer background (Default Blue)
7 : selection : Status & Window button active colour (Default Red)
8 : event Color (Default Red)
9 : close button
10 : voice icon
11 : operator icon
12 : halfoperator icon
13 : male ASL
14 : female ASL
15 : unknown ASL
*/
  }
  else echo('Kein chattype angegeben');
}
else if($irc_active && (su_lvl_check($su_allowed_minlevel['irc_su_minlevel'])||$row[chatallowed]==1)) //check chat-rights, set nickname, input optional password
{
addnav('Zurück zum Dorf','village.php');

    $nick=$session[user][login];
    // Accents ersetzen
    //$arr_srch = array( ' ','è','é','ê','à','á','â','ä', 'ì','í','î','ò','ó','ô','ö', 'ù','ú','û','ü','\'' );
    //$arr_repl = array( '_','e','e','e','a','a','a','ae','i','i','i','o','o','o','oe','u','u','u','ue','' );
    //funchat.net erlaubt Umlaute
    $arr_srch = array( ' ','\'' );
    $arr_repl = array( '_','' );
    $nick = str_replace($arr_srch, $arr_repl, $nick);
    $nick=substr($nick,0,29);

    output('`n`n`cWenn Du ein Chat-Passwort hast kannst Du es hier eingeben`n`n');
    output('<form name=chat action="chat.php?op=chat" method=POST>
      <input type=password name=pass>`n`n
      <input type=hidden name=nick value='.$nick.'>
      <input type=hidden name=chattype value=pJirc>
      <input type=submit value="Eintreten">');
    output('`n`n`&Dein Nickname wird so aussehen: `#'.$nick);
//    output('`&`n`nDu kannst ihn mit dem Befehl `#/nick neuerName`& ändern');
//    output('`n`n`4Das Chat-Applet hat nichts mit LotGD zu tun, Fehleranfragen diesbezüglich sind zwecklos.`c');
//    output('`n`n`&Teststatus! `7Wer Sonderzeichen im Login hat und deswegen einen verstümmelten Namen hat möge sich bitte per Anfrage melden.`c');
    output('`n`n<table width=100% border=0><tr><td colspan=2 align=center>`QFAQ`0</td></tr>');
    output('<tr><td valign=top>`4Q:</td><td>`0Wie schütze ich meinen Nicknamen vor Mißbrauch?`n</td></tr>
            <tr><td valign=top>`@A:</td><td>`0Indem du ihn registrierst. Tippe dazu `#/msg nickserv register DEIN_PASSWORT DEINE_MAILADRESSE`0 und folge den Anweisungen.`nDas Passwort welches du dabei nutzt gibst du später immer in das Feld oben ein.</td></tr>
            <tr><td colspan=2><hr></td></tr>');
    output('<tr><td valign=top>`4Q:</td><td>`0Ich erhalte die Meldung `4Kann nicht verbinden mit : java.security.AccessControlException : access denied (java.net.SocketPermission irc.lotgd.net resolve)`0`n</td></tr>
            <tr><td valign=top>`@A:</td><td>`0Du mußt deine Java-Version auf den neuesten Stand bringen.</td></tr>
            <tr><td colspan=2><hr></td></tr>');

    output('</table>');
}
else //chat forbidden
{
    output('Hier gibt es keinen Chat oder das Adminteam hat entschieden daß Du nicht chatten darfst.');
}
popup_footer();
?>