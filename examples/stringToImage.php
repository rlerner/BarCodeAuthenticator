<?php

//Require the Bar Code authenticator. Circumvents the autoloader in this instance.
require_once '../BarCodeAuthenticator.class.php';

// Value to hash to MD5, and then encode into a Bar Code Image (that will then be output to the browser)
$toHash = "This Is A String To Encode";

// Create the Bar Code Authenticator Object
$bca = new \RLerner\BarCodeAuthenticator\BarCodeAuthenticator();

// 16 is the size you want to use for a 32 character string ((16*16)/8) = 32
$bca->imageSize = 16;

// Convert this string to MD5, and then convert it to a binary stream.
$binaryStream = $bca->stringToBinaryStream(md5($toHash));

// Now Convert the Binary Stream to a Bar Coded Image
$imageResource = $bca->streamToImage($binaryStream);

// Set the MIME Type to inform the browser that the document is an image, of type PNG
header("Content-Type: image/png");

// Output the PNG Image
imagepng($imageResource,"demo.png");
