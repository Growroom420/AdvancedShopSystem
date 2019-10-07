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

/**
 * phpBB Studio - Advanced Shop System: Item type "Points"
 */
class points extends base
{
	/** @var \phpbbstudio\aps\points\distributor */
	protected $aps_distributor;

	/** @var \phpbbstudio\aps\core\functions */
	protected $aps_functions;

	/**
	 * Set the APS Distributor object.
	 *
	 * @param  \phpbbstudio\aps\points\distributor	$aps_distributor	APS Distributor object
	 * @return void
	 * @access public
	 */
	public function set_aps_distributor(\phpbbstudio\aps\points\distributor $aps_distributor)
	{
		$this->aps_distributor = $aps_distributor;
	}

	/**
	 * Set the APS Functions object.
	 *
	 * @param  \phpbbstudio\aps\core\functions	$aps_functions	APS Functions object
	 * @return void
	 * @access public
	 */
	public function set_aps_functions(\phpbbstudio\aps\core\functions $aps_functions)
	{
		$this->aps_functions = $aps_functions;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_language_key()
	{
		return 'ASS_TYPE_POINTS';
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
				$key .= '_' . utf8_strtoupper($mode);
			break;

			case 'action':
				// do nothing
			break;

			default:
				$key .= '_CONFIRM';
		}

		return $this->language->lang($key, $this->aps_functions->get_name());
	}

	/**
	 * {@inheritDoc}
	 */
	public function activate(array $data)
	{
		$points = $this->aps_functions->equate_points($this->user->data['user_points'], $data['points']);
		$points = $this->aps_functions->boundaries($points);
		$points = $this->aps_functions->format_points($points);

		return $this->aps_distributor->update_points($points);
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_acp_template(array $data)
	{
		$points = !empty($data['points']) ? $data['points'] : 0.00;

		$this->template->assign_var('TYPE_POINTS', $this->aps_functions->format_points($points));

		return '@phpbbstudio_ass/items/points.html';
	}

	/**
	 * {@inheritDoc}
	 */
	public function validate_acp_data(array $data)
	{
		$errors = [];

		if (empty($data['points']))
		{
			$errors[] = $this->language->lang('ASS_TYPE_POINTS_NOT_EMPTY', $this->aps_functions->get_name());
		}

		return $errors;
	}
}
