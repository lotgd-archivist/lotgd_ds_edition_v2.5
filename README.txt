Legend of the Green Dragon
by Eric "MightyE" Stevens
http://www.mightye.org

Original Software Project Page:
http://sourceforge.net/projects/lotgd

Primary game server:
http://lotgd.net

########################
Das erste deutsche Release des Spielkerns wurde von Anpera erstellt und ist noch immer
als LoGD 0.9.7+jt ext (GER) unter http://www.anpera.net erh�ltlich.

Die hier vorliegende Version basiert auf der Arbeit von Anpera. Es handelt sich um eine stark erweiterte und optimierte Version von http://lotgd.drachenserver.de, auch bekannt als LoGD 0.9.7+jt ext (GER) Dragonslayer Edition V/2.5

Sie enth�lt viele Erweiterungen und Verbesserungen, die sie einzigartig machen, allerdings auch inkompatibel zu vielen Modifikationen, die im Internet zu finden sind.


######################
INSTALLATIONSANLEITUNG
######################


UPDATE VON EINER BESTEHENDEN INSTALLATION
=========================================

Mit der Version V/2.5 haben wir zum ersten mal den kompletten Update Pfad mitdokumentiert.
Solltest du bereits eine funktionierende Version 2 auf deinem Webserver installiert haben, so gestaltet sich ein Update f�r dich sehr einfach.
1. Mache ein Backup sowohl von den Dateien als auch von deiner Datenbank (!)
2. Ernsthaft! Mach ein Backup!
3. L�sche alle alten lotgd - Dateien auf deinem Webserver mit Ausnahme deiner 
   dbconnect.php
4. Lade alle Dateien aus diesem Archiv in das jetzt leere Verzeichnis mit Ausnahme der 
   dbconnect.php.dist
5. Hast Du ein Backup Deiner Datenbank gemacht? Gut!
6. F�hre alle SQL Anfragen aus, die sich in der f�r Dich passenden Update-Datei befinden.   
   Die Update Datei folgt dem folgenden Namensschema:
   lotgd_update_alte_version-neue_version.sql
   
   Wenn Du bspw. eine bestehende Installation der V/2 zu einer V/2.5 machen willst, 
   dann verwende die Datei
   ------------------------
   lotgd_update_v2-v2.5.sql 
   ------------------------
   
   Wenn Du bspw. eine bestehende Installation der V/2 zu einer V/3 machen willst, 
   dann verwende zun�chst die Datei 
   ------------------------
   lotgd_update_v2-v2.5.sql 
   ------------------------
	 und dann
	 ------------------------
   lotgd_update_v2.5-v3.sql 
   ------------------------   

Ein Update von einer �lteren oder inkompatiblen Version wie 
LoGD 0.9.7+jt ext (GER), LOTGD 1.x oder auch 
LoGD 0.9.7+jt ext (GER) Dragonslayer Edition V/1
wird leider nicht unterst�tzt, da auf Grund der stetigen Entwicklung dieses Releases signifikante Teile ge�ndert worden sein k�nnen, die sich nicht ohne gr��eren Aufwand auf andere Installationen �bertragen lassen.

Es ist technisch nicht unm�glich, schlie�lich haben wir es ja auch gemacht,allerdings geben wir keinen Support.


INSTALLATION:
================
Um dieses Paket installieren zu k�nnen brauchst Du
einen Webspace mit 

- mindestens 10MB Speicherplatz
- PHP 4.4.1 oder h�her (PHP 5.1 Kompatibilit�t ist nicht vollst�ndig getestet, sollte aber funktionieren)
- MySQL 4 oder MySQL 5 (getestet mit beiden Versionen, auf MySQL 5 optimiert)
- (Optional) phpMyAdmin zum administrieren der Datenbank

MySQL Setup:
Das Erstellen der ben�tigten Datenbanken sollte recht einfach und problemlos von Statten gehen.
Erstelle eine Datenbank oder verwende eine bereits vorhandene Datenbank.
Achte darauf, dass der User, der Zugriff auf die Datenbank hat zumindest die folgenden Rechte 
f�r die Datenbank besitzt:
"Select Table Data", "Insert Table Data", "Update Table Data", 
"Delete Table Data", "Manage indexes", "Lock tables"

F�hre anschlie�end alle Befehle im SQL Script 
-----------------
lotgd_install.sql 
-----------------
aus, um die ben�tigten Tabellen zu erstellen und mit einigen Daten zu f�llen.


PHP Setup:
==========
Lade alle Dateien und Ordner aus diesem Archiv auf deinen Webspace in das Verzeichnis aus dem das Spiel sp�ter gestartet werden soll.
Bearbeite nun die Datei
------------------
dbconnect.php.dist
------------------
und f�ge dort deine Zugangsdaten zum MySQL Server und der entsprechenden LOTGD Datenbank ein.

$DB_USER="Dein_DB_Username"; //Wurde dir von deinem Provider mitgeteilt
$DB_PASS="Dein_DB_Passwort"; //Kennst du selbst am Besten
$DB_HOST="meistens localhost"; //Wurde dir von deinem Provider mitgeteilt
$DB_NAME="Dein_DB_Name"; //Name der Datenbank

