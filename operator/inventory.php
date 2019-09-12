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

use phpbbstudio\ass\entity\category;
use phpbbstudio\ass\entity\item;

/**
 * phpBB Studio - Advanced Shop System: Inventory operator
 */
class inventory
{
	/** @var \phpbbstudio\aps\core\functions */
	protected $aps_functions;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbbstudio\ass\operator\item */
	protected $operator_item;

	/** @var \phpbb\user */
	protected $user;

	/** @var string Inventory table */
	protected $inventory_table;

	/** @var string Items table */
	protected $items_table;

	/** @var string Users table */
	protected $users_table;

	/**
	 * Constructor.
	 *
	 * @param  \phpbbstudio\aps\core\functions		$aps_functions		APS Functions object
	 * @param  \phpbb\config\config					$config				Config object
	 * @param  \phpbb\db\driver\driver_interface	$db					Database object
	 * @param  \phpbbstudio\ass\operator\item		$operator_item		Item operator object
	 * @param  \phpbb\user							$user				User object
	 * @param  string								$inventory_table	Inventory table
	 * @param  string								$items_table		Items table
	 * @param  string								$users_table		Users table
	 * @return void
	 * @access public
	 */
	public function __construct(
		\phpbbstudio\aps\core\functions $aps_functions,
		\phpbb\config\config $config,
		\phpbb\db\driver\driver_interface $db,
		\phpbbstudio\ass\operator\item $operator_item,
		\phpbb\user $user,
		$inventory_table,
		$items_table,
		$users_table
	)
	{
		$this->aps_functions	= $aps_functions;
		$this->config			= $config;
		$this->db				= $db;
		$this->operator_item	= $operator_item;
		$this->user				= $user;

		$this->inventory_table	= $inventory_table;
		$this->items_table		= $items_table;
		$this->users_table		= $users_table;
	}

	/**
	 * Clean up the inventories.
	 *
	 * @return bool								Whether or not inventory rows were removed
	 * @access public
	 */
	public function clean_inventory()
	{
		$sql = 'DELETE inv
				FROM ' . $this->inventory_table . ' inv
				JOIN ' . $this->items_table . ' i
					ON i.item_id = inv.item_id
				WHERE (i.item_count <> 0
						AND i.item_count <= inv.use_count
						AND (i.item_delete_seconds = 0
							OR i.item_delete_seconds + inv.use_time <= ' . time() . '))
					OR (i.item_expire_seconds <> 0
						AND i.item_expire_seconds + i.item_delete_seconds + inv.purchase_time <= ' . time() . ')';
		$this->db->sql_query($sql);

		return (bool) $this->db->sql_affectedrows();
	}

	/**
	 * Get a user's inventory.
	 *
	 * @param  category|null	$category		The category entity
	 * @return array							The user's inventory
	 * @access public
	 */
	public function get_inventory(category $category = null)
	{
		$rowset = [];

		$sql_where = $category !== null ? ' AND i.category_id = ' . $category->get_id() : '';

		$sql = 'SELECT i.*, u.username as gifter_name, u.user_colour as gifter_colour
				FROM ' . $this->inventory_table . ' i
				LEFT JOIN ' . $this->users_table . ' u
					ON i.gifter_id = u.user_id
				WHERE i.user_id = ' . (int) $this->user->data['user_id'] .
				$sql_where;
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$rowset[(int) $row['item_id']] = $row;
		}
		$this->db->sql_freeresult($result);

