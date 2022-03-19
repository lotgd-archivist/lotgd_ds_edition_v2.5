<?

/*********************************************
Lots of Code from: lonnyl69 - Thanks Lonny !
Also Thanks to Excalibur @ dragonprime for your help.
By: Kevin Hatfield - Arune v1.0
06-19-04 - Public Release
Written for Fishing Add-On - Poseidon Pool
Translation and simple modifications by deZent deZent@onetimepad.de
********************************************/

// Bugfix&Modification by Maris (Maraxxus@gmx.de)

require_once "common.php";

define('MAX_ITEMS',100);

checkday();

page_header("Kerras Angelladen");

output("`@`c`bKerras Angelladen`b`c`n");

$sql = "SELECT worms,minnows,boatcoupons FROM account_extra_info WHERE acctid=".$session['user']['acctid']."";
$result = db_query($sql) or die(db_error(LINK));
$rowf = db_fetch_assoc($result);

if($session['user']['dragonkills'] == 0) {
	
	output('`5Kerra mustert Dich und meint dann: `6"Du solltest erst mehr Erfahrung sammeln, ehe Du dich an die Herausforderung des Angelns machst!"`n');
	addnav('Angeln!','fish.php');
	addnav('Zurück zum See','pool.php');
	
}
else {

	$inventory=$rowf['worms'];
	$inventory+=$rowf['minnows'];
	$inventory+=$rowf['boatcoupons'];
	
	$space= max(MAX_ITEMS - $inventory,0);
	
	$cost = array();
	$max = array();
	
	$cost['worms'] = 5;
	$cost['minnows'] = 6;
	$cost['boatcoupons'] = 10;
	$max['worms'] = min( floor($session['user']['gold'] / $cost['worms']) , $space );
	$max['minnows'] = min( floor($session['user']['gold'] / $cost['minnows']) , $space );
	$max['boatcoupons'] = min( floor($session['user']['gold'] / $cost['boatcoupons']) , $space );
	
	$op = ($_GET['op']) ? $_GET['op'] : '';
	
	switch($op) {
		
		case '':
			
			output('`5Hinter der Theke des anheimelnden, kleinen Angelladens steht der Besitzer: Kerra. Er begrüßt Dich freundlich und fragt dann nach Deinen Wünschen.`n`n');
		
			output('`5In deinem Beutel siehst Du:`n');
			output('`!'.$rowf['minnows'].' Fliegen`5, `1'.$rowf['worms'].' Angelwürmer und `!'.$rowf['boatcoupons'].' Coupons für ein Ruderboot.`n');
			if ($inventory > MAX_ITEMS) {
				output('`4Du bemerkst, dass dein Beutel schon voll ist.`n`n');
			}
			else {
				output('`5Du hast noch für '.$space.' Dinge Platz im Beutel.`n`n');
						
				output('`5Was möchtest Du kaufen?`n');
							
				output('`nFliegen zum Preis von `@'.$cost['minnows'].' Gold `5das Stück.');
				output('<form method="POST" action="bait.php?op=trade&what=minnows">',true);
				output('<input type="text" name="count" value="'.$max['minnows'].'"><input type="hidden" name="cost" value="'.$cost['minnows'].'"> <input type="submit" value="Fliegen kaufen"></form>',true);
				addnav('','bait.php?op=trade&what=minnows');
				
				if($session['user']['dragonkills'] >= 2) {
				
					output('`nWürmer zum Preis von `@'.$cost['worms'].' Gold `5das Stück.');
					output('<form method="POST" action="bait.php?op=trade&what=worms">',true);
					output('<input type="text" name="count" value="'.$max['worms'].'"><input type="hidden" name="cost" value="'.$cost['worms'].'"> <input type="submit" value="Würmer kaufen"></form>',true);
					addnav('','bait.php?op=trade&what=worms');
					
				}
				else
				{
                  output("Wenn du erfahrender wärst, könntest du mit Würmern angeln...");
                }
				if($session['user']['dragonkills'] >= 10)
                {
                    output('`nBootscoupons zum Preis von `@'.$cost['boatcoupons'].' Gold `5das Stück.');
					output('<form method="POST" action="bait.php?op=trade&what=boatcoupons">',true);
					output('<input type="text" name="count" value="'.$max['boatcoupons'].'"><input type="hidden" name="cost" value="'.$cost['boatcoupons'].'"> <input type="submit" value="Bootscoupons kaufen"></form>',true);
					addnav('','bait.php?op=trade&what=boatcoupons');
					
				}
				else
				{
                  output("Wenn du noch erfahrender wärst, könntest du mit einem Boot auf den See hinaus rudern...");
                }
			}
			
			addnav('Zurück zum See','pool.php');
			
			break;
					
		case 'trade':
		
		$sql = "SELECT worms,minnows,boatcoupons FROM account_extra_info WHERE acctid=".$session['user']['acctid']."";
$result = db_query($sql) or die(db_error(LINK));
$rowf = db_fetch_assoc($result);
			
			$what = $_GET['what'];
			$count = min($max[$what],$_POST['count']);
			$cost = $_POST['cost'] * $count;		
			$totalcount=$rowf[$what];
			$totalcount+=$count;
			
			if ($what=='minnows')  { $bname='Fliegen'; }
			elseif ($what=='worms')  { $bname='Würmer'; }
            elseif ($what=='boatcoupons')  { $bname='Bootscoupons'; }
            
  $sql = "UPDATE account_extra_info SET $what=$totalcount WHERE acctid=".$session['user']['acctid']."";
  db_query($sql);

			$session['user']['gold'] -= $cost;			
							
			output('`5Du kaufst `&'.$count.' '.$bname.'`5 für `^'.$cost.'`5 Gold!`nKerra schiebt Dir einen kleinen Beutel herüber, nimmt das Gold entgegegen und schaut Dich abwartend an.');
			
			addnav('Noch mehr kaufen','bait.php');
			addnav('Auf zum angeln!','fish.php');
			addnav('Zurück zum See','pool.php');
			
			break;
		
		
	}
}

page_footer();
?> 
