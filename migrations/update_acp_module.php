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
 * phpBB Studio - Advanced Shop System: ACP Module update migration
 */
class update_acp_module extends \phpbb\db\migration\migration
{
	/**
	 * Checks whether the Advanced Shop System ACP module does exist or not.
	 *
	 * @return bool			True if the module exists, false otherwise
	 * @access public
	 */
	public function effectively_installed()
	{
		$sql = 'SELECT module_id
				FROM ' . $this->table_prefix . "modules
				WHERE module_class = 'acp'
					AND module_langname = 'ACP_ASS_INVENTORY'";
		$result = $this->db->sql_query($sql);
		$module_id = (bool) $this->db->sql_fetchfield('module_id');
		$this->db->sql_freeresult($result);

		return $module_id !== false;
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
	 * Update the Advanced Shop System ACP module to the database.
	 *
	 * @return array		Array of module data
	 * @access public
	 */
	public function update_data()
	{
		return [
			['module.add', [
				'acp',
				'ACP_ASS_SYSTEM',
				[
					'module_basename'	=> '\phpbbstudio\ass\acp\main_module',
					'modes'				=> ['inventory'],
				],
			]],
		];
	}
}
