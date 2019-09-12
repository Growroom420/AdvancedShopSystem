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
 * phpBB Studio - Advanced Shop System: Directories migration
 */
class install_directories extends \phpbb\db\migration\migration
{
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
	 * Create the shop directories to the filesystem.
	 *
	 * @return array
	 * @access public
	 */
	public function update_data()
	{
		return [
			['custom', [[$this, 'create_shop_directories']]],
		];
	}

	/**
	 * Create the shop directories.
	 *
	 * @throws \Exception
	 * @return void
	 * @access public
	 */
	public function create_shop_directories()
	{
		global $phpbb_container;

		/** @var \phpbb\filesystem\filesystem $filesystem */
		$filesystem = $phpbb_container->get('filesystem');

		$directories = [
			$this->phpbb_root_path . 'files/aps',
			$this->phpbb_root_path . 'images/aps',
		];

		$filesystem->mkdir($directories);
	}
}
