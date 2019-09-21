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
	'ACP_ASS_HELP_TOUR'				=> 'Take the tour',

	'ACP_ASS_ITEM_DATES'			=> 'Item dates',
	'ACP_ASS_ITEM_STATES'			=> 'Item states',
	'ACP_ASS_ITEM_SALE_INFO'		=> 'Item sale info',
	'ACP_ASS_ITEM_STOCK_INFO'		=> 'Item stock info',

	/**
	 * Translators, we are sorry about the HTML in these strings.
	 * It is the only sensible way to 'nicely' show the information.
	 * These lines are seen by an Administrator when taking the “Help” tour.
	 * This tour can be taken when creating/editing an item in the ACP.
	 *
	 * Please leave the new lines in place, as they are rendered as is.
	 */
	'ACP_ASS_HELP_ACTIVE'			=> 'This determines the item’s active status.

By default any new item is <span class="icon-green">activated</span>.
But it is possible to be <span class="icon-red">deactivated</span>, so users without the correct permission (<em>Can view inactive items</em>) can not see or use it.
This allows administrators (or any user with the forementioned permission) to test the view and action of the item.

If an item has conflicts (<em>errors</em>), it will have to be <span class="icon-orange">resolved</span>.
Conflicts occur when the item is activated by a user but there are problems with the item data.
For example the item’s file no longer exists, or some specific item type data is missing or incorrect.
Depending on the <i>Deactivate item with conflicts</i> setting, the item is automatically <span class="icon-red">deactivated</span> and has to be <span class="icon-green">activated</span> again manually.',

	'ACP_ASS_HELP_AVAILABLE'		=> 'This determines the item’s availability.
	
This is an optional field.
All dates are in the board’s timezone.
An elegant date picker has been added for ease-of-use.

The period can be removed by clicking on <span class="icon-red">Clear</span> in the date picker.
Clicking on the information icon (<icon class="icon fa-info fa-fw icon-red"></icon>) will show the board’s current time.

If the current date and time falls within the provided period, this item will be availabe in the shop.
When this item is no longer available, it will not show up in the shop but still be available in a user’s inventory.
If this field is left empty, this item is always available.',

	'ACP_ASS_HELP_BACKGROUND'		=> 'This determines the item’s background.
	
This is an optional field.
Select an image with the build-in filesystem.

This is the main image used to display this item through out the shop and inventory.
The background image displays best with a <i>3:2 ratio</i> (<em>width:height ratio</em>).
An image can be selected with the build-in filesystem interface.
This will show all available images in the shop’s image directory: <samp>./images/aps/</samp>
Clicking on the trash bin (<icon class="icon fa-trash-o fa-fw"></icon>) will remove the image.

When no background is selected, it will default to the <i>Item icon</i>.
If no <i>Item icon</i> is selected either, it will default to the <i>No image icon</i> setting.',

	'ACP_ASS_HELP_COUNT'			=> 'This determines the item’s use count.
	
This is a required field.
The value has to be equal to or greater than 0.
If set to 0, the item can be used <span class="icon-green">unlimited</span> times.

Once a user has purchased this item, it shows up in their inventory.
From their inventory they can <i>activate</i> the item, for example download the file or receive the points.
Every time the user <i>activates</i> the item, the <i>Item use count</i> is increased.
This value determines how many times a user can <i>activate</i> (<em>use</em>) this item.',

	'ACP_ASS_HELP_DATES'			=> 'This shows various item dates.
	
This fieldset is only visible when <strong>editing</strong> an item.

The first date shown is the date and time when this item was created.
The second date shown is the date and time when this item was last edited.',

	'ACP_ASS_HELP_DELETE_STRING'	=> 'This determines the item’s delete period.
	
This is an optional field.
The value has to be a valid <a href="https://www.php.net/strtotime" target="_blank"><code>strtotime()</code></a> string.
This string must be in <strong>English</strong>!

