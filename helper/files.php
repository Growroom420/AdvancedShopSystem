<?php
/**
 *
 * phpBB Studio - Advanced Shop System. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbstudio\ass\helper;

use phpbb\exception\runtime_exception;
use phpbb\finder;

/**
 * phpBB Studio - Advanced Shop System: Files helper
 */
class files
{
	/** @var \phpbb\cache\service */
	protected $cache;

	/** @var \phpbb\files\factory */
	protected $factory;

	/** @var \phpbb\finder */
	protected $finder;

	/** @var \phpbb\filesystem\filesystem */
	protected $filesystem;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var array Shop files modes */
	protected $modes = ['files', 'images'];

	/** @var string Shop files mode */
	protected $mode = '';

	/** @var array File extensions */
	protected $extensions = [
		'archives'	=> ['7z', 'ace', 'bz2', 'gtar', 'gz', 'rar', 'tar', 'tgz', 'torrent', 'zip'],
		'images'	=> ['gif', 'jpeg', 'jpg', 'png', 'tga', 'tif', 'tiff'],
		'documents'	=> ['ai', 'doc', 'docm', 'docx', 'dot', 'dotm', 'dotx', 'odg', 'odp', 'ods', 'odt', 'pdf', 'ppt', 'pptm', 'pptx', 'ps', 'rtf', 'xls', 'xlsb', 'xlsm', 'xlsx'],
		'text'		=> ['c', 'cpp', 'csv', 'diz', 'h', 'hpp', 'ini', 'js', 'log', 'txt', 'xml'],
		'other'		=> ['mp3', 'mpeg', 'mpg', 'ogg', 'ogm'],
	];

	/** @var array File extensions specific icons */
	protected $specific_icons = [
		'powerpoint-o'	=> ['ppt', 'pptm', 'pptx'],
		'excel-o'		=> ['csv', 'xls', 'xlsb', 'xlsm', 'xlsx'],
		'pdf-o'			=> ['pdf'],
		'audio-o'		=> ['mp3'],
		'movie-o'		=> ['mpeg', 'mpg', 'ogg', 'ogm'],
		'code-o'		=> ['c', 'cpp', 'h', 'hpp', 'ini', 'js'],
	];

	/** @var array File extensions icons */
	protected $icons = [
		'archives'	=> 'file-archive-o',
		'images'	=> 'file-image-o',
		'documents'	=> 'file-word-o',
		'text'		=> 'file-text-o',
		'other'		=> 'file-o',
	];

	/**
	 * Constructor.
	 *
	 * @param  \phpbb\cache\service				$cache			Cache object
	 * @param  \phpbb\files\factory				$factory		Files factory object
	 * @param  \phpbb\filesystem\filesystem		$filesystem		Filesystem object
	 * @param  string							$root_path		phpBB root path
	 * @return void
	 * @access public
	 */
	public function __construct(
		\phpbb\cache\service $cache,
		\phpbb\files\factory $factory,
		\phpbb\filesystem\filesystem $filesystem,
		$root_path
	)
	{
		$this->cache		= $cache;
		$this->factory		= $factory;
		$this->filesystem	= $filesystem;

		$this->root_path	= $root_path;
	}

	/**
	 * Get the shop files modes.
	 *
	 * @return array						The shop files modes
	 * @access public
	 */
	public function get_modes()
	{
		return (array) $this->modes;
	}

	/**
	 * Set the shop files mode.
	 *
	 * @param  string		$mode			The shop files mode
	 * @return self			$this			This object for chaining calls
	 * @access public
	 */
	public function set_mode($mode)
	{
		$this->mode = $mode;

		return $this;
	}

	/**
	 * Set the finder instance.
	 *
	 * @return void
	 * @access public
	 */
	public function set_finder()
	{
		if ($this->finder === null)
		{
			$this->finder = new finder($this->filesystem, $this->root_path, $this->cache);
		}
	}

