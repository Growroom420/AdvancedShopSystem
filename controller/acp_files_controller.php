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

/**
 * phpBB Studio - Advanced Shop System: ACP Files controller
 */
class acp_files_controller
{
	/** @var \phpbbstudio\ass\helper\files */
	protected $files;

	/** @var \phpbb\language\language */
	protected $language;

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
	 * @param  \phpbbstudio\ass\helper\files	$files			Files object
	 * @param  \phpbb\language\language			$language		Language object
	 * @param  \phpbb\request\request			$request		Request object
	 * @param  \phpbb\template\template			$template		Template object
	 * @param  \phpbb\user						$user			User object
	 * @return void
	 * @access public
	 */
	public function __construct(
		\phpbbstudio\ass\helper\files $files,
		\phpbb\language\language $language,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user
	)
	{
		$this->files			= $files;
		$this->language			= $language;
		$this->request			= $request;
		$this->template			= $template;
		$this->user				= $user;
	}

	/**
	 * Handle and display the "Files" ACP mode.
	 *
	 * @return void
	 * @access public
	 */
	public function files()
	{
		$this->language->add_lang(['ass_acp_files', 'ass_acp_common', 'ass_common'], 'phpbbstudio/ass');
		$this->language->add_lang('posting');

		$mode		= $this->request->variable('m', '', true);
		$action		= $this->request->variable('action', '', true);
		$directory	= $this->request->variable('dir', '', true);
		$item_file	= $this->request->variable('file', '', true);
		$item_img	= $this->request->variable('image', '', true);
		$img_input	= $this->request->variable('input', '', true);

		$s_item_img	= $this->request->is_set('image');
		$img_value	= $item_img ? $item_img : ($s_item_img ? true : '');

		switch ($mode)
		{
			case 'images':
			case 'files':
				$this->files->set_mode($mode);
				$json_response = new \phpbb\json_response;

				$form_key = 'ass_files';
				add_form_key($form_key);

				switch ($action)
				{
					case 'add_dir':
						if (!check_form_key($form_key))
						{
							trigger_error($this->language->lang('FORM_INVALID') . adm_back_link($this->get_file_action($mode, $directory), E_USER_WARNING));
						}

						$folder = $this->request->variable('folder', '', true);

						$refresh = str_replace('&amp;', '&', $this->get_file_action($mode, $directory));

						try
						{
							$this->files->add($directory, $folder);
						}
						catch (runtime_exception $e)
						{
							trigger_error($this->language->lang_array($e->getMessage(), array_merge([$this->language->lang('ASS_FOLDER')], $e->get_parameters())) . adm_back_link($refresh), E_USER_WARNING);
						}

						$json_response->send(['REFRESH_DATA' => ['url' => $refresh, 'time' => 0]]);

						redirect($refresh);
					break;

					case 'add_file':
						if (!check_form_key($form_key))
						{
							trigger_error($this->language->lang('FORM_INVALID') . adm_back_link($this->get_file_action($mode, $directory)), E_USER_WARNING);
						}

						$refresh = str_replace('&amp;', '&', $this->get_file_action($mode, $directory));

						try
						{
							$this->files->upload($directory, 'file');
						}
						catch (runtime_exception $e)
						{
							trigger_error($this->language->lang_array($e->getMessage(), array_merge([$this->language->lang('ASS_FILENAME')], $e->get_parameters())) . adm_back_link($refresh), E_USER_WARNING);
						}

						redirect($refresh);
					break;

					case 'delete_dir':
					case 'delete_file':
						$type = $action === 'delete_dir' ? 'FOLDER' : 'FILE';

						if (confirm_box(true))
						{
							$this->files->delete($directory);

							trigger_error("ASS_{$type}_DELETE_SUCCESS");
						}
						else
						{
							confirm_box(false, "ASS_{$type}_DELETE", '');
						}
					break;

					case 'select_file':
						if (($item_img || $item_file) && !$this->request->is_set('dir'))
						{
							$directory = pathinfo($item_img, PATHINFO_DIRNAME);
							$directory = $directory === '.' ? '' : $directory;
						}

						$this->template->assign_vars([
							'S_FILE_SELECT'	=> $s_item_img ? $img_input : 'file',
						]);
					break;
				}

				$files = $this->files->view($directory);

				foreach ($files['folders'] as $folder)
				{
					$file_time = $this->files->get_file_time($directory, $folder);

					$this->template->assign_block_vars('ass_folders', [
						'NAME'			=> $folder,
						'TIME'			=> $file_time ? $this->user->format_date($file_time) : '',
						'U_DELETE'		=> $this->get_file_action($mode, ($directory ? $directory . '%2F' : '') . $folder, 'delete_dir'),
						'U_VIEW'		=> $this->get_file_action($mode, ($directory ? $directory . '%2F' : '') . $folder, $action, $img_value, $item_file, $img_input),
					]);
				}

				foreach ($files['files'] as $file)
				{
					$dir_file = $directory ? $directory . '/' . $file : $file;

					$file_size = $this->files->get_file_size($directory, $file);
					$file_time = $this->files->get_file_time($directory, $file);

					$this->template->assign_block_vars('ass_files', [
						'NAME'			=> $file,
						'ICON'			=> $this->files->get_file_icon($file),
						'IMG'			=> $this->files->get_path($directory, true, $file),
						'SIZE'			=> $file_size ? get_formatted_filesize($file_size) : '',
						'TIME'			=> $file_time ? $this->user->format_date($file_time) : '',
						'VALUE'			=> $dir_file,
						'S_SELECTED'	=> $s_item_img ? $dir_file === $item_img : $dir_file === $item_file,
						'U_DELETE'		=> $this->get_file_action($mode, ($directory ? $directory . '%2F' : '') . $file, 'delete_file'),
					]);
				}

				$directories = array_filter(explode('/', $directory));

				$this->template->assign_vars([
					'DIRECTORIES'	=> $directories,

					'S_FILE_MODE'	=> $mode,

					'U_ACTION'		=> $this->get_file_action($mode, '', $action, $img_value, $item_file, $img_input),
					'U_ACTION_FORM'	=> $this->get_file_action($mode, implode('%2F', $directories), $action),
					'U_BACK'		=> $this->u_action,
				]);
			break;

			default:
				$this->template->assign_vars([
					'ALLOWED_EXTS'	=> $mode === 'images' ? implode(',', $this->files->get_extensions()) : '',
					'S_FILE_INDEX'	=> true,
					'U_FILE_FILES'	=> $this->get_file_action('files'),
					'U_FILE_IMAGES'	=> $this->get_file_action('images'),
				]);
			break;
		}
	}

	/**
	 * Get a custom form action for the "files" mode.
	 *
	 * @param  string	$mode			The file mode (images|files)
	 * @param  string	$directory		The file directory
	 * @param  string	$action			The action
	 * @param  string	$image			The image name
	 * @param  string	$file			The file name
	 * @param  string	$input			The input field name
	 * @return string					The custom form action
	 * @access protected
	 */
	protected function get_file_action($mode, $directory = '', $action = '', $image = '', $file = '', $input = '')
	{
		$mode		= $mode ? "&m={$mode}" : '';
		$action		= $action ? "&action={$action}" : '';
		$directory	= $directory ? "&dir={$directory}" : '';
		$file		= $file ? "&file={$file}" : '';
		$input		= $input ? "&input={$input}" : '';

		$image		= $image === true ? '&image=' : ($image ? "&image={$image}" : '');

		return "{$this->u_action}{$mode}{$directory}{$action}{$image}{$file}{$input}";
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
