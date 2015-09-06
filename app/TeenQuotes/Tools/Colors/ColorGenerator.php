<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Tools\Colors;

use InvalidArgumentException;

class ColorGenerator implements ColorGeneratorInterface
{
    /**
     * Default colors.
     *
     * @var array
     */
    private $colors = [
        '#27ae60', '#16a085', '#d35400', '#e74c3c', '#8e44ad', '#F9690E', '#2c3e50', '#f1c40f', '#65C6BB', '#E08283',
    ];

    /**
     * The current key in the colors' array.
     *
     * @var int
     */
    private static $currentKey = 0;

    /**
     * Get the current color.
     *
     * @return string The hexadecimal color, with a leading #
     */
    public function currentColor()
    {
        return $this->colors[self::$currentKey];
    }

    /**
     * Get the current color and increment the counter.
     *
     * @return string The hexadecimal color, with a leading #
     */
    public function nextColor()
    {
        $color = $this->currentColor();

        $this->incrementColorCounter();

        return $color;
    }

    /**
     * Lighten the current color for a given percentage, between 0 and 100.
     *
     * @param int $percentage
     *
     * @throws InvalidArgumentException The given percentage is not between 0 and 100
     *
     * @return string The hexadecimal color, with a leading #
     */
    public function lighten($percentage)
    {
        $this->guardPercentage($percentage);

        $color = $this->currentColor();
        $steps = $this->convertBounds($percentage);

        return $this->adjustBrightness($color, $steps);
    }

    /**
     * Darken the current color for a given percentage, between 0 and 100.
     *
     * @param int $percentage
     *
     * @throws InvalidArgumentException The given percentage is not between 0 and 100
     *
     * @return string The hexadecimal color, with a leading #
     */
    public function darken($percentage)
    {
        $this->guardPercentage($percentage);

        $color = $this->currentColor();
        $steps = -1 * $this->convertBounds($percentage);

        return $this->adjustBrightness($color, $steps);
    }

    /**
     * Go from [0; 100] to [0; 255].
     *
     * @param int $percentage
     *
     * @return int
     */
    private function convertBounds($percentage)
    {
        return round($percentage / 100 * 255);
    }

    /**
     * Increment the color counter.
     */
    private function incrementColorCounter()
    {
        // It was the last color, go back to the beginning
        if (self::$currentKey == count($this->colors) - 1) {
            self::$currentKey = 0;
        } else {
            self::$currentKey += 1;
        }
    }

    /**
     * Check that a given percentage value is valid.
     *
     * @param int $percentage
     *
     * @throws InvalidArgumentException The given percentage is not between 0 and 100
     */
    private function guardPercentage($percentage)
    {
        if (!is_numeric($percentage) or $percentage < 0 or $percentage > 100) {
            throw new InvalidArgumentException("Expected a percentage between 0 and 100. Got {$percentage}");
        }
    }

    /**
     * Lighten or darken a color from an hexadecimal code.
     *
     * @author http://stackoverflow.com/questions/3512311/how-to-generate-lighter-darker-color-with-php
     *
     * @param string $hex   The color in hexadecimal
     * @param int    $steps Steps should be between -255 and 255. Negative = darker, positive = lighter
     *
     * @return string The hexadecimal color, with a leading #
     */
    private function adjustBrightness($hex, $steps)
    {
        $steps = max(-255, min(255, $steps));

        // Format the hex color string
        $hex = str_replace('#', '', $hex);
        if (strlen($hex) == 3) {
            $hex = str_repeat(substr($hex, 0, 1), 2).str_repeat(substr($hex, 1, 1), 2).str_repeat(substr($hex, 2, 1), 2);
        }

        // Get decimal values
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // Adjust number of steps and keep it inside 0 to 255
        $r = max(0, min(255, $r + $steps));
        $g = max(0, min(255, $g + $steps));
        $b = max(0, min(255, $b + $steps));

        $r_hex = str_pad(dechex($r), 2, '0', STR_PAD_LEFT);
        $g_hex = str_pad(dechex($g), 2, '0', STR_PAD_LEFT);
        $b_hex = str_pad(dechex($b), 2, '0', STR_PAD_LEFT);

        return '#'.$r_hex.$g_hex.$b_hex;
    }
}
