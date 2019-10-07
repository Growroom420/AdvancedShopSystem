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

use phpbb\exception\runtime_exception;
use phpbbstudio\ass\entity\category;
use phpbbstudio\ass\entity\item;
use phpbbstudio\ass\items\type\item_type;

/**
 * phpBB Studio - Advanced Shop System: ACP Items controller
 */
class acp_items_controller
{
	/** @var \phpbb\cache\driver\driver_interface */
	protected $cache;

	/** @var \phpbbstudio\ass\items\manager */
	protected $items_manager;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \phpbbstudio\ass\operator\category */
	protected $operator_cat;

	/** @var \phpbbstudio\ass\operator\inventory */
	protected $operator_inv;

	/** @var \phpbbstudio\ass\operator\item */
	protected $operator_item;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbbstudio\ass\helper\time */
	protected $time;

	/** @var \phpbb\user */
	protected $user;

	/** @var string phpBB admin path */
	protected $admin_path;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var string php File extension */
	protected $php_ext;

	/** @var string Custom form action */
	protected $u_action;

	/**
	 * Constructor.
	 *
	 * @param  \phpbb\cache\driver\driver_interface		$cache				Cache object
	 * @param  \phpbbstudio\ass\items\manager			$items_manager		Items manager object
	 * @param  \phpbb\language\language					$language			Language object
	 * @param  \phpbb\log\log							$log				Log object
	 * @param  \phpbbstudio\ass\operator\category		$operator_cat		Category operator object
	 * @param  \phpbbstudio\ass\operator\inventory		$operator_inv		Inventory operator object
	 * @param  \phpbbstudio\ass\operator\item			$operator_item		Item operator object
	 * @param  \phpbb\request\request					$request			Request object
	 * @param  \phpbb\template\template					$template			Template object
	 * @param  \phpbbstudio\ass\helper\time				$time				Time helper object
	 * @param  \phpbb\user								$user				User object
	 * @param  string									$admin_path			phpBB admin path
	 * @param  string									$root_path			phpBB root path
	 * @param  string									$php_ext			php File extension
	 * @return void
	 * @access public
	 */
	public function __construct(
		\phpbb\cache\driver\driver_interface $cache,
		\phpbbstudio\ass\items\manager $items_manager,
		\phpbb\language\language $language,
		\phpbb\log\log $log,
		\phpbbstudio\ass\operator\category $operator_cat,
		\phpbbstudio\ass\operator\inventory $operator_inv,
		\phpbbstudio\ass\operator\item $operator_item,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbbstudio\ass\helper\time $time,
		\phpbb\user $user,
		$admin_path,
		$root_path,
		$php_ext
	)
	{
		$this->cache			= $cache;
		$this->items_manager	= $items_manager;
		$this->language			= $language;
		$this->log				= $log;
		$this->operator_cat		= $operator_cat;
		$this->operator_inv		= $operator_inv;
		$this->operator_item	= $operator_item;
		$this->request			= $request;
		$this->template			= $template;
		$this->time				= $time;
		$this->user				= $user;

		$this->admin_path		= $root_path . $admin_path;
		$this->root_path		= $root_path;
		$this->php_ext			= $php_ext;
	}

