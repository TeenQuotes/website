<?php

namespace TeenQuotes\Tools\Colors;

use InvalidArgumentException;

interface ColorGeneratorInterface
{
    /**
     * Get the current color.
     *
     * @return string The hexadecimal color, with a leading #
     */
    public function currentColor();

    /**
     * Get the current color and increment the counter.
     *
     * @return string The hexadecimal color, with a leading #
     */
    public function nextColor();

    /**
     * Lighten the current color for a given percentage, between 0 and 100.
     *
     * @param int $percentage
     *
     * @throws InvalidArgumentException The given percentage is not between 0 and 100
     *
     * @return string The hexadecimal color, with a leading #
     */
    public function lighten($percentage);

    /**
     * Darken the current color for a given percentage, between 0 and 100.
     *
     * @param int $percentage
     *
     * @throws InvalidArgumentException The given percentage is not between 0 and 100
     *
     * @return string The hexadecimal color, with a leading #
     */
    public function darken($percentage);
}
