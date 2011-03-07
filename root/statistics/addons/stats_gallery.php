<?php
/**
*
* @package phpBB Statistics
* @version $Id: stats_gallery.php 153 2010-06-21 18:00:30Z marc1706 $
* @copyright (c) 2009 - 2010 Marc Alexander(marc1706) www.m-a-styles.de
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/


/**
* @ignore
*/
if (!defined('IN_PHPBB') || !defined('IN_STATS_MOD'))
{
	exit;
}

/**
* @package phpBB Statistics - Gallery add-on
*/
class stats_gallery
{	
	/**
	* module filename
	* file must be in "statistics/addons/"
	*/
	var $module_file = 'stats_gallery';
	
	/**
	* module language name
	* please choose a distinct name, i.e. 'STATS_...'
	* $module_name always has to be $module_file in capital letters
	*/
	var $module_name = 'STATS_GALLERY';
	
	/**
	* module-language file
	* file must be in "language/{$user->lang}/mods/stats/addons/"
	*/
	var $template_file = 'stats_gallery';
	
	/**
	* set this to false if you do not need any acp settings
	*/
	var $load_acp_settings = false;
	
	/**
	* you can add some vars here
	*/
	var $u_action;
	
	/**
	* add-on functions below
	*/
	function load_stats()
	{
		global $config, $db, $template, $stats_config, $user;
		global $phpbb_root_path, $phpEx;
		
		$limit_count = request_var('limit_count', 10); //replace 10 by the config option

		//create an array containing the limit_count options as $option=>$option_lang
		$limit_options = array(
			'1'		=> 1,
			'3'		=> 3,
			'5'		=> 5,
			'10'	=> 10,
			'15'	=> 15,
		);
		$limit_prompt = sprintf($user->lang['LIMIT_PROMPT'], $user->lang['LIMIT_TEXT_GALLERY']);
		
		// include gallery constants
		if(!defined('ALBUM_CAT'))
		{
			include(GALLERY_ROOT_PATH . 'includes/constants.' . $phpEx);
		}
		$album_access_array = $this->get_album_access_array_stats();
		$album_ary = $this->gallery_acl_album_ids_stats('i_view');
		
		// Number of total albums
		$sql = 'SELECT COUNT(album_id) AS albums
				FROM ' . GALLERY_ALBUMS_TABLE . '
				WHERE album_type = ' . ALBUM_UPLOAD;
		$result = $db->sql_query($sql);
		$total_albums = (int) $db->sql_fetchfield('albums');
		$db->sql_freeresult($result);
		
		// Number of total categories
		$sql = 'SELECT COUNT(album_id) AS cats
				FROM ' . GALLERY_ALBUMS_TABLE . '
				WHERE album_type = ' . ALBUM_CAT;
		$result = $db->sql_query($sql);
		$total_cats = (int) $db->sql_fetchfield('cats');
		$db->sql_freeresult($result);
		
		// Number of total images
		$sql = 'SELECT COUNT(image_id) AS images
				FROM ' . GALLERY_IMAGES_TABLE;
		$result = $db->sql_query($sql);
		$total_images = (int) $db->sql_fetchfield('images');
		$db->sql_freeresult($result);
		
		// Number of total image views
		$sql = 'SELECT SUM(image_view_count) AS total_views
				FROM ' . GALLERY_IMAGES_TABLE;
		$result = $db->sql_query($sql);
		$total_views = (int) $db->sql_fetchfield('total_views');
		$db->sql_freeresult($result);
		
		// Number of total comments
		$sql = 'SELECT COUNT(comment_id) AS comments
				FROM ' . GALLERY_COMMENTS_TABLE;
		$result = $db->sql_query($sql);
		$total_comments = (int) $db->sql_fetchfield('comments');
		$db->sql_freeresult($result);
		
		// Number of total contests
		$sql = 'SELECT COUNT(album_id) AS contests
				FROM ' . GALLERY_ALBUMS_TABLE . '
				WHERE album_type = ' . ALBUM_CONTEST;
		$result = $db->sql_query($sql);
		$total_contests = (int) $db->sql_fetchfield('contests');
		$db->sql_freeresult($result);
		
		// Image information
		$sql = 'SELECT COUNT(image_id) AS images
				FROM ' . GALLERY_IMAGES_TABLE . '
				WHERE image_status = ' . IMAGE_UNAPPROVED;
		$result = $db->sql_query($sql);
		$unapproved_images = (int) $db->sql_fetchfield('images');
		$db->sql_freeresult($result);
		
		$sql = 'SELECT COUNT(image_id) AS images
				FROM ' . GALLERY_IMAGES_TABLE . '
				WHERE image_status = ' . IMAGE_APPROVED;
		$result = $db->sql_query($sql);
		$approved_images = (int) $db->sql_fetchfield('images');
		$db->sql_freeresult($result);
		
		$sql = 'SELECT COUNT(image_id) AS images
				FROM ' . GALLERY_IMAGES_TABLE . '
				WHERE image_status = ' . IMAGE_LOCKED;
		$result = $db->sql_query($sql);
		$locked_images = (int) $db->sql_fetchfield('images');
		$db->sql_freeresult($result);
		
		$sql = 'SELECT COUNT(image_id) AS images
				FROM ' . GALLERY_IMAGES_TABLE . '
				WHERE image_contest = ' . IMAGE_CONTEST;
		$result = $db->sql_query($sql);
		$contest_images = (int) $db->sql_fetchfield('images');
		$db->sql_freeresult($result);
		
		// Top albums by images
		$sql = 'SELECT album_id, album_name, album_images_real AS album_images
				FROM ' . GALLERY_ALBUMS_TABLE . '
				WHERE ' . $db->sql_in_set('album_id', $album_ary) . '
					AND album_user_id < 1
				GROUP BY album_id, album_name, album_images_real
				ORDER BY album_images DESC';
		$result = $db->sql_query_limit($sql, $limit_count);
		while ($row = $db->sql_fetchrow($result))
		{
			$top_albums_by_images[] = $row;
		}
		$db->sql_freeresult($result);
		
		if(isset($top_albums_by_images))
		{
			$max_count = $top_albums_by_images[0]['album_images'];
			$template->assign_var('S_TOP_ALBUMS_IMAGES', true);
			foreach ($top_albums_by_images as $current_album)
			{						
				$template->assign_block_vars('albums_images_row', array(
					'U_ALBUM'					=> append_sid(GALLERY_ROOT_PATH . 'album.' . $phpEx, 'album_id=' . $current_album['album_id']),
					'ALBUM_NAME'				=> $current_album['album_name'],
					'COUNT'						=> $current_album['album_images'],
					'PCT'						=> number_format($current_album['album_images'] / $total_images * 100, 3),
					'BARWIDTH'					=> number_format($current_album['album_images'] / $max_count * 100, 1),
				));
			}
		}
		
		// Top images by views
		$sql = 'SELECT image_album_id, image_id, image_name, image_view_count AS views
				FROM ' . GALLERY_IMAGES_TABLE . '
				WHERE ' . $db->sql_in_set('image_album_id', $album_ary) . '
				GROUP BY image_id, image_name, image_album_id, image_view_count
				ORDER BY image_view_count DESC';
		$result = $db->sql_query_limit($sql, $limit_count);
		while ($row = $db->sql_fetchrow($result))
		{
			$top_images_by_views[] = $row;
		}
		$db->sql_freeresult($result);

		if(isset($top_images_by_views))
		{
			$max_count = $top_images_by_views[0]['views'];
			$template->assign_var('S_TOP_IMAGES_VIEWS', true);
			foreach ($top_images_by_views as $current_image)
			{						
				$template->assign_block_vars('images_views_row', array(
					'U_IMAGE'					=> append_sid(GALLERY_ROOT_PATH . 'image.' . $phpEx, 'album_id=' . $current_image['image_album_id'] . '&amp;image_id=' . $current_image['image_id']),
					'IMAGE_NAME'				=> $current_image['image_name'],
					'COUNT'						=> $current_image['views'],
					'PCT'						=> number_format($current_image['views'] / $total_views * 100, 3),
					'BARWIDTH'					=> number_format($current_image['views'] / $max_count * 100, 1),
				));
			}
		}
		
		// Top images by rating
		$sql = 'SELECT image_album_id, image_id, image_name, image_rate_avg AS rating
				FROM ' . GALLERY_IMAGES_TABLE . '
				WHERE ' . $db->sql_in_set('image_album_id', $album_ary) . '
				GROUP BY image_id, image_name, image_album_id, image_rate_avg
				ORDER BY image_rate_avg DESC';
		$result = $db->sql_query_limit($sql, $limit_count);
		while ($row = $db->sql_fetchrow($result))
		{
			$top_images_by_rating[] = $row;
		}
		$db->sql_freeresult($result);

		if(isset($top_images_by_rating) && $top_images_by_rating[0]['rating'] > 0)
		{
			$max_count = $top_images_by_rating[0]['rating'];
			$template->assign_var('S_TOP_IMAGES_RATING', true);
			foreach ($top_images_by_rating as $current_image)
			{						
				$template->assign_block_vars('images_rating_row', array(
					'U_IMAGE'					=> append_sid(GALLERY_ROOT_PATH . 'image.' . $phpEx, 'album_id=' . $current_image['image_album_id'] . '&amp;image_id=' . $current_image['image_id']),
					'IMAGE_NAME'				=> $current_image['image_name'],
					'COUNT'						=> number_format($current_image['rating'] / 100, 3),
					'PCT'						=> number_format($current_image['rating'] / 10, 3),
					'BARWIDTH'					=> number_format($current_image['rating'] / $max_count * 100, 1),
				));
			}
		}
		
		// Top users by images
		$sql = 'SELECT gu.user_id AS user_id, gu.user_images AS count, u.username AS username, u.user_colour AS user_colour
				FROM ' . GALLERY_USERS_TABLE . ' gu, ' . USERS_TABLE . ' u
				WHERE gu.user_id = u.user_id
				GROUP BY gu.user_id, u.username, u.user_colour, gu.user_images
				ORDER BY count DESC';
		$result = $db->sql_query_limit($sql, $limit_count);
		while ($row = $db->sql_fetchrow($result))
		{
			$top_users_by_images[] = $row;
		}
		$db->sql_freeresult($result);

		if(isset($top_users_by_images))
		{
			$max_count = $top_users_by_images[0]['count'];
			$template->assign_var('S_TOP_USERS_IMAGES', true);
			foreach ($top_users_by_images as $current_user)
			{						
				$template->assign_block_vars('users_images_row', array(
					'U_USER'					=> get_username_string('full', $current_user['user_id'], $current_user['username'], $current_user['user_colour']),
					'COUNT'						=> number_format($current_user['count']),
					'PCT'						=> number_format($current_user['count'] / $total_images  * 100, 3),
					'BARWIDTH'					=> number_format($current_user['count'] / $max_count * 100, 1),
				));
			}
		}
		
		
		
		$template->assign_vars(array(
			'TOTAL_IMAGES'				=> $total_images,
			'TOTAL_CATS'				=> $total_cats,
			'TOTAL_ALBUMS'				=> $total_albums,
			'TOTAL_CONTESTS'			=> $total_contests,
			'TOTAL_COMMENTS'			=> $total_comments,
			'APPROVED_IMAGES'			=> $approved_images,
			'UNAPPROVED_IMAGES'			=> $unapproved_images,
			'LOCKED_IMAGES'				=> $locked_images,
			'CONTEST_IMAGES'			=> $contest_images,
			'TOP_ALBUMS_BY_IMAGES'		=> sprintf($user->lang['TOP_ALBUMS_BY_IMAGES'], $limit_count),
			'TOP_IMAGES_BY_VIEWS'		=> sprintf($user->lang['TOP_IMAGES_BY_VIEWS'], $limit_count),
			'TOP_IMAGES_BY_RATING'		=> sprintf($user->lang['TOP_IMAGES_BY_RATING'], $limit_count),
			'TOP_USERS_BY_IMAGES'		=> sprintf($user->lang['TOP_USERS_BY_IMAGES'], $limit_count),
		));
		
		$template->assign_var('LIMIT_SELECT_BOX', make_select_box($limit_options, $limit_count, 'limit_count', $limit_prompt, $user->lang['GO'], $this->u_action));
	}
	
