<?php
/**
 * This file is part of Leafiny.
 *
 * Copyright (C) Magentix SARL
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

/**
 * Class Attribute_Twig_Filters
 */
class Attribute_Twig_Filters
{
    /**
     * Add twig filters
     *
     * @param Twig\Environment $twig
     */
    public function __construct(Twig\Environment $twig)
    {
        $twig->addFilter(new Twig\TwigFilter('array_values', [$this, 'arrayValues']));
    }

    /**
     * Array values
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function arrayValues($value)
    {
        if (is_array($value)) {
            return array_values($value);
        }

        return $value;
    }
}
