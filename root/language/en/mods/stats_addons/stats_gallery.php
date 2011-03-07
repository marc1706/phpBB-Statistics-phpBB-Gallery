<?php
/**
*
* @package phpBB Statistics
* @version $Id: stats_gallery.php 99 2010-03-04 20:40:53Z marc1706 $
* @copyright (c) 2009 - 2010 Marc Alexander(marc1706) www.m-a-styles.de
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* @translator (c) ( Marc Alexander - http://www.m-a-styles.de ), TheUniqueTiger - Nayan Ghosh
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
	'STATS_GALLERY'				=> 'Gallery Statistics',
	'STATS_GALLERY_EXPLAIN'		=> 'This Add-On displays statistics about your Gallery',
	'TOTAL_CATS'				=> 'Total categories',
	'TOTAL_ALBUMS' 				=> 'Total albums',
	'TOTAL_CONTESTS'			=> 'Total contests',
	'TOTAL_IMAGES'				=> 'Total images',
	'TOTAL_COMMENTS'			=> 'Total comments',
	'IMAGE_INFO'				=> 'Images',
	'APPROVED_IMAGES'			=> 'Approved Images',
	'UNAPPROVED_IMAGES'			=> 'Unapproved Images',
	'LOCKED_IMAGES'				=> 'Locked Images',
	'CONTEST_IMAGES'			=> 'Contest Images',
	'LIMIT_TEXT_GALLERY'		=> 'albums, images, users',
	'ALBUMS'					=> 'Albums',
	'TOP_ALBUMS_BY_IMAGES'		=> 'Top %d albums (by images)',
	'TOP_IMAGES_BY_VIEWS'		=> 'Top %d images (by views)',
	'TOP_IMAGES_BY_RATING'		=> 'Top %d images (by rating)',
	'TOP_USERS_BY_IMAGES'		=> 'Top %d users (by images)',
	'IMAGES'					=> 'Images',
));
?>