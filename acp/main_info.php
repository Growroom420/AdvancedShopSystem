<?php
/**
 *
 * phpBB Studio - Advanced Shop System. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbstudio\ass\acp;

/**
 * phpBB Studio - Advanced Shop System: ACP info
 */
class main_info
{
	public function module()
	{
		return [
			'filename'	=> '\phpbbstudio\ass\acp\main_module',
			'title'		=> 'ACP_ASS_SYSTEM',
			'modes'		=> [
				'overview'		=> [
					'title'	=> 'ACP_ASS_OVERVIEW',
					'auth'	=> 'ext_phpbbstudio/ass && acl_a_ass_overview',
					'cat'	=> ['ACP_ASS_SYSTEM'],
				],
				'settings'		=> [
					'title'	=> 'ACP_ASS_SETTINGS',
					'auth'	=> 'ext_phpbbstudio/ass && acl_a_ass_settings',
					'cat'	=> ['ACP_ASS_SYSTEM'],
				],
				'items'			=> [
					'title'	=> 'ACP_ASS_ITEMS',
					'auth'	=> 'ext_phpbbstudio/ass && acl_a_ass_items',
					'cat'	=> ['ACP_ASS_SYSTEM'],
				],
				'files'			=> [
					'title'	=> 'ACP_ASS_FILES',
					'auth'	=> 'ext_phpbbstudio/ass && acl_a_ass_files',
					'cat'	=> ['ACP_ASS_SYSTEM'],
				],
				'logs'			=> [
					'title'	=> 'ACP_ASS_LOGS',
					'auth'	=> 'ext_phpbbstudio/ass && acl_a_ass_logs',
					'cat'	=> ['ACP_ASS_SYSTEM'],
				],
			],
		];
	}
}
