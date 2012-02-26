<?php

class City
{
	public static $timeline = array
	(
		0 => 'night',
		5 => 'morning',
		12 => 'afternoon',
		17 => 'evening',
		22 => 'night'
	);

	public static function get($file)
	{
		if(!file_exists($file))
		{
			return false;
		}
		$cities = array();

		$rows = explode("\n", file_get_contents($file));

		$city = null;

		foreach($rows as $row)
		{
			if(trim($row) == '')
			{
				continue;
			}
			if(substr($row, 0, 1) == "\t")
			{
				list($key, $value) = explode(':', trim($row));

				$city->$key = trim($value);

				continue;
			}
			$cities[] = $city = new City($row);
		}
		return self::sort($cities);
	}
	public static function sort($cities)
	{
		$first = -1;

		foreach($cities as $k => $city)
		{
			if($city->get_period() == 'morning')
			{
				$first = $k;

				break;
			}
		}
		$before = array_slice($cities, 0, $first);
		$after = array_slice($cities, $first);

		return array_merge($after, $before);
	}

	public $name;
	public $zone;
	public $city;

	public $font;
	public $side;

	public $morning;
	public $afternoon;
	public $evening;
	public $night;

	public $woeid;
	public $sunrise;
	public $sunset;

	public function __construct($name)
	{
		$this->name = $name;
		$this->zone = 0;

		$this->font = 'OpenSans-Regular.ttf';
		$this->side = 'left';

		$this->messages = new stdClass();
	}

	public function get_light()
	{
		if(!$this->get_woeid())
		{
			return 1;
		}
		$url = sprintf
		(
			'http://weather.yahooapis.com/forecastrss?w=%d&u=c', $this->woeid
		);
		if(!$contents = file_get_contents($url))
		{
			return 1;
		}
		if(!preg_match('/sunrise="(.*?)"/', $contents, $matches))
		{
			return 1;
		}
		$this->sunrise = $this->get_minutes_from_text($matches[1]);

		if(!preg_match('/sunset="(.*?)"/', $contents, $matches))
		{
			return 1;
		}
		$this->sunset = $this->get_minutes_from_text($matches[1]);

		$current = $this->get_hour() * 60 + (int)date('i');

		$A = 120;
		$B = $A * 2;

		if($current <= $this->sunrise - $A || $current >= $this->sunset + $A)
		{
			return 0;
		}
		if($current >= $this->sunrise + $A && $current <= $this->sunset - $A)
		{
			return 1;
		}
		if($current < $this->sunrise)
		{
			return ($A - ($this->sunrise - $current)) / $B;
		}
		if($current < $this->sunrise + $A)
		{
			return ($current - $this->sunrise) / $B + 0.5;
		}
		if($current < $this->sunset)
		{
			return ($this->sunset - $current) / $B + 0.5;
		}
		return 0.5 - ($current - $this->sunset) / $B;
	}

	public function get_woeid()
	{
		if($this->woeid)
		{
			return $this->woeid;
		}
		$city = $this->city ? $this->city : $this->name;

		$query = urlencode(sprintf
		(
			'select * from geo.places where text="%s"', $city
		));
		$url = sprintf
		(
			'http://query.yahooapis.com/v1/public/yql?q=%s&format=xml', $query
		);
		if(!$contents = file_get_contents($url))
		{
			return false;
		}
		$xml = new SimpleXMLElement($contents);

		foreach($xml->results->place as $k => $place)
		{
			if($place->placeTypeName == 'Town')
			{
				return $this->woeid = $place->woeid;
			}
		}
		return false;
	}

	public function get_message()
	{
		$period = $this->get_period();

		if(isset($this->$period))
		{
			return $this->$period;
		}
		return 'hello';
	}

	public function get_period()
	{
		$hour = $this->get_hour();

		$period = 'night';

		foreach(self::$timeline as $h => $p)
		{
			if($h > $hour)
			{
				break;
			}
			$period = $p;
		}
		return $period;
	}

	public function get_hour()
	{
		$diff = $this->zone - floor($this->zone);

		$hour = (date('G') + floor($this->zone) + 24) % 24;

		return $hour + $diff;
	}

	private function get_minutes_from_text($text)
	{
		$hour = preg_replace('/[^0-9:]/', '', $text);

		list($hours, $minutes) = explode(':', $hour);

		if(strpos($text, 'pm') !== false)
		{
			$hours += 12;
		}
		return $hours * 60 + $minutes;
	}
}