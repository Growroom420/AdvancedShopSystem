<?php
/**
 *
 * phpBB Studio - Advanced Shop System. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbstudio\ass\helper;

use phpbbstudio\ass\entity\category;
use phpbbstudio\ass\entity\item;
use phpbbstudio\ass\exceptions\shop_inactive_exception;
use phpbb\exception\http_exception;

/**
 * phpBB Studio - Advanced Shop System: Controller helper
 */
class controller
{
	/** @var \phpbbstudio\aps\core\functions */
	protected $aps_functions;

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\config\db_text */
	protected $config_text;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbbstudio\ass\operator\category */
	protected $operator_cat;

	/** @var \phpbbstudio\ass\helper\router */
	protected $router;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/**
	 * Constructor.
	 *
	 * @param  \phpbbstudio\aps\core\functions		$aps_functions		APS Functions object
	 * @param  \phpbb\auth\auth						$auth				Auth object
	 * @param  \phpbb\config\config					$config				Config object
	 * @param  \phpbb\config\db_text				$config_text		Config text object
	 * @param  \phpbb\controller\helper				$helper				Controller helper object
	 * @param  \phpbb\language\language				$language			Language object
	 * @param  \phpbbstudio\ass\operator\category	$operator_cat		Category operator object
	 * @param  \phpbbstudio\ass\helper\router		$router				Router helper object
	 * @param  \phpbb\template\template				$template			Template object
	 * @param  \phpbb\user							$user				User object
	 * @return void
	 * @access public
	 */
	public function __construct(
		\phpbbstudio\aps\core\functions $aps_functions,
		\phpbb\auth\auth $auth,
		\phpbb\config\config $config,
		\phpbb\config\db_text $config_text,
		\phpbb\controller\helper $helper,
		\phpbb\language\language $language,
		\phpbbstudio\ass\operator\category $operator_cat,
		router $router,
		\phpbb\template\template $template,
		\phpbb\user $user
	)
	{
		$this->aps_functions	= $aps_functions;
		$this->auth				= $auth;
		$this->config			= $config;
		$this->config_text		= $config_text;
		$this->helper			= $helper;
		$this->language			= $language;
		$this->operator_cat		= $operator_cat;
		$this->router			= $router;
		$this->template			= $template;
		$this->user				= $user;
	}

	/**
	 * Check whether the shop is enabled and active.
	 *
	 * @return void
	 * @throws http_exception
	 * @throws shop_inactive_exception
	 * @access public
	 */
	public function check_shop()
	{
		if (!$this->config['ass_enabled'])
		{
			throw new http_exception(404, 'PAGE_NOT_FOUND');
		}

		$this->language->add_lang('ass_common', 'phpbbstudio/ass');
		$this->language->add_lang('aps_display', 'phpbbstudio/aps');

		if (!$this->config['ass_active'] && !$this->auth->acl_get('u_ass_can_view_inactive_shop'))
		{
			throw new shop_inactive_exception(409, 'ASS_SHOP_INACTIVE');
		}
	}

	/**
	 * Create and set up the shop.
	 *
	 * @param  string				$mode			The shop mode (shop|inventory|history)
	 * @param  category|null		$category		The category entity
	 * @param  item|null			$item			The item entity
	 * @return void
	 * @access public
	 */
	public function create_shop($mode, category $category = null, item $item = null)
	{
		$this->create_shop_crumbs($mode, $category, $item);
		$this->create_shop_navbar($mode, $category);

		$this->template->assign_vars([
			'S_ASS_INVENTORY'	=> $mode === 'inventory',
			'S_ASS_SHOP'		=> $mode === 'shop',
			'S_CAN_GIFT'		=> $this->auth->acl_get('u_ass_can_gift') && $this->config['ass_gift_enabled'],
			'S_CAN_PURCHASE'	=> $this->auth->acl_get('u_ass_can_purchase') && $this->user->data['is_registered'],
			'S_RECEIVE_GIFT'	=> $this->auth->acl_get('u_ass_can_receive_gift'),
		]);
	}

