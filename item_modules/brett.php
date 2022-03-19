<?php

function brett_hook_process ( $item_hook , &$item ) {
	
	global $session,$item_hook_info;
	
	switch ( $item_hook ) {
		
		case 'furniture':
			
			// Wie viele Nachrichten gleichzeitig?
			$int_max_posts = 3;
			
			$arr_house = db_fetch_assoc(db_query('SELECT h.*,a.name AS ownername,a.sex FROM houses h LEFT JOIN accounts a ON a.acctid=h.owner WHERE h.houseid='.$item_hook_info['hid']));
			$bool_owner = ($arr_house['owner'] == $session['user']['acctid'] ? true : false);
							
			output('`&Du betrachtest das dreckige Stück Holz genauer, welches an der Wand angebracht ist. Auf den zweiten Blick fällt dir erst auf, dass hier anscheinend des öfteren'.(!empty($arr_house['ownername']) ? ' von '.$arr_house['ownername'].'`&' : '').' beschriftete Pergamentstückchen provisorisch in das verwitterte Holz gepinnt werden.`n`n');
			
			require_once(LIB_PATH.'board.lib.php');

			board_view('house'.$item_hook_info['hid'],($bool_owner ? 2:0),'An der Wand sind folgende Nachrichten zu lesen:','Es scheinen keine Nachrichten vorhanden zu sein.');		
			
			output('`n`n');
			
			if($bool_owner) {
				
				board_view_form("Aufhängen","`&Hier kannst Du als Hauseigentümer".($session['user']['sex'] ? 'in' : '')." eine Nachricht hinterlassen:");
				if($_GET['board_action'] == "add") {
					if(board_add('house'.$item_hook_info['hid'],100,$int_max_posts) != -1) {
						redirect($item_hook_info['link']);	
					}
					output('`n`&Das Platzangebot reicht leider für gerade einmal '.$int_max_posts.' Nachrichten gleichzeitig - entferne zuerst eine der bereits vorhandenen Nachrichten.');
				}		
			}
					
			addnav($item_hook_info['back_msg'],$item_hook_info['back_link']);
			
		break;
			
	}
		
	
}

?>