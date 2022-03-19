<?
/**
* music.php: 	Stellt Seitenrahmen für einen Musik- / Streaming-Player zwecks Abspielen der Ingame-Musik
*				zur Verfügung
* @author talion
* @version DS-E V/2
*/

// Ausgabestring
$str_out = '';
	
require_once('common.php');

if(!$session['user']['loggedin']) {
	exit;
}

/*if($session['user']['superuser'] <= 0 && 'Masher' != $session['user']['login']) {
	exit;
}*/

// Feststellen, ob Spieler über benötigtes Item verfügt
/*if(!item_count(' tpl_id="brde" AND owner='.$session['user']['acctid'])) {
	exit;
}*/

// Seitenstart
popup_header('Der Wanderbarde');
	
$str_out .= '<div align="center">		
					<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="300" height="150" id="mp3player"
							codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" >
						<param name="movie" value="media/player/mp3player.swf?config=media/player/config.xml&file=media/player/playlists/theme.xml" />
					    <param name="wmode" value="transparent" />
					    <param nam="allowScriptAccess" value="always">
					    <embed src="media/player/mp3player.swf?config=media/player/config.xml&file=media/player/playlists/theme.xml" wmode="transparent" width="300" height="150" name="mp3player" id="mp3pl"  
					    	type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" allowScriptAccess="true" />
					</object>
					<br />
					[ <a href="#" onclick="window.opener.player_active_time=window.opener.player_start_time;">Aktualisieren</a> ]
					<p>Falls der Player nicht korrekt funktioniert, lade dir bitte <a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" target="_blank">hier</a> eine aktuelle Version des Flash-Plugins!</p>
				</div>
				<script>
					window.resizeTo(500,400);
					window.opener.player_active_time = window.opener.player_start_time;
					window.opener.current_pl = (!window.opener.current_pl ? "theme" : window.opener.current_pl);
				
					window.setInterval("load_pl()",1000);
					
					var playing = "";
					
					function load_pl () {
						
						if(!window.opener == null) {
							controlPlayer("stop","");
							clearInterval();
							return;
						}
						
						if(window.opener.current_pl == null) {
							return;
						}
						
						// Zu ladende Playlist ermitteln
						var pl = window.opener.current_pl;
					
						if(window.opener.player_active_time >= window.opener.player_start_time && pl != playing) {
							controlPlayer("load","media/player/playlists/"+pl+".xml");
							playing = pl;
						}
					
					}
								
					<!-- this is the small javascript that sends the controls to flash -->
	

					function controlPlayer(func,param) {
						thisMovie().jsControl(func,param);
					}
					
					function thisMovie() {
						
					    if (navigator.appName.indexOf("Microsoft") != -1) {
					    	return parent.document.getElementById("mp3player");
					    }
					    else {
					    	return parent.document.getElementById("mp3pl");
					    }
					}
				</script>	
					
				';
	
// Ausgabe
rawoutput($str_out);
	
// Seitenende
popup_footer();

?>