The amount of time after which an item is automatically deleted.
An item can be automatically deleted after it has expired, see <i>Item expire time</i>.
Or it can be automatically deleted after the use count has been reached, see <i>Item use count</i>.
If this is left empty, the users will have to <span class="icon-red">delete these items manually</span>.',

	'ACP_ASS_HELP_DESC'				=> 'This determines the item’s description.
	
This is an optional field.
Smilies, URLs and (custom) BBCodes are automatically rendered.
Any <em>HTML</em> markup will not be rendered and will be shown <i>as is</i>.

A description can be added to better describe the item.
Whether it is to describe what it is for, what it does or provide additional information.
The description is visible both when viewing an item in the shop and in the inventory.',

	'ACP_ASS_HELP_EXPIRE_STRING'	=> 'This determines the item’s expire period.
	
This is an optional field.
The value has to be a valid <a href="https://www.php.net/strtotime" target="_blank"><code>strtotime()</code></a> string.
This string must be in <strong>English</strong>!

The amount of time after which this item will expire.
After this item has expired, it can not longer be used.
If this is left empty, this item will <span class="icon-green">never expire</span>.',

	'ACP_ASS_HELP_FEATURED'			=> 'This determines the item’s featured period.
	
This is an optional field.
All dates are in the board’s timezone.
An elegant date picker has been added for ease-of-use.

The period can be removed by clicking on <span class="icon-red">Clear</span> in the date picker.
Clicking on the information icon (<icon class="icon fa-info fa-fw icon-red"></icon>) will show the board’s current time.

If the current date and time falls within the provided period, this item will be marked as <span class="icon-green">Featured</span>.
When an item is featured, it is shown distinctively and separately in the shop index.
When an item is both on sale and featured, it is shown distinctively and separately in the shop index.',

	'ACP_ASS_HELP_GIFT'				=> 'This determines the item’s gift status.
	
This is a required field.

If gifting is disabled, this item can only be purchased for personal use.
Otherwise this item can be gifted to a different user.
A different price can be set for this item, when a user is gifting it, see <i>Item gift type</i>.',

	'ACP_ASS_HELP_GIFT_ONLY'		=> 'This determines the item’s gift only status.
	
This is a required field.

If this is enabled, this item can only be gifted to other users, meaning it can not be purchased for personal use.
A different price can be set for this item, when a user is gifting it, see <i>Item gift type</i>.\',',

	'ACP_ASS_HELP_GIFT_PERCENTAGE'	=> 'This determines the item’s gift percentage.
	
When the <i>Item gift type</i> is set to <span class="icon-green">percentage</span>, this value will determine the gift price.
The percentage entered here will be added to the initial <i>Item price</i>.
So if the percentage is set to <samp>25%</samp> and the initial price is <samp>10</samp>, the gift price is <samp>12,50</samp>.
The equation would be <samp>10,00 * 1,25 = 12,50</samp>, or <samp>10,00 + (0,25 * 10,00) = 12,50</samp>',

	'ACP_ASS_HELP_GIFT_PRICE'		=> 'This determines the item’s gift price.
	
When the <i>Item gift type</i> is set to <span class="icon-green">price</span>, this value will determine the gift price.
The value entered here will be the gift price. No calculations, just the straight up gift price.',

	'ACP_ASS_HELP_GIFT_TYPE'		=> 'This determines the item’s gift type.
	
This is a required field.

When a user is gifting an item, a different price can be set.
It can either be an <i>Item gift price</i> or <i>Item gift percentage</i>.
This value will determine which price is used.',

	'ACP_ASS_HELP_ICON'				=> 'This determines the item’s icon.

This is an optional field.
It should be a valid <a href="https://fontawesome.com/v4.7.0/icons/" target="_blank">Font Awesome 4.7</a> icon.
An elegant iconpicker has been added for ease-of-use.

An item’s icon has a supplementary role.
It is shown when viewing an item, for aesthetics.
And it is used when there is no <i>Item background</i> provided.',

	'ACP_ASS_HELP_IMAGES'			=> 'This determines the item’s images.
	
