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
 * phpBB Studio - Advanced Shop System: Item entity interface
 */
interface item_interface
{
	/**
	 * Load the item data.
	 *
	 * @param  int			$id				The item identifier
	 * @param  string		$slug			The item slug
	 * @param  int			$category_id	The category identifier
	 * @return item			$this			This object for chaining calls
	 * @throws runtime_exception
	 * @access public
	 */
	public function load($id, $slug = '', $category_id = 0);

	/**
	 * Import the item data.
	 *
	 * @param  array		$data			The item data
	 * @return item			$this			This object for chaining calls
	 * @access public
	 */
	public function import(array $data);

	/**
	 * Save the item data.
	 *
	 * @return item			$this			This object for chaining calls
	 * @throws runtime_exception
	 * @access public
	 */
	public function save();

	/**
	 * Insert the item data.
	 *
	 * @return item			$this			This object for chaining calls
	 * @throws runtime_exception
	 * @access public
	 */
	public function insert();

	/**
	 * Get the item identifier.
	 *
	 * @return int							The item identifier
	 * @access public
	 */
	public function get_id();

	/**
	 * Get the category identifier.
	 *
	 * @return int							The category identifier
	 * @access public
	 */
	public function get_category();

	/**
	 * Set the category identifier.
	 *
	 * @param  int			$category_id	The category identifier
	 * @return item			$this			This object for chaining calls
	 * @access public
	 */
	public function set_category($category_id);

	/**
	 * Get the category slug.
	 *
	 * @return string						The category slug
	 * @access public
	 */
	public function get_category_slug();

	/**
	 * Set the category slug.
	 *
	 * @param  string		$slug			The category slug
	 * @return item			$this			This object for chaining calls
	 * @access public
	 */
	public function set_category_slug($slug);

	/**
	 * Set the item title.
	 *
	 * @return string						The item title
	 * @access public
	 */
	public function get_title();

	/**
	 * Set the item title.
	 *
	 * @param  string		$title			The item title
	 * @return item			$this			This object for chaining calls
	 * @throws runtime_exception
	 * @access public
	 */
	public function set_title($title);

	/**
	 * Get the item slug.
	 *
	 * @return string						The item slug
	 * @access public
	 */
	public function get_slug();

	/**
	 * Set the item slug.
	 *
	 * @param  string		$slug			The item slug
	 * @return item			$this			This object for chaining calls
	 * @throws runtime_exception
	 * @access public
	 */
	public function set_slug($slug);

	/**
	 * Get the item icon.
	 *
	 * @return string						The item icon
	 * @access public
	 */
	public function get_icon();

	/**
	 * Set the item icon.
	 *
	 * @param  string		$icon			The item icon
	 * @return item			$this			This object for chaining calls
	 * @access public
	 */
	public function set_icon($icon);

	/**
	 * Get the item description for display (HTML).
	 *
	 * @return string						The item description
	 * @access public
	 */
	public function get_desc_for_display();

	/**
	 * Get the item description for edit (BBCode).
	 *
	 * @return string						The item description
	 * @access public
	 */
	public function get_desc();

	/**
	 * Set the item description for storage (XML).
	 *
	 * @param  string		$desc			The item description
	 * @return item			$this			This object for chaining calls
	 * @access public
	 */
	public function set_desc($desc);

	/**
	 * Get the item active status
	 *
	 * @return bool							The item active status
	 * @access public
	 */
	public function get_active();

	/**
	 * Set the item active status
	 *
	 * @param  bool			$active			The item active status
	 * @return item			$this			This object for chaining calls
	 * @access public
	 */
	public function set_active($active);

	/**
	 * Get the item order.
	 *
	 * @return int							The item order
	 * @access public
	 */
	public function get_order();

	/**
	 * Set the item order.
	 *
	 * @param  int			$order			The item order
	 * @return item			$this			This object for chaining calls
	 * @access public
	 */
	public function set_order($order);

