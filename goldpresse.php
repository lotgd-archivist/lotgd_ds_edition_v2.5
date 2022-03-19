<?php

// A Flag for your community
// [by Joshua Schmidtke]
//
// idea and coding by: Joshua Schmidtke [alias Mikay Kun]
// 
// build: 2006-08-08
// version: BETA

require_once("common.php");

popup_header("Die magische Goldpresse");

# Objektbau ANFANG ##############################################################

if ($_POST[op]=="save")
	{
	if (file_exists("images/goldpresse/".$session['user']['acctid'].".png"))
		{ output('`4Bild erneuert!`n`n'); }
		
	else
		{ output('`4Bild erstellt!`n`n'); }
		
	$resextra=db_query("SELECT charclass, birthday FROM account_extra_info WHERE acctid=".$session['user']['acctid']);
	$rowextra=db_fetch_assoc($resextra);

	$arr_race = race_get($session['user']['race'],true);
	
	$row['name']=ereg_replace("`([^>])", "", $session['user']['name']);
	$row['race']=ereg_replace("`([^>])", "", $arr_race['colname']);
	$row['dragonkills']=$session['user']['dragonkills'];
	$row['armor']=ereg_replace("`([^>])", "", $session['user']['armor']);
	$row['weapon']=ereg_replace("`([^>])", "", $session['user']['weapon']);
	$row['level']=$session['user']['level'];
	$row['birthday']=$rowextra['birthday'];

	$image = imagecreatefrompng("images/goldpresse/vorlage".$_POST['vorlage'].".png");
	$farbe_b = imagecolorallocate($image,255,255,0);
	$farbe_l = imagecolorallocate($image,153,153,153);
	$farbe_g = imagecolorallocate($image,0,255,0);
	$farbe_r = imagecolorallocate($image,255,0,0);

	imagestring ($image, 4,10, 10, "Name:", $farbe_b);
	imagestring ($image, 4,53, 10, $row['name'], $farbe_g);
	imagestring ($image, 1,10, 23, "_______________________________________________________________________________", $farbe_l);

	imagestring ($image, 2,10, 35, "Drachenkills:", $farbe_b);
	imagestring ($image, 2,95, 35, $row['dragonkills'], $farbe_g);

	imagestring ($image, 2,10, 54, "Level:", $farbe_b);
	imagestring ($image, 2,50, 54, $row['level'], $farbe_g);

	imagestring ($image, 2,10, 72, "Rasse:", $farbe_b);
	imagestring ($image, 2,50, 72, $row['race'], $farbe_g);

	imagestring ($image, 2,150, 35, "Waffe:", $farbe_b);
	imagestring ($image, 2,205, 35, $row['weapon'], $farbe_g);

	imagestring ($image, 2,150, 54, "Rüstung:", $farbe_b);
	imagestring ($image, 2,205, 54, $row['armor'], $farbe_g);
	
	if (getsetting('activategamedate','0')==1 && $row['birthday']!='')
		{
		imagestring ($image, 2,150, 72, "Ankunft:", $farbe_b);
		imagestring ($image, 2,205, 72, getgamedate($row['birthday']), $farbe_g);
		}

	imagestring ($image, 1,10, 90, "_______________________________________________________________________________", $farbe_l);
	imagestring ($image, 2,11, 100, "Server: http://".$_SERVER['SERVER_NAME'], $farbe_b);
	
	imagestring ($image, 1,342, 103, "by Mikay Kun", $farbe_b);

	imagepng($image, "images/goldpresse/".$session['user']['acctid'].".png"); 
	}
	
# Objektbau ENDE ################################################################

output('<script language="javascript">window.resizeTo(640,400);</script>',true);

output('`tWillkommen zur `bmagischen Goldpresse`b. Hier kannst du für deine Signatur in Foren und andere nette Orte ein Aushängeschild deines Charakters pressen.');
output('<form method="post" action="goldpresse.php">',true);

if (file_exists("images/goldpresse/".$session['user']['acctid'].".png"))
	{
	output('<img src="ci_images.php?id='.$session['user']['acctid'].'">`n`n',true);
	output('`&[ Adresse: http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['REQUEST_URI']).'ci_images.php?id='.$session['user']['acctid'].' ]`n`n');
	}
	
output('<hr>`t`n',true);
output('`bDesign:`b`n`n');
output('<input type="radio" name="vorlage" value="01" checked> Dragonslayer-Design`n');
output('<input type="radio" name="vorlage" value="02"> DarkStar`n');

output('`n[ Designänderung wird nach einer erneuten Aktualisierung sichtbar. ]');
	
if (file_exists("images/goldpresse/".$session['user']['acctid'].".png"))
	{
	output('`n`n`^Da du bereits ein Aushängeschild hast kannst du es aktualiesieren lassen.`n`n');
	output('<input type="submit" value="Aktualisieren"><input type="hidden" value="save" name="op">',true);
	}
	
else
	{
	output('`n`n`^Da du noch kein Aushängeschild besitzt kannst du es dir mit der Goldpresse anfertigen.`n`n');
	output('<input type="submit" value="Pressen"><input type="hidden" value="save" name="op">',true);
	}
	
output('</form>',true);

popup_footer(false);

?>