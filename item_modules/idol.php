<?php

function idol_hook_process ( $item_hook , &$item ) {
	
	global $session,$item_hook_info;
	
	switch ( $item_hook ) {
		
		case 'pvp_victory':
			
			$badguy = $item_hook_info['badguy'];
			
			output("`n`^Du nimmst $badguy[creaturename] `^das $item[name]`^ ab!`0`n");
			
			addnews("`^".$session['user']['name']."`^ nimmt {$badguy['creaturename']}`^ das {$item['name']} `^ab!");
			
			item_set(' id='.$item['id'], array('owner'=>$session['user']['acctid']) );
			
			break;
			
	}
		
	
}

?>
