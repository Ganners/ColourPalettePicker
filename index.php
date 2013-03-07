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

/**
 * Creates a colour scheme based on an image.
 * Not yet sure on implementation method
 */
class Create_Colour_Scheme {

			/**
			 * The image resouces
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
        
        /**
         * Initial solution to the problem. Initially slow and unreliable with optimizations
         * 
         * $this->_frequencyTable = $this->_applyFrequencyWeights($this->_frequencyTable);
         * 
         */

        /**
         * New solution, using an array iterator to compare with the next item, reducing the
         * number of loops significantly.
         */
        $this->_frequencyTable = $this->_mergeFrequencyColours($this->_frequencyTable);

        $this->_colourScheme = $this->_convertColourArrayToRGB($this->_frequencyTable);
    }

    /**
     * Returns the colour scheme if it's not empty,
     * else it returns false
     * 
     * @return array|false
     */
    public function getColourScheme() {
    	if(!empty($this->_colourScheme))
    		return $this->_colourScheme;
    	else
    		return FALSE;
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
     * Creates a frequency table to group same colours
     * together for faster processing in the next stage
     * 
     * @param  array $colourTable
     * @return array
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
     * 1. Order by RGB
     * 2. While the count of the array is less than our target frequency
     *    Loop through each colour
     * 3. If the colour is in threshold of 1 than the other, merge
     *    repeat with threshold+1
     * 4. Order by frequency
     * 
     * @param array $frequencyTable
     * @param array $targetTableLength - How long we'd like the table to end up
     * 
     * The accuracy is determined by the threshold. The lower the threshold the more loops
     * and so the slower the calculation.
     */
    private function _mergeFrequencyColours(array $frequencyTable, $targetTableLength = 10) {

        $threshold = 5;
        $thresholdIncrementations = 5;

        //Order by the RGB key index
        ksort($frequencyTable);

        //Set table pointer to the beginning
        reset($frequencyTable);

        while(count($frequencyTable) >= $targetTableLength) {
            $currentRGB = current($frequencyTable);
            $currentRGBKey = key($frequencyTable);
            if(next($frequencyTable)) {

                $nextRGB = current($frequencyTable);
                $nextRGBKey = key($frequencyTable);

                $RGB1 = new RGB($currentRGBKey);
                $RGB2 = new RGB($nextRGBKey);

                if($RGB1->getDistance($RGB2) <= $threshold) {
                    if($currentRGB > $nextRGB) {
                        //Eat it's frequency value and unset
                        $frequencyTable[$currentRGBKey] += $frequencyTable[$nextRGBKey];
                        unset($frequencyTable[$nextRGBKey]);
                    } else {
                        //Eat it's frequency value and unset
                        $frequencyTable[$nextRGBKey] += $frequencyTable[$currentRGBKey];
                        unset($frequencyTable[$currentRGBKey]);
                    }
                }
            } else {
                //Add on our new threshold and repeat
                $threshold += $thresholdIncrementations;
                reset($frequencyTable);
            }
        }

        //Sort it by value
        arsort($frequencyTable);
        return $frequencyTable;

    }

    /**
     * A different solution that compares all items with all items. Problem was it
     * ran a little slow and returned odd results with larger images. Left in
     * for reference.
     */
    private function _applyFrequencyWeights(array $frequencyTable, $threshold = 8) {

    	$weightedFrequencyTable = array();
    	$discardedColours = array();
        $frequencyLimit = 1000;

        // Need to optimize this loop, though it needs to happen!
        // Attempt 1. Cut out the lower frequencies, only use the top 100
        if(count($frequencyTable) > $frequencyLimit)
            $frequencyTable = array_slice($frequencyTable, 0, $frequencyLimit);

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

    					$weightedFrequencyTable[$colour] = $frequency + $frequencyComparison 
    						+ (isset($weightedFrequencyTable[$colour]) ? $weightedFrequencyTable[$colour] : 0);

    					$discardedColours[] = $colourComparison;

    					//Unset incase it has already been added
    					unset($weightedFrequencyTable[$colourComparison]);
    				} else {
    					$weightedFrequencyTable[$colourComparison] = $frequency + $frequencyComparison 
    						+ (isset($weightedFrequencyTable[$colour]) ? $weightedFrequencyTable[$colour] : 0);

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
     * Converts our colour frequency table to an array
     * of RGB objects
     */
    private function _convertColourArrayToRGB(array $frequencyTable) {

        $RGBColourTable = array();

    	foreach($frequencyTable as $colour => $frequency) {
    		$RGBColourTable[$colour] = new RGB($colour);
    	}

    	return $RGBColourTable;
    }

    /**
     * Returns the current image width
     */
	private function _getWidth() {
		return imagesx($this->_imageResource);
	}

    /**
     * returns the current image height
     */
	private function _getHeight() {
		return imagesy($this->_imageResource);
	}

}

/****************
Prototype Usage
*****************/

$im = imagecreatefromjpeg('412446_486798961348575_2026754479_o.jpg');
$colour_scheme = new Create_Colour_Scheme($im);

foreach($colour_scheme->getColourScheme() as $colour) { ?>
	<div style="width: 30px; height: 30px; float: left; background: <?php echo $colour->getHex(); ?>"></div>
<?php } ?>