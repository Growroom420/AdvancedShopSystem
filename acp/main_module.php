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
 * phpBB Studio - Advanced Shop System: ACP module
 */
class main_module
{
	/** @var string ACP Page title */
	public $page_title;

	/** @var string ACP Page template */
	public $tpl_name;

	/** @var string Custom form action */
	public $u_action;

	public function main($id, $mode)
	{
		/** @var \Symfony\Component\DependencyInjection\ContainerInterface $phpbb_container */
		global $phpbb_container;

		$language = $phpbb_container->get('language');

		$request = $phpbb_container->get('request');

		if ($request->variable('action', '', true) === 'select_file')
		{
			$mode = 'files';
		}

		/** @var \phpbbstudio\ass\controller\acp_settings_controller $controller */
		$controller = $phpbb_container->get("phpbbstudio.ass.controller.acp.{$mode}");

		// Set the page title and template
		$this->tpl_name = 'ass_' . $mode;
		$this->page_title = $language->lang('ACP_ASS_SYSTEM') . ' &bull; ' . $language->lang('ACP_ASS_' . utf8_strtoupper($mode));

		// Make the custom form action available in the controller and handle the mode
		$controller->set_page_url($this->u_action)->{$mode}();
	}
}
