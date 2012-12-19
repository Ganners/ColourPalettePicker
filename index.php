<?php

/**
 * The RGB class, to hold our object better than
 * just storing it in an array.
 */
class RGB {

	/**
	 * The three RGB values
	 * @param int
	 */
	private $red, $green, $blue;

	/**
	 * Creates the RGB object
	 * @param int $r - Red value
	 * @param int $g - Green value
	 * @param int $b - Blue value
	 */
	public function __construct($r, $g, $b) {
		$this->red = $r;
		$this->green = $g;
		$this->blue = $b;
	}

	/**
	 * Returns getHex()
	 * @return string
	 */
	public function __toString() {
		return $this->getHex();
	}

	/**
	 * Returns the red value
	 * @return int
	 */
	public function getRed() {
		return $this->red;
	}

	/**
	 * Returns the green value
	 * @return int
	 */
	public function getGreen() {
		return $this->green;
	}

	/**
	 * Returns the blue value
	 * @return int
	 */
	public function getBlue() {
		return $this->blue;
	}

	/**
	 * Returns the RGB array
	 * @return array
	 */
	public function getRGB() {
		return array($this->red, $this->green, $this->blue);
	}

	/**
	 * Converts RGB to Hex string
	 * @return string
	 */
	public function getHex() {
		$red = str_pad(dechex($this->red), 2, '0');
		$green = str_pad(dechex($this->green), 2, '0');
		$blue =str_pad(dechex($this->blue), 2, '0');
		return "#{$red}{$green}{$blue}";
	}

}

/**
 * Creates a colour scheme based on an image.
 * Not yet sure on implementation method
 */
class Create_Colour_Scheme {

			/**
			 * The image resouces, from GD lib
			 */
    private $imageResource,
			/**
			 * The colour scheme containing final colours
			 */
    		$colourScheme = array(),
			/**
			 * The colour table - probably a table of all colours
			 * and their occurance
			 */
    		$colourTable = array();

    /**
     * Sets up the object
     * @param resource $image_resource - Our GDLib image resource
     */
    public function __construct($image_resource) {
        $this->imageResource = $image_resource;
    }

    /**
     * Processes the image and outputs a colourscheme
     * @param $return_type - What format the colours should be returned (rgb|hex)
     * @return array
     */
    private function createColourTable($return_type = 'rgb') {
    	
    }

}

/****************
Prototype Usage
*****************/

$im = imagecreatefromjpeg('img001.jpg');
$colour_scheme = new Create_Colour_Scheme($im);

$rgb = new RGB(255, 0, 0);
echo $rgb;