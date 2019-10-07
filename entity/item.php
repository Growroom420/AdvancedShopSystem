<?php
/**
 *
 * phpBB Studio - Advanced Shop System. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbstudio\ass\entity;

use phpbb\exception\runtime_exception;

/**
 * phpBB Studio - Advanced Shop System: Item entity
 */
class item implements item_interface
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\textformatter\s9e\parser */
	protected $parser;

	/** @var \phpbb\textformatter\s9e\renderer */
	protected $renderer;

	/** @var \phpbbstudio\ass\helper\time */
	protected $time;

	/** @var \phpbb\textformatter\s9e\utils */
	protected $utils;

	/** @var string Items table */
	protected $items_table;

	/** @var array Item data */
	protected $data;

	/** @var double Maximum value for DECIMAL:14 */
	const DECIMAL_14 = 999999999999.99;

	/** @var int Maximum value for INT:3 */
	const INT_3 = 999;

	/** @var int Maximum value for ULINT */
	const ULINT = 4294967295;

	/**
	 * Constructor.
	 *
	 * @param  \phpbb\config\config					$config			Config object
	 * @param  \phpbb\db\driver\driver_interface	$db				Database object
	 * @param  \phpbb\textformatter\s9e\parser		$parser			Text formatter parser object
	 * @param  \phpbb\textformatter\s9e\renderer	$renderer		Text formatter renderer object
	 * @param  \phpbbstudio\ass\helper\time			$time			Time helper object
	 * @param  \phpbb\textformatter\s9e\utils		$utils			Text formatter utilities object
	 * @param  string								$items_table	Items table
	 * @return void
	 * @access public
	 */
	public function __construct(
		\phpbb\config\config $config,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\textformatter\s9e\parser $parser,
		\phpbb\textformatter\s9e\renderer $renderer,
		\phpbbstudio\ass\helper\time $time,
		\phpbb\textformatter\s9e\utils $utils,
		$items_table
	)
	{
		$this->config		= $config;
		$this->db			= $db;
		$this->parser		= $parser;
		$this->renderer		= $renderer;
		$this->time			= $time;
		$this->utils		= $utils;

		$this->items_table	= $items_table;
	}

	/**
	 * {@inheritDoc}
	 */
	public function load($id, $slug = '', $category_id = 0)
	{
		$where = ($id <> 0) ? 'item_id = ' . (int) $id : 'category_id = ' . (int) $category_id . " AND item_slug = '" . $this->db->sql_escape($slug) . "'";

		$sql = 'SELECT * FROM ' . $this->items_table . ' WHERE ' . $where;
		$result = $this->db->sql_query($sql);
		$this->data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($this->data === false)
		{
			throw new runtime_exception('ASS_ERROR_NOT_FOUND');
		}

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function import(array $data)
	{
		$this->data = $data;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function save()
	{
		if (empty($this->data['item_id']))
		{
			throw new runtime_exception('ASS_ERROR_NOT_EXISTS');
		}

		$data = array_diff_key($this->data, ['item_id' => null, 'category_slug' => null]);

		$data['item_edit_time'] = time();

		$sql = 'UPDATE ' . $this->items_table . '
				SET ' . $this->db->sql_build_array('UPDATE', $data) . '
				WHERE item_id = ' . $this->get_id();
		$this->db->sql_query($sql);

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function insert()
	{
		if (!empty($this->data['item_id']))
		{
			throw new runtime_exception('ASS_ERROR_ALREADY_EXISTS');
		}

		$sql = 'SELECT COALESCE(MAX(item_order), 0) as item_order FROM ' . $this->items_table;
		$result = $this->db->sql_query($sql);
		$order = (int) $this->db->sql_fetchfield('item_order');
		$this->db->sql_freeresult($result);

		$this->data['item_order'] = ++$order;
		$this->data['item_create_time'] = time();

		$sql = 'INSERT INTO ' . $this->items_table . ' ' . $this->db->sql_build_array('INSERT', $this->data);
		$this->db->sql_query($sql);

		$this->data['item_id'] = (int) $this->db->sql_nextid();

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_id()
	{
		return isset($this->data['item_id']) ? (int) $this->data['item_id'] : 0;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_category()
	{
		return isset($this->data['category_id']) ? (int) $this->data['category_id'] : 0;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_category($category_id)
	{
		$category_id = (int) $category_id;

		$this->data['category_id'] = $category_id;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_category_slug()
	{
		return isset($this->data['category_slug']) ? (string) $this->data['category_slug'] : '';
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_category_slug($slug)
	{
		$slug = (string) $slug;

		$this->data['category_slug'] = $slug;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_title()
	{
		return isset($this->data['item_title']) ? (string) $this->data['item_title'] : '';
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_title($title)
	{
		$title = (string) $title;

		if ($title === '')
		{
			throw new runtime_exception('ASS_ERROR_TOO_SHORT', ['TITLE', 0, 0]);
		}

		if (($length = utf8_strlen($title)) > 255)
		{
			throw new runtime_exception('ASS_ERROR_TOO_LONG', ['TITLE', 255, $length]);
		}

		$this->data['item_title'] = $title;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_slug()
	{
		return isset($this->data['item_slug']) ? (string) $this->data['item_slug'] : '';
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_slug($slug)
	{
		$slug = (string) $slug;

		if ($slug === '')
		{
			throw new runtime_exception('ASS_ERROR_TOO_SHORT', ['SLUG', 0, 0]);
		}

		if (($length = utf8_strlen($slug)) > 255)
		{
			throw new runtime_exception('ASS_ERROR_TOO_LONG', ['SLUG', 255, $length]);
		}

		// Route should not contain any unexpected special characters
		if (!preg_match('/^[^!"#$%&*\'()+,.\/\\\\:;<=>?@\\[\\]^`{|}~ ]*$/', $slug))
		{
			throw new runtime_exception('ASS_ERROR_ILLEGAL_CHARS', ['SLUG']);
		}

		// Routes must be unique
		if (!$this->get_id() || ($this->get_id() && $this->get_slug() !== '' && $this->get_slug() !== $slug))
		{
			$sql = 'SELECT item_title
					FROM ' . $this->items_table . "
					WHERE item_slug = '" . $this->db->sql_escape($slug) . "'
						AND item_id <> " . $this->get_id() . '
						AND category_id = ' . $this->get_category();
			$result = $this->db->sql_query_limit($sql, 1);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if ($row !== false)
			{
				throw new runtime_exception('ASS_ERROR_NOT_UNIQUE', ['SLUG', $row['item_title']]);
			}
		}

		$this->data['item_slug'] = $slug;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_icon()
	{
		return isset($this->data['item_icon']) ? (string) $this->data['item_icon'] : '';
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_icon($icon)
	{
		$icon = (string) $icon;

		$this->data['item_icon'] = $icon;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_desc_for_display()
	{
		return isset($this->data['item_desc']) ? (string) $this->renderer->render($this->data['item_desc']) : '';
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_desc()
	{
		return isset($this->data['item_desc']) ? (string) $this->utils->unparse($this->data['item_desc']) : '';
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_desc($desc)
	{
		$desc = (string) $desc;

		$desc = $this->parser->parse($desc);

		$this->data['item_desc'] = $desc;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_active()
	{
		return isset($this->data['item_active']) ? (bool) $this->data['item_active'] : true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_active($active)
	{
		$active = (bool) $active;

		$this->data['item_active'] = $active;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_order()
	{
		return isset($this->data['item_order']) ? (int) $this->data['item_order'] : 0;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_order($order)
	{
		$order = (int) $order;

		$this->data['item_order'] = $order;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_type()
	{
		return isset($this->data['item_type']) ? (string) $this->data['item_type'] : '';
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_type($type)
	{
		$type = (string) $type;

		if ($type === '')
		{
			throw new runtime_exception('ASS_ERROR_TOO_SHORT', ['TYPE', 0, 0]);
		}

		$this->data['item_type'] = $type;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_data()
	{
		return isset($this->data['item_data']) ? (array) json_decode($this->data['item_data'], true) : [];
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_data(array $data)
	{
		$data = (array) $data;

		$this->data['item_data'] = json_encode($data);

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_price()
	{
		return isset($this->data['item_price']) ? (double) $this->data['item_price'] : 0.00;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_price($price)
	{
		$price = (double) $price;

		if ($price < 0)
		{
			throw new runtime_exception('ASS_ERROR_TOO_LOW', ['PRICE', 0, $price]);
		}

		if ($price > self::DECIMAL_14)
		{
			throw new runtime_exception('ASS_ERROR_TOO_HIGH', ['PRICE', self::DECIMAL_14, $price]);
		}

		$this->data['item_price'] = $price;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_count()
	{
		return isset($this->data['item_count']) ? (int) $this->data['item_count'] : 0;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_count($count)
	{
		$count = (int) $count;

		if ($count < 0)
		{
			throw new runtime_exception('ASS_ERROR_TOO_LOW', ['COUNT', 0, $count]);
		}

		if ($count > self::ULINT)
		{
			throw new runtime_exception('ASS_ERROR_TOO_HIGH', ['COUNT', self::ULINT, $count]);
		}

		$this->data['item_count'] = $count;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_stack()
	{
		return isset($this->data['item_stack']) ? (int) $this->data['item_stack'] : 1;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_stack($stack)
	{
		$stack = (int) $stack;

		if ($stack < 1)
		{
			throw new runtime_exception('ASS_ERROR_TOO_LOW', ['STACK', 1, $stack]);
		}

		if ($stack > self::ULINT)
		{
			throw new runtime_exception('ASS_ERROR_TOO_HIGH', ['STACK', self::ULINT, $stack]);
		}

		$this->data['item_stack'] = $stack;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_purchases()
	{
		return isset($this->data['item_purchases']) ? (int) $this->data['item_purchases'] : 0;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_purchases($purchases)
	{
		$purchases = (int) $purchases;

		if ($purchases < 0)
		{
			throw new runtime_exception('ASS_ERROR_TOO_LOW', ['PURCHASES', 0, $purchases]);
		}

		if ($purchases > self::ULINT)
		{
			throw new runtime_exception('ASS_ERROR_TOO_HIGH', ['PURCHASES', self::ULINT, $purchases]);
		}

		$this->data['item_purchases'] = $purchases;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_stock()
	{
		return isset($this->data['item_stock']) ? (int) $this->data['item_stock'] : 0;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_stock($stock)
	{
		$stock = (int) $stock;

		if ($stock < 0)
		{
			throw new runtime_exception('ASS_ERROR_TOO_LOW', ['STOCK', 0, $stock]);
		}

		if ($stock > self::ULINT)
		{
			throw new runtime_exception('ASS_ERROR_TOO_HIGH', ['STOCK', self::ULINT, $stock]);
		}

		$this->data['item_stock'] = $stock;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_stock_threshold()
	{
		return isset($this->data['item_stock_threshold']) ? (int) $this->data['item_stock_threshold'] : 0;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_stock_threshold($threshold)
	{
		$threshold = (int) $threshold;

		if ($threshold < 0)
		{
			throw new runtime_exception('ASS_ERROR_TOO_LOW', ['STOCK_THRESHOLD', 0, $threshold]);
		}

		if ($threshold > self::ULINT)
		{
			throw new runtime_exception('ASS_ERROR_TOO_HIGH', ['STOCK_THRESHOLD', self::ULINT, $threshold]);
		}

		$this->data['item_stock_threshold'] = $threshold;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_stock_unlimited()
	{
		return isset($this->data['item_stock_unlimited']) ? (bool) $this->data['item_stock_unlimited'] : true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_stock_unlimited($unlimited)
	{
		$unlimited = (bool) $unlimited;

		$this->data['item_stock_unlimited'] = $unlimited;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_expire_string()
	{
		return isset($this->data['item_expire_string']) ? (string) $this->data['item_expire_string'] : '';
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_expire_string($string)
	{
		$string = (string) $string;

		$seconds = strtotime($string);

		if ($string !== '' && $seconds === false)
		{
			throw new runtime_exception('ASS_ERROR_INVALID', ['EXPIRE_STRING']);
		}

		$this->set_expire_seconds($seconds);

		$this->data['item_expire_string'] = $string;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_expire_seconds()
	{
		return isset($this->data['item_expire_seconds']) ? (int) $this->data['item_expire_seconds'] : 0;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_expire_seconds($seconds)
	{
		$seconds = (int) $seconds;

		if ($seconds !== 0)
		{
			$seconds = $seconds - time();

			if ($seconds < 0)
			{
				throw new runtime_exception('ASS_ERROR_UNSIGNED', ['EXPIRE_SECONDS']);
			}
		}

		$this->data['item_expire_seconds'] = $seconds;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_delete_string()
	{
		return isset($this->data['item_delete_string']) ? (string) $this->data['item_delete_string'] : '';
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_delete_string($string)
	{
		$string = (string) $string;

		$seconds = strtotime($string);

		if ($string !== '' && $seconds === false)
		{
			throw new runtime_exception('ASS_ERROR_INVALID', ['DELETE_STRING']);
		}

		$this->set_delete_seconds($seconds);

		$this->data['item_delete_string'] = $string;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_delete_seconds()
	{
		return isset($this->data['item_delete_seconds']) ? (int) $this->data['item_delete_seconds'] : 0;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_delete_seconds($seconds)
	{
		$seconds = (int) $seconds;

		if ($seconds !== 0)
		{
			$seconds = $seconds - time();

			if ($seconds < 0)
			{
				throw new runtime_exception('ASS_ERROR_UNSIGNED', ['DELETE_SECONDS']);
			}
		}

		$this->data['item_delete_seconds'] = $seconds;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_refund_string()
	{
		return isset($this->data['item_refund_string']) ? (string) $this->data['item_refund_string'] : '';
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_refund_string($string)
	{
		$string = (string) $string;

		$seconds = strtotime($string);

		if ($string !== '' && $seconds === false)
		{
			throw new runtime_exception('ASS_ERROR_INVALID', ['REFUND_STRING']);
		}

		$this->set_refund_seconds($seconds);

		$this->data['item_refund_string'] = $string;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_refund_seconds()
	{
		return isset($this->data['item_refund_seconds']) ? (int) $this->data['item_refund_seconds'] : 0;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_refund_seconds($seconds)
	{
		$seconds = (int) $seconds;

		if ($seconds !== 0)
		{
			$seconds = $seconds - time();

			if ($seconds < 0)
			{
				throw new runtime_exception('ASS_ERROR_UNSIGNED', ['REFUND_SECONDS']);
			}
		}

		$this->data['item_refund_seconds'] = $seconds;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_gift()
	{
		return isset($this->data['item_gift']) ? (bool) $this->data['item_gift'] : true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_gift($gift)
	{
		$gift = (bool) $gift;

		$this->data['item_gift'] = $gift;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_gift_only()
	{
		return isset($this->data['item_gift_only']) ? (bool) $this->data['item_gift_only'] : false;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_gift_only($gift_only)
	{
		$gift_only = (bool) $gift_only;

		$this->data['item_gift_only'] = $gift_only;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_gift_type()
	{
		return isset($this->data['item_gift_type']) ? (bool) $this->data['item_gift_type'] : true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_gift_type($type)
	{
		$type = (bool) $type;

		$this->data['item_gift_type'] = $type;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_gift_percentage()
	{
		return isset($this->data['item_gift_percentage']) ? (int) $this->data['item_gift_percentage'] : 0;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_gift_percentage($percentage)
	{
		$percentage = (int) $percentage;

		if ($percentage < -100)
		{
			throw new runtime_exception('ASS_ERROR_TOO_LOW', ['PRICE', -100, $percentage]);
		}

		if ($percentage > self::INT_3)
		{
			throw new runtime_exception('ASS_ERROR_TOO_HIGH', ['PRICE', self::INT_3, $percentage]);
		}

		$this->data['item_gift_percentage'] = $percentage;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_gift_price()
	{
		return isset($this->data['item_gift_price']) ? (double) $this->data['item_gift_price'] : 0.00;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_gift_price($price)
	{
		$price = (double) $price;

		if ($price < 0)
		{
			throw new runtime_exception('ASS_ERROR_TOO_LOW', ['GIFT_PRICE', 0, $price]);
		}

		if ($price > self::DECIMAL_14)
		{
			throw new runtime_exception('ASS_ERROR_TOO_HIGH', ['GIFT_PRICE', self::DECIMAL_14, $price]);
		}

		$this->data['item_gift_price'] = $price;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_sale_price()
	{
		return isset($this->data['item_sale_price']) ? (double) $this->data['item_sale_price'] : 0.00;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_sale_price($price)
	{
		$price = (double) $price;

		if ($price < 0)
		{
			throw new runtime_exception('ASS_ERROR_TOO_LOW', ['SALE_PRICE', 0, $price]);
		}

		if ($price > self::DECIMAL_14)
		{
			throw new runtime_exception('ASS_ERROR_TOO_HIGH', ['SALE_PRICE', self::DECIMAL_14, $price]);
		}

		$this->data['item_sale_price'] = $price;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_sale_start()
	{
		return isset($this->data['item_sale_start']) ? (int) $this->data['item_sale_start'] : 0;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_sale_start($time)
	{
		$time = (string) $time;

		if ($time !== '')
		{
			$time = $this->time->create_from_format($time);
		}

		$time = (int) $time;

		$this->data['item_sale_start'] = $time;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_sale_until()
	{
		return isset($this->data['item_sale_until']) ? (int) $this->data['item_sale_until'] : 0;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_sale_until($time)
	{
		$time = (string) $time;

		if ($time !== '')
		{
			$time = $this->time->create_from_format($time);
		}

		$time = (int) $time;

		$this->data['item_sale_until'] = $time;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_featured_start()
	{
		return isset($this->data['item_featured_start']) ? (int) $this->data['item_featured_start'] : 0;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_featured_start($time)
	{
		$time = (string) $time;

		if ($time !== '')
		{
			$tz = date_default_timezone_get();

			date_default_timezone_set($this->config['board_timezone']);

			$time = date_timestamp_get(\DateTime::createFromFormat('d/m/Y H:i', $time));

			date_default_timezone_set($tz);
		}

		$time = (int) $time;

		$this->data['item_featured_start'] = $time;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_featured_until()
	{
		return isset($this->data['item_featured_until']) ? (int) $this->data['item_featured_until'] : 0;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_featured_until($time)
	{
		$time = (string) $time;

		if ($time !== '')
		{
			$tz = date_default_timezone_get();

			date_default_timezone_set($this->config['board_timezone']);

			$time = date_timestamp_get(\DateTime::createFromFormat('d/m/Y H:i', $time));

			date_default_timezone_set($tz);
		}

		$time = (int) $time;

		$this->data['item_featured_until'] = $time;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_available_start()
	{
		return isset($this->data['item_available_start']) ? (int) $this->data['item_available_start'] : 0;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_available_start($time)
	{
		$time = (string) $time;

		if ($time !== '')
		{
			$tz = date_default_timezone_get();

			date_default_timezone_set($this->config['board_timezone']);

			$time = date_timestamp_get(\DateTime::createFromFormat('d/m/Y H:i', $time));

			date_default_timezone_set($tz);
		}

		$time = (int) $time;

		$this->data['item_available_start'] = $time;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_available_until()
	{
		return isset($this->data['item_available_until']) ? (int) $this->data['item_available_until'] : 0;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_available_until($time)
	{
		$time = (string) $time;

		if ($time !== '')
		{
			$tz = date_default_timezone_get();

			date_default_timezone_set($this->config['board_timezone']);

			$time = date_timestamp_get(\DateTime::createFromFormat('d/m/Y H:i', $time));

			date_default_timezone_set($tz);
		}

		$time = (int) $time;

		$this->data['item_available_until'] = $time;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_background()
	{
		return isset($this->data['item_background']) ? (string) $this->data['item_background'] : '';
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_background($background)
	{
		$background = (string) $background;

		$this->data['item_background'] = $background;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_images()
	{
		return isset($this->data['item_images']) ? (array) json_decode($this->data['item_images'], true) : [];
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_images(array $images)
	{
		$images = (array) $images;

		$images = array_filter($images);

		$this->data['item_images'] = json_encode($images);

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_related_enabled()
	{
		return isset($this->data['item_related_enabled']) ? (bool) $this->data['item_related_enabled'] : true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_related_enabled($related)
	{
		$related = (bool) $related;

		$this->data['item_related_enabled'] = $related;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_related_items()
	{
		return isset($this->data['item_related_items']) ? (array) json_decode($this->data['item_related_items'], true) : [];
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_related_items(array $items)
	{
		$items = (array) $items;

		$items = array_filter($items);
		$items = array_unique($items);

		if (($count = count($items)) > 8)
		{
			throw new runtime_exception('ASS_ERROR_TOO_HIGH', ['RELATED_ITEMS', 8, $count]);
		}

		$this->data['item_related_items'] = json_encode($items);

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_create_time()
	{
		return isset($this->data['item_create_time']) ? (int) $this->data['item_create_time'] : 0;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_edit_time()
	{
		return isset($this->data['item_edit_time']) ? (int) $this->data['item_edit_time'] : 0;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_conflict()
	{
		return isset($this->data['item_conflict']) ? (bool) $this->data['item_conflict'] : false;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_conflict($state)
	{
		$state = (bool) $state;

		$this->data['item_conflict'] = $state;

		return $this;
	}
}
