<?php
/**
 *
 * phpBB Studio - Advanced Shop System. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbstudio\ass\helper;

/**
 * phpBB Studio - Advanced Shop System: Time helper
 */
class time
{
	/** @var \phpbb\language\language */
	protected $language;

	/** @var string	The board's default timezone */
	protected $timezone;

	/** @var string The default datetime format */
	protected $format = 'd/m/Y H:i';

	const WEEK		= 604800;
	const DAY		= 86400;
	const HOUR		= 3600;
	const MINUTE	= 60;

	/**
	 * Constructor.
	 *
	 * @param  \phpbb\config\config			$config			Config object
	 * @param  \phpbb\language\language		$language		Language object
	 * @return void
	 * @access public
	 */
	public function __construct(
		\phpbb\config\config $config,
		\phpbb\language\language $language
	)
	{
		$this->language	= $language;
		$this->timezone	= $config['board_timezone'];
	}

	/**
	 * Get the board's default timezone.
	 *
	 * @return string					The board's default timezone
	 * @access public
	 */
	public function get_timezone()
	{
		return $this->timezone;
	}

	/**
	 * Get the default datetime format.
	 *
	 * @return string					The default datetime format
	 * @access public
	 */
	public function get_format()
	{
		return $this->format;
	}

	/**
	 * Create and get the UNIX timestamp from a formatted string.
	 *
	 * @param  string	$string			The formatted string
	 * @return int
	 * @access public
	 */
	public function create_from_format($string)
	{
		$tz = date_default_timezone_get();

		date_default_timezone_set($this->timezone);

		$unix = date_timestamp_get(\DateTime::createFromFormat($this->format, $string));

		date_default_timezone_set($tz);

		return (int) $unix;
	}

	/**
	 * Turn an amount of seconds into a readable string.
	 *
	 * For example "1 day, 2 hours and 5 minutes"
	 *
	 * @param  int		$seconds		The amount of seconds
	 * @return string
	 * @access public
	 */
	public function seconds_to_string($seconds)
	{
		$time = [
			'ASS_DAYS'		=> 0,
			'ASS_HOURS'		=> 0,
			'ASS_MINUTES'	=> 0,
			'ASS_SECONDS'	=> 0,
		];

		$time['ASS_DAYS'] = floor($seconds / self::DAY);

		$seconds = ($seconds % self::DAY);
		$time['ASS_HOURS'] = floor($seconds / self::HOUR);

		$seconds %= self::HOUR;
		$time['ASS_MINUTES'] = floor($seconds / self::MINUTE);

		$seconds %= self::MINUTE;
		$time['ASS_SECONDS'] = $seconds;

		$time = array_filter($time);
		$count = count($time);
		$index = 1;
		$string = '';

		foreach ($time as $key => $value)
		{
			if ($index !== 1)
			{
				$string .= $this->language->lang($index === $count ? 'ASS_AND' : 'COMMA_SEPARATOR');
			}

			$string .= $this->language->lang($key, $value);

			$index++;
		}

		return $string;
	}

	/**
	 * Check if the current time is within two UNIX timestamps.
	 *
	 * @param  int		$start			The first UNIX timestamp
	 * @param  int		$until			The second UNIX timestamp
	 * @return bool
	 * @access public
	 */
	public function within($start, $until)
	{
		return (bool) ($start < time() && time() < $until);
	}

	/**
	 * Check if the current time is after a provided UNIX timestamp + additional seconds.
	 *
	 * An example would be that the provided timestamp is a item purchase time
	 * and the additional seconds are the amount of seconds before the item is non-refundable.
	 *
	 * If the additional seconds is 0, it means the item is already non-refundable.
	 *
	 * @param  int		$time			The provided UNIX timestamp
	 * @param  int		$seconds		The additional seconds
	 * @return bool						Whether or not the time has expired
	 * @access public
	 */
	public function has_expired($time, $seconds)
	{
		return (bool) (!empty($seconds) && $time + $seconds <= time());
	}

	/**
	 * Check if the current time is within a week of a provided UNIX timestamp + additional seconds.
	 *
	 * @param  int		$time			The provided UNIX timestamp
	 * @param  int		$seconds		The additional seconds
	 * @return bool						Whether or not the time will expire within a week
	 * @access public
	 */
	public function will_expire($time, $seconds)
	{
		return (bool) (!empty($seconds) && ($time + $seconds) >= (time() - self::WEEK));
	}
}
