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
     * @param string $base_url
     * @param array $phrases
     * @return string
     */
    public function createShareUrl($base_url, array $phrases)
    {  
        return $base_url . '?q='. urlencode(implode('|', $phrases));
    }

    /**
     * @param string $url
     * @return array List of phrases
     */
    public function decodeShareUrl($url)
    {
        $phrases = [];
        parse_str(parse_url($url, PHP_URL_QUERY), $queryParams);
        if (isset($queryParams['q']) && is_string($queryParams['q'])) {
            $q = explode('|', $queryParams['q']);
            if (is_array($q) && count($q) >= 2 && count($q) <= 3) {
                foreach ($q as $p) {
                    if (is_string($p)) {
                        $phrases[] = htmlspecialchars(trim($p));
                    }
                }
            }
        }
        return $phrases;
    }
}