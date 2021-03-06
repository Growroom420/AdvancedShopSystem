<?php
/**
 *
 * phpBB Studio - Advanced Shop System. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbstudio\ass\notification\type;

/**
 * phpBB Studio - Advanced Shop System: Notification type "Gift"
 */
class gift extends \phpbb\notification\type\base
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbbstudio\ass\helper\router */
	protected $router;

	/** @var \phpbb\user_loader */
	protected $user_loader;

	/**
	 * Set the auth object.
	 *
	 * @param  \phpbb\auth\auth					$auth			Auth object
	 * @return void
	 * @access public
	 */
	public function set_auth(\phpbb\auth\auth $auth)
	{
		$this->auth = $auth;
	}

	/**
	 * Set the Advanced Shop System router object.
	 *
	 * @param  \phpbbstudio\ass\helper\router	$router			ASS Router object
	 * @return void
	 * @access public
	 */
	public function set_router(\phpbbstudio\ass\helper\router $router)
	{
		$this->router = $router;
	}

	/**
	 * Set the user loader object.
	 *
	 * @param  \phpbb\user_loader				$user_loader	User loader object
	 * @return void
	 * @access public
	 */
	public function set_user_loader(\phpbb\user_loader $user_loader)
	{
		$this->user_loader = $user_loader;
	}

	/**
	 * Get notification type name.
	 *
	 * @return string			The notification name as defined in services.yml
	 * @access public
	 */
	public function get_type()
	{
		return 'phpbbstudio.ass.notification.type.gift';
	}

	/**
	 * Notification option data (for outputting to the user).
	 *
	 * @var    bool|array		False if the service should use it's default data
	 * 							Array of data (including keys 'id', 'lang', and 'group')
	 * @access public
	 * @static
	 */
	static public $notification_option = [
		'lang'	=> 'ASS_NOTIFICATION_TYPE_GIFT',
		'group'	=> 'ASS_NOTIFICATION_GROUP',
	];

	/**
	 * Is this type available to the current user.
	 * (defines whether or not it will be shown in the UCP Edit notification options)
	 *
	 * @return bool				Whether or not this is available to the user
	 * @access public
	 */
	public function is_available()
	{
		return $this->auth->acl_get('u_ass_can_receive_gift');
	}

	/**
	 * Get the id of the notification.
	 *
	 * @param  array	$data	The notification type specific data
	 * @return int				Identifier of the notification
	 * @access public
	 * @static
	 */
	public static function get_item_id($data)
	{
		return $data['notification_id'];
	}

	/**
	 * Get the id of the parent.
	 *
	 * @param  array	$data	The type notification specific data
	 * @return int				Identifier of the parent
	 * @access public
	 * @static
	 */
	public static function get_item_parent_id($data)
	{
		return $data['inventory_id'];
	}

	/**
	 * Find the users who want to receive notifications.
	 *
	 * @param array $data		The type specific data
	 * @param array $options	Options for finding users for notification
	 * 				  ignore_users => array of users and user types that should not receive notifications from this type
	 *              				  because they've already been notified
	 * 				  e.g.: array(2 => array(''), 3 => array('', 'email'), ...)
	 * @return array			Array of user identifiers with their notification method(s)
	 * @access public
	 */
	public function find_users_for_notification($data, $options = [])
	{
		return [
			$data['recipient_id'] => $this->notification_manager->get_default_methods(),
		];
	}

	/**
	 * Users needed to query before this notification can be displayed.
	 *
	 * @return array			Array of user identifiers to query.
	 * @access public
	 */
	public function users_to_query()
	{
		return [$this->get_data('user_id')];
	}

	/**
	 * Get the user's avatar.
	 *
	 * @return string			The HTML formatted avatar
	 */
	public function get_avatar()
	{
		return $this->user_loader->get_avatar($this->get_data('user_id'), false, true);
	}

	/**
	 * Get the HTML formatted title of this notification.
	 *
	 * @return string			The HTML formatted title
	 * @access public
	 */
	public function get_title()
	{
		return $this->language->lang('ASS_NOTIFICATION_GIFT', $this->user_loader->get_username($this->get_data('user_id'), 'no_profile'));
	}

	/**
	 * Get the url to this item.
	 *
	 * @return string			URL to the Inventory page
	 * @access public
	 */
	public function get_url()
	{
		$index = $this->get_data('inventory_index');
		$index = $index ? $index : 1;

		return $this->router->inventory($this->get_data('category_slug'), $this->get_data('item_slug'), $index, 'gift');
	}

	/**
	 * Get email template.
	 *
	 * @return string|bool		Whether or not this notification has an email option template
	 * @access public
	 */
	public function get_email_template()
	{
		return false;
	}

	/**
	 * Get email template variables.
	 *
	 * @return array			Array of variables that can be used in the email template
	 * @access public
	 */
	public function get_email_template_variables()
	{
		return [];
	}

	/**
	 * Function for preparing the data for insertion in an SQL query.
	 * (The service handles insertion)
	 *
	 * @param  array	$data				The type specific data
	 * @param  array	$pre_create_data	Data from pre_create_insert_array()
	 * @return void
	 * @access public
	 */
	public function create_insert_array($data, $pre_create_data = [])
	{
		$this->set_data('inventory_index', $data['inventory_index']);
		$this->set_data('category_slug', $data['category_slug']);
		$this->set_data('item_slug', $data['item_slug']);
		$this->set_data('user_id', $data['user_id']);

		parent::create_insert_array($data, $pre_create_data);
	}
}
