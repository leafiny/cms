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
 * Class Frontend_Helper_Combine_Css
 */
class Frontend_Helper_Combine_Css extends Frontend_Helper_Combine
{
    /**
     * Retrieve file content
     *
     * @param Leafiny_Object $file
     *
     * @return string
     */
    public function getContent(Leafiny_Object $file): string
    {
        $content = parent::getContent($file);

        preg_match_all('/url\(([^\s]*)\)/i', $content, $matches);

        $toUpdate = array_unique($matches[1] ?? []);

        foreach ($toUpdate as $url) {
            $path = trim($url, "'");
            $path = trim($path, '"');

            if (substr($path, 0, 4) === 'http') {
                continue;
            }
            if (substr($path, 0, 4) === 'data') {
                continue;
            }
            if (substr($path, 0, 1) === '/') {
                continue;
            }

            $content = str_replace($url, '"' . dirname($file->getData('url')) . US . $path . '"', $content);
        }

        return $this->clean($content);
    }

    /**
     * Clean the file
     *
     * @param string $content
     *
     * @return string
     */
    protected function clean(string $content): string
    {
        $tokenizers = [
            '/\s+/'                                          => ' ', // Normalize whitespace
            '/(\s+)(\/\*(.*?)\*\/)(\s+)/'                    => '$2', // Remove spaces before and after comments
            '!/\*[^*]*\*+([^/][^*]*\*+)*/!'                  => '', // Remove comments
            '/;(?=\s*})/'                                    => '', // Remove ; before }
            '/(,|:|;|\{|}) /'                                => '$1', // Remove space after , : ; { }
            '/ (,|;|\{|})/'                                  => '$1', // Remove space before , ; { }
            '/(:| )0\.([0-9]+)(%|em|ex|px|in|cm|mm|pt|pc)/i' => '${1}.${2}${3}', // Strips leading 0 on decimal values
            '/(:| )(\.?)0(%|em|ex|px|in|cm|mm|pt|pc)/i'      => '${1}0', // Strips units if value is 0
        ];

        foreach ($tokenizers as $pattern => $replacement) {
            $content = preg_replace($pattern, $replacement, $content);
        }

        return trim($content);
    }
}
