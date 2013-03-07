<?php

function __colourPaletteGrabberAutoload($classname) {

	$filename = str_replace("\\", DIRECTORY_SEPARATOR, 
		dirname(__FILE__) . DIRECTORY_SEPARATOR . "{$classname}.php");

	if(file_exists($filename))
		require_once($filename);
	else 
		throw new ErrorException("File for class does not exist: {$filename}");

}

spl_autoload_register('__colourPaletteGrabberAutoload');