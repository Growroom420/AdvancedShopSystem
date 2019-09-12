<?php
/**
 *
 * phpBB Studio - Advanced Shop System. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbstudio\ass\migrations;

/**
 * phpBB Studio - Advanced Shop System: Tables migration
 */
class install_tables extends \phpbb\db\migration\migration
{
	/**
	 * Checks whether the Advanced Shop System DB table does exist or not.
	 *
	 * @return bool			True if this migration is installed, false otherwise.
	 * @access public
	 */
	public function effectively_installed()
	{
		return $this->db_tools->sql_table_exists($this->table_prefix . 'ass_categories');
	}

	/**
	 * Assign migration file dependencies for this migration.
	 *
	 * @return array		Array of migration files
	 * @access public
	 * @static
	 */
	static public function depends_on()
	{
		return ['\phpbbstudio\ass\migrations\install_acp_module'];
	}

	/**
	 * Add the Advanced Shop System tables and columns to the database.
	 *
	 * @return array		Array of tables and columns data
	 * @access public
	 */
	public function update_schema()
	{
		return [
			'add_tables'		=> [
				$this->table_prefix . 'ass_categories'	=> [
					'COLUMNS'		=> [
						'category_id'			=> ['ULINT', null, 'auto_increment'],
						'category_order'		=> ['ULINT', 0],
						'category_active'		=> ['BOOL', 1],
						'category_title'		=> ['VCHAR_UNI', ''],
						'category_slug'			=> ['VCHAR_UNI', ''],
						'category_icon'			=> ['VCHAR_UNI', ''],
						'category_desc'			=> ['MTEXT_UNI', ''],
						'item_conflicts'		=> ['ULINT', 0],
					],
					'PRIMARY_KEY'	=> 'category_id',
				],

				$this->table_prefix . 'ass_items'		=> [
					'COLUMNS'		=> [
						'item_id'				=> ['ULINT', null, 'auto_increment'],
						'item_order'			=> ['INT:11', 0],
						'item_active'			=> ['BOOL', 1],
						'item_title'			=> ['VCHAR_UNI', ''],
						'item_slug'				=> ['VCHAR_UNI', ''],
						'item_icon'				=> ['VCHAR_UNI', ''],
						'item_desc'				=> ['MTEXT_UNI', ''],
						'item_type'				=> ['VCHAR_UNI', ''],
						'item_data'				=> ['MTEXT_UNI', ''],
						'item_price'			=> ['DECIMAL:14', 0.00],
						'item_count'			=> ['ULINT', 0],
						'item_purchases'		=> ['ULINT', 0],
						'item_stock'			=> ['ULINT', 0],
						'item_stock_threshold'	=> ['ULINT', 0],
						'item_stock_unlimited'	=> ['BOOL', 1],
						'item_expire_string'	=> ['VCHAR_UNI', ''],
						'item_expire_seconds'	=> ['INT:11', 0],
						'item_delete_string'	=> ['VCHAR_UNI', ''],
						'item_delete_seconds'	=> ['INT:11', 0],
						'item_refund_string'	=> ['VCHAR_UNI', ''],
						'item_refund_seconds'	=> ['INT:11', 0],
						'item_gift'				=> ['BOOL', 0],
						'item_gift_type'		=> ['BOOL', 0],
						'item_gift_percentage'	=> ['INT:3', 0],
						'item_gift_price'		=> ['DECIMAL:14', 0.00],
						'item_sale_price'		=> ['DECIMAL:14', 0.00],
						'item_sale_start'		=> ['TIMESTAMP', 0],
						'item_sale_until'		=> ['TIMESTAMP', 0],
						'item_featured_start'	=> ['TIMESTAMP', 0],
						'item_featured_until'	=> ['TIMESTAMP', 0],
						'item_create_time'		=> ['TIMESTAMP', 0],
						'item_edit_time'		=> ['TIMESTAMP', 0],
						'item_background'		=> ['VCHAR_UNI', ''],
						'item_images'			=> ['MTEXT_UNI', ''],
						'item_conflict'			=> ['BOOL', 0],
						'category_id'			=> ['ULINT', 0],
					],
					'PRIMARY_KEY'	=> 'item_id',
					'KEYS'			=> [
						'category_id'	=> ['INDEX', 'category_id'],
					],
				],

				$this->table_prefix . 'ass_inventory'	=> [
					'COLUMNS'		=> [
						'inventory_id'		=> ['ULINT', null, 'auto_increment'],
						'category_id'		=> ['ULINT', 0],
						'item_id'			=> ['ULINT', 0],
						'user_id'			=> ['ULINT', 0],
						'gifter_id'			=> ['ULINT', 0],
						'use_count'			=> ['ULINT', 0],
						'use_time'			=> ['TIMESTAMP', 0],
						'purchase_time'		=> ['TIMESTAMP', 0],
						'purchase_price'	=> ['DECIMAL:14', 0.00],
					],
					'PRIMARY_KEY'	=> 'inventory_id',
					'KEYS'			=> [
						'category_id'	=> ['INDEX', 'category_id'],
						'item_id'		=> ['INDEX', 'item_id'],
						'user_id'		=> ['INDEX', 'user_id'],
						'gifter_id'		=> ['INDEX', ['gifter_id']],
					],
				],

				$this->table_prefix . 'ass_logs'		=> [
					'COLUMNS'		=> [
						'log_id'			=> ['ULINT', null, 'auto_increment'],
						'log_ip'			=> ['VCHAR:40', ''],
						'log_time'			=> ['TIMESTAMP', 0],
						'points_old'		=> ['DECIMAL:14', 0.00],
						'points_sum'		=> ['DECIMAL:14', 0.00],
						'points_new'		=> ['DECIMAL:14', 0.00],
						'item_purchase'		=> ['BOOL', 0],
						'item_id'			=> ['ULINT', 0],
						'category_id'		=> ['ULINT', 0],
						'user_id'			=> ['ULINT', 0],
						'recipient_id'		=> ['ULINT', 0],
					],
					'PRIMARY_KEY'	=> 'log_id',
					'KEYS'			=> [
						'item_id'		=> ['INDEX', 'item_id'],
						'category_id'	=> ['INDEX', 'category_id'],
						'user_id'		=> ['INDEX', 'user_id'],
						'recipient_id'	=> ['INDEX', 'recipient_id'],
					],
				],
			],
		];
	}

	/**
	 * Reverts the database schema by providing a set of change instructions
	 *
	 * @return array    Array of schema changes
	 * 					(compatible with db_tools->perform_schema_changes())
	 * @access public
	 */
	public function revert_schema()
	{
		return [
			'drop_tables'		=> [
				$this->table_prefix . 'ass_categories',
				$this->table_prefix . 'ass_inventory',
				$this->table_prefix . 'ass_items',
				$this->table_prefix . 'ass_logs',
			],
		];
	}
}