	/**
	 * Handle and display the "Items" ACP mode.
	 *
	 * @return void
	 * @access public
	 */
	public function items()
	{
		$this->language->add_lang(['ass_acp_common', 'ass_common'], 'phpbbstudio/ass');

		$action			= $this->request->variable('action', '', true);
		$item_id		= $this->request->variable('iid', 0);
		$category_id	= $this->request->variable('cid', 0);

		$s_items = ($item_id || ($category_id && strpos($action, 'cat_') !== 0));

		switch ($action)
		{
			case 'move':
				$id		= $this->request->variable('id', 0);
				$order	= $this->request->variable('order', 0) + 1;

				$s_items ? $this->operator_item->move($id, $order) : $this->operator_cat->move($id, $order);
			break;

			case 'resolve':
				$item		= $this->operator_item->get_entity()->load($item_id);
				$category	= $this->operator_cat->get_entity()->load($item->get_category());

				if (confirm_box(true))
				{
					$item->set_conflict(false)
						->save();

					$category->set_conflicts($category->get_conflicts() - 1)
						->save();

					$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_ACP_ASS_ITEM_RESOLVED', time(), [$item->get_title()]);

					if ($this->request->is_ajax())
					{
						$json_response = new \phpbb\json_response;

						$json_response->send([
							'MESSAGE_TITLE'	=> $this->language->lang('INFORMATION'),
							'MESSAGE_TEXT'	=> $this->language->lang('ACP_ASS_ITEM_RESOLVE_SUCCESS')
						]);
					}

					trigger_error($this->language->lang('ACP_ASS_ITEM_RESOLVE_SUCCESS') . adm_back_link($this->u_action . '&amp;iid=' . $item->get_id()));
				}
				else
				{
					confirm_box(false, 'ACP_ASS_ITEM_RESOLVE', '');

					redirect($this->u_action . '&amp;iid=' . $item->get_id());
				}
			break;

			case 'type':
				$json_response = new \phpbb\json_response;

				$type = $this->items_manager->get_type($this->request->variable('type', '', true));

				if ($type !== null)
				{
					$data = $item_id ? $this->operator_item->get_entity()->load($item_id)->get_data() : [];

					$this->template->set_filenames(['type' => $type->get_acp_template($data)]);

					$this->template->assign_var('U_ACTION', $this->u_action);

					try
					{
						$body = $this->template->assign_display('type');

						$json_response->send([
							'success'	=> true,
							'body'		=> $body,
						]);
					}
					/** @noinspection PhpRedundantCatchClauseInspection */
					catch (\Twig\Error\Error $e)
					{
						$json_response->send([
							'error'			=> true,
							'MESSAGE_TEXT'	=> $e->getMessage(),
							'MESSAGE_TITLE'	=> $this->language->lang('INFORMATION'),
						]);
					}
				}
				else
				{
					$json_response->send([
						'error'			=> true,
						'MESSAGE_TEXT'	=> $this->language->lang('ASS_ITEM_TYPE_NOT_EXIST'),
						'MESSAGE_TITLE'	=> $this->language->lang('INFORMATION'),
					]);
				}
			break;

			case 'cat_delete':
			case 'delete':
				$s_item = $action === 'delete';

				$item		= $s_item ? $this->operator_item->get_entity()->load($item_id) : null;
				$category	= $this->operator_cat->get_entity()->load($s_item ? $item->get_category() : $category_id);

				$l_mode = $s_item ? 'ITEM' : 'CATEGORY';
				$u_mode = $this->u_action . ($s_item ? "&cid={$item->get_category()}" : '');

				if ($s_item)
				{
					$type = $this->items_manager->get_type($item->get_type());

					if ($type !== null && $type->is_admin_authed() === false)
					{
						trigger_error($this->language->lang('ACP_ASS_ITEM_TYPE_NO_AUTH'), E_USER_WARNING);
					}
				}

				if (confirm_box(true))
				{
					if (!$s_item)
					{
						$this->operator_cat->delete_category($category_id);
					}

					$this->operator_item->delete_items($category_id, $item_id);
					$this->operator_inv->delete_items($category_id, $item_id);

					$log_title = $s_item ? $item->get_title() : $category->get_title();

					$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, "LOG_ACP_ASS_{$l_mode}_DELETED", false, [$log_title]);

					$message = $this->language->lang("ACP_ASS_{$l_mode}_DELETE_SUCCESS");

					if (!$this->request->is_ajax())
					{
						$message .= adm_back_link($u_mode);
					}

					trigger_error($message);
				}
				else
				{
					confirm_box(false, "ACP_ASS_{$l_mode}_DELETE", build_hidden_fields([
						'action'	=> $action,
						'cid'		=> $category_id,
						'iid'		=> $item_id,
					]));

					redirect($u_mode);
				}
			break;

			case 'cat_add':
			case 'cat_edit':
			case 'add':
			case 'copy':
			case 'edit':
				$s_edit = in_array($action, ['edit', 'cat_edit']);
				$s_item = in_array($action, ['add', 'copy', 'edit']);

				$entity = $s_item ? $this->operator_item->get_entity() : $this->operator_cat->get_entity();

				if ($s_edit)
				{
					try
					{
						$entity->load(($s_item ? $item_id : $category_id));
					}
					catch (runtime_exception $e)
					{
						$message = $this->language->lang($e->getMessage(), $this->language->lang($s_item ? 'ASS_ITEM' : 'ASS_CATEGORY'));
						trigger_error($message . adm_back_link($this->u_action), E_USER_WARNING);
					}
				}
				else if ($s_item)
				{
					// Copy an item
					if ($action === 'copy')
					{
						$data = $this->operator_item->get_items_by_id([$item_id], false);

						if (empty($data[0]))
						{
							$message = $this->language->lang('ASS_ERROR_NOT_FOUND', $this->language->lang('ASS_ITEM'));
							trigger_error($message . adm_back_link($this->u_action), E_USER_WARNING);
						}

						$data = array_diff_key($data[0], array_flip([
							'item_id',
							'item_title',
							'item_slug',
							'item_purchases',
							'item_stock',
							'item_create_time',
							'item_edit_time',
							'item_conflict',
							'category_slug',
						]));

						$entity->import($data);

						$action = 'add';
						$item_id = 0;
						$category_id = $entity->get_category();
					}
					else
					{
						$entity->set_category($category_id);
					}
				}

				if ($s_item && $s_edit)
				{
					$type = $this->items_manager->get_type($entity->get_type());

					if ($type !== null && $type->is_admin_authed() === false)
					{
						$message = $this->language->lang('ACP_ASS_ITEM_TYPE_NO_AUTH');
						$u_back = $this->u_action . ($entity->get_category() ? '&cid=' . $entity->get_category() : '');

						trigger_error($message . adm_back_link($u_back), E_USER_WARNING);
					}
				}

				$this->add_edit_data($entity, $s_item);

				$this->template->assign_vars([
					'S_ASS_ADD'		=> !$s_edit,
					'S_ASS_EDIT'	=> $s_edit,
				]);
			break;
		}

