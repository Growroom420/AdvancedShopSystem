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

use phpbbstudio\ass\entity\item;
use phpbbstudio\ass\entity\category;
use phpbbstudio\ass\exceptions\shop_exception;
use phpbbstudio\ass\exceptions\shop_item_exception;
use phpbbstudio\ass\items\type\item_type;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * phpBB Studio - Advanced Shop System: Inventory controller
 */
class inventory_controller
{
	/** @var \phpbbstudio\aps\points\distributor */
	protected $aps_distributor;

	/** @var \phpbbstudio\aps\core\functions */
	protected $aps_functions;

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbbstudio\ass\helper\controller */
	protected $controller;

	/** @var \phpbb\controller\helper */
	protected $helper;

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

	/** @var \phpbbstudio\ass\operator\inventory */
	protected $operator_inv;

	/** @var \phpbbstudio\ass\operator\item */
	protected $operator_item;

	/** @var \phpbbstudio\ass\notification\notification */
	protected $notification;

	/** @var \phpbb\pagination */
	protected $pagination;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbbstudio\ass\helper\router */
	protected $router;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbbstudio\ass\helper\time */
	protected $time;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\user_loader */
	protected $user_loader;

	/**
	 * Constructor.
	 *
	 * @param  \phpbbstudio\aps\points\distributor			$aps_distributor	APS Distributor object
	 * @param  \phpbbstudio\aps\core\functions				$aps_functions		APS Functions object
	 * @param  \phpbb\auth\auth								$auth				Auth object
	 * @param  \phpbb\config\config							$config				Config object
	 * @param  \phpbbstudio\ass\helper\controller			$controller			ASS Controller helper object
	 * @param  \phpbb\controller\helper						$helper				Controller helper object
	 * @param  \phpbbstudio\ass\items\manager				$items_manager		Items manager object
	 * @param  \phpbb\language\language						$language			Language object
	 * @param  \phpbbstudio\ass\helper\log					$log				Log helper object
	 * @param  \phpbb\log\log								$log_phpbb			Log object
	 * @param  \phpbbstudio\ass\operator\category			$operator_cat		Category operator object
	 * @param  \phpbbstudio\ass\operator\inventory			$operator_inv		Inventory operator object
	 * @param  \phpbbstudio\ass\operator\item				$operator_item		Item operator object
	 * @param  \phpbbstudio\ass\notification\notification	$notification		Notification helper object
	 * @param  \phpbb\pagination							$pagination			Pagination object
	 * @param  \phpbb\request\request						$request			Request object
	 * @param  \phpbbstudio\ass\helper\router				$router				Router helper object
	 * @param  \phpbb\template\template						$template			Template object
	 * @param  \phpbbstudio\ass\helper\time					$time				Time helper object
	 * @param  \phpbb\user									$user				User object
	 * @param  \phpbb\user_loader							$user_loader		User loader object
	 * @return void
	 * @access public
	 */
	public function __construct(
		\phpbbstudio\aps\points\distributor $aps_distributor,
		\phpbbstudio\aps\core\functions $aps_functions,
		\phpbb\auth\auth $auth,
		\phpbb\config\config $config,
		\phpbbstudio\ass\helper\controller $controller,
		\phpbb\controller\helper $helper,
		\phpbbstudio\ass\items\manager $items_manager,
		\phpbb\language\language $language,
		\phpbbstudio\ass\helper\log $log,
		\phpbb\log\log $log_phpbb,
		\phpbbstudio\ass\operator\category $operator_cat,
		\phpbbstudio\ass\operator\inventory $operator_inv,
		\phpbbstudio\ass\operator\item $operator_item,
		\phpbbstudio\ass\notification\notification $notification,
		\phpbb\pagination $pagination,
		\phpbb\request\request $request,
		\phpbbstudio\ass\helper\router $router,
		\phpbb\template\template $template,
		\phpbbstudio\ass\helper\time $time,
		\phpbb\user $user,
		\phpbb\user_loader $user_loader
	)
	{
		$this->aps_distributor	= $aps_distributor;
		$this->aps_functions	= $aps_functions;
		$this->auth				= $auth;
		$this->config			= $config;
		$this->controller		= $controller;
		$this->helper			= $helper;
		$this->items_manager	= $items_manager;
		$this->language			= $language;
		$this->log				= $log;
		$this->log_phpbb		= $log_phpbb;
		$this->operator_cat		= $operator_cat;
		$this->operator_inv		= $operator_inv;
		$this->operator_item	= $operator_item;
		$this->notification		= $notification;
		$this->pagination		= $pagination;
		$this->request			= $request;
		$this->router			= $router;
		$this->template			= $template;
		$this->time				= $time;
		$this->user				= $user;
		$this->user_loader		= $user_loader;
	}

