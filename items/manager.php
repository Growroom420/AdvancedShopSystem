<?php
/**
 *
 * phpBB Studio - Advanced Shop System. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbstudio\ass\items;

use phpbbstudio\ass\items\type\item_type;

/**
 * phpBB Studio - Advanced Shop System: Item type manager
 */
class manager
{
	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\di\service_collection */
	protected $types;

	/**
	 * Constructor.
	 *
	 * @param  \phpbb\language\language			$language		Language object
	 * @param  \phpbb\template\template			$template		Template object
	 * @param  \phpbb\di\service_collection		$types			Item types collection
	 * @return void
	 * @access public
	 */
	public function __construct(
		\phpbb\language\language $language,
		\phpbb\template\template $template,
		\phpbb\di\service_collection $types
	)
	{
		$this->language	= $language;
		$this->template	= $template;
		$this->types	= $types;
	}

	/**
	 * Get an item type.
	 *
	 * @param  string			$type		The item type
	 * @return item_type|null
	 * @access public
	 */
	public function get_type($type)
	{
		return isset($this->types[$type]) ? $this->types[$type] : null;
	}

	/**
	 * Set and assign the item types for a <select> element.
	 *
	 * @param  string			$type		The selected item type
	 * @return void
	 * @access public
	 */
	public function set_types_for_select($type)
	{
		/** @var item_type $service */
		foreach ($this->types as $id => $service)
		{
			if ($service->is_admin_authed())
			{
				$this->template->assign_block_vars('item_types', [
					'ID'			=> $id,
					'TITLE'			=> $this->language->lang($service->get_language('title')),
					'S_SELECTED'	=> $id === $type,
				]);
			}
		}
	}
}