	/**
	* acp frontend for the add-on
	* if you want to use this, set $load_acp_settings to true
	*/
	function load_acp()
	{
		$display_vars = array(
					'title' => 'STATS_TEST',
					'vars' => array(
						'legend1' 							=> 'STATS_TEST',
						'stats_test'						=> array('lang' => 'STATS_SHOW'  , 'validate' => 'bool'  , 'type' => 'radio:yes_no'  , 'explain' => false),
					)
				);
	
	}
	
	
	/**
	* API functions
	*/
	function install()
	{
		global $db;
		
		$sql = 'SELECT addon_id AS addon_id FROM ' . STATS_ADDONS_TABLE . ' ORDER BY addon_id DESC';
		$result = $db->sql_query_limit($sql, 1);
		$id = (int) $db->sql_fetchfield('addon_id');
		$db->sql_freeresult($result);
	
		set_stats_addon($this->module_file, 1);
		
		$sql = 'UPDATE ' . STATS_ADDONS_TABLE . '
				SET addon_id = ' . ($id + 1) . "
				WHERE addon_classname = '" . $this->module_file . "'";
		$result = $db->sql_query($sql);
		$db->sql_freeresult($result);
	}
	
	function uninstall()
	{
		global $db;
		
		$del_addon = $this->module_file;
		
		$sql = 'DELETE FROM ' . STATS_ADDONS_TABLE . "
			WHERE addon_classname = '" . $del_addon . "'";
		return $db->sql_query($sql);
	}
	
