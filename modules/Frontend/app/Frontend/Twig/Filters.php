<?php

declare(strict_types=1);

/**
 * Class Frontend_Twig_Filters
 */
class Frontend_Twig_Filters
{
    /**
     * Add twig filters
     *
     * @param Twig\Environment $twig
     */
    public function __construct(Twig\Environment $twig)
    {
        $twig->addFilter(new Twig\TwigFilter('number_format', 'number_format'));
    }
}
