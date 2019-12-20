<?php
/**
 *
 * phpBB Studio - Advanced Shop System. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

/**
 * Some characters you may want to copy&paste: ’ » “ ” …
 */
$lang = array_merge($lang, [
	'ACP_ASS_CATEGORIES_EXPLAIN'	=> 'Here you can manage the categories within the shop. You can add, edit, delete and order categories. Clicking on a category will show the items within.',
	'ACP_ASS_FILES_EXPLAIN'			=> 'Here you can manage the files used throughout the shop. The “files” category hold all the files used for actual items, while the “images” category holds all the images for displaying an item.',
	'ACP_ASS_FILES_FILES_EXPLAIN'	=> 'Here you can upload files that can later be used as the actual item being purchased. Therefore accessing these file paths directly through a browser has been restricted.',
	'ACP_ASS_FILES_IMAGES_EXPLAIN'	=> 'Here you can upload images that can later be used for displaying an item in the shop. Most images used through the shop work best with a 3:2 ratio (width:height).',
	'ACP_ASS_INVENTORY_EXPLAIN'		=> 'Here you can manage users’ inventories. You can either add or delete multiple items at once for multiple users and/or groups, or you can manage an individual user’s inventory.',
	'ACP_ASS_ITEMS_EXPLAIN'			=> 'Here you can manage the items within a shop category. You can add, edit, delete and order items.',
	'ACP_ASS_LOGS_EXPLAIN'			=> 'Here you can manage and view a list of all Advanced Shop System actions carried out by users. There are various display and sorting options for your convenience.',
	'ACP_ASS_OVERVIEW_EXPLAIN'		=> 'Some of these statistics are based on the currently available categories, items and logs, not taking any deleted rows into account.',
	'ACP_ASS_SETTINGS_EXPLAIN'		=> 'Here you can adjust the basic shop settings of your board, enable the shop, give it fitting icons and among other settings adjust the default carousel and panel settings.',

	'ACP_ASS_AMOUNT_ITEMS'			=> 'Amount of items',
	'ACP_ASS_AMOUNT_USERS'			=> 'Amount of users',
	'ACP_ASS_AVAILABLE'				=> 'Available',

	'ACP_ASS_APPLY'					=> 'Apply',
	'ACP_ASS_CLEAR'					=> 'Clear',
	'ACP_ASS_COPY'					=> 'Copy',

	'ACP_ASS_CAROUSEL_ARROWS'			=> 'Display arrows',
	'ACP_ASS_CAROUSEL_ARROWS_DESC'		=> 'Whether or not the “previous” and “next” arrows should be displayed.',
	'ACP_ASS_CAROUSEL_DOTS'				=> 'Display dots',
	'ACP_ASS_CAROUSEL_DOTS_DESC'		=> 'Whether or not the “navigation dots” should be displayed.',
	'ACP_ASS_CAROUSEL_FADE'				=> 'Use “fade” animation',
	'ACP_ASS_CAROUSEL_FADE_DESC'		=> 'Whether or not the “fade” animation should be used instead of the “slide” animation.',
	'ACP_ASS_CAROUSEL_PLAY'				=> 'Enable autoplay',
	'ACP_ASS_CAROUSEL_PLAY_DESC'		=> 'Whether or not the carousel should automatically start playing.',
	'ACP_ASS_CAROUSEL_PLAY_SPEED'		=> 'Autoplay speed',
	'ACP_ASS_CAROUSEL_PLAY_SPEED_DESC'	=> 'The amount of milliseconds a slide should be displayed before automatically sliding to the next one.',
	'ACP_ASS_CAROUSEL_SPEED'			=> 'Animation speed',
	'ACP_ASS_CAROUSEL_SPEED_DESC'		=> 'The amount of milliseconds for the duration of the “fade” or “slide” animation.',

	'ACP_ASS_CATEGORY_ADD_SUCCESS'		=> 'You have successfully added the category.',
	'ACP_ASS_CATEGORY_EDIT_SUCCESS'		=> 'You have successfully edited the category.',
	'ACP_ASS_CATEGORY_DELETE'			=> 'Delete category',
	'ACP_ASS_CATEGORY_DELETE_CONFIRM'	=> 'Are you sure you wish to delete this category?
											<br><br>This will delete all items within this category aswell.
											<br>These items will also be deleted from users’ inventories.
											<br>However, any actions performed by these items will not be reverted.
											<br><br>This action can <strong><u>not</u></strong> be undone!',
	'ACP_ASS_CATEGORY_DELETE_SUCCESS'	=> 'You have successfully deleted the category.',

	'ACP_ASS_CONFLICT'					=> 'Conflict',
	'ACP_ASS_CONFLICT_DESC'				=> 'The user this happened to has been logged in the <a href="%1$s">Admin log</a>
											<br>Most likely this item’s <strong>“%2$s”</strong> are invalid.',
	'ACP_ASS_CONFLICTS'					=> 'Conflicts',
	'ACP_ASS_CONFLICTS_DEACTIVATE'		=> 'Deactivate items with conflicts',

	'ACP_ASS_INVENTORY_ADD_SUCCESS'		=> [
		1 => 'You have successfully added this item to their inventory.',
		2 => 'You have successfully added these items to their inventory.',
	],
	'ACP_ASS_INVENTORY_DELETE_SUCCESS'	=> [
		1 => 'You have successfully deleted this item to their inventory.',
		2 => 'You have successfully deleted these items to their inventory.',
	],

	'ACP_ASS_ITEM_ADD_SUCCESS'			=> 'You have successfully added the item.',
	'ACP_ASS_ITEM_EDIT_SUCCESS'			=> 'You have successfully edited the item.',
	'ACP_ASS_ITEM_DELETE'				=> 'Delete item',
	'ACP_ASS_ITEM_DELETE_CONFIRM'		=> 'Are you sure you wish to delete this item?
											<br><br>This will also delete this item from users’ inventories.
											<br>However, any actions performed by this item will not be reverted.
											<br><br>This action can <strong><u>not</u></strong> be undone!',
	'ACP_ASS_ITEM_DELETE_SUCCESS'		=> 'You have successfully deleted the item.',
	'ACP_ASS_ITEM_MARK_FEATURED'		=> 'Mark this item as “featured”',
	'ACP_ASS_ITEM_MARK_SALE'			=> 'Mark this item as “on sale”',
	'ACP_ASS_ITEM_UNMARK_FEATURED'		=> 'Unmark this item as “featured”',
	'ACP_ASS_ITEM_UNMARK_SALE'			=> 'Unmark this item as “on sale”',

	'ACP_ASS_CATEGORY_ACTIVE'			=> 'Category active',
	'ACP_ASS_CATEGORY_DESC'				=> 'Category description',
	'ACP_ASS_CATEGORY_ICON'				=> 'Category icon',
	'ACP_ASS_CATEGORY_SLUG'				=> 'Category slug',
	'ACP_ASS_CATEGORY_TITLE'			=> 'Category title',

	'ACP_ASS_GIFT_ICON'					=> 'Gift icon',
	'ACP_ASS_GIFT_ITEMS'				=> 'Gift items',
	'ACP_ASS_GIFT_PERCENTAGE'			=> 'Percentage',
	'ACP_ASS_GIFT_PRICE'				=> 'Price',
	'ACP_ASS_GIFTING_ENABLED'			=> 'Gifting enabled',

	'ACP_ASS_INVENTORY_ICON'			=> 'Inventory icon',

	'ACP_ASS_ITEM_ACTIVE'				=> 'Item active',
	'ACP_ASS_ITEM_AVAILABLE'			=> 'Item available',
	'ACP_ASS_ITEM_AVAILABLE_DESC'		=> 'The period this item will be available in the shop.',
	'ACP_ASS_ITEM_BACKGROUND'			=> 'Item background image',
	'ACP_ASS_ITEM_BACKGROUND_DESC'		=> 'The image that is used throughout the shop to display the item.',
	'ACP_ASS_ITEM_COUNT'				=> 'Item use count',
	'ACP_ASS_ITEM_COUNT_DESC'			=> 'The amount of times this item can be used once purchased.',
	'ACP_ASS_ITEM_COUNT_ZERO_DESC'		=> 'Set to 0 to for unlimited usages.',
	'ACP_ASS_ITEM_DESC'					=> 'Item description',
	'ACP_ASS_ITEM_EXPIRE_DESC'			=> 'The amount of time after which this item will expire and can no longer be used.',
	'ACP_ASS_ITEM_EXPIRE_STRING'		=> 'Item expire time',
	'ACP_ASS_ITEM_DELETE_DESC'			=> 'The amount of time after which the item will be automatically removed from the user’s inventory,<br>when either the item has expired or the use count has been reached.',
	'ACP_ASS_ITEM_DELETE_STRING'		=> 'Item delete time',
	'ACP_ASS_ITEM_FEATURED'				=> 'Item featured',
	'ACP_ASS_ITEM_FEATURED_DESC'		=> 'The period this item will be featured.',
	'ACP_ASS_ITEM_GIFT'					=> 'Item gift',
	'ACP_ASS_ITEM_GIFT_DESC'			=> 'Whether or not this item can be gifted to other users.',
	'ACP_ASS_ITEM_GIFT_ONLY'			=> 'Item gift only',
	'ACP_ASS_ITEM_GIFT_ONLY_DESC'		=> 'Whether or not this item can only be gifted to other users and not purchased for themselves.',
	'ACP_ASS_ITEM_GIFT_PERCENTAGE'		=> 'Item gift percentage',
	'ACP_ASS_ITEM_GIFT_PERCENTAGE_DESC'	=> 'The percentage that will be added on top of the regular item price when this item is being gifted.',
	'ACP_ASS_ITEM_GIFT_PRICE'			=> 'Item gift price',
	'ACP_ASS_ITEM_GIFT_PRICE_DESC'		=> 'The price that will be used instead of the regular item price when this item is being gifted.',
	'ACP_ASS_ITEM_GIFT_TYPE'			=> 'Item gift type',
	'ACP_ASS_ITEM_GIFT_TYPE_DESC'		=> 'Whether to use the “percentage” or the “price” when this item is being gifted.',
	'ACP_ASS_ITEM_ICON'					=> 'Item icon',
	'ACP_ASS_ITEM_IMAGES'				=> 'Item images',
	'ACP_ASS_ITEM_PRICE'				=> 'Item price',
	'ACP_ASS_ITEM_REFUND_DESC'			=> 'The amount of time before which an unused item can be refunded.',
	'ACP_ASS_ITEM_REFUND_STRING'		=> 'Item refund time',
	'ACP_ASS_ITEM_RELATED_ENABLED'		=> 'Enable related items',
	'ACP_ASS_ITEM_RELATED_ITEMS'		=> 'Related items',
	'ACP_ASS_ITEM_RESOLVE'				=> 'Resolve',
	'ACP_ASS_ITEM_RESOLVE_CONFIRM'		=> 'Are you sure you want to mark this item conflict as resolved?',
	'ACP_ASS_ITEM_RESOLVE_SUCCESS'		=> 'You have successfully marked this item conflict as resolved.',
	'ACP_ASS_ITEM_SALE'					=> 'Item sale',
	'ACP_ASS_ITEM_SALE_DESC'			=> 'The period this item will be on sale.',
	'ACP_ASS_ITEM_SALE_PRICE'			=> 'Item sale price',
	'ACP_ASS_ITEM_SALE_PRICE_DESC'		=> 'The price that will be used when this item is on sale.',
	'ACP_ASS_ITEM_SLUG'					=> 'Item slug',
	'ACP_ASS_ITEM_STACK'				=> 'Item stack count',
	'ACP_ASS_ITEM_STACK_DESC'			=> 'The amount of identical items that can be in an inventory at the same time.',
	'ACP_ASS_ITEM_STOCK'				=> 'Item stock',
	'ACP_ASS_ITEM_STOCK_DESC'			=> 'The current item’s stock.',
	'ACP_ASS_ITEM_STOCK_THRESHOLD'		=> 'Item stock threshold',
	'ACP_ASS_ITEM_STOCK_THRESHOLD_DESC'	=> 'When the stock reaches this threshold, a notification is send to the authorized users.',
	'ACP_ASS_ITEM_STOCK_UNLIMITED'		=> 'Item stock unlimited',
	'ACP_ASS_ITEM_STOCK_UNLIMITED_DESC'	=> 'Whether or not this item’s stock is unlimited.',
	'ACP_ASS_ITEM_STR_TO_TIME'			=> 'Examples of valid formatted time strings are',
	'ACP_ASS_ITEM_STR_TO_TIME_DESC'		=> 'Any valid formatted time string for <a href="https://www.php.net/manual/en/function.strtotime.php"><code>strtotime()</code></a>.',
	'ACP_ASS_ITEM_TIMEZONE_BOARD'		=> 'The current board’s time',
	'ACP_ASS_ITEM_TIMEZONE_YOUR'		=> 'Your current time',
	'ACP_ASS_ITEM_TIMEZONE_DESC'		=> 'For consistency reasons all dates are in the board’s timezone.',
	'ACP_ASS_ITEM_TITLE'				=> 'Item title',
	'ACP_ASS_ITEM_TYPE'					=> 'Item type',
	'ACP_ASS_ITEM_TYPE_SELECT'			=> 'Select an item type',
	'ACP_ASS_ITEM_TYPE_NO_AUTH'			=> 'You are not authorised to manage this item type.',
	'ACP_ASS_ITEMS_PER_PAGE'			=> 'Items per page',

	'ACP_ASS_LOCATIONS'					=> 'Link locations',
	'ACP_ASS_LOCATIONS_DESC'			=> 'Determine where the link to the Shop page should be displayed.',

	'ACP_ASS_LOG_DELETED_ALL'			=> 'You have successfully deleted all log entries.',
	'ACP_ASS_LOG_DELETED_ENTRY'			=> 'You have successfully deleted this log entry.',
	'ACP_ASS_LOG_DELETED_ENTRIES'		=> 'You have successfully deleted these log entries.',
	'ACP_ASS_LOGS_PER_PAGE'				=> 'Logs per page',

	'ACP_ASS_NO_IMAGE_ICON'				=> '“No image” icon',
	'ACP_ASS_NO_ITEMS_SELECTED'			=> 'You haven’t selected any items.',
	'ACP_ASS_NOTES'						=> 'Notes',
	'ACP_ASS_NOTES_NO'					=> 'There are no notes',

	'ACP_ASS_NUMBER_CATEGORIES'			=> 'Number of categories',
	'ACP_ASS_NUMBER_CONFLICTS'			=> 'Number of item conflicts',
	'ACP_ASS_NUMBER_FEATURED'			=> 'Number of featured items',
	'ACP_ASS_NUMBER_ITEMS'				=> 'Number of items',
	'ACP_ASS_NUMBER_PURCHASES'			=> 'Number of purchases',
	'ACP_ASS_NUMBER_SALE'				=> 'Number of sale items',
	'ACP_ASS_NUMBER_SPENT'				=> 'Total %s spent',

	'ACP_ASS_OVERVIEW_BIGGEST_BUYERS'		=> 'Biggest buyers',
	'ACP_ASS_OVERVIEW_BIGGEST_BUYERS_NO'	=> 'No biggest buyers',
	'ACP_ASS_OVERVIEW_BIGGEST_GIFTERS'		=> 'Biggest gifters',
	'ACP_ASS_OVERVIEW_BIGGEST_GIFTERS_NO'	=> 'No biggest gifters',
	'ACP_ASS_OVERVIEW_BIGGEST_SPENDERS'		=> 'Biggest spenders',
	'ACP_ASS_OVERVIEW_BIGGEST_SPENDERS_NO'	=> 'No biggest spenders',
	'ACP_ASS_OVERVIEW_FEATURED_NO'			=> 'No featured items',
	'ACP_ASS_OVERVIEW_FEATURED_UPCOMING'	=> 'Upcoming featured',
	'ACP_ASS_OVERVIEW_FEATURED_UPCOMING_NO'	=> 'No upcoming featured items',
	'ACP_ASS_OVERVIEW_LOW_STOCK'			=> 'Low stock',
	'ACP_ASS_OVERVIEW_LOW_STOCK_NO'			=> 'No low stock items',
	'ACP_ASS_OVERVIEW_RECENT_ITEMS'			=> 'Recent items',
	'ACP_ASS_OVERVIEW_RECENT_ITEMS_NO'		=> 'No recent items',
	'ACP_ASS_OVERVIEW_RECENT_PURCHASES'		=> 'Recent purchases',
	'ACP_ASS_OVERVIEW_RECENT_PURCHASES_NO'	=> 'No recent purchases',
	'ACP_ASS_OVERVIEW_SALE_NO'				=> 'No sale items',
	'ACP_ASS_OVERVIEW_SALE_UPCOMING'		=> 'Upcoming sale',
	'ACP_ASS_OVERVIEW_SALE_UPCOMING_NO'		=> 'No upcoming sale items',
	'ACP_ASS_OVERVIEW_SELLERS_LOW'			=> 'Low sellers',
	'ACP_ASS_OVERVIEW_SELLERS_LOW_NO'		=> 'No low selling items',
	'ACP_ASS_OVERVIEW_SELLERS_TOP'			=> 'Top sellers',
	'ACP_ASS_OVERVIEW_SELLERS_TOP_NO'		=> 'No top selling items',

	'ACP_ASS_PANEL_FEATURED'			=> 'Featured',
	'ACP_ASS_PANEL_SALE'				=> 'Sale',
	'ACP_ASS_PANEL_FEATURED_SALE'		=> 'Featured sales',
	'ACP_ASS_PANEL_RANDOM'				=> 'Random',
	'ACP_ASS_PANEL_LIMITED'				=> 'Limited',
	'ACP_ASS_PANEL_RECENT'				=> 'Recent',

	'ACP_ASS_PANEL_BANNER_COLOUR'		=> 'Banner colour',
	'ACP_ASS_PANEL_BANNER_SIZE'			=> 'Banner size',
	'ACP_ASS_PANEL_BANNER_SIZE_NORMAL'	=> 'Normal',
	'ACP_ASS_PANEL_BANNER_SIZE_SMALL'	=> 'Small',
	'ACP_ASS_PANEL_BANNER_SIZE_TINY'	=> 'Tiny',
	'ACP_ASS_PANEL_ICON'				=> 'Icon',
	'ACP_ASS_PANEL_ICON_COLOUR'			=> 'Icon colour',
	'ACP_ASS_PANEL_LIMIT'				=> 'Limit',
	'ACP_ASS_PANEL_ORDER'				=> 'Order',
	'ACP_ASS_PANEL_WIDTH'				=> 'Width',

	'ACP_ASS_PURGE_CACHE'				=> 'Automatically purge cache',
	'ACP_ASS_PURGE_CACHE_DESC'			=> 'This will automatically purge the cache when performing any actions through the “Files” interface.',

	'ACP_ASS_SETTINGS_CAROUSEL'			=> 'Carousel settings',
	'ACP_ASS_SETTINGS_DEFAULT'			=> 'Default settings',
	'ACP_ASS_SETTINGS_DISPLAY'			=> 'Display settings',
	'ACP_ASS_SETTINGS_GIFT'				=> 'Gift settings',
	'ACP_ASS_SETTINGS_INVENTORY'		=> 'Inventory settings',
	'ACP_ASS_SETTINGS_SHOP'				=> 'Shop settings',
	'ACP_ASS_SETTINGS_SPECIAL'			=> 'Special settings',
	'ACP_ASS_SETTINGS_TYPE'				=> 'Type settings',

	'ACP_ASS_SHOP_ENABLED'				=> 'Shop enabled',
	'ACP_ASS_SHOP_ENABLED_DESC'			=> 'This will make the shop completely unavailable. As if it does not exist.',
	'ACP_ASS_SHOP_ACTIVE'				=> 'Shop active',
	'ACP_ASS_SHOP_ACTIVE_DESC'			=> 'This will make the shop unavailable to users who do not have the required permissions.',
	'ACP_ASS_SHOP_INACTIVE_DESC'		=> 'Shop inactive message',
	'ACP_ASS_SHOP_INACTIVE_DESC_DESC'	=> 'This message will be displayed to users who do not have the required permissions when the shop is inactive.',
	'ACP_ASS_SHOP_ICON'					=> 'Shop icon',

	'ACP_ASS_USER_INVENTORY'			=> 'User’s inventory',
]);
