<?
/**
 * Enter description here...
 *
 * @author dragonslayer
 * @package defaultPackage
 * @longdesc rss_item_count, rss_title, rss_description, rss_link, rss_image
 */

/**
 * @desc Write the MOTD RSS File
 * @author Dragonslayer for lotgd.drachenserver.de
 * @return void
 */
function rss_write_motd_feed_file()
{
	$str_sql = 'SELECT motdtitle as title, motdbody as description, motddate as pubdate FROM motd ORDER BY motddate DESC LIMIT '.(int)(getsetting('rss_item_count',10));
	$db_result = db_query($str_sql);
	$arr_items = array();
	while($arr_item = mysql_fetch_assoc($db_result))
	{
		$arr_item['link'] = getsetting('rss_link','http://www.atrahor.de');
		$arr_items[] = $arr_item;
	}
	rss_write_feed_file($arr_items,getsetting('rss_file_abs_path',''));
}

/**
 * Writes a general RSS 0.91 File
 *
 * @param array $arr_data
 * @param string $str_file_name Filename which contains the path to the file to write
 * The array must have the indices
 * title, link, description, pubdate
 * @param string $str_category Contains an optional category
 * @return int Errorcode as defined in the function
 * @author dragonslayer
 */
function rss_write_feed_file($arr_data, $str_file_name, $str_category = 'MOTD')
{
	/**********************************
	 * Define some needed variables
	 **********************************/
	define('OK',1);
	define('GENERAL_ERROR',2);
	define('FILE_NOT_WRITEABLE',3);
	define('DATA_ARRAY_MALFORMED',4);
	define('COULD_NOT_WRITE_DATA',5);

	if(empty($str_file_name))
	{
		return FILE_NOT_WRITEABLE;
	}
	if(!is_array($arr_data))
	{
		return DATA_ARRAY_MALFORMED;
	}

	/**
	 * @desc The RSS File Header
	 */
	$str_xml_header = '<?xml version="1.0" encoding="iso-8859-1"?>
		<rss version="0.91">
			<channel>
				<title>'.getsetting('rss_title','LOTGD Webfeed').'</title>
				<description>'.getsetting('rss_description','').'</description>
				<link>'.getsetting('rss_link','http://atrahor.de').'</link>
				<lastBuildDate>'.date('D, j M Y G:i:s T').'</lastBuildDate>
				<generator>LOTGD Dragonslayer Edition</generator>
				<image>
					<url>'.getsetting('rss_image','LOTGD Webfeed').'</url>
					<title>'.getsetting('rss_title','LOTGD Webfeed').'</title>
					<link>'.getsetting('rss_link','http://atrahor.de').'</link>
					<description>'.getsetting('rss_description','').'</description>
				</image>
	';
	/**
	 * @desc The RSS item template
	 */
	$str_xml_item = '
				<item>
					<title>%title%</title>
					<link>'.getsetting('rss_link','http://atrahor.de').'</link>
					<description>
					%description%
		  			</description>
					<category>'.$str_category.'</category>
					<pubDate>%pubdate%</pubDate>
				</item>
	';
	/**
	 * The RSS footer
	 */
	$str_xml_footer = '
			</channel>
		</rss>
	';

	/**********************************
	 * Start processing the array Data
	 **********************************/

	//Will contains the XML content of all items
	$str_xml_items = '';
	//The search array
	$arr_search = array('%title%','%link%','%description%','%pubdate%');
	//Process the data from the input array
	foreach($arr_data as $arr_item)
	{
		/**
		 * The array must have the indices
		 * title, link, description, pubdate
		 */
		if (!array_key_exists('title',$arr_item) ||
			!array_key_exists('link',$arr_item) ||
			!array_key_exists('description',$arr_item) ||
			!array_key_exists('pubdate',$arr_item))
		{
			return DATA_ARRAY_MALFORMED;
		}

		//Strip tags from the title
		$arr_replace['title'] = strip_appoencode($arr_item['title'],3,true);
		//The link to the
		$arr_replace['link'] = $arr_item['link'];
		//Strip tags from the title
		$arr_replace['description'] = strip_appoencode($arr_item['description'],3,true);
		//The date the item has been published
		$arr_replace['pubdate'] = $arr_item['pubdate'];

		//Generate one item
		$str_xml_items .= str_replace($arr_search,$arr_replace,$str_xml_item);
	}

	/**********************************
	 * Output the XML to a file
	 **********************************/
	$int_filehandle = fopen($str_file_name,'w');
	if(!$int_filehandle)
	{
		return FILE_NOT_WRITEABLE;
	}
	if(!fwrite($int_filehandle,$str_xml_header.$str_xml_items.$str_xml_footer))
	{
		return COULD_NOT_WRITE_DATA;
	}
	else
	{
		fclose($int_filehandle);
	}
}

?>