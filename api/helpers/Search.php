<?php

class Search {

    const SEARCH_ENGINE_GOOGLE = 'google';
    const SEARCH_ENGINE_BING = 'bing';

    /**
     * @var string
     */
    protected $searchEngine;

    /**
     * @param string $searchEngine
     */
    public function __construct($searchEngine = '')
    {
        $this->setSearchEngine($searchEngine ?: self::SEARCH_ENGINE_BING);
    }

    /**
     * @param string phrase
     * @param string site e.g. twitter.com
     * @return int
     */
    public function search($phrase, $site = '')
    {
        $q = '"' . $phrase . '"';
        if (!empty($site)) {
            $q = "site:$site $q";
        }
        $methodName = 'search' . ucfirst($this->searchEngine);
        return $this->$methodName($q);
    }

    /**
     * @param string $searchEngine
     * @return $this
     */
    public function setSearchEngine($searchEngine)
    {
        if (!in_array($searchEngine, [
            self::SEARCH_ENGINE_GOOGLE,
            self::SEARCH_ENGINE_BING
        ])) {
            throw new Exception('Invalid search engine');
        }
        $this->searchEngine = $searchEngine;
        return $this;
    }

    /**
     * @param string $q
     * @return int
     */
    protected function searchGoogle($q)
    {
        $response = $this->curl('https://www.google.com/search?q=' . urlencode($q));
        preg_match('/<div id="resultStats">About ([0-9,]+) results/', $response, $matches, PREG_OFFSET_CAPTURE);
        if (!empty($matches[1][0])) {
            return preg_replace('/[^0-9]/', '', $matches[1][0]);
        }
        return 0;
    }

    /**
     * @param string $q
     * @return int
     */
    protected function searchBing($q)
    {
        $response = $this->curl('https://www.bing.com/search?q=' . urlencode($q));
        preg_match('/<span class="sb_count">([0-9,]+) results/', $response, $matches, PREG_OFFSET_CAPTURE);
        if (!empty($matches[1][0])) {
            return preg_replace('/[^0-9]/', '', $matches[1][0]);
        }
        return 0;
    }

    /**
     * @param string $url
     * @return string
     */
    protected function curl($url)
    {
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0');
        $response = curl_exec($ch);
        curl_close($ch); 
        return $response;
    }
}