	/**
	 * Get the item type.
	 *
	 * @return string						The item type
	 * @access public
	 */
	public function get_type();

	/**
	 * Set the item type.
	 *
	 * @param  string		$type			The item type
	 * @return item			$this			This object for chaining calls
	 * @throws runtime_exception
	 * @access public
	 */
	public function set_type($type);

	/**
	 * Get the item type data.
	 *
	 * @return array						The item type data
	 * @access public
	 */
	public function get_data();

	/**
	 * Set the item type data.
	 *
	 * @param  array		$data			The item type data
	 * @return item			$this			This object for chaining calls
	 * @access public
	 */
	public function set_data(array $data);

	/**
	 * Get the item price.
	 *
	 * @return double						The item price
	 * @access public
	 */
	public function get_price();

	/**
	 * Set the item price.
	 *
	 * @param  double		$price			The item price
	 * @return item			$this			This object for chaining calls
	 * @throws runtime_exception
	 * @access public
	 */
	public function set_price($price);

	/**
	 * Get the item count.
	 *
	 * @return int							The item count
	 * @access public
	 */
	public function get_count();

	/**
	 * Set the item count.
	 *
	 * @param  int			$count			The item count
	 * @return item			$this			This object for chaining calls
	 * @throws runtime_exception
	 * @access public
	 */
	public function set_count($count);

	/**
	 * Get the item purchases.
	 *
	 * @return int							The item purchases
	 * @access public
	 */
	public function get_purchases();

	/**
	 * Set the item purchases.
	 *
	 * @param  int			$purchases		The item purchases
	 * @return item			$this			This object for chaining calls
	 * @throws runtime_exception
	 * @access public
	 */
	public function set_purchases($purchases);

	/**
	 * Get the item stock.
	 *
	 * @return int							The item stock
	 * @access public
	 */
	public function get_stock();

	/**
	 * Set the item stock.
	 *
	 * @param  int			$stock			The item stock
	 * @return item			$this			This object for chaining calls
	 * @throws runtime_exception
	 * @access public
	 */
	public function set_stock($stock);

	/**
	 * Get the item stock notification threshold.
	 *
	 * @return int							The item stock notification threshold
	 * @access public
	 */
	public function get_stock_threshold();

	/**
	 * Set the item stock notification threshold.
	 *
	 * @param  int			$threshold		The item stock notification threshold
	 * @return item			$this			This object for chaining calls
	 * @throws runtime_exception
	 * @access public
	 */
	public function set_stock_threshold($threshold);

	/**
	 * Get the item unlimited stock status.
	 *
	 * @return bool							The item unlimited stock status
	 * @access public
	 */
	public function get_stock_unlimited();

	/**
	 * Set the item unlimited stock status.
	 *
	 * @param  int			$unlimited		The item unlimited stock status
	 * @return item			$this			This object for chaining calls
	 * @access public
	 */
	public function set_stock_unlimited($unlimited);

	/**
	 * Get the item expire string.
	 *
	 * @return string						The item expire string
	 * @access public
	 */
	public function get_expire_string();

	/**
	 * Set the item expire string.
	 *
	 * @param  string		$string			The item expire string
	 * @return item			$this			This object for chaining calls
	 * @throws runtime_exception
	 * @access public
	 */
	public function set_expire_string($string);

	/**
	 * Get the item expire seconds.
	 *
	 * @return int							The item expire seconds
	 * @access public
	 */
	public function get_expire_seconds();

	/**
	 * Set the item expire seconds.
	 *
	 * @param  int			$seconds		The item expire seconds
	 * @return $this
	 * @access public
	 */
	public function set_expire_seconds($seconds);

	/**
	 * Get the item delete string.
	 *
	 * @return string						The item delete string
	 * @access public
	 */
	public function get_delete_string();

