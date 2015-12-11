<?php
namespace RLerner\BarCodeAuthenticator;

/**
 * BarCodeAuthenticator - Generate Identifiable Images
 *
 * Provides several methods for generating and reading binary streams, and bar-codes.
 *
 * PHP Version 5.3.0
 *
 * LICENSE: MIT License
 *
 * @category
 * @package BarCodeAuthenticator
 * @author Robert Lerner <rlerne@gmail.com>
 * @copyright 2015 Robert Lerner
 * @license https://opensource.org/licenses/MIT MIT License 1.0
 * @version 0.1.0
 * @link https://github.com/rlerner/BarCodeAuthenticator
 *
 *
 * BarCodeAuthenticator uses Semantic Versioning v2.0.0 [http://www.semver.org/]. In short, anything less than v1.0.0 is considered an unstable alpha.
 */
class BarCodeAuthenticator {

	/**
	 * Pixel Size of Bar Code
	 *
	 * The Bar Code Generator works exclusively in square images, so X and Y will be imageSize.
	 * The size provided must be a multiple of 8, and will determine how many bytes can be stored in the bar code.
	 * For example, if you specified 16, then ((16*16)/8) = 32 8-Bit Bytes.
	 *
	 * @var integer
	 */
	public $imageSize = 16; // if ($i%8!=0) then fail

	/**
	 * X-Coordinate to Start Reading Bar Code
	 *
	 * Bar codes can appear anywhere in the image, set this value to the left most pixel value (starting from 0).
	 * For non-modified barcodes created by this class, set this to zero (0).
	 *
	 * @var integer
	 */
	public $baseReadingOriginX = 0;

	/**
	 * Y-Coordinate to Start Reading Bar Code
	 *
	 * Bar codes can appear anywhere in the image, set this value to the top most pixel value (starting from 0).
	 * For non-modified barcodes created by this class, set this to zero (0).
	 *
	 * @var integer
	 */
	public $baseReadingOriginY = 0;

	/**
	 * Returns Binary Stream from String
	 *
     * This method parses an ASCII String into bytes (8-bit chunks) and outputs a binary stream.
     *
	 * @param String Data Stream of ASCII Characters, that must be (($imageSize*$imageSize)/8) characters in length.
     * @return Binary Stream Representation of String
     * @access public
     * @since Method available since 0.1.0
	*/
	public function stringToBinaryStream($string) {
		if (strlen($string)!=(($this->imageSize*$this->imageSize)/8)) {
			throw New Exception("Invalid String Length, expecting " . (($this->imageSize*$this->imageSize)/8) . " characters");
		}
		$hashSplit = str_split($string);
		$outStr = '';
		foreach($hashSplit as $v) {
			$outStr .= str_pad(decbin(ord($v)),8,'0',STR_PAD_LEFT);
		}
		return $outStr;
	}

	/**
	 * Returns a String Representation of a Binary Stream
	 *
     * This method parses a binary stream into 8-bit chunks and outputs the ASCII Value correlating with that character.
     *
	 * @param string $binary A Binary Representation of Data in 8-bit chunks.
     * @return String representation of the Binary Stream
     * @access public
     * @since Method available since 0.1.0
	*/
	public function binaryStreamToString($binary) {
		$hashSplit = str_split($binary,8);
		$outStr = '';
		foreach($hashSplit as $v) {
			$outStr .= chr(bindec($v));
		}
		return $outStr;
	}

	/**
	 * Returns a Bar Code Image Representation of a Binary Stream
	 *
     * Returns a GD Library image.
     *
	 * @param string Binary Stream
     * @return Resource GD Library Resource containing the bar code image.
     * @access public
     * @since Method available since 0.1.0
	*/
	public function streamToImage($stream) {
		$iterator = 0;
		$im = imagecreatetruecolor($this->imageSize,$this->imageSize);
		$color[0] = imagecolorallocate($im,255,255,255);
		$color[1] = imagecolorallocate($im,0,0,0);
		for ($y=0;$y<$this->imageSize;$y++) {
			for ($x=0;$x<$this->imageSize;$x++) {
				imagesetpixel ($im,$x,$y,$color[substr($stream,$iterator,1)]);
				$iterator++;
			}
		}

		return $im;
	}

	/**
	 * Posterize a color value
	 *
	 * Posterizes (reduces the color to min or max). Works by converting a truecolor pixel to grayscale by averaging R,G, and B values,
	 * and sets $min to averages between 0-127, and $max to averages between 128-255 (if threshold default is used.)
     *
     * @param integer $val Color Value to Posterize
     * @param integer $min Low-End Posterization Color
     * @param integer $max High-End Posterization Color
     * @param integer $threshold Value between 0-255 dictating the posterization break point.
     * @return integer Representing Posterized Value
     * @access public
     * @since Method available since 0.1.0
	*/
	public function posterizeValue($val,$min=0,$max=16777215,$threshold=127) {
		$r = ($val >> 16) & 0xFF;
		$g = ($val >> 8) & 0xFF;
		$b = $val & 0xFF;
		$bwColor = ($r+$g+$b)/3;
		$val = $min;
		if ($bwColor>$threshold) {
			$val = $max;
		}
		return $val;
	}

	/**
	 * Returns a Binary Stream from an Image
	 *
     * Reads a GD Library Image resource containing a barcode, and returns a binary stream of data.
     *
	 * @param Resource GD Library Image Resource containing a Bar Code
     * @return Binary Stream Representation of Bar Code
     * @access public
     * @since Method available since 0.1.0
	*/
	public function imageToStream($im) {
		for ($y=0;$y<$this->imageSize;$y++) {
			for ($x=0;$x<$this->imageSize;$x++) {
				$col = imagecolorat($im,$x+$this->baseReadingOriginX,$y+$this->baseReadingOriginY);
				$col = $this->posterizeValue($col);
				//Compare Color
				if ($col==16777215) {
					$outStr .= 0;
				} else {
					$outStr .= 1;
				}
			}
		}
	return $outStr;
	}
}
