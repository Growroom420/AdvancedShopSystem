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

/**
 * phpBB Studio - Advanced Shop System: ACP Overview controller
 */
class acp_overview_controller
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\config\db_text */
	protected $config_text;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbbstudio\ass\operator\item */
	protected $operator_item;

	/** @var \phpbb\textformatter\s9e\parser */
	protected $parser;

	/** @var \phpbb\textformatter\s9e\renderer */
	protected $renderer;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user_loader */
	protected $user_loader;

	/** @var \phpbb\textformatter\s9e\utils */
	protected $utils;

	/** @var string Categories table */
	protected $categories_table;

	/** @var string Items table */
	protected $items_table;

	/** @var string Logs table */
	protected $logs_table;

	/** @var string Custom form action */
	protected $u_action;

	/**
	 * Constructor.
	 *
	 * @param  \phpbb\config\config					$config					Config object
	 * @param  \phpbb\config\db_text				$config_text			Config text object
	 * @param  \phpbb\db\driver\driver_interface	$db						Database object
	 * @param  \phpbb\language\language				$language				Language object
	 * @param  \phpbbstudio\ass\operator\item		$operator_item			Item operator object
	 * @param  \phpbb\textformatter\s9e\parser		$parser					Text formatter parser object
	 * @param  \phpbb\textformatter\s9e\renderer	$renderer				Text formatter renderer object
	 * @param  \phpbb\request\request				$request				Request object
	 * @param  \phpbb\template\template				$template				Template object
	 * @param  \phpbb\user_loader					$user_loader			User loader object
	 * @param  \phpbb\textformatter\s9e\utils		$utils					Text formatter utilities object
	 * @param  string								$categories_table		Categories table
	 * @param  string								$items_table			Items table
	 * @param  string								$logs_table				Logs table
	 * @return void
	 * @access public
	 */
	public function __construct(
		\phpbb\config\config $config,
		\phpbb\config\db_text $config_text,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\language\language $language,
		\phpbbstudio\ass\operator\item $operator_item,
		\phpbb\textformatter\s9e\parser $parser,
		\phpbb\textformatter\s9e\renderer $renderer,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user_loader $user_loader,
		\phpbb\textformatter\s9e\utils $utils,
		$categories_table,
		$items_table,
		$logs_table
	)
	{
		$this->config			= $config;
		$this->config_text		= $config_text;
		$this->db				= $db;
		$this->language			= $language;
		$this->operator_item	= $operator_item;
		$this->parser			= $parser;
		$this->renderer			= $renderer;
		$this->request			= $request;
		$this->template			= $template;
		$this->user_loader		= $user_loader;
		$this->utils			= $utils;

		$this->categories_table	= $categories_table;
		$this->items_table		= $items_table;
		$this->logs_table		= $logs_table;
	}

	/**
	 * Handle and display the "Overview" ACP mode.
	 *
	 * @return void
	 * @access public
	 */
	public function overview()
	{
		$this->language->add_lang(['ass_acp_common', 'ass_common'], 'phpbbstudio/ass');

		$action = $this->request->variable('action', '', true);
		$submit = $this->request->is_set_post('submit');

		$notes = $this->config_text->get('ass_admin_notes');

		if ($action === 'notes')
		{
			if ($submit)
			{
				$notes = $this->parser->parse($this->request->variable('notes', '', true));

				$this->config_text->set('ass_admin_notes', $notes);
			}
			else
			{
				$this->template->assign_vars([
					'NOTES_EDIT'	=> $this->utils->unparse($notes),
					'S_NOTES'		=> true,
				]);
			}
		}

		$item_modes = [
			'featured', 'featured_coming', 'sale', 'sale_coming',
			'low_stock', 'low_sellers', 'top_sellers', 'recent',
		];

		foreach ($item_modes as $mode)
		{
			$items = $this->get_items($mode);

			foreach ($items as $item)
			{
				$this->template->assign_block_vars($mode, $this->operator_item->get_variables($item));
			}
		}

		foreach ($this->get_recent() as $row)
		{
			$item = $this->operator_item->get_entity()->import($row);

			$this->template->assign_block_vars('purchases', array_merge(
				$this->operator_item->get_variables($item),
				['PURCHASE_TIME'	=> (int) $row['log_time']]
			));
		}

		$buyers = $this->get_users('buyers');
		$gifters = $this->get_users('gifters');
		$spenders = $this->get_users('spenders');

		$this->user_loader->load_users(array_merge(array_keys($buyers), array_keys($gifters)));

		$users = [
			'buyers'	=> $buyers,
			'gifters'	=> $gifters,
			'spenders'	=> $spenders,
		];

		foreach ($users as $user_mode => $users_array)
		{
			foreach ($users_array as $user_id => $count)
			{
				$this->template->assign_block_vars($user_mode, [
					'NAME'		=> $this->user_loader->get_username($user_id, 'full'),
					'AVATAR'	=> $this->user_loader->get_avatar($user_id),
					'COUNT'		=> $count,
				]);
			}
		}

		$this->template->assign_vars([
			'COUNTS'			=> $this->get_counts(),
			'NOTES'				=> $notes ? $this->renderer->render($notes) : '',

			'GIFTING_ENABLED'	=> (bool) $this->config['ass_gift_enabled'],
			'NO_IMAGE_ICON'		=> (string) $this->config['ass_no_image_icon'],
			'SHOP_ACTIVE'		=> (bool) $this->config['ass_active'],
			'SHOP_ENABLED'		=> (bool) $this->config['ass_enabled'],

			'U_ACTION'			=> $this->u_action,
			'U_NOTES'			=> $this->u_action . '&action=notes',
		]);
	}

	/**
	 * Get items for a specific mode.
	 *
	 * @param  string		$mode		The item mode (featured|sale|etc..)
	 * @return array					Item entities
	 * @access protected
	 */
	protected function get_items($mode)
	{
		$sql_array = [
			'SELECT'	=> 'i.*',
			'FROM'		=> [$this->items_table => 'i'],
			'WHERE'		=> $this->get_sql_where($mode),
			'ORDER_BY'	=> $this->get_sql_order($mode),
		];

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query_limit($sql, 5);
		$rowset = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		return $this->operator_item->get_entities($rowset);
	}

	/**
	 * Get recent items.
	 *
	 * @return array					Item entities
	 * @access protected
	 */
	protected function get_recent()
	{
		$sql = 'SELECT i.*, l.log_time
				FROM ' . $this->logs_table . ' l,
					' . $this->items_table . ' i
				WHERE i.item_id = l.item_id
					AND l.item_purchase = 1
				ORDER BY l.log_time DESC';
		$result = $this->db->sql_query_limit($sql, 5);
		$rowset = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		return (array) $rowset;
	}

	/**
	 * Get users for a specific mode.
	 *
	 * @param  string		$mode		The mode (buyers|gifters|spenders)
	 * @return array					User rows
	 * @access protected
	 */
	protected function get_users($mode)
	{
		$users = [];

		switch ($mode)
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
		$result = $this->db->sql_query_limit($sql, 5);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$users[(int) $row['user_id']] = $row['count'];
		}
		$this->db->sql_freeresult($result);

		return (array) $users;
	}

	/**
	 * Get counts for various things.
	 *
	 * @return array					Array of counts
	 * @access protected
	 */
	protected function get_counts()
	{
		$counts = [
			'categories'	=> (int) $this->db->get_row_count($this->categories_table),
			'items'			=> (int) $this->db->get_row_count($this->items_table),
		];

		$sql = 'SELECT COUNT(i.item_id) as count
				FROM ' . $this->items_table . ' i
				WHERE ' . $this->get_sql_where('featured');
		$result = $this->db->sql_query_limit($sql , 1);
		$counts['featured'] = (int) $this->db->sql_fetchfield('count');
		$this->db->sql_freeresult($result);

		$sql = 'SELECT COUNT(i.item_id) as count
				FROM ' . $this->items_table . ' i
				WHERE ' . $this->get_sql_where('sale');
		$result = $this->db->sql_query_limit($sql , 1);
		$counts['sale'] = (int) $this->db->sql_fetchfield('count');
		$this->db->sql_freeresult($result);

		$sql = 'SELECT SUM(item_purchases) as count
				FROM ' . $this->items_table;
		$result = $this->db->sql_query_limit($sql , 1);
		$counts['purchases'] = (int) $this->db->sql_fetchfield('count');
		$this->db->sql_freeresult($result);

		$sql = 'SELECT SUM(points_sum) as count
				FROM ' . $this->logs_table;
		$result = $this->db->sql_query_limit($sql , 1);
		$counts['spent'] = (double) $this->db->sql_fetchfield('count');
		$this->db->sql_freeresult($result);

		$sql = 'SELECT COUNT(item_conflict) as count
				FROM ' . $this->items_table . '
				WHERE item_conflict = 1';
		$result = $this->db->sql_query_limit($sql , 1);
		$counts['errors'] = (double) $this->db->sql_fetchfield('count');
		$this->db->sql_freeresult($result);

		return $counts;
	}

	/**
	 * Get the SQL WHERE statement for a specific mode
	 *
	 * @param  string		$mode
	 * @return string
	 * @access protected
	 */
	protected function get_sql_where($mode)
	{
		switch ($mode)
		{
			case 'low_stock':
				return 'i.item_stock_unlimited <> 1';

			case 'featured':
				return 'i.item_sale_start = 0
					AND i.item_featured_start <> 0 
					AND i.item_featured_until <> 0
					AND (' . time() . ' BETWEEN i.item_featured_start AND i.item_featured_until)';

			case 'featured_coming':
				return 'i.item_featured_start <> 0 
					AND i.item_featured_until <> 0
					AND i.item_featured_start > ' . time();

			case 'sale':
				return 'i.item_featured_start = 0
					AND i.item_sale_start <> 0 
					AND i.item_sale_until <> 0
					AND (' . time() . ' BETWEEN i.item_sale_start AND item_sale_until)';

			case 'sale_coming':
				return 'i.item_sale_start <> 0 
					AND i.item_sale_until <> 0
					AND i.item_sale_start > ' . time();

			default:
				return '';
		}
	}

	/**
	 * Get the SQL ORDER BY statement for a specific mode.
	 *
	 * @param  string		$mode
	 * @return string
	 * @access protected
	 */
	protected function get_sql_order($mode)
	{
		switch ($mode)
		{
			case 'low_stock':
				return 'i.item_stock ASC, i.item_title ASC';

			case 'low_sellers':
				return 'i.item_purchases ASC, i.item_title ASC';

			case 'top_sellers':
				return 'i.item_purchases DESC, i.item_title ASC';

			case 'recent':
				return 'i.item_create_time DESC';

			case 'featured':
				return 'i.item_featured_until ASC, i.item_title ASC';

			case 'featured_coming':
				return 'i.item_featured_start ASC, i.item_title ASC';

			case 'sale':
				return 'i.item_sale_until ASC, i.item_title ASC';

			case 'sale_coming':
				return 'i.item_sale_start ASC, i.item_title ASC';

			default:
				return 'i.item_title ASC';
		}
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
