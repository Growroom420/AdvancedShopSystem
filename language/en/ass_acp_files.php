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
	'ASS_FILE'			=> 'File',
	'ASS_FILES'			=> 'Files',
	'ASS_FILENAME'		=> 'Filename',
	'ASS_FILETIME'		=> 'Filetime',
	'ASS_FILESIZE'		=> 'Filesize',

	'ASS_FILE_DELETE'			=> 'Delete file',
	'ASS_FILE_DELETE_CONFIRM'	=> 'Are you sure you wish to delete this file?
									<br>This action can <u>not</u> be reverted!',
	'ASS_FILE_DELETE_SUCCESS'	=> 'The file has been deleted successfully.',

	'ASS_FOLDER'		=> 'Folder',
	'ASS_FOLDERS'		=> 'Folders',

	'ASS_FOLDER_DELETE'			=> 'Delete folder',
	'ASS_FOLDER_DELETE_CONFIRM'	=> 'Are you sure you wish to delete this folder?
									<br>This will also <strong>delete all folders and files within</strong>.
									<br>This action can <u>not</u> be reverted!',
	'ASS_FOLDER_DELETE_SUCCESS'	=> 'The folder has been deleted successfully.',
]);