	/**
	*
	* @package phpBB Gallery
	* @version $Id: stats_gallery.php 153 2010-06-21 18:00:30Z marc1706 $
	* @copyright (c) 2007 nickvergessen nickvergessen@gmx.de http://www.flying-bits.org
	* @license http://opensource.org/licenses/gpl-license.php GNU Public License
	* included in order to prevent errors
	*/
	function get_album_access_array_stats()
	{
		global $cache, $config, $db, $user;
		global $album_access_array, $gallery_config;

		if (!isset($gallery_config['loaded']))
		{
			// If we don't have the config, we don't have the function to call it aswell?
			$sql = 'SELECT *
				FROM ' . GALLERY_CONFIG_TABLE;
			$result = $db->sql_query($sql);

			while ($row = $db->sql_fetchrow($result))
			{
				$gallery_config[$row['config_name']] = $row['config_value'];
			}
			$db->sql_freeresult($result);
			$gallery_config['loaded'] = true;
		}
		$albums = $cache->obtain_album_list();

		$permissions = $permission_parts['misc'] = $permission_parts['m'] = $permission_parts['c'] = $permission_parts['i'] = array();
		$permission_parts['i'] = array('i_view', 'i_watermark', 'i_upload', 'i_approve', 'i_edit', 'i_delete', 'i_report', 'i_rate');
		$permission_parts['c'] = array('c_read', 'c_post', 'c_edit', 'c_delete');
		$permission_parts['m'] = array('m_comments', 'm_delete', 'm_edit', 'm_move', 'm_report', 'm_status');
		$permission_parts['misc'] = array('a_list', 'i_count', 'i_unlimited', 'album_count', 'album_unlimited');
		$permissions = array_merge($permissions, $permission_parts['i'], $permission_parts['c'], $permission_parts['m'], $permission_parts['misc']);

		if (!$album_access_array)
		{
			$pull_data = '';
			$user_groups_ary = array();

			//set all parts of the permissions to 0 / "no"
			foreach ($permissions as $permission)
			{
				$album_access_array[-1][$permission] = GALLERY_ACL_NO;
				$album_access_array[OWN_GALLERY_PERMISSIONS][$permission] = GALLERY_ACL_NO;
				$album_access_array[PERSONAL_GALLERY_PERMISSIONS][$permission] = GALLERY_ACL_NO;
				//generate for the sql
				$pull_data .= " MAX($permission) as $permission,";
			}
			$album_access_array[-1]['m_'] = GALLERY_ACL_NO;
			$album_access_array[OWN_GALLERY_PERMISSIONS]['m_'] = GALLERY_ACL_NO;
			$album_access_array[PERSONAL_GALLERY_PERMISSIONS]['m_'] = GALLERY_ACL_NO;
			foreach ($albums as $album)
			{
				foreach ($permissions as $permission)
				{
					$album_access_array[$album['album_id']][$permission] = GALLERY_ACL_NO;
				}
				$album_access_array[$album['album_id']]['m_'] = GALLERY_ACL_NO;
			}

			// Testing user permissions?
			$user_id = ($user->data['user_perm_from'] == 0) ? $user->data['user_id'] : $user->data['user_perm_from'];

			// Only available in >= 3.0.6
			if (version_compare($config['version'], '3.0.5', '>'))
			{
				$sql = 'SELECT ug.group_id
					FROM ' . USER_GROUP_TABLE . ' ug
					LEFT JOIN ' . GROUPS_TABLE . " g
						ON (ug.group_id = g.group_id)
					WHERE ug.user_id = $user_id
						AND ug.user_pending = 0
						AND g.group_skip_auth = 0";
			}
			else
			{
				$sql = 'SELECT group_id
					FROM ' . USER_GROUP_TABLE . "
					WHERE user_id = $user_id
						AND user_pending = 0";
			}
			$result = $db->sql_query($sql);
			while ($row = $db->sql_fetchrow($result))
			{
				$user_groups_ary[] = $row['group_id'];
			}
			$db->sql_freeresult($result);

			$sql_array = array(
				'SELECT'		=> "p.perm_album_id, $pull_data p.perm_system",
				'FROM'			=> array(GALLERY_PERMISSIONS_TABLE => 'p'),

				'LEFT_JOIN'		=> array(
					array(
						'FROM'		=> array(GALLERY_ROLES_TABLE => 'pr'),
						'ON'		=> 'p.perm_role_id = pr.role_id',
					),
				),

				'WHERE'			=> 'p.perm_user_id = ' . $user_id . ' OR ' . $db->sql_in_set('p.perm_group_id', $user_groups_ary, false, true),
				'GROUP_BY'		=> 'p.perm_system DESC, p.perm_album_id ASC',
			);
			$sql = $db->sql_build_query('SELECT', $sql_array);
			$result = $db->sql_query($sql);
			while ($row = $db->sql_fetchrow($result))
			{
				switch ($row['perm_system'])
				{
					case PERSONAL_GALLERY_PERMISSIONS:
						foreach ($permissions as $permission)
						{
							$album_access_array[PERSONAL_GALLERY_PERMISSIONS][$permission] = $row[$permission];
							if ((substr($permission, 0, 2) == 'm_') && ($row[$permission] == GALLERY_ACL_YES))
							{
								$album_access_array[PERSONAL_GALLERY_PERMISSIONS]['m_'] = $row[$permission];
							}
						}
					break;

					case OWN_GALLERY_PERMISSIONS:
						foreach ($permissions as $permission)
						{
							$album_access_array[OWN_GALLERY_PERMISSIONS][$permission] = $row[$permission];
							if ((substr($permission, 0, 2) == 'm_') && ($row[$permission] == GALLERY_ACL_YES))
							{
								$album_access_array[OWN_GALLERY_PERMISSIONS]['m_'] = $row[$permission];
							}
						}
					break;

					case 1:
						foreach ($permissions as $permission)
						{
							// if the permission is true ($row[$permission] == 1) and global_permission is never ($album_access_array[PERSONAL_GALLERY_PERMISSIONS][$permission] == 2) we set it to "never"
							$album_access_array[$row['perm_album_id']][$permission] = (($row[$permission]) ? (($row[$permission] == GALLERY_ACL_YES && ($album_access_array[PERSONAL_GALLERY_PERMISSIONS][$permission] == GALLERY_ACL_NEVER)) ? $album_access_array[PERSONAL_GALLERY_PERMISSIONS][$permission] : $row[$permission]) : GALLERY_ACL_NO);
							if ((substr($permission, 0, 2) == 'm_') && ($row[$permission] == GALLERY_ACL_YES))
							{
								$album_access_array[$row['perm_album_id']]['m_'] = $row[$permission];
							}
						}
					break;

					case 0:
						foreach ($permissions as $permission)
						{
							$album_access_array[$row['perm_album_id']][$permission] = $row[$permission];
							if ((substr($permission, 0, 2) == 'm_') && ($row[$permission] == GALLERY_ACL_YES))
							{
								$album_access_array[$row['perm_album_id']]['m_'] = $row[$permission];
							}
						}
					break;
				}
			}
			$db->sql_freeresult($result);
		}

		return $album_access_array;
	}
	
