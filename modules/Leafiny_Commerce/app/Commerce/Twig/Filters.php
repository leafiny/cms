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
     * @param mixed $value
     *
     * @return string
     */
    public function currency($value): string
    {
        return number_format((float)$value, 2, ',', '') . ' ' . $this->getCurrency();
    }

    /**
     * Retrieve store currency
     *
     * @return string
     */
    public function getCurrency(): string
    {
        /** @var Commerce_Helper_Cart $helper */
        $helper = App::getSingleton('helper', 'cart');

        return $helper->getCurrency() ?: '';
    }
}