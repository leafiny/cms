<?php

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
