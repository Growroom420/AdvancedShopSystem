<?php
/**
 *
 * phpBB Studio - Advanced Shop System. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

/**
* Some characters you may want to copy&paste: ’ » “ ” …
*/
$lang = array_merge($lang, [
	'ACL_CAT_PHPBB_STUDIO'	=> 'phpBB Studio',

	'ACL_A_ASS_OVERVIEW'	=> '<strong>Advanced Shop System</strong> - Can see the overview',
	'ACL_A_ASS_SETTINGS'	=> '<strong>Advanced Shop System</strong> - Can manage the settings',
	'ACL_A_ASS_ITEMS'		=> '<strong>Advanced Shop System</strong> - Can manage the items',
	'ACL_A_ASS_FILES'		=> '<strong>Advanced Shop System</strong> - Can manage the files',
	'ACL_A_ASS_LOGS'		=> '<strong>Advanced Shop System</strong> - Can manage the logs',

	'ACL_U_ASS_CAN_PURCHASE'						=> '<strong>Advanced Shop System</strong> - Can purchase',
	'ACL_U_ASS_CAN_VIEW_INACTIVE_SHOP'				=> '<strong>Advanced Shop System</strong> - Can view inactive shop',
	'ACL_U_ASS_CAN_VIEW_INACTIVE_ITEMS'				=> '<strong>Advanced Shop System</strong> - Can view inactive items',
	'ACL_U_ASS_CAN_GIFT'							=> '<strong>Advanced Shop System</strong> - Can gift',
	'ACL_U_ASS_CAN_RECEIVE_GIFT'					=> '<strong>Advanced Shop System</strong> - Can receive gift',
	'ACL_U_ASS_CAN_RECEIVE_STOCK_NOTIFICATIONS'		=> '<strong>Advanced Shop System</strong> - Can receive stock notifications',
]);