	/**
	 * Handle the purchase/gift action.
	 *
	 * @param  string		$category_slug		The category slug
	 * @param  string		$item_slug			The item slug
	 * @param  bool			$purchase			Whether it's a purchase or a gift
	 * @return Response
	 * @access public
	 */
	public function purchase($category_slug, $item_slug, $purchase)
	{
		$this->controller->check_shop();

		if (!$this->user->data['is_registered'])
		{
			throw new shop_exception(401, 'ASS_ERROR_NOT_AUTH_PURCHASE');
		}

		$category = $this->operator_cat->load_entity($category_slug);
		$item = $this->operator_item->load_entity($item_slug, $category->get_slug(), $category->get_id());

		$this->template->assign_vars($this->operator_item->get_variables($item));

		if ($purchase && !$this->auth->acl_get('u_ass_can_purchase'))
		{
			throw new shop_exception(403, 'ASS_ERROR_NOT_AUTH_PURCHASE');
		}

		if (!$this->operator_item->is_available($item))
		{
			throw new shop_exception(410, 'ASS_ERROR_NOT_AVAILABLE');
		}

		if (!$purchase)
		{
			if (!$this->auth->acl_get('u_ass_can_gift'))
			{
				throw new shop_exception(403, 'ASS_ERROR_NOT_AUTH_GIFT');
			}

			if (!$item->get_gift())
			{
				throw new shop_exception(400, 'ASS_ERROR_NOT_GIFTABLE');
			}
		}

		if (!$this->operator_inv->check_price($item, $purchase))
		{
			throw new shop_exception(400, 'ASS_ERROR_NOT_ENOUGH_POINTS', [$this->aps_functions->get_name()]);
		}

		if (!$item->get_stock() && !$item->get_stock_unlimited())
		{
			throw new shop_exception(400, 'ASS_ERROR_OUT_OF_STOCK');
		}

		$stack = 0;
		$user_id = 0;
		$username = '';

		if (confirm_box(true) && !$purchase)
		{
			$username = $this->request->variable('username', '', true);
			$user_id = (int) $this->user_loader->load_user_by_username($username);
			$user2 = $this->user_loader->get_user($user_id);

			if ($user_id === ANONYMOUS)
			{
				throw new shop_exception(404, 'NO_USER');
			}

			if ($user_id === (int) $this->user->data['user_id'])
			{
				throw new shop_exception(403, 'ASS_ERROR_NOT_GIFT_SELF');
			}

			$auth2 = new \phpbb\auth\auth;
			$auth2->acl($user2);

			if (!$auth2->acl_get('u_ass_can_receive_gift'))
			{
				throw new shop_exception(403, 'ASS_ERROR_NOT_AUTH_RECEIVE');
			}

			$username = $this->user_loader->get_username($user_id, 'no_profile');
		}

		if ($purchase || (confirm_box(true) && !$purchase))
		{
			$stack = $this->operator_inv->get_inventory_stack($item, $user_id);

			if ($stack >= $item->get_stack())
			{
				$message = $purchase ? 'ASS_ERROR_STACK_LIMIT' : 'ASS_ERROR_STACK_LIMIT_USER';
				$params = $purchase ? [] : [$username];

				throw new shop_exception(409, $message, $params);
			}

			$auth = !empty($auth2) ? $auth2 : $this->auth;

			if ($stack && !$auth->acl_get('u_ass_can_stack'))
			{
				$message = $purchase ? 'ASS_ERROR_STACK_NO_AUTH' : 'ASS_ERROR_STACK_NO_AUTH_USER';
				$params = $purchase ? [] : [$username];

				throw new shop_exception(409, $message, $params);
			}
		}

		if (!$this->request->is_ajax())
		{
			$this->controller->create_shop('shop', $category, $item);
		}

		$l_mode = $purchase ? 'ASS_PURCHASE' : 'ASS_GIFT';

		$this->template->assign_vars([
			'ASS_ITEM_STACK'		=> $stack,
			'ASS_PURCHASE_PRICE'	=> $this->operator_inv->get_price($item, $purchase),
			'S_ASS_PURCHASE'		=> $purchase,
		]);

		if (confirm_box(true))
		{
			$points_new = $this->operator_inv->add_purchase($item, $user_id, $purchase);
			$inventory_id = $this->operator_inv->get_purchase_id();

			$item->set_purchases($item->get_purchases() + 1)
				->set_stock($item->get_stock() - (int) !$item->get_stock_unlimited())
				->save();

			if ($item->get_stock() === $item->get_stock_threshold() && !$item->get_stock_unlimited())
			{
				$this->notification->low_stock($item);
			}

			if (!$purchase)
			{
				$this->notification->gift($item, $user_id, $inventory_id, $stack + 1);

				if ($this->config['allow_privmsg'] && $this->auth->acl_get('u_sendpm'))
				{
					$u_pm = $this->router->regular('ucp', [
						'i'		=> 'pm',
						'mode'	=> 'compose',
						'u'		=> $user_id,
					], true, $this->request->is_ajax());
				}
			}

			$this->log->add($item, true, $this->operator_inv->get_price($item), $user_id);

			$this->template->assign_vars([
				'NEW_USER_POINTS'	=> $points_new,
				'RECIPIENT_NAME'	=> $username,
				'U_SEND_PM'			=> !empty($u_pm) ? $u_pm : '',
			]);

			if ($this->request->is_ajax())
			{
				$this->template->set_filenames([
					'body'	=> 'ass_purchase_ajax.html',
				]);

				$this->template->assign_var('S_PURCHASE_SUCCESS', true);

				return new JsonResponse([
					'MESSAGE_TITLE'	=> $this->language->lang($l_mode),
					'MESSAGE_TEXT'	=> $this->template->assign_display('body'),
					'id'			=> $item->get_id(),
					'points'		=> $this->aps_functions->display_points($points_new, false),
					'stock'			=> !$item->get_stock_unlimited() ? $item->get_stock() : false,
				]);
			}
			else
			{
				return $this->helper->render('ass_purchase.html', $this->language->lang($l_mode));
			}
		}
		else
		{
			$ajax = $this->request->is_ajax() ? '_ajax' : '';
			$body = "ass_purchase{$ajax}.html";

			confirm_box(false, $l_mode, '', $body, $this->helper->get_current_url());

			return new RedirectResponse($this->router->item($category->get_slug(), $item->get_slug()));
		}
	}

