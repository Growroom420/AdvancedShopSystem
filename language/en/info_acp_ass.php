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
	'ACP_ASS_SYSTEM'		=> 'Advanced Shop System',
	'ACP_ASS_CATEGORIES'	=> 'Categories',
	'ACP_ASS_FILES'			=> 'Files',
	'ACP_ASS_INVENTORY'		=> 'Inventory',
	'ACP_ASS_ITEMS'			=> 'Items',
	'ACP_ASS_LOGS'			=> 'Logs',
	'ACP_ASS_OVERVIEW'		=> 'Overview',
	'ACP_ASS_SETTINGS'		=> 'Settings',

	'LOG_ACP_ASS_CATEGORY_ADDED'		=> '<strong>Advanced Shop System &mdash;</strong> Category added<br>» %s',
	'LOG_ACP_ASS_CATEGORY_DELETED'		=> '<strong>Advanced Shop System &mdash;</strong> Category deleted<br>» %s',
	'LOG_ACP_ASS_CATEGORY_EDITED'		=> '<strong>Advanced Shop System &mdash;</strong> Category edited<br>» %s',

	'LOG_ACP_ASS_ITEM_ADDED'			=> '<strong>Advanced Shop System &mdash;</strong> Item added<br>» %s',
	'LOG_ACP_ASS_ITEM_DELETED'			=> '<strong>Advanced Shop System &mdash;</strong> Item deleted<br>» %s',
	'LOG_ACP_ASS_ITEM_EDITED'			=> '<strong>Advanced Shop System &mdash;</strong> Item edited<br>» %s',

	'LOG_ACP_ASS_ITEM_RESOLVED'			=> '<strong>Advanced Shop System &mdash;</strong> Item conflict resolved<br>» %s',

	'LOG_ACP_ASS_INVENTORY_ADD'			=> '<strong>Advanced Shop System &mdash;</strong> Added items to users’ inventories.<br>» Items (%1$s): %2$s<br>» Users (%3$s): %4$s',
	'LOG_ACP_ASS_INVENTORY_ADD_USER'	=> '<strong>Advanced Shop System &mdash;</strong> Added items to a user’s inventory.<br>» Items (%1$s): %2$s<br>» Username: %3$s',
	'LOG_ACP_ASS_INVENTORY_DELETE'		=> '<strong>Advanced Shop System &mdash;</strong> Deleted items from users’ inventories.<br>» Items (%1$s): %2$s<br>» Users (%3$s): %4$s',
	'LOG_ACP_ASS_INVENTORY_DELETE_USER'	=> '<strong>Advanced Shop System &mdash;</strong> Deleted an item from a user’s inventory.<br>» Item: %1$s<br>» Username: %2$s',

	'LOG_ACP_ASS_LOG_DELETED_ALL'		=> '<strong>Advanced Shop System &mdash;</strong> Deleted all log entries',
	'LOG_ACP_ASS_LOG_DELETED_ENTRY'		=> '<strong>Advanced Shop System &mdash;</strong> Deleted a log entry',
	'LOG_ACP_ASS_LOG_DELETED_ENTRIES'	=> '<strong>Advanced Shop System &mdash;</strong> Deleted multiple log entries',

	'LOG_ACP_ASS_ITEM_TYPE_ERROR'		=> '<strong>Advanced Shop System &mdash;</strong> An error occurred while activating an item:<br>» %1$s › %2$s',
]);
