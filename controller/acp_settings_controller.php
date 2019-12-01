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
 * phpBB Studio - Advanced Shop System: ACP Settings controller
 */
class acp_settings_controller
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\config\db_text */
	protected $config_text;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \phpbb\textformatter\s9e\parser */
	protected $parser;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\textformatter\s9e\utils */
	protected $utils;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var string php File extension */
	protected $php_ext;

	/** @var string Custom form action */
	protected $u_action;

	/**
	 * Constructor.
	 *
	 * @param  \phpbb\config\config				$config			Config object
	 * @param  \phpbb\config\db_text			$config_text	Config text object
	 * @param  \phpbb\language\language			$language		Language object
	 * @param  \phpbb\log\log					$log			Log object
	 * @param  \phpbb\textformatter\s9e\parser	$parser			Text formatter parser object
	 * @param  \phpbb\request\request			$request		Request object
	 * @param  \phpbb\template\template			$template		Template object
	 * @param  \phpbb\user						$user			User object
	 * @param  \phpbb\textformatter\s9e\utils	$utils			Text formatter utilities object
	 * @param  string							$root_path		phpBB root path
	 * @param  string							$php_ext		php File extension
	 * @return void
	 * @access public
	 */
	public function __construct(
		\phpbb\config\config $config,
		\phpbb\config\db_text $config_text,
		\phpbb\language\language $language,
		\phpbb\log\log $log,
		\phpbb\textformatter\s9e\parser $parser,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\textformatter\s9e\utils $utils,
		$root_path,
		$php_ext
	)
	{
		$this->config			= $config;
		$this->config_text		= $config_text;
		$this->language			= $language;
		$this->log				= $log;
		$this->parser			= $parser;
		$this->request			= $request;
		$this->template			= $template;
		$this->user				= $user;
		$this->utils			= $utils;

		$this->root_path		= $root_path;
		$this->php_ext			= $php_ext;
	}

	/**
	 * Handle and display the "Settings" ACP mode.
	 *
	 * @return void
	 * @access public
	 */
	public function settings()
	{
		$this->language->add_lang(['ass_acp_common', 'ass_common'], 'phpbbstudio/ass');

		$errors = [];
		$submit = $this->request->is_set_post('submit');

		$form_key = 'shop_settings';
		add_form_key($form_key);

		if ($submit)
		{
			if (!check_form_key($form_key))
			{
				$errors[] = $this->language->lang('FORM_INVALID');
			}
		}

		$banner_sizes	= ['small', 'tiny'];
		$banner_colours	= ['blue', 'red', 'green', 'orange', 'aqua', 'yellow', 'pink', 'violet', 'purple', 'gold', 'silver', 'bronze'];
		$icon_colours	= ['blue', 'red', 'green', 'orange', 'aqua', 'yellow', 'pink', 'violet', 'purple', 'gold', 'silver', 'bronze',
			'bluegray', 'gray', 'lightgray', 'black', 'white', 'lighten', 'darken'];

		$panels = [
			'featured'		=> ['limit' => ['min' => 0, 'max' => 10], 'order' => ['min' => 1, 'max' => 6], 'width' => ['min' => 4, 'max' => 6]],
			'sale'			=> ['limit' => ['min' => 0, 'max' => 10], 'order' => ['min' => 1, 'max' => 6], 'width' => ['min' => 4, 'max' => 6]],
			'featured_sale'	=> ['limit' => ['min' => 0, 'max' => 4], 'order' => ['min' => 1, 'max' => 6], 'width' => ['min' => 4, 'max' => 6]],
			'random'		=> ['limit' => ['min' => 0, 'max' => 20], 'order' => ['min' => 1, 'max' => 6], 'width' => ['min' => 3, 'max' => 4]],
			'recent'		=> ['limit' => ['min' => 0, 'max' => 10], 'order' => ['min' => 1, 'max' => 6], 'width' => ['min' => 4, 'max' => 6]],
			'limited'		=> ['limit' => ['min' => 0, 'max' => 10], 'order' => ['min' => 1, 'max' => 6], 'width' => ['min' => 4, 'max' => 6]],
		];

		$options = ['banner_size', 'banner_colour', 'icon_colour', 'icon', 'limit', 'order', 'width'];
		$settings = [
			'int'		=> ['enabled', 'active', 'gift_enabled', 'deactivate_conflicts', 'purge_cache', 'items_per_page', 'logs_per_page', 'carousel_arrows', 'carousel_dots', 'carousel_fade', 'carousel_play', 'carousel_play_speed', 'carousel_speed'],
			'string'	=> ['shop_icon', 'inventory_icon', 'no_image_icon', 'gift_icon'],
		];

		// General settings
		foreach ($settings as $type => $data)
		{
			foreach ($data as $name)
			{
				$config_name = "ass_{$name}";
				$default = $this->config[$config_name];
				settype($default, $type);

				$this->template->assign_var(utf8_strtoupper($name), $default);

				if ($submit && empty($errors))
				{
					$value = $this->request->variable($name, '', $type === 'string');

					if ($value !== $default)
					{
						$this->config->set($config_name, $value);
					}
				}
			}
		}

		// Panel settings
		$variables = [];

		foreach ($panels as $panel => $data)
		{
			foreach ($options as $option)
			{
				$name = "{$panel}_{$option}";
				$config_name = "ass_panel_{$name}";

				$default = $this->config[$config_name];
				$variables[utf8_strtoupper($option)][$panel] = $default;

				if ($submit && empty($errors))
				{
					$value = $this->request->variable($name, $default);

					if (isset($data[$option]))
					{
						if ($value < $data[$option]['min'])
						{
							$field = $this->language->lang('ACP_ASS_PANEL_' . utf8_strtoupper($panel));
							$field .= $this->language->lang('COLON');
							$field .= ' ' . $this->language->lang('ACP_ASS_PANEL_' . utf8_strtoupper($option));

							$errors[] = $this->language->lang('ASS_ERROR_TOO_LOW', $field, $data[$option]['min'], $value);

							continue;
						}

						if ($value > $data[$option]['max'])
						{
							$field = $this->language->lang('ACP_ASS_PANEL_' . utf8_strtoupper($panel));
							$field .= $this->language->lang('COLON');
							$field .= ' ' . $this->language->lang('ACP_ASS_PANEL_' . utf8_strtoupper($option));

							$errors[] = $this->language->lang('ASS_ERROR_TOO_HIGH', $field, $data[$option]['max'], $value);

							continue;
						}
					}

					if ($value != $default)
					{
						$this->config->set($config_name, $value);
					}
				}
			}
		}

		uksort($panels, function($a, $b)
		{
			if ($this->config["ass_panel_{$a}_order"] == $this->config["ass_panel_{$b}_order"])
			{
				return 0;
			}

			return $this->config["ass_panel_{$a}_order"] < $this->config["ass_panel_{$b}_order"] ? -1 : 1;
		});

		if ($submit && empty($errors))
		{
			$message = $this->request->variable('inactive_desc', '', true);
			$message = $this->parser->parse($message);

			$this->config_text->set('ass_inactive_desc', $message);

			meta_refresh(3, $this->u_action);

			trigger_error($this->language->lang('CONFIG_UPDATED') . adm_back_link($this->u_action));
		}

		$message = $this->config_text->get('ass_inactive_desc');
		$message = $this->utils->unparse($message);

		$this->generate_bbcodes();

		$this->template->assign_vars(array_merge($variables, [
			'ERRORS'				=> $errors,

			'INACTIVE_DESC'			=> $message,

			'SHOP_BLOCKS'			=> $panels,
			'SHOP_ICON_COLOURS'		=> $icon_colours,
			'SHOP_BANNER_COLOURS'	=> $banner_colours,
			'SHOP_BANNER_SIZES'		=> $banner_sizes,

			'U_ACTION'				=> $this->u_action,
		]));
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
