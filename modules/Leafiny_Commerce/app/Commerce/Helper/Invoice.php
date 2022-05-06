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
 * Class Commerce_Helper_Invoice
 */
class Commerce_Helper_Invoice extends Core_Helper
{
    /**
     * PDF Object
     * 
     * @var FPDF $pdf
     */
    protected $pdf;
    /**
     * Invoice
     * 
     * @var Leafiny_Object $invoice
     */
    protected $invoice;
    /**
     * Font name
     * 
     * @var string $font
     */
    protected $font = 'helvetica';

    /**
     * Generate an invoice in PDF
     *
     * @param Leafiny_Object $invoice
     * @param string         $dest
     *
     * @return string
     */
    public function getPdf(Leafiny_Object $invoice, string $dest = 'D'): string
    {
        $this->invoice = $invoice;

        $this->init();
        $this->header();
        $this->seller();
        $this->buyer();
        $this->title();
        $this->products();
        $this->info();
        $this->totals();
        $this->payment();
        $this->footer();

        return $this->pdf->Output($dest, $this->invoice->getData('invoice_increment_id') . '.pdf');
    }

    /**
     * Init PDF
     */
    protected function init(): void
    {
        define('FPDF_FONTPATH', $this->getMediaFile('fonts'));

        $this->pdf = new FPDF();
        $this->pdf->AddPage();
        $this->pdf->SetAutoPageBreak(false);
        $this->pdf->SetFont($this->font, '', 11);
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->SetDrawColor(102, 102, 102);
    }

    /**
     * Header
     */
    protected function header(): void
    {
        /* Logo */
        if ($this->invoice->getData('logo')) {
            $this->pdf->Image($this->getMediaFile('logo'), 12, 13, 50);
            $this->pdf->SetXY(10, 10);
        }

        /* Seller address */
        /** @var Leafiny_Object|null $seller */
        $seller = $this->invoice->getData('seller');

        /* Date */
        $date = [
            $seller->getData('city'),
            $this->invoice->getData('date')
        ];
        $date = array_filter($date);

        if ($this->invoice->getData('date')) {
            $this->pdf->Cell(190, 10, join(', ', $date), 0, 0, 'R');
        }
    }

    /**
     * Seller
     */
    protected function seller(): void
    {
        /** @var Leafiny_Object|null $seller */
        $seller = $this->invoice->getData('seller');

        if (!$seller) {
            return;
        }

        $locality = [$seller->getData('postcode') . ' ' . $seller->getData('city')];
        if ($seller->getData('state')) {
            $locality[] = $seller->getData('state');
        }

        $this->pdf->SetXY(10, 22);
        $this->pdf->Cell(40, 10, $seller->getData('company'));
        $this->pdf->SetXY(10, 27);
        $this->pdf->Cell(40, 10, $seller->getData('street'));
        $this->pdf->SetXY(10, 32);
        $this->pdf->Cell(40, 10, join(', ', $locality));
        $this->pdf->SetXY(10, 37);
        $this->pdf->Cell(40, 10, $seller->getData('email'));
    }

    /**
     * Buyer
     */
    protected function buyer(): void
    {
        /** @var Leafiny_Object|null $buyer */
        $buyer = $this->invoice->getData('buyer');

        if (!$buyer) {
            return;
        }

        $this->pdf->SetFont($this->font, '', 9);
        $this->pdf->SetXY(138, 37);
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->Cell(60, 25, '', 1);
        $this->pdf->SetXY(140, 37);
        if ($buyer->getData('company')) {
            $this->pdf->Cell(40, 10, $buyer->getData('company'));
        } else {
            $this->pdf->Cell(40, 10, $buyer->getData('firstname') . ' ' . $buyer->getData('lastname'));
        }
        $this->pdf->SetXY(140, 42);
        $this->pdf->Cell(40, 10, $buyer->getData('street_1'));
        $this->pdf->SetXY(140, 47);
        $this->pdf->Cell(40, 10, $buyer->getData('street_2'));
        $this->pdf->SetXY(140, 52);
        $this->pdf->Cell(40, 10, $buyer->getData('postcode') . ' ' . $buyer->getData('city'));
        $this->pdf->SetFont($this->font, '', 11);
    }

    /**
     * Title
     */
    protected function title(): void
    {
        $this->pdf->SetXY(9, 67);
        $this->pdf->SetFont($this->font, '', 25);
        $this->pdf->Cell(40, 10, App::translate('Invoice'));
        $this->pdf->SetFont($this->font, '', 17);
        $this->pdf->SetXY(43, 68);
        $this->pdf->SetTextColor(102, 102, 102);
        $this->pdf->Cell(158, 10, $this->invoice->getData('invoice_increment_id'));
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->Line(10, 77, 198, 77);
    }

