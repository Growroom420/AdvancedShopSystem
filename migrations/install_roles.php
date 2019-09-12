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
class install_roles extends \phpbb\db\migration\migration
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
		return ['\phpbbstudio\ass\migrations\install_permissions'];
	}

	/**
	 * Add the Advanced Shop System permissions' roles to the database.
	 *
	 * @return array		Array of permission
	 * @access public
	 */
	public function update_data()
	{
		$data = [];

		if ($this->role_exists('ROLE_USER_STANDARD'))
		{
			$data[] = ['permission.permission_set', ['ROLE_USER_STANDARD', 'u_ass_can_purchase']];
			$data[] = ['permission.permission_set', ['ROLE_USER_STANDARD', 'u_ass_can_view_inactive_shop']];
			$data[] = ['permission.permission_set', ['ROLE_USER_STANDARD', 'u_ass_can_view_inactive_items']];
			$data[] = ['permission.permission_set', ['ROLE_USER_STANDARD', 'u_ass_can_gift']];
			$data[] = ['permission.permission_set', ['ROLE_USER_STANDARD', 'u_ass_can_receive_gift']];
		}

		if ($this->role_exists('ROLE_USER_FULL'))
		{
			$data[] = ['permission.permission_set', ['ROLE_USER_FULL', 'u_ass_can_purchase']];
			$data[] = ['permission.permission_set', ['ROLE_USER_FULL', 'u_ass_can_view_inactive_shop']];
			$data[] = ['permission.permission_set', ['ROLE_USER_FULL', 'u_ass_can_view_inactive_items']];
			$data[] = ['permission.permission_set', ['ROLE_USER_FULL', 'u_ass_can_gift']];
			$data[] = ['permission.permission_set', ['ROLE_USER_FULL', 'u_ass_can_receive_gift']];
		}

		if ($this->role_exists('ROLE_ADMIN_STANDARD'))
		{
			$data[] = ['permission.permission_set', ['ROLE_ADMIN_STANDARD', 'a_ass_overview']];
			$data[] = ['permission.permission_set', ['ROLE_ADMIN_STANDARD', 'a_ass_settings']];
			$data[] = ['permission.permission_set', ['ROLE_ADMIN_STANDARD', 'a_ass_items']];
			$data[] = ['permission.permission_set', ['ROLE_ADMIN_STANDARD', 'a_ass_files']];
			$data[] = ['permission.permission_set', ['ROLE_ADMIN_STANDARD', 'a_ass_logs']];
		}

		if ($this->role_exists('ROLE_ADMIN_FULL'))
		{
			$data[] = ['permission.permission_set', ['ROLE_ADMIN_FULL', 'a_ass_overview']];
			$data[] = ['permission.permission_set', ['ROLE_ADMIN_FULL', 'a_ass_settings']];
			$data[] = ['permission.permission_set', ['ROLE_ADMIN_FULL', 'a_ass_items']];
			$data[] = ['permission.permission_set', ['ROLE_ADMIN_FULL', 'a_ass_files']];
			$data[] = ['permission.permission_set', ['ROLE_ADMIN_FULL', 'a_ass_logs']];
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
