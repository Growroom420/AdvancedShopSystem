<?php
/**
 *
 * phpBB Studio - Advanced Shop System. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbstudio\ass\controller;

use phpbbstudio\ass\entity\item;
use phpbbstudio\ass\entity\category;

/**
 * phpBB Studio - Advanced Shop System: ACP Inventory controller
 */
class acp_inventory_controller
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\group\helper */
	protected $group_helper;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \phpbbstudio\ass\operator\category */
	protected $operator_cat;

	/** @var \phpbbstudio\ass\operator\item */
	protected $operator_item;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var string Groups table */
	protected $groups_table;

	/** @var string Users table */
	protected $users_table;

	/** @var string Usergroup table */
	protected $user_group_table;

	/** @var string Inventory table */
	protected $inventory_table;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var string php File extension */
	protected $php_ext;

	/** @var string Custom form action */
	protected $u_action;

	public function __construct(
		\phpbb\db\driver\driver_interface $db,
		\phpbb\group\helper $group_helper,
		\phpbb\language\language $language,
		\phpbb\log\log $log,
		\phpbbstudio\ass\operator\category $operator_cat,
		\phpbbstudio\ass\operator\item $operator_item,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		$groups_table,
		$users_table,
		$user_group_table,
		$inventory_table,
		$root_path,
		$php_ext
	)
	{
		$this->db				= $db;
		$this->group_helper		= $group_helper;
		$this->language			= $language;
		$this->log				= $log;
		$this->operator_cat		= $operator_cat;
		$this->operator_item	= $operator_item;
		$this->request			= $request;
		$this->template			= $template;
		$this->user				= $user;

		$this->groups_table		= $groups_table;
		$this->users_table		= $users_table;
		$this->user_group_table	= $user_group_table;
		$this->inventory_table	= $inventory_table;

		$this->root_path		= $root_path;
		$this->php_ext			= $php_ext;
	}

	/**
	 * Handle and display the "Inventory" ACP mode.
	 *
	 * @return void
	 * @access public
	 */
	public function inventory()
	{
		$this->language->add_lang(['ass_acp_common', 'ass_common'], 'phpbbstudio/ass');

		$type = $this->request->variable('type', '', true);
		$submit = $this->request->is_set_post('submit');

		$errors = [];

		$form_key = 'ass_inventory';
		add_form_key($form_key);

		switch ($type)
		{
			case 'global';
				$action		= $this->request->variable('action', 'add', true);
				$item_ids	= $this->request->variable('items', [0]);
				$group_ids	= $this->request->variable('groups', [0]);
				$usernames	= $this->request->variable('usernames', '', true);

				$items = [];

				/** @var category $category */
				foreach ($this->operator_cat->get_categories() as $category)
				{
					$this->template->assign_block_vars('categories', array_merge([
						'S_INACTIVE'	=> !$category->get_active(),
					], $this->operator_cat->get_variables($category)));

					$items += $category_items = $this->operator_item->get_items($category->get_id());

					/** @var item $item */
					foreach ($category_items as $item)
					{
						$this->template->assign_block_vars('categories.items', array_merge([
							'S_INACTIVE'	=> !$item->get_active(),
							'S_SELECTED'	=> in_array($item->get_id(), $item_ids),
						], $this->operator_item->get_variables($item)));
					}
				}

				if ($submit)
				{
					if (!check_form_key($form_key))
					{
						$errors[] = 'FORM_INVALID';
					}

					if (!in_array($action, ['add', 'delete']))
					{
						$errors[] = 'NO_ACTION';
					}

					if (empty($item_ids))
					{
						$errors[] = 'ACP_ASS_NO_ITEMS_SELECTED';
					}

					$user_ids = [];
					$usernames_array = array_filter(explode("\n", $usernames));

					if (empty($usernames_array) && empty($group_ids))
					{
						$this->language->add_lang('acp/permissions');

						$errors[] = 'NO_USER_GROUP_SELECTED';
					}

					if (!empty($usernames_array))
					{
						$this->get_ids_from_usernames($usernames_array, $user_ids, $errors);
					}

					if (!empty($group_ids))
					{
						$this->get_ids_from_groups($group_ids, $user_ids);
					}

					if (empty($errors) && empty($user_ids))
					{
						$errors[] = 'NO_GROUP_MEMBERS';
					}

					if (empty($errors))
					{
						$user_ids = array_unique($user_ids);

						$this->update_inventory($action, $user_ids, $item_ids);

						$count_items = count($item_ids);
						$count_users = count($user_ids);

						$item_names = [];

						foreach ($item_ids as $item_id)
						{
							$item_names[] = $items[$item_id]->get_title();
						}

						$l_action = 'ACP_ASS_INVENTORY_' . utf8_strtoupper($action);

						$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, "LOG_{$l_action}", time(), [$count_items, implode(', ', $item_names), $count_users, implode(', ', $usernames_array)]);

						$message = $this->language->lang("{$l_action}_SUCCESS", $count_items);
						$message .= '<br>&raquo; ' . $this->language->lang('ACP_ASS_AMOUNT_ITEMS') . $this->language->lang('COLON') . ' ' . $count_items;
						$message .= '<br>&raquo; ' . $this->language->lang('ACP_ASS_AMOUNT_USERS') . $this->language->lang('COLON') . ' ' . $count_users;
						$message .= adm_back_link($this->u_action);

						trigger_error($message);
					}
				}

				$sql = 'SELECT group_id, group_name, group_type
						FROM ' . $this->groups_table . "
						WHERE group_name <> 'BOTS'
							AND group_name <> 'GUESTS'
						ORDER BY group_type DESC, group_name ASC";
				$result = $this->db->sql_query($sql);
				while ($row = $this->db->sql_fetchrow($result))
				{
					$this->template->assign_block_vars('groups', [
						'ID'			=> (int) $row['group_id'],
						'NAME'			=> $this->group_helper->get_name($row['group_name']),
						'S_SELECTED'	=> in_array((int) $row['group_id'], $group_ids),
						'S_SPECIAL'		=> GROUP_SPECIAL === (int) $row['group_type'],
					]);
				}
				$this->db->sql_freeresult($result);

				$this->template->assign_vars([
					'USERNAMES'		=> $usernames,
					'S_ADD'			=> $action === 'add',
					'U_FIND_USER'	=> append_sid("{$this->root_path}memberlist.{$this->php_ext}", [
						'mode'	=> 'searchuser',
						'form'	=> 'ass_inventory',
						'field'	=> 'usernames',
					]),
				]);
			break;

			case 'manage':
				$action		= $this->request->variable('action', '', true);
				$username	= $this->request->variable('username', '', true);
				$user_id	= $this->request->variable('u', 0);
				$item_ids	= $this->request->variable('items', [0]);

				if (empty($username) && empty($user_id))
				{
					$this->template->assign_var('U_FIND_USER', append_sid("{$this->root_path}memberlist.{$this->php_ext}", [
						'mode'			=> 'searchuser',
						'form'			=> 'ass_inventory',
						'field'			=> 'username',
						'select_single'	=> true,
					]));
				}
				else
				{
					if (empty($user_id))
					{
						$user_ids = [];

						$this->get_ids_from_usernames([$username], $user_ids, $errors);

						if (empty($user_ids))
						{
							trigger_error($this->language->lang('NO_USER') . adm_back_link($this->u_action . "&type={$type}"), E_USER_WARNING);
						}

						$user_id = (int) reset($user_ids);
					}

					$rowset = [];

					$sql = 'SELECT i.*, u.username as gifter_name, u.user_colour as gifter_colour
							FROM ' . $this->inventory_table . ' i
							LEFT JOIN ' . $this->users_table . ' u
								ON i.gifter_id = u.user_id
							WHERE i.user_id = ' . (int) $user_id;
					$result = $this->db->sql_query($sql);
					while ($row = $this->db->sql_fetchrow($result))
					{
						$rowset[(int) $row['item_id']] = $row;
					}
					$this->db->sql_freeresult($result);

					$inventory = [
						'categories'	=> array_column($rowset, 'category_id'),
						'items'			=> array_keys($rowset),
					];

					$items = [];
					$categories = $this->operator_cat->get_categories();

					/** @var category $category */
					foreach ($categories as $category)
					{
						$this->template->assign_block_vars('categories', array_merge([
							'S_INACTIVE'	=> !$category->get_active(),
							'S_INVENTORY'	=> in_array($category->get_id(), $inventory['categories']),
						], $this->operator_cat->get_variables($category)));

						$items += $category_items = $this->operator_item->get_items($category->get_id());

						/** @var item $item */
						foreach ($category_items as $item)
						{
							$variables = array_merge([
								'S_INACTIVE'	=> !$item->get_active(),
								'S_INVENTORY'	=> in_array($item->get_id(), $inventory['items']),
								'S_SELECTED'	=> in_array($item->get_id(), $item_ids),
							], $this->operator_item->get_variables($item));

							if ($variables['S_INVENTORY'])
							{
								$row = $rowset[$item->get_id()];

								$variables = array_merge($variables, [
									'GIFTER'		=> $row['gifter_id'] ? get_username_string('full', $row['gifter_id'], $row['gifter_name'], $row['gifter_colour']) : '',
									'PURCHASE_TIME'	=> $this->user->format_date($row['purchase_time']),
									'USE_TIME'		=> $row['use_time'] ? $this->user->format_date($row['use_time']) : $this->language->lang('NEVER'),
									'USE_COUNT'		=> (int) $row['use_count'],
								]);
							}

							$this->template->assign_block_vars('categories.items', $variables);
						}
					}

					$u_back = $this->u_action . "&type={$type}&u={$user_id}";

					if ($action === 'delete')
					{
						$item_id = $this->request->variable('iid', 0);

						if (confirm_box(true))
						{
							$this->update_inventory($action, [$user_id], [$item_id]);

							if (empty($username))
							{
								$username = $this->get_username($user_id);
							}

							$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_ACP_ASS_INVENTORY_DELETE_USER', time(), [$items[$item_id]->get_title(), $username]);

							trigger_error($this->language->lang('ASS_DELETE_SUCCESS') . adm_back_link($u_back));
						}
						else
						{
							confirm_box(false, 'ASS_DELETE', '');

							redirect($u_back);
						}
					}

					if ($submit)
					{
						if (!check_form_key($form_key))
						{
							$errors[] = 'FORM_INVALID';
						}

						if (empty($item_ids))
						{
							$errors[] = 'ACP_ASS_NO_ITEMS_SELECTED';
						}

						if (empty($errors))
						{
							$this->update_inventory('add', [$user_id], $item_ids);

							if (empty($username))
							{
								$username = $this->get_username($user_id);
							}

							$count_items = count($item_ids);

							$item_names = [];

							foreach ($item_ids as $item_id)
							{
								$item_names[] = $items[$item_id]->get_title();
							}

							$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_ACP_ASS_INVENTORY_ADD_USER', time(), [$count_items, implode(', ', $item_names), $username]);

							trigger_error($this->language->lang('ACP_ASS_INVENTORY_ADD_SUCCESS', $count_items) . adm_back_link($u_back));
						}
					}

					$this->template->assign_vars([
						'USERNAME'	=> $username,

						'U_DELETE'	=> $this->u_action . "&type={$type}&u={$user_id}&action=delete&iid=",
					]);
				}
			break;

			default:
				$this->template->assign_vars([
					'U_GLOBAL'	=> $this->u_action . '&amp;type=global',
					'U_MANAGE'	=> $this->u_action . '&amp;type=manage',
				]);
			break;
		}

		$this->template->assign_vars([
			'ERRORS'	=> $errors,

			'S_TYPE'	=> $type,

			'U_ACTION'	=> $this->u_action . ($type ? "&type={$type}" : ''),
			'U_BACK'	=> $this->u_action,
		]);
	}

	/**
	 * Update users' inventories.
	 *
	 * @param  string	$action			The action to perform (add|delete)
	 * @param  array	$user_ids		The user identifiers
	 * @param  array	$item_ids		The item identifiers
	 * @return void
	 * @access protected
	 */
	protected function update_inventory($action, array $user_ids, array $item_ids)
	{
		switch ($action)
		{
			case 'add':
				$data = [];
				$owned = [];

				$sql = 'SELECT item_id, user_id
						FROM ' . $this->inventory_table . '
						WHERE ' . $this->db->sql_in_set('item_id', $item_ids) . '
							AND ' . $this->db->sql_in_set('user_id', $user_ids);
				$result = $this->db->sql_query($sql);
				while ($row = $this->db->sql_fetchrow($result))
				{
					$owned[(int) $row['item_id']][] = (int) $row['user_id'];
				}
				$this->db->sql_freeresult($result);

				$items = $this->operator_item->get_items_by_id($item_ids);

				foreach ($item_ids as $item_id)
				{
					/** @var item $item */
					$item = $items[$item_id];

					$users = !empty($owned[$item_id]) ? array_diff($user_ids, $owned[$item_id]) : $user_ids;

					foreach ($users as $user_id)
					{
						$data[] = [
							'category_id'		=> $item->get_category(),
							'item_id'			=> $item->get_id(),
							'user_id'			=> $user_id,
							'gifter_id'			=> (int) $this->user->data['user_id'],
							'purchase_time'		=> time(),
							'purchase_price'	=> 0.00,
						];
					}
				}

				$this->db->sql_multi_insert($this->inventory_table, $data);
			break;

			case 'delete':
				$sql = 'DELETE FROM ' . $this->inventory_table . '
						WHERE ' . $this->db->sql_in_set('item_id', $item_ids) . '
							AND ' . $this->db->sql_in_set('user_id', $user_ids);
				$this->db->sql_query($sql);
			break;
		}
	}

	/**
	 * Get user identifiers from usernames.
	 *
	 * @param  array	$usernames		The usernames
	 * @param  array	$user_ids		The user identifiers
	 * @param  array	$errors			The errors
	 * @return void
	 * @access protected
	 */
	protected function get_ids_from_usernames(array $usernames, array &$user_ids, array &$errors)
	{
		$usernames_clean = [];
		$usernames_found = [];

		foreach ($usernames as $username)
		{
			$usernames_clean[$username] = utf8_clean_string($username);
		}

		$sql = 'SELECT user_id, username_clean
				FROM ' . $this->users_table . '
				WHERE ' . $this->db->sql_in_set('username_clean', $usernames_clean);
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$usernames_found[] = $row['username_clean'];
			$user_ids[] = (int) $row['user_id'];
		}
		$this->db->sql_freeresult($result);

		$usernames_not_found = array_diff($usernames_clean, $usernames_found);

		if (!empty($usernames_not_found))
		{
			$errors[] = count($usernames_not_found) > 1 ? 'NO_USERS' : 'NO_USER';

			$not_found = array_intersect($usernames_clean, $usernames_not_found);
			$not_found = array_flip($not_found);

			foreach ($not_found as $username)
			{
				$errors[] = '&raquo; ' . $username;
			}
		}
	}

	/**
	 * Get user identifiers from group identifiers.
	 *
	 * @param  array	$group_ids		The group identifiers
	 * @param  array	$user_ids		The user identifiers
	 * @return void
	 * @access protected
	 */
	protected function get_ids_from_groups(array $group_ids, array &$user_ids)
	{
		$sql = 'SELECT user_id
				FROM ' . $this->user_group_table . '
				WHERE user_pending <> 1
					AND ' . $this->db->sql_in_set('group_id', $group_ids);
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$user_ids[] = (int) $row['user_id'];
		}
		$this->db->sql_freeresult($result);
	}

	/**
	 * Get a username from user identifier.
	 *
	 * @param  int		$user_id		The user identifier
	 * @return string					The username
	 * @access protected
	 */
	protected function get_username($user_id)
	{
		$sql = 'SELECT username
				FROM ' . $this->users_table . '
				WHERE user_id = ' . (int) $user_id;
		$result = $this->db->sql_query_limit($sql, 1);
		$username = $this->db->sql_fetchfield('username');
		$this->db->sql_freeresult($result);

		return (string) $username;
	}

	/**
	 * Set custom form action.
	 *
	 * @param  string		$u_action	Custom form action
	 * @return self			$this		This controller for chaining calls
	 * @access public
	 */
	public function set_page_url($u_action)
	{
		$this->u_action = $u_action;

		return $this;
	}
}
