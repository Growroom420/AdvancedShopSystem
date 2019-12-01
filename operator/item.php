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

use phpbb\exception\runtime_exception;
use phpbbstudio\ass\exceptions\shop_exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use phpbbstudio\ass\entity\item as entity;

/**
 * phpBB Studio - Advanced Shop System: Item operator
 */
class item
{
	/** @var \phpbbstudio\aps\core\dbal */
	protected $aps_dbal;

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var ContainerInterface */
	protected $container;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbbstudio\ass\helper\files */
	protected $files;

	/** @var \phpbb\path_helper */
	protected $path_helper;

	/** @var \phpbbstudio\ass\helper\router */
	protected $router;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbbstudio\ass\helper\time */
	protected $time;

	/** @var \phpbb\user */
	protected $user;

	/** @var string Categories table */
	protected $categories_table;

	/** @var string Items table */
	protected $items_table;

	/**
	 * Constructor.
	 *
	 * @param  \phpbbstudio\aps\core\dbal			$aps_dbal				APS DBAL object
	 * @param  \phpbb\auth\auth						$auth					Auth object
	 * @param  ContainerInterface					$container				Service container object
	 * @param  \phpbb\db\driver\driver_interface	$db						Database object
	 * @param  \phpbbstudio\ass\helper\files		$files					Files helper object
	 * @param  \phpbb\path_helper					$path_helper			Path helper object
	 * @param  \phpbbstudio\ass\helper\router		$router					Router helper object
	 * @param  \phpbb\template\template				$template				Template object
	 * @param  \phpbbstudio\ass\helper\time			$time					Time helper object
	 * @param  \phpbb\user							$user					User object
	 * @param  string								$categories_table		Categories table
	 * @param  string								$items_table			Items table
	 * @return void
	 * @access public
	 */
	public function __construct(
		\phpbbstudio\aps\core\dbal $aps_dbal,
		\phpbb\auth\auth $auth,
		ContainerInterface $container,
		\phpbb\db\driver\driver_interface $db,
		\phpbbstudio\ass\helper\files $files,
		\phpbb\path_helper $path_helper,
		\phpbbstudio\ass\helper\router $router,
		\phpbb\template\template $template,
		\phpbbstudio\ass\helper\time $time,
		\phpbb\user $user,
		$categories_table,
		$items_table
	)
	{
		$this->aps_dbal			= $aps_dbal;
		$this->auth				= $auth;
		$this->container		= $container;
		$this->db				= $db;
		$this->files			= $files;
		$this->path_helper		= $path_helper;
		$this->router			= $router;
		$this->template			= $template;
		$this->time				= $time;
		$this->user				= $user;

		$this->categories_table	= $categories_table;
		$this->items_table		= $items_table;
	}

	/**
	 * Get an item entity.
	 *
	 * @return entity|object				The item entity
	 * @access public
	 */
	public function get_entity()
	{
		return $this->container->get('phpbbstudio.ass.entity.item');
	}

	/**
	 * Get loaded item entities for a rowset.
	 *
	 * @param  array	$rowset				The rowset retrieved from the items table
	 * @return entity[]						Item entities
	 * @access public
	 */
	public function get_entities(array $rowset)
	{
		$items = [];

		foreach ($rowset as $row)
		{
			$items[(int) $row['item_id']] = $this->get_entity()->import($row);
		}

		return (array) $items;
	}

	/**
	 * Load an item entity from a slug.
	 *
	 * @param  string	$item_slug			The item slug
	 * @param  string	$category_slug		The category slug
	 * @param  string	$category_id		The category identifier
	 * @return entity						The item entity
	 * @throws shop_exception
	 * @access public
	 */
	public function load_entity($item_slug, $category_slug, $category_id)
	{
		$item = $this->get_entity();

		try
		{
			$item->load(0, $item_slug, $category_id);
		}
		catch (runtime_exception $e)
		{
			throw new shop_exception(404, 'ASS_ERROR_NOT_FOUND_ITEM');
		}

		$item->set_category_slug($category_slug);

		if (!$item->get_active() && !$this->auth->acl_get('u_ass_can_view_inactive_items'))
		{
			throw new shop_exception(403, 'ASS_ERROR_NOT_ACTIVE_ITEM');
		}

		return $item;
	}

