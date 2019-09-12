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
 * phpBB Studio - Advanced Shop System: Permissions migration
 */
class install_permissions extends \phpbb\db\migration\migration
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
		return ['\phpbbstudio\ass\migrations\install_acp_module'];
	}

	/**
	 * Add the ASS extension permissions to the database.
	 *
	 * @return array		Array of permissions
	 * @access public
	 */
	public function update_data()
	{
		return [
			/* Admin Group permissions */
			['permission.add', ['a_ass_overview']],
			['permission.add', ['a_ass_settings']],
			['permission.add', ['a_ass_items']],
			['permission.add', ['a_ass_files']],
			['permission.add', ['a_ass_logs']],

			/* Registered user Group permissions */
			['permission.add', ['u_ass_can_purchase']],
			['permission.add', ['u_ass_can_view_inactive_shop']],
			['permission.add', ['u_ass_can_view_inactive_items']],
			['permission.add', ['u_ass_can_gift']],
			['permission.add', ['u_ass_can_receive_gift']],
			['permission.add', ['u_ass_can_receive_stock_notifications']],
		];
	}
}