	/**
	 * Set the item delete string.
	 *
	 * @param  string		$string			The item delete string
	 * @return item			$this			This object for chaining calls
	 * @throws runtime_exception
	 * @access public
	 */
	public function set_delete_string($string);

	/**
	 * Get the item delete seconds.
	 *
	 * @return int							The item delete seconds
	 * @access public
	 */
	public function get_delete_seconds();

	/**
	 * Set the item delete seconds.
	 *
	 * @param  int			$seconds		The item delete seconds
	 * @return $this
	 * @access public
	 */
	public function set_delete_seconds($seconds);

	/**
	 * Get the item refund string.
	 *
	 * @return string						The item refund string
	 * @access public
	 */
	public function get_refund_string();

	/**
	 * Set the item refund string.
	 *
	 * @param  string		$string			The item refund string
	 * @return item			$this			This object for chaining calls
	 * @throws runtime_exception
	 * @access public
	 */
	public function set_refund_string($string);

	/**
	 * Get the item refund seconds.
	 *
	 * @return int							The item refund seconds
	 * @access public
	 */
	public function get_refund_seconds();

	/**
	 * Set the item refund seconds.
	 *
	 * @param  int			$seconds		The item refund seconds
	 * @return $this
	 * @access public
	 */
	public function set_refund_seconds($seconds);

	/**
	 * Get the item gift status.
	 *
	 * @return bool							The item gift status
	 * @access public
	 */
	public function get_gift();

	/**
	 * Set the item gift status.
	 *
	 * @param  bool			$gift			The item gift status
	 * @return item			$this			This object for chaining calls
	 * @access public
	 */
	public function set_gift($gift);

	/**
	 * Get the item gift only status.
	 *
	 * @return bool							The item gift only status
	 * @access public
	 */
	public function get_gift_only();

	/**
	 * Set the item gift only status.
	 *
	 * @param  bool			$gift_only		The item gift only status
	 * @return item			$this			This object for chaining calls
	 * @access public
	 */
	public function set_gift_only($gift_only);

	/**
	 * Get the item gift type.
	 *
	 * @return bool							The item gift type
	 * @access public
	 */
	public function get_gift_type();

	/**
	 * Set the item gift type.
	 *
	 * @param  bool			$type			The item gift type
	 * @return item			$this			This object for chaining calls
	 * @access public
	 */
	public function set_gift_type($type);

	/**
	 * Get the item gift price percentage.
	 *
	 * @return int							The item gift price percentage
	 * @access public
	 */
	public function get_gift_percentage();

	/**
	 * Set the item gift price percentage.
	 *
	 * @param  int			$percentage		The item gift price percentage
	 * @return item			$this			This object for chaining calls
	 * @throws runtime_exception
	 * @access public
	 */
	public function set_gift_percentage($percentage);

	/**
	 * Get the item gift price.
	 *
	 * @return double						The item gift price
	 * @access public
	 */
	public function get_gift_price();

	/**
	 * Set the item gift price.
	 *
	 * @param  double		$price			The item gift price
	 * @return item			$this			This object for chaining calls
	 * @throws runtime_exception
	 * @access public
	 */
	public function set_gift_price($price);

	/**
	 * Get the item sale price.
	 *
	 * @return double						The item sale price
	 * @access public
	 */
	public function get_sale_price();

	/**
	 * Set the item sale price.
	 *
	 * @param  double		$price			The item sale price
	 * @return item			$this			This object for chaining calls
	 * @throws runtime_exception
	 * @access public
	 */
	public function set_sale_price($price);

	/**
	 * Set the item sale start timestamp.
	 *
	 * @return int							The item sale start timestamp
	 * @access public
	 */
	public function get_sale_start();

	/**
	 * Set the item sale start timestamp.
	 *
	 * @param  int			$time			The item sale start timestamp
	 * @return item			$this			This object for chaining calls
	 * @access public
	 */
	public function set_sale_start($time);

