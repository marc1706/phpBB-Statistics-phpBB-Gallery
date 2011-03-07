<?php
/**
*
* @package phpBB Statistics
* @version $Id: stats_gallery.php 100 2010-03-05 22:58:14Z marc1706 $
* @copyright (c) 2009 - 2010 Marc Alexander(marc1706) www.m-a-styles.de
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* @translator (c) ( Marc Alexander - http://www.m-a-styles.de )
*/

if (!defined('IN_PHPBB') || !defined('IN_STATS_MOD'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}


/*	Example:
$lang = array_merge($lang, array(	
	'STATS'								=> 'phpBB Statistics',	

));
*/

$lang = array_merge($lang, array(	
	'STATS_GALLERY'				=> 'Gallerie Statistiken',
	'STATS_GALLERY_EXPLAIN'		=> 'Dieses Add-On zeigt Statistiken über deine Gallerie an',
	'TOTAL_CATS'				=> 'Anzahl Kategorien',
	'TOTAL_ALBUMS' 				=> 'Anzahl Alben',
	'TOTAL_CONTESTS'			=> 'Anzahl Wettbewerbe',
	'TOTAL_IMAGES'				=> 'Anzahl Bilder',
	'TOTAL_COMMENTS'			=> 'Anzahl Bilder',
	'IMAGE_INFO'				=> 'Bilder',
	'APPROVED_IMAGES'			=> 'Freigegebene Bilder',
	'UNAPPROVED_IMAGES'			=> 'Auf Freigabe wartende Bilder',
	'LOCKED_IMAGES'				=> 'Gesperrte Bilder',
	'CONTEST_IMAGES'			=> 'Wettbewerbsbilder',
	'LIMIT_TEXT_GALLERY'		=> 'Alben, Bilder, Benutzer',
	'ALBUMS'					=> 'Alben',
	'TOP_ALBUMS_BY_IMAGES'		=> 'Top %d Alben (nach Bildern)',
	'TOP_IMAGES_BY_VIEWS'		=> 'Top %d Bilder (nach Zugriffen)',
	'TOP_IMAGES_BY_RATING'		=> 'Top %d Bilder (nach Bewertung)',
	'TOP_USERS_BY_IMAGES'		=> 'Top %d Benutzer (nach Bildern)',
	'IMAGES'					=> 'Bilder',
));
?>