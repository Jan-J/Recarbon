<?php

namespace Recarbon;

class Carbon extends \Carbon\Carbon {

	public static $date_format = 'd/m/Y';
	public static $semi_date_format = 'd/m/Y H:i';
	public static $full_date_format = 'd/m/Y H:i:s';
    public static $datetime_format = 'Y-m-d H:i:s';
    public static $datetime_t_format = 'Y-m-d\TH:i:s';

	public static $time_format = 'H:i';
	public static $full_time_format = 'H:i:s';

	/**
	 * @param $time
	 * @param null $object
	 * @return static
	 * @throws \InvalidArgumentException
	 */
	public static function createFromDateTime($time, $object = null)
	{
		$format = static::$datetime_format;

		if ($object !== null) {
			$dt = parent::createFromFormat($format, $time, self::safeCreateDateTimeZone($object));
		} else {
			$dt = parent::createFromFormat($format, $time);
		}

		if ($dt instanceof \DateTime) {
			return self::instance($dt);
		}

		$errors = \DateTime::getLastErrors();
		throw new \InvalidArgumentException(implode(PHP_EOL, $errors['errors']));
	}

    /**
     * @param $time
     * @param null $object
     * @return static
     * @throws \InvalidArgumentException
     */
    public static function createFromDateTimeT($time, $object = null)
    {
        $format = static::$datetime_t_format;

        if ($object !== null) {
            $dt = parent::createFromFormat($format, $time, self::safeCreateDateTimeZone($object));
        } else {
            $dt = parent::createFromFormat($format, $time);
        }

        if ($dt instanceof \DateTime) {
            return static::instance($dt);
        }

        $errors = \DateTime::getLastErrors();
        throw new \InvalidArgumentException(implode(PHP_EOL, $errors['errors']));
    }


	/**
	 * Create a Carbon instance from a specific format
	 *
	 * @param  string              $format
	 * @param  string              $time
	 * @param  DateTimeZone|string $tz
	 *
	 * @return static|null
	 *
	 */
	public static function createFromFormatFailGracefully($format, $time, $tz = null){
		try
		{
			return static::createFromFormat($format, $time, $tz);
		}
		catch(\Exception $e)
		{
			return null;
		}
	}

    /**
     * Create a carbon instance from a string.  This is an alias for the
     * constructor that allows better fluent syntax as it allows you to do
     * Carbon::parse('Monday next week')->fn() rather than
     * (new Carbon('Monday next week'))->fn()
     *
     * @param string              $time
     * @param DateTimeZone|string $tz
     *
     * @return static|null
     */
    public static function parseFailGracefully($time, $tz = null){
        try
        {
            return static::parse($time, $tz);
        }
        catch(\Exception $e)
        {
            return null;
        }
    }

	/**
	 * Returns date in format: d/m/Y
	 *
	 * @return string
	 */
	public function toDateHuman()
	{
		return $this->format(static::$date_format);
	}

	/**
	 * Returns date in format: d/m/Y H:i
	 *
	 * @return string
	 */
	public function toSemiDateHuman()
	{
		return $this->format(static::$semi_date_format);
	}

	/**
	 * Returns date in format: d/m/Y H:i:s
	 *
	 * @return string
	 */
	public function toFullDateHuman()
	{
		return $this->format(static::$full_date_format);
	}

	/**
	 * Format the instance as time(H:i)
	 *
	 * @return string
	 */
	public function toShortTimeString()
	{
		return $this->format('H:i');
	}

    /**
     * Format the instance as DateTimeT(Y-m-dTH:i:s)
     *
     * @return string
     */
    public function toDateTimeTString()
    {
        return $this->format(static::$datetime_t_format);
    }

	/**
	 * @param \DateTime $_time
	 * @return static
	 */
	public function setTimeByDateTime(\DateTime $_time){
		return $this->setTime($_time->format('H'), $_time->format('i'), $_time->format('s'));
	}

	/**
	 * @param $dayNumber
	 * @param bool $starts_with_zero
	 * @param bool $first_day_monday
	 * @return \Carbon\Carbon
	 */
	public static function createFromWeekDayNumber($dayNumber, $starts_with_zero = true, $first_day_monday = true)
	{
		if($first_day_monday)
			$format = 'N';
		else
			$format = 'w';

		if($starts_with_zero && $first_day_monday)
			$dayNumber += 1;

		if(!$starts_with_zero && !$first_day_monday)
			$dayNumber -= 1;

		$dt = static::now();

		$offset = $dt->format($format);

		$dt->subDays($offset - $dayNumber);

		return $dt;
	}

	/**
	 * Returns date in iCal format: Ymd
	 *
	 * @return string
	 */
	public function toDateICal()
	{
		return $this->format('Ymd');
	}

	/**
	 * Returns date in iCal format: Ymd\THis
	 *
	 * @return string
	 */
	public function toFullDateICal()
	{
		return $this->format('Ymd\THis');
	}

}