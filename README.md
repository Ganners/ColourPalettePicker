Colour Palette Picker
=====================

A PHP class which loops through and image and picks out frequently occuring
colours. Using a weight system it filters these colours into the most dominant
and orders.

Example usage can be found in examples/example1.php

Different speed and performance can be achieved by playing with $_threshold
and $_thresholdIncrementations. The higher the number the greater the
performance, the lower the number the better the accuracy.


- Mark Gannaway