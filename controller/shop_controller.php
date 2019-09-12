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

use Symfony\Component\HttpFoundation\Response;

/**
 * phpBB Studio - Advanced Shop System: Shop controller
 */
class shop_controller
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbbstudio\ass\helper\controller */
	protected $controller;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbbstudio\ass\items\manager */
	protected $items_manager;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbbstudio\ass\operator\category */
	protected $operator_cat;

	/** @var \phpbbstudio\ass\operator\item */
	protected $operator_item;

	/** @var \phpbb\pagination */
	protected $pagination;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/**
	 * Constructor.
	 *
	 * @param  \phpbb\config\config					$config				Config object
	 * @param  \phpbbstudio\ass\helper\controller	$controller			ASS Controller helper object
	 * @param  \phpbb\db\driver\driver_interface	$db					Database object
	 * @param  \phpbb\controller\helper				$helper				Controller helper object
	 * @param  \phpbbstudio\ass\items\manager		$items_manager		Items manager object
	 * @param  \phpbb\language\language				$language			Language object
	 * @param  \phpbbstudio\ass\operator\category	$operator_cat		Category operator object
	 * @param  \phpbbstudio\ass\operator\item		$operator_item		Item operator object
	 * @param  \phpbb\pagination					$pagination			Pagination object
	 * @param  \phpbb\request\request				$request			Request object
	 * @param  \phpbb\template\template				$template			Template object
	 * @return void
	 * @access public
	 */
	public function __construct(
		\phpbb\config\config $config,
		\phpbbstudio\ass\helper\controller $controller,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\controller\helper $helper,
		\phpbbstudio\ass\items\manager $items_manager,
		\phpbb\language\language $language,
		\phpbbstudio\ass\operator\category $operator_cat,
		\phpbbstudio\ass\operator\item $operator_item,
		\phpbb\pagination $pagination,
		\phpbb\request\request $request,
		\phpbb\template\template $template
	)
	{
		$this->config			= $config;
		$this->controller		= $controller;
		$this->db				= $db;
		$this->helper			= $helper;
		$this->items_manager	= $items_manager;
		$this->language			= $language;
		$this->operator_cat		= $operator_cat;
		$this->operator_item	= $operator_item;
		$this->pagination		= $pagination;
		$this->request			= $request;
		$this->template			= $template;
	}

	/**
	 * Display the shop index.
	 *
	 * @return Response
	 * @access public
	 */
	public function shop()
	{
		$this->controller->check_shop();
		$this->controller->create_shop('shop');

		$this->controller->setup_carousel();
		$this->controller->setup_panels();

		$this->operator_item->assign_specific_items('featured', $this->config['ass_panel_featured_limit']);
		$this->operator_item->assign_specific_items('sale', $this->config['ass_panel_sale_limit']);
		$this->operator_item->assign_specific_items('featured_sale', $this->config['ass_panel_featured_sale_limit']);
		$this->operator_item->assign_specific_items('recent', $this->config['ass_panel_recent_limit']);
		$this->operator_item->assign_specific_items('limited', $this->config['ass_panel_limited_limit']);
		$this->operator_item->assign_specific_items('random', $this->config['ass_panel_random_limit']);

		$this->template->assign_vars([
			'SHOP_PANEL_RANDOM_WIDTH'	=> (int) $this->config['ass_panel_random_limit'] % 3 === 0 ? 4 : 3,
		]);

		return $this->helper->render('ass_shop.html', $this->language->lang('ASS_SHOP'));
	}

	/**
	 * Display a shop category.
	 *
	 * @param  string		$category_slug		The category slug
	 * @param  int			$page				The page number
	 * @return Response
	 * @access public
	 */
	public function category($category_slug, $page = 1)
	{
		$this->controller->check_shop();

		$category = $this->operator_cat->load_entity($category_slug);

		$this->controller->create_shop('shop', $category);

		$this->controller->setup_panels();

		$this->template->assign_vars($this->operator_cat->get_variables($category));

		$sql_where	= '';

		$types = [0 => null];
		$type_array = ['ASS_ALL'];

		foreach($this->operator_item->get_item_types($category->get_id()) as $type)
		{
			$types[] = $type;
			$type_array[] = $this->items_manager->get_type($type)->get_language('title');
		}

		$params_array = [
			'above'	=> ['default' => '', 'sql' => 'i.item_price > {VALUE}'],
			'below'	=> ['default' => '', 'sql' => 'i.item_price < {VALUE}'],
			'gift'	=> ['default' => 0, 'sql' => 'i.item_gift = {VALUE}'],
			'sale'	=> ['default' => 0, 'sql' => 'i.item_sale_start < ' . time() . ' AND i.item_sale_until < ' . time()],
			'type'	=> ['default' => 0, 'sql' => 'i.item_type = {VALUE}'],
			'title'	=> ['default' => '', 'sql' => $this->db->sql_lower_text('i.item_title') . ' {VALUE}', 'mb' => true],
		];
		$days_array = [
			0	=> 'ASS_ALL',
			1	=> '1_DAY',
			7	=> '7_DAYS',
			14	=> '2_WEEKS',
			30	=> '1_MONTH',
			90	=> '3_MONTHS',
			180	=> '6_MONTHS',
			365	=> '1_YEAR',
		];
		$sort_array = [
			'order'		=> ['title' => 'ASS_ITEM_ORDER', 'sql' => 'i.item_order'],
			'item'		=> ['title' => 'ASS_ITEM_TITLE', 'sql' => 'i.item_title'],
			'price'		=> ['title' => 'ASS_ITEM_PRICE', 'sql' => 'i.item_price'],
			'stock'		=> ['title' => 'ASS_ITEM_STOCK', 'sql' => 'i.item_stock'],
			'time'		=> ['title' => 'ASS_ITEM_CREATE_TIME', 'sql' => 'i.item_create_time'],
		];
		$dir_array = [
			'desc'		=> ['title' => 'DESCENDING', 'sql' => 'DESC'],
			'asc'		=> ['title' => 'ASCENDING', 'sql' => 'ASC'],
		];

		$days = $this->request->variable('days', 0, false, \phpbb\request\request_interface::GET);
		$sort = $this->request->variable('sort', 'order', true, \phpbb\request\request_interface::GET);
		$dir = $this->request->variable('direction', 'desc', true, \phpbb\request\request_interface::GET);

		$dir = in_array($dir, array_keys($dir_array)) ? $dir : 'asc';
		$sort = in_array($sort, array_keys($sort_array)) ? $sort : 'order';
		$days = in_array($days, array_keys($days_array)) ? $days : 0;
		$time = $days * \phpbbstudio\ass\helper\time::DAY;

		$params = [
			'sort'		=> $sort,
			'direction'	=> $dir,
		];

		if ($time)
		{
			$params['days'] = $days;

			$sql_where .= ' AND i.item_create_time > ' . (time() - $time);
		}

		foreach ($params_array as $key => $param)
		{
			$value = $this->request->variable(
				$key,
				$params_array[$key]['default'],
				!empty($params_array[$key]['mb']),
				\phpbb\request\request_interface::GET
			);

			if (!empty($value))
			{
				$params[$key] = $value;

				$value_sql = $value !== -1 ? $value : 0;

				switch ($key)
				{
					case 'type':
						if (in_array($value, array_keys($type_array)))
						{
							$value_sql = "'" . $types[$value] . "'";
						}
					break;

					case 'title':
						$value_sql = $this->db->sql_like_expression(utf8_strtolower($value_sql) . $this->db->get_any_char());
					break;
				}

				$param_sql = str_replace('{VALUE}', $value_sql, $params_array[$key]['sql']);

				$sql_where .= ' AND ' . $param_sql;

				$this->template->assign_var('SORT_' . utf8_strtoupper($key), $value);
			}
		}

		$sql_dir	= $dir_array[$dir]['sql'];
		$sql_order	= $sort_array[$sort]['sql'];

		$limit = (int) $this->config['ass_items_per_page'];
		$start = ($page - 1) * $limit;

		$total = $this->operator_item->get_item_count($category->get_id());
		$items = $this->operator_item->get_items($category->get_id(), $sql_where, $sql_order, $sql_dir, true, $limit, $start);

		foreach ($items as $item)
		{
			$this->template->assign_block_vars('ass_items', $this->operator_item->get_variables($item));
		}

		$this->pagination->generate_template_pagination([
			'routes' => ['phpbbstudio_ass_category', 'phpbbstudio_ass_category_pagination'],
			'params' => array_merge(['category_slug' => $category->get_slug()], $params),
		], 'shop_pagination', 'page', $total, $limit, $start);

		$this->template->assign_vars([
			'ITEMS_COUNT'		=> $this->language->lang('ASS_ITEMS_COUNT', $total),

			'SORT_DAYS'			=> $days,
			'SORT_DAYS_ARRAY'	=> $days_array,
			'SORT_DIR'			=> $dir,
			'SORT_DIR_ARRAY'	=> $dir_array,
			'SORT_SORT'			=> $sort,
			'SORT_SORT_ARRAY'	=> $sort_array,
			'SORT_TYPE_ARRAY'	=> $type_array,

			'T_PANEL_SIZE'		=> $limit < 8 ? 6 : 3,
		]);

		return $this->helper->render('ass_category.html', $category->get_title());
	}

	/**
	 * Display a shop item.
	 *
	 * @param  string		$category_slug		The category slug
	 * @param  string		$item_slug			The item slug
	 * @return Response
	 * @access public
	 */
	public function item($category_slug, $item_slug)
	{
		$this->controller->check_shop();

		$category = $this->operator_cat->load_entity($category_slug);
		$item = $this->operator_item->load_entity($item_slug, $category->get_slug(), $category->get_id());

		$this->controller->create_shop('shop', $category, $item);

		$this->controller->setup_carousel();
		$this->controller->setup_panels();

		$this->operator_item->assign_related_items($item);

		$this->template->assign_vars($this->operator_item->get_variables($item));

		return $this->helper->render('ass_item.html', $item->get_title());
	}
}
