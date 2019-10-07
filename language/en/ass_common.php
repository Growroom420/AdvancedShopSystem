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
	'ASS_ALL'					=> 'All',
	'ASS_AND'					=> ' and ', // The "and" in the following sentence: 2 days, 6 hours and 5 minutes
	'ASS_AVAILABILITY_END_ON'	=> 'Availability will end on %s',

	'ASS_CATEGORY'			=> 'Category',
	'ASS_CATEGORY_INACTIVE'	=> 'This category is currently not active!',
	'ASS_CATEGORY_TITLE'	=> 'Category title',
	'ASS_CATEGORIES_NONE'	=> 'There are no categories yet.',

	'ASS_DELETE'			=> 'Delete',
	'ASS_DELETE_CONFIRM'	=> 'Are you sure you wish to delete this item?',
	'ASS_DELETE_SUCCESS'	=> 'You have successfully deleted this item.',

	'ASS_EXPIRATION_DATE'	=> 'Expiration date',

	'ASS_FEATURED'			=> 'Featured',
	'ASS_FEATURED_ITEMS'	=> 'Featured items',

	'ASS_GIFT'				=> 'Gift', // verb: to gift
	'ASS_GIFT_CONFIRM'		=> 'Are you sure you wish to gift this item?',
	'ASS_GIFT_SUCCESS'		=> 'You have successfully gifted this item.',
	'ASS_GIFT_USER'			=> 'To who do you want to gift this item?',
	'ASS_GIFT_TIME'			=> 'Gift time',
	'ASS_GIFTABLE'			=> 'Giftable',
	'ASS_GIFTED_BY'			=> 'This item was gifted to you by %s',
	'ASS_GIFTS_GIVEN'		=> 'Gifts given',
	'ASS_GIFTS_RECEIVED'	=> 'Gifts received',

	'ASS_HISTORY'			=> 'History',
	'ASS_HISTORY_EMPTY'		=> 'It appears you do not have a shopping history yet',

	'ASS_INVENTORY_ADDED'		=> 'This item has been added to your inventory',
	'ASS_INVENTORY_ADDED_USER'	=> 'This item has been added to %s’s inventory',
	'ASS_INVENTORY_GO'			=> 'Go to your inventory',

	'ASS_ITEM'					=> 'Item',
	'ASS_ITEM_CREATE_TIME'		=> 'Item creation time',
	'ASS_ITEM_DESCRIPTION'		=> 'Item description',
	'ASS_ITEM_EDIT_TIME'		=> 'Item edit time',
	'ASS_ITEM_EXPIRE_NEVER'		=> 'This item has <span>no</span> expiration date',
	'ASS_ITEM_EXPIRE_SOON'		=> 'This item will expire soon!',
	'ASS_ITEM_EXPIRE_WITHIN'	=> 'This item will expire within <span>%s</span>',
	'ASS_ITEM_EXPIRED'			=> 'This item has expired.',
	'ASS_ITEM_GIFT'				=> 'Gift this item',
	'ASS_ITEM_HELP'				=> 'Item help',
	'ASS_ITEM_ICON'				=> 'Item icon',
	'ASS_ITEM_IMAGE'			=> 'Item image',
	'ASS_ITEM_IMAGES'			=> 'Item images',
	'ASS_ITEM_INFORMATION'		=> 'Item information',
	'ASS_ITEM_INACTIVE'			=> 'This item is currently not active!',
	'ASS_ITEM_ORDER'			=> 'Item order',
	'ASS_ITEM_PRICE'			=> 'Item price',
	'ASS_ITEM_PURCHASE'			=> 'Purchase this item',
	'ASS_ITEM_REFUND_NEVER'		=> 'This item can <span>not</span> be refunded',
	'ASS_ITEM_REFUND_WITHIN'	=> 'This item can be refuned within <span>%s</span>',
	'ASS_ITEM_STOCK'			=> 'Item stock',
	'ASS_ITEM_TITLE'			=> 'Item title',
	'ASS_ITEM_TYPE'				=> 'Item type',
	'ASS_ITEM_TYPE_NOT_EXIST'	=> 'The requested item type does not exist',
	'ASS_ITEM_USE_BEFORE'		=> 'Make sure to use this item before',
	'ASS_ITEM_USE_COUNT'		=> [
		1	=> 'This item can be used <span>%d</span> time',
		2	=> 'This item can be used <span>%d</span> times',
	],
	'ASS_ITEM_USE_REACHED'		=> 'You have reached the use limit for this item.',
	'ASS_ITEM_USE_UNLIMITED'	=> 'This item can be used <span title="Unlimited">&infin;</span> times', // &infin; is the infinite sign
	'ASS_ITEMS_NONE'			=> 'There are no items yet.',
	'ASS_ITEMS_NONE_INVENTORY'	=> 'It appears you do not have any items yet',
	'ASS_ITEMS_RECENT'			=> 'Recent items',
	'ASS_ITEMS_LIMITED'			=> 'Limited items',
	'ASS_ITEMS_COUNT'			=> [
		1	=> '%d item',
		2	=> '%d items',
	],

	'ASS_INVENTORY'			=> 'Inventory',

	'ASS_POINTS_BALANCE'	=> 'Your new %s balance is',
	'ASS_POINTS_DEDUCTED'	=> 'Deducted from your account',
	'ASS_POINTS_NEW'		=> '%s new',
	'ASS_POINTS_OLD'		=> '%s old',
	'ASS_PRICE_ABOVE'		=> 'Price above',
	'ASS_PRICE_BELOW'		=> 'Price below',
	'ASS_PURCHASE'			=> 'Purchase', // verb: to purchase
	'ASS_PURCHASE_CONFIRM'	=> 'Are you sure you wish to purchase this item?',
	'ASS_PURCHASE_SUCCESS'	=> 'You have successfully purchased this item.',
	'ASS_PURCHASE_TIME'		=> 'Purchase time',
	'ASS_PURCHASES'			=> 'Purchases', // plural noun

	'ASS_RECIPIENT_NAME'	=> 'Recipient name',
	'ASS_REFUND'			=> 'Refund',
	'ASS_REFUND_BEFORE'		=> 'Refundable before',
	'ASS_REFUND_CONFIRM'	=> 'Are you sure you wish to refund this item?',
	'ASS_REFUND_SUCCESS'	=> 'You have successfully refunded this item.',
	'ASS_RELATED_ITEMS'		=> 'Related items',

	'ASS_ON_SALE'			=> 'On sale',
	'ASS_SALE'				=> 'Sale',
	'ASS_SALE_END_ON'		=> 'Sale will end on %s',
	'ASS_SALE_DISCOUNT'		=> 'Sale discount',
	'ASS_SALE_ITEMS'		=> 'Sale items',
	'ASS_SALE_PERCENTAGE'	=> 'Sale percentage',
	'ASS_SALE_PRICE'		=> 'Sale price',

	'ASS_SHOP'				=> 'Shop',
	'ASS_SHOP_INACTIVE'		=> 'The shop is currently not active!',
	'ASS_SHOP_INDEX'		=> 'Shop index',
	'ASS_STOCK'				=> 'Stock',
	'ASS_STOCK_CURRENT'		=> 'Current stock',
	'ASS_STOCK_INITIAL'		=> 'Initial stock',
	'ASS_STOCK_OUT'			=> 'Out of Stock',

	'ASS_TIME_CREATED'		=> 'Creation time',
	'ASS_TIME_EDITED'		=> 'Last edit time',

	'ASS_TYPE_FILE'					=> 'Download file',
	'ASS_TYPE_FILE_CONFIRM'			=> 'Are you sure you wish to download this file?',
	'ASS_TYPE_FILE_LOG'				=> 'downloaded a file',
	'ASS_TYPE_FILE_START'			=> 'Your file should start downloading within a few seconds.',
	'ASS_TYPE_FILE_START_NOT'		=> 'If the download does not start automatically, you can click on the download button below.',
	'ASS_TYPE_FILE_SUCCESS'			=> 'You have successfully downloaded this file.',
	'ASS_TYPE_FILE_TITLE'			=> 'File',
	'ASS_TYPE_FILE_NOT_EXIST'		=> 'The requested file does not exist.',
	'ASS_TYPE_POINTS'				=> 'Add %s',
	'ASS_TYPE_POINTS_CONFIRM'		=> 'Are you sure you wish to add these %s?',
	'ASS_TYPE_POINTS_LOG'			=> 'added %s',
	'ASS_TYPE_POINTS_SUCCESS'		=> 'You have successfully added these %s.',
	'ASS_TYPE_POINTS_TITLE'			=> '%s',
	'ASS_TYPE_POINTS_NOT_EMPTY'		=> 'The %s value can not be empty.',

	'ASS_UNAVAILABLE_CATEGORY'		=> 'Category is unavailable',
	'ASS_UNAVAILABLE_ITEM'			=> 'Item is unavailable',
	'ASS_UNAVAILABLE_TYPE'			=> 'Item type is unavailable',
	'ASS_UNLIMITED'			=> 'Unlimited',
	'ASS_UNLIMITED_STOCK'	=> 'Unlimited stock',
	'ASS_USAGES'			=> 'Usages',
	'ASS_USED_LAST'			=> 'Last used',

	'ASS_DAYS'				=> [
		1	=> '%d day',
		2	=> '%d days',
	],
	'ASS_HOURS'				=> [
		1	=> '%d hour',
		2	=> '%d hours',
	],
	'ASS_MINUTES'			=> [
		1	=> '%d minute',
		2	=> '%d minutes',
	],
	'ASS_SECONDS'			=> [
		1	=> '%d second',
		2	=> '%d seconds',
	],

	'ASS_LOG_ITEM_GIFTED'			=> 'Gave a gift to %s',
	'ASS_LOG_ITEM_PURCHASED'		=> 'Purchased an item',
	'ASS_LOG_ITEM_RECEIVED'			=> 'Received a gift from %s',
	'ASS_LOG_ITEM_USED'				=> 'Used an item',

	'ASS_ERROR_TITLE'				=> 'Oh oh, it looks like you found our secret pot of %s',
	'ASS_ERROR_LOGGED'				=> 'This error has been logged. You can notify an Administrator to check the “Admin logs”.',

	'ASS_ERROR_ALREADY_EXISTS'		=> 'The requested %s already exists.',
	'ASS_ERROR_NOT_ACTIVE_CATEGORY'	=> 'The requested category is currently not active.',
	'ASS_ERROR_NOT_ACTIVE_ITEM'		=> 'The requested item is currently not active.',
	'ASS_ERROR_NOT_AUTH_GIFT'		=> 'You are not authorised to gift items.',
	'ASS_ERROR_NOT_AUTH_PURCHASE'	=> 'You are not authorised to purchase items.',
	'ASS_ERROR_NOT_AUTH_RECEIVE'	=> 'The requested user is not authorised to receive gifts.',
	'ASS_ERROR_NOT_AVAILABLE'		=> 'The requested item is no longer available.',
	'ASS_ERROR_NOT_ENOUGH_POINTS'	=> 'You currently do not have enough %s to purchase this item.',
	'ASS_ERROR_NOT_EXISTS'			=> 'The requested %s does not yet exists.',
	'ASS_ERROR_NOT_FOUND_CATEGORY'	=> 'The requested category could not be found.',
	'ASS_ERROR_NOT_FOUND_ITEM'		=> 'The requested item could not be found.',
	'ASS_ERROR_NOT_FOUND'			=> 'The requested %s could not be found.',
	'ASS_ERROR_NOT_GIFTABLE'		=> 'The requested item can not be gifted.',
	'ASS_ERROR_NOT_GIFT_SELF'		=> 'You are not allowed to gift yourself.',
	'ASS_ERROR_NOT_OWNED'			=> 'You currently do not own this item.',
	'ASS_ERROR_NOT_OWNED_STACK'		=> 'You currently do not own this many of this item.',
	'ASS_ERROR_NOT_REFUND'			=> 'You are no longer authorised to refund this item. You have already used it.',
	'ASS_ERROR_NOT_UNIQUE'			=> 'The field “%1$s” contains a non-unique value. The value is already in use by “%2$s”.',
	'ASS_ERROR_OUT_OF_STOCK'		=> 'The requested item is currently out of stock.',
	'ASS_ERROR_STACK_LIMIT'			=> 'You currently already own the maximum allowed copies of this item.',
	'ASS_ERROR_STACK_LIMIT_USER'	=> '%s currently already owns the maximum allowed copies of this item.',
	'ASS_ERROR_STACK_NO_AUTH'		=> 'You are not authorised to own multiple copies of the same items.',
	'ASS_ERROR_STACK_NO_AUTH_USER'	=> '%s is not authorised to own multiple copies of the same items.',
	'ASS_ERROR_TOO_HIGH'			=> 'The field “%1$s” is too high. It must be lower than %2$s and it currently is %3$s.',
	'ASS_ERROR_TOO_LONG'			=> 'The field “%1$s” is too long. It must have less than %2$s characters and currently has %3$s characters.',
	'ASS_ERROR_TOO_LOW'				=> 'The field “%1$s” is too low. It must be higher than %2$s and it currently is %3$s.',
	'ASS_ERROR_TOO_SHORT'			=> 'The field “%1$s” is too short. It must have more than %2$s characters and currently has %3$s characters.',
	'ASS_ERROR_UNSIGNED'			=> 'The field “%s” is negative. It must have a positive value.',
	'ASS_ERROR_ILLEGAL_CHARS'		=> 'The field “%s” contains illegal characters.',

	'ASS_WARNING_STACK'				=> [
		1 => 'You currently already own %d copy of this item.',
		2 => 'You currently already own %d copies of this item.',
	],
]);
