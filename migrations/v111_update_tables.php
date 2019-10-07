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
 * phpBB Studio - Advanced Shop System: v1.1.1 Stack column migration
 */
class v111_update_tables extends \phpbb\db\migration\migration
{
	/**
	 * Checks whether the Advanced Shop System DB column does exist or not.
	 *
	 * @return bool			True if this migration is installed, false otherwise.
	 * @access public
	 */
	public function effectively_installed()
	{
		return $this->db_tools->sql_column_exists($this->table_prefix . 'ass_items', 'item_stack');
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
					'item_stack'			=> ['ULINT', 1, 'after' => 'item_count'],
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
					'item_stack',
				],
			],
		];
	}
}
