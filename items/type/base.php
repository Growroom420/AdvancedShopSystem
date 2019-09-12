<?php
/**
 *
 * phpBB Studio - Advanced Shop System. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbstudio\ass\items\type;

use phpbbstudio\ass\entity\category;
use phpbbstudio\ass\entity\item;

/**
 * phpBB Studio - Advanced Shop System: Item type "Base"
 */
abstract class base implements item_type
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var string Table prefix */
	protected $table_prefix;

	/** @var category */
	protected $category;

	/** @var item */
	protected $item;

	/**
	 * Constructor.
	 *
	 * @param  \phpbb\auth\auth						$auth				Auth object
	 * @param  \phpbb\config\config					$config				Config object
	 * @param  \phpbb\db\driver\driver_interface	$db					Database object
	 * @param  \phpbb\controller\helper				$helper				Controller helper object
	 * @param  \phpbb\language\language				$language			Language object
	 * @param  \phpbb\log\log						$log				Log object
	 * @param  \phpbb\request\request				$request			Request object
	 * @param  \phpbb\template\template				$template			Template object
	 * @param  \phpbb\user							$user				User object
	 * @param  string								$table_prefix		Table prefix
	 * @return void
	 * @access public
	 */
	public function __construct(
		\phpbb\auth\auth $auth,
		\phpbb\config\config $config,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\controller\helper $helper,
		\phpbb\language\language $language,
		\phpbb\log\log $log,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		$table_prefix
	)
	{
		$this->auth			= $auth;
		$this->config		= $config;
		$this->db			= $db;
		$this->helper		= $helper;
		$this->language		= $language;
		$this->log			= $log;
		$this->request		= $request;
		$this->template		= $template;
		$this->user			= $user;
		$this->table_prefix	= $table_prefix;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_category(category $category)
	{
		$this->category = $category;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set_item(item $item)
	{
		$this->item = $item;
	}

	/**
	 * {@inheritDoc}
	 */
	public function is_admin_authed()
	{
		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_language($mode = '')
	{
		$key = $this->get_language_key();

		switch ($mode)
		{
			case 'success':
			case 'title':
			case 'log':
				return $key . '_' . utf8_strtoupper($mode);

			default:
			case 'action':
				return $key;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	abstract public function get_language_key();

	/**
	 * {@inheritDoc}
	 */
	abstract public function activate(array $data);

	/**
	 * {@inheritDoc}
	 */
	abstract public function get_acp_template(array $data);

	/**
	 * {@inheritDoc}
	 */
	public function validate_acp_data(array $data)
	{
		return [];
	}

	/**
	 * {@inheritDoc}
	 */
	static public function get_confirm_ajax()
	{
		return 'shop_inventory_use';
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_confirm_template(array $data)
	{
		return $this->request->is_ajax() ? '@phpbbstudio_ass/ass_confirm_body.html' : 'confirm_body.html';
	}
}