    /**
     * Products
     */
    protected function products(): void
    {
        $this->pdf->SetXY(10, 80);
        $this->pdf->SetFillColor(0, 0, 0);
        $this->pdf->SetTextColor(255, 255, 255);
        $this->pdf->Cell(188, 6, '', 1, 0, 'C', 1);
        $this->pdf->SetXY(10, 78);
        $this->pdf->SetFont($this->font, '', 10);

        $this->pdf->Cell(98, 10, App::translate('Item'));
        $this->pdf->Cell(27, 10, App::translate('Price Excl. Tax'));
        $this->pdf->Cell(15, 10, App::translate('Qty'));
        $this->pdf->Cell(48, 10, App::translate('Total Excl. Tax'));

        $this->pdf->SetXY(10, 86);

        $this->pdf->SetFillColor(255, 255, 255);
        $this->pdf->SetTextColor(0, 0, 0);

        $this->pdf->Cell(98, 110, '', 1, 0, 'C', 1);
        $this->pdf->Cell(27, 110, '', 1, 0, 'C', 1);
        $this->pdf->Cell(15, 110, '', 1, 0, 'C', 1);
        $this->pdf->Cell(48, 110, '', 1, 0, 'C', 1);

        $lineStart = 86;
        $lineSize  = 6;

        $this->pdf->SetXY(10, $lineStart);

        if (!$this->invoice->getData('items')) {
            return;
        }

        $this->pdf->SetFont('courier', '', 9);

        /** @var Leafiny_Object $item */
        foreach ($this->invoice->getData('items') as $item) {
            $name = '[' . $item->getData('product_sku') . '] ' . $item->getData('product_name');
            $lines = explode("\n", wordwrap($name, 48));

            $currentLineStart = $lineStart + 5;

            foreach ($lines as $line) {
                $this->pdf->Cell(100, 10, trim($line));
                $this->pdf->SetXY(10, $currentLineStart);

                $currentLineStart = $currentLineStart + 5;
            }

            $this->pdf->SetXY(110, $lineStart);

            $this->pdf->Cell(25, 10, $this->formatCurrency((float)$item->getData('excl_tax_unit')), 0, 0, 'R');
            $this->pdf->Cell(15, 10, $item->getData('qty'), 0, 0, 'C');
            $this->pdf->Cell(48, 10, $this->formatCurrency((float)$item->getData('excl_tax_row')), 0, 0, 'R');

            $lineStart += $lineSize;
            if (count($lines) > 1) {
                $lineStart += (count($lines) * 5) - 5;
            }

            $this->pdf->SetXY(10, $lineStart);
        }
    }

    /**
     * Info
     */
    protected function info(): void
    {
        $start = 197;

        $this->pdf->SetFont($this->font, '', 10);
        $this->pdf->SetXY(10, $start);

        $order = App::translate('Order:') . ' ' . $this->invoice->getData('sale_increment_id');

        $this->pdf->Cell(125, 10, $order);
        $this->pdf->SetXY(10, $start);

        $start = $start + 10;

        if (!$this->invoice->getData('info')) {
            return;
        }

        $lines = explode("\n", wordwrap($this->invoice->getData('info'), 50));

        $this->pdf->SetXY(10, $start);

        $start = $start + 5;

        foreach ($lines as $line) {
            $this->pdf->Cell(125, 10, trim($line));
            $this->pdf->SetXY(10, $start);

            $start = $start + 5;
        }
    }

