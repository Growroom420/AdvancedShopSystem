<?php
/**
 *
 * phpBB Studio - Advanced Shop System. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbstudio\ass\items\type;

use phpbbstudio\ass\entity\category;
use phpbbstudio\ass\entity\item;
use phpbbstudio\ass\exceptions\shop_exception;
use phpbbstudio\ass\exceptions\shop_item_exception;

/**
 * phpBB Studio - Advanced Shop System: Item type interface
 */
interface item_type
{
	/**
	 * Set the category entity.
	 *
	 * @param  category		$category		Category entity
	 * @return void
	 * @access public
	 */
	public function set_category(category $category);

	/**
	 * Set the item entity.
	 *
	 * @param  item			$item			Item entity
	 * @return void
	 * @access public
	 */
	public function set_item(item $item);

	/**
	 * Whether or not the administrator is authorised to handle this item type.
	 *
	 * @return bool
	 * @access public
	 */
	public function is_admin_authed();

	/**
	 * Get the language key for a specific mode.
	 *
	 * @param  string		$mode			The mode
	 * @return string						The language key
	 * @access public
	 */
	public function get_language($mode = '');

	/**
	 * Get the base language key for this item type.
	 *
	 * @return string						The language key
	 * @access public
	 * @abstract
	 */
	public function get_language_key();

	/**
	 * Activate this item.
	 *
	 * @param  array		$data			The item type data
	 * @return mixed						Something that can be casted to (bool) to indicate success
	 * @throws shop_exception				When there is an error with the user's request
	 * @throws shop_item_exception			When there is an error with the item's data
	 * @access public
	 * @abstract
	 */
	public function activate(array $data);

	/**
	 * Get the ACP template and assign any variables for this item type.
	 *
	 * @param  array		$data			The item type data
	 * @return string						The path to the ACP template: "@vendor_extension/the_file.html"
	 * @access public
	 * @abstract
	 */
	public function get_acp_template(array $data);

	/**
	 * Validate the ACP data.
	 *
	 * @param  array		$data			The item type data
	 * @return array						An array with errors
	 * @access public
	 */
	public function validate_acp_data(array $data);

	/**
	 * Get the AJAX callback for the activate confirm action.
	 *
	 * @return string						The name of the AJAX callback
	 * @access public
	 * @static
	 */
	static public function get_confirm_ajax();

	/**
	 * Get the confirm template and assign any variables for this item type.
	 *
	 * @param  array		$data			The item type data
	 * @return string						The path to the template: "@vendor_extension/the_file.html"
	 * @access public
	 */
	public function get_confirm_template(array $data);
}