		return (array) $rowset;
	}

	/**
	 * Get an inventory item.
	 *
	 * @param  item				$item			The item entity
	 * @return array
	 * @access public
	 */
	public function get_inventory_item(item $item)
	{
		$sql = 'SELECT * 
				FROM ' . $this->inventory_table . '
				WHERE user_id = ' . (int) $this->user->data['user_id'] . '
					AND item_id = ' . $item->get_id() . '
					AND category_id = ' . $item->get_category();
		$result = $this->db->sql_query_limit($sql, 1);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return (array) $row;
	}

	/**
	 * Check if a user can purchase an item and does not already own it.
	 *
	 * @param  item				$item			The item entity
	 * @param  int				$user_id		The user identifier
	 * @return bool
	 * @access public
	 */
	public function check_purchase(item $item, $user_id = 0)
	{
		$user_id = $user_id ? $user_id : $this->user->data['user_id'];

		$sql = 'SELECT 1
				FROM ' . $this->inventory_table . '
				WHERE user_id = ' . (int) $user_id . '
					AND item_id = ' . $item->get_id() . '
					AND category_id = ' . $item->get_category();
		$result = $this->db->sql_query_limit($sql, 1);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return (bool) ($row !== false);
	}

	/**
	 * Add a purchase (or gift).
	 *
	 * @param  item				$item			The item entity
	 * @param  int				$user_id		The user identifier
	 * @param  bool				$purchase		Whether it's a purchase or a gift
	 * @return double
	 * @access public
	 */
	public function add_purchase(item $item, $user_id = 0, $purchase = true)
	{
		$price = $this->get_price($item, $purchase);
		$points = $this->user->data['user_points'] - $price;

		$this->db->sql_transaction('begin');

		$this->update_user_points($points);

		$sql = 'INSERT INTO ' . $this->inventory_table . ' ' . $this->db->sql_build_array('INSERT', [
			'category_id'		=> $item->get_category(),
			'item_id'			=> $item->get_id(),
			'user_id'			=> $user_id ? $user_id : (int) $this->user->data['user_id'],
			'gifter_id'			=> $user_id ? (int) $this->user->data['user_id'] : $user_id,
			'purchase_time'		=> time(),
			'purchase_price'	=> $price,
		]);
		$this->db->sql_query($sql);

		$this->db->sql_transaction('commit');

		return (double) $points;
	}

	/**
	 * Get the purchase (inventory row) identifier.
	 *
	 * @return int								The purchase identifier
	 * @access public
	 */
	public function get_purchase_id()
	{
		return (int) $this->db->sql_nextid();
	}

	/**
	 * Update a user's points.
	 *
	 * @param  double			$points			The new user points
	 * @return bool
	 * @access public
	 */
	public function update_user_points($points)
	{
		$sql = 'UPDATE ' . $this->users_table . ' SET ' . $this->db->sql_build_array('UPDATE', [
				'user_points' => $points,
			]) . ' WHERE user_id = ' . (int) $this->user->data['user_id'];
		$this->db->sql_query($sql);

		return (bool) $this->db->sql_affectedrows();
	}

	/**
	 * Delete an inventory item.
	 *
	 * @param  item				$item			The item entity
	 * @return bool								Whether or not the item was deleted
	 * @access public
	 */
	public function delete(item $item)
	{
		$sql = 'DELETE FROM ' . $this->inventory_table . '
				WHERE user_id = ' . (int) $this->user->data['user_id'] . '
					AND item_id = ' . $item->get_id() . '
					AND category_id = ' . $item->get_category();
		$this->db->sql_query($sql);

		return (bool) $this->db->sql_affectedrows();
	}

	/**
	 * Delete inventory item(s).
	 *
	 * @param  int				$category_id	The category identifier
	 * @param  int				$item_id		The item identifier
	 * @return int
	 * @access public
	 */
	public function delete_items($category_id, $item_id)
	{
		$sql = 'DELETE FROM ' . $this->inventory_table . '
				WHERE ' . ($category_id ? 'category_id = ' . (int) $category_id : 'item_id = ' . (int) $item_id);
		$this->db->sql_query($sql);

		return (int) $this->db->sql_affectedrows();
	}

	/**
	 * Update the activated count for an inventory item.
	 *
	 * @param  item				$item			The item entity
	 * @param  array			$data			The inventory item data
	 * @return bool								Whether or not the inventory item was updated
	 * @access public
	 */
	public function activated(item $item, array $data)
	{
		$sql = 'UPDATE ' . $this->inventory_table . ' 
			SET ' . $this->db->sql_build_array('UPDATE', $data) . '
			WHERE user_id = ' . (int) $this->user->data['user_id'] . '
				AND item_id = ' . $item->get_id() . '
				AND category_id = ' . $item->get_category();
		$this->db->sql_query($sql);

		return (bool) $this->db->sql_affectedrows();
	}

	/**
	 * Check if the current user can afford this item.
	 *
	 * @param  item				$item			The item entity
	 * @param  bool				$purchase		Whether it's a purchase or a gift
	 * @return bool								Whether or not the user can afford this item
	 * @access public
	 */
	public function check_price(item $item, $purchase = true)
	{
		if ($this->config['aps_points_min'] !== '')
		{
			$item_points = $this->get_price($item, $purchase);
			$user_points = $this->user->data['user_points'];

			$points = $user_points - $item_points;

			if ($points < $this->config['aps_points_min'])
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Get the price for an item, taking into account sale and gift.
	 *
	 * @param  item				$item			The item entity
	 * @param  bool				$purchase		Whether it's a purchase or a gift
	 * @return double							The item price
	 * @access public
	 */
	public function get_price(item $item, $purchase = true)
	{
		if ($purchase)
		{
			return $this->operator_item->on_sale($item) ? $item->get_sale_price() : $item->get_price();
		}

		if (!$item->get_gift_type())
		{
			return $item->get_gift_price();
		}

		$pct = $item->get_gift_percentage() / 100;
		$pct = $pct + 1;

		$price = $this->get_price($item) * $pct;

		return $this->aps_functions->format_points($price);
	}
}
