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
	 * 
	 */
	public function getDistance($colour) {
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

/**
 * Creates a colour scheme based on an image.
 * Not yet sure on implementation method
 */
class Create_Colour_Scheme {

			/**
			 * The image resouces, from GD lib
			 */
    private $_imageResource,
			/**
			 * The colour scheme containing final colours
			 */
    		$_colourScheme = array(),
			/**
			 * The colour table - probably a table of all colours
			 * and their occurance
			 */
    		$_colourTable = array();

    /**
     * Sets up the object
     * @param resource $image_resource - Our GDLib image resource
     */
    public function __construct($image_resource) {
        $this->_imageResource = $image_resource;
        $this->_colourTable = $this->_createColourTable();
        $this->_frequencyTable = $this->_createFrequencyTable($this->_colourTable);
        $this->_frequencyTable = $this->_applyFrequencyWeights($this->_frequencyTable);
        var_dump($this->_frequencyTable);
    }

    /**
     * Processes the image and outputs a colourscheme
     * @param $return_type - What format the colours should be returned (rgb|hex)
     * @return array
     */
    private function _createColourTable() {
    	$colourTable = array();
		for($y = 0; $y < $this->_getHeight(); $y++) {
			for($x = 0; $x < $this->_getWidth(); $x++) {
				$RGBValue = imagecolorat($this->_imageResource, $x, $y);
				$colourTable[] = $RGBValue;
			}
		}
		return $colourTable;
    }

    /**
     * 
     */
    private function _createFrequencyTable(array $colourTable) {
    	$frequencyTable = array();
    	foreach($colourTable as $colour) {
    		if(isset($frequencyTable[$colour]))
    			$frequencyTable[$colour] += 1;
    		else
    			$frequencyTable[$colour] = 1;
    	}
    	return $frequencyTable;
    }

    /**
     * 
     */
    private function _applyFrequencyWeights(array $frequencyTable, $threshold = 2) {

    	$weightedFrequencyTable = array();
    	$discardedColours = array();

    	foreach($frequencyTable as $colour => $frequency) {

    		foreach($frequencyTable as $colourComparison => $frequencyComparison) {

    			//Ignore if we're looking at the current key anyway
    			if($colour == $colourComparison)
    				continue;

    			if(in_array($colour, $discardedColours) || in_array($colourComparison, $discardedColours))
    				continue;

    			$RGB = new RGB($colour);
    			$RGBComparison = new RGB($colourComparison);
    			$colourDistance = $RGB->getDistance($RGBComparison);

    			// Check if they are within the threshold. If they are then
    			// the result with the heigher frequency swollows the lower
    			// frequency. (om nom nom nom nom)
    			if($colourDistance < $threshold) {
    				if($frequency > $frequencyComparison) {
    					//echo "Keeping {$colour} and discarding {$colourComparison} as distance is {$colourDistance}<br />";

    					$weightedFrequencyTable[$colour] = $frequency + $frequencyComparison;
    					$discardedColours[] = $colourComparison;

    					//Unset incase it has already been added
    					unset($weightedFrequencyTable[$colourComparison]);
    				} else {
    					//echo "Keeping {$colourComparison} and discarding {$colour} as distance is {$colourDistance}<br />";

    					$weightedFrequencyTable[$colourComparison] = $frequency + $frequencyComparison;
    					$discardedColours[] = $colour;

    					//Unset incase it has already been added
    					unset($weightedFrequencyTable[$colour]);
    				}
    			} else {
    				$weightedFrequencyTable[$colour] = $frequency;
    			}
    		}
    	}
    	return $weightedFrequencyTable;
    }

    /**
     * 
     */
	private function _getWidth() {
		return imagesx($this->_imageResource);
	}

    /**
     * 
     */
	private function _getHeight() {
		return imagesy($this->_imageResource);
	}

}

/****************
Prototype Usage
*****************/

//$im = imagecreatefromjpeg('half_black_half_white.jpg');
$im = imagecreatefromjpeg('half_black_half_white_weight_test.jpg');
$colour_scheme = new Create_Colour_Scheme($im);

