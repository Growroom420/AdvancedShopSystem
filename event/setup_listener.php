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
	/** @var \phpbb\language\language */
	protected $language;

	/**
	 * Constructor.
	 *
	 * @param  \phpbb\language\language		$language		Language object
	 * @return void
	 * @access public
	 */
	public function __construct(\phpbb\language\language $language)
	{
		$this->language	= $language;
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
	 * Add ASS permissions.
	 *
	 * @event  core.permissions
	 * @param  \phpbb\event\data		$event		The event object
	 * @return void
	 * @access public
	 */
	public function ass_setup_permissions($event)
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
			'a_ass_overview',
			'a_ass_settings',
			'a_ass_items',
			'a_ass_files',
			'a_ass_logs',
			'u_ass_can_purchase',
			'u_ass_can_view_inactive_shop',
			'u_ass_can_view_inactive_items',
			'u_ass_can_gift',
			'u_ass_can_receive_gift',
			'u_ass_can_receive_stock_notifications',
		];

		foreach ($perms as $permission)
		{
			$permissions[$permission] = ['language' => 'ACL_' . utf8_strtoupper($permission), 'cat' => 'phpbb_studio'];
		}

		$event['permissions'] = $permissions;
	}
}