		if ($s_items)
		{
			/** @var item $entity */
			foreach ($this->operator_item->get_items($category_id) as $entity)
			{
				/** @var item_type $type */
				$type = $this->items_manager->get_type($entity->get_type());

				$s_auth = $type !== null ? $type->is_admin_authed() : true;

				$this->template->assign_block_vars('ass_items', [
					'ID'			=> $entity->get_id(),
					'TITLE'			=> $entity->get_title(),
					'SLUG'			=> $entity->get_slug(),
					'ICON'			=> $entity->get_icon(),
					'CONFLICT'		=> $entity->get_conflict(),

					'S_ACTIVE'		=> $entity->get_active(),
					'S_AVAILABLE'	=> $this->operator_item->is_available($entity),
					'S_AUTH'		=> $s_auth,

					'U_DELETE'		=> $s_auth ? "{$this->u_action}&amp;action=delete&iid={$entity->get_id()}" : '',
					'U_COPY'		=> $s_auth ? "{$this->u_action}&amp;action=copy&iid={$entity->get_id()}" : '',
					'U_EDIT'		=> $s_auth ? "{$this->u_action}&amp;action=edit&iid={$entity->get_id()}" : '',
				]);
			}
		}
		else
		{
			/** @var category $entity */
			foreach ($this->operator_cat->get_categories() as $entity)
			{
				$this->template->assign_block_vars('ass_categories', [
					'ID'		=> $entity->get_id(),
					'TITLE'		=> $entity->get_title(),
					'SLUG'		=> $entity->get_slug(),
					'ICON'		=> $entity->get_icon(),
					'CONFLICT'	=> $entity->get_conflicts(),

					'S_ACTIVE'	=> $entity->get_active(),
					'S_AUTH'	=> true,

					'U_DELETE'	=> "{$this->u_action}&amp;action=cat_delete&cid={$entity->get_id()}",
					'U_EDIT'	=> "{$this->u_action}&amp;action=cat_edit&cid={$entity->get_id()}",
					'U_VIEW'	=> "{$this->u_action}&amp;cid={$entity->get_id()}",
				]);
			}
		}

