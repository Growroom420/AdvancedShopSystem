<?php
/**
 *
 * phpBB Studio - Advanced Shop System. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbstudio\ass\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * phpBB Studio - Advanced Shop System: Setup listener
 */
class setup_listener implements EventSubscriberInterface
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbbstudio\aps\core\functions */
	protected $functions;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\template\template */
	protected $template;

	/**
	 * Constructor.
	 *
	 * @param  \phpbb\config\config				$config			Config object
	 * @param  \phpbbstudio\aps\core\functions	$functions		APS Functions object
	 * @param  \phpbb\language\language			$language		Language object
	 * @param  \phpbb\template\template			$template		Template object
	 * @return void
	 * @access public
	 */
	public function __construct(
		\phpbb\config\config $config,
		\phpbbstudio\aps\core\functions $functions,
		\phpbb\language\language $language,
		\phpbb\template\template $template
	)
	{
		$this->config		= $config;
		$this->functions	= $functions;
		$this->language		= $language;
		$this->template		= $template;
	}

	/**
	 * Assign functions defined in this class to event listeners in the core.
	 *
	 * @return array
	 * @access public
	 * @static
	 */
	static public function getSubscribedEvents()
	{
		return [
			'core.user_setup_after'		=> 'ass_load_lang',
			'core.page_header_after'	=> 'ass_setup_links',
			'core.permissions'			=> 'ass_setup_permissions',
		];
	}

	/**
	 * Load language after user set up.
	 *
	 * @event  core.user_setup_after
	 * @return void
	 * @access public
	 */
	public function ass_load_lang()
	{
		$this->language->add_lang('ass_lang', 'phpbbstudio/ass');
	}

	/**
	 * Set up ASS link locations.
	 *
	 * @return void
	 */
	public function ass_setup_links()
	{
		$locations = array_filter($this->functions->get_link_locations('ass_link_locations'));

		if ($locations)
		{
			$this->template->assign_vars(array_combine(array_map(function($key) {
				return 'S_ASS_' . strtoupper($key);
			}, array_keys($locations)), $locations));
		}

		$this->template->assign_vars([
			'ASS_SHOP_ICON'		=> (string) $this->config['ass_shop_icon'],
			'S_ASS_ENABLED'		=> (bool) $this->config['ass_enabled'],
		]);
	}

	/**
	 * Add ASS permissions.
	 *
	 * @event  core.permissions
	 * @param  \phpbb\event\data		$event		The event object
	 * @return void
	 * @access public
	 */
	public function ass_setup_permissions(\phpbb\event\data $event)
	{
		$categories = $event['categories'];
		$permissions = $event['permissions'];

		if (empty($categories['phpbb_studio']))
		{
			/* Setting up a custom CAT */
			$categories['phpbb_studio'] = 'ACL_CAT_PHPBB_STUDIO';

			$event['categories'] = $categories;
		}

		$perms = [
			'a_ass_inventory',
			'a_ass_items',
			'a_ass_files',
			'a_ass_logs',
			'a_ass_overview',
			'a_ass_settings',
			'u_ass_can_gift',
			'u_ass_can_purchase',
			'u_ass_can_receive_gift',
			'u_ass_can_receive_stock_notifications',
			'u_ass_can_stack',
			'u_ass_can_view_inactive_items',
			'u_ass_can_view_inactive_shop',
		];

		foreach ($perms as $permission)
		{
			$permissions[$permission] = ['language' => 'ACL_' . utf8_strtoupper($permission), 'cat' => 'phpbb_studio'];
		}

		$event['permissions'] = $permissions;
	}
}