	/**
	 * Get the file size of a shop file.
	 *
	 * @param  string		$directory		The shop file directory
	 * @param  string		$file			The shop file
	 * @return int
	 * @access public
	 */
	public function get_file_size($directory, $file)
	{
		$path = $this->get_path($directory, true, $file);

		return $this->filesystem->exists($path) ? filesize($path) : 0;
	}

	/**
	 * Get the file modification time of a shop file.
	 *
	 * @param  string		$directory		The shop file directory
	 * @param  string		$file			The shop file
	 * @return int
	 * @access public
	 */
	public function get_file_time($directory, $file)
	{
		$path = $this->get_path($directory, true, $file);

		return $this->filesystem->exists($path) ? filemtime($path) : 0;
	}

	/**
	 * Get the file icon of a shop file.
	 *
	 * @param  string		$file			The shop file
	 * @return string
	 * @access public
	 */
	public function get_file_icon($file)
	{
		$extension = pathinfo($file, PATHINFO_EXTENSION);

		foreach ($this->specific_icons as $icon => $extensions)
		{
			if (in_array($extension, $extensions))
			{
				return $icon;
			}
		}

		foreach ($this->extensions as $type => $extensions)
		{
			if (in_array($extension, $extensions))
			{
				return $this->icons[$type];
			}
		}

		return $this->icons['other'];
	}

	/**
	 * View a shop file directory.
	 *
	 * @param  string		$directory		The shop file directory
	 * @return array						The files and folders in the shop file directory
	 * @access public
	 */
	public function view($directory = '')
	{
		$files = [];
		$folders = [];

		$needle = $this->get_path($directory);
		$target = $this->get_path($directory, false);

		$this->set_finder();

		foreach ($this->finder->core_path($target)->get_files() as $path)
		{
			$file = $this->clean_path($path, $needle);

			if ($this->is_htaccess($file))
			{
				continue;
			}

			if ($this->is_not_nested($file))
			{
				$files[] = $file;
			}
		}

		foreach ($this->finder->core_path($target)->get_directories() as $path)
		{
			$folder = $this->clean_path($path, $needle);

			if ($this->is_not_nested($folder))
			{
				$folders[] = $folder;
			}
		}

		if ($this->mode === 'files')
		{
			$this->create_htaccess();
		}

		return [
			'files'		=> $files,
			'folders'	=> $folders,
		];
	}

	/**
	 * Select a shop file.
	 *
	 * @param  string		$directory		The shop file directory
	 * @param  string		$image			The shop file image
	 * @return array
	 * @access public
	 */
	public function select($directory, $image = '')
	{
		$files = [];

		$needle = $this->get_path($directory);
		$target = $this->get_path($directory, false);

		$this->set_finder();

		foreach ($this->finder->core_path($target)->get_files() as $path)
		{
			$file = $this->clean_path($path, $needle);

			if ($this->is_not_nested($path))
			{
				$files[] = [
					'NAME'			=> $file,
					'S_SELECTED'	=> $path === $image,
				];
			}
		}

		return $files;
	}

	/**
	 * Add a shop file.
	 *
	 * @param  string		$directory		The shop file directory
	 * @param  string		$folder			The shop file folder
	 * @return void
	 * @access public
	 */
	public function add($directory, $folder)
	{
		if ($folder === '')
		{
			throw new runtime_exception('ASS_ERROR_TOO_SHORT', [0, 0]);
		}

		if (!preg_match('/^[^!"#$%&*\'()+,.\/\\\\:;<=>?@\\[\\]^`{|}~ ]*$/', $folder))
		{
			throw new runtime_exception('ASS_ERROR_ILLEGAL_CHARS');
		}

		$target = $this->get_path($directory, true, $folder);

		if ($this->filesystem->exists($target))
		{
			throw new runtime_exception('ASS_ERROR_NOT_UNIQUE', [$folder]);
		}

		$this->filesystem->mkdir($target);
	}

