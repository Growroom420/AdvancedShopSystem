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
 * phpBB Studio - Advanced Shop System: Gift column migration
 */
class update_tables extends \phpbb\db\migration\migration
{
	/**
	 * Checks whether the Advanced Shop System DB column does exist or not.
	 *
	 * @return bool			True if this migration is installed, false otherwise.
	 * @access public
	 */
	public function effectively_installed()
	{
		return $this->db_tools->sql_column_exists($this->table_prefix . 'ass_items', 'item_gift_only');
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
		return ['\phpbbstudio\ass\migrations\install_tables'];
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
			'add_columns'		=> [
				$this->table_prefix . 'ass_items'	=> [
					'item_available_until'		=> ['TIMESTAMP', 0, 'after' => 'item_featured_until'],
					'item_available_start'		=> ['TIMESTAMP', 0, 'after' => 'item_featured_until'],
					'item_gift_only'			=> ['BOOL', 0, 'after' => 'item_gift'],
					'item_related_items'		=> ['VCHAR:255', '', 'after' => 'item_images'],
					'item_related_enabled'		=> ['BOOL', 1, 'after' => 'item_images'],
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
			'drop_columns'		=> [
				$this->table_prefix . 'ass_items'	=> [
					'item_available_start',
					'item_available_until',
					'item_gift_only',
					'item_related_enabled',
					'item_related_items',
				],
			],
		];
	}
}
