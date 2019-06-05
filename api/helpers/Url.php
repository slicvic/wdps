<?php

class Url {
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
        return $this->baseUrl() . '?q='. urlencode(implode('|', $phrases));
    }

    /**
     * @param string $query
     * @return array List of phrases
     */
    public function decodeShareUrlQuery($query)
    {
        $phrases = [];
        if (!is_string($query)) {
            return $phrases;
        }
        $rawPhrases = explode('|', urldecode($query));
        if (is_array($rawPhrases) && count($rawPhrases) >= 2 && count($rawPhrases) <= 3) {
            foreach ($rawPhrases as $phrase) {
                if (is_string($phrase)) {
                    $phrases[] = htmlspecialchars(trim($phrase));
                }
            }
        }
        return $phrases;
    }
}