	/**
	 * Upload a shop file from an <input> element.
	 *
	 * @param  string		$directory			The shop file directory
	 * @param  string		$input_name			The name of the <input> element
	 * @return void
	 * @access public
	 */
	public function upload($directory, $input_name)
	{
		/** @var \phpbb\files\upload $upload */
		$upload = $this->factory->get('files.upload');

		if (!$upload->is_valid($input_name))
		{
			throw new runtime_exception('FORM_INVALID');
		}

		$file = $upload
			->set_allowed_extensions($this->get_extensions())
			->handle_upload('files.types.form', $input_name);

		$upload->common_checks($file);

		if ($file->error)
		{
			throw new runtime_exception(implode('<br>', array_unique($file->error)));
		}

		if ($this->exists($directory, $file->get('realname')))
		{
			throw new runtime_exception('ASS_ERROR_NOT_UNIQUE', [$file->get('realname')]);
		}

		$file->move_file($this->get_path($directory, false));
	}

	/**
	 * Check if a shop file exists.
	 *
	 * @param  string	$directory			The shop file directory
	 * @param  string	$file				The shop file
	 * @return bool							Whether or not the file exists
	 * @access public
	 */
	public function exists($directory, $file = '')
	{
		return (bool) $this->filesystem->exists($this->get_path($directory, true, $file));
	}

	/**
	 * Delete a shop file from a given path.
	 *
	 * @param  string		$path			Path to a shop file
	 * @return void
	 * @access public
	 */
	public function delete($path)
	{
		$this->filesystem->remove($this->get_path($path));
	}

	/**
	 * Get a path to a shop file.
	 *
	 * @param  string		$directory		The shop file directory
	 * @param  bool			$root			Whether or not to include the phpBB root path
	 * @param  string		$file			The shop file
	 * @return string						The shop file path
	 * @access public
	 */
	public function get_path($directory, $root = true, $file = '')
	{
		$root_path = $root ? $this->root_path : '';

		$directory = $directory ? "/{$directory}" : '';
		$file = $file ? "/{$file}" : '';

		return "{$root_path}{$this->mode}/aps{$directory}{$file}";
	}

	/**
	 * Get the file extensions for a mode.
	 *
	 * @return array						The file extensions
	 * @access public
	 */
	public function get_extensions()
	{
		if ($this->mode === 'images')
		{
			return (array) $this->extensions[$this->mode];
		}
		else
		{
			$extensions = [];

			foreach ($this->extensions as $array)
			{
				$extensions = array_merge($extensions, $array);
			}

			return (array) $extensions;
		}
	}

	/**
	 * Clean the needle from a path.
	 *
	 * @param  string		$path			The path
	 * @param  string		$needle			The needle
	 * @return string
	 * @access protected
	 */
	protected function clean_path($path, $needle)
	{
		return trim(str_replace($needle, '', $path), '/');
	}

	/**
	 * Check if a path is nested.
	 *
	 * @param  string		$path			The path
	 * @return bool							Whether or not the path is nested
	 * @access protected
	 */
	protected function is_not_nested($path)
	{
		return strpos($path, '/') === false;
	}

	/**
	 * Check if a file is the ".htaccess" file.
	 *
	 * @param  string		$file			The file
	 * @return bool
	 * @access public
	 */
	protected function is_htaccess($file)
	{
		return $this->mode === 'files' && $file === '.htaccess';
	}

	/**
	 * Create a ".htaccess" file.
	 *
	 * @return void
	 * @access protected
	 */
	protected function create_htaccess()
	{
		$htaccess = $this->root_path . $this->mode . '/aps/.htaccess';

		if (!$this->filesystem->exists($htaccess))
		{
			$this->filesystem->dump_file($htaccess,
"<Files *>
    Order Allow,Deny
    Deny from All
</Files>");
		}
	}
}
