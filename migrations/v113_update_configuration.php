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
 * phpBB Studio - Advanced Shop System: v1.1.3 Update configuration migration
 */
class v113_update_configuration extends \phpbb\db\migration\container_aware_migration
{
	/**
	 * Checks whether the Advanced Shop System configuration does exist or not.
	 *
	 * @return bool			True if this migration is installed, false otherwise.
	 * @access public
	 */
	public function effectively_installed()
	{
		return $this->config->offsetExists('ass_link_locations');
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
			['config.add', ['ass_link_locations', 0]],
		];
	}
}
