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
     * Create a Carbon instance from a specific format
     *
     * @param  string              $format
     * @param  string              $time
     * @param  \DateTimeZone|string|null $tz
     *
     * @return static
     *
     * @throws \InvalidArgumentException
     */
    public static function createFromFormat($format, $time, $tz = null)
    {
        if ($tz !== null) {
            $dt = parent::createFromFormat($format, $time, static::safeCreateDateTimeZone($tz));
        } else {
            $dt = parent::createFromFormat($format, $time);
        }

        if ($dt instanceof \DateTime) {
            return static::instance($dt);
        }

        $errors = static::getLastErrors();
        throw new \InvalidArgumentException(implode(PHP_EOL, $errors['errors']));
    }

	/**
	 * @param $time
     * @param  \DateTimeZone|string|null $tz
	 * @return static
	 * @throws \InvalidArgumentException
	 */
	public static function createFromDateTime($time, $tz = null)
	{
		$format = static::$datetime_format;

		return static::createFromFormat($format, $time, $tz);
	}

    /**
     * @param $time
     * @param  \DateTimeZone|string|null $tz
     * @return static
     * @throws \InvalidArgumentException
     */
    public static function createFromDateTimeT($time, $tz = null)
    {
        $format = static::$datetime_t_format;

        return static::createFromFormat($format, $time, $tz);
    }

	/**
	 * Create a Carbon instance from a specific format
     * Fails gracefully to null
	 *
	 * @param  string              $format
	 * @param  string              $time
	 * @param  \DateTimeZone|string $tz
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
     * Fails gracefully to null
     *
     * @param string              $time
     * @param \DateTimeZone|string $tz
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

    /**
     * Closest quarter of an hour
     *
     * @return static
     */
    public function closestQuarterOfHour()
    {
        $quarter = 15;
        $delta = $this->minute % $quarter;

        $minutes_to_add = ( $delta == 0 ) ? 0 : $quarter - $delta;

        return $this
            ->addMinutes( $minutes_to_add )
            ->second(0);
    }

    /**
     * Modifies(increments) date basing on interval made from interval string argument
     * e.g. '2 months', 'month-3', '2 minutes 20 seconds' etc.
     *
     * @param string $interval
     * @param bool $reverse
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function addFromIntervalString($interval, $reverse = false){
        $intervals_number = preg_match_all('/((\d+(\s|-)*)[A-z]+)|([A-z]+(-*\d+)|([A-z]+))/', $interval, $matches);

        if( $intervals_number > 0 )
            $intervals = $matches[0];
        else
            $intervals = array();

        foreach($intervals as $interval)
        {
            if( preg_match('/\d+/', $interval, $matches) )
            {
                $quantity = intval($matches[0]);
            }
            else
            {
                $quantity = 1;
            }

            if( preg_match('/([A-z]+)/', $interval, $matches) )
            {
                $period = preg_replace('/s$/', '', $matches[0]);
            }
            else
            {
                throw new \InvalidArgumentException('Invalid interval string provided: ' . $interval);
            }

            if( $reverse )
                $method_prefix = 'sub';
            else
                $method_prefix = 'add';

            $method = $method_prefix . mb_convert_case($period, MB_CASE_TITLE) . 's';

            if( ! method_exists($this, $method))
            {
                throw new \InvalidArgumentException('Invalid interval string provided: ' . $interval);
            }

            $this->$method($quantity);

        }

        return $this;
    }

    /**
     * Modifies(decrements) date basing on interval made from interval string argument
     * e.g. '2 months', 'month-3', '2 minutes 20 seconds' etc.
     *
     * @param string $interval
     * @return $this
     */
    public function subFromIntervalString($interval){
        return $this->addFromIntervalString($interval, true);
    }

}