<?php

class Url {
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
     * @param string $baseUrl
     * @param array $phrases
     * @return string
     */
    public function createShareUrl($baseUrl, array $phrases)
    {  
        return $baseUrl . '?q='. urlencode(implode('|', $phrases));
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