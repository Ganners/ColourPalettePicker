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
	 * @param int $colourIndex - RGB integer value of pixel
	 */
	public function __construct($colourIndex) {
		$this->red = ($colourIndex >> 16) & 0xFF;
		$this->green = ($colourIndex >> 8) & 0xFF;
		$this->blue = $colourIndex & 0xFF;
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
	 * Returns the distance from two colours. Using 3D pythagoras
     * @param [int|RGB] $colour
	 */
	public function getDistance($colour) {

        //If the colour isn't currently an RGB object, create one for it
		if(is_int($colour)) {
			$colour = new RGB($colour);
		}

		$distance = sqrt(
				pow($colour->getRed()-$this->getRed(), 2) + 
				pow($colour->getGreen()-$this->getGreen(), 2) +
				pow($colour->getBlue()-$this->getBlue(), 2)
			);


		return $distance;
	}

	/**
	 * Converts RGB to Hex string
	 * @return string
	 */
	public function getHex() {
		$red = str_pad(dechex($this->red), 2, '0');
		$green = str_pad(dechex($this->green), 2, '0');
		$blue = str_pad(dechex($this->blue), 2, '0');
		return "#{$red}{$green}{$blue}";
	}
}