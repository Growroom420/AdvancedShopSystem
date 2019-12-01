<?php
/**
 *
 * phpBB Studio - Advanced Shop System. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbstudio\ass\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * phpBB Studio - Advanced Shop System: Blocks listener
 */
class blocks_listener implements EventSubscriberInterface
{
	/** @var \phpbbstudio\ass\operator\blocks */
	protected $blocks;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\language\language */
	protected $language;

	/**
	 * Constructor.
	 *
	 * @param  \phpbbstudio\ass\operator\blocks	$blocks			ASS Blocks object
	 * @param  \phpbb\config\config				$config			Config object
	 * @param  \phpbb\language\language			$language		Language object
	 * @return void
	 * @access public
	 */
	public function __construct(
		\phpbbstudio\ass\operator\blocks $blocks,
		\phpbb\config\config $config,
		\phpbb\language\language $language
	)
	{
		$this->blocks	= $blocks;
		$this->config	= $config;
		$this->language	= $language;
	}

	/**
	 * Assign functions defined in this class to event listeners in the core.
	 *
	 * @return array
	 * @access public
	 * @static
	 */
	static public function getSubscribedEvents()
	{
		return ['phpbbstudio.aps.display_blocks' => 'ass_display_blocks'];
	}

	/**
	 * Load language after user set up.
	 *
	 * @event  core.user_setup_after
	 * @param  \phpbb\event\data	$event		The event object
	 * @return void
	 * @access public
	 */
	public function ass_display_blocks(\phpbb\event\data $event)
	{
		$this->language->add_lang(['ass_common', 'ass_display'], 'phpbbstudio/ass');

		$blocks = $event['page_blocks'];

		$blocks['items'] = [
			'title'		=> $this->language->lang('ASS_SHOP'),
			'auth'		=> $this->config['ass_enabled'] && $this->config['ass_active'],
			'blocks'	=> [],
		];

		foreach ($this->blocks->get_blocks() as $type => $data)
		{
			foreach ($data as $block => $title)
			{
				$blocks['items']['blocks'][$block] = [
					'title'		=> $this->language->lang($title),
					'template'	=> '@phpbbstudio_ass/blocks/' . $block . '.html',
					'function'	=> [$this->blocks, $block],
				];
			}
		}

		$event['page_blocks'] = $blocks;
	}
}