	/**
	 * Display the inventory and handle any actions.
	 *
	 * @param  string		$category_slug		The category slug
	 * @param  string		$item_slug			The item slug
	 * @param  int			$index				The item index
	 * @param  string		$action				The action
	 * @return Response
	 * @access public
	 */
	public function inventory($category_slug, $item_slug, $index, $action)
	{
		$index0 = (int) $index - 1;

		$this->controller->check_shop();

		$this->operator_inv->clean_inventory();

		$category	= $category_slug ? $this->operator_cat->load_entity($category_slug) : null;
		$item		= $item_slug ? $this->operator_item->load_entity($item_slug, $category->get_slug(), $category->get_id()) : null;

		$s_category	= $category !== null;
		$s_item		= $item !== null;

		$inventory	= $this->operator_inv->get_inventory($category);

		$cat_ids	= array_column($inventory, 'category_id');
		$item_ids	= array_column($inventory, 'item_id', 'inventory_id');

		$categories	= $this->operator_cat->get_categories_by_id($cat_ids);
		$items		= $this->operator_item->get_items_by_id($item_ids);

		$variables	= [];
		$item_map	= [];

		foreach ($item_ids as $inventory_id => $item_id)
		{
			$item_map[$item_id] = array_keys($item_ids, $item_id);
		}

		if ($s_item && !in_array($item->get_id(), array_keys($items)))
		{
			throw new shop_exception(404, 'ASS_ERROR_NOT_OWNED');
		}

		$this->controller->create_shop('inventory', $category);

		if ($s_category && $s_item && !empty($action))
		{
			$type = $this->items_manager->get_type($item->get_type());

			if ($type === null)
			{
				$this->log_phpbb->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_ACP_ASS_ITEM_TYPE_ERROR', time(), [$category->get_title(), $item->get_title()]);

				throw new shop_item_exception(404, 'ASS_ITEM_TYPE_NOT_EXIST');
			}

			$row = $this->operator_inv->get_inventory_item($item, $index0);

			switch ($action)
			{
				case 'activate':
					$type->set_category($category);
					$type->set_item($item);

					if (confirm_box(true))
					{
						try
						{
							$success = $type->activate($item->get_data());
						}
						catch (shop_item_exception $e)
						{
							$this->log_phpbb->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_ACP_ASS_ITEM_TYPE_ERROR', time(), [$category->get_title(), $item->get_title()]);

							if (!$item->get_conflict())
							{
								if ($this->config['ass_deactivate_conflicts'])
								{
									$item->set_active(false);
								}

								$item->set_conflict(true)
									->save();

								$category->set_conflicts($category->get_conflicts() + 1)
									->save();
							}

							throw $e;
						}

						$message = !empty($success) ? $this->language->lang($type->get_language('success')) : 'Some error message';
						$title = !empty($success) ? $this->language->lang($type->get_language('title')) : $this->language->lang('INFORMATION');

						$count	= (int) $row['use_count'];

						if (!empty($success) && empty($success['file']))
						{
							$count++;
						}

						$limit	= $item->get_count() && $item->get_count() === $count;
						$delete	= !$item->get_delete_seconds() && $limit;

						$file = !empty($success['file']) ? $success['file'] : false;

						if ($file)
						{
							$limit = $item->get_count() && $item->get_count() === ($count + 1);
							$delete = !$item->get_delete_seconds() && $limit;

							$u_file = $this->router->inventory($item->get_category_slug(), $item->get_slug(), $index, 'download', ['hash' => generate_link_hash($file)]);
						}
						else if (!empty($success))
						{
							$data = [
								'use_count'	=> $count,
								'use_time'	=> time(),
							];

							$delete
								? $this->operator_inv->delete($row['inventory_id'])
								: $this->operator_inv->activated($row['inventory_id'], $data);

							$data['use_time'] = $this->user->format_date($data['use_time']);

							$this->log->add($item, false);
						}

						if ($this->request->is_ajax())
						{
							if ($delete)
							{
								$inventory_ids = $item_map[$item->get_id()];

								unset($inventory_ids[array_search($row['inventory_id'], $inventory_ids)]);

								if (!empty($inventory_ids))
								{
									$inventory_ids = array_values($inventory_ids);

									// Get the first inventory row
									$row = $inventory[$inventory_ids[0]];

									$stack = $this->get_stack_info($inventory_ids, $index0);

									$vars = $this->get_inventory_variables($category, $item, $row, $stack);

									if (!empty($vars['BACKGROUND_SRC']))
									{
										// Fix the incorrect web root path
										$vars['BACKGROUND_SRC'] = $this->operator_item->get_absolute_background_path($vars['BACKGROUND_SRC']);
									}

									$this->template->set_filenames(['item' => '@phpbbstudio_ass/ass_item_inventory.html']);
									$this->template->assign_vars(['item' => $vars]);
									$next_item = $this->template->assign_display('item');
								}
							}

							return new JsonResponse([
								'MESSAGE_TITLE'	=> $title,
								'MESSAGE_TEXT'	=> $message,
								'success'		=> $success,
								'delete'		=> $delete,
								'limit'			=> $limit ? $this->language->lang('ASS_ITEM_USE_REACHED') : false,
								'id'			=> $item->get_id(),
								'data'			=> !empty($data) ? $data : false,
								'file'			=> !empty($u_file) ? $u_file . '&force=1' : false,
								'item'			=> !empty($next_item) ? $next_item : false,
								'index'			=> $index,
							]);
						}
						else
						{
							if (!empty($u_file))
							{
								return new RedirectResponse($u_file);
							}

							return $this->helper->message($message);
						}
					}
					else
					{
						confirm_box(false, $type->get_language(), '', $type->get_confirm_template($item->get_data()), $this->helper->get_current_url());

						return new RedirectResponse($this->router->inventory($item->get_category_slug(), $item->get_slug(), $index));
					}
				break;

				case 'download':
					$hash = $this->request->variable('hash', '', true);

					$data = $type->activate($item->get_data());

					if (empty($data['file']))
					{
						break;
					}

					$file = $data['file'];

					if (check_link_hash($hash, $file) && $this->request->is_set('force', \phpbb\request\request_interface::GET))
					{
						$count	= (int) $row['use_count'] + 1;
						$limit	= $item->get_count() && $item->get_count() === $count;
						$delete	= !$item->get_delete_seconds() && $limit;

						$data = [
							'use_count'	=> $count,
							'use_time'	=> time(),
						];

						$delete
							? $this->operator_inv->delete($row['inventory_id'])
							: $this->operator_inv->activated($row['inventory_id'], $data);

						$data['use_time'] = $this->user->format_date($data['use_time']);

						$this->log->add($item, false);

						return $this->download($file);
					}
					else if ($this->request->is_set('hash', \phpbb\request\request_interface::GET))
					{
						$u_file = $this->router->inventory($item->get_category_slug(), $item->get_slug(), $index, 'download', ['hash' => $hash, 'force' => true]);

						$this->template->assign_var('U_DOWNLOAD_FILE', $u_file);
					}
				break;

				case 'delete':
				case 'refund':
					$l_action = 'ASS_' . utf8_strtoupper($action);

					if (confirm_box(true))
					{
						if ($action === 'refund')
						{
							if (!empty($row['use_count']))
							{
								throw new shop_exception(403, 'ASS_ERROR_NOT_REFUND');
							}

							$points = $this->aps_functions->equate_points($this->user->data['user_points'], $row['purchase_price']);
							$points = $this->aps_functions->boundaries($points);
							$points = $this->aps_functions->format_points($points);

							$this->aps_distributor->update_points($points);

							$item->set_purchases($item->get_purchases() - 1)
								->set_stock($item->get_stock() + (int) !$item->get_stock_unlimited())
								->save();
						}

						$this->operator_inv->delete($row['inventory_id']);

						if ($this->request->is_ajax())
						{
							$inventory_ids = $item_map[$item->get_id()];

							unset($inventory_ids[array_search($row['inventory_id'], $inventory_ids)]);

							if (!empty($inventory_ids))
							{
								$inventory_ids = array_values($inventory_ids);

								// Get the first inventory row
								$row = $inventory[$inventory_ids[0]];

								$stack = $this->get_stack_info($inventory_ids, $index0);

								$vars = $this->get_inventory_variables($category, $item, $row, $stack);

								if (!empty($vars['BACKGROUND_SRC']))
								{
									// Fix the incorrect web root path
									$vars['BACKGROUND_SRC'] = $this->operator_item->get_absolute_background_path($vars['BACKGROUND_SRC']);
								}

								$this->template->set_filenames(['item' => '@phpbbstudio_ass/ass_item_inventory.html']);
								$this->template->assign_vars(['item' => $vars]);
								$next_item = $this->template->assign_display('item');
							}

							return new JsonResponse([
								'MESSAGE_TITLE'	=> $this->language->lang($l_action),
								'MESSAGE_TEXT'	=> $this->language->lang($l_action . '_SUCCESS'),
								'id'			=> $item->get_id(),
								'item'			=> !empty($next_item) ? $next_item : false,
								'index'			=> $index,
							]);
						}
						else
						{
							return $this->helper->message($l_action . '_SUCCESS');
						}
					}
					else
					{
						$body = $this->request->is_ajax() ? '@phpbbstudio_ass/ass_confirm_body.html' : 'confirm_body.html';

						confirm_box(false, $l_action, '', $body, $this->helper->get_current_url());

						return new RedirectResponse($this->helper->route('phpbbstudio_ass_inventory'));
					}
				break;
			}
		}

		$counts = [
			'expire'	=> 0,
			'gifts'		=> 0,
			'total'		=> 0,
		];

		/** @var category $cat */
		foreach ($categories as $cat)
		{
			$this->template->assign_block_vars('ass_categories', $this->operator_cat->get_variables($cat));

			/** @var item $it */
			foreach ($items as $it)
			{
				if ($it->get_category() === $cat->get_id())
				{
					$inventory_ids	= $item_map[$it->get_id()];
					$inventory_id	= $inventory_ids[0];

					$s_this_item	= $s_item && $item->get_id() === $it->get_id();

					if ($s_this_item)
					{
						if (isset($inventory_ids[$index0]))
						{
							$inventory_id = $inventory_ids[$index0];
						}
						else
						{
							throw new shop_exception(404, 'ASS_ERROR_NOT_OWNED_STACK');
						}
					}

					// Get the first inventory row
					$row = $inventory[$inventory_id];

					$stack = $this->get_stack_info($inventory_ids, $index0);

					$vars = $this->get_inventory_variables($cat, $it, $row, $stack, $index, $counts);

					$this->template->assign_block_vars('ass_categories.items', $vars);

					if (empty($variables) && $s_this_item)
					{
						$variables = $vars;
					}
				}
			}
		}

		if (!empty($variables['S_TYPE_ERROR']))
		{
			$this->log_phpbb->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_ACP_ASS_ITEM_TYPE_ERROR', time(), [$category->get_title(), $item->get_title()]);
		}

		$this->template->assign_vars([
			'ITEM_INFO'		=> $variables,

			'COUNT_EXPIRE'	=> $counts['expire'],
			'COUNT_GIFTS'	=> $counts['gifts'],
			'COUNT_TOTAL'	=> $counts['total'],

			'S_IS_GIFT'		=> $action === 'gift',

			'T_SHOP_ICON'	=> $s_category ? $category->get_icon() : $this->config['ass_inventory_icon'],

			'L_VIEW_SHOP'	=> $s_category ? $category->get_title() : $this->language->lang('ASS_SHOP_INDEX'),
			'U_VIEW_SHOP'	=> $s_category ? $this->router->category($category->get_slug()) : $this->helper->route('phpbbstudio_ass_shop'),
		]);

		return $this->helper->render('ass_inventory.html', $this->language->lang('ASS_INVENTORY'));
	}

