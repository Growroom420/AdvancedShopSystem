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
use phpbbstudio\ass\entity\category as entity;
use phpbbstudio\ass\exceptions\shop_exception;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * phpBB Studio - Advanced Shop System: Category operator
 */
class category
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var ContainerInterface */
	protected $container;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbbstudio\ass\helper\router */
	protected $router;

	/** @var string Categories table */
	protected $categories_table;

	/**
	 * Constructor.
	 *
	 * @param  \phpbb\auth\auth						$auth				Auth object
	 * @param  ContainerInterface					$container			Service container object
	 * @param  \phpbb\db\driver\driver_interface	$db					Database object
	 * @param  \phpbbstudio\ass\helper\router		$router				Router helper object
	 * @param  string								$categories_table	Categories table
	 * @return void
	 * @access public
	 */
	public function __construct(
		\phpbb\auth\auth $auth,
		ContainerInterface $container,
		\phpbb\db\driver\driver_interface $db,
		\phpbbstudio\ass\helper\router $router,
		$categories_table
	)
	{
		$this->auth				= $auth;
		$this->container		= $container;
		$this->db				= $db;
		$this->router			= $router;

		$this->categories_table	= $categories_table;
	}

	/**
	 * Get a category entity.
	 *
	 * @return entity|object				The category entity
	 * @access public
	 */
	public function get_entity()
	{
		return $this->container->get('phpbbstudio.ass.entity.category');
	}

	/**
	 * Get loaded category entities for a rowset.
	 *
	 * @param  array	$rowset				The rowset retrieved from the categories table
	 * @return array						Category entities
	 * @access public
	 */
	public function get_entities(array $rowset)
	{
		$categories = [];

		foreach ($rowset as $row)
		{
			$categories[(int) $row['category_id']] = $this->get_entity()->import($row);
		}

		return (array) $categories;
	}

	/**
	 * Load a category entity from a slug.
	 *
	 * @param  string	$category_slug		The category slug
	 * @return entity						The category entity
	 * @throws shop_exception
	 * @access public
	 */
	public function load_entity($category_slug)
	{
		$category = $this->get_entity();

		try
		{
			$category->load(0, $category_slug);
		}
		catch (runtime_exception $e)
		{
			throw new shop_exception(404, 'ASS_ERROR_NOT_FOUND_CATEGORY');
		}

		if (!$category->get_active() && !$this->auth->acl_get('u_ass_can_view_inactive_shop'))
		{
			throw new shop_exception(403, 'ASS_ERROR_NOT_ACTIVE_CATEGORY');
		}

		return $category;
	}

	/**
	 * Get all (active) category entities.
	 *
	 * @param  bool		$only_active		Whether or not to only load active categories
	 * @return array						Category entities
	 * @access public
	 */
	public function get_categories($only_active = false)
	{
		$sql = 'SELECT * 
				FROM ' . $this->categories_table . ' 
				' . ($only_active && !$this->auth->acl_get('u_ass_can_view_inactive_shop') ? 'WHERE category_active = 1' : '') . '
				ORDER BY category_order ASC';
		$result = $this->db->sql_query($sql);
		$rowset = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		return $this->get_entities($rowset);
	}

	/**
	 * Get category entities from identifiers.
	 *
	 * @param  array	$ids				The category identifiers
	 * @return array						Category entities
	 * @access public
	 */
	public function get_categories_by_id(array $ids)
	{
		$sql = 'SELECT *
				FROM ' . $this->categories_table . '
				WHERE ' . $this->db->sql_in_set('category_id', array_unique($ids), false, true) . '
				ORDER BY category_order ASC';
		$result = $this->db->sql_query($sql);
		$rowset = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		return $this->get_entities($rowset);
	}

	/**
	 * Delete a category.
	 *
	 * @param  int		$category_id		The category identifier
	 * @return bool							Whether or not the category was deleted
	 * @access public
	 */
	public function delete_category($category_id)
	{
		$sql = 'DELETE FROM ' . $this->categories_table . '
				WHERE category_id = ' . (int) $category_id;
		$this->db->sql_query($sql);

		return (bool) $this->db->sql_affectedrows();
	}

	/**
	 * Move a category.
	 *
	 * @param  int		$category_id		The category identifier
	 * @param  int		$order				The new category order
	 * @return bool							Whether or not the category was moved
	 * @access public
	 */
	public function move($category_id, $order)
	{
		$category_id = (int) $category_id;
		$order = (int) $order;

		$entity	= $this->get_entity()->load($category_id);
		$lower	= $entity->get_order() > $order;

		$min = $lower ? $order - 1 : $entity->get_order();
		$max = $lower ? $entity->get_order() : $order + 1;

		$sql = 'UPDATE ' . $this->categories_table . '
				SET category_order = ' . $this->db->sql_case('category_id = ' . $entity->get_id(), $order, ($lower ? 'category_order + 1' : ' category_order - 1')) . '
				WHERE category_id = ' . $entity->get_id() . '
					OR category_order BETWEEN ' . $min . ' AND ' . $max;
		$this->db->sql_query($sql);

		return (bool) $this->db->sql_affectedrows();
	}

	/**
	 * Get category template variables.
	 *
	 * @param  entity	$category				The category entity
	 * @param  string	$prefix					The variables prefix
	 * @param  bool		$prepend				Whether or not "statuses" and "URLs" should be prepended
	 * @return array							The template variables
	 * @access public
	 */
	public function get_variables(entity $category, $prefix = '', $prepend = true)
	{
		$bool = $prepend ? 'S_' : '';
		$link = $prepend ? 'U_' : '';

		return [
			"{$prefix}TITLE"			=> $category->get_title(),
			"{$prefix}SLUG"				=> $category->get_slug(),
			"{$prefix}DESC"				=> $category->get_desc(),
			"{$prefix}DESC_HTML"		=> $category->get_desc_for_display(),
			"{$prefix}ICON"				=> $category->get_icon(),
			"{$prefix}CONFLICTS"		=> $category->get_conflicts(),

			"{$bool}{$prefix}ACTIVE"	=> $category->get_active(),

			"{$link}{$prefix}VIEW"		=> $category->get_id() ? $this->router->category($category->get_slug()) : '',
		];
	}
}
