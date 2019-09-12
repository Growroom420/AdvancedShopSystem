<?php
/**
 *
 * phpBB Studio - Advanced Shop System. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbstudio\ass\migrations;

/**
 * phpBB Studio - Advanced Shop System: Configuration migration
 */
class install_configuration extends \phpbb\db\migration\container_aware_migration
{
	/**
	 * Checks whether the Advanced Shop System configuration does exist or not.
	 *
	 * @return bool			True if this migration is installed, false otherwise.
	 * @access public
	 */
	public function effectively_installed()
	{
		return isset($this->config['ass_enabled']);
	}

	/**
	 * Assign migration file dependencies for this migration.
	 *
	 * @return array		Array of migration files
	 * @access public
	 * @static
	 */
	static public function depends_on()
	{
		return ['\phpbbstudio\ass\migrations\install_tables'];
	}

	/**
	 * Add the Advanced Shop System configuration to the database.
	 *
	 * @return array		Array of configuration
	 * @access public
	 */
	public function update_data()
	{
		$parser = $this->container->get('text_formatter.parser');
		$notes = ':white_check_mark: Install the [b][color=#313131]“Advanced Shop System”[/color][/b].
:arrow_forward: Create your [b]first category[/b].
:arrow_forward: Create your [b]first item[/b].
:arrow_forward: [color=#313131]Purchase[/color] your first item!
:tada: [color=#313131]Celebrate having a shop![/color]';

		$notes = $parser->parse($notes);

		return [
			['config.add', ['ass_enabled', 1]],
			['config.add', ['ass_active', 1]],
			['config_text.add', ['ass_inactive_desc', '']],
			['config.add', ['ass_shop_icon', 'fa-shopping-cart']],
			['config.add', ['ass_inventory_icon', 'fa-archive']],
			['config.add', ['ass_no_image_icon', 'fa-picture-o']],
			['config.add', ['ass_gift_icon', 'fa-gift']],
			['config.add', ['ass_gift_enabled', 1]],
			['config.add', ['ass_deactivate_conflicts', 1]],
			['config.add', ['ass_items_per_page', 20]],
			['config.add', ['ass_logs_per_page', 10]],

			['config.add', ['ass_panel_featured_banner_size', '']],
			['config.add', ['ass_panel_featured_banner_colour', 'gold']],
			['config.add', ['ass_panel_featured_icon_colour', 'lighten']],
			['config.add', ['ass_panel_featured_icon', 'fa-star']],
			['config.add', ['ass_panel_featured_limit', 5]],

			['config.add', ['ass_panel_sale_banner_size', '']],
			['config.add', ['ass_panel_sale_banner_colour', 'green']],
			['config.add', ['ass_panel_sale_icon_colour', 'darken']],
			['config.add', ['ass_panel_sale_icon', 'fa-tag']],
			['config.add', ['ass_panel_sale_limit', 5]],

			['config.add', ['ass_panel_featured_sale_banner_size', 'small']],
			['config.add', ['ass_panel_featured_sale_banner_colour', 'purple']],
			['config.add', ['ass_panel_featured_sale_icon_colour', 'white']],
			['config.add', ['ass_panel_featured_sale_icon', 'fa-lightbulb-o']],
			['config.add', ['ass_panel_featured_sale_limit', 3]],

			['config.add', ['ass_panel_recent_banner_size', 'small']],
			['config.add', ['ass_panel_recent_banner_colour', 'blue']],
			['config.add', ['ass_panel_recent_icon_colour', 'white']],
			['config.add', ['ass_panel_recent_icon', 'fa-line-chart']],
			['config.add', ['ass_panel_recent_limit', 5]],

			['config.add', ['ass_panel_limited_banner_size', 'small']],
			['config.add', ['ass_panel_limited_banner_colour', 'red']],
			['config.add', ['ass_panel_limited_icon_colour', 'white']],
			['config.add', ['ass_panel_limited_icon', 'fa-sort-numeric-desc']],
			['config.add', ['ass_panel_limited_limit', 5]],

			['config.add', ['ass_panel_random_banner_size', '']],
			['config.add', ['ass_panel_random_banner_colour', '']],
			['config.add', ['ass_panel_random_icon_colour', '']],
			['config.add', ['ass_panel_random_icon', '']],
			['config.add', ['ass_panel_random_limit', 10]],

			['config.add', ['ass_carousel_arrows', 1]],
			['config.add', ['ass_carousel_dots', 1]],
			['config.add', ['ass_carousel_fade', 0]],
			['config.add', ['ass_carousel_play', 1]],
			['config.add', ['ass_carousel_play_speed', 3000]],
			['config.add', ['ass_carousel_speed', 300]],

			['config.add', ['ass_notification_gift_id', 0]],
			['config.add', ['ass_notification_stock_id', 0]],

			['config_text.add', ['ass_admin_notes', $notes]],
		];
	}
}
