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
 * Class Leafiny_Captcha
 */
class Leafiny_Captcha
{
    /**
     * Captcha text
     *
     * @var string|null $text
     */
    private $text = null;
    /**
     * Font file in ttf format
     *
     * @var string $font
     */
    protected $font = __DIR__ . DS . 'Captcha' . DS . 'font.ttf';
    /**
     * Text color in hexadecimal
     *
     * @var string $color
     */
    protected $color = '#000000';
    /**
     * Background Color in hexadecimal
     *
     * @var string $background
     */
    protected $background = '#FFFFFF';
    /**
     * Text length
     *
     * @var int $length
     */
    protected $length = 5;
    /**
     * Image width in px
     *
     * @var int $width
     */
    protected $width = 92;
    /**
     * Image height in px
     *
     * @var int $height
     */
    protected $height = 40;
    /**
     * Allowed chars
     *
     * @var string $chars
     */
    protected $chars = 'abcdefghijklmnopqrstuvwxyz';

    /**
     * Retrieve text to print
     *
     * @return string
     */
    public function getText(): string
    {
        if ($this->text !== null) {
            return $this->text;
        }

        $this->text = substr(str_shuffle(str_repeat($this->getChars(), 5)), 0, $this->getLength());

        return $this->text;
    }

    /**
     * Retrieve PNG image data
     *
     * @return string
     */
    public function getImage(): string
    {
        $image = imagecreate($this->getWidth(), $this->getHeight());

        $color = $this->hexToRgb($this->getBackground());
        $bgColor = imagecolorallocate($image, (int)$color['r'], (int)$color['g'], (int)$color['b']);

        $color = $this->hexToRgb($this->getColor());
        $ftColor = imagecolorallocate($image, (int)$color['r'], (int)$color['g'], (int)$color['b']);

        $text = $this->getText();
        $font = $this->getFont();

        imagettftext($image, 20, 0, 5, 28, $bgColor, $font, $text);
        imagettftext($image, 20, 0, 5, 28, $ftColor, $font, $text);

        ob_start();
        imagepng($image);
        $result = ob_get_clean();

        imagedestroy($image);

        return $result;
    }

    /**
     * Gets the HTML inline base64
     *
     * @return string
     */
    public function inline(): string
    {
        return 'data:image/png;base64,' . base64_encode($this->getImage());
    }

    /**
     * Retrieve RGB color from hexadecimal
     *
     * @param string $color
     *
     * @return string[]
     */
    public function hexToRgb(string $color): array
    {
        $hex = ltrim($color,'#');

        return [
            'r' => hexdec(substr($hex,0,2)),
            'g' => hexdec(substr($hex,2,2)),
            'b' => hexdec(substr($hex,4,2))
        ];
    }

    /**
     * Retrieve font
     *
     * @return string
     */
    public function getFont(): string
    {
        return $this->font;
    }

    /**
     * Set font file in ttf format
     *
     * @param string $font
     */
    public function setFont(string $font): void
    {
        $this->font = $font;
    }

    /**
     * Retrieve text color
     *
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * Set text color in hexadecimal
     *
     * @param string $color
     */
    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    /**
     * Retrieve background color
     *
     * @return string
     */
    public function getBackground(): string
    {
        return $this->background;
    }

    /**
     * Set background color in hexadecimal
     *
     * @param string $background
     */
    public function setBackground(string $background): void
    {
        $this->background = $background;
    }

    /**
     * Retrieve allowed chars
     *
     * @return string
     */
    public function getChars(): string
    {
        return $this->chars;
    }

    /**
     * Set allowed chars
     *
     * @param string $chars
     */
    public function setChars(string $chars): void
    {
        $this->chars = $chars;
    }

    /**
     * Retrieve text length
     *
     * @return int
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * Set text length
     *
     * @param int $length
     */
    public function setLength(int $length): void
    {
        $this->length = $length;
    }

    /**
     * Retrieve image width
     *
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * Set image width
     *
     * @param int $width
     */
    public function setWidth(int $width): void
    {
        $this->width = $width;
    }
    /**
     * Retrieve image height
     *
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * Set image height
     *
     * @param int $height
     */
    public function setHeight(int $height): void
    {
        $this->height = $height;
    }
}
