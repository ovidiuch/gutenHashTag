<?php

include('city.php');

class Map
{
	private $cities;
	private $image;

	private $bold = 'OpenSans-Bold.ttf';
	private $light = 'OpenSans-Light.ttf';

	private $big = 14;
	private $small = 10;

	public function generate()
	{
		date_default_timezone_set('Europe/London');

		$this->cities = City::get('cities.txt');

		$this->create_image();

		$this->create_background();

		$this->create_text();
	}

	public function output($quality = 100)
	{
		if(!$this->image)
		{
			$this->generate();
		}
		header('Content-Type: image/jpeg');

		imagejpeg($this->image, null, $quality);

		imagedestroy($this->image);
	}

	public function save($path, $quality = 100)
	{
		if(!$this->image)
		{
			$this->generate();
		}
		return imagejpeg($this->image, $path, $quality);
	}

	private function create_image()
	{
		$this->image = imagecreatetruecolor
		(
			300, ceil(count($this->cities) / 2) * 46 + 6
		);
	}

	private function create_background()
	{
		$bg = imagecolorallocate($this->image, 10, 10, 10);

		imagefill($this->image, 0, 0, $bg);
	}

	private function create_text()
	{
		$x = 10;
		$y = 20;

		foreach($this->cities as $k => $city)
		{
			$light = $city->get_light();

			$g = 75 + (150 * $light);
			$w = 75 + (180 * $light);

			$dark = imagecolorallocate($this->image, 75, 75, 75);
			$medium = imagecolorallocate($this->image, $g, $g, $g);
			$light = imagecolorallocate($this->image, $w, $w, $w);

			if($k && $k % 2 == 0)
			{
				$x = 10;
				$y += 46;
			}
			$this->write
			(
				$city->get_message(), $city->font, $this->small, $medium, $x, $y
			);
			$this->write
			(
				'#', $this->light, $this->big, $dark, $x, $y + 20
			);
			$city_name = str_replace(' ', '', $city->name);

			$this->write
			(
				$city_name, $this->bold, $this->big, $light, $x + 12, $y + 20
			);
			$x += 140;
		}
	}

	private function write($text, $font, $size, $color, $x, $y)
	{
		imagettftext
		(
			$this->image, $size, 0, $x, $y, $color, "fonts/$font", $text
		);
	}
}