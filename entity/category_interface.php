<?php
/**
 *
 * phpBB Studio - Advanced Shop System. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbstudio\ass\entity;

use phpbb\exception\runtime_exception;

/**
 * phpBB Studio - Advanced Shop System: Category entity interface
 */
interface category_interface
{
	/**
	 * Load the category data.
	 *
	 * @param  int			$id			The category identifier
	 * @param  string		$slug		The category slug
	 * @return category		$this		This object for chaining calls
	 * @throws runtime_exception
	 * @access public
	 */
	public function load($id, $slug = '');

	/**
	 * Import the category data.
	 *
	 * @param  array		$data		The category data
	 * @return category		$this		This object for chaining calls
	 * @access public
	 */
	public function import(array $data);

	/**
	 * Save the category data.
	 *
	 * @return category		$this		This object for chaining calls
	 * @access public
	 */
	public function save();

	/**
	 * Insert the category data.
	 *
	 * @return category		$this		This object for chaining calls
	 * @access public
	 */
	public function insert();

	/**
	 * Get the category identifier.
	 *
	 * @return int						The category identifier
	 * @access public
	 */
	public function get_id();

	/**
	 * Get the category title.
	 *
	 * @return string					The category title
	 * @access public
	 */
	public function get_title();

	/**
	 * Set the category title.
	 *
	 * @param  string		$title		The category title
	 * @return category		$this		This object for chaining calls
	 * @throws runtime_exception
	 * @access public
	 */
	public function set_title($title);

	/**
	 * Get the category slug.
	 *
	 * @return string					The category slug
	 * @access public
	 */
	public function get_slug();

	/**
	 * Set the category slug
	 *
	 * @param  string		$slug		The category slug
	 * @return category		$this		This object for chaining calls
	 * @throws runtime_exception
	 * @access public
	 */
	public function set_slug($slug);

	/**
	 * Get the category icon.
	 *
	 * @return string					The category icon
	 * @access public
	 */
	public function get_icon();

	/**
	 * Set the category icon.
	 *
	 * @param  string		$icon		The category icon.
	 * @return category		$this		This object for chaining calls
	 * @throws runtime_exception
	 * @access public
	 */
	public function set_icon($icon);

	/**
	 * Get the category description for display (HTML).
	 *
	 * @return string					The category description
	 * @access public
	 */
	public function get_desc_for_display();

	/**
	 * Get the category description for edit (BBCode).
	 *
	 * @return string					The category description
	 * @access public
	 */
	public function get_desc();

	/**
	 * Set the category description for storage (XML).
	 *
	 * @param  string		$desc		The category description
	 * @return category		$this		This object for chaining calls
	 * @access public
	 */
	public function set_desc($desc);

	/**
	 * Get the category active status.
	 *
	 * @return bool						The category active status
	 * @access public
	 */
	public function get_active();

	/**
	 * Set the category active status.
	 *
	 * @param  bool			$active		The category active status
	 * @return category		$this		This object for chaining calls
	 * @access public
	 */
	public function set_active($active);

	/**
	 * Get the category order.
	 *
	 * @return int						The category order
	 * @access public
	 */
	public function get_order();

	/**
	 * Set the category order.
	 *
	 * @param  int			$order		The category order
	 * @return category		$this		This object for chaining calls
	 * @access public
	 */
	public function set_order($order);

	/**
	 * Get the amount of items with conflicts.
	 *
	 * @return int						The amount of items with conflicts
	 * @access public
	 */
	public function get_conflicts();

	/**
	 * Set the amount of items with conflicts.
	 *
	 * @param  int			$conflicts	The amount of items with conflicts
	 * @return category		$this		This object for chaining calls
	 * @access public
	 */
	public function set_conflicts($conflicts);
}
