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
        $content = preg_replace('/\s{2,}/', ' ', $content);
        $content = str_replace("\n", '', $content);
        $content = str_replace(', ', ',', $content);
        $content = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $content);

        return $content;
    }
}