	/**
	 * Get item entities.
	 *
	 * @param  int		$category_id		The category identifier
	 * @param  string	$sql_where			The SQL WHERE statement
	 * @param  string	$sql_order			The SQL ORDER BY statement
	 * @param  string	$sql_dir			The SQL ORDER BY direction
	 * @param  bool		$active_only		Whether or not to only load active items
	 * @param  int		$limit				The amount of item entities to return
	 * @param  int		$start				The offset from where to return entities
	 * @return entity[]						Item entities
	 * @access public
	 */
	public function get_items($category_id, $sql_where = '', $sql_order = 'i.item_order', $sql_dir = 'ASC', $active_only = false, $limit = 0, $start = 0)
	{
		$sql_active = $active_only ? $this->get_sql_where_active('i.') : '';
		$sql_available = $active_only ? $this->get_sql_where_available('i.') : '';

		$sql = 'SELECT i.*, c.category_slug
				FROM ' . $this->items_table . ' i,
					' . $this->categories_table . ' c
				WHERE i.category_id = c.category_id
					AND i.category_id = ' . (int) $category_id . $sql_active . $sql_available . $sql_where . "
				ORDER BY {$sql_order} {$sql_dir}";
		$result = $limit ? $this->db->sql_query_limit($sql, $limit, $start) : $this->db->sql_query($sql);
		$rowset = (array) $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		return $this->get_entities($rowset);
	}

	/**
	 * Get item entities from identifiers.
	 *
	 * @param  array	$ids				The item identifiers
	 * @param  bool		$as_entities		Whether or not to return entities
	 * @return entity[]						Item entities
	 * @access public
	 */
	public function get_items_by_id(array $ids, $as_entities = true)
	{
		$sql = 'SELECT i.*, c.category_slug
				FROM ' . $this->items_table . ' i,
					' . $this->categories_table . ' c
				WHERE i.category_id = c.category_id
					AND ' . $this->db->sql_in_set('i.item_id', array_unique($ids), false, true) .
					$this->get_sql_where_active('i.') . '
				ORDER BY i.item_order ASC';
		$result = $this->db->sql_query($sql);
		$rowset = (array) $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		if ($as_entities)
		{
			return $this->get_entities($rowset);
		}
		else
		{
			return $rowset;
		}
	}

	/**
	 * Get the item count for a specific category.
	 *
	 * @param  int		$category_id		The category identifier
	 * @param  string	$sql_where			The SQL WHERE statement
	 * @return int							The item count
	 * @access public
	 */
	public function get_item_count($category_id, $sql_where = '')
	{
		$sql = 'SELECT COUNT(item_id) as count
				FROM ' . $this->items_table . ' i,
					' . $this->categories_table . ' c
				WHERE i.category_id = c.category_id
					AND i.category_id = ' . (int) $category_id . $sql_where . $this->get_sql_where_active('i.');
		$result = $this->db->sql_query($sql);
		$count = $this->db->sql_fetchfield('count');
		$this->db->sql_freeresult($result);

		return (int) $count;
	}

	/**
	 * Get the item types for a specific category.
	 *
	 * @param  int		$category_id		The category identifier
	 * @return array						The item types
	 * @access public
	 */
	public function get_item_types($category_id)
	{
		$types = [];

		$sql = 'SELECT item_type
				FROM ' . $this->items_table . '
				WHERE category_id = ' . (int) $category_id . '
					' . $this->get_sql_where_active() . '
				GROUP BY item_type';
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$types[] = $row['item_type'];
		}
		$this->db->sql_freeresult($result);

