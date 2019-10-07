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
 * phpBB Studio - Advanced Shop System: v1.1.1 Update configuration migration
 */
class v111_update_configuration extends \phpbb\db\migration\container_aware_migration
{
	/**
	 * Checks whether the Advanced Shop System configuration does exist or not.
	 *
	 * @return bool			True if this migration is installed, false otherwise.
	 * @access public
	 */
	public function effectively_installed()
	{
		return isset($this->config['ass_panel_sale_width']);
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
		return ['\phpbbstudio\ass\migrations\install_configuration'];
	}

	/**
	 * Add the Advanced Shop System configuration to the database.
	 *
	 * @return array		Array of configuration
	 * @access public
	 */
	public function update_data()
	{
		return [
			['config.add', ['ass_panel_featured_order', 1]],
			['config.add', ['ass_panel_featured_width', 6]],

			['config.add', ['ass_panel_sale_order', 2]],
			['config.add', ['ass_panel_sale_width', 6]],

			['config.add', ['ass_panel_featured_sale_order', 3]],
			['config.add', ['ass_panel_featured_sale_width', 6]],

			['config.add', ['ass_panel_recent_order', 4]],
			['config.add', ['ass_panel_recent_width', 6]],

			['config.add', ['ass_panel_limited_order', 5]],
			['config.add', ['ass_panel_limited_width', 6]],

			['config.add', ['ass_panel_random_order', 6]],
			['config.add', ['ass_panel_random_width', 3]],
		];
	}
}
