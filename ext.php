<?php
/**
 *
 * phpBB Studio - Advanced Shop System. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbstudio\ass;

/**
 * phpBB Studio - Advanced Shop System: Extension base
 */
class ext extends \phpbb\extension\base
{
	/**
	 * Indicate whether or not the extension can be enabled.
	 *
	 * @return bool						True if the extension can be enabled, false otherwise
	 * @access public
	 */
	public function is_enableable()
	{
		/** @var \phpbb\extension\manager $ext_manager */
		$ext_manager = $this->container->get('ext.manager');

		if (!$ext_manager->is_enabled('phpbbstudio/aps'))
		{
			$user = $this->container->get('user');
			$lang = $user->lang;

			$user->add_lang_ext('phpbbstudio/ass', 'ass_ext');

			$lang['EXTENSION_NOT_ENABLEABLE'] .= '<br>' . $user->lang('ASS_REQUIRES_APS');

			$user->lang = $lang;

			return false;
		}

		$md_manager = $ext_manager->create_extension_metadata_manager('phpbbstudio/aps');
		$aps_version = (string) $md_manager->get_metadata('version');
		$aps_required = '1.0.5-RC1';

		/** Make sure the APS version is 1.0.5-RC1 or higher */
		if (phpbb_version_compare($aps_version, $aps_required, '<'))
		{
			$user = $this->container->get('user');
			$lang = $user->lang;

			$user->add_lang_ext('phpbbstudio/ass', 'ass_ext');

			$lang['EXTENSION_NOT_ENABLEABLE'] .= '<br>' . $user->lang('ASS_REQUIRES_APS_VERSION', $aps_required);

			$user->lang = $lang;

			return false;
		}

		return true;
	}

	/**
	 * Enable notifications for the extension.
	 *
	 * @param  mixed	$old_state		State returned by previous call of this method
	 * @return mixed					Returns false after last step, otherwise temporary state
	 * @access public
	 */
	public function enable_step($old_state)
	{
		if ($old_state === false)
		{
			/** @var \phpbb\notification\manager $notification_manager */
			$notification_manager = $this->container->get('notification_manager');

			$notification_manager->enable_notifications('phpbbstudio.ass.notification.type.gift');
			$notification_manager->enable_notifications('phpbbstudio.ass.notification.type.stock');

			return 'notification';
		}

		return parent::enable_step($old_state);
	}

	/**
	 * Disable notifications for the extension.
	 *
	 * @param  mixed	$old_state		State returned by previous call of this method
	 * @return mixed					Returns false after last step, otherwise temporary state
	 * @access public
	 */
	public function disable_step($old_state)
	{
		if ($old_state === false)
		{
			try
			{
				if ($this->container->hasParameter('phpbbstudio.ass.extended'))
				{
					$language = $this->container->get('language');
					$language->add_lang('ass_ext', 'phpbbstudio/ass');

					$message = $language->lang('ASS_DISABLE_EXTENDED', $this->container->getParameter('phpbbstudio.ass.extended'));

					// Trigger error for the ACP
					@trigger_error($message, E_USER_WARNING);

					// Throw an exception for the CLI
					throw new \RuntimeException($message);
				}
			}
			catch (\InvalidArgumentException $e)
			{
				// Continue
			}

			/** @var \phpbb\notification\manager $notification_manager */
			$notification_manager = $this->container->get('notification_manager');

			$notification_manager->disable_notifications('phpbbstudio.ass.notification.type.gift');
			$notification_manager->disable_notifications('phpbbstudio.ass.notification.type.stock');

			return 'notification';
		}

		return parent::disable_step($old_state);
	}

	/**
	 * Purge notifications for the extension.
	 *
	 * @param  mixed	$old_state		State returned by previous call of this method
	 * @return mixed					Returns false after last step, otherwise temporary state
	 * @access public
	 */
	public function purge_step($old_state)
	{
		if ($old_state === false)
		{
			/** @var \phpbb\notification\manager $notification_manager */
			$notification_manager = $this->container->get('notification_manager');

			$notification_manager->purge_notifications('phpbbstudio.ass.notification.type.gift');
			$notification_manager->purge_notifications('phpbbstudio.ass.notification.type.stock');

			return 'notification';
		}

		return parent::purge_step($old_state);
	}
}