		$this->template->assign_vars([
			'S_ITEMS'		=> $s_items,

			'U_ACTION'		=> $this->u_action . ($action ? "&amp;action=$action" : '') . ($category_id ? "&amp;cid=$category_id" : '') . ($item_id ? "&amp;iid=$item_id" : ''),
			'U_ASS_ADD'		=> $this->u_action . '&amp;action=' . ($s_items ? 'add' : 'cat_add') . ($category_id ? "&amp;cid=$category_id" : ''),
			'U_BACK'		=> $this->u_action,
		]);
	}

	/**
	 * Handle adding and editing an entity.
	 *
	 * @param  item|category	$entity		The entity
	 * @param  bool				$s_item		Whether it's an item or a category
	 * @return void
	 * @access protected
	 */
	protected function add_edit_data($entity, $s_item)
	{
		$errors = [];
		$submit = $this->request->is_set_post('submit');
		$s_edit = (bool) $entity->get_id();
		$l_mode = $s_item ? 'ITEM' : 'CATEGORY';

		$form_key = 'add_edit_categories';
		add_form_key($form_key);

		$data = [
			'active'	=> $this->request->variable('active', false),
			'title'		=> $this->request->variable('title', '', true),
			'slug'		=> $this->request->variable('slug', '', true),
			'icon'		=> $this->request->variable('icon', '', true),
			'desc'		=> $this->request->variable('desc', '', true),
		];

		if ($s_item)
		{
			$data += [
				'type'				=> $this->request->variable('type', '', true),
				'price'				=> $this->request->variable('price', 0.00),
				'stock_unlimited'	=> $this->request->variable('stock_unlimited', false),
				'stock'				=> $this->request->variable('stock', 0),
				'stock_threshold'	=> $this->request->variable('stock_threshold', 0),
				'gift'				=> $this->request->variable('gift', false),
				'gift_only'			=> $this->request->variable('gift_only', false),
				'gift_type'			=> $this->request->variable('gift_type', false),
				'gift_percentage'	=> $this->request->variable('gift_percentage', 0),
				'gift_price'		=> $this->request->variable('gift_price', 0.00),
				'sale_price'		=> $this->request->variable('sale_price', 0.00),
				'sale_start'		=> $this->request->variable('sale_start', ''),
				'sale_until'		=> $this->request->variable('sale_until', ''),
				'featured_start'	=> $this->request->variable('featured_start', ''),
				'featured_until'	=> $this->request->variable('featured_until', ''),
				'available_start'	=> $this->request->variable('available_start', ''),
				'available_until'	=> $this->request->variable('available_until', ''),
				'count'				=> $this->request->variable('count', 0),
				'stack'				=> $this->request->variable('stack', 1),
				'refund_string'		=> $this->request->variable('refund_string', '', true),
				'expire_string'		=> $this->request->variable('expire_string', '', true),
				'delete_string'		=> $this->request->variable('delete_string', '', true),
				'background'		=> $this->request->variable('background', '', true),
				'images'			=> $this->request->variable('images', [''], true),
				'related_enabled'	=> $this->request->variable('related_enabled', false),
				'related_items'		=> $this->request->variable('related_items', [0]),
			];

			$post_variables = $this->request->get_super_global(\phpbb\request\request_interface::POST);

			$data['data'] = isset($post_variables['data']) ? $this->request->escape($post_variables['data'], true) : [];
		}

		if ($submit)
		{
			if (!check_form_key($form_key))
			{
				$errors[] = $this->language->lang('FORM_INVALID');
			}

			foreach ($data as $key => $value)
			{
				try
				{
					$entity->{"set_{$key}"}($value);

					if ($key === 'slug' && $value !== $entity->get_slug())
					{
						$s_purge = true;
					}
				}
				catch (runtime_exception $e)
				{
					$errors[] = $this->get_error_message($e, $l_mode);
				}
			}

			if ($s_item)
			{
				$type = $this->items_manager->get_type($entity->get_type());

				if ($type === null)
				{
					$errors[] = $this->language->lang('ASS_ITEM_TYPE_NOT_EXIST');
				}
				else
				{
					$errors += (array) $type->validate_acp_data($entity->get_data());
				}
			}

			if (empty($errors))
			{
				$function	= $s_edit ? 'save' : 'insert';
				$message	= $s_edit ? "ACP_ASS_{$l_mode}_EDIT_SUCCESS" : "ACP_ASS_{$l_mode}_ADD_SUCCESS";
				$action		= $s_edit ? "LOG_ACP_ASS_{$l_mode}_EDITED" : "LOG_ACP_ASS_{$l_mode}_ADDED";

				try
				{
					$entity->$function();

					$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, $action, false, [$entity->get_title()]);

					if (!empty($s_purge))
					{
						$this->cache->purge();
					}

					$return = $this->u_action . ($s_item ? "&amp;cid={$entity->get_category()}" : '');

					meta_refresh(3, $return);

					trigger_error($this->language->lang($message) . adm_back_link($return));
				}
				catch (runtime_exception $e)
				{
					$errors[] = $this->get_error_message($e, $l_mode);
				}
			}
		}

		$this->generate_bbcodes();

		if ($s_item)
		{
			$type = $this->items_manager->get_type($entity->get_type());

			$this->items_manager->set_types_for_select($entity->get_type());

			$this->template->assign_vars($this->operator_item->get_variables($entity, 'ITEM_', false));

			$this->template->assign_vars([
				'ITEM_HELP_DATA'	=> $this->get_help_data(array_keys($data), $s_edit),

				'T_ITEM_TEMPLATE'	=> $type !== null ? $type->get_acp_template($entity->get_data()) : '',

				'U_ITEM_IMAGE'		=> $this->u_action . '&amp;m=images&amp;action=select_file&amp;image=',
				'U_ITEM_RESOLVE'	=> $this->u_action . '&amp;iid=' . $entity->get_id() . '&amp;action=resolve',
				'U_ITEM_TYPE'		=> $this->u_action . '&amp;iid=' . $entity->get_id() . '&amp;action=type',
				'U_ITEM_ERROR_LOG'	=> append_sid("{$this->admin_path}index.{$this->php_ext}", [
					'i'			=> 'acp_logs',
					'mode'		=> 'admin',
					'keywords'	=> urlencode(htmlspecialchars_decode($entity->get_title())),
				]),
			]);

			$this->set_related_items_for_select($entity->get_id(), $entity->get_related_items());

			if ($s_edit && !$submit && $type === null)
			{
				$errors[] = $this->language->lang('ASS_ITEM_TYPE_NOT_EXIST');
			}
		}
		else
		{
			$this->template->assign_vars($this->operator_cat->get_variables($entity, 'CATEGORY_', false));
		}

		$this->template->assign_vars([
			'ASS_ERRORS'	=> $errors,
			'DATE_FORMAT'	=> $this->time->get_format(),
			'TIMEZONE'		=> $this->time->get_timezone(),
		]);
	}

	/**
	 * Generate BBCodes for a textarea editor.
	 *
	 * @return void
	 * @access protected
	 */
	protected function generate_bbcodes()
	{
		include_once $this->root_path . 'includes/functions_display.' . $this->php_ext;

		$this->language->add_lang('posting');

		display_custom_bbcodes();

		$this->template->assign_vars([
			'S_BBCODE_IMG'		=> true,
			'S_BBCODE_QUOTE'	=> true,
			'S_BBCODE_FLASH'	=> true,
			'S_LINKS_ALLOWED'	=> true,
		]);
	}

	/**
	 * Get a localised error message from a thrown exception.
	 *
	 * @param  runtime_exception	$e		The thrown exception
	 * @param  string				$mode	The mode (ITEM|CATEGORY)
	 * @return string						The localised error message.
	 * @access protected
	 */
	protected function get_error_message(runtime_exception $e, $mode)
	{
		$params = $e->get_parameters();

		$field = array_shift($params);
		$field = $field ? "ACP_ASS_{$mode}_{$field}" : "ASS_{$mode}";

		$params = array_merge([$this->language->lang($field)], $params);

		return $this->language->lang_array($e->getMessage(), $params);
	}

	/**
	 * The item's data keys.
	 *
	 * @param  array		$data		Item's data keys
	 * @param  bool			$s_edit		Whether or not the item is being edited
	 * @return array					Item help data values
	 * @access protected
	 */
	protected function get_help_data(array $data, $s_edit)
	{
		$this->language->add_lang('ass_acp_help', 'phpbbstudio/ass');

		$data = array_filter($data, function($value) {
			return $value !== 'data' && strpos($value, '_until') === false;
		});

		if ($s_edit)
		{
			$data = array_merge($data, ['dates', 'states', 'stock_info', 'sale_info']);
		}

		return $data;
	}

	/**
	 * Assign categories and items to the template for the related items selection.
	 *
	 * @param  int			$item_id	The item identifiers
	 * @param  array		$item_ids	The related items identifiers
	 * @return void
	 * @access protected
	 */
	protected function set_related_items_for_select($item_id, array $item_ids)
	{
		/** @var category $category */
		foreach ($this->operator_cat->get_categories() as $category)
		{
			$this->template->assign_block_vars('categories', array_merge([
				'S_INACTIVE'	=> !$category->get_active(),
			], $this->operator_cat->get_variables($category)));

			/** @var item $item */
			foreach ($this->operator_item->get_items($category->get_id()) as $item)
			{
				if ($item->get_id() === $item_id)
				{
					continue;
				}

				$this->template->assign_block_vars('categories.items', array_merge([
					'S_INACTIVE'	=> !$item->get_active(),
					'S_SELECTED'	=> in_array($item->get_id(), $item_ids),
				], $this->operator_item->get_variables($item)));
			}
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
