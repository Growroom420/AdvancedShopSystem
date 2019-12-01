<?php
/**
 *
 * phpBB Studio - Advanced Shop System. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbstudio\ass\operator;

/**
 * phpBB Studio - Advanced Shop System: Blocks operator
 */
class blocks
{
	/** @var \phpbbstudio\aps\core\functions */
	protected $aps_functions;

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\group\helper */
	protected $group_helper;

	/** @var \phpbbstudio\ass\operator\item */
	protected $operator_item;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user_loader */
	protected $user_loader;

	/** @var string ASS Categories table */
	protected $categories_table;

	/** @var string ASS Items table */
	protected $items_table;

	/** @var string ASS Logs table */
	protected $logs_table;

	/**
	 * Constructor.
	 *
	 * @param \phpbbstudio\aps\core\functions	$aps_functions		APS Functions object
	 * @param \phpbb\auth\auth					$auth				Auth object
	 * @param \phpbb\config\config				$config				Config object
	 * @param \phpbb\db\driver\driver_interface	$db					Database object
	 * @param \phpbb\group\helper				$group_helper		Group helper object
	 * @param \phpbbstudio\ass\operator\item	$operator_item		Item operator object
	 * @param \phpbb\template\template			$template			Template object
	 * @param \phpbb\user_loader				$user_loader		User loader object
	 * @param string							$categories_table	ASS Categories table
	 * @param string							$items_table		ASS Items table
	 * @param string							$logs_table			ASS Logs table
	 */
	public function __construct(
		\phpbbstudio\aps\core\functions $aps_functions,
		\phpbb\auth\auth $auth,
		\phpbb\config\config $config,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\group\helper $group_helper,
		item $operator_item,
		\phpbb\template\template $template,
		\phpbb\user_loader $user_loader,
		$categories_table,
		$items_table,
		$logs_table
	)
	{
		$this->aps_functions	= $aps_functions;
		$this->auth				= $auth;
		$this->config			= $config;
		$this->db				= $db;
		$this->group_helper		= $group_helper;
		$this->operator_item	= $operator_item;
		$this->template			= $template;
		$this->user_loader		= $user_loader;

		$this->categories_table	= $categories_table;
		$this->items_table		= $items_table;
		$this->logs_table		= $logs_table;
	}

	/**
	 * Get the Shop display blocks.
	 *
	 * @return array			The shop display blocks
	 */
	public function get_blocks()
	{
		return [
			'charts' => [
				'purchases_category'	=> 'ASS_DISPLAY_PURCHASES_CATEGORY',
				'purchases_group'		=> 'ASS_DISPLAY_PURCHASES_GROUP',
			],
			'items' => [
				'sale'					=> 'ASS_SALE_ITEMS',
				'featured'				=> 'ASS_FEATURED_ITEMS',
				'recent'				=> 'ASS_ITEMS_RECENT',
				'purchases'				=> 'ASS_DISPLAY_PURCHASES_RECENT',
				'available'				=> 'ASS_DISPLAY_LIMITED_AVAILABLE',
				'limited'				=> 'ASS_DISPLAY_LIMITED_STOCK',
			],
			'users' => [
				'buyers'				=> 'ASS_DISPLAY_BIGGEST_BUYERS',
				'gifters'				=> 'ASS_DISPLAY_BIGGEST_GIFTERS',
				'spenders'				=> 'ASS_DISPLAY_BIGGEST_SPENDERS',
			],
		];
	}

	/**
	 * Display the "charts" blocks.
	 *
	 * @param  string	$block		The block name
	 * @return void
	 */
	protected function charts($block)
	{
		$s_group = $block === 'purchases_group';

		if ($s_group)
		{
			$sql = 'SELECT g.group_name, COUNT(l.log_id) as purchases
					FROM ' . $this->logs_table . ' l,
						' . $this->aps_functions->table('users') . ' u,
						' . $this->aps_functions->table('groups') . ' g
					WHERE l.item_purchase = 1
						AND l.user_id = u.user_id
						AND u.group_id = g.group_id
						AND g.group_type <> ' . GROUP_HIDDEN . '
					GROUP BY g.group_name
					ORDER BY purchases DESC';
		}
		else
		{
			$sql = 'SELECT c.category_title, SUM(i.item_purchases) as purchases
					FROM ' . $this->items_table . ' i,
						' . $this->categories_table . ' c
					WHERE i.category_id = c.category_id' .
					(!$this->auth->acl_get('u_ass_can_view_inactive_items') . ' AND c.category_active = 1') . '
					GROUP BY i.category_id
					ORDER BY c.category_order';
		}
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$name = $s_group ? $this->group_helper->get_name($row['group_name']) : $row['category_title'];

			$this->template->assign_block_vars($block, [
				'NAME'		=> (string) $name,
				'PURCHASES'	=> (int) $row['purchases'],
			]);
		}
		$this->db->sql_freeresult($result);
	}

	/**
	 * Display the "items" blocks.
	 *
	 * @param  string	$block		The block name
	 * @return void
	 */
	protected function items($block)
	{
		$items = $this->operator_item->assign_specific_items($block, 3, 0, false);

		foreach ($items as $item)
		{
			$this->template->assign_block_vars($block, $this->operator_item->get_variables($item));
		}
	}

	/**
	 * Display the "users" blocks.
	 *
	 * @param  string	$block		The block name
	 * @return void
	 */
	protected function users($block)
	{
		switch ($block)
		{
			case 'buyers':
				$select = 'COUNT(log_id)';
				$where =  ' WHERE recipient_id = 0';
			break;

			case 'gifters':
				$select = 'COUNT(log_id)';
				$where = ' WHERE recipient_id <> 0';
			break;

			case 'spenders':
			default:
				$select = 'SUM(points_sum)';
				$where = '';
			break;
		}

		$sql = 'SELECT ' . $select . ' as count, user_id
				FROM ' . $this->logs_table . $where . '
				GROUP BY user_id
				ORDER BY count DESC';
		$result = $this->db->sql_query_limit($sql, 3);
		$rowset = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		$users = array_column($rowset, 'count', 'user_id');

		$this->user_loader->load_users(array_map('intval', array_keys($users)));

		foreach ($users as $user_id => $count)
		{
			$avatar = $this->user_loader->get_avatar($user_id);
			$avatar = $avatar ? $avatar : $this->aps_functions->get_no_avatar();

			$this->template->assign_block_vars($block, [
				'NAME'		=> $this->user_loader->get_username($user_id, 'full'),
				'AVATAR'	=> $avatar,
				'COUNT'		=> $count,
			]);
		}

		$this->template->assign_var('USER_BLOCK', $block);
	}

	/**
	 * Magic method to call a function within this object.
	 *
	 * Needed as APS only calls each function ones,
	 * and ASS groups all blocks to certain categories.
	 * So, the block listener calls the block name in this object,
	 * which by this method is routed to the category's function.
	 *
	 * @see \phpbbstudio\ass\event\blocks_listener
	 *
	 * @param $method
	 * @param $arguments
	 * @return void
	 */
	public function __call($method, $arguments)
	{
		foreach ($this->get_blocks() as $type => $blocks)
		{
			if (in_array($method, array_keys($blocks)))
			{
				call_user_func_array([$this, $type], $arguments);
			}
		}
	}
}
