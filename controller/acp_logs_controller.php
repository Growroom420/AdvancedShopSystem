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

use phpbbstudio\ass\entity\category;
use phpbbstudio\ass\entity\item;
use phpbbstudio\ass\items\type\item_type;

/**
 * phpBB Studio - Advanced Shop System: ACP Logs controller
 */
class acp_logs_controller
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbbstudio\ass\items\manager */
	protected $items_manager;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbbstudio\ass\helper\log */
	protected $log;

	/** @var \phpbb\log\log */
	protected $log_phpbb;

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

	/** @var \phpbb\user */
	protected $user;

	/** @var string Custom form action */
	protected $u_action;

	/**
	 * Constructor.
	 *
	 * @param  \phpbb\config\config					$config				Config object
	 * @param  \phpbbstudio\ass\items\manager		$items_manager		Items manager object
	 * @param  \phpbb\language\language				$language			Language object
	 * @param  \phpbbstudio\ass\helper\log			$log				Log helper object
	 * @param  \phpbb\log\log						$log_phpbb			Log object
	 * @param  \phpbbstudio\ass\operator\category	$operator_cat		Category operator object
	 * @param  \phpbbstudio\ass\operator\item		$operator_item		Item operator object
	 * @param  \phpbb\pagination					$pagination			Pagination object
	 * @param  \phpbb\request\request				$request			Request object
	 * @param  \phpbb\template\template				$template			Template object
	 * @param  \phpbb\user							$user				User object
	 * @return void
	 * @access public
	 */
	public function __construct(
		\phpbb\config\config $config,
		\phpbbstudio\ass\items\manager $items_manager,
		\phpbb\language\language $language,
		\phpbbstudio\ass\helper\log $log,
		\phpbb\log\log $log_phpbb,
		\phpbbstudio\ass\operator\category $operator_cat,
		\phpbbstudio\ass\operator\item $operator_item,
		\phpbb\pagination $pagination,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user
	)
	{
		$this->config			= $config;
		$this->items_manager	= $items_manager;
		$this->language			= $language;
		$this->log				= $log;
		$this->log_phpbb		= $log_phpbb;
		$this->operator_cat		= $operator_cat;
		$this->operator_item	= $operator_item;
		$this->pagination		= $pagination;
		$this->request			= $request;
		$this->template			= $template;
		$this->user				= $user;
	}

	/**
	 * Handle and display the "Logs" ACP mode.
	 *
	 * @return void
	 * @access public
	 */
	public function logs()
	{
		$this->language->add_lang(['ass_acp_common', 'ass_common'], 'phpbbstudio/ass');

		$show_array = [
			'all'		=> ['title' => 'ASS_ALL',			'sql' => ''],
			'use'		=> ['title' => 'ASS_USAGES',		'sql' => 'l.item_purchase = 0'],
			'buy'		=> ['title' => 'ASS_PURCHASES',		'sql' => 'l.item_purchase = 1 AND l.recipient_id = 0'],
			'given'		=> ['title' => 'ASS_GIFTS_GIVEN',	'sql' => 'l.item_purchase = 1 AND l.recipient_id <> 0'],
		];
		$sort_array = [
			'time'		=> ['title' => 'TIME',				'sql' => 'l.log_time'],
			'price'		=> ['title' => 'ASS_ITEM_PRICE',	'sql' => 'l.points_sum'],
			'item'		=> ['title' => 'ASS_ITEM_TITLE',	'sql' => 'i.item_title'],
			'category'	=> ['title' => 'ASS_CATEGORY_TITLE', 'sql' => 'c.category_title'],
			'recipient'	=> ['title' => 'ASS_RECIPIENT_NAME', 'sql' => 'recipient_name'],
		];
		$dir_array = [
			'desc'		=> ['title' => 'DESCENDING', 'sql' => 'DESC'],
			'asc'		=> ['title' => 'ASCENDING', 'sql' => 'ASC'],
		];

		$page = $this->request->variable('page', 1);
		$show = $this->request->variable('display', 'all', true);
		$sort = $this->request->variable('sort', 'time', true);
		$dir = $this->request->variable('direction', 'desc', true);

		$show = in_array($show, array_keys($show_array)) ? $show : 'all';
		$sort = in_array($sort, array_keys($sort_array)) ? $sort : 'time';
		$dir = in_array($dir, array_keys($dir_array)) ? $dir : 'desc';

		$delete_mark = $this->request->variable('del_marked', false, false, \phpbb\request\request_interface::POST);
		$delete_all	= $this->request->variable('del_all', false, false, \phpbb\request\request_interface::POST);
		$marked		= $this->request->variable('mark', [0]);

		$log_action = $this->u_action . "&display={$show}&sort={$sort}&direction={$dir}";

		if (($delete_mark || $delete_all))
		{
			if (confirm_box(true))
			{
				$this->log->delete($delete_all, $marked);

				$l_delete = $delete_all ? 'ALL' : (count($marked) > 1 ? 'ENTRIES' : 'ENTRY');

				$this->log_phpbb->add('admin', $this->user->data['user_id'], $this->user->ip, "LOG_ACP_ASS_LOG_DELETED_{$l_delete}");

				trigger_error($this->language->lang("ACP_ASS_LOG_DELETED_{$l_delete}") . adm_back_link($log_action . "&page{$page}"));
			}
			else
			{
				confirm_box(false, $this->language->lang('CONFIRM_OPERATION'), build_hidden_fields([
					'del_marked'	=> $delete_mark,
					'del_all'		=> $delete_all,
					'mark'			=> $marked,
				]));

				redirect($log_action . "&page{$page}");
			}
		}

		$sql_where = $show_array[$show]['sql'];
		$sql_order = $sort_array[$sort]['sql'];
		$sql_dir = $dir_array[$dir]['sql'];

		$limit = (int) $this->config['ass_logs_per_page'];
		$start = ($page - 1) * $limit;

		$total = $this->log->get_user_logs_count($sql_where);
		$rowset = $this->log->get_user_logs($sql_where, $sql_order, $sql_dir, $limit, $start);

		$categories	= $this->operator_cat->get_categories_by_id(array_column($rowset, 'category_id'));
		$items		= $this->operator_item->get_items_by_id(array_column($rowset, 'item_id'));

		foreach ($rowset as $row)
		{
			$category_id = (int) $row['category_id'];
			$item_id = (int) $row['item_id'];

			/** @var category $category */
			$category = !empty($categories[$category_id]) ? $categories[$category_id] : null;

			/** @var item $item */
			$item = !empty($items[$item_id]) ? $items[$item_id] : null;

			/** @var item_type $type */
			$type = $item ? $this->items_manager->get_type($item->get_type()) : null;

			$this->template->assign_block_vars('ass_logs', [
				'CATEGORY_TITLE'	=> $category ? $category->get_title() : $this->language->lang('ASS_UNAVAILABLE_CATEGORY'),
				'ITEM_TITLE'		=> $item ? $item->get_title() : $this->language->lang('ASS_UNAVAILABLE_ITEM'),

				'LOG_ACTION'		=> $type ? $this->language->lang($type->get_language('log')) : $this->language->lang('ASS_UNAVAILABLE_' . (!$item ? 'ITEM' : 'TYPE')),
				'LOG_ID'			=> $row['log_id'],
				'LOG_IP'			=> $row['log_ip'],
				'LOG_TIME'			=> $this->user->format_date($row['log_time']),

				'POINTS_NEW'		=> $row['points_new'],
				'POINTS_OLD'		=> $row['points_old'],
				'POINTS_SUM'		=> -$row['points_sum'],

				'RECIPIENT'			=> $row['recipient_id'] ? get_username_string('full', $row['recipient_id'], $row['recipient_name'], $row['recipient_colour']) : '',
				'USER'				=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),

				'S_PURCHASE'		=> $row['item_purchase'],
				'S_GIFT_RECEIVE'	=> $row['recipient_id'] != 0,
			]);
		}

		$this->pagination->generate_template_pagination($log_action, 'pagination', 'page', $total, $limit, $start);

		$this->template->assign_vars([
			'SORT_DISPLAY'			=> $show,
			'SORT_DISPLAY_ARRAY'	=> $show_array,
			'SORT_SORT'				=> $sort,
			'SORT_SORT_ARRAY'		=> $sort_array,
			'SORT_DIR'				=> $dir,
			'SORT_DIR_ARRAY'		=> $dir_array,

			'TOTAL_LOGS'			=> $this->language->lang('TOTAL_LOGS', $total),

			'U_ACTION'				=> $this->u_action,
		]);
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
