<?php

class UrlHelper
{
    const SHARE_URL_QUERY_DELIMITER = '_or_';
    
    /**
     * @return string
     */
    public function baseUrl()
    {
        return 'http://' . $_SERVER['SERVER_NAME'];
    }

    /**
     * @return string
     */
    public function currentUrl()
    {
        return $this->baseUrl() . $_SERVER['REQUEST_URI'];
    }

    /**
     * @param string $url
     * @return string
     */
    public function createTinyUrl($url)
    {  
        $ch = curl_init();  
        curl_setopt($ch, CURLOPT_URL, 'https://tinyurl.com/api-create.php?url=' . $url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        $data = curl_exec($ch);  
        curl_close($ch);  
        return $data;
    }

    /**
     * @param array $phrases
     * @return string
     */
    public function createShareUrl(array $phrases)
    {  
        $encPhrases = [];
        foreach ($phrases as $phrase) {
            $encPhrases[] = urlencode(str_replace(' ', '-', strtolower(trim($phrase))));
        }
        $shareUrl = $this->baseUrl() . '/' . implode(self::SHARE_URL_QUERY_DELIMITER, $encPhrases);
        return $shareUrl;
    }

    /**
     * @param string $query
     * @return array List of phrases
     */
    public function decodeSearchQuery($query)
    {
        $phrases = [];
        if (empty($query) || !is_string($query)) {
            return $phrases;
        }
        $rawPhrases = explode(self::SHARE_URL_QUERY_DELIMITER, urldecode($query));
        if (is_array($rawPhrases)) {
            foreach ($rawPhrases as $phrase) {
                if (!empty($phrase) && is_string($phrase)) {
                    $phrases[] = htmlspecialchars(trim(str_replace(['-', '"'], [' ', "'"], $phrase)));
                }
            }
        }
        return $phrases;
    }

    /**
     * @return array List of phrases
     */
    public function decodeCurrentSearchQuery()
    {
        $phrases = [];
        if (!empty($_SERVER['REQUEST_URI'])) {
            $requestUri = trim($_SERVER['REQUEST_URI'], " \t\n\r\0\x0B/");
            $phrases = $this->decodeSearchQuery($requestUri);
        }
        return $phrases;
    }
}