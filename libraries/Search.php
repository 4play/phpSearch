<?php

namespace Fir\Libraries;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;

class Search {

    /**
     * The Bing API Key
     * @var
     */
    public $key;

    /**
     * API end point
     * @var
     */
    public $endpoint;

    /**
     * The response filter
     * @var
     */
    public $responseFilter;

    private $markets = ['es-AR' => ['Argentina', 'Spanish'], 'en-AU' => ['Australia', 'English'], 'de-AT' => ['Austria', 'German'], 'nl-BE' => ['Belgium', 'Dutch'], 'fr-BE' => ['Belgium', 'French'], 'pt-BR' => ['Brazil', 'Portuguese'], 'en-CA' => ['Canada', 'English'], 'fr-CA' => ['Canada', 'French'], 'es-CL' => ['Chile', 'Spanish'], 'da-DK' => ['Denmark', 'Danish'], 'fi-FI' => ['Finland', 'Finnish'], 'fr-FR' => ['France', 'French'], 'de-DE' => ['Germany', 'German'], 'zh-HK' => ['Hong Kong SAR', 'Traditional Chinese'], 'en-IN' => ['India', 'English'], 'en-ID' => ['Indonesia', 'English'], 'it-IT' => ['Italy', 'Italian'], 'ja-JP' => ['Japan', 'Japanese'], 'ko-KR' => ['Korea', 'Korean'], 'en-MY' => ['Malaysia', 'English'], 'es-MX' => ['Mexico', 'Spanish'], 'nl-NL' => ['Netherlands', 'Dutch'], 'en-NZ' => ['New Zealand', 'English'], 'no-NO' => ['Norway', 'Norwegian'], 'zh-CN' => ['People\'s republic of China', 'Chinese'], 'pl-PL' => ['Poland', 'Polish'], 'pt-PT' => ['Portugal', 'Portuguese'], 'en-PH' => ['Republic of the Philippines', 'English'], 'ru-RU' => ['Russia', 'Russian'], 'ar-SA' => ['Saudi Arabia', 'Arabic'], 'en-ZA' => ['South Africa', 'English'], 'es-ES' => ['Spain', 'Spanish'], 'sv-SE' => ['Sweden', 'Swedish'], 'fr-CH' => ['Switzerland', 'French'], 'de-CH' => ['Switzerland', 'German'], 'zh-TW' => ['Taiwan', 'Traditional Chinese'], 'tr-TR' => ['Turkey', 'Turkish'], 'en-GB' => ['United Kingdom', 'English'], 'en-US' => ['United States', 'English'], 'es-US' => ['United States', 'Spanish']];

    /**
     * @param   array   $params
     * @return  string
     */
    public function request($params) {
        $httpClient = new Client(['http_errors' => false]);

        try {
            $request = $httpClient->request('GET', 'https://api.bing.microsoft.com/v7.0/'.$this->endpoint.'?'.http_build_query($params).(isset($this->responseFilter) ? '&responseFilter='.$this->responseFilter : ''), [
                'headers' => ['Ocp-Apim-Subscription-Key' => $this->key]
            ]);

            $output = $request->getBody()->getContents();
        } catch (\Exception $e) {
            $output = $e->getMessage();
        }

        return $output;
    }

    /**
     * Returns the available markets
     *
     * @return  array
     */
    public function getMarkets() {
        return $this->markets;
    }

    /**
     * Builds the query syntax for the "site:" operator
     *
     * @param   string  $domains The list of the domains to be searched
     * @return  string
     */
    public function specificSites($domains) {
        $output = '';
        if(!empty($domains)) {
            $domains = explode(PHP_EOL, str_replace(array('http://', 'https://'), '', $domains));

            $urls = [];
            foreach($domains as $domain) {
                $urls[] = 'site:'.rtrim($domain, '/');
            }

            $output = ' ('.implode(' OR ', $urls).')';
        }
        return $output;
    }
}