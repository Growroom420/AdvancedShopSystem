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
	'ASS_DISABLE_EXTENDED'	=> 'Disabling the Advanced Shop System is not possible as it is still being extended by an other extension. Extension name: %s',
	'ASS_REQUIRES_APS'		=> 'The <strong>“Advanced Shop System”</strong> extensions requires that the <strong>“Advanced Points System”</strong> extension is enabled.',
]);
