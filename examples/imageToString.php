<?php

//Require the Bar Code authenticator. Circumvents the autoloader in this instance.
require_once '../BarCodeAuthenticator.class.php';

// Create the Bar Code Authenticator Object
$bca = new \RLerner\BarCodeAuthenticator\BarCodeAuthenticator();

// Use GD Library to create an image resource for the image containing the bar code.
$im = imagecreatefrompng("demo.png");

// Since the demo.png image is 100x100, and the bar code is in the bottom right, we need to
// set the base reading offset to 84 for each coordinate.
$bca->baseReadingOriginX = $bca->baseReadingOriginY = 84;

// Convert the image to a binary stream of 100110010101 etc
$stream = $bca->imageToStream($im);

// Convert the binary stream back to ASCII characters, and output.
echo $bca->binaryStreamToString($stream);

