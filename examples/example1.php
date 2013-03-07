<?php

$im = imagecreatefromjpeg('test.jpg');
$palette = new ColourPalette($im);

foreach($palette->getColourScheme() as $colour) { ?>
	<div style="width: 30px; height: 30px; float: left; background: <?php echo $colour->getHex(); ?>"></div>
<?php } ?>