	/**
	 * Get the item sale until timestamp.
	 *
	 * @return int							The item sale until timestamp
	 * @access public
	 */
	public function get_sale_until();

	/**
	 * Set the item sale until timestamp.
	 *
	 * @param  int			$time			The item sale until timestamp
	 * @return item			$this			This object for chaining calls
	 * @access public
	 */
	public function set_sale_until($time);

	/**
	 * Get the item featured start timestamp.
	 *
	 * @return int							The item featured start timestamp
	 * @access public
	 */
	public function get_featured_start();

	/**
	 * Set the item featured start timestamp.
	 *
	 * @param  int			$time			The item featured start timestamp
	 * @return item			$this			This object for chaining calls
	 * @access public
	 */
	public function set_featured_start($time);

	/**
	 * Get the item featured until timestamp.
	 *
	 * @return int							The item featured until timestamp
	 * @access public
	 */
	public function get_featured_until();

	/**
	 * Set the item featured until timestamp.
	 *
	 * @param  int			$time			The item featured until timestamp
	 * @return item			$this			This object for chaining calls
	 * @access public
	 */
	public function set_featured_until($time);

	/**
	 * Get the item available start timestamp.
	 *
	 * @return int							The item available start timestamp
	 * @access public
	 */
	public function get_available_start();

	/**
	 * Set the item available start timestamp.
	 *
	 * @param  int			$time			The item available start timestamp
	 * @return item			$this			This object for chaining calls
	 * @access public
	 */
	public function set_available_start($time);

	/**
	 * Get the item available until timestamp.
	 *
	 * @return int							The item available until timestamp
	 * @access public
	 */
	public function get_available_until();

	/**
	 * Set the item available until timestamp.
	 *
	 * @param  int			$time			The item available until timestamp
	 * @return item			$this			This object for chaining calls
	 * @access public
	 */
	public function set_available_until($time);

	/**
	 * Get the item background image.
	 *
	 * @return string						The item background image
	 * @access public
	 */
	public function get_background();

	/**
	 * Set the item background image.
	 *
	 * @param  string		$background		The item background image
	 * @return item			$this			This object for chaining calls
	 * @access public
	 */
	public function set_background($background);

	/**
	 * Get the item images.
	 *
	 * @return array						The item images.
	 * @access public
	 */
	public function get_images();

	/**
	 * Set the item images.
	 *
	 * @param  array		$images			The item images
	 * @return item			$this			This object for chaining calls
	 * @access public
	 */
	public function set_images(array $images);

	/**
	 * Get the item related items status.
	 *
	 * @return bool							The item related item status
	 * @access public
	 */
	public function get_related_enabled();

	/**
	 * Set the item related items status.
	 *
	 * @param  bool			$related		The item related item status
	 * @return item			$this			This object for chaining calls
	 * @throws runtime_exception
	 * @access public
	 */
	public function set_related_enabled($related);

	/**
	 * Get the item related items.
	 *
	 * @return array						The item related items.
	 * @access public
	 */
	public function get_related_items();

	/**
	 * Set the item related items.
	 *
	 * @param  array		$items			The item related items
	 * @return item			$this			This object for chaining calls
	 * @access public
	 */
	public function set_related_items(array $items);

	/**
	 * Get the item creation timestamp.
	 *
	 * @return int							The item creation timestamp
	 * @access public
	 */
	public function get_create_time();

	/**
	 * Get the item last edited timestamp.
	 *
	 * @return int							The item last edited timestamp
	 * @access public
	 */
	public function get_edit_time();

	/**
	 * Get the item conflict state.
	 *
	 * @return bool							The item conflict state
	 * @access public
	 */
	public function get_conflict();

	/**
	 * Set the item conflict state.
	 *
	 * @param  bool			$state			The item conflict state
	 * @return item			$this			This object for chaining calls
	 * @access public
	 */
	public function set_conflict($state);
}
