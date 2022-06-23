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
     * The font size
     *
     * @var int $fontSize
     */
    protected $fontSize = 20;

    /**
     * Text angle
     *
     * @var int $angle
     */
    protected $angle = 0;

    /**
     * Padding left
     *
     * @var int $paddingLeft
     */
    protected $paddingLeft = 5;

    /**
     * Padding top
     *
     * @var int $paddingTop
     */
    protected $paddingTop = 28;

    /**
     * Allowed chars
     *
     * @var string $chars
     */
    protected $chars = 'abcdefghijkmnopqrstuvwxyz23456789';

    /**
     * Retrieve the text to print
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
        imagecolorallocate($image, (int)$color['r'], (int)$color['g'], (int)$color['b']);

        $color = $this->hexToRgb($this->getColor());
        $ftColor = imagecolorallocate($image, (int)$color['r'], (int)$color['g'], (int)$color['b']);

        imagettftext(
            $image,
            $this->getFontSize(),
            $this->getAngle(),
            $this->getPaddingLeft(),
            $this->getPaddingTop(),
            $ftColor,
            $this->getFont(),
            $this->getText()
        );

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

    /**
     * Retrieve font size
     *
     * @return int
     */
    public function getFontSize(): int
    {
        return $this->fontSize;
    }

    /**
     * Set font size
     *
     * @param int $fontSize
     */
    public function setFontSize(int $fontSize): void
    {
        $this->fontSize = $fontSize;
    }

    /**
     * Retrieve padding top
     *
     * @return int
     */
    public function getPaddingTop(): int
    {
        return $this->paddingTop;
    }

    /**
     * Set padding top
     *
     * @param int $paddingTop
     */
    public function setPaddingTop(int $paddingTop): void
    {
        $this->paddingTop = $paddingTop;
    }

    /**
     * Retrieve padding left
     *
     * @return int
     */
    public function getPaddingLeft(): int
    {
        return $this->paddingLeft;
    }

    /**
     * Set padding top
     *
     * @param int $paddingLeft
     */
    public function setPaddingLeft(int $paddingLeft): void
    {
        $this->paddingLeft = $paddingLeft;
    }

    /**
     * Retrieve angle
     *
     * @return int
     */
    public function getAngle(): int
    {
        return $this->angle;
    }

    /**
     * Set angle
     *
     * @param int $angle
     */
    public function setAngle(int $angle): void
    {
        $this->angle = $angle;
    }
}
