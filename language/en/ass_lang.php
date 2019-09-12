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
	'ASS_INVENTORY'			=> 'Inventory',
	'ASS_SHOP'				=> 'Shop',

	'ASS_NOTIFICATION_GROUP'		=> 'Shop Notifications',
	'ASS_NOTIFICATION_TYPE_GIFT'	=> 'Someone gave you a gift',
	'ASS_NOTIFICATION_TYPE_STOCK'	=> 'An item has reached its low stock threshold',

	'ASS_NOTIFICATION_GIFT'			=> '<strong>You have received a gift</strong> from %s',
	'ASS_NOTIFICATION_STOCK'		=> '<strong>An item is low on stock:</strong><br>&raquo; %s',
]);
