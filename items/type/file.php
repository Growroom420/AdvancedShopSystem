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

use phpbbstudio\ass\exceptions\shop_item_exception;

/**
 * phpBB Studio - Advanced Shop System: Item type "File"
 */
class file extends base
{
	/** @var \phpbbstudio\ass\helper\files */
	protected $files;

	/**
	 * Set the files helper object.
	 *
	 * @param  \phpbbstudio\ass\helper\files	$files	Files helper object
	 * @return void
	 * @access public
	 */
	public function set_files(\phpbbstudio\ass\helper\files $files)
	{
		$this->files = $files;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_language_key()
	{
		return 'ASS_TYPE_FILE';
	}

	/**
	 * {@inheritDoc}
	 */
	public function activate(array $data)
	{
		$this->files->set_mode('files');

		if (!$this->files->exists($data['file']))
		{
			throw new shop_item_exception(404, 'ASS_TYPE_FILE_NOT_EXIST');
		}

		return [
			'file' => $this->files->get_path($data['file']),
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_acp_template(array $data)
	{
		$this->template->assign_vars([
			'TYPE_FILE'		=> !empty($data['file']) ? $data['file'] : '',
			'U_FILE'		=> '&amp;m=files&amp;action=select_file&amp;file=',
		]);

		return '@phpbbstudio_ass/items/file.html';
	}

	/**
	 * {@inheritDoc}
	 */
	public function validate_acp_data(array $data)
	{
		$errors = [];

		$this->files->set_mode('files');

		if (empty($data['file']) || !$this->files->exists($data['file']))
		{
			$errors[] = 'ASS_TYPE_FILE_NOT_EXIST';
		}

		return $errors;
	}
}