		return $types;
	}

	/**
	 * Delete item(s).
	 *
	 * @param  int		$category_id		The category identifier
	 * @param  int		$item_id			The item identifier
	 * @return int							Whether or not items were deleted
	 * @access public
	 */
	public function delete_items($category_id, $item_id)
	{
		$sql = 'DELETE FROM ' . $this->items_table . '
				WHERE ' . ($category_id ? 'category_id = ' . (int) $category_id : 'item_id = ' . (int) $item_id);
		$this->db->sql_query($sql);

		return (int) $this->db->sql_affectedrows();
	}

	/**
	 * Move an item.
	 *
	 * @param  int		$item_id			The item identifier
	 * @param  int		$order				The new item order
	 * @return bool							Whether or not the item was moved
	 * @access public
	 */
	public function move($item_id, $order)
	{
		$item_id = (int) $item_id;
		$order = (int) $order;

		$entity	= $this->get_entity()->load($item_id);
		$lower	= $entity->get_order() > $order;

		$min = $lower ? $order - 1 : $entity->get_order();
		$max = $lower ? $entity->get_order() : $order + 1;

		$sql = 'UPDATE ' . $this->items_table . '
				SET item_order = ' . $this->db->sql_case('item_id = ' . $entity->get_id(), $order, ($lower ? 'item_order + 1' : 'item_order - 1')) . '
				WHERE category_id = ' . $entity->get_category() . '
					AND (item_id = ' . $entity->get_id() . '
						OR item_order BETWEEN ' . $min . ' AND ' . $max . ')';
		$this->db->sql_query($sql);

		return (bool) $this->db->sql_affectedrows();
	}

	/**
	 * Get and assign specific items to the template.
	 *
	 * @param  string	$mode				The specific mode
	 * @param  int		$limit				The amount of items to assign
	 * @param  int		$category_id		The category identifier
	 * @param  bool		$assign				Whether or not to assign template variables
	 * @return entity[]
	 * @access public
	 */
	public function assign_specific_items($mode, $limit, $category_id = 0, $assign = true)
	{
		$items = [];

		$sql_array = [
			'SELECT'	=> 'i.*, c.category_slug',
			'FROM'		=> [
				$this->items_table		=> 'i',
				$this->categories_table	=> 'c',
			],
			'WHERE'		=> 'i.category_id = c.category_id' .
				($category_id ? ' AND i.category_id = ' . (int) $category_id : '') .
				$this->get_sql_where_active('i.') .
				$this->get_sql_where_available('i.'),
		];

		switch ($mode)
		{
			case 'featured':
				$sql_array['ORDER_BY'] = 'i.item_featured_start DESC, i.item_featured_until DESC';
				$sql_array['WHERE'] .= ' AND i.item_sale_until < ' . time() . '
										AND i.item_featured_start <> 0 AND i.item_featured_until <> 0
										AND (' . time() . ' BETWEEN i.item_featured_start AND i.item_featured_until)';
			break;

			case 'sale':
				$sql_array['ORDER_BY'] = 'i.item_sale_start DESC, i.item_sale_until DESC';
				$sql_array['WHERE'] .= ' AND i.item_featured_until < ' . time() . '
										AND i.item_sale_start <> 0 AND i.item_sale_until <> 0
										AND (' . time() . ' BETWEEN i.item_sale_start AND i.item_sale_until)';
			break;

			case 'featured_sale':
				$sql_array['ORDER_BY'] = 'i.item_sale_start DESC, i.item_featured_start DESC';
				$sql_array['WHERE'] .= ' AND i.item_featured_start <> 0 AND i.item_featured_until <> 0
										AND (' . time() . ' BETWEEN i.item_featured_start AND i.item_featured_until)
										AND i.item_sale_start <> 0 AND i.item_sale_until <> 0
										AND (' . time() . ' BETWEEN i.item_sale_start AND i.item_sale_until)';
			break;

			case 'recent':
				$sql_array['ORDER_BY'] = 'i.item_create_time DESC';
			break;

			case 'limited':
				$sql_array['ORDER_BY'] = 'i.item_stock ASC';
				$sql_array['WHERE'] .= ' AND i.item_stock <> 0 AND i.item_stock_unlimited = 0';
			break;

			case 'random':
				$sql_array['ORDER_BY'] = $this->aps_dbal->random();
			break;

			case 'purchases':
				$sql_array['ORDER_BY'] = 'i.item_purchases DESC';
			break;

			case 'available':
				$sql_array['ORDER_BY'] = 'i.item_available_start DESC, i.item_available_until DESC';
				$sql_array['WHERE'] .= ' AND i.item_available_start <> 0 AND i.item_available_until <> 0
										AND (' . time() . ' BETWEEN i.item_available_start AND i.item_available_until)';
			break;
		}

		$sql_array['ORDER_BY'] = !empty($sql_array['ORDER_BY']) ? $sql_array['ORDER_BY'] : 'i.item_order ASC';

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query_limit($sql, (int) $limit);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$item = $this->get_entity()->import($row);

			if ($assign)
			{
				$this->template->assign_block_vars("ass_{$mode}", $this->get_variables($item));
			}

			$items[$item->get_id()] = $item;
		}
		$this->db->sql_freeresult($result);

		return $items;
	}

	/**
	 * Get and assign related items to the template
	 *
	 * @param  entity	$item				The item entity
	 * @return void
	 * @access public
	 */
	public function assign_related_items(entity $item)
	{
		$item_ids = $item->get_related_items();

		if (empty($item_ids))
		{
			$sql = 'SELECT i.*, c.category_slug, ABS(CAST(i.item_order as SIGNED) - ' . $item->get_order() . ') as distance
					FROM ' . $this->items_table . ' i,
						' . $this->categories_table . ' c
					WHERE i.category_id = c.category_id
						AND i.category_id = ' . $item->get_category() . '
						AND i.item_id <> ' . $item->get_id() .
						$this->get_sql_where_active('i.') .
						$this->get_sql_where_available('i.') . '
					ORDER BY distance ASC';
			$result = $this->db->sql_query_limit($sql, 4);
			$rowset = (array) $this->db->sql_fetchrowset($result);
			$this->db->sql_freeresult($result);

			usort($rowset, function($a, $b) {
				return $a['item_order'] - $b['item_order'];
			});
		}
		else
		{
			$rowset = $this->get_items_by_id($item_ids, false);
		}

		foreach ($rowset as $row)
		{
			$entity = $this->get_entity()->import($row);

			$this->template->assign_block_vars('ass_related', $this->get_variables($entity));
		}
	}

	/**
	 * Get item template variables.
	 *
	 * @param  entity	$item				The item entity
	 * @param  string	$prefix				The variables prefix
	 * @param  bool		$prepend			Whether or not "statuses" should be prepended
	 * @param  int		$index				The item index
	 * @return array						The template variables
	 * @access public
	 */
	public function get_variables(entity $item, $prefix = '', $prepend = true, $index = 1)
	{
		$bool = $prepend ? 'S_' : '';

		return [
			"{$prefix}ID"				=> $item->get_id(),
			"{$prefix}TITLE"			=> $item->get_title(),
			"{$prefix}SLUG"				=> $item->get_slug(),
			"{$prefix}DESC"				=> $item->get_desc(),
			"{$prefix}DESC_HTML"		=> $item->get_desc_for_display(),
			"{$prefix}ICON"				=> $item->get_icon(),
			"{$prefix}PRICE"			=> $item->get_price(),
			"{$prefix}BACKGROUND"		=> $item->get_background(),
			"{$prefix}BACKGROUND_SRC"	=> $item->get_background() ? $this->get_background_path($item->get_background()) : '',
			"{$prefix}IMAGES"			=> $item->get_images(),
			"{$prefix}IMAGES_SRC"		=> $item->get_images() ? array_map([$this, 'get_background_path'], $item->get_images()) : [],
			"{$prefix}RELATED_ITEMS"	=> $item->get_related_items(),

			"{$prefix}COUNT"			=> $item->get_count(),
			"{$prefix}STACK"			=> $item->get_stack(),
			"{$prefix}STOCK"			=> $item->get_stock(),
			"{$prefix}STOCK_INITIAL"	=> !$item->get_stock_unlimited() ? $item->get_stock() + $item->get_purchases() : false,
			"{$prefix}STOCK_THRESHOLD"	=> $item->get_stock_threshold(),
			"{$prefix}PURCHASES"		=> $item->get_purchases(),

			"{$prefix}CREATE_TIME"		=> $item->get_create_time(),
			"{$prefix}EDIT_TIME"		=> $item->get_edit_time(),

			"{$prefix}GIFT_PRICE"		=> $item->get_gift_price(),
			"{$prefix}GIFT_PERCENTAGE"	=> $item->get_gift_percentage(),
			"{$prefix}GIFT_TYPE"		=> $item->get_gift_type(),

			"{$prefix}SALE_PRICE"		=> $item->get_sale_price(),
			"{$prefix}SALE_DIFF"		=> $item->get_sale_price() ? $item->get_price() - $item->get_sale_price() : 0,
			"{$prefix}SALE_PCT"			=> $item->get_sale_price() && $item->get_price() ? round(($item->get_price() - $item->get_sale_price()) / $item->get_price() * 100) : 0,
			"{$prefix}SALE_START_UNIX"	=> $item->get_sale_start(),
			"{$prefix}SALE_UNTIL_UNIX"	=> $item->get_sale_until(),

			"{$prefix}FEATURED_START_UNIX"	=> $item->get_featured_start(),
			"{$prefix}FEATURED_UNTIL_UNIX"	=> $item->get_featured_until(),

			"{$prefix}AVAILABLE_START_UNIX"	=> $item->get_available_start(),
			"{$prefix}AVAILABLE_UNTIL_UNIX"	=> $item->get_available_until(),

			"{$prefix}EXPIRE_STRING"	=> $item->get_expire_string(),
			"{$prefix}EXPIRE_SECONDS"	=> $item->get_expire_seconds(),
			"{$prefix}EXPIRE_WITHIN"	=> $item->get_expire_seconds() ? $this->time->seconds_to_string($item->get_expire_seconds()) : '',
			"{$prefix}EXPIRE_UNIX"		=> $item->get_expire_seconds() ? time() + $item->get_expire_seconds() : 0,
			"{$prefix}DELETE_STRING"	=> $item->get_delete_string(),
			"{$prefix}DELETE_SECONDS"	=> $item->get_delete_seconds(),
			"{$prefix}DELETE_WITHIN"	=> $item->get_delete_seconds() ? $this->time->seconds_to_string($item->get_delete_seconds()) : '',
			"{$prefix}DELETE_UNIX"		=> $item->get_delete_seconds() ? time() + $item->get_delete_seconds() : 0,
			"{$prefix}REFUND_STRING"	=> $item->get_refund_string(),
			"{$prefix}REFUND_SECONDS"	=> $item->get_refund_seconds(),
			"{$prefix}REFUND_WITHIN"	=> $item->get_refund_seconds() ? $this->time->seconds_to_string($item->get_refund_seconds()) : '',
			"{$prefix}REFUND_UNIX"		=> $item->get_refund_seconds() ? time() + $item->get_refund_seconds() : 0,

			"{$bool}{$prefix}ACTIVE"			=> $item->get_active(),
			"{$bool}{$prefix}AVAILABLE"			=> $this->is_available($item),
			"{$bool}{$prefix}CONFLICT"			=> $item->get_conflict(),
			"{$bool}{$prefix}FEATURED"			=> $this->is_featured($item),
			"{$bool}{$prefix}GIFT"				=> $item->get_gift(),
			"{$bool}{$prefix}GIFT_ONLY"			=> $item->get_gift_only(),
			"{$bool}{$prefix}RELATED_ENABLED"	=> $item->get_related_enabled(),
			"{$bool}{$prefix}SALE"				=> $this->on_sale($item),
			"{$bool}{$prefix}STOCK_UNLIMITED"	=> $item->get_stock_unlimited(),
			"{$bool}{$prefix}OUT_OF_STOCK"		=> !$item->get_stock_unlimited() && !$item->get_stock(),

			"U_{$prefix}ACTIVATE"		=> $item->get_category_slug() ? $this->router->inventory($item->get_category_slug(), $item->get_slug(), $index, 'activate') : '',
			"U_{$prefix}DELETE"			=> $item->get_category_slug() ? $this->router->inventory($item->get_category_slug(), $item->get_slug(), $index, 'delete') : '',
			"U_{$prefix}GIFT"			=> $item->get_category_slug() ? $this->router->gift($item->get_category_slug(), $item->get_slug()) : '',
			"U_{$prefix}INVENTORY"		=> $item->get_category_slug() ? $this->router->inventory($item->get_category_slug(), $item->get_slug(), $index) : '',
			"U_{$prefix}PURCHASE"		=> $item->get_category_slug() ? $this->router->purchase($item->get_category_slug(), $item->get_slug()) : '',
			"U_{$prefix}REFUND"			=> $item->get_category_slug() ? $this->router->inventory($item->get_category_slug(), $item->get_slug(), $index, 'refund') : '',
			"U_{$prefix}VIEW"			=> $item->get_category_slug() ? $this->router->item($item->get_category_slug(), $item->get_slug()) : '',
		];
	}

	/**
	 * Check whether or not an item is available.
	 *
	 * @param  entity	$item			The item entity
	 * @return bool						Whether or not the item is available
	 * @access public
	 */
	public function is_available(entity $item)
	{
		return $item->get_available_start() ? $this->time->within($item->get_available_start(), $item->get_available_until()) : true;
	}

	/**
	 * Check whether or not an item is featured.
	 *
	 * @param  entity	$item			The item entity
	 * @return bool						Whether or not the item is featured
	 * @access public
	 */
	public function is_featured(entity $item)
	{
		return $item->get_featured_start() ? $this->time->within($item->get_featured_start(), $item->get_featured_until()) : false;
	}

	/**
	 * Check whether or not an item is on sale.
	 *
	 * @param  entity	$item			The item entity
	 * @return bool						Whether or not the item is on sale
	 * @access public
	 */
	public function on_sale(entity $item)
	{
		return $item->get_sale_start() ? $this->time->within($item->get_sale_start(),$item->get_sale_until()) : false;
	}

	/**
	 * Get an item background image path.
	 *
	 * @param  string	$background		The item background image
	 * @param  bool		$root			Whether or not to include the phpBB root path
	 * @return string					The item background image path
	 * @access public
	 */
	public function get_background_path($background, $root = true)
	{
		return $this->path_helper->update_web_root_path($this->files->set_mode('images')->get_path($background, $root));
	}

	/**
	 * Get an item background image absolute path.
	 *
	 * @param  string	$background		The item background image
	 * @return string					The item background image absolute path
	 * @access public
	 */
	public function get_absolute_background_path($background)
	{
		$background = $this->path_helper->remove_web_root_path($background);

		if (strpos($background, '.') === 0)
		{
			$background = substr($background, 1);
		}

		return generate_board_url() . $background;
	}

	/**
	 * Get the item active SQL WHERE statement
	 *
	 * @param  string	$alias			The table alias
	 * @return string					The SQL WHERE statement
	 * @access protected
	 */
	protected function get_sql_where_active($alias = '')
	{
		$sql_where = '';

		if (!$this->auth->acl_get('u_ass_can_view_inactive_items'))
		{
			$sql_where .= " AND {$alias}item_active = 1";

			if ($alias)
			{
				$sql_where .= ' AND c.category_active = 1';
			}
		}

		return $sql_where;
	}

	/**
	 * Get the item availability SQL WHERE statement
	 *
	 * @param  string	$alias			The table alias
	 * @return string					The SQL WHERE statement
	 * @access protected
	 */
	protected function get_sql_where_available($alias = '')
	{
		$time = time();

		return " AND (({$alias}item_available_start = 0 AND {$alias}item_available_until = 0)
			OR ({$time} BETWEEN {$alias}item_available_start AND {$alias}item_available_until))";
	}
}
