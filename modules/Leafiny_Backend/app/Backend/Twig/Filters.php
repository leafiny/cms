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
 * Class Backend_Twig_Filters
 */
class Backend_Twig_Filters
{
    /**
     * Add twig filters
     *
     * @param Twig\Environment $twig
     */
    public function __construct(Twig\Environment $twig)
    {
        $twig->addFilter(new Twig\TwigFilter('truncate', 'mb_strimwidth'));
    }
}
