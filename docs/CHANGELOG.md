# phpBB Studio - Advanced Shop System

#### v1.1.0-beta
- Fixed shop index’ random items not being random
- Fixed category’s items displaying in reverse order
- Fixed inactive category’s items still being visible / usable.
- Fixed ACP item description’s colour palette
  - No longer interferes with the “Create a group” item type palette
  - Now responsive and properly resizes on different screen sizes
- Fixed ACP item prices values
  - Proper minimum and maximum values have been added to the HTML
  - PHP logic has been added in order to check the respective values and show errors
- Fixed ACP item gift percentage, it can now also be negative _(-100% / +999%)_
- Fixed users being able to gift themselves
- Added ACP item _“Gift only”_ configuration
  - Items can be configured so they can only be gifted to an other user and not purchased for personal use
  - Please note, if _Gift only_  is set to Yes, but _Can be gifted_ is set to No, no actions will be shown in the shop
- Added ACP item _“Availability”_ configuration
  - Items can be configured to only be available within a certain time
  - If the item is no longer available, it will not show up in the shop, but it will still show up in users’ inventories
- Added ACP item _“Related items”_ configuration
  - Related items can now be toggled on/off
  - Upto 8 items can be selected to be related to this item
  - If no items are selected, it will show the 4 closest ordered items
- Added ACP item _copy_ functionality
  - Items can now be copied, duplicated, to easily create similar items within a single category
- Added ACP Files module back link
- Added ACP Inventory module
  - Administrators can now manage users’ inventory
  - Either globally add/delete items from user(s) and/or group(s) at once
  - Or adding/deleting items from a single user’s inventory
- Enhanced ACP select boxes, they now use _Select2.js_

#### v1.0.0-beta
 - first release
