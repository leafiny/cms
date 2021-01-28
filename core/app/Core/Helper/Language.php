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
 * Class Core_Helper_Language
 */
class Core_Helper_Language extends Core_Helper
{
    /**
     * Retrieve language name by code
     * 
     * @param string $code
     * 
     * @return string
     */
    public function getName(string $code): string
    {
        $languages = $this->getList();

        if (!isset($languages[$code])) {
            return '';
        }

        return $languages[$code];
    }

    /**
     * Retrieve language list
     * 
     * @return string[]
     */
    public function getList(): array
    {
        return [
            'af_ZA'      => 'Afrikaans (South Africa)',
            'sq_AL'      => 'Albanian (Albania)',
            'ar_DZ'      => 'Arabic (Algeria)',
            'ar_EG'      => 'Arabic (Egypt)',
            'ar_KW'      => 'Arabic (Kuwait)',
            'ar_MA'      => 'Arabic (Morocco)',
            'ar_SA'      => 'Arabic (Saudi Arabia)',
            'az_Latn_AZ' => 'Azerbaijani (Latin, Azerbaijan)',
            'bn_BD'      => 'Bangla (Bangladesh)',
            'eu_ES'      => 'Basque (Spain)',
            'be_BY'      => 'Belarusian (Belarus)',
            'bs_Latn_BA' => 'Bosnian (Latin, Bosnia &amp; Herzegovina)',
            'bg_BG'      => 'Bulgarian (Bulgaria)',
            'ca_ES'      => 'Catalan (Spain)',
            'zh_Hans_CN' => 'Chinese (Simplified Han, China)',
            'zh_Hant_HK' => 'Chinese (Traditional Han, Hong Kong SAR China)',
            'zh_Hant_TW' => 'Chinese (Traditional Han, Taiwan)',
            'hr_HR'      => 'Croatian (Croatia)',
            'cs_CZ'      => 'Czech (Czechia)',
            'da_DK'      => 'Danish (Denmark)',
            'nl_BE'      => 'Dutch (Belgium)',
            'nl_NL'      => 'Dutch (Netherlands)',
            'en_AU'      => 'English (Australia)',
            'en_CA'      => 'English (Canada)',
            'en_IE'      => 'English (Ireland)',
            'en_NZ'      => 'English (New Zealand)',
            'en_GB'      => 'English (United Kingdom)',
            'en_US'      => 'English (United States)',
            'et_EE'      => 'Estonian (Estonia)',
            'fil_PH'     => 'Filipino (Philippines)',
            'fi_FI'      => 'Finnish (Finland)',
            'fr_BE'      => 'French (Belgium)',
            'fr_CA'      => 'French (Canada)',
            'fr_FR'      => 'French (France)',
            'fr_LU'      => 'French (Luxembourg)',
            'fr_CH'      => 'French (Switzerland)',
            'gl_ES'      => 'Galician (Spain)',
            'ka_GE'      => 'Georgian (Georgia)',
            'de_AT'      => 'German (Austria)',
            'de_DE'      => 'German (Germany)',
            'de_LU'      => 'German (Luxembourg)',
            'de_CH'      => 'German (Switzerland)',
            'el_GR'      => 'Greek (Greece)',
            'gu_IN'      => 'Gujarati (India)',
            'he_IL'      => 'Hebrew (Israel)',
            'hi_IN'      => 'Hindi (India)',
            'hu_HU'      => 'Hungarian (Hungary)',
            'is_IS'      => 'Icelandic (Iceland)',
            'id_ID'      => 'Indonesian (Indonesia)',
            'it_IT'      => 'Italian (Italy)',
            'it_CH'      => 'Italian (Switzerland)',
            'ja_JP'      => 'Japanese (Japan)',
            'km_KH'      => 'Khmer (Cambodia)',
            'ko_KR'      => 'Korean (South Korea)',
            'lo_LA'      => 'Lao (Laos)',
            'lv_LV'      => 'Latvian (Latvia)',
            'lt_LT'      => 'Lithuanian (Lithuania)',
            'mk_MK'      => 'Macedonian (North Macedonia)',
            'ms_MY'      => 'Malay (Malaysia)',
            'nb_NO'      => 'Norwegian Bokmål (Norway)',
            'nn_NO'      => 'Norwegian Nynorsk (Norway)',
            'fa_IR'      => 'Persian (Iran)',
            'pl_PL'      => 'Polish (Poland)',
            'pt_BR'      => 'Portuguese (Brazil)',
            'pt_PT'      => 'Portuguese (Portugal)',
            'ro_RO'      => 'Romanian (Romania)',
            'ru_RU'      => 'Russian (Russia)',
            'sr_Cyrl_RS' => 'Serbian (Cyrillic, Serbia)',
            'sr_Latn_RS' => 'Serbian (Latin, Serbia)',
            'sk_SK'      => 'Slovak (Slovakia)',
            'sl_SI'      => 'Slovenian (Slovenia)',
            'es_AR'      => 'Spanish (Argentina)',
            'es_BO'      => 'Spanish (Bolivia)',
            'es_CL'      => 'Spanish (Chile)',
            'es_CO'      => 'Spanish (Colombia)',
            'es_CR'      => 'Spanish (Costa Rica)',
            'es_MX'      => 'Spanish (Mexico)',
            'es_PA'      => 'Spanish (Panama)',
            'es_PE'      => 'Spanish (Peru)',
            'es_ES'      => 'Spanish (Spain)',
            'es_VE'      => 'Spanish (Venezuela)',
            'sw_KE'      => 'Swahili (Kenya)',
            'sv_FI'      => 'Swedish (Finland)',
            'sv_SE'      => 'Swedish (Sweden)',
            'th_TH'      => 'Thai (Thailand)',
            'tr_TR'      => 'Turkish (Turkey)',
            'uk_UA'      => 'Ukrainian (Ukraine)',
            'vi_VN'      => 'Vietnamese (Vietnam)',
            'cy_GB'      => 'Welsh (United Kingdom)',
        ];
    }
}
