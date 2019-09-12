<?php
/**
 *
 * phpBB Studio - Advanced Shop System. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbstudio\ass\helper;

use phpbbstudio\ass\entity\item;

/**
 * phpBB Studio - Advanced Shop System: Log helper
 */
class log
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\user */
	protected $user;

	/** @var string Categories table */
	protected $categories_table;

	/** @var string Items table */
	protected $items_table;

	/** @var string Logs table */
	protected $logs_table;

	/** @var string Users table */
	protected $users_table;

	/**
	 * Constructor.
	 *
	 * @param  \phpbb\db\driver\driver_interface	$db					Database object
	 * @param  \phpbb\user							$user				User object
	 * @param  string								$categories_table	Categories table
	 * @param  string								$items_table		Items table
	 * @param  string								$logs_table			Logs table
	 * @param  string								$users_table		Users table
	 * @return void
	 * @access public
	 */
	public function __construct(
		\phpbb\db\driver\driver_interface $db,
		\phpbb\user $user,
		$categories_table,
		$items_table,
		$logs_table,
		$users_table
	)
	{
		$this->db				= $db;
		$this->user				= $user;

		$this->categories_table	= $categories_table;
		$this->items_table		= $items_table;
		$this->logs_table		= $logs_table;
		$this->users_table		= $users_table;
	}

	/**
	 * Add a log entry to the shop logs table.
	 *
	 * @param  item			$item				The shop item
	 * @param  bool			$purchase			Whether it is a purchase/gift or an activation
	 * @param  double		$points_sum			The points sum (cost of the purchase/gift)
	 * @param  int			$recipient_id		The recipient identifier
	 * @param  double		$points_old			The user's old points total
	 * @param  double		$points_new			The user's new points total
	 * @param  int			$time				The log time
	 * @return bool								Whether or not the log was successfully added
	 * @access public
	 */
	public function add(item $item, $purchase, $points_sum = 0.00, $recipient_id = 0, $points_old = 0.00, $points_new = 0.00, $time = 0)
	{
		$time = $time ? $time : time();

		if ($purchase && $points_sum)
		{
			$points_old = $points_old ? $points_old : $this->user->data['user_points'];
			$points_new = $points_new ? $points_new : $points_old - $points_sum;
		}

		$data = [
			'log_ip'		=> (string) $this->user->ip,
			'log_time'		=> (int) $time,
			'points_old'	=> (double) $points_old,
			'points_sum'	=> (double) $points_sum,
			'points_new'	=> (double) $points_new,
			'item_purchase'	=> (bool) $purchase,
			'item_id'		=> (int) $item->get_id(),
			'category_id'	=> (int) $item->get_category(),
			'user_id'		=> (int) $this->user->data['user_id'],
			'recipient_id'	=> (int) $recipient_id,
		];

		$sql = 'INSERT INTO ' . $this->logs_table . ' ' . $this->db->sql_build_array('INSERT', $data);
		$this->db->sql_query($sql);

		return (bool) $this->db->sql_affectedrows();
	}

	/**
	 * Get the shop log entries for a specific user.
	 *
	 * @param  string		$sql_where				The SQL WHERE statement
	 * @param  string		$sql_order				The SQL ORDER BY statement
	 * @param  string		$sql_dir				The SQL ORDER BY direction
	 * @param  int			$limit					The amount of entries to return
	 * @param  int			$start					The offset from where to return entries
	 * @param  int			$user_id				The user identifier
	 * @return array								The shop log entries
	 * @access public
	 */
	public function get_user_logs($sql_where, $sql_order, $sql_dir, $limit, $start, $user_id = 0)
	{
		if ($user_id)
		{
			$user_where = '(l.user_id = ' . (int) $user_id . ' OR l.recipient_id = ' . (int) $user_id . ')';

			$sql_where = $sql_where ? $user_where . ' AND ' . $sql_where : $user_where;
		}

		$sql_array = [
			'SELECT'	=> 'l.*, i.item_title, c.category_title,
							u.username, u.user_colour,
							r.username as recipient_name, r.user_colour as recipient_colour',
			'FROM'		=> [$this->logs_table			=> 'l'],
			'LEFT_JOIN'	=> [
				[
					'FROM'	=> [$this->categories_table	=> 'c'],
					'ON'	=> 'l.category_id = c.category_id',
				],
				[
					'FROM'	=> [$this->items_table		=> 'i'],
					'ON'	=> 'l.item_id = i.item_id',
				],
				[
					'FROM'	=> [$this->users_table		=> 'r'],
					'ON'	=> 'l.recipient_id = r.user_id',
				],
				[
					'FROM'	=> [$this->users_table		=> 'u'],
					'ON'	=> 'l.user_id = u.user_id',
				],
			],
			'WHERE'		=> $sql_where,
			'ORDER_BY'	=> "{$sql_order} {$sql_dir}",
		];

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query_limit($sql, $limit, $start);
		$rowset = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		return (array) $rowset;
	}

	/**
	 * Get the shop log entries count for a specific user.
	 *
	 * @param  string		$sql_where				The SQL WHERE statement
	 * @param  int			$user_id				The user identifier
	 * @return int									The shop log entries count
	 * @access public
	 */
	public function get_user_logs_count($sql_where, $user_id = 0)
	{
		if ($user_id)
		{
			$user_where = 'l.user_id = ' . (int) $user_id;

			$sql_where = $sql_where ? $user_where . ' AND ' . $sql_where : $user_where;
		}

		$sql_array = [
			'SELECT'	=> 'COUNT(l.log_id) as count',
			'FROM'		=> [$this->logs_table => 'l'],
			'WHERE'		=> $sql_where,
		];

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$count = $this->db->sql_fetchfield('count');
		$this->db->sql_freeresult($result);

		return (int) $count;
	}

	/**
	 * Delete shop log entries.
	 *
	 * @param  bool		$all		Delete all log entries
	 * @param  array	$ids		The log entry identifiers
	 * @return bool					Whether or not any entries were deleted
	 * @access public
	 */
	public function delete($all, array $ids)
	{
		$sql = 'DELETE FROM ' . $this->logs_table
			. (!$all ? ' WHERE ' . $this->db->sql_in_set('log_id', $ids) : '');
		$this->db->sql_query($sql);

		return (bool) $this->db->sql_affectedrows();
	}
}
