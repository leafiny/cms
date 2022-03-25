<?php

declare(strict_types=1);

/**
 * Class Commerce_Twig_Filters
 */
class Commerce_Twig_Filters
{
    /**
     * Add twig filters
     *
     * @param Twig\Environment $twig
     */
    public function __construct(Twig\Environment $twig)
    {
        $twig->addFilter(new Twig\TwigFilter('currency', [$this, 'currency']));
    }

    /**
     * Format currency
     *
     * @param mixed       $value
     * @param string|null $currency
     *
     * @return string
     */
    public function currency($value, ?string $currency = null): string
    {
        /** @var Commerce_Helper_Cart $helper */
        $helper = App::getSingleton('helper', 'cart');

        return $helper->formatCurrency($value, $currency);
    }
}