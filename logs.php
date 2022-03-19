<?php

require_once('common.php');

page_header('Multi');

su_check(SU_RIGHT_MULTI,true);

if ($_GET['op']=='multi')
{
	if (!empty($_POST['setupban']) && count($_POST['userid'])>0)
	{

		$str_lnk = 'su_bans.php?op=edit_ban&ids[]='.implode('&ids[]=',$_POST['userid']).'&ret='.urlencode('logs.php?op=multi');

		redirect($str_lnk);

	}
	elseif (!empty($_POST['deleteuser']) && count($_POST['userid'])>0) {

		$str_lnk = 'su_delete.php?ids[]='.implode('&ids[]=',$_POST['userid']).'&ret='.urlencode('logs.php?op=multi');

		redirect($str_lnk);

	}

	else
	{
		output('`n');
	}

	require_once(LIB_PATH.'board.lib.php');

	if($_GET['board_action'] == 'add') {
		board_add('multi_ex');
	}

	board_view('multi_ex',2,'`&Aktuelle Ausnahmen von der Multiregelung:','',false,true);
	output('`n`n`&Ausnahme aufnehmen (Alle davon betroffenen Accounts + Bis-Datum!):`n');
	board_view_form('Speichern!','');
	output('`n');

	$in_ip = $in_id = '';
	if ($_GET['searchby']!='id') {

		$sql = 'SELECT lastip FROM accounts WHERE lastip!="" GROUP BY lastip HAVING COUNT(*) > 1';
		$result = db_query($sql) or die(db_error(LINK));
		while ($row = db_fetch_assoc($result)) {
			$in_ip .= ',"'.$row['lastip'].'"';
		}

	}
	if ($_GET['searchby']!='ip') {

		$sql = 'SELECT uniqueid FROM accounts WHERE uniqueid!="" GROUP BY uniqueid HAVING COUNT(*) > 1';
		$result = db_query($sql) or die(db_error(LINK));
		while ($row = db_fetch_assoc($result)) {
			$in_id .= ',"'.$row['uniqueid'].'"';
		}
	}

	$minaccs = ($_POST['minaccs']) ? $_POST['minaccs'] : $_GET['minaccs'];
	$minaccs = ($minaccs) ? $minaccs : 3;

	$ip = $id = $users = array();
	$sql = 'SELECT a.acctid,name,lastip,uniqueid,dragonkills,level,laston,referer,guildid
			FROM accounts a
			LEFT JOIN account_extra_info aei USING(acctid)
			WHERE (lastip IN (-1'.$in_ip.') OR uniqueid IN (-1'.$in_id.')) ORDER BY dragonkills ASC, level ASC';
	$result = db_query($sql) or die(db_error(LINK));
	while ($row = db_fetch_assoc($result))
	{
		if ((!isset($id[$row['uniqueid']]) || $_GET['searchby']=='ip') && (!isset($ip[$row['lastip']]) || $_GET['searchby']=='id'))
		{
			if ($_GET['searchby']!='id') $ip[$row['lastip']] = count($users);
			if ($_GET['searchby']!='ip') $id[$row['uniqueid']] = count($users);
			$users[] = array($row);
		}
		elseif (isset($id[$row['uniqueid']]))
		{
			$ip[$row['lastip']] = $id[$row['uniqueid']];
			$users[$id[$row['uniqueid']]][] = $row;
		}
		else
		{
			$id[$row['uniqueid']] = $ip[$row['lastip']];
			$users[$ip[$row['lastip']]][] = $row;
		}
	}

	addnav('','logs.php?op=multi&searchby='.$_GET['searchby']);

	output('`n`bMultiaccounts`b`nNaaa, wer spielt denn hier noch wen?`n`n');
	output('<form method="POST" action="logs.php?op=multi&searchby='.$_GET['searchby'].'">Spieler mit ',true);
	output('<select onchange="this.form.submit()" name="minaccs" size="1">',true);
	output('<option value="2" '.(($minaccs==2)?'selected="selected"':'').'>2</option>',true);
	output('<option value="3" '.(($minaccs==3)?'selected="selected"':'').'>3</option>',true);
	output('<option value="4" '.(($minaccs==4)?'selected="selected"':'').'>4</option>',true);
	output('</select></form>',true);
	output(' oder mehr Multiaccounts suchen nach: ');

	// Max. Multi-Gruppen / Seite
	$int_max_pp = 200000;
	// Aktuelle Gruppenzahl
	$int_grp_counter = 0;

	if ($_GET['searchby']!='ip') {
		output('<a href="logs.php?op=multi&searchby=ip&minaccs='.$minaccs.'">IP</a> ',true);
		addnav('','logs.php?op=multi&searchby=ip&minaccs='.$minaccs);
	}
	else
	{
		output('`&`bIP`b`0 ');
	}
	if ($_GET['searchby']!='id') {
		output('<a href="logs.php?op=multi&searchby=id&minaccs='.$minaccs.'">ID</a> ',true);
		addnav('','logs.php?op=multi&searchby=id&minaccs='.$minaccs);
	}
	else
	{
		output('`&`bID`b`0 ');
	}
	if (!empty($_GET['searchby'])) {
		output('<a href="logs.php?op=multi&searchby=&minaccs='.$minaccs.'">Beidem</a> ',true);
		addnav('','logs.php?op=multi&searchby=&minaccs='.$minaccs);
	}
	else
	{
		output('`&`bBeidem`b`0 ');
	}

	$counter = 0;

	output('<table><tr><td>',true);
	foreach ($users AS $list)
	{

		if (count($list)<$minaccs)
		{
			continue;
		}

		$int_grp_counter++;

		if($int_grp_counter >= $int_max_pp)
		{

			break;
		}

		$tmpstr = $linkstr =  '';
		$ips = $ids = $accts = array();
		foreach ($list AS $item)
		{
			$tmpstr .= ('<tr><td><input type="checkbox" name="userid[]" value="'.$item['acctid'].'"><input type="hidden" name="multi_id[]" value="'.$item['acctid'].'"></td>
							<td>'.$item['acctid'].'</td>
							<td>'.$item['name'].'</td>
							<td>'.$item['lastip'].'</td>
							<td>'.$item['uniqueid'].'</td>
							<td>'.$item['dragonkills'].'</td>
							<td>'.$item['level'].'</td>
							<td>'.$item['laston'].'</td>
							<td>'.$item['referer'].'</td>
							<td>'.$item['guildid'].'</td>
							</tr>');
			$linkstr .= '&multi_id[]='.$item['acctid'];
			$counter++;
		}
		output('<form action="logs.php?op=multi&searchby='.$_GET['searchby'].'" method="post">',true);
		addnav('','logs.php?op=multi&searchby='.$_GET['searchby']);
		output("<table align='center' class='input' width='100%'><tr><td>&nbsp;</td>
						<td>`bAcctID`b</td>
						<td>`bName`b</td>
						<td>`bIP`b</td>
						<td>`bID`b</td>
						<td>`bDK`b</td>
						<td>`bLevel`b</td>
						<td>`bZuletzt da`b</td>
						<td>`bGew. von`b</td>
						<td>`bG-ID`b</td>
						</tr>",true);
		output($tmpstr,true);

		$linkstr = 'multi.php?'.$linkstr;

		output('<tr><td colspan="6" align="left">
						<input type="submit" name="deleteuser" value="löschen">
						<input type="submit" name="setupban" value="Accounts bannen">
						<a href="#" onclick="'.popup($linkstr).';return false;">Analyse</a>
					</td></tr>',true);
		output('</table>`n`n',true);
		output('</form>',true);
		addnav('',$linkstr);
	}
	output('</td></tr></table>',true);
	output('`b'.$counter.'`b Multis`n');
	addnav('Aktualisieren','logs.php?op=multi&searchby='.$_GET['searchby'].'&minaccs='.$minaccs);
	addnav('Zurück','logs.php');
}
else
{
	redirect('logs.php?op=multi');
}
addnav('Zurück zur Grotte','superuser.php');
addnav('Zurück zum Weltlichen','village.php');
page_footer();
?>