This is an optional field.
Select an image with the build-in filesystem.

These images are displayed when viewing this item in the shop.
An image can be selected with the build-in filesystem interface.
This will show all available images in the shop’s image directory: <samp>./images/aps/</samp>
Clicking on the trash bin (<icon class="icon fa-trash-o fa-fw"></icon>) will remove the image.
Clicking on the plus sign (<icon class="icon fa-plus fa-fw"></icon>) will add an additional image row.',

	'ACP_ASS_HELP_PRICE'			=> 'This determines the item’s price.
	
This is a required field.
The value has to be equal to or greater than 0.
If set to 0, the item can be purchased for <span class="icon-green">free</span>.

The default price users have to pay to purchase this item.
A different price can be set when the item is on sale, see <i>Item sale price</i>.
And a different price can be set when this item is being gifted, see <i>Item gift price</i>.',

	'ACP_ASS_HELP_REFUND_STRING'	=> 'This determines the item’s refund period.
	
This is an optional field.
The value has to be a valid <a href="https://www.php.net/strtotime" target="_blank"><code>strtotime()</code></a> string.
This string must be in <strong>English</strong>!

The amount of time before which this item can be refunded.
For example, if this is set to <samp>1 week</samp>, this item can be refunded within one week of the purchase time.
An item can only be refunded when it has not yet been used.
If this is left empty, this item can <span class="icon-red">not be refunded</span>.',

	'ACP_ASS_HELP_RELATED_ENABLED'	=> 'This determines the item’s related items status.
	
This is a required field.

If enabled, this item’s <i>Related items</i> will be displayed in the shop’s item page.
If enabled but no <i>Related items</i> are selected, the closest <em>ordered</em> items will be displayed.',

	'ACP_ASS_HELP_RELATED_ITEMS'	=> 'This determines the item’s related items.
	
This is an optional field.
An elegant select box has been added for ease-of-use.
A maximum of 8 items may be selected.

If <i>Enable related items</i> is enabled, these items will be displayed.
If <i>Enable related items</i> is enabled and no items are selected, the closest <em>ordered</em> items will be displayed.',

	'ACP_ASS_HELP_SALE'				=> 'This determines the item’s sale period.
	
This is an optional field.
All dates are in the board’s timezone.
An elegant date picker has been added for ease-of-use.

The period can be removed by clicking on <span class="icon-red">Clear</span> in the date picker.
Clicking on the information icon (<icon class="icon fa-info fa-fw icon-red"></icon>) will show the board’s current time.

If the current date and time falls within the provided period, this item will be marked as <span class="icon-green">On sale</span>.
When an item is on sale, it is shown distinctively and separately in the shop index and can be purchased for the <i>Item sale price</i>.
When an item is both on sale and featured, it is shown distinctively and separately in the shop index.',

	'ACP_ASS_HELP_SALE_INFO'		=> 'This shows various item sale statistics.
	
This fieldset is only visible when <strong>editing</strong> an item.

Firstly it shows the <i>Item sale price</i>, the price users have to pay when this item is on sale.
Secondly it shows the <i>Item sale discount</i>, the amount of points users save when purchasing this item when it is on sale.
Thirdly it shows the <i>Item sale percentage</i>, the discount percentage of the initial <i>Item price</i>.
Which is calculated with <samp>(initial price - sale price) / initial price * 100%</samp>.
So an example would be: <samp>(50 - 40) / 50 * 100% = 20% discount</samp>',

	'ACP_ASS_HELP_SALE_PRICE'		=> 'This determines the item’s sale price.
	
This is an optional field.
The value has to be equal to or greater than 0.
If set to 0 and the item is on sale, the item is <span class="icon-green">free</span>.

If the item is on sale, this price will be used instead of the initial <i>Item price</i>.
Whether or not the item is on sale, is determined by the <i>Item sale</i> period.',

	// The below <samp> of illegal characters contains an apostrophe: '
	// This needs to be escaped with a backslash in front of it: \
	'ACP_ASS_HELP_SLUG'				=> 'This determines the item’s URL.
	