Benenne nun die Datei um und (wenn m�glich) �ndere die Zugriffsrechte derart, dass die Datei von niemandem �berschrieben werden kann (chmod -w dbconnect.php) und nur der Webserver und niemand sie sonst lesen kann. (chown webservername dbconnect.php - Shellzugriff n�tig)
-----------------------------------
dbconnect.php.dist -> dbconnect.php
-----------------------------------

Achte darauf, dass dein Webserver Schreibrechte f�r den templates/ Ordner besitzt, denn dort hinein schreiben wir die Datei, welche die Farben den einzelnen Farbtags zuordnet (colors.css). Sollte die Datei bereits existieren, so reicht es aus Schreibrechte f�r die Datei zu erteilen (chmod +w colors.css), ansonsten kannst du es auch erlauben den gesamten Ordner beschreibbar zu machen (chmod +w templates/ ) 

Spielstart:
===========
Das Spiel ist nun installiert und l�sst sich �ber einen Webbrowser aus dem Installationsverzeichnis heraus starten. Als erstes solltest Du Dich als Admin einloggen.
W�hrend der Installation wurde ein User
-----------------------------------
Username: Admin, Passwort: CHANGEME
-----------------------------------
erzeugt, mit dem du in die Superuser-Grotte gehen und das Spiel deinen W�nschen anpassen kannst. Die Spieleinstellungen sind vielf�ltig, also nimm dir hierf�r Zeit, �ndere jedoch zuvor schleunigst sowohl deinen Usernamen als auch dein Passwort �ber den User Editor!
Sobald du dich das erste mal einloggst bekommst du den Titel "F�rst von Atrahor". �ndere in den Spieleinstellungen den Titel des Dorfes. Wenn du der F�rst sein willst, dann muss dein Titel im Usereditor auf F�rst von "Dorfname" umbenannt werden.


Probleme?
=========
F: Ich kann mich nicht mit dem oben genannten Usernamen und Passwort einloggen!
A: F�hre das folgende SQL Kommando aus, um f�r den Admin User das Passwort festzulegen:
UPDATE accounts SET password=md5('DEIN PASSWORT') WHERE acctid=1; 
A: Erlaube Cookies und Javascript f�r die Domain unter der das Spiel installiert wurde. 

F: Ich erhalte seltsame Zeichen anstelle der Umlaute ����
A: Dein Apache Webserver ist nicht korrekt eingestellt. Bitte deinen Serveradmin darum die configuration des Apache um die Zeile
AddDefaultCharset ISO-8859-1
zu erg�nzen, dann klappt alles prima!

F: Alle Texte sind so grau in grau, auf anderen Servern ist das viel bunter
A: Bei einer frischen Installation existiert die Datei colors.css noch nicht. Diese Datei wird erst dann erzeugt, wenn in der Admingrotte der Farbeneditor aufgerufen und der Link "CSS Datei schreiben" angeklickt wurde.

F: Ich erhalte im Gerichtshof bei der Betrachtung der aktuellen verd�chtigen Taten einen SQL Fehler.
A: Deine SQL Version ist zu alt. Update auf Version 4.1.1 (mindestens) oder �ffne die Datei court.php und suche die folgende Zeile:
/** If you are using mysql < ver 4.1.1 try using the following query :
Befolge die dortigen Anweisungen.

F: Der MOTD Link leuchtet permanent und bei jedem Seitenaufruf wird ein Popup ge�ffnet
A: Erstelle als Admin eine neue MOTD (zum Beispiel Begr��ungstext f�r neue Spieler), dann ist das Problem behoben!

F: Beim klicken auf einen "zur�ck" Link aus einer Spielerbiographie erhalte ich manchmal einen SQL Fehler.
A: Tja, der Fehler ist bekannt, wir arbeiten daran *g*

F: Ich bekomme direkt nach der Installation einen Fehler der besagt, dass Windows nicht mit so einem kleinen Datum umgehen kann.
A: PANIK !!! Nein, keine Sorge, beim ersten Start des Spiels werden viele Variablen auf einen Standardwert gesetzt und in der DB gespeichert. Dabei kann es auf manchen Servern zu Fehlern kommen. Einfach neu laden und dann ist alles bueno!


##########
Dankesch�n
##########

Das Spiel unter Atrahor.de/lotgd.drachenserver.de w�re nicht entstanden oder �berhaupt so weit gekommen, wenn es da nicht die vielen kleinen Helferlein g�be, die ihr Leben, ihre Freizeit und ihre Sozialf�higkeit selbstlos aufgegeben h�tten. Aus diesem Grunde danken ich den folgenden Spielern ganz besonders herzlich:

In alphabetischer Reihenfolge nach $session['user']['superuser'] gruppiert ;-) 

Admins :
--------
*S�m�ya Af�ye Jaheira
*Lady Datuaria Morticia
*Feldherr Ibga
*Elent�ri Nuir Sith

Mods :
------
*Actuarius David
*Arc Firzerza Salvan
*Arc mandra Sersee
*Donneraxt LOKI
*Falknerin Laulajatar
*L�rd Sephias
*Nigromant Tyrael
*Selige O-Ren-Ishi
*Siniestro Drakhan
*Splitterherz Niphredil

Codewiesel :
------------
�Ang�lique Fossla
�Chevalier Salator
�Cirdan Mikay Kun
�Farbfinsternis Alucard
�Siv�ntri�l Talion
�Sturmreiter Maris
