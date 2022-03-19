<?php
/*
NEW SOURCE.PHP by ALUCARD :>
*/

require_once "common.php";


$illegal_files = array(
	'dbconnect.php',
	'topwebvote.php', // hide completely
	'lodge.php',
	'housefeats.php',
    'alchemie.php',
	'alchemiechosenfeats.php',
	'alchemiedinner.php',
	'alchemiefurniture.php',
	'alchemiethepath.php',
	'alchemievillageevents.php',
	'alchemiehouses.php',
	'alchemieexpedition.php',
	'translator_de.php',
	'source.php',
	'anticheat.php',
	'common.php',
	'vendor.php',
	'chat.php',
	'test.php',
	'dg_main.php',
	'dg_output.php',
	'dg_battle.php',
	'dg_su.php',
	'dg_builds.php',
	'tragoraz.php',
	'schatzsuche.php',
	'trophy.php',
	'watchsu.php',	
	'dragon.php',
	'vampire.php',
	'gladiator.php',
	'alter.php',
	'kudzu.php',
	'darkhorse.php',
	'necromancer.php',
	'sacrificealtar.php',
	'stonehenge.php',
	'castle.php',
	'randdragon.php',
	'forestlake.php',
	'remains.php',
	'wannabe.php',
	'graeultat.php',
	'runemaster.php',
	'cairn.php',
	'fish.php',
	'fish_delete.php'
);

$url=$_GET['url'];
$dir = str_replace("\\","/",dirname($url)."/");
$subdir = str_replace("\\","/",dirname($_SERVER['SCRIPT_NAME'])."/");

while(substr($subdir,0,2)=="//" ){
     $subdir = substr($subdir,1);
}

$legal_dirs = array(
	array('dir'=>$subdir,'td'=>1),
	array('dir'=>$subdir.'special/','td'=>1),
	//array('dir'=>$subdir.'lib/','td'=>0),
	//array('dir'=>$subdir.'item_modules/','td'=>0),
	//array('dir'=>$subdir.'module/','td'=>1),	
);


//popup_header('Quellcode der Dragonslayer-Edition');
$str_out = '<html><head><title>Quellcode der Dragonslayer-Edition</title><link href="newstyle.css" rel="stylesheet" type="text/css"><style type="text/css">
					@import url(templates/colors.css);
				</style></head><body bgcolor="#000000" text="#CCCCCC"><table cellpadding=5 cellspacing=0 width="100%"><tr><td class="popupheader"><b>Quellcode der Dragonslayer-Edition</b></td></tr><tr><td valign="top" width="100%">';

$str_out .= '`c`b`&Quellcode der Dragonslayer-Edition : '.$logd_version.'`0`b`c`n`n';
$str_out .= 'Anmerkung: Dies ist nur ein Auszug aus dem Source. Um das jeweils aktuelle, vollständige Release zu erhalten,
                        ist eine Anfrage mit gültiger Email-Adresse erforderlich. Ebenso sollte darin die Serveradresse bzw. 
                        sonstiger Verwendungszweck aufgeführt sein. Was wir uns unbedingt verbitten, ist Diebstahl unserer Arbeit
                        ohne Nennung des Copyrights.`n
                        Falls beim Lesen des Source ein Bug entdeckt werden sollte, bitten wir um sofortige Meldung per Anfrage!`n`n';

if($session['message'] != '') {
	output('`n`b'.$session['message'].'`b`n`n');
	$session['message'] = '';
}

function in_dir( $dir ){
	global $legal_dirs;
	foreach($legal_dirs as $d){
		if( $d['dir'] == $dir ){
			return 1;
		}
	}
	return 0;
}

switch($_GET['op']) {
			

	

	case 'show':
		$file = urldecode($_GET['file']);
		$check = preg_replace('/(.*?)\//','',$file);
		$dir = str_replace( $check, '', $file );
		$file = '.'.$file;
		
		
		$str_out .= '`n`c`&`b'.$file.'`b`c`n';
		$str_out .= '<a href="source.php">zurück</a>`n';
		if( in_array( $check, $illegal_files ) || !in_dir( $dir )){
			$str_out .= '`4`b<big><big>Datei kann nicht angezeigt werden!</big></big>`b`n';
		}
		else{
			$buffer = highlight_file( $file, true );
			$rows = count(explode('<br />',$buffer));
			$znr = '';
			for($i=1; $i <= $rows; $i++) {
				$znr .= "$i:<br />";
			}
			$buffer = '<code><nobr>'.$buffer.'</nobr></code>';
			$str_out .= '<table style="width: 100%;padding:0px;margin:0px;" cellspacing="0">
							<tr>
								<td style="text-align: right; width: 25px; background: #AFAFAF; border-right: 1px solid #000000;">
									<code><nobr>'.$znr.'</nobr></code>
								</td>
								<td style="background: #EFEFEF;" valign="top">';
			output($str_out);
			$output .= $buffer;
			$str_out =			'</td>
							</tr>
						</table>';
		}
	break;
	
	
	
	// Standardansicht, Auswahl
	default:
		$session['disablevital'] = false;
		$files = array();
		foreach( $legal_dirs as $curr_dir ){
			$d 		  = dir('./'.$curr_dir['dir']);
			$files[$curr_dir['dir']] = array();
			while (false !== ($entry = $d->read())) { 
				$end = substr($entry,strrpos($entry,"."));
				if( $end != '.php' && $end != '.lib.php' ){
					continue;
				}
				array_push($files[$curr_dir['dir']],$entry);
				
			}
			sort($files[$curr_dir['dir']]);
		}	
		
		
		$str_out .= '`c<table cellspacing="2" cellpadding="2"><tr>';
		$lasttd = 1;
		foreach( $legal_dirs as $curr_dir ){
			if( $lasttd ){
				$str_out .= '<td valign="top"><table cellspacing="2" cellpadding="2">';
			}
			$str_out .= '<tr class="trhead"><td colspan="4">`c.'.$curr_dir['dir'].'`c</td></tr>';
			foreach( $files[$curr_dir['dir']] as $file ){
				if( in_array( $file, $illegal_files ) ){
					continue;
				}
				$style = ($style == 'trlight' ? 'trdark' : 'trlight');
				
				$showlink = 'source.php?op=show&file='.urlencode($curr_dir['dir'].$file);
				
				
				$str_out .= '<tr class="'.$style.'">
								<td>'.$file.'</td>
								<td><nobr>[ <a href="'.$showlink.'">show</a> ]</nobr></td>
							</tr>';
			}	
			//$str_out .= '';
			if( $curr_dir['td'] ){
				$str_out .= '</table></td>';
			}
			$lasttd = $curr_dir['td']; 
		}
		$str_out .= '</tr></table>`c';
				
		break;
	
}
$str_out .= '</td></tr><tr><td bgcolor="#000000" align="center">0.9.7+jt ext(GER) Dragonslayer Edition V/2</td></tr>

	           </table></body></html>';
output($str_out, true );
echo $output;
//popup_footer();
?>
