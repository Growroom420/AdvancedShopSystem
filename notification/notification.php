<?php
/**
 *
 * phpBB Studio - Advanced Shop System. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbstudio\ass\notification;

use phpbbstudio\ass\entity\item;

/**
 * phpBB Studio - Advanced Shop System: Notification
 */
class notification
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\notification\manager */
	protected $manager;

	/** @var \phpbbstudio\ass\operator\item */
	protected $operator_item;

	/** @var \phpbb\user */
	protected $user;

	/**
	 * Constructor.
	 *
	 * @param  \phpbb\config\config				$config				Config object
	 * @param  \phpbb\notification\manager		$manager			Notification manager object
	 * @param  \phpbbstudio\ass\operator\item	$operator_item		Item operator object
	 * @param  \phpbb\user						$user				User object
	 * @return void
	 * @access public
	 */
	public function __construct(
		\phpbb\config\config $config,
		\phpbb\notification\manager $manager,
		\phpbbstudio\ass\operator\item $operator_item,
		\phpbb\user $user
	)
	{
		$this->config			= $config;
		$this->manager			= $manager;
		$this->operator_item	= $operator_item;
		$this->user				= $user;
	}

	/**
	 * Send a "Received a gift" notification.
	 *
	 * @param  item		$item				The item entity
	 * @param  int		$user_id			The user identifier
	 * @param  int		$inventory_id		The inventory identifier
	 * @param  int		$inventory_index	The inventory index identifier
	 * @return void
	 * @access public
	 */
	public function gift(item $item, $user_id, $inventory_id, $inventory_index)
	{
		$this->config->increment('ass_notification_gift_id', 1);

		$this->manager->add_notifications('phpbbstudio.ass.notification.type.gift', [
			'notification_id'	=> (int) $this->config['ass_notification_gift_id'],
			'inventory_id'		=> (int) $inventory_id,
			'inventory_index'	=> (int) $inventory_index,
			'category_slug'		=> $item->get_category_slug(),
			'item_slug'			=> $item->get_slug(),
			'user_id'			=> (int) $this->user->data['user_id'],
			'recipient_id'		=> (int) $user_id,
		]);
	}

	/**
	 * Send a "Low stock" notification.
	 *
	 * @param  item		$item				The item entity
	 * @return void
	 * @access public
	 */
	public function low_stock(item $item)
	{
		$this->config->increment('ass_notification_stock_id', 1);

		$this->manager->add_notifications('phpbbstudio.ass.notification.type.stock', [
			'notification_id'	=> (int) $this->config['ass_notification_stock_id'],
			'category_slug'		=> $item->get_category_slug(),
			'item_slug'			=> $item->get_slug(),
			'item_title'		=> $item->get_title(),
			'item_avatar'		=> $this->get_avatar($item),
			'item_id'			=> $item->get_id(),
		]);
	}

	/**
	 * Get an "avatar" (image or icon) for an item.
	 *
	 * @param  item		$item				The item entity
	 * @return string						The item avatar
	 * @access protected
	 */
	protected function get_avatar(item $item)
	{
		if ($item->get_background())
		{
			$src = $this->operator_item->get_background_path($item->get_background(), false);
			$src = generate_board_url() . '/' . $src;

			return '<img src="' . $src . '" alt="' . $item->get_title() . '">';
		}
		else if ($item->get_icon())
		{
			return $this->get_avatar_icon($item->get_icon());
		}
		else if ($this->config['ass_no_image_icon'])
		{
			return $this->get_avatar_icon($this->config['ass_no_image_icon']);
		}
		else
		{
			return '';
		}
	}

	/**
	 * Get an icon that can be used as item avatar.
	 *
	 * There is in-line style here to properly display (centered) an icon,
	 * if this is not in-line, there has to be a CSS file included on all pages
	 *  -- as notifications can be viewed from all pages --
	 * for an edge case scenario where the user has a "Low stock" notification.
	 *
	 * @param  string	$icon				The icon
	 * @return string						The icon element
	 * @access public
	 */
	protected function get_avatar_icon($icon)
	{
		return '<i class="icon ' . $icon . ' fa-fw pull-left" style="margin-right: 5px; width: 50px; height: 50px; line-height: 50px;" aria-hidden="true"></i>';
	}
}