This is a required field.
The maximum length is 255 characters.
The value has to be unique per category.
It can not contain any spaces or illegal characters: <samp>.,:;!@#$%^&*()[]{}|/\<=>?+~`\'"</samp>	

A slug is used to create a clean (<em>human-friendly</em>) URL.
So the URL in a user’s browser will look something like <i><samp>http://example.com/aps/shop/category-1/<strong>the-item-slug</strong></samp></i>.
If the <i>Item slug</i> has no value yet, it will automatically be created from the <i>Item title</i>.
Afterwards it can still be changed to a different or more appropriate slug.',

	'ACP_ASS_HELP_STATES'			=> 'This shows various item states.
	
This fieldset is only visible when <strong>editing</strong> an item.

The first check indicates whether or not this item is currently on sale, if the current time is within the <i>Item sale</i> period.
The second check indicates whether or not this item is currently featured, if the current time is within the <i>Item featured</i> period.
The third check indicates whether or not this item is currently available, if the current time is within the <i>Item available</i> period or if the period is not set.',

	'ACP_ASS_HELP_STOCK'			=> 'This determines the item’s current stock.
	
This is a required field.
The value has to be equal to or greater than 0.
If set to 0 without unlimited stock, this item is <em class="icon-red">Out of stock</em>.

Representation of the <strong>current</strong> stock.
This is something different than the <i>Initial stock</i>.
If more stock is needed or available, the new available amount can be set.
If the <i>Item stock unlimited</i> is enabled, this value will not change.

The <i>Initial stock</i> is calculated with the current <i>Item stock</i> and adding the purchases.',

	'ACP_ASS_HELP_STOCK_INFO'		=> 'This shows various item stock statistics.
	
This fieldset is only visible when <strong>editing</strong> an item.

The <strong>current</strong> <i>Item stock</i> shows how much stock is currently available.
The <i>Purchases</i> shows how many times this item has been purchased.
It shows the <i>Initial item stock</i>, the total stock that was available. 
Including the times the stock has been amended by an administrator.
The <samp>initial</samp> stock is the sum of the <samp>current</samp> stock and the <samp>purchases.</samp>',

	'ACP_ASS_HELP_STOCK_THRESHOLD'	=> 'This determines the item’s stock notification threshold.
	
This is a required field.
Only works if <i>Item stock unlimited</i> is disabled.

If the <i>Item stock</i> reaches this value, a notification is send to users with the correct permission (<em>Can receive stock notification</em>).
These users also have an option to disable this notification themselves in the <em>User Control Panel</em>.

This notification is intended so these users can amend the stock or take other mandatory actions.
However, it can alternatively be used as an advertisement tool, notifying users that only a few more copies of this item are available.',

	'ACP_ASS_HELP_STOCK_UNLIMITED'	=> 'This determines the item’s unlimited stock status.

This is a required field.
By default any new item has <span class="icon-green">unlimited</span> (<em>&infin;</em>) stock.

If the stock is not unlimited, the value of <i>Item stock</i> is used instead.',

	'ACP_ASS_HELP_TITLE'			=> 'This determines the item’s title.

This is a required field.
The maximum length is 255 characters.
It should be a general or descriptive heading for the item.

If the <i>Item slug</i> does not yet have a value, it will automatically be created from the title when done.',

	'ACP_ASS_HELP_TYPE'				=> 'This determines the item’s type.
	
This is a required field.
By default only the <i>Points</i> and <i>Files</i> item types are available.

Once an <i>Item type</i> is selected, additional fields will show up.
Depending on the type, it is possible to further determine what it is this item exactly does.
For example what file can be downloaded or how many points can be purchased/gifted.

Additional item types can be found over at the <a href="https://www.phpbbstudio.com" target="_blank">phpBB Studio</a>.',
]);