    /**
     * Totals
     */
    protected function totals(): void
    {
        $beginning = 196;

        $lines = $this->getCustom('order_total_lines');

        if (!$lines) {
            $lines = [
                'Subtotal' => [
                    'column' => 'excl_tax_subtotal',
                    'style' => '',
                    'required' => true,
                ],
                'Shipping' => [
                    'column' => 'excl_tax_shipping',
                    'style' => '',
                    'required' => true,
                ],
                'Discount' => [
                    'column' => 'excl_tax_discount',
                    'style' => '',
                    'required' => false,
                ],
                'Total Excl. Tax' => [
                    'column' => 'excl_tax_total',
                    'style' => 'B',
                    'required' => true,
                ],
                'Tax' => [
                    'column' => 'tax_total',
                    'style' => '',
                    'required' => true,
                ],
            ];
        }

        $this->pdf->SetXY(135, $beginning);
        $this->pdf->SetFillColor(255, 255, 204);
        $this->pdf->Cell(63, count($lines) * 6, '', 1, 0, 'C', 1);

        $start = $beginning - 5;

        foreach ($lines as $label => $data) {
            $start = $start + 5;

            if (!$data['required'] && !$this->invoice->getData($data['column'])) {
                continue;
            }

            $this->pdf->SetFont('courier', $data['style'],10);
            $this->pdf->SetXY(135, $start);
            $this->pdf->Cell(35, 10, App::translate($label));
            $this->pdf->SetXY(170, $start);
            $this->pdf->Cell(28, 10, $this->formatCurrency((float)$this->invoice->getData($data['column'])), 0, 0, 'R');
        }

        $start = $beginning + count($lines) * 6;

        $this->pdf->SetFillColor(248, 248, 248);
        $this->pdf->SetXY(135, $start);
        $this->pdf->Cell(63, 10, '', 1, 0, 'C', 1);

        $this->pdf->SetFont('courier', 'B', 10);
        $this->pdf->SetXY(135, $start);
        $this->pdf->Cell(35, 10, App::translate('Total Incl. Tax'));
        $this->pdf->SetXY(170, $start);
        $this->pdf->Cell(28, 10, $this->formatCurrency((float)$this->invoice->getData('incl_tax_total')), 0, 0, 'R');
    }

    /**
     * Payment
     */
    protected function payment(): void
    {
        $this->pdf->SetXY(9, 244);

        $this->pdf->SetTextColor(102, 102, 102);
        $this->pdf->SetFont($this->font, '', 17);
        $this->pdf->Cell(188, 10, App::translate('Payment'));
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->Line(10, 252, 198, 252);

        $this->pdf->SetXY(10, 254);
        $this->pdf->SetFont($this->font, 'B', 9);
        $this->pdf->Cell(30, 10, $this->invoice->getData('payment_title'));
    }

    /**
     * Footer
     */
    protected function footer(): void
    {
        $this->pdf->SetTextColor(89, 89, 89);
        $this->pdf->SetFont($this->font, '', 9);

        /** @var Leafiny_Object|null $seller */
        $seller = $this->invoice->getData('seller');

        if ($seller) {
            $this->pdf->SetXY(10, 276);
            $address = array_filter(
                [
                    $seller->getData('company'),
                    $seller->getData('street'),
                    $seller->getData('postcode'),
                    $seller->getData('city'),
                    $seller->getData('state'),
                ]
            );
            $this->pdf->Cell(188, 10, join(' - ', $address), 0, 0, 'C');
        }

        if ($this->invoice->getData('legal')) {
            $this->pdf->SetXY(10, 281);
            $this->pdf->Cell(188, 10, join(' - ', $this->invoice->getData('legal')), 0, 0, 'C');
        }
    }

    /**
     * Retrieve Media File
     *
     * @param string $value
     *
     * @return string
     */
    protected function getMediaFile(string $value): string
    {
        $file = $this->invoice->getData($value);

        return $this->getModulesDir() . $this->getModuleFile($file, Core_Helper::MEDIA_DIRECTORY);
    }

    /**
     * Format currency
     *
     * @param float|null $price
     *
     * @return string
     */
    protected function formatCurrency(?float $price): string
    {
        if ($price === null) {
            $price = 0;
        }

        /** @var Commerce_Helper_Cart $helper */
        $helper = App::getSingleton('helper', 'cart');

        return $this->currencySymbol($helper->formatCurrency($price, $this->invoice->getData('sale_currency')));
    }

    /**
     * Retrieve currency symbol ASCII code
     *
     * @param string $price
     *
     * @return string
     */
    protected function currencySymbol(string $price): string
    {
        $symbols = [
            '$' => 36,
            '€' => 128,
            '¢' => 162,
            '£' => 163,
            '¥' => 165,
            'č' => 269,
            'ł' => 322,
            'ƒ' => 402,
            '₡' => 8353,
            '₦' => 8358,
            '₩' => 8361,
            '₪' => 8362,
            '₫' => 8363,
            '₭' => 8365,
            '₮' => 8366,
            '₱' => 8369,
            '₴' => 8372,
            '₼' => 8380,
            '₽' => 8381,
            '฿' => 3647,
        ];

        foreach ($symbols as $symbol => $asciiCode) {
            $price = str_replace($symbol, utf8_encode(chr($asciiCode)), $price);
        }

        return $price;
    }
}