	/**
	 * Display the history page.
	 *
	 * @param  int			$page		The page number
	 * @return Response
	 * @access public
	 */
	public function history($page)
	{
		$this->controller->check_shop();
		$this->controller->create_shop_crumbs('history');

		$points_name = $this->aps_functions->get_name();
		$points_new = $this->language->lang('ASS_POINTS_NEW', $points_name);
		$points_old = $this->language->lang('ASS_POINTS_OLD', $points_name);

		$show_array = [
			'all'		=> ['title' => 'ASS_ALL',			'sql' => ''],
			'use'		=> ['title' => 'ASS_USAGES',		'sql' => 'l.item_purchase = 0'],
			'buy'		=> ['title' => 'ASS_PURCHASES',		'sql' => 'l.item_purchase = 1'],
			'given'		=> ['title' => 'ASS_GIFTS_GIVEN',	'sql' => 'l.item_purchase = 1 AND l.recipient_id <> 0'],
			'received'	=> ['title' => 'ASS_GIFTS_RECEIVED', 'sql' => 'l.recipient_id = ' . (int) $this->user->data['user_id']],
		];
		$sort_array = [
			'time'		=> ['title' => 'TIME',				'sql' => 'l.log_time'],
			'old'		=> ['title' => $points_old,			'sql' => 'l.points_old'],
			'new'		=> ['title' => $points_new,			'sql' => 'l.points_new'],
			'price'		=> ['title' => 'ASS_ITEM_PRICE',	'sql' => 'l.points_sum'],
			'item'		=> ['title' => 'ASS_ITEM_TITLE',	'sql' => 'i.item_title'],
			'category'	=> ['title' => 'ASS_CATEGORY_TITLE', 'sql' => 'c.category_title'],
			'recipient'	=> ['title' => 'ASS_RECIPIENT_NAME', 'sql' => 'recipient_name'],
		];
		$dir_array = [
			'desc'		=> ['title' => 'DESCENDING', 'sql' => 'DESC'],
			'asc'		=> ['title' => 'ASCENDING', 'sql' => 'ASC'],
		];

		$show = $this->request->variable('display', 'all', true, \phpbb\request\request_interface::GET);
		$sort = $this->request->variable('sort', 'time', true, \phpbb\request\request_interface::GET);
		$dir = $this->request->variable('direction', 'desc', true, \phpbb\request\request_interface::GET);

		$show = in_array($show, array_keys($show_array)) ? $show : 'all';
		$sort = in_array($sort, array_keys($sort_array)) ? $sort : 'time';
		$dir = in_array($dir, array_keys($dir_array)) ? $dir : 'desc';

		$sql_where = $show_array[$show]['sql'];
		$sql_order = $sort_array[$sort]['sql'];
		$sql_dir = $dir_array[$dir]['sql'];

		$limit = (int) $this->config['ass_logs_per_page'];
		$start = ($page - 1) * $limit;

		$total = $this->log->get_user_logs_count($sql_where, $this->user->data['user_id']);
		$rowset = $this->log->get_user_logs($sql_where, $sql_order, $sql_dir, $limit, $start, $this->user->data['user_id']);

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
				'S_GIFT_RECEIVE'	=> $row['recipient_id'] == $this->user->data['user_id'],

				'U_CATEGORY'		=> $category ? $this->router->category($category->get_slug()) : '',
				'U_ITEM'			=> $item ? $this->router->item($item->get_category_slug(), $item->get_slug()) : '',
			]);
		}

		$this->pagination->generate_template_pagination([
			'routes' => ['phpbbstudio_ass_history', 'phpbbstudio_ass_history_pagination'],
			'params' => ['display' => $show, 'sort' => $sort, 'direction' => $dir],
		], 'shop_pagination', 'page', $total, $limit, $start);

		$this->template->assign_vars([
			'SORT_DISPLAY'			=> $show,
			'SORT_DISPLAY_ARRAY'	=> $show_array,
			'SORT_SORT'				=> $sort,
			'SORT_SORT_ARRAY'		=> $sort_array,
			'SORT_DIR'				=> $dir,
			'SORT_DIR_ARRAY'		=> $dir_array,

			'TOTAL_LOGS'			=> $this->language->lang('TOTAL_LOGS', $total),

			'S_ASS_INVENTORY'		=> true,
		]);

		return $this->helper->render('ass_history.html', $this->language->lang('ASS_INVENTORY'));
	}

	/**
	 * Create a download response for a specific file.
	 *
	 * @param  string		$file		The file name
	 * @return Response
	 * @access protected
	 */
	protected function download($file)
	{
		$response = new Response($file);

		$response->headers->set('Content-type', 'application/octet-stream');
		$response->headers->set('Content-Disposition', 'attachment; filename="' . basename($file) . '";');
		$response->headers->set('Content-length', filesize($file));

		$response->sendHeaders();
		$response->setContent(readfile($file));

		return $response;
	}

	/**
	 * Get an inventory item's template variables.
	 *
	 * @param  category		$category	The category entity
	 * @param  item			$item		The item entity
	 * @param  array		$row		The inventory row
	 * @param  array		$stack		The item stack information
	 * @param  int			$index		The item index
	 * @param  array		$counts		The inventory overview counts
	 * @return array					The inventory item template variables
	 * @access protected
	 */
	protected function get_inventory_variables(category $category, item $item, array $row, array $stack, $index = 1, array &$counts = [])
	{
		/** @var item_type $type */
		$type = $this->items_manager->get_type($item->get_type());

		$s_type = $type !== null;

		if ($s_type)
		{
			$type->set_category($category);
			$type->set_item($item);
		}

		$s_has_expired = $this->time->has_expired($row['purchase_time'], $item->get_expire_seconds());
		$s_will_expire = $this->time->will_expire($row['purchase_time'], $item->get_expire_seconds());

		if (!empty($counts))
		{
			$counts = [
				'expire'	=> !$s_has_expired && $s_will_expire ? $counts['expire'] + 1 : $counts['expire'],
				'gifts'		=> $row['gifter_id'] ? $counts['gifts'] + 1 : $counts['gifts'],
				'total'		=> $counts['total'] + $stack['count'],
			];
		}

		return array_merge($this->operator_item->get_variables($item, '', true, $index), [
			'ACTIVATE'		=> $s_type ? $this->language->lang($type->get_language('action')) : '',
			'GIFTER_NAME'	=> $row['gifter_id'] ? get_username_string('full', $row['gifter_id'], $row['gifter_name'], $row['gifter_colour']) : '',
			'PURCHASE_UNIX'	=> (int) $row['purchase_time'],
			'STACK_COUNT'	=> (int) $stack['count'],
			'USE_COUNT'		=> (int) $row['use_count'],
			'USE_UNIX'		=> (int) $row['use_time'],

			'S_AJAX'		=> $s_type ? $type->get_confirm_ajax() : '',
			'S_GIFTED'		=> !empty($row['gifter_id']),
			'S_LIMIT'		=> $item->get_count() && (int) $row['use_count'] >= $item->get_count(),
			'S_REFUND'		=> $item->get_refund_seconds() && !$row['use_count'] ? (int) $row['purchase_time'] + $item->get_refund_seconds() > time() : false,
			'S_HAS_EXPIRED'	=> $s_has_expired,
			'S_WILL_EXPIRE'	=> $s_will_expire,
			'S_TYPE_ERROR'	=> !$s_type,

			'U_STACK_NEXT'	=> $stack['next'] ? $this->router->inventory($item->get_category_slug(), $item->get_slug(), $stack['next']) : '',
			'U_STACK_PREV'	=> $stack['prev'] ? $this->router->inventory($item->get_category_slug(), $item->get_slug(), $stack['prev']) : '',
		]);
	}

	/**
	 * Calculate an inventory item's stacking information.
	 *
	 * @param  array	$array			The inventory identifiers
	 * @param  int		$index			The current index (0 based)
	 * @return array					The stacking information
	 * @access protected
	 */
	protected function get_stack_info(array $array, $index)
	{
		// The amount of inventory items for this specific item
		$count = count($array);

		// Whether or not the current item index is the first or the last
		$prev = $index !== 0 ? $index - 1 : false;
		$next = $index < ($count - 1) ? $index + 1 : false;

		/**
		 * Because the array with inventory identifiers is 0-based,
		 * but we use a 1-based approach for routes,
		 * we have to increment the previous and next indices by 1.
		 */
		$prev = $prev !== false ? $prev + 1 : 0;
		$next = $next !== false ? $next + 1 : 0;

		return [
			'count'	=> (int) $count,
			'next'	=> (int) $next,
			'prev'	=> (int) $prev,
		];
	}
}
