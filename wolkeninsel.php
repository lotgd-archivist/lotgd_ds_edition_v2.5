<?php

//Idee und Umsetzung    
//Morpheus aka Apollon  
//f�r lotgd.at       
//Mail to: Apollon@magic.ms
//
//Die Insel ist f�r die pool.php gemacht, kann aber auch ganz einfach in einem anderen See im Garten sein.
//Instalation: In den Hauptordner kopieren, pool.php �ffnen und suchen:
//addnav("Ufer des Sees");
//dahinter einf�gen:
//addnav("W?Zur Wolkeninsel","wolkeninsel.php");
//ansonsten wo gew�nsct verlinken

require_once "common.php";

page_header('Die Wolkeninsel');
        output('`c`b`@Die Wolkeninsel`b`c`n');
if ($_GET['op']==""){
	addnav('Weiter','wolkeninsel.php?op=insel');
        output('`@Du gehst am Ufer des Flusses entlang bis zu der kleinen, wei�en Br�cke, die zum anderen Ufer f�hrt, welches st�ndig im Nebel liegt und deshalb vom Garten nicht gesehen werden kann.`nVorsichtig gehst Du, Schritt f�r Schritt, �ber den Steg auf die andere Seite, als sich der Nebel lichtet und du eine Insel mit v�llig anderem Wetter inmitten der Wolken erblickst.`nDer Himmel �ber dir ist klar und blau, die Sonne scheint ');
	switch(e_rand(1,10))
		{
		case 1:
		output('und die V�gel singen fr�hlich ihre Lieder, w�hrend kleine `^Feen `@lustig dazu tanzen.`n`n');
		break;
		case 2:
		output('und Du gehst �ber dieses wundervolle Fleckchen Erde, jeden Schritt genie�end, zum `&Pavillon`@.`n`n');
		break;
		case 3:
		output('und ein `TEichh�rnchen `@kreuzt Deinen Weg, sieht Dich verschmitzt an und l�uft lustig quiekend zum n�chsten Baum.`n`n');
		break;
		case 4:
		output('und 2 `&Schw�ne `@watscheln verliebt �ber die Wiese bis zum See, in dem sie schlie�lich gemeinsam davon schwimmen.`n`n');
		break;
		case 5:
		output('und eine `vEntenmutter `@f�hrt ihre Jungen, quer �ber die Wiese, zu ihrer ersten Schwimmstunde zum See.`n`n');
		break;
		case 6:
		output('und die Luft ist klar und warm, wie an einem sch�nen `6Sommertag`@.`n`n');
		break;
		case 7:
		output('und Dein `$Herz `@beginnt h�her zu schlagen bei diesem wundervollen, traumhaft sch�nen Anblick.`n`n');
		break;
		case 8:
		output('und Du f�hlst Dich, als ob Du soeben hier `6neu geboren`@ worden w�rst im Paradies.`n`n');
		break;
		case 9:
		output('und Du glaubst, auf der Insel der `^G�tter`@ zu sein, so wundersch�n und ruhig wie dieser Ort ist.`n`n');
		break;
		case 10:
		output('und Du f�hlst Dich `6seelig `@und zufrieden, diesen wundervollen Ort gefunden zu haben.`n`n');
		break;
	}
}
else if ($_GET['op']=="insel"){
        output('`@In der Mitte der Insel steht ein kleiner Pavillon, umringt von B�umen auf einer Wiese, durch die ein sanfter Wind weht und Geschichten erz�hlt von der Liebe.`nAm Ufer ist ein Strand aus feinem, wei�em Sand der zum Baden einl�dt. Der Boden unter Dir scheint so sanft und weich, da� Du glaubst, auf Wolken zu wandeln.`n�berall bl�hen Blumen in den sch�nsten Farben und ein kleines Rinnsal bahnt sich, lustig pl�tschernd, seinen Weg zum See, w�hrend Du hier und da kleine Feen sehen kannst, die sich im lustigen Tanze in der Luft bewegen.`n`n');
        addcommentary();
        viewcommentary("wolkeninsel","Hier fl�stern:`n",20,"fl�stert");
        addnav("Wandern");
        addnav("G?zum Garten","gardens.php");
        addnav("D?zum Dorf","village.php");
}
page_footer();
?>