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
	'ASS_DISPLAY_BIGGEST_BUYERS'		=> 'Biggest buyers',
	'ASS_DISPLAY_BIGGEST_GIFTERS'		=> 'Biggest gifters',
	'ASS_DISPLAY_BIGGEST_SPENDERS'		=> 'Biggest spenders',
	'ASS_DISPLAY_LIMITED_AVAILABLE'		=> 'Limited availability items',
	'ASS_DISPLAY_LIMITED_STOCK'			=> 'Limited stock items',
	'ASS_DISPLAY_PURCHASES_CATEGORY'	=> 'Purchases per category',
	'ASS_DISPLAY_PURCHASES_GROUP'		=> 'Purchases per group',
	'ASS_DISPLAY_PURCHASES_RECENT'		=> 'Recent purchases',
]);
