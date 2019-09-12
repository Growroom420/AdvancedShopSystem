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
 * phpBB Studio - Advanced Shop System: Category entity
 */
class category implements category_interface
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\textformatter\s9e\parser */
	protected $parser;

	/** @var \phpbb\textformatter\s9e\renderer */
	protected $renderer;

	/** @var \phpbb\textformatter\s9e\utils */
	protected $utils;

	/** @var string Categories table */
	protected $categories_table;

	/** @var array Category data */
	protected $data;

	/**
	 * Constructor.
	 *
	 * @param  \phpbb\db\driver\driver_interface	$db					Database object
	 * @param  \phpbb\textformatter\s9e\parser		$parser				Text formatter parser object
	 * @param  \phpbb\textformatter\s9e\renderer	$renderer			Text formatter renderer object
	 * @param  \phpbb\textformatter\s9e\utils		$utils				Text formatter utilities object
	 * @param  string								$categories_table	Categories table
	 * @return void
	 * @access public
	 */
	public function __construct(
		\phpbb\db\driver\driver_interface $db,
		\phpbb\textformatter\s9e\parser $parser,
		\phpbb\textformatter\s9e\renderer $renderer,
		\phpbb\textformatter\s9e\utils $utils,
		$categories_table
	)
	{
		$this->db				= $db;
		$this->parser			= $parser;
		$this->renderer			= $renderer;
		$this->utils			= $utils;

		$this->categories_table	= $categories_table;
	}

	/**
	 * {@inheritDoc}
	 */
	public function load($id, $slug = '')
	{
		$where = $id <> 0 ? 'category_id = ' . (int) $id : "category_slug = '" . $this->db->sql_escape($slug) . "'";

		$sql = 'SELECT * FROM ' . $this->categories_table . ' WHERE ' . $where;
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
		if (empty($this->data['category_id']))
		{
			throw new runtime_exception('ASS_ERROR_NOT_EXISTS');
		}

		$data = array_diff_key($this->data, ['category_id' => null]);

		$sql = 'UPDATE ' . $this->categories_table . '
				SET ' . $this->db->sql_build_array('UPDATE', $data) . '
				WHERE category_id = ' . $this->get_id();
		$this->db->sql_query($sql);

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function insert()
	{
		if (!empty($this->data['category_id']))
		{
			throw new runtime_exception('ASS_ERROR_ALREADY_EXISTS');
		}

		$sql = 'SELECT COALESCE(MAX(category_order), 0) as category_order FROM ' . $this->categories_table;
		$result = $this->db->sql_query($sql);
		$order = (int) $this->db->sql_fetchfield('category_order');
		$this->db->sql_freeresult($result);

		$this->data['category_order'] = ++$order;

		$sql = 'INSERT INTO ' . $this->categories_table . ' ' . $this->db->sql_build_array('INSERT', $this->data);
		$this->db->sql_query($sql);

		$this->data['category_id'] = (int) $this->db->sql_nextid();

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_id()
	{
		return isset($this->data['category_id']) ? (int) $this->data['category_id'] : 0;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_title()
	{
		return isset($this->data['category_title']) ? (string) $this->data['category_title'] : '';
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

		$this->data['category_title'] = $title;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_slug()
	{
		return isset($this->data['category_slug']) ? (string) $this->data['category_slug'] : '';
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
			$sql = 'SELECT category_title
					FROM ' . $this->categories_table . "
					WHERE category_slug = '" . $this->db->sql_escape($slug) . "'
						AND category_id <> " . $this->get_id();
			$result = $this->db->sql_query_limit($sql, 1);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if ($row !== false)
			{
				throw new runtime_exception('ASS_ERROR_NOT_UNIQUE', ['SLUG', $row['category_title']]);
			}
		}

		$this->data['category_slug'] = $slug;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_icon()
	{
		return isset($this->data['category_icon']) ? (string) $this->data['category_icon'] : '';
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_icon($icon)
	{
		$icon = (string) $icon;

		if ($icon === '')
		{
			throw new runtime_exception('ASS_ERROR_TOO_SHORT', ['ICON', 0, 0]);
		}

		$this->data['category_icon'] = $icon;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_desc_for_display()
	{
		return isset($this->data['category_desc']) ? (string) $this->renderer->render($this->data['category_desc']) : '';
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_desc()
	{
		return isset($this->data['category_desc']) ? (string) $this->utils->unparse($this->data['category_desc']) : '';
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_desc($desc)
	{
		$desc = (string) $desc;

		$desc = $this->parser->parse($desc);

		$this->data['category_desc'] = $desc;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_active()
	{
		return isset($this->data['category_active']) ? (bool) $this->data['category_active'] : true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_active($active)
	{
		$active = (bool) $active;

		$this->data['category_active'] = $active;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_order()
	{
		return isset($this->data['category_order']) ? (int) $this->data['category_order'] : 0;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_order($order)
	{
		$order = (int) $order;

		$this->data['category_order'] = $order;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_conflicts()
	{
		return isset($this->data['item_conflicts']) ? (int) $this->data['item_conflicts'] : 0;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_conflicts($conflicts)
	{
		$conflicts = (int) $conflicts;

		$this->data['item_conflicts'] = $conflicts;

		return $this;
	}
}