	/**
	 * Create and set up the shop navigation.
	 *
	 * @param  string				$mode			The shop mode (shop|inventory|history)
	 * @param  category|null		$category		The category entity
	 * @return void
	 * @access public
	 */
	public function create_shop_navbar($mode, category $category = null)
	{
		$categories = $this->operator_cat->get_categories(true);

		$title = $mode === 'shop' ? 'ASS_SHOP_INDEX' : 'ASS_INVENTORY';
		$route = $mode === 'shop' ? 'phpbbstudio_ass_shop' : 'phpbbstudio_ass_inventory';

		$this->template->assign_block_vars('ass_shop_categories', [
			'ID'			=> 0,
			'TITLE'			=> $this->language->lang($title),
			'ICON'			=> 'fa-bookmark',
			'S_SELECTED'	=> $category === null,
			'U_VIEW'		=> $this->helper->route($route),
		]);

		/** @var category $cat */
		foreach ($categories as $cat)
		{
			$this->template->assign_block_vars('ass_shop_categories', [
				'ID'			=> $cat->get_id(),
				'TITLE'			=> $cat->get_title(),
				'ICON'			=> $cat->get_icon(),
				'S_SELECTED'	=> $category !== null && $cat->get_id() === $category->get_id(),
				'U_VIEW'		=> $this->router->category($cat->get_slug(), $mode),
			]);
		}
	}

	/**
	 * Create and set up the shop breadcrumbs.
	 *
	 * @param  string				$mode			The shop mode (shop|inventory|history)
	 * @param  category|null		$category		The category entity
	 * @param  item|null			$item			The item entity
	 * @return void
	 * @access public
	 */
	public function create_shop_crumbs($mode, category $category = null, item $item = null)
	{
		$title = $mode === 'shop' ? 'ASS_SHOP_INDEX' : 'ASS_INVENTORY';
		$route = $mode === 'shop' ? 'phpbbstudio_ass_shop' : 'phpbbstudio_ass_inventory';

		$this->template->assign_block_vars_array('navlinks', [
			[
				'FORUM_NAME'	=> $this->aps_functions->get_name(),
				'U_VIEW_FORUM'	=> $this->helper->route('phpbbstudio_aps_display'),
			],
			[
				'FORUM_NAME'	=> $this->language->lang($title),
				'U_VIEW_FORUM'	=> $this->helper->route($route),
			],
		]);

		if ($mode === 'history')
		{
			$this->template->assign_block_vars('navlinks', [
				'FORUM_NAME'	=> $this->language->lang('ASS_HISTORY'),
				'U_VIEW_FORUM'	=> $this->helper->route('phpbbstudio_ass_history'),
			]);
		}

		if ($category instanceof category)
		{
			$this->template->assign_block_vars('navlinks', [
				'FORUM_NAME'	=> $category->get_title(),
				'U_VIEW_FORUM'	=> $this->router->category($category->get_slug(), $mode),
			]);
		}

		if ($item instanceof item)
		{
			$this->template->assign_block_vars('navlinks', [
				'FORUM_NAME'	=> $item->get_title(),
				'U_VIEW_FORUM'	=> $this->router->item($item->get_category_slug(), $item->get_slug()),
			]);
		}
	}

	/**
	 * Set up and assign carousel variables.
	 *
	 * @return void
	 * @access public
	 */
	public function setup_carousel()
	{
		$this->template->assign_vars([
			'SHOP_CAROUSEL_ARROWS'		=> (bool) $this->config['ass_carousel_arrows'],
			'SHOP_CAROUSEL_DOTS'		=> (bool) $this->config['ass_carousel_dots'],
			'SHOP_CAROUSEL_FADE'		=> (bool) $this->config['ass_carousel_fade'],
			'SHOP_CAROUSEL_PLAY'		=> (bool) $this->config['ass_carousel_play'],
			'SHOP_CAROUSEL_PLAY_SPEED'	=> (int) $this->config['ass_carousel_play_speed'],
			'SHOP_CAROUSEL_SPEED'		=> (int) $this->config['ass_carousel_speed'],
		]);
	}

	/**
	 * Set up and assign panel variables.
	 *
	 * @return void
	 * @access public
	 */
	public function setup_panels()
	{
		$panels = ['featured', 'sale', 'featured_sale', 'recent', 'limited', 'random'];
		$options = [
			'icon'			=> '',
			'icon_colour'	=> 'icon-',
			'banner_colour'	=> 'shop-panel-icon-',
			'banner_size'	=> 'shop-panel-icon-',
		];

		foreach ($panels as $panel)
		{
			if ($this->config["ass_panel_{$panel}_icon"])
			{
				$icon = '';

				foreach ($options as $option => $prefix)
				{
					$icon .= " {$prefix}{$this->config["ass_panel_{$panel}_{$option}"]}";
				}

				$this->template->assign_var('SHOP_PANEL_' . utf8_strtoupper($panel) . '_ICON', $icon);
			}
		}
	}
}