	/**
	* Get album lists by permissions
	*
	* @param	string	$permission		One of the permissions, Exp: i_view
	* @param	string	$mode			'array' || 'string'
	* please run this before accessing this function:
	* $album_access_array = $this->get_album_access_array_stats();
	*/
	function gallery_acl_album_ids_stats($permission, $mode = 'array', $display_in_rrc = false, $display_pgalleries = true)
	{
		global $user, $album_access_array, $cache;

		$album_list = '';
		$album_array = array();
		$albums = $cache->obtain_album_list();
		foreach ($albums as $album)
		{
			if ($album['album_user_id'] == $user->data['user_id'])
			{
				$acl_case = OWN_GALLERY_PERMISSIONS;
			}
			else if ($album['album_user_id'] > NON_PERSONAL_ALBUMS)
			{
				$acl_case = PERSONAL_GALLERY_PERMISSIONS;
			}
			else
			{
				$acl_case = $album['album_id'];
			}
			if (($album_access_array[$acl_case][$permission] == GALLERY_ACL_YES) && (!$display_in_rrc || ($display_in_rrc && $album['display_in_rrc'])) && ($display_pgalleries || ($album['album_user_id'] == NON_PERSONAL_ALBUMS)))
			{
				$album_list .= (($album_list) ? ', ' : '') . $album['album_id'];
				$album_array[] = $album['album_id'];
			}
		}

		return ($mode == 'array') ? $album_array : $album_list;
	}
	
	// END - @package phpBB Gallery
}
?>