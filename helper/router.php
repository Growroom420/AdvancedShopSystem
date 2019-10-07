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

/**
 * phpBB Studio - Advanced Shop System: Router helper
 */
class router
{
	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var string php File extension */
	protected $php_ext;

	/**
	 * Constructor.
	 *
	 * @param  \phpbb\controller\helper		$helper			Controller helper object
	 * @param  string						$root_path		phpBB root path
	 * @param  string						$php_ext		php File extension
	 * @return void
	 * @access public
	 */
	public function __construct(\phpbb\controller\helper $helper, $root_path, $php_ext)
	{
		$this->helper		= $helper;

		$this->root_path	= $root_path;
		$this->php_ext		= $php_ext;
	}

	/**
	 * Get the URL for a category.
	 *
	 * @param  string	$category_slug		The category slug
	 * @param  string	$mode				The mode
	 * @return string						The URL
	 * @access public
	 */
	public function category($category_slug, $mode = 'category')
	{
		$mode = $mode === 'inventory' ? $mode : 'category';

		return $this->helper->route("phpbbstudio_ass_{$mode}", ['category_slug' => $category_slug]);
	}

	/**
	 * Get the URL for the inventory.
	 *
	 * @param  string	$category_slug		The category slug
	 * @param  string	$item_slug			The item slug
	 * @param  int		$index				The item index
	 * @param  string	$action				The action
	 * @param  array	$params				Additional parameters
	 * @return string						The URL
	 * @access public
	 */
	public function inventory($category_slug = '', $item_slug = '', $index = 1, $action = '', array $params = [])
	{
		$params = array_merge(['category_slug' => $category_slug, 'item_slug' => $item_slug, 'index' => $index, 'action' => $action], $params);

		return $this->helper->route('phpbbstudio_ass_inventory', $params);
	}

	/**
	 * Get the URL for the gift action.
	 *
	 * @param  string	$category_slug		The category slug
	 * @param  string	$item_slug			The item slug
	 * @return string						The URL
	 * @access public
	 */
	public function gift($category_slug, $item_slug)
	{
		return $this->helper->route('phpbbstudio_ass_gift', ['category_slug' => $category_slug, 'item_slug' => $item_slug]);
	}

	/**
	 * Get the URL for an item.
	 *
	 * @param  string	$category_slug		The category slug
	 * @param  string	$item_slug			The item slug
	 * @return string						The URL
	 * @access public
	 */
	public function item($category_slug, $item_slug)
	{
		return $this->helper->route('phpbbstudio_ass_item', ['category_slug' => $category_slug, 'item_slug' => $item_slug]);
	}

	/**
	 * Get the URL for the purchase action.
	 *
	 * @param  string	$category_slug		The category slug
	 * @param  string	$item_slug			The item slug
	 * @return string						The URL
	 * @access public
	 */
	public function purchase($category_slug, $item_slug)
	{
		return $this->helper->route('phpbbstudio_ass_purchase', ['category_slug' => $category_slug, 'item_slug' => $item_slug]);
	}

	/**
	 * Get the URL for a 'regular' phpBB page (viewtopic, viewforum, etc..).
	 *
	 * @param  string		$page			The phpBB page
	 * @param  string|array	$params			The parameters
	 * @param  bool			$is_amp			Whether it is & or &amp;
	 * @param  bool			$ajax			Whether the request is AJAX or not
	 * @return string						The URL
	 * @access public
	 */
	public function regular($page, $params = '', $is_amp = true, $ajax = false)
	{
		if ($ajax)
		{
			return append_sid(generate_board_url() . "/{$page}.{$this->php_ext}", $params, $is_amp, false, false);
		}
		else
		{
			return append_sid("{$this->root_path}{$page}.{$this->php_ext}", $params, $is_amp, false, false);
		}
	}
}
