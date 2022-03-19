<?php

//Idee und Umsetzung    
//Morpheus aka Apollon  
//für lotgd.at       
//Mail to: Apollon@magic.ms
//
//Die Insel ist für die pool.php gemacht, kann aber auch ganz einfach in einem anderen See im Garten sein.
//Instalation: In den Hauptordner kopieren, pool.php öffnen und suchen:
//addnav("Ufer des Sees");
//dahinter einfügen:
//addnav("W?Zur Wolkeninsel","wolkeninsel.php");
//ansonsten wo gewünsct verlinken

require_once "common.php";

page_header('Die Wolkeninsel');
        output('`c`b`@Die Wolkeninsel`b`c`n');
if ($_GET['op']==""){
	addnav('Weiter','wolkeninsel.php?op=insel');
        output('`@Du gehst am Ufer des Flusses entlang bis zu der kleinen, weißen Brücke, die zum anderen Ufer führt, welches ständig im Nebel liegt und deshalb vom Garten nicht gesehen werden kann.`nVorsichtig gehst Du, Schritt für Schritt, über den Steg auf die andere Seite, als sich der Nebel lichtet und du eine Insel mit völlig anderem Wetter inmitten der Wolken erblickst.`nDer Himmel über dir ist klar und blau, die Sonne scheint ');
	switch(e_rand(1,10))
		{
		case 1:
		output('und die Vögel singen fröhlich ihre Lieder, während kleine `^Feen `@lustig dazu tanzen.`n`n');
		break;
		case 2:
		output('und Du gehst über dieses wundervolle Fleckchen Erde, jeden Schritt genießend, zum `&Pavillon`@.`n`n');
		break;
		case 3:
		output('und ein `TEichhörnchen `@kreuzt Deinen Weg, sieht Dich verschmitzt an und läuft lustig quiekend zum nächsten Baum.`n`n');
		break;
		case 4:
		output('und 2 `&Schwäne `@watscheln verliebt über die Wiese bis zum See, in dem sie schließlich gemeinsam davon schwimmen.`n`n');
		break;
		case 5:
		output('und eine `vEntenmutter `@führt ihre Jungen, quer über die Wiese, zu ihrer ersten Schwimmstunde zum See.`n`n');
		break;
		case 6:
		output('und die Luft ist klar und warm, wie an einem schönen `6Sommertag`@.`n`n');
		break;
		case 7:
		output('und Dein `$Herz `@beginnt höher zu schlagen bei diesem wundervollen, traumhaft schönen Anblick.`n`n');
		break;
		case 8:
		output('und Du fühlst Dich, als ob Du soeben hier `6neu geboren`@ worden wärst im Paradies.`n`n');
		break;
		case 9:
		output('und Du glaubst, auf der Insel der `^Götter`@ zu sein, so wunderschön und ruhig wie dieser Ort ist.`n`n');
		break;
		case 10:
		output('und Du fühlst Dich `6seelig `@und zufrieden, diesen wundervollen Ort gefunden zu haben.`n`n');
		break;
	}
}
else if ($_GET['op']=="insel"){
        output('`@In der Mitte der Insel steht ein kleiner Pavillon, umringt von Bäumen auf einer Wiese, durch die ein sanfter Wind weht und Geschichten erzählt von der Liebe.`nAm Ufer ist ein Strand aus feinem, weißem Sand der zum Baden einlädt. Der Boden unter Dir scheint so sanft und weich, daß Du glaubst, auf Wolken zu wandeln.`nÜberall blühen Blumen in den schönsten Farben und ein kleines Rinnsal bahnt sich, lustig plätschernd, seinen Weg zum See, während Du hier und da kleine Feen sehen kannst, die sich im lustigen Tanze in der Luft bewegen.`n`n');
        addcommentary();
        viewcommentary("wolkeninsel","Hier flüstern:`n",20,"flüstert");
        addnav("Wandern");
        addnav("G?zum Garten","gardens.php");
        addnav("D?zum Dorf","village.php");
}
page_footer();
?>