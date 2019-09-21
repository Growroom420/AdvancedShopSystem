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
 * phpBB Studio - Advanced Shop System: Permission roles migration
 */
class update_roles extends \phpbb\db\migration\migration
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
		return ['\phpbbstudio\ass\migrations\update_permissions'];
	}

	/**
	 * Update the Advanced Shop System permissions' roles to the database.
	 *
	 * @return array		Array of permission
	 * @access public
	 */
	public function update_data()
	{
		$data = [];

		if ($this->role_exists('ROLE_ADMIN_STANDARD'))
		{
			$data[] = ['permission.permission_set', ['ROLE_ADMIN_STANDARD', 'a_ass_inventory']];
		}

		if ($this->role_exists('ROLE_ADMIN_FULL'))
		{
			$data[] = ['permission.permission_set', ['ROLE_ADMIN_FULL', 'a_ass_inventory']];
		}

		return $data;
	}

	/**
	 * Checks whether the given role does exist or not.
	 *
	 * @param  string	$role	The name of the role
	 * @return bool				True if the role exists, false otherwise
	 */
	private function role_exists($role)
	{
		$sql = 'SELECT role_id
				FROM ' . ACL_ROLES_TABLE . '
				WHERE role_name = "' . $this->db->sql_escape($role) . '"';
		$result = $this->db->sql_query_limit($sql, 1);
		$role_id = $this->db->sql_fetchfield('role_id');
		$this->db->sql_freeresult($result);

		return (bool) $role_id;
	}
}
