<?php

// Eichh�rnchen-Farm
// 1. Vorabversion
// by Maris (Maraxxus@gmx.de)

function squirrelfarm_hook_process ( $item_hook , &$item ) {

	global $session,$item_hook_info;

	switch ( $item_hook ) {

		case 'furniture':

			if($item_hook_info['op'] == 'take')
			{
				$items = item_get(' id='.$_GET['obj_id'].' AND owner="1300000"',false);
				output("`&Du greifst in das Gehege und ziehst ".$items['name']."`& am Nackenfell heraus.`nDein fachm�nnischer Blick auf die H�rnchen-Unterseite verr�t dir, dass es sich um ein `^".$items['special_info']."`& handelt.`n`nWas hast du nun damit vor?");
				addnav("Aktionen");
				addnav("Mitnehmen",$item_hook_info['link'].'&op=take2&obj_id='.$items['id']);
				if ($items['value1']<$items['value2'])
				{
					addnav("~~~");
					addnav("F�ttern",$item_hook_info['link'].'&op=feed&obj_id='.$items['id']);
				}
				if ($items['hvalue']<$items['hvalue2'])
				{
					addnav("~~~");
					addnav("Pflegen",$item_hook_info['link'].'&op=care&obj_id='.$items['id']);
				}
				addnav("~~~");
				addnav("Ins Taufbecken tunken",$item_hook_info['link'].'&op=name&obj_id='.$items['id']);
				addnav("~~~");
				addnav("Den Hals umdrehen",$item_hook_info['link'].'&op=kill&obj_id='.$items['id']);
				addnav("~~~");
				if (($session['user']['race']=='vmp' || $session['user']['race']=='wwf') &! (strpos($items['name'],"Vampir`th�rnchen`0") || strpos($items['name'],"Wer`th�rnchen`0")))
				{
					addnav("Bei�en",$item_hook_info['link'].'&op=bite&obj_id='.$items['id']);
					addnav("~~~");
				}
				addnav("Zur�ck setzen",$item_hook_info['link']);
			}
			elseif($item_hook_info['op'] == 'name')
			{
				$items = item_get(' id='.$_GET['obj_id'].' AND owner="1300000"',false);
				$name = $items['name'];
				if ($name[strlen($name)-1] == ")")
				{
					output("`4Dieser Nager hat schon einen Namen und kann nicht erneut benannt werden!`0");
				}
				else
				{
					output("`&Wie soll das Tierchen heissen?`n");
					output("<form action=".$item_hook_info['link']."&op=name2&obj_id=".$items['id']." method='POST'><input name='newname' value=\"".HTMLEntities($newname)."\" size=\"30\" maxlength=\"30\"> <input type='submit' value='Mal probieren'></form>");
					addnav("",$item_hook_info['link'].'&op=name2&obj_id='.$items['id']);
				}
				addnav("Zur�ck",$item_hook_info['link']);
			}
			elseif($item_hook_info['op'] == 'name2')
			{
				$items = item_get(' id='.$_GET['obj_id'].' AND owner="1300000"',false);
				$_POST['newname']=str_replace("`0","",$_POST['newname']);
				$_POST['newname'] = preg_replace("/[`][c]/","",$_POST['newname']);

				if (strlen($_POST['newname'])>30)
				{
					$msg.="Der Name ist zu lang, inklusive Farbcodes darf er nicht l�nger als 30 Zeichen sein.`n";
				}

				$colorcount = substr_count($_POST['newname'],'`');

				if ($colorcount>getsetting("maxcolors",8))
				{
					$msg.="Zu viele Farben. Du kannst maximal ".getsetting("maxcolors",8)." Farbcodes benutzen.`n";
				}
				if ($msg=="")
				{
					output("`&Dein ".$items['name']."`&  wird so hei�en: {$_POST['newname']}`n`n`0Ist es das was du willst?`n`n");
					output("<form action=".$item_hook_info['link']."&op=changename&obj_id=".$items['id']." method='POST'><input type='hidden' name='newname' value=".URLENCODE($_POST['newname'])."><input type='submit' value='Ja' class='button'></form>");
					output("`n`n<a href=".$item_hook_info['link']."&op=name&obj_id=".$items['id'].">Nein, ich will nochmal!</a>",true);
					addnav("",$item_hook_info['link'].'&op=name&obj_id='.$items['id']);
					addnav("",$item_hook_info['link'].'&op=changename&obj_id='.$items['id']);
				}
				else
				{
					output("`bFalscher Name`b`n$msg");
					output("`n`n`&Wie soll dein ".$items['name']."`& hei�en?`n");
					output("<form action=".$item_hook_info['link']."&op=name2&obj_id=".$items['id']." method='POST'><input name='newname' value=\"".HTMLEntities($regname)."\"size=\"30\" maxlength=\"30\"> <input type='submit' value='Vorschau'></form>");
					addnav("",$item_hook_info['link'].'&op=name2&obj_id='.$items['id']);
				}
				addnav("Zur�ck",$item_hook_info['link']);
			}
			elseif($item_hook_info['op'] == 'changename')
			{
				$items = item_get(' id='.$_GET['obj_id'].' AND owner="1300000"',false);
				$newname = URLDECODE($_POST['newname']);
				output("`&Du tunkst dein ".$items['name']."`& beherzt ins Taufbecken und gibts ihm den sch�nen Namen ".$newname."`&.`n");
				
				insertcommentary($session['user']['acctid'],'/me `6tauft ein '.$items['name'].'`6 auf den wundersch�nen Namen `^'.$newname.'`6.','sqf'.$item['id']);
				
				$item_change['name'] = $newname." `&(".$items['name']."`&)";
				item_set('id='.$items['id'],$item_change);
				addnav("Zur�ck",$item_hook_info['link']);
			}
			elseif($item_hook_info['op'] == 'take2')
			{
				$items = item_get(' id='.$_GET['obj_id'].' AND owner="1300000"',false);
				output("`&Du packst ".$items['name']."`& leise pfeifend ein. Denn bei dir ists immer noch am sch�nsten!`n");
				$item_change['owner'] = $session['user']['acctid'];
				$item_change['deposit1'] = 0;
				item_set('id='.$items['id'],$item_change);
				
				insertcommentary($session['user']['acctid'],'/me `@entnimmt '.$items['name'].'`@ aus dem K�fig.','sqf'.$item['id']);
				
				addnav("Auf gehts!",$item_hook_info['link']);
			}
			elseif($item_hook_info['op'] == 'putin')
			{
				output("`n<table border='0'><tr><td>`2`bFolgende Eichh�rnchen nagen dir gerade L�cher in deine Taschen:`b</td></tr><tr><td valign='top'>",true);
				$sql = 'SELECT i.id,i.name,special_info FROM items i LEFT JOIN items_tpl it ON i.tpl_id=it.tpl_id LEFT JOIN items_classes ic ON it.tpl_class=ic.id WHERE owner='.$session['user']['acctid'].' AND ic.class_name="Kleintiere" ORDER BY i.id ASC';
				$result = db_query($sql);
				$amount=db_num_rows($result);

				if (!$amount) { output("`iKein einziges!"); }

				for ($i=1;$i<=$amount;$i++){

					$items = db_fetch_assoc($result);

					output("<a href=".$item_hook_info['link']."&op=putin2&obj_id=".$items['id'].">`&-$items[name]</a>&nbsp&nbsp&nbsp&nbsp&nbsp(`i$items[special_info]`i)`0`n",true);
					addnav("",$item_hook_info['link'].'&op=putin2&obj_id='.$items['id']);
				}
				output("</td></tr></table>",true);

				addnav('Zur Farm',$item_hook_info['link']);
			}

			elseif($item_hook_info['op'] == 'putin2')
			{
				$items = item_get(' id='.$_GET['obj_id'].' AND owner='.$session['user']['acctid'],false);
				output("`&".$items['name']."`& wirklich in die Zuchtfarm setzen?");
				addnav("Jawohl",$item_hook_info['link'].'&op=putin3&obj_id='.$items['id']);
				addnav("Nein",$item_hook_info['link']);
			}
			elseif($item_hook_info['op'] == 'putin3')
			{
				output("`&Du setzt den kleinen Nager in sein neues Heim, wo er alsbald im dichten Unterholz verschwindet und dich keines Blickes mehr w�rdigt.`n");
				$items = item_get(' id='.$_GET['obj_id'].' AND owner='.$session['user']['acctid'],false);
				$item_change['owner'] = 1300000;
				$item_change['deposit1'] = $item['id'];
				item_set('id='.$items['id'],$item_change);
				
				insertcommentary($session['user']['acctid'],'/me `@setzt '.$items['name'].'`@ in die Zuchtfarm.','sqf'.$item['id']);
				
				addnav("Machs gut!",$item_hook_info['link']);
			}
			elseif($item_hook_info['op'] == 'kill')
			{
				$items = item_get(' id='.$_GET['obj_id'].' AND owner="1300000"',false);
				output("`&Willst du wirklich das Leben von ".$items['name']."`& so brutal beenden?");
				addnav("Knack!",$item_hook_info['link'].'&op=kill2&obj_id='.$items['id']);
				addnav("Nicht doch...",$item_hook_info['link']);
			}
			elseif($item_hook_info['op'] == 'kill2')
			{
				$items = item_get(' id='.$_GET['obj_id'].' AND owner="1300000"',false);
				output($items['name']."`& weilt nun nicht mehr unter uns.`nDu stopfst die �berreste aus und nimmst sie mit.");
				item_delete( ' id='.$items['id']);
				$res = item_tpl_list_get( 'tpl_name="`tAusgestopftes Eichh�rnchen`0" LIMIT 1' );
				if( db_num_rows($res) )
				{
					$itemnew = db_fetch_assoc($res);
					item_add( $session['user']['acctid'], 0, $itemnew);
				}
				
				insertcommentary($session['user']['acctid'],'/me `4dreht '.$items['name'].'`4 in gemeiner Weise den Hals um!','sqf'.$item['id']);
				
				addnav("Zur�ck",$item_hook_info['link']);
			}
			elseif($item_hook_info['op'] == 'care')
			{
				$items = item_get(' id='.$_GET['obj_id'].' AND owner="1300000"',false);
				output("`&Durch eine aufmerksame und liebevolle Krallen- und Gebisspflege wird der kleine Nager k�nftig noch herzhafter zubeissen und sich noch st�rker festkrallen k�nnen.`nAllerdings wird dich die diamantbeschichtete Feile `^1 Edelstein`& kosten.`nBist du sicher, dass du ".$items['name']."`& pflegen willst?");
				addnav("Klar doch!",$item_hook_info['link'].'&op=care2&obj_id='.$items['id']);
				addnav("Dann nicht...",$item_hook_info['link']);
			}
			elseif($item_hook_info['op'] == 'care2')
			{
				$items = item_get(' id='.$_GET['obj_id'].' AND owner="1300000"',false);
				if ($items['hvalue']>$items['hvalue2'])
				{
					output($items['name']."`& ist bereits so gut wie es geht gepflegt.`nMehr ist hier nicht m�glich!`n");
				}
				elseif ($session['user']['gems']<1)
				{
					output("`&Neben etwas Verstand fehlt dir auch der ben�tigte `^Edelstein`& um ".$items['name']."`& zu pflegen!");
				}
				else
				{
					$item_change['hvalue'] = $items['hvalue']+1;
					item_set('id='.$items['id'],$item_change);
					$session['user']['gems']--;
					output($items['name']."`& ertr�gt die Prozedur nur widerwillig, ist aber mit dem Resultat sichtlich zufrieden.`nZ�hne und Krallen sind nun viel sch�rfer!");
				}
				addnav("Zur�ck",$item_hook_info['link']);
			}
			elseif($item_hook_info['op'] == 'bite')
			{
				$items = item_get(' id='.$_GET['obj_id'].' AND owner="1300000"',false);
				output("`&Bist du sicher, dass du ".$items['name']."`& bei�en willst?");
				addnav("Hunger!",$item_hook_info['link'].'&op=bite2&obj_id='.$items['id']);
				addnav("Nee",$item_hook_info['link']);
			}
			elseif($item_hook_info['op'] == 'bite2')
			{
				$items = item_get(' id='.$_GET['obj_id'].' AND owner="1300000"',false);
				if (strpos($items['name'],"Baby`th�rnchen`0"))
				{
					output("`&Das war zwar sehr lecker, doch leider war ".$items['name']." `&noch viel zu klein f�r eine Verwandlung und ist gestorben.`nF�r den kleinen Snack bekommst du ein paar Lebenspunkte.`n");
					$session['user']['hitpoints']*=1.1;
					
					insertcommentary($session['user']['acctid'],'/me `4vergreift sich an '.$items['name'].'`4 als kleinem Imbiss!','sqf'.$item['id']);
					
				}
				else
				{
					output($items['name']."`& beginnt nach deinem Biss die unheilvolle Verwandlung.");
										
					insertcommentary($session['user']['acctid'],'/me `8bei�t '.$items['name'].'`8!','sqf'.$item['id']);
					
					if (strpos($items['name'],"Frustriertes"))
					{
						if ($session['user']['race']=='vmp')
						{
							$res = item_tpl_list_get( 'tpl_name="`&Frustriertes `8Vampir`th�rnchen`0" LIMIT 1' );
						}
						elseif ($session['user']['race']=='wwf')
						{
							$res = item_tpl_list_get( 'tpl_name="`&Frustriertes `TWer`th�rnchen`0" LIMIT 1' );
						}
					}
					else
					{
						if ($session['user']['race']=='vmp')
						{
							$res = item_tpl_list_get( 'tpl_name="`8Vampir`th�rnchen`0" LIMIT 1' );
						}
						elseif ($session['user']['race']=='wwf')
						{
							$res = item_tpl_list_get( 'tpl_name="`TWer`th�rnchen`0" LIMIT 1' );
						}
					}
					if( db_num_rows($res) )
					{
						$itemnew = db_fetch_assoc($res);
						$itemnew['deposit1']=$item['id'];
						$itemnew['tpl_value1']=$items['value1'];
						$itemnew['tpl_hvalue']=$items['hvalue'];
						$itemnew['tpl_special_info']=$items['special_info'];
						if (strpos($items['name'],"("))
						{
							$oldname=substr($items['name'],0,strpos($items['name'],"("));
							trim($oldname);
							if ($session['user']['race']=='vmp')
							{
								$itemnew['tpl_name']=$oldname." `&(`8Vampir`th�rnchen`0`&)";
							}
							else
							{
								$itemnew['tpl_name']=$oldname." `&(`TWer`th�rnchen`0`&)";
							}
						}
						item_add( 1300000, 0, $itemnew);
					}
				}

				item_delete( ' id='.$items['id']);
				addnav("Zur�ck",$item_hook_info['link']);
			}
			elseif($item_hook_info['op'] == 'feed')
			{
				$items = item_get(' id='.$_GET['obj_id'].' AND owner="1300000"',false);
				output("`&Womit willst du den kleinen Nager f�ttern?`n`n");
				output("`n<table border='0'><tr><td>`&`bDu hast in deinen Taschen:`b</td></tr><tr><td valign='top'>",true);
				$result = item_list_get( 'owner='.$session['user']['acctid'].' AND (i.name="Macadamia-N�sse" OR i.name="Acolytenfutter" OR i.name="Eine Hand voll Erdn�sse" OR i.name="Starkbier") ' , ' ORDER BY hvalue,id ASC ' );
				$amount=db_num_rows($result);
				if (!$amount) { output("`iLediglich ganz ungesunde Sachen..."); }
				for ($i=1;$i<=$amount;$i++){

					$items2 = db_fetch_assoc($result);

					output("<a href=".$item_hook_info['link']."&op=feed2&obj_id=".$items['id']."&obj2_id=".$items2['id'].">`&-$items2[name]</a>`0`n",true);
					addnav("",$item_hook_info['link'].'&op=feed2&obj_id='.$items['id'].'&obj2_id='.$items2['id']);
				}
				output("</td></tr></table>",true);
				addnav("Zur�ck",$item_hook_info['link']);
			}
			elseif($item_hook_info['op'] == 'feed2')
			{
				$items = item_get(' id='.$_GET['obj_id'].' AND owner="1300000"',false);
				$items2 = item_get(' id='.$_GET['obj2_id'],false);

				if ($items2['name']=="Eine Hand voll Erdn�sse")
				{
					output("`&Nachdem du wei�t, dass diese Erdn�sse tote Eichh�rnchen zum Leben erwecken willst du gar nicht herausfinden, was sie mit den Viechern anstellen, wenn sie noch leben!`n");
					addnav("Nochmal",$item_hook_info['link'].'&op=feed&obj_id='.$items['id']);
				}
				elseif ($items2['name']=="Macadamia-N�sse")
				{
					if (strpos($items['name'],"Baby`th�rnchen`0"))
					{
						output("`&Gierig schlingt der kleine Racker die leckren Macadamia-N�sse in sich hinein.`n");
						$item_change['value1'] = $items['value1']+1;
						if ($item_change['value1']>=$items['value2'])
						{
							output("`&Der Kleine ist nun kr�ftig genug um es mit den Gefahren der Welt aufnehmen zu k�nnen!");
							$res = item_tpl_list_get( 'tpl_name="`tKiller-Eichh�rnchen`0" LIMIT 1' );
							if( db_num_rows($res) )
							{
								$itemnew = db_fetch_assoc($res);
								$itemnew['deposit1']=$item['id'];
								$itemnew['tpl_special_info']=$items['special_info'];
								if (strpos($items['name'],"("))
								{
									$oldname=substr($items['name'],0,strpos($items['name'],"("));
									trim($oldname);
									$itemnew['tpl_name']=$oldname." `&(`tKiller-Eichh�rnchen`0`&)";
								}
								item_add( 1300000, 0, $itemnew);
								item_delete( ' id='.$items['id']);
							}
	
							insertcommentary(1,'/msg `@'.$items['name'].'`@ ist ausgewachsen zu '.$itemnew['tpl_name'].'`@!','sqf'.$item['id']);
						}
						else
						{
							output("`&Schon bald wird er gro� und stark sein!`n");
							item_set('id='.$items['id'],$item_change);
						}
						item_delete( ' id='.$items2['id']);
					}
					else
					{
						output("`&Dieses Eichh�rnchen ist schon gro� und braucht keine Nahrung dieser Art mehr!`n");
						addnav("Nochmal",$item_hook_info['link'].'&op=feed&obj_id='.$items['id']);
					}
				}
				elseif ($items2['name']=="Starkbier")
				{
					if (strpos($items['name'],"Frustriertes %P`!a`@r`^t`4y`th�rnchen`0"))
					{
						output("`&Dieses Eichh�rnchen ist ziemlich fertig mit der Welt und braucht bestenfalls eine Entziehungskur!`n");
						addnav("Nochmal",$item_hook_info['link'].'&op=feed&obj_id='.$items['id']);
					}
					elseif (strpos($items['name'],"P`!a`@r`^t`4y`th�rnchen`0"))
					{
						$item_change['value1'] = $items['value1']+1;
						if ($item_change['value1']>$items['value2'])
						{
							output($items['name']."`& ist bereits bei bester Stimmung und braucht keinen Alkohol!`n");
						}
						else
						{
							output($items['name']."`& gie�t sich einen hinter die Binde und ist bereit f�r die Party!`n");
							item_set('id='.$items['id'],$item_change);
							item_delete( ' id='.$items2['id']);
						}

					}
					else
					{
						output("`&Wie bitte? Du solltest besser selbst die Finger von dem Zeug lassen!`n");
						addnav("Nochmal",$item_hook_info['link'].'&op=feed&obj_id='.$items['id']);
					}

				}
				elseif ($items2['name']=="Acolytenfutter")
				{
					if (strpos($items['name'],"Baby`th�rnchen`0"))
					{
						output("`&Dein Sch�tzling ist noch viel zu klein um diese Nahrung zu sich zu nehmen.`nVersuch es doch mal mit etwas Kr�ftigendem.");
						addnav("Nochmal",$item_hook_info['link'].'&op=feed&obj_id='.$items['id']);
					}
					elseif (strpos($items['name'],"P`!a`@r`^t`4y`th�rnchen`0"))
					{
						output("`&Dein `%P`!a`@r`^t`4y`th�rnchen`& verschm�ht diese Art von K�stlichkeiten!`n");
						addnav("Nochmal",$item_hook_info['link'].'&op=feed&obj_id='.$items['id']);
					}
					else
					{
						if (strpos($items['name'],"Frustriertes"))
						{
							output("`&Gierig schlingt der Nager das leckre Acolytenfutter in sich hinein.`n");
							$item_change['value1'] = $items['value1']+1;
							if ($item_change['value1']>=$items['value2'])
							{
								output("`&Das hat ihn soweit bes�nftigt, dass er nun nicht mehr frustriert ist!");

								$tplname= substr($items['tpl_id'],0,7);
								$res = item_tpl_list_get( "tpl_id='$tplname' LIMIT 1" );

								if( db_num_rows($res) )
								{
									$itemnew = db_fetch_assoc($res);
									$itemnew['deposit1']=$item['id'];
									$itemnew['tpl_hvalue']=$items['hvalue'];
									$itemnew['tpl_hvalue2']=$items['hvalue2'];
									$itemnew['tpl_value1']=1;
									$itemnew['tpl_special_info']=$items['special_info'];
									if (strpos($items['name'],"("))
									{
										$oldname=substr($items['name'],0,strpos($items['name'],"("));
										trim($oldname);
										$itemnew['tpl_name']=$oldname." `&(".$itemnew['tpl_name']."`&)";
									}
									item_add( 1300000, 0, $itemnew);
									item_delete( ' id='.$items['id']);
								}

								insertcommentary(1,'/msg `@'.$items['name'].'`@ ist nun nicht mehr frustriert!','sqf'.$item['id']);
								
							}
							else
							{
								output("`&Es sieht dich zwar etwas freundlicher an, aber sein Vertrauen hast du noch lange nicht zur�ck gewonnen!`n");
								item_set('id='.$items['id'],$item_change);
							}


							item_delete( ' id='.$items2['id']);
						}
						else
						{
							$item_change['value1'] = $items['value1']+1;
							if ($item_change['value1']>$items['value2'])
							{
								output($items['name']."`& scheint es gerade sehr gut zu gehen.`nDas Futter bleibt unangetastet!`n");
							}
							else
							{
								output($items['name']."`& frisst das Futter schnell auf und sch�pft neue Kraft.`n");
								item_set('id='.$items['id'],$item_change);
								item_delete( ' id='.$items2['id']);
							}
						}
					}
				}

				addnav("Zur�ck",$item_hook_info['link']);
			}
			else
			{
				output("`&Du stapfst langsamen Schrittes die Treppenstufen zum Keller hinab und betrachtest die eigenartige Konstruktion.`nDie Eichh�rnchenzuchtfarm, die du erblickst, bietet den kleinen, pelzigen Vierbeinern einen optimalen Ort um sich von den Strapazen des Alltags zu erholen.`n`n");
				output("`n<table border='0'><tr><td>`2`bDerzeit tummeln sich in der Farm:`b</td></tr><tr><td valign='top'>",true);

				$result = item_list_get( 'owner="1300000" AND i.deposit1='.$item['id'].' ' , ' ORDER BY hvalue,id ASC ' );


				$amount=db_num_rows($result);

				if (!$amount) { output("`iNur die Wollm�use, und davon nicht zu wenige!"); }

				for ($i=1;$i<=$amount;$i++){

					$items = db_fetch_assoc($result);

					output("<a href=".$item_hook_info['link']."&op=take&obj_id=".$items['id'].">`&-$items[name]</a>`0`n",true);
					addnav("",$item_hook_info['link'].'&op=take&obj_id='.$items['id']);
				}
				output("</td></tr></table>",true);

				if ($amount<6)
				{
					addnav('Eichh�rnchen');
					addnav('Dazutun',$item_hook_info['link'].'&op=putin');
				}
				else
				{
					addnav("�berf�llt");
				}
				$roomname="sqf".$item['id'];
				output("`n`n`n");
				addcommentary();
				viewcommentary($roomname,"Sagen:",20,"sagt");
				addnav('Sonstiges');
				addnav($item_hook_info['back_msg'],$item_hook_info['back_link']);
			}
			break;

	}


}

?>
